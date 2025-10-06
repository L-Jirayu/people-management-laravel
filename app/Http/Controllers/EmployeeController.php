<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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
            'emp_code'       => 'required|string|max:20|unique:employees,emp_code',
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|max:150|unique:employees,email',
            'phone'          => 'nullable|string|max:30',
            'position'       => 'nullable|string|max:100',
            'salary'         => 'nullable|numeric',
            'hired_date'     => 'nullable|date',
            'status'         => 'required|in:active,inactive',
            'attachments.*'  => 'file|max:10240', // 10MB ต่อไฟล์
        ]);

        $data['salary'] = $data['salary'] ?? 0;
        $data['attachments'] = [];
        $emp = Employee::create($data);

        // 📁 upload: uploads/{id}/
        if ($request->hasFile('attachments')) {
            $path = public_path("uploads/{$emp->id}");
            if (!File::exists($path)) File::makeDirectory($path, 0755, true);

            $files = [];
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($path, $filename);
                $files[] = $filename;

                $now = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s').' UTC+7';
                Log::channel('uploadlog')->info("[{$now}] ✅ Uploaded '{$filename}' by {$data['first_name']} {$data['last_name']}");
            }

            $emp->attachments = $files;
            $emp->save();
        }

        return redirect()->route('employees.index');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', ['emp' => $employee]);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'emp_code'       => "required|string|max:20|unique:employees,emp_code,{$employee->id}",
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => "required|email|max:150|unique:employees,email,{$employee->id}",
            'phone'          => 'nullable|string|max:30',
            'position'       => 'nullable|string|max:100',
            'salary'         => 'nullable|numeric',
            'hired_date'     => 'nullable|date',
            'status'         => 'required|in:active,inactive',
            'attachments.*'  => 'file|max:10240',
            'delete_files'   => 'array',
            'delete_files.*' => 'string',
        ]);

        $data['salary'] = $data['salary'] ?? 0;

        // ⚠️ ห้าม update attachments ตรง ๆ เพราะมันจะถูกล้าง
        unset($data['attachments']);

        // อัปเดตเฉพาะฟิลด์ข้อมูลทั่วไป
        $employee->fill($data);
        $employee->save();

        // ✅ โหลด attachments เดิม (กัน null/array ซ้อน)
        $attachments = collect($employee->attachments ?? [])
            ->flatten()
            ->filter(fn($f) => is_string($f) && $f !== '')
            ->values()
            ->all();

        $path = public_path("uploads/{$employee->id}");
        if (!File::exists($path)) File::makeDirectory($path, 0755, true);

        // 🗑️ ลบไฟล์ที่ติ๊กออก
        if ($request->filled('delete_files')) {
            foreach ($request->input('delete_files', []) as $filename) {
                $filePath = $path . DIRECTORY_SEPARATOR . $filename;
                if (File::exists($filePath)) {
                    File::delete($filePath);
                    $now = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7';
                    Log::channel('uploadlog')->warning("[{$now}] ❌ Deleted '{$filename}' by {$data['first_name']} {$data['last_name']}");
                }
                $attachments = array_values(array_diff($attachments, [$filename]));
            }
        }

        // 📁 เพิ่มไฟล์ใหม่ (ต่อจากเดิม)
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($path, $filename);
                $attachments[] = $filename;

                $now = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s') . ' UTC+7';
                Log::channel('uploadlog')->info("[{$now}] ✅ Uploaded '{$filename}' (update) by {$data['first_name']} {$data['last_name']}");
            }
        }

        // ✅ Clean & Save attachments ใหม่
        $attachments = collect($attachments)
            ->flatten()
            ->unique()
            ->filter(fn($f) => is_string($f) && $f !== '')
            ->values()
            ->all();

        $employee->attachments = $attachments;
        $employee->save();

        return redirect()->route('employees.index');
    }

    public function destroy(Employee $employee)
    {
        $path = public_path("uploads/{$employee->id}");
        if (File::exists($path)) File::deleteDirectory($path);

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
        $generatedAt = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i').' UTC+7';

        $pdf = Pdf::loadView('employees.pdf', [
            'rows'         => $rows,
            'q'            => $q,
            'generated_at' => $generatedAt
        ])->setPaper('a4', 'landscape');

        $filename = $q ? "employees_".preg_replace('/\W+/', '_', $q).".pdf" : "employees.pdf";
        return $pdf->stream($filename);
    }
}
