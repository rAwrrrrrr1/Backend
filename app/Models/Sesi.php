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

    public function bookingBadmintons(){
        return $this->hasMany(BookingBadminton::class, 'id_sesi');
    }

    public function bookingFutsals(){
        return $this->hasMany(BookingFutsal::class, 'id_sesi');
    }

    public function bookingSoccers(){
        return $this->hasMany(BookingSoccer::class, 'id_sesi');
    }
}