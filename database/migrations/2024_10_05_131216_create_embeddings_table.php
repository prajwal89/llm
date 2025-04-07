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
        Schema::create('embeddings', function (Blueprint $table): void {
            $table->id();
            $table->longText('text');
            $table->string('model_name');
            $table->string('use_case');
            $table->string('embedable_type')->nullable();
            $table->string('embedable_id')->nullable();

            $driver = DB::getDriverName();

            match ($driver) {
                'mysql' => $table->string('embedding_md5', 32)
                    ->virtualAs('MD5(CONCAT(CAST(text AS CHAR), CAST(model_name AS CHAR), CAST(use_case AS CHAR)))')
                    ->nullable(),
                'sqlite' => $table->string('embedding_md5', 32)->nullable(),
                default => throw new RuntimeException("Unsupported database driver: {$driver}"),
            };

            $table->json('vectors');
            $table->unique('embedding_md5');
            $table->index(['embedable_type', 'embedable_id']);
            $table->timestamps();
        });

        // if (DB::getDriverName() === 'sqlite') {
        //     DB::statement("
        //         CREATE TRIGGER populate_embedding_md5
        //         AFTER INSERT OR UPDATE ON embeddings
        //         FOR EACH ROW
        //         BEGIN
        //             UPDATE embeddings
        //             SET embedding_md5 = LOWER(HEX(RANDOMBLOB(16))) -- Replace with SQLite MD5 logic if required
        //             WHERE id = NEW.id;
        //         END
        //     ");
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS populate_embedding_md5');
        }

        Schema::dropIfExists('embeddings');
    }
};
