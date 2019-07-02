<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class FixIsSubscription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if (Schema::hasColumn('sales', 'isSubscription'))
        {
            Schema::table('sales', function (Blueprint $table)
            {
                $table->dropColumn('isSubscription');
            });
        }
        if (Schema::hasColumn('sales', 'is_subscription'))
        {
            Schema::table('sales', function (Blueprint $table)
            {
                $table->dropColumn('is_subscription');
            });
        }

        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('is_subscription')->default(true);
        });
        
        if (Schema::hasColumn('etax_products', 'isSubscription'))
        {
            Schema::table('etax_products', function (Blueprint $table)
            {
                $table->dropColumn('isSubscription');
            });
        }
        if (Schema::hasColumn('etax_products', 'is_subscription'))
        {
            Schema::table('etax_products', function (Blueprint $table)
            {
                $table->dropColumn('is_subscription');
            });
        }

        Schema::table('etax_products', function (Blueprint $table) {
            $table->boolean('is_subscription')->default(true);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function ($table) {
            $table->dropColumn('is_subscription');
        });   
    }
}
