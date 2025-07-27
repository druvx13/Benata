<?php
// Helper functions for the Benata Matrix Blog

// Function to fetch all posts with their categories
function get_all_posts($pdo, $limit = null, $offset = null, $status = 'published') {
    $sql = "SELECT p.*, GROUP_CONCAT(c.name) as category_names FROM posts p LEFT JOIN post_categories pc ON p.id = pc.post_id LEFT JOIN categories c ON pc.category_id = c.id WHERE p.status = :status GROUP BY p.id ORDER BY p.created_at DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT :limit";
        if ($offset !== null) {
            $sql .= " OFFSET :offset";
        }
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    if ($limit !== null) {
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        if ($offset !== null) {
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to fetch a single post by slug
function get_post_by_slug($pdo, $slug, $status = 'published') {
    $sql = "SELECT p.*, GROUP_CONCAT(c.name) as category_names FROM posts p LEFT JOIN post_categories pc ON p.id = pc.post_id LEFT JOIN categories c ON pc.category_id = c.id WHERE p.slug = :slug AND p.status = :status GROUP BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['slug' => $slug, 'status' => $status]);
    return $stmt->fetch();
}

// Function to fetch recent posts (for sidebar)
function get_recent_posts($pdo, $limit = 5) {
    $sql = "SELECT id, title, slug, created_at FROM posts ORDER BY created_at DESC LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to fetch all categories with post counts
function get_categories_with_count($pdo) {
    $sql = "SELECT c.name, c.slug, COUNT(pc.post_id) as post_count FROM categories c LEFT JOIN post_categories pc ON c.id = pc.category_id GROUP BY c.id ORDER BY c.name";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Function to handle subscriber signup
function add_subscriber($pdo, $email) {
    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM subscribers WHERE email = :email";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(['email' => $email]);
    
    if ($check_stmt->fetch()) {
        return "This email is already subscribed.";
    }

    // Insert new subscriber
    $insert_sql = "INSERT INTO subscribers (email) VALUES (:email)";
    $insert_stmt = $pdo->prepare($insert_sql);
    
    if ($insert_stmt->execute(['email' => $email])) {
        return "success";
    } else {
        return "Failed to subscribe. Please try again.";
    }
}

// Function to safely output HTML content
function escape($html) {
    return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

// Function to count all posts
function count_all_posts($pdo) {
    $sql = "SELECT COUNT(*) FROM posts";
    $stmt = $pdo->query($sql);
    return (int)$stmt->fetchColumn();
}

// Function to fetch approved comments for a post
function get_approved_comments($pdo, $post_id) {
    $sql = "SELECT * FROM comments WHERE post_id = :post_id AND is_approved = TRUE ORDER BY created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['post_id' => $post_id]);
    return $stmt->fetchAll();
}

// Function to add a new comment (requires approval by default)
function add_comment($pdo, $post_id, $author_name, $author_email, $content) {
    // Basic validation
    if (empty(trim($author_name)) || empty(trim($content))) {
        return "Name and comment are required.";
    }

    $sql = "INSERT INTO comments (post_id, author_name, author_email, content, is_approved) VALUES (:post_id, :author_name, :author_email, :content, FALSE)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([
        'post_id' => $post_id,
        'author_name' => trim($author_name),
        'author_email' => filter_var(trim($author_email), FILTER_VALIDATE_EMAIL) ? trim($author_email) : null, // Sanitize email
        'content' => trim($content)
    ])) {
        return "success";
    } else {
        return "Failed to add comment. Please try again.";
    }
}

// Function to fetch posts by category slug
function get_posts_by_category_slug($pdo, $category_slug) {
    $sql = "SELECT p.*, GROUP_CONCAT(c.name) as category_names
            FROM posts p
            JOIN post_categories pc ON p.id = pc.post_id
            JOIN categories c ON pc.category_id = c.id
            WHERE c.slug = :slug AND p.status = 'published'
            GROUP BY p.id
            ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['slug' => $category_slug]);
    return $stmt->fetchAll();
}

?>
