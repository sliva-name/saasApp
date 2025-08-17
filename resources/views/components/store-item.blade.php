@props(['store'])

<div class="group relative">
    @foreach($store->domains as $domain)
        <a href="http://{{ $domain->domain }}:8000" target="_blank" class="flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70">
            <div class="flex items-center min-w-0">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 mr-3">
                    <i class="fas fa-store text-sm"></i>
                </div>
                <span class="truncate">{{ $domain->domain }}</span>
            </div>
            <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 px-2 py-1 rounded-full whitespace-nowrap">Активен</span>
        </a>
    @endforeach
    
    <div class="absolute right-0 top-0 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1 mr-2 mt-2">
        <button class="p-1 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-300" title="Настройки">
            <i class="fas fa-cog text-xs"></i>
        </button>
        <button class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-300" title="Удалить">
            <i class="fas fa-trash text-xs"></i>
        </button>
    </div>
</div>