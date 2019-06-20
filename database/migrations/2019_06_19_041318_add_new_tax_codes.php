<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class AddNewTaxCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->double('cc_compras1')->default(0);
            $table->double('cc_compras2')->default(0);
            $table->double('cc_compras3')->default(0);
            $table->double('cc_compras4')->default(0);
            $table->double('cc_importaciones1')->default(0);
            $table->double('cc_importaciones2')->default(0);
            $table->double('cc_importaciones3')->default(0);
            $table->double('cc_importaciones4')->default(0);
            $table->double('cc_propiedades1')->default(0);
            $table->double('cc_propiedades2')->default(0);
            $table->double('cc_propiedades3')->default(0);
            $table->double('cc_propiedades4')->default(0);
            
            $table->double('cc_iva_compras1')->default(0);
            $table->double('cc_iva_compras2')->default(0);
            $table->double('cc_iva_compras3')->default(0);
            $table->double('cc_iva_compras4')->default(0);
            $table->double('cc_iva_importaciones1')->default(0);
            $table->double('cc_iva_importaciones2')->default(0);
            $table->double('cc_iva_importaciones3')->default(0);
            $table->double('cc_iva_importaciones4')->default(0);
            $table->double('cc_iva_propiedades1')->default(0);
            $table->double('cc_iva_propiedades2')->default(0);
            $table->double('cc_iva_propiedades3')->default(0);
            $table->double('cc_iva_propiedades4')->default(0);
            
            $table->double('cc_ventas_canasta')->default(0);
        });
        
        Schema::table('calculated_taxes', function (Blueprint $table) {
            $table->double('b100')->default(0);
            $table->double('i100')->default(0);
            $table->double('b165')->default(0);
            $table->double('i165')->default(0);
            $table->double('b170')->default(0);
            $table->double('i170')->default(0);
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('isSubscription')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('cc_compras1');
            $table->dropColumn('cc_compras2');
            $table->dropColumn('cc_compras3');
            $table->dropColumn('cc_compras4');
            $table->dropColumn('cc_importaciones1');
            $table->dropColumn('cc_importaciones2');
            $table->dropColumn('cc_importaciones3');
            $table->dropColumn('cc_importaciones4');
            $table->dropColumn('cc_propiedades1');
            $table->dropColumn('cc_propiedades2');
            $table->dropColumn('cc_propiedades3');
            $table->dropColumn('cc_propiedades4');
            
            $table->dropColumn('cc_iva_compras1');
            $table->dropColumn('cc_iva_compras2');
            $table->dropColumn('cc_iva_compras3');
            $table->dropColumn('cc_iva_compras4');
            $table->dropColumn('cc_iva_importaciones1');
            $table->dropColumn('cc_iva_importaciones2');
            $table->dropColumn('cc_iva_importaciones3');
            $table->dropColumn('cc_iva_importaciones4');
            $table->dropColumn('cc_iva_propiedades1');
            $table->dropColumn('cc_iva_propiedades2');
            $table->dropColumn('cc_iva_propiedades3');
            $table->dropColumn('cc_iva_propiedades4');
            
            $table->dropColumn('cc_ventas_canasta');
        });
        
        Schema::table('calculated_taxes', function ($table) {
            $table->dropColumn('b100');
            $table->dropColumn('i100');
            $table->dropColumn('b165');
            $table->dropColumn('i165');
            $table->dropColumn('b170');
            $table->dropColumn('i170');
        });
    }
}