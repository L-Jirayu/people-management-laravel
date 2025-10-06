<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function form(Request $request)
    {
        $me = $request->session()->get('user');
        // ดึงรายชื่อไฟล์ใน public/upload เพื่อแสดงผล
        $files = [];
        $dir = public_path('upload');
        if (is_dir($dir)) {
            foreach (scandir($dir) as $f) {
                if ($f === '.' || $f === '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $f;
                if (is_file($path)) {
                    $files[] = [
                        'name' => $f,
                        'url'  => asset('upload/' . $f),
                        'size' => filesize($path),
                    ];
                }
            }
        }

        return view('uploads.form', compact('me', 'files'));
    }

    public function store(Request $request)
    {
        // จำกัด 10MB = 10240 KB
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ], [
            'file.max' => 'ขนาดไฟล์ต้องไม่เกิน 10MB',
        ]);

        $user = $request->session()->get('user');
        $username = $user['username'] ?? 'guest';

        // สร้างโฟลเดอร์ public/upload ถ้ายังไม่มี
        $uploadDir = public_path('upload');
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $file = $validated['file'];
        $originalName = $file->getClientOriginalName();
        $ext  = $file->getClientOriginalExtension();
        // ตั้งชื่อไฟล์ใหม่ กันชื่อชนกัน/แปลก
        $safeBase = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBase = Str::slug($safeBase, '_');
        $newName  = $safeBase . '_' . now('Asia/Bangkok')->format('Ymd_His') . ($ext ? '.'.$ext : '');

        try {
            $file->move($uploadDir, $newName);

            // สร้างข้อความ log แบบเดียวกับสไตล์ authlog ที่คุณชอบ
            $ts = now('Asia/Bangkok')->format('Y-m-d H:i:s'); // +7
            $sizeKB = number_format(filesize($uploadDir.DIRECTORY_SEPARATOR.$newName)/1024, 2);

            // ใช้ channel stack เพื่อให้เห็นทั้งในไฟล์ upload.log และ stdout
            Log::channel('uploadlog_stack')->info(
                "[{$ts} UTC+7] ✅ Upload สำเร็จ — {$username} — {$originalName} → /upload/{$newName} ({$sizeKB} KB)"
            );

            return back()->with('msg', "อัปโหลดสำเร็จ: {$originalName}");
        } catch (\Throwable $e) {
            $ts = now('Asia/Bangkok')->format('Y-m-d H:i:s');
            Log::channel('uploadlog_stack')->warning(
                "[{$ts} UTC+7] ❌ Upload ล้มเหลว — {$username} — {$originalName} — {$e->getMessage()}"
            );
            return back()->withErrors(['file' => 'อัปโหลดไม่สำเร็จ: '.$e->getMessage()]);
        }
    }
}
