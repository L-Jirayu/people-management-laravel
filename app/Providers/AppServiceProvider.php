<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

            // ยิงบรรทัดยืนยันไป stdout ให้เห็นทันทีว่าเคลียร์แล้ว
            $stamp = '[' . now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';
            fwrite(STDOUT, "$stamp ℹ️  Logs cleared (auth.log + storage/logs)\n");
        }
    }

    private function isServeCommand(): bool
    {
        $argv = $_SERVER['argv'] ?? [];
        return isset($argv[1]) && str_starts_with($argv[1], 'serve');
    }

    private function clearLogs(): void
    {
        $auth = base_path('auth.log');
        if (is_file($auth)) {
            @file_put_contents($auth, '');
        }

        foreach (glob(storage_path('logs/*.log')) ?: [] as $file) {
            @file_put_contents($file, '');
        }

        if (!is_file($auth)) {
            @touch($auth);
        }
    }
}
