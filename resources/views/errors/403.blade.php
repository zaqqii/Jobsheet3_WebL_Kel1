@extends('layouts.app')

@section('title', '403 Forbidden')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center bg-white p-6 rounded-md shadow max-w-md w-full border">
        <h1 class="text-5xl font-bold text-slate-900 mb-2">403</h1>
        <p class="text-slate-600 mb-6 font-medium">{{ $exception->getMessage() }}</p>
        <a href="{{ route('pos.create') }}" class="inline-block bg-slate-900 text-white px-4 py-2 rounded-md text-sm">
            Kembali ke Kasir
        </a>
    </div>
</div>
@endsection