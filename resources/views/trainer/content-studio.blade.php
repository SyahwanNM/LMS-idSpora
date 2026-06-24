@php
    $isAdmin = $isAdmin ?? false;

    $moduleTargetModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'pdf';
    });
    $videoTargetModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'video';
    });
    $existingQuizModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'quiz';
    });

    $isModuleApproved = $moduleTargetModules->isNotEmpty() && $moduleTargetModules->every(fn($m) => ($m->review_status ?? '') === 'approved');
    $isVideoApproved = $videoTargetModules->isNotEmpty() && $videoTargetModules->every(fn($m) => ($m->review_status ?? '') === 'approved');
    $isQuizApproved = $existingQuizModules->isNotEmpty() && $existingQuizModules->every(fn($m) => ($m->review_status ?? '') === 'approved');

    $hasEmptyModuleSlot = $moduleTargetModules->contains(fn($m) => empty($m->content_url));
    $hasEmptyVideoSlot = $videoTargetModules->contains(fn($m) => empty($m->content_url));
    $hasEmptyQuizSlot = $existingQuizModules->contains(fn($m) => ($m->quiz_questions_count ?? 0) <= 0);

    $moduleLocked = $isAdmin
        ? !($schemePermissions['can_module'] ?? false)
        : ($courseMaterialLocked || !($schemePermissions['can_module'] ?? false) || $isModuleApproved);
    $videoLocked = $isAdmin
        ? !($schemePermissions['can_video'] ?? false)
        : ($courseMaterialLocked || !($schemePermissions['can_video'] ?? false) || $isVideoApproved);
    $quizLocked = $isAdmin
        ? !($schemePermissions['can_quiz'] ?? false)
        : ($courseMaterialLocked || !($schemePermissions['can_quiz'] ?? false) || $isQuizApproved);

    $moduleTabLocked = $isAdmin ? false : !($schemePermissions['can_module'] ?? false);
    $videoTabLocked = $isAdmin ? false : !($schemePermissions['can_video'] ?? false);
    $quizTabLocked = $isAdmin ? false : !($schemePermissions['can_quiz'] ?? false);
@endphp
@extends($isAdmin ? 'layouts.admin-trainer' : 'layouts.trainer')

@section('title', 'Content Studio - Trainer')

@push($isAdmin ? 'admin-trainer-styles' : 'styles')
<style>
@if($isAdmin)
:root {
    --main-navy-clr: #1a1d78;
    --main-navy-hover: #151761;
    --base-clr: #f1f5fa;
    --yellow-clr: #ffcd00;
    --yellow-hover: #e6b800;
    --white-clr: #ffffff;
    --gray-second-clr: #6c757d;
    --line-clr: #e9ecef;
    --success-clr: #198754;
    --success-bg: #d1e7dd;
    --warning-clr: #ffc107;
    --warning-bg: #fff3cd;

    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
    --spacing-2xl: 48px;
    --spacing-3xl: 64px;

    --shadow-sm: 0 2px 8px rgba(26, 29, 120, 0.05);
    --shadow-md: 0 4px 16px rgba(26, 29, 120, 0.08);
    --shadow-lg: 0 12px 32px rgba(26, 29, 120, 0.12);

    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-2xl: 24px;

    --font-size-xs: 0.85rem;
    --font-size-sm: 0.95rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.15rem;
    --font-size-xl: 1.35rem;
    --font-size-2xl: 1.75rem;
    --font-size-3xl: 2.25rem;
}
@endif

.course-hero {
    background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-2xl);
    padding: var(--spacing-2xl) var(--spacing-3xl);
    color: var(--white-clr);
    box-shadow: 0 20px 40px rgba(26, 29, 120, 0.25);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
    border: 1px solid rgba(255,255,255,0.1);
}

.course-hero::before {
    content: '';
    position: absolute;
    top: -50%; left: -20%;
    width: 150%; height: 150%;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
    pointer-events: none;
}
.hero-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-3xl);
}

.hero-back {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-md);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: var(--white-clr);
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-xs);
    font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
    transition: 0.3s;
}

.hero-back:hover {
    background: rgba(255, 255, 255, 0.15);
}

.hero-badges {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

/* Badge Accent */
.hero-pill-accent {
    background: var(--main-navy-clr, #1a1d78);
    color: var(--white-clr);
    padding: var(--spacing-sm) var(--spacing-lg);
    border-radius: 999px;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* Badge ID di pojok kanan */
.hero-pill-outline {
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.05);
    color: var(--white-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: 999px;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.5px;
}

.hero-body {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
    gap: var(--spacing-2xl);
    align-items: center;
}

.hero-copy h1 {
    margin: 0 0 var(--spacing-md) 0;
    font-size: var(--font-size-3xl);
    font-weight: 800;
    color: var(--white-clr);
}

.hero-copy h1 span {
    color: var(--white-clr);
    font-style: italic;
}

.hero-kicker {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-xl);
    margin: 0 0 var(--spacing-md) 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.8);
}

.kicker-icon {
    color: var(--white-clr);
    margin-right: var(--spacing-sm);
}

.hero-stats {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.stat-chip {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(16px);
    border-radius: 16px;
    padding: 12px 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}
.stat-chip:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    border-color: rgba(255,255,255,0.2);
}

.stat-chip > i {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    flex-shrink: 0;
    -webkit-text-fill-color: transparent;
    -webkit-text-stroke: 1.2px var(--white-clr);
}

.stat-chip:nth-child(3) > i {
    -webkit-text-stroke: 1.2px var(--white-clr);
}

.stat-chip > div {
    display: flex;
    flex-direction: column;
}

.stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: transparent;
    color: #f5c542;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.stat-chip:nth-child(1) .stat-icon {
    background: rgba(27, 23, 99, 0.15);
    color: var(--main-navy-clr);
}


.stat-chip:nth-child(2) .stat-icon {
    background: rgba(255, 165, 0, 0.25);
    color: #ffa500;
}

.stat-chip:nth-child(3) .stat-icon {
    background: rgba(245, 197, 66, 0.25);
    color: #f5c542;
}

.stat-label {
    margin: 0;
    font-size: var(--font-size-xs);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 700;
}

.stat-value {
    margin: 2px 0 0 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--white-clr);
}

.hero-image-wrap {
    background: rgba(255, 255, 255, 0.12);
    padding: var(--spacing-md);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-image-wrap img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: var(--radius-xl);
    display: block;
}

.course-tabs {
    margin: var(--spacing-lg) 0 var(--spacing-xl);
    background: #ffffff;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border-radius: 99px;
    padding: 8px;
    display: flex;
    gap: 8px;
}

.tab-pill {
    flex: 1;
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: 99px;
    border: 1px solid transparent;
    background: transparent;
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--gray-second-clr);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tab-pill i {
    font-size: var(--font-size-base);
}

.tab-pill:hover {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    border: 1px solid transparent;
    transition: all 0.2s ease;
}

.tab-pill:hover i {
    color: var(--white-clr);
}

.tab-pill.active {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    box-shadow: 0 4px 15px rgba(26, 29, 120, 0.2);
}

.tab-pill.active i {
    color: var(--white-clr);
}

.unit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.unit-header p {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.8px;
    color: var(--gray-second-clr);
    text-transform: uppercase;
}

.btn-propose {
    border: 1px solid var(--line-clr);
    background: transparent;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--main-navy-clr);
    cursor: pointer;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s;
}

.btn-propose:hover {
    background: var(--base-clr);
}

.btn-propose i {
    font-size: var(--font-size-sm);
    font-weight: bold;
}

.unit-card {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid rgba(27, 23, 99, 0.06);
    border-radius: 24px;
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.unit-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0; width: 4px;
    background: linear-gradient(to bottom, var(--main-navy-clr), #4f46e5);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.unit-card:hover {
    box-shadow: 0 20px 40px rgba(27, 23, 99, 0.08);
    transform: translateY(-4px);
    border-color: rgba(27, 23, 99, 0.12);
}
.unit-card:hover::before {
    opacity: 1;
}

.unit-card:not(.compact).collapsed {
    border: 1px solid var(--line-clr);
    box-shadow: none;
}

.unit-card:not(.compact).collapsed:hover {
    border-color: #dce4f0;
}

.unit-card.compact {
    border: 1px solid var(--line-clr);
    box-shadow: none;
    opacity: 1;
    padding: var(--spacing-lg);
    transition: all 0.2s;
}

.unit-card.compact:hover {
    border-color: #dce4f0;
}

.unit-card.compact.expanded {
    border: 2px solid var(--main-navy-clr);
    box-shadow: var(--shadow-lg);
}

.unit-top {
    display: flex;
    align-items: center;
    gap: 14px;
}

.unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Collapsed state untuk first card (non-compact) */
.unit-card:not(.compact).collapsed .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

.unit-card:not(.compact).collapsed:hover .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Default style untuk compact cards (Module 2-4) */
.unit-card.compact .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Hover style untuk compact cards */
.unit-card.compact:hover .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Expanded style untuk compact cards */
.unit-card.compact.expanded .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

.unit-index.muted {
    background: var(--line-clr);
    color: var(--gray-second-clr);
}

.unit-card.compact .unit-index.muted {
    background: #e8eff7;
    color: var(--gray-light);
}

.unit-card.compact.expanded .unit-index.muted {
    background: var(--main-navy-clr);
    color: var(--white-clr);
}

.unit-title h3 {
    margin: 0;
    font-size: var(--font-size-base);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.unit-meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xs);
    font-size: var(--font-size-xs);
    color: var(--gray-second-clr);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.4px;
}

.unit-meta i {
    font-size: var(--font-size-xs);
    margin-right: 2px;
}

.unit-status {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--success-clr);
    font-weight: 600;
}

.unit-status i {
    font-size: var(--font-size-xs);
}

.unit-toggle {
    margin-left: auto;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    border: 1px solid rgba(27, 23, 99, 0.1);
    background: #ffffff;
    color: var(--main-navy-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.unit-toggle:hover {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    border-color: transparent;
    box-shadow: 0 6px 15px rgba(27, 23, 99, 0.2);
    transform: translateY(-2px);
}
.unit-toggle i {
    font-size: var(--font-size-lg);
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    transform: rotate(180deg);
}

.unit-card.collapsed .unit-toggle i {
    transform: rotate(0deg);
}

.unit-card.compact .unit-toggle i {
    transform: rotate(0deg);
}

.unit-assets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.unit-card.collapsed .unit-assets {
    display: none;
}

.asset-mini {
    position: relative;
    border: 1px solid rgba(27, 23, 99, 0.08);
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 16px;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}
.asset-mini:hover {
    background: #ffffff;
    border-color: rgba(27, 23, 99, 0.2);
    box-shadow: 0 10px 30px rgba(27, 23, 99, 0.08);
    transform: translateY(-3px);
}
.asset-mini:hover {
    background: #ffffff;
    border-color: rgba(27, 23, 99, 0.2);
    box-shadow: 0 10px 30px rgba(27, 23, 99, 0.08);
    transform: translateY(-3px);
}

.asset-mini i {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-lg);
    background: rgba(27, 23, 99, 0.08);
    color: var(--main-navy-clr);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-lg);
    flex-shrink: 0;
}

.asset-mini:nth-child(2) i {
    background: var(--warning-bg);
    color: #e5a91e;
}

.asset-mini:nth-child(3) i {
    background: var(--success-bg);
    color: var(--success-clr);
}

.asset-mini h4 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.asset-mini p {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.course-right {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.grading-card {
    background: linear-gradient(135deg, var(--navy-gradient-alt), #1b144c);
    border-radius: 18px;
    padding: 18px;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.grading-head {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    font-weight: 700;
}

.bolt {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.grading-status {
    background: rgba(255, 255, 255, 0.08);
    padding: var(--spacing-md);
    border-radius: var(--radius-xl);
}

.grading-status p {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: var(--font-size-xs);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: rgba(255, 255, 255, 0.7);
}

.grading-status h4 {
    margin: 0;
    font-size: var(--font-size-base);
    font-weight: 700;
}

.grading-notes {
    margin: 0;
    padding-left: var(--spacing-lg);
    font-size: var(--font-size-xs);
    color: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.grading-btn {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: var(--white-clr);
    padding: var(--spacing-md);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    cursor: pointer;
}

.instructor-card {
    background: var(--white-clr);
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.instructor-title {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    color: var(--gray-second-clr);
    letter-spacing: 0.6px;
    text-transform: uppercase;
}

.instructor-item {
    display: flex;
    gap: var(--spacing-md);
    align-items: center;
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-xl);
    padding: var(--spacing-md);
}

.instructor-item .dot {
    width: 28px;
    height: 28px;
    border-radius: var(--radius-lg);
    background: var(--base-clr);
}

.instructor-item h4 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.instructor-item p {
    margin: var(--spacing-xs) 0 0 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* Tab Content Management */
.tab-content {
    display: none;
    flex-direction: column;
    gap: var(--spacing-lg);
    width: 100%;
}

.tab-content.active {
    display: flex;
}

/* Quiz Recap Styles */
.recap-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-sm);
}

.stat-box {
    background: var(--white-clr);
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-xl);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.stat-box-label {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.8px;
    color: var(--gray-second-clr);
    text-transform: uppercase;
}

.stat-box-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-box-content h2 {
    margin: 0;
    font-size: var(--font-size-6xl);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.stat-box-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-lg);
}

.stat-box-icon.green {
    background: var(--success-bg);
    color: var(--success-clr);
}

.stat-box-icon.purple {
    background: rgba(27, 23, 99, 0.08);
    color: var(--main-navy-clr);
}

.stat-box-sub {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--success-clr);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-box:nth-child(2) .stat-box-sub {
    color: var(--main-navy-clr);
}

.stat-box-sub i {
    font-size: var(--font-size-xs);
}

.grading-registry {
    background: var(--white-clr);
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-lg);
}

.registry-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-lg);
}

.registry-header h3 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 900;
    color: var(--main-navy-clr);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.export-btn {
    border: 1px solid var(--line-clr);
    background: transparent;
    font-size: var(--font-size-xs);
    font-weight: 700;
    color: var(--main-navy-clr);
    cursor: pointer;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s;
}

.export-btn:hover {
    background: var(--base-clr);
}

.export-btn i {
    font-size: var(--font-size-base);
}

.registry-table {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.table-header {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-md) var(--spacing-lg);
    background: var(--base-clr);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-sm);
    font-size: var(--font-size-xs);
    font-weight: 800;
    color: var(--gray-second-clr);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}




.col-learner {
    display: flex;
    align-items: center;
    gap: 12px;
}

.col-learner img {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-xl);
    object-fit: cover;
    flex-shrink: 0;
}

.col-learner h4 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.col-learner p {
    margin: 2px 0 0 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
}

.col-submission p {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.col-submission span {
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
}

.col-score {
    display: flex;
    align-items: center;
    gap: 8px;
}

.score-bullet {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.score-bullet.green {
    background: var(--success-clr);
}

.score-bullet.orange {
    background: #f5a623;
}

.col-score strong {
    font-size: var(--font-size-sm);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.col-certificate {
    display: flex;
    justify-content: flex-start;
}

.badge-issued {
    background: var(--success-bg);
    color: var(--success-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.badge-issued i {
    font-size: var(--font-size-xs);
}

.badge-pending {
    background: var(--line-clr);
    color: var(--gray-second-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* Enrollment Styles */
.enrollment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-md);
}

.enrollment-header h3 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 900;
    color: var(--main-navy-clr);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.total-badge {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.learner-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
}

.learner-card {
    background: var(--white-clr);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.learner-card:hover {
    border-color: rgba(26, 29, 120, 0.1);
    box-shadow: 0 12px 35px rgba(26, 29, 120, 0.08);
    transform: translateY(-3px);
}

.learner-card.inactive {
    opacity: 0.6;
}

.learner-card img {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-xl);
    object-fit: cover;
    flex-shrink: 0;
}

.learner-info {
    flex: 1;
}

.learner-info h4 {
    margin: 0 0 var(--spacing-xs) 0;
    font-size: var(--font-size-base);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.learner-info p {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--main-navy-clr);
    letter-spacing: 0.3px;
}

.learner-date {
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
}

.inactive-badge {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    background: var(--error-bg);
    color: var(--error-clr);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* Navigation Icons */
.nav-icon {
    color: #e3e3e3;
}

.nav-icon-lg {
    color: #e3e3e3;
    font-size: var(--font-size-3xl);
}

/* Hero Back Icon */
.back-icon {
    font-size: var(--font-size-base);
}

/* Grading Icon */
.grading-icon {
    color: var(--white-clr);
    font-size: var(--font-size-2xl);
}

@media (max-width: 1024px) {
    .hero-body {
        grid-template-columns: 1fr;
    }

    .course-layout {
        grid-template-columns: 1fr;
    }

    .unit-assets {
        grid-template-columns: 1fr;
    }

    .recap-stats {
        grid-template-columns: 1fr;
    }

    .learner-grid {
        grid-template-columns: 1fr;
    }

    .table-header,
    .table-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

}

@media (max-width: 720px) {
    .course-tabs {
    margin: var(--spacing-lg) 0 var(--spacing-xl);
    background: #ffffff;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border-radius: 99px;
    padding: 8px;
    display: flex;
    gap: 8px;
}

    .table-header {
        display: none;
    }

    .table-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}


    .col-learner,
    .col-submission,
    .col-score,
    .col-certificate {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
}

main.detail-course {
    padding: 0;
    background-color: var(--base-clr);
    overflow-y: auto;
    max-width: none;
    margin: 0;
    width: 100%;
}

.course-hero {
    background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-2xl);
    padding: var(--spacing-2xl) var(--spacing-3xl);
    color: var(--white-clr);
    box-shadow: 0 20px 40px rgba(26, 29, 120, 0.25);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
    border: 1px solid rgba(255,255,255,0.1);
}

.course-hero::before {
    content: '';
    position: absolute;
    top: -50%; left: -20%;
    width: 150%; height: 150%;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
    pointer-events: none;
}
.hero-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-3xl);
}

.hero-back {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-md);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: var(--white-clr);
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-xs);
    font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
    transition: 0.3s;
}

.hero-back:hover {
    background: rgba(255, 255, 255, 0.15);
}

.hero-badges {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

/* Badge Accent */
.hero-pill-accent {
    background: var(--main-navy-clr, #1a1d78);
    color: var(--white-clr);
    padding: var(--spacing-sm) var(--spacing-lg);
    border-radius: 999px;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* Badge ID di pojok kanan */
.hero-pill-outline {
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.05);
    color: var(--white-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: 999px;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.5px;
}

.hero-body {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
    gap: var(--spacing-2xl);
    align-items: center;
}

.hero-copy h1 {
    margin: 0 0 var(--spacing-md) 0;
    font-size: var(--font-size-3xl);
    font-weight: 800;
    color: var(--white-clr);
}

.hero-copy h1 span {
    color: var(--white-clr);
    font-style: italic;
}

.hero-kicker {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-xl);
    margin: 0 0 var(--spacing-md) 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.8);
}

.kicker-icon {
    color: var(--white-clr);
    margin-right: var(--spacing-sm);
}

.hero-stats {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.stat-chip {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(16px);
    border-radius: 16px;
    padding: 12px 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}
.stat-chip:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    border-color: rgba(255,255,255,0.2);
}

.stat-chip > i {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    flex-shrink: 0;
    -webkit-text-fill-color: transparent;
    -webkit-text-stroke: 1.2px var(--white-clr);
}

.stat-chip:nth-child(3) > i {
    -webkit-text-stroke: 1.2px var(--white-clr);
}

.stat-chip > div {
    display: flex;
    flex-direction: column;
}

.stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: transparent;
    color: #f5c542;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.stat-chip:nth-child(1) .stat-icon {
    background: rgba(27, 23, 99, 0.15);
    color: var(--main-navy-clr);
}

.stat-chip:nth-child(2) .stat-icon {
    background: rgba(255, 165, 0, 0.25);
    color: #ffa500;
}

.stat-chip:nth-child(3) .stat-icon {
    background: rgba(245, 197, 66, 0.25);
    color: #f5c542;
}

.stat-label {
    margin: 0;
    font-size: var(--font-size-xs);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 700;
}

.stat-value {
    margin: 2px 0 0 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--white-clr);
}

.hero-image-wrap {
    background: rgba(255, 255, 255, 0.12);
    padding: var(--spacing-md);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-image-wrap img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: var(--radius-xl);
    display: block;
}

.course-tabs {
    margin: var(--spacing-lg) 0 var(--spacing-xl);
    background: #ffffff;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border-radius: 99px;
    padding: 8px;
    display: flex;
    gap: 8px;
}

.tab-pill {
    flex: 1;
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: 99px;
    border: 1px solid transparent;
    background: transparent;
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--gray-second-clr);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tab-pill i {
    font-size: var(--font-size-base);
}

.tab-pill:hover {
    background: #f8fafc;
    color: var(--main-navy-clr);
}

.tab-pill.active {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    box-shadow: 0 4px 15px rgba(26, 29, 120, 0.2);
}

.tab-pill.active i {
    color: var(--white-clr);
}

.unit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.unit-header p {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.8px;
    color: var(--gray-second-clr);
    text-transform: uppercase;
}

.btn-propose {
    border: 1px solid var(--line-clr);
    background: transparent;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--main-navy-clr);
    cursor: pointer;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s;
}

.btn-propose:hover {
    background: var(--base-clr);
}

.btn-propose i {
    font-size: var(--font-size-sm);
    font-weight: bold;
}

.unit-card {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid rgba(27, 23, 99, 0.06);
    border-radius: 24px;
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.unit-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0; width: 4px;
    background: linear-gradient(to bottom, var(--main-navy-clr), #4f46e5);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.unit-card:hover {
    box-shadow: 0 20px 40px rgba(27, 23, 99, 0.08);
    transform: translateY(-4px);
    border-color: rgba(27, 23, 99, 0.12);
}
.unit-card:hover::before {
    opacity: 1;
}

.unit-card:not(.compact).collapsed {
    border: 1px solid var(--line-clr);
    box-shadow: none;
}

.unit-card:not(.compact).collapsed:hover {
    border-color: #dce4f0;
}

.unit-card.compact {
    border: 1px solid var(--line-clr);
    box-shadow: none;
    opacity: 1;
    padding: var(--spacing-lg);
    transition: all 0.2s;
}

.unit-card.compact:hover {
    border-color: #dce4f0;
}

.unit-card.compact.expanded {
    border: 2px solid var(--main-navy-clr);
    box-shadow: var(--shadow-lg);
}

.unit-top {
    display: flex;
    align-items: center;
    gap: 14px;
}

.unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Collapsed state untuk first card (non-compact) */
.unit-card:not(.compact).collapsed .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

.unit-card:not(.compact).collapsed:hover .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Default style untuk compact cards (Module 2-4) */
.unit-card.compact .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Hover style untuk compact cards */
.unit-card.compact:hover .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

/* Expanded style untuk compact cards */
.unit-card.compact.expanded .unit-index {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--main-navy-clr) 0%, #51376c 100%);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: var(--font-size-xl);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.unit-card:hover .unit-index {
    transform: scale(1.05) rotate(-5deg);
    box-shadow: 0 8px 20px rgba(27, 23, 99, 0.3);
}

.unit-index.muted {
    background: var(--line-clr);
    color: var(--gray-second-clr);
}

.unit-card.compact .unit-index.muted {
    background: #e8eff7;
    color: var(--gray-light);
}

.unit-card.compact.expanded .unit-index.muted {
    background: var(--main-navy-clr);
    color: var(--white-clr);
}

.unit-title h3 {
    margin: 0;
    font-size: var(--font-size-base);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.unit-meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xs);
    font-size: var(--font-size-xs);
    color: var(--gray-second-clr);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.4px;
}

.unit-meta i {
    font-size: var(--font-size-xs);
    margin-right: 2px;
}

.unit-status {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--success-clr);
    font-weight: 600;
}

.unit-status i {
    font-size: var(--font-size-xs);
}

.unit-toggle {
    margin-left: auto;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    border: 1px solid rgba(27, 23, 99, 0.1);
    background: #ffffff;
    color: var(--main-navy-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.unit-toggle:hover {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    border-color: transparent;
    box-shadow: 0 6px 15px rgba(27, 23, 99, 0.2);
    transform: translateY(-2px);
}
.unit-toggle i {
    font-size: var(--font-size-lg);
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    transform: rotate(180deg);
}

.unit-card.collapsed .unit-toggle i {
    transform: rotate(0deg);
}

.unit-card.compact .unit-toggle i {
    transform: rotate(0deg);
}

.unit-assets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.unit-card.collapsed .unit-assets {
    display: none;
}

.asset-mini {
    position: relative;
    border: 1px solid rgba(27, 23, 99, 0.08);
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 16px;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}
.asset-mini:hover {
    background: #ffffff;
    border-color: rgba(27, 23, 99, 0.2);
    box-shadow: 0 10px 30px rgba(27, 23, 99, 0.08);
    transform: translateY(-3px);
}
.asset-mini:hover {
    background: #ffffff;
    border-color: rgba(27, 23, 99, 0.2);
    box-shadow: 0 10px 30px rgba(27, 23, 99, 0.08);
    transform: translateY(-3px);
}

.asset-mini i {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-lg);
    background: rgba(27, 23, 99, 0.08);
    color: var(--main-navy-clr);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-lg);
    flex-shrink: 0;
}

.asset-mini:nth-child(2) i {
    background: var(--warning-bg);
    color: #e5a91e;
}

.asset-mini:nth-child(3) i {
    background: var(--success-bg);
    color: var(--success-clr);
}

.asset-mini h4 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.asset-mini p {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.course-right {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.grading-card {
    background: linear-gradient(135deg, var(--navy-gradient-alt), #1b144c);
    border-radius: 18px;
    padding: 18px;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.grading-head {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    font-weight: 700;
}

.bolt {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.grading-status {
    background: rgba(255, 255, 255, 0.08);
    padding: var(--spacing-md);
    border-radius: var(--radius-xl);
}

.grading-status p {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: var(--font-size-xs);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: rgba(255, 255, 255, 0.7);
}

.grading-status h4 {
    margin: 0;
    font-size: var(--font-size-base);
    font-weight: 700;
}

.grading-notes {
    margin: 0;
    padding-left: var(--spacing-lg);
    font-size: var(--font-size-xs);
    color: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.grading-btn {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: var(--white-clr);
    padding: var(--spacing-md);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    cursor: pointer;
}

.instructor-card {
    background: var(--white-clr);
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.instructor-title {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    color: var(--gray-second-clr);
    letter-spacing: 0.6px;
    text-transform: uppercase;
}

.instructor-item {
    display: flex;
    gap: var(--spacing-md);
    align-items: center;
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-xl);
    padding: var(--spacing-md);
}

.instructor-item .dot {
    width: 28px;
    height: 28px;
    border-radius: var(--radius-lg);
    background: var(--base-clr);
}

.instructor-item h4 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.instructor-item p {
    margin: var(--spacing-xs) 0 0 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* Tab Content Management */
.tab-content {
    display: none;
    flex-direction: column;
    gap: var(--spacing-lg);
    width: 100%;
}

.tab-content.active {
    display: flex;
}

/* Quiz Recap Styles */
.recap-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-sm);
}

.stat-box {
    background: var(--white-clr);
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-xl);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.stat-box-label {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.8px;
    color: var(--gray-second-clr);
    text-transform: uppercase;
}

.stat-box-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-box-content h2 {
    margin: 0;
    font-size: var(--font-size-6xl);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.stat-box-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-lg);
}

.stat-box-icon.green {
    background: var(--success-bg);
    color: var(--success-clr);
}

.stat-box-icon.purple {
    background: rgba(27, 23, 99, 0.08);
    color: var(--main-navy-clr);
}

.stat-box-sub {
    margin: 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--success-clr);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-box:nth-child(2) .stat-box-sub {
    color: var(--main-navy-clr);
}

.stat-box-sub i {
    font-size: var(--font-size-xs);
}

.grading-registry {
    background: var(--white-clr);
    border: 1px solid var(--line-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-lg);
}

.registry-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-lg);
}

.registry-header h3 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 900;
    color: var(--main-navy-clr);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.export-btn {
    border: 1px solid var(--line-clr);
    background: transparent;
    font-size: var(--font-size-xs);
    font-weight: 700;
    color: var(--main-navy-clr);
    cursor: pointer;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s;
}

.export-btn:hover {
    background: var(--base-clr);
}

.export-btn i {
    font-size: var(--font-size-base);
}

.registry-table {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.table-header {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-md) var(--spacing-lg);
    background: var(--base-clr);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-sm);
    font-size: var(--font-size-xs);
    font-weight: 800;
    color: var(--gray-second-clr);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}




.col-learner {
    display: flex;
    align-items: center;
    gap: 12px;
}

.col-learner img {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-xl);
    object-fit: cover;
    flex-shrink: 0;
}

.col-learner h4 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.col-learner p {
    margin: 2px 0 0 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
}

.col-submission p {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.col-submission span {
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
}

.col-score {
    display: flex;
    align-items: center;
    gap: 8px;
}

.score-bullet {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.score-bullet.green {
    background: var(--success-clr);
}

.score-bullet.orange {
    background: #f5a623;
}

.col-score strong {
    font-size: var(--font-size-sm);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.col-certificate {
    display: flex;
    justify-content: flex-start;
}

.badge-issued {
    background: var(--success-bg);
    color: var(--success-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.badge-issued i {
    font-size: var(--font-size-xs);
}

.badge-pending {
    background: var(--line-clr);
    color: var(--gray-second-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* Enrollment Styles */
.enrollment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-md);
}

.enrollment-header h3 {
    margin: 0;
    font-size: var(--font-size-sm);
    font-weight: 900;
    color: var(--main-navy-clr);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.total-badge {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.learner-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
}

.learner-card {
    background: var(--white-clr);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.learner-card:hover {
    border-color: rgba(26, 29, 120, 0.1);
    box-shadow: 0 12px 35px rgba(26, 29, 120, 0.08);
    transform: translateY(-3px);
}

.learner-card.inactive {
    opacity: 0.6;
}

.learner-card img {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-xl);
    object-fit: cover;
    flex-shrink: 0;
}

.learner-info {
    flex: 1;
}

.learner-info h4 {
    margin: 0 0 var(--spacing-xs) 0;
    font-size: var(--font-size-base);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.learner-info p {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--main-navy-clr);
    letter-spacing: 0.3px;
}

.learner-date {
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-second-clr);
}

.inactive-badge {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    background: var(--error-bg);
    color: var(--error-clr);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: var(--font-size-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* Navigation Icons */
.nav-icon {
    color: #e3e3e3;
}

.nav-icon-lg {
    color: #e3e3e3;
    font-size: var(--font-size-3xl);
}

/* Hero Back Icon */
.back-icon {
    font-size: var(--font-size-base);
}

/* Grading Icon */
.grading-icon {
    color: var(--white-clr);
    font-size: var(--font-size-2xl);
}

@media (max-width: 1024px) {
    .hero-body {
        grid-template-columns: 1fr;
    }

    .course-layout {
        grid-template-columns: 1fr;
    }

    .unit-assets {
        grid-template-columns: 1fr;
    }

    .recap-stats {
        grid-template-columns: 1fr;
    }

    .learner-grid {
        grid-template-columns: 1fr;
    }

    .table-header,
    .table-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

}

@media (max-width: 720px) {
    .course-tabs {
    margin: var(--spacing-lg) 0 var(--spacing-xl);
    background: #ffffff;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border-radius: 99px;
    padding: 8px;
    display: flex;
    gap: 8px;
}

    .table-header {
        display: none;
    }

    .table-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}


    .col-learner,
    .col-submission,
    .col-score,
    .col-certificate {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
}


/* Enrollment Tab Styles */
.enrollment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-lg);
}

.enrollment-header h3 {
    margin: 0;
    font-size: var(--font-size-lg);
    font-weight: 800;
    color: var(--main-navy-clr);
}

.total-badge {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.5px;
}

.learner-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.learner-card {
    background: var(--white-clr);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.learner-card:hover {
    border-color: rgba(26, 29, 120, 0.1);
    box-shadow: 0 12px 35px rgba(26, 29, 120, 0.08);
    transform: translateY(-3px);
}

.learner-card img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--line-clr);
}

.learner-info {
    display: flex;
    flex-direction: column;
}

.learner-info h4 {
    margin: 0 0 2px 0;
    font-size: var(--font-size-base);
    font-weight: 700;
    color: var(--main-navy-clr);
}

.learner-info p {
    margin: 0 0 4px 0;
    font-size: var(--font-size-xs);
    color: var(--gray-second-clr);
}

.learner-date {
    font-size: var(--font-size-xs);
    color: #10b981;
    font-weight: 600;
}

</style>
@endpush

@php
    $pageTitle = 'Content Studio';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Courses', 'url' => route('trainer.courses')],
        ['label' => 'Detail', 'url' => route ('trainer.detail-course',  $course->id)],
        ['label' => 'Course Studio']
    ];

    $courseStatus = strtolower(trim((string) ($course->status ?? '')));
    $courseRejectionReason = trim((string) ($course->rejection_reason ?? ''));
    $moduleRejectionNotes = collect($activeUnitModules ?? [])
        ->filter(function ($module) {
            return strtolower(trim((string) ($module->review_status ?? ''))) === 'rejected'
                && trim((string) ($module->review_rejection_reason ?? '')) !== '';
        })
        ->map(function ($module) {
            $title = trim((string) ($module->title ?? 'Modul'));
            $reason = trim((string) ($module->review_rejection_reason ?? ''));
            return $title . ': ' . $reason;
        })
        ->values();

    $showCourseRejectionNotice = $courseStatus === 'rejected'
        || $courseRejectionReason !== ''
        || $moduleRejectionNotes->isNotEmpty();

    $activeSchemeType = (int) ($activeSchemeType ?? 1);
    $courseMaterialLocked = (bool) ($courseMaterialLocked ?? false);
    $courseInvitationStatus = (string) ($courseInvitationStatus ?? '');
    $schemePermissions = $schemePermissions ?? [
        'can_module' => true,
        'can_video' => true,
        'can_quiz' => true,
    ];
    $activeTab = (string) ($activeTab ?? 'module');
    $moduleTargetModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'pdf';
    });
    $videoTargetModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'video';
    });
    $moduleTargetIds = $moduleTargetModules->isNotEmpty()
        ? $moduleTargetModules->pluck('id')->implode(',')
        : collect($activeUnitModules ?? [])->pluck('id')->implode(',');
    $videoTargetIds = $videoTargetModules->isNotEmpty()
        ? $videoTargetModules->pluck('id')->implode(',')
        : collect($activeUnitModules ?? [])->pluck('id')->implode(',');
    $processingModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return filled($module->processing_status ?? null);
    });
    $processingSummary = [
        'total' => $processingModules->count(),
        'assigned' => $processingModules->where('processing_status', 'assigned_to_admin_course')->count(),
        'uploaded' => $processingModules->where('processing_status', 'processed_uploaded')->count(),
        'revision' => $processingModules->where('processing_status', 'revision_requested')->count(),
        'ready' => $processingModules->where('processing_status', 'ready_for_publish')->count(),
    ];
@endphp

@push($isAdmin ? 'admin-trainer-styles' : 'styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" />
    <style>
        main.content-studio-main {
            width: 100%;
            margin: 0;
            padding: 0;
            flex: 1;
        }

        .studio-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-lg);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--line-clr);
            margin-bottom: var(--spacing-lg);
        }

        .studio-title-wrap {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }

        .back-btn {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-xl);
            border: 1px solid #d8dee9;
            color: var(--gray-second-clr);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: var(--white-clr);
        }

        .kicker {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-xs);
            color: #d4a62f;
            letter-spacing: 0.12em;
            font-weight: 600;
        }

        .studio-title-wrap h1 {
            margin: 0;
            font-size: var(--font-size-2xl);
            color: var(--main-navy-clr);
            font-weight: 600;
        }

        .studio-tabs {
            background: #eef2f7;
            border: 1px solid #dce3ee;
            border-radius: var(--radius-2xl);
            padding: var(--spacing-xs);
            display: flex;
            gap: var(--spacing-xs);
        }

        .studio-tab {
            border: none;
            border-radius: var(--radius-xl);
            background: transparent;
            color: var(--gray-second-clr);
            font-size: var(--font-size-xs);
            font-weight: 600;
            padding: var(--spacing-sm) var(--spacing-lg);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .studio-tab.active {
            background: var(--white-clr);
            color: var(--main-navy-clr);
            box-shadow: var(--shadow-md);
        }

        .studio-tab.is-locked {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .revision-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: var(--spacing-lg);
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #fecaca;
            background: #fef2f2;
        }

        .revision-alert .icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #fee2e2;
            color: #b91c1c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .revision-alert .label {
            margin: 0 0 4px 0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b91c1c;
        }

        .revision-alert .reason {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #7f1d1d;
            white-space: pre-line;
        }

        .revision-alert ul {
            margin: 6px 0 0 16px;
            padding: 0;
        }

        .revision-alert li {
            margin: 0 0 4px 0;
            color: #7f1d1d;
            font-size: 13px;
            line-height: 1.45;
        }

        .processing-banner {
            margin-bottom: var(--spacing-lg);
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #d8e3f3;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.04);
        }

        .processing-banner-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .processing-banner-title {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #334155;
        }

        .processing-banner-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .processing-stat {
            border-radius: 12px;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .processing-stat .value {
            display: block;
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.1;
            margin-bottom: 3px;
        }

        .processing-stat .label {
            display: block;
            font-size: 0.72rem;
            color: #64748b;
            line-height: 1.35;
        }

        .video-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
        }

        .video-status-pill.assigned {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .video-status-pill.uploaded {
            background: #ecfeff;
            color: #0f766e;
        }

        .video-status-pill.revision {
            background: #fff7ed;
            color: #9a3412;
        }

        .video-status-pill.ready {
            background: #dcfce7;
            color: #166534;
        }

        .video-status-pill.pending {
            background: #fef3c7;
            color: #92400e;
        }

        @media (max-width: 992px) {
            .processing-banner-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .processing-banner-grid {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: var(--white-clr);
            border: 1px solid #e3e9f2;
            border-radius: 24px;
            padding: var(--spacing-lg);
            display: none;
        }

        .panel.active {
            display: block;
        }

        .text-upload-shell {
            border: 1px solid #d9e3f1;
            border-radius: 18px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.05);
        }

        .text-upload-header {
            padding: 18px 18px 14px;
            border-bottom: 1px solid #e7edf6;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        .text-upload-header h3 {
            margin: 0 0 6px;
            font-size: 18px;
            color: var(--main-navy-clr);
            font-weight: 700;
        }

        .text-upload-header p {
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            color: #64748b;
        }

        .material-outline {
            margin: 12px 0 0;
            padding-left: 18px;
            display: grid;
            gap: 6px;
        }

        .material-outline li {
            font-size: 12px;
            color: #475569;
            line-height: 1.45;
        }

        .text-editor-block {
            padding: 16px 18px;
            border-bottom: 1px solid #e7edf6;
        }

        .text-editor-label {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #64748b;
        }

        .text-editor-input {
            width: 100%;
            min-height: 180px;
            border: 1px solid #cdd8e8;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            line-height: 1.65;
            color: #1e293b;
            background: #fcfdff;
            resize: vertical;
        }

        .text-editor-input:focus {
            outline: none;
            border-color: var(--main-navy-clr);
            box-shadow: 0 0 0 3px rgba(35, 29, 121, 0.12);
            background: #fff;
        }

        .wysiwyg-container {
            border: 1px solid #cdd8e8;
            border-radius: 12px;
            background: #ffffff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.2s ease;
        }

        .wysiwyg-container:focus-within {
            border-color: var(--main-navy-clr);
            box-shadow: 0 0 0 3px rgba(35, 29, 121, 0.12);
        }

        .wysiwyg-toolbar {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafd;
        }

        .wysiwyg-btn {
            border: 1px solid #ccd9ea;
            background: #fff;
            color: #334155;
            border-radius: 8px;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .wysiwyg-btn:hover {
            border-color: var(--main-navy-clr, #2e2050);
            background-color: rgba(46, 32, 80, 0.08); /* Fallback light plum */
            background-color: color-mix(in srgb, var(--main-navy-clr, #2e2050) 8%, transparent);
            color: var(--main-navy-clr, #2e2050);
        }

        .wysiwyg-select {
            border: 1px solid #ccd9ea;
            background: #fff;
            color: #334155;
            border-radius: 8px;
            height: 34px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
        }

        .wysiwyg-select:hover {
            border-color: var(--main-navy-clr);
            color: var(--main-navy-clr);
        }

        .wysiwyg-editor {
            min-height: 350px;
            padding: 16px;
            font-size: 15px;
            line-height: 1.7;
            color: #1e293b;
            background: #fff;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .wysiwyg-editor:focus {
            outline: none;
        }

        .wysiwyg-editor[placeholder].is-editor-empty:before {
            content: attr(placeholder);
            color: #94a3b8;
            font-style: italic;
            pointer-events: none;
            display: block;
        }

        .wysiwyg-editor h1,
        .wysiwyg-editor h2,
        .wysiwyg-editor h3 {
            color: var(--main-navy-clr);
            margin: 12px 0 6px;
            line-height: 1.35;
        }

        .wysiwyg-editor h1 {
            font-size: 24px;
        }

        .wysiwyg-editor h2 {
            font-size: 20px;
        }

        .wysiwyg-editor h3 {
            font-size: 18px;
        }

        .wysiwyg-editor ul {
            margin: 0 0 10px 20px;
            padding-left: 20px;
            list-style-type: disc;
        }

        .wysiwyg-editor ol {
            margin: 0 0 10px 20px;
            padding-left: 20px;
            list-style-type: decimal;
        }

        .wysiwyg-editor img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 12px 0;
            border-radius: 12px;
        }

        .wysiwyg-editor .module-inline-image {
            margin: 12px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .wysiwyg-editor .module-inline-image img {
            max-width: 560px;
            width: 100%;
            max-height: 360px;
            object-fit: contain;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .wysiwyg-editor .module-inline-image[data-image-align="left"] {
            justify-content: flex-start;
        }

        .wysiwyg-editor .module-inline-image[data-image-align="center"] {
            justify-content: center;
        }

        .wysiwyg-editor .module-inline-image[data-image-align="right"] {
            justify-content: flex-end;
        }

        .wysiwyg-editor .module-inline-image.is-selected img {
            outline: 3px solid rgba(35, 29, 121, 0.22);
            outline-offset: 3px;
        }

        .module-code-block {
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #f8fafc;
            margin: 10px 0;
            overflow: hidden;
        }

        .module-code-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-bottom: 1px solid #d7deea;
            background: #eef2f7;
        }

        .module-code-lang {
            border: 1px solid #c7d1e2;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 12px;
            color: #334155;
            background: #fff;
        }

        .module-code-copy {
            border: 1px solid #c7d1e2;
            border-radius: 6px;
            background: #fff;
            color: #334155;
            font-size: 12px;
            padding: 4px 8px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .module-code-copy:hover,
        .module-preview-article .module-code-copy:hover {
            background: var(--main-navy-clr, #2e2050) !important;
            color: #ffffff !important;
            border-color: var(--main-navy-clr, #2e2050) !important;
        }

        .module-code-delete {
            border: 1px solid #f87171;
            border-radius: 6px;
            background: #fff;
            color: #ef4444;
            font-size: 12px;
            padding: 4px 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .module-code-delete:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
        }

        .module-code-block pre {
            margin: 0;
            padding: 12px 14px;
            background: #0f172a;
            color: #e2e8f0;
            overflow-x: auto;
            font-family: Consolas, Monaco, 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .module-code-block code[contenteditable="true"] {
            display: block;
            min-height: 36px;
            white-space: pre;
            outline: none;
        }

        .text-editor-note {
            margin: 8px 0 0;
            font-size: 12px;
            color: #64748b;
        }

        .dropzone {
            margin: 16px 18px 18px;
            min-height: 124px;
            border-radius: 14px;
            border: 2px dashed #cfd9e8;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.25s ease;
            padding: 18px;
        }

        .dropzone:hover {
            border-color: var(--main-navy-clr);
            background: #f3f7fd;
        }

        .dropzone i {
            font-size: 28px;
            color: var(--main-navy-clr);
        }

        .dropzone h2 {
            margin: 0;
            font-size: 16px;
            color: var(--main-navy-clr);
            font-weight: 700;
        }

        .dropzone p {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.02em;
            color: #64748b;
            font-weight: 500;
        }

        .panel-footer {
            margin-top: var(--spacing-lg);
            padding-top: var(--spacing-md);
            border-top: 1px solid var(--line-clr);
            display: flex;
            justify-content: flex-end;
            gap: 5px;
        }

        .primary-btn {
            border: none;
            background: #3f2a54;
            color: var(--white-clr);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: 12px;
            font-size: var(--font-size-sm);
            font-weight: 600;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .primary-btn:hover {
            background: #4c3366 !important;
            color: var(--white-clr) !important;
        }

        .primary-btn i {
            color: var(--white-clr);
        }

        .secondary-btn {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: 12px;
            font-size: var(--font-size-sm);
            font-weight: 600;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
        }

        .secondary-btn:hover {
            border-color: var(--main-navy-clr);
            color: var(--main-navy-clr);
            background: #f8fafc;
        }

        .validation-card {
            background: #3f2a54;
            color: var(--white-clr);
            border-radius: 32px;
            padding: var(--spacing-lg);
        }

        .validation-card h3 {
            margin: 0 0 var(--spacing-md) 0;
            color: var(--white-clr);
            font-size: var(--font-size-md);
            font-weight: 600;
        }

        .validation-card ol {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .validation-card li {
            display: grid;
            grid-template-columns: 32px 1fr;
            gap: var(--spacing-sm);
        }

        .validation-card li span {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white-clr);
            font-weight: 600;
            font-size: var(--font-size-sm);
        }

        .validation-card h4 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-sm);
            font-weight: 600;
        }

        .validation-card p {
            margin: 0;
            color: #b8bce2;
            font-size: var(--font-size-xs);
        }

        .quiz-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .meta-box {
            border: 1px solid #dde5f1;
            border-radius: var(--radius-xl);
            padding: var(--spacing-sm);
            background: #f8fafc;
        }

        .meta-box {
            cursor: default;
            transition: all 0.2s ease;
        }

        #passingGradeBox {
            cursor: pointer;
        }

        #passingGradeBox:hover {
            opacity: 0.9;
        }

        .meta-box p {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-xs);
            color: var(--gray-second-clr);
            font-weight: 600;
            letter-spacing: 0.08em;
        }

        .meta-value {
            background: #eff3f8;
            border: 1px solid #dbe3ef;
            border-radius: var(--radius-lg);
            padding: var(--spacing-sm);
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 4px;
        }

        .meta-value strong {
            color: var(--main-navy-clr);
            font-size: var(--font-size-lg);
            font-weight: 600;
            min-width: 50px;
            text-align: center;
            display: inline-block;
        }

        .meta-value span {
            color: #b0bdd2;
            font-size: var(--font-size-xl);
            font-weight: 600;
        }

        .meta-value em {
            font-style: normal;
            color: var(--white-clr);
            background: #2c237f;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            margin-left: auto;
        }

        .meta-input {
            border: none !important;
            background: transparent !important;
            font-weight: 600;
            color: var(--main-navy-clr);
            width: 50px;
            text-align: center;
            display: none;
            outline: none;
            padding: 0;
            margin: 0;
            font-size: var(--font-size-lg);
            box-shadow: none !important;
        }

        .meta-input:focus {
            outline: none;
            box-shadow: none;
            border: none;
        }

        .quiz-editor {
            border: 1px solid #e4eaf4;
            border-radius: 24px;
            padding: var(--spacing-lg);
            background: var(--white-clr);
        }

        .q-head {
            display: grid;
            grid-template-columns: max-content minmax(0, 1fr) 100px auto;
            gap: var(--spacing-md);
            align-items: stretch;
            margin-bottom: var(--spacing-md);
        }

        .q-number {
            width: auto;
            height: 100%;
            aspect-ratio: 1 / 1;
            min-width: 40px;
            border-radius: 12px;
            background: #3f2a54;
            color: var(--white-clr);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-xl);
            font-weight: 600;
            align-self: stretch;
        }

        .q-inputs label,
        .q-score label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-size: var(--font-size-xs);
            color: var(--gray-second-clr);
            font-weight: 600;
            letter-spacing: 0.08em;
        }

        .q-inputs input,
        .q-score input {
            width: 100%;
            border: 1px solid #d9e1ee;
            border-radius: var(--radius-xl);
            background: #f3f6fb;
            padding: var(--spacing-sm);
            font-size: var(--font-size-base);
            color: var(--main-navy-clr);
            outline: none;
        }

        .q-inputs input:focus,
        .q-score input:focus {
            border-color: var(--main-navy-clr);
            background: #fff;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--spacing-md);
        }

        .option-btn {
            border: 1px solid #e1e8f3;
            background: #f8fafc;
            border-radius: var(--radius-xl);
            padding: var(--spacing-sm);
            min-height: 48px;
            height: 48px;
            text-align: left;
            font-size: var(--font-size-sm);
            color: #b8c2d3;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            justify-content: flex-start;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .option-btn:hover {
            background: #eff3f8;
            border-color: var(--main-navy-clr);
        }

        .option-btn.is-correct {
            border: 2px solid #14b87a;
            background: #e8faf2;
            color: #1da775;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-weight: 600;
        }

        .delete-question {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            height: fit-content;
            margin-top: 22px;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .delete-question:hover {
            background: #e25555;
        }

        .option-container {
            display: flex;
            gap: var(--spacing-sm);
            align-items: stretch;
        }

        .option-input {
            border: 1px solid #d9e1ee;
            border-radius: 8px;
            padding: var(--spacing-sm);
            min-height: 48px;
            height: 48px;
            font-size: 13px;
            background: #f8fafc;
            flex: 1;
            min-width: 0;
            outline: none;
            transition: all 0.2s ease;
        }

        .option-input:focus {
            border-color: var(--main-navy-clr);
            background: #fff;
        }

        .options-section {
            margin-top: var(--spacing-md);
        }

        .options-label {
            margin: 0 0 var(--spacing-sm) 0;
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-second-clr);
            letter-spacing: 0.08em;
        }

        @media (max-width: 1024px) {
            main.content-studio-main {
                padding: var(--spacing-lg);
            }

            .options-grid {
                grid-template-columns: 1fr;
            }

            .q-head {
                grid-template-columns: 40px minmax(0, 1fr);
            }

            .delete-question {
                grid-column: 1 / -1;
                margin-top: var(--spacing-sm);
                justify-self: start;
            }
        }

        @media (max-width: 720px) {
            main.content-studio-main {
                padding: var(--spacing-md);
            }

            .studio-tabs {
                width: 100%;
            }

            .studio-tab {
                flex: 1;
                padding: var(--spacing-sm) var(--spacing-sm);
            }

            .quiz-meta,
            .options-grid {
                grid-template-columns: 1fr;
            }

            .q-head {
                grid-template-columns: 1fr;
                gap: var(--spacing-sm);
            }

            .q-number {
                width: 32px;
                height: 32px;
                font-size: var(--font-size-lg);
            }

            .q-score {
                grid-row: 2;
                grid-column: 1;
            }

            .delete-question {
                margin-top: 0;
            }
        }

        .module-form,
        .quiz-form {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .file-list h3 {
            font-size: var(--font-size-md);
            font-weight: 600;
            color: var(--main-navy-clr);
            margin: 0 0 var(--spacing-md) 0;
        }

        .quiz-actions {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-md);
            justify-content: flex-start;
        }

        .quiz-actions .primary-btn {
            border-radius: 999px;
            border: none;
            padding: var(--spacing-md) var(--spacing-2xl);
            font-weight: 700;
            font-size: var(--font-size-xs);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            transition: all 0.2s ease;
        }

        .quiz-add-btn {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #dbe3ef;
        }

        .quiz-add-btn i {
            color: #94a3b8;
        }

        .quiz-add-btn:hover {
            background: #f1f5f9;
            color: var(--main-navy-clr);
            border-color: #cbd5e1;
        }

        .quiz-add-btn:hover i {
            color: var(--main-navy-clr);
        }

        .quiz-save-btn {
            background: #1f1b5a;
            color: var(--white-clr);
            box-shadow: 0 10px 20px rgba(31, 27, 90, 0.2);
        }

        .quiz-save-btn i {
            color: var(--white-clr);
        }

        .quiz-save-btn:hover {
            background: #19164a;
        }

        @media (max-width: 768px) {
            .quiz-actions {
                flex-direction: column-reverse;
            }

            .quiz-actions .primary-btn {
                width: 100%;
                justify-content: center;
            }
            
            .q-head {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-md);
                padding: var(--spacing-md);
            }

            .q-inputs, .q-score {
                width: 100%;
            }

            .delete-question {
                width: 100%;
                justify-content: center;
                margin-top: var(--spacing-sm);
            }

            .quiz-meta {
                grid-template-columns: 1fr;
            }

            .options-grid {
                grid-template-columns: 1fr;
            }
            
            .option-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .option-btn {
                width: 100%;
                justify-content: center;
                margin-bottom: var(--spacing-xs);
            }
        }

        /* Split view body */
        .studio-split-body {
            display: grid;
            grid-template-columns: 1fr;
            background: #ffffff;
        }

        @media (min-width: 992px) {
            .split-active .studio-split-body {
                grid-template-columns: 1fr 1fr;
                align-items: stretch;
                height: 750px;
                border: 1px solid var(--line-clr);
                border-radius: 12px;
                overflow: hidden;
            }
        }

        .studio-editor-pane {
            min-width: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .studio-preview-pane {
            display: none;
            min-width: 0;
            height: 100%;
        }

        @media (min-width: 992px) {
            .split-active .studio-preview-pane {
                display: flex;
                flex-direction: column;
                height: 100%;
                min-height: 0;
                border-left: 1px solid var(--line-clr);
                background: #ffffff;
                padding: 0;
            }
        }

        .preview-header {
            display: none;
        }

        @media (min-width: 992px) {
            .split-active .preview-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                min-height: 55px;
                padding: 10px 16px;
                border-bottom: 1px solid var(--line-clr);
                background: #f8fafc;
                box-sizing: border-box;
                flex-shrink: 0;
            }
        }

        .live-preview-body {
            flex: 1;
            overflow-y: auto;
            background: transparent;
            padding: 0;
        }

        @media (min-width: 992px) {
            .split-active .live-preview-body {
                padding: 16px !important;
                background: #ffffff;
                min-height: 0;
            }
        }

        /* Beautiful Modern Scrollbars */
        .wysiwyg-editor,
        .live-preview-body,
        #modulePreviewBody {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 rgba(0, 0, 0, 0.02);
        }

        .wysiwyg-editor::-webkit-scrollbar,
        .live-preview-body::-webkit-scrollbar,
        #modulePreviewBody::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .wysiwyg-editor::-webkit-scrollbar-track,
        .live-preview-body::-webkit-scrollbar-track,
        #modulePreviewBody::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
            border-radius: 10px;
        }

        .wysiwyg-editor::-webkit-scrollbar-thumb,
        .live-preview-body::-webkit-scrollbar-thumb,
        #modulePreviewBody::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            transition: background 0.2s ease;
        }

        .wysiwyg-editor::-webkit-scrollbar-thumb:hover,
        .live-preview-body::-webkit-scrollbar-thumb:hover,
        #modulePreviewBody::-webkit-scrollbar-thumb:hover {
            background: var(--main-navy-clr, #1a1d78);
        }

        .module-code-block pre {
            scrollbar-width: thin;
            scrollbar-color: #475569 rgba(0, 0, 0, 0.02);
        }

        .module-code-block pre::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .module-code-block pre::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
        }

        .module-code-block pre::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
            transition: background 0.2s ease;
        }

        .module-code-block pre::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Set up the editor pane to fill height and be borderless when split is active */
        @media (min-width: 992px) {
            .split-active .studio-editor-pane {
                padding: 0;
                background: #ffffff;
                height: 100%;
                min-height: 0;
            }
            .split-active .wysiwyg-container {
                border: none;
                border-radius: 0;
                box-shadow: none;
                height: 100%;
                min-height: 0;
            }
            .split-active .wysiwyg-container:focus-within {
                border: none;
                box-shadow: none;
            }
            .split-active .wysiwyg-toolbar {
                border-radius: 0;
                border: none;
                border-bottom: 1px solid var(--line-clr);
                background: #f8fafc;
                padding: 10px 16px;
                min-height: 55px;
                height: auto;
                flex-wrap: wrap;
                box-sizing: border-box;
                display: flex;
                align-items: center;
            }
            .split-active .wysiwyg-editor {
                flex: 1;
                overflow-y: auto;
                min-height: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                padding: 16px !important;
            }
            /* Hide the redundant labels and helper notes inside editor pane in split mode */
            .split-active .studio-editor-pane > .text-editor-label,
            .split-active .studio-editor-pane > .text-editor-note {
                display: none !important;
            }
        }
        
        .module-preview-article {
            color: var(--main-text-clr, #1e293b);
            font-size: var(--font-size-base, 14px);
            line-height: var(--line-height-normal, 1.65);
        }
        
        .module-preview-article h1,
        .module-preview-article h2,
        .module-preview-article h3 {
            color: var(--main-navy-clr);
            margin: 1.5em 0 0.8em;
            font-weight: 700;
        }
        
        .module-preview-article h1:first-child,
        .module-preview-article h2:first-child,
        .module-preview-article h3:first-child {
            margin-top: 0;
        }
        
        .module-preview-article h1 { font-size: 1.55rem; border-bottom: 1px solid var(--line-clr); padding-bottom: 8px; }
        .module-preview-article h2 { font-size: 1.35rem; }
        .module-preview-article h3 { font-size: 1.15rem; }
        
        .module-preview-article p {
            margin: 0 0 1em;
        }
        
        .module-preview-article ul {
            margin: 0 0 1em 20px;
            padding-left: 20px;
            list-style-type: disc;
        }
        
        .module-preview-article ol {
            margin: 0 0 1em 20px;
            padding-left: 20px;
            list-style-type: decimal;
        }
        
        .module-preview-article li {
            margin-bottom: 0.5em;
        }
        
        .module-preview-article img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 16px auto;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }
        
        .module-preview-article .module-inline-image {
            margin: 16px 0;
            display: flex;
        }
        
        .module-preview-article .module-inline-image img {
            max-width: 100%;
            max-height: 360px;
            object-fit: contain;
        }
        
        .module-preview-article .module-inline-image[data-image-align="left"] {
            justify-content: flex-start;
        }
        
        .module-preview-article .module-inline-image[data-image-align="center"] {
            justify-content: center;
        }
        
        .module-preview-article .module-inline-image[data-image-align="right"] {
            justify-content: flex-end;
        }
        
        .btn-propose.split-active {
            background: var(--main-navy-clr) !important;
            color: #fff !important;
            border-color: var(--main-navy-clr) !important;
        }
        
        .spin-icon {
            display: inline-block;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@section($isAdmin ? 'admin-trainer-content' : 'content')
    <main class="content-studio-main">
        <header class="studio-header">
            <div class="studio-title-wrap">
                <a class="back-btn" href="{{ $isAdmin ? route('admin.trainer.studio.list') : route('trainer.detail-course', $course->id) }}">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <p class="kicker">STUDIO NARASUMBER  PENYUSUNAN MATERI</p>
                    <h1>{{ $course->name }}</h1>
                </div>
            </div>

            <div class="studio-tabs" role="tablist">
                <a
                    href="{{ $moduleTabLocked ? 'javascript:void(0)' : request()->fullUrlWithQuery(['tab' => 'module']) }}"
                    class="studio-tab {{ $activeTab === 'module' ? 'active' : '' }} {{ $moduleTabLocked ? 'is-locked' : '' }}"
                    data-tab="module" {{ $moduleTabLocked ? 'data-locked="1"' : '' }}>
                    MODUL
                    @if($moduleTabLocked)
                        <i class="bi bi-lock-fill ms-1 text-warning" style="font-size: 0.9rem;"></i>
                    @endif
                </a>
                <a
                    href="{{ $videoTabLocked ? 'javascript:void(0)' : request()->fullUrlWithQuery(['tab' => 'video']) }}"
                    class="studio-tab {{ $activeTab === 'video' ? 'active' : '' }} {{ $videoTabLocked ? 'is-locked' : '' }}"
                    data-tab="video" {{ $videoTabLocked ? 'data-locked="1"' : '' }}>
                    VIDEO
                    @if($videoTabLocked)
                        <i class="bi bi-lock-fill ms-1 text-warning" style="font-size: 0.9rem;"></i>
                    @endif
                </a>
                <a
                    href="{{ $quizTabLocked ? 'javascript:void(0)' : request()->fullUrlWithQuery(['tab' => 'quiz']) }}"
                    class="studio-tab {{ $activeTab === 'quiz' ? 'active' : '' }} {{ $quizTabLocked ? 'is-locked' : '' }}"
                    data-tab="quiz" {{ $quizTabLocked ? 'data-locked="1"' : '' }}>
                    PENYUSUNAN QUIZ
                    @if($quizTabLocked)
                        <i class="bi bi-lock-fill ms-1 text-warning" style="font-size: 0.9rem;"></i>
                    @endif
                </a>
            </div>
        </header>

        @if(($processingSummary['total'] ?? 0) > 0)
            <section class="processing-banner">
                <div class="processing-banner-head">
                    <p class="processing-banner-title">Processing Snapshot</p>
                    <span class="hero-pill-outline">{{ $processingSummary['total'] }} modul aktif</span>
                </div>
                <div class="processing-banner-grid">
                    <div class="processing-stat">
                        <span class="value">{{ $processingSummary['assigned'] ?? 0 }}</span>
                        <span class="label">Diserahkan ke admin course</span>
                    </div>
                    <div class="processing-stat">
                        <span class="value">{{ $processingSummary['uploaded'] ?? 0 }}</span>
                        <span class="label">Hasil edit diunggah</span>
                    </div>
                    <div class="processing-stat">
                        <span class="value">{{ $processingSummary['revision'] ?? 0 }}</span>
                        <span class="label">Revisi diminta</span>
                    </div>
                    <div class="processing-stat">
                        <span class="value">{{ $processingSummary['ready'] ?? 0 }}</span>
                        <span class="label">Siap dipublikasikan</span>
                    </div>
                </div>
            </section>
        @endif

        @if($courseMaterialLocked)
            <section
                style="margin-bottom:20px; padding: 16px; border: 1px solid #f59e0b; border-radius: 12px; background: #fffbeb; color: #92400e; font-size: 13px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; align-items: start; gap: 12px; flex: 1;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.2rem; color: #d97706; margin-top: 2px;"></i>
                    <div>
                        <strong style="display: block; margin-bottom: 4px;">Materi Course Terkunci</strong>
                        Materi course masih terkunci sampai undangan trainer diterima. Kamu masih bisa melihat detail course ini, tetapi semua upload, penggantian file, dan penyusunan quiz dinonaktifkan sementara.
                    </div>
                </div>
                @if(isset($courseInvitation) && $courseInvitation)
                    <button type="button" class="btn-propose" onclick="openSchemeSelectionModal({{ $courseInvitation->id }}, '{{ addslashes($course->name) }}', 'course')" style="border: 1.5px solid var(--main-navy-clr, #1a1d78); color: #ffffff; font-weight: 700; height: 34px; padding: 0 var(--spacing-md); font-size: 0.72rem; border-radius: 8px; background: var(--main-navy-clr, #1a1d78); border-color: var(--main-navy-clr, #1a1d78); flex-shrink: 0; transition: all 0.2s;" onmouseover="this.style.background='var(--main-navy-hover, #151761)'; this.style.borderColor='var(--main-navy-hover, #151761)';" onmouseout="this.style.background='var(--main-navy-clr, #1a1d78)'; this.style.borderColor='var(--main-navy-clr, #1a1d78)';">
                        <i class="bi bi-check-circle-fill"></i> TERIMA UNDANGAN & PILIH SKEMA
                    </button>
                @endif
            </section>
        @elseif(!$isAdmin && (!$schemePermissions['can_module'] || !$schemePermissions['can_video'] || !$schemePermissions['can_quiz']))
            <section
                style="margin-bottom:16px; padding: 12px 14px; border:1px dashed #cbd5e1; border-radius: 12px; background:#f8fafc; color:#475569; font-size:13px;">
                @if(!$schemePermissions['can_module'])
                    Tab modul dikunci oleh skema aktif.
                @endif
                @if(!$schemePermissions['can_video'])
                    Tab video dikunci oleh skema aktif.
                @endif
                @if(!$schemePermissions['can_quiz'])
                    Tab quiz dikunci oleh skema aktif.
                @endif
            </section>
        @endif

        @if($showCourseRejectionNotice)
            <section class="revision-alert" aria-label="Alasan revisi materi course">
                <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <p class="label">Alasan Revisi dari Admin</p>
                    @if($courseRejectionReason !== '')
                        <p class="reason">{{ $courseRejectionReason }}</p>
                    @endif
                    @if($moduleRejectionNotes->isNotEmpty())
                        <ul>
                            @foreach($moduleRejectionNotes as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        @endif

        <section class="studio-layout">
            <div class="studio-panels">
                <section id="studioSplitWrapper" class="panel panel-module {{ $activeTab === 'module' ? 'active' : '' }}" data-panel="module">
                    @if(!$isAdmin && $isModuleApproved)
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); color: #059669; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-patch-check-fill" style="font-size: 24px;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Materi Telah Disetujui (Terkunci)</strong>
                            <span style="font-size: 14px;">Modul teks ini telah disetujui oleh admin trainer dan tidak dapat diubah lagi.</span>
                        </div>
                    </div>
                    @endif
                    @if(isset($isAdmin) && $isAdmin && !$schemePermissions['can_module'])
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-info-circle-fill" style="font-size: 24px;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Mode Pantau (Read-Only)</strong>
                            <span style="font-size: 14px;">Penyusunan modul pada kelas ini adalah tugas Trainer berdasarkan skema yang dipilih. Anda hanya dapat melihat konten.</span>
                        </div>
                    </div>
                    @endif
                    <form id="moduleForm" class="upload-form"
                        action="{{ $isAdmin ? route('admin.courses.studio.upload', $course->id) : route('trainer.courses.studio.upload', $course->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" name="target_modules" value="{{ $moduleTargetIds }}">
                        <input type="hidden" name="replace_module_id" value="">
                        <input type="hidden" name="module_content_html" id="moduleContentHtml" value="">

                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--spacing-sm); padding-bottom: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--line-clr);">
                            <div>
                                <h3 style="margin: 0; font-size: 16px; color: var(--main-navy-clr); font-weight: 800;">Tulis Materi Seperti Modul Teks</h3>
                                <p style="margin: 4px 0 0; font-size: 12px; color: var(--gray-second-clr);">
                                    Susun penjelasan materi dalam bentuk teks secara terstruktur, lalu sisipkan gambar pendukung.
                                </p>
                            </div>
                            <div style="display: flex; gap: var(--spacing-sm); align-items: center;">
                                <button type="button" id="toggleSplitViewBtn" class="btn-propose">
                                    <i class="bi bi-columns-gap"></i> SPLIT VIEW
                                </button>
                                <button type="button" class="btn-propose" data-bs-toggle="modal" data-bs-target="#styleGuideModal">
                                    <i class="bi bi-journal-text"></i> STYLE GUIDE
                                </button>
                            </div>
                        </div>

                        <!-- Split Pane Body -->
                        <div class="studio-split-body">
                            <!-- Left Column: Editor Pane -->
                            <div class="studio-editor-pane">
                                <p class="text-editor-label mb-2">Editor Materi</p>
                                <div class="wysiwyg-container">
                                    <div class="wysiwyg-toolbar" id="wysiwygToolbar">
                                        <button type="button" class="wysiwyg-btn" data-action="bold" title="Bold" {{ $moduleLocked ? 'disabled' : '' }}><i class="bi bi-type-bold"></i></button>
                                        <button type="button" class="wysiwyg-btn" data-action="italic" title="Italic" {{ $moduleLocked ? 'disabled' : '' }}><i
                                                class="bi bi-type-italic"></i></button>
                                        <button type="button" class="wysiwyg-btn" data-action="h1" title="Heading 1" {{ $moduleLocked ? 'disabled' : '' }}>H1</button>
                                        <button type="button" class="wysiwyg-btn" data-action="h2" title="Heading 2" {{ $moduleLocked ? 'disabled' : '' }}>H2</button>
                                        <button type="button" class="wysiwyg-btn" data-action="h3" title="Heading 3" {{ $moduleLocked ? 'disabled' : '' }}>H3</button>
                                        <button type="button" class="wysiwyg-btn" data-action="ul" title="Bullet List" {{ $moduleLocked ? 'disabled' : '' }}><i class="bi bi-list-ul"></i></button>
                                        <select class="wysiwyg-select" id="listStyleSelect" title="Ubah Gaya List" {{ $moduleLocked ? 'disabled' : '' }} style="margin-left: 2px;">
                                            <option value="" disabled selected>Gaya List</option>
                                            <option value="disc">Bulatan (Disc)</option>
                                            <option value="circle">Lingkaran (Circle)</option>
                                            <option value="square">Kotak (Square)</option>
                                            <option value="decimal">Angka (1, 2, 3)</option>
                                            <option value="lower-alpha">Huruf Kecil (a, b, c)</option>
                                            <option value="upper-alpha">Huruf Besar (A, B, C)</option>
                                            <option value="lower-roman">Romawi Kecil (i, ii, iii)</option>
                                            <option value="upper-roman">Romawi Besar (I, II, III)</option>
                                        </select>
                                        <button type="button" class="wysiwyg-btn" data-action="image" title="Insert Image" {{ $moduleLocked ? 'disabled' : '' }}><i class="bi bi-image"></i></button>
                                        <button type="button" class="wysiwyg-btn" data-action="align-left" title="Rata Kiri" {{ $moduleLocked ? 'disabled' : '' }}><i class="bi bi-text-left"></i></button>
                                        <button type="button" class="wysiwyg-btn" data-action="align-center"
                                            title="Rata Tengah" {{ $moduleLocked ? 'disabled' : '' }}><i class="bi bi-text-center"></i></button>
                                        <button type="button" class="wysiwyg-btn" data-action="align-right" {{ $moduleLocked ? 'disabled' : '' }} title="Rata Kanan"><i
                                                class="bi bi-text-right"></i></button>
                                        <button type="button" class="wysiwyg-btn" data-action="code" {{ $moduleLocked ? 'disabled' : '' }} title="Insert Code Block"><i
                                                class="bi bi-code-square"></i></button>
                                    </div>
                                    <input type="file" id="moduleImageInput" accept="image/*" style="display:none;" {{ $moduleLocked ? 'disabled' : '' }} />
                                    @php
                                        $firstModuleContent = $moduleTargetModules->first()->description ?? '';
                                    @endphp
                                    <div id="moduleWysiwygEditor" class="wysiwyg-editor"
                                        contenteditable="{{ $moduleLocked ? 'false' : 'true' }}" spellcheck="true"
                                        placeholder="Tulis pengantar materi di sini..."
                                        style="{{ $moduleLocked ? 'pointer-events:none; opacity:.72; background:#f8fafc;' : '' }}">{!! !empty($firstModuleContent) ? $firstModuleContent : '<p><br></p>' !!}</div>
                                </div>
                                <p class="text-editor-note">Gunakan tombol <strong>Image</strong> untuk gambar, atau <strong>Code Block</strong> untuk potongan kode.</p>
                            </div>

                            <!-- Right Column: Live Preview -->
                            <div class="studio-preview-pane">
                                <div class="preview-header">
                                    <p class="text-editor-label" style="margin: 0;">Live Preview</p>
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: rgba(32, 179, 134, 0.1); color: rgb(32, 179, 134); display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-arrow-repeat spin-icon"></i> REAL-TIME
                                    </span>
                                </div>
                                <div class="live-preview-body" id="moduleLivePreview"></div>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <button type="button" class="secondary-btn" id="previewModuleBtn">
                                <i class="bi bi-eye"></i> PREVIEW FULLSCREEN
                            </button>
                            <button type="submit" id="uploadSubmitBtn" class="primary-btn" {{ $moduleLocked ? 'disabled' : '' }}>
                                <i class="bi bi-cloud-arrow-up-fill"></i> SIMPAN MATERI TEKS
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel panel-video {{ $activeTab === 'video' ? 'active' : '' }}" data-panel="video">
                    @if(!$isAdmin && $isVideoApproved)
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); color: #059669; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-patch-check-fill" style="font-size: 24px;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Video Telah Disetujui (Terkunci)</strong>
                            <span style="font-size: 14px;">Lampiran video ini telah disetujui oleh admin trainer dan tidak dapat diubah lagi.</span>
                        </div>
                    </div>
                    @endif
                    @if(isset($isAdmin) && $isAdmin && !$schemePermissions['can_video'])
                    <div style="background: rgba(245, 197, 66, 0.1); border: 1px solid #f5c542; color: #b8860b; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-info-circle-fill" style="font-size: 24px;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Mode Pantau (Read-Only)</strong>
                            <span style="font-size: 14px;">Penyusunan video pada kelas ini adalah tugas Trainer berdasarkan skema yang dipilih. Anda hanya dapat melihat konten.</span>
                        </div>
                    </div>
                    @endif
                    <form id="videoForm" class="upload-form"
                        action="{{ $isAdmin ? route('admin.courses.studio.upload', $course->id) : route('trainer.courses.studio.upload', $course->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" name="target_modules" value="{{ $videoTargetIds }}">
                        <input type="hidden" name="replace_module_id" value="">
                        <input type="hidden" name="module_content_html" value="">

                        <div class="text-upload-shell">
                            <div class="text-upload-header">
                                <h3>Unggah Video Pembelajaran</h3>
                                <p>Tab ini khusus untuk video. File lain tidak akan diproses di sini, sehingga alur upload
                                    lebih jelas dan cepat.</p>
                                <ul class="material-outline">
                                    <li>Gunakan video dengan penjelasan singkat dan jelas.</li>
                                    <li>Pastikan durasi dan resolusi sesuai kebutuhan kelas.</li>
                                    <li>Tambahkan judul file yang mudah dikenali admin.</li>
                                </ul>
                            </div>

                            <div class="dropzone" id="videoDropzone"
                                style="{{ $videoLocked ? 'pointer-events:none; opacity:.72;' : '' }}">
                                <input type="file" id="videoFileInput" multiple accept=".mp4" name="files[]"
                                    style="display: none" {{ $videoLocked ? 'disabled' : '' }} />
                                <i class="bi bi-camera-video"></i>
                                <h2>Lampiran Video</h2>
                                <p>Format: MP4</p>
                                <p style="font-size: 12px; color: #64748b; margin-top: 2px">Klik atau drag-and-drop file
                                    video ke area ini</p>
                            </div>
                        </div>

                        <div id="videoFileList" class="file-list" style="margin-top: 20px; display: none">
                            <h3>Video yang Diunggah</h3>
                            <ul id="videoUploadedFiles" style="list-style: none; padding: 0; margin: 0"></ul>
                        </div>

                        @php
                            $existingVideoMaterials = $activeUnitModules->filter(function ($module) {
                                return (string) ($module->type ?? '') === 'video' && !empty($module->content_url);
                            });
                        @endphp

                        <div id="existingVideoMaterialsBlock" class="file-list"
                            style="margin-top: 16px; display: {{ $existingVideoMaterials->isNotEmpty() ? 'block' : 'none' }};">
                            <h3>Video Tersimpan Sebelumnya</h3>
                            <ul id="existingVideoMaterialsList" style="list-style: none; padding: 0; margin: 0;">
                                @foreach($existingVideoMaterials as $material)
                                    <li
                                        style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                            <i class="bi bi-camera-video"
                                                style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                            <div>
                                                <p
                                                    style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">
                                                    {{ $material->file_name ?: basename($material->content_url) }}
                                                </p>
                                                <p style="margin: 0; font-size: 12px; color: #999;">VIDEO  Slot
                                                    {{ $material->order_no }}
                                                </p>
                                                @php
                                                    $processingStatus = (string) ($material->processing_status ?? '');
                                                    $processingLabel = match ($processingStatus) {
                                                        'assigned_to_admin_course' => 'Diserahkan',
                                                        'processed_uploaded' => 'Hasil Edit Diunggah',
                                                        'revision_requested' => 'Revisi Diminta',
                                                        'ready_for_publish' => 'Siap Publikasi',
                                                        default => '',
                                                    };
                                                    $processingClass = match ($processingStatus) {
                                                        'assigned_to_admin_course' => 'assigned',
                                                        'processed_uploaded' => 'uploaded',
                                                        'revision_requested' => 'revision',
                                                        'ready_for_publish' => 'ready',
                                                        default => 'pending',
                                                    };
                                                @endphp
                                                @if($processingStatus !== '')
                                                    <span class="video-status-pill {{ $processingClass }}">
                                                        <i class="bi bi-activity"></i>{{ $processingLabel }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 6px;">
                                            <button type="button" class="preview-material-btn"
                                                data-view-url="{{ $isAdmin ? route('admin.courses.studio.material.view', [$course->id, $material->id]) : route('trainer.courses.studio.material.view', [$course->id, $material->id]) }}"
                                                data-material-type="video"
                                                data-file-name="{{ $material->file_name ?: basename($material->content_url) }}"
                                                title="Preview File"
                                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border-radius: 4px; text-decoration: none; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                            <button type="button" class="select-replace-btn" {{ ($videoLocked || $material->review_status === 'approved') ? 'disabled' : '' }} data-module-id="{{ $material->id }}" data-module-type="video"
                                                data-file-name="{{ $material->file_name ?: basename($material->content_url) }}"
                                                title="Ganti File"
                                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.2s; font-size: 12px; {{ ($videoLocked || $material->review_status === 'approved') ? 'opacity: 0.5; pointer-events: none;' : '' }}"
                                                onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="panel-footer">
                            <button type="submit" class="primary-btn" id="videoUploadSubmitBtn" {{ $videoLocked ? 'disabled' : '' }}>
                                <i class="bi bi-send"></i> SUBMIT FOR REVIEW
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel panel-quiz {{ $activeTab === 'quiz' ? 'active' : '' }}" data-panel="quiz">
                    @if(!$isAdmin && $isQuizApproved)
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); color: #059669; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-patch-check-fill" style="font-size: 24px;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Kuis Telah Disetujui (Terkunci)</strong>
                            <span style="font-size: 14px;">Kuis unit ini telah disetujui oleh admin trainer dan tidak dapat diubah lagi.</span>
                        </div>
                    </div>
                    @endif
                    @if(isset($isAdmin) && $isAdmin && !$schemePermissions['can_quiz'])
                    <div style="background: rgba(245, 197, 66, 0.1); border: 1px solid #f5c542; color: #b8860b; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-info-circle-fill" style="font-size: 24px;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Mode Pantau (Read-Only)</strong>
                            <span style="font-size: 14px;">Penyusunan quiz pada kelas ini adalah tugas Trainer berdasarkan skema yang dipilih. Anda hanya dapat melihat konten.</span>
                        </div>
                    </div>
                    @endif
                    @php
                        $existingQuizModules = $activeUnitModules->filter(function ($module) {
                            return $module->type === 'quiz';
                        })->values();

                        $existingQuizPayloads = $existingQuizModules->mapWithKeys(function ($module) {
                            $questions = $module->quizQuestions
                                ->sortBy('order_no')
                                ->values()
                                ->map(function ($question) {
                                    $answers = $question->answers->sortBy('order_no')->values();
                                    $correctIdx = $answers->search(function ($answer) {
                                        return (bool) $answer->is_correct;
                                    });
                                    if ($correctIdx === false) {
                                        $correctIdx = 0;
                                    }

                                    return [
                                        'text' => (string) $question->question,
                                        'weight' => (int) ($question->points ?? 10),
                                        'options' => $answers->pluck('answer_text')->values()->toArray(),
                                        'correctAnswer' => (int) $correctIdx,
                                    ];
                                })
                                ->toArray();

                            return [
                                (int) $module->id => $questions,
                            ];
                        });
                    @endphp

                    @if($existingQuizModules->isNotEmpty())
                        <div class="file-list" style="margin-bottom: 16px; display: block;">
                            <h3>Slot Quiz Unit Ini</h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($existingQuizModules as $quizModule)
                                    @php
                                        $questionCount = (int) ($quizModule->quiz_questions_count ?? 0);
                                        $isFilledQuiz = $questionCount > 0;
                                    @endphp
                                    <li
                                        style="padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                <i class="bi bi-patch-check"
                                                    style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                <div>
                                                    <p
                                                        style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">
                                                        {{ $quizModule->title ?: ('Quiz Unit ' . ($unitIndex + 1)) }}
                                                    </p>
                                                    <p style="margin: 0; font-size: 12px; color: #999;">
                                                        {{ $questionCount }} Soal  Slot
                                                        {{ $quizModule->order_no }}
                                                        @if($quizModule->updated_at)
                                                             Update terakhir {{ $quizModule->updated_at->format('d M Y H:i') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                                <span
                                                    style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; background: rgba(27, 23, 99, 0.1); color: var(--main-navy-clr); font-size: 12px; font-weight: 600;">
                                                    <i class="bi bi-clock-history"></i>
                                                    {{ $isFilledQuiz ? 'Tersimpan' : 'Belum Diisi' }}
                                                </span>
                                                <button type="button" class="quiz-edit-btn" data-module-id="{{ $quizModule->id }}"
                                                    {{ $quizLocked ? 'disabled' : '' }}
                                                    title="{{ $isFilledQuiz ? 'Edit Quiz' : 'Buat Quiz' }}"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                    <i class="bi {{ $isFilledQuiz ? 'bi-pencil-square' : 'bi-plus-lg' }}"></i>
                                                </button>
                                                @if($quizModule->quizQuestions->isNotEmpty())
                                                    <button type="button" class="quiz-history-toggle"
                                                        data-target="quiz-history-{{ $quizModule->id }}" title="Lihat Riwayat Soal"
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if($quizModule->quizQuestions->isNotEmpty())
                                            <div id="quiz-history-{{ $quizModule->id }}"
                                                style="margin-top: 10px; display: none; flex-direction: column; gap: 8px;">
                                                @foreach($quizModule->quizQuestions as $questionIndex => $question)
                                                    <div
                                                        style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px;">
                                                        <p
                                                            style="margin: 0 0 6px 0; font-size: 13px; font-weight: 600; color: var(--main-navy-clr);">
                                                            {{ $questionIndex + 1 }}. {{ $question->question }}
                                                        </p>
                                                        <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b;">
                                                            @foreach($question->answers as $answer)
                                                                <li
                                                                    style="margin-bottom: 4px; color: {{ $answer->is_correct ? '#0f766e' : '#64748b' }}; font-weight: {{ $answer->is_correct ? '600' : '400' }};">
                                                                    {{ $answer->answer_text }}
                                                                    @if($answer->is_correct)
                                                                        <span style="margin-left: 6px; font-size: 11px;">(Jawaban benar)</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="quizForm" class="quiz-form" action="{{ $isAdmin ? route('admin.courses.studio.quiz', $course->id) : route('trainer.courses.studio.quiz', $course->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" id="quizModuleId" name="quiz_module_id" value="">

                        <div class="quiz-meta">
                            <div class="meta-box" id="passingGradeBox">
                                <p>BATAS KELULUSAN (PASSING GRADE)</p>
                                <div class="meta-value">
                                    <input type="text" id="passingGradeInput" class="meta-input" value="70"
                                        inputmode="numeric" pattern="[0-9]*" />
                                    <strong id="passingGrade">70</strong>
                                    <span>%</span>
                                </div>
                            </div>
                            <div class="meta-box">
                                <p>BOBOT TOTAL TERDETEKSI</p>
                                <div class="meta-value">
                                    <strong id="totalWeight">0</strong><span> Points</span><em
                                        id="verifyStatus">PENDING</em>
                                </div>
                            </div>
                        </div>

                        <div id="questionsContainer" style="display: flex; flex-direction: column; gap: var(--spacing-lg);">
                        </div>

                        <div class="quiz-actions" style="margin-top: 24px;">
                            <button type="button" id="addQuestionBtn" class="primary-btn quiz-add-btn" {{ $quizLocked ? 'disabled' : '' }}>
                                <i class="bi bi-plus-lg"></i> TAMBAH SOAL
                            </button>
                            <button type="submit" class="primary-btn quiz-save-btn" {{ $quizLocked ? 'disabled' : '' }}>
                                <i class="bi bi-check-lg"></i> SIMPAN QUIZ
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </section>
    </main>

    <div id="modulePreviewModal"
        style="display:none; position:fixed; inset:0; z-index:10020; background:rgba(2,6,23,.62); align-items:center; justify-content:center; padding:20px;">
        <div
            style="background:#fff; border-radius:14px; width:min(980px, 96vw); max-height:90vh; overflow:hidden; box-shadow:0 24px 60px rgba(15,23,42,.3); display:flex; flex-direction:column;">
            <div
                style="padding:14px 16px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <div>
                    <h3 style="margin:0; font-size:16px; color:var(--main-navy-clr);">Preview Modul</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Tampilan yang akan dilihat peserta LMS</p>
                </div>
                <button type="button" id="modulePreviewCloseBtn"
                    style="width:34px; height:34px; border:1px solid #d1d5db; border-radius:10px; background:#fff; color:#334155; cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="modulePreviewBody" style="padding:18px; overflow:auto; background:#f8fafc; flex:1; min-height:0;"></div>
        </div>
    </div>

    <!-- REPLACEMENT CONFIRMATION MODAL -->
    <div id="replacementModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div
            style="background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 500px; width: 100%; animation: slideUp 0.3s ease; overflow-y: auto; max-height: 90vh;">
            <!-- Header -->
            <div
                style="padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px; background: #f9fafb;">
                <i class="bi bi-arrow-repeat" style="font-size: 24px; color: var(--main-navy-clr);"></i>
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: var(--main-navy-clr);">Ganti File Materi
                </h3>
            </div>

            <!-- Body -->
            <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
                <!-- File Lama -->
                <div>
                    <p
                        style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase;">
                        File Saat Ini</p>
                    <div
                        style="padding: 12px; background: #f3f4f6; border-radius: 8px; border-left: 3px solid var(--main-navy-clr);">
                        <p id="modalOldFileName"
                            style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);"></p>
                        <p id="modalOldFileInfo" style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;"></p>
                    </div>
                </div>

                <!-- File Baru -->
                <div>
                    <p
                        style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase;">
                        Pilih File Pengganti</p>
                    <div style="position: relative; border: 2px dashed #dfe6f2; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #f8fafc;"
                        id="replacementDropzone"
                        onmouseover="this.style.borderColor='var(--main-navy-clr)'; this.style.background='#f0f5fc';"
                        onmouseout="this.style.borderColor='#dfe6f2'; this.style.background='#f8fafc';">
                        <i class="bi bi-cloud-arrow-up"
                            style="font-size: 28px; color: var(--main-navy-clr); display: block; margin-bottom: 8px;"></i>
                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: var(--main-navy-clr);">Pilih File
                            Baru</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;">atau drag & drop</p>
                        <input type="file" id="replacementFileInput" style="display: none;"
                            accept=".mp4" />
                    </div>
                </div>

                <!-- Preview File Baru -->
                <div id="replacementPreview" style="display: none;">
                    <p
                        style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase;">
                        Preview Pengganti</p>
                    <div
                        style="padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 3px solid #10b981; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-check-circle-fill" style="font-size: 20px; color: #10b981;"></i>
                        <div style="flex: 1;">
                            <p id="replacementFileName"
                                style="margin: 0; font-size: 14px; font-weight: 600; color: #059669;"></p>
                            <p id="replacementFileSize" style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 8px; justify-content: flex-end; background: #f9fafb;">
                <button type="button" id="replacementCancelBtn"
                    style="padding: 10px 16px; background: #e5e7eb; color: var(--main-navy-clr); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: background 0.2s;"
                    onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                    BATAL
                </button>
                <button type="button" id="replacementConfirmBtn" disabled
                    style="padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 600; font-size: 13px; transition: all 0.2s; opacity: 0.5;">
                    GANTI FILE
                </button>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION MODAL -->
    <div id="notificationModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div
            style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 400px; width: 90%; animation: slideUp 0.3s ease;">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px;">
                <i id="modalIcon" class="bi" style="font-size: 24px;"></i>
                <h3 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: var(--main-navy-clr);"></h3>
            </div>
            <div style="padding: 16px 24px; color: #64748b; font-size: 14px; line-height: 1.6;">
                <p id="modalMessage" style="margin: 0;"></p>
            </div>
            <div
                style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 8px; justify-content: flex-end;">
                <button id="modalCloseBtn" type="button"
                    style="padding: 8px 16px; background: #f3f4f6; color: var(--main-navy-clr); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.2s;"
                    onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    OK
                </button>
            </div>
        </div>
    </div>

    <div id="materialPreviewModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65); z-index: 10001; align-items: center; justify-content: center; padding: 20px;">
        <div
            style="background: #fff; border-radius: 12px; width: min(980px, 100%); max-height: 92vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.25); display: flex; flex-direction: column;">
            <div
                style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="min-width: 0;">
                    <h3 style="margin: 0; font-size: 16px; color: var(--main-navy-clr);">Preview Materi</h3>
                    <p id="materialPreviewName"
                        style="margin: 2px 0 0 0; font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    </p>
                </div>
                <button type="button" id="materialPreviewCloseBtn"
                    style="border: none; background: #f3f4f6; color: #334155; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="materialPreviewBody" style="padding: 0; height: min(74vh, 760px); background: #f8fafc;"></div>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        const courseId = @json($course->id);
        const editorImageUploadUrl = @json($isAdmin ? route('admin.courses.studio.editor-image', $course->id) : route('trainer.courses.studio.editor-image', $course->id));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        let replacementState = {
            moduleId: null,
            fileName: null,
            fileType: null,
            selectedFile: null
        };

        function showNotificationModal(title, message, type = 'info') {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('modalIcon');
            const titleEl = document.getElementById('modalTitle');
            const messageEl = document.getElementById('modalMessage');

            titleEl.textContent = title;
            messageEl.textContent = message;

            if (type === 'success') {
                icon.className = 'bi bi-check-circle-fill';
                icon.style.color = '#10b981';
                titleEl.style.color = '#10b981';
            } else if (type === 'error') {
                icon.className = 'bi bi-exclamation-circle-fill';
                icon.style.color = '#ef4444';
                titleEl.style.color = '#ef4444';
            } else if (type === 'warning') {
                icon.className = 'bi bi-exclamation-triangle-fill';
                icon.style.color = '#f59e0b';
                titleEl.style.color = '#b45309';
            } else {
                icon.className = 'bi bi-info-circle-fill';
                icon.style.color = 'var(--main-navy-clr)';
                titleEl.style.color = 'var(--main-navy-clr)';
            }

            modal.style.display = 'flex';
        }

        function closeNotificationModal() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        function openMaterialPreview(viewUrl, fileName, materialType) {
            const modal = document.getElementById('materialPreviewModal');
            const nameEl = document.getElementById('materialPreviewName');
            const body = document.getElementById('materialPreviewBody');

            if (!modal || !nameEl || !body) {
                return;
            }

            nameEl.textContent = fileName || 'Materi';

            if (String(materialType || '').toLowerCase() === 'video') {
                body.innerHTML = `<video controls style="width:100%; height:100%; background:#000;"><source src="${viewUrl}"></video>`;
            } else {
                body.innerHTML = `<iframe src="${viewUrl}" style="width:100%; height:100%; border:none;" title="Preview Materi"></iframe>`;
            }

            modal.style.display = 'flex';
        }

        function closeMaterialPreview() {
            const modal = document.getElementById('materialPreviewModal');
            const body = document.getElementById('materialPreviewBody');

            if (body) {
                body.innerHTML = '';
            }

            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openReplacementModal(moduleId, fileName, fileType) {
            replacementState = { moduleId, fileName, fileType, selectedFile: null };

            const modal = document.getElementById('replacementModal');
            const oldFileName = document.getElementById('modalOldFileName');
            const oldFileInfo = document.getElementById('modalOldFileInfo');
            const preview = document.getElementById('replacementPreview');
            const confirmBtn = document.getElementById('replacementConfirmBtn');
            const fileInput = document.getElementById('replacementFileInput');

            if (!modal || !oldFileName || !oldFileInfo || !preview || !confirmBtn || !fileInput) {
                return;
            }

            oldFileName.textContent = fileName;
            oldFileInfo.textContent = `Tipe: ${String(fileType).toUpperCase()}`;
            preview.style.display = 'none';
            confirmBtn.disabled = true;
            confirmBtn.style.opacity = '0.5';
            fileInput.value = '';

            modal.style.display = 'flex';
        }

        function closeReplacementModal() {
            document.getElementById('replacementModal').style.display = 'none';
            replacementState = { moduleId: null, fileName: null, fileType: null, selectedFile: null };
        }

        function validateReplacementFile(file) {
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const uploadType = ext === 'mp4' ? 'video' : 'pdf';

            if (uploadType !== replacementState.fileType) {
                showNotificationModal('Tipe File Tidak Sesuai', `File harus bertipe ${String(replacementState.fileType).toUpperCase()}.`, 'error');
                return false;
            }

            if (file.size > 512 * 1024 * 1024) {
                showNotificationModal('File Terlalu Besar', 'Ukuran file maksimal 512MB.', 'error');
                return false;
            }

            return true;
        }

        document.getElementById('modalCloseBtn').addEventListener('click', closeNotificationModal);
        document.getElementById('notificationModal').addEventListener('click', (e) => {
            if (e.target.id === 'notificationModal') closeNotificationModal();
        });
        document.getElementById('replacementModal').addEventListener('click', (e) => {
            if (e.target.id === 'replacementModal') closeReplacementModal();
        });
        document.getElementById('materialPreviewCloseBtn').addEventListener('click', closeMaterialPreview);
        document.getElementById('materialPreviewModal').addEventListener('click', (e) => {
            if (e.target.id === 'materialPreviewModal') closeMaterialPreview();
        });

        document.addEventListener("DOMContentLoaded", function () {
            // --- TAB SWITCHING ---
            const tabs = document.querySelectorAll(".studio-tab");
            const panels = document.querySelectorAll("[data-panel]");
            const schemePermissions = @json($schemePermissions);
            const activeSchemeType = @json($activeSchemeType);

            const setTab = (targetTab, updateUrl = true) => {
                tabs.forEach((tab) => tab.classList.toggle("active", tab.dataset.tab === targetTab));
                panels.forEach((panel) => panel.classList.toggle("active", panel.dataset.panel === targetTab));

                if (updateUrl) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', targetTab);
                    window.history.replaceState({}, '', url.toString());
                }
            };

            tabs.forEach((tab) => {
                tab.addEventListener("click", (e) => {
                    if (tab.dataset.locked === '1') {
                        e.preventDefault();
                        showNotificationModal('Fitur Dikunci Skema', `Tab ${String(tab.dataset.tab || '').toUpperCase()} tidak tersedia pada skema aktif.`, 'warning');
                        return;
                    }
                    e.preventDefault();
                    setTab(tab.dataset.tab);
                });
            });

            const requestedTab = new URLSearchParams(window.location.search).get('tab');
            const serverActiveTab = @json($activeTab);
            if (requestedTab === 'module' || requestedTab === 'video' || requestedTab === 'quiz') {
                if (requestedTab === 'module' && !schemePermissions.can_module) {
                    setTab(schemePermissions.can_video ? 'video' : 'quiz', false);
                } else if (requestedTab === 'video' && !schemePermissions.can_video) {
                    setTab(schemePermissions.can_module ? 'module' : 'quiz', false);
                } else if (requestedTab === 'quiz' && !schemePermissions.can_quiz) {
                    setTab(schemePermissions.can_module ? 'module' : 'video', false);
                } else {
                    setTab(requestedTab, false);
                }
            } else {
                if (serverActiveTab === 'module' && schemePermissions.can_module) {
                    setTab('module', false);
                } else if (serverActiveTab === 'video' && schemePermissions.can_video) {
                    setTab('video', false);
                } else if (serverActiveTab === 'quiz' && schemePermissions.can_quiz) {
                    setTab('quiz', false);
                } else if (schemePermissions.can_module) {
                    setTab('module', false);
                } else if (schemePermissions.can_video) {
                    setTab('video', false);
                } else {
                    setTab('quiz', false);
                }
            }

            // --- UPLOAD LOGIC ---
            let videoUploadedFiles = [];
            let persistedMaterials = @json($uploadedMaterials);
            const activeUnitModules = @json($activeUnitModules->map(function ($module) {
                return ['id' => $module->id, 'type' => $module->type];
            })->values());
            const existingQuizPayloads = @json($existingQuizPayloads);
            const quizSlotModuleIds = activeUnitModules
                .filter((module) => module.type === 'quiz')
                .map((module) => Number(module.id));
            const videoForm = document.getElementById('videoForm');
            const videoDropzone = document.getElementById('videoDropzone');
            const videoFileInput = document.getElementById('videoFileInput');
            const videoFileList = document.getElementById('videoFileList');
            const videoUploadedFilesList = document.getElementById('videoUploadedFiles');
            const existingVideoMaterialsBlock = document.getElementById('existingVideoMaterialsBlock');
            const existingVideoMaterialsList = document.getElementById('existingVideoMaterialsList');
            const videoUploadBtn = document.getElementById('videoUploadSubmitBtn');
            const moduleForm = document.getElementById("moduleForm");
            const uploadBtn = document.getElementById("uploadSubmitBtn");
            const courseMaterialsLocked = @json($courseMaterialLocked);
            const videoLocked = @json($videoLocked);
            const targetModulesInput = moduleForm.querySelector('input[name="target_modules"]');
            const materialDraftInput = document.getElementById('materialDraftInput');
            const materialDraftStorageKey = `trainer-course-draft-${{ (int) $course->id }}`;
            const moduleEditor = document.getElementById('moduleWysiwygEditor');
            const moduleContentInput = document.getElementById('moduleContentHtml');
            const moduleImageInput = document.getElementById('moduleImageInput');
            const previewModuleBtn = document.getElementById('previewModuleBtn');
            const modulePreviewModal = document.getElementById('modulePreviewModal');
            const modulePreviewBody = document.getElementById('modulePreviewBody');
            const modulePreviewCloseBtn = document.getElementById('modulePreviewCloseBtn');
            const toolbar = document.getElementById('wysiwygToolbar');
            let selectedModuleImage = null;

            const codeLangOptions = `
                                    <option value="html">HTML</option>
                                    <option value="css">CSS</option>
                                    <option value="javascript">JavaScript</option>
                                    <option value="php">PHP</option>
                                `;

            function escapeCode(raw) {
                return String(raw ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function insertCodeBlock() {
                const codeBlockHtml = `
                                        <div class="module-code-block" contenteditable="false">
                                            <div class="module-code-top">
                                                <select class="module-code-lang">${codeLangOptions}</select>
                                                <div class="module-code-actions" style="display: flex; gap: 6px;">
                                                    <button type="button" class="module-code-copy">Copy Code</button>
                                                    <button type="button" class="module-code-delete"><i class="bi bi-trash"></i> Hapus</button>
                                                </div>
                                            </div>
                                            <pre><code class="language-html" contenteditable="true" spellcheck="false"></code></pre>
                                        </div>
                                        <p><br></p>
                                    `;
                document.execCommand('insertHTML', false, codeBlockHtml);
            }

            let lastEditorRange = null;
            document.addEventListener('selectionchange', () => {
                if (isSelectionInEditor()) {
                    const sel = window.getSelection();
                    if (sel && sel.rangeCount > 0) {
                        lastEditorRange = sel.getRangeAt(0).cloneRange();
                    }
                }
            });

            function applyListStyle(styleType) {
                if (!styleType) return;
                
                if (lastEditorRange) {
                    const sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(lastEditorRange);
                } else if (!isSelectionInEditor()) {
                    focusEditorEnd();
                }

                const selection = window.getSelection();
                if (!selection || selection.rangeCount === 0) return;

                let anchor = selection.anchorNode;
                let listNode = null;
                while (anchor && anchor !== moduleEditor) {
                    if (anchor.nodeName === 'UL' || anchor.nodeName === 'OL') {
                        listNode = anchor;
                        break;
                    }
                    anchor = anchor.parentNode;
                }

                if (!listNode) {
                    const isOrdered = ['decimal', 'lower-alpha', 'upper-alpha', 'lower-roman', 'upper-roman'].includes(styleType);
                    if (isOrdered) {
                        document.execCommand('insertOrderedList');
                    } else {
                        document.execCommand('insertUnorderedList');
                    }

                    const newSelection = window.getSelection();
                    let newAnchor = newSelection.anchorNode;
                    while (newAnchor && newAnchor !== moduleEditor) {
                        if (newAnchor.nodeName === 'UL' || newAnchor.nodeName === 'OL') {
                            listNode = newAnchor;
                            break;
                        }
                        newAnchor = newAnchor.parentNode;
                    }
                }

                if (listNode) {
                    const isOrderedStyle = ['decimal', 'lower-alpha', 'upper-alpha', 'lower-roman', 'upper-roman'].includes(styleType);
                    if (isOrderedStyle && listNode.nodeName === 'UL') {
                        const ol = document.createElement('ol');
                        ol.style.listStyleType = styleType;
                        while (listNode.firstChild) {
                            ol.appendChild(listNode.firstChild);
                        }
                        listNode.parentNode.replaceChild(ol, listNode);
                        listNode = ol;
                    } else if (!isOrderedStyle && listNode.nodeName === 'OL') {
                        const ul = document.createElement('ul');
                        ul.style.listStyleType = styleType;
                        while (listNode.firstChild) {
                            ul.appendChild(listNode.firstChild);
                        }
                        listNode.parentNode.replaceChild(ul, listNode);
                        listNode = ul;
                    } else {
                        listNode.style.listStyleType = styleType;
                    }
                }
                syncEditorContentToInput();
            }

            async function insertImageFromFile(file) {
                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }

                if (!file || !file.type || !file.type.startsWith('image/')) {
                    showNotificationModal('Tipe File Tidak Sesuai', 'Silakan pilih file gambar yang valid.', 'error');
                    return;
                }

                const selection = window.getSelection();
                const savedRange = selection && selection.rangeCount > 0 ? selection.getRangeAt(0).cloneRange() : null;

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('image', file);

                try {
                    const response = await fetch(editorImageUploadUrl, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.success || !data.url) {
                        throw new Error(data.error || data.message || 'Gagal mengunggah gambar.');
                    }

                    if (savedRange && selection) {
                        selection.removeAllRanges();
                        selection.addRange(savedRange);
                    }

                    const imageHtml = `
                                            <figure class="module-inline-image" data-image-align="center">
                                                <img src="${String(data.url || '')}" alt="Gambar materi" data-image-width="560" style="width:560px; max-width:100%;" />
                                            </figure>
                                        `;
                    document.execCommand('insertHTML', false, imageHtml);
                    syncEditorContentToInput();
                } catch (error) {
                    showNotificationModal('Gagal', error.message || 'Gagal mengunggah gambar.', 'error');
                }
            }

            function setSelectedImageAlignment(alignment) {
                if (!selectedModuleImage) {
                    showNotificationModal('Pilih Gambar', 'Klik gambar terlebih dahulu, lalu pilih perataan yang diinginkan.', 'warning');
                    return;
                }

                const targetFigure = selectedModuleImage.classList?.contains('module-inline-image')
                    ? selectedModuleImage
                    : selectedModuleImage.closest('.module-inline-image');

                if (!targetFigure) return;

                targetFigure.dataset.imageAlign = alignment;
                syncEditorContentToInput();
            }

            function renderPreviewContent(targetContainer) {
                if (!moduleEditor || !targetContainer) return;
                const rawHtml = moduleEditor.innerHTML || '';
                
                if (rawHtml.replace(/<[^>]*>/g, '').trim() === '' && !rawHtml.includes('<img')) {
                    targetContainer.innerHTML = '<div style="color: #94a3b8; font-style: italic; text-align: center; padding: 40px 0;">Belum ada konten materi. Mulai menulis di editor sebelah kiri...</div>';
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'module-preview-article';
                wrapper.innerHTML = rawHtml;

                wrapper.querySelectorAll('.module-code-block').forEach((block) => {
                    const lang = block.querySelector('.module-code-lang')?.value || 'plaintext';
                    const codeText = block.querySelector('code')?.textContent || '';
                    const pre = document.createElement('pre');
                    const code = document.createElement('code');
                    code.className = `language-${lang}`;
                    code.textContent = codeText;
                    pre.appendChild(code);

                    const copyBtn = document.createElement('button');
                    copyBtn.type = 'button';
                    copyBtn.className = 'module-code-copy';
                    copyBtn.textContent = 'Copy Code';
                    copyBtn.style.margin = '8px 0 0';
                    copyBtn.addEventListener('click', () => {
                        navigator.clipboard.writeText(codeText);
                    });

                    const holder = document.createElement('div');
                    holder.className = 'module-code-block';
                    holder.appendChild(pre);
                    holder.appendChild(copyBtn);

                    block.replaceWith(holder);
                });

                targetContainer.innerHTML = '';
                targetContainer.appendChild(wrapper);

                targetContainer.querySelectorAll('pre code').forEach((el) => {
                    if (window.hljs) {
                        window.hljs.highlightElement(el);
                    }
                });
            }

            let previewDebounceTimer;
            function updateLivePreviewDebounced() {
                clearTimeout(previewDebounceTimer);
                previewDebounceTimer = setTimeout(() => {
                    const livePreviewContainer = document.getElementById('moduleLivePreview');
                    if (livePreviewContainer && livePreviewContainer.offsetParent !== null) {
                        renderPreviewContent(livePreviewContainer);
                    }
                }, 250);
            }

            function checkEditorEmpty() {
                if (!moduleEditor) return;
                const text = moduleEditor.textContent.trim();
                const hasImages = moduleEditor.querySelector('img') !== null;
                const isEmpty = text === '' && !hasImages;
                moduleEditor.classList.toggle('is-editor-empty', isEmpty);
                updateLivePreviewDebounced();
            }

            function syncEditorContentToInput() {
                if (!moduleEditor || !moduleContentInput) return;
                moduleContentInput.value = moduleEditor.innerHTML.trim();
                try {
                    localStorage.setItem(materialDraftStorageKey, moduleContentInput.value);
                } catch (_) {
                }
                checkEditorEmpty();
            }

            if (moduleEditor && moduleContentInput) {
                try {
                    const savedRichDraft = localStorage.getItem(materialDraftStorageKey);
                    if (
                        savedRichDraft &&
                        savedRichDraft.trim() !== '' &&
                        !savedRichDraft.includes('data:image/') &&
                        savedRichDraft.length <= 60000
                    ) {
                        moduleEditor.innerHTML = savedRichDraft;
                    } else if (savedRichDraft && (savedRichDraft.includes('data:image/') || savedRichDraft.length > 60000)) {
                        localStorage.removeItem(materialDraftStorageKey);
                    }
                } catch (_) {
                }

                moduleEditor.addEventListener('input', syncEditorContentToInput);
                moduleEditor.addEventListener('change', syncEditorContentToInput);
                
                // Sanitize pasted content to prevent dirty styles (which block text editor actions)
                moduleEditor.addEventListener('paste', function (e) {
                    e.preventDefault();
                    // Get plain text from clipboard
                    const text = (e.originalEvent || e).clipboardData.getData('text/plain');
                    
                    // Insert plain text at the current cursor position
                    if (document.queryCommandSupported('insertText')) {
                        document.execCommand('insertText', false, text);
                    } else {
                        const selection = window.getSelection();
                        if (!selection.rangeCount) return;
                        selection.deleteFromDocument();
                        selection.getRangeAt(0).insertNode(document.createTextNode(text));
                    }
                    syncEditorContentToInput();
                });
                
                checkEditorEmpty();
            }

            const toggleSplitViewBtn = document.getElementById('toggleSplitViewBtn');
            const splitWrapper = document.getElementById('studioSplitWrapper');
            const livePreviewContainer = document.getElementById('moduleLivePreview');

            let isSplitActive = localStorage.getItem('moduleSplitViewActive') !== 'false';
            if (window.innerWidth < 992) {
                isSplitActive = false;
            }

            function setSplitView(active) {
                isSplitActive = active;
                if (splitWrapper) {
                    splitWrapper.classList.toggle('split-active', active);
                }
                if (toggleSplitViewBtn) {
                    toggleSplitViewBtn.classList.toggle('split-active', active);
                    toggleSplitViewBtn.innerHTML = active 
                        ? '<i class="bi bi-columns-gap"></i> EDITOR ONLY' 
                        : '<i class="bi bi-columns-gap"></i> SPLIT VIEW';
                }
                localStorage.setItem('moduleSplitViewActive', active);
                if (active) {
                    renderPreviewContent(livePreviewContainer);
                }
            }

            if (toggleSplitViewBtn) {
                toggleSplitViewBtn.addEventListener('click', () => {
                    setSplitView(!isSplitActive);
                });
            }

            setSplitView(isSplitActive);

            // Sync heights of wysiwygToolbar and previewHeader to prevent vertical misalignment
            const wysiwygToolbar = document.getElementById('wysiwygToolbar');
            const previewHeader = document.querySelector('.preview-header');
            if (wysiwygToolbar && previewHeader) {
                const syncHeaderHeight = () => {
                    if (splitWrapper && splitWrapper.classList.contains('split-active')) {
                        previewHeader.style.height = `${wysiwygToolbar.offsetHeight}px`;
                    } else {
                        previewHeader.style.height = '';
                    }
                };

                // Watch resize of toolbar dynamically
                if (typeof ResizeObserver !== 'undefined') {
                    const observer = new ResizeObserver(() => {
                        syncHeaderHeight();
                    });
                    observer.observe(wysiwygToolbar);
                }

                // Call on split view toggle
                const originalSetSplitView = setSplitView;
                setSplitView = function(active) {
                    originalSetSplitView(active);
                    syncHeaderHeight();
                };

                // Initial sync
                syncHeaderHeight();
            }

            function isSelectionInEditor() {
                if (!moduleEditor) return false;
                const selection = window.getSelection();
                if (selection && selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    return moduleEditor.contains(range.commonAncestorContainer);
                }
                return false;
            }

            function focusEditorEnd() {
                if (!moduleEditor) return;
                moduleEditor.focus();
                
                const targetNode = moduleEditor.querySelector('p') || moduleEditor;
                const range = document.createRange();
                const selection = window.getSelection();
                range.selectNodeContents(targetNode);
                range.collapse(false);
                selection.removeAllRanges();
                selection.addRange(range);
            }

            if (toolbar) {
                toolbar.addEventListener('mousedown', function (event) {
                    if (courseMaterialsLocked) {
                        event.preventDefault();
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        return;
                    }

                    const btn = event.target.closest('.wysiwyg-btn');
                    if (!btn) return;

                    // Prevent focus loss on the button click
                    event.preventDefault();

                    const action = btn.dataset.action;

                    // Focus the editor if selection is not currently inside it
                    if (!isSelectionInEditor()) {
                        focusEditorEnd();
                    }

                    if (action === 'bold') document.execCommand('bold');
                    if (action === 'italic') document.execCommand('italic');
                    if (action === 'ul') document.execCommand('insertUnorderedList');
                    if (action === 'h1') document.execCommand('formatBlock', false, 'h1');
                    if (action === 'h2') document.execCommand('formatBlock', false, 'h2');
                    if (action === 'h3') document.execCommand('formatBlock', false, 'h3');
                    if (action === 'image' && moduleImageInput) moduleImageInput.click();
                    if (action === 'align-left') {
                        if (selectedModuleImage) {
                            setSelectedImageAlignment('left');
                        } else {
                            document.execCommand('justifyLeft');
                        }
                    }
                    if (action === 'align-center') {
                        if (selectedModuleImage) {
                            setSelectedImageAlignment('center');
                        } else {
                            document.execCommand('justifyCenter');
                        }
                    }
                    if (action === 'align-right') {
                        if (selectedModuleImage) {
                            setSelectedImageAlignment('right');
                        } else {
                            document.execCommand('justifyRight');
                        }
                    }
                    if (action === 'code') insertCodeBlock();

                    syncEditorContentToInput();
                });
            }

            if (moduleImageInput) {
                moduleImageInput.addEventListener('change', function (event) {
                    if (courseMaterialsLocked) {
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        moduleImageInput.value = '';
                        return;
                    }

                    const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                    if (file) {
                        insertImageFromFile(file);
                    }
                    moduleImageInput.value = '';
                });
            }

            if (moduleEditor) {
                moduleEditor.addEventListener('click', function (event) {
                    const clickedImage = event.target.closest('.module-inline-image, .module-inline-image img');
                    if (clickedImage) {
                        selectedModuleImage = clickedImage.classList.contains('module-inline-image')
                            ? clickedImage
                            : clickedImage.closest('.module-inline-image');
                    } else {
                        selectedModuleImage = null;
                    }

                    moduleEditor.querySelectorAll('.module-inline-image').forEach((figure) => {
                        figure.classList.toggle('is-selected', figure === selectedModuleImage);
                    });

                    const copyBtn = event.target.closest('.module-code-copy');
                    if (copyBtn) {
                        const codeEl = copyBtn.closest('.module-code-block')?.querySelector('code');
                        const text = codeEl ? codeEl.textContent || '' : '';
                        navigator.clipboard.writeText(text).then(() => {
                            const old = copyBtn.textContent;
                            copyBtn.textContent = 'Copied';
                            setTimeout(() => copyBtn.textContent = old, 1000);
                        }).catch(() => {
                        });
                        return;
                    }

                    const deleteBtn = event.target.closest('.module-code-delete');
                    if (deleteBtn) {
                        if (courseMaterialsLocked) {
                            showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                            return;
                        }
                        const codeBlock = deleteBtn.closest('.module-code-block');
                        if (codeBlock) {
                            codeBlock.remove();
                            syncEditorContentToInput();
                        }
                        return;
                    }
                });

                moduleEditor.addEventListener('change', function (event) {
                    const langSelect = event.target.closest('.module-code-lang');
                    if (!langSelect) return;
                    const codeEl = langSelect.closest('.module-code-block')?.querySelector('code');
                    if (codeEl) {
                        codeEl.className = `language-${langSelect.value}`;
                    }
                    syncEditorContentToInput();
                }, true);

                const listStyleSelect = document.getElementById('listStyleSelect');
                if (listStyleSelect) {
                    listStyleSelect.addEventListener('change', function (event) {
                        if (courseMaterialsLocked) {
                            event.preventDefault();
                            listStyleSelect.value = '';
                            showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                            return;
                        }

                        const styleType = event.target.value;
                        if (styleType) {
                            applyListStyle(styleType);
                        }
                        
                        event.target.value = '';
                    });

                    listStyleSelect.addEventListener('mousedown', function(event) {
                        if (courseMaterialsLocked) {
                            event.preventDefault();
                            showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                            return;
                        }
                    });
                }
            }

            function openModulePreview() {
                if (!moduleEditor || !modulePreviewModal || !modulePreviewBody) return;
                syncEditorContentToInput();
                const rawHtml = moduleContentInput.value || '';
                if (rawHtml.trim() === '') {
                    showNotificationModal('Preview Kosong', 'Silakan isi materi terlebih dahulu sebelum preview.', 'warning');
                    return;
                }

                renderPreviewContent(modulePreviewBody);
                modulePreviewModal.style.display = 'flex';
            }

            if (previewModuleBtn) {
                previewModuleBtn.addEventListener('click', openModulePreview);
            }

            if (modulePreviewCloseBtn) {
                modulePreviewCloseBtn.addEventListener('click', () => {
                    modulePreviewModal.style.display = 'none';
                });
            }

            if (modulePreviewModal) {
                modulePreviewModal.addEventListener('click', (e) => {
                    if (e.target === modulePreviewModal) {
                        modulePreviewModal.style.display = 'none';
                    }
                });
            }

            function escapeHtml(raw) {
                return String(raw ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderVideoMaterials() {
                const items = Array.isArray(persistedMaterials)
                    ? [...persistedMaterials].filter((material) => String(material.type || '') === 'video')
                    : [];
                items.sort((a, b) => (a.order_no || 0) - (b.order_no || 0));

                if (!existingVideoMaterialsBlock || !existingVideoMaterialsList) {
                    return;
                }

                if (items.length === 0) {
                    existingVideoMaterialsBlock.style.display = 'none';
                    existingVideoMaterialsList.innerHTML = '';
                    return;
                }

                existingVideoMaterialsBlock.style.display = 'block';
                existingVideoMaterialsList.innerHTML = items.map((material) => {
                    const slot = material.order_no ?? '-';
                    const fileName = escapeHtml(material.file_name || 'file');
                    const viewUrl = escapeHtml(material.view_url || '#');
                    const moduleId = Number(material.module_id || 0);
                    const processingStatus = String(material.processing_status || '');
                    const processingLabel = {
                        assigned_to_admin_course: 'Diserahkan',
                        processed_uploaded: 'Hasil Edit Diunggah',
                        revision_requested: 'Revisi Diminta',
                        ready_for_publish: 'Siap Publikasi',
                    }[processingStatus] || '';
                    const processingClass = {
                        assigned_to_admin_course: 'assigned',
                        processed_uploaded: 'uploaded',
                        revision_requested: 'revision',
                        ready_for_publish: 'ready',
                    }[processingStatus] || 'pending';

                    return `
                                                        <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                                <i class="bi bi-camera-video" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                                <div>
                                                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">${fileName}</p>
                                                                    <p style="margin: 0; font-size: 12px; color: #999;">VIDEO � Slot ${slot}</p>
                                                                    ${processingStatus ? `<span class="video-status-pill ${processingClass}"><i class="bi bi-activity"></i>${escapeHtml(processingLabel)}</span>` : ''}
                                                                </div>
                                                            </div>
                                                            <div style="display: flex; gap: 6px;">
                                                                <button type="button" class="preview-material-btn"
                                                                    data-view-url="${viewUrl}" data-material-type="video" data-file-name="${fileName}"
                                                                    title="Preview File"
                                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border-radius: 4px; border: none; text-decoration: none; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </button>
                                                                <button type="button" class="select-replace-btn"
                                                                    ${(videoLocked || material.review_status === 'approved') ? 'disabled' : ''}
                                                                    data-module-id="${moduleId}" data-module-type="video" data-file-name="${fileName}"
                                                                    title="Ganti File"
                                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.2s; font-size: 12px; ${(videoLocked || material.review_status === 'approved') ? 'opacity: 0.5; pointer-events: none;' : ''}"
                                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </button>
                                                            </div>
                                                        </li>
                                                    `;
                }).join('');
            }

            renderVideoMaterials();

            document.addEventListener('click', function (event) {
                const replaceBtn = event.target.closest('.select-replace-btn');
                if (replaceBtn) {
                    openReplacementModal(
                        parseInt(replaceBtn.dataset.moduleId, 10),
                        replaceBtn.dataset.fileName || 'file',
                        replaceBtn.dataset.moduleType
                    );
                    return;
                }



                const previewBtn = event.target.closest('.preview-material-btn');
                if (previewBtn) {
                    openMaterialPreview(
                        previewBtn.dataset.viewUrl || '#',
                        previewBtn.dataset.fileName || 'Materi',
                        previewBtn.dataset.materialType || 'pdf'
                    );
                    return;
                }

                const editQuizBtn = event.target.closest('.quiz-edit-btn');
                if (editQuizBtn) {
                    if (courseMaterialsLocked) {
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        return;
                    }

                    const moduleId = Number(editQuizBtn.dataset.moduleId || 0);
                    const key = String(moduleId);
                    const quizPayload = (existingQuizPayloads && Object.prototype.hasOwnProperty.call(existingQuizPayloads, key))
                        ? existingQuizPayloads[key]
                        : [];

                    if (Array.isArray(quizPayload) && quizPayload.length > 0) {
                        quizQuestions = quizPayload.map((q) => ({
                            id: questionCounter++,
                            text: q.text || '',
                            weight: Number(q.weight || 10),
                            options: Array.isArray(q.options) && q.options.length ? q.options : ['', '', '', ''],
                            correctAnswer: Number(q.correctAnswer || 0),
                        }));
                    } else {
                        quizQuestions = createDefaultQuestions(5);
                    }

                    document.getElementById('quizModuleId').value = String(moduleId);
                    renderAllQuestions();
                    saveQuizDraft();
                    setTab('quiz');
                    showNotificationModal(
                        'Editor Quiz Siap',
                        (Array.isArray(quizPayload) && quizPayload.length > 0)
                            ? 'Quiz tersimpan dimuat ke editor. Kamu bisa ubah lalu simpan ulang.'
                            : 'Slot quiz kosong dibuka otomatis dengan 5 soal default. Silakan isi kontennya.',
                        'success'
                    );
                }
            });

            const quizHistoryToggleButtons = document.querySelectorAll('.quiz-history-toggle');
            quizHistoryToggleButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const targetId = this.dataset.target;
                    const target = document.getElementById(targetId);
                    if (!target) return;

                    const isHidden = target.style.display === 'none' || target.style.display === '';
                    target.style.display = isHidden ? 'flex' : 'none';
                });
            });

            // Setup replacement modal
            const replacementDropzone = document.getElementById('replacementDropzone');
            const replacementFileInput = document.getElementById('replacementFileInput');
            const replacementCancelBtn = document.getElementById('replacementCancelBtn');
            const replacementConfirmBtn = document.getElementById('replacementConfirmBtn');

            replacementDropzone.addEventListener('click', () => {
                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }
                replacementFileInput.click();
            });
            replacementDropzone.addEventListener('dragover', (e) => { e.preventDefault(); });
            replacementDropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }
                if (e.dataTransfer.files.length > 0) {
                    const file = e.dataTransfer.files[0];
                    if (validateReplacementFile(file)) {
                        replacementState.selectedFile = file;
                        showReplacementPreview(file);
                    }
                }
            });

            replacementFileInput.addEventListener('change', (e) => {
                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    replacementFileInput.value = '';
                    return;
                }
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    if (validateReplacementFile(file)) {
                        replacementState.selectedFile = file;
                        showReplacementPreview(file);
                    }
                }
            });

            replacementCancelBtn.addEventListener('click', closeReplacementModal);

            replacementConfirmBtn.addEventListener('click', () => {
                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }

                if (!replacementState.selectedFile) {
                    showNotificationModal('Perhatian', 'Silakan pilih file terlebih dahulu.', 'error');
                    return;
                }

                replacementConfirmBtn.disabled = true;
                replacementConfirmBtn.textContent = 'MEMPROSES...';

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('target_modules', String(replacementState.moduleId));
                formData.append('replace_module_id', String(replacementState.moduleId));
                formData.append('files[]', replacementState.selectedFile);

                fetch(moduleForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(async (res) => {
                        let data = {};
                        try {
                            data = await res.json();
                        } catch (_) {
                            data = {};
                        }

                        if (!res.ok) {
                            const firstValidationError = data.errors ? Object.values(data.errors).flat()[0] : null;
                            throw new Error(data.error || data.message || firstValidationError || "Unknown error");
                        }

                        return data;
                    })
                    .then(data => {
                        closeReplacementModal();
                        if (data.success) {
                            const updates = Array.isArray(data.updated_modules) ? data.updated_modules : [];
                            updates.forEach((row) => {
                                const idx = persistedMaterials.findIndex((m) => Number(m.module_id) === Number(row.module_id));
                                if (idx >= 0) {
                                    persistedMaterials[idx] = row;
                                } else {
                                    persistedMaterials.push(row);
                                }
                            });
                            renderVideoMaterials();
                            showNotificationModal('Berhasil', data.message || 'File berhasil diganti!', 'success');
                        } else {
                            let errorMsg = data.error || data.message || 'Unknown error';

                            // If there are available types info, add it to the error message
                            if (data.available_types && typeof data.available_types === 'object' && Object.keys(data.available_types).length > 0) {
                                const typeInfos = Object.entries(data.available_types)
                                    .map(([type, info]) => `${type.toUpperCase()}: ${info.filled}/${info.count} terisi`)
                                    .join(' | ');
                                errorMsg += '\n\n?? Slot Tersedia: ' + typeInfos;
                            }

                            showNotificationModal('Gagal', errorMsg, 'error');
                        }
                    })
                    .catch(err => {
                        closeReplacementModal();
                        showNotificationModal('Gagal', err.message || 'Terjadi kesalahan koneksi.', 'error');
                    })
                    .finally(() => {
                        replacementConfirmBtn.disabled = false;
                        replacementConfirmBtn.textContent = 'GANTI FILE';
                    });
            });

            // Define showReplacementPreview inside DOMContentLoaded
            window.showReplacementPreview = function (file) {
                document.getElementById('replacementFileName').textContent = file.name;
                document.getElementById('replacementFileSize').textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
                document.getElementById('replacementPreview').style.display = 'block';
                replacementConfirmBtn.disabled = false;
                replacementConfirmBtn.style.opacity = '1';
                replacementConfirmBtn.style.cursor = 'pointer';
            };

            if (videoDropzone && videoFileInput) {
                videoDropzone.addEventListener("click", () => {
                    if (courseMaterialsLocked) {
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        return;
                    }
                    videoFileInput.click();
                });
                videoDropzone.addEventListener("dragover", (e) => { e.preventDefault(); videoDropzone.style.borderColor = "#3c2957"; videoDropzone.style.background = "#f0f5fc"; });
                videoDropzone.addEventListener("dragleave", () => { videoDropzone.style.borderColor = "#dfe6f2"; videoDropzone.style.background = "#f8fafc"; });
                videoDropzone.addEventListener("drop", (e) => {
                    e.preventDefault();
                    if (courseMaterialsLocked) {
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        return;
                    }
                    videoDropzone.style.borderColor = "#dfe6f2";
                    videoDropzone.style.background = "#f8fafc";
                    handleVideoFiles(e.dataTransfer.files);
                });
                videoFileInput.addEventListener("change", (e) => {
                    if (courseMaterialsLocked) {
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        videoFileInput.value = '';
                        return;
                    }
                    handleVideoFiles(e.target.files);
                });
            }

            function handleVideoFiles(files) {
                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }

                if (!schemePermissions.can_video) {
                    showNotificationModal('Fitur Dikunci Skema', 'Skema aktif tidak mengizinkan upload video.', 'warning');
                    return;
                }

                Array.from(files).forEach((file) => {
                    if (getUploadType(file) !== 'video') {
                        return;
                    }
                    videoUploadedFiles.push(file);
                });

                if (Array.from(files).length > 0 && videoUploadedFiles.length === 0) {
                    showNotificationModal('Tidak Sesuai Skema', 'Hanya file MP4 yang bisa diupload di tab video.', 'warning');
                }

                updateVideoFileList();
                if (videoFileInput) {
                    videoFileInput.value = '';
                }
            }

            function getUploadType(file) {
                const ext = (file.name.split('.').pop() || '').toLowerCase();
                return ext === 'mp4' ? 'video' : 'pdf';
            }

            function updateVideoFileList() {
                if (videoUploadedFiles.length > 0) {
                    videoFileList.style.display = "block";
                    videoUploadedFilesList.innerHTML = videoUploadedFiles.map((file, index) => `
                                                            <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                                                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                                    <i class="bi bi-camera-video" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                                    <div>
                                                                        <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">${file.name}</p>
                                                                        <p style="margin: 0; font-size: 12px; color: #999;">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="delete-video-file" data-index="${index}" style="background: #ff6b6b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">HAPUS</button>
                                                            </li>
                                                        `).join("");

                    document.querySelectorAll(".delete-video-file").forEach(btn => {
                        btn.addEventListener("click", (e) => {
                            videoUploadedFiles.splice(parseInt(e.target.dataset.index), 1);
                            updateVideoFileList();
                        });
                    });
                } else {
                    videoFileList.style.display = "none";
                }
            }

            if (videoForm) {
                videoForm.addEventListener("submit", (e) => {
                    e.preventDefault();

                    if (courseMaterialsLocked) {
                        showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                        return;
                    }

                    if (videoUploadedFiles.length === 0) {
                        showNotificationModal('Perhatian', 'Silakan pilih minimal 1 file video untuk diupload.', 'error');
                        return;
                    }

                    const selectedTypes = [...new Set(videoUploadedFiles.map(getUploadType))];
                    if (selectedTypes.some((type) => type !== 'video')) {
                        showNotificationModal('Tipe Materi Tidak Sesuai', 'Tab video hanya menerima file MP4.', 'error');
                        return;
                    }

                    videoUploadBtn.disabled = true;
                    videoUploadBtn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> UPLOADING...';

                    const formData = new FormData(videoForm);
                    formData.delete('files[]');
                    formData.set('replace_module_id', '');
                    formData.set('module_content_html', '');

                    const filteredVideoIds = activeUnitModules
                        .filter(module => module.type === 'video')
                        .map(module => module.id);

                    if (filteredVideoIds.length > 0) {
                        const dynamicTargetModules = filteredVideoIds.join(',');
                        formData.set('target_modules', dynamicTargetModules);
                    }

                    videoUploadedFiles.forEach(file => formData.append('files[]', file));

                    fetch(videoForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(async (res) => {
                            let data = {};
                            try {
                                data = await res.json();
                            } catch (_) {
                                data = {};
                            }

                            if (!res.ok) {
                                const firstValidationError = data.errors ? Object.values(data.errors).flat()[0] : null;
                                throw new Error(data.error || data.message || firstValidationError || "Unknown error");
                            }

                            return data;
                        })
                        .then(data => {
                            if (data.success) {
                                const updates = Array.isArray(data.updated_modules) ? data.updated_modules : [];
                                updates.forEach((row) => {
                                    const idx = persistedMaterials.findIndex((m) => Number(m.module_id) === Number(row.module_id));
                                    if (idx >= 0) {
                                        persistedMaterials[idx] = row;
                                    } else {
                                        persistedMaterials.push(row);
                                    }
                                });
                                renderVideoMaterials();
                                videoUploadedFiles = [];
                                updateVideoFileList();
                                showNotificationModal('Berhasil', data.message || 'Video berhasil disubmit ke Admin!', 'success');
                                return;
                            }

                            showNotificationModal('Gagal', data.error || data.message || 'Unknown error', 'error');
                        })
                        .catch(err => {
                            showNotificationModal('Gagal', err.message || 'Terjadi kesalahan koneksi.', 'error');
                        })
                        .finally(() => {
                            videoUploadBtn.disabled = false;
                            videoUploadBtn.innerHTML = '<i class="bi bi-send"></i> SUBMIT VIDEO';
                        });
                });
            }

            // AJAX SUBMIT UPLOAD
            moduleForm.addEventListener("submit", (e) => {
                e.preventDefault();

                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }

                syncEditorContentToInput();
                const editorContent = moduleContentInput ? moduleContentInput.value.trim() : '';
                const isDefaultContent = editorContent === '<p>Tulis pengantar materi di sini...</p>' || 
                                         editorContent === '<p><br></p>' || 
                                         editorContent === '<br>' || 
                                         editorContent === '';
                const hasEditorContent = !isDefaultContent;

                const pdfFiles = document.getElementById('pdfFileInput')?.files;
                const hasPdfFile = pdfFiles && pdfFiles.length > 0;

                if (!hasEditorContent) {
                    return showNotificationModal('Perhatian', 'Silakan isi materi di editor terlebih dahulu.', 'error');
                }

                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> UPLOADING...';

                const formData = new FormData(moduleForm);
                formData.set('replace_module_id', '');

                fetch(moduleForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(async (res) => {
                        let data = {};
                        try {
                            data = await res.json();
                        } catch (_) {
                            data = {};
                        }

                        if (!res.ok) {
                            const firstValidationError = data.errors
                                ? Object.values(data.errors).flat()[0]
                                : null;
                            throw new Error(data.error || data.message || firstValidationError || "Unknown error");
                        }

                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            const updates = Array.isArray(data.updated_modules) ? data.updated_modules : [];
                            updates.forEach((row) => {
                                const idx = persistedMaterials.findIndex((m) => Number(m.module_id) === Number(row.module_id));
                                if (idx >= 0) {
                                    persistedMaterials[idx] = row;
                                } else {
                                    persistedMaterials.push(row);
                                }
                            });
                            try {
                                localStorage.removeItem(materialDraftStorageKey);
                            } catch (_) {}

                            if (moduleEditor) {
                                checkEditorEmpty();
                            }
                            showNotificationModal('Berhasil', data.message || 'Materi berhasil disubmit ke Admin!', 'success');
                            return;
                        } else {
                            const firstValidationError = data.errors
                                ? Object.values(data.errors).flat()[0]
                                : null;
                            let errorMsg = data.error || data.message || firstValidationError || 'Unknown error';

                            // If there are available types info, add it to the error message
                            if (data.available_types && typeof data.available_types === 'object' && Object.keys(data.available_types).length > 0) {
                                const typeInfos = Object.entries(data.available_types)
                                    .map(([type, info]) => `${type.toUpperCase()}: ${info.filled}/${info.count} terisi`)
                                    .join(' | ');
                                errorMsg += '\n\n?? Slot Tersedia: ' + typeInfos;
                            }

                            showNotificationModal('Gagal', errorMsg, 'error');
                        }
                    })
                    .catch(err => showNotificationModal('Gagal', err.message || 'Terjadi kesalahan koneksi.', 'error'))
                    .finally(() => {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = '<i class="bi bi-send"></i> SUBMIT FOR REVIEW';
                    });
            });


            // --- QUIZ LOGIC (FIXED FOR CONTROLLER) ---
            let quizQuestions = [];
            let questionCounter = 1;
            const qContainer = document.getElementById("questionsContainer");
            const addQuestionBtn = document.getElementById("addQuestionBtn");
            const passingGradeInput = document.getElementById("passingGradeInput");
            const passingGradeDisplay = document.getElementById("passingGrade");
            const totalWeightDisplay = document.getElementById("totalWeight");
            const verifyStatusDisplay = document.getElementById("verifyStatus");
            const currentUnit = new URLSearchParams(window.location.search).get('unit') || '0';
            const quizDraftStorageKey = `trainer_quiz_draft_{{ $course->id }}_${currentUnit}`;

            function saveQuizDraft() {
                const payload = {
                    passingGrade: parseInt(passingGradeInput.value) || 70,
                    questions: quizQuestions,
                    questionCounter,
                };
                localStorage.setItem(quizDraftStorageKey, JSON.stringify(payload));
            }

            function loadQuizDraft() {
                const raw = localStorage.getItem(quizDraftStorageKey);
                if (!raw) {
                    return false;
                }

                try {
                    const parsed = JSON.parse(raw);
                    if (!parsed || !Array.isArray(parsed.questions) || parsed.questions.length === 0) {
                        return false;
                    }

                    quizQuestions = parsed.questions;
                    questionCounter = Math.max(parsed.questionCounter || 1, quizQuestions.length + 1);

                    const restoredPassingGrade = parseInt(parsed.passingGrade);
                    const finalPassingGrade = Number.isNaN(restoredPassingGrade) ? 70 : Math.max(0, Math.min(100, restoredPassingGrade));
                    passingGradeInput.value = finalPassingGrade;
                    passingGradeDisplay.textContent = finalPassingGrade;

                    renderAllQuestions();
                    return true;
                } catch (_) {
                    return false;
                }
            }

            addQuestionBtn.addEventListener("click", addQuestion);

            function addQuestion() {
                quizQuestions.push({ id: questionCounter++, text: "", weight: 10, options: ["", "", "", ""], correctAnswer: 0 });
                renderAllQuestions();
                saveQuizDraft();
            }

            function createDefaultQuestions(count = 5) {
                questionCounter = 1;
                return Array.from({ length: count }, () => ({
                    id: questionCounter++,
                    text: "",
                    weight: 10,
                    options: ["", "", "", ""],
                    correctAnswer: 0,
                }));
            }

            function renderAllQuestions() {
                qContainer.innerHTML = "";
                quizQuestions.forEach((q, index) => {
                    const qEl = document.createElement("article");
                    qEl.className = "quiz-editor";
                    qEl.innerHTML = `
                                                            <div class="q-head">
                                                                <div class="q-number">${index + 1}</div>
                                                                <div class="q-inputs">
                                                                    <label>PERTANYAAN</label>
                                                                    <input type="text" class="q-text" placeholder="Masukkan pertanyaan..." value="${q.text}" />
                                                                </div>
                                                                <div class="q-score">
                                                                    <label>BOBOT</label>
                                                                    <input type="number" class="q-weight" value="${q.weight}" min="1" />
                                                                </div>
                                                                <button type="button" class="delete-question"><i class="bi bi-trash"></i> HAPUS</button>
                                                            </div>
                                                            <div class="options-section">
                                                                <p class="options-label">PILIHAN JAWABAN</p>
                                                                <div class="options-grid">
                                                                    ${q.options.map((opt, oIdx) => `
                                                                        <div class="option-container">
                                                                            <button type="button" class="option-btn ${q.correctAnswer === oIdx ? 'is-correct' : ''}" data-opt="${oIdx}">
                                                                                <i class="bi ${q.correctAnswer === oIdx ? 'bi-check-circle-fill' : 'bi-circle'}"></i>
                                                                                <span>Opsi ${oIdx + 1}</span>
                                                                            </button>
                                                                            <input type="text" class="option-input" value="${opt}" placeholder="Jawaban opsi ${oIdx + 1}" />
                                                                        </div>
                                                                    `).join("")}
                                                                </div>
                                                            </div>
                                                        `;

                    // Event Listeners for this question
                    qEl.querySelector(".q-text").addEventListener("input", (e) => {
                        q.text = e.target.value;
                        saveQuizDraft();
                    });
                    qEl.querySelector(".q-weight").addEventListener("input", (e) => {
                        q.weight = parseInt(e.target.value) || 0;
                        updateTotalWeight();
                        saveQuizDraft();
                    });
                    qEl.querySelector(".delete-question").addEventListener("click", () => {
                        quizQuestions.splice(index, 1);
                        renderAllQuestions();
                        saveQuizDraft();
                    });

                    qEl.querySelectorAll(".option-btn").forEach(btn => {
                        btn.addEventListener("click", () => {
                            q.correctAnswer = parseInt(btn.dataset.opt);
                            renderAllQuestions(); // Re-render to update UI
                            saveQuizDraft();
                        });
                    });

                    qEl.querySelectorAll(".option-input").forEach((inp, oIdx) => {
                        inp.addEventListener("input", (e) => {
                            q.options[oIdx] = e.target.value;
                            saveQuizDraft();
                        });
                    });

                    qContainer.appendChild(qEl);
                });
                updateTotalWeight();
            }

            function updateTotalWeight() {
                const total = quizQuestions.reduce((sum, q) => sum + q.weight, 0);
                totalWeightDisplay.textContent = total;
                verifyStatusDisplay.textContent = total > 0 ? "VERIFIED" : "PENDING";
                verifyStatusDisplay.style.background = total > 0 ? "#2c237f" : "#ff6b6b";
            }

            // Passing Grade UI Edit
            document.getElementById("passingGradeBox").addEventListener("click", () => {
                passingGradeDisplay.style.display = "none";
                passingGradeInput.style.display = "inline-block";
                passingGradeInput.focus();
                passingGradeInput.select();
            });

            passingGradeInput.addEventListener("blur", () => {
                let val = parseInt(passingGradeInput.value) || 70;
                val = Math.max(0, Math.min(100, val));
                passingGradeInput.value = val;
                passingGradeDisplay.textContent = val;
                passingGradeDisplay.style.display = "inline";
                passingGradeInput.style.display = "none";
                saveQuizDraft();
            });

            // Initialize from draft, fallback to template default slot and default questions
            if (!loadQuizDraft()) {
                const firstQuizSlotId = quizSlotModuleIds.length > 0 ? quizSlotModuleIds[0] : null;
                if (firstQuizSlotId) {
                    document.getElementById('quizModuleId').value = String(firstQuizSlotId);
                }
                quizQuestions = createDefaultQuestions(5);
                renderAllQuestions();
                saveQuizDraft();
            }

            // AJAX SUBMIT QUIZ
            document.getElementById("quizForm").addEventListener("submit", function (e) {
                e.preventDefault();

                if (courseMaterialsLocked) {
                    showNotificationModal('Materi Terkunci', 'Materi course belum bisa diubah sebelum undangan diterima.', 'warning');
                    return;
                }

                // Validasi Kosong
                if (quizQuestions.length === 0) return showNotificationModal('Perhatian', 'Tambahkan minimal 1 soal!', 'error');
                const isInvalid = quizQuestions.some(q => q.text.trim() === "" || q.options.some(o => o.trim() === ""));
                if (isInvalid) return showNotificationModal('Perhatian', 'Semua pertanyaan dan opsi jawaban wajib diisi!', 'error');

                // Dapatkan quiz_module_id dari activeUnitModules
                const explicitQuizModuleId = Number(document.getElementById('quizModuleId').value || 0);
                let quizModuleId = explicitQuizModuleId > 0 ? explicitQuizModuleId : null;

                if (!quizModuleId) {
                    for (const module of activeUnitModules) {
                        if (module.type === 'quiz') {
                            quizModuleId = module.id;
                            break;
                        }
                    }
                }

                if (!quizModuleId) {
                    return showNotificationModal('Perhatian', 'Modul quiz tidak ditemukan untuk bab ini.', 'error');
                }

                // Format data untuk dikirim ke Controller via fetch JSON
                const quizData = {
                    quiz_module_id: quizModuleId,
                    passingGrade: parseInt(passingGradeInput.value),
                    questions: quizQuestions.map(q => ({
                        text: q.text,
                        options: q.options,
                        correctAnswer: q.correctAnswer,
                        weight: q.weight
                    }))
                };

                const btnSubmit = this.querySelector('.quiz-save-btn');
                const origText = btnSubmit.innerHTML;
                btnSubmit.innerHTML = 'MENYIMPAN...';
                btnSubmit.disabled = true;

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(quizData)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem(quizDraftStorageKey);
                            showNotificationModal('Berhasil', 'Kuis berhasil disimpan! ' + data.message, 'success');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            showNotificationModal('Gagal', data.message || 'Pastikan data terisi dengan benar.', 'error');
                        }
                    })
                    .catch(err => showNotificationModal('Gagal', 'Terjadi kesalahan jaringan.', 'error'))
                    .finally(() => {
                        btnSubmit.innerHTML = origText;
                        btnSubmit.disabled = false;
                    });
            });
        });
    </script>

    <!-- Modal Style Guide (Panduan Gaya Penulisan Editor) -->
    <div class="modal fade" id="styleGuideModal" tabindex="-1" aria-labelledby="styleGuideModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 18px 24px;">
                    <h5 class="modal-title" id="styleGuideModalLabel" style="font-weight: 800; color: #1a1d78; display: flex; align-items: center; gap: 8px; margin: 0;">
                        <i class="bi bi-journal-text" style="color: #ffcd00; font-size: 1.35rem;"></i> Panduan Gaya Penulisan Editor (Style Guide)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 24px 32px; color: #334155; font-size: 0.92rem; line-height: 1.6; max-height: 65vh; overflow-y: auto;">
                    <p style="font-weight: 500; margin-bottom: 20px; color: #64748b;">
                        Gunakan panduan visual ini untuk memastikan materi pembelajaran yang Anda buat di editor teks seragam, rapi, dan mudah dipahami oleh Admin dan Peserta.
                    </p>
                    
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        
                        <!-- Section 1 -->
                        <div style="display: flex; gap: 14px; align-items: flex-start;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(26, 29, 120, 0.08); display: flex; align-items: center; justify-content: center; color: var(--main-navy-clr, #1a1d78); font-weight: bold; flex-shrink: 0;">
                                H
                            </div>
                            <div>
                                <h6 style="font-weight: 800; margin: 0 0 6px; color: var(--main-navy-clr, #1a1d78); font-size: 0.95rem;">1. Penggunaan Heading (Tajuk)</h6>
                                <p style="margin: 0; color: #475569; font-size: 0.88rem;">Gunakan tingkat heading secara hierarkis:</p>
                                <ul style="margin: 4px 0 0; padding-left: 20px; color: #475569; font-size: 0.86rem; list-style-type: disc;">
                                    <li><strong>Heading 1 (H1)</strong> untuk judul materi utama.</li>
                                    <li><strong>Heading 2 (H2)</strong> untuk judul sub-bab atau topik penting.</li>
                                    <li><strong>Heading 3 (H3)</strong> untuk detail sub-topik.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Section 2 -->
                        <div style="display: flex; gap: 14px; align-items: flex-start;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(26, 29, 120, 0.08); display: flex; align-items: center; justify-content: center; color: var(--main-navy-clr, #1a1d78); flex-shrink: 0;">
                                <i class="bi bi-type-bold"></i>
                            </div>
                            <div>
                                <h6 style="font-weight: 800; margin: 0 0 6px; color: var(--main-navy-clr, #1a1d78); font-size: 0.95rem;">2. Penyorotan Teks (Emphasis)</h6>
                                <p style="margin: 0; color: #475569; font-size: 0.88rem;">
                                    Gunakan tombol <strong>Bold (Tebal)</strong> hanya untuk istilah kunci, definisi krusial, atau kata penting. Gunakan <strong>Italic (Miring)</strong> untuk istilah asing atau kutipan. Hindari pemformatan berlebihan agar modul tetap mudah dipindai secara visual.
                                </p>
                            </div>
                        </div>

                        <!-- Section 3 -->
                        <div style="display: flex; gap: 14px; align-items: flex-start;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(26, 29, 120, 0.08); display: flex; align-items: center; justify-content: center; color: var(--main-navy-clr, #1a1d78); flex-shrink: 0;">
                                <i class="bi bi-code-square"></i>
                            </div>
                            <div>
                                <h6 style="font-weight: 800; margin: 0 0 6px; color: var(--main-navy-clr, #1a1d78); font-size: 0.95rem;">3. Penulisan Kode (Code Blocks)</h6>
                                <p style="margin: 0; color: #475569; font-size: 0.88rem;">
                                    Gunakan tombol <strong>Code Block (&lt;/&gt;)</strong> untuk menyisipkan perintah sintaksis, baris kode program, atau perintah terminal. Jangan menuliskan kode langsung di teks paragraf biasa demi kerapian tampilan.
                                </p>
                            </div>
                        </div>

                        <!-- Section 4 -->
                        <div style="display: flex; gap: 14px; align-items: flex-start;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(26, 29, 120, 0.08); display: flex; align-items: center; justify-content: center; color: var(--main-navy-clr, #1a1d78); flex-shrink: 0;">
                                <i class="bi bi-image"></i>
                            </div>
                            <div>
                                <h6 style="font-weight: 800; margin: 0 0 6px; color: var(--main-navy-clr, #1a1d78); font-size: 0.95rem;">4. Integrasi Gambar & Media</h6>
                                <p style="margin: 0; color: #475569; font-size: 0.88rem;">
                                    Gunakan tombol <strong>Insert Image</strong> untuk diagram, tangkapan layar, atau ilustrasi relevan. Pastikan ukuran file gambar optimal (resolusi tajam namun ukuran file tetap ringan) dan diletakkan di baris baru.
                                </p>
                            </div>
                        </div>

                        <!-- Section 5 -->
                        <div style="display: flex; gap: 14px; align-items: flex-start;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(26, 29, 120, 0.08); display: flex; align-items: center; justify-content: center; color: var(--main-navy-clr, #1a1d78); flex-shrink: 0;">
                                <i class="bi bi-list-ul"></i>
                            </div>
                            <div>
                                <h6 style="font-weight: 800; margin: 0 0 6px; color: var(--main-navy-clr, #1a1d78); font-size: 0.95rem;">5. Struktur Daftar Rincian (List)</h6>
                                <p style="margin: 0; color: #475569; font-size: 0.88rem;">
                                    Gunakan <strong>Bullet List</strong> untuk rincian tak berurutan (misal: fitur, kelebihan). Gunakan penomoran manual jika langkah-langkah harus diikuti secara berurutan.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                    <button type="button" class="secondary-btn" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 700; font-size: 0.84rem; padding: 8px 20px; cursor: pointer;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @include('trainer.partials.scheme-selection-modal')
@endsection


