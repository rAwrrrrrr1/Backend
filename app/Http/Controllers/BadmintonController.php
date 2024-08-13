<?php

namespace App\Http\Controllers;

use App\Models\Badminton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BadmintonController extends Controller
{
    public function index()
    {
        $badmintons = Badminton::all();

        if (!$badmintons) {
            return response()->json(['message' => 'Data Badminton tidak ditemukan'], 404);
        }

        return response()->json(['data' => $badmintons], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $gambar = $request->file('gambar');
        
        // Generate nama file yang terenkripsi
        $encryptedFileName = hash('sha256', time()) . '.' . $gambar->getClientOriginalExtension();
        
        // Simpan file gambar dengan nama terenkripsi ke dalam direktori 'public/images'
        $gambarPath = $gambar->storeAs('public/images', $encryptedFileName);

        $badminton = Badminton::create([
            'nama' => $request->input('nama'),
            'keterangan' => $request->input('keterangan'),
            'harga' => $request->input('harga'),
            'gambar' => $gambarPath,
        ]);

        return response()->json(['success' => true, 'message' => 'Data Badminton berhasil disimpan', 'data' => $badminton], 200);
    }

    public function show($id)
    {
        $badminton = Badminton::find($id);

        if (!$badminton) {
            return response()->json(['message' => 'Data Badminton tidak ditemukan'], 404);
        }

        return response()->json(['data' => $badminton], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'harga' => 'required|numeric',
            // 'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $badminton = Badminton::find($id);

        if (!$badminton) {
            return response()->json(['message' => 'Data Badminton tidak ditemukan'], 404);
        }
        
        $badminton->nama = $request->input('nama');
        $badminton->keterangan = $request->input('keterangan');
        $badminton->harga = $request->input('harga');

        
        // if ($request->hasFile('gambar')) {
        //     Storage::delete($badminton->gambar);
            
        //     $gambar = $request->file('gambar');
        //     $gambarPath = $gambar->store('public/images');
        //     $badminton->gambar = $gambarPath;
        // }

        $badminton->save();

        return response()->json(['message' => 'Data Badminton berhasil diupdate', 'data' => $badminton], 200);
    }

    public function destroy($id)
    {
        $badminton = Badminton::find($id);

        if (!$badminton) {
            return response()->json(['message' => 'Data Badminton tidak ditemukan'], 404);
        }

        $badminton->delete();

        return response()->json(['message' => 'Data Badminton berhasil dihapus', 'data' => $badminton], 200);
    }
}