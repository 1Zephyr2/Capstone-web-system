@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 shadow-sm transition-all']) }}>
