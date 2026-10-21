## Skenario Pengujian Manual Hak Akses

### 1. Pengujian Admin
**Langkah pengujian:**
1. Buka halaman login aplikasi POS.
2. Login menggunakan akun admin.
3. Akses halaman `/products`.

**Hasil yang diharapkan:** Admin berhasil login dan dapat mengakses halaman produk karena memiliki hak akses admin.

### 2. Pengujian Kasir
**Langkah pengujian:**
1. Buka halaman login aplikasi POS.
2. Login menggunakan akun kasir.
3. Coba akses halaman `/products`.
4. Akses halaman `/pos`.

**Hasil yang diharapkan:** Kasir tidak dapat mengakses halaman `/products` dan mendapatkan respons `403 Forbidden`, tetapi dapat mengakses halaman `/pos`.

### 3. Pengujian Pengguna Belum Login
**Langkah pengujian:**
1. Pastikan pengguna sudah logout.
2. Coba akses halaman `/products`.
3. Coba akses `/pos` menggunakan permintaan dengan header `Accept: application/json`.

**Hasil yang diharapkan:** Akses `/products` diarahkan ke halaman login, sedangkan permintaan ke `/pos` menghasilkan respons `401 Unauthorized`.

### 4. Pengujian Akun Nonaktif
**Langkah pengujian:**
1. Ubah nilai `is_active` akun kasir menjadi `false` melalui Laravel Tinker.
2. Coba login menggunakan email dan password kasir yang benar.
3. Amati respons aplikasi.
4. Aktifkan kembali akun setelah pengujian selesai.

**Hasil yang diharapkan:** Akun nonaktif ditolak saat login. Setelah pengujian, nilai `is_active` dikembalikan menjadi `true`.

### 5. Pengujian Password Salah
**Langkah pengujian:**
1. Buka halaman login.
2. Masukkan email kasir yang terdaftar dan password yang salah.
3. Klik tombol login.

**Hasil yang diharapkan:** Login ditolak dan aplikasi menampilkan pesan `Email atau kata sandi salah.`
