// ============================================
// TrainHub - Calendar Page
// Jadwalkan & track workout plans
// ============================================

// === AUTH CHECK ===
if (typeof requireAuth === 'function' && !requireAuth()) {
    throw new Error('Unauthorized');
}

// === STATE ===
let currentDate = new Date();
let schedules = {}; // Store semua jadwal (key: date string)
let allPlans = []; // Store list plan buat dropdown
let selectedDate = null;

// === DOM ELEMENTS ===
const calendarGrid = document.getElementById('calendar-grid');
const currentMonthSpan = document.getElementById('current-month');
const prevMonthBtn = document.getElementById('prev-month');
const nextMonthBtn = document.getElementById('next-month');
const scheduleModal = document.getElementById('schedule-modal');
const planSelect = document.getElementById('plan-select');
const saveScheduleBtn = document.getElementById('save-schedule-btn');
const cancelModalBtn = document.getElementById('cancel-modal-btn');
const selectedDateSpan = document.getElementById('selected-date');

// === MAIN FUNCTIONS ===

/**
 * Initialize calendar
 */
async function init() {
    Loading.show('Memuat kalender...');
    
    try {
        await loadPlans();
        await loadSchedules();
        renderCalendar();
        
        // Check if ada query param untuk auto-schedule
        checkAutoSchedule();
        
    } catch (error) {
        console.error('Init error:', error);
        Toast.error('Gagal memuat kalender');
    } finally {
        Loading.hide();
    }
}

/**
 * Load semua plans untuk dropdown
 */
async function loadPlans() {
    try {
        const result = await window.api.get('/plans');
        allPlans = result.data || [];
        
        // Populate dropdown
        planSelect.innerHTML = '<option value="">-- Pilih Plan --</option>';
        
        if (allPlans.length === 0) {
            planSelect.innerHTML += '<option value="" disabled>Belum ada plan. Buat dulu di AI Planner</option>';
        } else {
            allPlans.forEach(plan => {
                planSelect.innerHTML += `<option value="${plan.plan_id}">${plan.plan_name}</option>`;
            });
        }
        
    } catch (error) {
        console.error('Error loading plans:', error);
        Toast.error('Gagal memuat daftar plan');
    }
}

/**
 * Load schedules untuk bulan yang dipilih
 */
async function loadSchedules() {
    const month = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`;
    
    try {
        const result = await window.api.get(`/calendar?month=${month}`);
        
        // Convert array ke object dengan key = tanggal
        schedules = {};
        (result.data || []).forEach(s => {
            schedules[s.scheduled_date] = s;
        });
        
    } catch (error) {
        console.error('Error loading schedules:', error);
        // Jangan throw error, biar calendar tetep render (kosong)
        schedules = {};
    }
}

/**
 * Render calendar grid
 */
function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update header bulan
    currentMonthSpan.textContent = new Date(year, month).toLocaleDateString('id-ID', { 
        month: 'long', 
        year: 'numeric' 
    });
    
    // Get first day & total days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    let html = '';
    
    // Empty cells sebelum hari pertama (Senin = 0)
    const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;
    for (let i = 0; i < adjustedFirstDay; i++) {
        html += `<div class="calendar-cell opacity-50 cursor-default hover:bg-gray-800"></div>`;
    }
    
    // Render setiap hari
    const today = new Date().toISOString().split('T')[0];
    
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const schedule = schedules[dateStr];
        const isToday = dateStr === today;
        const isPast = new Date(dateStr) < new Date(today);
        
        // Base classes
        let cellClass = 'calendar-cell';
        if (isToday) cellClass += ' today';
        if (schedule) cellClass += schedule.is_completed ? ' completed' : ' has-schedule';
        if (isPast && !schedule) cellClass += ' opacity-50';
        
        // Cell content
        let content = `<div class="text-sm font-semibold mb-1">${day}</div>`;
        
        if (schedule) {
            const statusIcon = schedule.is_completed 
                ? `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                     <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                   </svg>`
                : `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                   </svg>`;
            
            const statusClass = schedule.is_completed ? 'text-green-400' : 'text-orange-400';
            
            content += `
                <div class="text-xs truncate mb-1" title="${schedule.plan_name}">
                    ${schedule.plan_name}
                </div>
                <div class="flex items-center gap-1 ${statusClass}">
                    ${statusIcon}
                    <span class="text-xs font-medium">
                        ${schedule.is_completed ? 'Selesai' : 'Dijadwalkan'}
                    </span>
                </div>
            `;
            
            // Add complete button jika belum selesai
            if (!schedule.is_completed && !isPast) {
                content += `
                    <button 
                        onclick="event.stopPropagation(); markAsComplete(${schedule.schedule_id}, '${dateStr}')"
                        class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white text-xs py-1 px-2 rounded transition-colors"
                        title="Tandai selesai"
                    >
                        ✓ Selesai
                    </button>
                `;
            }
        } else if (!isPast) {
            content += `
                <div class="text-xs text-gray-500 mt-1">
                    Klik untuk jadwalkan
                </div>
            `;
        }
        
        html += `<div class="${cellClass}" onclick="openScheduleModal('${dateStr}')">${content}</div>`;
    }
    
    calendarGrid.innerHTML = html;
}

/**
 * Open schedule modal
 */
function openScheduleModal(dateStr) {
    selectedDate = dateStr;
    
    // Format tanggal yang lebih readable
    selectedDateSpan.textContent = new Date(dateStr).toLocaleDateString('id-ID', { 
        weekday: 'long',
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
    
    // Jika sudah ada jadwal, pre-select plan-nya
    if (schedules[dateStr]) {
        planSelect.value = schedules[dateStr].plan_id;
    } else {
        planSelect.value = '';
    }
    
    // Show modal
    scheduleModal.classList.add('active');
}

/**
 * Save schedule
 */
saveScheduleBtn.addEventListener('click', async () => {
    const planId = planSelect.value;
    
    if (!planId) {
        Toast.warning('Pilih plan dulu!');
        planSelect.focus();
        return;
    }
    
    Loading.show('Menyimpan jadwal...');
    
    try {
        await window.api.post('/calendar', {
            plan_id: parseInt(planId),
            date: selectedDate
        });
        
        Toast.success('Jadwal berhasil disimpan!');
        
        // Close modal
        scheduleModal.classList.remove('active');
        
        // Reload schedules
        await loadSchedules();
        renderCalendar();
        
    } catch (error) {
        console.error('Error saving schedule:', error);
        Toast.error('Gagal menyimpan jadwal: ' + error.message);
    } finally {
        Loading.hide();
    }
});

/**
 * Cancel modal
 */
cancelModalBtn.addEventListener('click', () => {
    scheduleModal.classList.remove('active');
});

// Close modal saat klik backdrop
scheduleModal.addEventListener('click', (e) => {
    if (e.target === scheduleModal) {
        scheduleModal.classList.remove('active');
    }
});

// Close modal dengan ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && scheduleModal.classList.contains('active')) {
        scheduleModal.classList.remove('active');
    }
});

/**
 * Mark schedule as complete
 */
async function markAsComplete(scheduleId, dateStr) {
    const schedule = schedules[dateStr];
    
    if (!schedule) {
        Toast.error('Schedule tidak ditemukan');
        return;
    }
    
    const confirmed = confirm(
        `Tandai workout "${schedule.plan_name}" sebagai selesai?\n\nSelamat! 🎉`
    );
    
    if (!confirmed) return;
    
    Loading.show('Menyimpan progress...');
    
    try {
        await window.api.put(`/calendar/complete?schedule_id=${scheduleId}`, {
            notes: null // Bisa ditambahin input catatan nanti
        });
        
        Toast.success('Workout selesai! Keep it up! 💪');
        
        // Update local state
        schedules[dateStr].is_completed = 1;
        
        // Re-render calendar
        renderCalendar();
        
        // Bisa tambahin confetti effect di sini 🎉
        showCelebration();
        
    } catch (error) {
        console.error('Error marking complete:', error);
        Toast.error('Gagal menyimpan: ' + error.message);
    } finally {
        Loading.hide();
    }
}

/**
 * Show celebration animation (bonus UX)
 */
function showCelebration() {
    // Simple confetti effect
    const confetti = document.createElement('div');
    confetti.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 5rem;
        animation: celebrate 1s ease-out;
        pointer-events: none;
        z-index: 10000;
    `;
    confetti.textContent = '🎉';
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes celebrate {
            0% {
                transform: translate(-50%, -50%) scale(0);
                opacity: 1;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(1) translateY(-100px);
                opacity: 0;
            }
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(confetti);
    
    setTimeout(() => {
        confetti.remove();
        style.remove();
    }, 1000);
}

/**
 * Month navigation - Previous
 */
prevMonthBtn.addEventListener('click', async () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    Loading.show('Memuat bulan sebelumnya...');
    
    try {
        await loadSchedules();
        renderCalendar();
    } finally {
        Loading.hide();
    }
});

/**
 * Month navigation - Next
 */
nextMonthBtn.addEventListener('click', async () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    Loading.show('Memuat bulan berikutnya...');
    
    try {
        await loadSchedules();
        renderCalendar();
    } finally {
        Loading.hide();
    }
});

/**
 * Check if ada query param untuk auto-schedule
 * Contoh: calendar.html?schedule_plan=123
 */
function checkAutoSchedule() {
    const urlParams = new URLSearchParams(window.location.search);
    const planIdToSchedule = urlParams.get('schedule_plan');
    
    if (planIdToSchedule) {
        // Pre-select plan di dropdown
        planSelect.value = planIdToSchedule;
        
        // Auto-open modal untuk hari ini
        const today = new Date().toISOString().split('T')[0];
        openScheduleModal(today);
        
        // Clean URL (remove query param)
        window.history.replaceState({}, document.title, window.location.pathname);
        
        Toast.info('Pilih tanggal untuk jadwalkan plan ini');
    }
}

/**
 * Quick navigation ke hari ini
 */
function goToToday() {
    currentDate = new Date();
    loadSchedules().then(renderCalendar);
    Toast.info('Kembali ke bulan ini');
}

// Bisa tambahin tombol "Today" di HTML nanti
// <button onclick="goToToday()">Hari Ini</button>

// === INITIALIZATION ===
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}