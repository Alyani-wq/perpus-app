<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom foto pada tabel members.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'foto')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('foto')->nullable();
            });
        }
    }

    /**
     * Menghapus kolom foto saat rollback.
     */
    public function down(): void
    {
        if (Schema::hasColumn('members', 'foto')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }
    }
};
