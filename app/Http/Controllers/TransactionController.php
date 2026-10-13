<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::paginate(12);

        return view('pos.create', ['products' => $products]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'user_id' => 1, // sementara di-hardcode, login baru ada di Pertemuan 7
                'total' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                // harga diambil ulang dari database, bukan dari input klien
                $product = Product::findOrFail($item['product_id']);
                // Pengecekan validasi stok mencukupi
                if ($item['qty'] > $product->stock) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        redirect()->back()->withErrors([
                            'items' => "Stok untuk produk {$product->name} tidak mencukupi (sisa: {$product->stock})."
                        ])
                    );
                }
                $subtotal = $product->price * $item['qty'];
                $total += $subtotal;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ]);
            }

            $transaction->update(['total' => $total]);
        });

        return redirect()->route('pos.create')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index()
    {
        $transactions = Transaction::with(['details.product', 'user'])
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}