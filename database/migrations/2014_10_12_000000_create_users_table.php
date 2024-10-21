<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 191)->unique();
            $table->string('user_type', 20)->comment('admin | user');
            $table->string('membership_type', 50)->nullable()->comment('trial | member');
            $table->bigInteger('package_id')->nullable();
            $table->date('subscription_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('profile_picture', 191)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('state', 191)->nullable();
            $table->string('zip', 30)->nullable();
            $table->text('address')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamp('t_email_send_at')->nullable();
            $table->timestamp('s_email_send_at')->nullable();
            $table->string('provider')->nullable(); // Social Login
            $table->string('provider_id')->nullable(); // Social Login
            $table->text('custom_fields')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
}
