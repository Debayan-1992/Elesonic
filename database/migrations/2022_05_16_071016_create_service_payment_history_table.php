<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicePaymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_payment_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stripe_token');
            $table->string('user_token', 255);
            $table->integer('service_id');
            $table->integer('user_id');
            $table->double('amount');
            $table->string('payment_date')->nullable(); 
            $table->text('payment_json')->nullalbe();
            $table->string('charge_id')->nullalbe();
            $table->string('txn_id')->nullalbe();
            $table->string('status')->nullalbe();
            $table->text('comment')->nullable();
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('service_payment_history');
    }
}
