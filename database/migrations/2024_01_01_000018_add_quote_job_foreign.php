<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign('converted_to_job_id')
                  ->references('id')->on('work_orders')
                  ->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['converted_to_job_id']);
        });
    }
};