@props(['title' => null, 'align' => 'left'])
{{-- Explanations appear on hover and keyboard focus. --}}
<span class="relative inline-block align-middle info-tip group">
    <button type="button" aria-label="More information"
            class="w-5 h-5 inline-flex items-center justify-center rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
        <i class="bi bi-info-circle"></i>
    </button>
    <div class="info-tip-panel invisible opacity-0 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 transition-opacity absolute z-40 {{ $align === 'right' ? 'right-0' : 'left-0' }} top-7 w-80 max-w-[85vw] bg-white border border-gray-200 rounded-xl shadow-xl p-4 text-xs text-gray-600 leading-relaxed normal-case tracking-normal font-normal text-left space-y-2">
        @if($title)<p class="font-bold text-gray-900 text-sm">{{ $title }}</p>@endif
        {{ $slot }}
    </div>
</span>
