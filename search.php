<?php
// search.php
require 'includes/db.php';
require 'includes/functions.php';

$query = trim($_GET['q'] ?? '');

$results = [];
$total_results = 0;

if ($query) {
    // Simple search in title and content
    $sql = "SELECT p.*, GROUP_CONCAT(c.name) as category_names
            FROM posts p
            LEFT JOIN post_categories pc ON p.id = pc.post_id
            LEFT JOIN categories c ON pc.category_id = c.id
            WHERE (p.title LIKE :query OR p.content LIKE :query) AND p.status = 'published'
            GROUP BY p.id
            ORDER BY p.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll();
    $total_results = count($results);
}

$recent_posts = get_recent_posts($pdo);
$categories = get_categories_with_count($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($query): ?>
        <title>Search Results for "<?= escape($query) ?>" | Benata Matrix</title>
        <meta name="description" content="Search results for '<?= escape($query) ?>' on Benata Matrix Blog.">
    <?php else: ?>
        <title>Search | Benata Matrix</title>
        <meta name="description" content="Search posts on Benata Matrix Blog.">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Orbitron:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body id="main-body" class="bg-black text-green-400 font-mono matrix-bg min-h-screen theme-dark">
    <button id="theme-toggle" class="theme-toggle-btn">Toggle Theme</button>
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
                    <div class="terminal-text flex flex-wrap gap-2">
                        <?php
                        // Find max count for sizing
                        $max_count = 1;
                        foreach ($categories as $cat) {
                            if ($cat['post_count'] > $max_count) $max_count = $cat['post_count'];
                        }
                        foreach ($categories as $cat):
                            // Calculate relative size (simple linear scaling)
                            $font_size = 0.8 + (0.6 * ($cat['post_count'] / ($max_count > 0 ? $max_count : 1)));
                        ?>
                            <a href="category.php?slug=<?= urlencode($cat['slug']) ?>" class="retro-link" style="font-size: <?= $font_size ?>rem;">
                                <?= escape($cat['name']) ?> (<?= escape($cat['post_count']) ?>)
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-8">
                    <h2 class="retro-heading text-lg mb-4">> SEARCH</h2>
                    <form method="get" action="search.php" class="terminal-text">
                        <div class="flex">
                            <input type="text" name="q" placeholder="Search posts..." value="<?= escape($query) ?>" class="flex-grow bg-black border border-green-500 p-2 mr-2 text-green-400" required>
                            <button type="submit" class="bg-green-900 text-green-300 px-4 py-2 border border-green-500 hover:bg-green-800">
                                > GO
                            </button>
                        </div>
                    </form>
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
                <div class="content-section p-6 rounded-lg">
                    <h2 class="retro-heading text-xl mb-6">
                        <?php if ($query): ?>
                            > SEARCH RESULTS FOR "<?= escape(strtoupper($query)) ?>" (<?= $total_results ?>)
                        <?php else: ?>
                            > SEARCH
                        <?php endif; ?>
                    </h2>
                    <?php if ($query): ?>
                        <?php if ($results): ?>
                            <div class="space-y-8">
                                <?php foreach ($results as $post): ?>
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
                        <?php else: ?>
                            <p class="terminal-text">No posts found matching your search terms.</p>
                        <?php endif; ?>
                    <?php else: ?>
                         <p class="terminal-text">Enter a term in the search box to find posts.</p>
                    <?php endif; ?>
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
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const body = document.getElementById('main-body');
    const toggleButton = document.getElementById('theme-toggle');
    const themes = ['theme-dark', 'theme-alternate-dark'];
    let currentThemeIndex = 0;

    // Check for saved theme in localStorage
    const savedThemeIndex = localStorage.getItem('benata_theme_index');
    if (savedThemeIndex !== null) {
        currentThemeIndex = parseInt(savedThemeIndex, 10) % themes.length;
        body.className = body.className.replace(/theme-\S+/g, themes[currentThemeIndex]);
    }

    toggleButton.addEventListener('click', function() {
        currentThemeIndex = (currentThemeIndex + 1) % themes.length;
        body.className = body.className.replace(/theme-\S+/g, themes[currentThemeIndex]);
        localStorage.setItem('benata_theme_index', currentThemeIndex);
    });
});
</script>
</body>
</html>
