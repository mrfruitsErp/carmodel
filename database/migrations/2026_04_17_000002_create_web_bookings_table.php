<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_vehicle_id')->nullable()->constrained('fleet_vehicles')->nullOnDelete();
            $table->string('type', 20)->default('noleggio'); // noleggio | contatto
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['nuova','confermata','rifiutata','annullata'])->default('nuova');
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_bookings');
    }
};