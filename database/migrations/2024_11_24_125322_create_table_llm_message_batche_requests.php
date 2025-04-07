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
        Schema::create('llm_message_batch_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('custom_id');
            $table->string('batch_id');
            $table->string('status');
            $table->longText('system_prompt')->nullable();
            $table->longText('user_prompt')->nullable();
            $table->longText('response')->nullable();
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->string('model_name');
            $table->string('responseable_id')->nullable();
            $table->string('responseable_type')->nullable();
            $table->index(['responseable_id', 'responseable_type'], 'responseable_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_message_batch_requests');
    }
};
