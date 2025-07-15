<aside class="w-64 bg-white border-r border-gray-200 p-4 overflow-y-auto h-screen">
    <h2 class="text-lg font-semibold mb-4">Магазины</h2>

    <ul class="space-y-2">
        @foreach($stores as $store)
            <li>
                @foreach($store->domains as $domain)
                    <a href="http://{{ $domain->domain }}:8000" target="_blank"
                       class="block px-3 py-2 rounded text-sm text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition">
                        🌐 {{ $domain->domain }}
                    </a>
                @endforeach
            </li>
        @endforeach
    </ul>
</aside>
