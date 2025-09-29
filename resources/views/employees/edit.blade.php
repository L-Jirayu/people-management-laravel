{{-- resources/views/employees/edit.blade.php --}}
@extends('layout')
@section('content')
<div class="header">
  <div>
    <h1>Edit Employee #{{ $emp->id }}</h1>
    <div class="subtitle">ปรับปรุงข้อมูลพนักงาน</div>
  </div>
  <div class="actions">
    <a class="btn" href="{{ route('employees.index') }}">← Back</a>
  </div>
</div>

<div class="card" style="max-width:1040px">
  @if ($errors->any())
    <div class="subtitle" style="color:#9b1c1c; margin-bottom:12px">กรอกข้อมูลให้ถูกต้อง</div>
  @endif

  <form method="post" action="{{ route('employees.update', $emp) }}">
    @csrf @method('PUT')
    <div class="row-3">
      <div class="field">
        <label>Emp Code *</label>
        <input type="text" name="emp_code" value="{{ old('emp_code',$emp->emp_code) }}" required>
      </div>
      <div class="field">
        <label>First Name *</label>
        <input type="text" name="first_name" value="{{ old('first_name',$emp->first_name) }}" required>
      </div>
      <div class="field">
        <label>Last Name *</label>
        <input type="text" name="last_name" value="{{ old('last_name',$emp->last_name) }}" required>
      </div>
    </div>

    <div class="row">
      <div class="field">
        <label>Email *</label>
        <input type="email" name="email" value="{{ old('email',$emp->email) }}" required>
      </div>
      <div class="field">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone',$emp->phone) }}">
      </div>
    </div>

    <div class="row">
      <div class="field">
        <label>Position</label>
        <input type="text" name="position" value="{{ old('position',$emp->position) }}">
      </div>
      <div class="field">
        <label>Salary</label>
        <input type="number" step="0.01" name="salary" value="{{ old('salary',$emp->salary) }}">
      </div>
    </div>

    <div class="row">
      <div class="field">
        <label>Hired Date</label>
        <input type="date" name="hired_date" value="{{ old('hired_date',$emp->hired_date) }}">
      </div>
      <div class="field">
        <label>Status</label>
        <select name="status">
          <option value="active"  {{ old('status',$emp->status)==='active'?'selected':'' }}>active</option>
          <option value="inactive"{{ old('status',$emp->status)==='inactive'?'selected':'' }}>inactive</option>
        </select>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary" type="submit">Update</button>
      <a class="btn" href="{{ route('employees.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
