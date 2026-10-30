<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Product([
            'name'  => $row['nama_produk'],
            'price' => $row['harga'],
            'stock' => $row['stok'],
            'category_id' => 1,
        ]);
    }
}