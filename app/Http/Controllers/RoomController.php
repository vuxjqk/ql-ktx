<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Service;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with('floor.branch')
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalRooms = Room::count();
        $fullRooms = Room::whereRaw('current_occupancy >= capacity')->count();
        $emptyRooms = Room::whereRaw('current_occupancy = 0')->count();
        $missingRooms = Room::whereRaw('current_occupancy < capacity AND current_occupancy > 0')->count();

        $branches = Branch::pluck('name', 'id')->toArray();

        return view('rooms.index', compact(
            'rooms',
            'totalRooms',
            'fullRooms',
            'emptyRooms',
            'missingRooms',
            'branches'
        ));
    }

    public function create()
    {
        $branches = Branch::with('floors')->get();

        $options = [];

        foreach ($branches as $branch) {
            $floors = $branch->floors->mapWithKeys(function ($floor) {
                return [$floor->id => 'Tầng ' . $floor->floor_number];
            })->toArray();

            $options[$branch->name] = $floors;
        }

        $services = Service::pluck('name', 'id')->toArray();

        return view('rooms.create', compact('options', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('floor_id', $request->floor_id);
                })
            ],
            'floor_id' => 'required|exists:floors,id',
            'price_per_day' => 'required|numeric|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:255',
            'current_occupancy' => 'required|integer|min:0|lte:capacity',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'services' => 'array',
            'services.*' => 'exists:services,id',
        ]);

        $room = Room::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('room_images', 'public');

                $room->images()->create(['image_path' => $path]);
            }
        }

        $room->services()->sync($validated['services'] ?? []);

        return redirect()->route('rooms.index')->with('success', __('Đã tạo thành công'));
    }

    public function show(Room $room)
    {
        $room->load(['floor.branch', 'images', 'services', 'amenities', 'activeBookings'])
            ->loadCount('favourites')
            ->loadAvg('reviews', 'rating');

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $room->load(['images', 'services']);

        $branches = Branch::with('floors')->get();

        $options = [];

        foreach ($branches as $branch) {
            $floors = $branch->floors->mapWithKeys(function ($floor) {
                return [$floor->id => 'Tầng ' . $floor->floor_number];
            })->toArray();

            $options[$branch->name] = $floors;
        }

        $services = Service::pluck('name', 'id')->toArray();

        return view('rooms.edit', compact('room', 'options', 'services'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('floor_id', $request->floor_id);
                })->ignore($room->id)
            ],
            'floor_id' => 'required|exists:floors,id',
            'price_per_day' => 'required|numeric|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:255',
            'current_occupancy' => 'required|integer|min:0|lte:capacity',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(Room $room)
    {
        try {
            $paths = $room->images->pluck('image_path')->toArray();

            $room->images()->delete();
            $room->services()->detach();
            $room->delete();

            if ($paths) {
                Storage::disk('public')->delete($paths);
            }

            return redirect()->route('rooms.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }

    public function storeImages(Request $request, Room $room)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('room_images', 'public');

                $room->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->back()->with('success', __('Đã tạo thành công'));
    }

    public function destroyImage(RoomImage $image)
    {
        try {
            $path = $image->image_path;

            $image->delete();

            if ($path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }

    public function updateServices(Request $request, Room $room)
    {
        $validated = $request->validate([
            'services' => 'array',
            'services.*' => 'exists:services,id',
        ]);

        $room->services()->sync($validated['services'] ?? []);

        return redirect()->back()->with('success', __('Đã cập nhật thành công'));
    }
}
