<!-- Google Fonts: Inter/Roboto Style -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --ids-primary: #FFB703;
        --ids-secondary: #FB8500;
        --ids-bg: #F8F9FA;
        --ids-card-bg: #FFFFFF;
        --ids-text-main: #1A1D1F;
        --ids-text-muted: #6F767E;
        --ids-border: #EFEFEF;
    }

    body {
        background-color: var(--ids-bg) !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Override standard container to allow sidebar feel */
    .finance-wrapper {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: 0 -12px; /* Pull out of standard container padding */
    }

    /* Sidebar-like Nav */
    .finance-sidebar {
        width: 240px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid var(--ids-border);
        display: none; /* Hide on small screens */
    }

    @media (min-width: 992px) {
        .finance-sidebar { display: block; }
    }

    .nav-menu-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--ids-text-muted);
        letter-spacing: 1px;
        margin-bottom: 16px;
        display: block;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: var(--ids-text-main);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        transition: all 0.2s;
    }

    .sidebar-link i {
        font-size: 1.2rem;
        margin-right: 12px;
        color: var(--ids-text-muted);
    }

    .sidebar-link:hover {
        background: #F4F4F4;
        color: var(--ids-text-main);
    }

    .sidebar-link.active {
        background: #FEF6E6;
        color: var(--ids-text-main);
    }

    .sidebar-link.active i {
        color: var(--ids-secondary);
    }

    .badge-notif {
        background: #D93F3F;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        margin-left: auto;
    }

    /* Main Content */
    .finance-main {
        flex: 1;
        padding: 24px;
    }
</style>
