<?php
// Delete a Blog Post
require '../includes/db.php';
require '../includes/functions.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Verify post exists
$stmt = $pdo->prepare("SELECT title FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User confirmed deletion
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        try {
            $pdo->beginTransaction();
            
            // Delete category associations first (due to foreign key constraints)
            $del_cats_stmt = $pdo->prepare("DELETE FROM post_categories WHERE post_id = ?");
            $del_cats_stmt->execute([$id]);
            
            // Delete the post
            $del_post_stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $del_post_stmt->execute([$id]);
            
            $pdo->commit();
            header('Location: index.php?message=' . urlencode('Post deleted successfully.'));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error deleting post: " . $e->getMessage());
        }
    } else {
        // User cancelled
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post - Admin - Benata Matrix</title>
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
        .confirm-box { max-width: 500px; margin: 50px auto; padding: 2rem; border: 2px solid #ff5555; background: rgba(30, 0, 0, 0.8); text-align: center; }
        button { padding: 0.75rem 1.5rem; margin: 0 0.5rem; background: #330000; border: 1px solid #ff5555; color: #ff5555; cursor: pointer; }
        button:hover { background: #550000; }
        .cancel-btn { border-color: #00ff41; color: #00ff41; background: #003300; }
        .cancel-btn:hover { background: #005500; }
    </style>
</head>
<body class="font-mono">
    <div class="admin-header flex justify-between items-center">
        <h1 class="text-xl retro-heading">DELETE POST</h1>
        <div>
            <a href="index.php" class="retro-link">Dashboard</a>
        </div>
    </div>
    <div class="flex">
        <nav class="admin-sidebar">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="create_post.php" class="nav-link">Create Post</a>
            <a href="manage_subscribers.php" class="nav-link">Manage Subscribers</a>
        </nav>
        <main class="admin-content">
            <div class="confirm-box terminal-text">
                <h2 class="text-2xl mb-4 retro-heading">CONFIRM DELETION</h2>
                <p class="mb-6">Are you sure you want to delete the post titled:</p>
                <p class="text-xl mb-6"><strong><?= escape($post['title']) ?></strong>?</p>
                <p class="mb-6 text-red-400">This action cannot be undone.</p>
                
                <form method="post">
                    <input type="hidden" name="confirm" value="yes">
                    <button type="submit">YES, DELETE IT</button>
                    <a href="index.php" class="cancel-btn">CANCEL</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
