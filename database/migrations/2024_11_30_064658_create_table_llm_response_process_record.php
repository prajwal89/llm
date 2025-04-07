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
        Schema::create('llm_response_process_record', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
            $table->string('processable_id');
            $table->string('processable_type');
            $table->mediumText('error')->nullable();
            $table->index(['processable_id', 'processable_type'], 'processable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_llm_response_process_record');
    }
};
