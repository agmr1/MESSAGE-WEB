<?php
require_once 'db.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App - <?php echo htmlspecialchars($current_name); ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="chat-container">
        <div class="sidebar">
            <div class="user-header">
                <h2><?php echo htmlspecialchars($current_name); ?></h2>
                <p>@<?php echo htmlspecialchars($current_username); ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="search-container">
                <input type="text" id="search-user" placeholder="Cari pengguna...">
                <button id="search-btn">Cari</button>
            </div>
            
            <div class="chat-list" id="chat-list">
                <!-- Daftar chat akan dimuat di sini -->
            </div>
        </div>
        
        <div class="chat-area">
            <div class="chat-header">
                <h2 id="chat-with-name">Pilih percakapan</h2>
            </div>
            
            <div class="messages-container" id="messages-container">
                <!-- Pesan akan dimuat di sini -->
            </div>
            
            <div class="message-input-container">
                <input type="text" id="message-input" placeholder="Ketik pesan..." disabled>
                <button id="send-btn" disabled>Kirim</button>
                <input type="hidden" id="receiver-id">
            </div>
        </div>
    </div>

    <div class="sidebar">
        <!-- ... bagian user header ... -->
        
        <div class="inbox-header">
            <h3>Inbox</h3>
            <button id="refresh-inbox">⟳ Refresh</button>
        </div>
        
        <div class="inbox-list" id="inbox-list">
            <!-- Daftar pesan masuk akan muncul di sini -->
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Load chat list
        loadChatList();
        
        // Search user
        $('#search-btn').click(function() {
            const searchTerm = $('#search-user').val().trim();
            if (searchTerm) {
                searchUser(searchTerm);
            }
        });
        
        // Send message
        $('#send-btn').click(function() {
            sendMessage();
        });
        
        // Send message on Enter key
        $('#message-input').keypress(function(e) {
            if (e.which === 13) { // Enter key
                sendMessage();
            }
        });
        
        // Auto refresh messages every 3 seconds
        setInterval(function() {
            if ($('#receiver-id').val()) {
                loadMessages($('#receiver-id').val());
            }
        }, 3000);
    });
    
    function loadChatList() {
        $.get('get_chat_list.php', function(data) {
            $('#chat-list').html(data);
        });
    }
    
    function searchUser(searchTerm) {
        $.get('search_user.php', { search: searchTerm }, function(data) {
            $('#chat-list').html(data);
        });
    }
    
    function startChat(userId, userName) {
        $('#chat-with-name').text('Chat dengan ' + userName);
        $('#receiver-id').val(userId);
        $('#message-input').prop('disabled', false);
        $('#send-btn').prop('disabled', false);
        loadMessages(userId);
    }
    
    function loadMessages(receiverId) {
        $.get('get_messages.php', { receiver_id: receiverId }, function(data) {
            $('#messages-container').html(data);
            scrollToBottom();
        });
    }
    
    function sendMessage() {
        const message = $('#message-input').val().trim();
        const receiverId = $('#receiver-id').val();
        
        if (message && receiverId) {
            $.post('send_message.php', {
                receiver_id: receiverId,
                message: message
            }, function() {
                $('#message-input').val('');
                loadMessages(receiverId);
            });
        }
    }
    
    function scrollToBottom() {
        const container = $('#messages-container');
        container.scrollTop(container[0].scrollHeight);
    }
    // Fungsi load inbox
    function loadInbox() {
        $.get('get_inbox.php', function(data) {
            $('#inbox-list').html(data);
        });
    }

    // Refresh manual
    $('#refresh-inbox').click(function() {
        loadInbox();
    });

    // Auto-refresh setiap 5 detik
    setInterval(loadInbox, 5000);

    // Panggil pertama kali
    loadInbox();
        // Fungsi untuk memuat daftar percakapan
    function loadConversations() {
        $.get('get_chat_list.php', function(data) {
            $('#chat-list').html(data);
        });
    }

    // Auto-refresh setiap 3 detik
    setInterval(loadConversations, 3000);

    // Panggil pertama kali
    loadConversations();


    </script>

</body>
</html>