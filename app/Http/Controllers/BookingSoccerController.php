<?php

namespace App\Http\Controllers;

use App\Models\Soccer;
use App\Models\Sesi;
use App\Models\BookingSoccer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingSoccerController extends Controller
{
    public function index($tanggal)
    {
        try {
            $tanggal = Carbon::parse($tanggal);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid'], 400);
        }

        $jadwalSoccers = BookingSoccer::where('tanggal', $tanggal)
            ->orderBy('id_lapangan')
            ->orderBy('id_sesi')
            ->get();

        if ($jadwalSoccers->isEmpty()) {
            return response()->json(['message' => 'Jadwal Soccer tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Jadwal Soccer Berhasil Ditemukan', 'data' => $jadwalSoccers], 200);
    }

    public function addBooking(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|numeric',
            'nama_penyewa' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $bookingSoccer = BookingSoccer::find($id);

        if (!$bookingSoccer) {
            return response()->json(['message' => 'Data Soccer tidak ditemukan'], 404);
        }

        if ($bookingSoccer->status === 'booked') {
            return response()->json(['message' => 'Jadwal Sudah Booked'], 400);
        }

        $bookingSoccer->status = 'booked';
        $bookingSoccer->id_user = $request->input('id_user');
        $bookingSoccer->nama_penyewa = $request->input('nama_penyewa');

        $bookingSoccer->save();

        return response()->json(['message' => 'Data Booking Soccer berhasil ditambah', 'data' => $bookingSoccer], 200);
    }

    public function cancelBooking($id)
    {
        $bookingSoccer = BookingSoccer::find($id);

        if (!$bookingSoccer) {
            return response()->json(['message' => 'Data Soccer tidak ditemukan'], 404);
        }
        
        $loggedInUserId = Auth::id();
        if ($bookingSoccer->id_user !== $loggedInUserId) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk membatalkan booking ini'], 403);
        }
        
        $bookingSoccer->status = 'kosong';
        $bookingSoccer->id_user = null;
        $bookingSoccer->nama_penyewa = null;

        $bookingSoccer->save();

        return response()->json(['message' => 'Booking berhasil dibatalkan', 'data' => $bookingSoccer], 200);
    }

    public function showBooking($id)
    {
        $bookingSoccers = BookingSoccer::where('id_user', $id)
            ->orderBy('tanggal')
            ->orderBy('id_sesi')
            ->get();

        if ($bookingSoccers->isEmpty()) {
            return response()->json(['message' => 'Data Booking Soccer tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Data Booking Soccer Berhasil Ditemukan', 'data' => $bookingSoccers], 200);
    }
}