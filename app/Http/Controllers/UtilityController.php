<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Utility;
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
                Rule::unique('utilities', 'month')->where('room_id', $room->id)
            ],
            'electric_usage' => 'required|numeric|min:0',
            'water_usage' => 'required|numeric|min:0',
            'electric_cost' => 'required|numeric|min:0',
            'water_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['room_id'] = $room->id;

        Utility::create($validated);

        return redirect()->back()->with('success', 'Bản ghi tiện ích đã được tạo thành công');
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
}
