<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('allow_preorder')->default(false)->after('is_new_arrival');
            $table->string('preorder_note')->nullable()->after('allow_preorder');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(false)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['allow_preorder', 'preorder_note']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('is_preorder');
        });
    }
};
