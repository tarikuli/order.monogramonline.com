<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('production_category_code');
            $table->string('production_category_description');
            $table->string('production_category_display_order');
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
        Schema::drop('production_categories');
    }
}
