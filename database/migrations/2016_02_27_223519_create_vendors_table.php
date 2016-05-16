<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
			$table->string('vendor_name');
			$table->string('email')->unique();
			$table->string('zip_code')->nullable();
			$table->string('state')->nullable();
			$table->string('phone_number');
			$table->string('country');
			$table->enum('is_deleted', array(0, 1))->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('vendors');
    }
}
