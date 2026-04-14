<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Prima aggiorna i valori non validi
        DB::statement("UPDATE users SET role = 'admin' WHERE role NOT IN ('admin','manager','operatore','vendite')");

        // Poi cambia il tipo colonna
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','operatore','vendite') NOT NULL DEFAULT 'operatore'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) NOT NULL DEFAULT 'admin'");
    }
};