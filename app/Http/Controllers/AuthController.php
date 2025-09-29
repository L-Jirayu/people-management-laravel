<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // timestamp แบบ UTC+7 ในรูปแบบ [YYYY-MM-DD HH:MM:SS UTC+7]
        $stamp = '[' . now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';

        if ($u === $this->MOCK_USER['username'] && $p === $this->MOCK_USER['password']) {
            $request->session()->put('user', [
                'username' => $u,
                'role'     => $this->MOCK_USER['role'],
            ]);

            Log::channel('authlog_stack')->info("$stamp ✅ Login สำเร็จ — {$u}");

            return redirect()->route('employees.index');
        }

        Log::channel('authlog_stack')->warning("$stamp ❌ ชื่อหรือรหัสผ่านไม่ถูกต้อง — {$u}");

        return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        $user = $request->session()->get('user')['username'] ?? '-';
        $stamp = '[' . now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7]';

        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::channel('authlog_stack')->info("$stamp ✅ Logout สำเร็จ — {$user}");

        return redirect()->route('login.form');
    }
}
