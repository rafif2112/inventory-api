# API Inventori

API Inventori adalah aplikasi berbasis Laravel yang dirancang untuk mengelola inventaris barang di sekolah. Aplikasi ini menyediakan endpoint API untuk mengelola data barang, pengguna, dan transaksi peminjaman.

## Petunjuk Instalasi

Ikuti langkah-langkah berikut setelah melakukan clone repository:

### 1. Instalasi Dependensi
Jalankan perintah berikut untuk menginstal semua package yang diperlukan:
```bash
composer install
```

### 2. Update Dependensi (Opsional)
Untuk memperbarui semua package ke versi terbaru:
```bash
composer update
```

### 3. Membuat File Environment
Salin file environment example menjadi file environment aktif:
```bash
cp .env.example .env
```

### 4. Generate Kunci Aplikasi
Buat kunci enkripsi unik untuk aplikasi Laravel:
```bash
php artisan key:generate
```

### 5. Konfigurasi Database
Buka file `.env` dan konfigurasikan koneksi database Anda dengan mengisi nilai-nilai berikut:
- `DB_CONNECTION` - Jenis database (mysql, postgresql, sqlite, dll)
- `DB_HOST` - Alamat host database (biasanya localhost)
- `DB_PORT` - Port database (3306 untuk MySQL, 5432 untuk PostgreSQL)
- `DB_DATABASE` - Nama database yang akan digunakan
- `DB_USERNAME` - Username untuk mengakses database
- `DB_PASSWORD` - Password untuk mengakses database

Contoh konfigurasi untuk MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Menjalankan Migrasi dan Seeder
Buat tabel-tabel database dan isi dengan data contoh:
```bash
php artisan migrate --seed
```

Perintah ini akan:
- Membuat semua tabel yang diperlukan di database
- Mengisi tabel dengan data sampel untuk pengujian

### 7. Instalasi Laravel Passport
Instal dan konfigurasi Laravel Passport untuk autentikasi API:
```bash
php artisan passport:install --uuids
```

**Catatan Penting:**
- Setelah menjalankan perintah di atas, tekan Enter dua kali untuk menghasilkan UUID clients
- Anda akan melihat output yang menampilkan **Personal Access Client** credentials
- Salin nilai `Client ID` dan `Client Secret` dari **Personal Access Client**
- Tambahkan kredensial tersebut ke file `.env` Anda:

```env
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=your_client_id_here
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=your_client_secret_here
```

### 8. Menjalankan Aplikasi
Untuk menjalankan server development:
```bash
php artisan serve
```

Aplikasi akan dapat diakses di `http://localhost:8000`

## Catatan Penting

⚠️ **Setiap kali Anda mereset database (menjalankan `php artisan migrate:fresh` atau `php artisan db:wipe`), Anda HARUS menginstal ulang Laravel Passport (ulangi langkah 7).**

## Endpoint API

Setelah instalasi selesai, API akan tersedia dengan endpoint dasar:
```
http://localhost:8000/api/
```

Dokumentasi API lengkap dapat diakses setelah aplikasi berjalan.

## Troubleshooting

### Masalah Umum:
1. **Error "Key length is invalid"**: Jalankan `php artisan key:generate`
2. **Error database connection**: Periksa konfigurasi database di file `.env`
3. **Error Passport**: Pastikan telah menjalankan `php artisan passport:install --uuids`

## Lisensi

PPLG XII-V [lisensi MIT](https://opensource.org/licenses/MIT).
