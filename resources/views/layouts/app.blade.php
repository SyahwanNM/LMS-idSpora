<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    @php
        $isAdminLayoutUser = auth()->check() && strcasecmp(auth()->user()->role ?? '', 'admin') === 0;
    @endphp

    @if($isAdminLayoutUser)
        <style>
            .global-notification { position: fixed; top: 14px; right: 14px; display:flex; flex-direction:column; gap:10px; align-items:flex-end; z-index:12050; pointer-events:none; }
            .notification { min-width: 300px; max-width:420px; pointer-events:auto; display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px; box-shadow: 0 8px 30px rgba(2,6,23,0.12); color:#fff; transform: translateY(-6px) scale(.99); opacity:0; transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease; }
            .notification.show { transform: translateY(0) scale(1); opacity:1; }
            .notification.success { background: linear-gradient(90deg,#16a34a,#34d399); }
            .notification.error { background: linear-gradient(90deg,#dc2626,#f43f5e); }
            .notification .notif-message{ flex:1; font-weight:600; font-size:0.95rem; }
            .notification .notif-close { background:transparent; border:0; color:rgba(255,255,255,.95); }
        </style>

        @if(session('success') || session('login_success') || session('error'))
            <div id="globalNotifications" class="global-notification" aria-live="polite" aria-atomic="true">
                @if(session('login_success'))
                    <div class="notification success" role="status" data-timeout="4200">
                        <div class="notif-message">{{ session('login_success') }}</div>
                        <button class="notif-close" aria-label="Close" type="button">&times;</button>
                    </div>
                @elseif(session('success'))
                    <div class="notification success" role="status" data-timeout="3800">
                        <div class="notif-message">{{ session('success') }}</div>
                        <button class="notif-close" aria-label="Close" type="button">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="notification error" role="status" data-timeout="6000">
                        <div class="notif-message">{{ session('error') }}</div>
                        <button class="notif-close" aria-label="Close" type="button">&times;</button>
                    </div>
                @endif
            </div>
        @endif

        <script>
            (function(){
                function wireBanner(){
                    try {
                        const wrap = document.getElementById('globalNotifications');
                        if(!wrap) return;
                        wrap.querySelectorAll('.notification').forEach(function(n){
                            setTimeout(function(){ n.classList.add('show'); }, 20);
                            const timeout = parseInt(n.getAttribute('data-timeout') || 4000, 10);
                            const closeBtn = n.querySelector('.notif-close');
                            const hide = function(){ n.classList.remove('show'); setTimeout(()=> n.remove(), 260); };
                            if(closeBtn) closeBtn.addEventListener('click', hide);
                            setTimeout(hide, timeout);
                        });
                    } catch(e){}
                }

                document.addEventListener('DOMContentLoaded', wireBanner);

                window.adminNotify = function(type, message, timeout){
                    try {
                        const kind = (type === 'error') ? 'error' : 'success';
                        const text = (message == null) ? '' : String(message);
                        const ms = Number.isFinite(Number(timeout)) ? Math.max(800, Number(timeout)) : 3800;

                        let wrap = document.getElementById('globalNotifications');
                        if(!wrap){
                            wrap = document.createElement('div');
                            wrap.id = 'globalNotifications';
                            wrap.className = 'global-notification';
                            wrap.setAttribute('aria-live', 'polite');
                            wrap.setAttribute('aria-atomic', 'true');
                            document.body.appendChild(wrap);
                        }

                        const n = document.createElement('div');
                        n.className = 'notification ' + kind;
                        n.setAttribute('role', 'status');
                        n.setAttribute('data-timeout', String(ms));

                        const msg = document.createElement('div');
                        msg.className = 'notif-message';
                        msg.textContent = text;

                        const close = document.createElement('button');
                        close.className = 'notif-close';
                        close.setAttribute('aria-label', 'Close');
                        close.type = 'button';
                        close.innerHTML = '&times;';

                        n.appendChild(msg);
                        n.appendChild(close);
                        wrap.appendChild(n);

                        const hide = function(){ n.classList.remove('show'); setTimeout(()=> n.remove(), 260); };
                        close.addEventListener('click', hide);
                        setTimeout(function(){ n.classList.add('show'); }, 20);
                        setTimeout(hide, ms);
                    } catch(e){}
                };
            })();
        </script>
    @endif

    <div class="min-h-screen bg-gray-100">
        @yield('content')
    </div>
</body>
</html>
