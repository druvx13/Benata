<?php
// Manage Blog Subscribers
require '../includes/db.php';
require '../includes/functions.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Handle deletion of a subscriber
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $del_stmt = $pdo->prepare("DELETE FROM subscribers WHERE id = ?");
    if ($del_stmt->execute([$delete_id])) {
        $message = "Subscriber deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Error deleting subscriber.";
        $message_type = "error";
    }
}

// Fetch all subscribers
$subscribers = $pdo->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC")->fetchAll();
$total_subscribers = count($subscribers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscribers - Admin - Benata Matrix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Orbitron:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background-color: #000; color: #00ff41; }
        .admin-header { padding: 1rem 2rem; background: #002200; border-bottom: 1px solid #00ff41; }
        .admin-sidebar { width: 250px; background: #001100; border-right: 1px solid #00ff41; }
        .admin-content { flex: 1; padding: 2rem; }
        .nav-link { display: block; padding: 1rem; color: #00ff41; text-decoration: none; border-bottom: 1px solid #003300; }
        .nav-link:hover, .nav-link.active { background: #003300; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #00aa00; padding: 0.5rem; text-align: left; }
        th { background: #002200; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #dff0d8; background-color: #3c763d; border-color: #d6e9c6; }
        .alert-error { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
    </style>
</head>
<body class="font-mono">
    <div class="admin-header flex justify-between items-center">
        <h1 class="text-xl retro-heading">MANAGE SUBSCRIBERS</h1>
        <div>
            <a href="index.php" class="retro-link">Dashboard</a>
            | <a href="../index.php" class="retro-link">View Blog</a>
        </div>
    </div>
    <div class="flex">
        <nav class="admin-sidebar">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="create_post.php" class="nav-link">Create Post</a>
            <a href="manage_subscribers.php" class="nav-link active">Manage Subscribers</a>
        </nav>
        <main class="admin-content">
            <h2 class="text-2xl mb-4 retro-heading">> SUBSCRIBER LIST</h2>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $message_type ?> terminal-text"><?= escape($message) ?></div>
            <?php endif; ?>
            
            <p class="terminal-text mb-4">Total Subscribers: <strong><?= $total_subscribers ?></strong></p>

            <div class="content-section p-4">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Subscribed On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscribers as $subscriber): ?>
                        <tr>
                            <td><?= $subscriber['id'] ?></td>
                            <td><?= escape($subscriber['email']) ?></td>
                            <td><?= date('Y-m-d H:i:s', strtotime($subscriber['subscribed_at'])) ?></td>
                            <td>
                                <a href="?delete_id=<?= $subscriber['id'] ?>" class="text-red-400" onclick="return confirm('Are you sure you want to delete this subscriber?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($subscribers)): ?>
                            <tr><td colspan="4" class="text-center">No subscribers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
