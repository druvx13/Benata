<?php
// sitemap.php
header('Content-type: application/xml');

require 'includes/db.php';
require 'includes/functions.php';

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Add homepage
echo '  <url>' . PHP_EOL;
echo '    <loc>' . BASE_URL . '/</loc>' . PHP_EOL;
echo '    <changefreq>daily</changefreq>' . PHP_EOL;
echo '    <priority>1.0</priority>' . PHP_EOL;
echo '  </url>' . PHP_EOL;

// Add all published posts
$posts = get_all_posts($pdo); // Modify get_all_posts to only get 'published' if you add status
foreach ($posts as $post) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . BASE_URL . '/post.php?slug=' . urlencode($post['slug']) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($post['updated_at'])) . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>monthly</changefreq>' . PHP_EOL;
    echo '    <priority>0.8</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// Add categories (if you create a category.php page)
$categories = get_categories_with_count($pdo);
foreach ($categories as $category) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . BASE_URL . '/category.php?slug=' . urlencode($category['slug']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.6</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

echo '</urlset>' . PHP_EOL;
?>
