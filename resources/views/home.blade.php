<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAS Web GIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        #map { height: 400px; width: 100%; border-radius: 10px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="text-center mb-4">📍 Aplikasi Peta Lokasi Favorit</h1>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5>Cari Lokasi</h5>
                    <div class="input-group mb-3">
                        <input type="text" id="searchBox" class="form-control" placeholder="Cari nama tempat...">
                        <button class="btn btn-primary" onclick="searchLocation()">Cari</button>
                    </div>
                    <div id="map"></div>
                </div>
            </div>

            <div class="card shadow-sm bg-info bg-opacity-10" id="saveCard" style="display: none;">
                <div class="card-body">
                    <h5>Simpan Lokasi Ini?</h5>
                    <form action="{{ route('place.store') }}" method="POST">
                        @csrf
                        <input type="text" name="name" id="inputName" class="form-control mb-2" readonly>
                        <div class="row mb-2">
                            <div class="col"><input type="text" name="lat" id="inputLat" class="form-control" readonly></div>
                            <div class="col"><input type="text" name="lon" id="inputLon" class="form-control" readonly></div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan ke Database</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Lokasi Tersimpan</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <ul class="list-group">
                        @foreach($places as $place)
                        <li class="list-group-item">
                            <h6 class="fw-bold">{{ $place->name }}</h6>
                            <small>Lat: {{ $place->latitude }}, Lon: {{ $place->longitude }}</small>
                            
                            <form action="{{ route('place.update', $place->id) }}" method="POST" class="mt-2">
                                @csrf @method('PUT')
                                <div class="input-group input-group-sm">
                                    <input type="text" name="notes" class="form-control" value="{{ $place->notes }}">
                                    <button class="btn btn-warning" type="submit">Update Note</button>
                                </div>
                            </form>

                            <form action="{{ route('place.destroy', $place->id) }}" method="POST" class="mt-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100">Hapus</button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-7.2278, 107.9087], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker;

    function searchLocation() {
        var query = document.getElementById('searchBox').value;
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
            .then(res => res.json())
            .then(data => {
                if(data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;
                    var name = data[0].display_name;

                    map.setView([lat, lon], 16);
                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lon]).addTo(map).bindPopup(name).openPopup();

                    document.getElementById('inputName').value = name;
                    document.getElementById('inputLat').value = lat;
                    document.getElementById('inputLon').value = lon;
                    document.getElementById('saveCard').style.display = 'block';
                } else {
                    alert('Lokasi tidak ditemukan');
                }
            });
    }
</script>
</body>
</html>