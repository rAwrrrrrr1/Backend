<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BookingSoccer;
use App\Models\BookingFutsal;
use App\Models\BookingBadminton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::all();

        if (!$transaksis) {
            return response()->json(['message' => 'Data Transaksi tidak ditemukan'], 404);
        }

        return response()->json(['data' => $transaksis], 200);
    }

    public function indexByUser($id)
    {
        $transaksi = Transaksi::where('id_user', $id)->get();

        if (!$transaksi) {
            return response()->json(['message' => 'Data Transaksi tidak ditemukan'], 404);
        }

        return response()->json(['data' => $transaksi], 200);
    }

    public function show($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Data Transaksi tidak ditemukan'], 404);
        }

        return response()->json(['data' => $transaksi], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|numeric',
            'id_booking' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transaksi = Transaksi::create([
            'id_user' => $request->input('id_user'),
            'id_booking' => $request->input('id_booking'),
            'total_harga' => $request->input('total_harga'),
            'status' => $request->input('status'),
        ]);

        return response()->json(['message' => 'Data Transaksi berhasil disimpan', 'data' => $transaksi], 200);
    }
}
