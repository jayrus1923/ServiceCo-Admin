<?php

$preservedMode = 'manual';

if (isset($_COOKIE['last_sidebar_mode']) && in_array($_COOKIE['last_sidebar_mode'], ['manual', 'auto-hide'])) {
    $preservedMode = $_COOKIE['last_sidebar_mode'];
    setcookie('last_sidebar_mode', $preservedMode, time() + 3600, '/');
}

$_SESSION['sidebar_mode'] = $preservedMode;

if (isset($_SESSION['user_uid'])) {
    header('Location: Dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceCo Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f7f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .left-section {
            flex: 1;
            background: linear-gradient(135deg, #2c642c 0%, #347433 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .bg-pattern-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: url('tricycle.png');
            background-size: 200px;
            background-repeat: repeat;
            background-position: center;
        }

        .tricycle-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            height: 80%;
            opacity: 0.05;
            background-image: url('tricycle.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            z-index: 2;
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .logo-icon img {
            width: 50px;
            height: 50px;
        }

        .logo-text {
            text-align: left;
        }

        .logo-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .logo-title .service {
            color: white;
        }

        .logo-title .co {
            color: #FCB53B;
        }

        .logo-subtitle {
            font-size: 1.8rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .tagline {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.4rem;
            font-weight: 300;
            line-height: 1.6;
            text-align: center;
            max-width: 500px;
            margin-top: 10px;
            padding: 0 20px;
        }

        .right-section {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            z-index: 1;
        }

        .right-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.03;
            background-image: url('Images/tringle_icon.png');
            background-size: 300px;
            background-repeat: repeat;
        }

        .login-form-container {
            width: 100%;
            max-width: 450px;
            z-index: 2;
            animation: fadeIn 0.8s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-title {
            color: #347433;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .login-form {
            background-color: #ffffff;
            padding: 0;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #374151;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            z-index: 1;
        }

        .form-input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #347433;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(52, 116, 51, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #347433;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background-color: #347433;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-button:hover {
            background-color: #2c642c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 116, 51, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button i {
            font-size: 18px;
        }

        .forgot-password {
            display: block;
            text-align: center;
            color: #347433;
            text-decoration: none;
            margin-top: 20px;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .forgot-password:hover {
            color: #2c642c;
            text-decoration: underline;
        }

        .forgot-password.disabled {
            color: #9ca3af;
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-size: 0.95rem;
        }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-size: 0.95rem;
        }

        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-size: 0.95rem;
        }

        .timer-text {
            font-weight: 600;
            color: #dc2626;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1024px) {
            .login-container {
                flex-direction: column;
            }

            .left-section, .right-section {
                flex: none;
                width: 100%;
                min-height: 50vh;
            }

            .right-section {
                box-shadow: 0 -5px 30px rgba(0, 0, 0, 0.1);
            }
        }

        @media (max-width: 768px) {
            .logo-title {
                font-size: 2.5rem;
            }
            .logo-subtitle {
                font-size: 1.2rem;
            }
            .tagline {
                font-size: 1.1rem;
            }
            .login-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 480px) {
            .logo-title {
                font-size: 2rem;
            }
            .logo-subtitle {
                font-size: 1rem;
            }
            .tagline {
                font-size: 0.9rem;
            }
            .login-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="left-section">
            <div class="bg-pattern-top"></div>
            <div class="tricycle-bg"></div>
            
            <div class="logo-container">
                <div class="logo-wrapper">
                    <div class="logo-icon">
                        <img src="Images/tricycle.png" alt="Tricycle Icon">
                    </div>
                    <div class="logo-text">
                        <h1 class="logo-title">
                            <span class="service">Service</span><span class="co">Co</span>
                        </h1>
                        <div class="logo-subtitle">Tricycle Booking System</div>
                    </div>
                </div>
                <p class="tagline">Efficient, Reliable, and Convenient Tricycle Booking Service</p>
            </div>
        </div>

        <div class="right-section">
            <div class="right-bg-pattern"></div>
            <div class="login-form-container">
                <div class="login-header">
                    <h2 class="login-title">Login Here</h2>
                    <p class="login-subtitle">Enter your credentials to access admin panel</p>
                </div>

                <div id="errorMessage" class="error-message"></div>
                <div id="successMessage" class="success-message"></div>
                <div id="warningMessage" class="warning-message"></div>

                <form id="loginForm" class="login-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" class="form-input" placeholder="admin@serviceco.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" class="form-input" placeholder="Enter your Password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-button">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>

                    <a id="forgotPass" class="forgot-password">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script>
        const preservedMode = '<?php echo $preservedMode; ?>';
        localStorage.setItem('sidebarMode', preservedMode);
        localStorage.setItem('persistent_sidebar_mode', preservedMode);
    </script>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";
        import { getFirestore, doc, getDoc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBpA5CT6Z1U880I8DgMS3pgkeFuKgQPoyk",
            authDomain: "serviceco-37c60.firebaseapp.com",
            projectId: "serviceco-37c60",
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);

        const COOLDOWN_MINUTES = 3;
        const COOLDOWN_MS = COOLDOWN_MINUTES * 60 * 1000;
        let cooldownTimer = null;

        function checkCooldown() {
            const lastResetTime = localStorage.getItem('lastPasswordResetTime');
            const forgotPassLink = document.getElementById('forgotPass');
            const warningMessage = document.getElementById('warningMessage');
            
            if (lastResetTime) {
                const elapsed = Date.now() - parseInt(lastResetTime);
                const remaining = COOLDOWN_MS - elapsed;
                
                if (remaining > 0) {
                    startCooldown(remaining);
                    return true;
                } else {
                    localStorage.removeItem('lastPasswordResetTime');
                }
            }
            
            forgotPassLink.classList.remove('disabled');
            warningMessage.style.display = 'none';
            return false;
        }

        function startCooldown(remainingTime) {
            const forgotPassLink = document.getElementById('forgotPass');
            const warningMessage = document.getElementById('warningMessage');
            
            forgotPassLink.classList.add('disabled');
            
            if (cooldownTimer) {
                clearInterval(cooldownTimer);
            }
            
            const updateTimer = () => {
                const currentRemaining = parseInt(localStorage.getItem('passwordResetRemaining') || remainingTime);
                const mins = Math.floor(currentRemaining / 60000);
                const secs = Math.floor((currentRemaining % 60000) / 1000);
                
                warningMessage.innerHTML = `<i class="fas fa-clock"></i> Please wait ${mins}:${secs.toString().padStart(2, '0')} minutes before requesting another password reset.`;
                warningMessage.style.display = 'block';
                
                if (currentRemaining <= 0) {
                    clearInterval(cooldownTimer);
                    localStorage.removeItem('lastPasswordResetTime');
                    localStorage.removeItem('passwordResetRemaining');
                    forgotPassLink.classList.remove('disabled');
                    warningMessage.style.display = 'none';
                }
                
                localStorage.setItem('passwordResetRemaining', currentRemaining - 1000);
            };
            
            updateTimer();
            cooldownTimer = setInterval(updateTimer, 1000);
        }

        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        document.getElementById("loginForm").addEventListener("submit", async (e) => {
            e.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const errorMessage = document.getElementById("errorMessage");
            const loginButton = document.querySelector(".login-button");

            errorMessage.style.display = "none";
            errorMessage.textContent = "";
            document.getElementById("successMessage").style.display = "none";
            document.getElementById("warningMessage").style.display = "none";

            const originalText = loginButton.innerHTML;
            loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
            loginButton.disabled = true;

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;

                const docSnap = await getDoc(doc(db, "users", user.uid));

                if (docSnap.exists() && docSnap.data().role === "Admin") {
                    const sidebarMode = localStorage.getItem('persistent_sidebar_mode') || 
                                       localStorage.getItem('sidebarMode') || 
                                       preservedMode || 
                                       'manual';
                    
                    const response = await fetch('Dashboard.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'set_session=1&uid=' + user.uid + '&email=' + encodeURIComponent(user.email)
                    });
                    
                    localStorage.setItem('user_uid', user.uid);
                    localStorage.setItem('user_email', user.email);
                    localStorage.setItem('user_role', 'Admin');
                    localStorage.setItem('is_logged_in', 'true');
                    localStorage.setItem('login_timestamp', Date.now().toString());
                    
                    localStorage.setItem('sidebarMode', sidebarMode);
                    localStorage.setItem('persistent_sidebar_mode', sidebarMode);
                    document.cookie = `last_sidebar_mode=${sidebarMode}; path=/; max-age=3600`;
                    document.cookie = `user_uid=${user.uid}; path=/; max-age=3600`;
                    document.cookie = `user_email=${encodeURIComponent(user.email)}; path=/; max-age=3600`;
                    
                    localStorage.removeItem('just_logged_out');
                    
                    setTimeout(() => {
                        window.location.href = "Dashboard.php";
                    }, 100);
                } else {
                    errorMessage.textContent = "Access Denied: Admin access only";
                    errorMessage.style.display = "block";
                    await auth.signOut();
                    localStorage.clear();
                }

            } catch (error) {
                let errorMsg = "Login failed. Please check your credentials.";
                
                switch (error.code) {
                    case 'auth/invalid-email':
                        errorMsg = "Invalid email format";
                        break;
                    case 'auth/user-not-found':
                        errorMsg = "No account found with this email";
                        break;
                    case 'auth/wrong-password':
                        errorMsg = "Incorrect password";
                        break;
                    case 'auth/too-many-requests':
                        errorMsg = "Too many failed attempts. Try again later";
                        break;
                }
                
                errorMessage.textContent = errorMsg;
                errorMessage.style.display = "block";
            } finally {
                loginButton.innerHTML = originalText;
                loginButton.disabled = false;
            }
        });

        document.getElementById("forgotPass").addEventListener("click", async (e) => {
            e.preventDefault();

            const lastResetTime = localStorage.getItem('lastPasswordResetTime');
            if (lastResetTime) {
                const elapsed = Date.now() - parseInt(lastResetTime);
                if (elapsed < COOLDOWN_MS) {
                    const remaining = COOLDOWN_MS - elapsed;
                    startCooldown(remaining);
                    return;
                }
            }

            const email = document.getElementById("email").value;
            const errorMessage = document.getElementById("errorMessage");
            const successMessage = document.getElementById("successMessage");
            const warningMessage = document.getElementById("warningMessage");
            const forgotPassLink = document.getElementById("forgotPass");

            errorMessage.style.display = "none";
            successMessage.style.display = "none";
            warningMessage.style.display = "none";

            if (!email) {
                errorMessage.textContent = "Please enter your email address first";
                errorMessage.style.display = "block";
                return;
            }

            forgotPassLink.classList.add('disabled');
            const originalText = forgotPassLink.innerHTML;
            forgotPassLink.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                await sendPasswordResetEmail(auth, email);
                
                const resetTime = Date.now();
                localStorage.setItem('lastPasswordResetTime', resetTime);
                localStorage.setItem('passwordResetRemaining', COOLDOWN_MS);
                
                successMessage.textContent = "Password reset email sent! Check your inbox.";
                successMessage.style.display = "block";
                
                startCooldown(COOLDOWN_MS);
                
            } catch (error) {
                errorMessage.textContent = "Failed to send reset email. Make sure email is correct.";
                errorMessage.style.display = "block";
                forgotPassLink.classList.remove('disabled');
            } finally {
                forgotPassLink.innerHTML = originalText;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('just_logged_out') === 'true') {
                document.getElementById('email').value = '';
                document.getElementById('password').value = '';
                localStorage.removeItem('just_logged_out');
            }
            
            const logoImg = document.querySelector('.logo-icon img');
            if (logoImg) {
                logoImg.onerror = function() {
                    this.style.display = 'none';
                    const parent = this.parentElement;
                    parent.innerHTML = '<i class="fas fa-taxi" style="font-size: 40px; color: #347433;"></i>';
                };
            }
            
            checkCooldown();
        });
    </script>
</body>
</html>