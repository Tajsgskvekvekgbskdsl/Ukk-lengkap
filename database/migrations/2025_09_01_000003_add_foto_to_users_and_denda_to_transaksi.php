<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - users.foto        : path foto profil, default avatar jika kosong.
 * - transaksi.denda / denda_dibayar
 *                     : denda yang terutang & sudah dibayar (otomatis dihitung).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'foto')) {
                $table->string('foto', 255)->nullable()->after('status');
            }
        });

        Schema::table('transaksi', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksi', 'denda')) {
                $table->decimal('denda', 12, 2)->default(0)->after('status');
            }
            if (! Schema::hasColumn('transaksi', 'denda_dibayar')) {
                $table->decimal('denda_dibayar', 12, 2)->default(0)->after('denda');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'foto')) {
                $table->dropColumn('foto');
            }
        });

        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi', 'denda_dibayar')) {
                $table->dropColumn('denda_dibayar');
            }
            if (Schema::hasColumn('transaksi', 'denda')) {
                $table->dropColumn('denda');
            }
        });
    }
};
