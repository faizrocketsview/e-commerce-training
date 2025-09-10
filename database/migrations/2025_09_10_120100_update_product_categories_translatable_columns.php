<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('id');
        });

        DB::statement("UPDATE product_categories SET name_json = JSON_OBJECT('en', CAST(name AS CHAR))");

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['name']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->json('name')->after('id');
        });

        DB::statement("UPDATE product_categories SET name = name_json");

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['name_json']);
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('name_tmp', 150)->nullable()->after('id');
        });

        DB::statement("UPDATE product_categories SET name_tmp = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')), NULL)");

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['name']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('name', 150)->after('id');
        });

        DB::statement("UPDATE product_categories SET name = name_tmp");

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['name_tmp']);
        });
    }
};


