<nav class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-6 py-3">
    <ol class="flex items-center">
        @foreach ($items as $index => $item)
            <li>
                @if (isset($item['url']))
                    <a href="{{ $item['url'] }}"
                        class="text-sm text-blue-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        @if ($index === 0)
                            <i class="fas fa-home me-1"></i>
                        @endif

                        {{ __($item['label']) }}
                    </a>
                @else
                    <span class="text-sm text-gray-600">{{ __($item['label']) }}</span>
                @endif

                @if (!$loop->last)
                    <span class="text-sm text-gray-600 mx-2">/</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
