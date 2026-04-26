<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aggiunge `letto_at` ai web_bookings: timestamp di quando il messaggio
     * è stato segnato come "letto" da un operatore. NULL = non letto.
     * Usato per la campanellina notifiche e l'elenco messaggi nel dashboard.
     */
    public function up(): void
    {
        Schema::table('web_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('web_bookings', 'letto_at')) {
                $table->timestamp('letto_at')->nullable()->after('confirmed_at')->index();
            }
            if (!Schema::hasColumn('web_bookings', 'letto_da_user_id')) {
                $table->unsignedBigInteger('letto_da_user_id')->nullable()->after('letto_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('web_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('web_bookings', 'letto_da_user_id')) {
                $table->dropColumn('letto_da_user_id');
            }
            if (Schema::hasColumn('web_bookings', 'letto_at')) {
                $table->dropColumn('letto_at');
            }
        });
    }
};
