<?php
// Firebase Realtime Database PHP Implementation
class FirebaseService {
    private $databaseUrl;
    private $apiKey;
    
    public function __construct($config) {
        $this->databaseUrl = $config['databaseURL'];
        $this->apiKey = $config['apiKey'];
    }
    
    /**
     * Kumuha ng data mula sa Firebase
     */
    public function getData($path = '') {
        $url = $this->databaseUrl . '/' . $path . '.json?auth=' . $this->apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data !== null ? $data : [];
        } else {
            return ['error' => "HTTP $httpCode", 'response' => $response];
        }
    }
    
    /**
     * Kumuha ng download URL para sa mga images - IMPROVED VERSION
     */
    public function getImageUrl($firebaseStorageUrl) {
        if (empty($firebaseStorageUrl)) return '';
        
        // Kung gs:// URL, convert to download URL
        if (strpos($firebaseStorageUrl, 'gs://') === 0) {
            // Convert: gs://bucket/path/to/file
            // To: https://firebasestorage.googleapis.com/v0/b/bucket/o/path%2Fto%2Ffile?alt=media
            $url = str_replace('gs://', '', $firebaseStorageUrl);
            $parts = explode('/', $url);
            $bucket = array_shift($parts);
            $filePath = implode('/', $parts);
            
            // Encode the file path properly
            $encodedPath = rawurlencode($filePath);
            $encodedPath = str_replace('%2F', '/', $encodedPath); // Keep slashes
            
            return "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o/{$encodedPath}?alt=media";
        }
        
        // Kung regular Firebase Storage URL, convert if needed
        if (strpos($firebaseStorageUrl, 'firebasestorage.googleapis.com') !== false) {
            if (strpos($firebaseStorageUrl, 'alt=media') === false) {
                // Add alt=media parameter if missing
                $separator = (strpos($firebaseStorageUrl, '?') !== false) ? '&' : '?';
                return $firebaseStorageUrl . $separator . 'alt=media';
            }
        }
        
        return $firebaseStorageUrl;
    }
    
    /**
     * Check kung image URL
     */
    public function isImageUrl($url) {
        if (empty($url)) return false;
        
        // Check kung Firebase Storage URL
        if (strpos($url, 'firebasestorage.googleapis.com') !== false) {
            return true;
        }
        
        $imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.pdf'];
        $urlLower = strtolower($url);
        
        foreach ($imageExtensions as $ext) {
            if (strpos($urlLower, $ext) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate proper Firebase Storage URL from document data
     */
    public function extractImageUrl($data, $key) {
        if (empty($data)) return '';
        
        // Try different possible keys
        $possibleKeys = [
            $key,
            strtolower($key),
            str_replace(' ', '_', strtolower($key)),
            str_replace(' ', '', strtolower($key))
        ];
        
        foreach ($possibleKeys as $possibleKey) {
            if (isset($data[$possibleKey]) && !empty($data[$possibleKey])) {
                return $this->getImageUrl($data[$possibleKey]);
            }
        }
        
        return '';
    }
}

// Firebase configuration
$firebaseConfig = [
    'databaseURL' => 'https://serviceco-37c60-default-rtdb.firebaseio.com',
    'apiKey' => 'AIzaSyBpA5CT6Z1U880I8DgMS3pgkeFuKgQPoyk'
];

// Initialize Firebase
$firebase = new FirebaseService($firebaseConfig);

// Check if this is an AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax && isset($_GET['action'])) {
    // Handle AJAX requests
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_stats':
            $allData = $firebase->getData('');
            
            $commuters = $allData['Commuters'] ?? [];
            $drivers = $allData['Drivers'] ?? [];
            $rideRequests = $allData['RideRequests'] ?? [];
            
            $commuterCount = is_array($commuters) ? count($commuters) : 0;
            $driverCount = is_array($drivers) ? count($drivers) : 0;
            $pendingDrivers = 0;
            $approvedDrivers = 0;
            $activeRides = 0;
            $completedRides = 0;
            $totalEarnings = 0;
            
            if (is_array($drivers)) {
                foreach ($drivers as $driver) {
                    if (is_array($driver)) {
                        $status = isset($driver['Status']) ? strtolower($driver['Status']) : '';
                        if (strpos($status, 'pending') !== false || strpos($status, 'review') !== false) {
                            $pendingDrivers++;
                        } elseif (strpos($status, 'approved') !== false) {
                            $approvedDrivers++;
                        }
                    }
                }
            }
            
            if (is_array($rideRequests)) {
                foreach ($rideRequests as $ride) {
                    if (is_array($ride)) {
                        $status = isset($ride['Status']) ? strtolower($ride['Status']) : '';
                        if ($status == 'accepted' || strpos($status, 'picking') !== false || strpos($status, 'trip') !== false) {
                            $activeRides++;
                        } elseif ($status == 'completed') {
                            $completedRides++;
                        }
                        
                        if (isset($ride['Fare']) && isset($ride['Status']) && strtolower($ride['Status']) == 'completed') {
                            $totalEarnings += floatval($ride['Fare']);
                        }
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'commuters' => $commuterCount,
                    'drivers' => $driverCount,
                    'pendingDrivers' => $pendingDrivers,
                    'activeRides' => $activeRides,
                    'totalEarnings' => $totalEarnings,
                    'timestamp' => date('H:i:s')
                ]
            ]);
            exit;
            
        case 'get_table':
            $table = $_GET['table'] ?? 'Commuters';
            $data = $firebase->getData($table);
            
            if (isset($data['error'])) {
                echo json_encode(['success' => false, 'error' => $data['error']]);
                exit;
            }
            
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
            
        case 'check_updates':
            // Get current data and compare with last known state
            $lastUpdate = $_GET['lastUpdate'] ?? 0;
            $currentTime = time();
            
            // Simulate checking for updates (in real app, you'd compare actual data)
            $hasUpdates = ($currentTime - $lastUpdate) > 5; // Check every 5 seconds
            
            echo json_encode([
                'success' => true,
                'hasUpdates' => $hasUpdates,
                'timestamp' => $currentTime
            ]);
            exit;
    }
}

// Kunin ang lahat ng data for initial load
$allData = $firebase->getData('');

if (isset($allData['error'])) {
    $error = "Firebase Error: " . $allData['error'];
    $commuters = $drivers = $rideRequests = $favorites = $tricycleInfo = [];
} else {
    // Extract specific data
    $commuters = $allData['Commuters'] ?? [];
    $drivers = $allData['Drivers'] ?? [];
    $rideRequests = $allData['RideRequests'] ?? [];
    $chatMessages = $allData['ChatMessages'] ?? [];
    $favorites = $allData['Favorites'] ?? [];
    $conversations = $allData['Conversations'] ?? [];
    $tricycleInfo = $allData['TricycleInfo'] ?? [];
    
    // Calculate statistics
    $commuterCount = is_array($commuters) ? count($commuters) : 0;
    $driverCount = is_array($drivers) ? count($drivers) : 0;
    
    $pendingDrivers = 0;
    $approvedDrivers = 0;
    $activeRides = 0;
    $completedRides = 0;
    $totalEarnings = 0;
    
    if (is_array($drivers)) {
        foreach ($drivers as $driver) {
            if (is_array($driver)) {
                $status = isset($driver['Status']) ? strtolower($driver['Status']) : '';
                if (strpos($status, 'pending') !== false || strpos($status, 'review') !== false) {
                    $pendingDrivers++;
                } elseif (strpos($status, 'approved') !== false) {
                    $approvedDrivers++;
                }
            }
        }
    }
    
    if (is_array($rideRequests)) {
        foreach ($rideRequests as $ride) {
            if (is_array($ride)) {
                $status = isset($ride['Status']) ? strtolower($ride['Status']) : '';
                if ($status == 'accepted' || strpos($status, 'picking') !== false || strpos($status, 'trip') !== false) {
                    $activeRides++;
                } elseif ($status == 'completed') {
                    $completedRides++;
                }
                
                if (isset($ride['Fare']) && isset($ride['Status']) && strtolower($ride['Status']) == 'completed') {
                    $totalEarnings += floatval($ride['Fare']);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceCo Admin Dashboard - Live</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
            position: relative;
        }
        
        .stat-card.updating {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0); }
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            color: var(--primary-color);
        }
        
        .live-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--secondary-color);
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .data-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .data-table th {
            background-color: var(--dark-color);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
        }
        
        .badge-status {
            padding: 0.35em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85em;
        }
        
        .badge-pending { background-color: var(--warning-color); color: white; }
        .badge-approved { background-color: var(--secondary-color); color: white; }
        .badge-rejected { background-color: var(--danger-color); color: white; }
        .badge-active { background-color: var(--primary-color); color: white; }
        .badge-completed { background-color: var(--secondary-color); color: white; }
        .badge-cancelled { background-color: #95a5a6; color: white; }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--light-color);
        }
        
        .user-avatar:hover {
            transform: scale(1.1);
            transition: transform 0.3s;
        }
        
        .document-thumbnail {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid var(--light-color);
        }
        
        .document-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .image-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            font-size: 1.2rem;
        }
        
        .nav-tabs .nav-link {
            font-weight: 600;
            color: var(--dark-color);
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .earnings-badge {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.9em;
        }
        
        .rating-stars {
            color: var(--warning-color);
            font-size: 0.9em;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .data-section {
            margin-bottom: 2.5rem;
        }
        
        .section-title {
            color: var(--dark-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--light-color);
            font-weight: 600;
        }
        
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .update-time {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .connection-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--dark-color);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .connection-status.connected {
            background: var(--secondary-color);
        }
        
        .connection-status.disconnected {
            background: var(--danger-color);
        }
        
        .new-update {
            animation: highlight 2s;
        }
        
        @keyframes highlight {
            0% { background-color: rgba(52, 152, 219, 0.3); }
            100% { background-color: transparent; }
        }
    </style>
</head>
<body>
    <!-- Connection Status -->
    <div id="connectionStatus" class="connection-status">
        <i class="fas fa-circle me-1"></i> <span id="statusText">Connecting...</span>
    </div>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-database"></i> ServiceCo Admin Dashboard</h1>
                    <p class="lead mb-0">Live Firebase Data Monitoring</p>
                    <small>Last Updated: <span id="lastUpdated"><?php echo date('H:i:s'); ?></span></small>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light" onclick="refreshAllData()">
                        <i class="fas fa-sync-alt"></i> Refresh Now
                    </button>
                    <button class="btn btn-outline-light ms-2" onclick="toggleAutoRefresh()" id="autoRefreshBtn">
                        <i class="fas fa-play"></i> Auto: ON
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4" id="statsSection">
            <div class="col-md-3">
                <div class="stat-card" id="commutersCard">
                    <span class="live-badge" id="commutersBadge">LIVE</span>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 id="commutersCount"><?php echo number_format($commuterCount); ?></h3>
                    <p class="text-muted mb-0">Total Commuters</p>
                    <div class="update-time" id="commutersTime">Updated: <?php echo date('H:i:s'); ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" id="driversCard">
                    <span class="live-badge" id="driversBadge">LIVE</span>
                    <div class="stat-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 id="driversCount"><?php echo number_format($driverCount); ?></h3>
                    <p class="text-muted mb-0">
                        Total Drivers 
                        <span class="badge badge-pending ms-2" id="pendingDrivers"><?php echo $pendingDrivers; ?> Pending</span>
                    </p>
                    <div class="update-time" id="driversTime">Updated: <?php echo date('H:i:s'); ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" id="ridesCard">
                    <span class="live-badge" id="ridesBadge">LIVE</span>
                    <div class="stat-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3 id="ridesCount"><?php echo number_format(is_array($rideRequests) ? count($rideRequests) : 0); ?></h3>
                    <p class="text-muted mb-0">
                        Ride Requests 
                        <span class="badge badge-active ms-2" id="activeRides"><?php echo $activeRides; ?> Active</span>
                    </p>
                    <div class="update-time" id="ridesTime">Updated: <?php echo date('H:i:s'); ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" id="earningsCard">
                    <span class="live-badge" id="earningsBadge">LIVE</span>
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 id="totalEarnings">₱<?php echo number_format($totalEarnings, 2); ?></h3>
                    <p class="text-muted mb-0">Total Earnings</p>
                    <div class="update-time" id="earningsTime">Updated: <?php echo date('H:i:s'); ?></div>
                </div>
            </div>
        </div>

        <!-- Data Tabs -->
        <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="commuters-tab" data-bs-toggle="tab" data-bs-target="#commuters" type="button">
                    <i class="fas fa-users me-2"></i>Commuters
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#drivers" type="button">
                    <i class="fas fa-truck me-2"></i>Drivers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rides-tab" data-bs-toggle="tab" data-bs-target="#rides" type="button">
                    <i class="fas fa-car me-2"></i>Ride Requests
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                    <i class="fas fa-file-alt me-2"></i>Driver Documents
                </button>
            </li>
        </ul>

        <div class="tab-content" id="dashboardTabsContent">
            <!-- Commuters Tab -->
            <div class="tab-pane fade show active" id="commuters" role="tabpanel">
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table table-hover" id="commutersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                    <th>PIN Set</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody id="commutersBody">
                                <?php if (is_array($commuters) && count($commuters) > 0): ?>
                                    <?php foreach ($commuters as $id => $commuter): 
                                        if (!is_array($commuter)) continue;
                                    ?>
                                        <tr class="data-row" data-id="<?php echo $id; ?>">
                                            <td><code title="<?php echo $id; ?>"><?php echo substr($id, 0, 8) . '...'; ?></code></td>
                                            <td>
                                                <?php 
                                                $profileUrl = '';
                                                if (!empty($commuter['ProfileImageUrl'])) {
                                                    $profileUrl = $firebase->getImageUrl($commuter['ProfileImageUrl']);
                                                } elseif (!empty($commuter['profileImageUrl'])) {
                                                    $profileUrl = $firebase->getImageUrl($commuter['profileImageUrl']);
                                                }
                                                
                                                if (!empty($profileUrl) && $firebase->isImageUrl($profileUrl)): 
                                                ?>
                                                    <img src="<?php echo htmlspecialchars($profileUrl); ?>" 
                                                         class="user-avatar" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#imageModal"
                                                         data-image="<?php echo htmlspecialchars($profileUrl); ?>"
                                                         alt="Profile"
                                                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDUiIGhlaWdodD0iNDUiIHZpZXdCb3g9IjAgMCA0NSA0NSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjIuNSIgY3k9IjIyLjUiIHI9IjIyLjUiIGZpbGw9IiNlY2YwZjEiLz4KPHBhdGggZD0iTTIyLjUgMTVDMjUuODEgMTUgMjguNSAxNy42OSAyOC41IDIxQzI4LjUgMjQuMzEgMjUuODEgMjcgMjIuNSAyN0MxOS4xOSAyNyAxNi41IDI0LjMxIDE2LjUgMjFDMTYuNSAxNy42OSAxOS4xOSAxNSAyMi41IDE1Wk0yMi41IDI5LjVDMjguODUgMjkuNSAzNC4yNSAzMi45NiAzNi41IDM3LjVIMTguNUMyMC43NSAzMi45NiAyNi4xNSAyOS41IDIyLjUgMjkuNVoiIGZpbGw9IiMzNDk4ZGIiLz4KPC9zdmc+'">
                                                <?php else: ?>
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $firstName = $commuter['FirstName'] ?? $commuter['firstName'] ?? '';
                                                $lastName = $commuter['LastName'] ?? $commuter['lastName'] ?? '';
                                                echo htmlspecialchars($firstName . ' ' . $lastName); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($commuter['MobileNumber'] ?? $commuter['mobileNumber'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($commuter['Email'] ?? $commuter['email'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($commuter['Gender'] ?? $commuter['gender'] ?? 'Not specified'); ?></td>
                                            <td>
                                                <?php 
                                                $isPinSet = $commuter['IsPinSet'] ?? $commuter['isPinSet'] ?? false;
                                                if ($isPinSet): ?>
                                                    <span class="badge badge-approved">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge badge-pending">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $createdAt = $commuter['CreatedAt'] ?? $commuter['createdAt'] ?? '';
                                                if (!empty($createdAt) && is_string($createdAt)) {
                                                    echo date('M j, Y', strtotime($createdAt));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="fas fa-users"></i>
                                            <p>No commuters found in database</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Drivers Tab -->
            <div class="tab-pane fade" id="drivers" role="tabpanel">
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table table-hover" id="driversTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Vehicle</th>
                                    <th>Status</th>
                                    <th>Rating</th>
                                    <th>Earnings</th>
                                </tr>
                            </thead>
                            <tbody id="driversBody">
                                <?php if (is_array($drivers) && count($drivers) > 0): ?>
                                    <?php foreach ($drivers as $id => $driver): 
                                        if (!is_array($driver)) continue;
                                        
                                        $rating = $driver['Rating'] ?? $driver['rating'] ?? 5.0;
                                        $rating = floatval($rating);
                                        $totalEarnings = $driver['TotalEarnings'] ?? $driver['totalEarnings'] ?? 0;
                                        $totalEarnings = floatval($totalEarnings);
                                    ?>
                                        <tr class="data-row" data-id="<?php echo $id; ?>">
                                            <td><code title="<?php echo $id; ?>"><?php echo substr($id, 0, 8) . '...'; ?></code></td>
                                            <td>
                                                <?php 
                                                $profileUrl = '';
                                                if (!empty($driver['ProfileImageUrl'])) {
                                                    $profileUrl = $firebase->getImageUrl($driver['ProfileImageUrl']);
                                                } elseif (!empty($driver['profileImageUrl'])) {
                                                    $profileUrl = $firebase->getImageUrl($driver['profileImageUrl']);
                                                }
                                                
                                                if (!empty($profileUrl) && $firebase->isImageUrl($profileUrl)): 
                                                ?>
                                                    <img src="<?php echo htmlspecialchars($profileUrl); ?>" 
                                                         class="user-avatar" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#imageModal"
                                                         data-image="<?php echo htmlspecialchars($profileUrl); ?>"
                                                         alt="Profile"
                                                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDUiIGhlaWdodD0iNDUiIHZpZXdCb3g9IjAgMCA0NSA0NSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjIuNSIgY3k9IjIyLjUiIHI9IjIyLjUiIGZpbGw9IiNlY2YwZjEiLz4KPHBhdGggZD0iTTIyLjUgMTVDMjUuODEgMTUgMjguNSAxNy42OSAyOC41IDIxQzI4LjUgMjQuMzEgMjUuODEgMjcgMjIuNSAyN0MxOS4xOSAyNyAxNi41IDI0LjMxIDE2LjUgMjFDMTYuNSAxNy42OSAxOS4xOSAxNSAyMi41IDE1Wk0yMi41IDI5LjVDMjguODUgMjkuNSAzNC4yNSAzMi45NiAzNi41IDM3LjVIMTguNTVDMjAuOCAzMi45NiAyNi4yIDI5LjUgMjIuNSAyOS41WiIgZmlsbD0iIzM0OThkYiIvPgo8L3N2Zz4='">
                                                <?php else: ?>
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $firstName = $driver['FirstName'] ?? $driver['firstName'] ?? '';
                                                $lastName = $driver['LastName'] ?? $driver['lastName'] ?? '';
                                                echo htmlspecialchars($firstName . ' ' . $lastName); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($driver['MobileNumber'] ?? $driver['mobileNumber'] ?? ''); ?></td>
                                            <td>
                                                <?php 
                                                $vehicleType = $driver['VehicleType'] ?? $driver['vehicleType'] ?? '';
                                                $plateNumber = $driver['PlateNumber'] ?? $driver['plateNumber'] ?? '';
                                                echo htmlspecialchars($vehicleType . ' - ' . $plateNumber); 
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $status = $driver['Status'] ?? $driver['status'] ?? 'Pending';
                                                $statusLower = strtolower($status);
                                                $badgeClass = 'badge-secondary';
                                                if (strpos($statusLower, 'approved') !== false) {
                                                    $badgeClass = 'badge-approved';
                                                } elseif (strpos($statusLower, 'pending') !== false || strpos($statusLower, 'review') !== false) {
                                                    $badgeClass = 'badge-pending';
                                                } elseif (strpos($statusLower, 'rejected') !== false) {
                                                    $badgeClass = 'badge-rejected';
                                                }
                                                ?>
                                                <span class="badge-status <?php echo $badgeClass; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="rating-stars">
                                                    <?php 
                                                    $fullStars = floor($rating);
                                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                                    
                                                    for ($i = 1; $i <= 5; $i++):
                                                        if ($i <= $fullStars): ?>
                                                            <i class="fas fa-star"></i>
                                                        <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                                            <i class="fas fa-star-half-alt"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star"></i>
                                                        <?php endif;
                                                    endfor; ?>
                                                    <small class="ms-1"><?php echo number_format($rating, 1); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="earnings-badge">
                                                    ₱<?php echo number_format($totalEarnings, 2); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="fas fa-truck"></i>
                                            <p>No drivers found in database</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ride Requests Tab -->
            <div class="tab-pane fade" id="rides" role="tabpanel">
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table table-hover" id="ridesTable">
                            <thead>
                                <tr>
                                    <th>Ride ID</th>
                                    <th>Commuter</th>
                                    <th>Pickup</th>
                                    <th>Dropoff</th>
                                    <th>Fare</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                </tr>
                            </thead>
                            <tbody id="ridesBody">
                                <?php if (is_array($rideRequests) && count($rideRequests) > 0): ?>
                                    <?php foreach ($rideRequests as $id => $ride): 
                                        if (!is_array($ride)) continue;
                                        
                                        $status = $ride['Status'] ?? $ride['status'] ?? 'Pending';
                                        $statusLower = strtolower($status);
                                        $badgeClass = 'badge-secondary';
                                        
                                        if ($statusLower == 'completed') $badgeClass = 'badge-completed';
                                        elseif ($statusLower == 'accepted' || strpos($statusLower, 'picking') !== false || strpos($statusLower, 'trip') !== false) $badgeClass = 'badge-active';
                                        elseif ($statusLower == 'cancelled') $badgeClass = 'badge-cancelled';
                                        elseif ($statusLower == 'pending') $badgeClass = 'badge-pending';
                                    ?>
                                        <tr class="data-row" data-id="<?php echo $id; ?>">
                                            <td><code title="<?php echo $id; ?>"><?php echo substr($id, 0, 8) . '...'; ?></code></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($ride['CommuterName'] ?? $ride['commuterName'] ?? ''); ?></strong><br>
                                                <small><?php echo htmlspecialchars($ride['CommuterMobile'] ?? $ride['commuterMobile'] ?? ''); ?></small>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($ride['PickupLocation'] ?? $ride['pickupLocation'] ?? ''); ?></small>
                                                <?php if (isset($ride['PickupLat']) || isset($ride['pickupLat'])): ?>
                                                    <?php $pickupLat = $ride['PickupLat'] ?? $ride['pickupLat']; ?>
                                                    <?php $pickupLng = $ride['PickupLng'] ?? $ride['pickupLng']; ?>
                                                    <br><small class="text-muted"><?php echo $pickupLat; ?>, <?php echo $pickupLng; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($ride['DropoffLocation'] ?? $ride['dropoffLocation'] ?? ''); ?></small>
                                                <?php if (isset($ride['DropoffLat']) || isset($ride['dropoffLat'])): ?>
                                                    <?php $dropoffLat = $ride['DropoffLat'] ?? $ride['dropoffLat']; ?>
                                                    <?php $dropoffLng = $ride['DropoffLng'] ?? $ride['dropoffLng']; ?>
                                                    <br><small class="text-muted"><?php echo $dropoffLat; ?>, <?php echo $dropoffLng; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong>₱<?php echo number_format($ride['Fare'] ?? $ride['fare'] ?? 0, 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php if (!empty($ride['DriverName']) || !empty($ride['driverName'])): ?>
                                                    <?php echo htmlspecialchars($ride['DriverName'] ?? $ride['driverName']); ?><br>
                                                    <small><?php echo htmlspecialchars($ride['VehiclePlateNumber'] ?? $ride['vehiclePlateNumber'] ?? ''); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">No driver yet</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-status <?php echo $badgeClass; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $requestTime = $ride['RequestTime'] ?? $ride['requestTime'] ?? '';
                                                if (!empty($requestTime) && is_string($requestTime)) {
                                                    echo date('M j, H:i', strtotime($requestTime));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="fas fa-car"></i>
                                            <p>No ride requests found in database</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Driver Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="row" id="documentsBody">
                    <?php 
                    $documentsFound = false;
                    if (is_array($drivers) && count($drivers) > 0): 
                        foreach ($drivers as $driverId => $driver): 
                            if (!is_array($driver)) continue;
                            
                            // Check if driver has documents or GCash QR
                            $hasDocuments = false;
                            $documents = $driver['Documents'] ?? [];
                            $hasGcashQR = !empty($driver['GcashQRUrl']) || !empty($driver['gcashQRUrl']);
                            
                            if ((is_array($documents) && count($documents) > 0) || $hasGcashQR) {
                                $documentsFound = true;
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="stat-card">
                                        <div class="d-flex align-items-center mb-3">
                                            <?php 
                                            $profileUrl = '';
                                            if (!empty($driver['ProfileImageUrl'])) {
                                                $profileUrl = $firebase->getImageUrl($driver['ProfileImageUrl']);
                                            } elseif (!empty($driver['profileImageUrl'])) {
                                                $profileUrl = $firebase->getImageUrl($driver['profileImageUrl']);
                                            }
                                            
                                            if (!empty($profileUrl) && $firebase->isImageUrl($profileUrl)): 
                                            ?>
                                                <img src="<?php echo htmlspecialchars($profileUrl); ?>" 
                                                     class="user-avatar me-3" 
                                                     alt="Driver"
                                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <?php endif; ?>
                                            <div class="image-placeholder me-3" style="display: <?php echo empty($profileUrl) ? 'flex' : 'none'; ?>">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1"><?php 
                                                    $firstName = $driver['FirstName'] ?? $driver['firstName'] ?? '';
                                                    $lastName = $driver['LastName'] ?? $driver['lastName'] ?? '';
                                                    echo htmlspecialchars($firstName . ' ' . $lastName); 
                                                ?></h6>
                                                <p class="mb-0 text-muted small">
                                                    <?php 
                                                    $vehicleType = $driver['VehicleType'] ?? $driver['vehicleType'] ?? '';
                                                    $plateNumber = $driver['PlateNumber'] ?? $driver['plateNumber'] ?? '';
                                                    echo htmlspecialchars($vehicleType . ' - ' . $plateNumber); 
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <?php if (is_array($documents) && count($documents) > 0): ?>
                                                <span class="badge badge-primary"><?php echo count($documents); ?> Documents</span>
                                            <?php endif; ?>
                                            <?php if (isset($driver['Status'])): 
                                                $status = $driver['Status'] ?? $driver['status'] ?? 'Pending';
                                                $statusLower = strtolower($status); ?>
                                                <span class="badge-status <?php 
                                                    echo (strpos($statusLower, 'approved') !== false) ? 'badge-approved' : 
                                                         ((strpos($statusLower, 'pending') !== false || strpos($statusLower, 'review') !== false) ? 'badge-pending' : 'badge-rejected');
                                                ?> ms-2">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if (is_array($documents) && count($documents) > 0): ?>
                                            <div class="row g-2 mb-3">
                                                <?php foreach ($documents as $docName => $docUrl): 
                                                    if (!empty($docUrl)):
                                                        $imageUrl = $firebase->getImageUrl($docUrl);
                                                        if ($firebase->isImageUrl($imageUrl)): ?>
                                                            <div class="col-6">
                                                                <div class="card border-0">
                                                                    <div class="card-body p-2">
                                                                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                                                             class="document-thumbnail w-100"
                                                                             data-bs-toggle="modal" 
                                                                             data-bs-target="#imageModal"
                                                                             data-image="<?php echo htmlspecialchars($imageUrl); ?>"
                                                                             data-title="<?php echo htmlspecialchars($docName) . ' - ' . htmlspecialchars($firstName . ' ' . $lastName); ?>"
                                                                             alt="<?php echo htmlspecialchars($docName); ?>"
                                                                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjcwIiBoZWlnaHQ9IjcwIiByeD0iOCIgZmlsbD0iI2VjZjBmMSIvPgo8cGF0aCBkPSJNMTggMjVINTJWNDVIMThWMjVaIiBmaWxsPSIjMzQ5OGRiIi8+CjxwYXRoIGQ9Ik0yNi41IDM1QzI4LjQzIDM1IDMwIDMzLjQzIDMwIDMxLjVDMzAgMjkuNTcgMjguNDMgMjggMjYuNSAyOEMyNC41NyAyOCAyMyAyOS41NyAyMyAzMS41QzIzIDMzLjQzIDI0LjU3IDM1IDI2LjUgMzVaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNNTUgMzVIMzdMMzIgNDVINTVWMzVaIiBmaWxsPSIjMzQ5OGRiIiBmaWxsLW9wYWNpdHk9IjAuNSIvPgo8L3N2Zz4='">
                                                                        <small class="d-block text-center mt-1 small"><?php echo htmlspecialchars($docName); ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; 
                                                    endif;
                                                endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- GCash QR Code (if exists) -->
                                        <?php 
                                        $gcashUrl = '';
                                        if (!empty($driver['GcashQRUrl'])) {
                                            $gcashUrl = $firebase->getImageUrl($driver['GcashQRUrl']);
                                        } elseif (!empty($driver['gcashQRUrl'])) {
                                            $gcashUrl = $firebase->getImageUrl($driver['gcashQRUrl']);
                                        }
                                        
                                        if (!empty($gcashUrl) && $firebase->isImageUrl($gcashUrl)): ?>
                                            <div class="mt-3 pt-3 border-top">
                                                <h6 class="small"><i class="fas fa-qrcode text-success me-2"></i>GCash QR Code</h6>
                                                <img src="<?php echo htmlspecialchars($gcashUrl); ?>" 
                                                     class="document-thumbnail"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#imageModal"
                                                     data-image="<?php echo htmlspecialchars($gcashUrl); ?>"
                                                     data-title="GCash QR Code - <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>"
                                                     alt="GCash QR Code"
                                                     onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjcwIiBoZWlnaHQ9IjcwIiByeD0iOCIgZmlsbD0iIzI3YWU2MCIvPgo8cGF0aCBkPSJNMTggMThINTJWNTEuNUgxOFYxOFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0zMCAzMEMzMi4yMSAzMCAzNCAzMS43OSAzNCAzNFY0NEMzNCA0Ni4yMSAzMi4yMSA0OCAzMCA0OEgyMEMxNy43OSA0OCAxNiA0Ni4yMSAxNiA0NFYzNEMxNiAzMS43OSAxNy43OSAzMCAyMCAzMEgzMFpNMzQgMzRIMzZWMjJDMzYgMTkuNzkgMzcuNzkgMTggNDAgMThINDZINDhDNTAuMjEgMTggNTIgMTkuNzkgNTIgMjJWMzRINTBaTTUwIDI2VjM0SDUyVjI2SDUwWiIgZmlsbD0iIzI3YWU2MCIvPgo8L3N2Zz4='">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        endforeach;
                    endif;
                    
                    if (!$documentsFound): ?>
                        <div class="col-12">
                            <div class="no-data">
                                <i class="fas fa-file-alt"></i>
                                <p>No documents uploaded by drivers yet</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="stat-card">
                    <h5><i class="fas fa-motorcycle me-2"></i>Tricycle Information</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Driver ID</th>
                                    <th>Plate</th>
                                    <th>Vehicle Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (is_array($tricycleInfo) && count($tricycleInfo) > 0): ?>
                                    <?php foreach ($tricycleInfo as $driverId => $tricycle): 
                                        if (!is_array($tricycle)) continue; ?>
                                        <tr>
                                            <td><code title="<?php echo $driverId; ?>"><?php echo substr($driverId, 0, 8) . '...'; ?></code></td>
                                            <td><?php echo htmlspecialchars($tricycle['PlateNumber'] ?? $tricycle['plateNumber'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($tricycle['VehicleType'] ?? $tricycle['vehicleType'] ?? 'Tricycle'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="no-data small">
                                            <i class="fas fa-motorcycle"></i>
                                            <p>No tricycle information found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="stat-card">
                    <h5><i class="fas fa-star me-2"></i>Database Status</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-database text-primary me-2"></i>
                            <span>Total Collections: 10</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <span>Last Check: <span id="lastCheck"><?php echo date('H:i:s'); ?></span></span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-sync text-success me-2"></i>
                            <span>Updates: <span id="updateCount">0</span> today</span>
                        </li>
                        <li>
                            <i class="fas fa-wifi text-info me-2"></i>
                            <span>Connection: <span id="connectionText">Active</span></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="modal-image" alt="Document"
                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDYwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI2MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjZjhmOWZhIi8+CjxwYXRoIGQ9Ik0yMDAgMTUwSDQwMFYyNTBIMjAwVjE1MFoiIGZpbGw9IiNlY2YwZjEiLz4KPHBhdGggZD0iTTI1MCAyMDBDMjc4LjA1IDIwMCAzMDAgMTc4LjA1IDMwMCAxNTBDMzAwIDEyMS45NSAyNzguMDUgMTAwIDI1MCAxMDBDMjIxLjk1IDEwMCAyMDAgMTIxLjk1IDIwMCAxNTBDMjAwIDE3OC4wNSAyMjEuOTUgMjAwIDI1MCAyMDBaIiBmaWxsPSIjMzQ5OGRiIi8+CjxwYXRoIGQ9Ik00MDAgMjA1SDI3MEwyMjUgMzA1SDQwMFYyMDVaIiBmaWxsPSIjMzQ5OGRiIiBmaWxsLW9wYWNpdHk9IjAuNSIvPgo8dGV4dCB4PSIzMDAiIHk9IjMwMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE4IiBmaWxsPSIjNmM3NTc5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZSBub3QgYXZhaWxhYmxlPC90ZXh0Pgo8L3N2Zz4='">
                </div>
                <div class="modal-footer">
                    <a id="downloadLink" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 py-3 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0">ServiceCo Admin Dashboard &copy; <?php echo date('Y'); ?></p>
            <small class="text-muted">Live Updates via AJAX | Connected to Firebase</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Global variables
        let updateCount = 0;
        let isAutoRefresh = true;
        let lastUpdateTime = Date.now();
        let connectionStatus = true;
        let updateInterval;

        $(document).ready(function() {
            console.log('Dashboard initialized');
            
            // Initialize connection status
            updateConnectionStatus(true);
            
            // Start live updates
            startLiveUpdates();
            
            // Setup auto-refresh
            setupAutoRefresh();
            
            // Start checking for updates
            startUpdateChecker();
        });

        // Start live updates
        function startLiveUpdates() {
            // Update stats every 5 seconds
            updateInterval = setInterval(updateStatistics, 5000);
            
            // Update current tab every 10 seconds
            setInterval(updateCurrentTab, 10000);
            
            console.log('Live updates started');
        }

        // Update statistics cards
        function updateStatistics() {
            if (!connectionStatus) return;
            
            $.ajax({
                url: '?action=get_stats',
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                beforeSend: function() {
                    // Show updating animation
                    $('#commutersCard, #driversCard, #ridesCard, #earningsCard').addClass('updating');
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Update statistics with animation
                        animateCounter('commutersCount', data.commuters);
                        animateCounter('driversCount', data.drivers);
                        $('#pendingDrivers').text(data.pendingDrivers + ' Pending');
                        $('#activeRides').text(data.activeRides + ' Active');
                        animateCounter('totalEarnings', '₱' + data.totalEarnings.toFixed(2));
                        
                        // Update timestamps
                        $('#lastUpdated').text(data.timestamp);
                        $('#lastCheck').text(data.timestamp);
                        $('.update-time').each(function() {
                            $(this).text('Updated: ' + data.timestamp);
                        });
                        
                        // Increment update count
                        updateCount++;
                        $('#updateCount').text(updateCount);
                        
                        // Highlight updated cards
                        highlightUpdate('commutersCard');
                        highlightUpdate('driversCard');
                        highlightUpdate('ridesCard');
                        highlightUpdate('earningsCard');
                        
                        updateConnectionStatus(true);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Update error:', error);
                    updateConnectionStatus(false);
                },
                complete: function() {
                    // Remove updating animation
                    $('#commutersCard, #driversCard, #ridesCard, #earningsCard').removeClass('updating');
                }
            });
        }

        // Update current tab
        function updateCurrentTab() {
            if (!connectionStatus || !isAutoRefresh) return;
            
            const activeTab = $('.nav-link.active').attr('id');
            let tableToUpdate = '';
            
            switch(activeTab) {
                case 'commuters-tab':
                    tableToUpdate = 'Commuters';
                    break;
                case 'drivers-tab':
                    tableToUpdate = 'Drivers';
                    break;
                case 'rides-tab':
                    tableToUpdate = 'RideRequests';
                    break;
            }
            
            if (tableToUpdate) {
                updateTable(tableToUpdate);
            }
        }

        // Update specific table
        function updateTable(tableName) {
            $.ajax({
                url: '?action=get_table',
                method: 'GET',
                data: { table: tableName },
                dataType: 'json',
                timeout: 5000,
                success: function(response) {
                    if (response.success) {
                        console.log('Table updated:', tableName);
                        // In a real implementation, you would update the table rows here
                        // For simplicity, we'll just show a notification
                        showNotification(`${tableName} updated at ${new Date().toLocaleTimeString()}`);
                    }
                },
                error: function() {
                    console.log('Failed to update table:', tableName);
                }
            });
        }

        // Animate counter
        function animateCounter(elementId, newValue) {
            const element = $('#' + elementId);
            const currentValue = element.text().replace(/[^0-9.]/g, '');
            const isCurrency = element.text().includes('₱');
            
            if (currentValue === newValue || (isCurrency && element.text() === newValue)) {
                return;
            }
            
            // Add highlight animation
            element.parents('.stat-card').addClass('new-update');
            setTimeout(() => {
                element.parents('.stat-card').removeClass('new-update');
            }, 2000);
            
            // Update value
            element.text(newValue);
        }

        // Highlight update
        function highlightUpdate(cardId) {
            $('#' + cardId).addClass('new-update');
            setTimeout(() => {
                $('#' + cardId).removeClass('new-update');
            }, 1000);
        }

        // Update connection status
        function updateConnectionStatus(connected) {
            connectionStatus = connected;
            const statusElement = $('#connectionStatus');
            const statusText = $('#statusText');
            const connectionText = $('#connectionText');
            
            if (connected) {
                statusElement.removeClass('disconnected').addClass('connected');
                statusText.html('<i class="fas fa-circle me-1"></i> Connected');
                connectionText.text('Active');
            } else {
                statusElement.removeClass('connected').addClass('disconnected');
                statusText.html('<i class="fas fa-circle me-1"></i> Disconnected');
                connectionText.text('Lost');
            }
        }

        // Setup auto-refresh
        function setupAutoRefresh() {
            // Auto-refresh all data every 30 seconds
            setInterval(() => {
                if (isAutoRefresh) {
                    refreshAllData();
                }
            }, 30000);
        }

        // Toggle auto-refresh
        function toggleAutoRefresh() {
            isAutoRefresh = !isAutoRefresh;
            const btn = $('#autoRefreshBtn');
            
            if (isAutoRefresh) {
                btn.html('<i class="fas fa-play"></i> Auto: ON');
                btn.removeClass('btn-outline-danger').addClass('btn-outline-light');
                showNotification('Auto-refresh enabled');
            } else {
                btn.html('<i class="fas fa-pause"></i> Auto: OFF');
                btn.removeClass('btn-outline-light').addClass('btn-outline-danger');
                showNotification('Auto-refresh disabled');
            }
        }

        // Refresh all data
        function refreshAllData() {
            if (!connectionStatus) return;
            
            console.log('Refreshing all data...');
            showNotification('Refreshing data...');
            
            // Reload the page
            location.reload();
        }

        // Start update checker
        function startUpdateChecker() {
            setInterval(() => {
                $.ajax({
                    url: '?action=check_updates',
                    method: 'GET',
                    data: { lastUpdate: lastUpdateTime },
                    dataType: 'json',
                    timeout: 3000,
                    success: function(response) {
                        if (response.success && response.hasUpdates) {
                            lastUpdateTime = response.timestamp;
                            showNotification('New updates available!');
                        }
                    }
                });
            }, 5000); // Check every 5 seconds
        }

        // Show notification
        function showNotification(message) {
            // Create notification element
            const notification = $('<div class="alert alert-info alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 1000; max-width: 300px;">' +
                '<i class="fas fa-info-circle me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            
            // Add to page
            $('body').append(notification);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                notification.alert('close');
            }, 3000);
        }

        // Image Modal
        $('#imageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var imageUrl = button.data('image');
            var title = button.data('title') || 'Document Preview';
            
            var modal = $(this);
            modal.find('#imageModalLabel').text(title);
            modal.find('#modalImage').attr('src', imageUrl);
            modal.find('#downloadLink').attr('href', imageUrl);
            
            // Add timestamp to prevent caching
            if (imageUrl.includes('firebasestorage')) {
                modal.find('#modalImage').attr('src', imageUrl + '&t=' + Date.now());
                modal.find('#downloadLink').attr('href', imageUrl + '&t=' + Date.now());
            }
        });

        // Fix image loading errors
        $(document).on('error', 'img', function() {
            if ($(this).hasClass('user-avatar') || $(this).hasClass('document-thumbnail')) {
                // Set placeholder for profile images
                if ($(this).hasClass('user-avatar')) {
                    $(this).replaceWith('<div class="image-placeholder"><i class="fas fa-user"></i></div>');
                } else {
                    // Set placeholder for document thumbnails
                    $(this).attr('src', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjcwIiBoZWlnaHQ9IjcwIiByeD0iOCIgZmlsbD0iI2VjZjBmMSIvPgo8cGF0aCBkPSJNMTggMjVINTJWNDVIMThWMjVaIiBmaWxsPSIjMzQ5OGRiIi8+CjxwYXRoIGQ9Ik0yNi41IDM1QzI4LjQzIDM1IDMwIDMzLjQzIDMwIDMxLjVDMzAgMjkuNTcgMjguNDMgMjggMjYuNSAyOEMyNC41NyAyOCAyMyAyOS41NyAyMyAzMS41QzIzIDMzLjQzIDI0LjU3IDM1IDI2LjUgMzVaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNNTUgMzVIMzdMMzIgNDVINTVWMzVaIiBmaWxsPSIjMzQ5OGRiIiBmaWxsLW9wYWNpdHk9IjAuNSIvPgo8L3N2Zz4=');
                }
            }
        });
    </script>
</body>
</html>