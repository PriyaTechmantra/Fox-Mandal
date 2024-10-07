<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lms_notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('book_id');

            $table->unsignedBigInteger('sender_id');

            $table->unsignedBigInteger('receiver_id');

            $table->string('message');

            $table->integer('notification_type')
                ->comment('Type of notification: 1=request, 2=transfer, 3=return');

            $table->boolean('is_read')->default(false)
                ->comment('0=unread, 1=read');

            $table->timestamps();

            $table->foreign('sender_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('receiver_id')
                ->references('id')->on('users')
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
        Schema::dropIfExists('lms_notifications');
    }
};


