<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Not authenticated");
}

$user_id = $_SESSION['user_id'];

// Ambil semua pesan masuk terbaru per pengirim
$query = "
    SELECT 
        u.id as sender_id,
        u.name as sender_name,
        m.message,
        m.created_at,
        SUM(IF(m.is_read = 0, 1, 0)) as unread_count
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.receiver_id = ?
    GROUP BY m.sender_id
    ORDER BY m.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);

while ($row = $stmt->fetch()) {
    $unread_class = $row['unread_count'] > 0 ? 'unread' : '';
    $time = date('H:i', strtotime($row['created_at']));
    
    echo '<div class="inbox-item ' . $unread_class . '" onclick="startChat(' . $row['sender_id'] . ', \'' . htmlspecialchars($row['sender_name']) . '\')">';
    echo '<div class="inbox-sender">' . htmlspecialchars($row['sender_name']) . ' • ' . $time . '</div>';
    echo '<div class="inbox-preview">' . htmlspecialchars(substr($row['message'], 0, 50)) . '...</div>';
    if ($row['unread_count'] > 0) {
        echo '<span class="unread-badge">' . $row['unread_count'] . '</span>';
    }
    echo '</div>';
}
?>