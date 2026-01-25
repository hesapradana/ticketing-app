<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default ticket types based on existing enum values
        DB::table('ticket_types')->insert([
            ['nama' => 'Reguler', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Premium', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add new column
        Schema::table('tikets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable()->after('event_id')->constrained()->onDelete('cascade');
        });

        // Migrate existing data
        $regulerId = DB::table('ticket_types')->where('nama', 'Reguler')->value('id');
        $premiumId = DB::table('ticket_types')->where('nama', 'Premium')->value('id');

        DB::table('tikets')->where('tipe', 'reguler')->update(['ticket_type_id' => $regulerId]);
        DB::table('tikets')->where('tipe', 'premium')->update(['ticket_type_id' => $premiumId]);

        // Drop old column
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });

        // Make ticket_type_id not nullable
        Schema::table('tikets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the enum column
        Schema::table('tikets', function (Blueprint $table) {
            $table->enum('tipe', ['reguler', 'premium'])->after('event_id')->default('reguler');
        });

        // Migrate data back
        $regulerId = DB::table('ticket_types')->where('nama', 'Reguler')->value('id');
        $premiumId = DB::table('ticket_types')->where('nama', 'Premium')->value('id');

        DB::table('tikets')->where('ticket_type_id', $regulerId)->update(['tipe' => 'reguler']);
        DB::table('tikets')->where('ticket_type_id', $premiumId)->update(['tipe' => 'premium']);

        // Drop foreign key and column
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn('ticket_type_id');
        });

        // Delete default ticket types
        DB::table('ticket_types')->whereIn('nama', ['Reguler', 'Premium'])->delete();
    }
};
