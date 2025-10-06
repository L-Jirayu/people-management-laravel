<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole() && $this->isServeCommand()) {
            $this->clearLogs();

            // 🕒 Timestamp สำหรับแสดงใน Console
            $stamp = '[' . now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';
            fwrite(STDOUT, "$stamp ℹ️  Logs cleared (auth.log + upload.log + storage/logs)\n");
        }
    }

    /**
     * ตรวจสอบว่ากำลังรันคำสั่ง php artisan serve อยู่หรือไม่
     */
    private function isServeCommand(): bool
    {
        $argv = $_SERVER['argv'] ?? [];
        return isset($argv[1]) && str_starts_with($argv[1], 'serve');
    }

    /**
     * ล้างไฟล์ log ทั้งหมดที่กำหนด
     */
    private function clearLogs(): void
    {
        $auth = base_path('auth.log');
        $upload = base_path('upload.log');
        $storageLogs = glob(storage_path('logs/*.log')) ?: [];

        // สร้างไฟล์ถ้ายังไม่มี
        foreach ([$auth, $upload] as $file) {
            if (!is_file($file)) {
                @touch($file);
            }
        }

        // ล้างไฟล์หลักสองตัว
        @file_put_contents($auth, '');
        @file_put_contents($upload, '');

        // ล้างทุกไฟล์ใน storage/logs
        foreach ($storageLogs as $file) {
            @file_put_contents($file, '');
        }

        // ✅ เขียนบันทึกลงทั้งสองไฟล์ว่าเคลียร์เรียบร้อยแล้ว
        $stamp = '[' . Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';
        $msg = "$stamp ℹ️  Logs cleared (auth.log + upload.log + storage/logs)\n";

        File::append($auth, $msg);
        File::append($upload, $msg);
    }
}
