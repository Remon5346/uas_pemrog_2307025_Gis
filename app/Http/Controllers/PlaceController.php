<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place; 

class PlaceController extends Controller
{
    // 1. READ: Tampilkan halaman utama & data
    public function index()
    {
        $places = Place::orderBy('created_at', 'desc')->get();
        return view('home', compact('places'));
    }

    // 2. CREATE: Simpan lokasi baru
    public function store(Request $request)
    {
        // Validasi data tidak boleh kosong
        $request->validate([
            'name' => 'required',
            'lat' => 'required',
            'lon' => 'required'
        ]);

        Place::create([
            'name' => $request->name,
            'latitude' => $request->lat,
            'longitude' => $request->lon,
            'notes' => 'Lokasi tersimpan dari Peta', 
        ]);

        return redirect()->back()->with('success', 'Lokasi berhasil disimpan!');
    }

    // 3. UPDATE: Edit catatan
    public function update(Request $request, $id)
    {
        $place = Place::find($id);
        $place->update(['notes' => $request->notes]);
        return redirect()->back()->with('success', 'Catatan berhasil diupdate!');
    }

    // 4. DELETE: Hapus data
    public function destroy($id)
    {
        $place = Place::find($id);
        $place->delete();
        return redirect()->back()->with('success', 'Lokasi dihapus!');
    }
}