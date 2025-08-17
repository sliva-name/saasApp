@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" @class([
    'flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors',
    'text-indigo-700 bg-indigo-50 dark:text-indigo-200 dark:bg-indigo-900/30' => $active,
    'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' => !$active,
])>
    <i class="{{ $icon }} mr-3 text-gray-500 dark:text-gray-400"></i>
    <span>{{ $slot }}</span>
</a>