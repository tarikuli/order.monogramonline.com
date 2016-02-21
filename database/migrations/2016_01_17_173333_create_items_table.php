<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
			$table->string('store_id');
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->string('item_id')->nullable();
            $table->text('item_option')->nullable();
            $table->string('item_quantity')->nullable();
            $table->string('item_thumb')->nullable();
            $table->string('item_unit_price')->nullable();
            $table->string('item_url')->nullable();
            $table->enum('item_taxable', array('Yes', 'No'))->default('No');
            $table->string('tracking_number')->nullable();
			$table->integer('batch_route_id')->nullable();
			$table->string('batch_creation_date')->nullable();
            $table->integer('batch_number')->default(0);
			$table->string('station_name')->nullable();
			$table->string('previous_station')->nullable();
			$table->string('item_order_status')->nullable();
			$table->integer('item_order_status_2')->nullable();
            $table->string('data_parse_type')->nullable();
			$table->string('item_status')->nullable();
			$table->text('rejection_message')->nullable();
            $table->enum('is_deleted', array(0, 1))->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('items');
    }
}
