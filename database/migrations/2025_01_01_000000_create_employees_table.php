<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $t) {
            $t->id();
            $t->string('emp_code', 20)->unique();
            $t->string('first_name', 100);
            $t->string('last_name', 100);
            $t->string('email', 150)->unique();
            $t->string('phone', 30)->nullable();
            $t->string('position', 100)->nullable();
            $t->decimal('salary', 12, 2)->default(0);
            $t->date('hired_date')->nullable();
            $t->enum('status', ['active', 'inactive'])->default('active');
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
