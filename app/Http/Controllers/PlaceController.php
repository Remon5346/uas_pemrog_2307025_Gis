<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaceController extends Controller
{
    // 1. KUNCI HALAMAN: Wajib Login
    public function __construct()
    {
        $this->middleware('auth');
    }

    // MENAMPILKAN DATA (READ)
    public function index()
    {
        $places = Place::where('user_id', Auth::id())->get();
        return view('home', compact('places'));
    }

    // MENYIMPAN DATA BARU (CREATE)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $place = new Place();
        $place->name = $request->name;
        $place->latitude = $request->latitude;
        $place->longitude = $request->longitude;
        $place->notes = $request->notes;
        $place->user_id = Auth::id(); // Isolasi Data
        
        $place->save();

        return redirect()->back()->with('success', 'Lokasi berhasil disimpan!');
    }

    // MENGUPDATE DATA (UPDATE) - INI YANG KEMARIN KURANG
    public function update(Request $request, $id)
    {
        // Cari data berdasarkan ID dan pastikan milik User yang login
        $place = Place::where('id', $id)->where('user_id', Auth::id())->first();

        if ($place) {
            $place->notes = $request->notes; // Update catatannya
            $place->save();
            return redirect()->back()->with('success', 'Catatan berhasil diperbarui!');
        } else {
            return redirect()->back()->with('error', 'Gagal update! Data tidak ditemukan.');
        }
    }

    
    public function destroy($id)
    {
        $place = Place::where('id', $id)->where('user_id', Auth::id())->first();

        if ($place) {
            $place->delete();
            return redirect()->back()->with('success', 'Lokasi berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Gagal hapus! Data tidak ditemukan.');
        }
    }
}