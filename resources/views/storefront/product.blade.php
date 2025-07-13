@extends('layouts.store')

@section('title', $product->name)

@section('content')
    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-3xl font-bold">{{ $product->name }}</h2>
        <p class="text-gray-600 mt-2">{{ $product->description }}</p>
        <p class="text-xl font-bold mt-4">{{ $product->price }} ₽</p>
    </div>
@endsection
