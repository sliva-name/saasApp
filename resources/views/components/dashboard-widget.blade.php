{{-- resources/views/components/dashboard-widget.blade.php --}}
@props([
    'title',
    'value',
    'icon' => 'fas fa-chart-line',
    'color' => 'indigo',
    'trend' => null, // 'up', 'down' или null
    'change' => null,
    'link' => null,
])

@php
    // Цветовые схемы для разных состояний
    $colorClasses = [
        'indigo' => [
            'bg' => 'bg-indigo-100 dark:bg-indigo-900/30',
            'text' => 'text-indigo-600 dark:text-indigo-300',
            'icon' => 'text-indigo-500 dark:text-indigo-400',
        ],
        'green' => [
            'bg' => 'bg-green-100 dark:bg-green-900/30',
            'text' => 'text-green-600 dark:text-green-300',
            'icon' => 'text-green-500 dark:text-green-400',
        ],
        'red' => [
            'bg' => 'bg-red-100 dark:bg-red-900/30',
            'text' => 'text-red-600 dark:text-red-300',
            'icon' => 'text-red-500 dark:text-red-400',
        ],
        'blue' => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/30',
            'text' => 'text-blue-600 dark:text-blue-300',
            'icon' => 'text-blue-500 dark:text-blue-400',
        ],
        'orange' => [
            'bg' => 'bg-orange-100 dark:bg-orange-900/30',
            'text' => 'text-orange-600 dark:text-orange-300',
            'icon' => 'text-orange-500 dark:text-orange-400',
        ],
    ];
    
    $selectedColor = $colorClasses[$color] ?? $colorClasses['indigo'];
@endphp

<div 
    @class([
        'rounded-xl p-5 transition-all duration-200 hover:shadow-md',
        'bg-white dark:bg-neutral-800',
        'cursor-pointer hover:ring-1 hover:ring-opacity-50' => $link,
        'hover:ring-indigo-200 dark:hover:ring-indigo-800' => $link && $color === 'indigo',
        'hover:ring-green-200 dark:hover:ring-green-800' => $link && $color === 'green',
        'hover:ring-red-200 dark:hover:ring-red-800' => $link && $color === 'red',
    ])
    @if($link) onclick="window.location.href='{{ $link }}'" @endif
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 100)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center">
                <div @class([
                    'flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center',
                    $selectedColor['bg'],
                ])>
                    <i @class([$icon, 'text-lg', $selectedColor['icon']])></i>
                </div>
                
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </h3>
                    <div class="mt-1 flex items-baseline">
                        <p @class([
                            'text-2xl font-semibold',
                            $selectedColor['text'],
                        ])>
                            {{ $value }}
                        </p>
                        
                        @if($trend && $change)
                            <span @class([
                                'ml-2 flex items-baseline text-sm font-medium',
                                'text-green-600 dark:text-green-400' => $trend === 'up',
                                'text-red-600 dark:text-red-400' => $trend === 'down',
                            ])>
                                @if($trend === 'up')
                                    <i class="fas fa-arrow-up text-xs mr-0.5"></i>
                                @else
                                    <i class="fas fa-arrow-down text-xs mr-0.5"></i>
                                @endif
                                <span>{{ $change }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        @if($link)
            <div class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <i class="fas fa-chevron-right"></i>
            </div>
        @endif
    </div>
    
    @if($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>