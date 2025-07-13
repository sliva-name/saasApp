@extends('layouts.app')
@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Магазин создан</h1>

        <p class="mb-2"><strong>Домен:</strong>
            <a href="http://{{ $domain }}:8000" target="_blank" class="text-blue-600 hover:underline">
                {{ $domain }}
            </a>
        </p>

        <p><strong>Тариф:</strong> {{ ucfirst($store->plan) }}</p>
    </div>
@endsection
