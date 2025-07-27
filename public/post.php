<?php
// Single post page
require '../src/includes/db.php';
require '../src/includes/functions.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    // Redirect to home or show 404
    header('Location: index.php');
    exit;
}

$post = get_post_by_slug($pdo, $slug);

if (!$post) {
    // Show 404 page
    http_response_code(404);
    echo "Post not found.";
    exit;
}

$recent_posts = get_recent_posts($pdo);
$categories = get_categories_with_count($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($post['title']) ?> | Benata Matrix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Orbitron:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-black text-green-400 font-mono matrix-bg min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 glow-text">BENATA MATRIX</h1>
            <div class="terminal-text text-lg mb-2">> Dhruv Solanki's Digital Journal</div>
            <div class="terminal-text text-lg mb-4">> Philosophy, Code & Consciousness</div>
            <a href="index.php" class="retro-link text-xl">&laquo; Back to Blog</a>
        </header>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar (same as index) -->
            <aside class="md:w-1/3 sidebar p-6 rounded-lg">
                <div class="mb-8">
                    <h2 class="retro-heading text-lg mb-4">> ABOUT</h2>
                    <div class="terminal-text space-y-3">
                        <p>Welcome to my digital sanctuary where I explore the intersections of technology, philosophy, and self-discovery.</p>
                        <p>Here I document my journey through computer engineering, coding projects, and spiritual inquiries.</p>
                    </div>
                </div>
                <div class="mb-8">
                    <h2 class="retro-heading text-lg mb-4">> CATEGORIES</h2>
                    <div class="terminal-text space-y-2">
                        <?php foreach ($categories as $category): ?>
                            <p>> <a href="#" class="retro-link"><?= escape($category['name']) ?></a> (<?= escape($category['post_count']) ?>)</p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-8">
                    <h2 class="retro-heading text-lg mb-4">> RECENT POSTS</h2>
                    <div class="terminal-text space-y-3">
                        <?php foreach ($recent_posts as $p): ?>
                        <div>
                            <p class="text-sm"><a href="post.php?slug=<?= urlencode($p['slug']) ?>" class="retro-link"><?= escape($p['title']) ?></a></p>
                            <p class="text-xs post-date"><?= date('Y-m-d', strtotime($p['created_at'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-8">
                    <h2 class="retro-heading text-lg mb-4">> CONNECT</h2>
                    <div class="terminal-text space-y-2">
                        <p><i class="fab fa-github mr-2"></i><a href="https://github.com/druvx13" class="retro-link">github.com/druvx13</a></p>
                        <p><i class="fab fa-instagram mr-2"></i><a href="#" class="retro-link">@druvx13</a></p>
                        <p><i class="fas fa-envelope mr-2"></i><a href="mailto:dk@theescape.eu.org" class="retro-link">dk@theescape.eu.org</a></p>
                    </div>
                </div>
            </aside>
            <!-- Main Content -->
            <main class="md:w-2/3 space-y-8">
                <!-- Post Content -->
                <div class="content-section p-6 rounded-lg">
                    <?php if (!empty($post['category_names'])): 
                        $cat_names = explode(',', $post['category_names']);
                    ?>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <?php foreach ($cat_names as $cat_name): ?>
                            <span class="tag px-2 py-1 text-xs"><?= escape(trim($cat_name)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <h1 class="text-3xl font-bold mb-4 retro-heading"><?= escape($post['title']) ?></h1>
                    <p class="post-date mb-6">Published on <?= date('F j, Y', strtotime($post['created_at'])) ?></p>
                    <div class="post-content terminal-text">
                        <?= $post['content'] ?> <!-- Assuming content is stored as HTML -->
                    </div>
                </div>
            </main>
        </div>
        <!-- Footer -->
        <footer class="mt-12 text-center terminal-text text-sm">
            <div class="border-t border-green-500 pt-6">
                <p>© 2023 Benata Matrix | Dhruv Solanki</p>
                <p class="mt-2">"Benata, Mi estas" - Blessed, I am.</p>
                <p class="mt-2">Parul University, Vadodara, Gujarat</p>
            </div>
        </footer>
    </div>
</body>
</html>
