<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Backfill existing rows
        foreach (['products', 'product_variants'] as $table) {
            DB::table($table)->whereNull('uuid')->orderBy('id')
                ->each(fn ($row) => DB::table($table)->where('id', $row->id)
                    ->update(['uuid' => (string) Str::uuid()]));
        }

        Schema::table('products', fn (Blueprint $table) => $table->unique('uuid'));
        Schema::table('product_variants', fn (Blueprint $table) => $table->unique('uuid'));
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('uuid'));
        Schema::table('product_variants', fn (Blueprint $table) => $table->dropColumn('uuid'));
    }
};
