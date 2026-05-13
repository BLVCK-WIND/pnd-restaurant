<article class="menu-card reveal" style="transition-delay:{{ $delay ?? 0 }}s">
    <div class="menu-card-img-wrap">
        @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}"
                 alt="{{ $item->name }}"
                 class="menu-card-img"
                 loading="lazy">
        @else
            <div class="menu-card-img flex items-center justify-center" style="background:#292a29">
                <span class="material-symbols-outlined" style="font-size:2.5rem;color:#584238">restaurant</span>
            </div>
        @endif
        <div class="menu-card-overlay"></div>
    </div>

    <div class="p-5">
        <div class="flex justify-between items-baseline gap-3 mb-2">
            <h3 class="font-serif text-lg font-semibold text-on-surface leading-tight">{{ $item->name }}</h3>
            <span class="price-badge shrink-0">
                {{ number_format($item->price, 0, ',', '.') }}đ
            </span>
        </div>
        <div class="h-px bg-outline-variant mb-3"></div>
        @if($item->description)
            <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">
                {{ $item->description }}
            </p>
        @endif
    </div>
</article>