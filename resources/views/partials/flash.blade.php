@unless(request()->routeIs('admin.*'))
@php
    $hasMessages = session('success') || session('error') || $errors->any();
@endphp
@if($hasMessages)
    <div class="flash-toast-container" aria-live="polite" aria-atomic="true">
        @if(session('success'))
            <div class="flash-toast flash-success" data-timeout="4500" role="status">
                <div class="flash-icon">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03 13 5l-1.06-1.06-4.97 4.95L4.53 7.47 3.47 8.53z"/>
                    </svg>
                </div>
                <div class="flash-body">
                    <div class="flash-title">Berhasil</div>
                    <div class="flash-message">{{ session('success') }}</div>
                </div>
                <button class="flash-close" type="button" aria-label="Tutup">&times;</button>
                <div class="flash-progress"></div>
            </div>
        @endif
        @if(session('error'))
            <div class="flash-toast flash-error" data-timeout="6000" role="alert">
                <div class="flash-icon">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0-8.707L5.354 3.646 3.646 5.354 6.293 8l-2.647 2.646 1.708 1.708L8 9.707l2.646 2.647 1.708-1.708L9.707 8l2.647-2.646-1.708-1.708z"/>
                    </svg>
                </div>
                <div class="flash-body">
                    <div class="flash-title">Gagal</div>
                    <div class="flash-message">{{ session('error') }}</div>
                </div>
                <button class="flash-close" type="button" aria-label="Tutup">&times;</button>
                <div class="flash-progress"></div>
            </div>
        @endif
        @if($errors->any())
            <div class="flash-toast flash-warning" data-timeout="8000" role="alert">
                <div class="flash-icon">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" d="M7.002 1.566a1.5 1.5 0 0 1 1.996 0l6.285 5.798c.864.797.33 2.278-.898 2.278H1.615c-1.228 0-1.762-1.48-.898-2.278zM8 5c-.535 0-.954.462-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                    </svg>
                </div>
                <div class="flash-body">
                    <div class="flash-title">Perlu Perhatian</div>
                    <ul class="flash-list">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="flash-close" type="button" aria-label="Tutup">&times;</button>
                <div class="flash-progress"></div>
            </div>
        @endif
    </div>
    <script>
    (function(){
        const toasts = document.querySelectorAll('.flash-toast');
        toasts.forEach(t => {
            // Trigger entrance animation
            requestAnimationFrame(() => t.classList.add('show'));
            const timeout = parseInt(t.getAttribute('data-timeout')||'5000',10);
            // Auto dismiss
            const timer = setTimeout(() => dismissToast(t), timeout);
            // Pause on hover
            t.addEventListener('mouseenter', () => {
                t.classList.add('paused');
                clearTimeout(timer);
            });
            t.addEventListener('mouseleave', () => {
                if(!t.classList.contains('closing')){
                    const remaining = timeout * progressRemainingRatio(t);
                    setTimeout(() => dismissToast(t), remaining);
                }
                t.classList.remove('paused');
            });
            // Close button
            const closeBtn = t.querySelector('.flash-close');
            if(closeBtn){
                closeBtn.addEventListener('click', () => dismissToast(t));
            }
        });

        function progressRemainingRatio(toast){
            const bar = toast.querySelector('.flash-progress');
            if(!bar) return 1;
            const width = parseFloat(getComputedStyle(bar).getPropertyValue('--progress-width')||'100');
            return width/100;
        }

        function dismissToast(toast){
            if(toast.classList.contains('closing')) return;
            toast.classList.add('closing');
            toast.classList.remove('show');
            setTimeout(()=> toast.remove(), 450);
        }
    })();
    </script>
    <style>
        .flash-toast-container{position:fixed;top:1rem;right:1rem;display:flex;flex-direction:column;gap:.75rem;z-index:1080;max-width:340px;width:100%;}
        .flash-toast{--flash-bg:#fff;--flash-border:#e5e7eb;--flash-color:#111827;--flash-accent:#6366f1;--progress-width:100;position:relative;display:flex;align-items:flex-start;gap:.75rem;padding:.9rem 1rem .95rem 1rem;border:1px solid var(--flash-border);background:linear-gradient(135deg,var(--flash-bg) 0%,#f9fafb 100%);border-radius:14px;box-shadow:0 8px 24px -8px rgba(0,0,0,.18),0 1px 3px rgba(0,0,0,.08);transform:translateY(-8px) scale(.96);opacity:0;transition:transform .45s cubic-bezier(.16,.8,.24,1),opacity .45s ease,box-shadow .3s ease;overflow:hidden;font-size:.875rem;}
        .flash-toast.show{transform:translateY(0) scale(1);opacity:1;}
        .flash-toast.closing{opacity:0;transform:translateY(-6px) scale(.95);}
        .flash-icon{flex:0 0 auto;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:12px;background:var(--flash-accent);color:#fff;box-shadow:0 4px 10px -2px rgba(0,0,0,.25),0 0 0 1px rgba(255,255,255,.15) inset;}
        .flash-body{flex:1 1 auto;min-width:0;}
        .flash-title{font-weight:600;margin-bottom:2px;letter-spacing:.3px;font-size:.78rem;text-transform:uppercase;opacity:.85;}
        .flash-message,.flash-list{color:var(--flash-color);margin:0;line-height:1.3;}
        .flash-list{padding-left:1rem;margin-top:.1rem;}
        .flash-list li{margin:0 0 2px 0;}
        .flash-close{background:none;border:none;color:var(--flash-color);font-size:1.15rem;line-height:1;cursor:pointer;padding:0 .25rem;opacity:.55;transition:opacity .2s ease, transform .2s ease;align-self:flex-start;margin-left:.25rem;}
        .flash-close:hover{opacity:1;transform:scale(1.08);}        
        .flash-progress{position:absolute;left:0;bottom:0;height:3px;width:100%;background:linear-gradient(90deg,var(--flash-accent) 0%,#0ea5e9 60%,#10b981 100%);animation:flash-progress linear forwards;height:3px;}
        .flash-toast.paused .flash-progress{animation-play-state:paused;}
        @keyframes flash-progress{from{--progress-width:100;width:100%;}to{--progress-width:0;width:0%;}}

        /* Variants */
        .flash-success{--flash-accent:#16a34a;--flash-border:#bbf7d0;--flash-bg:#ecfdf5;--flash-color:#065f46;}
        .flash-error{--flash-accent:#dc2626;--flash-border:#fecaca;--flash-bg:#fef2f2;--flash-color:#7f1d1d;}
        .flash-warning{--flash-accent:#d97706;--flash-border:#fde68a;--flash-bg:#fffbeb;--flash-color:#78350f;}

        /* Scrollable container fallback */
        @media (max-width: 576px){
            .flash-toast-container{left:0;right:0;top:.5rem;align-items:center;padding:0 .5rem;}
            .flash-toast{width:100%;}
        }
    </style>
@endif
@endunless