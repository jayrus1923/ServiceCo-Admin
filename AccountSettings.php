<?php
session_start();
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sidebar_mode'])) {
    $_SESSION['sidebar_mode'] = $_POST['sidebar_mode'];
    echo json_encode(['success' => true, 'message' => 'Sidebar mode saved']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_display_preferences'])) {
    $_SESSION['display_preferences'] = [
        'date_format' => $_POST['date_format'] ?? 'mm/dd/yyyy',
        'time_format' => $_POST['time_format'] ?? '12'
    ];
    echo json_encode(['success' => true, 'message' => 'Display preferences saved']);
    exit;
}

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

$displayPrefs = isset($_SESSION['display_preferences']) ? $_SESSION['display_preferences'] : [];
$dateFormat = isset($displayPrefs['date_format']) ? $displayPrefs['date_format'] : 'mm/dd/yyyy';
$timeFormat = isset($displayPrefs['time_format']) ? $displayPrefs['time_format'] : '12';

$now = new DateTime('now', new DateTimeZone('Asia/Manila'));
$currentDate = $now->format('m/d/Y');
$currentTime12 = $now->format('h:i A');
$currentTime24 = $now->format('H:i');
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
        }

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
            background-color: #347433;
            color: white;
            border-color: #347433;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(52, 116, 51, 0.2);
        }

        .sidebar-hover-info {
            font-size: 13px;
            color: #666;
            margin-top: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #347433;
            line-height: 1.5;
        }

        .sidebar-hover-info i {
            margin-right: 8px;
            color: #347433;
        }

        .success-message {
            background-color: #d1fae5;
            color: #347433;
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
            color: #347433;
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
            border-color: #347433;
            box-shadow: 0 0 0 3px rgba(52, 116, 51, 0.1);
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
            background: #347433;
            color: white;
        }

        .btn-primary:hover {
            background: #2d6a2d;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(52, 116, 51, 0.2);
        }

        .preview-box {
            background-color: #f0fdf4;
            border: 1px solid #347433;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            font-size: 13px;
            color: #347433;
        }
        
        .preview-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
        }
        
        .preview-label {
            font-weight: 600;
            min-width: 60px;
            color: #333;
        }
        
        .preview-value {
            font-weight: 500;
            color: #347433;
        }

        .current-time {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
            text-align: right;
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
        <div id="successMessage" class="success-message">
            <i class="fas fa-check-circle success-icon"></i>
            <span id="successMessageText">Settings saved successfully!</span>
        </div>

        <div class="welcome-section">
            <h1 class="welcome-title">Account Settings</h1>
            <p style="color: #666; font-size: 14px;">Manage your account preferences</p>
            <div class="current-time">Current Philippine Time: <?php echo $currentTime12; ?></div>
        </div>

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
                <select id="dateFormat" onchange="updatePreview()">
                    <option value="mm/dd/yyyy" <?php echo $dateFormat == 'mm/dd/yyyy' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                    <option value="dd/mm/yyyy" <?php echo $dateFormat == 'dd/mm/yyyy' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                    <option value="yyyy-mm-dd" <?php echo $dateFormat == 'yyyy-mm-dd' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                </select>
            </div>

            <div class="form-group">
                <label for="timeFormat">Time Format</label>
                <select id="timeFormat" onchange="updatePreview()">
                    <option value="12" <?php echo $timeFormat == '12' ? 'selected' : ''; ?>>12-hour </option>
                    <option value="24" <?php echo $timeFormat == '24' ? 'selected' : ''; ?>>24-hour </option>
                </select>
            </div>

            <div id="livePreview" class="preview-box">
                <div class="preview-item">
                    <span class="preview-label">Date:</span>
                    <span id="previewDate" class="preview-value"><?php echo $currentDate; ?></span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Time:</span>
                    <span id="previewTime" class="preview-value"><?php echo $currentTime12; ?></span>
                </div>
            </div>

            <div class="button-group" style="margin-top: 20px;">
                <button class="btn btn-primary" onclick="saveDisplayPreferences()">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentSidebarMode = '<?php echo $sidebarMode; ?>';
        
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
            
            document.addEventListener('sidebarModeChanged', function(e) {
                currentSidebarMode = e.detail.mode;
                document.getElementById('manualModeBtn').classList.toggle('active', e.detail.mode === 'manual');
                document.getElementById('autoHideModeBtn').classList.toggle('active', e.detail.mode === 'auto-hide');
                setTimeout(() => adjustContentPosition(), 10);
            });
            
            document.addEventListener('sidebarAutoHide', function(e) {
                setTimeout(() => adjustContentPosition(), 10);
            });
            
            document.addEventListener('sidebarToggled', function(e) {
                setTimeout(() => adjustContentPosition(), 10);
            });
            
            setTimeout(() => adjustContentPosition(), 100);
        });

        function updatePreview() {
            const dateFormat = document.getElementById('dateFormat').value;
            const timeFormat = document.getElementById('timeFormat').value;
            
            const options = { timeZone: 'Asia/Manila' };
            const now = new Date();
            
            const phTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
            
            const month = String(phTime.getMonth() + 1).padStart(2, '0');
            const day = String(phTime.getDate()).padStart(2, '0');
            const year = phTime.getFullYear();
            
            let formattedDate;
            switch(dateFormat) {
                case 'mm/dd/yyyy':
                    formattedDate = `${month}/${day}/${year}`;
                    break;
                case 'dd/mm/yyyy':
                    formattedDate = `${day}/${month}/${year}`;
                    break;
                case 'yyyy-mm-dd':
                    formattedDate = `${year}-${month}-${day}`;
                    break;
                default:
                    formattedDate = `${month}/${day}/${year}`;
            }
            
            let hours = phTime.getHours();
            let minutes = String(phTime.getMinutes()).padStart(2, '0');
            let formattedTime;
            
            if (timeFormat === '12') {
                const ampm = hours >= 12 ? 'PM' : 'AM';
                let displayHour = hours % 12;
                displayHour = displayHour ? displayHour : 12;
                formattedTime = `${String(displayHour).padStart(2, '0')}:${minutes} ${ampm}`;
            } else {
                formattedTime = `${String(hours).padStart(2, '0')}:${minutes}`;
            }
            
            document.getElementById('previewDate').textContent = formattedDate;
            document.getElementById('previewTime').textContent = formattedTime;
        }

        function setSidebarMode(mode) {
            document.getElementById('manualModeBtn').classList.toggle('active', mode === 'manual');
            document.getElementById('autoHideModeBtn').classList.toggle('active', mode === 'auto-hide');
            
            saveSidebarModeToServer(mode);
            
            if (typeof window.changeSidebarMode === 'function') {
                window.changeSidebarMode(mode);
            } else {
                applySidebarMode(mode);
            }
            
            setTimeout(() => adjustContentPosition(), 50);
            showSuccessMessage('Sidebar mode changed to ' + (mode === 'manual' ? 'Manual Toggle' : 'Auto-Hide on Hover'));
        }

        function applySidebarMode(mode) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            
            sidebar.classList.remove('auto-hide', 'collapsed');
            
            if (mode === 'manual') {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) sidebar.classList.add('collapsed');
                
                const menuButton = document.querySelector('.menu-toggle');
                if (menuButton) menuButton.style.display = 'flex';
                
                const toggleEvent = new CustomEvent('sidebarToggled', { 
                    detail: { collapsed: isCollapsed, mode: 'manual' }
                });
                document.dispatchEvent(toggleEvent);
                
            } else if (mode === 'auto-hide') {
                sidebar.classList.add('auto-hide');
                
                const menuButton = document.querySelector('.menu-toggle');
                if (menuButton) menuButton.style.display = 'none';
                
                const autoHideEvent = new CustomEvent('sidebarAutoHide', { 
                    detail: { expanded: false }
                });
                document.dispatchEvent(autoHideEvent);
            }
            
            currentSidebarMode = mode;
            
            const modeEvent = new CustomEvent('sidebarModeChanged', { 
                detail: { mode: mode }
            });
            document.dispatchEvent(modeEvent);
            
            adjustContentPosition();
        }

        function adjustContentPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.account-content');
            
            if (!sidebar || !content) return;
            
            const isAutoHide = sidebar.classList.contains('auto-hide');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const isHovered = sidebar.matches(':hover') && isAutoHide;
            
            let sidebarWidth;
            if (isAutoHide) {
                sidebarWidth = isHovered ? 240 : 70;
            } else {
                sidebarWidth = isCollapsed ? 70 : 240;
            }
            
            content.style.marginLeft = sidebarWidth + 'px';
            content.style.width = `calc(100% - ${sidebarWidth}px)`;
            
            const navbar = document.getElementById('navbar');
            if (navbar) navbar.style.left = sidebarWidth + 'px';
        }

        function saveSidebarModeToServer(mode) {
            fetch('AccountSettings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'sidebar_mode=' + mode
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('sidebarMode', mode);
                }
            })
            .catch(error => console.error('Error saving sidebar mode:', error));
        }

        function saveDisplayPreferences() {
            const formData = new FormData();
            formData.append('save_display_preferences', 'true');
            formData.append('date_format', document.getElementById('dateFormat').value);
            formData.append('time_format', document.getElementById('timeFormat').value);
            
            fetch('AccountSettings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Display preferences saved successfully!');
                    
                    const displayEvent = new CustomEvent('displayPreferencesChanged', {
                        detail: {
                            date_format: document.getElementById('dateFormat').value,
                            time_format: document.getElementById('timeFormat').value
                        },
                        bubbles: true
                    });
                    document.dispatchEvent(displayEvent);
                    
                    localStorage.setItem('displayPreferences', JSON.stringify({
                        date_format: document.getElementById('dateFormat').value,
                        time_format: document.getElementById('timeFormat').value
                    }));
                }
            })
            .catch(error => {
                console.error('Error saving display preferences:', error);
                showErrorMessage('Error saving preferences');
            });
        }

        function showSuccessMessage(message) {
            const successMessage = document.getElementById('successMessage');
            const messageText = document.getElementById('successMessageText');
            
            messageText.textContent = message;
            successMessage.classList.add('show');
            
            setTimeout(() => successMessage.classList.remove('show'), 4000);
        }

        function showErrorMessage(message) {
            const successMessage = document.getElementById('successMessage');
            const messageText = document.getElementById('successMessageText');
            
            messageText.textContent = message;
            successMessage.style.backgroundColor = '#fee2e2';
            successMessage.style.color = '#991b1b';
            successMessage.style.borderColor = '#fecaca';
            successMessage.classList.add('show');
            
            setTimeout(() => {
                successMessage.classList.remove('show');
                successMessage.style.backgroundColor = '#d1fae5';
                successMessage.style.color = '#347433';
                successMessage.style.borderColor = '#a7f3d0';
            }, 4000);
        }
        
        window.adjustContentPosition = adjustContentPosition;
        window.updatePreview = updatePreview;
        window.saveDisplayPreferences = saveDisplayPreferences;
        window.setSidebarMode = setSidebarMode;
    </script>
</body>
</html>