{{-- resources/views/components/quick-action.blade.php --}}
@props([
    'icon' => 'fas fa-plus',
    'label',
    'color' => 'indigo',
    'href' => '#',
    'badge' => null,
    'tooltip' => false,
])

@php
    // Цветовые схемы
    $colorClasses = [
        'indigo' => [
            'bg' => 'bg-indigo-100 dark:bg-indigo-900/40',
            'hover' => 'hover:bg-indigo-200 dark:hover:bg-indigo-800/60',
            'text' => 'text-indigo-600 dark:text-indigo-300',
            'icon' => 'text-indigo-500 dark:text-indigo-400',
        ],
        'green' => [
            'bg' => 'bg-green-100 dark:bg-green-900/40',
            'hover' => 'hover:bg-green-200 dark:hover:bg-green-800/60',
            'text' => 'text-green-600 dark:text-green-300',
            'icon' => 'text-green-500 dark:text-green-400',
        ],
        'red' => [
            'bg' => 'bg-red-100 dark:bg-red-900/40',
            'hover' => 'hover:bg-red-200 dark:hover:bg-red-800/60',
            'text' => 'text-red-600 dark:text-red-300',
            'icon' => 'text-red-500 dark:text-red-400',
        ],
        'blue' => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/40',
            'hover' => 'hover:bg-blue-200 dark:hover:bg-blue-800/60',
            'text' => 'text-blue-600 dark:text-blue-300',
            'icon' => 'text-blue-500 dark:text-blue-400',
        ],
        'purple' => [
            'bg' => 'bg-purple-100 dark:bg-purple-900/40',
            'hover' => 'hover:bg-purple-200 dark:hover:bg-purple-800/60',
            'text' => 'text-purple-600 dark:text-purple-300',
            'icon' => 'text-purple-500 dark:text-purple-400',
        ],
    ];
    
    $selectedColor = $colorClasses[$color] ?? $colorClasses['indigo'];
@endphp

<div class="relative group" x-data="{ showTooltip: false }">
    <a 
        href="{{ $href }}"
        @class([
            'flex flex-col items-center justify-center p-4 rounded-xl transition-all duration-200',
            'shadow-sm hover:shadow-md',
            $selectedColor['bg'],
            $selectedColor['hover'],
        ])
        @if($tooltip)
            @mouseenter="showTooltip = true"
            @mouseleave="showTooltip = false"
            @focus="showTooltip = true"
            @blur="showTooltip = false"
        @endif
    >
        <div @class([
            'h-12 w-12 rounded-full flex items-center justify-center mb-3 transition-transform group-hover:scale-110',
            $selectedColor['bg'],
        ])>
            <i @class([
                $icon,
                'text-xl',
                $selectedColor['icon'],
            ])></i>
        </div>
        
        <span @class([
            'text-sm font-medium text-center',
            $selectedColor['text'],
        ])>
            {{ $label }}
        </span>
        
        @if($badge)
            <span class="absolute top-2 right-2 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-medium rounded-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                {{ $badge }}
            </span>
        @endif
    </a>
    
    @if($tooltip)
        <div 
            x-show="showTooltip"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute z-10 w-48 px-3 py-2 mt-2 text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-neutral-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700"
            style="left: 50%; transform: translateX(-50%);"
        >
            {{ $tooltip }}
            <div class="absolute -top-1.5 left-1/2 -ml-1.5 w-3 h-3 rotate-45 bg-white dark:bg-neutral-800 border-t border-l border-gray-200 dark:border-gray-700"></div>
        </div>
    @endif
</div>