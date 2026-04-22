<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabella settings esiste già con colonne in inglese
        // Aggiungiamo solo le colonne mancanti per il nuovo sistema

        Schema::table('settings', function (Blueprint $table) {
            // Aggiunge 'gruppo' se non esiste (alias italiano di 'group')
            if (!Schema::hasColumn('settings', 'gruppo')) {
                $table->string('gruppo', 50)->nullable()->index()->after('tenant_id');
            }
            // Aggiunge 'chiave' se non esiste (alias italiano di 'key')
            if (!Schema::hasColumn('settings', 'chiave')) {
                $table->string('chiave', 100)->nullable()->after('gruppo');
            }
            // Aggiunge 'valore' se non esiste (alias italiano di 'value')
            if (!Schema::hasColumn('settings', 'valore')) {
                $table->text('valore')->nullable()->after('chiave');
            }
            // Aggiunge 'is_secret' se non esiste
            if (!Schema::hasColumn('settings', 'is_secret')) {
                $table->boolean('is_secret')->default(false)->after('valore');
            }
        });

        // Crea tabella setting_permissions se non esiste
        if (!Schema::hasTable('setting_permissions')) {
            Schema::create('setting_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('gruppo', 50);
                $table->boolean('can_edit')->default(false);
                $table->timestamps();

                $table->unique(['tenant_id', 'user_id', 'gruppo']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = ['gruppo', 'chiave', 'valore', 'is_secret'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('setting_permissions');
    }
};