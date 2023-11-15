<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soccer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'keterangan',
        'gambar',
    ];
}