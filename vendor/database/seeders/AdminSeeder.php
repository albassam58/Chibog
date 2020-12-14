<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "admin@admin.com",
            'email_verified_at' => date('Y-m-d H:i:s'),
            'password' => bcrypt('admin'),
            'mobile_number' => "09177828316",
            "region" => "NCR",
            "province" => "NATIONAL CAPITAL REGION - SECOND DISTRICT",
            "city" => "QUEZON CITY",
            "barangay" => "LAGING HANDA",
            "street" => "51 A Scout Fuentebella",
        ]);
    }
}
