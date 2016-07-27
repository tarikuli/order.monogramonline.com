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
			$table->string('code',50);
			$table->string('name',250)->nullable();
			$table->string('unit',10)->nullable();
			$table->double('price')->default('0.00');
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
