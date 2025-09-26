<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Room;
use App\Models\RoomAssignment;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UtilityController extends Controller
{
    public function create(Room $room)
    {
        $lastUtility = Utility::where('room_id', $room->id)
            ->orderBy('month', 'desc')
            ->first();

        return view('utilities.create', compact('room', 'lastUtility'));
    }

    public function store(Request $request, Room $room)
    {
        $request->merge([
            'month' => $request->input('month') . '-01',
        ]);

        $validated = $request->validate([
            'month' => [
                'required',
                'date',
                Rule::unique('utilities', 'month')->where(function ($query) use ($room) {
                    return $query->where('room_id', $room->id);
                }),
            ],
            'electric_usage' => 'required|numeric|min:0',
            'water_usage' => 'required|numeric|min:0',
            'electric_cost' => 'required|numeric|min:0',
            'water_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['room_id'] = $room->id;

        $utility = Utility::create($validated);

        $assignments = RoomAssignment::where('room_id', $room->id)
            ->whereNull('checked_out_at')
            ->get();

        $userCount = $assignments->count();

        if ($userCount === 0) {
            return redirect()->back()->with('warning', 'Không có người dùng nào đang thuê phòng này.');
        }

        $month = Carbon::parse($validated['month']);
        $electricPerUser = intdiv($utility->electric_cost, $userCount);
        $waterPerUser = intdiv($utility->water_cost, $userCount);
        $dueDate = $month->copy()->endOfMonth()->addDays(7);

        foreach ($assignments as $assignment) {
            $totalAmount = $room->price_per_month + $electricPerUser + $waterPerUser;

            $bill = Bill::create([
                'code' => $this->generateCode(),
                'user_id' => $assignment->user_id,
                'room_assignment_id' => $assignment->id,
                'amount' => $totalAmount,
                'status' => 'pending',
                'due_date' => $dueDate,
            ]);

            $bill->items()->createMany([
                [
                    'type' => 'Tiền phòng',
                    'amount' => $room->price_per_month,
                    'description' => 'Phí thuê phòng tháng ' . $month->format('m/Y'),
                ],
                [
                    'type' => 'Điện',
                    'amount' => $electricPerUser,
                    'description' => 'Tiền điện tháng ' . $month->format('m/Y'),
                ],
                [
                    'type' => 'Nước',
                    'amount' => $waterPerUser,
                    'description' => 'Tiền nước tháng ' . $month->format('m/Y'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Tiện ích và hóa đơn đã được tạo thành công.');
    }

    public function update(Request $request, Utility $utility)
    {
        $room = $utility->room;

        $request->merge([
            'last_month' => $request->input('last_month') . '-01',
        ]);

        $validated = $request->validate([
            'last_month' => [
                'required',
                'date',
                Rule::unique('utilities', 'month')
                    ->where('room_id', $room->id)
                    ->ignore($utility->id),
            ],
            'last_electric_usage' => 'required|numeric|min:0',
            'last_water_usage' => 'required|numeric|min:0',
            'last_electric_cost' => 'required|numeric|min:0',
            'last_water_cost' => 'required|numeric|min:0',
            'last_notes' => 'nullable|string',
        ]);

        $utility->update([
            'room_id' => $room->id,
            'month' => $validated['last_month'],
            'electric_usage' => $validated['last_electric_usage'],
            'water_usage' => $validated['last_water_usage'],
            'electric_cost' => $validated['last_electric_cost'],
            'water_cost' => $validated['last_water_cost'],
            'notes' => $validated['last_notes'],
        ]);

        return redirect()->back()->with('success', 'Bản ghi tiện ích đã được cập nhật thành công');
    }

    public function destroy(Utility $utility)
    {
        try {
            $utility->delete();
            return redirect()->back()->with('success', 'Bản ghi tiện ích đã được xoá thành công');
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? 'Không thể xóa bản ghi này vì đang được sử dụng'
                : 'Đã xảy ra lỗi khi xoá bản ghi';
            return redirect()->back()->with('error', $msg);
        }
    }

    private function generateCode()
    {
        $date = now()->format('ymdHis');
        $countToday = Bill::whereDate('created_at', today())->count();
        return 'HD' . $date . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    }
}
