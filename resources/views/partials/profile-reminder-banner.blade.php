{{-- Profile Reminder Banner Component - Enhanced Design -- Only show for non-admin users --}}
@if(Auth::check() && Auth::user()->role !== 'admin')
<div id="profileReminderBanner" class="profile-reminder-banner" style="display: none;">
    <div class="container-fluid px-4">
        <div class="reminder-content-wrapper">
            <div class="reminder-content">
                <!-- Profile Picture dengan animasi pulse -->
                <div class="reminder-icon-wrapper">
                    <div class="reminder-icon-pulse"></div>
                    <div class="reminder-icon">
                        <img src="{{ Auth::user()->avatar_url }}" 
                             alt="Profile" 
                             class="reminder-profile-img"
                             referrerpolicy="no-referrer"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6b7280&color=ffffff&format=png';">
                    </div>
                </div>
                
                <!-- Text Content -->
                <div class="reminder-text flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="mb-0 fw-bold reminder-title">Lengkapi Profil Anda</h6>
                        <span class="reminder-badge">Penting!</span>
                    </div>
                    <p class="mb-3 small reminder-description">Profil Anda belum lengkap. Lengkapi untuk pengalaman yang lebih baik dan akses fitur lengkap.</p>
                    
                    <!-- Progress Section -->
                    <div class="progress-section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="progress-label">Kelengkapan Profil</span>
                            <span class="progress-percentage fw-bold" id="reminderPercentage">0%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-track">
                                <div id="reminderProgressBar" class="progress-fill" 
                                     style="width: 0%;">
                                    <div class="progress-shine"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="reminder-actions">
                    <a id="reminderCTABtn" href="{{ route('profile.edit') }}" class="reminder-cta-btn">
                        <i class="bi bi-arrow-right-circle me-2"></i>
                        Lengkapi Sekarang
                    </a>
                    <button type="button" class="reminder-dismiss-btn" id="reminderDismissBtn" aria-label="Close" style="display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1000 !important;">
                        <i class="bi bi-x-lg" style="display: block !important; visibility: visible !important; opacity: 1 !important; color: white !important;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-reminder-banner {
    position: fixed;
    top: 70px;
    left: 0;
    right: 0;
    z-index: 1030;
    padding: 0;
    animation: slideDownBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes slideDownBounce {
    0% {
        transform: translateY(-120%);
        opacity: 0;
    }
    60% {
        transform: translateY(10px);
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.reminder-content-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    background-size: 200% 200%;
    animation: gradientShift 8s ease infinite;
    padding: 1.5rem;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4),
                0 4px 16px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: visible !important;
}

.reminder-content-wrapper::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.reminder-content {
    display: flex !important;
    align-items: center !important;
    gap: 1.5rem !important;
    color: white;
    position: relative;
    z-index: 1;
    width: 100%;
}

/* Icon dengan pulse effect */
.reminder-icon-wrapper {
    position: relative;
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}

.reminder-icon-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.7;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0;
    }
}

.reminder-icon {
    position: relative;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.reminder-profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.reminder-text {
    flex: 1;
    min-width: 0;
}

.reminder-title {
    color: white;
    font-size: 1.1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.reminder-badge {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.reminder-description {
    color: rgba(255, 255, 255, 0.95);
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
}

/* Progress Section */
.progress-section {
    margin-top: 1rem;
}

.progress-label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

.progress-percentage {
    font-size: 1rem;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.progress-container {
    margin-top: 0.5rem;
}

.progress-track {
    height: 10px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 50%, #fbbf24 100%);
    background-size: 200% 100%;
    border-radius: 10px;
    position: relative;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    animation: progressShine 2s linear infinite;
    box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}

@keyframes progressShine {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.progress-shine {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shine 2s infinite;
}

@keyframes shine {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(200%); }
}

/* Actions */
.reminder-actions {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    flex-shrink: 0 !important;
    position: relative !important;
    z-index: 10 !important;
}

.reminder-cta-btn {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1e1b4b;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    white-space: nowrap;
    box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4),
                0 2px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    position: relative;
    overflow: hidden;
}

.reminder-cta-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.reminder-cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(251, 191, 36, 0.5),
                0 4px 12px rgba(0, 0, 0, 0.3);
}

.reminder-cta-btn:hover::before {
    width: 300px;
    height: 300px;
}

.reminder-cta-btn:active {
    transform: translateY(0);
}

.reminder-dismiss-btn {
    background: rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.5) !important;
    color: white !important;
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.3s ease;
    font-size: 1.1rem;
    flex-shrink: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    line-height: 1;
    position: relative !important;
    z-index: 10 !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.reminder-dismiss-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.6);
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.reminder-dismiss-btn:active {
    transform: rotate(90deg) scale(0.95);
}

.reminder-dismiss-btn i {
    font-size: 1.1rem !important;
    line-height: 1 !important;
    display: block !important;
    color: white !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Responsive */
@media (max-width: 991px) {
    .profile-reminder-banner {
        top: 70px;
    }
    
    .reminder-content-wrapper {
        padding: 1.25rem;
        border-radius: 0 0 15px 15px;
    }
    
    .reminder-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .reminder-icon-wrapper {
        margin: 0 auto;
        width: 50px;
        height: 50px;
    }
    
    .reminder-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .reminder-text {
        width: 100%;
    }
    
    .reminder-title {
        font-size: 1rem;
    }
    
    .reminder-description {
        font-size: 0.85rem;
    }
    
    .reminder-actions {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .reminder-cta-btn {
        flex: 1;
        min-width: 180px;
        justify-content: center;
        font-size: 0.9rem;
        padding: 0.7rem 1.25rem;
    }
    
    .reminder-dismiss-btn {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .reminder-content-wrapper {
        padding: 1rem;
    }
    
    .reminder-title {
        font-size: 0.95rem;
    }
    
    .reminder-description {
        font-size: 0.8rem;
        margin-bottom: 0.75rem !important;
    }
    
    .reminder-badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.6rem;
    }
    
    .progress-label {
        font-size: 0.75rem;
    }
    
    .progress-percentage {
        font-size: 0.9rem;
    }
    
    .reminder-cta-btn {
        font-size: 0.85rem;
        padding: 0.65rem 1rem;
        min-width: 150px;
    }
    
    .reminder-icon-wrapper {
        width: 45px;
        height: 45px;
    }
    
    .reminder-icon {
        width: 45px;
        height: 45px;
        font-size: 1.3rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('profileReminderBanner');
    
    if (!banner) {
        console.warn('Profile reminder banner element not found');
        return;
    }
    
    const progressBar = document.getElementById('reminderProgressBar');
    const percentageText = document.getElementById('reminderPercentage');
    const ctaBtn = document.getElementById('reminderCTABtn');
    const dismissBtn = document.getElementById('reminderDismissBtn');
    
    console.log('Checking profile reminder status...');
    
    // Check reminder status
    fetch('{{ route("profile.reminder.check") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Reminder check response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Reminder check data:', data);
        
        if (data.should_show) {
            console.log('Reminder should be shown');
            
            // Update progress bar dengan animasi
            const percentage = data.completion_percentage || 0;
            
            if (progressBar) {
                // Animate progress bar
                setTimeout(() => {
                    progressBar.style.width = percentage + '%';
                    progressBar.setAttribute('aria-valuenow', percentage);
                }, 300);
            }
            
            if (percentageText) {
                percentageText.textContent = percentage + '%';
            }
            
            // Add deep-link to first missing field
            if (data.first_missing_field && ctaBtn) {
                const editUrl = '{{ route("profile.edit") }}';
                ctaBtn.href = editUrl + '?focus=' + data.first_missing_field;
            }
            
            // Show banner dengan delay untuk animasi
            setTimeout(() => {
                banner.style.display = 'block';
                console.log('Banner displayed');
                
                // Adjust body padding to prevent content overlap
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    document.body.style.paddingTop = (navbar.offsetHeight + banner.offsetHeight) + 'px';
                }
            }, 100);
        } else {
            console.log('Reminder should not be shown. Reason:', data.message || 'Unknown');
            banner.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error checking reminder:', error);
        banner.style.display = 'none';
    });
    
    // Handle dismiss button
    if (dismissBtn) {
        dismissBtn.addEventListener('click', function() {
        // Animate out
        banner.style.animation = 'slideUp 0.3s ease-in forwards';
        
        fetch('{{ route("profile.reminder.dismiss") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setTimeout(() => {
                    banner.style.display = 'none';
                    document.body.style.paddingTop = '';
                }, 300);
            }
        })
        .catch(error => {
            console.error('Error dismissing reminder:', error);
        });
        });
    } else {
        console.warn('Dismiss button not found');
    }
});

// Add slide up animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-120%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endif