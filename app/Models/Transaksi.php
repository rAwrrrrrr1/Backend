<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_pembayaran',
        'tanggal_pembayaran',
        'no_booking_badminton',
        'no_booking_futsal',
        'no_booking_soccer',
        'id_user',
        'total_pembayaran'
    ];
}
