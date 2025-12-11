@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    .biodata {
        max-width: 450px;
        margin: 20px auto auto 20px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
    }

    .biodata img {
        display: block;
        margin: 10px auto 20px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }

    .biodata h4{
        text-align: center;
        margin-bottom: 1px;
        font-size: 18px;
        color: #000;
    }

    .biodata h6{
        text-align: center;
        margin-bottom: 15px;
        font-size: 15px;
        color: #333;
    }
</style>
<body>
    <section>
        <div class="biodata">
            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
                 referrerpolicy="no-referrer"
                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6b7280&color=ffffff&format=png';">
            <h4>{{ Auth::user()->name }}</h4>
            <h6>{{ Auth::user()->email }}</h6>

            <h2 class="mt-3">Biodata</h2>
            <p>Name: {{ Auth::user()->name }}</p>
            <p>Email: {{ Auth::user()->email }}</p>
            <p>Role: {{ Auth::user()->role ?? 'user' }}</p>

            <hr>
            <h2 class="mt-3">Event Yang Didaftarkan</h2>
            @php($regs = Auth::user()->eventRegistrations()->with('event')->latest()->get())
            @if($regs->isEmpty())
                <p class="text-muted">Belum ada event yang didaftarkan.</p>
            @else
                <ul style="list-style:none; padding-left:0;">
                    @foreach($regs as $reg)
                        <li style="margin-bottom:12px;">
                            <div style="display:flex; align-items:center; justify-content:space-between;">
                                <div>
                                    <strong>{{ $reg->event?->title ?? 'Event' }}</strong>
                                    <div class="text-muted" style="font-size:12px;">
                                        {{ optional($reg->event)->date_start ? optional($reg->event)->date_start->format('d M Y') : '' }}
                                        @if(optional($reg->event)->location)
                                            • {{ $reg->event->location }}
                                        @endif
                                    </div>
                                </div>
                                @if($reg->event)
                                    <a href="{{ route('events.show', $reg->event) }}" class="btn btn-sm btn-primary">Lihat Detail</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <hr>
            <h2 class="mt-3">Event Tersimpan</h2>
            @php($saved = Auth::user()->savedEvents()->latest('user_saved_events.created_at')->get())
            @if($saved->isEmpty())
                <p class="text-muted">Belum ada event yang disimpan.</p>
            @else
                <ul style="list-style:none; padding-left:0;">
                    @foreach($saved as $ev)
                        <li style="margin-bottom:12px;">
                            <div style="display:flex; align-items:center; justify-content:space-between;">
                                <div>
                                    <strong>{{ $ev->title ?? 'Event' }}</strong>
                                    <div class="text-muted" style="font-size:12px;">
                                        {{ $ev->event_date ? \Carbon\Carbon::parse($ev->event_date)->format('d M Y') : '' }}
                                        @if($ev->location)
                                            • {{ $ev->location }}
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('events.show', $ev) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</body>
</html>
