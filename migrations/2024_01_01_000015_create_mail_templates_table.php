<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('trigger_event', [
                'claim_opened','cid_expiry_48h','survey_scheduled',
                'job_completed','vehicle_ready','rental_expiry_24h',
                'invoice_overdue','quote_sent','manual'
            ]);
            $table->string('subject', 500);
            $table->longText('body_html');
            $table->text('body_text')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['tenant_id', 'trigger_event']);
        });
        Schema::create('mail_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('mail_templates')->nullOnDelete();
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject', 500);
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['queued','sent','failed','bounced'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_automatic')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->index('status');
            $table->index('customer_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('mail_log');
        Schema::dropIfExists('mail_templates');
    }
};
