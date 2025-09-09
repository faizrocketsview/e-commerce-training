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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();

            $table->string('model', 200);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('status', 30);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('total_inserted')->nullable();
            $table->unsignedBigInteger('total_failed')->nullable();
            $table->unsignedBigInteger('total_rows')->nullable();
            $table->unsignedInteger('total_completed_chunk')->nullable();
            $table->unsignedInteger('total_chunk')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->string('deleted_token', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
