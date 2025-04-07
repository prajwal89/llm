<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('llm_usages', function (Blueprint $table): void {
            $driver = DB::getDriverName();

            match ($driver) {
                'mysql' => $table
                    ->string('prompt_md5', 32)
                    ->virtualAs('MD5(CONCAT(CAST(system_prompt AS CHAR), CAST(user_prompt AS CHAR), CAST(model_name AS CHAR)))')
                    ->change(),
                'sqlite' => $table->string('prompt_md5', 32)->change(),
                default => throw new RuntimeException("Unsupported database driver: {$driver}"),
            };
        });
    }
};
