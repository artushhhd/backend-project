<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->string('email')->unique();
            $col->timestamp('email_verified_at')->nullable();
            $col->string('password');

            // Роли: 1 - superadmin, 2 - admin, 3 - moderator, 4 - user
            $col->unsignedTinyInteger('role')->default(4)->index();

            // Иерархия (parent_id)
            $col->foreignId('parent_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $col->rememberToken();
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};  
