<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('app_user_id')->index()->unsigned();
            $table->string('uuid' , 191)->unique()->index()->nullable();
            $table->enum('device_type' , ['android' , 'ios'])->nullable();
            $table->string('notification_token')->nullable();
            $table->string('verification_code')->nullable();
            $table->boolean('phone_verification_status')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('api_token')->nullable();
            $table->decimal('latitude' , 10 ,7)->nullable();
            $table->decimal('longitude' , 10 ,7)->nullable();
            $table->integer('tile_id')->index()->unsigned()->nullable();
            $table->integer('city_id')->index()->unsigned()->nullable();
            $table->integer('tx')->nullable();
            $table->integer('rx')->nullable();
            $table->integer('version')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_device_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_device_id')->index()->unsigned();
            $table->enum('action' , ['create' , 'verify' , 'report'])->nullable();
            $table->string('data')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_device_histories');
    }
}
