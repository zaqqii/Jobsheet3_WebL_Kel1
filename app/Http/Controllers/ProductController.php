<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ImportError;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        ImportError::query()->delete();

        $lines = file(
            $request->file('csv')->getRealPath(),
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        $rows = array_map('str_getcsv', $lines);
        $header = array_map('trim', array_shift($rows));

        $created = 0;
        $updated = 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if (count($row) !== count($header)) {
                ImportError::create([
                    'row_number' => $rowNumber,
                    'error_message' => 'Jumlah kolom tidak sesuai',
                ]);

                continue;
            }

            $validator = Validator::make(
                array_combine($header, $row),
                [
                    'name' => ['required', 'string'],
                    'category_id' => ['required', 'exists:categories,id'],
                    'price' => ['required', 'integer', 'min:0'],
                ]
            );

            if ($validator->fails()) {
                ImportError::create([
                    'row_number' => $rowNumber,
                    'error_message' => implode(
                        ', ',
                        $validator->errors()->all()
                    ),
                ]);

                continue;
            }

            $data = $validator->validated();

            $product = Product::updateOrCreate(
                [
                    'name' => $data['name'],
                    'category_id' => $data['category_id'],
                ],
                [
                    'price' => $data['price'],
                ]
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;
        }

        return back()->with(
            'success',
            "{$created} produk baru, {$updated} diperbarui."
        );
    }
}