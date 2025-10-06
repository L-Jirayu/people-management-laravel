{{-- resources/views/employees/edit.blade.php --}}
@extends('layout')
@section('content')
<div class="header">
  <div>
    <h1>แก้ไขข้อมูลพนักงาน</h1>
    <div class="subtitle">ปรับปรุงข้อมูลพนักงานที่มีอยู่</div>
  </div>
  <div class="actions">
    <a class="btn" href="{{ route('employees.index') }}">← Back</a>
  </div>
</div>

<div class="card" style="max-width:1040px; margin:auto;">
  @if ($errors->any())
    <div class="subtitle" style="color:#9b1c1c; margin-bottom:12px">กรอกข้อมูลให้ถูกต้อง</div>
  @endif

  <form method="post" action="{{ route('employees.update', $emp->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row-3">
      <div class="field">
        <label>Emp Code *</label>
        <input type="text" name="emp_code" value="{{ old('emp_code', $emp->emp_code) }}" required>
      </div>
      <div class="field">
        <label>First Name *</label>
        <input type="text" name="first_name" value="{{ old('first_name', $emp->first_name) }}" required>
      </div>
      <div class="field">
        <label>Last Name *</label>
        <input type="text" name="last_name" value="{{ old('last_name', $emp->last_name) }}" required>
      </div>
    </div>

    <div class="row">
      <div class="field">
        <label>Email *</label>
        <input type="email" name="email" value="{{ old('email', $emp->email) }}" required>
      </div>
      <div class="field">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $emp->phone) }}">
      </div>
    </div>

    <div class="row">
      <div class="field">
        <label>Position</label>
        <input type="text" name="position" value="{{ old('position', $emp->position) }}">
      </div>
      <div class="field">
        <label>Salary</label>
        <input type="number" step="0.01" name="salary" value="{{ old('salary', $emp->salary) }}">
      </div>
    </div>

    <div class="row">
      <div class="field">
        <label>Hired Date</label>
        <input type="date" name="hired_date" value="{{ old('hired_date', $emp->hired_date ? $emp->hired_date->format('Y-m-d') : '') }}">
      </div>
      <div class="field">
        <label>Status</label>
        <select name="status">
          <option value="active"  {{ old('status', $emp->status)==='active'?'selected':'' }}>active</option>
          <option value="inactive"{{ old('status', $emp->status)==='inactive'?'selected':'' }}>inactive</option>
        </select>
      </div>
    </div>

    {{-- ★ รวม “ไฟล์ที่เคยอัปโหลด” + “ติ๊กเพื่อลบ” + “แนบไฟล์เพิ่ม” ไว้ที่เดียว --}}
    <div class="field">
      <label>ไฟล์ที่เคยอัปโหลด</label>
      @php
        // ให้แน่ใจว่าเป็น array เสมอ
        $files = (array) ($emp->attachments ?? []);
      @endphp

      @if (count($files))
        <ul style="margin:0; padding-left:18px;">
          @foreach($files as $f)
            @php
              // กันเคสที่ element ไม่ใช่ string (เช่น เป็น array/object)
              $fname = is_array($f) ? ($f['name'] ?? json_encode($f)) : (string)$f;
            @endphp
            <li style="margin-bottom:6px;">
              <a href="{{ asset('uploads/'.$emp->id.'/'.$fname) }}" target="_blank">{{ $fname }}</a>
              <label style="margin-left:8px; font-size:12px; color:#b91c1c;">
                <input type="checkbox" name="delete_files[]" value="{{ $fname }}"> ลบไฟล์นี้
              </label>
            </li>
          @endforeach
        </ul>
      @else
        <div class="subtitle" style="font-size:13px; color:#6b7280;">— ไม่มีไฟล์ —</div>
      @endif
    </div>

    <div class="field">
      <label>แนบไฟล์เพิ่มเติม (หลายไฟล์ได้, จำกัด 10MB ต่อไฟล์)</label>
      <input type="file" name="attachments[]" multiple>
      <div class="subtitle" style="font-size:12px; color:#6b7280; margin-top:4px">
        หากติ๊ก “ลบไฟล์นี้” ไฟล์จะถูกลบทันทีเมื่อกด Update; การอัปโหลดใหม่จะถูกเพิ่มต่อจากของเดิม
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary" type="submit">Update</button>
      <a class="btn" href="{{ route('employees.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
