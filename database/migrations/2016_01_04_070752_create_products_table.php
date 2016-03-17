<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
		/*TODO: add this fields to migration*/
		/*ALTER TABLE  `products` ADD  `product_orderable` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0' AFTER  `width` ,
ADD  `product_gift_cert` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0' AFTER  `product_orderable` ,
ADD  `product_headline` TEXT NULL AFTER  `product_gift_cert` ,
ADD  `product_caption` TEXT NULL AFTER  `product_headline` ,
ADD  `product_abstract` TEXT NULL AFTER  `product_caption` ,
ADD  `product_label` TEXT NULL AFTER  `product_abstract` ,
ADD  `product_condition` VARCHAR( 255 ) NULL AFTER  `product_label` ;*/
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
			$table->integer('product_production_category')->nullable();
			$table->integer('product_collection')->default(0);
			$table->integer('product_occasion')->default(0);
			$table->double('product_price')->default(0);
			$table->double('product_sale_price')->default(0);
			$table->string('product_thumb')->nullable();
			$table->integer('batch_route_id')->nullable();
			$table->text('product_keywords')->nullable();
			$table->text('product_description')->nullable();
			$table->double('height')->default(0.0);
			$table->double('width')->default(0.0);
			$table->enum('is_taxable', array(0, 1))->default(1);
            $table->enum('is_deleted', array(0, 1))->default(0);

			$table->index('id_catalog');
			$table->index('product_model');
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
