<?php
session_start();

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

setcookie('last_sidebar_mode', $sidebarMode, time() + 3600, '/');

session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #347433 0%, #2c642c 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .logout-container {
            text-align: center;
            color: white;
            padding: 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .spinner {
            width: 80px;
            height: 80px;
            border: 6px solid rgba(255, 255, 255, 0.2);
            border-top: 6px solid white;
            border-radius: 50%;
            animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            margin: 0 auto 30px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        h2 {
            margin: 0 0 15px 0;
            font-weight: 700;
            font-size: 28px;
            letter-spacing: 1px;
        }
        
        p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin-top: 30px;
            overflow: hidden;
        }
        
        .progress {
            width: 0%;
            height: 100%;
            background: white;
            border-radius: 3px;
            animation: progress 1.5s linear forwards;
        }
        
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="spinner"></div>
        <h2>Securely Logging Out</h2>
        <p>Please wait while we clear your session and redirect you to the login page.</p>
        <div class="progress-bar">
            <div class="progress"></div>
        </div>
    </div>

    <script>
        const savedSidebarMode = localStorage.getItem('sidebarMode') || '<?php echo $sidebarMode; ?>';
        
        localStorage.setItem('persistent_sidebar_mode', savedSidebarMode);
        
        const preservedMode = localStorage.getItem('persistent_sidebar_mode');
        
        localStorage.clear();
        sessionStorage.clear();
        
        if (preservedMode) {
            localStorage.setItem('persistent_sidebar_mode', preservedMode);
            localStorage.setItem('sidebarMode', preservedMode);
        }
        
        document.cookie.split(";").forEach(function(c) {
            if (c.trim().startsWith('last_sidebar_mode')) return;
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });
        
        (async function() {
            try {
                const { getAuth, signOut } = await import("https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js");
                
                const firebaseConfig = {
                    apiKey: "AIzaSyBpA5CT6Z1U880I8DgMS3pgkeFuKgQPoyk",
                    authDomain: "serviceco-37c60.firebaseapp.com",
                    projectId: "serviceco-37c60",
                };
                
                const app = firebase.apps[0] || firebase.initializeApp(firebaseConfig);
                const auth = getAuth(app);
                
                await signOut(auth);
            } catch (error) {
                console.log('Firebase logout handled');
            }
            
            setTimeout(() => {
                localStorage.setItem('just_logged_out', 'true');
                
                if (preservedMode) {
                    localStorage.setItem('sidebarMode', preservedMode);
                }
                
                window.location.replace('Login.php?logout=' + Date.now());
            }, 1800);
        })();
    </script>
</body>
</html>