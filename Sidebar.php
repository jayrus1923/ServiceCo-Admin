<?php
$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Admin Panel - ServiceCo</title>
  <style>
    :root {
      --sidebar-bg: #ffffff;
      --sidebar-text: #1f2937;
      --sidebar-accent: #347433;
      --co-color: #FCB53B;
      --hover-bg: rgba(52, 116, 51, 0.1);
      --active-bg: #347433;
      --active-text: #ffffff;
      --icon-size: 20px;
      --transition-speed: 0.4s;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f8fafc;
    }

    /* Quick Redirect Overlay */
    .redirect-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #347433 0%, #2c642c 100%);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      color: white;
      text-align: center;
      padding: 20px;
    }

    .redirect-spinner {
      width: 50px;
      height: 50px;
      border: 4px solid rgba(255, 255, 255, 0.2);
      border-top: 4px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Sidebar Styles */
    .sidebar {
      width: 240px;
      background-color: var(--sidebar-bg);
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
      padding: 20px 0;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 800;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      overflow-y: auto;
      overflow-x: hidden;
      display: none;
    }

    /* Collapsed Sidebar */
    .sidebar.collapsed {
      width: 70px;
    }

    /* Auto-Hide Sidebar */
    .sidebar.auto-hide {
      width: 70px;
      transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar.auto-hide:hover {
      width: 240px;
      box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
    }

    /* Sidebar Header */
    .sidebar-header {
      padding: 0px 20px 13px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .sidebar.collapsed .sidebar-header,
    .sidebar.auto-hide .sidebar-header {
      justify-content: center;
      padding: 0px 0px 14px;
    }

    .sidebar.auto-hide:hover .sidebar-header {
      justify-content: space-between;
      padding: 0px 20px 13px;
    }

    .admin-header {
      display: flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, var(--sidebar-accent) 0%, var(--co-color) 100%);
      padding: 8px 12px;
      border-radius: 8px;
      color: white;
      box-shadow: 0 4px 12px rgba(52, 116, 51, 0.2);
    }

    .sidebar.collapsed .admin-header,
    .sidebar.auto-hide .admin-header {
      display: none;
    }

    .sidebar.auto-hide:hover .admin-header {
      display: flex;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateX(-10px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .admin-icon {
      font-size: 16px;
    }

    .admin-title {
      font-weight: 700;
      font-size: 16px;
      letter-spacing: 0.5px;
    }

    /* Menu Toggle */
    .menu-toggle {
      cursor: pointer;
      color: #6b7280;
      transition: all 0.3s;
      padding: 6px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f9fafb;
      width: 36px;
      height: 36px;
      font-size: 24px;
    }

    .menu-toggle:hover {
      color: var(--sidebar-accent);
      background-color: var(--hover-bg);
    }

    /* Auto-hide: Laging visible ang menu button */
    .sidebar.auto-hide .menu-toggle {
      display: flex !important;
    }

    .sidebar.auto-hide:hover .menu-toggle {
      display: flex;
    }

    /* User Section */
    .user-section {
      padding: 0 20px 20px 20px;
      margin-bottom: 20px;
      border-bottom: 1px solid #e5e7eb;
    }

    .sidebar.collapsed .user-section,
    .sidebar.auto-hide .user-section {
      display: none;
    }

    .sidebar.auto-hide:hover .user-section {
      display: block;
      animation: fadeIn 0.3s ease 0.1s both;
    }

    .user-info {
      background-color: #f9fafb;
      padding: 10px 12px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-icon {
      width: 36px;
      height: 36px;
      background-color: var(--sidebar-accent);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 16px;
      flex-shrink: 0;
    }

    .user-details {
      flex: 1;
      min-width: 0;
      overflow: hidden;
    }

    .user-email {
      font-size: 13px;
      color: #1f2937;
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .user-status {
      font-size: 11px;
      color: #6b7280;
      display: flex;
      align-items: center;
      gap: 4px;
      margin-top: 2px;
    }

    .status-dot {
      width: 6px;
      height: 6px;
      background-color: #10b981;
      border-radius: 50%;
    }

    /* Navigation Menu */
    .sidebar ul {
      list-style: none;
      padding-left: 0;
    }

    .sidebar ul li {
      margin-bottom: 5px;
    }

    .sidebar ul li a {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      border-radius: 8px;
      color: var(--sidebar-text);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      margin: 0 15px;
    }

    .sidebar.collapsed ul li a,
    .sidebar.auto-hide ul li a {
      justify-content: center;
      padding: 12px;
      margin: 0 10px;
    }

    .sidebar.auto-hide:hover ul li a {
      justify-content: flex-start;
      padding: 12px 15px;
      margin: 0 15px;
    }

    .sidebar ul li a:hover {
      background-color: var(--hover-bg);
      color: var(--sidebar-accent);
      transform: translateX(5px);
    }

    .sidebar ul li.active a {
      background-color: var(--active-bg);
      color: var(--active-text);
      box-shadow: 0 4px 6px rgba(52, 116, 51, 0.2);
    }

    .sidebar ul li.active a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 70%;
      background-color: var(--active-text);
      border-radius: 0 4px 4px 0;
    }

    .sidebar ul li a .material-symbols-outlined {
      font-size: var(--icon-size);
      width: 24px;
      text-align: center;
    }

    .link-text {
      transition: all 0.3s ease;
      font-size: 14px;
      margin-left: 12px;
    }

    .sidebar.collapsed .link-text,
    .sidebar.auto-hide .link-text {
      display: none;
    }

    .sidebar.auto-hide:hover .link-text {
      display: inline;
      animation: fadeIn 0.3s ease 0.2s both;
    }

    /* Logout Button */
    .logout-container {
      position: absolute;
      bottom: 30px;
      width: 100%;
      padding: 0 15px;
    }

    .sidebar.collapsed .logout-container,
    .sidebar.auto-hide .logout-container {
      padding: 0 10px;
    }

    .sidebar.auto-hide:hover .logout-container {
      padding: 0 15px;
    }

    .logout-btn {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      border-radius: 8px;
      background-color: #fee2e2;
      color: #dc2626;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      border: none;
      width: 100%;
      cursor: pointer;
      font-size: 14px;
    }

    .sidebar.collapsed .logout-btn,
    .sidebar.auto-hide .logout-btn {
      justify-content: center;
      padding: 12px;
    }

    .sidebar.auto-hide:hover .logout-btn {
      justify-content: flex-start;
      padding: 12px 15px;
    }

    .sidebar.collapsed .logout-btn span:last-child,
    .sidebar.auto-hide .logout-btn span:last-child {
      display: none;
    }

    .sidebar.auto-hide:hover .logout-btn span:last-child {
      display: inline;
      animation: fadeIn 0.3s ease 0.3s both;
    }

    .logout-btn:hover {
      background-color: #fecaca;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(220, 38, 38, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }
      
      .sidebar .admin-header,
      .sidebar .user-section,
      .sidebar .link-text {
        display: none;
      }
      
      .sidebar .sidebar-header {
        justify-content: center;
        padding: 15px 0 15px;
        margin-bottom: 30px;
        border-bottom: none;
      }
      
      .sidebar ul li a {
        justify-content: center;
        padding: 12px;
        margin: 0 10px;
      }
      
      .logout-btn span:last-child {
        display: none;
      }
      
      .logout-btn {
        justify-content: center;
        padding: 12px;
      }
      
      /* Auto-hide menu button sa mobile */
      .sidebar.auto-hide .menu-toggle {
        display: flex !important;
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        transform: translateX(-100%);
        width: 240px;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .sidebar.collapsed,
      .sidebar.auto-hide {
        transform: translateX(-100%);
        width: 70px;
      }
      
      .sidebar.collapsed.active,
      .sidebar.auto-hide.active {
        transform: translateX(0);
      }
    }
  </style>
</head>
<body>
  <!-- Quick Redirect Overlay -->
  <div class="redirect-overlay" id="redirectOverlay">
    <div class="redirect-spinner"></div>
    <h2>Checking Access...</h2>
    <p>Please wait while we verify your credentials.</p>
  </div>

  <!-- Sidebar -->
  <div class="sidebar <?php 
      if ($sidebarMode === 'auto-hide') echo 'auto-hide';
      elseif ($sidebarMode === 'manual') {
          $isCollapsed = isset($_SESSION['sidebar_collapsed']) ? $_SESSION['sidebar_collapsed'] : false;
          if ($isCollapsed) echo 'collapsed';
      }
  ?>" id="sidebar">
    <!-- Header Area with Admin Panel and Toggle -->
    <div class="sidebar-header">
      <div class="admin-header">
        <div class="admin-icon">
          <i class="fas fa-crown"></i>
        </div>
        <div class="admin-title">Admin Panel</div>
      </div>
      <!-- MENU BUTTON: Laging visible ngayon -->
      <span class="material-symbols-outlined menu-toggle" onclick="window.toggleSidebar()" id="menuButton">menu</span>
    </div>

    <!-- User Section -->
    <div class="user-section">
      <div class="user-info">
        <div class="user-icon">
          <i class="fas fa-user"></i>
        </div>
        <div class="user-details">
          <div class="user-email" id="userEmail">admin@serviceco.com</div>
          <div class="user-status">
            <div class="status-dot"></div>
            <span>Online</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Navigation Menu -->
    <ul>
      <li class="<?= (basename($_SERVER['PHP_SELF']) == 'Dashboard.php') ? 'active' : ''; ?>">
        <a href="Dashboard.php">
          <span class="material-symbols-outlined">dashboard</span>
          <span class="link-text">Dashboard</span>
        </a>
      </li>
      <li class="<?= (basename($_SERVER['PHP_SELF']) == 'UserManagement.php') ? 'active' : ''; ?>">
        <a href="UserManagement.php">
          <span class="material-symbols-outlined">group</span>
          <span class="link-text">User Management</span>
        </a>
      </li>
      <li class="<?= (basename($_SERVER['PHP_SELF']) == 'App.php') ? 'active' : ''; ?>">
        <a href="App.php">
          <span class="material-symbols-outlined">apps</span>
          <span class="link-text">App Management</span>
        </a>
      </li>
      <li class="<?= (basename($_SERVER['PHP_SELF']) == 'Report.php') ? 'active' : ''; ?>">
        <a href="Report.php">
          <span class="material-symbols-outlined">summarize</span>
          <span class="link-text">Reports</span>
        </a>
      </li>
    </ul>

    <!-- Logout Button -->
    <div class="logout-container">
      <button onclick="logout()" class="logout-btn">
        <span class="material-symbols-outlined">logout</span>
        <span class="link-text">Logout</span>
      </button>
    </div>
  </div>

  <script>
    // GLOBAL STATE
    window.sidebarState = {
        mode: '<?php echo $sidebarMode; ?>',
        isCollapsed: <?php echo (isset($_SESSION['sidebar_collapsed']) && $_SESSION['sidebar_collapsed']) ? 'true' : 'false'; ?>,
        isAutoHide: <?php echo ($sidebarMode === 'auto-hide') ? 'true' : 'false'; ?>
    };
    
    // FAST AUTH CHECK
    document.addEventListener('DOMContentLoaded', function() {
        const isLoggedIn = localStorage.getItem('is_logged_in') === 'true';
        const userEmail = localStorage.getItem('user_email');
        const userRole = localStorage.getItem('user_role');
        
        if (!isLoggedIn || !userEmail || userRole !== 'Admin') {
            setTimeout(() => {
                window.location.href = 'Login.php';
            }, 500);
            return;
        }
        
        // Logged in - show sidebar immediately
        document.getElementById('redirectOverlay').style.display = 'none';
        document.getElementById('sidebar').style.display = 'block';
        
        // Show user info
        document.getElementById('userEmail').textContent = userEmail;
        
        // Initialize sidebar
        initializeSidebar();
        
        // Force update on load
        setTimeout(() => {
            window.updateAllPositions();
        }, 100);
    });

    function initializeSidebar() {
        const sidebar = document.getElementById("sidebar");
        const menuButton = document.getElementById("menuButton");
        
        // Check current mode
        const isAutoHide = sidebar.classList.contains('auto-hide');
        const isManual = !isAutoHide;
        
        // Sidebar toggle function - FIXED VERSION
        window.toggleSidebar = function() {
            console.log("Toggle sidebar clicked. Current mode:", window.sidebarState.mode);
            
            // Auto-hide mode: dapat hindi mag-toggle, pero mag-e-expand lang sa hover
            if (window.sidebarState.mode === 'auto-hide') {
                console.log("Auto-hide mode: Sidebar expands on hover only, not on click");
                
                // Para sa auto-hide, hindi dapat mag-toggle sa click
                // Pero kung gusto mo pa ring mag-toggle kahit auto-hide, alisin ang return statement
                // For now, disable toggling in auto-hide mode
                return;
            }
            
            // Manual mode: toggle collapsed state
            const wasCollapsed = sidebar.classList.contains("collapsed");
            
            if (wasCollapsed) {
                sidebar.classList.remove("collapsed");
            } else {
                sidebar.classList.add("collapsed");
            }
            
            // Update global state
            window.sidebarState.isCollapsed = !wasCollapsed;
            window.sidebarState.isAutoHide = false;
            window.sidebarState.mode = 'manual';
            
            // Save to session storage
            localStorage.setItem('sidebarCollapsed', !wasCollapsed);
            
            // Dispatch event for navbar and content to listen to
            const event = new CustomEvent('sidebarToggled', {
                detail: {
                    collapsed: !wasCollapsed,
                    mode: 'manual'
                }
            });
            document.dispatchEvent(event);
            
            // Mobile handling
            if (window.innerWidth <= 576) {
                sidebar.classList.toggle("active");
            }
            
            // AJAX call to save state
            const isCollapsed = sidebar.classList.contains('collapsed');
            
            fetch('save_sidebar_state.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'collapsed=' + isCollapsed
            }).catch(error => console.error('Error saving sidebar state:', error));
            
            // Update all positions
            window.updateAllPositions();
            
            // Force update content positions
            setTimeout(() => {
                if (typeof window.adjustContentPosition === 'function') {
                    window.adjustContentPosition();
                }
                window.updateAllPositions();
            }, 50);
        };
        
        // Logout function
        window.logout = function() {
            localStorage.setItem('just_logged_out', 'true');
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = 'Logout.php';
        };
        
        // Active menu highlighting
        const currentPage = window.location.pathname.split('/').pop();
        const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
        
        sidebarLinks.forEach(link => {
            const linkPage = link.href.split('/').pop();
            if (linkPage === currentPage) {
                link.parentElement.classList.add('active');
            }
        });
        
        // Auto-hide hover effects
        if (isAutoHide) {
            // Make sure menu button is visible in auto-hide mode
            if (menuButton) {
                menuButton.style.display = 'flex';
            }
            
            sidebar.addEventListener('mouseenter', function() {
                if (window.sidebarState.mode === 'auto-hide') {
                    // Dispatch event para malaman ng navbar/content na nag-expand ang sidebar
                    const event = new CustomEvent('sidebarAutoHide', {
                        detail: { expanded: true }
                    });
                    document.dispatchEvent(event);
                    
                    // Update positions
                    setTimeout(() => {
                        window.updateAllPositions();
                        if (typeof window.adjustContentPosition === 'function') {
                            window.adjustContentPosition();
                        }
                    }, 50);
                }
            });
            
            sidebar.addEventListener('mouseleave', function() {
                if (window.sidebarState.mode === 'auto-hide') {
                    // Dispatch event para malaman ng navbar/content na nag-collapse ang sidebar
                    const event = new CustomEvent('sidebarAutoHide', {
                        detail: { expanded: false }
                    });
                    document.dispatchEvent(event);
                    
                    // Update positions
                    setTimeout(() => {
                        window.updateAllPositions();
                        if (typeof window.adjustContentPosition === 'function') {
                            window.adjustContentPosition();
                        }
                    }, 50);
                }
            });
        }
        
        // GLOBAL FUNCTION to change sidebar mode
        window.changeSidebarMode = function(newMode) {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.querySelector('.menu-toggle');
            
            console.log('Changing sidebar mode to:', newMode);
            
            // Remove current mode classes
            sidebar.classList.remove('auto-hide', 'collapsed');
            
            if (newMode === 'auto-hide') {
                // Auto-hide mode
                sidebar.classList.add('auto-hide');
                
                // Show menu button in auto-hide mode
                if (menuButton) {
                    menuButton.style.display = 'flex';
                }
                
                // Update global state
                window.sidebarState.mode = 'auto-hide';
                window.sidebarState.isAutoHide = true;
                window.sidebarState.isCollapsed = false;
                
                // Remove hover events first
                sidebar.removeEventListener('mouseenter', arguments.callee);
                sidebar.removeEventListener('mouseleave', arguments.callee);
                
                // Add hover events for auto-hide
                sidebar.addEventListener('mouseenter', function() {
                    if (window.sidebarState.mode === 'auto-hide') {
                        const event = new CustomEvent('sidebarAutoHide', {
                            detail: { expanded: true }
                        });
                        document.dispatchEvent(event);
                        
                        setTimeout(() => {
                            window.updateAllPositions();
                            if (typeof window.adjustContentPosition === 'function') {
                                window.adjustContentPosition();
                            }
                        }, 50);
                    }
                });
                
                sidebar.addEventListener('mouseleave', function() {
                    if (window.sidebarState.mode === 'auto-hide') {
                        const event = new CustomEvent('sidebarAutoHide', {
                            detail: { expanded: false }
                        });
                        document.dispatchEvent(event);
                        
                        setTimeout(() => {
                            window.updateAllPositions();
                            if (typeof window.adjustContentPosition === 'function') {
                                window.adjustContentPosition();
                            }
                        }, 50);
                    }
                });
                
            } else {
                // Manual mode
                window.sidebarState.mode = 'manual';
                window.sidebarState.isAutoHide = false;
                
                // Check if it should start collapsed
                const shouldCollapse = localStorage.getItem('sidebarCollapsed') === 'true';
                if (shouldCollapse) {
                    sidebar.classList.add('collapsed');
                    window.sidebarState.isCollapsed = true;
                } else {
                    window.sidebarState.isCollapsed = false;
                }
                
                // Show menu button
                if (menuButton) {
                    menuButton.style.display = 'flex';
                }
                
                // Remove auto-hide hover events
                sidebar.removeEventListener('mouseenter', arguments.callee);
                sidebar.removeEventListener('mouseleave', arguments.callee);
            }
            
            // Dispatch mode changed event
            const event = new CustomEvent('sidebarModeChanged', {
                detail: { 
                    mode: newMode,
                    collapsed: sidebar.classList.contains('collapsed')
                }
            });
            document.dispatchEvent(event);
            
            // Update positions
            window.updateAllPositions();
            
            // Force content adjustment
            setTimeout(() => {
                if (typeof window.adjustContentPosition === 'function') {
                    window.adjustContentPosition();
                }
            }, 100);
            
            // Save to localStorage
            localStorage.setItem('sidebarMode', newMode);
        };
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('menuButton');
            
            if (window.innerWidth <= 576 && 
                !sidebar.contains(e.target) && 
                e.target !== menuButton && 
                !menuButton.contains(e.target)) {
                sidebar.classList.remove('active');
                window.updateAllPositions();
            }
        });
        
        // Window resize handler
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            
            if (window.innerWidth > 576) {
                sidebar.classList.remove('active');
            }
            
            window.updateAllPositions();
        });
        
        // Initialize based on current mode
        if (window.sidebarState.mode === 'auto-hide') {
            window.changeSidebarMode('auto-hide');
        } else {
            window.changeSidebarMode('manual');
        }
    }
    
    // POSITION UPDATE FUNCTION
    window.updateAllPositions = function() {
        const sidebar = document.getElementById("sidebar");
        
        if (!sidebar) return;
        
        const isAutoHide = sidebar.classList.contains('auto-hide');
        const isCollapsed = sidebar.classList.contains('collapsed');
        const isHovered = sidebar.matches(':hover') && isAutoHide;
        
        // Calculate sidebar width
        let sidebarWidth = 240; // Default expanded
        
        if (isAutoHide) {
            if (isHovered) {
                sidebarWidth = 240; // Hovered = expanded
            } else {
                sidebarWidth = 70; // Not hovered = collapsed
            }
        } else {
            if (isCollapsed) {
                sidebarWidth = 70;
            } else {
                sidebarWidth = 240;
            }
        }
        
        console.log('Sidebar position update:', {
            mode: isAutoHide ? 'auto-hide' : 'manual',
            collapsed: isCollapsed,
            hovered: isHovered,
            width: sidebarWidth
        });
        
        // Update all content elements
        const contentElements = document.querySelectorAll(".dashboard-content, .account-content, .user-content, .app-content, .report-content");
        contentElements.forEach(el => {
            el.style.marginLeft = sidebarWidth + 'px';
            el.style.width = `calc(100% - ${sidebarWidth}px)`;
            el.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        // Update navbar
        const navbar = document.getElementById("navbar");
        if (navbar) {
            navbar.style.left = sidebarWidth + 'px';
        }
    };
</script>
</body>
</html>