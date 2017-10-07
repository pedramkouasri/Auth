<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reg_message_id')->index()->unsigned()->nullable();
            $table->char('phone' , '12')->index();
            $table->boolean('is_active')->default(false);
            $table->string('invite_code')->nullable();
            $table->string('telegram_invite_code')->nullable();
            $table->boolean('is_invite_code_used')->default(false);
            $table->boolean('is_charkhune')->default(false);
            $table->string('jhoobin_token' , 12)->nullable();
            $table->integer('prize_count')->default(0);
            $table->integer('level')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('app_user_informations', function (Blueprint $table) {
            $table->integer('app_user_id')->unsigned()->index();
            $table->integer('city_id')->unsigned()->index()->nullable();
            $table->boolean('gender' , ['male' , 'female'])->nullable();
            $table->date('birth_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_users');
        Schema::dropIfExists('app_user_informations');
    }
}
