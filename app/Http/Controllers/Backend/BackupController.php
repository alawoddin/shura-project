<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use RuntimeException;

class BackupController extends Controller
{
    public function index(BackupService $backups)
    {
        $files = $backups->list();

        return view('admin.pages.backup.index', compact('files'));
    }

    public function store(BackupService $backups)
    {
        try {
            $name = $backups->create();

            $notification = [
                'message' => 'بکاپ با موفقیت گرفته شد: '.$name,
                'alert-type' => 'success',
            ];
        } catch (RuntimeException $exception) {
            report($exception);

            $notification = [
                'message' => $exception->getMessage(),
                'alert-type' => 'error',
            ];
        } catch (\Throwable $exception) {
            report($exception);

            $notification = [
                'message' => 'گرفتن بکاپ ناموفق بود. دوباره تلاش کنید.',
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('admin.backup')->with($notification);
    }

    public function download(string $filename, BackupService $backups)
    {
        return response()->download($backups->path($filename), $filename);
    }

    public function destroy(string $filename, BackupService $backups)
    {
        try {
            $backups->delete($filename);

            $notification = [
                'message' => 'بکاپ حذف شد.',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $exception) {
            report($exception);

            $notification = [
                'message' => 'حذف بکاپ ناموفق بود.',
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('admin.backup')->with($notification);
    }
}
