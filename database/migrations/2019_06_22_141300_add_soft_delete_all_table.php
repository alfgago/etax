<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteAllTable extends Migration
{
    protected $tables = ['atv_certificates', 'bills', 'bill_items', 'books', 'calculated_taxes', 'clients',
        'codigo_iva_repercutidos', 'codigo_iva_soportados', 'coupons', 'invoices', 'invoice_items', 'payments',
        'permissions', 'plans_invitations', 'products', 'product_categories', 'providers', 'users', 'xml_haciendas'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       foreach ($this->tables as $table) {
           Schema::table($table, function (Blueprint $tab) {
               $tab->softDeletes();
           });
       }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $tab) {
                $tab->dropSoftDeletes();
            });
        }
    }
}
