<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
			$table->string('template_name');
			$table->enum('show_header', [0, 1])->default(1);
			$table->string('repeated_fields')->nullable();
			$table->string('delimited_char')->nullable();
			$table->enum('break_kits', [0, 1])->default(0);
			$table->enum('is_active', [0, 1])->default(1);
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
        Schema::drop('templates');
    }
}
