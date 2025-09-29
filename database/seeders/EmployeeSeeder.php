<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('employees')->count() === 0) {
            DB::table('employees')->insert([
                [
                    'emp_code' => 'E001',
                    'first_name' => 'Alice',
                    'last_name' => 'Wong',
                    'email' => 'alice@example.com',
                    'phone' => '080-111-1111',
                    'position' => 'HR',
                    'salary' => 30000,
                    'hired_date' => '2023-01-10',
                    'status' => 'active'
                ],
                [
                    'emp_code' => 'E002',
                    'first_name' => 'Bob',
                    'last_name' => 'Chan',
                    'email' => 'bob@example.com',
                    'phone' => '080-222-2222',
                    'position' => 'Engineer',
                    'salary' => 45000,
                    'hired_date' => '2024-03-15',
                    'status' => 'active'
                ],
            ]);
        }
    }
}
