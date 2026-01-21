<x-layouts.app>
    <div class="hero min-h-screen relative" style="background-image: url('/assets/images/banner.jpg'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-blue-900/70"></div>
        <div class="hero-content text-center text-white relative">
            <div class="max-w-4xl">
                <h1 class="text-5xl font-bold">Hi, Amankan Tiketmu yuk.</h1>
                <p class="py-6">
                    BengTix: Beli tiket, auto asik.
                </p>
            </div>
        </div>
    </div>

    <section id="events" class="max-w-7xl mx-auto py-12 px-6">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-black uppercase italic">Event</h2>
            <div class="flex gap-2">
                <a href="{{ route('home') }}#events">
                    <x-user.category-pill :label="'Semua'" :active="!request('kategori')" />
                </a>
                @foreach($categories as $kategori)
                <a href="{{ route('home', ['kategori' => $kategori->id]) }}#events">
                    <x-user.category-pill :label="$kategori->nama" :active="request('kategori') == $kategori->id" />
                </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($events as $event)
            <x-user.event-card
                :title="$event->judul"
                :date="$event->tanggal_waktu"
                :location="$event->lokasi"
                :price="$event->tikets_min_harga"
                :image="$event->gambar"
                :href="route('events.show', $event)"
                :soldOut="$event->isSoldOut()"
                :lowStock="$event->isLowStock()" />
            @endforeach
        </div>
    </section>
</x-layouts.app>
