<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_options', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('template_id');
			$table->enum('line_item_field', [0, 1])->default(1);
			$table->string('option_name');
			$table->string('option_category');
			$table->text('value');
			$table->string('width')->nullable();
			$table->string('format');
			$table->integer('template_order');
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
        Schema::drop('template_options');
    }
}
