<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('show_home')->default(false);
            $table->integer('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->string('name')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('top')->default(false);
            $table->string('slug', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->string('meta_og', 255)->nullable();
            $table->enum('status', ['A', 'I', 'D'])->default('A'); //Active, Inactive, Deleted
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
        Schema::dropIfExists('brands');
    }
}
