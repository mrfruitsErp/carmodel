<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['admin','manager','receptionist','mechanic','sales'])->default('receptionist')->after('email');
            $table->string('phone', 30)->nullable()->after('role');
            $table->string('avatar_path')->nullable()->after('phone');
            $table->boolean('active')->default(true)->after('avatar_path');
            $table->timestamp('last_login_at')->nullable()->after('active');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tenant_id','role','phone','avatar_path','active','last_login_at']);
        });
    }
};
