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
        Schema::table('embeddings', function (Blueprint $table): void {
            $table->dropUnique('embeddings_use_case_embedable_type_embedable_id_unique');
            $table->dropUnique('embeddings_embedding_md5_unique');
            $table->dropColumn('embedding_md5');
            $table->renameColumn('text', 'input_text');

            $driver = DB::getDriverName();

            match ($driver) {
                'mysql' => $table->string('input_text_md5', 32)
                    ->virtualAs('MD5(CONVERT(input_text USING utf8mb4))')
                    ->after('embedable_id'),
                'sqlite' => $table->string('input_text_md5', 32)->nullable(),
                default => throw new RuntimeException("Unsupported database driver: {$driver}"),
            };

            // $table->string('md5', 32)
            // ->virtualAs('MD5(CONCAT(CONVERT(model_name USING utf8mb4), CONVERT(input_text USING utf8mb4)))')
            // ->after('embedable_id');

            $table->unique(['input_text_md5', 'model_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
