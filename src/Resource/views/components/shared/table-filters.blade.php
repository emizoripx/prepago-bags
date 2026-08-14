<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        @if(isset($filters))
            {{ $filters }}
        @endif
    </div>
    
    <div class="relative w-full md:w-80 xl:w-96 flex-shrink-0">
        <span class="h-full absolute inset-y-0 left-0 flex items-center pl-3">
            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-slate-400">
                <path d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z"></path>
            </svg>
        </span>
        <input wire:model.debounce.500ms="search" placeholder="{{ $searchPlaceholder ?? 'Buscar...' }}" class="appearance-none rounded-xl border border-slate-200 block pl-10 pr-6 py-2.5 w-full bg-slate-50 text-sm placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all shadow-sm" />
    </div>
</div>
