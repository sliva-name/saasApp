@extends('layouts.app')
@section('content')
    <div class="flex">
        {{-- Sidebar --}}
        @include('components.sidebar', ['stores' => $stores])

        {{-- Main content --}}
        <main class="flex-1 p-6 overflow-x-auto">
            <h1 class="text-2xl font-bold mb-6">Детали магазинов</h1>

            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full text-sm table-auto border border-gray-200">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Пользователь</th>
                        <th class="px-4 py-2 text-left">План</th>
                        <th class="px-4 py-2 text-left">Slug</th>
                        <th class="px-4 py-2 text-left">Тема</th>
                        <th class="px-4 py-2 text-left">Создан</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($stores as $store)
                        <tr>
                            <td class="px-4 py-2">{{ $store->id }}</td>
                            <td class="px-4 py-2">
                                {{ $store->owner?->name ?? '—' }}
                                <div class="text-xs text-gray-500">{{ $store->owner?->email }}</div>
                            </td>
                            <td class="px-4 py-2">{{ $store->plan }}</td>
                            <td class="px-4 py-2">{{ $store->slug }}</td>
                            <td class="px-4 py-2">{{ $store->theme?->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $store->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $stores->links() }}
            </div>
        </main>
    </div>
@endsection
