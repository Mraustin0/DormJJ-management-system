@if ($paginator->hasPages())
<nav class="flex items-center justify-between mt-2">
    {{-- ข้อมูลจำนวน --}}
    <div class="text-sm text-gray-500">
        แสดง {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} จาก {{ $paginator->total() }} รายการ
    </div>

    {{-- ปุ่มเปลี่ยนหน้า --}}
    <div class="flex items-center gap-1">

        {{-- ย้อนกลับ --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 rounded-lg text-sm text-gray-300 cursor-not-allowed select-none">
                &lsaquo;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                &lsaquo;
            </a>
        @endif

        {{-- หมายเลขหน้า --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1.5 text-sm text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-[#4A90E2] text-white select-none">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- ถัดไป --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                &rsaquo;
            </a>
        @else
            <span class="px-3 py-1.5 rounded-lg text-sm text-gray-300 cursor-not-allowed select-none">
                &rsaquo;
            </span>
        @endif

    </div>
</nav>
@endif
