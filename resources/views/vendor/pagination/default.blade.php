@if ($paginator->hasPages())
    <nav>
        <ul class="inline-flex items-center rounded-xl overflow-hidden shadow-md border border-gray-200">
            
            {{-- TOMBOL SEBELUMNYA --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="flex items-center justify-center w-10 h-10 text-sm text-gray-400 bg-gray-50 border-r border-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="flex items-center justify-center w-10 h-10 text-sm text-gray-600 bg-white hover:bg-blue-50 border-r border-gray-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                </li>
            @endif

            {{-- NOMOR HALAMAN --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="flex items-center justify-center w-10 h-10 text-sm text-gray-500 bg-white border-r border-gray-200">...</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="flex items-center justify-center w-10 h-10 text-sm font-bold text-white border-r border-blue-400" style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white hover:bg-blue-50 border-r border-gray-200 transition">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- TOMBOL BERIKUTNYA --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="flex items-center justify-center w-10 h-10 text-sm text-gray-600 bg-white hover:bg-blue-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </li>
            @else
                <li>
                    <span class="flex items-center justify-center w-10 h-10 text-sm text-gray-400 bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif