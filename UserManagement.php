<?php
session_start();

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

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

function fetchTricycleInfoFromFirebase() {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/TricycleInfo.json";
    
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

function fetchReuploadDocumentsFromFirebase() {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/ReuploadDocuments.json";
    
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

function rejectDriver($driverId, $reason, $rejectedDocuments = []) {
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
        $driverData['Status'] = 'Offline';
        $driverData['RejectedDocuments'] = is_array($rejectedDocuments) ? array_values($rejectedDocuments) : [];
        
        unset($driverData['DeactivationReason']);
        unset($driverData['SuspendedUntil']);
        unset($driverData['SuspensionReason']);
        unset($driverData['ReactivationReason']);
        
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
        $driverData['Status'] = 'Offline';
        unset($driverData['RejectionReason']);
        unset($driverData['RejectedDocuments']);
        unset($driverData['RejectedDocumentUrls']);
        unset($driverData['DeactivationReason']);
        unset($driverData['SuspendedUntil']);
        unset($driverData['SuspensionReason']);
        unset($driverData['ReactivationReason']);
        
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

function approveTricycle($driverId) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/TricycleInfo/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $tricycleData = json_decode($response, true);
        
        if ($tricycleData) {
            $tricycleData['TricycleStatus'] = 'Approved';
            $tricycleData['UpdatedAt'] = date('c');
            $tricycleData['RejectionReason'] = '';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tricycleData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return $httpCode == 200;
        }
    }
    return false;
}

function rejectTricycle($driverId, $reason) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/TricycleInfo/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $tricycleData = json_decode($response, true);
        
        if ($tricycleData) {
            $tricycleData['TricycleStatus'] = 'Rejected';
            $tricycleData['UpdatedAt'] = date('c');
            $tricycleData['RejectionReason'] = $reason;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tricycleData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return $httpCode == 200;
        }
    }
    return false;
}

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
        unset($driverData['SuspensionReason']);
        unset($driverData['ReactivationReason']);
        
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

function suspendDriver($driverId, $suspendUntil, $reason) {
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
        $driverData['SuspensionReason'] = $reason;
        $driverData['SuspendedUntil'] = $suspendUntil;
        $driverData['SuspendedDate'] = date('c');
        $driverData['LastUpdated'] = date('c');
        unset($driverData['DeactivationReason']);
        unset($driverData['ReactivationReason']);
        
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

function reactivateDriver($driverId, $reason) {
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
        $driverData['ReactivationReason'] = $reason;
        $driverData['ReactivationDate'] = date('c');
        $driverData['LastUpdated'] = date('c');
        $driverData['Status'] = 'Offline';
        unset($driverData['DeactivationReason']);
        unset($driverData['DeactivatedDate']);
        unset($driverData['SuspendedUntil']);
        unset($driverData['SuspendedDate']);
        unset($driverData['SuspensionReason']);
        
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

function approveSingleDocument($driverId, $documentKey, $documentUrl) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        if (!$driverData) return false;
        
        if (!isset($driverData['Documents'])) {
            $driverData['Documents'] = [];
        }
        
        $driverData['Documents'][$documentKey] = $documentUrl;
        
        if (isset($driverData['RejectedDocuments']) && is_array($driverData['RejectedDocuments'])) {
            $driverData['RejectedDocuments'] = array_values(array_filter($driverData['RejectedDocuments'], function($doc) use ($documentKey) {
                return $doc !== $documentKey;
            }));
        }
        
        if (isset($driverData['RejectedDocumentUrls']) && is_array($driverData['RejectedDocumentUrls'])) {
            unset($driverData['RejectedDocumentUrls'][$documentKey]);
        }
        
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

function approveAllDocuments($driverId, $reuploadedDocs) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        if (!$driverData) return false;
        
        if (!isset($driverData['Documents'])) {
            $driverData['Documents'] = [];
        }
        
        foreach ($reuploadedDocs as $docKey => $docUrl) {
            $driverData['Documents'][$docKey] = $docUrl;
        }
        
        $driverData['RejectedDocuments'] = [];
        $driverData['RejectedDocumentUrls'] = [];
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

function updateDocumentStatus($driverId, $rejectedDocs, $reuploadedUrls) {
    $firebaseUrl = "https://serviceco-37c60-default-rtdb.firebaseio.com/Drivers/{$driverId}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $driverData = json_decode($response, true);
        
        $updateData = [
            'DocumentStatus' => 'Pending Review',
            'RejectedDocuments' => array_values($rejectedDocs),
            'RejectedDocumentUrls' => (object)$reuploadedUrls,
            'LastUpdated' => date('c')
        ];
        
        $updatedData = array_merge($driverData, $updateData);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatedData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    return false;
}

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
            
            $rejectedDocuments = [];
            if (isset($_POST['rejected_documents']) && !empty($_POST['rejected_documents'])) {
                $decoded = json_decode($_POST['rejected_documents'], true);
                $rejectedDocuments = is_array($decoded) ? $decoded : [];
            }
            
            $success = rejectDriver($driverId, $rejectionReason, $rejectedDocuments);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been rejected." : "Failed to reject driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'approve_tricycle') {
            $success = approveTricycle($driverId);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Tricycle for {$driverName} has been approved!" : "Failed to approve tricycle."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'reject_tricycle') {
            $rejectionReason = $_POST['rejection_reason'] ?? 'Tricycle information incomplete or invalid';
            $success = rejectTricycle($driverId, $rejectionReason);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Tricycle for {$driverName} has been rejected." : "Failed to reject tricycle."
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
            $suspendReason = $_POST['suspend_reason'] ?? 'No reason provided';
            $success = suspendDriver($driverId, $suspendUntil, $suspendReason);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been suspended until " . date('M j, Y', strtotime($suspendUntil)) : "Failed to suspend driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'reactivate') {
            $reactivationReason = $_POST['reactivation_reason'] ?? 'Account reactivated by admin';
            $success = reactivateDriver($driverId, $reactivationReason);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Driver {$driverName} has been reactivated." : "Failed to reactivate driver."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'approve_single_document') {
            $documentKey = $_POST['document_key'] ?? '';
            $documentUrl = $_POST['document_url'] ?? '';
            $success = approveSingleDocument($driverId, $documentKey, $documentUrl);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "Document {$documentKey} has been approved!" : "Failed to approve document."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
            
        } elseif ($action === 'approve_all_documents') {
            $reuploadedDocsJson = $_POST['reuploaded_docs'] ?? '{}';
            $reuploadedDocs = json_decode($reuploadedDocsJson, true);
            $success = approveAllDocuments($driverId, $reuploadedDocs);
            $actionResult = [
                'success' => $success,
                'message' => $success ? "All documents have been approved!" : "Failed to approve documents."
            ];
            if ($success) echo "<meta http-equiv='refresh' content='1;url=?tab={$activeTab}'>";
        }
    }
}

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'applicationTab';

$firebaseDrivers = fetchDriversFromFirebase();
$tricycleInfos = fetchTricycleInfoFromFirebase();
$reuploadDocs = fetchReuploadDocumentsFromFirebase();

$driverApplications = [];
$driverMonitoring = [];
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;
$activeCount = 0;
$suspendedCount = 0;
$deactivatedCount = 0;
$onlineCount = 0;
$tricyclePendingCount = 0;
$tricycleApprovedCount = 0;
$tricycleRejectedCount = 0;

$colorOptions = ["Red", "Blue", "Black", "White", "Silver", "Gray", "Green", "Yellow", "Orange", "Brown", "Purple", "Pink", "Cyan", "Teal", "Lime", "Gold", "Maroon", "Navy", "Other"];
$yearOptions = ["2026", "2025", "2024", "2023", "2022", "2021", "2020", "2019", "2018", "2017", "2016", "2015", "2014", "2013", "2012", "2011", "2010", "2009", "2008", "2007", "2006", "2005", "2004", "2003", "2002", "2001", "2000", "1999", "1998", "1997", "1996", "1995", "Older"];
$capacityOptions = ["1-2 seater", "3-4 seater"];
$statusOptions = ["Active", "Inactive", "Under Maintenance", "For Repair"];

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
        
        $rejectedDocuments = $driver['RejectedDocuments'] ?? [];
        if (!is_array($rejectedDocuments)) {
            $rejectedDocuments = [];
        }
        
        $rejectedDocumentUrls = $driver['RejectedDocumentUrls'] ?? [];
        if (!is_array($rejectedDocumentUrls)) {
            $rejectedDocumentUrls = [];
        }
        
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
        
        if ($status == 'Online') $onlineCount++;
        
        $tricycleData = $tricycleInfos[$driverId] ?? [];
        $tricycleStatus = $tricycleData['TricycleStatus'] ?? 'Pending';
        $tricycleRejectionReason = $tricycleData['RejectionReason'] ?? '';
        
        if ($tricycleStatus == 'Approved') {
            $tricycleApprovedCount++;
        } elseif ($tricycleStatus == 'Rejected') {
            $tricycleRejectedCount++;
        } else {
            $tricyclePendingCount++;
        }
        
        $documents = $driver['Documents'] ?? [];
        $cleanDocuments = [];
        if (is_array($documents)) {
            foreach ($documents as $key => $value) {
                $cleanDocuments[$key] = $value;
            }
        }
        
        $profileImageUrl = $driver['ProfileImageUrl'] ?? '';
        
        $suspendedUntil = '';
        if (!empty($driver['SuspendedUntil'])) {
            try {
                $suspendedUntil = date('M j, Y', strtotime($driver['SuspendedUntil']));
            } catch (Exception $e) {
                $suspendedUntil = $driver['SuspendedUntil'];
            }
        }
        
        $suspensionReason = $driver['SuspensionReason'] ?? '';
        $deactivationReason = $driver['DeactivationReason'] ?? '';
        $reactivationReason = $driver['ReactivationReason'] ?? '';
        
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
            'rejected_documents' => $rejectedDocuments,
            'rejected_document_urls' => $rejectedDocumentUrls,
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
            'tricycle_data' => $tricycleData,
            'tricycle_status' => $tricycleStatus,
            'tricycle_rejection_reason' => $tricycleRejectionReason,
            'account_status' => $accountStatus,
            'suspended_until' => $suspendedUntil,
            'suspension_reason' => $suspensionReason,
            'deactivation_reason' => $deactivationReason,
            'reactivation_reason' => $reactivationReason,
            'reupload_documents' => $reuploadDocs[$driverId] ?? []
        ];
        
        if (!empty($driver['RegistrationCompleted']) && $driver['RegistrationCompleted'] === true) {
            $driverMonitoring[] = [
                'driver_id' => $driverId,
                'name' => $fullName,
                'initials' => $initials,
                'barangay' => $driver['Barangay'] ?? 'Not Specified',
                'status' => $status,
                'status_class' => $status == 'Online' ? 'active' : 'pending',
                'account_status' => $accountStatus,
                'account_status_class' => strtolower($accountStatus),
                'email' => !empty($driver['Email']) ? $driver['Email'] : 'Not Specified',
                'phone' => !empty($driver['MobileNumber']) ? $driver['MobileNumber'] : 'Not Specified',
                'total_trips' => $driver['TotalCompletedRides'] ?? 0,
                'rating' => number_format($driver['Rating'] ?? 5.0, 1),
                'joined_date' => !empty($driver['RegistrationDate']) ? 
                    date('M j, Y', strtotime($driver['RegistrationDate'])) : 
                    (isset($driver['CreatedAt']) ? date('M j, Y', strtotime($driver['CreatedAt'])) : 'Unknown'),
                'profile_image' => $profileImageUrl,
                'suspended_until' => $suspendedUntil,
                'suspension_reason' => $suspensionReason,
                'deactivation_reason' => $deactivationReason,
                'reactivation_reason' => $reactivationReason,
                'tricycle_data' => $tricycleData,
                'tricycle_status' => $tricycleStatus,
                'tricycle_rejection_reason' => $tricycleRejectionReason,
                'doc_status' => $docStatus,
                'reupload_documents' => $reuploadDocs[$driverId] ?? []
            ];
        }
    }
}

usort($driverApplications, function($a, $b) {
    return strtotime($b['application_date']) - strtotime($a['application_date']);
});

$totalApplicants = count($driverApplications);
$totalRegisteredDrivers = count($driverMonitoring);

$documentDisplayNames = [
    '2x2_Picture' => '2x2 ID Picture',
    'Cedula' => 'Cedula',
    'Barangay_Clearance' => 'Barangay Clearance',
    'Driver\'s_License' => 'Driver\'s License',
    'ORCR' => 'OR/CR',
    'Plate_Number' => 'Plate Number',
    'GCash_QR_Code' => 'GCash QR Code'
];

$documentIcons = [
    '2x2_Picture' => 'fas fa-id-card',
    'Cedula' => 'fas fa-file-contract',
    'Barangay_Clearance' => 'fas fa-file-alt',
    'Driver\'s_License' => 'fas fa-id-badge',
    'ORCR' => 'fas fa-car',
    'Plate_Number' => 'fas fa-image',
    'GCash_QR_Code' => 'fas fa-qrcode'
];
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
        .stat-card { background: white; border-radius: 8px; padding: 15px; border-left: 4px solid #347433; box-shadow: 0 2px 4px rgba(0,0,0,0.05); cursor: pointer; transition: all 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.approved { border-left-color: #347433; }
        .stat-card.rejected { border-left-color: #ef4444; }
        .stat-card.active { border-left-color: #347433; }
        .stat-card.suspended { border-left-color: #f59e0b; }
        .stat-card.deactivated { border-left-color: #6b7280; }
        .stat-card.tricycle-pending { border-left-color: #f59e0b; }
        .stat-card.tricycle-approved { border-left-color: #347433; }
        .stat-card.tricycle-rejected { border-left-color: #ef4444; }
        .stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .stat-title { font-size: 13px; color: #666; font-weight: 500; }
        .stat-icon { width: 35px; height: 35px; border-radius: 6px; display: flex; align-items: center; justify-content: center; background-color: #f0fdf4; color: #347433; }
        .stat-card.pending .stat-icon { background-color: #fef3c7; color: #f59e0b; }
        .stat-card.rejected .stat-icon { background-color: #fee2e2; color: #ef4444; }
        .stat-card.suspended .stat-icon { background-color: #fef3c7; color: #f59e0b; }
        .stat-card.deactivated .stat-icon { background-color: #e5e7eb; color: #6b7280; }
        .stat-card.tricycle-pending .stat-icon { background-color: #fef3c7; color: #f59e0b; }
        .stat-card.tricycle-approved .stat-icon { background-color: #d1fae5; color: #347433; }
        .stat-card.tricycle-rejected .stat-icon { background-color: #fee2e2; color: #ef4444; }
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
        
        .data-table { width: 100%; border-collapse: collapse; min-width: 1200px; }
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
        .status-badge.tricycle-pending { background-color: #fef3c7; color: #f59e0b; }
        .status-badge.tricycle-approved { background-color: #d1fae5; color: #347433; }
        .status-badge.tricycle-rejected { background-color: #fee2e2; color: #ef4444; }
        
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
        .action-btn.tricycle { background-color: #8b5cf6; color: white; }
        .action-btn.tricycle:hover { background-color: #7c3aed; }
        .action-btn.approve-doc { background-color: #2E7D32; color: white; }
        .action-btn.approve-doc:hover { background-color: #1e5a22; }
        
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
        
        .rejected-documents-list {
            background: #fff3f3;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid #ef4444;
        }
        
        .rejected-documents-list h5 {
            color: #ef4444;
            font-size: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .rejected-documents-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .rejected-documents-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            margin-bottom: 6px;
            background: white;
            border-radius: 6px;
            border: 1px solid #ffcdd2;
            transition: background 0.2s;
        }
        
        .rejected-documents-list li:hover {
            background-color: #f5f5f5;
        }
        
        .rejected-documents-list li.has-url {
            border-left: 4px solid #2E7D32;
        }
        
        .doc-icon-small {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fee2e2;
            color: #ef4444;
            font-size: 16px;
        }
        
        .rejected-documents-list li.has-url .doc-icon-small {
            background-color: #e8f5e9;
            color: #2E7D32;
        }
        
        .doc-name {
            font-size: 14px;
            color: #333;
            font-weight: 500;
            flex: 1;
        }
        
        .doc-status {
            font-size: 11px;
            color: #ef4444;
            background: #fee2e2;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .rejected-documents-list li.has-url .doc-status {
            background-color: #2E7D32;
            color: white;
        }
        
        .reuploaded-section {
            margin-top: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #8b5cf6;
        }
        
        .reuploaded-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .reuploaded-header h5 {
            color: #8b5cf6;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }
        
        .reuploaded-docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .reuploaded-doc-card {
            background: white;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #e0e7ff;
            transition: all 0.2s;
        }
        
        .reuploaded-doc-card:hover {
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
            border-color: #8b5cf6;
        }
        
        .reuploaded-doc-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .reuploaded-doc-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #ede9fe;
            color: #8b5cf6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .reuploaded-doc-name {
            font-weight: 600;
            color: #333;
            font-size: 15px;
            flex: 1;
        }
        
        .reuploaded-doc-actions {
            display: flex;
            gap: 8px;
        }
        
        .reuploaded-doc-actions button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.2s;
        }
        
        .btn-preview {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-preview:hover {
            background-color: #2563eb;
        }
        
        .btn-approve-doc {
            background-color: #2E7D32;
            color: white;
        }
        
        .btn-approve-doc:hover {
            background-color: #1e5a22;
        }
        
        .btn-approve-all {
            background-color: #8b5cf6;
            color: white;
            padding: 10px 22px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-approve-all:hover {
            background-color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);
        }
        
        .btn-approve-all:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
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
        .not-specified {
            color: #999;
            font-style: italic;
        }
        
        .documents-section { margin-top: 30px; }
        .documents-section h4 { 
            font-size: 18px; 
            margin-bottom: 20px; 
            color: #333; 
            padding-bottom: 10px; 
            border-bottom: 2px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .document-count-badge {
            background-color: #347433;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .documents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .document-item { background: white; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .document-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .document-icon { width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background-color: #f0fdf4; color: #347433; font-size: 22px; }
        .document-name { font-weight: 600; color: #333; font-size: 16px; flex: 1; }
        .document-status-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        .document-status-badge.reuploaded {
            background-color: #2E7D32;
            color: white;
        }
        .document-status-badge.pending {
            background-color: #f59e0b;
            color: white;
        }
        .preview-btn { 
            width: 100%; 
            padding: 10px; 
            background: #347433; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 8px; 
        }
        .preview-btn:hover { background: #2d6a2c; }
        .preview-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .preview-image { width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px; }
        
        .document-selection { 
            max-height: 300px; 
            overflow-y: auto; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px; 
            padding: 10px; 
            margin-bottom: 15px; 
        }
        .document-checkbox { 
            display: flex; 
            align-items: center; 
            padding: 10px; 
            border-bottom: 1px solid #f3f4f6; 
            cursor: pointer; 
            transition: background 0.2s; 
        }
        .document-checkbox:hover { background-color: #f9fafb; }
        .document-checkbox:last-child { border-bottom: none; }
        .document-checkbox input[type="checkbox"] { 
            width: 18px; 
            height: 18px; 
            margin-right: 12px; 
            accent-color: #ef4444; 
            cursor: pointer; 
        }
        .document-checkbox label { 
            font-size: 14px; 
            color: #333; 
            cursor: pointer; 
            flex: 1; 
        }
        .document-checkbox small { 
            font-size: 11px; 
            color: #999; 
            margin-left: 10px; 
        }
        .select-all-container { 
            background-color: #f9fafb; 
            padding: 12px; 
            border-radius: 6px; 
            margin-bottom: 15px; 
            display: flex; 
            align-items: center; 
        }
        .select-all-container input[type="checkbox"] { 
            width: 18px; 
            height: 18px; 
            margin-right: 10px; 
            accent-color: #ef4444; 
            cursor: pointer; 
        }
        .select-all-container label { 
            font-weight: 600; 
            font-size: 14px; 
            color: #333; 
            cursor: pointer; 
        }
        .selection-info { 
            font-size: 12px; 
            color: #666; 
            margin-top: 8px; 
            text-align: right; 
        }
        
        .btn { padding: 10px 22px; border-radius: 6px; font-weight: 500; cursor: pointer; border: none; font-size: 15px; transition: all 0.2s; }
        .btn-primary { background: #347433; color: white; }
        .btn-primary:hover { background: #2d6a2c; transform: translateY(-2px); }
        .btn-secondary { background-color: #e5e7eb; color: #4b5563; }
        .btn-secondary:hover { background-color: #d1d5db; transform: translateY(-2px); }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; transform: translateY(-2px); }
        .btn-warning { background-color: #f59e0b; color: white; }
        .btn-warning:hover { background-color: #d97706; transform: translateY(-2px); }
        .btn-success { background-color: #2E7D32; color: white; }
        .btn-success:hover { background-color: #1e5a22; transform: translateY(-2px); }
        .btn-purple { background-color: #8b5cf6; color: white; }
        .btn-purple:hover { background-color: #7c3aed; transform: translateY(-2px); }
        
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
        
        .reupload-history-section {
            margin-top: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #8b5cf6;
        }
        
        .reupload-history-section h5 {
            color: #8b5cf6;
            font-size: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .reupload-history-item {
            background: white;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #e0e7ff;
        }
        
        .reupload-date {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .reupload-docs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .reupload-doc-tag {
            background: #ede9fe;
            color: #8b5cf6;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        
        .reupload-doc-tag:hover {
            background: #8b5cf6;
            color: white;
        }
        
        .driver-approval-panel {
            margin-top: 30px;
            padding: 20px;
            background: #f0fdf4;
            border-radius: 8px;
            border: 2px solid #347433;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .driver-approval-panel h4 {
            color: #347433;
            font-size: 18px;
            font-weight: 600;
        }
        
        .driver-approval-panel p {
            color: #555;
            margin-top: 5px;
        }
        
        .driver-approval-panel button {
            background: #347433;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s;
        }
        
        .driver-approval-panel button:hover {
            background: #2d6a2c;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 116, 51, 0.3);
        }
        
        .document-approve-btn {
            margin-top: 10px;
            background-color: #2E7D32;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 100%;
        }
        
        .document-approve-btn:hover {
            background-color: #1e5a22;
        }
        
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        
        @media (max-width: 768px) { 
            .user-management-content { margin-left: 70px; width: calc(100% - 70px); padding: 15px; } 
            .tab-buttons-container { flex-direction: column; } 
            .documents-grid { grid-template-columns: 1fr; } 
            .driver-info { flex-direction: column; text-align: center; } 
            .search-filter-container { flex-direction: column; }
            .search-box { width: 100%; }
            .filter-buttons { width: 100%; justify-content: flex-start; }
            .driver-approval-panel { flex-direction: column; gap: 15px; text-align: center; }
            .reuploaded-docs-grid { grid-template-columns: 1fr; }
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 14px;
            color: #999;
        }

        .modal-header.approve-header { background-color: #347433; }
        .modal-header.reject-header { background-color: #ef4444; }
        .modal-header.suspend-header { background-color: #f59e0b; }
        .modal-header.deactivate-header { background-color: #6b7280; }
        .modal-header.reactivate-header { background-color: #347433; }
        .modal-header.tricycle-header { background-color: #8b5cf6; }
        
        .filter-section {
            margin: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include 'Sidebar.php'; ?>
    <?php include 'NavigationBar.php'; ?>

    <div class="user-management-content">
        <div class="welcome-section">
            <h1 class="welcome-title">Driver Application Management</h1>
            <p class="welcome-subtitle">Document Status • <?php echo $totalApplicants; ?> total applicants | Tricycle Status • <?php echo $tricyclePendingCount; ?> pending approval</p>
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
            <div id="applicationTab" class="tab-content <?php echo $activeTab == 'applicationTab' ? 'active' : ''; ?>">
                <div class="stats-grid">
                    <div class="stat-card" onclick="filterApplicationsByStatus('all')">
                        <div class="stat-header">
                            <div class="stat-title">Total Applicants</div>
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $totalApplicants; ?></div>
                        <div class="stat-change">Click to view all</div>
                    </div>
                    <div class="stat-card pending" onclick="filterApplicationsByStatus('pending')">
                        <div class="stat-header">
                            <div class="stat-title">Pending Review</div>
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $pendingCount; ?></div>
                        <div class="stat-change">Awaiting approval</div>
                    </div>
                    <div class="stat-card approved" onclick="filterApplicationsByStatus('approved')">
                        <div class="stat-header">
                            <div class="stat-title">Approved</div>
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $approvedCount; ?></div>
                        <div class="stat-change">Ready to drive</div>
                    </div>
                    <div class="stat-card rejected" onclick="filterApplicationsByStatus('rejected')">
                        <div class="stat-header">
                            <div class="stat-title">Rejected</div>
                            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $rejectedCount; ?></div>
                        <div class="stat-change">Needs re-upload</div>
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
                                <th>Tricycle Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTableBody">
                            <?php if (empty($driverApplications)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 50px;">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <h3>No Driver Applications Found</h3>
                                    <p>No driver applications have been submitted yet.</p>
                                </div>
                            </td></tr>
                            <?php else: ?>
                                <?php foreach ($driverApplications as $app): ?>
                                <tr data-doc-status="<?php echo strtolower($app['doc_status']); ?>" data-tricycle-status="<?php echo strtolower($app['tricycle_status']); ?>">
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
                                        <?php if (!empty($app['rejection_reason']) && $app['doc_status'] == 'Rejected'): ?>
                                            <br><small style="color: #ef4444;">Reason: <?php echo htmlspecialchars(substr($app['rejection_reason'], 0, 30)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $tricycleStatusClass = 'tricycle-pending';
                                        if ($app['tricycle_status'] == 'Approved') $tricycleStatusClass = 'tricycle-approved';
                                        elseif ($app['tricycle_status'] == 'Rejected') $tricycleStatusClass = 'tricycle-rejected';
                                        ?>
                                        <span class="status-badge <?php echo $tricycleStatusClass; ?>">
                                            <?php echo $app['tricycle_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $expectedDocs = ['2x2_Picture', 'Cedula', 'Barangay_Clearance', 'Driver\'s_License', 'ORCR', 'Plate_Number', 'GCash_QR_Code'];
                                        $uploadedDocsCount = 0;
                                        if (!empty($app['documents']) && is_array($app['documents'])) {
                                            foreach ($expectedDocs as $docKey) {
                                                if (isset($app['documents'][$docKey]) && !empty($app['documents'][$docKey])) {
                                                    $uploadedDocsCount++;
                                                }
                                            }
                                        }
                                        $hasReuploadedDocs = !empty($app['rejected_document_urls']) && count($app['rejected_document_urls']) > 0;
                                        $canApproveDriver = ($app['doc_status'] != 'Approved' && $uploadedDocsCount == 7 && !$hasReuploadedDocs);
                                        $canRejectDriver = ($app['doc_status'] != 'Rejected' && $app['doc_status'] != 'Approved' && $uploadedDocsCount > 0);
                                        ?>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick='showApplicationDetails(<?php echo json_encode($app, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <?php if ($canApproveDriver): ?>
                                            <button class="action-btn approve" onclick='showApproveConfirmation("<?php echo htmlspecialchars($app['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($app['name'], ENT_QUOTES); ?>")'>
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <?php endif; ?>
                                            <?php if ($canRejectDriver): ?>
                                            <button class="action-btn reject" onclick='showRejectModal(
                                                "<?php echo htmlspecialchars($app['driver_id'], ENT_QUOTES); ?>", 
                                                "<?php echo htmlspecialchars($app['name'], ENT_QUOTES); ?>", 
                                                <?php echo json_encode($app['documents'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                                <?php echo json_encode(array_keys($app['rejected_document_urls'] ?? []), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
                                            )'>
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
                    
                    <div id="noApplicationsMessage" class="empty-state" style="display: none;">
                        <i class="fas fa-filter"></i>
                        <h3>No applications match your filter</h3>
                        <p>Try adjusting your filter criteria.</p>
                    </div>
                </div>
            </div>

            <div id="monitoringTab" class="tab-content <?php echo $activeTab == 'monitoringTab' ? 'active' : ''; ?>">
                <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="stat-card" onclick="filterDriversByStatus('all')">
                        <div class="stat-header">
                            <div class="stat-title">Total Drivers</div>
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $totalRegisteredDrivers; ?></div>
                        <div class="stat-change">All registered drivers</div>
                    </div>
                    <div class="stat-card active" onclick="filterDriversByStatus('active')">
                        <div class="stat-header">
                            <div class="stat-title">Active Drivers</div>
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $activeCount; ?></div>
                        <div class="stat-change">Currently active</div>
                    </div>
                    <div class="stat-card suspended" onclick="filterDriversByStatus('suspended')">
                        <div class="stat-header">
                            <div class="stat-title">Suspended</div>
                            <div class="stat-icon"><i class="fas fa-pause-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $suspendedCount; ?></div>
                        <div class="stat-change">Temporarily suspended</div>
                    </div>
                    <div class="stat-card deactivated" onclick="filterDriversByStatus('deactivated')">
                        <div class="stat-header">
                            <div class="stat-title">Deactivated</div>
                            <div class="stat-icon"><i class="fas fa-ban"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $deactivatedCount; ?></div>
                        <div class="stat-change">Permanently deactivated</div>
                    </div>
                </div>

                <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-top: 15px;">
                    <div class="stat-card" onclick="filterDriversByStatus('online')">
                        <div class="stat-header">
                            <div class="stat-title">Online Now</div>
                            <div class="stat-icon"><i class="fas fa-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $onlineCount; ?></div>
                        <div class="stat-change">Ready to accept rides</div>
                    </div>
                    <div class="stat-card tricycle-pending" onclick="filterDriversByStatus('tricycle-pending')">
                        <div class="stat-header">
                            <div class="stat-title">Tricycle Pending</div>
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $tricyclePendingCount; ?></div>
                        <div class="stat-change">Awaiting approval</div>
                    </div>
                    <div class="stat-card tricycle-approved" onclick="filterDriversByStatus('tricycle-approved')">
                        <div class="stat-header">
                            <div class="stat-title">Tricycle Approved</div>
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $tricycleApprovedCount; ?></div>
                        <div class="stat-change">Approved tricycles</div>
                    </div>
                    <div class="stat-card tricycle-rejected" onclick="filterDriversByStatus('tricycle-rejected')">
                        <div class="stat-header">
                            <div class="stat-title">Tricycle Rejected</div>
                            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                        </div>
                        <div class="stat-value"><?php echo $tricycleRejectedCount; ?></div>
                        <div class="stat-change">Needs revision</div>
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
                            <button class="filter-btn" onclick="filterDrivers('tricycle-pending', this)">
                                <i class="fas fa-clock"></i> Tricycle Pending
                            </button>
                            <button class="filter-btn" onclick="filterDrivers('tricycle-approved', this)">
                                <i class="fas fa-check-circle"></i> Tricycle Approved
                            </button>
                            <button class="filter-btn" onclick="filterDrivers('tricycle-rejected', this)">
                                <i class="fas fa-times-circle"></i> Tricycle Rejected
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
                                <th>Tricycle Status</th>
                                <th>Trips</th>
                                <th>Rating</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="monitoringTableBody">
                            <?php if (empty($driverMonitoring)): ?>
                            <tr><td colspan="8" style="text-align: center; padding: 50px;">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <h3>No Registered Drivers Found</h3>
                                    <p>No drivers have been registered and approved yet.</p>
                                </div>
                            </td></tr>
                            <?php else: ?>
                                <?php foreach ($driverMonitoring as $driver): ?>
                                <tr data-account-status="<?php echo strtolower($driver['account_status']); ?>" data-online-status="<?php echo strtolower($driver['status']); ?>" data-tricycle-status="<?php echo strtolower($driver['tricycle_status']); ?>">
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
                                    <td>
                                        <?php 
                                        $tricycleStatusClass = 'tricycle-pending';
                                        if ($driver['tricycle_status'] == 'Approved') $tricycleStatusClass = 'tricycle-approved';
                                        elseif ($driver['tricycle_status'] == 'Rejected') $tricycleStatusClass = 'tricycle-rejected';
                                        ?>
                                        <span class="status-badge <?php echo $tricycleStatusClass; ?>">
                                            <?php echo $driver['tricycle_status']; ?>
                                        </span>
                                        <?php if (!empty($driver['tricycle_rejection_reason'])): ?>
                                            <br><small style="color: #ef4444;"><?php echo htmlspecialchars(substr($driver['tricycle_rejection_reason'], 0, 20)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($driver['total_trips']); ?></td>
                                    <td>⭐ <?php echo $driver['rating']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick='showActiveDriverDetails(<?php echo json_encode($driver, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            
                                            <button class="action-btn tricycle" onclick='showTricycleDetailsModal(<?php echo json_encode($driver, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i class="fas fa-motorcycle"></i> Tricycle
                                            </button>
                                            
                                            <?php if ($driver['account_status'] == 'Active'): ?>
                                                <button class="action-btn suspend" onclick='showSuspendModal("<?php echo htmlspecialchars($driver['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($driver['name'], ENT_QUOTES); ?>")'>
                                                    <i class="fas fa-pause"></i> Suspend
                                                </button>
                                                <button class="action-btn deactivate" onclick='showDeactivateModal("<?php echo htmlspecialchars($driver['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($driver['name'], ENT_QUOTES); ?>")'>
                                                    <i class="fas fa-ban"></i> Deactivate
                                                </button>
                                            <?php elseif ($driver['account_status'] == 'Suspended' || $driver['account_status'] == 'Deactivated'): ?>
                                                <button class="action-btn reactivate" onclick='showReactivationModal("<?php echo htmlspecialchars($driver['driver_id'], ENT_QUOTES); ?>", "<?php echo htmlspecialchars($driver['name'], ENT_QUOTES); ?>")'>
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
                    
                    <div id="noDriversMessage" class="empty-state" style="display: none;">
                        <i class="fas fa-filter"></i>
                        <h3>No drivers match your filter</h3>
                        <p>Try adjusting your filter criteria.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>ServiceCo User Management &copy; <?php echo date('Y'); ?>. All rights reserved.</p>
        </div>
    </div>

    <div class="modal" id="applicationModal">
        <div class="modal-content">
            <div class="modal-header" id="applicationModalHeader">
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
                <button class="btn btn-primary" id="approveModalBtn" onclick="approveFromModal()">Approve Driver</button>
                <button class="btn btn-danger" id="rejectModalBtn" onclick="showRejectModalFromDetails()">Reject</button>
            </div>
        </div>
    </div>

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
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('activeDriverModal')">Close</button>
            </div>
        </div>
    </div>

    <div class="modal" id="tricycleModal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header" style="background-color: #8b5cf6;">
                <h3>Tricycle Details</h3>
                <button class="modal-close" onclick="closeModal('tricycleModal')">&times;</button>
            </div>
            <div class="modal-body" id="tricycleDetailsContent">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #347433;"></i>
                    <p style="margin-top: 20px;">Loading...</p>
                </div>
            </div>
            <div class="modal-footer" id="tricycleModalFooter">
                <button class="btn btn-secondary" onclick="closeModal('tricycleModal')">Close</button>
                <button class="btn btn-primary" id="approveTricycleBtn" onclick="approveTricycleFromModal()">Approve Tricycle</button>
                <button class="btn btn-danger" id="rejectTricycleBtn" onclick="showRejectTricycleModalFromDetails()">Reject Tricycle</button>
            </div>
        </div>
    </div>

    <div class="modal" id="rejectModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header" style="background-color: #ef4444;">
                <h4>Reject Application</h4>
                <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Select which documents need to be re-uploaded:</p>
                
                <div class="select-all-container">
                    <input type="checkbox" id="selectAllDocs" onclick="toggleSelectAll()">
                    <label for="selectAllDocs"><strong>Select All Documents</strong></label>
                </div>
                
                <div class="document-selection" id="documentSelection"></div>
                
                <div class="selection-info" id="selectionInfo">No documents selected</div>
                
                <p style="margin-bottom: 10px; color: #555; margin-top: 15px;">Reason for rejection:</p>
                <textarea id="rejectionReason" class="reason-input" rows="4" placeholder="e.g., Blurry photos, incomplete information, etc..."></textarea>
                
                <div id="rejectError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please provide a reason and select at least one document.
                </div>
                
                <p style="font-size: 12px; color: #666; margin-top: 5px;">Driver will only be able to re-upload the selected documents.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn btn-danger" onclick="validateAndSubmitRejection()">Reject Selected Documents</button>
            </div>
        </div>
    </div>

    <div class="modal" id="rejectTricycleModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #ef4444;">
                <h4>Reject Tricycle</h4>
                <button class="modal-close" onclick="closeModal('rejectTricycleModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Please provide reason for rejection:</p>
                <textarea id="tricycleRejectionReason" class="reason-input" rows="4" placeholder="e.g., Invalid documents, incorrect information, missing requirements..."></textarea>
                
                <div id="tricycleRejectError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please provide a reason for rejection.
                </div>
                
                <p style="font-size: 12px; color: #666;">Driver will see this reason in their notification.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('rejectTricycleModal')">Cancel</button>
                <button class="btn btn-danger" onclick="submitTricycleRejection()">Reject Tricycle</button>
            </div>
        </div>
    </div>

    <div class="modal" id="deactivateModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #6b7280;">
                <h4>Deactivate Driver</h4>
                <button class="modal-close" onclick="closeModal('deactivateModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Please provide reason for deactivation:</p>
                <textarea id="deactivationReason" class="reason-input" rows="4" placeholder="e.g., Violation of terms, fraudulent activity, etc..."></textarea>
                
                <div id="deactivateError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please provide a reason for deactivation.
                </div>
                
                <p style="font-size: 12px; color: #666;">This driver will be permanently deactivated.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('deactivateModal')">Cancel</button>
                <button class="btn btn-danger" onclick="validateAndSubmitDeactivation()">Deactivate Driver</button>
            </div>
        </div>
    </div>

    <div class="modal" id="suspendModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #f59e0b;">
                <h4>Suspend Driver</h4>
                <button class="modal-close" onclick="closeModal('suspendModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Select suspension period and provide reason:</p>
                
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
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Suspension Reason:</label>
                    <textarea id="suspendReason" class="reason-input" rows="3" placeholder="e.g., Violation of terms, multiple complaints, etc..."></textarea>
                    <div id="suspendReasonError" class="error-message-text">
                        <i class="fas fa-exclamation-circle"></i> Please provide a reason for suspension.
                    </div>
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
                <button class="btn btn-secondary" onclick="closeModal('suspendModal')">Cancel</button>
                <button class="btn btn-warning" onclick="validateAndSubmitSuspension()">Suspend Driver</button>
            </div>
        </div>
    </div>

    <div class="modal" id="reactivationModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #347433;">
                <h4>Reactivate Driver</h4>
                <button class="modal-close" onclick="closeModal('reactivationModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #555;">Please provide reason for reactivation:</p>
                <textarea id="reactivationReason" class="reason-input" rows="4" placeholder="e.g., Issue resolved, appeal approved, etc..."></textarea>
                
                <div id="reactivationError" class="error-message-text">
                    <i class="fas fa-exclamation-circle"></i> Please provide a reason for reactivation.
                </div>
                
                <p style="font-size: 12px; color: #666;">Driver will see this reason in their notification.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('reactivationModal')">Cancel</button>
                <button class="btn btn-success" onclick="validateAndSubmitReactivation()">Reactivate Driver</button>
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
                <a href="#" id="downloadLink" target="_blank" style="text-decoration: none;">
                    <button class="btn btn-primary">Download</button>
                </a>
            </div>
        </div>
    </div>

    <div class="modal" id="approveSingleDocModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #2E7D32;">
                <h4>Approve Document</h4>
                <button class="modal-close" onclick="closeModal('approveSingleDocModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon" style="text-align: center; font-size: 48px; color: #2E7D32; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p id="approveDocText" style="font-size: 16px; color: #555; margin: 20px 0; text-align: center;">Approve this document?</p>
                <p id="approveDocDetails" style="font-size: 14px; color: #666; text-align: center;">This will move the document to Approved Documents section.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('approveSingleDocModal')">Cancel</button>
                <button class="btn btn-success" id="confirmApproveDocBtn">Approve</button>
            </div>
        </div>
    </div>

    <div class="modal" id="approveAllDocsModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #8b5cf6;">
                <h4>Approve All Documents</h4>
                <button class="modal-close" onclick="closeModal('approveAllDocsModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon" style="text-align: center; font-size: 48px; color: #8b5cf6; margin-bottom: 20px;">
                    <i class="fas fa-check-double"></i>
                </div>
                <p id="approveAllDocsText" style="font-size: 16px; color: #555; margin: 20px 0; text-align: center;">Approve all re-uploaded documents?</p>
                <p id="approveAllDocsDetails" style="font-size: 14px; color: #666; text-align: center;">All selected documents will be moved to Approved Documents section.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('approveAllDocsModal')">Cancel</button>
                <button class="btn btn-purple" id="confirmApproveAllDocsBtn">Approve All</button>
            </div>
        </div>
    </div>

    <div class="modal" id="confirmationModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #347433;">
                <h4 id="confirmationTitle">Confirm Approval</h4>
                <button class="modal-close" onclick="closeModal('confirmationModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon" style="text-align: center; font-size: 48px; color: #347433; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p id="confirmationText" style="font-size: 16px; color: #555; margin: 20px 0; text-align: center;">Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('confirmationModal')">Cancel</button>
                <button class="btn btn-success" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>

    <div class="modal" id="tricycleConfirmModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #8b5cf6;">
                <h4 id="tricycleConfirmTitle">Confirm Tricycle Approval</h4>
                <button class="modal-close" onclick="closeModal('tricycleConfirmModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon" style="text-align: center; font-size: 48px; color: #8b5cf6; margin-bottom: 20px;">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <p id="tricycleConfirmText" style="font-size: 16px; color: #555; margin: 20px 0; text-align: center;">Approve this tricycle?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('tricycleConfirmModal')">Cancel</button>
                <button class="btn btn-purple" id="confirmTricycleBtn">Confirm</button>
            </div>
        </div>
    </div>

    <form id="actionForm" method="POST" style="display: none;">
        <input type="hidden" name="action" id="actionInput">
        <input type="hidden" name="driver_id" id="driverIdInput">
        <input type="hidden" name="driver_name" id="driverNameInput">
        <input type="hidden" name="rejection_reason" id="rejectionReasonInput">
        <input type="hidden" name="rejected_documents" id="rejectedDocumentsInput">
        <input type="hidden" name="deactivation_reason" id="deactivationReasonInput">
        <input type="hidden" name="suspend_until" id="suspendUntilInput">
        <input type="hidden" name="suspend_reason" id="suspendReasonInput">
        <input type="hidden" name="reactivation_reason" id="reactivationReasonInput">
        <input type="hidden" name="document_key" id="documentKeyInput">
        <input type="hidden" name="document_url" id="documentUrlInput">
        <input type="hidden" name="reuploaded_docs" id="reuploadedDocsInput">
        <input type="hidden" name="active_tab" id="activeTabInput" value="<?php echo $activeTab; ?>">
    </form>

    <div class="success-message" id="successMessage"></div>
    <div class="error-message-toast" id="errorMessage"></div>

    <script>
        let currentAppData = null;
        let currentDriverData = null;
        let currentTricycleData = null;
        let currentDriverId = null;
        let currentDriverName = null;
        let currentAction = null;
        let currentDocuments = {};
        let currentDocumentKey = null;
        let currentDocumentUrl = null;
        let currentReuploadedDocs = {};
        let currentPreSelectedDocs = [];

        const colorOptions = <?php echo json_encode($colorOptions); ?>;
        const yearOptions = <?php echo json_encode($yearOptions); ?>;
        const capacityOptions = <?php echo json_encode($capacityOptions); ?>;
        const statusOptions = <?php echo json_encode($statusOptions); ?>;
        
        const documentDisplayNames = <?php echo json_encode($documentDisplayNames); ?>;
        const documentIcons = <?php echo json_encode($documentIcons); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            setupSearch();
            if (window.updateAllContentPositions) {
                window.updateAllContentPositions();
            }
        });

        function openTab(tabName, button) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            button.classList.add('active');
            document.getElementById('activeTabInput').value = tabName;
        }

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
            
            let visibleCount = 0;
            
            for (let row of rows) {
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) continue;
                
                let found = false;
                for (let cell of row.cells) {
                    if (cell.textContent.toLowerCase().includes(term)) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? '' : 'none';
                if (found) visibleCount++;
            }
            
            const noResultsMsg = tableId === 'applicationTable' ? document.getElementById('noApplicationsMessage') : document.getElementById('noDriversMessage');
            if (noResultsMsg) {
                if (visibleCount === 0 && rows.length > 0) {
                    noResultsMsg.style.display = 'block';
                } else {
                    noResultsMsg.style.display = 'none';
                }
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
            
            let visibleCount = 0;
            
            for (let row of rows) {
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) continue;
                
                if (status === 'all') {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    const docStatus = row.getAttribute('data-doc-status');
                    row.style.display = docStatus === status ? '' : 'none';
                    if (docStatus === status) visibleCount++;
                }
            }
            
            const noResultsMsg = document.getElementById('noApplicationsMessage');
            if (noResultsMsg) {
                if (visibleCount === 0 && rows.length > 0) {
                    noResultsMsg.style.display = 'block';
                } else {
                    noResultsMsg.style.display = 'none';
                }
            }
        }

        function filterApplicationsByStatus(status) {
            const buttons = document.querySelectorAll('#applicationTab .filter-btn');
            let targetButton = null;
            
            if (status === 'all') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('All'));
            } else if (status === 'pending') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Pending') && !btn.textContent.includes('Tricycle'));
            } else if (status === 'approved') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Approved') && !btn.textContent.includes('Tricycle'));
            } else if (status === 'rejected') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Rejected') && !btn.textContent.includes('Tricycle'));
            }
            
            if (targetButton) {
                filterApplications(status, targetButton);
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
            
            let visibleCount = 0;
            
            for (let row of rows) {
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) continue;
                
                if (status === 'all') {
                    row.style.display = '';
                    visibleCount++;
                } else if (status.startsWith('tricycle-')) {
                    const tricycleStatus = row.getAttribute('data-tricycle-status');
                    const statusValue = status.replace('tricycle-', '');
                    row.style.display = tricycleStatus === statusValue ? '' : 'none';
                    if (tricycleStatus === statusValue) visibleCount++;
                } else if (status === 'online') {
                    const onlineStatus = row.getAttribute('data-online-status');
                    row.style.display = onlineStatus === 'online' ? '' : 'none';
                    if (onlineStatus === 'online') visibleCount++;
                } else {
                    const accountStatus = row.getAttribute('data-account-status');
                    row.style.display = accountStatus === status ? '' : 'none';
                    if (accountStatus === status) visibleCount++;
                }
            }
            
            const noResultsMsg = document.getElementById('noDriversMessage');
            if (noResultsMsg) {
                if (visibleCount === 0 && rows.length > 0) {
                    noResultsMsg.style.display = 'block';
                } else {
                    noResultsMsg.style.display = 'none';
                }
            }
        }

        function filterDriversByStatus(status) {
            const buttons = document.querySelectorAll('#monitoringTab .filter-btn');
            let targetButton = null;
            
            if (status === 'all') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('All'));
            } else if (status === 'active') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Active'));
            } else if (status === 'suspended') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Suspended'));
            } else if (status === 'deactivated') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Deactivated'));
            } else if (status === 'online') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Online'));
            } else if (status === 'tricycle-pending') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Tricycle Pending'));
            } else if (status === 'tricycle-approved') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Tricycle Approved'));
            } else if (status === 'tricycle-rejected') {
                targetButton = Array.from(buttons).find(btn => btn.textContent.includes('Tricycle Rejected'));
            }
            
            if (targetButton) {
                filterDrivers(status, targetButton);
            }
        }

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('active');
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.remove('active');
            
            const rejectError = document.getElementById('rejectError');
            if (rejectError) rejectError.style.display = 'none';
            const deactivateError = document.getElementById('deactivateError');
            if (deactivateError) deactivateError.style.display = 'none';
            const suspendError = document.getElementById('suspendError');
            if (suspendError) suspendError.style.display = 'none';
            const tricycleError = document.getElementById('tricycleRejectError');
            if (tricycleError) tricycleError.style.display = 'none';
            const reasonError = document.getElementById('suspendReasonError');
            if (reasonError) reasonError.style.display = 'none';
            const reactivationError = document.getElementById('reactivationError');
            if (reactivationError) reactivationError.style.display = 'none';
        }

        window.closeModal = closeModal;

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
            if (!docUrl) return;
            
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

        function populateDocumentCheckboxes(documents, preSelectedDocs = []) {
            currentDocuments = documents || {};
            currentPreSelectedDocs = preSelectedDocs || [];
            
            const docOrder = ['2x2_Picture', 'Cedula', 'Barangay_Clearance', 'Driver\'s_License', 'ORCR', 'Plate_Number', 'GCash_QR_Code'];
            const container = document.getElementById('documentSelection');
            
            let html = '';
            let allEnabled = true;
            
            docOrder.forEach(docKey => {
                const displayName = documentDisplayNames[docKey] || docKey.replace(/_/g, ' ');
                const hasDocument = currentDocuments[docKey] ? true : false;
                const isPreSelected = currentPreSelectedDocs.includes(docKey);
                
                if (!hasDocument) allEnabled = false;
                
                html += `
                    <div class="document-checkbox">
                        <input type="checkbox" id="doc_${docKey}" value="${docKey}" ${!hasDocument ? 'disabled' : ''} ${isPreSelected ? 'checked' : ''}>
                        <label for="doc_${docKey}">
                            ${displayName} 
                            <small>${hasDocument ? '✓ Uploaded' : '❌ Missing'}</small>
                        </label>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            const selectAll = document.getElementById('selectAllDocs');
            if (selectAll) {
                selectAll.checked = allEnabled && currentPreSelectedDocs.length === docOrder.filter(key => currentDocuments[key]).length;
            }
            
            document.querySelectorAll('#documentSelection input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', updateSelectionInfo);
            });
            
            updateSelectionInfo();
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAllDocs');
            const checkboxes = document.querySelectorAll('#documentSelection input[type="checkbox"]:not(:disabled)');
            
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            
            updateSelectionInfo();
        }

        function updateSelectionInfo() {
            const checkboxes = document.querySelectorAll('#documentSelection input[type="checkbox"]:checked');
            const count = checkboxes.length;
            const info = document.getElementById('selectionInfo');
            
            if (count === 0) {
                info.innerHTML = 'No documents selected';
                info.style.color = '#666';
            } else if (count === 1) {
                info.innerHTML = '1 document selected for rejection';
                info.style.color = '#ef4444';
            } else {
                info.innerHTML = count + ' documents selected for rejection';
                info.style.color = '#ef4444';
            }
        }

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
            
            let uploadedDocsGrid = '';
            const docOrder = ['2x2_Picture', 'Cedula', 'Barangay_Clearance', 'Driver\'s_License', 'ORCR', 'Plate_Number', 'GCash_QR_Code'];
            let uploadedDocsCount = 0;
            
            if (application.documents && Object.keys(application.documents).length > 0) {
                docOrder.forEach(docKey => {
                    if (application.documents[docKey]) {
                        const isRejected = application.rejected_documents && application.rejected_documents.includes(docKey);
                        const hasReupload = application.rejected_document_urls && application.rejected_document_urls[docKey];
                        
                        if (!isRejected || (isRejected && hasReupload && application.doc_status === 'Approved')) {
                            uploadedDocsCount++;
                            const docUrl = application.documents[docKey];
                            const displayName = displayNames[docKey] || docKey;
                            
                            uploadedDocsGrid += `
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
                    }
                });
            }

            if (!uploadedDocsGrid) {
                uploadedDocsGrid = '<div style="text-align: center; padding: 40px; color: #666;">No uploaded documents available.</div>';
            }

            let rejectedDocsHtml = '';
            if (application.rejection_reason && application.doc_status == 'Rejected' && application.rejected_documents && application.rejected_documents.length > 0) {
                let docsList = '';
                application.rejected_documents.forEach(doc => {
                    const icon = documentIcons[doc] || 'fas fa-file';
                    const displayName = displayNames[doc] || doc.replace(/_/g, ' ');
                    
                    docsList += `
                        <li>
                            <div class="doc-icon-small"><i class="${icon}"></i></div>
                            <span class="doc-name">${displayName}</span>
                            <span class="doc-status">NEED RE-UPLOAD</span>
                        </li>
                    `;
                });
                
                if (docsList) {
                    rejectedDocsHtml = `
                        <div class="rejected-documents-list">
                            <h5><i class="fas fa-exclamation-triangle"></i> Documents to Re-upload:</h5>
                            <ul>${docsList}</ul>
                        </div>
                    `;
                }
            }

            let reuploadedDocsHtml = '';
            let reuploadedDocsCount = 0;
            let reuploadedDocsList = {};
            
            if (application.rejected_documents && application.rejected_documents.length > 0 && 
                application.rejected_document_urls && Object.keys(application.rejected_document_urls).length > 0) {
                
                let docsGrid = '';
                application.rejected_documents.forEach(doc => {
                    const icon = documentIcons[doc] || 'fas fa-file';
                    const displayName = displayNames[doc] || doc.replace(/_/g, ' ');
                    
                    if (application.rejected_document_urls[doc]) {
                        reuploadedDocsCount++;
                        reuploadedDocsList[doc] = application.rejected_document_urls[doc];
                        
                        docsGrid += `
                            <div class="reuploaded-doc-card">
                                <div class="reuploaded-doc-header">
                                    <div class="reuploaded-doc-icon"><i class="${icon}"></i></div>
                                    <span class="reuploaded-doc-name">${displayName}</span>
                                </div>
                                <div class="reuploaded-doc-actions">
                                    <button class="btn-preview" onclick='previewDocument("${escapeHtml(displayName)}", "${application.rejected_document_urls[doc]}")'>
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <button class="btn-approve-doc" onclick='showApproveSingleDocModal("${escapeHtml(application.driver_id)}", "${escapeHtml(application.name)}", "${doc}", "${application.rejected_document_urls[doc]}")'>
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                });
                
                if (docsGrid) {
                    let approveAllButton = '';
                    if (reuploadedDocsCount > 0 && reuploadedDocsCount === application.rejected_documents.length) {
                        approveAllButton = `
                            <div class="approve-all-container">
                                <button class="btn-approve-all" onclick='showApproveAllDocsModal("${escapeHtml(application.driver_id)}", "${escapeHtml(application.name)}", ${JSON.stringify(reuploadedDocsList)})'>
                                    <i class="fas fa-check-double"></i> Approve All (${reuploadedDocsCount})
                                </button>
                            </div>
                        `;
                    }
                    
                    reuploadedDocsHtml = `
                        <div class="reuploaded-section">
                            <div class="reuploaded-header">
                                <h5><i class="fas fa-cloud-upload-alt"></i> Re-uploaded Documents (${reuploadedDocsCount})</h5>
                                ${approveAllButton}
                            </div>
                            <div class="reuploaded-docs-grid">
                                ${docsGrid}
                            </div>
                        </div>
                    `;
                }
            }

            let driverApprovalPanel = '';
            const canApproveDriver = uploadedDocsCount === 7 && application.doc_status !== 'Approved';
            
            if (canApproveDriver) {
                driverApprovalPanel = `
                    <div class="driver-approval-panel">
                        <div>
                            <h4><i class="fas fa-check-circle"></i> Ready for Approval!</h4>
                            <p>All 7 documents are uploaded and ready. Click Approve to finalize driver registration.</p>
                        </div>
                        <button onclick='showApproveConfirmation("${escapeHtml(application.driver_id)}", "${escapeHtml(application.name)}")'>
                            <i class="fas fa-check-circle"></i> APPROVE DRIVER
                        </button>
                    </div>
                `;
            }

            let rejectionReasonHtml = '';
            if (application.rejection_reason && application.doc_status == 'Rejected') {
                rejectionReasonHtml = `
                    <div class="rejection-reason">
                        <h5><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h5>
                        <p>${escapeHtml(application.rejection_reason)}</p>
                    </div>
                `;
            }

            const sectionTitle = application.doc_status === 'Approved' ? 'Approved Documents' : 'Uploaded Documents';
            
            const content = `
                <div class="driver-info">
                    <div class="profile-pic-circle">
                        ${application.profile_image ? `<img src="${application.profile_image}" alt="Profile">` : '<i class="fas fa-user"></i>'}
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
                        <h5><i class="fas fa-file"></i> Documents Summary</h5>
                        <p><strong>Uploaded:</strong> ${uploadedDocsCount}/7 files</p>
                        <p><strong>Re-uploaded:</strong> ${reuploadedDocsCount}/${application.rejected_documents?.length || 0} documents</p>
                        <p><strong>Registration:</strong> ${application.registration_completed ? '✅ Complete' : '⏳ Pending'}</p>
                    </div>
                </div>
                
                ${rejectionReasonHtml}
                
                ${rejectedDocsHtml}
                
                ${reuploadedDocsHtml}
                
                <div class="documents-section">
                    <h4>
                        ${sectionTitle}
                        <span class="document-count-badge">${uploadedDocsCount}/7</span>
                    </h4>
                    <div class="documents-grid">${uploadedDocsGrid}</div>
                </div>
                
                ${driverApprovalPanel}
            `;
            
            document.getElementById('applicationDetailsContent').innerHTML = content;
            
            const approveBtn = document.getElementById('approveModalBtn');
            const rejectBtn = document.getElementById('rejectModalBtn');
            
            if (approveBtn) {
                approveBtn.style.display = (application.doc_status != 'Approved' && uploadedDocsCount == 7 && reuploadedDocsCount === 0) ? 'inline-block' : 'none';
            }
            if (rejectBtn) {
                rejectBtn.style.display = (application.doc_status != 'Rejected' && application.doc_status != 'Approved' && uploadedDocsCount > 0) ? 'inline-block' : 'none';
            }
            
            const modalHeader = document.getElementById('applicationModalHeader');
            if (application.doc_status == 'Rejected') {
                modalHeader.style.backgroundColor = '#ef4444';
            } else if (application.doc_status == 'Approved') {
                modalHeader.style.backgroundColor = '#347433';
            } else {
                modalHeader.style.backgroundColor = '#347433';
            }
            
            showModal('applicationModal');
        }

        function showApproveSingleDocModal(driverId, driverName, documentKey, documentUrl) {
            currentDriverId = driverId;
            currentDriverName = driverName;
            currentDocumentKey = documentKey;
            currentDocumentUrl = documentUrl;
            
            const displayName = documentDisplayNames[documentKey] || documentKey.replace(/_/g, ' ');
            document.getElementById('approveDocText').innerHTML = `Approve <strong>${displayName}</strong>?`;
            
            document.getElementById('confirmApproveDocBtn').onclick = function() {
                submitSingleDocApproval();
            };
            
            showModal('approveSingleDocModal');
        }

        function submitSingleDocApproval() {
            document.getElementById('actionInput').value = 'approve_single_document';
            document.getElementById('driverIdInput').value = currentDriverId;
            document.getElementById('driverNameInput').value = currentDriverName;
            document.getElementById('documentKeyInput').value = currentDocumentKey;
            document.getElementById('documentUrlInput').value = currentDocumentUrl;
            document.getElementById('activeTabInput').value = 'applicationTab';
            
            closeModal('approveSingleDocModal');
            document.getElementById('actionForm').submit();
        }

        function showApproveAllDocsModal(driverId, driverName, reuploadedDocs) {
            currentDriverId = driverId;
            currentDriverName = driverName;
            currentReuploadedDocs = reuploadedDocs;
            
            const count = Object.keys(reuploadedDocs).length;
            document.getElementById('approveAllDocsText').innerHTML = `Approve all ${count} documents?`;
            
            document.getElementById('confirmApproveAllDocsBtn').onclick = function() {
                submitAllDocsApproval();
            };
            
            showModal('approveAllDocsModal');
        }

        function submitAllDocsApproval() {
            document.getElementById('actionInput').value = 'approve_all_documents';
            document.getElementById('driverIdInput').value = currentDriverId;
            document.getElementById('driverNameInput').value = currentDriverName;
            document.getElementById('reuploadedDocsInput').value = JSON.stringify(currentReuploadedDocs);
            document.getElementById('activeTabInput').value = 'applicationTab';
            
            closeModal('approveAllDocsModal');
            document.getElementById('actionForm').submit();
        }

        function showActiveDriverDetails(driver) {
            currentDriverData = driver;
            
            let suspendedInfo = '';
            if (driver.account_status == 'Suspended' && driver.suspended_until) {
                suspendedInfo = `<p><strong>Suspended Until:</strong> ${escapeHtml(driver.suspended_until)}</p>`;
                if (driver.suspension_reason) {
                    suspendedInfo += `<p><strong>Reason:</strong> ${escapeHtml(driver.suspension_reason)}</p>`;
                }
            }
            
            let deactivationInfo = '';
            if (driver.account_status == 'Deactivated' && driver.deactivation_reason) {
                deactivationInfo = `<p><strong>Deactivation Reason:</strong> ${escapeHtml(driver.deactivation_reason)}</p>`;
            }
            
            let reactivationInfo = '';
            if (driver.reactivation_reason) {
                reactivationInfo = `<p><strong>Reactivation Reason:</strong> ${escapeHtml(driver.reactivation_reason)}</p>`;
            }

            let accountStatusBadge = `<span class="status-badge ${driver.account_status_class}">${escapeHtml(driver.account_status)}</span>`;

            let reuploadHistoryHtml = '';
            const displayNames = {
                '2x2_Picture': '2x2 Picture',
                'Cedula': 'Cedula',
                'Barangay_Clearance': 'Barangay Clearance',
                'Driver\'s_License': 'Driver\'s License',
                'ORCR': 'OR/CR',
                'Plate_Number': 'Plate Number',
                'GCash_QR_Code': 'GCash QR Code'
            };
            
            if (driver.reupload_documents && Object.keys(driver.reupload_documents).length > 0) {
                let historyItems = '';
                
                const reuploadDocs = driver.reupload_documents;
                const uploadDates = [];
                
                Object.keys(reuploadDocs).forEach(key => {
                    const parts = key.split('_');
                    if (parts.length >= 2) {
                        const dateStr = parts[parts.length - 1];
                        if (dateStr.length === 8 && !isNaN(dateStr)) {
                            const year = dateStr.substring(0, 4);
                            const month = dateStr.substring(4, 6);
                            const day = dateStr.substring(6, 8);
                            const formattedDate = `${year}-${month}-${day}`;
                            if (!uploadDates.includes(formattedDate)) {
                                uploadDates.push(formattedDate);
                            }
                        }
                    }
                });
                
                uploadDates.sort().reverse().forEach(date => {
                    let docsForDate = '';
                    Object.keys(reuploadDocs).forEach(key => {
                        if (key.includes(date.replace(/-/g, ''))) {
                            const docName = key.split('_')[0];
                            const displayName = displayNames[docName] || docName.replace(/_/g, ' ');
                            docsForDate += `
                                <span class="reupload-doc-tag" onclick='previewDocument("${escapeHtml(displayName)}", "${reuploadDocs[key]}")' style="cursor: pointer;">
                                    <i class="${getDocumentIcon(docName)}"></i> ${displayName}
                                </span>
                            `;
                        }
                    });
                    
                    if (docsForDate) {
                        historyItems += `
                            <div class="reupload-history-item">
                                <div class="reupload-date"><i class="far fa-calendar-alt"></i> ${date}</div>
                                <div class="reupload-docs">${docsForDate}</div>
                            </div>
                        `;
                    }
                });
                
                if (historyItems) {
                    reuploadHistoryHtml = `
                        <div class="reupload-history-section">
                            <h5><i class="fas fa-history"></i> Re-upload History</h5>
                            ${historyItems}
                        </div>
                    `;
                }
            }

            const content = `
                <div class="driver-info">
                    <div class="profile-pic-circle">
                        ${driver.profile_image ? `<img src="${driver.profile_image}" alt="Profile">` : '<i class="fas fa-user"></i>'}
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
                        ${reactivationInfo}
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
                
                ${reuploadHistoryHtml}
            `;
            
            document.getElementById('activeDriverDetailsContent').innerHTML = content;
            showModal('activeDriverModal');
        }

        function showTricycleDetailsModal(driver) {
            currentDriverData = driver;
            currentDriverId = driver.driver_id;
            currentDriverName = driver.name;
            
            let tricycle = driver.tricycle_data || {};
            let tricycleStatus = driver.tricycle_status || 'Pending';
            
            let statusBadge = '';
            if (tricycleStatus == 'Approved') {
                statusBadge = '<span class="status-badge tricycle-approved">Approved</span>';
            } else if (tricycleStatus == 'Rejected') {
                statusBadge = '<span class="status-badge tricycle-rejected">Rejected</span>';
            } else {
                statusBadge = '<span class="status-badge tricycle-pending">Pending</span>';
            }
            
            let rejectionHtml = '';
            if (tricycleStatus == 'Rejected' && driver.tricycle_rejection_reason) {
                rejectionHtml = `
                    <div class="rejection-reason">
                        <h5><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h5>
                        <p>${escapeHtml(driver.tricycle_rejection_reason)}</p>
                    </div>
                `;
            }
            
            let lastUpdated = 'Never';
            if (tricycle.UpdatedAt) {
                try {
                    lastUpdated = new Date(tricycle.UpdatedAt).toLocaleString();
                } catch (e) {}
            }
            
            const canApprove = (driver.account_status == 'Active' && driver.doc_status == 'Approved');
            
            const content = `
                <div style="padding: 10px;">
                    <h3 style="margin-bottom: 20px;">Tricycle Details for ${escapeHtml(driver.name)} ${statusBadge}</h3>
                    
                    <div class="tricycle-grid">
                        <div class="tricycle-item">
                            <div class="tricycle-label">Plate Number</div>
                            <div class="tricycle-value">${formatValue(tricycle.PlateNumber || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Body Number</div>
                            <div class="tricycle-value">${formatValue(tricycle.BodyNumber || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Make & Model</div>
                            <div class="tricycle-value">${formatValue(tricycle.MakeModel || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Driver's License</div>
                            <div class="tricycle-value">${formatValue(tricycle.LicenseNumber || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Year Model</div>
                            <div class="tricycle-value">${formatValue(tricycle.YearModel || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Color</div>
                            <div class="tricycle-value">${formatValue(tricycle.Color || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Engine Number</div>
                            <div class="tricycle-value">${formatValue(tricycle.EngineNumber || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Chassis Number</div>
                            <div class="tricycle-value">${formatValue(tricycle.ChassisNumber || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Seating Capacity</div>
                            <div class="tricycle-value">${formatValue(tricycle.SeatingCapacity || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Status</div>
                            <div class="tricycle-value">${formatValue(tricycle.Status || 'Not Specified')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Vehicle Type</div>
                            <div class="tricycle-value">${formatValue(tricycle.VehicleType || 'Tricycle')}</div>
                        </div>
                        <div class="tricycle-item">
                            <div class="tricycle-label">Last Updated</div>
                            <div class="tricycle-value">${lastUpdated}</div>
                        </div>
                    </div>
                    
                    ${rejectionHtml}
                    
                    ${!canApprove && tricycleStatus != 'Approved' ? 
                        '<div style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 6px; padding: 15px; margin-top: 20px;"><i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i> Note: Driver must be active and documents approved before tricycle can be approved.</div>' : ''}
                </div>
            `;
            
            document.getElementById('tricycleDetailsContent').innerHTML = content;
            
            const approveBtn = document.getElementById('approveTricycleBtn');
            const rejectBtn = document.getElementById('rejectTricycleBtn');
            
            if (approveBtn && rejectBtn) {
                if (tricycleStatus == 'Approved') {
                    approveBtn.style.display = 'none';
                    rejectBtn.style.display = 'none';
                } else {
                    approveBtn.style.display = canApprove ? 'inline-block' : 'none';
                    rejectBtn.style.display = 'inline-block';
                }
            }
            
            showModal('tricycleModal');
        }

        function showApproveConfirmation(driverId, name) {
            document.getElementById('confirmationTitle').textContent = 'Approve Application';
            document.getElementById('confirmationText').textContent = `Approve ${name}'s application?`;
            currentDriverId = driverId;
            currentDriverName = name;
            currentAction = 'approve';
            
            document.getElementById('confirmActionBtn').onclick = function() {
                submitAction('approve', currentDriverId, currentDriverName);
                closeModal('confirmationModal');
            };
            
            showModal('confirmationModal');
        }

        function approveFromModal() {
            if (currentAppData) {
                showApproveConfirmation(currentAppData.driver_id, currentAppData.name);
            }
        }

        function approveTricycleFromModal() {
            if (currentDriverData) {
                document.getElementById('tricycleConfirmTitle').textContent = 'Approve Tricycle';
                document.getElementById('tricycleConfirmText').textContent = `Approve tricycle for ${currentDriverData.name}?`;
                currentAction = 'approve_tricycle';
                
                document.getElementById('confirmTricycleBtn').onclick = function() {
                    submitAction('approve_tricycle', currentDriverData.driver_id, currentDriverData.name);
                    closeModal('tricycleConfirmModal');
                };
                
                showModal('tricycleConfirmModal');
            }
        }

        function showRejectModal(driverId, name, documents, preSelectedDocs = []) {
            currentDriverId = driverId;
            currentDriverName = name;
            
            document.getElementById('rejectionReason').value = '';
            document.getElementById('selectAllDocs').checked = false;
            
            populateDocumentCheckboxes(documents, preSelectedDocs);
            
            const errorMsg = document.getElementById('rejectError');
            if (errorMsg) errorMsg.style.display = 'none';
            
            showModal('rejectModal');
        }

        function showRejectModalFromDetails() {
            if (currentAppData) {
                const preSelectedDocs = Object.keys(currentAppData.rejected_document_urls || {});
                showRejectModal(currentAppData.driver_id, currentAppData.name, currentAppData.documents, preSelectedDocs);
            }
        }

        function validateAndSubmitRejection() {
            const reason = document.getElementById('rejectionReason').value.trim();
            const selectedDocs = Array.from(document.querySelectorAll('#documentSelection input[type="checkbox"]:checked'))
                .map(cb => cb.value);
            
            const errorMsg = document.getElementById('rejectError');
            
            if (reason === '' || selectedDocs.length === 0) {
                errorMsg.style.display = 'flex';
                errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please provide a reason and select at least one document.';
                return;
            }
            
            errorMsg.style.display = 'none';
            closeModal('rejectModal');
            
            document.getElementById('rejectedDocumentsInput').value = JSON.stringify(selectedDocs);
            
            submitAction('reject', currentDriverId, currentDriverName, reason);
        }

        function showRejectTricycleModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('tricycleRejectionReason').value = '';
            const errorMsg = document.getElementById('tricycleRejectError');
            if (errorMsg) errorMsg.style.display = 'none';
            showModal('rejectTricycleModal');
        }

        function showRejectTricycleModalFromDetails() {
            if (currentDriverData) {
                showRejectTricycleModal(currentDriverData.driver_id, currentDriverData.name);
            }
        }

        function validateTricycleRejectionReason() {
            const reason = document.getElementById('tricycleRejectionReason').value.trim();
            const errorMsg = document.getElementById('tricycleRejectError');
            errorMsg.style.display = reason === '' ? 'flex' : 'none';
        }

        function submitTricycleRejection() {
            const reason = document.getElementById('tricycleRejectionReason').value.trim();
            const errorMsg = document.getElementById('tricycleRejectError');
            
            if (reason === '') {
                errorMsg.style.display = 'flex';
                return;
            }
            
            errorMsg.style.display = 'none';
            closeModal('rejectTricycleModal');
            submitAction('reject_tricycle', currentDriverId, currentDriverName, reason);
        }

        function showDeactivateModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('deactivationReason').value = '';
            const errorMsg = document.getElementById('deactivateError');
            if (errorMsg) errorMsg.style.display = 'none';
            showModal('deactivateModal');
        }

        function validateDeactivationReason() {
            const reason = document.getElementById('deactivationReason').value.trim();
            const errorMsg = document.getElementById('deactivateError');
            errorMsg.style.display = reason === '' ? 'flex' : 'none';
        }

        function validateAndSubmitDeactivation() {
            const reason = document.getElementById('deactivationReason').value.trim();
            const errorMsg = document.getElementById('deactivateError');
            
            if (reason === '') {
                errorMsg.style.display = 'flex';
                return;
            }
            
            errorMsg.style.display = 'none';
            closeModal('deactivateModal');
            document.getElementById('deactivationReasonInput').value = reason;
            submitAction('deactivate', currentDriverId, currentDriverName);
        }

        function showSuspendModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('suspendDuration').value = '7';
            document.getElementById('customDateContainer').style.display = 'none';
            document.getElementById('suspendReason').value = '';
            
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 7);
            updateSuspendDateDisplay(defaultDate);
            document.getElementById('suspendUntilValue').value = defaultDate.toISOString();
            
            const errorMsg = document.getElementById('suspendError');
            if (errorMsg) errorMsg.style.display = 'none';
            
            const reasonError = document.getElementById('suspendReasonError');
            if (reasonError) reasonError.style.display = 'none';
            
            showModal('suspendModal');
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
                    errorMsg.style.display = 'flex';
                    errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please select a custom date.';
                    return false;
                }
                
                const selectedDate = new Date(customDate);
                const now = new Date();
                
                if (selectedDate <= now) {
                    errorMsg.style.display = 'flex';
                    errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Suspension date must be in the future.';
                    return false;
                }
                
                updateSuspendDateDisplay(selectedDate);
                document.getElementById('suspendUntilValue').value = selectedDate.toISOString();
                errorMsg.style.display = 'none';
                return true;
            }
            return true;
        }

        function validateSuspendReason() {
            const reason = document.getElementById('suspendReason').value.trim();
            const errorMsg = document.getElementById('suspendReasonError');
            errorMsg.style.display = reason === '' ? 'flex' : 'none';
            return reason !== '';
        }

        function updateSuspendDateDisplay(date) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const formattedDate = `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
            document.getElementById('suspendUntilText').textContent = formattedDate;
        }

        function validateAndSubmitSuspension() {
            const reason = document.getElementById('suspendReason').value.trim();
            const reasonError = document.getElementById('suspendReasonError');
            
            if (reason === '') {
                reasonError.style.display = 'flex';
                return;
            }
            
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
            
            reasonError.style.display = 'none';
            closeModal('suspendModal');
            document.getElementById('suspendUntilInput').value = suspendUntil;
            document.getElementById('suspendReasonInput').value = reason;
            submitAction('suspend', currentDriverId, currentDriverName);
        }

        function showReactivationModal(driverId, name) {
            currentDriverId = driverId;
            currentDriverName = name;
            document.getElementById('reactivationReason').value = '';
            const errorMsg = document.getElementById('reactivationError');
            if (errorMsg) errorMsg.style.display = 'none';
            showModal('reactivationModal');
        }

        function validateReactivationReason() {
            const reason = document.getElementById('reactivationReason').value.trim();
            const errorMsg = document.getElementById('reactivationError');
            errorMsg.style.display = reason === '' ? 'flex' : 'none';
        }

        function validateAndSubmitReactivation() {
            const reason = document.getElementById('reactivationReason').value.trim();
            const errorMsg = document.getElementById('reactivationError');
            
            if (reason === '') {
                errorMsg.style.display = 'flex';
                return;
            }
            
            errorMsg.style.display = 'none';
            closeModal('reactivationModal');
            document.getElementById('reactivationReasonInput').value = reason;
            submitAction('reactivate', currentDriverId, currentDriverName);
        }

        function submitAction(action, driverId, driverName, reason = '') {
            const form = document.getElementById('actionForm');
            document.getElementById('actionInput').value = action;
            document.getElementById('driverIdInput').value = driverId;
            document.getElementById('driverNameInput').value = driverName;
            
            if (reason) {
                if (action === 'reject' || action === 'reject_tricycle') {
                    document.getElementById('rejectionReasonInput').value = reason;
                } else if (action === 'deactivate') {
                    document.getElementById('deactivationReasonInput').value = reason;
                } else if (action === 'suspend') {
                    document.getElementById('suspendReasonInput').value = reason;
                } else if (action === 'reactivate') {
                    document.getElementById('reactivationReasonInput').value = reason;
                }
            }
            
            form.submit();
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        };
    </script>
</body>
</html>