<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('iscancel', ['Y','N'])->default('N');
            $table->string('name', 200)->nullable();
            $table->string('added_by')->default('admin');
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->enum('type', ['new', 'old'])->default('new'); //product type
            $table->integer('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->string('photos', 2000)->nullable();
            $table->string('thumbnail_img', 100)->nullable();
            $table->text('description')->nullable();
            $table->double('unit_price', 12,2)->nullable();
            $table->double('purchase_price', 12,2)->nullable();
            $table->integer('variant_product')->default(0);
            $table->text('attributes')->nullable();
            $table->text('choice_options')->nullable();
            $table->text('colors')->nullable();
            $table->text('variations')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('featured')->default(false);
            $table->integer('current_stock')->default(0);
            $table->string('unit', 20)->nullable();
            $table->integer('min_qty')->default(1);
            $table->double('discount', 12,2)->nullable();
            $table->enum('discount_type',['flat', '%'])->default('flat');
            $table->double('tax', 12,2)->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_img', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('attribute', 255)->nullable();
            $table->integer('refund_replace_day')->default(0);
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
        Schema::dropIfExists('products');
    }
}
