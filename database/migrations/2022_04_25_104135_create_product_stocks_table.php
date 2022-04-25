<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('position')->default(0);
            $table->integer('product_id')->nullable();
            $table->string('variant', 255)->nullable();
            $table->tinyInteger('set_default')->default(0);
            $table->string('sku', 255)->nullable();
            $table->double('price',12,2)->default(0);
            $table->integer('qty')->default(0);
            $table->string('color', 255)->nullable();
            $table->string('color_name', 255)->nullable();
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('product_stocks');
    }
}
