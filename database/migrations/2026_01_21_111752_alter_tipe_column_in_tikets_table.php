<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Masukkan tipe tiket default berdasarkan nilai enum yang ada
        DB::table('ticket_types')->insert([
            ['nama' => 'Reguler', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Premium', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Tambah kolom baru
        Schema::table('tikets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable()->after('event_id')->constrained()->onDelete('cascade');
        });

        // Migrasi data yang ada
        $regulerId = DB::table('ticket_types')->where('nama', 'Reguler')->value('id');
        $premiumId = DB::table('ticket_types')->where('nama', 'Premium')->value('id');

        DB::table('tikets')->where('tipe', 'reguler')->update(['ticket_type_id' => $regulerId]);
        DB::table('tikets')->where('tipe', 'premium')->update(['ticket_type_id' => $premiumId]);

        // Hapus kolom lama
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });

        // Ubah ticket_type_id menjadi tidak nullable
        Schema::table('tikets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable(false)->change();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom enum
        Schema::table('tikets', function (Blueprint $table) {
            $table->enum('tipe', ['reguler', 'premium'])->after('event_id')->default('reguler');
        });

        // Kembalikan data
        $regulerId = DB::table('ticket_types')->where('nama', 'Reguler')->value('id');
        $premiumId = DB::table('ticket_types')->where('nama', 'Premium')->value('id');

        DB::table('tikets')->where('ticket_type_id', $regulerId)->update(['tipe' => 'reguler']);
        DB::table('tikets')->where('ticket_type_id', $premiumId)->update(['tipe' => 'premium']);

        // Hapus foreign key dan kolom
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn('ticket_type_id');
        });

        // Hapus tipe tiket default
        DB::table('ticket_types')->whereIn('nama', ['Reguler', 'Premium'])->delete();
    }
};
