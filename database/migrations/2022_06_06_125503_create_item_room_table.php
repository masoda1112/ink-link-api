<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
            $table->foreignId('room_id');
            $table->integer('using_time');
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            $table
            ->foreign('item_id')
            ->references('id')
            ->on('items')
            ->onDelete('cascade');

            $table
            ->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_room');
    }
};
