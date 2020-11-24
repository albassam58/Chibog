<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vendors')->insert([
            "first_name" => "John",
            "last_name" => "Doe",
            "email_verified_at" => date('Y-m-d H:i:s'),
            "email" => "admin@admin.com",
            "password" => bcrypt("admin"),
            "mobile_number" => "09177828316",
            "region" => "NCR",
            "province" => "NATIONAL CAPITAL REGION - SECOND DISTRICT",
            "city" => "QUEZON CITY",
            "barangay" => "LAGING HANDA",
            "street" => "51 A Scout Fuentebella",
            "api_token" => null,
            "status" => 2
        ]);
    }
}
