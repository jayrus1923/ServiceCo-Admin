<?php
// AccountSettings.php - WITH REAL-TIME AUTO-HIDE
session_start();

// Handle sidebar mode saving
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
    <title>Tricycle Booking - Account Settings</title>
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

        .account-content {
            margin-top: 70px;
            padding: 20px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        .sidebar.collapsed ~ .account-content,
        .sidebar.auto-hide ~ .account-content {
            margin-left: 70px;
            width: calc(100% - 70px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* IMPORTANT: FIXED - Direct sibling selector for auto-hide hover */
        .sidebar.auto-hide:hover ~ .account-content {
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

        /* Sidebar Settings */
        .sidebar-settings {
            margin-bottom: 20px;
        }

        .sidebar-settings h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .sidebar-mode-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .sidebar-mode-btn {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background-color: white;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sidebar-mode-btn:hover {
            background-color: #f8f9fa;
            border-color: #d1d5db;
            transform: translateY(-1px);
        }

        .sidebar-mode-btn.active {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .sidebar-hover-info {
            font-size: 13px;
            color: #666;
            margin-top: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #10b981;
            line-height: 1.5;
        }

        .sidebar-hover-info i {
            margin-right: 8px;
            color: #10b981;
        }

        /* Success Message */
        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #a7f3d0;
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        .success-message.show {
            display: flex;
        }

        @keyframes slideDown {
            from { 
                opacity: 0; 
                transform: translateY(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .success-icon {
            color: #10b981;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 12px 15px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .toggle-label {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
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
            border-radius: 24px;
            transition: .3s;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            border-radius: 50%;
            transition: .3s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #0da271;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .account-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 15px;
            }
            
            .sidebar-mode-buttons {
                flex-direction: column;
            }
            
            .sidebar-mode-btn {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <div class="account-content">
        <!-- Success Message -->
        <div id="successMessage" class="success-message">
            <i class="fas fa-check-circle success-icon"></i>
            <span>Sidebar mode saved successfully! Changes applied immediately.</span>
        </div>

        <div class="welcome-section">
            <h1 class="welcome-title">Account Settings</h1>
            <p style="color: #666; font-size: 14px;">Manage your account preferences</p>
        </div>

        <!-- Sidebar Settings Section -->
        <div class="settings-card">
            <h3>Sidebar Settings</h3>
            <div class="sidebar-settings">
                <h4>Sidebar Mode:</h4>
                <div class="sidebar-mode-buttons">
                    <button class="sidebar-mode-btn <?php echo $sidebarMode === 'manual' ? 'active' : ''; ?>" 
                            onclick="setSidebarMode('manual')" id="manualModeBtn">
                        <i class="fas fa-hand-pointer"></i> Manual Toggle
                    </button>
                    <button class="sidebar-mode-btn <?php echo $sidebarMode === 'auto-hide' ? 'active' : ''; ?>" 
                            onclick="setSidebarMode('auto-hide')" id="autoHideModeBtn">
                        <i class="fas fa-mouse-pointer"></i> Auto-Hide on Hover
                    </button>
                </div>
                <div class="sidebar-hover-info">
                    <i class="fas fa-info-circle"></i> Auto-Hide mode will collapse the sidebar and expand it when you hover over it. Manual mode allows you to click the menu button to toggle.
                </div>
            </div>
        </div>

        <div class="settings-card">
            <h3>Display Preferences</h3>
            
            <div class="form-group">
                <label for="dateFormat">Date Format</label>
                <select id="dateFormat">
                    <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                    <option value="dd/mm/yyyy">DD/MM/YYYY</option>
                    <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                </select>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">Show Week Numbers</span>
                <div class="toggle-switch">
                    <input type="checkbox" id="weekNumbers" class="toggle-checkbox" checked>
                    <label for="weekNumbers" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">Auto-save Forms</span>
                <div class="toggle-switch">
                    <input type="checkbox" id="autoSave" class="toggle-checkbox" checked>
                    <label for="autoSave" class="toggle-slider"></label>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <h3>System Preferences</h3>
            
            <div class="toggle-container">
                <span class="toggle-label">Show Dashboard Stats</span>
                <div class="toggle-switch">
                    <input type="checkbox" id="showStats" class="toggle-checkbox" checked>
                    <label for="showStats" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">Show Recent Activities</span>
                <div class="toggle-switch">
                    <input type="checkbox" id="showActivities" class="toggle-checkbox" checked>
                    <label for="showActivities" class="toggle-slider"></label>
                </div>
            </div>

            <div class="toggle-container">
                <span class="toggle-label">Confirm Before Actions</span>
                <div class="toggle-switch">
                    <input type="checkbox" id="confirmActions" class="toggle-checkbox" checked>
                    <label for="confirmActions" class="toggle-slider"></label>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <h3>Data Management</h3>
            
            <div style="margin-bottom: 15px;">
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                    Manage your account data and settings
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button class="btn btn-secondary" onclick="clearCache()">
                    <i class="fas fa-trash"></i> Clear Cache
                </button>
                
                <button class="btn btn-secondary" onclick="clearHistory()">
                    <i class="fas fa-history"></i> Clear Activity History
                </button>
                
                <button class="btn btn-primary" onclick="saveSettings()">
                    <i class="fas fa-save"></i> Save All Settings
                </button>
            </div>
        </div>
    </div>

    <script>
        // Store current sidebar mode
        let currentSidebarMode = '<?php echo $sidebarMode; ?>';
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Account Settings loaded. Current mode:', currentSidebarMode);
            
            // Initialize toggles
            document.querySelectorAll('.toggle-checkbox').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    console.log(this.id + ': ' + this.checked);
                });
            });
            
            // Listen for sidebar mode changes from Sidebar.php
            document.addEventListener('sidebarModeChanged', function(e) {
                console.log('Sidebar mode changed to:', e.detail.mode);
                currentSidebarMode = e.detail.mode;
                
                // Update button states
                document.getElementById('manualModeBtn').classList.toggle('active', e.detail.mode === 'manual');
                document.getElementById('autoHideModeBtn').classList.toggle('active', e.detail.mode === 'auto-hide');
                
                // Adjust content position
                setTimeout(() => {
                    adjustContentPosition();
                }, 10);
            });
            
            // Listen for sidebar auto-hide hover events
            document.addEventListener('sidebarAutoHide', function(e) {
                console.log('Sidebar auto-hide hover:', e.detail.expanded);
                
                // Force immediate position adjustment on hover
                setTimeout(() => {
                    adjustContentPosition();
                }, 10);
            });
            
            // Listen for sidebar toggle events
            document.addEventListener('sidebarToggled', function(e) {
                console.log('Sidebar manually toggled:', e.detail.collapsed);
                
                // Force immediate position adjustment
                setTimeout(() => {
                    adjustContentPosition();
                }, 10);
            });
            
            // Listen for global sidebar position updates
            if (typeof window.updateAllPositions === 'function') {
                // Hook into the global update function
                const originalUpdateAllPositions = window.updateAllPositions;
                window.updateAllPositions = function() {
                    originalUpdateAllPositions();
                    adjustContentPosition();
                };
            }
            
            // Initial position adjustment
            setTimeout(() => {
                adjustContentPosition();
            }, 100);
            
            // Set up periodic check for sidebar changes
            setInterval(() => {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    const currentClasses = sidebar.className;
                    
                    // Check if classes have changed
                    if (window.lastSidebarClasses !== currentClasses) {
                        window.lastSidebarClasses = currentClasses;
                        adjustContentPosition();
                    }
                }
            }, 100);
        });

        function setSidebarMode(mode) {
            console.log('Setting sidebar mode to:', mode);
            
            // Update button states immediately
            document.getElementById('manualModeBtn').classList.toggle('active', mode === 'manual');
            document.getElementById('autoHideModeBtn').classList.toggle('active', mode === 'auto-hide');
            
            // Save to server
            saveSidebarModeToServer(mode);
            
            // Apply immediately using Sidebar.php's global function
            if (typeof window.changeSidebarMode === 'function') {
                window.changeSidebarMode(mode);
            } else {
                // Fallback: Trigger sidebar mode change
                applySidebarMode(mode);
            }
            
            // Force immediate position adjustment
            setTimeout(() => {
                adjustContentPosition();
            }, 50);
            
            // Show success message
            showSuccessMessage('Sidebar mode changed to ' + (mode === 'manual' ? 'Manual Toggle' : 'Auto-Hide on Hover'));
        }

        function applySidebarMode(mode) {
            const sidebar = document.getElementById('sidebar');
            
            if (!sidebar) {
                console.error('Sidebar not found!');
                return;
            }
            
            console.log('Applying mode:', mode);
            
            // Remove all mode classes first
            sidebar.classList.remove('auto-hide', 'collapsed');
            
            if (mode === 'manual') {
                // Manual mode - check if sidebar should be collapsed
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                console.log('Manual mode, collapsed state:', isCollapsed);
                
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                }
                
                // Show menu button
                const menuButton = document.querySelector('.menu-toggle');
                if (menuButton) {
                    menuButton.style.display = 'flex';
                }
                
                // Dispatch toggle event for manual mode
                const toggleEvent = new CustomEvent('sidebarToggled', { 
                    detail: { 
                        collapsed: isCollapsed,
                        mode: 'manual'
                    }
                });
                document.dispatchEvent(toggleEvent);
                
            } else if (mode === 'auto-hide') {
                // Auto-hide mode - always collapsed initially
                sidebar.classList.add('auto-hide');
                
                // Hide menu button in auto-hide mode
                const menuButton = document.querySelector('.menu-toggle');
                if (menuButton) {
                    menuButton.style.display = 'none';
                }
                
                // Dispatch auto-hide event for collapsed state
                const autoHideEvent = new CustomEvent('sidebarAutoHide', { 
                    detail: { expanded: false }
                });
                document.dispatchEvent(autoHideEvent);
            }
            
            // Update current mode
            currentSidebarMode = mode;
            
            // Dispatch mode changed event
            const modeEvent = new CustomEvent('sidebarModeChanged', { 
                detail: { 
                    mode: mode,
                    collapsed: sidebar.classList.contains('collapsed') || sidebar.classList.contains('auto-hide')
                }
            });
            document.dispatchEvent(modeEvent);
            
            // Force immediate position adjustment
            adjustContentPosition();
        }

        // NEW FUNCTION: Adjust content position based on sidebar state
        function adjustContentPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.account-content');
            
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
            
            console.log('Adjusting content position:', {
                isAutoHide,
                isCollapsed,
                isHovered,
                sidebarWidth
            });
            
            // Update content position immediately
            content.style.marginLeft = sidebarWidth + 'px';
            content.style.width = `calc(100% - ${sidebarWidth}px)`;
            content.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            
            // Also update navbar if exists
            const navbar = document.getElementById('navbar');
            if (navbar) {
                navbar.style.left = sidebarWidth + 'px';
            }
            
            // Force a reflow to ensure CSS updates immediately
            void content.offsetWidth;
        }

        function saveSidebarModeToServer(mode) {
            fetch('AccountSettings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'sidebar_mode=' + mode
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Sidebar mode saved to server:', mode);
                    localStorage.setItem('sidebarMode', mode);
                } else {
                    console.error('Failed to save to server:', data.message);
                }
            })
            .catch(error => {
                console.error('Error saving sidebar mode:', error);
                localStorage.setItem('sidebarMode', mode);
            });
        }

        function showSuccessMessage(message) {
            const successMessage = document.getElementById('successMessage');
            const messageText = successMessage.querySelector('span');
            
            messageText.textContent = message;
            successMessage.classList.add('show');
            
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 4000);
        }

        function saveSettings() {
            const dateFormat = document.getElementById('dateFormat').value;
            const weekNumbers = document.getElementById('weekNumbers').checked;
            const autoSave = document.getElementById('autoSave').checked;
            
            showSuccessMessage('All settings saved successfully!');
        }

        function clearCache() {
            if (confirm('Clear all cache data? This will reset your sidebar preferences.')) {
                localStorage.clear();
                showSuccessMessage('Cache cleared successfully!');
            }
        }

        function clearHistory() {
            if (confirm('Clear all activity history?')) {
                showSuccessMessage('Activity history cleared!');
            }
        }
        
        // Make the function available globally
        window.adjustContentPosition = adjustContentPosition;
    </script>
</body>
</html>