<?php

namespace App\Http\Controllers;

use App\Models\Futsal;
use App\Models\Sesi;
use App\Models\BookingFutsal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingFutsalController extends Controller
{
    public function index($tanggal)
    {
        try {
            $tanggal = Carbon::parse($tanggal);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid'], 400);
        }

        $jadwalFutsals = BookingFutsal::where('tanggal', $tanggal)
            ->orderBy('id_lapangan')
            ->orderBy('id_sesi')
            ->get();

        if ($jadwalFutsals->isEmpty()) {
            return response()->json(['message' => 'Jadwal Futsal tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Jadwal Futsal Berhasil Ditemukan', 'data' => $jadwalFutsals], 200);
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

        $bookingFutsal = BookingFutsal::find($id);

        if (!$bookingFutsal) {
            return response()->json(['message' => 'Data Futsal tidak ditemukan'], 404);
        }

        if ($bookingFutsal->status === 'booked') {
            return response()->json(['message' => 'Jadwal Sudah Booked'], 400);
        }

        $bookingFutsal->status = 'booked';
        $bookingFutsal->id_user = $request->input('id_user');
        $bookingFutsal->nama_penyewa = $request->input('nama_penyewa');

        $bookingFutsal->save();

        return response()->json(['message' => 'Data Booking Futsal berhasil ditambah', 'data' => $bookingFutsal], 200);
    }

    public function cancelBooking($id)
    {
        $bookingFutsal = BookingFutsal::find($id);

        if (!$bookingFutsal) {
            return response()->json(['message' => 'Data Futsal tidak ditemukan'], 404);
        }
        
        $loggedInUserId = Auth::id();
        if ($bookingFutsal->id_user !== $loggedInUserId) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk membatalkan booking ini'], 403);
        }
        
        $bookingFutsal->status = 'kosong';
        $bookingFutsal->id_user = null;
        $bookingFutsal->nama_penyewa = null;

        $bookingFutsal->save();

        return response()->json(['message' => 'Booking berhasil dibatalkan', 'data' => $bookingFutsal], 200);
    }

    public function showBooking($id)
    {
        $bookingFutsals = BookingFutsal::where('id_user', $id)
            ->orderBy('tanggal')
            ->orderBy('id_sesi')
            ->get();

        if ($bookingFutsals->isEmpty()) {
            return response()->json(['message' => 'Data Booking Futsal tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Data Booking Futsal Berhasil Ditemukan', 'data' => $bookingFutsals], 200);
    }
}