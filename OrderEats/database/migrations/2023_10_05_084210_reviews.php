<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shipper_id');
            $table->integer('rating');
            $table->text('comment');
            $table->date('date');

            $table->foreign('order_id')->references('order_id')->on('Orders');
            $table->foreign('user_id')->references('user_id')->on('User');
            $table->foreign('shipper_id')->references('user_id')->on('User');
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
        Schema::dropIfExists('reviews');
    }
}
