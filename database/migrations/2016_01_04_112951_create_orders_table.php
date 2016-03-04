<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->nullable();
            $table->string('short_order')->nullable();
            $table->string('item_count')->nullable();
            $table->string('coupon_description')->nullable();
            $table->string('coupon_id')->nullable();
            $table->string('coupon_value')->nullable();
            $table->string('shipping_charge')->nullable();
            $table->string('tax_charge')->nullable();
            $table->string('total')->nullable();
            $table->string('card_name')->nullable();
            $table->string('card_expiry')->nullable();
            $table->string('order_comments')->nullable();
            $table->string('order_date')->nullable();
            $table->string('order_numeric_time')->nullable();
            $table->string('order_ip')->nullable();
            $table->string('paypal_merchant_email')->nullable();
            $table->string('paypal_txid')->nullable();
            $table->string('space_id')->nullable();
            $table->string('store_id')->nullable();
            $table->string('store_name')->nullable();
            $table->string('ship_state')->nullable();
            $table->string('order_status')->default(4);
            $table->double('sub_total')->default(0);
            $table->enum('is_filtered', array(0, 1))->default(0);
            $table->enum('is_deleted', array(0, 1))->default(0);


			$table->index('order_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('orders');
    }
}
