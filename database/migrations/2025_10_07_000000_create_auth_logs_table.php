<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auth_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event', 32);              // login_success, login_failed, logout_success, logout_failed
            $table->string('username')->nullable();
            $table->boolean('success')->default(false);
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('message')->nullable();    // ข้อความสั้น ๆ
            $table->timestamp('occurred_at')->useCurrent(); // เก็บเวลาเกิดเหตุ (จะใส่ Asia/Bangkok ให้จากโค้ด)
            $table->timestamps();                     // created_at / updated_at (ตาม timezone ของแอป)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_logs');
    }
};
