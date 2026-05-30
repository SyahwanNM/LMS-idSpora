<style>
.trainer-page #sidebar {
    box-sizing: border-box;
    height: calc(100vh - 60px);
    width: 250px;
    padding: 16px 16px 16px; /* Reduced top padding */
    background-color: var(--white-clr);
    border-right: 1px solid var(--line-clr);
    position: fixed;
    top: 60px;
    left: 0;
    align-self: start;
    transition: none;
    overflow: hidden;
    white-space: nowrap;
    display: flex;
    flex-direction: column;
    z-index: 900;
}

.trainer-page #sidebar + .main-wrapper {
    margin-left: 250px;
    transition: none;
}

html.sidebar-ready .trainer-page #sidebar {
    transition: 300ms ease-in-out;
}

html.sidebar-ready .trainer-page #sidebar + .main-wrapper {
    transition: margin-left 300ms ease-in-out;
}

.trainer-page #sidebar ul li {
    margin-bottom: 2px;
}

.trainer-page #sidebar.close {
    padding: 24px 5px 16px;
    width: 60px;
}

html.sidebar-collapsed .trainer-page #sidebar {
    padding: 24px 5px 16px;
    width: 60px;
}

.trainer-page #sidebar.close + .main-wrapper {
    margin-left: 60px;
}

html.sidebar-collapsed .trainer-page #sidebar + .main-wrapper {
    margin-left: 60px;
}

.trainer-page #sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
    flex: 1;
    overflow: hidden;
}

/*BASE*/
.trainer-page #sidebar a,
.trainer-page #sidebar .dropdown-btn,
.trainer-page #sidebar .logo {
    border-radius: 10px;
    padding: 8px 12px;
    text-decoration: none;
    color: var(--text-clr);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-bottom: 2px;
    position: relative;
}

.trainer-page #sidebar ul li {
    margin-bottom: 6px;
}

.trainer-page .dropdown-btn {
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    font: inherit;
    cursor: pointer;
}

.trainer-page #sidebar svg {
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.trainer-page #sidebar i.bi {
    transition: all 0.3s ease;
}

/*ACTIVE*/

.trainer-page #sidebar ul li.active a {
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #3b2885 100%);
    color: var(--white-clr);
    box-shadow: 0 4px 15px rgba(27, 23, 99, 0.3);
    font-weight: 600;
}

.trainer-page #sidebar ul li.active a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 60%;
    width: 4px;
    background: #6366f1;
    border-radius: 0 4px 4px 0;
}

.trainer-page #sidebar ul li.active a svg {
    filter: drop-shadow(0 2px 4px rgba(255,255,255,0.2));
}

.trainer-page #sidebar ul li.active a i.bi {
    color: var(--white-clr);
    filter: drop-shadow(0 2px 4px rgba(255,255,255,0.2));
}

/*HOVER*/

.trainer-page #sidebar ul li:not(.active) a:hover,
.trainer-page #sidebar ul li:not(.active) .dropdown-btn:hover {
    background-color: var(--blue-background-clr);
    color: var(--main-navy-clr);
    transform: translateX(4px);
}

.trainer-page #sidebar ul li:not(.active) a:hover svg,
.trainer-page #sidebar ul li:not(.active) .dropdown-btn:hover svg {
    transform: scale(1.1);
}

.trainer-page #sidebar ul li:not(.active) a:hover i.bi {
    color: var(--main-navy-clr);
    transform: scale(1.1);
}

/*CLICK EFFECT*/

.trainer-page #sidebar ul li:not(.active) a:active,
.trainer-page #sidebar ul li:not(.active) .dropdown-btn:active {
    background-color: var(--main-navy-clr);
    color: var(--white-clr);
    transform: scale(0.98);
}

/*SUB MENU*/
.trainer-page #sidebar .sub-menu {
    display: grid;
    grid-template-rows: 0fr;
    transition: 300ms ease-in-out;
}

.trainer-page #sidebar .sub-menu > div {
    overflow: hidden;
}

.trainer-page #sidebar .sub-menu.show {
    grid-template-rows: 1fr;
}

.trainer-page #sidebar .sub-menu a {
    padding-left: 32px;
}

/*DROPDOWN ICON*/
.trainer-page .dropdown-btn svg {
    transition: transform 200ms ease;
}

.trainer-page .rotate svg:last-child {
    transform: rotate(180deg);
}

/* SIDEBAR HEADER */
.sidebar-header-title {
    padding: 0 16px 4px;
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.trainer-page #sidebar.close .sidebar-header-title {
    display: none;
}

/* SIDEBAR FOOTER HELP BOX */
.sidebar-footer {
    margin-top: auto;
    padding: 12px;
    flex-shrink: 0;
}

.help-box {
    background: linear-gradient(145deg, #f8fafc, #f1f5f9);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 12px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.help-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

.help-icon {
    width: 34px;
    height: 34px;
    background: var(--blue-background-clr);
    color: var(--main-navy-clr);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
}

.help-box h6 {
    font-size: 12px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0 0 2px;
}

.help-box p {
    font-size: 10px;
    color: #64748b;
    margin: 0 0 8px;
    line-height: 1.3;
    white-space: normal;
}

.btn-help {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px 12px;
    background: var(--main-navy-clr);
    color: white;
    font-size: 11px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-help:hover {
    background: #3b2885;
}

.trainer-page #sidebar.close .sidebar-footer {
    display: none;
}

/* Hide header and footer on mobile bottom nav */
@media (max-width: 768px) {
    .sidebar-header-title,
    .sidebar-footer {
        display: none !important;
    }
}

/* Navbar */
.trainer-page .navbar {
    height: 60px;
    width: auto;
    background-color: var(--white-clr);
    border-bottom: 1px solid var(--line-clr);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.trainer-page .breadcrum {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.trainer-page .breadcrum a {
    color: var(--text-clr);
    text-decoration: none;
    line-height: 1;
}

.trainer-page .breadcrum a:hover {
    color: var(--main-navy-clr);
    text-decoration: none;
}

.trainer-page .breadcrum span {
    line-height: 1;
}

.trainer-page .breadcrum .active {
    color: var(--main-navy-clr);
    font-weight: 500;
}

.trainer-page .navbar-right {
    display: flex;
    align-items: center;
    gap: 24px;
}

.trainer-page .search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.trainer-page .search-box input {
    padding: 10px 16px 10px 40px;
    border: 1px solid var(--line-clr);
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    width: 240px;
    transition: all 0.3s ease;
    line-height: 1.5;
    height: 40px;
}

.trainer-page .search-box input:focus {
    border-color: var(--main-navy-clr);
    box-shadow: 0 0 0 3px rgba(37, 35, 70, 0.1);
}

.trainer-page .search-box svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    fill: var(--text-clr);
    width: 18px;
    height: 18px;
}

.trainer-page .notification {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.trainer-page .notification:hover {
    background-color: var(--base-clr);
}

.trainer-page .notification svg {
    color: var(--text-clr);
    width: 20px;
    height: 20px;
    display: block;
}
.trainer-page .profile-photo {
    display: flex;
    align-items: center;
}

.trainer-page .profile-photo .dropdown-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.trainer-page .profile-photo .dropdown-btn:hover {
    background-color: var(--base-clr);
}

.trainer-page .profile-photo .dropdown-btn svg {
    display: block;
    flex-shrink: 0;
}

.trainer-page .profile-photo .dropdown-btn .name {
    line-height: 1;
    font-size: 14px;
}

/* TABLET RESPONSIVE (Auto-collapse sidebar) */
@media (max-width: 992px) and (min-width: 769px) {
    .trainer-page #sidebar {
        padding: 24px 5px 8px;
        width: 60px;
    }
    
    .trainer-page #sidebar + .main-wrapper {
        margin-left: 60px;
    }
    
    .trainer-page #sidebar span,
    .trainer-page #sidebar .dropdown-btn svg:last-child {
        display: none;
    }
    
    .trainer-page #sidebar ul a,
    .trainer-page #sidebar ul .dropdown-btn {
        justify-content: center;
        padding: 13.6px 0;
    }
    
    .trainer-page #sidebar svg {
        margin: 0 auto;
    }
}

/* MOBILE RESPONSIVE (Floating Glass Bottom Navigation) */
@media (max-width: 768px) {
    .trainer-page #sidebar {
        height: 65px;
        width: 92%;
        left: 4%;
        border-right: none;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        padding: 0;
        position: fixed;
        top: auto;
        bottom: 16px;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(24px) saturate(180%);
        -webkit-backdrop-filter: blur(24px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 40px rgba(31, 38, 135, 0.07), inset 0 0 0 1px rgba(255,255,255,0.3);
    }

    .trainer-page #sidebar + .main-wrapper,
    .trainer-page #sidebar.close + .main-wrapper {
        margin-left: 0;
        margin-bottom: 90px;
    }

    .trainer-page #sidebar > ul {
        display: contents;
    }

    .trainer-page #sidebar ul li {
        margin-bottom: 0;
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .trainer-page #sidebar ul a,
    .trainer-page #sidebar ul .dropdown-btn {
        width: 100%;
        height: 48px;
        padding: 4px 0;
        margin: 2px 4px;
        border-radius: 12px;
        justify-content: center;
        flex-direction: column;
        gap: 2px;
        background: transparent;
    }

    /* Active State in Mobile Nav */
    .trainer-page #sidebar ul li.active a {
        background: linear-gradient(135deg, var(--main-navy-clr) 0%, #3b2885 100%);
        box-shadow: 0 4px 12px rgba(27, 23, 99, 0.3);
        transform: translateY(-2px);
    }
    
    .trainer-page #sidebar ul li.active a::before {
        display: none; /* Hide the left accent line on mobile */
    }

    /* Tampilkan teks dengan font sangat kecil */
    .trainer-page #sidebar ul li span {
        display: block;
        font-size: 10px;
        line-height: 1;
        margin-top: 2px;
        font-weight: 600;
    }

    /* Sembunyikan toggle, panah dropdown */
    .trainer-page #sidebar .dropdown-btn svg:last-child,
    .trainer-page #toggle-btn {
        display: none;
    }

    /* Hover effect tuning for mobile */
    .trainer-page #sidebar ul li:not(.active) a:hover {
        transform: translateY(-2px);
    }

    /* Navbar styling adjustments for tablet/mobile */
    .trainer-page .navbar {
        padding: 0 16px;
    }

    .trainer-page .search-box input {
        width: 160px;
    }

    .trainer-page .navbar-right {
        gap: 16px;
    }
}

/* SMALL MOBILE SPECIFIC */
@media (max-width: 480px) {
    .trainer-page .search-box {
        display: none; /* Sembunyikan kolom pencarian agar navbar tidak terlalu sesak */
    }

    .trainer-page .navbar-right {
        gap: 12px;
    }

    .trainer-page .breadcrum a,
    .trainer-page .breadcrum span:not(.active) {
        display: none;
    }

    .trainer-page .breadcrum .active {
        font-size: 14px;
    }
}

</style>

<nav id="sidebar">
    <div class="sidebar-header-title">
        <span>MENU UTAMA</span>
    </div>
    <ul>
        <li class="{{ request()->routeIs('trainer.dashboard') ? 'active' : '' }}">
            <a href="{{ route('trainer.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                    <rect x="14" y="3" width="7" height="5" rx="1"></rect>
                    <rect x="14" y="12" width="7" height="9" rx="1"></rect>
                    <rect x="3" y="16" width="7" height="5" rx="1"></rect>
                </svg>
                <span>Dasbor</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('trainer.events', 'trainer.events.*') ? 'active' : '' }}">
            <a href="{{ route('trainer.events') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <circle cx="12" cy="15" r="1"></circle>
                </svg>
                <span>Event</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('trainer.courses', 'trainer.courses.*', 'trainer.detail-course') ? 'active' : '' }}">
            <a href="{{ route('trainer.courses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                <span>Course</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('trainer.feedback', 'trainer.feedback.*') ? 'active' : '' }}">
            <a href="{{ route('trainer.feedback') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                </svg>
                <span>Ulasan</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('trainer.certificates.*') ? 'active' : '' }}">
            <a href="{{ route('trainer.certificates.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="6"></circle>
                    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                </svg>
                <span>Sertifikat</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('trainer.finance', 'trainer.finance.*') ? 'active' : '' }}">
            <a href="{{ route('trainer.finance') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path>
                    <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path>
                    <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"></path>
                </svg>
                <span>Keuangan</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="help-box">
            <div class="help-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h6>Pusat Bantuan</h6>
            <p>Butuh bantuan? Silakan hubungi tim support.</p>
            <a href="https://wa.me/628989260731" target="_blank" class="btn-help">Hubungi</a>
        </div>
    </div>
</nav>





