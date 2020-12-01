<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableCuisineAndMealItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('cuisine_id')->nullable()->change();
            $table->smallInteger('meal')->comment('1. Breakfast; 2. Lunch; 3. Snack; 4. Dinner')->nullable()->change();
            $table->smallInteger('dish')->after('meal')->comment('1. Main Dish; 2. Appetizer; 3. Dessert; 4. Drinks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('cuisine_id')->nullable(false)->change();
            $table->smallInteger('meal')->comment('1. Breakfast; 2. Lunch; 3. Snack; 4. Dinner')->nullable(false)->change();
            $table->dropColumn('dish');
        });
    }
}
