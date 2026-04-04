<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('province', 5)->nullable();
            $table->string('vat_number', 30)->nullable();
            $table->string('fiscal_code', 20)->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('plan', ['starter', 'professional', 'enterprise'])->default('starter');
            $table->boolean('active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tenants'); }
};
