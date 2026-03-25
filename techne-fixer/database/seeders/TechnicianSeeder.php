<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class TechnicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Tom',
            'email' => 'tester@gmail.com',
            'password' => Hash::make('Technician@123'),
            'role' => 'technician',
            'status' => 'active'
        ]);
    }
}
