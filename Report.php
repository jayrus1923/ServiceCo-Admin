<?php
// Reports.php - WITH SIDEBAR SYSTEM
session_start();

// Get current sidebar mode from session
$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceCo - Reports & Complaints</title>
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
            background-color: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        /* Reports Content */
        .reports-content {
            margin-top: 70px;
            padding: 25px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        .sidebar.collapsed ~ .reports-content,
        .sidebar.auto-hide ~ .reports-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        /* IMPORTANT: FIXED - Direct sibling selector for auto-hide hover */
        .sidebar.auto-hide:hover ~ .reports-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        /* Welcome Section */
        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #347433;
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

        /* Main Layout */
        .main-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: white;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
        }

        /* Search and Filter Section */
        .filter-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-title {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 12px;
        }

        .filter-group {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            background-color: white;
            color: #333;
            min-width: 150px;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        /* Table Container */
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .table-header {
            padding: 18px 20px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .search-box {
            position: relative;
            width: 200px;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 35px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
        }

        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }

        /* Table Design */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            white-space: nowrap;
        }

        .data-table td {
            padding: 14px 16px;
            color: #4b5563;
            font-size: 13px;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table tbody tr:hover {
            background-color: #f9fafb;
            cursor: pointer;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.pending {
            background-color: #fef3c7;
            color: #f59e0b;
        }

        .status-badge.resolved {
            background-color: #d1fae5;
            color: #347433;
        }

        /* Action buttons in table */
        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background-color: #3b82f6;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: #2563eb;
        }

        /* Report Generation Section */
        .report-generation {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .report-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .report-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-option input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .report-option label {
            font-size: 13px;
            color: #333;
            cursor: pointer;
        }

        .date-range {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }

        .date-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            width: 100%;
        }

        .generate-btn {
            width: 100%;
            padding: 10px;
            background-color: #347433;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            font-size: 14px;
        }

        .generate-btn:hover {
            background-color: #347433;
        }

        /* Chart Section */
        .chart-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        /* Category Breakdown */
        .category-breakdown {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .category-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }

        .category-item.overcharging {
            border-left-color: #ef4444;
        }

        .category-item.rudeness {
            border-left-color: #f59e0b;
        }

        .category-item.reckless {
            border-left-color: #347433;
        }

        .category-name {
            font-size: 13px;
            font-weight: 500;
            color: #333;
        }

        .category-count {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            background-color: white;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
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
            z-index: 1100;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 700px;
            border-radius: 8px;
            overflow: hidden;
            max-height: 90vh;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 18px 20px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .modal-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 20px;
            cursor: pointer;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            padding: 0;
            line-height: 1;
        }

        .modal-close:hover {
            color: #374151;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            max-height: calc(90vh - 130px);
        }

        .modal-footer {
            padding: 15px 20px;
            background-color: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e5e7eb;
        }

        /* Complaint Details */
        .complaint-details {
            display: grid;
            gap: 20px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .detail-row {
                grid-template-columns: 1fr;
            }
        }

        .detail-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            color: #333;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .description-box {
            grid-column: 1 / -1;
        }

        .description-box .detail-value {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        /* Attachment */
        .attachment-box {
            grid-column: 1 / -1;
        }

        .attachment-preview {
            margin-top: 10px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px dashed #e5e7eb;
            text-align: center;
        }

        .attachment-icon {
            font-size: 36px;
            color: #9ca3af;
            margin-bottom: 10px;
        }

        /* Buttons */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 13px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background-color: #347433;
            color: white;
        }

        .btn-primary:hover {
            background-color: #347433;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Success/Error Messages */
        .success-message {
            display: none;
            position: fixed;
            top: 90px;
            right: 25px;
            background-color: #347433;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            z-index: 1000;
            animation: slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
            font-size: 13px;
        }

        .error-message {
            display: none;
            position: fixed;
            top: 90px;
            right: 25px;
            background-color: #ef4444;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            z-index: 1000;
            animation: slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
            font-size: 13px;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
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
            .reports-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 15px;
            }
            
            .sidebar.collapsed ~ .reports-content,
            .sidebar.auto-hide ~ .reports-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .sidebar.auto-hide:hover ~ .reports-content {
                margin-left: 240px !important;
                width: calc(100% - 240px) !important;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-select {
                width: 100%;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .search-box {
                width: 100%;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
            
            .modal-content {
                width: 95%;
                margin: 10px;
            }
        }

        @media (max-width: 576px) {
            .reports-content {
                padding: 12px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .date-range {
                grid-template-columns: 1fr;
            }
        }

        /* Smooth sidebar transition */
        .reports-content, .sidebar, .navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <!-- Reports Content -->
    <div class="reports-content">
        <?php
        // Simulated data for complaints
        $complaints = [
            [
                'id' => 1,
                'complainant' => 'Severino Manalo',
                'date' => 'Today, 9:45 AM',
                'category' => 'Overcharging',
                'status' => 'Pending',
                'status_class' => 'pending',
                'driver' => 'Juan Dela Cruz',
                'driver_id' => 'DRV-082026-001',
                'description' => 'Driver charged PHP 150 for a trip that usually costs PHP 80. Refused to use meter.',
                'attachment' => 'receipt.jpg'
            ],
            [
                'id' => 2,
                'complainant' => 'Anastacio Rivera',
                'date' => 'Today, 8:20 AM',
                'category' => 'Rudeness',
                'status' => 'Resolved',
                'status_class' => 'resolved',
                'driver' => 'Maria Santos',
                'driver_id' => 'DRV-082026-002',
                'description' => 'Driver was rude and used offensive language during the trip.',
                'attachment' => null
            ],
            [
                'id' => 3,
                'complainant' => 'Florentino Ramos',
                'date' => 'Sep 13, 4:10 PM',
                'category' => 'Reckless Driving',
                'status' => 'Resolved',
                'status_class' => 'resolved',
                'driver' => 'Pedro Gonzales',
                'driver_id' => 'DRV-082026-003',
                'description' => 'Driver was speeding and swerving between lanes without signaling.',
                'attachment' => 'video.mp4'
            ],
            [
                'id' => 4,
                'complainant' => 'Isidro Bautista',
                'date' => 'Sep 12, 11:30 AM',
                'category' => 'Overcharging',
                'status' => 'Resolved',
                'status_class' => 'resolved',
                'driver' => 'Ana Reyes',
                'driver_id' => 'DRV-082026-004',
                'description' => 'Overcharged by PHP 50 for a short distance trip.',
                'attachment' => 'receipt.jpg'
            ],
            [
                'id' => 5,
                'complainant' => 'Rogelio Cruz',
                'date' => 'Sep 11, 6:00 PM',
                'category' => 'Rudeness',
                'status' => 'Pending',
                'status_class' => 'pending',
                'driver' => 'Luis Torres',
                'driver_id' => 'DRV-082026-005',
                'description' => 'Driver refused to help with luggage and was impolite.',
                'attachment' => null
            ],
            [
                'id' => 6,
                'complainant' => 'Eduardo Santos',
                'date' => 'Sep 10, 2:15 PM',
                'category' => 'Rudeness',
                'status' => 'Pending',
                'status_class' => 'pending',
                'driver' => 'Sofia Lim',
                'driver_id' => 'DRV-082026-006',
                'description' => 'Driver was dismissive and unhelpful when asked for directions.',
                'attachment' => null
            ],
            [
                'id' => 7,
                'complainant' => 'Maria Santos',
                'date' => 'Sep 10, 4:19 PM',
                'category' => 'Reckless Driving',
                'status' => 'Pending',
                'status_class' => 'pending',
                'driver' => 'Juan Dela Cruz',
                'driver_id' => 'DRV-082026-001',
                'description' => 'Driver ran through red light and almost caused an accident.',
                'attachment' => 'photo.jpg'
            ],
            [
                'id' => 8,
                'complainant' => 'Angela Ramirez',
                'date' => 'Sep 9, 10:28 AM',
                'category' => 'Reckless Driving',
                'status' => 'Resolved',
                'status_class' => 'resolved',
                'driver' => 'Maria Santos',
                'driver_id' => 'DRV-082026-002',
                'description' => 'Driver was texting while driving and not paying attention to the road.',
                'attachment' => null
            ],
            [
                'id' => 9,
                'complainant' => 'Kevin Dela Cruz',
                'date' => 'Sep 8, 9:05 AM',
                'category' => 'Overcharging',
                'status' => 'Resolved',
                'status_class' => 'resolved',
                'driver' => 'Pedro Gonzales',
                'driver_id' => 'DRV-082026-003',
                'description' => 'Charged extra PHP 30 without explanation.',
                'attachment' => 'receipt.jpg'
            ]
        ];

        // Graph data - Complaints per month
        $monthlyData = [
            'labels' => ['Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'data' => [12, 9, 7, 5, 3]
        ];

        // Statistics
        $totalComplaints = count($complaints);
        $pendingComplaints = count(array_filter($complaints, function($c) { return $c['status'] === 'Pending'; }));
        $resolvedComplaints = count(array_filter($complaints, function($c) { return $c['status'] === 'Resolved'; }));

        // Category breakdown
        $categoryCount = [
            'Overcharging' => count(array_filter($complaints, function($c) { return $c['category'] === 'Overcharging'; })),
            'Rudeness' => count(array_filter($complaints, function($c) { return $c['category'] === 'Rudeness'; })),
            'Reckless Driving' => count(array_filter($complaints, function($c) { return $c['category'] === 'Reckless Driving'; }))
        ];
        ?>
        
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="welcome-title">Reports</h1>
            <p class="welcome-subtitle">View and manage all complaints efficiently.</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalComplaints; ?></div>
                <div class="stat-label">Total Complaints</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $pendingComplaints; ?></div>
                <div class="stat-label">Pending Complaints</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $resolvedComplaints; ?></div>
                <div class="stat-label">Resolved Complaints</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo round(($resolvedComplaints / $totalComplaints) * 100, 1); ?>%</div>
                <div class="stat-label">Resolution Rate</div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Left Column: Complaints Table -->
            <div>
                <!-- Search and Filter -->
                <div class="filter-section">
                    <div class="filter-title">Search Filters</div>
                    <div class="filter-group">
                        <select class="filter-select" id="categoryFilter">
                            <option value="">Category: All</option>
                            <option value="Overcharging">Overcharging</option>
                            <option value="Rudeness">Rudeness</option>
                            <option value="Reckless Driving">Reckless Driving</option>
                        </select>
                        
                        <select class="filter-select" id="statusFilter">
                            <option value="">Status: All</option>
                            <option value="Pending">Pending</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                        
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Complaints Table -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">Complaints List</div>
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="searchInput" placeholder="Search complaints...">
                        </div>
                    </div>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Complainant</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="complaintsTable">
                            <?php foreach ($complaints as $complaint): ?>
                            <tr data-id="<?php echo $complaint['id']; ?>">
                                <td><?php echo $complaint['complainant']; ?></td>
                                <td><?php echo $complaint['date']; ?></td>
                                <td><?php echo $complaint['category']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $complaint['status_class']; ?>">
                                        <?php echo $complaint['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" onclick="viewComplaint(<?php echo $complaint['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Reports and Charts -->
            <div>
                <!-- Report Generation -->
                <div class="report-generation">
                    <div class="report-title">Report Generation</div>
                    <div class="report-options">
                        <div class="report-option">
                            <input type="checkbox" id="reportAll" checked>
                            <label for="reportAll">All Complaints</label>
                        </div>
                        <div class="report-option">
                            <input type="checkbox" id="reportPending">
                            <label for="reportPending">Pending Only</label>
                        </div>
                        <div class="report-option">
                            <input type="checkbox" id="reportResolved">
                            <label for="reportResolved">Resolved Only</label>
                        </div>
                        <div class="report-option">
                            <input type="checkbox" id="reportByCategory">
                            <label for="reportByCategory">By Category</label>
                        </div>
                        
                        <div class="date-range">
                            <input type="date" class="date-input" id="reportDateFrom" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                            <input type="date" class="date-input" id="reportDateTo" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <button class="generate-btn" onclick="generateReport()">
                            <i class="fas fa-file-export"></i> Generate Report
                        </button>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="chart-section">
                    <div class="chart-title">Complaints per Month</div>
                    <div class="chart-container">
                        <canvas id="complaintsChart"></canvas>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="category-breakdown">
                    <div class="chart-title">Complaints by Category</div>
                    <div class="category-list">
                        <?php foreach ($categoryCount as $category => $count): ?>
                        <div class="category-item <?php echo strtolower(str_replace(' ', '', $category)); ?>">
                            <span class="category-name"><?php echo $category; ?></span>
                            <span class="category-count"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>ServiceCo Complaint Management &copy; <?php echo date('Y'); ?>. All rights reserved.</p>
        </div>
    </div>

    <!-- Complaint Details Modal -->
    <div class="modal" id="complaintModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Complaint Details</h3>
                <button class="modal-close" onclick="closeModal('complaintModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="complaint-details" id="complaintDetailsContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('complaintModal')">Close</button>
                <button class="btn btn-primary" id="resolveBtn" onclick="resolveComplaint()">Mark as Resolved</button>
                <button class="btn btn-danger" id="deleteBtn" onclick="deleteComplaint()">Delete Complaint</button>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div class="success-message" id="successMessage"></div>
    <div class="error-message" id="errorMessage"></div>

    <script>
        // Store current sidebar mode
        let currentSidebarMode = '<?php echo $sidebarMode; ?>';
        
        // Current complaint for modal actions
        let currentComplaintId = null;
        let currentComplaintStatus = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Listen for sidebar events from Sidebar.php
            document.addEventListener('sidebarModeChanged', function(e) {
                console.log('Reports: Sidebar mode changed to:', e.detail.mode);
                currentSidebarMode = e.detail.mode;
                adjustReportsPosition();
            });
            
            // Listen for sidebar auto-hide hover events
            document.addEventListener('sidebarAutoHide', function(e) {
                console.log('Reports: Sidebar auto-hide hover:', e.detail.expanded);
                
                // Force immediate position adjustment on hover
                setTimeout(() => {
                    adjustReportsPosition();
                }, 10);
            });
            
            // Listen for sidebar toggle events
            document.addEventListener('sidebarToggled', function(e) {
                console.log('Reports: Sidebar manually toggled:', e.detail.collapsed);
                
                // Force immediate position adjustment
                setTimeout(() => {
                    adjustReportsPosition();
                }, 10);
            });
            
            // Listen for global sidebar position updates
            if (typeof window.updateAllPositions === 'function') {
                // Hook into the global update function
                const originalUpdateAllPositions = window.updateAllPositions;
                window.updateAllPositions = function() {
                    originalUpdateAllPositions();
                    adjustReportsPosition();
                };
            }
            
            // Initial position adjustment
            setTimeout(() => {
                adjustReportsPosition();
            }, 100);
            
            // Set up periodic check for sidebar changes
            setInterval(() => {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    const currentClasses = sidebar.className;
                    
                    // Check if classes have changed
                    if (window.lastSidebarClasses !== currentClasses) {
                        window.lastSidebarClasses = currentClasses;
                        adjustReportsPosition();
                    }
                }
            }, 100);
            
            // Initialize Chart
            const ctx = document.getElementById('complaintsChart').getContext('2d');
            
            const complaintsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($monthlyData['labels']); ?>,
                    datasets: [{
                        label: 'Complaints',
                        data: <?php echo json_encode($monthlyData['data']); ?>,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(139, 92, 246)'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
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
                                size: 12
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 10,
                            cornerRadius: 4
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
                                    size: 11
                                },
                                padding: 8,
                                stepSize: 5
                            },
                            title: {
                                display: true,
                                text: 'Number of Complaints',
                                font: {
                                    size: 12,
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
                                    size: 11
                                },
                                padding: 8
                            },
                            title: {
                                display: true,
                                text: 'Months',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                color: '#666'
                            }
                        }
                    }
                }
            });
            
            // Filter functionality
            setupFilters();
        });

        // FUNCTION: Adjust reports position based on sidebar state
        function adjustReportsPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.reports-content');
            
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
            
            console.log('Reports adjusting position:', {
                isAutoHide,
                isCollapsed,
                isHovered,
                sidebarWidth
            });
            
            // Update reports content position immediately
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
        window.adjustReportsPosition = adjustReportsPosition;

        // Setup filter functionality
        function setupFilters() {
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#complaintsTable tr');
            
            function applyFilters() {
                const category = categoryFilter.value.toLowerCase();
                const status = statusFilter.value.toLowerCase();
                const search = searchInput.value.toLowerCase();
                
                tableRows.forEach(row => {
                    const categoryCell = row.cells[2].textContent.toLowerCase();
                    const statusCell = row.cells[3].textContent.toLowerCase();
                    const complainantCell = row.cells[0].textContent.toLowerCase();
                    const dateCell = row.cells[1].textContent.toLowerCase();
                    
                    const matchesCategory = !category || categoryCell.includes(category);
                    const matchesStatus = !status || statusCell.includes(status);
                    const matchesSearch = !search || 
                        complainantCell.includes(search) || 
                        dateCell.includes(search) ||
                        categoryCell.includes(search);
                    
                    if (matchesCategory && matchesStatus && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            categoryFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            searchInput.addEventListener('input', applyFilters);
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('searchInput').value = '';
            
            const tableRows = document.querySelectorAll('#complaintsTable tr');
            tableRows.forEach(row => {
                row.style.display = '';
            });
            
            showMessage('Filters cleared', 'success');
        }

        // View complaint details
        function viewComplaint(complaintId) {
            const complaints = <?php echo json_encode($complaints); ?>;
            const complaint = complaints.find(c => c.id == complaintId);
            
            if (complaint) {
                currentComplaintId = complaintId;
                currentComplaintStatus = complaint.status;
                
                const content = `
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Complainant</div>
                            <div class="detail-value">${complaint.complainant}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Date Reported</div>
                            <div class="detail-value">${complaint.date}</div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Category</div>
                            <div class="detail-value">${complaint.category}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge ${complaint.status_class}">${complaint.status}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Driver</div>
                            <div class="detail-value">${complaint.driver}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Driver ID</div>
                            <div class="detail-value">${complaint.driver_id}</div>
                        </div>
                    </div>
                    
                    <div class="description-box">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">${complaint.description}</div>
                    </div>
                    
                    <div class="attachment-box">
                        <div class="detail-label">Attachment</div>
                        ${complaint.attachment ? `
                            <div class="attachment-preview">
                                <div class="attachment-icon">
                                    <i class="fas fa-file-${getFileIcon(complaint.attachment)}"></i>
                                </div>
                                <div style="font-size: 13px; color: #666; margin-bottom: 10px;">${complaint.attachment}</div>
                                <button class="btn btn-secondary" onclick="downloadAttachment('${complaint.attachment}')">
                                    <i class="fas fa-download"></i> Download Attachment
                                </button>
                            </div>
                        ` : `
                            <div class="detail-value" style="text-align: center; color: #9ca3af;">
                                <i class="fas fa-times" style="margin-right: 8px;"></i> No attachment
                            </div>
                        `}
                    </div>
                `;
                
                document.getElementById('complaintDetailsContent').innerHTML = content;
                
                // Update button text based on status
                const resolveBtn = document.getElementById('resolveBtn');
                const deleteBtn = document.getElementById('deleteBtn');
                
                if (complaint.status === 'Resolved') {
                    resolveBtn.innerHTML = '<i class="fas fa-undo"></i> Reopen Complaint';
                    resolveBtn.onclick = function() { reopenComplaint(); };
                } else {
                    resolveBtn.innerHTML = '<i class="fas fa-check"></i> Mark as Resolved';
                    resolveBtn.onclick = function() { resolveComplaint(); };
                }
                
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Complaint';
                deleteBtn.onclick = function() { deleteComplaint(); };
                
                showModal('complaintModal');
            }
        }

        // Get file icon based on extension
        function getFileIcon(filename) {
            const extension = filename.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                return 'image';
            } else if (['mp4', 'avi', 'mov'].includes(extension)) {
                return 'video';
            } else if (['pdf'].includes(extension)) {
                return 'pdf';
            } else {
                return 'file';
            }
        }

        // Download attachment
        function downloadAttachment(filename) {
            showMessage(`Downloading: ${filename}`, 'success');
        }

        // Resolve complaint
        function resolveComplaint() {
            if (!currentComplaintId) return;
            
            showMessage('Complaint marked as resolved!', 'success');
            closeModal('complaintModal');
            
            // Update table row
            updateComplaintStatus(currentComplaintId, 'resolved');
        }

        // Reopen complaint
        function reopenComplaint() {
            if (!currentComplaintId) return;
            
            showMessage('Complaint reopened!', 'success');
            closeModal('complaintModal');
            
            // Update table row
            updateComplaintStatus(currentComplaintId, 'pending');
        }

        // Delete complaint
        function deleteComplaint() {
            if (!currentComplaintId) return;
            
            if (confirm('Are you sure you want to delete this complaint?')) {
                showMessage('Complaint deleted!', 'success');
                closeModal('complaintModal');
                
                // Remove from table
                const row = document.querySelector(`tr[data-id="${currentComplaintId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Update statistics
                updateStatistics();
            }
        }

        // Update complaint status in table
        function updateComplaintStatus(complaintId, status) {
            const row = document.querySelector(`tr[data-id="${complaintId}"]`);
            if (row) {
                const statusCell = row.cells[3];
                if (status === 'resolved') {
                    statusCell.innerHTML = '<span class="status-badge resolved">Resolved</span>';
                } else {
                    statusCell.innerHTML = '<span class="status-badge pending">Pending</span>';
                }
            }
            
            // Update statistics
            updateStatistics();
        }

        // Update statistics display
        function updateStatistics() {
            // In real app, you would recalculate statistics here
            // For demo, we'll just show a message
            showMessage('Statistics updated', 'success');
        }

        // Generate report
        function generateReport() {
            const includeAll = document.getElementById('reportAll').checked;
            const includePending = document.getElementById('reportPending').checked;
            const includeResolved = document.getElementById('reportResolved').checked;
            const includeByCategory = document.getElementById('reportByCategory').checked;
            const dateFrom = document.getElementById('reportDateFrom').value;
            const dateTo = document.getElementById('reportDateTo').value;
            
            // Validate date range
            if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
                showMessage('End date must be after start date', 'error');
                return;
            }
            
            // Simulate report generation
            showMessage('Report generated successfully! Download will start shortly...', 'success');
            
            // In real app, you would make an AJAX call here
            // and generate/download the report
        }

        // Modal functions
        function showModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        });

        // Show success/error message
        function showMessage(message, type) {
            const messageDiv = type === 'success' ? document.getElementById('successMessage') : document.getElementById('errorMessage');
            const otherDiv = type === 'success' ? document.getElementById('errorMessage') : document.getElementById('successMessage');
            
            otherDiv.style.display = 'none';
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>