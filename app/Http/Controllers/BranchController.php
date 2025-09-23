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
        $branches = Branch::paginate(10);
        $totalBranches = $branches->total();
        return view('branches.index', compact('branches', 'totalBranches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('branchCreation', [
            'name' => 'required|string|max:255|unique:branches,name',
            'address' => 'nullable|string|max:255|unique:branches,address',
        ]);

        Branch::create($validated);
        return redirect()->route('branches.index')->with('success', 'Chi nhánh đã được tạo thành công');
    }

    public function update(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'address' => 'nullable|string|max:255|unique:branches,address,' . $branch->id,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branches.index')
                ->withErrors($validator, 'branchUpdation')
                ->withInput()
                ->with('update_action', route('branches.update', $branch->id));
        }

        $branch->update($validator->validated());

        return redirect()->route('branches.index')->with('success', 'Chi nhánh đã được cập nhật thành công');
    }

    public function destroy(Branch $branch)
    {
        try {
            $branch->delete();
            return redirect()->route('branches.index')->with('success', 'Chi nhánh đã được xoá thành công');
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? 'Không thể xóa chi nhánh này vì đang được sử dụng'
                : 'Đã xảy ra lỗi khi xoá chi nhánh';
            return redirect()->back()->with('error', $msg);
        }
    }
}
