<?php
// save_sidebar_state.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['collapsed'])) {
        $_SESSION['sidebar_collapsed'] = $_POST['collapsed'] === 'true';
        echo json_encode(['success' => true]);
    } elseif (isset($_POST['sidebar_mode'])) {
        $_SESSION['sidebar_mode'] = $_POST['sidebar_mode'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
?>