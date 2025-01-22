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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('status_pembayaran');
            $table->date('tanggal_pembayaran');
            //foreign key dari booking badminton
            $table->unsignedBigInteger('no_booking_badminton');
            $table->foreign('no_booking_badminton')->references('no_booking')->on('booking_badmintons');
            //foreign key dari booking futsal
            $table->unsignedBigInteger('no_booking_futsal');
            $table->foreign('no_booking_futsal')->references('no_booking')->on('booking_futsals');
            //foreign key dari booking soccer
            $table->unsignedBigInteger('no_booking_soccer');
            $table->foreign('no_booking_soccer')->references('no_booking')->on('booking_soccers');
            //foreign key dari user
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users');
            $table->float('total_pembayaran');
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
        Schema::dropIfExists('transaksis');
    }
};
