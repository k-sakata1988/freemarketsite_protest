<div class="items-container">
@forelse ($items as $item)
    <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}">
            @php
                $imageUrl = 'https://via.placeholder.com/200x200?text=商品画像';
                if ($item->image_path) {
                    if (Str::startsWith($item->image_path, ['http://', 'https://'])) {
                        $imageUrl = $item->image_path;
                    } else {
                        $imageUrl = asset('storage/' . $item->image_path);
                    }
                }
            @endphp

            <img src="{{ $imageUrl }}" alt="{{ $item->name }}" onerror="this.src='https://via.placeholder.com/200x200?text=商品画像';">

            <h3>{{ $item->name }}</h3>
            <p>¥{{ number_format($item->price) }}</p>

            @if($item->purchase)
                <span class="sold-label">Sold</span>
            @endif
        </a>
    </div>
@empty
    <p>商品がありません。</p>
@endforelse
</div>
