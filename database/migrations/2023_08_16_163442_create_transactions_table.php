<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->datetime('trans_date');
            $table->bigInteger('transaction_category_id')->unsigned()->nullable();
            $table->string('dr_cr', 2);
            $table->string('type', 20)->comment('income|expense|transfer|others');
            $table->decimal('amount', 28, 8);
            $table->string('method', 100)->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->bigInteger('ref_id')->nullable();
            $table->string('ref_type')->nullable();
            $table->bigInteger('employee_id')->nullable();
            $table->bigInteger('vendor_id')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('business_id')->unsigned();
            $table->bigInteger('created_user_id')->nullable();
            $table->bigInteger('updated_user_id')->nullable();
            $table->timestamps();

            $table->foreign('transaction_category_id')->references('id')->on('transaction_categories')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
