<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class CustomizeAuthLogger
{
    /**
     * Tap the given logger instance.
     *
     * NOTE (Monolog v3):
     * - ไม่มี setDateTimeFormat() แล้ว
     * - timezone จะอิงตาม config('app.timezone') ที่ Laravel set ไว้ด้วย date_default_timezone_set
     */
    public function __invoke($logger): void
    {
        // ต้องการรูปแบบ:
        // [YYYY-MM-DD HH:MM:SS UTC+7] local.INFO: ข้อความ ...
        $format = "[%datetime% UTC+7] %channel%.%level_name%: %message% %context% %extra%\n";

        // กำหนด date format ผ่าน constructor (ถูกกับ Monolog v3)
        $formatter = new LineFormatter(
            $format,
            'Y-m-d H:i:s', // date format
            true,          // allowInlineLineBreaks
            true           // ignoreEmptyContextAndExtra
        );

        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof StreamHandler) {
                $handler->setFormatter($formatter);
            }
        }

        // ไม่ต้อง setTimezone ใน logger แล้ว (Monolog v3 ตัดทิ้ง)
        // ให้ตั้ง timezone ที่ config/app.php => 'timezone' => 'Asia/Bangkok'
    }
}
