<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aggiungi notes solo se non esiste
        if (!Schema::hasColumn('users', 'notes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('role');
            });
        }

        // Registro accessi
        Schema::create('user_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['login', 'logout', 'failed_login']);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_logs');
        if (Schema::hasColumn('users', 'notes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
};