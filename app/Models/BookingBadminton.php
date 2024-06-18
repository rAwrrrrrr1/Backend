<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingBadminton extends Model
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

    public function badminton()
    {
        return $this->belongsTo(Badminton::class, 'id_lapangan');
    }
    
    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'id_sesi');
    }
}