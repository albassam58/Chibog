<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('name');
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('region');
            $table->string('province');
            $table->string('city');
            $table->string('barangay');
            $table->string('street');
            $table->integer('type')->comment('1. Same Day Delivery; 2. Next Day Delivery');
            $table->string('schedule_day');
            $table->time('schedule_time_in')->nullable();
            $table->time('schedule_time_out')->nullable();
            $table->double('rate', 12, 2)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
