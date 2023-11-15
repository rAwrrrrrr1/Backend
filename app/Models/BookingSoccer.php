<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSoccer extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'tanggal',
        'id_lapangan',
        'id_sesi',
        'id_user',
        'nama_penyewa',
    ];
}