<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use HasFactory;

    protected $fillable = [
        'waktu',
    ];

    public function bookingBadmintons()
    {
        return $this->hasMany(BookingBadminton::class, 'id_sesi');
    }
}