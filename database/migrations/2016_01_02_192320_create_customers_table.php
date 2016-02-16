<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /*
     * Previous
        $table->bigInteger('customer_id')->nullable();
        $table->string('ship_full_name')->nullable();
        $table->string('company_name')->nullable();
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->string('shipping_address_1')->nullable();
        $table->string('shipping_address_2')->nullable();
        $table->string('ship_city')->nullable();
        $table->string('ship_state')->nullable();
        $table->string('ship_country')->nullable();
        $table->string('ship_zip')->nullable();
        $table->string('ship_phone')->nullable();
        $table->string('ship_email')->nullable();

        $table->string('bill_company_name')->nullable();
        $table->string('bill_first_name')->nullable();
        $table->string('bill_last_name')->nullable();
        $table->string('bill_address_1')->nullable();
        $table->string('bill_address_2')->nullable();
        $table->string('bill_city')->nullable();
        $table->string('bill_state')->nullable();
        $table->string('bill_country')->nullable();
        $table->string('bill_zip')->nullable();
        $table->string('bill_phone')->nullable();
        $table->string('bill_full_name')->nullable();
        $table->string('bill_email')->nullable();
        $table->string('mailing_list')->nullable();
    */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
            $table->string('ship_full_name')->nullable();
            $table->string('ship_first_name')->nullable();
            $table->string('ship_last_name')->nullable();
            $table->string('ship_company_name')->nullable();
            $table->string('ship_address_1')->nullable();
            $table->string('ship_address_2')->nullable();
            $table->string('ship_city')->nullable();
            $table->string('ship_state')->nullable();
            $table->string('ship_zip')->nullable();
            $table->string('ship_country')->nullable();
            $table->string('ship_phone')->nullable();
            $table->string('ship_email')->nullable();
            $table->string('shipping')->nullable();

            $table->string('bill_full_name')->nullable();
            $table->string('bill_first_name')->nullable();
            $table->string('bill_last_name')->nullable();
            $table->string('bill_company_name')->nullable();
            $table->string('bill_address_1')->nullable();
            $table->string('bill_address_2')->nullable();
            $table->string('bill_city')->nullable();
            $table->string('bill_state')->nullable();
            $table->string('bill_zip')->nullable();
            $table->string('bill_country')->nullable();
            $table->string('bill_phone')->nullable();
            $table->string('bill_email')->nullable();
            $table->string('bill_mailing_list')->nullable();
            $table->enum('is_deleted', array(0, 1))->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('customers');
    }
}
