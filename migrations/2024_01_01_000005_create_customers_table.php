<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['private', 'company'])->default('private');
            // Privato
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('fiscal_code', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            // Azienda
            $table->string('company_name')->nullable();
            $table->string('vat_number', 30)->nullable();
            $table->string('sdi_code', 10)->nullable();
            $table->string('pec_email')->nullable();
            // Contatti
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('phone2', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            // Indirizzo
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('province', 5)->nullable();
            $table->string('country', 50)->default('IT');
            // Gestionale
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->enum('source', ['walk-in','referral','web','phone','insurance'])->default('walk-in');
            $table->decimal('total_value', 12, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index('tenant_id');
            $table->index('fiscal_code');
            $table->index('vat_number');
        });
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};
