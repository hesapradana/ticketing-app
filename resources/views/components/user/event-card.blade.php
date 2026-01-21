@props(['title', 'date', 'location', 'price', 'image', 'href' => null, 'soldOut' => false, 'lowStock' => false])

@php
$formattedPrice = $price ? 'Rp ' . number_format($price, 0, ',', '.') : 'Harga tidak tersedia';
$formattedDate = $date
    ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y, H:i')
    : 'Tanggal tidak tersedia';
$imageUrl = $image
    ? (filter_var($image, FILTER_VALIDATE_URL)
        ? $image
        : asset('images/events/' . $image))
    : asset('images/konser.jpeg');
@endphp

<a href="{{ $href ?? '#' }}" class="block">
    <div class="card bg-base-100 h-96 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="h-48 overflow-hidden bg-gray-100 rounded-t-lg flex items-center justify-center relative">
            <img
                src="{{ $imageUrl }}"
                alt="{{ $title }}"
                class="max-w-full max-h-full object-contain"
            >
            @if($soldOut)
                <span class="badge badge-error absolute top-2 right-2 text-white font-bold">SOLD OUT</span>
            @elseif($lowStock)
                <span class="badge badge-warning absolute top-2 right-2 font-bold">Tiket Terbatas</span>
            @endif
        </div>

        <div class="card-body">
            <h2 class="card-title">
                {{ $title }}
            </h2>

            <p class="text-sm text-gray-500">
                {{ $formattedDate }}
            </p>

            <p class="text-sm">
                📍 {{ $location }}
            </p>

            <p class="font-bold text-lg mt-2">
                {{ $formattedPrice }}
            </p>

        </div>
    </div>
</a>
