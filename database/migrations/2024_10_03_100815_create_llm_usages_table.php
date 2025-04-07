<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('llm_usages', function (Blueprint $table): void {
            $table->id();
            $table->longText('system_prompt');
            $table->longText('user_prompt');
            $table->longText('response');
            $table->integer('input_tokens');
            $table->integer('output_tokens');
            $table->integer('time_taken_ms');

            $driver = DB::getDriverName();

            match ($driver) {
                'mysql' => $table->string('prompt_md5', 32)
                    ->virtualAs('MD5(CONCAT(CAST(system_prompt AS CHAR), CAST(user_prompt AS CHAR)))')
                    ->nullable(),
                'sqlite' => $table->string('prompt_md5', 32)->nullable(),
                default => throw new RuntimeException("Unsupported database driver: {$driver}"),
            };

            $table->string('blade_template_name')->nullable();
            $table->string('model_name');
            $table->string('responseable_id')->nullable();
            $table->string('responseable_type')->nullable();
            $table->index(['responseable_id', 'responseable_type']);
            $table->timestamps();
        });

        // if (DB::getDriverName() === 'sqlite') {
        //     DB::statement("
        // CREATE TRIGGER populate_prompt_md5
        // AFTER INSERT ON llm_usages
        // FOR EACH ROW
        // WHEN NEW.prompt_md5 IS NULL
        // BEGIN
        //     UPDATE llm_usages
        //     SET prompt_md5 = LOWER(HEX(RANDOMBLOB(16)))
        //     WHERE id = NEW.id;
        // END;
        //      ");
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS populate_prompt_md5');
        }

        Schema::dropIfExists('llm_usages');
    }
};
