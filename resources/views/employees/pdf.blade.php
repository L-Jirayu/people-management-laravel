<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>Employees Report</title>
  <style>
    /* === Thai font (local file) === */
    @font-face{
      font-family:'NotoSansThai';
      src:url("{{ public_path('fonts/NotoSansThai-Regular.ttf') }}") format('truetype');
      font-weight:normal;font-style:normal;
    }
    @font-face{
      font-family:'NotoSansThai';
      src:url("{{ public_path('fonts/NotoSansThai-Bold.ttf') }}") format('truetype');
      font-weight:bold;font-style:normal;
    }

    /* === Page & base === */
    @page { margin: 18px 18px 28px 18px; } /* margin เล็กลงเพราะแนวนอน */
    body{
      font-family: 'NotoSansThai', DejaVu Sans, sans-serif;
      font-size: 12px;            /* ลดลงนิดให้พอดีกระดาษ */
      color: #1f2937;
    }

    .header{
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 8px;
    }
    h1{ margin: 0 0 4px 0; font-size: 18px; }
    .muted{ color:#6b7280; font-size: 10px }

    /* === Table === */
    table{
      width:100%;
      border-collapse: collapse;
      border-spacing:0;
      table-layout: fixed;        /* fixed layout ช่วยให้คอลัมน์นิ่ง */
    }
    th, td{
      border:1px solid #e5e7eb;
      padding:5px 6px;
      vertical-align: top;
      word-break: break-word;     /* ตัดคำยาว (อีเมล/โค้ด) */
      overflow-wrap: anywhere;
    }
    thead th{
      background:#f3f4f6;
      font-size:11px;
      text-align:left;
    }
    .right{text-align:right}

    /* ปรับความกว้างคอลัมน์ให้เข้ากับแนวนอน A4 (~ 280mm พื้นที่ใช้งาน) */
    th.w-id      { width: 32px; }
    th.w-code    { width: 70px; }
    th.w-name    { width: 140px; }
    th.w-email   { width: 170px; }
    th.w-pos     { width: 120px; }
    th.w-salary  { width: 80px; }
    th.w-status  { width: 70px; }

    .badge{
      display:inline-block; padding:2px 8px; border:1px solid #d1fae5;
      background:#ecfdf5; color:#065f46; border-radius:999px;
      font-size:9px; font-weight:700;
    }
    .badge.gray{ background:#f3f4f6; color:#374151; border-color:#e5e7eb }

    footer{
      position: fixed; bottom: 0; left: 0; right: 0;
      font-size: 10px; color:#6b7280; text-align: right;
    }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <h1>Employees</h1>
      <div class="muted">Filter: {{ $q ?: '—' }}</div>
    </div>
    <div class="muted">Generated at: {{ $generated_at }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th class="w-id">ID</th>
        <th class="w-code">Emp Code</th>
        <th class="w-name">Name</th>
        <th class="w-email">Email</th>
        <th class="w-pos">Position</th>
        <th class="w-salary right">Salary</th>
        <th class="w-status">Status</th>
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
        <td class="right">{{ number_format((float)$r->salary, 2) }}</td>
        <td><span class="badge {{ $r->status==='active'?'':'gray' }}">{{ $r->status }}</span></td>
      </tr>
      @empty
      <tr>
        <td colspan="7" style="text-align:center; color:#6b7280">— No data —</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <footer>Page {PAGE_NUM} / {PAGE_COUNT}</footer>
</body>
</html>
