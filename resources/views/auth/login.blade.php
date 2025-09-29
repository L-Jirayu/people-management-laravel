@extends('layout')
@section('content')
<div class="card" style="max-width:720px; margin:48px auto 0">
  <div class="header">
    <h1>เข้าสู่ระบบ</h1>
    <div class="subtitle">ต้องการทราบรหัสผ่านติดต่อ Admin</div>
  </div>

  @error('login')
    <div class="subtitle" style="color:#9b1c1c; margin-bottom:12px">{{ $message }}</div>
  @enderror

  <form method="post" action="{{ route('login.do') }}" class="mt-3">
    @csrf
    <div class="field">
      <label>Username</label>
      <input type="text" name="username" value="{{ old('username') }}" required>
    </div>
    <div class="field">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary" type="submit">Login</button>
    </div>
  </form>
</div>
@endsection
