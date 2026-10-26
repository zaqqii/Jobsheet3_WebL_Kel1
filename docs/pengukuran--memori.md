# Pengukuran Memori: get() vs lazyById()
Dataset: sekitar 52.500 row pada tabel transactions.   
Transaction::lazyById(): 69.5 MB
Transaction::get(): 127.8 MB

Kesimpulan kelompok:
Metode get() mencatat pemakaian memori yang jauh lebih besar (127.8 MB) karena memuat seluruh dokumen atau data ke dalam memori secara bersamaan. Sebaliknya, lazyById() hanya membutuhkan 69.5 MB memori karena mengambil data dari database per chunk kecil secara bertahap. Hasil ini membuktikan bahwa penggunaan lazyById() menjaga memori server tetap datar dan stabil, sehingga jauh lebih efisien dan aman saat harus menangani dataset berukuran besar.  