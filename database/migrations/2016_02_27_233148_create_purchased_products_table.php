<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchased_products', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('purchase_id');
			$table->integer('product_id');
			$table->double('quantity')->default(0.0);
			$table->double('price')->default(0.0);
			$table->double('sub_total')->default(0.0);
			$table->enum('is_deleted', [0, 1])->default(0);
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
        Schema::drop('purchased_products');
    }
}
