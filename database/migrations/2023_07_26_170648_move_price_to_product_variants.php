<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->after('name');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
