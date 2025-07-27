<?php
// Admin Dashboard
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'];
$posts = get_all_posts($pdo);
$subscribers = $pdo->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Benata Matrix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Orbitron:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/style.css">
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
    </style>
</head>
<body class="font-mono">
    <div class="admin-header flex justify-between items-center">
        <h1 class="text-xl retro-heading">ADMIN DASHBOARD</h1>
        <div>
            Logged in as <strong><?= escape($admin_username) ?></strong>
            | <a href="logout.php" class="retro-link">Logout</a>
        </div>
    </div>
    <div class="flex">
        <nav class="admin-sidebar">
            <a href="index.php" class="nav-link active">Dashboard</a>
            <a href="create_post.php" class="nav-link">Create Post</a>
            <a href="manage_subscribers.php" class="nav-link">Manage Subscribers</a>
            <!-- Add more links for other admin sections -->
        </nav>
        <main class="admin-content">
            <h2 class="text-2xl mb-4 retro-heading">> OVERVIEW</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="content-section p-4">
                    <h3 class="text-xl mb-2">Recent Posts</h3>
                    <ul class="terminal-text">
                        <?php foreach (array_slice($posts, 0, 5) as $post): ?>
                            <li class="mb-2">
                                <a href="../post.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" class="retro-link"><?= escape($post['title']) ?></a>
                                (<a href="edit_post.php?id=<?= $post['id'] ?>" class="text-yellow-400">Edit</a>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="content-section p-4">
                    <h3 class="text-xl mb-2">Subscriber Stats</h3>
                    <p class="terminal-text">Total Subscribers: <strong><?= count($subscribers) ?></strong></p>
                    <h4 class="mt-4 mb-2">Latest Subscribers:</h4>
                    <ul class="terminal-text">
                        <?php foreach (array_slice($subscribers, 0, 5) as $subscriber): ?>
                            <li><?= escape($subscriber['email']) ?> (<?= date('Y-m-d', strtotime($subscriber['subscribed_at'])) ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="content-section p-4">
                <h3 class="text-xl mb-2">All Posts</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td><?= escape($post['title']) ?></td>
                            <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                            <td>
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="text-yellow-400 mr-2">Edit</a>
                                <a href="delete_post.php?id=<?= $post['id'] ?>" class="text-red-400" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
