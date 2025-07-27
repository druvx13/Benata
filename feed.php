<?php
// feed.php
header('Content-Type: application/rss+xml; charset=UTF-8');

require 'includes/db.php';
require 'includes/functions.php';

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;
echo '<channel>' . PHP_EOL;
echo '  <title>Benata Matrix</title>' . PHP_EOL;
echo '  <description>Dhruv Solanki\'s Digital Journal on Philosophy, Code & Consciousness</description>' . PHP_EOL;
echo '  <link>' . BASE_URL . '/</link>' . PHP_EOL;
echo '  <atom:link href="' . BASE_URL . '/feed.php" rel="self" type="application/rss+xml" />' . PHP_EOL;
echo '  <language>en-us</language>' . PHP_EOL;
echo '  <lastBuildDate>' . date('r') . '</lastBuildDate>' . PHP_EOL;

$posts = get_all_posts($pdo, 10); // Get last 10 published posts

foreach ($posts as $post) {
    $post_url = BASE_URL . '/post.php?slug=' . urlencode($post['slug']);
    $description = htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 200) . '...', ENT_XML1, 'UTF-8');

    echo '  <item>' . PHP_EOL;
    echo '    <title>' . htmlspecialchars($post['title'], ENT_XML1, 'UTF-8') . '</title>' . PHP_EOL;
    echo '    <description>' . $description . '</description>' . PHP_EOL;
    echo '    <link>' . $post_url . '</link>' . PHP_EOL;
    echo '    <guid isPermaLink="true">' . $post_url . '</guid>' . PHP_EOL;
    echo '    <pubDate>' . date('r', strtotime($post['created_at'])) . '</pubDate>' . PHP_EOL;
    echo '  </item>' . PHP_EOL;
}

echo '</channel>' . PHP_EOL;
echo '</rss>' . PHP_EOL;
?>
