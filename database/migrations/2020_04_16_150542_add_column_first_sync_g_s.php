<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFirstSyncGS extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('integracion_empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('integracion_empresas', 'first_sync_gs')) {
                $table->boolean('first_sync_gs')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('integracion_empresas', function (Blueprint $table) {
            $table->dropColumn('first_sync_gs');
        });
    }
}
