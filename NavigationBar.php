<?php
$userUid = isset($_SESSION['user_uid']) ? $_SESSION['user_uid'] : (isset($_COOKIE['user_uid']) ? $_COOKIE['user_uid'] : '');
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : (isset($_COOKIE['user_email']) ? urldecode($_COOKIE['user_email']) : '');
?>
<nav class="navbar" id="navbar">
    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 240px;
            right: 0;
            height: 70px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed ~ .navbar,
        .sidebar.auto-hide ~ .navbar {
            left: 70px !important;
        }

        .sidebar.auto-hide:hover ~ .navbar {
            left: 240px !important;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-main {
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        }

        .logo-service {
            color: #347433;
        }

        .logo-co {
            color: #FCB53B;
        }

        .logo-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-left: 10px;
            padding-left: 10px;
            border-left: 1px solid #e5e7eb;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 25px;
            position: relative;
        }

        .profile-container {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .profile-container:hover {
            background-color: #f8f9fa;
        }

        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #347433;
            overflow: hidden;
            background-color: #347433;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 15px;
        }

        .profile-role {
            font-size: 13px;
            color: #6b7280;
        }

        .profile-dropdown-btn {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            font-size: 18px;
            transition: transform 0.3s;
            padding: 5px;
        }

        .profile-dropdown-btn.rotate {
            transform: rotate(180deg);
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            width: 280px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 20px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .dropdown-header .profile-pic {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .dropdown-header .profile-info {
            flex: 1;
        }

        .dropdown-header .profile-name {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .dropdown-menu {
            padding: 10px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #4b5563;
            text-decoration: none;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: #347433;
        }

        .dropdown-item i {
            width: 20px;
            font-size: 18px;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 8px 0;
        }

        @media (max-width: 768px) {
            .navbar {
                left: 70px;
                padding: 0 20px;
            }
            .profile-info {
                display: none;
            }
            .page-title {
                font-size: 18px;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                left: 0;
            }
            .logo-text {
                display: none;
            }
        }
    </style>

    <div class="navbar-left">
        <div class="logo-container">
            <img src="Images/tricycle.png" alt="Tricycle Booking Logo" class="logo-image">
            <div class="logo-text">
                <div class="logo-main">
                    <span class="logo-service">Service</span><span class="logo-co">Co</span>
                </div>
                <div class="logo-subtitle">Tricycle Booking System</div>
            </div>
        </div>
        <div class="page-title" id="pageTitle">
            <?php 
            $currentPage = basename($_SERVER['PHP_SELF']);
            $pageTitles = [
                'Dashboard.php' => 'Dashboard',
                'UserManagement.php' => 'User Management',
                'App.php' => 'App Management',
                'Report.php' => 'Reports',
                'AccountSettings.php' => 'Account Settings',
                'ProfileSettings.php' => 'Profile Settings'
            ];
            echo isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : 'Dashboard';
            ?>
        </div>
    </div>

    <div class="navbar-right">
        <div class="profile-container" id="profileContainer">
            <div class="profile-pic" id="profilePic">
                <span id="profileInitials">AU</span>
                <img id="profileImage" src="" alt="" style="display: none;">
            </div>
            <div class="profile-info">
                <div class="profile-name" id="profileName">Loading...</div>
                <div class="profile-role" id="profileRole">Administrator</div>
            </div>
            <button class="profile-dropdown-btn" id="dropdownBtn">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>

        <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header">
                <div class="profile-pic" id="dropdownProfilePic">
                    <span id="dropdownInitials">AU</span>
                    <img id="dropdownImage" src="" alt="" style="display: none;">
                </div>
                <div class="profile-info">
                    <div class="profile-name" id="dropdownName">Loading...</div>
                    <div class="profile-role" id="dropdownRole">Administrator</div>
                </div>
            </div>
            
            <div class="dropdown-menu">
                <a href="ProfileSettings.php" class="dropdown-item">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile Settings</span>
                </a>
                <a href="AccountSettings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Account Settings</span>
                </a>
                
                <div class="dropdown-divider"></div>
                
                <a href="Logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof firebase === "undefined") {
                console.error("Firebase not loaded. Make sure Firebase is loaded in main page.");
                return;
            }
            const db = firebase.firestore();
            const userUid = "<?php echo $userUid; ?>";
            if (!userUid) {
                console.log("No user UID found");
                return;
            }
            async function loadUserData() {
                db.collection("users").doc(userUid).onSnapshot(function(doc) {
                    if (!doc.exists) return;
                    const data = doc.data();
                    const firstName = data.firstName || "Admin";
                    const lastName = data.lastName || "User";
                    const fullName = `${firstName} ${lastName}`.trim();
                    const initials = firstName.charAt(0).toUpperCase() + lastName.charAt(0).toUpperCase();
                    const profilePicUrl = data.profilePicUrl || null;
                    document.getElementById("profileName").textContent = fullName;
                    document.getElementById("dropdownName").textContent = fullName;
                    document.getElementById("profileRole").textContent = data.position || "System Administrator";
                    document.getElementById("dropdownRole").textContent = data.position || "System Administrator";
                    if (profilePicUrl) {
                        document.getElementById("profileInitials").style.display = "none";
                        document.getElementById("profileImage").src = profilePicUrl;
                        document.getElementById("profileImage").style.display = "block";
                        document.getElementById("dropdownInitials").style.display = "none";
                        document.getElementById("dropdownImage").src = profilePicUrl;
                        document.getElementById("dropdownImage").style.display = "block";
                    } else {
                        document.getElementById("profileInitials").textContent = initials;
                        document.getElementById("profileInitials").style.display = "flex";
                        document.getElementById("profileImage").style.display = "none";
                        document.getElementById("dropdownInitials").textContent = initials;
                        document.getElementById("dropdownInitials").style.display = "flex";
                        document.getElementById("dropdownImage").style.display = "none";
                    }
                });
            }
            function toggleDropdown() {
                document.getElementById("profileDropdown").classList.toggle("active");
                document.getElementById("dropdownBtn").classList.toggle("rotate");
            }
            document.getElementById("profileContainer").addEventListener("click", toggleDropdown);
            loadUserData();
        });
    </script>
</nav>