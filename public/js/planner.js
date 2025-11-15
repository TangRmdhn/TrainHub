// File: public/js/planner.js

// Ambil elemen dari app.html
const generateBtn = document.getElementById('ai-generate-btn');
const promptText = document.getElementById('ai-prompt');
const resultDiv = document.getElementById('ai-result');

generateBtn.addEventListener('click', async () => {
    const prompt = promptText.value;
    if (!prompt) {
        alert('Masukkan prompt dulu');
        return;
    }

    // Tampilkan loading...
    resultDiv.innerHTML = `<p class="text-yellow-500">Bentar, AI lagi mikir...</p>`;
    generateBtn.disabled = true;
    generateBtn.textContent = 'Generating...';

    try {
        const token = localStorage.getItem('jwt_token');

        // === LANGKAH 1: Panggil AI (Kode lu udah bener) ===
        const responseAI = await fetch('/api/ai/generate-plan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ prompt: prompt })
        });

        if (!responseAI.ok) {
            const err = await responseAI.json();
            throw new Error(`AI Gagal: ${err.message || responseAI.statusText}`);
        }

        const planData = await responseAI.json(); 
        console.log('AI Plan Received:', planData);

        // === LANGKAH 2: [BARU] Render plan ke HTML ===
        // (Sekarang kita panggil fungsi di bawah)
        renderPlanToHTML(planData);

        // === LANGKAH 3: [BARU & KRUSIAL] Simpen plan ini ke DB! ===
        // Kita panggil API kedua pake JSON yang barusan kita dapet
        console.log('Menyimpan plan ke database...');
        
        const responseSave = await fetch('/api/workout-plan', { // <-- Ini endpoint Fitur 3
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token // Pake token yang sama
            },
            body: JSON.stringify(planData) // Kirim SEMUA JSON dari AI
        });

        if (!responseSave.ok) {
            // Kalo gagal simpen, kasih tau, tapi plan tetep tampil
            const errSave = await responseSave.json();
            console.warn('Gagal menyimpan plan:', errSave.message);
            alert('Plan berhasil dibuat, tapi gagal disimpan di akun Anda.');
        } else {
            const saveResult = await responseSave.json();
            console.log('Plan berhasil disimpan!', saveResult);
            // Tambahin notif sukses di atas plan-nya
            const successMsg = document.createElement('p');
            successMsg.className = 'text-green-500 font-semibold mb-2';
            successMsg.textContent = `Rencana "${planData.plan_name}" berhasil disimpan! (ID: ${saveResult.plan_id})`;
            resultDiv.prepend(successMsg);
        }

    } catch (error) {
        console.error('Gagal generate plan:', error);
        resultDiv.innerHTML = `<p class="text-red-500">Error: ${error.message}</p>`;
    } finally {
        // Balikin tombolnya
        generateBtn.disabled = false;
        generateBtn.textContent = 'Generate Rencana';
    }
});


/**
 * [BARU] Fungsi buat nampilin JSON plan ke HTML
 * Ini buat ngisi div#ai-result
 */
function renderPlanToHTML(planData) {
    let html = '';

    // Tampilkan Nama Plan dan Catatan
    html += `<h3 class="text-2xl font-bold text-orange-500 mb-2">${planData.plan_name}</h3>`;
    html += `<p class="text-gray-300 italic mb-6">${planData.notes}</p>`;

    // Loop setiap HARI (schedule)
    planData.schedule.forEach(day => {
        html += `<div class="mb-4 p-4 bg-gray-800 border border-gray-700 rounded-lg">`;
        html += `<h4 class="text-xl font-semibold text-white mb-3">${day.day}</h4>`;
        
        if (day.exercises.length === 0) {
            html += `<p class="text-gray-400">Rest day.</p>`;
        } else {
            html += `<ul class="list-disc list-inside space-y-2">`;
            // Loop setiap LATIHAN (exercises)
            day.exercises.forEach(ex => {
                let detail = ex.duration_seconds 
                    ? `${ex.duration_seconds} detik` 
                    : `${ex.sets} set x ${ex.reps} reps`;
                
                html += `<li class="text-gray-200">
                           <span class="font-medium text-white">${ex.exercise_name}</span> - ${detail}
                         </li>`;
            });
            html += `</ul>`;
        }
        html += `</div>`;
    });

    resultDiv.innerHTML = html;
}