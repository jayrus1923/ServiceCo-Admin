<?php
// RecentActivities.php
session_start();

// Get current sidebar mode from session
$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tricycle Booking System - Recent Activities</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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

        /* Recent Activities Content */
        .main-content {
            margin-top: 70px;
            padding: 30px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        .sidebar.collapsed ~ .main-content,
        .sidebar.auto-hide ~ .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        /* IMPORTANT: FIXED - Direct sibling selector for auto-hide hover */
        .sidebar.auto-hide:hover ~ .main-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        /* Header Section */
        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 16px;
            font-weight: 400;
        }

        /* Controls Section */
        .controls-section {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .search-container {
            position: relative;
            flex: 1;
            min-width: 300px;
            max-width: 400px;
        }

        .search-box {
            width: 100%;
            padding: 14px 20px 14px 48px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            background-color: white;
            transition: all 0.3s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .search-box:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Filter Buttons */
        .btn-filter {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #e5e7eb;
            background-color: white;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-filter:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        .btn-filter.active {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }

        .btn-filter.active:hover {
            background-color: #0da271;
            border-color: #0da271;
        }

        /* Main Content Card */
        .content-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            padding: 24px 30px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        .card-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .export-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            background-color: white;
            border: 1px solid #10b981;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .export-btn:hover {
            background-color: #f0fdf4;
        }

        /* Activities Table */
        .activities-table-container {
            overflow-x: auto;
            width: 100%;
        }

        .activities-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .activities-table thead {
            background-color: #f9fafb;
        }

        .activities-table th {
            padding: 18px 24px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .activities-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s;
        }

        .activities-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .activities-table td {
            padding: 20px 24px;
            font-size: 15px;
            color: #4b5563;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #34d399);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .user-id {
            font-size: 13px;
            color: #9ca3af;
        }

        .activity-type {
            font-weight: 500;
            color: #1f2937;
        }

        .activity-desc {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .timestamp {
            color: #6b7280;
            font-weight: 500;
        }

        .timestamp-detail {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            min-width: 100px;
            justify-content: center;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Pagination */
        .pagination-container {
            background-color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-info {
            font-size: 14px;
            color: #6b7280;
        }

        .pagination {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: white;
            color: #374151;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .page-link:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        .page-link.active {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }

        .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-link.arrow {
            font-size: 16px;
        }

        .ellipsis {
            color: #9ca3af;
            font-size: 14px;
            padding: 0 8px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 25px;
            margin-top: 40px;
            color: #9ca3af;
            font-size: 14px;
            border-top: 1px solid #f3f4f6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 20px;
            }
            
            .sidebar.collapsed ~ .main-content,
            .sidebar.auto-hide ~ .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .sidebar.auto-hide:hover ~ .main-content {
                margin-left: 240px !important;
                width: calc(100% - 240px) !important;
            }
            
            .controls-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-container {
                min-width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 24px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .card-actions {
                justify-content: flex-start;
            }
            
            .pagination-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .activities-table {
                font-size: 14px;
            }
            
            .activities-table th,
            .activities-table td {
                padding: 14px 16px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 15px;
            }
            
            .btn-filter {
                padding: 10px 16px;
                font-size: 13px;
            }
            
            .page-link {
                width: 36px;
                height: 36px;
                font-size: 13px;
            }
        }

        /* Smooth sidebar transition */
        .main-content, .sidebar, .navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Simulated data
        $recentActivities = [
            [
                'name' => 'Severino Manalo',
                'activity_type' => 'New Driver',
                'timestamp' => 'Today, 9:45 AM',
                'status' => 'Approved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Anastacio Rivera',
                'activity_type' => 'Updated Profile',
                'timestamp' => 'Today, 8:20 AM',
                'status' => 'Approved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Florentino Ramos',
                'activity_type' => 'Submitted Documents',
                'timestamp' => 'Sep 13, 4:10 PM',
                'status' => 'Pending',
                'status_color' => '#f59e0b'
            ],
            [
                'name' => 'Isidro Bautista',
                'activity_type' => 'Permit Renewed',
                'timestamp' => 'Sep 12, 11:30 AM',
                'status' => 'Renewed',
                'status_color' => '#3b82f6'
            ],
            [
                'name' => 'Rogelio Cruz',
                'activity_type' => 'Complaint Filed',
                'timestamp' => 'Sep 11, 6:00 PM',
                'status' => 'In Review',
                'status_color' => '#8b5cf6'
            ],
            [
                'name' => 'Eduardo Santos',
                'activity_type' => 'Application Rejected',
                'timestamp' => 'Sep 10, 2:15 PM',
                'status' => 'Rejected',
                'status_color' => '#ef4444'
            ],
            [
                'name' => 'Juan Dela Cruz',
                'activity_type' => 'Driver Registration',
                'timestamp' => 'Sep 9, 10:15 AM',
                'status' => 'Approved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Maria Santos',
                'activity_type' => 'Profile Update',
                'timestamp' => 'Sep 8, 3:45 PM',
                'status' => 'Approved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Pedro Gonzales',
                'activity_type' => 'Submitted Documents',
                'timestamp' => 'Sep 7, 11:20 AM',
                'status' => 'Pending',
                'status_color' => '#f59e0b'
            ],
            [
                'name' => 'Ana Reyes',
                'activity_type' => 'Complaint Filed',
                'timestamp' => 'Sep 6, 9:30 AM',
                'status' => 'Resolved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Carlos Garcia',
                'activity_type' => 'License Renewed',
                'timestamp' => 'Sep 5, 2:15 PM',
                'status' => 'Approved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Lorna Dimagiba',
                'activity_type' => 'New Driver',
                'timestamp' => 'Sep 4, 10:45 AM',
                'status' => 'Pending',
                'status_color' => '#f59e0b'
            ],
            [
                'name' => 'Ricardo Dizon',
                'activity_type' => 'Document Verification',
                'timestamp' => 'Sep 3, 3:20 PM',
                'status' => 'Approved',
                'status_color' => '#10b981'
            ],
            [
                'name' => 'Susan Lim',
                'activity_type' => 'Complaint Filed',
                'timestamp' => 'Sep 2, 1:30 PM',
                'status' => 'In Review',
                'status_color' => '#8b5cf6'
            ],
            [
                'name' => 'Michael Tan',
                'activity_type' => 'Application Submitted',
                'timestamp' => 'Sep 1, 9:15 AM',
                'status' => 'Pending',
                'status_color' => '#f59e0b'
            ]
        ];

        // Pagination simulation
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 10;
        $totalItems = count($recentActivities);
        $totalPages = ceil($totalItems / $itemsPerPage);
        $startIndex = ($currentPage - 1) * $itemsPerPage;
        $paginatedActivities = array_slice($recentActivities, $startIndex, $itemsPerPage);
        ?>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Recent Activities</h1>
            <p class="page-subtitle">View all system activities and monitor user actions</p>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-box" placeholder="Search by name, activity type, or status...">
            </div>
            
            <div class="filter-actions">
                <button class="btn-filter active" data-filter="all">
                    <i class="fas fa-list"></i> All Activities
                </button>
                <button class="btn-filter" data-filter="pending">
                    <i class="fas fa-clock"></i> Pending
                </button>
                <button class="btn-filter" data-filter="approved">
                    <i class="fas fa-check-circle"></i> Approved
                </button>
                <button class="btn-filter" data-filter="rejected">
                    <i class="fas fa-times-circle"></i> Rejected
                </button>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Activity Log</h2>
                <div class="card-actions">
                    <button class="export-btn" onclick="exportActivities()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
            
            <div class="activities-table-container">
                <table class="activities-table" id="activitiesTable">
                    <thead>
                        <tr>
                            <th width="25%">User</th>
                            <th width="25%">Activity Type</th>
                            <th width="20%">Timestamp</th>
                            <th width="20%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginatedActivities as $index => $activity): 
                            $userId = 'USER' . str_pad($startIndex + $index + 1, 4, '0', STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo substr($activity['name'], 0, 1); ?>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name"><?php echo $activity['name']; ?></div>
                                        <div class="user-id"><?php echo $userId; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="activity-type"><?php echo $activity['activity_type']; ?></div>
                                <div class="activity-desc">Tricycle Booking System</div>
                            </td>
                            <td>
                                <div class="timestamp"><?php echo $activity['timestamp']; ?></div>
                                <div class="timestamp-detail">System recorded</div>
                            </td>
                            <td>
                                <span class="status-badge" style="background-color: <?php echo $activity['status_color']; ?>10; color: <?php echo $activity['status_color']; ?>;">
                                    <span class="status-dot" style="background-color: <?php echo $activity['status_color']; ?>;"></span>
                                    <?php echo $activity['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-filter" style="padding: 6px 12px; font-size: 13px;" onclick="viewDetails('<?php echo $activity['name']; ?>')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                Showing <?php echo $startIndex + 1; ?> to <?php echo min($startIndex + $itemsPerPage, $totalItems); ?> of <?php echo $totalItems; ?> entries
            </div>
            
            <div class="pagination">
                <a href="?page=<?php echo max(1, $currentPage - 1); ?>" class="page-link arrow <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <?php 
                // Display first page
                if ($currentPage > 3): ?>
                    <a href="?page=1" class="page-link <?php echo 1 == $currentPage ? 'active' : ''; ?>">
                        1
                    </a>
                    <?php if ($currentPage > 4): ?>
                        <span class="ellipsis">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php 
                // Display pages around current page
                for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $currentPage ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php 
                // Display last page
                if ($currentPage < $totalPages - 2): ?>
                    <?php if ($currentPage < $totalPages - 3): ?>
                        <span class="ellipsis">...</span>
                    <?php endif; ?>
                    <a href="?page=<?php echo $totalPages; ?>" class="page-link <?php echo $totalPages == $currentPage ? 'active' : ''; ?>">
                        <?php echo $totalPages; ?>
                    </a>
                <?php endif; ?>
                
                <a href="?page=<?php echo min($totalPages, $currentPage + 1); ?>" class="page-link arrow <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Tricycle Booking System &copy; <?php echo date('Y'); ?>. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Store current sidebar mode
        let currentSidebarMode = '<?php echo $sidebarMode; ?>';
        
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for sidebar events from Sidebar.php
            document.addEventListener('sidebarModeChanged', function(e) {
                console.log('Recent Activities: Sidebar mode changed to:', e.detail.mode);
                currentSidebarMode = e.detail.mode;
                adjustContentPosition();
            });
            
            // Listen for sidebar auto-hide hover events
            document.addEventListener('sidebarAutoHide', function(e) {
                console.log('Recent Activities: Sidebar auto-hide hover:', e.detail.expanded);
                
                // Force immediate position adjustment on hover
                setTimeout(() => {
                    adjustContentPosition();
                }, 10);
            });
            
            // Listen for sidebar toggle events
            document.addEventListener('sidebarToggled', function(e) {
                console.log('Recent Activities: Sidebar manually toggled:', e.detail.collapsed);
                
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
            
            // Filter buttons functionality
            document.querySelectorAll('.btn-filter[data-filter]').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all filter buttons
                    document.querySelectorAll('.btn-filter[data-filter]').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    const rows = document.querySelectorAll('.activities-table tbody tr');
                    
                    rows.forEach(row => {
                        const status = row.querySelector('.status-badge').textContent.trim().toLowerCase();
                        
                        if (filter === 'all' || status.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    updatePaginationInfo();
                });
            });

            // Search functionality
            const searchBox = document.querySelector('.search-box');
            searchBox.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('.activities-table tbody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                updatePaginationInfo(visibleCount);
            });
        });

        // FUNCTION: Adjust content position based on sidebar state
        function adjustContentPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.main-content');
            
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
            
            console.log('Recent Activities adjusting position:', {
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
        
        // Make the function available globally
        window.adjustContentPosition = adjustContentPosition;

        // Export button functionality
        function exportActivities() {
            let csv = [];
            let rows = document.querySelectorAll("#activitiesTable tr");
            
            if (rows.length === 0) {
                alert('No data to export!');
                return;
            }

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");

                for (let j = 0; j < cols.length; j++) {
                    // Clean the text and add quotes
                    let text = cols[j].innerText.replace(/"/g, '""');
                    row.push('"' + text + '"');
                }

                csv.push(row.join(","));
            }

            // Add header for metadata
            const header = [
                '"Tricycle Booking System - Recent Activities Export"',
                `"Export Date: ${new Date().toLocaleDateString()}"`,
                `"Export Time: ${new Date().toLocaleTimeString()}"`,
                '""' // Empty row
            ];
            csv.unshift(...header);

            // Create and download CSV file
            let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
            let downloadLink = document.createElement("a");

            downloadLink.download = `recent-activities-${new Date().toISOString().split('T')[0]}.csv`;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";

            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            
            // Show success message
            alert('Activities exported successfully!');
        }

        // View details function
        function viewDetails(userName) {
            alert(`Viewing details for: ${userName}`);
        }

        // Update pagination info
        function updatePaginationInfo(visibleCount = null) {
            const infoElement = document.querySelector('.pagination-info');
            if (!infoElement) return;
            
            if (visibleCount !== null) {
                infoElement.textContent = `Showing ${visibleCount} of ${visibleCount} filtered entries`;
            }
        }
    </script>
</body>
</html>