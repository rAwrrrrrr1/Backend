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
use Carbon\Carbon;
use Mindtrans\Config;
use Mindtrans\Snap;

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

    public function show($type, $no_booking)
    {
        $transaksi = null;

        switch ($type) {
            case 'Badminton':
                $transaksi = Transaksi::where('no_booking_badminton', $no_booking)->first();
                break;
            case 'Futsal':
                $transaksi = Transaksi::where('no_booking_futsal', $no_booking)->first();
                break;
            case 'Soccer':
                $transaksi = Transaksi::where('no_booking_soccer', $no_booking)->first();
                break;
            default:
                return response()->json(['message' => 'Invalid booking type'], 400);
        }

        if (!$transaksi) {
            return response()->json(['message' => 'Data Transaksi tidak ditemukan'], 404);
        }

        return response()->json(['data' => $transaksi], 200);
    }

    public function payment(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Data Transaksi tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation errors:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $bukti_pembayaran = $request->file('bukti_pembayaran');
        $gambarPath = $bukti_pembayaran->store('public/images/bukti_pembayaran/' . $transaksi->no_booking);
        
        $transaksi->bukti_pembayaran = $gambarPath;
        $transaksi->status_pembayaran = 'Menunggu Konfirmasi';
        $transaksi->tanggal_pembayaran = Carbon::now();

        $transaksi->save();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Upload Bukti Pembayaran'
        ], 200);
    }

    public function confirmation($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Data Transaksi tidak ditemukan'], 404);
        }

        $transaksi->status_pembayaran = 'Sudah Dibayar';

        $transaksi->save();

        return response()->json(['message' => 'Pembayaran Berhasil Dikonfirmasi'], 200);
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'id_user' => 'required|numeric',
    //         'id_booking' => 'required|numeric',
    //         'total_harga' => 'required|numeric',
    //         'status' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }

    //     $transaksi = Transaksi::create([
    //         'id_user' => $request->input('id_user'),
    //         'id_booking' => $request->input('id_booking'),
    //         'total_harga' => $request->input('total_harga'),
    //         'status' => $request->input('status'),
    //     ]);

    //     return response()->json(['message' => 'Data Transaksi berhasil disimpan', 'data' => $transaksi], 200);
    // }

    // public function __construct()
    // {
    //     // Set Midtrans configuration
    //     Config::$serverKey = config('midtrans.server_key');
    //     Config::$isProduction = config('midtrans.is_production');
    //     Config::$isSanitized = config('midtrans.sanitize');
    //     Config::$is3ds = config('midtrans.3ds');
    // }

    // public function createTransaction(Request $request)
    // {
    //     // Retrieve booking details from the request
    //     $bookingBadminton = $request->input('no_booking_badminton');
    //     $bookingFutsal = $request->input('no_booking_futsal');
    //     $bookingSoccer = $request->input('no_booking_soccer');
    //     $userId = $request->input('id_user');
    //     $totalAmount = $request->input('total_pembayaran');

    //     // Create a new transaction entry in the database
    //     $transaksi = new Transaksi();
    //     $transaksi->status_pembayaran = 'pending';
    //     $transaksi->tanggal_pembayaran = now();
    //     $transaksi->no_booking_badminton = $bookingBadminton;
    //     $transaksi->no_booking_futsal = $bookingFutsal;
    //     $transaksi->no_booking_soccer = $bookingSoccer;
    //     $transaksi->id_user = $userId;
    //     $transaksi->total_pembayaran = $totalAmount;
    //     $transaksi->save();

    //     // Prepare Midtrans transaction data
    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $transaksi->id,
    //             'gross_amount' => $totalAmount,
    //         ],
    //         'customer_details' => [
    //             'first_name' => 'John', // You can replace this with user data
    //             'email' => 'john@example.com', // You can replace this with user data
    //         ],
    //         'item_details' => [
    //             [
    //                 'id' => 'booking-badminton',
    //                 'price' => 100000, // example price
    //                 'quantity' => 1,
    //                 'name' => 'Badminton Booking',
    //             ],
    //             [
    //                 'id' => 'booking-futsal',
    //                 'price' => 150000, // example price
    //                 'quantity' => 1,
    //                 'name' => 'Futsal Booking',
    //             ],
    //         ]
    //     ];

    //     // Generate the Midtrans payment URL
    //     $snapToken = Snap::getSnapToken($params);

    //     return response()->json(['token' => $snapToken, 'transaksi' => $transaksi]);
    // }

    // public function handleMidtransNotification(Request $request)
    // {
    //     $notification = new \Midtrans\Notification();
    //     $transactionStatus = $notification->transaction_status;
    //     $orderId = $notification->order_id;

    //     // Update transaction status based on Midtrans notification
    //     $transaksi = Transaksi::find($orderId);
    //     if ($transactionStatus == 'settlement') {
    //         $transaksi->status_pembayaran = 'paid';
    //     } elseif ($transactionStatus == 'pending') {
    //         $transaksi->status_pembayaran = 'pending';
    //     } elseif ($transactionStatus == 'deny') {
    //         $transaksi->status_pembayaran = 'failed';
    //     }

    //     $transaksi->save();

    //     return response()->json(['message' => 'Transaction status updated']);
    // }
}
