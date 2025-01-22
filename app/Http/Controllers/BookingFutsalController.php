<?php

namespace App\Http\Controllers;

use App\Models\Futsal;
use App\Models\Sesi;
use App\Models\BookingFutsal;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingFutsalController extends Controller
{
    public function index(Request $request, $tanggal)
    {
        $id_lapangan = $request->query('id_lapangan');

        try{
            $tangga = Carbon::parse($tanggal);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid'], 400);
        }

        $query = BookingFutsal::where('tanggal', $tanggal)
            ->with(['sesi', 'futsal'])
            ->orderBy('id_lapangan')
            ->orderBy('id_sesi');

        if ($id_lapangan) {
            $query = $query->where('id_lapangan', $id_lapangan);
        }

        $jadwalFutsals = $query->get();

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
            'no_booking' => 'required|string',
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
        $bookingFutsal->no_booking = $request->input('no_booking');

        $bookingFutsal->save();

        $futsal = Futsal::where('id', $bookingFutsal->id_lapangan)->first();
        if (!$futsal) {
            return response()->json(['message' => 'Lapangan tidak ditemukan'], 404);
        }
        $harga = $futsal->harga;

        $transaksi = Transaksi::where('no_booking_futsal', $request->input('no_booking'))->first();

        if (!$transaksi) {
            $transaksi = new Transaksi();
            $transaksi->status_pembayaran = 'Belum Dibayar';
            $transaksi->no_booking_futsal = $request->input('no_booking');
            $transaksi->id_user = $request->input('id_user');
            $transaksi->total_pembayaran = $harga;
        } else {
            $transaksi->total_pembayaran += $harga;
        }

        $transaksi->save();

        return response()->json(['message' => 'Data Booking Futsal berhasil ditambah', 'data' => $bookingFutsal], 200);
    }


    public function cancelBooking($id)
    {
        $bookingFutsal = BookingFutsal::find($id);
        $transaksi = Transaksi::where('no_booking_futsal', $bookingFutsal->no_booking);

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
        $bookingFutsal->no_booking = null;

        $bookingFutsal->save();
        
        if($transaksi) {
            $transaksi->delete();
        }

        return response()->json(['message' => 'Booking berhasil dibatalkan', 'data' => $bookingFutsal], 200);
    }

    public function showBooking($id)
    {
        $bookingFutsals = BookingFutsal::where('id_user', $id)
            ->with('sesi')
            ->orderBy('tanggal')
            ->orderBy('id_sesi')
            ->get();

        if ($bookingFutsals->isEmpty()) {
            return response()->json(['message' => 'Data Booking Futsal tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Data Booking Futsal Berhasil Ditemukan', 'data' => $bookingFutsals], 200);
    }

    public function detailBooking($id)
    {
        $bookingFutsal = BookingFutsal::where('id', $id)->first();

        if (!$bookingFutsal){
            return response()->json(['message' => 'Data Booking Futsal tidak ditemukan'], 400);
        }

        $transaksi = Transaksi::where('no_booking_futsal', $bookingFutsal->no_booking)->first();

        return response()->json(
            [
                'message' => 'Data Booking Futsal Berhasil Ditemukan',
                'dataBooking' => $bookingFutsal,
                'dataTransaksi' => $transaksi
            ],
            200
        );
    }
}