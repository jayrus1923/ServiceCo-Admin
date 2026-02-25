<?php // Firebase Config.php ?>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-storage-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-database-compat.js"></script>

<script>
    const firebaseConfig = {
        apiKey: "AIzaSyBpA5CT6Z1U880I8DgMS3pgkeFuKgQPoyk",
        authDomain: "serviceco-37c60.firebaseapp.com",
        databaseURL: "https://serviceco-37c60-default-rtdb.firebaseio.com",
        projectId: "serviceco-37c60",
        storageBucket: "serviceco-37c60.firebasestorage.app",
        messagingSenderId: "442469956271",
        appId: "1:442469956271:web:0ede934177298b3b74325a"
    };

    firebase.initializeApp(firebaseConfig);
    window.database = firebase.database();
    window.storage = firebase.storage();
    window.firebaseReady = true;
    window.dispatchEvent(new Event('firebaseReady'));
        
</script>