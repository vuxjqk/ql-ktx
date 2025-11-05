<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::filter($request->all())
            ->paginate(10)
            ->appends($request->query());
        $totalServices = $services->total();
        return view('services.index', compact('services', 'totalServices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('serviceCreation', [
            'name' => 'required|string|max:255|unique:services,name',
            'unit' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'free_quota' => 'nullable|numeric|min:0',
            'is_mandatory' => 'boolean',
        ]);

        if (!isset($validated['free_quota'])) {
            $validated['free_quota'] = 0;
        }

        if (!isset($validated['is_mandatory'])) {
            $validated['is_mandatory'] = false;
        }

        Service::create($validated);

        return redirect()->route('services.index')->with('success', __('Đã tạo thành công'));
    }

    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'unit' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'free_quota' => 'nullable|numeric|min:0',
            'is_mandatory' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('services.index')
                ->withErrors($validator, 'serviceUpdation')
                ->withInput()
                ->with('update_action', route('services.update', $service));
        }

        $data = $validator->validated();

        if (!isset($data['free_quota'])) {
            $data['free_quota'] = 0;
        }

        if (!isset($data['is_mandatory'])) {
            $data['is_mandatory'] = false;
        }

        $service->update($data);

        return redirect()->route('services.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(Service $service)
    {
        try {
            $service->delete();

            return redirect()->route('services.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }
}
