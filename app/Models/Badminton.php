<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badminton extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'keterangan',
        'gambar',
    ];

    public function bookingBadmintons()
    {
        return $this->hasMany(BookingBadminton::class, 'id_lapangan');
    }
}