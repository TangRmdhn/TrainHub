// File: public/js/planner.js

// Auth check (pake fungsi dari main.js)
if (typeof requireAuth === 'function' && !requireAuth()) {
    throw new Error('Unauthorized');
}

// Ambil elemen dari app.html
const generateBtn = document.getElementById('ai-generate-btn');
const promptText = document.getElementById('ai-prompt');
const resultDiv = document.getElementById('ai-result');

generateBtn.addEventListener('click', async () => {
    const prompt = promptText.value.trim();
    
    if (!prompt) {
        Toast.warning('Masukkan prompt dulu!');
        promptText.focus();
        return;
    }

    // Tampilkan loading state
    resultDiv.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="spinner mr-3"></div>
            <p class="text-yellow-500">Bentar, AI lagi mikir...</p>
        </div>
    `;
    generateBtn.disabled = true;
    generateBtn.innerHTML = `
        <div class="flex items-center justify-center gap-2">
            <div class="spinner"></div>
            <span>Generating...</span>
        </div>
    `;
    
    // Show loading overlay
    Loading.show('AI sedang membuat rencana latihan...');

    try {
        // === LANGKAH 1: Panggil AI (pake API helper dari main.js) ===
        const planData = await window.api.post('/ai/generate-plan', { prompt: prompt });
        
        console.log('AI Plan Received:', planData);

        // === LANGKAH 2: Render plan ke HTML ===
        renderPlanToHTML(planData);

        // === LANGKAH 3: Simpen plan ini ke DB ===
        console.log('Menyimpan plan ke database...');
        
        try {
            const saveResult = await window.api.post('/workout-plan', planData);
            
            console.log('Plan berhasil disimpan!', saveResult);
            
            // Tampilkan success notification
            Toast.success(`Rencana "${planData.plan_name}" berhasil disimpan!`);
            
            // Tambahin badge success di atas plan
            const successMsg = document.createElement('div');
            successMsg.className = 'bg-green-900/50 border border-green-600 text-green-400 px-4 py-3 rounded-lg mb-4 flex items-center gap-2';
            successMsg.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">Rencana berhasil disimpan!</p>
                    <p class="text-sm text-green-300">ID: ${saveResult.plan_id} • Lihat di <a href="plans.html" class="underline">My Plans</a></p>
                </div>
            `;
            resultDiv.prepend(successMsg);
            
        } catch (saveError) {
            // Kalo gagal simpen, kasih warning tapi plan tetep tampil
            console.warn('Gagal menyimpan plan:', saveError.message);
            
            Toast.warning('Plan berhasil dibuat, tapi gagal disimpan di akun Anda.');
            
            // Tampilkan warning badge
            const warningMsg = document.createElement('div');
            warningMsg.className = 'bg-yellow-900/50 border border-yellow-600 text-yellow-400 px-4 py-3 rounded-lg mb-4 flex items-center gap-2';
            warningMsg.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">Plan dibuat tapi tidak tersimpan</p>
                    <p class="text-sm text-yellow-300">${saveError.message}</p>
                </div>
            `;
            resultDiv.prepend(warningMsg);
        }

    } catch (error) {
        console.error('Gagal generate plan:', error);
        
        // Tampilkan error message
        resultDiv.innerHTML = `
            <div class="bg-red-900/50 border border-red-600 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">Gagal membuat rencana</p>
                    <p class="text-sm text-red-300">${error.message}</p>
                </div>
            </div>
        `;
        
        Toast.error('Gagal membuat rencana: ' + error.message);
        
    } finally {
        // Balikin tombolnya
        generateBtn.disabled = false;
        generateBtn.textContent = 'Generate Rencana';
        Loading.hide();
    }
});


/**
 * Fungsi buat nampilin JSON plan ke HTML
 * Ini buat ngisi div#ai-result
 */
function renderPlanToHTML(planData) {
    let html = '';

    // Tampilkan Nama Plan dan Catatan
    html += `<h3 class="text-2xl font-bold text-orange-500 mb-2">${planData.plan_name}</h3>`;
    html += `<p class="text-gray-300 italic mb-6">${planData.notes}</p>`;

    // Loop setiap HARI (schedule)
    planData.schedule.forEach((day, index) => {
        html += `<div class="card mb-4">`;
        html += `
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xl font-semibold text-white">${day.day}</h4>
                <span class="text-xs text-gray-500 bg-gray-800 px-3 py-1 rounded-full">
                    Day ${index + 1}
                </span>
            </div>
        `;
        
        if (day.exercises.length === 0) {
            html += `
                <div class="text-center py-6 bg-gray-800/50 rounded-lg">
                    <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-gray-400 font-medium">Rest Day</p>
                    <p class="text-xs text-gray-500 mt-1">Recover and recharge</p>
                </div>
            `;
        } else {
            html += `<div class="space-y-3">`;
            
            // Loop setiap LATIHAN (exercises)
            day.exercises.forEach((ex, exIndex) => {
                let detail = '';
                if (ex.duration_seconds) {
                    detail = `<span class="text-orange-400 font-semibold">${ex.duration_seconds}s</span>`;
                } else {
                    detail = `<span class="text-orange-400 font-semibold">${ex.sets} × ${ex.reps}</span>`;
                }
                
                html += `
                    <div class="flex items-start gap-3 p-3 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
                        <div class="flex-shrink-0 w-8 h-8 bg-orange-900/30 text-orange-500 rounded-full flex items-center justify-center font-bold text-sm">
                            ${exIndex + 1}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-white text-truncate">${ex.exercise_name}</p>
                            <p class="text-sm text-gray-400">${detail}</p>
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
        }
        html += `</div>`;
    });

    resultDiv.innerHTML = html;
};