// File: public/js/plans.js

if (typeof requireAuth === 'function' && !requireAuth()) {
    throw new Error('Unauthorized');
}

const plansContainer = document.getElementById('plans-container');

// Fetch semua plans
async function loadPlans() {
    Loading.show('Memuat rencana latihan...');
    
    try {
        const result = await window.api.get('/plans');
        allPlans = result.data || [];
        renderPlans(allPlans);
        
    } catch (error) {
        console.error('Error loading plans:', error);
        plansContainer.innerHTML = `
            <div class="col-span-full">
                <div class="bg-red-900/50 border border-red-600 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Gagal memuat rencana</p>
                        <p class="text-sm text-red-300">${error.message}</p>
                    </div>
                </div>
            </div>
        `;
        Toast.error('Gagal memuat rencana');
        
    } finally {
        Loading.hide();
    }
}


function renderPlans(plans) {
    if (plans.length === 0) {
        plansContainer.innerHTML = `
            <div class="col-span-full text-center py-16">
                <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-white mb-2">Belum Ada Rencana</h3>
                <p class="text-gray-400 mb-6">Buat rencana latihan pertama Anda dengan AI Planner!</p>
                <a href="app.html" class="btn btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Buat Rencana Baru
                </a>
            </div>
        `;
        return;
    }
    
    let html = plans.map(plan => {
        const date = Utils.formatDate(plan.created_at, {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
        
        return `
            <div class="card group">
                <!-- Header dengan icon -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0 pr-2">
                        <h3 class="text-xl font-semibold text-white mb-1 text-truncate group-hover:text-orange-400 transition-colors">
                            ${plan.plan_name}
                        </h3>
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            ${date}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-10 h-10 bg-orange-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Notes -->
                <p class="text-sm text-gray-400 mb-4 text-truncate-2 min-h-[2.5rem]">
                    ${plan.notes ? plan.notes : '<em class="text-gray-500">Tidak ada catatan</em>'}
                </p>
                
                <!-- Actions -->
                <div class="card-actions">
                    <button onclick="viewPlan(${plan.plan_id})" 
                            class="btn btn-primary btn-sm flex-1 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Detail
                    </button>
                    <button onclick="deletePlan(${plan.plan_id}, '${plan.plan_name.replace(/'/g, "\\'")}')" 
                            class="btn btn-danger btn-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    plansContainer.innerHTML = html;
}

async function viewPlan(planId) {
    Loading.show('Memuat detail...');
    
    try {
        const result = await window.api.get(`/plans?plan_id=${planId}`);
        const plan = result.data;
        
        // Tampilkan di modal
        showPlanModal(plan);
        
        // Atau redirect ke halaman detail (kalo mau bikin page terpisah)
        // window.location.href = `plan-detail.html?id=${planId}`;
        
    } catch (error) {
        console.error('Error viewing plan:', error);
        Toast.error('Gagal memuat detail plan');
    } finally {
        Loading.hide();
    }
}

function showPlanModal(plan) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.id = 'plan-detail-modal';
    
    let scheduleHTML = '';
    plan.schedule.forEach((day, index) => {
        scheduleHTML += `
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-white">${day.day}</h4>
                    <span class="text-xs text-gray-500 bg-gray-800 px-2 py-1 rounded">
                        Day ${index + 1}
                    </span>
                </div>
                ${day.exercises.length === 0 
                    ? '<p class="text-sm text-gray-400 italic py-2">Rest day</p>'
                    : `
                        <div class="space-y-2">
                            ${day.exercises.map((ex, i) => {
                                const detail = ex.duration_seconds 
                                    ? `${ex.duration_seconds}s`
                                    : `${ex.sets} × ${ex.reps}`;
                                return `
                                    <div class="flex items-center gap-2 text-sm bg-gray-800/50 p-2 rounded">
                                        <span class="flex-shrink-0 w-6 h-6 bg-orange-900/30 text-orange-500 rounded-full flex items-center justify-center text-xs font-bold">
                                            ${i + 1}
                                        </span>
                                        <span class="flex-1 text-gray-200">${ex.exercise_name}</span>
                                        <span class="text-orange-400 font-semibold">${detail}</span>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    `
                }
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div class="modal-content max-w-2xl">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-white mb-2">${plan.plan_name}</h3>
                    <p class="text-sm text-gray-400">${plan.notes || 'Tidak ada catatan'}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Dibuat: ${Utils.formatDate(plan.created_at)}
                    </p>
                </div>
                <button onclick="closePlanModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="max-h-96 overflow-y-auto mb-4">
                ${scheduleHTML}
            </div>
            
            <div class="flex gap-2 pt-4 border-t border-gray-800">
                <button onclick="schedulePlanToCalendar(${plan.plan_id})" 
                        class="btn btn-primary flex-1 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Jadwalkan
                </button>
                <button onclick="closePlanModal()" class="btn btn-secondary">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closePlanModal();
    });
}

function closePlanModal() {
    const modal = document.getElementById('plan-detail-modal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 200);
    }
}

function schedulePlanToCalendar(planId) {
    window.location.href = `calendar.html?schedule_plan=${planId}`;
}

async function deletePlan(planId, planName) {
    // Confirm dengan nama plan-nya
    const confirmed = confirm(
        `Yakin mau hapus plan "${planName}"?\n\nTindakan ini tidak bisa dibatalkan.`
    );
    
    if (!confirmed) return;
    
    Loading.show('Menghapus rencana...');
    
    try {
        await window.api.delete(`/plans?plan_id=${planId}`);
        
        Toast.success(`Plan "${planName}" berhasil dihapus!`);
        
        // Remove dari state
        allPlans = allPlans.filter(p => p.plan_id !== planId);
        
        // Re-render
        renderPlans(allPlans);
        
    } catch (error) {
        console.error('Error deleting plan:', error);
        Toast.error('Gagal menghapus plan: ' + error.message);
    } finally {
        Loading.hide();
    }
}

// Logout
document.getElementById('logout-btn').addEventListener('click', () => {
    localStorage.clear();
    window.location.href = 'login.html';
});

// Load on page ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadPlans);
} else {
    loadPlans();
}