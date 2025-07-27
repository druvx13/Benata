<?php
// Edit Existing Blog Post
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

// Fetch post data
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

// Fetch categories for checkboxes
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Fetch currently assigned categories
$assigned_cats_stmt = $pdo->prepare("SELECT category_id FROM post_categories WHERE post_id = ?");
$assigned_cats_stmt->execute([$id]);
$assigned_category_ids = $assigned_cats_stmt->fetchAll(PDO::FETCH_COLUMN);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $selected_categories = $_POST['categories'] ?? [];

    if (!$title || !$slug || !$content) {
        $message = 'Title, Slug, and Content are required.';
        $message_type = 'error';
    } else {
        // Check for duplicate slug (excluding current post)
        $check_stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
        $check_stmt->execute([$slug, $id]);
        if ($check_stmt->fetch()) {
            $message = 'Another post with this slug already exists. Please choose a unique slug.';
            $message_type = 'error';
        } else {
            try {
                $pdo->beginTransaction();
                
                // Update the post
                $update_stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, excerpt = ? WHERE id = ?");
                $update_stmt->execute([$title, $slug, $content, $excerpt, $id]);

                // Delete old category associations
                $delete_cats_stmt = $pdo->prepare("DELETE FROM post_categories WHERE post_id = ?");
                $delete_cats_stmt->execute([$id]);

                // Assign new categories
                if (!empty($selected_categories)) {
                    $cat_stmt = $pdo->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                    foreach ($selected_categories as $cat_id) {
                        $cat_stmt->execute([$id, (int)$cat_id]);
                    }
                }

                $pdo->commit();
                $message = 'Post updated successfully!';
                $message_type = 'success';
                
                // Refresh post data
                $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->execute([$id]);
                $post = $stmt->fetch();
                
                // Refresh assigned categories
                $assigned_cats_stmt = $pdo->prepare("SELECT category_id FROM post_categories WHERE post_id = ?");
                $assigned_cats_stmt->execute([$id]);
                $assigned_category_ids = $assigned_cats_stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = 'Error updating post: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Admin - Benata Matrix</title>
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
        input, textarea, select { width: 100%; padding: 0.5rem; margin: 0.5rem 0 1rem 0; background: #000; border: 1px solid #00ff41; color: #00ff41; }
        button { padding: 0.75rem 1.5rem; background: #003300; border: 1px solid #00ff41; color: #00ff41; cursor: pointer; }
        button:hover { background: #005500; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #dff0d8; background-color: #3c763d; border-color: #d6e9c6; }
        .alert-error { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
    </style>
</head>
<body class="font-mono">
    <div class="admin-header flex justify-between items-center">
        <h1 class="text-xl retro-heading">EDIT POST</h1>
        <div>
            <a href="index.php" class="retro-link">Dashboard</a>
            | <a href="../index.php" class="retro-link">View Blog</a>
        </div>
    </div>
    <div class="flex">
        <nav class="admin-sidebar">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="create_post.php" class="nav-link">Create Post</a>
            <a href="manage_subscribers.php" class="nav-link">Manage Subscribers</a>
        </nav>
        <main class="admin-content">
            <h2 class="text-2xl mb-4 retro-heading">> EDITING: <?= escape($post['title']) ?></h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?> terminal-text"><?= escape($message) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="content-section p-4 mb-6">
                    <label for="title" class="block mb-2">Title *</label>
                    <input type="text" id="title" name="title" value="<?= escape($post['title']) ?>" required>
                    
                    <label for="slug" class="block mb-2">Slug (URL-friendly) *</label>
                    <input type="text" id="slug" name="slug" value="<?= escape($post['slug']) ?>" required>
                    <small class="text-gray-400">e.g., my-new-post-title</small>
                    
                    <label for="excerpt" class="block mt-4 mb-2">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3"><?= escape($post['excerpt']) ?></textarea>
                    
                    <label for="content" class="block mt-4 mb-2">Content (HTML allowed) *</label>
                    <textarea id="content" name="content" rows="15" required><?= escape($post['content']) ?></textarea>
                </div>

                <div class="content-section p-4 mb-6">
                    <label class="block mb-2">Categories</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <?php foreach ($categories as $category): ?>
                            <div>
                                <input type="checkbox" id="cat_<?= $category['id'] ?>" name="categories[]" value="<?= $category['id'] ?>"
                                    <?= in_array($category['id'], $assigned_category_ids) ? 'checked' : '' ?>>
                                <label for="cat_<?= $category['id'] ?>"><?= escape($category['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit">UPDATE POST</button>
            </form>
        </main>
    </div>
</body>
</html>
