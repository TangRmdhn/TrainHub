// ============================================
// TrainHub - Calendar Page
// Jadwalkan & track workout plans
// ============================================

// === AUTH CHECK ===
if (typeof requireAuth === "function" && !requireAuth()) {
  throw new Error("Unauthorized");
}

// === STATE ===
let currentDate = new Date();
let schedules = {}; // Store semua jadwal (key: date string)
let allPlans = []; // Store list plan buat dropdown
let selectedDate = null;

// === DOM ELEMENTS ===
const calendarGrid = document.getElementById("calendar-grid");
const currentMonthSpan = document.getElementById("current-month");
const prevMonthBtn = document.getElementById("prev-month");
const nextMonthBtn = document.getElementById("next-month");
const scheduleModal = document.getElementById("schedule-modal");
const planSelect = document.getElementById("plan-select");
const saveScheduleBtn = document.getElementById("save-schedule-btn");
const cancelModalBtn = document.getElementById("cancel-modal-btn");
const selectedDateSpan = document.getElementById("selected-date");

// === MAIN FUNCTIONS ===

/**
 * Initialize calendar
 */
async function init() {
  Loading.show("Memuat kalender...");

  try {
    await loadPlans();
    await loadSchedules();
    renderCalendar();

    // Check if ada query param untuk auto-schedule
    checkAutoSchedule();
  } catch (error) {
    console.error("Init error:", error);
    Toast.error("Gagal memuat kalender");
  } finally {
    Loading.hide();
  }
}

/**
 * Check if ada query param untuk auto-schedule
 * Contoh: calendar.html?schedule_plan=123
 *
 * @returns {boolean} True jika ada auto-schedule
 */
function checkAutoSchedule() {
  const urlParams = new URLSearchParams(window.location.search);
  const planIdToSchedule = urlParams.get("schedule_plan");

  if (!planIdToSchedule) {
    return false; // Nggak ada auto-schedule
  }

  console.group("📅 Auto-Schedule Mode");
  console.log("Plan ID:", planIdToSchedule);

  // 1. Pre-select plan di dropdown
  planSelect.value = planIdToSchedule;

  // 2. Validate plan exists
  const planExists = allPlans.some((p) => p.plan_id == planIdToSchedule);

  if (!planExists) {
    console.error("❌ Plan not found");
    Toast.error("Plan tidak ditemukan");
    Loading.hide();
    console.groupEnd();
    return false;
  }

  // 3. Get plan name untuk display
  const plan = allPlans.find((p) => p.plan_id == planIdToSchedule);
  console.log("Plan:", plan.plan_name);

  // 4. Clean URL (remove query param)
  window.history.replaceState({}, document.title, window.location.pathname);

  // 5. Show helper UI
  showAutoScheduleHelper(plan);

  // 6. Highlight available dates
  highlightAvailableDates();

  console.log("✅ Ready - User can click any date");
  console.groupEnd();

  return true;
}

/**
 * Show helper banner untuk auto-schedule mode
 */
function showAutoScheduleHelper(plan) {
  // Create helper banner
  const banner = document.createElement("div");
  banner.id = "auto-schedule-banner";
  banner.className =
    "fixed top-20 left-1/2 transform -translate-x-1/2 z-50 animate-slideDown";
  banner.innerHTML = `
        <div class="bg-orange-600 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3 max-w-lg">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold">Jadwalkan: ${plan.plan_name}</p>
                <p class="text-sm opacity-90">Klik tanggal di kalender untuk menjadwalkan plan ini</p>
            </div>
            <button onclick="closeAutoScheduleBanner()" class="text-white hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

  document.body.appendChild(banner);

  // Auto-hide after 8 seconds
  setTimeout(() => {
    closeAutoScheduleBanner();
  }, 8000);
}

/**
 * Close auto-schedule banner
 */
function closeAutoScheduleBanner() {
  const banner = document.getElementById("auto-schedule-banner");
  if (banner) {
    banner.style.animation = "slideUp 0.3s ease-out";
    setTimeout(() => banner.remove(), 300);
  }
}

/**
 * Highlight available dates (visual cue)
 */
function highlightAvailableDates() {
  const cells = calendarGrid.querySelectorAll(".calendar-cell[data-date]");
  const today = new Date().toISOString().split("T")[0];

  cells.forEach((cell) => {
    const dateStr = cell.getAttribute("data-date");
    const schedule = schedules[dateStr];

    // Highlight empty cells di future (termasuk hari ini)
    if (!schedule && dateStr >= today) {
      cell.classList.add("available-slot");

      // Add tooltip
      cell.setAttribute("title", "Klik untuk jadwalkan");

      // Make it more obvious
      cell.style.borderColor = "#f97316";
      cell.style.borderWidth = "2px";
      cell.style.borderStyle = "dashed";
    }
  });
}
/**
 * Load semua plans untuk dropdown
 */
async function loadPlans() {
  try {
    const result = await window.api.get("/plans");

    allPlans = result.data || [];
    window.allPlans = allPlans;
    window.schedules = schedules;
        
        console.log('📋 Loaded plans:', allPlans.length);
    // Populate dropdown
    planSelect.innerHTML = '<option value="">-- Pilih Plan --</option>';

    if (allPlans.length === 0) {
      planSelect.innerHTML +=
        '<option value="" disabled>Belum ada plan. Buat dulu di AI Planner</option>';
    } else {
      allPlans.forEach((plan) => {
        planSelect.innerHTML += `<option value="${plan.plan_id}">${plan.plan_name}</option>`;
      });
    }
  } catch (error) {
    console.error("Error loading plans:", error);
    Toast.error("Gagal memuat daftar plan");
  }
}

async function loadSchedules() {
    const month = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`;
    
    console.group('📅 Loading Schedules');
    console.log('Month:', month);
    
    try {
        const result = await window.api.get(`/calendar?month=${month}`);
        
        console.log('✅ API Response:', result);
        console.log('Raw data:', result.data);
        
        // Convert array ke object dengan key = tanggal
        schedules = {};
        const dataArray = result.data || [];
        
        dataArray.forEach((s) => {
            // ✅ FIX: Normalize date format (remove timestamp if exists)
            const dateOnly = s.scheduled_date.split(' ')[0]; // "2025-11-16 00:00:00" -> "2025-11-16"
            
            // ✅ FIX: Convert is_completed to proper boolean
            const isCompleted = s.is_completed === 1 || s.is_completed === '1' || s.is_completed === true;
            
            schedules[dateOnly] = {
                ...s,
                scheduled_date: dateOnly,
                is_completed: isCompleted
            };
            
            console.log(`Added schedule for ${dateOnly}:`, schedules[dateOnly]);
        });
        
        console.log('📊 Final schedules object:', schedules);
        console.log('Total schedules:', Object.keys(schedules).length);
        console.groupEnd();
        
    } catch (error) {
        console.error('❌ Error loading schedules:', error);
        console.groupEnd();
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
            
            // ✅ FIX: Add complete button jika belum selesai DAN bukan hari yang sudah lewat
            if (schedule.is_completed === false && dateStr >= today) {
                content += `
                    <button 
                        class="complete-btn mt-2 w-full bg-green-600 hover:bg-green-700 text-white text-xs py-1 px-2 rounded transition-colors"
                        data-schedule-id="${schedule.schedule_id}"
                        data-date="${dateStr}"
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
        
        html += `<div class="${cellClass}" data-date="${dateStr}">${content}</div>`;
    }
    
    calendarGrid.innerHTML = html;
    
    // Attach event listeners AFTER render
    attachCellEventListeners();
}

/**
 * Attach click event listeners ke calendar cells
 */
function attachCellEventListeners() {
  console.group("🔧 Attaching Event Listeners");
  // Tambahkan di awal attachCellEventListeners()
  console.log("=== DEBUGGING ===");
  console.log("Calendar Grid:", calendarGrid);
  console.log(
    "Cells:",
    calendarGrid.querySelectorAll(".calendar-cell[data-date]")
  );
  console.log(
    "Complete Buttons:",
    calendarGrid.querySelectorAll(".complete-btn")
  );

  const cells = calendarGrid.querySelectorAll(".calendar-cell[data-date]");
  console.log("Found cells:", cells.length);

  cells.forEach((cell, index) => {
    const dateStr = cell.getAttribute("data-date");
    console.log(`Attaching listener to cell ${index}:`, dateStr);

    // ✅ Event listener untuk CELL (buka modal)
    cell.addEventListener("click", (e) => {
      console.log("Cell clicked:", dateStr);

      // ⚠️ Jangan buka modal kalau yang diklik adalah tombol complete
      if (
        e.target.classList.contains("complete-btn") ||
        e.target.closest(".complete-btn")
      ) {
        console.log("Complete button clicked, ignoring cell click");
        return;
      }

      console.log("Opening modal for:", dateStr);
      openScheduleModal(dateStr);
    });
  });

  // ✅ Event listener untuk TOMBOL COMPLETE (terpisah!)
  const completeBtns = calendarGrid.querySelectorAll(".complete-btn");
  console.log("Found complete buttons:", completeBtns.length);

  completeBtns.forEach((btn, index) => {
    const scheduleId = btn.getAttribute("data-schedule-id");
    const dateStr = btn.getAttribute("data-date");

    console.log(`Attaching listener to complete button ${index}:`, {
      scheduleId,
      dateStr,
    });

    btn.addEventListener("click", (e) => {
      e.stopPropagation(); // ⚠️ Penting! Cegah event bubbling ke cell
      console.log("Complete button clicked:", { scheduleId, dateStr });
      markAsComplete(scheduleId, dateStr);
    });
  });

  console.groupEnd();
}

/**
 * Open schedule modal
 */
function openScheduleModal(dateStr) {
  selectedDate = dateStr;

  // Format tanggal yang lebih readable
  selectedDateSpan.textContent = new Date(dateStr).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  // Jika sudah ada jadwal, pre-select plan-nya
  if (schedules[dateStr]) {
    planSelect.value = schedules[dateStr].plan_id;
  } else {
    planSelect.value = "";
  }

  // Show modal
  scheduleModal.classList.add("active");
}

// **
//  * Save schedule - AUTO-SCHEDULE ALL DAYS
//  */
saveScheduleBtn.addEventListener('click', async () => {
    const planId = planSelect.value;
    
    if (!planId) {
        Toast.warning('Pilih plan dulu!');
        planSelect.focus();
        return;
    }
    
    // Get plan details untuk tahu ada berapa hari
    const plan = allPlans.find(p => p.plan_id == planId);
    
    if (!plan) {
        Toast.error('Plan tidak ditemukan');
        return;
    }
    
    // Fetch plan detail untuk dapat schedule
    Loading.show('Memuat detail plan...');
    
    try {
        const planDetail = await window.api.get(`/plans?plan_id=${planId}`);
        const schedule = planDetail.data.schedule || [];
        
        if (schedule.length === 0) {
            Toast.error('Plan tidak memiliki jadwal latihan');
            Loading.hide();
            return;
        }
        
        // Confirm dengan user
        const confirmed = confirm(
            `Jadwalkan plan "${plan.plan_name}" dengan ${schedule.length} hari latihan?\n\n` +
            `Mulai dari: ${new Date(selectedDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}\n` +
            `Selesai di: ${new Date(new Date(selectedDate).getTime() + (schedule.length - 1) * 24 * 60 * 60 * 1000).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`
        );
        
        if (!confirmed) {
            Loading.hide();
            return;
        }
        
        Loading.show(`Menjadwalkan ${schedule.length} hari latihan...`);
        
        // Schedule semua hari
        let successCount = 0;
        let errorCount = 0;
        const errors = [];
        
        for (let i = 0; i < schedule.length; i++) {
            // Hitung tanggal untuk setiap hari
            const date = new Date(selectedDate);
            date.setDate(date.getDate() + i);
            const dateStr = date.toISOString().split('T')[0];
            
            try {
                await window.api.post('/calendar', {
                    plan_id: parseInt(planId),
                    date: dateStr
                });
                successCount++;
                console.log(`✅ Day ${i + 1} scheduled for ${dateStr}`);
            } catch (error) {
                errorCount++;
                errors.push(`Day ${i + 1}: ${error.message}`);
                console.error(`❌ Failed to schedule Day ${i + 1}:`, error);
            }
        }
        
        // Show result
        if (successCount === schedule.length) {
            Toast.success(`Berhasil menjadwalkan ${successCount} hari latihan!`);
        } else if (successCount > 0) {
            Toast.warning(`Berhasil: ${successCount}, Gagal: ${errorCount}`);
            console.warn('Errors:', errors);
        } else {
            Toast.error('Gagal menjadwalkan semua hari latihan');
        }
        
        // Close modal
        scheduleModal.classList.remove('active');
        
        // Reload schedules
        await loadSchedules();
        renderCalendar();
        
    } catch (error) {
        console.error('Error scheduling plan:', error);
        Toast.error('Gagal menjadwalkan: ' + error.message);
    } finally {
        Loading.hide();
    }
});

/**
 * Cancel modal
 */
cancelModalBtn.addEventListener("click", () => {
  scheduleModal.classList.remove("active");
});

// Close modal saat klik backdrop
scheduleModal.addEventListener("click", (e) => {
  if (e.target === scheduleModal) {
    scheduleModal.classList.remove("active");
  }
});

// Close modal dengan ESC key
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && scheduleModal.classList.contains("active")) {
    scheduleModal.classList.remove("active");
  }
});

/**
 * Mark schedule as complete
 */
async function markAsComplete(scheduleId, dateStr) {
  const schedule = schedules[dateStr];

  if (!schedule) {
    Toast.error("Schedule tidak ditemukan");
    return;
  }

  const confirmed = confirm(
    `Tandai workout "${schedule.plan_name}" sebagai selesai?\n\nSelamat! 🎉`
  );

  if (!confirmed) return;

  Loading.show("Menyimpan progress...");

  try {
    await window.api.put(`/calendar/complete?schedule_id=${scheduleId}`, {
      notes: null, // Bisa ditambahin input catatan nanti
    });

    Toast.success("Workout selesai! Keep it up! 💪");

    // Update local state
    schedules[dateStr].is_completed = 1;

    // Re-render calendar
    renderCalendar();

    // Celebration effect
    showCelebration();
  } catch (error) {
    console.error("Error marking complete:", error);
    Toast.error("Gagal menyimpan: " + error.message);
  } finally {
    Loading.hide();
  }
}
/**
 * Show celebration animation (bonus UX)
 */
function showCelebration() {
  // Simple confetti effect
  const confetti = document.createElement("div");
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
  confetti.textContent = "🎉";

  const style = document.createElement("style");
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
prevMonthBtn.addEventListener("click", async () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  Loading.show("Memuat bulan sebelumnya...");

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
nextMonthBtn.addEventListener("click", async () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  Loading.show("Memuat bulan berikutnya...");

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
  const planIdToSchedule = urlParams.get("schedule_plan");

  if (planIdToSchedule) {
    // Pre-select plan di dropdown
    planSelect.value = planIdToSchedule;

    // Auto-open modal untuk hari ini
    const today = new Date().toISOString().split("T")[0];
    openScheduleModal(today);

    // Clean URL (remove query param)
    window.history.replaceState({}, document.title, window.location.pathname);

    Toast.info("Pilih tanggal untuk jadwalkan plan ini");
  }
}

/**
 * Quick navigation ke hari ini
 */
function goToToday() {
  currentDate = new Date();
  loadSchedules().then(renderCalendar);
  Toast.info("Kembali ke bulan ini");
}

// Bisa tambahin tombol "Today" di HTML nanti
// <button onclick="goToToday()">Hari Ini</button>

// === INITIALIZATION ===
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", init);
} else {
  init();
}
