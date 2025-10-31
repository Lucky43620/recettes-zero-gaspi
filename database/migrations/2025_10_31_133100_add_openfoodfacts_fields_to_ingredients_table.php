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
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('openfoodfacts_id')->nullable()->unique()->after('barcode');
            $table->json('nutritional_info')->nullable()->after('openfoodfacts_id');
            $table->json('allergens')->nullable()->after('nutritional_info');
            $table->string('image_url')->nullable()->after('allergens');
            $table->json('labels')->nullable()->after('image_url');
            $table->string('brands')->nullable()->after('labels');
            $table->integer('avg_shelf_life_days')->nullable()->after('brands');
            $table->timestamp('openfoodfacts_synced_at')->nullable()->after('avg_shelf_life_days');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'openfoodfacts_id',
                'nutritional_info',
                'allergens',
                'image_url',
                'labels',
                'brands',
                'avg_shelf_life_days',
                'openfoodfacts_synced_at',
            ]);
        });
    }
};
