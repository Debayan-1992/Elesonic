<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('b_title')->nullable();
            $table->text('b_description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['A', 'I', 'D'])->default('A');
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
        Schema::dropIfExists('landing_banners');
    }
}
