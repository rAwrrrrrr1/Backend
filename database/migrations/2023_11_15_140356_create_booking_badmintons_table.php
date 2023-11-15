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
        Schema::create('booking_badmintons', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->date('tanggal');

            $table->unsignedBigInteger('id_lapangan');
            $table->foreign('id_lapangan')->references('id')->on('badmintons')->onDelete('cascade');

            $table->unsignedBigInteger('id_sesi');
            $table->foreign('id_sesi')->references('id')->on('sesis')->onDelete('cascade');

            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            $table->string('nama_penyewa')->nullable();
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
        Schema::dropIfExists('booking_badmintons');
    }
};