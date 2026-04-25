<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Le colonne legacy `key`, `value`, `group`, `type` della tabella `settings`
     * non sono più usate dall'applicazione (sostituite da `chiave`, `valore`, `gruppo`).
     * Restavano però NOT NULL senza default → 1364 al primo INSERT.
     * Le rendo nullable e copio i dati attuali da chiave/valore/gruppo per coerenza.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) return;

        // 1) Allenta i NOT NULL via SQL grezzo (evita dipendenza da doctrine/dbal)
        if (Schema::hasColumn('settings', 'key')) {
            DB::statement("ALTER TABLE `settings` MODIFY `key` VARCHAR(100) NULL");
        }
        if (Schema::hasColumn('settings', 'value')) {
            DB::statement("ALTER TABLE `settings` MODIFY `value` TEXT NULL");
        }
        if (Schema::hasColumn('settings', 'group')) {
            DB::statement("ALTER TABLE `settings` MODIFY `group` VARCHAR(50) NULL DEFAULT NULL");
        }
        if (Schema::hasColumn('settings', 'type')) {
            DB::statement("ALTER TABLE `settings` MODIFY `type` VARCHAR(20) NULL DEFAULT NULL");
        }

        // 2) Sincronizza i record già esistenti (best-effort)
        try {
            if (Schema::hasColumn('settings', 'key') && Schema::hasColumn('settings', 'chiave')) {
                DB::statement("UPDATE `settings` SET `key` = `chiave` WHERE (`key` IS NULL OR `key` = '') AND `chiave` IS NOT NULL");
            }
            if (Schema::hasColumn('settings', 'value') && Schema::hasColumn('settings', 'valore')) {
                DB::statement("UPDATE `settings` SET `value` = `valore` WHERE `value` IS NULL AND `valore` IS NOT NULL");
            }
            if (Schema::hasColumn('settings', 'group') && Schema::hasColumn('settings', 'gruppo')) {
                DB::statement("UPDATE `settings` SET `group` = `gruppo` WHERE (`group` IS NULL OR `group` = '') AND `gruppo` IS NOT NULL");
            }
        } catch (\Throwable $e) {
            // non bloccare la migrazione se la sync fallisce
        }
    }

    public function down(): void
    {
        // Non-reversible in modo sicuro: lasciamo nullable.
    }
};
