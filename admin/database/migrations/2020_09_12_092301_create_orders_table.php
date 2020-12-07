<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('item_id');
            $table->double('amount', 12, 2);
            $table->double('quantity', 12, 2);
            $table->text('special_instruction')->nullable();
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_region');
            $table->string('customer_province');
            $table->string('customer_city');
            $table->string('customer_barangay');
            $table->string('customer_street');
            $table->string('customer_mobile_number');
            $table->string('customer_email');
            $table->smallInteger('status')->comment('1. Pending; 2. Processing; 3. For Delivery; 4. Delivered');
            $table->smallInteger('rated')->default('0')->comment('0. If not rated; 1. If rated')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
