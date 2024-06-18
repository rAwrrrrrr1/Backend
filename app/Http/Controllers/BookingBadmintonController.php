<?php

namespace App\Http\Controllers;

use App\Models\Badminton;
use App\Models\Sesi;
use App\Models\BookingBadminton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingBadmintonController extends Controller
{
    public function index(Request $request, $tanggal)
    {
        $id_lapangan = $request->query('id_lapangan'); // Retrieve id_lapangan from query parameters

        try {
            $tanggal = Carbon::parse($tanggal);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid'], 400);
        }

        $query = BookingBadminton::where('tanggal', $tanggal)
            ->with(['sesi', 'badminton']) // Eager load the related Sesi and Badminton data
            ->orderBy('id_lapangan')
            ->orderBy('id_sesi');

        if ($id_lapangan) {
            $query->where('id_lapangan', $id_lapangan);
        }

        $jadwalBadmintons = $query->get();

        if ($jadwalBadmintons->isEmpty()) {
            return response()->json(['message' => 'Jadwal Badminton tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Jadwal Badminton Berhasil Ditemukan', 'data' => $jadwalBadmintons], 200);
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

        $bookingBadminton = BookingBadminton::find($id);

        if (!$bookingBadminton) {
            return response()->json(['message' => 'Data Badminton tidak ditemukan'], 404);
        }

        if ($bookingBadminton->status === 'booked') {
            return response()->json(['message' => 'Jadwal Sudah Booked'], 400);
        }

        $bookingBadminton->status = 'booked';
        $bookingBadminton->id_user = $request->input('id_user');
        $bookingBadminton->nama_penyewa = $request->input('nama_penyewa');

        $bookingBadminton->save();

        return response()->json(['message' => 'Data Booking Badminton berhasil ditambah', 'data' => $bookingBadminton], 200);
    }

    public function cancelBooking($id)
    {
        $bookingBadminton = BookingBadminton::find($id);

        if (!$bookingBadminton) {
            return response()->json(['message' => 'Data Badminton tidak ditemukan'], 404);
        }
        
        $loggedInUserId = Auth::id();
        if ($bookingBadminton->id_user !== $loggedInUserId) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk membatalkan booking ini'], 403);
        }
        
        $bookingBadminton->status = 'kosong';
        $bookingBadminton->id_user = null;
        $bookingBadminton->nama_penyewa = null;

        $bookingBadminton->save();

        return response()->json(['message' => 'Booking berhasil dibatalkan', 'data' => $bookingBadminton], 200);
    }

    public function showBooking($id)
    {

        $bookingBadmintons = BookingBadminton::where('id_user', $id)
            ->with('sesi')
            ->orderBy('tanggal')
            ->orderBy('id_sesi')
            ->get();

        if ($bookingBadmintons->isEmpty()) {
            return response()->json(['message' => 'Data Booking Badminton tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Data Booking Badminton Berhasil Ditemukan', 'data' => $bookingBadmintons], 200);
    }

    // $waktu_sesi = DB::table('booking_badmintons')
    //         ->join('sesis', 'booking_badmintons.id_sesi', '=', 'sesi.id')
    //         ->select('sesis.waktu'),
}