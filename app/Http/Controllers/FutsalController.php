<?php

namespace App\Http\Controllers;

use App\Models\Futsal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FutsalController extends Controller
{
    public function index()
    {
        $futsals = Futsal::all();

        if (!$futsals) {
            return response()->json(['message' => 'Data Futsal tidak ditemukan'], 404);
        }

        return response()->json(['data' => $futsals], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $gambar = $request->file('gambar');
        $gambarPath = $gambar->store('public/images');

        $futsal = Futsal::create([
            'nama' => $request->input('nama'),
            'keterangan' => $request->input('keterangan'),
            'harga' => $request->input('harga'),
            'gambar' => $gambarPath,
        ]);

        return response()->json(['message' => 'Data Futsal berhasil disimpan', 'data' => $futsal], 200);
    }

    public function show($id)
    {
        $futsal = Futsal::find($id);

        if (!$futsal) {
            return response()->json(['message' => 'Data Futsal tidak ditemukan'], 404);
        }

        return response()->json(['data' => $futsal], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $futsal = Futsal::find($id);

        if (!$futsal) {
            return response()->json(['message' => 'Data Futsal tidak ditemukan'], 404);
        }
        
        $futsal->nama = $request->input('nama');
        $futsal->keterangan = $request->input('keterangan');
        $futsal->harga = $request->input('harga');

        
        if ($request->hasFile('gambar')) {
            Storage::delete($futsal->gambar);
            
            $gambar = $request->file('gambar');
            $gambarPath = $gambar->store('public/images');
            $futsal->gambar = $gambarPath;
        }

        $futsal->save();

        return response()->json(['message' => 'Data Futsal berhasil diupdate', 'data' => $futsal], 200);
    }

    public function destroy($id)
    {
        $futsal = Futsal::find($id);

        if (!$futsal) {
            return response()->json(['message' => 'Data Futsal tidak ditemukan'], 404);
        }

        $futsal->delete();

        return response()->json(['message' => 'Data Futsal berhasil dihapus', 'data' => $futsal], 200);
    }
}