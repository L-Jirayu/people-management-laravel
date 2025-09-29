<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Carbon\Carbon;

class CustomizeAuthLogger
{
    /**
     * เรียกโดย Laravel เพื่อตกแต่ง logger หลังสร้าง channel แล้ว
     */
    public function __invoke($logger)
    {
        // ตั้ง timezone ให้แน่ใจว่าเป็น Asia/Bangkok
        $tz = new \DateTimeZone('Asia/Bangkok');

        // รูปแบบที่ต้องการ:
        // [2025-09-29 21:27:43 UTC+7] local.INFO: ข้อความ
        $format = "[%datetime% UTC+7] %channel%.%level_name%: %message% %context% %extra%\n";

        $formatter = new LineFormatter(
            $format,
            'Y-m-d H:i:s', // รูปแบบ datetime
            true,          // allowInlineLineBreaks
            true           // ignoreEmptyContextAndExtra
        );

        foreach ($logger->getHandlers() as $handler) {
            // ใช้เฉพาะ StreamHandler (single file)
            if ($handler instanceof StreamHandler) {
                // ตั้ง timezone ให้ handler
                $handler->setFormatter($formatter);
                $handler->getFormatter()->setDateTimeFormat('Y-m-d H:i:s');
            }
        }

        // บังคับให้ตัว Logger ใช้ timezone Asia/Bangkok
        if (method_exists($logger, 'setTimezone')) {
            $logger->setTimezone($tz);
        }
    }
}
