<?php

declare(strict_types=1);

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
        Schema::table('embeddings', function (Blueprint $table): void {
            // Adding a unique index on the combination of use_case, embedable_type, and embedable_id
            $table->unique(['use_case', 'embedable_type', 'embedable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('embeddings', function (Blueprint $table): void {
            // Dropping the unique index
            $table->dropUnique(['use_case', 'embedable_type', 'embedable_id']);
        });
    }
};
