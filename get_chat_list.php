<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Ambil daftar user yang pernah mengirim/menerima pesan
$query = "
    SELECT u.id, u.name, u.username, MAX(m.created_at) as last_message_time
    FROM users u
    JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?
    GROUP BY u.id
    ORDER BY last_message_time DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$current_user_id, $current_user_id, $current_user_id]);
$users = $stmt->fetchAll();

if (empty($users)) {
    echo '<div class="no-chats">Belum ada percakapan</div>';
} else {
    foreach ($users as $user) {
        echo '<div class="chat-item" onclick="startChat(' . $user['id'] . ', \'' . htmlspecialchars($user['name']) . '\')">';
        echo '<h3>' . htmlspecialchars($user['name']) . '</h3>';
        echo '<p>@' . htmlspecialchars($user['username']) . '</p>';
        echo '</div>';
    }
}
?>