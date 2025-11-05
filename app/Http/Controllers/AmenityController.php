<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $amenities = Amenity::filter($request->all())
            ->paginate(10)
            ->appends($request->query());
        $totalAmenities = $amenities->total();
        return view('amenities.index', compact('amenities', 'totalAmenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('amenityCreation', [
            'name' => 'required|string|max:255|unique:amenities,name',
            'description' => 'nullable|string',
        ]);

        Amenity::create($validated);

        return redirect()->route('amenities.index')->with('success', __('Đã tạo thành công'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:amenities,name,' . $amenity->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('amenities.index')
                ->withErrors($validator, 'amenityUpdation')
                ->withInput()
                ->with('update_action', route('amenities.update', $amenity));
        }

        $data = $validator->validated();

        $amenity->update($data);

        return redirect()->route('amenities.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(Amenity $amenity)
    {
        try {
            $amenity->delete();

            return redirect()->route('amenities.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }
}
