<?php
// Main blog page - displays list of posts
require 'includes/db.php';
require 'includes/functions.php';

// Pagination settings
$posts_per_page = 4;
$total_posts = count_all_posts($pdo);
$total_pages = ceil($total_posts / $posts_per_page);

// Get current page from URL, default to 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages;
}

// Calculate offset
$offset = ($current_page - 1) * $posts_per_page;

// Get posts for the current page
$posts = get_all_posts($pdo, $posts_per_page, $offset);

$recent_posts = get_recent_posts($pdo);
$categories = get_categories_with_count($pdo);

// Handle potential subscription via POST (simpler than AJAX for now)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $result = add_subscriber($pdo, $_POST['email']);
    if ($result === 'success') {
        $message = '<div class="alert alert-success terminal-text">Thank you for subscribing!</div>';
    } else {
        $message = '<div class="alert alert-error terminal-text">Error: ' . escape($result) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benata Matrix | Dhruv's Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Orbitron:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Removed inline styles and moved to style.css -->
</head>
<body class="bg-black text-green-400 font-mono matrix-bg min-h-screen">
    <!-- Scanline effect -->
    <div class="scanline"></div>
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 glow-text">BENATA MATRIX</h1>
            <div class="terminal-text text-lg mb-2">> Dhruv Solanki's Digital Journal</div>
            <div class="terminal-text text-lg mb-4">> Philosophy, Code & Consciousness</div>
            <div class="glow-text text-xl mb-4">"Benata, Mi estas"</div>
            <div class="w-32 h-1 bg-green-500 mx-auto mb-6"></div>
        </header>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
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
                        <?php foreach ($recent_posts as $post): ?>
                        <div>
                            <p class="text-sm"><a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="retro-link"><?= escape($post['title']) ?></a></p>
                            <p class="text-xs post-date"><?= date('Y-m-d', strtotime($post['created_at'])) ?></p>
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
                <!-- Terminal Window -->
                <div class="terminal-window rounded-lg overflow-hidden">
                    <div class="terminal-header flex items-center">
                        <div class="button-red"></div>
                        <div class="button-yellow"></div>
                        <div class="button-green"></div>
                        <div class="ml-4 text-green-300 text-sm">blog_terminal</div>
                    </div>
                    <div class="p-4 terminal-text">
                        <p>$ cat latest_posts.txt</p>
                        <p class="ml-4">Displaying recent articles...</p>
                        <p class="mt-4">$ echo "Benata, Mi estas"</p>
                        <p class="ml-4 glow-text">Benata, Mi estas</p>
                        <p class="mt-4 blink">_</p>
                    </div>
                </div>
                
                <!-- Subscription Message -->
                <?php if ($message): ?>
                    <?= $message ?>
                <?php endif; ?>
                
                <!-- Blog Posts -->
                <div class="content-section p-6 rounded-lg">
                    <h2 class="retro-heading text-xl mb-6">> LATEST ARTICLES</h2>
                    <div class="space-y-8">
                        <?php foreach ($posts as $post): ?>
                        <!-- Post -->
                        <article class="blog-post p-6 border border-green-500 bg-black/50">
                            <?php if (!empty($post['category_names'])): 
                                $cat_names = explode(',', $post['category_names']);
                            ?>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <?php foreach ($cat_names as $cat_name): ?>
                                    <span class="tag px-2 py-1 text-xs"><?= escape(trim($cat_name)) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <h3 class="text-2xl font-bold mb-2"><a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="retro-link"><?= escape($post['title']) ?></a></h3>
                            <p class="terminal-text mb-4"><?= escape($post['excerpt']) ?></p>
                            <div class="flex justify-between items-center">
                                <span class="post-date text-sm"><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                                <span class="terminal-text text-sm">~5 min read</span>
                            </div>
                        </article>
                        <?php endforeach; ?>
                        
                    </div>
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="mt-10 flex justify-center space-x-4 terminal-text">
                        <?php if ($current_page > 1): ?>
                            <a href="?page=<?= $current_page - 1 ?>" class="px-4 py-2 border border-green-500 hover:bg-green-900/30">Prev</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-4 py-2 border border-green-500 <?= ($i == $current_page) ? 'bg-green-900/30' : 'hover:bg-green-900/30' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?= $current_page + 1 ?>" class="px-4 py-2 border border-green-500 hover:bg-green-900/30">Next</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- Newsletter Section -->
                <div class="content-section p-6 rounded-lg">
                    <h2 class="retro-heading text-xl mb-4">> SUBSCRIBE</h2>
                    <div class="terminal-text">
                        <p class="mb-4">Join the matrix to receive updates on new posts:</p>
                        <form method="post">
                            <div class="flex">
                                <input type="email" name="email" placeholder="your@email.com" class="flex-grow bg-black border border-green-500 p-2 mr-2" required>
                                <button type="submit" class="bg-green-900 text-green-300 px-4 py-2 border border-green-500 hover:bg-green-800">
                                    > SUBSCRIBE
                                </button>
                            </div>
                        </form>
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
    <script src="script.js"></script>
</body>
</html>
