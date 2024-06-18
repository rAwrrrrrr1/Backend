<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        if (!$users) {
            return response()->json(['message' => 'Data User tidak ditemukan'], 404);
        }

        return response()->json(['data' => $users], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Data User tidak ditemukan'], 404);
        }

        return response()->json(['data' => $user], 200);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'password' => ['required'],
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Data User tidak ditemukan'], 404);
        }

        $user->password = bcrypt($request->password);

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal Mengganti Password', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Berhasil Mengganti Password', 'data' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Data User tidak ditemukan'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Data User berhasil dihapus', 'data' => $user], 200);
    }
}