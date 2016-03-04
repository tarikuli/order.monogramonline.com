<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('batch_code');
            $table->string('batch_route_name');
            $table->integer('batch_max_units');
			$table->integer('export_template')->nullable();
            $table->string('batch_options')->nullable();
            $table->enum('is_deleted', array(0, 1))->default(0);

			$table->index('batch_code');
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
        Schema::drop('batch_routes');
    }
}
