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
        Schema::table('llm_message_batch_requests', function (Blueprint $table): void {
            $table->foreign('batch_id')
                ->references('batch_id')
                ->on('llm_message_batches')
                ->cascadeOnDelete();
        });
    }
};
