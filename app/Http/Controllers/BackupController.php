<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected string $backupPath = 'HUIT';

    public function index()
    {
        $files = collect(Storage::disk('local')->files($this->backupPath));

        $backups = $files->map(function ($file) {
            return [
                'name' => basename($file),
                'size' => round(Storage::disk('local')->size($file) / 1048576, 2) . ' MB',
                'date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
            ];
        })->sortByDesc('date');

        return view('backup', compact('backups'));
    }

    public function store()
    {
        Artisan::call('backup:run');
        return back()->with('success', __('Đã tạo bản sao lưu mới!'));
    }

    public function download(string $filename)
    {
        $filename = basename($filename);
        $relativePath = "{$this->backupPath}/{$filename}";

        if (!Storage::disk('local')->exists($relativePath)) {
            return back()->with('warning', __('Không tìm thấy file sao lưu!'));
        }

        try {
            $absolutePath = Storage::disk('local')->path($relativePath);

            if (file_exists($absolutePath)) {
                return response()->download($absolutePath);
            } else {
                return back()->with('warning', __('Không tìm thấy file sao lưu!'));
            }
        } catch (\Exception $e) {
            Log::error('Backup download failed: ' . $e->getMessage());
            return back()->with('error', __('Tải xuống thất bại. Vui lòng thử lại.'));
        }
    }

    public function destroy(string $filename)
    {
        $filename = basename($filename);
        $relativePath = "{$this->backupPath}/{$filename}";

        if (Storage::disk('local')->exists($relativePath)) {
            Storage::disk('local')->delete($relativePath);
            return back()->with('success', __('Đã xóa bản sao lưu!'));
        }

        return back()->with('warning', __('Không tìm thấy file sao lưu!'));
    }
}
