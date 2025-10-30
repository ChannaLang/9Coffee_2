<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('product_raw_material', function (Blueprint $table) {
        $table->decimal('quantity_required', 8, 2)->change();
    });
}

public function down(): void
{
    Schema::table('product_raw_material', function (Blueprint $table) {
        $table->integer('quantity_required')->change();
    });
}

};
