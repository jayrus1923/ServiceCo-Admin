<?php
session_start();
date_default_timezone_set('Asia/Manila');

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

$displayPrefs = isset($_SESSION['display_preferences']) ? $_SESSION['display_preferences'] : [];
$dateFormat = isset($displayPrefs['date_format']) ? $displayPrefs['date_format'] : 'mm/dd/yyyy';
$timeFormat = isset($displayPrefs['time_format']) ? $displayPrefs['time_format'] : '12';

$now = new DateTime('now', new DateTimeZone('Asia/Manila'));

switch($dateFormat) {
    case 'mm/dd/yyyy':
        $formattedDate = $now->format('m/d/Y');
        break;
    case 'dd/mm/yyyy':
        $formattedDate = $now->format('d/m/Y');
        break;
    case 'yyyy-mm-dd':
        $formattedDate = $now->format('Y-m-d');
        break;
    default:
        $formattedDate = $now->format('m/d/Y');
}

$hours = (int)$now->format('H');
$minutes = $now->format('i');

if ($timeFormat === '12') {
    $ampm = $hours >= 12 ? 'PM' : 'AM';
    $displayHour = $hours % 12;
    $displayHour = $displayHour ? $displayHour : 12;
    $formattedTime = sprintf('%02d:%s %s', $displayHour, $minutes, $ampm);
} else {
    $formattedTime = sprintf('%02d:%s', $hours, $minutes);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tricycle Booking System - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .sidebar.auto-hide:hover ~ .dashboard-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

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

        .datetime-display {
            font-weight: 500;
            color: #10b981;
            margin-left: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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
            color: #666;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            min-height: 400px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
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

        .chart-container {
            flex: 1;
            position: relative;
            width: 100%;
            min-height: 300px;
        }

        #driverChart, #complaintChart {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
        }

        .complaints-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .complaint-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .complaint-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

        .footer {
            text-align: center;
            padding: 15px;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }

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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .chart-card {
                min-height: 350px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <div class="dashboard-content">
        <div class="welcome-section">
            <h1 class="welcome-title">Dashboard Overview</h1>
            <p class="welcome-subtitle">
                Monitor your tricycle booking system performance.
                <span class="datetime-display"><?php echo $formattedDate; ?> at <?php echo $formattedTime; ?></span>
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Applicants</div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-value" id="totalApplicants">0</div>
                <div class="stat-change">From Firebase</div>
            </div>

            <div class="stat-card pending">
                <div class="stat-header">
                    <div class="stat-title">Pending Review</div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-value" id="pendingCount">0</div>
                <div class="stat-change">Awaiting approval</div>
            </div>

            <div class="stat-card approved">
                <div class="stat-header">
                    <div class="stat-title">Approved</div>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-value" id="approvedCount">0</div>
                <div class="stat-change">Ready to drive</div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-header">
                    <div class="stat-title">Rejected</div>
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                </div>
                <div class="stat-value" id="rejectedCount">0</div>
                <div class="stat-change">Declined applications</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Online Now</div>
                    <div class="stat-icon"><i class="fas fa-circle"></i></div>
                </div>
                <div class="stat-value" id="onlineCount">0</div>
                <div class="stat-change">Active drivers</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="card-header">
                    <h2 class="card-title">📊 Monthly Driver Registrations</h2>
                    <a href="UserManagement.php" class="view-all">View all drivers</a>
                </div>
                <div class="chart-container">
                    <canvas id="driverChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="card-header">
                    <h2 class="card-title">📈 Complaints Trend </h2>
                    <a href="Report.php" class="view-all">View all complaints</a>
                </div>
                <div class="chart-container">
                    <canvas id="complaintChart"></canvas>
                </div>
            </div>
        </div>

        <div class="complaints-grid">
            <div class="complaint-card" onclick="window.location.href='Reports.php?filter=pending'">
                <div class="complaint-header">
                    <div class="complaint-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="complaint-info">
                        <h3>Pending Complaints</h3>
                        <p>Awaiting resolution</p>
                    </div>
                </div>
                <div class="complaint-value" id="pendingComplaints">0</div>
                <div class="complaint-percentage">From Complaints</div>
            </div>

            <div class="complaint-card resolved" onclick="window.location.href='Reports.php?filter=resolved'">
                <div class="complaint-header">
                    <div class="complaint-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="complaint-info">
                        <h3>Resolved Complaints</h3>
                        <p>Successfully resolved</p>
                    </div>
                </div>
                <div class="complaint-value" id="resolvedComplaints">0</div>
                <div class="complaint-percentage">From Complaints</div>
            </div>
        </div>

        <div class="footer">
            <p>ServiceCo Dashboard Management &copy; <?php echo date('Y'); ?>. All rights reserved.</p>
        </div>
    </div>

    <script>
        const database = firebase.database();
        let driverChart, complaintChart;
        
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            setupSidebarListeners();
            setTimeout(() => adjustDashboardPosition(), 100);
        });

        function loadDashboardData() {
            loadDriversData();
            loadComplaintsData();
        }

        function loadDriversData() {
            database.ref('Drivers').once('value', (snapshot) => {
                const drivers = snapshot.val();
                let total = 0, pending = 0, approved = 0, rejected = 0, online = 0;
                const monthlyData = {};

                if (drivers) {
                    Object.values(drivers).forEach(driver => {
                        if (!driver.FirstName && !driver.LastName) return;
                        
                        total++;
                        
                        const docStatus = driver.DocumentStatus || 'Pending';
                        if (docStatus === 'Approved') approved++;
                        else if (docStatus === 'Rejected') rejected++;
                        else pending++;
                        
                        if (driver.Status === 'Online') online++;
                        
                        if (driver.CreatedAt) {
                            try {
                                const month = new Date(driver.CreatedAt).toLocaleString('default', { month: 'short' });
                                monthlyData[month] = (monthlyData[month] || 0) + 1;
                            } catch (e) {}
                        }
                    });
                }

                document.getElementById('totalApplicants').textContent = total;
                document.getElementById('pendingCount').textContent = pending;
                document.getElementById('approvedCount').textContent = approved;
                document.getElementById('rejectedCount').textContent = rejected;
                document.getElementById('onlineCount').textContent = online;

                createDriverChart(monthlyData);
            });
        }

        function loadComplaintsData() {
            database.ref('Complaints').once('value', (snapshot) => {
                const complaints = snapshot.val();
                let pending = 0, resolved = 0;
                const monthlyData = {};

                if (complaints) {
                    Object.values(complaints).forEach(complaint => {
                        const status = complaint.Status || 'Pending';
                        if (status === 'Pending') pending++;
                        else if (status === 'Resolved') resolved++;
                        
                        if (complaint.DateSubmitted) {
                            try {
                                const month = new Date(complaint.DateSubmitted).toLocaleString('default', { month: 'short' });
                                monthlyData[month] = (monthlyData[month] || 0) + 1;
                            } catch (e) {}
                        }
                    });
                }

                document.getElementById('pendingComplaints').textContent = pending;
                document.getElementById('resolvedComplaints').textContent = resolved;

                createComplaintChart(monthlyData);
            });
        }

        function createDriverChart(monthlyData) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const data = months.map(m => monthlyData[m] || 0);

            if (driverChart) driverChart.destroy();

            const ctx = document.getElementById('driverChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

            driverChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Driver Registrations',
                        data: data,
                        borderColor: '#347433',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#347433',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, callback: v => Number.isInteger(v) ? v : '' }
                        }
                    }
                }
            });
        }

        function createComplaintChart(monthlyData) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const data = months.map(m => monthlyData[m] || 0);

            if (complaintChart) complaintChart.destroy();

            const ctx = document.getElementById('complaintChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
            gradient.addColorStop(1, 'rgba(239, 68, 68, 0.05)');

            complaintChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Complaints',
                        data: data,
                        borderColor: '#ef4444',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, callback: v => Number.isInteger(v) ? v : '' }
                        }
                    }
                }
            });
        }

        function setupSidebarListeners() {
            document.addEventListener('sidebarModeChanged', () => setTimeout(adjustDashboardPosition, 10));
            document.addEventListener('sidebarAutoHide', () => setTimeout(adjustDashboardPosition, 10));
            document.addEventListener('sidebarToggled', () => setTimeout(adjustDashboardPosition, 10));
        }

        function adjustDashboardPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.dashboard-content');
            if (!sidebar || !content) return;
            
            const isAutoHide = sidebar.classList.contains('auto-hide');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const isHovered = sidebar.matches(':hover') && isAutoHide;
            
            let w = 240;
            if (isAutoHide && !isHovered) w = 70;
            else if (isCollapsed) w = 70;
            
            content.style.marginLeft = w + 'px';
            content.style.width = `calc(100% - ${w}px)`;
            
            const navbar = document.getElementById('navbar');
            if (navbar) navbar.style.left = w + 'px';
        }
        
        window.adjustDashboardPosition = adjustDashboardPosition;
    </script>
</body>
</html>