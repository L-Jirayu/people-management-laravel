<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));
        $query = Employee::query();

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('emp_code', 'like', "%$q%")
                   ->orWhere('first_name', 'like', "%$q%")
                   ->orWhere('last_name', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            });
        }

        $rows = $query->orderBy('id', 'asc')->get();
        $me = $request->session()->get('user');

        return view('employees.index', compact('rows', 'q', 'me'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'emp_code'   => 'required|string|max:20|unique:employees,emp_code',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:150|unique:employees,email',
            'phone'      => 'nullable|string|max:30',
            'position'   => 'nullable|string|max:100',
            'salary'     => 'nullable|numeric',
            'hired_date' => 'nullable|date',
            'status'     => 'required|in:active,inactive',
        ]);

        $data['salary'] = $data['salary'] ?? 0;
        Employee::create($data);

        return redirect()->route('employees.index');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', ['emp' => $employee]);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'emp_code'   => "required|string|max:20|unique:employees,emp_code,{$employee->id}",
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => "required|email|max:150|unique:employees,email,{$employee->id}",
            'phone'      => 'nullable|string|max:30',
            'position'   => 'nullable|string|max:100',
            'salary'     => 'nullable|numeric',
            'hired_date' => 'nullable|date',
            'status'     => 'required|in:active,inactive',
        ]);

        $data['salary'] = $data['salary'] ?? 0;
        $employee->update($data);

        return redirect()->route('employees.index');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('msg', "ลบแล้ว (#{$employee->id})");
    }

    public function exportPdf(Request $request)
    {
        $q = trim($request->query('q', ''));
        $query = Employee::query();

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('emp_code', 'like', "%$q%")
                   ->orWhere('first_name', 'like', "%$q%")
                   ->orWhere('last_name', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            });
        }

        $rows = $query->orderBy('id', 'asc')->get();

        $generatedAt = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i') . ' UTC+7';

        $pdf = Pdf::loadView('employees.pdf', [
                'rows'         => $rows,
                'q'            => $q,
                'generated_at' => $generatedAt
            ])
            ->setPaper('a4', 'landscape');

        $filename = $q ? "employees_".preg_replace('/\W+/', '_', $q).".pdf" : "employees.pdf";
        return $pdf->stream($filename);
    }
}
