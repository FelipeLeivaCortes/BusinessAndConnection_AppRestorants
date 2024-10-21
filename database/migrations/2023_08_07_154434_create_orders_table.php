<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 100);
            $table->decimal('sub_total', 28, 8);
            $table->decimal('discount_percentage', 10, 2);
            $table->decimal('discount', 28, 8);
            $table->decimal('service_charge', 28, 8)->default(0);
            $table->decimal('grand_total', 28, 8);
            $table->decimal('paid', 28, 8)->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 = Pending | 1 = Accepted | 2 = Preparing | 3 = Ready | 4 = Delivered | 5 = Closed | 99 = Cancelled');
            $table->string('payment_method', 50)->nullable();
            $table->string('order_type', 20)->default('table')->comment('table | delivery | takeway | others');
            $table->datetime('delivery_time')->nullable();
            $table->string('table', 191)->nullable();
            $table->string('table_id', 99)->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_email', 100)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('customer_city', 191)->nullable();
            $table->string('customer_state', 191)->nullable();
            $table->string('customer_zip', 50)->nullable();
            $table->text('customer_address')->nullable();
            $table->bigInteger('created_user_id')->nullable();
            $table->bigInteger('updated_user_id')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('business_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('business_id')->references('id')->on('business')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
