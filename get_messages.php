<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    exit;
}

$current_user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

// Ambil pesan antara current user dan receiver
$query = "
    SELECT m.*, u.name as sender_name 
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?) 
    OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$current_user_id, $receiver_id, $receiver_id, $current_user_id]);
$messages = $stmt->fetchAll();

// Tandai pesan sebagai sudah dibaca
$pdo->prepare("UPDATE messages SET is_read = TRUE WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE")
    ->execute([$current_user_id, $receiver_id]);

foreach ($messages as $message) {
    $messageClass = $message['sender_id'] == $current_user_id ? 'sent' : 'received';
    $time = date('H:i', strtotime($message['created_at']));
    
    echo '<div class="message ' . $messageClass . '">';
    if ($messageClass === 'received') {
        echo '<div class="sender-name">' . htmlspecialchars($message['sender_name']) . '</div>';
    }
    echo '<div class="message-content">' . htmlspecialchars($message['message']) . '</div>';
    echo '<div class="message-time">' . $time . '</div>';
    echo '</div>';
}

if (empty($messages)) {
    echo '<div class="no-messages">Mulai percakapan dengan mengirim pesan pertama</div>';
}
?>