<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ServiceUsageController extends Controller
{
    public function index(Request $request, Room $room)
    {
        $room->load('floor.branch');

        $usageDate = $request->input('usage_date', now()->toDateString());

        $usages = $room->usages()
            ->withCount('shares')
            ->whereDate('usage_date', $usageDate)
            ->get()
            ->keyBy('service_id');

        $services = Service::filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalServices = Service::count();

        $activeBookings = $room->activeBookings()->count();

        return view('service_usages.index', compact('room', 'services', 'usages', 'usageDate', 'totalServices', 'activeBookings'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'usage_date'              => 'required|date|after_or_equal:' . now()->subDay()->toDateString() . '|before_or_equal:' . now()->toDateString(),
            'services'                => 'required|array|min:1',
            'services.*.service_id'   => 'required|exists:services,id|distinct',
            'services.*.usage_amount' => 'required|numeric|min:0',
        ]);

        $mandatoryIds = Service::where('is_mandatory', true)->pluck('id')->toArray();

        foreach ($validated['services'] as $service) {
            if (in_array($service['service_id'], $mandatoryIds) && $service['usage_amount'] <= 0) {
                throw ValidationException::withMessages([
                    'services.' . $service['service_id'] . '.usage_amount' => 'Dịch vụ bắt buộc phải có giá trị sử dụng lớn hơn 0.'
                ]);
            }
        }

        $validated['services'] = collect($validated['services'])
            ->filter(fn($service) => $service['usage_amount'] > 0)
            ->values()
            ->toArray();

        $usageDate = $validated['usage_date'];

        $servicesInput = $validated['services'];
        $serviceIds = collect($servicesInput)->pluck('service_id');
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $bookings = $room->activeBookings()->get();
        $bookingCount = $bookings->count();

        try {
            DB::transaction(function () use ($usageDate, $servicesInput, $serviceIds, $services, $room, $bookings, $bookingCount) {
                $room->usages()
                    ->where('usage_date', $usageDate)
                    ->whereNotIn('service_id', $serviceIds)
                    ->delete();

                foreach ($servicesInput as $input) {
                    $service = $services->get($input['service_id']);

                    $usage = $room->usages()->updateOrCreate([
                        'service_id' => $input['service_id'],
                        'usage_date' => $usageDate,
                    ], [
                        'usage_amount' => $input['usage_amount'],
                        'unit_price'   => $service->unit_price,
                        'subtotal'     => max(0, $input['usage_amount'] - $service->free_quota) * $service->unit_price,
                    ]);

                    $usage->shares()->delete();

                    if ($bookingCount > 0) {
                        $baseShare = (int) ($usage->subtotal / $bookingCount);
                        $remainder = $usage->subtotal % $bookingCount;
                        $shareAmount = $baseShare + ($remainder > 0 ? 1 : 0);

                        $bookings->each(function ($booking) use ($usage, $shareAmount) {
                            $usage->shares()->create([
                                'user_id' => $booking->user_id,
                                'share_amount' => $shareAmount,
                            ]);
                        });
                    }
                }
            });

            return redirect()->back()->with('success', __('Đã ghi nhận sử dụng dịch vụ thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi ghi nhận sử dụng dịch vụ phòng ' . $room->room_code . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Không thể ghi nhận sử dụng dịch vụ. Vui lòng thử lại.'));
        }
    }
}
