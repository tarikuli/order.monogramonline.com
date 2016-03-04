<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number');
			$table->string('item_id');
			$table->string('shipping_id')->nullable();
            $table->string('mail_class')->nullable();
            $table->string('package_shape')->nullable();
            $table->float('postage_amount')->default(0);
            $table->string('tracking_number')->nullable();
            $table->string('tracking_type')->nullable();
            $table->date('postmark_date')->nullable();
            $table->dateTime('transaction_datetime')->nullable();
            $table->string('transaction_id')->default('');
            $table->string('group_code')->nullable();
            $table->float('insured_fee')->deafult(0);
            $table->float('insurance_fee')->deafult(0);
            $table->float('tax_and_duty_amount')->deafult(0);
            $table->string('status')->nullable();
            $table->text('full_xml_source')->default('');
            $table->float('length')->deafult(0);
            $table->float('width')->deafult(0);
            $table->float('height')->deafult(0);
            $table->float('billed_weight')->deafult(0);
            $table->float('actual_weight')->deafult(0);
            $table->float('post_value')->deafult(0);
            $table->string('description')->nullable();
            $table->string('tax_and_duties_payer')->nullable();
            $table->string('recipient_tax_id')->nullable();
            $table->string('name')->nullable();
            $table->string('last_name')->deafult('');
            $table->string('company')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('address3')->nullable();
            $table->string('address4')->nullable();
            $table->string('city')->nullable();
            $table->string('state_city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('return_address')->default("MonogramOnline.com|575 Underhill Blvd|Suite 216|Syosset|NY 11791-3416");
            $table->string('carrier')->nullable();
            $table->enum('is_deleted', [0, 1])->default(0);

			$table->index('order_number');
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
        Schema::drop('shipping');
    }
}
