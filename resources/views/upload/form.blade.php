@extends('layout')

@section('content')
<div class="header">
  <div>
    <h1>Upload Files</h1>
    <div class="subtitle">อัปโหลดไฟล์ (จำกัด 10MB) — เก็บไว้ที่ <code>/public/upload</code></div>
  </div>
  <div class="actions">
    <span class="badge badge-wide center">
      <strong>Logged in:</strong>&nbsp;{{ $me['username'] ?? '-' }}
    </span>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn" type="submit">Logout</button>
    </form>
  </div>
</div>

@if(session('msg'))
  <div class="card mb-3" style="border-color:#d1fae5">
    <div class="subtitle" style="color:#065f46">{{ session('msg') }}</div>
  </div>
@endif

@if($errors->any())
  <div class="card mb-3" style="border-color:#f8caca">
    <div class="subtitle" style="color:#9b1c1c">
      {{ $errors->first() }}
    </div>
  </div>
@endif

<div class="card" style="max-width:820px; margin-left:auto; margin-right:auto;">
  <form method="post" action="{{ route('upload.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="field">
      <label>เลือกไฟล์ (สูงสุด 10MB)</label>
      <input type="file" name="file" required>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary" type="submit">Upload</button>
      <a class="btn" href="{{ route('employees.index') }}">Back</a>
    </div>
  </form>
</div>

<div class="card mt-4">
  <h2 style="margin-top:0">ไฟล์ที่อัปโหลดแล้ว</h2>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60%">ชื่อไฟล์</th>
          <th style="width:20%">ขนาด (KB)</th>
          <th style="width:20%">เปิดดู</th>
        </tr>
      </thead>
      <tbody>
        @forelse($files as $f)
          <tr>
            <td class="center">{{ $f['name'] }}</td>
            <td class="center">{{ number_format($f['size']/1024, 2) }}</td>
            <td class="center">
              <a class="btn" href="{{ $f['url'] }}" target="_blank" rel="noopener">Open</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="center" style="color:#6b7280">— ยังไม่มีไฟล์ —</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
