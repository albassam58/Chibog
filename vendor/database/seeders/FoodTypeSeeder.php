<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('food_types')->insert([
        	[
	        	'name' => 'Meats'
	        ], [
	        	'name' => 'Seafoods'
	        ], [
	        	'name' => 'Pasta'
	        ], [
	        	'name' => 'Noodles'
	        ], [
	        	'name' => 'Vegetables'
	        ], [
	        	'name' => 'Fruits'
	        ], [
	        	'name' => 'Dairy'
	        ], [
	        	'name' => 'Rice, Breads, and Cookies'
	        ], [
	        	'name' => 'Drinks'
	        ]
	    ]);
    }
}
