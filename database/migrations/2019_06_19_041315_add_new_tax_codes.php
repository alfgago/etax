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
            $table->double('cc_compras_exentas')->default(0);
            $table->double('cc_compras_sum')->default(0);
            
            $table->double('cc_ventas_canasta')->default(0);
            $table->double('cc_ventas_exentas')->default(0);
            $table->double('cc_ventas_aduana')->default(0);
        });
        
        Schema::table('calculated_taxes', function (Blueprint $table) {
            $table->double('b015')->default(0);
            $table->double('i015')->default(0);
            $table->double('b016')->default(0);
            $table->double('i016')->default(0);
            $table->double('b035')->default(0);
            $table->double('i035')->default(0);
            $table->double('b036')->default(0);
            $table->double('i036')->default(0);
            
            $table->double('b155')->default(0);
            $table->double('i155')->default(0);
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
            $table->dropColumn('cc_compras_exentas');
            
            $table->dropColumn('cc_ventas_canasta');
            $table->dropColumn('cc_ventas_exentas');
            $table->dropColumn('cc_ventas_aduana');
        });
        
        Schema::table('calculated_taxes', function ($table) {
            $table->dropColumn('b015');
            $table->dropColumn('i015');
            $table->dropColumn('b016');
            $table->dropColumn('i016');
            $table->dropColumn('b035');
            $table->dropColumn('i035');
            $table->dropColumn('b036');
            $table->dropColumn('i036');
            
            $table->dropColumn('b155');
            $table->dropColumn('i155');
            $table->dropColumn('b165');
            $table->dropColumn('i165');
            $table->dropColumn('b170');
            $table->dropColumn('i170');
        });
        
        Schema::table('sales', function ($table) {
            $table->dropColumn('isSubscription');
        });
    }
}