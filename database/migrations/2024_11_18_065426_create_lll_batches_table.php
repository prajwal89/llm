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
        Schema::create('llm_message_batches', function (Blueprint $table): void {
            $table->string('batch_id')->primary();
            $table->string('processing_status');
            $table->json('messages')->nullable();
            $table->string('model_name');
            $table->string('blade_template_name')->nullable();
            $table->unsignedBigInteger('responseable_id')->nullable();
            $table->string('responseable_type')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('cancel_initiated_at')->nullable();
            $table->timestamps();

            $table->index(['responseable_id', 'responseable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_batches');
    }
};
