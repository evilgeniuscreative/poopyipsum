<?php
// login.php - Login form and authentication logic
session_start();

// Configuration - correct credentials for poopyipsum.com
$VALID_USERNAME = 'admin';
$VALID_PASSWORD_HASH = password_hash('timesheet2024', PASSWORD_DEFAULT);

function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    if ($username === $VALID_USERNAME && password_verify($password, $VALID_PASSWORD_HASH)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true, 'redirect' => '/index.html']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

// Check if already logged in - redirect to timesheet
if (isAuthenticated()) {
    header('Location: /index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheet Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .login-btn {
            width: 100%;
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .login-btn:hover {
            background-color: #2563eb;
        }
        
        .login-btn:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }
        
        .error-message {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Timesheet Login</h1>
        
        <div id="error" class="error-message"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-input" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="login-btn" id="loginBtn">Login</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('loginBtn');
            const error = document.getElementById('error');
            
            btn.disabled = true;
            btn.textContent = 'Logging in...';
            error.style.display = 'none';
            
            try {
                const response = await fetch('/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = result.redirect || '/index.html';
                } else {
                    error.textContent = result.error || 'Login failed';
                    error.style.display = 'block';
                }
            } catch (err) {
                error.textContent = 'Network error. Please try again.';
                error.style.display = 'block';
            }
            
            btn.disabled = false;
            btn.textContent = 'Login';
        });
    </script>
</body>
</html>