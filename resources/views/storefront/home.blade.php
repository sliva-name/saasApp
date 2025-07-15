@extends('layouts.store')

@section('title', 'Главная')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Товары</h2>
    @if($products)
        <div class="my-5 block rounded-lg">
            <h3 class="text-xl font-semibold tracking-tight text-balance text-gray-900 sm:text-xl">
                Товаров пока нет
            </h3>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($products as $product)
                <a href="{{ route('store.products.show', $product) }}" class="block bg-white p-4 shadow rounded hover:shadow-md transition">
                    <h3 class="font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($product->description, 50) }}</p>
                    <p class="mt-2 font-bold">{{ $product->price }} ₽</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif

@endsection
