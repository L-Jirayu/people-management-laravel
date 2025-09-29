<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        if ($u === $this->MOCK_USER['username'] && $p === $this->MOCK_USER['password']) {
            $request->session()->put('user', [
                'username' => $u,
                'role' => $this->MOCK_USER['role'],
            ]);
            return redirect()->route('employees.index');
        }

        return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
