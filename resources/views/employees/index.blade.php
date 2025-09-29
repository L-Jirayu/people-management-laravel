{{-- resources/views/employees/index.blade.php --}}
@extends('layout')
@section('content')
<div class="header">
  <div>
    <h1>พนักงาน</h1>
    <div class="subtitle">จัดการข้อมูลพนักงาน (CRUD)</div>
  </div>
  <div class="actions">
    <span class="badge">Logged in: {{ $me['username'] ?? '' }}</span>
    <form method="post" action="{{ route('logout') }}">
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

<div class="card">
  <form method="get" class="mb-3" style="display:flex; gap:8px; flex-wrap:wrap">
    <input type="text" name="q" placeholder="ค้นหา emp_code / name / email" value="{{ $q }}" style="max-width:320px">
    <button class="btn" type="submit">Search</button>
    <a class="btn" href="{{ route('employees.index') }}">Reset</a>
    <div style="flex:1"></div>
    <a class="btn" href="{{ route('employees.export.pdf', array_filter(['q'=>$q])) }}" target="_blank" rel="noopener">Export PDF</a>
    <a class="btn btn-primary" href="{{ route('employees.create') }}">+ Add Employee</a>
  </form>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Emp Code</th><th>Name</th><th>Email</th>
          <th>Position</th><th>Salary</th><th>Status</th><th class="right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->emp_code }}</td>
            <td>{{ $r->first_name }} {{ $r->last_name }}</td>
            <td>{{ $r->email }}</td>
            <td>{{ $r->position }}</td>
            <td>{{ $r->salary }}</td>
            <td><span class="badge {{ $r->status==='active'?'':'gray' }}">{{ $r->status }}</span></td>
            <td class="right">
              <a class="btn" href="{{ route('employees.edit', $r) }}">Edit</a>
              <form method="post" action="{{ route('employees.destroy', $r) }}" style="display:inline" onsubmit="return confirm('ลบ #{{ $r->id }} ?');">
                @csrf @method('DELETE')
                <button class="btn btn-danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="center" style="color:var(--muted)">— no data —</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
