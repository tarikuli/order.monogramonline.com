<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id');
            $table->string('id_catalog');
			$table->string('vendor_id')->nullable();
			$table->string('product_model')->nullable();
			$table->string('product_url')->nullable();
			$table->string('product_name')->nullable();
			$table->string('ship_weight')->nullable();
			$table->string('product_master_category')->nullable();
			$table->string('product_category')->nullable();
			$table->string('product_sub_category')->nullable();
			$table->string('product_production_category')->nullable();
			$table->double('product_price')->default(0);
			$table->double('product_sale_price')->default(0);
			$table->string('product_thumb')->nullable();
			$table->integer('batch_route_id')->nullable();
			#$table->string('batch_route_code')->nullable();
			$table->text('product_keywords')->nullable();
			$table->text('product_description')->nullable();
			$table->double('height')->default(0.0);
			$table->double('width')->default(0.0);
			$table->enum('is_taxable', array(0, 1))->default(1);
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
        Schema::drop('products');
    }
}
