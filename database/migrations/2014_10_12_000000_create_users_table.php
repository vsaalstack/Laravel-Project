<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->boolean('is_admin')->default(0);
            $table->boolean('super_admin')->default(0);
            $table->boolean('status')->default(0);
            $table->string('login_method',20)->default('regular');
            $table->string('role')->default('agent');
            $table->boolean('term')->default(0);
            $table->string('subscription',100)->default(0);
            $table->string('chargebee_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('timezone')->default('Australia/Sydney');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
