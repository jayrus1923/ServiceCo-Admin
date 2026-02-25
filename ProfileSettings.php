<?php
session_start();

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

$isLoggedIn = false;
$userUid = '';

if (isset($_SESSION['user_uid']) && !empty($_SESSION['user_uid'])) {
    $isLoggedIn = true;
    $userUid = $_SESSION['user_uid'];
} elseif (isset($_COOKIE['user_uid']) && !empty($_COOKIE['user_uid'])) {
    $isLoggedIn = true;
    $userUid = $_COOKIE['user_uid'];
    $_SESSION['user_uid'] = $userUid;
    $_SESSION['user_email'] = isset($_COOKIE['user_email']) ? urldecode($_COOKIE['user_email']) : '';
}

if (!$isLoggedIn) {
    header('Location: Login.php');
    exit;
}

$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : (isset($_COOKIE['user_email']) ? urldecode($_COOKIE['user_email']) : '');

include 'Sidebar.php';
include 'NavigationBar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | Tricycle Booking Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .profile-content {
            margin-top: 70px;
            padding: 25px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        .sidebar.collapsed ~ .profile-content,
        .sidebar.auto-hide ~ .profile-content {
            margin-left: 70px !important;
            width: calc(100% - 70px) !important;
        }

        .sidebar.auto-hide:hover ~ .profile-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        .page-header {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .page-title-large {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        @media (max-width: 1024px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
        }

        .settings-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            height: fit-content;
        }

        .settings-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-card h3 i {
            color: #347433;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .profile-pic-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #347433;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: bold;
            flex-shrink: 0;
            border: 3px solid #347433;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-pic-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info h2 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .profile-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .profile-info p i {
            color: #94a3b8;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #475569;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            background-color: white;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #347433;
            box-shadow: 0 0 0 3px rgba(52, 116, 51, 0.1);
        }

        .form-group input:disabled {
            background-color: #f1f5f9;
            color: #94a3b8;
        }

        .phone-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            background-color: white;
            transition: all 0.3s;
        }

        .phone-input:focus {
            outline: none;
            border-color: #347433;
            box-shadow: 0 0 0 3px rgba(52, 116, 51, 0.1);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            background-color: white;
            transition: all 0.3s;
        }

        .password-wrapper input:focus {
            outline: none;
            border-color: #347433;
            box-shadow: 0 0 0 3px rgba(52, 116, 51, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #347433;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary {
            background-color: #347433;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background-color: #2c622b;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #e2e8f0;
        }

        .btn-change-pic {
            background-color: #f8fafc;
            color: #475569;
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .btn-change-pic:hover {
            background-color: #e2e8f0;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 400px;
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            padding: 20px;
            background-color: #347433;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            padding: 25px;
            text-align: center;
        }

        .preview-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #347433;
            margin: 0 auto 15px;
            display: none;
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: inline-block;
            padding: 12px 24px;
            background-color: #347433;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            margin: 15px 0;
            transition: all 0.2s;
        }

        .file-label:hover {
            background-color: #2c622b;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons .btn {
            flex: 1;
            justify-content: center;
        }

        .btn-danger {
            background-color: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .notification-container {
            position: fixed;
            top: 90px;
            right: 25px;
            z-index: 999;
        }

        .notification {
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
        }

        .notification.show {
            display: flex;
        }

        .notification.success {
            background-color: #347433;
            color: white;
        }

        .notification.error {
            background-color: #dc2626;
            color: white;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #347433;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .profile-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 20px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .profile-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-content">
        <div class="page-header">
            <h1 class="page-title-large">Profile Settings</h1>
            <p class="page-subtitle">Manage your personal information and password</p>
        </div>

        <div class="profile-layout">
            <div class="settings-card">
                <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
                
                <div class="profile-header">
                    <div class="profile-pic-large" id="profilePicLarge">
                        <span id="largeInitials">AU</span>
                        <img id="largeImage" src="" alt="" style="display: none;">
                    </div>
                    <div class="profile-info">
                        <h2 id="displayName">Loading...</h2>
                        <p><i class="fas fa-user-tag"></i> <span id="displayPosition">System Administrator</span></p>
                        <p><i class="fas fa-clock"></i> <span id="displayLastLogin">Just now</span></p>
                        <button class="btn-change-pic" onclick="openProfilePicModal()">
                            <i class="fas fa-camera"></i> Change Picture
                        </button>
                    </div>
                </div>

                <form id="profileForm">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="firstName" value="">
                    </div>
                    
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="lastName" value="">
                    </div>

                    <div class="form-group">
                        <label>E-mail Address</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="tel" id="phone" class="phone-input" value="" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>

                    <div class="button-group">
                        <button type="button" class="btn btn-secondary" onclick="showCancelModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <div class="settings-card">
                <h3><i class="fas fa-key"></i> Password Settings</h3>
                
                <div class="form-group">
                    <label>Current Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="currentPassword" placeholder="Enter current password">
                        <button type="button" class="toggle-password" onclick="togglePassword('currentPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="newPassword" placeholder="Enter new password">
                        <button type="button" class="toggle-password" onclick="togglePassword('newPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" placeholder="Confirm new password">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-secondary" onclick="clearPasswordFields()">
                        Clear
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updatePassword()" id="updatePasswordBtn">
                        Update Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="picModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Profile Picture</h3>
                <button class="modal-close" onclick="closeModal('picModal')">&times;</button>
            </div>
            <div class="modal-body">
                <img src="" alt="Preview" class="preview-image" id="imagePreview">
                <div class="profile-pic-large" style="margin: 0 auto;">
                    <span id="modalInitials">AU</span>
                    <img id="modalImage" src="" alt="" style="display: none;">
                </div>
                <input type="file" id="profileImageInput" class="file-input" accept="image/*">
                <label for="profileImageInput" class="file-label">
                    Choose New Photo
                </label>
                <div class="modal-buttons">
                    <button class="btn btn-secondary" onclick="closeModal('picModal')">Cancel</button>
                    <button class="btn btn-primary" onclick="saveProfilePicture()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="cancelModal">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#dc2626">
                <h3>Discard Changes</h3>
                <button class="modal-close" onclick="closeModal('cancelModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-icon" style="font-size: 48px; margin-bottom: 20px;">⚠️</div>
                <p style="margin-bottom: 20px;">Discard all changes?</p>
                <div class="modal-buttons">
                    <button class="btn btn-secondary" onclick="closeModal('cancelModal')">No</button>
                    <button class="btn btn-danger" onclick="discardChanges()">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <script>
        const db = firebase.firestore();
        const storage = firebase.storage();
        const auth = firebase.auth();
        const userUid = '<?php echo $userUid; ?>';

        function showNotification(msg, type) {
            const div = document.createElement('div');
            div.className = `notification ${type}`;
            div.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${msg}`;
            document.getElementById('notificationContainer').appendChild(div);
            setTimeout(() => div.classList.add('show'), 10);
            setTimeout(() => {
                div.classList.remove('show');
                setTimeout(() => div.remove(), 300);
            }, 3000);
        }

        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function adjustContentPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.profile-content');
            if (!sidebar || !content) return;
            const isAutoHide = sidebar.classList.contains('auto-hide');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const isHovered = sidebar.matches(':hover') && isAutoHide;
            let w = 240;
            if (isAutoHide && !isHovered) w = 70;
            else if (isCollapsed) w = 70;
            content.style.marginLeft = w + 'px';
            content.style.width = `calc(100% - ${w}px)`;
        }

        async function loadUserData() {
            try {
                document.getElementById('loadingOverlay').classList.add('active');
                const doc = await db.collection('users').doc(userUid).get();
                if (doc.exists) {
                    const d = doc.data();
                    document.getElementById('firstName').value = d.firstName || '';
                    document.getElementById('lastName').value = d.lastName || '';
                    document.getElementById('phone').value = d.phone || '';
                    
                    const fn = d.firstName || 'Admin';
                    const ln = d.lastName || 'User';
                    const full = `${fn} ${ln}`.trim();
                    document.getElementById('displayName').textContent = full;
                    
                    const inits = fn.charAt(0).toUpperCase() + ln.charAt(0).toUpperCase();
                    document.getElementById('largeInitials').textContent = inits;
                    document.getElementById('modalInitials').textContent = inits;
                    
                    if (d.profilePicUrl) {
                        document.getElementById('largeImage').src = d.profilePicUrl;
                        document.getElementById('largeImage').style.display = 'block';
                        document.getElementById('largeInitials').style.display = 'none';
                        document.getElementById('modalImage').src = d.profilePicUrl;
                        document.getElementById('modalImage').style.display = 'block';
                        document.getElementById('modalInitials').style.display = 'none';
                    }
                    if (window.updateNavbarName) window.updateNavbarName(full);
                    if (window.updateNavbarProfile && d.profilePicUrl) window.updateNavbarProfile(d.profilePicUrl);
                }
            } catch (e) {
                showNotification('Error loading data', 'error');
            } finally {
                document.getElementById('loadingOverlay').classList.remove('active');
            }
        }

        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const first = document.getElementById('firstName').value.trim();
            const last = document.getElementById('lastName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            if (!first || !last) return showNotification('First and last name required', 'error');
            if (phone && !/^\d{11}$/.test(phone)) return showNotification('Mobile number must be 11 digits', 'error');
            
            document.getElementById('loadingOverlay').classList.add('active');
            try {
                await db.collection('users').doc(userUid).update({ firstName: first, lastName: last, phone: phone });
                const full = `${first} ${last}`.trim();
                document.getElementById('displayName').textContent = full;
                const inits = first.charAt(0).toUpperCase() + last.charAt(0).toUpperCase();
                document.getElementById('largeInitials').textContent = inits;
                document.getElementById('modalInitials').textContent = inits;
                if (window.updateNavbarName) window.updateNavbarName(full);
                showNotification('Profile saved!', 'success');
            } catch (e) {
                showNotification('Error saving profile', 'error');
            } finally {
                document.getElementById('loadingOverlay').classList.remove('active');
            }
        });

        document.getElementById('profileImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        async function saveProfilePicture() {
            const file = document.getElementById('profileImageInput').files[0];
            if (!file) { closeModal('picModal'); return; }
            if (!file.type.match('image.*')) { showNotification('Please select an image', 'error'); return; }
            if (file.size > 2 * 1024 * 1024) { showNotification('File size must be less than 2MB', 'error'); return; }
            
            document.getElementById('loadingOverlay').classList.add('active');
            try {
                const fileName = `${Date.now()}_${file.name}`;
                const ref = storage.ref().child(`profile_pictures/${userUid}/${fileName}`);
                await ref.put(file);
                const url = await ref.getDownloadURL();
                await db.collection('users').doc(userUid).update({ profilePicUrl: url });
                
                document.getElementById('largeImage').src = url;
                document.getElementById('largeImage').style.display = 'block';
                document.getElementById('largeInitials').style.display = 'none';
                document.getElementById('modalImage').src = url;
                document.getElementById('modalImage').style.display = 'block';
                document.getElementById('modalInitials').style.display = 'none';
                
                if (window.updateNavbarProfile) window.updateNavbarProfile(url);
                showNotification('Picture updated!', 'success');
                closeModal('picModal');
            } catch (e) {
                showNotification('Error uploading picture', 'error');
            } finally {
                document.getElementById('loadingOverlay').classList.remove('active');
                document.getElementById('profileImageInput').value = '';
                document.getElementById('imagePreview').style.display = 'none';
            }
        }

        async function updatePassword() {
            const current = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (!current || !newPass || !confirm) {
                return showNotification('Please fill all fields', 'error');
            }

            if (newPass !== confirm) {
                return showNotification('Passwords do not match', 'error');
            }

            if (newPass.length < 8) {
                return showNotification('Password must be at least 8 characters', 'error');
            }

            const btn = document.getElementById('updatePasswordBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            document.getElementById('loadingOverlay').classList.add('active');

            try {
                const user = firebase.auth().currentUser;

                if (!user) {
                    throw new Error("User not logged in");
                }

                const credential = firebase.auth.EmailAuthProvider.credential(
                    user.email,
                    current
                );

                await user.reauthenticateWithCredential(credential);
                await user.updatePassword(newPass);

                showNotification('Password updated successfully!', 'success');

                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';

            } catch (error) {
                let msg = 'Error updating password';

                if (
                    error.code === 'auth/wrong-password' ||
                    error.code === 'auth/invalid-credential'
                ) {
                    msg = 'Current password is incorrect';
                } 
                else if (error.code === 'auth/weak-password') {
                    msg = 'New password is too weak';
                } 
                else if (error.code === 'auth/requires-recent-login') {
                    msg = 'Please login again';
                } 
                else {
                    msg = error.message;
                }

                showNotification(msg, 'error');
                console.error(error);

            } finally {
                document.getElementById('loadingOverlay').classList.remove('active');
                btn.disabled = false;
                btn.innerHTML = 'Update Password';
            }
        }

        function openProfilePicModal() { document.getElementById('picModal').classList.add('active'); }
        function closeModal(id) { 
            document.getElementById(id).classList.remove('active');
            if (id === 'picModal') {
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('profileImageInput').value = '';
            }
        }
        function showCancelModal() { document.getElementById('cancelModal').classList.add('active'); }
        async function discardChanges() { 
            closeModal('cancelModal');
            await loadUserData();
            showNotification('Changes discarded', 'success');
        }
        function clearPasswordFields() {
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            adjustContentPosition();
            loadUserData();
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.addEventListener('mouseenter', () => setTimeout(adjustContentPosition, 50));
                sidebar.addEventListener('mouseleave', () => setTimeout(adjustContentPosition, 50));
            }
            document.addEventListener('sidebarToggled', adjustContentPosition);
            document.addEventListener('sidebarModeChanged', adjustContentPosition);
        });
    </script>
</body>
</html>