<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Siti Aisyah',
                'email' => 'kasir@laundry.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'phone' => '081234567891',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'gender' => 'P',
                'position' => 'Kasir',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'kurir@laundry.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'phone' => '081234567892',
                'address' => 'Jl. Sudirman No. 25, Jakarta',
                'gender' => 'L',
                'position' => 'Kurir',
            ],
            [
                'name' => 'Ahmad Reza',
                'email' => 'cuci@laundry.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'phone' => '081234567893',
                'address' => 'Jl. Gatot Subroto No. 40, Jakarta',
                'gender' => 'L',
                'position' => 'Staff Cuci',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'setrika@laundry.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'phone' => '081234567894',
                'address' => 'Jl. Thamrin No. 15, Jakarta',
                'gender' => 'P',
                'position' => 'Staff Setrika',
            ]
        ];

        foreach ($employees as $employeeData) {
            User::firstOrCreate(
                ['email' => $employeeData['email']], 
                $employeeData
            );
        }
    }
}
