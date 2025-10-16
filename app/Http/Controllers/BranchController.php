<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::with('floors')->paginate(10);

        $totalBranches = $branches->total();

        return view('branches.index', compact('branches', 'totalBranches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('branchCreation', [
            'name' => 'required|string|max:255|unique:branches,name',
            'address' => 'nullable|string|max:255',
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', __('Đã tạo thành công'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branches.index')
                ->withErrors($validator, 'branchUpdation')
                ->withInput()
                ->with('update_action', route('branches.update', $branch));
        }

        $branch->update($validator->validated());

        return redirect()->route('branches.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(Branch $branch)
    {
        try {
            $branch->delete();

            return redirect()->route('branches.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }
}
