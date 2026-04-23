<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('claim_status_histories')) {
            Schema::create('claim_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('claim_id')->constrained('claims')->cascadeOnDelete();
                $table->string('status');
                $table->text('notes')->nullable();
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('claim_status_histories');
    }
};