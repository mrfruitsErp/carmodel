<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('plate', 20)->nullable();
            $table->string('vin', 17)->nullable();
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->string('version', 150)->nullable();
            $table->year('year');
            $table->unsignedInteger('mileage')->default(0);
            $table->enum('fuel_type', ['benzina','diesel','gpl','metano','elettrico','ibrido_benzina','ibrido_diesel','altro'])->default('benzina');
            $table->enum('transmission', ['manuale','automatico','semiautomatico'])->default('manuale');
            $table->string('color', 80)->nullable();
            $table->string('color_type', 50)->nullable();
            $table->unsignedSmallInteger('doors')->default(5);
            $table->unsignedSmallInteger('seats')->default(5);
            $table->unsignedInteger('engine_cc')->nullable();
            $table->unsignedSmallInteger('power_kw')->nullable();
            $table->unsignedSmallInteger('power_hp')->nullable();
            $table->string('body_type', 50)->nullable();
            $table->enum('condition', ['eccellente','ottimo','buono','discreto','da_riparare'])->default('ottimo');
            $table->unsignedTinyInteger('previous_owners')->default(1);
            $table->date('first_registration')->nullable();
            $table->json('features')->nullable();
            $table->decimal('asking_price', 10, 2);
            $table->decimal('min_price', 10, 2)->nullable();
            $table->boolean('price_negotiable')->default(true);
            $table->boolean('vat_deductible')->default(false);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('internal_notes')->nullable();
            $table->enum('status', ['bozza','attivo','venduto','sospeso','archiviato'])->default('bozza');
            $table->date('available_from')->nullable();
            $table->date('sold_date')->nullable();
            $table->decimal('sold_price', 10, 2)->nullable();
            $table->foreignId('sold_to_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index('tenant_id');
            $table->index('status');
            $table->index(['brand', 'model']);
            $table->index('asking_price');
        });

        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace','mobile_de','olx','autoungle','auto1','quattroruote','autosupermarket','instagram']);
            $table->string('external_id')->nullable();
            $table->string('external_url')->nullable();
            $table->enum('status', ['draft','publishing','published','updating','paused','expired','error','deleted'])->default('draft');
            $table->json('platform_data')->nullable();
            $table->json('platform_config')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('contacts')->default(0);
            $table->unsignedInteger('favorites')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->text('last_error_message')->nullable();
            $table->decimal('listed_price', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['sale_vehicle_id', 'platform']);
            $table->index(['tenant_id', 'platform', 'status']);
            $table->index('external_id');
        });

        Schema::create('marketplace_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace','mobile_de','olx','autoungle','auto1','quattroruote','autosupermarket','instagram','manual']);
            $table->string('lead_name')->nullable();
            $table->string('lead_email')->nullable();
            $table->string('lead_phone', 30)->nullable();
            $table->text('lead_message')->nullable();
            $table->string('external_lead_id')->nullable();
            $table->enum('status', ['nuovo','contattato','appuntamento','trattativa','vinto','perso'])->default('nuovo');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('appointment_at')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
            $table->index(['sale_vehicle_id', 'platform']);
        });

        Schema::create('marketplace_sync_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_listing_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('platform', ['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace','mobile_de','olx','autoungle','auto1','quattroruote','autosupermarket','instagram']);
            $table->enum('action', ['publish','update','delete','sync_stats','fetch_leads','refresh_token']);
            $table->enum('result', ['success','failed','partial']);
            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['tenant_id', 'platform', 'action']);
            $table->index('created_at');
        });

        Schema::create('marketplace_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace','mobile_de','olx','autoungle','auto1','quattroruote','autosupermarket','instagram']);
            $table->boolean('enabled')->default(false);
            $table->text('credentials')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
            $table->unique(['tenant_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_sync_log');
        Schema::dropIfExists('marketplace_leads');
        Schema::dropIfExists('marketplace_listings');
        Schema::dropIfExists('marketplace_credentials');
        Schema::dropIfExists('sale_vehicles');
    }
};