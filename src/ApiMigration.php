<?php
namespace Amuz\XePlugin\AuthAPI;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use XeDB;
use DB;

class ApiMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('user_id', 36)->nullable();
            $table->string('site_key')->default('default')->comment('site key. for multi web site support.');

            $table->string('name');
            $table->string('key', 64);
            $table->longText('secret');
            $table->boolean('active')->default(1);

            $table->longText('allow_services');
            $table->longText('allow_ip');

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('name');
            $table->index('key');
        });

        Schema::create('api_key_admin_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('api_key_id');
            $table->ipAddress('ip_address');
            $table->string('event');
            $table->timestamps();

            $table->index('ip_address');
            $table->index('event');
            $table->foreign('api_key_id')->references('id')->on('api_keys');
        });

        Schema::create('api_key_access_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('api_key_id');
            $table->ipAddress('ip_address');
            $table->text('url');
            $table->timestamps();

            $table->index('ip_address');
            $table->foreign('api_key_id')->references('id')->on('api_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('api_key_admin_events');
        Schema::dropIfExists('api_key_access_events');
    }
}
