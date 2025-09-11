<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>idSpora — Events</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
        }

        .hero-grad {
            background: radial-gradient(1200px 400px at 50% -10%, #5b4b8a 5%, #3b2f63 40%, #241c3c 85%);
        }

        .chip {
            @apply px-4 py-2 rounded-full border border-white/20 bg-white/5 text-white hover:bg-white/10 transition;
        }

        .chip-dark {
            @apply px-4 py-2 rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition;
        }

        .btn-primary {
            @apply inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 shadow-sm;
        }

        .price-strike {
            text-decoration: line-through;
        }
    </style>
</head>

<body class="bg-slate-50">
    {{-- NAVBAR --}}
    <header class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2">
                <span class="text-2xl font-extrabold text-amber-500">id</span>
                <span class="text-2xl font-extrabold text-slate-800">Spora</span>
            </a>
            <span class="text-base font-semibold text-slate-700">Events</span>
        </div>
    </header>
    {{-- HERO --}}
    <section class="hero-grad text-white py-10">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-3xl font-extrabold tracking-tight mb-2">Temukan & Ikuti Event Terbaik</h1>
            <p class="mb-6 text-white/80">Upgrade skill-mu bersama mentor industri lewat webinar, workshop, dan sesi
                onsite. Dapatkan promo spesial untuk pendaftaran event bulan ini!</p>
            <div class="flex flex-col items-center gap-4 mb-6">
                <div class="bg-amber-500/90 rounded-xl px-6 py-4 shadow-lg inline-block">
                    <span class="text-lg font-bold text-white">Promo September: Diskon hingga 30% untuk event AI & Data
                        Science!</span>
                </div>
                <ul class="flex flex-wrap justify-center gap-4 mt-2 text-sm">
                    <li class="bg-white/10 px-4 py-2 rounded-full border border-white/20">Sertifikat Resmi</li>
                    <li class="bg-white/10 px-4 py-2 rounded-full border border-white/20">Mentor Berpengalaman</li>
                    <li class="bg-white/10 px-4 py-2 rounded-full border border-white/20">Materi Eksklusif</li>
                    <li class="bg-white/10 px-4 py-2 rounded-full border border-white/20">Networking Peserta</li>
                </ul>
                <div class="mt-6">
                    <h2 class="text-lg font-bold text-white mb-3">Apa Kata Peserta?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white/10 border border-white/20 rounded-xl p-5 shadow-sm text-left">
                            <div class="flex items-center gap-3 mb-2">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                    class="w-10 h-10 rounded-full border" alt="User">
                                <span class="font-semibold text-white">Budi Santoso</span>
                            </div>
                            <p class="text-white/80 text-sm">“Event AI for Lectures sangat bermanfaat! Materinya
                                up-to-date dan mentornya ramah.”</p>
                        </div>
                        <div class="bg-white/10 border border-white/20 rounded-xl p-5 shadow-sm text-left">
                            <div class="flex items-center gap-3 mb-2">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg"
                                    class="w-10 h-10 rounded-full border" alt="User">
                                <span class="font-semibold text-white">Siti Rahma</span>
                            </div>
                            <p class="text-white/80 text-sm">“Saya dapat banyak insight baru dan relasi dari event
                                onsite. Recommended!”</p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="#event-list" class="btn-primary rounded-full px-8 py-3 text-lg font-semibold shadow-lg">Lihat
                Event</a>
        </div>
    </section>
    {{-- TESTIMONI & PARTNER --}}
    <section class="bg-white py-8">
        <form action="{{ route('events.index') }}" method="GET" class="flex gap-2 justify-center">
            <input
                class="w-full max-w-xs rounded-xl border border-slate-300 bg-white py-2 px-3 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500"
                name="q" value="{{ request('q') }}" placeholder="Cari event..." />
            <button class="btn-primary" type="submit">Cari</button>
        </form>
    </section>
    {{-- GRID EVENTS --}}
    <main class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            @php
                // OPSIONAL: contoh dummy saat tidak ada controller
                $dummy = collect(range(1, 9))->map(fn($i) => (object) [
                    'title' => 'AI for Lectures',
                    'date' => '04 September 2025',
                    'city' => 'Bandung',
                    'time' => '09:00 WIB',
                    'quota' => 50,
                    'price_original' => 100000,
                    'price_sale' => 75000,
                    'image_url' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?q=80&w=1200&auto=format&fit=crop',
                    'delivery' => 'Kombinasi',
                    'venue' => 'Gd. Sate/IT Telkom University',
                    'slug' => 'ai-for-lectures-' . $i,
                ]);
                $list = isset($events) ? $events : $dummy;
            @endphp
            <div class="grid gap-6 sm:gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @foreach($list as $event)
                    <article
                        class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden group transition hover:shadow-lg">
                        {{-- Poster / banner --}}
                        <div class="relative aspect-[4/3] bg-slate-100">
                            <img src="{{ $event->image_url ?? asset('images/event-placeholder.jpg') }}"
                                alt="{{ $event->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-medium text-slate-700 ring-1 ring-slate-200">
                                    {{ $event->delivery ?? 'Online' }}
                                </span>
                            </div>
                        </div>
                        {{-- Content --}}
                        <div class="p-4">
                            <h3 class="text-slate-900 font-semibold text-lg mb-2 line-clamp-1">{{ $event->title }}</h3>
                            <div class="space-y-2 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    {{-- calendar --}}
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
                                    </svg>
                                    <span>{{ $event->date }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    {{-- location pin --}}
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 21s7-4.35 7-10a7 7 0 1 0-14 0c0 5.65 7 10 7 10z" />
                                        <circle cx="12" cy="11" r="2" fill="currentColor" class="text-slate-400"></circle>
                                    </svg>
                                    <span>{{ $event->city ?? '-' }} • {{ $event->time ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    {{-- users --}}
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m14-11a4 4 0 1 0-8 0 4 4 0 0 0 8 0z" />
                                    </svg>
                                    <span>{{ $event->quota ?? 0 }} kuota</span>
                                </div>
                            </div>
                            {{-- Price + CTA --}}
                            <div class="mt-4 flex items-end justify-between">
                                <div class="leading-tight">
                                    @if(($event->price_original ?? 0) > ($event->price_sale ?? 0))
                                        <div class="text-[12px] text-slate-400 price-strike">
                                            Rp {{ number_format($event->price_original, 0, ',', '.') }}
                                        </div>
                                    @endif
                                    <div class="text-amber-600 font-bold">
                                        Rp {{ number_format($event->price_sale ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <a href="{{ route('events.show', $event->slug ?? 'detail') }}"
                                    class="rounded-full px-6 py-2 font-semibold text-white bg-amber-500 hover:bg-amber-600 shadow transition">Daftar</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            {{-- PAGINATION (opsional pakai ->links()) --}}
            {{-- PAGINATION hanya jika $events adalah paginator --}}
            @if(isset($events) && method_exists($events, 'links'))
                <div class="mt-8 text-center">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </main>
    {{-- FOOTER --}}
    <footer class="py-6 border-t border-slate-200 bg-white mt-10">
        <div class="max-w-4xl mx-auto px-4 text-sm text-slate-500 text-center">
            © {{ now()->year }} idSpora. All rights reserved.
        </div>
    </footer>
</body>

</html>