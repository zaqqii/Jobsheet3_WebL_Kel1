# Increment 6 – Validasi Form Request dan Total di Server

## 1. Pengujian Request POST `/pos`

### Pengujian qty = 0
- Request: `POST /pos`
- Parameter: `product_id=1`, `qty=0`
- Hasil: Laravel mengembalikan pesan `CSRF token mismatch.`
- Keterangan: Request ditolak oleh middleware CSRF sehingga validasi quantity belum dijalankan.

### Pengujian product_id tidak ada
- Request: `POST /pos`
- Parameter: `product_id=999999`, `qty=1`
- Hasil: Belum diuji.
- Keterangan: Hasil akan dicatat setelah request dilakukan.

## 2. Validasi Total Transaksi di Server

Validasi dengan aturan `numeric` saja belum cukup untuk mencegah manipulasi total. Pengguna masih bisa mengubah nilai total yang dikirim melalui form selama nilainya tetap berupa angka. Karena itu, total transaksi harus dihitung ulang di server berdasarkan harga produk dari database dan jumlah barang yang dibeli.