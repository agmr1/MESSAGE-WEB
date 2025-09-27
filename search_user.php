<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['search'])) {
    exit;
}

$current_user_id = $_SESSION['user_id'];
$searchTerm = '%' . $_GET['search'] . '%';

// Cari user berdasarkan username atau name
$query = "
    SELECT id, name, username 
    FROM users 
    WHERE (username LIKE ? OR name LIKE ?) AND id != ?
    LIMIT 10
";

$stmt = $pdo->prepare($query);
$stmt->execute([$searchTerm, $searchTerm, $current_user_id]);
$users = $stmt->fetchAll();

if (empty($users)) {
    echo '<div class="no-users">Tidak ada pengguna yang ditemukan</div>';
} else {
    foreach ($users as $user) {
        echo '<div class="chat-item" onclick="startChat(' . $user['id'] . ', \'' . htmlspecialchars($user['name']) . '\')">';
        echo '<h3>' . htmlspecialchars($user['name']) . '</h3>';
        echo '<p>@' . htmlspecialchars($user['username']) . '</p>';
        echo '</div>';
    }
}
?>