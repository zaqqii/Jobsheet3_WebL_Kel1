<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Penjualan</h2>
        <p>Periode: {{ $from }} sampai {{ $to }}</p>
        <p>Kategori: {{ $category ?? 'Semua kategori' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->created_at }}</td>
                <td>{{ $t->user->name }}</td>
                <td>Rp {{ number_format($t->filtered_total ?? $t->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="margin-top: 20px;">Total Penjualan: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
    <p>Jumlah Transaksi: {{ $jumlahTransaksi }} transaksi</p>

    {{-- BARU: tabel 10 produk terlaris --}}
    <h4 style="margin-top: 20px;">10 Produk Terlaris</h4>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Terjual (unit)</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ number_format($p->total_qty, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($p->total_penjualan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">Tidak ada penjualan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>