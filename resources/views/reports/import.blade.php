@extends('layouts.app') {{-- Sesuaikan dengan layout utama project kamu, misal: layouts.admin atau layouts.app --}}

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Import Data Produk (CSV)</h2>

        {{-- Pesan Sukses --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form Upload CSV --}}
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
            @csrf
            <div class="mb-4">
                <label for="csv" class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV Produk</label>
                <input type="file" name="csv" id="csv" accept=".csv, .txt" required
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('csv')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md shadow">
                Upload & Import
            </button>
        </form>

        {{-- Tabel Log Error Import --}}
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Kesalahan Import Terakhir</h3>

            @if(isset($importErrors) && $importErrors->count() > 0)
                <div class="overflow-x-auto border border-red-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-red-50 text-red-700">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Baris CSV</th>
                                <th class="px-4 py-3 text-left font-semibold">Pesan Error</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($importErrors as $index => $error)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-red-600">Baris ke-{{ $error->row_number }}</td>
                                    <td class="px-4 py-3 text-gray-800">{{ $error->error_message }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    Tidak ada data yang gagal di-import. Semua data bersih!
                </div>
            @endif
        </div>
    </div>
</div>
@endsection