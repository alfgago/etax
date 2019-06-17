<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyRatioFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->double('operative_prorrata')->default(0);
            $table->double('operative_ratio1')->default(0);
            $table->double('operative_ratio2')->default(0);
            $table->double('operative_ratio3')->default(0);
            $table->double('operative_ratio4')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function($table) {
            $table->dropColumn('operative_prorrata');
            $table->dropColumn('operative_ratio1');
            $table->dropColumn('operative_ratio2');
            $table->dropColumn('operative_ratio3');
            $table->dropColumn('operative_ratio4');
        });
    }
}
