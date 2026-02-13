<?php
// Dashboard.php
session_start();

// Get current sidebar mode from session
$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tricycle Booking System - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Dashboard Content - using your sidebar and navbar */
        .dashboard-content {
            margin-top: 70px;
            padding: 25px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        .sidebar.collapsed ~ .dashboard-content,
        .sidebar.auto-hide ~ .dashboard-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        /* IMPORTANT: FIXED - Direct sibling selector for auto-hide hover */
        .sidebar.auto-hide:hover ~ .dashboard-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        /* Welcome Section */
        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .welcome-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .welcome-subtitle {
            color: #666;
            font-size: 14px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-left: 4px solid #347433;
        }

        .stat-card.pending {
            border-left-color: #f59e0b;
        }

        .stat-card.approved {
            border-left-color: #347433;
        }

        .stat-card.rejected {
            border-left-color: #ef4444;
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0fdf4;
            color: #347433;
        }

        .stat-card.pending .stat-icon {
            background-color: #fef3c7;
            color: #f59e0b;
        }

        .stat-card.rejected .stat-icon {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 13px;
            color: #347433;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Main Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .chart-card, .activities-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
        }

        .chart-card {
            min-height: 400px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            flex-shrink: 0;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .view-all {
            color: #347433;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* Chart Container */
        .chart-container {
            flex: 1;
            position: relative;
            width: 100%;
            min-height: 300px;
        }

        #registrationChart {
            width: 100% !important;
            height: 100% !important;
        }

        /* Activities */
        .activities-list {
            margin-top: 10px;
            flex: 1;
            overflow-y: auto;
            max-height: 300px;
        }

        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-text {
            font-size: 14px;
            color: #555;
            margin-bottom: 4px;
        }

        .activity-text strong {
            color: #333;
        }

        .activity-time {
            font-size: 12px;
            color: #888;
        }

        /* Complaints Grid */
        .complaints-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .complaints-grid {
                grid-template-columns: 1fr;
            }
        }

        .complaint-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .complaint-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .complaint-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fef3c7;
            color: #f59e0b;
        }

        .complaint-card.resolved .complaint-icon {
            background-color: #f0fdf4;
            color: #347433;
        }

        .complaint-info h3 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .complaint-info p {
            font-size: 13px;
            color: #666;
        }

        .complaint-value {
            font-size: 32px;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin: 10px 0;
        }

        .complaint-percentage {
            font-size: 13px;
            color: #347433;
            text-align: center;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 15px;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 15px;
            }
            
            .sidebar.collapsed ~ .dashboard-content,
            .sidebar.auto-hide ~ .dashboard-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .sidebar.auto-hide:hover ~ .dashboard-content {
                margin-left: 240px !important;
                width: calc(100% - 240px) !important;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-card {
                min-height: 350px;
            }
            
            .chart-container {
                min-height: 250px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-content {
                padding: 12px;
            }
            
            .stats-grid {
                gap: 15px;
            }
        }

        /* Smooth sidebar transition */
        .dashboard-content, .sidebar, .navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <?php
        // Simulated data
        $totalApplicants = 150;
        $pendingApplicants = 18;
        $approvedDrivers = 120;
        $rejectedDrivers = 14;
        $pendingComplaints = 12;
        $resolvedComplaints = 30;

        // Recent activities data
        $recentActivities = [
            ['user' => 'Juan Dela Cruz', 'action' => 'applied for driver registration', 'time' => '2 mins ago'],
            ['user' => 'Maria Santos', 'action' => 'was approved as driver', 'time' => '15 mins ago'],
            ['user' => 'Pedro Gonzales', 'action' => 'submitted a complaint', 'time' => '30 mins ago'],
            ['user' => 'Ana Reyes', 'action' => 'updated profile information', 'time' => '1 hour ago'],
            ['user' => 'Luis Torres', 'action' => 'was rejected as driver', 'time' => '2 hours ago'],
        ];

        // Graph data
        $monthlyData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'data' => [65, 59, 80, 81, 56, 55, 40, 72, 85, 92, 78, 90]
        ];
        ?>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="welcome-title">Dashboard Overview</h1>
            <p class="welcome-subtitle">Monitor your tricycle booking system performance.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Total Applicants -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Applicants</div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $totalApplicants; ?></div>
                <div class="stat-change">+12% from last month</div>
            </div>

            <!-- Pending Applicants -->
            <div class="stat-card pending">
                <div class="stat-header">
                    <div class="stat-title">Pending Applicants</div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $pendingApplicants; ?></div>
                <div class="stat-change">-3% from last week</div>
            </div>

            <!-- Approved Drivers -->
            <div class="stat-card approved">
                <div class="stat-header">
                    <div class="stat-title">Approved Drivers</div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $approvedDrivers; ?></div>
                <div class="stat-change">+8% from last month</div>
            </div>

            <!-- Rejected Drivers -->
            <div class="stat-card rejected">
                <div class="stat-header">
                    <div class="stat-title">Rejected Drivers</div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $rejectedDrivers; ?></div>
                <div class="stat-change negative">+2% from last month</div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Driver Registration Graph -->
            <div class="chart-card">
                <div class="card-header">
                    <h2 class="card-title">Register Driver Graph</h2>
                    <a href="#" class="view-all">View all</a>
                </div>
                <div class="chart-container">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="activities-card">
                <div class="card-header">
                    <h2 class="card-title">Recent Activity</h2>
                    <a href="ActivityLog.php" class="view-all">View all</a>
                </div>
                
                <div class="activities-list">
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <p class="activity-text">
                            <strong><?php echo $activity['user']; ?></strong> <?php echo $activity['action']; ?>
                        </p>
                        <p class="activity-time"><?php echo $activity['time']; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Complaints Summary -->
        <div class="complaints-grid">
            <!-- Pending Complaints -->
            <div class="complaint-card">
                <div class="complaint-header">
                    <div class="complaint-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="complaint-info">
                        <h3>Pending Complaints</h3>
                        <p>Awaiting resolution</p>
                    </div>
                </div>
                <div class="complaint-value"><?php echo $pendingComplaints; ?></div>
                <div class="complaint-percentage">+4 from last week</div>
            </div>

            <!-- Resolved Complaints -->
            <div class="complaint-card resolved">
                <div class="complaint-header">
                    <div class="complaint-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="complaint-info">
                        <h3>Resolved Complaints</h3>
                        <p>Successfully resolved</p>
                    </div>
                </div>
                <div class="complaint-value"><?php echo $resolvedComplaints; ?></div>
                <div class="complaint-percentage">+12 from last week</div>
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
        
        // Initialize Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('registrationChart').getContext('2d');
            
            // Create gradient for the chart
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');
            
            const registrationChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($monthlyData['labels']); ?>,
                    datasets: [{
                        label: 'Driver Registrations',
                        data: <?php echo json_encode($monthlyData['data']); ?>,
                        borderColor: '#347433',
                        backgroundColor: gradient,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#347433',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 14
                            },
                            padding: 12,
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                padding: 10
                            },
                            title: {
                                display: true,
                                text: 'Number of Drivers',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#666'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                padding: 10
                            },
                            title: {
                                display: true,
                                text: 'Months',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#666'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    elements: {
                        line: {
                            tension: 0.4
                        }
                    }
                }
            });
            
            // Update chart on window resize
            window.addEventListener('resize', function() {
                registrationChart.resize();
            });

            // Listen for sidebar events from Sidebar.php
            document.addEventListener('sidebarModeChanged', function(e) {
                console.log('Dashboard: Sidebar mode changed to:', e.detail.mode);
                currentSidebarMode = e.detail.mode;
                adjustDashboardPosition();
            });
            
            // Listen for sidebar auto-hide hover events
            document.addEventListener('sidebarAutoHide', function(e) {
                console.log('Dashboard: Sidebar auto-hide hover:', e.detail.expanded);
                
                // Force immediate position adjustment on hover
                setTimeout(() => {
                    adjustDashboardPosition();
                }, 10);
            });
            
            // Listen for sidebar toggle events
            document.addEventListener('sidebarToggled', function(e) {
                console.log('Dashboard: Sidebar manually toggled:', e.detail.collapsed);
                
                // Force immediate position adjustment
                setTimeout(() => {
                    adjustDashboardPosition();
                }, 10);
            });
            
            // Listen for global sidebar position updates
            if (typeof window.updateAllPositions === 'function') {
                // Hook into the global update function
                const originalUpdateAllPositions = window.updateAllPositions;
                window.updateAllPositions = function() {
                    originalUpdateAllPositions();
                    adjustDashboardPosition();
                };
            }
            
            // Initial position adjustment
            setTimeout(() => {
                adjustDashboardPosition();
            }, 100);
            
            // Set up periodic check for sidebar changes
            setInterval(() => {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    const currentClasses = sidebar.className;
                    
                    // Check if classes have changed
                    if (window.lastSidebarClasses !== currentClasses) {
                        window.lastSidebarClasses = currentClasses;
                        adjustDashboardPosition();
                    }
                }
            }, 100);
        });

        // FUNCTION: Adjust dashboard position based on sidebar state
        function adjustDashboardPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.dashboard-content');
            
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
            
            console.log('Dashboard adjusting position:', {
                isAutoHide,
                isCollapsed,
                isHovered,
                sidebarWidth
            });
            
            // Update dashboard content position immediately
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
        window.adjustDashboardPosition = adjustDashboardPosition;
    </script>
</body>
</html>