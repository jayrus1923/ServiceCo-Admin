<?php
// Navigation Bar - FIXED AUTO-HIDE SYNC
?>
<nav class="navbar" id="navbar">
    <style>
        /* Navigation Bar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 240px; /* Default sidebar width */
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

        /* When sidebar is collapsed or auto-hide */
        .sidebar.collapsed ~ .navbar,
        .sidebar.auto-hide ~ .navbar {
            left: 70px !important;
        }

        /* IMPORTANT: Fixed auto-hide hover */
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
            letter-spacing: 0.5px;
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
            letter-spacing: 0.5px;
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
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .initials-fallback {
            width: 100%;
            height: 100%;
            background-color: #347433;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
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

        /* Profile Dropdown Menu */
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
        }

        .dropdown-header .profile-info {
            flex: 1;
        }

        .dropdown-header .profile-name {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .dropdown-header .profile-role {
            font-size: 13px;
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
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: #347433;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        .dropdown-item span {
            font-size: 14px;
            font-weight: 500;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 8px 0;
        }

        /* Responsive navbar */
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
                margin-left: 5px;
                padding-left: 5px;
            }

            .logo-main {
                font-size: 18px;
            }

            .logo-subtitle {
                font-size: 11px;
            }

            .profile-dropdown {
                width: 250px;
                right: -10px;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                left: 0;
            }

            .logo-text {
                display: none;
            }

            .page-title {
                font-size: 16px;
                margin-left: 0;
                border-left: none;
            }

            .profile-dropdown {
                position: fixed;
                top: 70px;
                right: 10px;
                left: 10px;
                width: auto;
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
                'ProfileSettings.php' => 'Profile Settings',
                'Notification.php' => 'Notifications'
            ];
            echo isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : 'Dashboard';
            ?>
        </div>
    </div>

    <div class="navbar-right">
        <div class="profile-container" id="profileContainer">
            <div class="profile-pic">
                <img src="Images/profile_icon.png" alt="Admin Profile" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="initials-fallback" style="display: none;">AU</div>
            </div>
            <div class="profile-info">
                <div class="profile-name">Admin User</div>
                <div class="profile-role">System Administrator</div>
            </div>
            <button class="profile-dropdown-btn" id="dropdownBtn">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>

        <!-- Profile Dropdown Menu -->
        <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header">
                <div class="profile-pic">
                    <img src="Images/profile_icon.png" alt="Admin Profile" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="initials-fallback" style="display: none;">AU</div>
                </div>
                <div class="profile-info">
                    <div class="profile-name">Admin User</div>
                    <div class="profile-role">System Administrator</div>
                </div>
            </div>
            
            <div class="dropdown-menu">
                <a href="ProfileSettings.php" class="dropdown-item" id="profileSettingsBtn">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile Settings</span>
                </a>
                <a href="AccountSettings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Account Settings</span>
                </a>
                <a href="Notification.php" class="dropdown-item">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
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
        // Function to update navbar position
        function updateNavbarPosition() {
            const sidebar = document.getElementById('sidebar');
            const navbar = document.getElementById('navbar');
            
            if (sidebar && navbar) {
                const isCollapsed = sidebar.classList.contains('collapsed');
                const isAutoHide = sidebar.classList.contains('auto-hide');
                const isHovered = sidebar.matches(':hover') && isAutoHide;
                
                if (isAutoHide && isHovered) {
                    // Auto-hide and hovered = expanded
                    navbar.style.left = '240px';
                } else if (isCollapsed || isAutoHide) {
                    // Collapsed or auto-hide (not hovered) = collapsed
                    navbar.style.left = '70px';
                } else {
                    // Manual expanded
                    navbar.style.left = '240px';
                }
                
                console.log('Navbar position updated to:', navbar.style.left);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const profileContainer = document.getElementById('profileContainer');
            const profileDropdown = document.getElementById('profileDropdown');
            const dropdownBtn = document.getElementById('dropdownBtn');
            
            // Toggle dropdown menu
            if (profileContainer) {
                profileContainer.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (profileDropdown) {
                        profileDropdown.classList.toggle('active');
                        dropdownBtn.classList.toggle('rotate');
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (profileDropdown && profileContainer && 
                    !profileContainer.contains(e.target) && 
                    !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                    dropdownBtn.classList.remove('rotate');
                }
            });
            
            // Update navbar position on load
            updateNavbarPosition();
            
            // Handle profile image error
            const profileImages = document.querySelectorAll('.profile-pic img');
            profileImages.forEach(img => {
                img.onerror = function() {
                    this.style.display = 'none';
                    const initials = this.nextElementSibling;
                    if (initials && initials.classList.contains('initials-fallback')) {
                        initials.style.display = 'flex';
                    }
                };
            });
            
            // Listen for sidebar events
            document.addEventListener('sidebarToggled', updateNavbarPosition);
            document.addEventListener('sidebarModeChanged', updateNavbarPosition);
            
            // Listen for sidebar hover events
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.addEventListener('mouseenter', function() {
                    if (this.classList.contains('auto-hide')) {
                        setTimeout(updateNavbarPosition, 50); // Small delay for transition
                    }
                });
                
                sidebar.addEventListener('mouseleave', function() {
                    if (this.classList.contains('auto-hide')) {
                        setTimeout(updateNavbarPosition, 50);
                    }
                });
            }
        });
        
        // Make update function available globally
        window.updateNavbarPosition = updateNavbarPosition;
        
        // Update navbar on window resize
        window.addEventListener('resize', updateNavbarPosition);
    </script>
</nav>