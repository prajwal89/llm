<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('llm_message_batches', function (Blueprint $table): void {

            if (FacadesDB::getDriverName() === 'sqlite') {
                $table->dropIndex('llm_message_batches_responseable_id_responseable_type_index');
            }

            $table->dropColumn('messages');
            $table->dropColumn('model_name');
            $table->dropColumn('blade_template_name');
            $table->dropColumn('responseable_id');
            $table->dropColumn('responseable_type');
        });

        Schema::table('llm_message_batches', function (Blueprint $table): void {
            $table->string('model_family')->after('processing_status');
        });
    }
};
