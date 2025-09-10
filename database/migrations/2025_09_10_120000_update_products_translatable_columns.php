<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add temporary JSON columns
        Schema::table('products', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('category_id');
            $table->json('description_json')->nullable()->after('sku');
        });

        // Step 2: Backfill existing values into JSON columns (under 'en')
        DB::statement("UPDATE products SET name_json = JSON_OBJECT('en', CAST(name AS CHAR))");
        DB::statement("UPDATE products SET description_json = CASE 
            WHEN description IS NULL THEN NULL 
            ELSE JSON_OBJECT('en', CAST(description AS CHAR)) 
        END");

        // Step 3: Drop old columns and rename JSON columns to original names
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->json('name')->after('category_id');
            $table->json('description')->nullable()->after('sku');
        });

        DB::statement("UPDATE products SET name = name_json, description = description_json");

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_json', 'description_json']);
        });
    }

    public function down(): void
    {
        // Best-effort revert: add temp columns, extract 'en', then drop JSON
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_tmp', 200)->nullable()->after('category_id');
            $table->text('description_tmp')->nullable()->after('sku');
        });

        DB::statement("UPDATE products SET name_tmp = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')), NULL)");
        DB::statement("UPDATE products SET description_tmp = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')), NULL)");

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 200)->after('category_id');
            $table->text('description')->nullable()->after('sku');
        });

        DB::statement("UPDATE products SET name = name_tmp, description = description_tmp");

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_tmp', 'description_tmp']);
        });
    }
};


