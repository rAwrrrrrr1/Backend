<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Sesi;
use App\Models\Badminton;
use App\Models\Futsal;
use App\Models\Soccer;
use App\Models\BookingBadminton;
use App\Models\BookingFutsal;
use App\Models\BookingSoccer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class SystemController extends Controller
{
    

    public function generateJadwalThisMonth()
    {
        $sesis = Sesi::all();
        $badmintons = Badminton::all();
        $futsals = Futsal::all();
        $soccers = Soccer::all();
        $bulanIni = Carbon::now()->startOfMonth();
        $sebulanKemudian = $bulanIni->copy()->addMonth();
        
        for ($tanggal = $bulanIni; $tanggal->lt($sebulanKemudian); $tanggal->addDay()) {            
            foreach ($sesis as $sesi) {
                foreach ($badmintons as $badminton) {
                    $jadwalExist = BookingBadminton::where('tanggal', $tanggal)
                        ->where('id_sesi', $sesi->id)
                        ->where('id_lapangan', $badminton->id)
                        ->exists();

                    if (!$jadwalExist) {
                        BookingBadminton::create([
                            'status' => 'kosong',
                            'tanggal' => $tanggal,
                            'id_lapangan' => $badminton->id,
                            'id_sesi' => $sesi->id,
                            'id_user' => null,
                            'nama_penyewa' => null,
                        ]);
                    }
                }
                
                foreach ($futsals as $futsal) {
                    $jadwalExist = BookingFutsal::where('tanggal', $tanggal)
                        ->where('id_sesi', $sesi->id)
                        ->where('id_lapangan', $futsal->id)
                        ->exists();

                    if (!$jadwalExist) {
                        BookingFutsal::create([
                            'status' => 'kosong',
                            'tanggal' => $tanggal,
                            'id_lapangan' => $futsal->id,
                            'id_sesi' => $sesi->id,
                            'id_user' => null,
                            'nama_penyewa' => null,
                        ]);
                    }
                }
                
                foreach ($soccers as $soccer) {
                    $jadwalExist = BookingSoccer::where('tanggal', $tanggal)
                        ->where('id_sesi', $sesi->id)
                        ->where('id_lapangan', $soccer->id)
                        ->exists();

                    if (!$jadwalExist) {
                        BookingSoccer::create([
                            'status' => 'kosong',
                            'tanggal' => $tanggal,
                            'id_lapangan' => $soccer->id,
                            'id_sesi' => $sesi->id,
                            'id_user' => null,
                            'nama_penyewa' => null,
                        ]);
                    }
                }
            }
        }

        return response()->json(['message' => 'Data Booking Bulan Ini Berhasil Digenerate'], 200);
    }

    public function generateJadwalNextMonth()
    {
        $sesis = Sesi::all();
        $badmintons = Badminton::all();
        $futsals = Futsal::all();
        $soccers = Soccer::all();
        $bulanIni = Carbon::now()->startOfMonth();
        $bulanDepan = $bulanIni->copy()->addMonth()->startOfMonth();
        $batasAkhir = $bulanDepan->copy()->addMonth()->startOfMonth(); // Add this line

        for ($tanggal = $bulanDepan; $tanggal->lt($batasAkhir); $tanggal->addDay()) {
            foreach ($sesis as $sesi) {
                foreach ($badmintons as $badminton) {
                    $jadwalExist = BookingBadminton::where('tanggal', $tanggal)
                        ->where('id_sesi', $sesi->id)
                        ->where('id_lapangan', $badminton->id)
                        ->exists();

                    if (!$jadwalExist) {
                        BookingBadminton::create([
                            'status' => 'kosong',
                            'tanggal' => $tanggal,
                            'id_lapangan' => $badminton->id,
                            'id_sesi' => $sesi->id,
                            'id_user' => null,
                            'nama_penyewa' => null,
                        ]);
                    }
                }
            }

            foreach ($sesis as $sesi) {
                foreach ($futsals as $futsal) {
                    $jadwalExist = BookingFutsal::where('tanggal', $tanggal)
                        ->where('id_sesi', $sesi->id)
                        ->where('id_lapangan', $futsal->id)
                        ->exists();

                    if (!$jadwalExist) {
                        BookingFutsal::create([
                            'status' => 'kosong',
                            'tanggal' => $tanggal,
                            'id_lapangan' => $futsal->id,
                            'id_sesi' => $sesi->id,
                            'id_user' => null,
                            'nama_penyewa' => null,
                        ]);
                    }
                }
            }
            
            foreach ($sesis as $sesi) {
                foreach ($soccers as $soccer) {
                    $jadwalExist = BookingSoccer::where('tanggal', $tanggal)
                        ->where('id_sesi', $sesi->id)
                        ->where('id_lapangan', $soccer->id)
                        ->exists();

                    if (!$jadwalExist) {
                        BookingSoccer::create([
                            'status' => 'kosong',
                            'tanggal' => $tanggal,
                            'id_lapangan' => $soccer->id,
                            'id_sesi' => $sesi->id,
                            'id_user' => null,
                            'nama_penyewa' => null,
                        ]);
                    }
                }
            }
        }

        return response()->json(['message' => 'Data Booking Bulan Depan Berhasil Digenerate'], 200);
    }
    
    public function clearCache()
    {
        $today = Carbon::now();
        
        BookingBadminton::where('status', 'kosong')
            ->where('tanggal', '<', $today)
            ->delete();
        
        BookingFutsal::where('status', 'kosong')
            ->where('tanggal', '<', $today)
            ->delete();
        
        BookingSoccer::where('status', 'kosong')
            ->where('tanggal', '<', $today)
            ->delete();

        return response()->json(['message' => 'Data Booking yang Expired berhasil dihapus'], 200);
    }

    public function setMaintenance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $system = System::find(1);

        if (!$system) {
            return response()->json(['message' => 'Error'], 404);
        }
        
        $system->status = $request->input('status');

        $system->save();

        return response()->json(['message' => 'Sukses'], 200);
    }
    
    public function setAllowBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $system = System::find(2);

        if (!$system) {
            return response()->json(['message' => 'Error'], 404);
        }
        
        $system->status = $request->input('status');

        $system->save();

        return response()->json(['message' => 'Sukses'], 200);
    }
}