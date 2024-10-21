<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->integer('table_no');
            $table->bigInteger('hall_id')->unsigned();
            $table->string('type', 20);
            $table->integer('chair_limit');
            $table->text('css')->nullable();
            $table->tinyInteger('status')->default(0)->comment('Live Status Related with Orders and Reservations');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('business_id')->unsigned();
            $table->timestamps();

            $table->foreign('hall_id')->references('id')->on('halls')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('business_id')->references('id')->on('business')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('tables');
    }
};
