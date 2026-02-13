<?php
// Notifications.php - WITH REAL-TIME AUTO-HIDE SUPPORT
session_start();

// Handle sidebar mode saving (if posted from this page)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sidebar_mode'])) {
    $_SESSION['sidebar_mode'] = $_POST['sidebar_mode'];
    echo json_encode(['success' => true, 'message' => 'Sidebar mode saved']);
    exit;
}

// Get current sidebar mode
$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tricycle Booking - Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        /* Notifications Content - WITH AUTO-HIDE SUPPORT */
        .notifications-content {
            margin-top: 70px;
            padding: 20px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        /* Manual mode - collapsed state */
        .sidebar.collapsed ~ .notifications-content {
            margin-left: 70px;
            width: calc(100% - 70px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* AUTO-HIDE MODE - NORMAL STATE (collapsed) */
        .sidebar.auto-hide ~ .notifications-content {
            margin-left: 70px !important;
            width: calc(100% - 70px) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* AUTO-HIDE MODE - HOVER STATE (expanded) */
        .sidebar.auto-hide:hover ~ .notifications-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        .welcome-section {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .welcome-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .settings-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .settings-card h3 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 4px;
        }

        .toggle-label {
            font-size: 14px;
            color: #333;
        }

        .toggle-switch {
            position: relative;
            width: 40px;
            height: 20px;
        }

        .toggle-checkbox {
            display: none;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 20px;
            transition: .3s;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: .3s;
        }

        .toggle-checkbox:checked + .toggle-slider {
            background-color: #10b981;
        }

        .toggle-checkbox:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #0da271;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #f0fdf4;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #10b981;
        }

        .notification-content {
            flex: 1;
        }

        .notification-text {
            font-size: 14px;
            color: #333;
        }

        .notification-time {
            font-size: 12px;
            color: #888;
        }

        /* Success Message (Top Right) */
        .success-message {
            position: fixed;
            top: 90px;
            right: 25px;
            background-color: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            z-index: 999;
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .error-message {
            position: fixed;
            top: 90px;
            right: 25px;
            background-color: #ef4444;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            z-index: 999;
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .notifications-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 15px;
            }
            
            .sidebar.collapsed ~ .notifications-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .sidebar.auto-hide ~ .notifications-content {
                margin-left: 70px !important;
                width: calc(100% - 70px) !important;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .success-message, .error-message {
                top: 70px;
                right: 15px;
                left: 15px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .notifications-content {
                margin-left: 0;
                width: 100%;
                padding: 12px;
                margin-top: 60px;
            }
            
            .sidebar.collapsed ~ .notifications-content,
            .sidebar.auto-hide ~ .notifications-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Smooth sidebar transition */
        .notifications-content, .sidebar, .navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body data-sidebar-mode="<?php echo $sidebarMode; ?>">
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <!-- Success Message -->
    <div class="success-message" id="successMsg">
        <i class="fas fa-check-circle"></i> Settings saved successfully!
    </div>

    <!-- Error Message -->
    <div class="error-message" id="errorMsg">
        <i class="fas fa-exclamation-circle"></i> Error saving settings
    </div>

    <div class="notifications-content">
        <div class="welcome-section">
            <h1 class="welcome-title">Notifications</h1>
            <p style="color: #666; font-size: 14px;">Manage your notification preferences</p>
        </div>

        <div class="settings-card">
            <h3><i class="fas fa-bell" style="color: #10b981; margin-right: 8px;"></i> Notification Channels</h3>
            
            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-envelope" style="color: #6b7280; margin-right: 8px;"></i>
                    Email Notifications
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="emailNotif" class="toggle-checkbox" checked>
                    <label for="emailNotif" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-bell" style="color: #6b7280; margin-right: 8px;"></i>
                    Push Notifications
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="pushNotif" class="toggle-checkbox" checked>
                    <label for="pushNotif" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-sms" style="color: #6b7280; margin-right: 8px;"></i>
                    SMS Notifications
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="smsNotif" class="toggle-checkbox">
                    <label for="smsNotif" class="toggle-slider"></label>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <h3><i class="fas fa-sliders-h" style="color: #10b981; margin-right: 8px;"></i> What to Notify</h3>
            
            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-user-plus" style="color: #6b7280; margin-right: 8px;"></i>
                    New Driver Applications
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="newApps" class="toggle-checkbox" checked>
                    <label for="newApps" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-exclamation-triangle" style="color: #6b7280; margin-right: 8px;"></i>
                    Driver Complaints
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="complaints" class="toggle-checkbox" checked>
                    <label for="complaints" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-cog" style="color: #6b7280; margin-right: 8px;"></i>
                    System Updates
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="systemUpdates" class="toggle-checkbox" checked>
                    <label for="systemUpdates" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-calendar-check" style="color: #6b7280; margin-right: 8px;"></i>
                    Booking Updates
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="bookingUpdates" class="toggle-checkbox">
                    <label for="bookingUpdates" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">
                    <i class="fas fa-money-bill-wave" style="color: #6b7280; margin-right: 8px;"></i>
                    Payment Transactions
                </span>
                <div class="toggle-switch">
                    <input type="checkbox" id="payments" class="toggle-checkbox" checked>
                    <label for="payments" class="toggle-slider"></label>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <h3><i class="fas fa-history" style="color: #10b981; margin-right: 8px;"></i> Recent Notifications</h3>
            
            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-text">New driver application submitted</div>
                    <div class="notification-time">10 minutes ago</div>
                </div>
                <i class="fas fa-ellipsis-v" style="color: #9ca3af; cursor: pointer;"></i>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-text">Complaint received about driver #234</div>
                    <div class="notification-time">2 hours ago</div>
                </div>
                <i class="fas fa-ellipsis-v" style="color: #9ca3af; cursor: pointer;"></i>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-text">System maintenance scheduled (Mar 15, 2AM)</div>
                    <div class="notification-time">Yesterday</div>
                </div>
                <i class="fas fa-ellipsis-v" style="color: #9ca3af; cursor: pointer;"></i>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-text">Password changed successfully</div>
                    <div class="notification-time">2 days ago</div>
                </div>
                <i class="fas fa-ellipsis-v" style="color: #9ca3af; cursor: pointer;"></i>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-text">Driver application approved</div>
                    <div class="notification-time">3 days ago</div>
                </div>
                <i class="fas fa-ellipsis-v" style="color: #9ca3af; cursor: pointer;"></i>
            </div>

            <div style="text-align: center; margin-top: 15px;">
                <button class="btn btn-secondary" onclick="viewAllNotifications()" style="width: 100%;">
                    <i class="fas fa-eye"></i> View All Notifications
                </button>
            </div>
        </div>

        <div class="settings-card">
            <div class="button-group">
                <button class="btn btn-secondary" onclick="resetNotifications()">
                    <i class="fas fa-undo"></i> Reset to Default
                </button>
                <button class="btn btn-primary" onclick="saveNotifications()">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
            <p style="color: #888; font-size: 12px; margin-top: 15px; text-align: center;">
                <i class="fas fa-info-circle"></i> Changes are applied immediately
            </p>
        </div>
    </div>

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
            const content = document.querySelector('.notifications-content');
            
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
            
            console.log('Adjusting notifications content position:', {
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

        // Notification settings functions
        function saveNotifications() {
            // Get all toggle states
            const settings = {
                emailNotif: document.getElementById('emailNotif').checked,
                pushNotif: document.getElementById('pushNotif').checked,
                smsNotif: document.getElementById('smsNotif').checked,
                newApps: document.getElementById('newApps').checked,
                complaints: document.getElementById('complaints').checked,
                systemUpdates: document.getElementById('systemUpdates').checked,
                bookingUpdates: document.getElementById('bookingUpdates').checked,
                payments: document.getElementById('payments').checked
            };
            
            console.log('Saving notification settings:', settings);
            
            // Save to localStorage
            localStorage.setItem('notificationSettings', JSON.stringify(settings));
            
            // Show success message
            showSuccessMessage('Notification settings saved successfully!');
        }

        function resetNotifications() {
            if (confirm('Reset all notification settings to default?')) {
                // Set all toggles to default
                document.getElementById('emailNotif').checked = true;
                document.getElementById('pushNotif').checked = true;
                document.getElementById('smsNotif').checked = false;
                document.getElementById('newApps').checked = true;
                document.getElementById('complaints').checked = true;
                document.getElementById('systemUpdates').checked = true;
                document.getElementById('bookingUpdates').checked = false;
                document.getElementById('payments').checked = true;
                
                showSuccessMessage('Notifications reset to default settings');
            }
        }

        function viewAllNotifications() {
            showInfoMessage('View all notifications - Coming soon!');
        }

        // Load saved settings from localStorage
        function loadSavedSettings() {
            const saved = localStorage.getItem('notificationSettings');
            if (saved) {
                try {
                    const settings = JSON.parse(saved);
                    
                    // Apply saved settings
                    if (settings.emailNotif !== undefined) document.getElementById('emailNotif').checked = settings.emailNotif;
                    if (settings.pushNotif !== undefined) document.getElementById('pushNotif').checked = settings.pushNotif;
                    if (settings.smsNotif !== undefined) document.getElementById('smsNotif').checked = settings.smsNotif;
                    if (settings.newApps !== undefined) document.getElementById('newApps').checked = settings.newApps;
                    if (settings.complaints !== undefined) document.getElementById('complaints').checked = settings.complaints;
                    if (settings.systemUpdates !== undefined) document.getElementById('systemUpdates').checked = settings.systemUpdates;
                    if (settings.bookingUpdates !== undefined) document.getElementById('bookingUpdates').checked = settings.bookingUpdates;
                    if (settings.payments !== undefined) document.getElementById('payments').checked = settings.payments;
                    
                    console.log('Loaded saved notification settings');
                } catch (e) {
                    console.error('Error loading saved settings:', e);
                }
            }
        }

        // Show success message
        function showSuccessMessage(msg) {
            const successDiv = document.getElementById('successMsg');
            const errorDiv = document.getElementById('errorMsg');
            
            // Hide error first
            errorDiv.style.display = 'none';
            
            // Update and show success
            successDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + msg;
            successDiv.style.display = 'block';
            
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 3000);
        }

        // Show error message
        function showErrorMessage(msg) {
            const successDiv = document.getElementById('successMsg');
            const errorDiv = document.getElementById('errorMsg');
            
            // Hide success first
            successDiv.style.display = 'none';
            
            // Update and show error
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            errorDiv.style.display = 'block';
            
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 3000);
        }

        // Show info message
        function showInfoMessage(msg) {
            showSuccessMessage(msg); // Reuse success style for info
        }

        // Initialize toggles with logging
        document.querySelectorAll('.toggle-checkbox').forEach(toggle => {
            toggle.addEventListener('change', function() {
                console.log(this.id + ' changed to: ' + this.checked);
            });
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved settings
            loadSavedSettings();
            
            // Initial position adjustment for sidebar
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
        });
    </script>
</body>
</html>