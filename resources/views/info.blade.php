<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir POS - Informasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col justify-between">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Point of Sale (POS) App</h1>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-12 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Selamat Datang di Sistem POS</h2>
        <p class="text-gray-600 text-lg mb-8">
            Aplikasi pengelolaan transaksi penjualan, produk, dan laporan kasir secara terintegrasi dan efisien.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 text-lg mb-2">Manajemen Transaksi</h3>
                <p class="text-gray-600 text-sm">Kemudahan pencatatan transaksi penjualan secara cepat untuk kasir.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 text-lg mb-2">Kelola Produk</h3>
                <p class="text-gray-600 text-sm">Pengaturan kategori dan stok barang yang rapi bagi Administrator.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 text-lg mb-2">Sistem Akses Role</h3>
                <p class="text-gray-600 text-sm">Keamanan data terjamin sesuai hak akses masing-masing pengguna.</p>
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('login') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white text-lg px-6 py-3 rounded-md font-semibold transition">
                Mulai Login Kasir / Admin
            </a>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center py-4 text-sm">
        &copy; {{ date('Y') }} POS System. All rights reserved.
    </footer>
</body>
</html>