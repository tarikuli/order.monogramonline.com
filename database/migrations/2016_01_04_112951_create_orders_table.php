<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /*
     * previous
        $table->string('order_id')->nullable();
        $table->string('email')->nullable();
        $table->string('customer_id')->nullable();
        $table->string('placed_by')->nullable();
        $table->string('store_id')->nullable();
        $table->string('market')->nullable();
        $table->string('order_date')->nullable();
        $table->string('paid')->nullable();
        $table->string('payment_method')->nullable();
        $table->string('sub_total')->nullable();
        $table->string('shipping_cost')->nullable();
        $table->string('discount')->nullable();
        $table->string('gift_wrap_cost')->nullable();
        $table->string('tax')->nullable();
        $table->string('adjustment')->nullable();
        $table->string('order_total')->nullable();
        $table->string('fraud_score')->nullable();
        $table->string('coupon_name')->nullable();
        $table->string('coupon_description')->nullable();
        $table->integer('coupon_value')->nullable();
        $table->string('shipping_method')->nullable();
        $table->string('four_pl_unique_id')->nullable();
        $table->string('short_order')->nullable();
        $table->string('order_comments')->nullable();
        $table->string('item_name')->nullable();
        $table->string('item_code')->nullable();
        $table->string('item_id')->nullable();
        $table->string('item_qty')->nullable();
        $table->string('item_price')->nullable();
        $table->string('item_cost')->nullable();
        $table->string('item_options')->nullable();
        $table->string('trk')->nullable();
        $table->string('ship_date')->nullable();
        $table->string('shipping_carrier')->nullable();
        $table->string('drop_shipper')->nullable();
        $table->string('return_request_code')->nullable();
        $table->string('return_request_date')->nullable();
        $table->string('return_disposition_code')->nullable();
        $table->string('return_date')->nullable();
        $table->string('rma')->nullable();
        $table->string('d_s_purchase_order')->nullable();
        $table->string('wf_batch')->nullable();
        $table->string('order_status')->nullable();
        $table->string('source')->nullable();
        $table->string('cancel_code')->nullable();
        $table->string('ip_address')->nullable();
        $table->string('credit_card_type')->nullable();
        $table->string('card_auth')->nullable();
        $table->string('card_event')->nullable();
    */
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
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('orders');
    }
}
