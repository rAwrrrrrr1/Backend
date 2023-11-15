<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Futsal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'keterangan',
        'gambar',
    ];
}