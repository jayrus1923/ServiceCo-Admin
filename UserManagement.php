<?php
session_start();

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

// ============================================
// FETCH DRIVERS FROM FIREBASE
// ============================================
function fetchDriversFromFirebase() {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && $response) {
        return json_decode($response, true) ?: [];
    }
    return [];
}

// ============================================
// ✅ UPDATE DRIVER TRICYCLE DETAILS
// ============================================
function updateDriverTricycle($driverId, $tricycleData) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        // Update only tricycle-related fields
        $driverData['PlateNumber'] = $tricycleData['plate_number'];
        $driverData['VehicleModel'] = $tricycleData['model'];
        $driverData['VehicleColor'] = $tricycleData['color'];
        $driverData['ORCRNumber'] = $tricycleData['or_cr_number'];
        $driverData['LicenseNumber'] = $tricycleData['license_number'];
        $driverData['LicenseExpiry'] = $tricycleData['expiry_date'];
        $driverData['BodyType'] = $tricycleData['body_type'];
        $driverData['PassengerCapacity'] = $tricycleData['passenger_capacity'];
        $driverData['YearManufactured'] = $tricycleData['year_manufactured'];
        $driverData['LastUpdated'] = date('c');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($driverData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

// ============================================
// ✅ APPROVE DRIVER
// ============================================
function approveDriver($driverId) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        $driverData['DocumentStatus'] = 'Approved';
        $driverData['RegistrationCompleted'] = true;
        $driverData['AccountStatus'] = 'Active';
        $driverData['LastUpdated'] = date('c');
        $driverData['ApprovedDate'] = date('c');
        unset($driverData['RejectionReason']);
        unset($driverData['DeactivationReason']);
        unset($driverData['SuspendedUntil']);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($driverData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

// ============================================
// ✅ REJECT DRIVER
// ============================================
function rejectDriver($driverId, $reason) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        $driverData['DocumentStatus'] = 'Rejected';
        $driverData['RegistrationCompleted'] = false;
        $driverData['AccountStatus'] = 'Rejected';
        $driverData['RejectionReason'] = $reason;
        $driverData['RejectedDate'] = date('c');
        $driverData['LastUpdated'] = date('c');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($driverData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

// ============================================
// ✅ DEACTIVATE DRIVER (WITH REASON)
// ============================================
function deactivateDriver($driverId, $reason) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        $driverData['AccountStatus'] = 'Deactivated';
        $driverData['Status'] = 'Offline';
        $driverData['DeactivationReason'] = $reason;
        $driverData['DeactivatedDate'] = date('c');
        $driverData['LastUpdated'] = date('c');
        unset($driverData['SuspendedUntil']);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($driverData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

// ============================================
// ✅ SUSPEND DRIVER (WITH DATE)
// ============================================
function suspendDriver($driverId, $suspendUntil) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        $driverData['AccountStatus'] = 'Suspended';
        $driverData['Status'] = 'Offline';
        $driverData['SuspendedUntil'] = $suspendUntil;
        $driverData['SuspendedDate'] = date('c');
        $driverData['LastUpdated'] = date('c');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($driverData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

// ============================================
// ✅ REACTIVATE DRIVER
// ============================================
function reactivateDriver($driverId) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        $driverData['AccountStatus'] = 'Active';
        $driverData['LastUpdated'] = date('c');
        unset($driverData['DeactivationReason']);
        unset($driverData['DeactivatedDate']);
        unset($driverData['SuspendedUntil']);
        unset($driverData['SuspendedDate']);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($driverData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

// ============================================
// HANDLE POST REQUESTS
// ============================================
$actionResult = null;
$rejectionReason = '';
$activeTab = 'applicationTab';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $driverId = $_POST['driver_id'] ?? '';
    $driverName = $_POST['driver_name'] ?? '';
    $activeTab = $_POST['active_tab'] ?? 'applicationTab';
    
    if ($action && $driverId) {
        if ($action === 'approve') {
            $success = approveDriver($driverId);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been approved!" : "Failed to approve driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'reject') {
            $rejectionReason = $_POST['rejection_reason'] ?? 'Incomplete or invalid documents';
            $success = rejectDriver($driverId, $rejectionReason);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been rejected." : "Failed to reject driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'deactivate') {
            $deactivationReason = $_POST['deactivation_reason'] ?? 'No reason provided';
            $success = deactivateDriver($driverId, $deactivationReason);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been deactivated." : "Failed to deactivate driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'suspend') {
            $suspendUntil = $_POST['suspend_until'] ?? date('c', strtotime('+7 days'));
            $success = suspendDriver($driverId, $suspendUntil);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been suspended until " . date('M j, Y', strtotime($suspendUntil)) : "Failed to suspend driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'reactivate') {
            $success = reactivateDriver($driverId);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been reactivated." : "Failed to reactivate driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'update_tricycle') {
            $tricycleData = [
                'plate_number' => $_POST['plate_number'] ?? 'Not Specified',
                'model' => $_POST['model'] ?? 'Not Specified',
                'color' => $_POST['color'] ?? 'Not Specified',
                'or_cr_number' => $_POST['or_cr_number'] ?? 'Not Specified',
                'license_number' => $_POST['license_number'] ?? 'Not Specified',
                'expiry_date' => $_POST['expiry_date'] ?? 'Not Specified',
                'body_type' => $_POST['body_type'] ?? 'Not Specified',
                'passenger_capacity' => $_POST['passenger_capacity'] ?? 'Not Specified',
                'year_manufactured' => $_POST['year_manufactured'] ?? 'Not Specified'
            ];
            $success = updateDriverTricycle($driverId, $tricycleData);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName}'s tricycle details have been updated!" : "Failed to update tricycle details."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
        }
    }
}

// Get active tab from URL parameter
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'applicationTab';

// Get drivers from Firebase
$firebaseDrivers = fetchDriversFromFirebase();

// Process drivers
$driverApplications = [];
$driverMonitoring = [];
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;
$activeCount = 0;
$suspendedCount = 0;
$deactivatedCount = 0;

if (!empty($firebaseDrivers)) {
    foreach ($firebaseDrivers as $driverId => $driver) {
        if (empty($driver['FirstName']) && empty($driver['LastName'])) continue;
        
        $fullName = trim(($driver['FirstName'] ?? '') . ' ' . ($driver['LastName'] ?? ''));
        $initials = '';
        if (!empty($driver['FirstName'])) $initials .= substr($driver['FirstName'], 0, 1);
        if (!empty($driver['LastName'])) $initials .= substr($driver['LastName'], 0, 1);
        if (empty($initials)) $initials = substr($driverId, -2);
        $initials = strtoupper($initials);
        
        $addressParts = [];
        if (!empty($driver['Barangay'])) $addressParts[] = $driver['Barangay'];
        if (!empty($driver['Municipality'])) $addressParts[] = $driver['Municipality'];
        if (!empty($driver['Province'])) $addressParts[] = $driver['Province'];
        $address = !empty($addressParts) ? implode(', ', $addressParts) : 'Not Specified';
        
        $appDate = 'Unknown';
        if (!empty($driver['CreatedAt'])) {
            try {
                $date = new DateTime($driver['CreatedAt']);
                $now = new DateTime();
                $diff = $now->diff($date);
                
                if ($diff->days == 0) $appDate = 'Today, ' . $date->format('g:i A');
                elseif ($diff->days == 1) $appDate = 'Yesterday, ' . $date->format('g:i A');
                else $appDate = $date->format('M j, Y');
            } catch (Exception $e) {
                $appDate = substr($driver['CreatedAt'], 0, 10);
            }
        }
        
        $status = $driver['Status'] ?? 'Offline';
        $statusClass = $status == 'Online' ? 'active' : 'pending';
        
        $docStatus = $driver['DocumentStatus'] ?? 'Pending';
        $rejectionReason = $driver['RejectionReason'] ?? '';
        
        if ($docStatus == 'Approved') {
            $docStatusClass = 'approved';
            $approvedCount++;
        } elseif ($docStatus == 'Rejected') {
            $docStatusClass = 'rejected';
            $rejectedCount++;
        } else {
            $docStatusClass = 'pending';
            $pendingCount++;
        }
        
        $accountStatus = $driver['AccountStatus'] ?? 'Pending';
        if ($accountStatus == 'Active') $activeCount++;
        elseif ($accountStatus == 'Suspended') $suspendedCount++;
        elseif ($accountStatus == 'Deactivated') $deactivatedCount++;
        
        $documents = $driver['Documents'] ?? [];
        $cleanDocuments = [];
        if (is_array($documents)) {
            foreach ($documents as $key => $value) {
                $cleanDocuments[$key] = $value;
            }
        }
        
        $profileImageUrl = $driver['ProfileImageUrl'] ?? '';
        
        // ✅ TRICYCLE DETAILS - WITH DEFAULT VALUES
        $tricycleDetails = [
            'plate_number' => !empty($driver['PlateNumber']) ? $driver['PlateNumber'] : ($driver['VehiclePlateNumber'] ?? 'Not Specified'),
            'model' => !empty($driver['VehicleModel']) ? $driver['VehicleModel'] : 'Not Specified',
            'color' => !empty($driver['VehicleColor']) ? $driver['VehicleColor'] : 'Not Specified',
            'or_cr_number' => !empty($driver['ORCRNumber']) ? $driver['ORCRNumber'] : 'Not Specified',
            'license_number' => !empty($driver['LicenseNumber']) ? $driver['LicenseNumber'] : 'Not Specified',
            'expiry_date' => !empty($driver['LicenseExpiry']) ? $driver['LicenseExpiry'] : 'Not Specified',
            'body_type' => !empty($driver['BodyType']) ? $driver['BodyType'] : 'Not Specified',
            'passenger_capacity' => !empty($driver['PassengerCapacity']) ? $driver['PassengerCapacity'] : 'Not Specified',
            'year_manufactured' => !empty($driver['YearManufactured']) ? $driver['YearManufactured'] : 'Not Specified'
        ];
        
        $suspendedUntil = '';
        if (!empty($driver['SuspendedUntil'])) {
            try {
                $suspendedUntil = date('M j, Y', strtotime($driver['SuspendedUntil']));
            } catch (Exception $e) {
                $suspendedUntil = $driver['SuspendedUntil'];
            }
        }
        
        $deactivationReason = $driver['DeactivationReason'] ?? '';
        
        $driverApplications[] = [
            'driver_id' => $driverId,
            'name' => $fullName,
            'initials' => $initials,
            'address' => $address,
            'application_date' => $appDate,
            'status' => $status,
            'status_class' => $statusClass,
            'doc_status' => $docStatus,
            'doc_status_class' => $docStatusClass,
            'rejection_reason' => $rejectionReason,
            'email' => !empty($driver['Email']) ? $driver['Email'] : 'Not Specified',
            'phone' => !empty($driver['MobileNumber']) ? $driver['MobileNumber'] : 'Not Specified',
            'vehicle_type' => $driver['VehicleType'] ?? 'Tricycle',
            'license_number' => $driver['LicenseNumber'] ?? 'Not Specified',
            'documents' => $cleanDocuments,
            'document_count' => count($cleanDocuments),
            'registration_completed' => $driver['RegistrationCompleted'] ?? false,
            'gcash_qr' => $driver['GcashQRUrl'] ?? ($cleanDocuments['GCash_QR_Code'] ?? null),
            'profile_image' => $profileImageUrl,
            'total_rides' => $driver['TotalCompletedRides'] ?? 0,
            'rating' => $driver['Rating'] ?? 5.0,
            'joined_date' => !empty($driver['RegistrationDate']) ? 
                date('M j, Y', strtotime($driver['RegistrationDate'])) : 
                (isset($driver['CreatedAt']) ? date('M j, Y', strtotime($driver['CreatedAt'])) : 'Unknown'),
            'tricycle' => $tricycleDetails
        ];
        
        if (!empty($driver['RegistrationCompleted']) && $driver['RegistrationCompleted'] === true) {
            $driverMonitoring[] = [
                'driver_id' => $driverId,
                'name' => $fullName,
                'initials' => $initials,
                'barangay' => $driver['Barangay'] ?? 'Not Specified',
                'status' => $status,
                'status_class' => $status == 'Online' ? 'active' : 'pending',
                'account_status' => $driver['AccountStatus'] ?? 'Active',
                'account_status_class' => strtolower($driver['AccountStatus'] ?? 'active'),
                'email' => !empty($driver['Email']) ? $driver['Email'] : 'Not Specified',
                'phone' => !empty($driver['MobileNumber']) ? $driver['MobileNumber'] : 'Not Specified',
                'total_trips' => $driver['TotalCompletedRides'] ?? 0,
                'rating' => number_format($driver['Rating'] ?? 5.0, 1),
                'joined_date' => !empty($driver['RegistrationDate']) ? 
                    date('M j, Y', strtotime($driver['RegistrationDate'])) : 
                    (isset($driver['CreatedAt']) ? date('M j, Y', strtotime($driver['CreatedAt'])) : 'Unknown'),
                'profile_image' => $profileImageUrl,
                'suspended_until' => $suspendedUntil,
                'deactivation_reason' => $deactivationReason,
                'tricycle' => $tricycleDetails
            ];
        }
    }
}

usort($driverApplications, function($a, $b) {
    return strtotime($b['application_date']) - strtotime($a['application_date']);
});

$totalApplicants = count($driverApplications);
$totalRegisteredDrivers = count($driverMonitoring);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceCo - Driver Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f8f9fa; color: #333; overflow-x: hidden; }
        
        .user-management-content { 
            margin-top: 70px; 
            padding: 25px; 
            margin-left: 240px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            width: calc(100% - 240px); 
        }
        
        .sidebar.collapsed ~ .user-management-content,
        .sidebar.auto-hide ~ .user-management-content { 
            margin-left: 70px; 
            width: calc(100% - 70px); 
        }
        
        .sidebar.auto-hide:hover ~ .user-management-content { 
            margin-left: 240px !important; 
            width: calc(100% - 240px) !important; 
        }
        
        .welcome-section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .welcome-title { font-size: 22px; font-weight: 600; color: #333; margin-bottom: 5px; }
        .welcome-subtitle { color: #666; font-size: 14px; }
        
        .action-result { margin-bottom: 20px; padding: 15px 20px; border-radius: 8px; }
        .action-result.success { background-color: #d1fae5; color: #347433; border-left: 4px solid #347433; }
        .action-result.error { background-color: #fee2e2; color: #ef4444; border-left: 4px solid #ef4444; }
        
        .tab-buttons-container { display: flex; gap: 15px; margin-bottom: 20px; }
        .tab-button { 
            padding: 15px 25px; 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 15px; 
            font-weight: 500; 
            color: #666; 
            cursor: pointer; 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            transition: all 0.2s;
        }
        .tab-button:hover { border-color: #347433; color: #347433; }
        .tab-button.active { background-color: #347433; color: white; border-color: #347433; }
        .tab-content-container { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; overflow: hidden; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px; }
        .stat-card { background: white; border-radius: 8px; padding: 15px; border-left: 4px solid #347433; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.approved { border-left-color: #347433; }
        .stat-card.rejected { border-left-color: #ef4444; }
        .stat-card.active { border-left-color: #347433; }
        .stat-card.suspended { border-left-color: #f59e0b; }
        .stat-card.deactivated { border-left-color: #6b7280; }
        .stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .stat-title { font-size: 13px; color: #666; font-weight: 500; }
        .stat-icon { width: 35px; height: 35px; border-radius: 6px; display: flex; align-items: center; justify-content: center; background-color: #f0fdf4; color: #347433; }
        .stat-card.pending .stat-icon { background-color: #fef3c7; color: #f59e0b; }
        .stat-card.rejected .stat-icon { background-color: #fee2e2; color: #ef4444; }
        .stat-card.suspended .stat-icon { background-color: #fef3c7; color: #f59e0b; }
        .stat-card.deactivated .stat-icon { background-color: #e5e7eb; color: #6b7280; }
        .stat-value { font-size: 24px; font-weight: 600; color: #333; margin-bottom: 5px; }
        .stat-change { font-size: 12px; color: #666; }
        
        .table-container { margin: 20px; background: white; border-radius: 8px; overflow: auto; }
        
        .search-filter-container { 
            padding: 15px; 
            border-bottom: 1px solid #eee; 
            display: flex; 
            gap: 10px; 
            align-items: center; 
            flex-wrap: wrap;
        }
        
        .search-box { 
            flex: 1; 
            position: relative; 
            min-width: 250px;
        }
        
        .search-input { 
            width: 100%; 
            padding: 10px 15px 10px 40px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 14px; 
        }
        
        .search-input:focus { 
            outline: none; 
            border-color: #347433; 
        }
        
        .search-icon { 
            position: absolute; 
            left: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #999; 
        }
        
        .filter-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 10px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            color: #666;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .filter-btn:hover {
            border-color: #347433;
            color: #347433;
            background-color: #f0fdf4;
        }
        
        .filter-btn.active {
            background-color: #347433;
            color: white;
            border-color: #347433;
        }
        
        .filter-btn i {
            font-size: 12px;
        }
        
        .data-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        .data-table thead { background-color: #f9fafb; border-bottom: 2px solid #eee; }
        .data-table th { padding: 12px 15px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; }
        .profile-circle { width: 35px; height: 35px; border-radius: 50%; background-color: #347433; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; margin-right: 10px; float: left; }
        .name-cell { display: flex; align-items: center; }
        .data-table tbody tr { border-bottom: 1px solid #eee; }
        .data-table tbody tr:hover { background-color: #f9f9f9; }
        .data-table td { padding: 12px 15px; color: #4b5563; font-size: 14px; vertical-align: middle; }
        
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-badge.pending { background-color: #fef3c7; color: #f59e0b; }
        .status-badge.active { background-color: #d1fae5; color: #347433; }
        .status-badge.approved { background-color: #d1fae5; color: #347433; }
        .status-badge.rejected { background-color: #fee2e2; color: #ef4444; }
        .status-badge.offline { background-color: #e5e7eb; color: #6b7280; }
        .status-badge.suspended { background-color: #fef3c7; color: #f59e0b; }
        .status-badge.deactivated { background-color: #e5e7eb; color: #6b7280; }
        
        .action-buttons { display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }
        .action-btn { 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 500; 
            cursor: pointer; 
            border: none; 
            white-space: nowrap; 
            display: inline-flex; 
            align-items: center; 
            gap: 5px; 
            transition: all 0.2s; 
        }
        .action-btn.view { background-color: #3b82f6; color: white; }
        .action-btn.view:hover { background-color: #2563eb; }
        .action-btn.approve { background-color: #347433; color: white; }
        .action-btn.approve:hover { background-color: #2d6a2c; }
        .action-btn.reject { background-color: #ef4444; color: white; }
        .action-btn.reject:hover { background-color: #dc2626; }
        .action-btn.deactivate { background-color: #6b7280; color: white; }
        .action-btn.deactivate:hover { background-color: #4b5563; }
        .action-btn.suspend { background-color: #f59e0b; color: white; }
        .action-btn.suspend:hover { background-color: #d97706; }
        .action-btn.reactivate { background-color: #347433; color: white; }
        .action-btn.reactivate:hover { background-color: #2d6a2c; }
        .action-btn.edit { background-color: #f59e0b; color: white; }
        .action-btn.edit:hover { background-color: #d97706; }
        .action-btn.save { background-color: #347433; color: white; }
        .action-btn.save:hover { background-color: #2d6a2c; }
        .action-btn.cancel { background-color: #6b7280; color: white; }
        .action-btn.cancel:hover { background-color: #4b5563; }
        
        .pagination { padding: 15px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; }
        .pagination-info { font-size: 13px; color: #666; }
        .footer { text-align: center; padding: 15px; color: #888; font-size: 12px; margin-top: 20px; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1100; justify-content: center; align-items: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; width: 90%; max-width: 900px; border-radius: 12px; overflow: hidden; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .modal-header { padding: 18px 25px; background: #347433; color: white; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; font-weight: 600; font-size: 20px; }
        .modal-close { background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .modal-close:hover { background: rgba(255,255,255,0.3); }
        .modal-body { padding: 25px; flex: 1; overflow-y: auto; }
        .modal-footer { padding: 20px 25px; background-color: #f9fafb; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb; }
        
        .driver-info { display: flex; align-items: center; gap: 25px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .profile-pic-circle { width: 100px; height: 100px; border-radius: 50%; background: #347433; display: flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: bold; flex-shrink: 0; overflow: hidden; }
        .profile-pic-circle img { width: 100%; height: 100%; object-fit: cover; }
        .driver-details { flex: 1; }
        .driver-details h4 { font-size: 24px; margin-bottom: 8px; color: #333; font-weight: 600; }
        .driver-details p { color: #666; font-size: 15px; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .driver-details i { width: 20px; color: #347433; }
        
        .details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .detail-card { background-color: #f8f9fa; border-radius: 8px; padding: 18px; border-left: 4px solid #347433; }
        .detail-card h5 { font-size: 16px; margin-bottom: 10px; color: #333; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .detail-card p { color: #555; font-size: 14px; margin-bottom: 5px; }
        
        /* ✅ TRICYCLE DETAILS CARD - EDITABLE WITH VALIDATION */
        .tricycle-details { margin-top: 20px; }
        .tricycle-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            gap: 15px; 
            margin-top: 15px; 
        }
        .tricycle-item { 
            background: white; 
            padding: 12px 15px; 
            border-radius: 6px; 
            border: 1px solid #e5e7eb; 
        }
        .tricycle-label { 
            font-size: 12px; 
            color: #6b7280; 
            margin-bottom: 5px; 
            font-weight: 500;
        }
        .tricycle-value { 
            font-size: 16px; 
            font-weight: 600; 
            color: #333; 
            word-break: break-word;
        }
        .tricycle-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #347433;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 5px;
        }
        .tricycle-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 116, 51, 0.2);
        }
        .tricycle-input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        .edit-mode {
            background-color: #fff9e6;
            border: 2px solid #f59e0b;
        }
        .not-specified {
            color: #999;
            font-style: italic;
        }
        
        /* ✅ VALIDATION ERROR STYLES */
        .validation-error {
            color: #ef4444;
            font-size: 11px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .validation-error i {
            font-size: 10px;
        }
        
        .documents-section { margin-top: 30px; }
        .documents-section h4 { font-size: 18px; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid #eee; }
        .documents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .document-item { background: white; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .document-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .document-icon { width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background-color: #f0fdf4; color: #347433; font-size: 22px; }
        .document-name { font-weight: 600; color: #333; font-size: 16px; }
        .preview-btn { width: 100%; padding: 10px; background: #347433; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .preview-btn:hover { background: #2d6a2c; }
        .preview-image { width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px; }
        
        .btn { padding: 10px 22px; border-radius: 6px; font-weight: 500; cursor: pointer; border: none; font-size: 15px; transition: all 0.2s; }
        .btn-primary { background: #347433; color: white; }
        .btn-primary:hover { background: #2d6a2c; transform: translateY(-2px); }
        .btn-secondary { background-color: #e5e7eb; color: #4b5563; }
        .btn-secondary:hover { background-color: #d1d5db; transform: translateY(-2px); }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; transform: translateY(-2px); }
        .btn-warning { background-color: #f59e0b; color: white; }
        .btn-warning:hover { background-color: #d97706; transform: translateY(-2px); }
        
        .rejection-reason { background: #fee2e2; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #ef4444; }
        .rejection-reason h5 { color: #ef4444; margin-bottom: 8px; }
        
        .reason-input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; margin-bottom: 5px; }
        .reason-input:focus { outline: none; border-color: #347433; }
        
        .error-message-text { 
            color: #ef4444; 
            font-size: 12px; 
            margin-top: 5px; 
            margin-bottom: 10px;
            display: none;
            align-items: center;
            gap: 5px;
        }
        .error-message-text i { font-size: 12px; }
        
        .date-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .date-input:focus {
            outline: none;
            border-color: #347433;
        }
        
        .success-message, .error-message-toast { 
            display: none; 
            position: fixed; 
            top: 90px; 
            right: 25px; 
            color: white; 
            padding: 15px 25px; 
            border-radius: 8px; 
            z-index: 1000; 
            animation: slideInRight 0.3s ease; 
        }
        .success-message { background-color: #347433; }
        .error-message-toast { background-color: #ef4444; }
        
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        
        @media (max-width: 768px) { 
            .user-management-content { margin-left: 70px; width: calc(100% - 70px); padding: 15px; } 
            .tab-buttons-container { flex-direction: column; } 
            .documents-grid { grid-template-columns: 1fr; } 
            .driver-info { flex-direction: column; text-align: center; } 
            .search-filter-container { flex-direction: column; }
            .search-box { width: 100%; }
            .filter-buttons { width: 100%; justify-content: flex-start; }
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <div class="user-management-content">
        <div class="welcome-section">
            <h1 class="welcome-title">Driver Application Management</h1>
            <p class="welcome-subtitle">Document Status • <?php echo $totalApplicants; ?> total applicants</p>
        </div>

        <?php if ($actionResult): ?>
        <div class="action-result <?php echo $actionResult['success'] ? 'success' : 'error'; ?>">
            <i class="fas <?php echo $actionResult['success'] ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($actionResult['message']); ?>
        </div>
        <?php endif; ?>

        <div class="tab-buttons-container">
            <button class="tab-button <?php echo $activeTab == 'applicationTab' ? 'active' : ''; ?>" onclick="openTab('applicationTab', this)">
                <i class="fas fa-file-alt"></i> Applications (<?php echo $totalApplicants; ?>)
            </button>
            <button class="tab-button <?php echo $activeTab == 'monitoringTab' ? 'active' : ''; ?>" onclick="openTab('monitoringTab', this)">
                <i class="fas fa-users"></i> Driver Management (<?php echo $totalRegisteredDrivers; ?>)
            </button>
        </div>

        <div class="tab-content-container">
            <!-- ✅ DRIVER APPLICATIONS TAB -->
            <div id="applicationTab" class="tab-content <?php echo $activeTab == 'applicationTab' ? 'active' : ''; ?>">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Applicants</div>
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $totalApplicants; ?></div>
                        <div class="stat-change">From Firebase</div>
                    </div>
                    <div class="stat-card pending">
                        <div class="stat-header">
                            <div class="stat-title">Pending Review</div>
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $pendingCount; ?></div>
                        <div class="stat-change">Awaiting approval</div>
                    </div>
                    <div class="stat-card approved">
                        <div class="stat-header">
                            <div class="stat-title">Approved</div>
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $approvedCount; ?></div>
                        <div class="stat-change">Ready to drive</div>
                    </div>
                    <div class="stat-card rejected">
                        <div class="stat-header">
                            <div class="stat-title">Rejected</div>
                            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $rejectedCount; ?></div>
                        <div class="stat-change">Needs review</div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="search-filter-container">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="applicationSearch" placeholder="Search by name, address, or ID...">
                        </div>
                        
                        <div class="filter-buttons">
                            <button class="filter-btn active" onclick="filterApplications('all', this)">
                                <i class="fas fa-list"></i> All
                            </button>
                            <button class="filter-btn" onclick="filterApplications('pending', this)">
                                <i class="fas fa-clock"></i> Pending
                            </button>
                            <button class="filter-btn" onclick="filterApplications('approved', this)">
                                <i class="fas fa-check-circle"></i> Approved
                            </button>
                            <button class="filter-btn" onclick="filterApplications('rejected', this)">
                                <i class="fas fa-times-circle"></i> Rejected
                            </button>
                        </div>
                    </div>

                    <table class="data-table" id="applicationTable">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Address</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th>Document Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($driverApplications)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 50px;">No driver applications found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($driverApplications as $index => $app): ?>
                                <tr data-doc-status="<?php echo strtolower($app['doc_status']); ?>">
                                    <td>
                                        <div class="name-cell">
                                            <div class="profile-circle"><?php echo htmlspecialchars($app['initials']); ?></div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($app['name']); ?></strong>
                                                <br><small style="color: #666;"><?php echo htmlspecialchars($app['driver_id']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($app['address']); ?></td>
                                    <td><?php echo $app['application_date']; ?></td>
                                    <td><span class="status-badge <?php echo $app['status_class']; ?>"><?php echo $app['status']; ?></span></td>
                                    <td>
                                        <span class="status-badge <?php echo $app['doc_status_class']; ?>">
                                            <?php echo $app['doc_status']; ?>
                                        </span>
                                        <?php if (!empty($app['rejection_reason'])): ?>
                                            <br><small style="color: #ef4444;">Reason: <?php echo htmlspecialchars(substr($app['rejection_reason'], 0, 30)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick='showApplicationDetails(<?php echo json_encode($app, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <?php if ($app['doc_status'] != 'Approved'): ?>
                                            <button class="action-btn approve" onclick='approveApplication("<?php echo htmlspecialchars($app['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($app['name'], ENT_QUOTES); ?>")'>
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <?php endif; ?>
                                            <?php if ($app['doc_status'] != 'Rejected' && $app['doc_status'] != 'Approved'): ?>
                                            <button class="action-btn reject" onclick='showRejectModal("<?php echo htmlspecialchars($app['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($app['name'], ENT_QUOTES); ?>")'>
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <div class="pagination-info">Showing <?php echo count($driverApplications); ?> applicants from Firebase</div>
                    </div>
                </div>
            </div>

            <!-- ✅ ACTIVE DRIVERS TAB -->
            <div id="monitoringTab" class="tab-content <?php echo $activeTab == 'monitoringTab' ? 'active' : ''; ?>">
                <div class="stats-grid">
                    <div class="stat-card active">
                        <div class="stat-header">
                            <div class="stat-title">Active Drivers</div>
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $activeCount; ?></div>
                        <div class="stat-change">Currently active</div>
                    </div>
                    <div class="stat-card suspended">
                        <div class="stat-header">
                            <div class="stat-title">Suspended</div>
                            <div class="stat-icon"><i class="fas fa-pause-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $suspendedCount; ?></div>
                        <div class="stat-change">Temporarily suspended</div>
                    </div>
                    <div class="stat-card deactivated">
                        <div class="stat-header">
                            <div class="stat-title">Deactivated</div>
                            <div class="stat-icon"><i class="fas fa-ban"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $deactivatedCount; ?></div>
                        <div class="stat-change">Permanently deactivated</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Online Now</div>
                            <div class="stat-icon"><i class="fas fa-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo count(array_filter($driverMonitoring, function($d) { return $d['status'] == 'Online'; })); ?></div>
                        <div class="stat-change">Ready to accept rides</div>
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="search-filter-container">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="monitoringSearch" placeholder="Search by name, ID, barangay, or status...">
                        </div>
                        
                        <div class="filter-buttons">
                            <button class="filter-btn active" onclick="filterDrivers('all', this)">
                                <i class="fas fa-list"></i> All
                            </button>
                            <button class="filter-btn" onclick="filterDrivers('active', this)">
                                <i class="fas fa-check-circle"></i> Active
                            </button>
                            <button class="filter-btn" onclick="filterDrivers('suspended', this)">
                                <i class="fas fa-pause-circle"></i> Suspended
                            </button>
                            <button class="filter-btn" onclick="filterDrivers('deactivated', this)">
                                <i class="fas fa-ban"></i> Deactivated
                            </button>
                            <button class="filter-btn" onclick="filterDrivers('online', this)">
                                <i class="fas fa-circle"></i> Online
                            </button>
                        </div>
                    </div>
                    
                    <table class="data-table" id="monitoringTable">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Barangay</th>
                                <th>Status</th>
                                <th>Account Status</th>
                                <th>Trips</th>
                                <th>Rating</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($driverMonitoring)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 50px;">No registered drivers found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($driverMonitoring as $driver): ?>
                                <tr data-account-status="<?php echo strtolower($driver['account_status']); ?>" data-online-status="<?php echo strtolower($driver['status']); ?>">
                                    <td>
                                        <div class="name-cell">
                                            <div class="profile-circle"><?php echo htmlspecialchars($driver['initials']); ?></div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($driver['name']); ?></strong>
                                                <br><small style="color: #666;"><?php echo htmlspecialchars($driver['driver_id']); ?></small>
                                                <?php if ($driver['account_status'] == 'Suspended' && !empty($driver['suspended_until'])): ?>
                                                    <br><small style="color: #f59e0b;">Until: <?php echo $driver['suspended_until']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($driver['barangay']); ?></td>
                                    <td><span class="status-badge <?php echo $driver['status_class']; ?>"><?php echo $driver['status']; ?></span></td>
                                    <td>
                                        <span class="status-badge <?php echo $driver['account_status_class']; ?>">
                                            <?php echo $driver['account_status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($driver['total_trips']); ?></td>
                                    <td>⭐ <?php echo $driver['rating']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick='showActiveDriverDetails(<?php echo json_encode($driver, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            
                                            <?php if ($driver['account_status'] == 'Active'): ?>
                                                <button class="action-btn suspend" onclick='showSuspendModal("<?php echo htmlspecialchars($driver['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($driver['name'], ENT_QUOTES); ?>")'>
                                                    <i class="fas fa-pause"></i> Suspend
                                                </button>
                                                <button class="action-btn deactivate" onclick='showDeactivateModal("<?php echo htmlspecialchars($driver['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($driver['name'], ENT_QUOTES); ?>")'>
                                                    <i class="fas fa-ban"></i> Deactivate
                                                </button>
                                            <?php elseif ($driver['account_status'] == 'Suspended' || $driver['account_status'] == 'Deactivated'): ?>
                                                <button class="action-btn reactivate" onclick='reactivateDriver("<?php echo htmlspecialchars($driver['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($driver['name'], ENT_QUOTES); ?>")'>
                                                    <i class="fas fa-check"></i> Reactivate
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>ServiceCo • Live Firebase Data • <?php echo date('Y-m-d H:i'); ?></p>
        </div>
    </div>

    <!-- ✅ APPLICATION DETAILS MODAL - DOCUMENTS ONLY -->
    <div class="modal" id="applicationModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Application Details</h3>
                <button class="modal-close" onclick="closeModal('applicationModal')">&times;</button>
            </div>
            <div class="modal-body" id="applicationDetailsContent">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #347433;"></i>
                    <p style="margin-top: 20px;">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('applicationModal')">Close</button>
                <button class="btn btn-primary" id="approveModalBtn" onclick="approveFromModal()">Approve</button>
                <button class="btn btn-danger" id="rejectModalBtn" onclick="showRejectModalFromDetails()">Reject</button>
            </div>
        </div>
    </div>

    <!-- ✅ ACTIVE DRIVER DETAILS MODAL - WITH EDITABLE TRICYCLE AND VALIDATION -->
    <div class="modal" id="activeDriverModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Driver Details</h3>
                <button class="modal-close" onclick="closeModal('activeDriverModal')">&times;</button>
            </div>
            <div class="modal-body" id="activeDriverDetailsContent">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #347433;"></i>
                    <p style="margin-top: 20px;">Loading...</p>
                </div>
            </div>
            <div class="modal-footer" id="activeDriverModalFooter">
                <button class="btn btn-secondary" onclick="closeModal('activeDriverModal')">Close</button>
                <button class="btn btn-warning" id="editTricycleBtn" onclick="enableTricycleEdit()">Edit Tricycle</button>
                <button class="btn btn-primary" id="saveTricycleBtn" style="display: none;" onclick="validateAndSaveTricycle()">Save Changes</button>
                <button class="btn btn-secondary" id="cancelEditBtn" style="display: none;" onclick="cancelTricycleEdit()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- ✅ REJECTION REASON MODAL -->
    <div class="modal" id="rejectModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h4>Reject Application</h4>
                <button class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Please provide reason for rejection:</p>
                <textarea id="rejectionReason" class="reason-input" rows="4" placeholder="e.g., Invalid driver's license, unclear documents, missing requirements..." oninput="validateRejectionReason()"></textarea>
                
                <div id="rejectError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please provide a reason for rejection.
                </div>
                
                <p style="font-size: 12px; color: #666;">Driver will see this reason in their notification.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button class="btn btn-danger" onclick="validateAndSubmitRejection()">Submit Rejection</button>
            </div>
        </div>
    </div>

    <!-- ✅ DEACTIVATE MODAL - WITH REASON -->
    <div class="modal" id="deactivateModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h4>Deactivate Driver</h4>
                <button class="modal-close" onclick="closeDeactivateModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Please provide reason for deactivation:</p>
                <textarea id="deactivationReason" class="reason-input" rows="4" placeholder="e.g., Violation of terms, fraudulent activity, etc..." oninput="validateDeactivationReason()"></textarea>
                
                <div id="deactivateError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please provide a reason for deactivation.
                </div>
                
                <p style="font-size: 12px; color: #666;">This driver will be permanently deactivated.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeactivateModal()">Cancel</button>
                <button class="btn btn-danger" onclick="validateAndSubmitDeactivation()">Deactivate Driver</button>
            </div>
        </div>
    </div>

    <!-- ✅ SUSPEND MODAL - WITH DATE -->
    <div class="modal" id="suspendModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h4>Suspend Driver</h4>
                <button class="modal-close" onclick="closeSuspendModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Select suspension period:</p>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Suspension Duration:</label>
                    <select id="suspendDuration" class="reason-input" onchange="updateSuspendDate()">
                        <option value="1">1 Day</option>
                        <option value="3">3 Days</option>
                        <option value="7" selected>7 Days</option>
                        <option value="14">14 Days</option>
                        <option value="30">30 Days</option>
                        <option value="60">60 Days</option>
                        <option value="90">90 Days</option>
                        <option value="custom">Custom Date</option>
                    </select>
                </div>
                
                <div id="customDateContainer" style="display: none; margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Custom Until Date:</label>
                    <input type="datetime-local" id="customSuspendDate" class="date-input" onchange="validateSuspendDate()">
                </div>
                
                <div id="suspendDateDisplay" style="background-color: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <span style="font-weight: 500;">Suspended until:</span> 
                    <span id="suspendUntilText"><?php echo date('M j, Y', strtotime('+7 days')); ?></span>
                </div>
                
                <div id="suspendError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please select a valid date.
                </div>
                
                <input type="hidden" id="suspendUntilValue" value="<?php echo date('c', strtotime('+7 days')); ?>">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeSuspendModal()">Cancel</button>
                <button class="btn btn-warning" onclick="validateAndSubmitSuspension()">Suspend Driver</button>
            </div>
        </div>
    </div>

    <!-- ✅ DOCUMENT PREVIEW MODAL -->
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
                <a href="#" id="downloadLink" target="_blank" style="text-decoration: none;">
                    <button class="btn btn-primary">Download</button>
                </a>
            </div>
        </div>
    </div>

    <!-- ✅ CONFIRMATION MODAL -->
    <div class="modal" id="confirmationModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h4 id="confirmationTitle">Confirm Action</h4>
                <button class="modal-close" onclick="closeModal('confirmationModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirmationText" style="font-size: 16px; color: #555; margin: 20px 0;">Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('confirmationModal')">Cancel</button>
                <button class="btn btn-primary" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>

    <!-- ✅ HIDDEN FORM -->
    <form id="actionForm" method="POST" style="display: none;">
        <input type="hidden" name="action" id="actionInput">
        <input type="hidden" name="driver_id" id="driverIdInput">
        <input type="hidden" name="driver_name" id="driverNameInput">
        <input type="hidden" name="rejection_reason" id="rejectionReasonInput">
        <input type="hidden" name="deactivation_reason" id="deactivationReasonInput">
        <input type="hidden" name="suspend_until" id="suspendUntilInput">
        <input type="hidden" name="active_tab" id="activeTabInput" value="<?php echo $activeTab; ?>">
        
        <!-- Tricycle Update Fields -->
        <input type="hidden" name="plate_number" id="plateNumberInput">
        <input type="hidden" name="model" id="modelInput">
        <input type="hidden" name="color" id="colorInput">
        <input type="hidden" name="or_cr_number" id="orCrNumberInput">
        <input type="hidden" name="license_number" id="licenseNumberInput">
        <input type="hidden" name="expiry_date" id="expiryDateInput">
        <input type="hidden" name="body_type" id="bodyTypeInput">
        <input type="hidden" name="passenger_capacity" id="passengerCapacityInput">
        <input type="hidden" name="year_manufactured" id="yearManufacturedInput">
    </form>

    <!-- ✅ MESSAGES -->
    <div class="success-message" id="successMessage"></div>
    <div class="error-message-toast" id="errorMessage"></div>

    <script>
        // ========== GLOBAL VARIABLES ==========
        let currentAppData = null;
        let currentDriverData = null;
        let currentDriverId = null;
        let currentDriverName = null;
        let currentAction = null;
        let isEditMode = false;

        // ========== DOCUMENT READY ==========
        document.addEventListener('DOMContentLoaded', function() {
            setupSearch();
            if (window.updateAllContentPositions) {
                window.updateAllContentPositions();
            }
        });

        // ========== TAB FUNCTIONS ==========
        function openTab(tabName, button) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            button.classList.add('active');
            document.getElementById('activeTabInput').value = tabName;
        }

        // ========== SEARCH/FILTER FUNCTIONS ==========
        function setupSearch() {
            const appSearch = document.getElementById('applicationSearch');
            if (appSearch) {
                appSearch.addEventListener('keyup', function() {
                    filterTable('applicationTable', this.value);
                });
            }

            const monSearch = document.getElementById('monitoringSearch');
            if (monSearch) {
                monSearch.addEventListener('keyup', function() {
                    filterTable('monitoringTable', this.value);
                });
            }
        }

        function filterTable(tableId, searchTerm) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            const rows = tbody.getElementsByTagName('tr');
            const term = searchTerm.toLowerCase();
            
            for (let row of rows) {
                let found = false;
                for (let cell of row.cells) {
                    if (cell.textContent.toLowerCase().includes(term)) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? '' : 'none';
            }
        }

        function filterApplications(status, button) {
            const buttons = button.parentElement.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const table = document.getElementById('applicationTable');
            if (!table) return;
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            const rows = tbody.getElementsByTagName('tr');
            
            for (let row of rows) {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    const docStatus = row.getAttribute('data-doc-status');
                    row.style.display = docStatus === status ? '' : 'none';
                }
            }
        }

        function filterDrivers(status, button) {
            const buttons = button.parentElement.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const table = document.getElementById('monitoringTable');
            if (!table) return;
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            const rows = tbody.getElementsByTagName('tr');
            
            for (let row of rows) {
                if (status === 'all') {
                    row.style.display = '';
                } else if (status === 'online') {
                    const onlineStatus = row.getAttribute('data-online-status');
                    row.style.display = onlineStatus === 'online' ? '' : 'none';
                } else {
                    const accountStatus = row.getAttribute('data-account-status');
                    row.style.display = accountStatus === status ? '' : 'none';
                }
            }
        }

        // ========== MODAL FUNCTIONS ==========
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('active');
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.remove('active');
            isEditMode = false;
        }

        // ========== DOCUMENT FUNCTIONS ==========
        function getDocumentIcon(docKey) {
            const icons = {
                '2x2_Picture': 'fas fa-id-card',
                'Cedula': 'fas fa-file-contract',
                'Barangay_Clearance': 'fas fa-file-alt',
                'Driver\'s_License': 'fas fa-id-badge',
                'ORCR': 'fas fa-car',
                'Plate_Number': 'fas fa-image',
                'GCash_QR_Code': 'fas fa-qrcode'
            };
            return icons[docKey] || 'fas fa-file';
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

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatValue(value) {
            if (!value || value === '' || value === 'Not Specified' || value === 'N/A') {
                return '<span class="not-specified">Not Specified</span>';
            }
            return escapeHtml(value);
        }

        // ✅ VALIDATION FUNCTIONS (Same as Mobile App)
        function isValidPlateNumber(plateNumber) {
            if (!plateNumber || plateNumber === 'Not Specified') return false;
            // Philippine plate number formats: ABC 123, ABC-123, ABC123, AB 1234, AB-1234, AB1234
            const pattern = /^[A-Z]{2,3}[-\s]?\d{3,4}$/i;
            return pattern.test(plateNumber.trim());
        }

        function isValidLicenseNumber(licenseNumber) {
            if (!licenseNumber || licenseNumber === 'Not Specified') return false;
            // Philippine license format: L01-23-456789 or similar
            const pattern = /^[A-Z]{1,2}\d{2}-\d{2}-\d{6}$/i;
            return pattern.test(licenseNumber.trim());
        }

        function isValidVehicleNumber(number, fieldType) {
            if (!number || number === 'Not Specified' || number === '') return true; // Optional fields
            // Alphanumeric with spaces and hyphens, 3-20 characters
            const pattern = /^[A-Z0-9\s-]{3,20}$/i;
            return pattern.test(number.trim());
        }

        function isValidYear(year) {
            if (!year || year === 'Not Specified') return false;
            if (year === 'Older') return true;
            const currentYear = new Date().getFullYear();
            const yearNum = parseInt(year);
            return yearNum >= 1990 && yearNum <= currentYear + 1;
        }

        function isNotExpired(expiryDate) {
            if (!expiryDate || expiryDate === 'Not Specified') return true; // Optional field
            try {
                const expiry = new Date(expiryDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                return expiry >= today;
            } catch (e) {
                return false;
            }
        }

        function formatPlateNumber(plateNumber) {
            if (!plateNumber || plateNumber === 'Not Specified') return '';
            // Remove spaces and dashes, then format as ABC 123
            const clean = plateNumber.replace(/[\s-]/g, '');
            if (clean.length >= 3) {
                const letters = clean.substring(0, Math.min(3, clean.length));
                const numbers = clean.substring(letters.length);
                return `${letters} ${numbers}`.toUpperCase();
            }
            return plateNumber.toUpperCase();
        }

        // ========== SHOW APPLICATION DETAILS ==========
        function showApplicationDetails(application) {
            currentAppData = application;
            
            const displayNames = {
                '2x2_Picture': '2x2 Picture',
                'Cedula': 'Cedula',
                'Barangay_Clearance': 'Barangay Clearance',
                'Driver\'s_License': 'Driver\'s License',
                'ORCR': 'OR/CR',
                'Plate_Number': 'Plate Number',
                'GCash_QR_Code': 'GCash QR Code'
            };
            
            let documentsGrid = '';
            const docOrder = ['2x2_Picture', 'Cedula', 'Barangay_Clearance', 'Driver\'s_License', 'ORCR', 'Plate_Number', 'GCash_QR_Code'];
            
            if (application.documents && Object.keys(application.documents).length > 0) {
                docOrder.forEach(docKey => {
                    if (application.documents[docKey]) {
                        const docUrl = application.documents[docKey];
                        const displayName = displayNames[docKey] || docKey;
                        
                        documentsGrid += `
                            <div class="document-item">
                                <div class="document-header">
                                    <div class="document-icon"><i class="${getDocumentIcon(docKey)}"></i></div>
                                    <div class="document-name">${escapeHtml(displayName)}</div>
                                </div>
                                <button class="preview-btn" onclick='previewDocument("${escapeHtml(displayName)}", "${docUrl}")'>
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        `;
                    }
                });
            }

            if (!documentsGrid) {
                documentsGrid = '<div style="text-align: center; padding: 40px; color: #666;">No documents uploaded yet.</div>';
            }

            let rejectionHtml = '';
            if (application.rejection_reason) {
                rejectionHtml = `
                    <div class="rejection-reason">
                        <h5><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h5>
                        <p>${escapeHtml(application.rejection_reason)}</p>
                    </div>
                `;
            }

            const content = `
                <div class="driver-info">
                    <div class="profile-pic-circle">
                        ${application.profile_image ? `<img src="${application.profile_image}" alt="Profile">` : application.initials || 'DR'}
                    </div>
                    <div class="driver-details">
                        <h4>${escapeHtml(application.name)}</h4>
                        <p><i class="fas fa-id-card"></i> ${escapeHtml(application.driver_id)}</p>
                        <p><i class="fas fa-map-marker-alt"></i> ${formatValue(application.address)}</p>
                        <p><i class="fas fa-envelope"></i> ${formatValue(application.email)}</p>
                        <p><i class="fas fa-phone"></i> ${formatValue(application.phone)}</p>
                    </div>
                </div>
                
                <div class="details-grid">
                    <div class="detail-card">
                        <h5><i class="fas fa-info-circle"></i> Application Status</h5>
                        <p><strong>Applied:</strong> ${escapeHtml(application.application_date)}</p>
                        <p><strong>Document Status:</strong> <span class="status-badge ${application.doc_status_class}">${escapeHtml(application.doc_status)}</span></p>
                        <p><strong>Vehicle:</strong> ${escapeHtml(application.vehicle_type)}</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-file"></i> Documents</h5>
                        <p><strong>Uploaded:</strong> ${application.document_count}/7 files</p>
                        <p><strong>Registration:</strong> ${application.registration_completed ? '✅ Complete' : '⏳ Pending'}</p>
                    </div>
                </div>
                
                ${rejectionHtml}
                
                <div class="documents-section">
                    <h4>Uploaded Documents (${application.document_count}/7)</h4>
                    <div class="documents-grid">${documentsGrid}</div>
                </div>
            `;
            
            document.getElementById('applicationDetailsContent').innerHTML = content;
            
            const approveBtn = document.getElementById('approveModalBtn');
            const rejectBtn = document.getElementById('rejectModalBtn');
            
            if (approveBtn) {
                approveBtn.style.display = application.doc_status != 'Approved' ? 'inline-block' : 'none';
            }
            if (rejectBtn) {
                rejectBtn.style.display = (application.doc_status != 'Rejected' && application.doc_status != 'Approved') ? 'inline-block' : 'none';
            }
            
            showModal('applicationModal');
        }

        // ========== SHOW ACTIVE DRIVER DETAILS - WITH EDITABLE TRICYCLE AND VALIDATION ==========
        function showActiveDriverDetails(driver) {
            currentDriverData = driver;
            currentDriverId = driver.driver_id;
            currentDriverName = driver.name;
            isEditMode = false;
            
            let suspendedInfo = '';
            if (driver.account_status == 'Suspended' && driver.suspended_until) {
                suspendedInfo = `<p><strong>Suspended Until:</strong> ${escapeHtml(driver.suspended_until)}</p>`;
            }
            
            let deactivationInfo = '';
            if (driver.account_status == 'Deactivated' && driver.deactivation_reason) {
                deactivationInfo = `<p><strong>Deactivation Reason:</strong> ${escapeHtml(driver.deactivation_reason)}</p>`;
            }

            let accountStatusBadge = `<span class="status-badge ${driver.account_status_class}">${escapeHtml(driver.account_status)}</span>`;

            // ✅ TRICYCLE DETAILS HTML - READ ONLY MODE
            let tricycleHtml = '';
            if (driver.tricycle) {
                tricycleHtml = `
                    <div class="tricycle-details">
                        <h5 style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                            <i class="fas fa-motorcycle" style="color: #347433;"></i> Tricycle Information
                            <span style="margin-left: auto; font-size: 13px; font-weight: normal; color: #666;">
                                <i class="fas fa-info-circle"></i> Click "Edit Tricycle" to update
                            </span>
                        </h5>
                        <div class="tricycle-grid" id="tricycleGrid">
                            <div class="tricycle-item">
                                <div class="tricycle-label">Plate Number</div>
                                <div class="tricycle-value" id="plateNumberDisplay">${formatValue(driver.tricycle.plate_number)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">Model</div>
                                <div class="tricycle-value" id="modelDisplay">${formatValue(driver.tricycle.model)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">Color</div>
                                <div class="tricycle-value" id="colorDisplay">${formatValue(driver.tricycle.color)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">Body Type</div>
                                <div class="tricycle-value" id="bodyTypeDisplay">${formatValue(driver.tricycle.body_type)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">Passenger Capacity</div>
                                <div class="tricycle-value" id="passengerCapacityDisplay">${formatValue(driver.tricycle.passenger_capacity)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">Year Manufactured</div>
                                <div class="tricycle-value" id="yearManufacturedDisplay">${formatValue(driver.tricycle.year_manufactured)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">OR/CR Number</div>
                                <div class="tricycle-value" id="orCrNumberDisplay">${formatValue(driver.tricycle.or_cr_number)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">License Number</div>
                                <div class="tricycle-value" id="licenseNumberDisplay">${formatValue(driver.tricycle.license_number)}</div>
                            </div>
                            <div class="tricycle-item">
                                <div class="tricycle-label">License Expiry</div>
                                <div class="tricycle-value" id="expiryDateDisplay">${formatValue(driver.tricycle.expiry_date)}</div>
                            </div>
                        </div>
                    </div>
                `;
            }

            const content = `
                <div class="driver-info">
                    <div class="profile-pic-circle">
                        ${driver.profile_image ? `<img src="${driver.profile_image}" alt="Profile">` : driver.initials || 'DR'}
                    </div>
                    <div class="driver-details">
                        <h4>${escapeHtml(driver.name)}</h4>
                        <p><i class="fas fa-id-card"></i> ${escapeHtml(driver.driver_id)}</p>
                        <p><i class="fas fa-map-marker-alt"></i> ${formatValue(driver.barangay)}</p>
                        <p><i class="fas fa-envelope"></i> ${formatValue(driver.email)}</p>
                        <p><i class="fas fa-phone"></i> ${formatValue(driver.phone)}</p>
                        <p><i class="fas fa-calendar-alt"></i> Joined ${escapeHtml(driver.joined_date)}</p>
                        <p><strong>Account Status:</strong> ${accountStatusBadge}</p>
                        ${suspendedInfo}
                        ${deactivationInfo}
                    </div>
                </div>
                
                <div class="details-grid">
                    <div class="detail-card">
                        <h5><i class="fas fa-info-circle"></i> Driver Information</h5>
                        <p><strong>Status:</strong> <span class="status-badge ${escapeHtml(driver.status_class)}">${escapeHtml(driver.status)}</span></p>
                        <p><strong>Total Trips:</strong> ${driver.total_trips}</p>
                        <p><strong>Rating:</strong> ⭐ ${driver.rating}</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-address-book"></i> Contact</h5>
                        <p><strong>Email:</strong> ${formatValue(driver.email)}</p>
                        <p><strong>Phone:</strong> ${formatValue(driver.phone)}</p>
                    </div>
                </div>
                
                ${tricycleHtml}
            `;
            
            document.getElementById('activeDriverDetailsContent').innerHTML = content;
            
            // Show/hide footer buttons
            document.getElementById('editTricycleBtn').style.display = 'inline-block';
            document.getElementById('saveTricycleBtn').style.display = 'none';
            document.getElementById('cancelEditBtn').style.display = 'none';
            
            showModal('activeDriverModal');
        }

        // ========== EDIT TRICYCLE FUNCTIONS ==========
        function enableTricycleEdit() {
            if (!currentDriverData || !currentDriverData.tricycle) return;
            
            isEditMode = true;
            
            // Hide edit button, show save and cancel
            document.getElementById('editTricycleBtn').style.display = 'none';
            document.getElementById('saveTricycleBtn').style.display = 'inline-block';
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            
            const tricycle = currentDriverData.tricycle;
            
            // Replace display values with input fields
            const plateNumberDisplay = document.getElementById('plateNumberDisplay');
            if (plateNumberDisplay) {
                const currentValue = tricycle.plate_number === 'Not Specified' ? '' : tricycle.plate_number;
                plateNumberDisplay.outerHTML = `<input type="text" id="plateNumberInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter plate number (e.g., ABC 123)" oninput="validatePlateNumber()">`;
            }
            
            const modelDisplay = document.getElementById('modelDisplay');
            if (modelDisplay) {
                const currentValue = tricycle.model === 'Not Specified' ? '' : tricycle.model;
                modelDisplay.outerHTML = `<input type="text" id="modelInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter model">`;
            }
            
            const colorDisplay = document.getElementById('colorDisplay');
            if (colorDisplay) {
                const currentValue = tricycle.color === 'Not Specified' ? '' : tricycle.color;
                colorDisplay.outerHTML = `<input type="text" id="colorInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter color">`;
            }
            
            const bodyTypeDisplay = document.getElementById('bodyTypeDisplay');
            if (bodyTypeDisplay) {
                const currentValue = tricycle.body_type === 'Not Specified' ? '' : tricycle.body_type;
                bodyTypeDisplay.outerHTML = `<input type="text" id="bodyTypeInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter body type">`;
            }
            
            const passengerCapacityDisplay = document.getElementById('passengerCapacityDisplay');
            if (passengerCapacityDisplay) {
                const currentValue = tricycle.passenger_capacity === 'Not Specified' ? '' : tricycle.passenger_capacity;
                passengerCapacityDisplay.outerHTML = `<input type="text" id="passengerCapacityInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter passenger capacity">`;
            }
            
            const yearManufacturedDisplay = document.getElementById('yearManufacturedDisplay');
            if (yearManufacturedDisplay) {
                const currentValue = tricycle.year_manufactured === 'Not Specified' ? '' : tricycle.year_manufactured;
                yearManufacturedDisplay.outerHTML = `<input type="text" id="yearManufacturedInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter year (e.g., 2020)">`;
            }
            
            const orCrNumberDisplay = document.getElementById('orCrNumberDisplay');
            if (orCrNumberDisplay) {
                const currentValue = tricycle.or_cr_number === 'Not Specified' ? '' : tricycle.or_cr_number;
                orCrNumberDisplay.outerHTML = `<input type="text" id="orCrNumberInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter OR/CR number">`;
            }
            
            const licenseNumberDisplay = document.getElementById('licenseNumberDisplay');
            if (licenseNumberDisplay) {
                const currentValue = tricycle.license_number === 'Not Specified' ? '' : tricycle.license_number;
                licenseNumberDisplay.outerHTML = `<input type="text" id="licenseNumberInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter license number (e.g., L01-23-456789)" oninput="validateLicenseNumber()">`;
            }
            
            const expiryDateDisplay = document.getElementById('expiryDateDisplay');
            if (expiryDateDisplay) {
                const currentValue = tricycle.expiry_date === 'Not Specified' ? '' : tricycle.expiry_date;
                expiryDateDisplay.outerHTML = `<input type="date" id="expiryDateInput" class="tricycle-input" value="${escapeHtml(currentValue)}" placeholder="Enter expiry date" onchange="validateExpiryDate()">`;
            }
        }

        // ✅ VALIDATION FUNCTIONS FOR EDIT MODE
        function validatePlateNumber() {
            const input = document.getElementById('plateNumberInput');
            if (!input) return false;
            
            const value = input.value.trim();
            let errorDiv = document.getElementById('plateNumberError');
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'plateNumberError';
                errorDiv.className = 'validation-error';
                input.parentNode.appendChild(errorDiv);
            }
            
            if (!value) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Plate number is required';
                errorDiv.style.display = 'flex';
                return false;
            } else if (!isValidPlateNumber(value)) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Invalid format. Use: ABC 123, ABC-123, or ABC123';
                errorDiv.style.display = 'flex';
                return false;
            } else {
                input.classList.remove('error');
                errorDiv.style.display = 'none';
                return true;
            }
        }

        function validateLicenseNumber() {
            const input = document.getElementById('licenseNumberInput');
            if (!input) return false;
            
            const value = input.value.trim();
            let errorDiv = document.getElementById('licenseNumberError');
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'licenseNumberError';
                errorDiv.className = 'validation-error';
                input.parentNode.appendChild(errorDiv);
            }
            
            if (!value) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> License number is required';
                errorDiv.style.display = 'flex';
                return false;
            } else if (!isValidLicenseNumber(value)) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Invalid format. Use: L01-23-456789';
                errorDiv.style.display = 'flex';
                return false;
            } else {
                input.classList.remove('error');
                errorDiv.style.display = 'none';
                return true;
            }
        }

        function validateExpiryDate() {
            const input = document.getElementById('expiryDateInput');
            if (!input) return true; // Optional field
            
            const value = input.value;
            let errorDiv = document.getElementById('expiryDateError');
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'expiryDateError';
                errorDiv.className = 'validation-error';
                input.parentNode.appendChild(errorDiv);
            }
            
            if (value && !isNotExpired(value)) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> License is expired';
                errorDiv.style.display = 'flex';
                return false;
            } else {
                input.classList.remove('error');
                errorDiv.style.display = 'none';
                return true;
            }
        }

        function validateYearManufactured() {
            const input = document.getElementById('yearManufacturedInput');
            if (!input) return false;
            
            const value = input.value.trim();
            let errorDiv = document.getElementById('yearManufacturedError');
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'yearManufacturedError';
                errorDiv.className = 'validation-error';
                input.parentNode.appendChild(errorDiv);
            }
            
            if (!value) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Year is required';
                errorDiv.style.display = 'flex';
                return false;
            } else if (!isValidYear(value)) {
                input.classList.add('error');
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Invalid year (1990-' + (new Date().getFullYear() + 1) + ')';
                errorDiv.style.display = 'flex';
                return false;
            } else {
                input.classList.remove('error');
                errorDiv.style.display = 'none';
                return true;
            }
        }

        function validateAllFields() {
            let isValid = true;
            
            if (document.getElementById('plateNumberInput')) {
                isValid = validatePlateNumber() && isValid;
            }
            
            if (document.getElementById('licenseNumberInput')) {
                isValid = validateLicenseNumber() && isValid;
            }
            
            if (document.getElementById('expiryDateInput')) {
                isValid = validateExpiryDate() && isValid;
            }
            
            if (document.getElementById('yearManufacturedInput')) {
                isValid = validateYearManufactured() && isValid;
            }
            
            return isValid;
        }

        function cancelTricycleEdit() {
            // Refresh the modal with original data
            showActiveDriverDetails(currentDriverData);
        }

        function validateAndSaveTricycle() {
            // Validate all fields before saving
            if (!validateAllFields()) {
                alert('Please fix the validation errors before saving.');
                return;
            }
            
            // Get all input values
            const plateNumber = document.getElementById('plateNumberInput')?.value.trim() || 'Not Specified';
            const model = document.getElementById('modelInput')?.value.trim() || 'Not Specified';
            const color = document.getElementById('colorInput')?.value.trim() || 'Not Specified';
            const bodyType = document.getElementById('bodyTypeInput')?.value.trim() || 'Not Specified';
            const passengerCapacity = document.getElementById('passengerCapacityInput')?.value.trim() || 'Not Specified';
            const yearManufactured = document.getElementById('yearManufacturedInput')?.value.trim() || 'Not Specified';
            const orCrNumber = document.getElementById('orCrNumberInput')?.value.trim() || 'Not Specified';
            const licenseNumber = document.getElementById('licenseNumberInput')?.value.trim() || 'Not Specified';
            const expiryDate = document.getElementById('expiryDateInput')?.value || 'Not Specified';
            
            // Format plate number
            const formattedPlateNumber = formatPlateNumber(plateNumber);
            
            // Set values in hidden form
            document.getElementById('plateNumberInput').value = formattedPlateNumber;
            document.getElementById('modelInput').value = model;
            document.getElementById('colorInput').value = color;
            document.getElementById('bodyTypeInput').value = bodyType;
            document.getElementById('passengerCapacityInput').value = passengerCapacity;
            document.getElementById('yearManufacturedInput').value = yearManufactured;
            document.getElementById('orCrNumberInput').value = orCrNumber;
            document.getElementById('licenseNumberInput').value = licenseNumber;
            document.getElementById('expiryDateInput').value = expiryDate;
            
            // Submit the form
            submitAction('update_tricycle', currentDriverId, currentDriverName);
        }

        // ========== APPROVE FUNCTIONS ==========
        function approveApplication(driverId, name) {
            showConfirmation('Approve Application', `Approve ${name}'s application?`, 'approve', driverId, name);
        }

        function approveFromModal() {
            if (currentAppData) {
                submitAction('approve', currentAppData.driver_id, currentAppData.name);
            }
        }

        // ========== REJECT FUNCTIONS ==========
        function showRejectModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('rejectionReason').value = '';
            const errorMsg = document.getElementById('rejectError');
            if (errorMsg) errorMsg.style.display = 'none';
            showModal('rejectModal');
        }

        function showRejectModalFromDetails() {
            if (currentAppData) {
                showRejectModal(currentAppData.driver_id, currentAppData.name);
            }
        }

        function closeRejectModal() {
            closeModal('rejectModal');
            const errorMsg = document.getElementById('rejectError');
            if (errorMsg) errorMsg.style.display = 'none';
        }

        function validateRejectionReason() {
            const reason = document.getElementById('rejectionReason').value.trim();
            const errorMsg = document.getElementById('rejectError');
            if (errorMsg) errorMsg.style.display = reason === '' ? 'flex' : 'none';
        }

        function validateAndSubmitRejection() {
            const reason = document.getElementById('rejectionReason').value.trim();
            const errorMsg = document.getElementById('rejectError');
            
            if (reason === '') {
                if (errorMsg) errorMsg.style.display = 'flex';
                return;
            }
            
            if (errorMsg) errorMsg.style.display = 'none';
            closeModal('rejectModal');
            submitAction('reject', currentDriverId, currentDriverName, reason);
        }

        // ========== DEACTIVATE FUNCTIONS ==========
        function showDeactivateModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('deactivationReason').value = '';
            const errorMsg = document.getElementById('deactivateError');
            if (errorMsg) errorMsg.style.display = 'none';
            showModal('deactivateModal');
        }

        function closeDeactivateModal() {
            closeModal('deactivateModal');
            const errorMsg = document.getElementById('deactivateError');
            if (errorMsg) errorMsg.style.display = 'none';
        }

        function validateDeactivationReason() {
            const reason = document.getElementById('deactivationReason').value.trim();
            const errorMsg = document.getElementById('deactivateError');
            if (errorMsg) errorMsg.style.display = reason === '' ? 'flex' : 'none';
        }

        function validateAndSubmitDeactivation() {
            const reason = document.getElementById('deactivationReason').value.trim();
            const errorMsg = document.getElementById('deactivateError');
            
            if (reason === '') {
                if (errorMsg) errorMsg.style.display = 'flex';
                return;
            }
            
            if (errorMsg) errorMsg.style.display = 'none';
            closeModal('deactivateModal');
            document.getElementById('deactivationReasonInput').value = reason;
            submitAction('deactivate', currentDriverId, currentDriverName);
        }

        // ========== SUSPEND FUNCTIONS ==========
        function showSuspendModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('suspendDuration').value = '7';
            document.getElementById('customDateContainer').style.display = 'none';
            
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 7);
            updateSuspendDateDisplay(defaultDate);
            document.getElementById('suspendUntilValue').value = defaultDate.toISOString();
            
            const errorMsg = document.getElementById('suspendError');
            if (errorMsg) errorMsg.style.display = 'none';
            
            showModal('suspendModal');
        }

        function closeSuspendModal() {
            closeModal('suspendModal');
            const errorMsg = document.getElementById('suspendError');
            if (errorMsg) errorMsg.style.display = 'none';
        }

        function updateSuspendDate() {
            const duration = document.getElementById('suspendDuration').value;
            const customContainer = document.getElementById('customDateContainer');
            
            if (duration === 'custom') {
                customContainer.style.display = 'block';
                const defaultDate = new Date();
                defaultDate.setDate(defaultDate.getDate() + 7);
                const year = defaultDate.getFullYear();
                const month = String(defaultDate.getMonth() + 1).padStart(2, '0');
                const day = String(defaultDate.getDate()).padStart(2, '0');
                const hours = String(defaultDate.getHours()).padStart(2, '0');
                const minutes = String(defaultDate.getMinutes()).padStart(2, '0');
                
                document.getElementById('customSuspendDate').value = `${year}-${month}-${day}T${hours}:${minutes}`;
                validateSuspendDate();
            } else {
                customContainer.style.display = 'none';
                const date = new Date();
                date.setDate(date.getDate() + parseInt(duration));
                updateSuspendDateDisplay(date);
                document.getElementById('suspendUntilValue').value = date.toISOString();
            }
        }

        function validateSuspendDate() {
            const duration = document.getElementById('suspendDuration').value;
            const errorMsg = document.getElementById('suspendError');
            
            if (duration === 'custom') {
                const customDate = document.getElementById('customSuspendDate').value;
                
                if (!customDate) {
                    if (errorMsg) {
                        errorMsg.style.display = 'flex';
                        errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please select a custom date.';
                    }
                    return false;
                }
                
                const selectedDate = new Date(customDate);
                const now = new Date();
                
                if (selectedDate <= now) {
                    if (errorMsg) {
                        errorMsg.style.display = 'flex';
                        errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Suspension date must be in the future.';
                    }
                    return false;
                }
                
                updateSuspendDateDisplay(selectedDate);
                document.getElementById('suspendUntilValue').value = selectedDate.toISOString();
                
                if (errorMsg) errorMsg.style.display = 'none';
                return true;
            }
            return true;
        }

        function updateSuspendDateDisplay(date) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const formattedDate = `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
            document.getElementById('suspendUntilText').textContent = formattedDate;
        }

        function validateAndSubmitSuspension() {
            const duration = document.getElementById('suspendDuration').value;
            let suspendUntil;
            
            if (duration === 'custom') {
                if (!validateSuspendDate()) return;
                suspendUntil = document.getElementById('suspendUntilValue').value;
            } else {
                const date = new Date();
                date.setDate(date.getDate() + parseInt(duration));
                suspendUntil = date.toISOString();
            }
            
            closeModal('suspendModal');
            document.getElementById('suspendUntilInput').value = suspendUntil;
            submitAction('suspend', currentDriverId, currentDriverName);
        }

        // ========== REACTIVATE FUNCTIONS ==========
        function reactivateDriver(driverId, name) {
            showConfirmation('Reactivate Driver', `Are you sure you want to reactivate ${name}?`, 'reactivate', driverId, name);
        }

        // ========== CONFIRMATION ==========
        function showConfirmation(title, message, action, driverId, driverName) {
            document.getElementById('confirmationTitle').textContent = title;
            document.getElementById('confirmationText').textContent = message;
            currentAction = action;
            currentDriverId = driverId;
            currentDriverName = driverName;
            showModal('confirmationModal');
        }

        // ========== SUBMIT ACTION ==========
        function submitAction(action, driverId, driverName, reason = '') {
            const form = document.getElementById('actionForm');
            document.getElementById('actionInput').value = action;
            document.getElementById('driverIdInput').value = driverId;
            document.getElementById('driverNameInput').value = driverName;
            
            if (reason) {
                if (action === 'reject') {
                    document.getElementById('rejectionReasonInput').value = reason;
                } else if (action === 'deactivate') {
                    document.getElementById('deactivationReasonInput').value = reason;
                }
            }
            
            form.submit();
        }

        // ========== CONFIRMATION HANDLER ==========
        document.getElementById('confirmActionBtn').addEventListener('click', function() {
            if (currentAction === 'approve') {
                submitAction('approve', currentDriverId, currentDriverName);
            } else if (currentAction === 'reject') {
                submitAction('reject', currentDriverId, currentDriverName, 'Rejected by admin');
            } else if (currentAction === 'deactivate') {
                submitAction('deactivate', currentDriverId, currentDriverName);
            } else if (currentAction === 'suspend') {
                submitAction('suspend', currentDriverId, currentDriverName);
            } else if (currentAction === 'reactivate') {
                submitAction('reactivate', currentDriverId, currentDriverName);
            }
            closeModal('confirmationModal');
        });

        // ========== CLOSE MODALS ON OUTSIDE CLICK ==========
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
                const rejectError = document.getElementById('rejectError');
                if (rejectError) rejectError.style.display = 'none';
                const deactivateError = document.getElementById('deactivateError');
                if (deactivateError) deactivateError.style.display = 'none';
                const suspendError = document.getElementById('suspendError');
                if (suspendError) suspendError.style.display = 'none';
                isEditMode = false;
            }
        }
    </script>
</body>
</html>