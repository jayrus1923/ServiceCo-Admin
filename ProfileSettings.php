<?php
// ProfileSettings.php - WITH REAL-TIME AUTO-HIDE SUPPORT
session_start();

// Handle sidebar mode saving (if posted from this page)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sidebar_mode'])) {
    $_SESSION['sidebar_mode'] = $_POST['sidebar_mode'];
    echo json_encode(['success' => true, 'message' => 'Sidebar mode saved']);
    exit;
}

// Get current sidebar mode
$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

// Simulated admin data
$adminData = [
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@tricyclebooking.com',
    'username' => 'admin',
    'phone' => '+63 912-345-6789',
    'position' => 'System Administrator',
    'last_login' => 'Today, 9:30 AM',
    'profile_pic' => 'Images/profile_icon.png' // Changed to image file in Images folder
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | Tricycle Booking Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: #1f2937;
            overflow-x: hidden;
        }

        /* Profile Content - WITH AUTO-HIDE SUPPORT */
        .profile-content {
            margin-top: 70px;
            padding: 25px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        /* Manual mode - collapsed state */
        .sidebar.collapsed ~ .profile-content {
            margin-left: 70px;
            width: calc(100% - 70px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* AUTO-HIDE MODE - NORMAL STATE (collapsed) */
        .sidebar.auto-hide ~ .profile-content {
            margin-left: 70px !important;
            width: calc(100% - 70px) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* AUTO-HIDE MODE - HOVER STATE (expanded) */
        .sidebar.auto-hide:hover ~ .profile-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        /* Page Header */
        .page-header {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
        }

        /* Main Layout */
        .profile-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        @media (max-width: 1024px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Settings Cards */
        .settings-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            height: fit-content;
        }

        .settings-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-card h3 i {
            color: #347433;
        }

        /* Profile Header */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .profile-pic-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #347433;
            font-size: 36px;
            font-weight: bold;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            border: 3px solid #347433;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-pic-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .initials-fallback {
            font-size: 36px;
            font-weight: bold;
            color: #347433;
        }

        .profile-info h2 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .profile-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .profile-info p i {
            color: #94a3b8;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #475569;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            background-color: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #347433;
            box-shadow: 0 0 0 3px rgba(52, 116, 51, 0.1);
        }

        .form-group input:disabled {
            background-color: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 30px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #347433;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        /* Button Styles */
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #347433;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2c622b;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .btn-change-pic {
            background-color: #f8fafc;
            color: #475569;
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .btn-change-pic:hover {
            background-color: #e2e8f0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 400px;
            border-radius: 12px;
            overflow: hidden;
            animation: modalSlide 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalSlide {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 20px;
            background-color: #dc2626;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-body {
            padding: 25px;
            text-align: center;
        }

        .modal-message {
            margin-bottom: 20px;
            font-size: 15px;
            color: #475569;
            line-height: 1.5;
        }

        .modal-icon {
            font-size: 48px;
            color: #dc2626;
            margin-bottom: 15px;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-danger {
            background-color: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .preview-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #347433;
            margin: 0 auto 15px;
            display: none;
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: inline-block;
            padding: 12px 24px;
            background-color: #347433;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            margin-bottom: 15px;
            transition: all 0.2s;
        }

        .file-label:hover {
            background-color: #2c622b;
            transform: translateY(-1px);
        }

        /* Success Message (Top Right) */
        .success-message {
            position: fixed;
            top: 90px;
            right: 25px;
            background-color: #347433;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            z-index: 999;
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(52, 116, 51, 0.3);
        }

        .error-notification {
            position: fixed;
            top: 90px;
            right: 25px;
            background-color: #dc2626;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            z-index: 999;
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 20px;
            }
            
            .sidebar.collapsed ~ .profile-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .sidebar.auto-hide ~ .profile-content {
                margin-left: 70px !important;
                width: calc(100% - 70px) !important;
            }
            
            .settings-card {
                padding: 20px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .success-message, .error-notification {
                top: 70px;
                right: 15px;
                left: 15px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .profile-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
                margin-top: 60px;
            }
            
            .sidebar.collapsed ~ .profile-content,
            .sidebar.auto-hide ~ .profile-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            .modal-content {
                width: 95%;
                max-width: 95%;
                margin: 15px;
            }
            
            .modal-buttons {
                flex-direction: column;
            }
        }

        /* Smooth sidebar transition */
        .profile-content, .sidebar, .navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body data-sidebar-mode="<?php echo $sidebarMode; ?>">
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <!-- Profile Content -->
    <div class="profile-content">
        <div class="page-header">
            <h1 class="page-title">Profile Settings</h1>
            <p class="page-subtitle">Manage your personal information and password</p>
        </div>

        <div class="profile-layout">
            <!-- Left Card: Personal Information -->
            <div class="settings-card">
                <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
                
                <div class="profile-header">
                    <div class="profile-pic-large" id="currentProfilePic">
                        <?php 
                        // Display profile_icon.png from Images folder
                        $profilePicPath = $adminData['profile_pic'];
                        ?>
                        <img src="<?php echo $profilePicPath; ?>" alt="Profile Picture" 
                             onerror="this.style.display='none'; document.getElementById('initialsFallback').style.display='flex';">
                        <div class="initials-fallback" id="initialsFallback" style="display: none;">
                            <?php echo substr($adminData['first_name'], 0, 1) . substr($adminData['last_name'], 0, 1); ?>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo $adminData['first_name'] . ' ' . $adminData['last_name']; ?></h2>
                        <p><i class="fas fa-user-tag"></i> <?php echo $adminData['position']; ?></p>
                        <p><i class="fas fa-clock"></i> Last login: <?php echo $adminData['last_login']; ?></p>
                        <button class="btn-change-pic" onclick="openProfilePicModal()">
                            <i class="fas fa-camera"></i> Upload Picture
                        </button>
                    </div>
                </div>

                <form id="profileForm">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" value="<?php echo $adminData['first_name']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" value="<?php echo $adminData['last_name']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail Address</label>
                        <input type="email" id="email" value="<?php echo $adminData['email']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Mobile Number</label>
                        <input type="tel" id="phone" value="<?php echo $adminData['phone']; ?>">
                    </div>

                    <div class="button-group">
                        <button type="button" class="btn btn-secondary" onclick="showCancelModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Card: Password Settings -->
            <div class="settings-card">
                <h3><i class="fas fa-key"></i> Password Settings</h3>
                
                <div class="form-group password-container">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" placeholder="Enter current password">
                    <button type="button" class="toggle-password" onclick="togglePassword('currentPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="error-message" id="currentPassError"></div>
                </div>

                <div class="form-group password-container">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" placeholder="Enter new password">
                    <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="error-message" id="newPassError"></div>
                </div>

                <div class="form-group password-container">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" placeholder="Confirm new password">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="error-message" id="confirmPassError"></div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-secondary" onclick="clearPasswordFields()">
                        <i class="fas fa-broom"></i> Clear Fields
                    </button>
                    <button type="button" class="btn btn-primary" onclick="validateAndUpdatePassword()">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Picture Modal -->
    <div class="modal" id="picModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload Profile Picture</h3>
                <button class="modal-close" onclick="closeModal('picModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="preview-container">
                    <img src="" alt="Profile Preview" class="preview-image" id="imagePreview">
                    <div class="profile-pic-large" id="modalProfilePic" style="margin: 0 auto 15px;">
                        <img src="<?php echo $adminData['profile_pic']; ?>" alt="Current Profile" 
                             onerror="this.style.display='none'; document.getElementById('modalFallback').style.display='flex';">
                        <div class="initials-fallback" id="modalFallback" style="display: none;">
                            <?php echo substr($adminData['first_name'], 0, 1) . substr($adminData['last_name'], 0, 1); ?>
                        </div>
                    </div>
                    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Current profile picture</p>
                    
                    <input type="file" id="profileImage" class="file-input" accept="image/*" onchange="previewImage()">
                    <label for="profileImage" class="file-label">
                        <i class="fas fa-upload"></i> Choose New Photo
                    </label>
                </div>
                
                <div class="modal-buttons">
                    <button class="btn btn-secondary" onclick="closeModal('picModal')" style="flex: 1;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button class="btn btn-primary" onclick="saveProfilePicture()" style="flex: 1;">
                        <i class="fas fa-check"></i> Save Picture
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal (Red) -->
    <div class="modal" id="cancelModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Discard Changes</h3>
                <button class="modal-close" onclick="closeModal('cancelModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="modal-message">Are you sure you want to discard all changes? All unsaved modifications will be lost.</p>
                <div class="modal-buttons">
                    <button class="btn btn-secondary" onclick="closeModal('cancelModal')" style="flex: 1;">
                        No, Keep Changes
                    </button>
                    <button class="btn btn-danger" onclick="discardChanges()" style="flex: 1;">
                        <i class="fas fa-trash"></i> Yes, Discard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message (Green - Top Right) -->
    <div class="success-message" id="successMsg"></div>

    <!-- Error Notification (Red - Top Right) -->
    <div class="error-notification" id="errorMsg"></div>

    <script>
        // ============ SIDEBAR INTEGRATION - AUTO-HIDE SUPPORT ============
        // Get current sidebar mode from session
        let currentSidebarMode = '<?php echo $sidebarMode; ?>';

        // Listen for sidebar mode changes
        document.addEventListener('sidebarModeChanged', function(e) {
            console.log('Sidebar mode changed to:', e.detail.mode);
            currentSidebarMode = e.detail.mode;
            
            // Force content position adjustment
            setTimeout(() => {
                adjustContentPosition();
            }, 50);
        });

        // Listen for sidebar auto-hide hover events
        document.addEventListener('sidebarAutoHide', function(e) {
            console.log('Sidebar auto-hide hover:', e.detail.expanded);
            
            // Force immediate position adjustment on hover
            setTimeout(() => {
                adjustContentPosition();
            }, 10);
        });

        // Listen for sidebar manual toggle events
        document.addEventListener('sidebarToggled', function(e) {
            console.log('Sidebar manually toggled:', e.detail.collapsed);
            
            // Force immediate position adjustment
            setTimeout(() => {
                adjustContentPosition();
            }, 10);
        });

        // Function to adjust content position based on sidebar state
        function adjustContentPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.profile-content');
            
            if (!sidebar || !content) return;
            
            const isAutoHide = sidebar.classList.contains('auto-hide');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const isHovered = sidebar.matches(':hover') && isAutoHide;
            
            // Calculate sidebar width
            let sidebarWidth;
            
            if (isAutoHide) {
                if (isHovered) {
                    sidebarWidth = 240; // Expanded on hover
                } else {
                    sidebarWidth = 70; // Collapsed normally
                }
            } else {
                if (isCollapsed) {
                    sidebarWidth = 70;
                } else {
                    sidebarWidth = 240;
                }
            }
            
            console.log('Adjusting profile content position:', {
                mode: isAutoHide ? 'auto-hide' : 'manual',
                isAutoHide,
                isCollapsed,
                isHovered,
                sidebarWidth
            });
            
            // Update content position
            content.style.marginLeft = sidebarWidth + 'px';
            content.style.width = `calc(100% - ${sidebarWidth}px)`;
            content.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            
            // Update success/error message positions
            const successMsg = document.getElementById('successMsg');
            const errorMsg = document.getElementById('errorMsg');
            
            if (successMsg) {
                successMsg.style.right = '25px';
                successMsg.style.left = 'auto';
            }
            if (errorMsg) {
                errorMsg.style.right = '25px';
                errorMsg.style.left = 'auto';
            }
            
            // Force a reflow
            void content.offsetWidth;
        }

        // Hook into global update function if it exists
        if (typeof window.updateAllPositions === 'function') {
            const originalUpdateAllPositions = window.updateAllPositions;
            window.updateAllPositions = function() {
                originalUpdateAllPositions();
                adjustContentPosition();
            };
        }

        // Make function globally available
        window.adjustContentPosition = adjustContentPosition;
        // ============ END SIDEBAR INTEGRATION ============

        // Profile picture state
        let currentProfilePicture = {
            type: 'image',
            imageUrl: '<?php echo $adminData["profile_pic"]; ?>',
            initials: '<?php echo substr($adminData["first_name"], 0, 1) . substr($adminData["last_name"], 0, 1); ?>'
        };

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Clear all password fields
        function clearPasswordFields() {
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            
            // Reset to password type
            ['currentPassword', 'newPassword', 'confirmPassword'].forEach(id => {
                document.getElementById(id).type = 'password';
            });
            
            // Reset eye icons
            document.querySelectorAll('.toggle-password i').forEach(icon => {
                icon.className = 'fas fa-eye';
            });
            
            // Clear error messages
            clearPasswordErrors();
            
            // Show success message
            showSuccessMessage('Password fields cleared');
        }

        // Clear error messages
        function clearPasswordErrors() {
            document.getElementById('currentPassError').style.display = 'none';
            document.getElementById('newPassError').style.display = 'none';
            document.getElementById('confirmPassError').style.display = 'none';
        }

        // Validate and update password
        function validateAndUpdatePassword() {
            const current = document.getElementById('currentPassword').value.trim();
            const newPass = document.getElementById('newPassword').value.trim();
            const confirm = document.getElementById('confirmPassword').value.trim();
            
            let isValid = true;
            clearPasswordErrors();
            
            // Validate current password
            if (!current) {
                document.getElementById('currentPassError').textContent = 'Please enter current password';
                document.getElementById('currentPassError').style.display = 'block';
                isValid = false;
            }
            
            // Validate new password
            if (!newPass) {
                document.getElementById('newPassError').textContent = 'Please enter new password';
                document.getElementById('newPassError').style.display = 'block';
                isValid = false;
            } else if (newPass.length < 8) {
                document.getElementById('newPassError').textContent = 'Password must be at least 8 characters';
                document.getElementById('newPassError').style.display = 'block';
                isValid = false;
            }
            
            // Validate confirm password
            if (!confirm) {
                document.getElementById('confirmPassError').textContent = 'Please confirm password';
                document.getElementById('confirmPassError').style.display = 'block';
                isValid = false;
            } else if (newPass !== confirm) {
                document.getElementById('confirmPassError').textContent = 'Passwords do not match';
                document.getElementById('confirmPassError').style.display = 'block';
                isValid = false;
            }
            
            if (isValid) {
                updatePassword();
            }
        }

        // Update password
        function updatePassword() {
            // Simulate password update
            showSuccessMessage('Password updated successfully!');
            
            // Clear fields after success
            setTimeout(() => {
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                
                // Reset to password type
                ['currentPassword', 'newPassword', 'confirmPassword'].forEach(id => {
                    document.getElementById(id).type = 'password';
                });
                
                // Reset eye icons
                document.querySelectorAll('.toggle-password i').forEach(icon => {
                    icon.className = 'fas fa-eye';
                });
                
                // Clear error messages
                clearPasswordErrors();
            }, 500);
        }

        // Show cancel confirmation modal
        function showCancelModal() {
            document.getElementById('cancelModal').classList.add('active');
        }

        // Close specific modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Discard changes
        function discardChanges() {
            document.getElementById('firstName').value = '<?php echo $adminData["first_name"]; ?>';
            document.getElementById('lastName').value = '<?php echo $adminData["last_name"]; ?>';
            document.getElementById('email').value = '<?php echo $adminData["email"]; ?>';
            document.getElementById('phone').value = '<?php echo $adminData["phone"]; ?>';
            
            // Close modal
            closeModal('cancelModal');
            
            // Show success message
            showSuccessMessage('Changes discarded successfully');
        }

        // Profile form handling
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            saveProfile();
        });

        function saveProfile() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            
            // Update profile header
            document.querySelector('.profile-info h2').textContent = firstName + ' ' + lastName;
            
            // Update initials fallback
            if (currentProfilePicture.type === 'initials') {
                const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
                currentProfilePicture.initials = initials;
                updateProfilePictureDisplay();
            }
            
            // Show success message
            showSuccessMessage('Profile information saved successfully!');
        }

        // Profile picture functions
        function openProfilePicModal() {
            document.getElementById('picModal').classList.add('active');
        }

        function previewImage() {
            const input = document.getElementById('profileImage');
            const preview = document.getElementById('imagePreview');
            const modalPic = document.getElementById('modalProfilePic');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    // Hide the current profile pic in modal
                    modalPic.style.display = 'none';
                    
                    currentProfilePicture.type = 'image';
                    currentProfilePicture.imageUrl = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updateProfilePictureDisplay() {
            const profilePic = document.getElementById('currentProfilePic');
            const modalPic = document.getElementById('modalProfilePic');
            
            if (currentProfilePicture.type === 'image') {
                profilePic.innerHTML = `<img src="${currentProfilePicture.imageUrl}" alt="Profile" 
                                         onerror="this.style.display='none'; document.getElementById('initialsFallback').style.display='flex';">`;
                if (modalPic) {
                    modalPic.innerHTML = `<img src="${currentProfilePicture.imageUrl}" alt="Profile">`;
                    modalPic.style.display = 'flex';
                }
            } else {
                profilePic.innerHTML = `<div class="initials-fallback" style="display: flex;">${currentProfilePicture.initials}</div>`;
                if (modalPic) {
                    modalPic.innerHTML = `<div class="initials-fallback" style="display: flex;">${currentProfilePicture.initials}</div>`;
                    modalPic.style.display = 'flex';
                }
            }
        }

        function saveProfilePicture() {
            updateProfilePictureDisplay();
            closeModal('picModal');
            showSuccessMessage('Profile picture updated successfully!');
        }

        // Show success message (green)
        function showSuccessMessage(msg) {
            const successDiv = document.getElementById('successMsg');
            const errorDiv = document.getElementById('errorMsg');
            
            // Hide error first
            errorDiv.style.display = 'none';
            
            // Show success
            successDiv.textContent = msg;
            successDiv.style.display = 'block';
            
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 3000);
        }

        // Show error message (red)
        function showErrorMessage(msg) {
            const successDiv = document.getElementById('successMsg');
            const errorDiv = document.getElementById('errorMsg');
            
            // Hide success first
            successDiv.style.display = 'none';
            
            // Show error
            errorDiv.textContent = msg;
            errorDiv.style.display = 'block';
            
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 3000);
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initial position adjustment
            setTimeout(() => {
                adjustContentPosition();
            }, 100);
            
            // Check for sidebar state changes periodically
            setInterval(() => {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    const currentClasses = sidebar.className;
                    
                    if (window.lastSidebarClasses !== currentClasses) {
                        window.lastSidebarClasses = currentClasses;
                        adjustContentPosition();
                    }
                }
            }, 100);
            
            // Check if profile image loads successfully
            const profileImg = document.querySelector('#currentProfilePic img');
            if (profileImg) {
                profileImg.onerror = function() {
                    this.style.display = 'none';
                    const initialsFallback = document.getElementById('initialsFallback');
                    if (initialsFallback) {
                        initialsFallback.style.display = 'flex';
                    }
                };
            }
        });
    </script>
</body>
</html>