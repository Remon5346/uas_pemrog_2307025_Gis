CSS, JavaScript
# UAS GIS - Ardi Nur Harom (2307025)

Aplikasi Sistem Informasi Geografis (GIS) berbasis Laravel
untuk menyimpan dan menampilkan data lokasi pada peta.

## Teknologi yang Digunakan
- Laravel 10
- PHP 8
- MySQL
- Leaflet JS
- HTML, 
## Fitur Aplikasi
- Menambahkan lokasi baru
- Menampilkan lokasi di peta
- Mengedit catatan lokasi
- Menghapus lokasi

## Cara Instalasi & Menjalankan Aplikasi

1. Clone repository
   git clone https://github.com/Remon5346/uas_pemrog_2307025_Gis.git

2. Masuk ke folder project
   cd uas_pemrog_2307025_Gis

3. Install dependency
   composer install

4. Copy file environment
   cp .env.example .env

5. Generate key aplikasi
   php artisan key:generate

6. Setting database di file .env
   DB_DATABASE=uas_gis  
   DB_USERNAME=root  
   DB_PASSWORD=

7. Jalankan server
   php artisan serve

Akses aplikasi melalui browser:
http://127.0.0.1:8000

## Screenshot Aplikasi

![Halaman Utama](screenshots/home.png)
![Tambah Lokasi](screenshots/add.png)
![Edit Lokasi](screenshots/edit.png)

