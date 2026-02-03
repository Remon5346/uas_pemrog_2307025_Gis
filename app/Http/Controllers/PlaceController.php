<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // PENTING: Untuk fitur Login

class PlaceController extends Controller
{
    // 1. KUNCI HALAMAN: Wajib Login untuk akses controller ini
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 2. ISOLASI DATA (READ): Hanya tampilkan data milik User yang sedang Login
        $places = Place::where('user_id', Auth::id())->get();
        return view('home', compact('places'));
    }

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
        
        // 3. ISOLASI DATA (CREATE): Catat ID User yang sedang Login
        $place->user_id = Auth::id(); 
        
        $place->save();

        return redirect()->back()->with('success', 'Lokasi berhasil disimpan!');
    }

    public function destroy($id)
    {
        // 4. KEAMANAN (DELETE): Pastikan yang dihapus adalah miliknya sendiri
        $place = Place::where('id', $id)->where('user_id', Auth::id())->first();

        if ($place) {
            $place->delete();
            return redirect()->back()->with('success', 'Lokasi berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }
    }
}