<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Floor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FloorController extends Controller
{
    public function store(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'floor_number' => [
                'required',
                'integer',
                'min:0',
                'max:255',
                Rule::unique('floors')->where(fn($query) => $query->where('branch_id', $branch->id))
            ],
            'gender_type' => 'required|in:male,female,mixed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branches.index')
                ->withErrors($validator, 'floorCreation')
                ->withInput()
                ->with('create_action', route('floors.store', $branch));
        }

        $validated = $validator->validated();
        $validated['branch_id'] = $branch->id;

        Floor::create($validated);

        return redirect()->route('branches.index')->with('success', __('Đã tạo thành công'));
    }

    public function update(Request $request, Floor $floor)
    {
        $validator = Validator::make($request->all(), [
            'floor_number' => [
                'required',
                'integer',
                'min:0',
                'max:255',
                Rule::unique('floors')->where(fn($query) => $query->where('branch_id', $floor->branch_id))->ignore($floor->id)
            ],
            'gender_type' => 'required|in:male,female,mixed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branches.index')
                ->withErrors($validator, 'floorUpdation')
                ->withInput()
                ->with('update_action', route('floors.update', $floor));
        }

        $floor->update($validator->validated());

        return redirect()->route('branches.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(Floor $floor)
    {
        try {
            $floor->delete();

            return redirect()->route('branches.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }

    public function getByBranch($branchId)
    {
        return Floor::where('branch_id', $branchId)->pluck('floor_number', 'id');
    }
}
