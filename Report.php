<?php
session_start();

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

        .sidebar.auto-hide:hover ~ .reports-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        .loading-overlay.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #347433;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

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
            transition: transform 0.2s;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: #347433;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
        }

        .main-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .main-layout { grid-template-columns: 1fr; }
        }

        .filter-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
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
            border-color: #347433;
        }

        .filter-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            flex: 1;
            min-width: 250px;
        }

        .filter-input:focus {
            outline: none;
            border-color: #347433;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: #347433;
            text-decoration: none;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2d6a2d;
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

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-success {
            background-color: #347433;
            color: white;
        }

        .btn-success:hover {
            background-color: #2d6a2d;
        }

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

        .type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .type-badge.commuter {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .type-badge.driver {
            background-color: #ffe4e6;
            color: #e11d48;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background-color: #347433;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: #2d6a2d;
        }

        .action-btn.small {
            padding: 4px 8px;
            font-size: 11px;
        }

        .action-btn.danger {
            background-color: #ef4444;
        }

        .action-btn.danger:hover {
            background-color: #dc2626;
        }

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

        .report-option-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .report-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-option input[type="radio"] {
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
            background-color: #2d6a2d;
        }

        .chart-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
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

        .category-breakdown {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
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
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #347433;
            transition: all 0.2s;
            cursor: pointer;
        }

        .category-item:hover {
            transform: translateX(2px);
            background-color: #f0fdf4;
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
            border-radius: 12px;
            overflow: hidden;
            max-height: 100vh;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px 25px;
            background-color: #347433;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
        }

        .modal-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.3);
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
            max-height: calc(90vh - 130px);
        }

        .modal-footer {
            padding: 20px 25px;
            background-color: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            border-top: 1px solid #e5e7eb;
        }

        .complaint-details {
            display: grid;
            gap: 25px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        @media (max-width: 768px) {
            .detail-row { grid-template-columns: 1fr; }
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
            padding: 12px 15px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            word-break: break-word;
            line-height: 1.5;
        }

        .description-box {
            grid-column: 1 / -1;
        }

        .attachment-box {
            grid-column: 1 / -1;
        }

        .attachment-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .attachment-item:hover {
            background-color: #f0fdf4;
            border-color: #347433;
        }

        .attachment-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            background-color: #f0fdf4;
            color: #347433;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .attachment-info {
            flex: 1;
        }

        .attachment-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
            word-break: break-all;
        }

        .attachment-meta {
            font-size: 11px;
            color: #999;
        }

        .preview-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            background-color: #347433;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .preview-btn:hover {
            background-color: #2d6a2d;
            transform: translateY(-1px);
        }

        .preview-image {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .pagination {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            color: #666;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            min-width: 36px;
            text-align: center;
        }

        .pagination-btn:hover {
            border-color: #347433;
            color: #347433;
            background-color: #f0fdf4;
        }

        .pagination-btn.active {
            background-color: #347433;
            color: white;
            border-color: #347433;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .confirmation-modal {
            max-width: 400px;
        }
        
        .confirmation-icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .confirmation-icon .fa-check-circle {
            color: #347433;
        }
        
        .confirmation-icon .fa-undo {
            color: #f59e0b;
        }
        
        .confirmation-icon .fa-exclamation-triangle {
            color: #ef4444;
        }
        
        .confirmation-title {
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        
        .confirmation-message {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            padding: 15px;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .reports-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 15px;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-select,
            .filter-input {
                width: 100%;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
            
            .date-range {
                grid-template-columns: 1fr;
            }
            
            .report-option-group {
                flex-direction: column;
                gap: 8px;
            }

            .preview-image {
                max-height: 300px;
            }
        }

        .reports-content, .sidebar, .navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="reports-content">
        <div class="welcome-section">
            <h1 class="welcome-title">Reports & Complaints Management</h1>
            <p class="welcome-subtitle">View, filter, and manage all complaints from commuters and drivers.</p>
        </div>

        <div class="stats-grid" id="statsGrid">
            <div class="stat-card" onclick="filterByStatus('all')">
                <div class="stat-value" id="totalComplaints">0</div>
                <div class="stat-label">Total Complaints</div>
            </div>
            <div class="stat-card" onclick="filterByStatus('pending')">
                <div class="stat-value" id="pendingComplaints">0</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card" onclick="filterByStatus('resolved')">
                <div class="stat-value" id="resolvedComplaints">0</div>
                <div class="stat-label">Resolved</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="resolutionRate">0%</div>
                <div class="stat-label">Resolution Rate</div>
            </div>
        </div>

        <div class="main-layout">
            <div>
                <div class="filter-section">
                    <div class="filter-group">
                        <select class="filter-select" id="categoryFilter">
                            <option value="">All Categories</option>
                        </select>
                        
                        <select class="filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                        </select>
                        
                        <input type="text" class="filter-input" id="searchFilter" placeholder="Search by name, complainant, accused, category...">
                        
                        <div class="filter-actions">
                            <button class="btn btn-secondary" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">📋 Complaints List</div>
                    </div>
                    
                    <table class="data-table" id="complaintsTable">
                        <thead>
                            <tr>
                                <th>Complainant</th>
                                <th>Against</th>
                                <th>Time</th>
                                <th>Category</th>
                                <th>Ride ID</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="complaintsTableBody"></tbody>
                    </table>
                    
                    <div id="pagination" class="pagination"></div>
                    
                    <div id="emptyState" class="empty-state" style="display: none;">
                        <i class="fas fa-search"></i>
                        <p>No complaints found matching your filters</p>
                    </div>
                </div>
            </div>

            <div>
                <div class="report-generation">
                    <div class="report-title">📊 Generate Report</div>
                    <div class="report-options">
                        <div class="report-option-group">
                            <div class="report-option">
                                <input type="radio" name="report_status" id="reportAll" value="" checked>
                                <label for="reportAll">All Complaints</label>
                            </div>
                            <div class="report-option">
                                <input type="radio" name="report_status" id="reportPending" value="pending">
                                <label for="reportPending">Pending Only</label>
                            </div>
                            <div class="report-option">
                                <input type="radio" name="report_status" id="reportResolved" value="resolved">
                                <label for="reportResolved">Resolved Only</label>
                            </div>
                        </div>
                        
                        <div class="report-option-group">
                            <div class="report-option">
                                <input type="radio" name="group_by" id="groupNone" value="none" checked>
                                <label for="groupNone">List View</label>
                            </div>
                            <div class="report-option">
                                <input type="radio" name="group_by" id="groupCategory" value="category">
                                <label for="groupCategory">Group by Category</label>
                            </div>
                        </div>
                        
                        <div class="date-range">
                            <input type="date" class="date-input" id="reportDateFrom">
                            <input type="date" class="date-input" id="reportDateTo">
                        </div>
                        
                        <button class="generate-btn" onclick="generateReport()">
                            <i class="fas fa-file-excel"></i> Generate & Download Report
                        </button>
                    </div>
                </div>

                <div class="chart-section">
                    <div class="chart-title">📈 Complaints Trend</div>
                    <div class="chart-container">
                        <canvas id="complaintsChart"></canvas>
                    </div>
                </div>

                <div class="category-breakdown">
                    <div class="chart-title">📊 Complaints by Category</div>
                    <div class="category-list" id="categoryBreakdown">
                        <div style="text-align: center; padding: 20px; color: #999;">No data available</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>ServiceCo Complaint Management &copy; <?php echo date('Y'); ?>. All rights reserved.</p>
        </div>
    </div>

    <div class="modal" id="complaintModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📄 Complaint Details</h3>
                <button class="modal-close" onclick="closeModal('complaintModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="complaint-details" id="complaintDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('complaintModal')">Close</button>
                <button class="btn btn-success" id="resolveBtn" onclick="showStatusConfirm()">Mark as Resolved</button>
                <button class="btn btn-danger" onclick="showDeleteConfirm()">Delete</button>
            </div>
        </div>
    </div>

    <div class="modal" id="statusConfirmModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header" id="confirmModalHeader" style="background-color: #f59e0b;">
                <h3 id="confirmModalTitle">Confirm Status Change</h3>
                <button class="modal-close" onclick="closeModal('statusConfirmModal')">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <div class="confirmation-icon" id="confirmModalIcon">
                    <i class="fas fa-undo" style="color: #f59e0b;"></i>
                </div>
                <p id="statusConfirmMessage" style="margin-bottom: 20px;">Are you sure you want to change the status?</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button class="btn btn-secondary" onclick="closeModal('statusConfirmModal')">Cancel</button>
                    <button class="btn" id="confirmActionBtn" onclick="confirmStatusChange()" style="background-color: #f59e0b; color: white;">Yes, Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="previewModal">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3 id="previewTitle">Document Preview</h3>
                <button class="modal-close" onclick="closeModal('previewModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('previewModal')">Close</button>
                <a href="#" id="downloadLink" target="_blank" class="btn btn-primary" style="decoration">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>

    <div class="modal" id="deleteConfirmModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header" style="background-color: #ef4444;">
                <h3>Confirm Delete</h3>
                <button class="modal-close" onclick="closeModal('deleteConfirmModal')">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f59e0b; margin-bottom: 15px;"></i>
                <p style="margin-bottom: 20px;">Are you sure you want to delete this complaint? This action cannot be undone.</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button class="btn btn-secondary" onclick="closeModal('deleteConfirmModal')">Cancel</button>
                    <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.isDataLoaded = false;
        let currentPage = 1;
        let itemsPerPage = 10;
        let filteredComplaints = [];

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (!window.isDataLoaded) {
                    document.getElementById('loadingOverlay').classList.add('fade-out');
                    setTimeout(() => window.updateAllPositions(), 100);
                }
            }, 5000);
            
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    closeModal(e.target.id);
                }
            });
            
            const today = new Date();
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(today.getDate() - 7);
            
            document.getElementById('reportDateFrom').value = sevenDaysAgo.toISOString().split('T')[0];
            document.getElementById('reportDateTo').value = today.toISOString().split('T')[0];
        });

        const database = firebase.database();
        let allComplaints = [];
        let currentComplaint = null;
        let complaintsChart = null;
        let pendingAction = null;

        function formatFileName(url) {
            const filename = url.split('/').pop().split('?')[0];
            const match = filename.match(/(COM-\d{8}-\d{3}|DRV-\d{8}-\d{3})/);
            if (match) {
                const id = match[1];
                const ext = filename.split('.').pop();
                return `${id}_Complaint_Evidence.${ext}`;
            }
            return filename;
        }

        function loadComplaints() {
            database.ref('Complaints').on('value', (snapshot) => {
                const data = snapshot.val();
                allComplaints = [];
                
                if (data) {
                    Object.entries(data).forEach(([key, complaint]) => {
                        if (!complaint) return;
                        
                        const processed = processComplaint(key, complaint);
                        allComplaints.push(processed);
                    });
                    
                    allComplaints.sort((a, b) => new Date(b.date_raw) - new Date(a.date_raw));
                }
                
                updateStats();
                applyFilters();
                updateCategoryBreakdown();
                updateChart();
                updateCategoryFilter();
                
                if (!window.isDataLoaded) {
                    window.isDataLoaded = true;
                    setTimeout(() => {
                        document.getElementById('loadingOverlay').classList.add('fade-out');
                        setTimeout(() => window.updateAllPositions(), 100);
                    }, 500);
                }
            });
        }

        loadComplaints();

        function processComplaint(key, complaint) {
            const dateSubmitted = complaint.DateSubmitted ? new Date(complaint.DateSubmitted) : new Date();
            const now = new Date();
            const diffMs = now - dateSubmitted;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);
            
            let displayDate;
            if (diffDays === 0) {
                if (diffHours === 0) {
                    if (diffMins === 0) displayDate = 'Just now';
                    else displayDate = diffMins + ' minute' + (diffMins > 1 ? 's' : '') + ' ago';
                } else {
                    displayDate = diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago';
                }
            } else if (diffDays === 1) {
                displayDate = 'Yesterday at ' + dateSubmitted.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            } else if (diffDays <= 7) {
                displayDate = diffDays + ' days ago';
            } else {
                displayDate = dateSubmitted.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + 
                             ' at ' + dateSubmitted.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            }
            
            const complainantType = complaint.ComplainantType || 'unknown';
            const accusedType = complaint.AccusedType || 'unknown';
            const status = complaint.Status || 'Pending';
            
            const attachments = [];
            if (complaint.AttachmentUrls && Array.isArray(complaint.AttachmentUrls)) {
                complaint.AttachmentUrls.forEach(url => {
                    attachments.push({
                        url: url,
                        display_name: formatFileName(url)
                    });
                });
            }
            
            return {
                id: key,
                firebase_key: key,
                complainant: complaint.ComplainantName || complaint.ComplainantPhone || 'Unknown',
                complainant_type: complainantType,
                complainant_phone: complaint.ComplainantPhone || '',
                complainant_id: complaint.ComplainantId || '',
                accused: complaint.AccusedName || 'Unknown',
                accused_type: accusedType,
                accused_phone: complaint.AccusedPhone || '',
                accused_id: complaint.AccusedId || '',
                date: displayDate,
                date_raw: dateSubmitted.toISOString(),
                category: complaint.Category || 'Other',
                status: status,
                status_class: status.toLowerCase(),
                ride_id: complaint.RideDisplayId || (complaint.RideId ? 'Ride-' + complaint.RideId.slice(-8) : 'N/A'),
                ride_firebase_key: complaint.RideId || '',
                description: complaint.Description || '',
                plate_number: complaint.VehicleNumber || '',
                attachments: attachments
            };
        }

        function updateStats() {
            const total = allComplaints.length;
            const pending = allComplaints.filter(c => c.status === 'Pending').length;
            const resolved = allComplaints.filter(c => c.status === 'Resolved').length;
            const rate = total > 0 ? Math.round((resolved / total) * 100) : 0;
            
            document.getElementById('totalComplaints').textContent = total;
            document.getElementById('pendingComplaints').textContent = pending;
            document.getElementById('resolvedComplaints').textContent = resolved;
            document.getElementById('resolutionRate').textContent = rate + '%';
        }

        function renderTable() {
            const tbody = document.getElementById('complaintsTableBody');
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageItems = filteredComplaints.slice(start, end);
            
            if (filteredComplaints.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 50px;">No complaints found</td></tr>';
                document.getElementById('pagination').style.display = 'none';
                return;
            }
            
            let html = '';
            pageItems.forEach(c => {
                html += `
                    <tr data-id="${c.id}" 
                        data-category="${c.category.toLowerCase()}"
                        data-status="${c.status.toLowerCase()}"
                        data-search="${c.complainant.toLowerCase()} ${c.accused.toLowerCase()} ${c.category.toLowerCase()} ${c.status.toLowerCase()} ${c.ride_id.toLowerCase()}">
                        <td>
                            ${escapeHtml(c.complainant)}
                            <span class="type-badge ${c.complainant_type}" style="margin-left: 5px; padding: 2px 6px; font-size: 10px;">
                                ${c.complainant_type === 'commuter' ? 'C' : 'D'}
                            </span>
                        </td>
                        <td>
                            ${escapeHtml(c.accused)}
                            <span class="type-badge ${c.accused_type}" style="margin-left: 5px; padding: 2px 6px; font-size: 10px;">
                                ${c.accused_type === 'commuter' ? 'C' : 'D'}
                            </span>
                        </td>
                        <td>${c.date}</td>
                        <td>${escapeHtml(c.category)}</td>
                        <td>${escapeHtml(c.ride_id)}</td>
                        <td>
                            <span class="status-badge ${c.status_class}">
                                ${c.status}
                            </span>
                        </td>
                        <td>
                            <button class="action-btn small" onclick='showComplaintDetails(${JSON.stringify(c).replace(/'/g, "\\'")})'>
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredComplaints.length / itemsPerPage);
            const pagination = document.getElementById('pagination');
            
            if (totalPages <= 1) {
                pagination.style.display = 'none';
                return;
            }
            
            pagination.style.display = 'flex';
            
            let html = '';
            html += `<button class="pagination-btn" onclick="changePage(1)" ${currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="pagination-btn" style="border: none; background: none;">...</span>`;
                }
            }
            
            html += `<button class="pagination-btn" onclick="changePage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>`;
            
            pagination.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            renderTable();
        }

        function updateCategoryBreakdown() {
            const categories = {};
            allComplaints.forEach(c => {
                categories[c.category] = (categories[c.category] || 0) + 1;
            });
            
            const sorted = Object.entries(categories).sort((a, b) => b[1] - a[1]);
            
            const container = document.getElementById('categoryBreakdown');
            if (sorted.length === 0) {
                container.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">No data available</div>';
                return;
            }
            
            let html = '';
            sorted.forEach(([category, count]) => {
                html += `
                    <div class="category-item" onclick="filterByCategory('${category}')">
                        <span class="category-name">${escapeHtml(category)}</span>
                        <span class="category-count">${count}</span>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function updateCategoryFilter() {
            const categories = [...new Set(allComplaints.map(c => c.category))].sort();
            const filter = document.getElementById('categoryFilter');
            let options = '<option value="">All Categories</option>';
            categories.forEach(cat => {
                options += `<option value="${cat.toLowerCase()}">${escapeHtml(cat)}</option>`;
            });
            filter.innerHTML = options;
        }

        function updateChart() {
            const months = [];
            const counts = [];
            
            for (let i = 5; i >= 0; i--) {
                const d = new Date();
                d.setMonth(d.getMonth() - i);
                const monthName = d.toLocaleDateString('en-US', { month: 'short' });
                months.push(monthName);
                
                const count = allComplaints.filter(c => {
                    const complaintMonth = new Date(c.date_raw).toLocaleDateString('en-US', { month: 'short' });
                    return complaintMonth === monthName;
                }).length;
                
                counts.push(count);
            }
            
            const ctx = document.getElementById('complaintsChart').getContext('2d');
            
            if (complaintsChart) complaintsChart.destroy();
            
            complaintsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Complaints',
                        data: counts,
                        backgroundColor: 'rgba(52, 116, 51, 0.1)',
                        borderColor: '#347433',
                        borderWidth: 3,
                        pointBackgroundColor: '#347433',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: true
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

        function applyFilters() {
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
            
            filteredComplaints = allComplaints.filter(c => {
                const matchesCategory = !categoryFilter || c.category.toLowerCase().includes(categoryFilter);
                const matchesStatus = !statusFilter || c.status.toLowerCase() === statusFilter;
                const matchesSearch = !searchFilter || 
                    c.complainant.toLowerCase().includes(searchFilter) ||
                    c.accused.toLowerCase().includes(searchFilter) ||
                    c.category.toLowerCase().includes(searchFilter) ||
                    c.ride_id.toLowerCase().includes(searchFilter);
                
                return matchesCategory && matchesStatus && matchesSearch;
            });
            
            currentPage = 1;
            renderTable();
        }

        function setupFilters() {
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const searchFilter = document.getElementById('searchFilter');
            
            categoryFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            searchFilter.addEventListener('input', applyFilters);
        }

        setupFilters();

        function filterByCategory(category) {
            document.getElementById('categoryFilter').value = category.toLowerCase();
            applyFilters();
        }

        function filterByStatus(status) {
            if (status === 'all') {
                document.getElementById('statusFilter').value = '';
            } else {
                document.getElementById('statusFilter').value = status;
            }
            applyFilters();
        }

        function clearFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('searchFilter').value = '';
            applyFilters();
        }

        function showComplaintDetails(complaint) {
            currentComplaint = complaint;
            
            let attachmentsHtml = '';
            if (complaint.attachments && complaint.attachments.length > 0) {
                attachmentsHtml = '<div class="attachment-list">';
                complaint.attachments.forEach(att => {
                    const isImage = att.url.match(/\.(jpg|jpeg|png|gif|webp)/i);
                    attachmentsHtml += `
                        <div class="attachment-item">
                            <div class="attachment-icon">
                                <i class="fas ${isImage ? 'fa-image' : 'fa-file'}"></i>
                            </div>
                            <div class="attachment-info">
                                <div class="attachment-name">${escapeHtml(att.display_name)}</div>
                                <div class="attachment-meta">${isImage ? 'Image' : 'Document'}</div>
                            </div>
                            <button class="preview-btn" onclick='previewDocument("${escapeHtml(att.display_name)}", "${att.url}")'>
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    `;
                });
                attachmentsHtml += '</div>';
            } else {
                attachmentsHtml = '<div class="detail-value" style="text-align: center; color: #9ca3af;"><i class="fas fa-times" style="margin-right: 8px;"></i> No attachments</div>';
            }
            
            document.getElementById('complaintDetailsContent').innerHTML = `
                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">Complainant</div>
                        <div class="detail-value">
                            ${escapeHtml(complaint.complainant)}
                            <span class="type-badge ${complaint.complainant_type}" style="margin-left: 5px;">
                                ${complaint.complainant_type === 'commuter' ? 'Commuter' : 'Driver'}
                            </span>
                        </div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">Accused</div>
                        <div class="detail-value">
                            ${escapeHtml(complaint.accused)}
                            <span class="type-badge ${complaint.accused_type}" style="margin-left: 5px;">
                                ${complaint.accused_type === 'commuter' ? 'Commuter' : 'Driver'}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">Date & Time</div>
                        <div class="detail-value">${complaint.date}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">Ride ID</div>
                        <div class="detail-value">${escapeHtml(complaint.ride_id)}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">Category</div>
                        <div class="detail-value">${escapeHtml(complaint.category)}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">Plate Number</div>
                        <div class="detail-value">${escapeHtml(complaint.plate_number) || 'N/A'}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge ${complaint.status_class}">${complaint.status}</span>
                        </div>
                    </div>
                </div>
                <div class="description-box">
                    <div class="detail-label">Description</div>
                    <div class="detail-value">${escapeHtml(complaint.description) || 'No description provided'}</div>
                </div>
                <div class="attachment-box">
                    <div class="detail-label">Attachments (${complaint.attachments.length})</div>
                    ${attachmentsHtml}
                </div>
            `;
            
            const resolveBtn = document.getElementById('resolveBtn');
            if (complaint.status === 'Resolved') {
                resolveBtn.innerHTML = '<i class="fas fa-undo"></i> Reopen';
                resolveBtn.className = 'btn btn-warning';
            } else {
                resolveBtn.innerHTML = '<i class="fas fa-check"></i> Mark as Resolved';
                resolveBtn.className = 'btn btn-success';
            }
            
            showModal('complaintModal');
        }

        function previewDocument(docName, docUrl) {
            document.getElementById('previewTitle').textContent = 'Preview: ' + docName;
            document.getElementById('previewContent').innerHTML = 
                '<img src="' + docUrl + '" alt="' + docName + '" class="preview-image" ' +
                'onerror="this.onerror=null; this.src=\'Images/profile_icon.png\';">' +
                '<div style="text-align: center; margin-top: 15px;">' +
                '<p><strong>' + escapeHtml(docName) + '</strong></p>' +
                '<p style="color: #666; font-size: 13px;">Click Download to view full size</p></div>';
            document.getElementById('downloadLink').href = docUrl;
            showModal('previewModal');
        }

        function showStatusConfirm() {
            if (!currentComplaint) return;
            
            const isResolved = currentComplaint.status === 'Resolved';
            const message = isResolved 
                ? 'Are you sure you want to reopen this complaint?' 
                : 'Are you sure you want to mark this complaint as resolved?';
            const title = isResolved ? 'Reopen Complaint' : 'Resolve Complaint';
            
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('statusConfirmMessage').textContent = message;
            
            const modalHeader = document.getElementById('confirmModalHeader');
            const confirmIcon = document.getElementById('confirmModalIcon');
            const confirmBtn = document.getElementById('confirmActionBtn');
            
            if (isResolved) {
                modalHeader.style.backgroundColor = '#f59e0b';
                confirmIcon.innerHTML = '<i class="fas fa-undo" style="color: #f59e0b;"></i>';
                confirmBtn.style.backgroundColor = '#f59e0b';
            } else {
                modalHeader.style.backgroundColor = '#347433';
                confirmIcon.innerHTML = '<i class="fas fa-check-circle" style="color: #347433;"></i>';
                confirmBtn.style.backgroundColor = '#347433';
            }
            
            pendingAction = 'status';
            showModal('statusConfirmModal');
        }

        function confirmStatusChange() {
            if (!currentComplaint || pendingAction !== 'status') return;
            
            const newStatus = currentComplaint.status === 'Resolved' ? 'Pending' : 'Resolved';
            
            database.ref(`Complaints/${currentComplaint.id}`).update({
                Status: newStatus,
                DateReviewed: new Date().toISOString()
            }).then(() => {
                closeModal('statusConfirmModal');
                closeModal('complaintModal');
                pendingAction = null;
            });
        }

        function showDeleteConfirm() {
            closeModal('complaintModal');
            showModal('deleteConfirmModal');
        }

        function confirmDelete() {
            if (!currentComplaint) return;
            
            database.ref(`Complaints/${currentComplaint.id}`).remove().then(() => {
                closeModal('deleteConfirmModal');
            });
        }

        function generateReport() {
            const status = document.querySelector('input[name="report_status"]:checked').value;
            const groupBy = document.querySelector('input[name="group_by"]:checked').value;
            let dateFrom = document.getElementById('reportDateFrom').value;
            let dateTo = document.getElementById('reportDateTo').value;
            
            let filtered = [...allComplaints];
            
            if (status) {
                filtered = filtered.filter(c => c.status.toLowerCase() === status);
            }
            
            if (dateFrom && dateTo) {
                const from = new Date(dateFrom);
                const to = new Date(dateTo);
                to.setHours(23, 59, 59);
                
                filtered = filtered.filter(c => {
                    const d = new Date(c.date_raw);
                    return d >= from && d <= to;
                });
            }
            
            const headers = ['ID', 'Complainant', 'Type', 'Accused', 'Accused Type', 'Category', 'Status', 'Date', 'Ride ID', 'Plate Number', 'Description'];
            const rows = [];
            
            if (groupBy === 'category') {
                const grouped = {};
                filtered.forEach(c => {
                    if (!grouped[c.category]) grouped[c.category] = [];
                    grouped[c.category].push(c);
                });
                
                Object.entries(grouped).forEach(([category, items]) => {
                    rows.push([`=== CATEGORY: ${category} ===`]);
                    items.forEach(c => {
                        rows.push([
                            c.id,
                            c.complainant,
                            c.complainant_type,
                            c.accused,
                            c.accused_type,
                            c.category,
                            c.status,
                            new Date(c.date_raw).toLocaleString(),
                            c.ride_id,
                            c.plate_number || 'N/A',
                            c.description.replace(/,/g, ';')
                        ]);
                    });
                    rows.push([]);
                });
            } else {
                filtered.forEach(c => {
                    rows.push([
                        c.id,
                        c.complainant,
                        c.complainant_type,
                        c.accused,
                        c.accused_type,
                        c.category,
                        c.status,
                        new Date(c.date_raw).toLocaleString(),
                        c.ride_id,
                        c.plate_number || 'N/A',
                        c.description.replace(/,/g, ';')
                    ]);
                });
            }
            
            let csv = headers.join(',') + '\n';
            csv += rows.map(r => r.map(cell => `"${cell}"`).join(',')).join('\n');
            
            const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `complaint_report_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</body>
</html>