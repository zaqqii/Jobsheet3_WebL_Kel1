<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Jobs\ProcessProductImport;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);
        $query = $this->filteredQuery($from, $to, $categoryId);

        return view('reports.index', [
            'totalPenjualan' => $this->salesTotal($from, $to, $categoryId),
            'jumlahTransaksi' => $query->count(),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'categories' => Category::orderBy('name')->get(),
            'categoryId' => $categoryId,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);
        $query = $this->filteredQuery($from, $to, $categoryId);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Total']);

            foreach ($query->lazyById() as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->created_at,
                    $t->user->name,
                    $t->filtered_total ?? $t->total,
                ]);
            }

            fclose($out);
        }, 'laporan-penjualan.csv');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);

        return Excel::download(
            new SalesReportExport($this->filteredQuery($from, $to, $categoryId)),
            'laporan-penjualan.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);
        $query = $this->filteredQuery($from, $to, $categoryId);

        $pdf = Pdf::loadView('reports.pdf', [
            'totalPenjualan' => $this->salesTotal($from, $to, $categoryId),
            'jumlahTransaksi' => $query->count(),
            'transactions' => $query->latest()->limit(50)->get(),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'category' => $categoryId ? Category::find($categoryId)?->name : null,
        ]);

        return $pdf->download('laporan-penjualan.pdf');
    }

    public function importForm(): View
    {
        return view('reports.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('csv')->store('imports');

        ProcessProductImport::dispatch($path);

        return back()->with('success', 'Import sedang diproses di background.');
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        return [$from, $to];
    }

    private function resolveCategoryId(Request $request): ?int
    {
        $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        return $request->filled('category_id')
            ? $request->integer('category_id')
            : null;
    }

    private function filteredQuery($from, $to, ?int $categoryId = null): Builder
    {
        $query = Transaction::with('user')
            ->whereBetween('created_at', [$from, $to]);

        if ($categoryId !== null) {
            $query
                ->whereHas('details.product', fn (Builder $products) => $products
                    ->where('category_id', $categoryId))
                ->withSum(['details as filtered_total' => fn (Builder $details) => $details
                    ->whereHas('product', fn (Builder $products) => $products
                        ->where('category_id', $categoryId))], 'subtotal');
        }

        return $query;
    }

    private function salesTotal($from, $to, ?int $categoryId): int
    {
        if ($categoryId === null) {
            return Transaction::query()
                ->whereBetween('created_at', [$from, $to])
                ->sum('total');
        }

        return TransactionDetail::query()
            ->whereHas('transaction', fn (Builder $transactions) => $transactions
                ->whereBetween('created_at', [$from, $to]))
            ->whereHas('product', fn (Builder $products) => $products
                ->where('category_id', $categoryId))
            ->sum('subtotal');
    }
}
