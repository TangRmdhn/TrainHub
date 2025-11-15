<?php
// File: test_api.php
// Simple API tester tanpa perlu Postman/curl

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>TrainHub API Tester</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            background: #1a1a1a; 
            color: #00ff00;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #ff6600; }
        .test-section { 
            background: #2a2a2a; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px;
            border-left: 4px solid #ff6600;
        }
        button { 
            background: #ff6600; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }
        button:hover { background: #cc5200; }
        pre { 
            background: #0a0a0a; 
            padding: 15px; 
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #333;
        }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        input, textarea { 
            width: 100%; 
            padding: 8px; 
            margin: 5px 0;
            background: #333;
            border: 1px solid #555;
            color: #00ff00;
            border-radius: 4px;
        }
        label { display: block; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔥 TrainHub API Tester</h1>
        
        <!-- TEST 1: REGISTER -->
        <div class="test-section">
            <h2>1️⃣ Test Register</h2>
            <form id="registerForm">
                <label>Username:</label>
                <input type="text" name="username" value="testuser<?php echo rand(1, 999); ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="test<?php echo rand(1, 999); ?>@trainhub.com" required>
                
                <label>Password:</label>
                <input type="password" name="password" value="password123" required>
                
                <br><br>
                <button type="submit">🚀 Test Register</button>
            </form>
            <pre id="registerResult">Waiting for test...</pre>
        </div>
        
        <!-- TEST 2: LOGIN -->
        <div class="test-section">
            <h2>2️⃣ Test Login</h2>
            <form id="loginForm">
                <label>Email:</label>
                <input type="email" name="email" value="demo@trainhub.com" required>
                
                <label>Password:</label>
                <input type="password" name="password" value="password123" required>
                
                <br><br>
                <button type="submit">🔑 Test Login</button>
            </form>
            <pre id="loginResult">Waiting for test...</pre>
        </div>
        
        <!-- TEST 3: GET PLANS (Protected) -->
        <div class="test-section">
            <h2>3️⃣ Test Get Plans (Protected Endpoint)</h2>
            <label>JWT Token (dari login):</label>
            <input type="text" id="tokenInput" placeholder="Paste token from login test">
            <br><br>
            <button onclick="testGetPlans()">📋 Test Get Plans</button>
            <pre id="plansResult">Waiting for test...</pre>
        </div>
        
        <!-- TEST 4: AI Generate Plan -->
        <div class="test-section">
            <h2>4️⃣ Test AI Generate Plan</h2>
            <label>Prompt:</label>
            <textarea id="aiPrompt" rows="3">Buatkan 3 hari plan untuk bulking dengan fokus upper body</textarea>
            <br><br>
            <button onclick="testAIGenerate()">🤖 Test AI Generate</button>
            <pre id="aiResult">Waiting for test...</pre>
        </div>
    </div>

    <script>
        const API_BASE = '/trainhub/api';
        
        // Helper: Display result
        function displayResult(elementId, data, isSuccess = true) {
            const el = document.getElementById(elementId);
            el.className = isSuccess ? 'success' : 'error';
            el.textContent = JSON.stringify(data, null, 2);
        }
        
        // Test 1: Register
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(`${API_BASE}/auth/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                displayResult('registerResult', {
                    status_code: response.status,
                    ...result
                }, response.ok);
                
            } catch (error) {
                displayResult('registerResult', { error: error.message }, false);
            }
        });
        
        // Test 2: Login
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                displayResult('loginResult', {
                    status_code: response.status,
                    ...result
                }, response.ok);
                
                // Auto-fill token jika login berhasil
                if (result.token) {
                    document.getElementById('tokenInput').value = result.token;
                }
                
            } catch (error) {
                displayResult('loginResult', { error: error.message }, false);
            }
        });
        
        // Test 3: Get Plans (Protected)
        async function testGetPlans() {
            const token = document.getElementById('tokenInput').value;
            
            if (!token) {
                displayResult('plansResult', { error: 'Token required! Login dulu.' }, false);
                return;
            }
            
            try {
                const response = await fetch(`${API_BASE}/plans`, {
                    method: 'GET',
                    headers: { 
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                const result = await response.json();
                displayResult('plansResult', {
                    status_code: response.status,
                    ...result
                }, response.ok);
                
            } catch (error) {
                displayResult('plansResult', { error: error.message }, false);
            }
        }
        
        // Test 4: AI Generate
        async function testAIGenerate() {
            const token = document.getElementById('tokenInput').value;
            const prompt = document.getElementById('aiPrompt').value;
            
            if (!token) {
                displayResult('aiResult', { error: 'Token required! Login dulu.' }, false);
                return;
            }
            
            displayResult('aiResult', { status: 'Generating... (may take 5-10 seconds)' }, true);
            
            try {
                const response = await fetch(`${API_BASE}/ai/generate-plan`, {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ prompt })
                });
                
                const result = await response.json();
                displayResult('aiResult', {
                    status_code: response.status,
                    ...result
                }, response.ok);
                
            } catch (error) {
                displayResult('aiResult', { error: error.message }, false);
            }
        }
    </script>
</body>
</html>