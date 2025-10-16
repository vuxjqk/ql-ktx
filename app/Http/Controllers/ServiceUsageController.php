<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceUsageController extends Controller
{
    public function edit(Room $room)
    {
        $room->load(['services.serviceUsages', 'floor.branch']);
        return view('service_usages.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'services'                => 'required|array|min:1',
            'services.*.service_id'   => 'required|exists:services,id',
            'services.*.usage_amount' => 'required|numeric|min:0',
        ]);

        try {
            $services = Service::whereIn('id', collect($validated['services'])->pluck('service_id'))->get()->keyBy('id');

            DB::transaction(function () use ($validated, $services, $room) {
                foreach ($validated['services'] as $input) {
                    $service = $services->get($input['service_id']);

                    $room->serviceUsages()->updateOrCreate([
                        'service_id' => $input['service_id'],
                        'usage_date' => today()->startOfMonth(),
                    ], [
                        'usage_amount' => $input['usage_amount'],
                        'unit_price'   => $service->unit_price,
                        'subtotal'     => $input['usage_amount'] * $service->unit_price,
                    ]);
                }
            });

            return redirect()->back()->with('success', __('Đã ghi nhận sử dụng dịch vụ thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi ghi nhận sử dụng dịch vụ phòng ' . $room->room_code . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Không thể ghi nhận sử dụng dịch vụ. Vui lòng thử lại.'));
        }
    }
}
