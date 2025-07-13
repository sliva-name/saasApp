@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Создание магазина</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stores.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label for="plan" class="block font-medium text-sm text-gray-700">Тариф</label>
            <select name="plan" id="plan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="free">Бесплатный</option>
                <option value="basic">Базовый</option>
                <option value="pro">Профессиональный</option>
            </select>
        </div>

        <div>
            <label for="custom_domain" class="block font-medium text-sm text-gray-700">Свой домен (необязательно)</label>
            <input type="text" name="custom_domain" id="custom_domain" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="example.com">
        </div>

        <div>
            <label for="theme_id" class="block font-medium text-sm text-gray-700">Выберите тему</label>
            <select name="theme_id" id="theme_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">По умолчанию</option>
                @foreach(\App\Models\Theme::all() as $theme)
                    <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Создать магазин</button>
        </div>
    </form>
@endsection
