<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
	 *
     * idCatalog - unique product part #
	 * parent_item - item master code/style (can be used instead of the item id, idcatalog => please don't use both columns)
	 * vendorId - Shipper's ID
	 * warehouse - location of the product, warehouse Code
	 * wh_bin - BIN location of the product
	 * sku - item SKU
	 * upc - item UPC
	 * vendorSku - Supplier's sku
	 * desc - short description
	 * skuCost - 999.99 (no $ sign)
	 * skuCost_sup - SKU cost in supplier currency
	 * skuWeight - item weight
	 * reOrderQty - reorder point qty (units)
	 * minReorder - re-order qty (units)
	 * qtyOnHand - qty on hand
	 * qtyFrozen - qty frozen (optional)
	 * sku_status - 0- Inactive, 1- Active, 2 - Discontinued
	 * rt_status - real time inventory for the item 0-Inactive, 1-Active
	 * inv_amazon - update Amazon inventory for the item 0-No, 1-Yes
	 * feed_houzz - update Houzz inventory for the item 0-No, 1-Yes
	 * feed_jet - update JET.com inventory for the item 0-No, 1-Yes
	 * order_qty_size - Order quantity size
	 * inv_ebay - zero - exclude from feed; feed qty if qty/av >= qty
	 * leadtime - Lead time for shipping (days)
	 * exp_date - SKU's expected time
	 * ASIN - Amazon's ASIN
	 * amz_price - Price on Amazon
	 * ebay_price - Price on eBay
	 * buy_com_price - Price on Rakuten (buy.com)
	 * del - if set as 1 remove this SKU from the inventory file
	 *
	 */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
			$table->string('id_catalog')->nullable();
			$table->string('vendor_id')->nullable();
			$table->string('sku')->nullable();
			$table->string('warehouse')->nullable();
			$table->string('wh_bin')->nullable();
			$table->string('parent_item')->nullable();
			$table->string('upc')->nullable();
			$table->string('asin')->nullable();
			$table->string('vendor_sku')->nullable();
			$table->string('desc')->nullable();
			$table->string('sku_cost')->nullable();
			$table->string('sku_cost_sup')->nullable();
			$table->string('sku_weight')->nullable();
			$table->string('re_order_qty')->nullable();
			$table->string('min_reorder')->nullable();
			$table->string('qty_on_hand')->nullable();
			$table->string('qty_alloc')->nullable();
			$table->string('qty_exp')->nullable();
			$table->string('qty_frozen')->nullable();
			$table->string('qty_av')->nullable();
			$table->string('inv_run_date')->nullable();
			$table->string('inv_value')->nullable();
			$table->string('inv_avg_cost')->nullable();
			$table->string('inv_rcv_date')->nullable();
			$table->string('inv_remain_qty')->nullable();
			$table->enum('sku_status', [0, 1, 2])->default(0);
			$table->enum('rt_status', [0, 1])->default(0);
			$table->enum('inv_amazon', [0, 1])->default(0);
			$table->string('inv_chl')->nullable();
			$table->string('order_qty_size')->nullable();
			$table->string('inv_u30')->nullable();
			$table->string('inv_ebay')->nullable();
			$table->string('inv_u180')->nullable();
			$table->string('exp_date')->nullable();
			$table->string('lead_time')->nullable();
			$table->string('inv_target')->nullable();
			$table->string('rt_qty_max')->nullable();
			$table->string('amz_price')->nullable();
			$table->string('ebay_price')->nullable();
			$table->string('buy_com_price')->nullable();
			$table->enum('feed_houzz', [0, 1])->default(0);
			$table->enum('feed_jet', [0, 1])->default(0);
			$table->string('inv_last_update')->nullable();
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
        Schema::drop('inventories');
    }
}
