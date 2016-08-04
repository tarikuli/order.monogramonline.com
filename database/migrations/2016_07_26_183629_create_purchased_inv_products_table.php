<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasedInvProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchased_inv_products', function (Blueprint $table) {
            $table->increments('id');
			$table->string('stock_no',250);
			$table->string('stock_name_discription',250)->nullable();
			$table->string('unit',10)->nullable();
			$table->double('unit_price')->default('0.00');
			$table->double('vendor_id')->nullable();
			$table->double('vendor_sku')->nullable();
			$table->double('lead_time_days')->default('0');
			$table->enum('is_deleted', array(0, 1))->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::drop('purchased_inv_products');
    }
}
