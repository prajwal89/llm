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
        Schema::table('llm_embeddings', function (Blueprint $table): void {
            $table->integer('total_tokens')->after('vectors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
