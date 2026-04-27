<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aggiunge tracking anti-spam ai web_bookings:
     *  - is_spam     → flag automatico "questo messaggio è probabilmente spam"
     *  - spam_reason → motivo della classificazione (per debug e training)
     *  - ip_address  → IP del visitatore (per rate-limit, blacklist, audit)
     *  - user_agent  → browser usato (utile per identificare bot)
     */
    public function up(): void
    {
        Schema::table('web_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('web_bookings', 'is_spam')) {
                $table->boolean('is_spam')->default(false)->index()->after('status');
            }
            if (!Schema::hasColumn('web_bookings', 'spam_reason')) {
                $table->string('spam_reason', 100)->nullable()->after('is_spam');
            }
            if (!Schema::hasColumn('web_bookings', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->index()->after('spam_reason');
            }
            if (!Schema::hasColumn('web_bookings', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('web_bookings', function (Blueprint $table) {
            foreach (['is_spam','spam_reason','ip_address','user_agent'] as $c) {
                if (Schema::hasColumn('web_bookings', $c)) $table->dropColumn($c);
            }
        });
    }
};
