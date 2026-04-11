<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Trainer - {{ $trainer->full_name_with_title ?: $trainer->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy: #1e2a4a;
            --sun: #f3c84b;
            --ink: #1f2937;
            --paper: #ffffff;
            --mist: #eef2f7;
        }

        body {
            margin: 0;
            background: radial-gradient(circle at top right, #dde7ff 0%, #f6f8fc 35%, #f8fbff 100%);
            color: var(--ink);
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            padding: 120px 20px 52px;
            background: linear-gradient(120deg, var(--navy), #334f8a);
            color: #fff;
        }

        .hero-inner,
        .content {
            width: min(1120px, 100%);
            margin: 0 auto;
        }

        .hero-card {
            display: flex;
            gap: 22px;
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 20px;
            padding: 24px;
            backdrop-filter: blur(4px);
        }

        .avatar {
            width: 108px;
            height: 108px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(243, 200, 75, 0.9);
            background: #d9dce3;
        }

        .hero-name {
            margin: 0;
            font-size: 1.9rem;
            line-height: 1.2;
        }

        .hero-headline {
            margin: 8px 0 0;
            color: #fdf3d0;
            font-weight: 500;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(243, 200, 75, 0.22);
            color: #ffe59f;
            font-size: 0.88rem;
        }

        .content {
            margin-top: -26px;
            padding: 0 20px 64px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            align-items: start;
        }

        .card {
            background: var(--paper);
            border: 1px solid #e4e8f1;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 10px 26px rgba(20, 28, 45, 0.08);
            margin-bottom: 20px;
        }

        .card h2 {
            margin: 0 0 12px;
            font-size: 1.1rem;
            color: #152238;
        }

        .bio {
            line-height: 1.7;
            color: #334155;
        }

        .socials {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #dae1ee;
            border-radius: 999px;
            padding: 8px 12px;
            text-decoration: none;
            color: #203457;
            font-weight: 500;
        }

        .social-link:hover {
            background: #f5f8ff;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .metric {
            background: var(--mist);
            border-radius: 12px;
            padding: 14px;
        }

        .metric-label {
            color: #5b6473;
            font-size: 0.84rem;
            margin-bottom: 6px;
        }

        .metric-value {
            margin: 0;
            font-size: 1.45rem;
            color: #13233d;
            font-weight: 700;
        }

        .class-list {
            display: grid;
            gap: 12px;
        }

        .class-item {
            border: 1px solid #e4e8f1;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #fff;
        }

        .class-title {
            margin: 0;
            color: #1f2f4b;
            font-size: 0.98rem;
            font-weight: 600;
        }

        .class-sub {
            margin: 4px 0 0;
            font-size: 0.84rem;
            color: #687385;
        }

        .cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: var(--sun);
            color: #141414;
            border-radius: 999px;
            padding: 9px 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .cta:hover {
            filter: brightness(0.95);
        }

        .empty {
            color: #6b7280;
            font-size: 0.93rem;
            margin: 0;
        }

        @media (max-width: 992px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero {
                padding-top: 104px;
            }

            .hero-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .metrics {
                grid-template-columns: 1fr;
            }

            .class-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .cta {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    @include('partials.navbar-after-login')

    <section class="hero">
        <div class="hero-inner">
            <div class="hero-card">
                <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}" class="avatar">
                <div>
                    <h1 class="hero-name">{{ $trainer->full_name_with_title ?: $trainer->name }}</h1>
                    <p class="hero-headline">{{ $trainer->profession ?: 'Trainer Profesional di idSpora' }}</p>
                    @if(!empty($trainer->institution))
                        <span class="pill"><i class="bi bi-buildings"></i> {{ $trainer->institution }}</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <main class="content">
        <div class="grid">
            <section>
                <article class="card">
                    <h2>Bio Trainer</h2>
                    <p class="bio">{{ $trainer->bio ?: 'Trainer ini belum menambahkan bio publik.' }}</p>
                </article>

                <article class="card">
                    <h2>Kelas Aktif</h2>
                    <div class="class-list">
                        @forelse($activeCourses as $course)
                            <div class="class-item">
                                <div>
                                    <p class="class-title">{{ $course->name }}</p>
                                    <p class="class-sub">Course • {{ $course->students_count ?? 0 }} peserta</p>
                                </div>
                                <a href="{{ route('courses.show', $course) }}" class="cta">Daftar</a>
                            </div>
                        @empty
                            <p class="empty">Belum ada course aktif saat ini.</p>
                        @endforelse

                        @foreach($activeEvents as $event)
                            <div class="class-item">
                                <div>
                                    <p class="class-title">{{ $event->title }}</p>
                                    <p class="class-sub">Event •
                                        {{ $event->event_date ? $event->event_date->translatedFormat('d M Y') : '-' }} •
                                        {{ $event->participants_count ?? 0 }} peserta</p>
                                </div>
                                <a href="{{ route('events.show', $event) }}" class="cta">Daftar</a>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <aside>
                <article class="card">
                    <h2>Tautan Sosial</h2>
                    <div class="socials">
                        @if(!empty($trainer->website))
                            <a href="{{ $trainer->website }}" target="_blank" rel="noopener" class="social-link">
                                <i class="bi bi-globe2"></i> Website
                            </a>
                        @endif
                        @if(!empty($trainer->linkedin_url))
                            <a href="{{ $trainer->linkedin_url }}" target="_blank" rel="noopener" class="social-link">
                                <i class="bi bi-linkedin"></i> LinkedIn
                            </a>
                        @endif
                        @if(empty($trainer->website) && empty($trainer->linkedin_url))
                            <p class="empty">Belum ada tautan sosial yang dibagikan.</p>
                        @endif
                    </div>
                </article>

                <article class="card">
                    <h2>Reputasi</h2>
                    <div class="metrics">
                        <div class="metric">
                            <div class="metric-label">Rating</div>
                            <p class="metric-value">{{ number_format((float) ($reputation['rating'] ?? 0), 1) }}</p>
                        </div>
                        <div class="metric">
                            <div class="metric-label">Total Siswa</div>
                            <p class="metric-value">{{ number_format((int) ($reputation['students'] ?? 0)) }}</p>
                        </div>
                        <div class="metric" style="grid-column: span 2;">
                            <div class="metric-label">Total Ulasan</div>
                            <p class="metric-value">{{ number_format((int) ($reputation['rating_count'] ?? 0)) }}</p>
                        </div>
                    </div>
                </article>
            </aside>
        </div>
    </main>
</body>

</html>