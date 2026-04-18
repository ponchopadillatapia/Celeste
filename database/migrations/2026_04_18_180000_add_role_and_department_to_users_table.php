<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('residente'); // admin, residente
            $table->string('department')->nullable();
            $table->string('email_verification_code', 6)->nullable();
            $table->string('password_reset_code', 6)->nullable();
            $table->timestamp('password_reset_code_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'email_verification_code', 'password_reset_code', 'password_reset_code_expires_at']);
        });
    }
};
