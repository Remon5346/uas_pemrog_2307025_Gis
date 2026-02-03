<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAS Web GIS - Peta Pribadi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
    <style>
        #map { height: 450px; width: 100%; border-radius: 10px; }
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>

    <div style="position: absolute; top: 20px; right: 20px; z-index: 9999;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark shadow-sm p-2">
                User: {{ Auth::user()->name ?? 'Guest' }}
            </span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm shadow fw-bold">
                    Logout 🚪
                </button>
            </form>
        </div>
    </div>

    <div class="container py-5">
        <h1 class="text-center mb-4 fw-bold">📍 My Favorite Places</h1>

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label fw-bold">Cari Lokasi Baru:</label>
                        <div class="input-group mb-3">
                            <input type="text" id="searchBox" class="form-control" placeholder="Ketik nama tempat (misal: Monas)...">
                            <button class="btn btn-primary" onclick="searchLocation()">🔍 Cari</button>
                        </div>
                        <div id="map"></div>
                    </div>
                </div>

                <div class="card shadow-sm bg-success bg-opacity-10 border-success" id="saveCard" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title text-success fw-bold">Simpan Lokasi Ini?</h5>
                        <form action="{{ route('place.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label>Nama Tempat</label>
                                <input type="text" name="name" id="inputName" class="form-control fw-bold" readonly>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <small>Latitude</small>
                                    <input type="text" name="latitude" id="inputLat" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col">
                                    <small>Longitude</small>
                                    <input type="text" name="longitude" id="inputLon" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label>Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" placeholder="Contoh: Tempat makan enak..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 fw-bold">💾 Simpan ke Database</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Daftar Lokasi Saya</span>
                        <span class="badge bg-secondary">{{ count($places) }} Lokasi</span>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 600px;">
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($places->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($places as $place)
                                <li class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-primary">{{ $place->name }}</h6>
                                            <small class="text-muted d-block">Lat: {{ $place->latitude }}, Lon: {{ $place->longitude }}</small>
                                            @if($place->notes)
                                                <div class="mt-1 p-2 bg-light rounded border small text-dark">
                                                    User Note: <i>{{ $place->notes }}</i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <form action="{{ route('place.update', $place->id) }}" method="POST" class="d-flex gap-1 mb-1">
                                            @csrf @method('PUT')
                                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Ubah catatan..." value="{{ $place->notes }}">
                                            <button class="btn btn-sm btn-warning" type="submit">Update</button>
                                        </form>

                                        <form action="{{ route('place.destroy', $place->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Yakin hapus lokasi ini?')">Hapus Lokasi</button>
                                        </form>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-5 text-muted">
                                <p>Belum ada data lokasi.</p>
                                <p>Cari lokasi di peta untuk menyimpannya!</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Inisialisasi Peta (Default ke Indonesia)
        var map = L.map('map').setView([-2.5489, 118.0149], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker;

        // Tampilkan marker untuk lokasi yang sudah disimpan (Opsional, agar peta ramai)
        @foreach($places as $p)
            L.marker([{{ $p->latitude }}, {{ $p->longitude }}])
                .addTo(map)
                .bindPopup("<b>{{ $p->name }}</b><br>{{ $p->notes }}");
        @endforeach

        // Fungsi Cari Lokasi
        function searchLocation() {
            var query = document.getElementById('searchBox').value;
            if(!query) return alert("Masukkan nama lokasi!");

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                .then(res => res.json())
                .then(data => {
                    if(data.length > 0) {
                        var lat = data[0].lat;
                        var lon = data[0].lon;
                        var name = data[0].display_name;

                        // Pindah tampilan peta
                        map.setView([lat, lon], 16);
                        
                        // Buat Marker Baru
                        if (marker) map.removeLayer(marker);
                        marker = L.marker([lat, lon]).addTo(map)
                            .bindPopup(name).openPopup();

                        // Isi Form Input Otomatis
                        document.getElementById('inputName').value = name;
                        document.getElementById('inputLat').value = lat;
                        document.getElementById('inputLon').value = lon;
                        
                        // Munculkan Card Simpan
                        document.getElementById('saveCard').style.display = 'block';
                    } else {
                        alert('Lokasi tidak ditemukan!');
                    }
                })
                .catch(err => console.error(err));
        }
    </script>
</body>
</html>