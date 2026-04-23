<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->json('custom_permissions')->nullable()->after('role');
            $table->string('phone', 30)->nullable()->after('custom_permissions');
            $table->string('avatar_path')->nullable()->after('phone');
            $table->text('notes')->nullable()->after('avatar_path');
            $table->timestamp('last_login_at')->nullable()->after('notes');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['custom_permissions','phone','avatar_path','notes','last_login_at']);
        });
    }
};