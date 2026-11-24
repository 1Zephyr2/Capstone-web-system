@props(['title' => null, 'align' => 'left'])
<details class="relative inline-block align-middle text-left">
    <summary class="list-none cursor-pointer inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <i class="bi bi-info-circle" aria-hidden="true"></i><span>More Info</span>
    </summary>
    <div class="absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} top-full z-40 mt-2 w-80 max-w-[85vw] rounded-xl border border-gray-200 bg-white p-4 text-xs font-normal leading-relaxed text-gray-600 shadow-xl normal-case tracking-normal space-y-2">
        @if($title)<p class="text-sm font-bold text-gray-900">{{ $title }}</p>@endif
        {{ $slot }}
    </div>
</details>
