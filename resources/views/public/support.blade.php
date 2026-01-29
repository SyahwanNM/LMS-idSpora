<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pertanyaan & Kendala - idSPORA</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background:#f8fafc; padding-top:90px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 2rem 1rem 3rem; }
        .cardx { background:#fff; border:1px solid #e5e7eb; border-radius:28px; box-shadow: 0 18px 50px rgba(15,23,42,.08); }
        .neu { background:#fff; border:1px solid #e5e7eb; border-radius: 16px; padding: .85rem 1rem; }
        .neu:focus { outline:none; border-color:#c7d2fe; box-shadow: 0 0 0 4px rgba(79,70,229,.12); }
        .btnx { border:none; border-radius: 18px; padding: .95rem 1.2rem; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; font-size: .78rem; }
        .btnx.primary { background:#4f46e5; color:#fff; box-shadow: 0 16px 26px rgba(79,70,229,.25); }
        .btnx.primary:hover { background:#4338ca; transform: translateY(-1px); }
        .badge-pill { display:inline-flex; align-items:center; gap:.5rem; padding:.55rem 1rem; border-radius:999px; font-weight:900; letter-spacing:.12em; font-size:.72rem; text-transform:uppercase; background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; }
        .muted { color:#64748b; }
    </style>
</head>
<body>
    @include('partials.navbar-before-login')

    <div class="wrap">
        <header class="text-center mb-5">
            <div class="badge-pill"><i class="bi bi-chat-dots"></i> Bantuan & Masukan</div>
            <h1 class="mt-4 text-4xl md:text-5xl" style="font-weight:900; letter-spacing:-.02em; color:#0f172a;">
                Pertanyaan, Kendala, atau Masukan?
            </h1>
            <p class="mt-3 muted" style="max-width: 720px; margin: 0 auto;">
                Sampaikan ke tim pengembang idSPORA. Ceritakan kendala secara jelas agar kami bisa menindaklanjuti lebih cepat.
            </p>
        </header>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#047857;">
                <b>Berhasil:</b> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-3" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b;">
                <b>Periksa lagi:</b>
                <ul class="mt-2 mb-0" style="padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="cardx p-4 p-md-5">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="p-4 rounded-4" style="background: radial-gradient(120% 120% at 30% 20%, rgba(79,70,229,.18) 0%, rgba(2,6,23,0) 55%), #ffffff; border:1px solid #eef2f7;">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center" style="width:46px;height:46px;border-radius:16px;background:#fef3c7;color:#92400e;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div>
                                <div style="font-weight:900; color:#0f172a;">Tips menulis kendala</div>
                                <div class="muted" style="font-size:.9rem;">Biar cepat ditangani</div>
                            </div>
                        </div>
                        <ul class="mb-0 muted" style="padding-left: 1.2rem; display:grid; gap:.5rem;">
                            <li>Sebutkan halaman/fitur yang bermasalah.</li>
                            <li>Tulis langkah-langkah hingga error muncul.</li>
                            <li>Jika ada, cantumkan pesan error.</li>
                            <li>Berikan saran perbaikan (opsional).</li>
                        </ul>
                        <div class="mt-4 rounded-4 overflow-hidden" style="border:10px solid #fff; box-shadow:0 18px 40px rgba(15,23,42,.12); background:#f1f5f9;">
                            <img src="{{ asset('aset/ai.jpg') }}" alt="Ilustrasi Bantuan" style="width:100%; height:220px; object-fit:cover; display:block;"
                                 onerror="this.onerror=null; this.src='{{ asset('aset/poster.png') }}';">
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <form method="POST" action="{{ route('public.support.store') }}" class="space-y-3">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:800; letter-spacing:.08em; font-size:.75rem; text-transform:uppercase; color:#94a3b8;">Nama</label>
                                <input class="neu w-100" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Nama kamu" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:800; letter-spacing:.08em; font-size:.75rem; text-transform:uppercase; color:#94a3b8;">Email</label>
                                <input class="neu w-100" name="email" type="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="email@domain.com" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:800; letter-spacing:.08em; font-size:.75rem; text-transform:uppercase; color:#94a3b8;">Jenis</label>
                                <select class="neu w-100" name="type">
                                    <option value="kendala" {{ old('type') === 'kendala' ? 'selected' : '' }}>Kendala / Bug</option>
                                    <option value="pertanyaan" {{ old('type') === 'pertanyaan' ? 'selected' : '' }}>Pertanyaan</option>
                                    <option value="masukan" {{ old('type') === 'masukan' ? 'selected' : '' }}>Masukan</option>
                                    <option value="lainnya" {{ old('type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:800; letter-spacing:.08em; font-size:.75rem; text-transform:uppercase; color:#94a3b8;">Subjek</label>
                                <input class="neu w-100" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Error saat submit form" />
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-weight:800; letter-spacing:.08em; font-size:.75rem; text-transform:uppercase; color:#94a3b8;">Pesan</label>
                                <textarea class="neu w-100" rows="6" name="message" placeholder="Ceritakan kendala/pertanyaan kamu...">{{ old('message') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-2">
                            <button class="btnx primary" type="submit">
                                KIRIM PESAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer-before-login')
</body>
</html>

