# README Increment 9

Pengolahan Data: Import, Export, dan Queue

## Ringkasan Pengerjaan Kelompok

Pada pertemuan ini kelompok kami mengembangkan fitur laporan penjualan, export data, import produk, dan queue pada aplikasi Simple POS. Berikut ringkasan hasilnya.

| Bagian | Hasil Pengerjaan |
|---|---|
| Laporan penjualan | Halaman `/reports` menampilkan total penjualan dan jumlah transaksi dengan filter tanggal dan filter kategori. Penjumlahan dikerjakan oleh database lewat `sum()` dan `count()`. |
| Export CSV | Memakai `streamDownload()` dan `lazyById()` sehingga data dialirkan per baris dan memori tetap kecil. |
| Export PDF | Memakai DomPDF. Isinya ringkasan total, 50 transaksi terbaru, dan tabel 10 produk terlaris. |
| Pengukuran memori | Perbandingan `get()` dan `lazyById()` dicatat di folder `docs/`. |
| Import produk | Import CSV memvalidasi tiap baris, melewati baris yang cacat, dan aman diulang karena memakai `updateOrCreate()`. Baris yang gagal dicatat di tabel `import_errors` dan ditampilkan di halaman import. |
| Queue | Import dipindah ke job `ProcessProductImport` dengan `$tries`, `$backoff`, dan `$timeout`. Job yang gagal dapat dilihat lewat `php artisan queue:failed`. |

## Diskusi Kelompok: Format untuk 100.000 Transaksi

Kami memilih **CSV**. CSV dibuat dengan `streamDownload()` dan `lazyById()`, jadi data dibaca dan ditulis sedikit demi sedikit dan memori tetap kecil berapa pun jumlah transaksinya. Excel masih bisa dipakai kalau bagian keuangan membutuhkan tampilan yang rapi, tetapi pembuatan file `.xlsx` lebih berat. PDF tidak cocok karena DomPDF memuat seluruh dokumen ke memori, dan PDF memang untuk dibaca atau dicetak, bukan untuk diolah lagi.

## Perbandingan Import Manual dan Laravel Excel

Versi manual (`ProcessProductImport`) membaca file dengan `file()` dan `str_getcsv()`, lalu memvalidasi tiap baris sendiri. Kelebihannya, alurnya bisa kami atur sendiri, termasuk `$tries`, `$backoff`, dan `$timeout`. Kekurangannya, kodenya lebih panjang (sekitar 59 baris) dan seluruh isi file dibaca ke memori sekaligus.

Versi Laravel Excel (`ProductImport`) lebih ringkas (sekitar 27 baris) karena pembacaan CSV dan header diurus oleh library. Kekurangannya, kontrolnya lebih terbatas dan butuh package serta ekstensi PHP tambahan (`zip` dan `gd`). Menurut kami, untuk file kecil versi manual sudah cukup, sedangkan untuk file besar atau format Excel versi Laravel Excel lebih cocok.

## Proses yang Layak Dipindah ke Queued Job

Menurut kami, proses yang layak dipindah ke queued job adalah export laporan penjualan ke Excel dan PDF, terutama untuk rentang tanggal yang panjang. Alasannya, prosesnya berat dan lama, terutama PDF karena DomPDF memuat seluruh isi dokumen ke memori, sehingga pengguna harus menunggu di browser dan request berisiko timeout. Kalau memakai queue, file dibuat di background lalu diunduh setelah selesai. Proses ini juga aman diulang kalau gagal karena hanya membaca data dan membuat file. Sebaliknya, penyimpanan transaksi di halaman kasir tetap dijalankan langsung karena kasir perlu segera tahu hasilnya.