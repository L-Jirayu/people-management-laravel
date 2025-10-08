<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AuthLog;
use Carbon\Carbon;

class AuthController extends Controller
{
    private array $MOCK_USER = [
        'username' => 'admin',
        'password' => '1234',
        'role'     => 'admin',
    ];

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $u = $request->input('username');
        $p = $request->input('password');

        // stamp แบบในไฟล์ log
        $stamp = '[' . now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';

        if ($u === $this->MOCK_USER['username'] && $p === $this->MOCK_USER['password']) {
            // ---- จำลองล็อกอินสำเร็จ
            $request->session()->put('user', [
                'username' => $u,
                'role'     => $this->MOCK_USER['role'],
            ]);

            // เขียนไฟล์ log (auth.log + stdout)
            Log::channel('authlog_stack')->info("$stamp ✅ Login สำเร็จ — {$u}");

            // เขียน DB log
            $this->dbAuthLog(
                event: 'login_success',
                username: $u,
                success: true,
                message: 'Login สำเร็จ',
                request: $request
            );

            return redirect()->route('employees.index');
        }

        // ---- ล้มเหลว
        Log::channel('authlog_stack')->warning("$stamp ❌ ชื่อหรือรหัสผ่านไม่ถูกต้อง — {$u}");

        $this->dbAuthLog(
            event: 'login_failed',
            username: $u,
            success: false,
            message: 'ชื่อหรือรหัสผ่านไม่ถูกต้อง',
            request: $request
        );

        return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        $user = $request->session()->get('user')['username'] ?? '-';
        $stamp = '[' . now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';

        // ล็อกเอาท์
        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ไฟล์ log
        Log::channel('authlog_stack')->info("$stamp ✅ Logout สำเร็จ — {$user}");

        // DB log
        $this->dbAuthLog(
            event: 'logout_success',
            username: $user,
            success: true,
            message: 'Logout สำเร็จ',
            request: $request
        );

        return redirect()->route('login.form');
    }

    /**
     * บันทึกเหตุการณ์ลง DB (Asia/Bangkok)
     */
    private function dbAuthLog(string $event, ?string $username, bool $success, string $message, Request $request): void
    {
        AuthLog::create([
            'event'       => $event,
            'username'    => $username,
            'success'     => $success,
            'ip'          => $request->ip(),
            'user_agent'  => (string) $request->userAgent(),
            'message'     => $message,
            'occurred_at' => Carbon::now('Asia/Bangkok'), // เก็บเป็นเวลา +7 ตามต้องการ
        ]);
    }
}
