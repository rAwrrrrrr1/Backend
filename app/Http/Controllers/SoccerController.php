<?php

namespace App\Http\Controllers;

use App\Models\Soccer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SoccerController extends Controller
{
    public function index()
    {
        $soccers = Soccer::all();

        if (!$soccers) {
            return response()->json(['message' => 'Data Soccer tidak ditemukan'], 404);
        }

        return response()->json(['data' => $soccers], 200);
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

        $soccer = Soccer::create([
            'nama' => $request->input('nama'),
            'keterangan' => $request->input('keterangan'),
            'harga' => $request->input('harga'),
            'gambar' => $gambarPath,
        ]);

        return response()->json(['message' => 'Data Soccer berhasil disimpan', 'data' => $soccer], 200);
    }

    public function show($id)
    {
        $soccer = Soccer::find($id);

        if (!$soccer) {
            return response()->json(['message' => 'Data Soccer tidak ditemukan'], 404);
        }

        return response()->json(['data' => $soccer], 200);
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

        $soccer = Soccer::find($id);

        if (!$soccer) {
            return response()->json(['message' => 'Data Soccer tidak ditemukan'], 404);
        }
        
        $soccer->nama = $request->input('nama');
        $soccer->keterangan = $request->input('keterangan');
        $soccer->harga = $request->input('harga');

        
        if ($request->hasFile('gambar')) {
            Storage::delete($soccer->gambar);
            
            $gambar = $request->file('gambar');
            $gambarPath = $gambar->store('public/images');
            $soccer->gambar = $gambarPath;
        }

        $soccer->save();

        return response()->json(['message' => 'Data Soccer berhasil diupdate', 'data' => $soccer], 200);
    }

    public function destroy($id)
    {
        $soccer = Soccer::find($id);

        if (!$soccer) {
            return response()->json(['message' => 'Data Soccer tidak ditemukan'], 404);
        }

        $soccer->delete();

        return response()->json(['message' => 'Data Soccer berhasil dihapus', 'data' => $soccer], 200);
    }
}
