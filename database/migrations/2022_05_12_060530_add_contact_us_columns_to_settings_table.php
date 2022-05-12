<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactUsColumnsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->text('address3')->nullable();
            $table->text('map_embed_link')->nullable();
            $table->string('site_email')->nullable();
            $table->string('site_link')->nullable();
            $table->string('site_number')->nullable();
            $table->string('site_number_office_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dropColumn(['address1', 'address2', 'address3', 'map_embed_link', 'site_email', 'site_link', 'site_number', 'site_number_office_name']);
        });
    }
}
