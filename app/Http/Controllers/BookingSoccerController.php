<?php

namespace App\Http\Controllers;

use App\Models\Soccer;
use App\Models\Sesi;
use App\Models\BookingSoccer;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingSoccerController extends Controller
{
    public function index(Request $request, $tanggal)
    {
        $id_lapangan = $request->query('id_lapangan');

        try{
            $tangga = Carbon::parse($tanggal);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid'], 400);
        }

        $query = BookingSoccer::where('tanggal', $tanggal)
            ->with(['sesi', 'soccer'])
            ->orderBy('id_lapangan')
            ->orderBy('id_sesi');

        if ($id_lapangan) {
            $query = $query->where('id_lapangan', $id_lapangan);
        }

        $jadwalSoccers = $query->get();

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
            'no_booking' => 'required|string',
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
        $bookingSoccer->no_booking = $request->input('no_booking');

        $bookingSoccer->save();

        $miniSoccer = Soccer::where('id', $bookingSoccer->id_lapangan)->first();
        if (!$miniSoccer) {
            return response()->json(['message' => 'Lapangan tidak ditemukan'], 404);
        }
        $harga = $miniSoccer->harga;

        $transaksi = Transaksi::where('no_booking_soccer', $request->input('no_booking'))->first();

        if (!$transaksi) {
            $transaksi = new Transaksi();
            $transaksi->status_pembayaran = 'Belum Dibayar';
            $transaksi->no_booking_soccer = $request->input('no_booking');
            $transaksi->id_user = $request->input('id_user');
            $transaksi->total_pembayaran = $harga;
        } else {
            $transaksi->total_pembayaran += $harga;
        }

        $transaksi->save();

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
        $bookingSoccer->no_booking = null;

        $bookingSoccer->save();

        if($transaksi){
            $transaksi->delete();
        }

        return response()->json(['message' => 'Booking berhasil dibatalkan', 'data' => $bookingSoccer], 200);
    }

    public function showBooking($id)
    {
        $bookingSoccers = BookingSoccer::where('id_user', $id)
            ->with('sesi')
            ->orderBy('tanggal')
            ->orderBy('id_sesi')
            ->get();

        if ($bookingSoccers->isEmpty()) {
            return response()->json(['message' => 'Data Booking Soccer tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Data Booking Soccer Berhasil Ditemukan', 'data' => $bookingSoccers], 200);
    }

    public function detailBooking($id)
    {
        $bookingSoccer = BookingSoccer::where('id', $id)->first();

        if (!$bookingSoccer) {
            return response()->json(['message' => 'Data Booking Mini Soccer tidak ditemukan'], 404);
        }

        $transaksi = Transaksi::where('no_booking_soccer', $bookingSoccer->no_booking)->first();

        return response()->json(
            [
                'message' => 'Data Booking Mini Soccer Berhasil Ditemukan',
                'dataBooking' => $bookingSoccer,
                'dataTransaksi' => $transaksi
            ],
            200
        );
    }
}