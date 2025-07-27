-- Create the database
CREATE DATABASE benata_matrix_blog;
USE benata_matrix_blog;

-- Table for blog posts
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL, -- For SEO friendly URLs
    content TEXT NOT NULL,
    excerpt TEXT, -- Short summary for the main page
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) UNIQUE NOT NULL
);

-- Junction table for many-to-many relationship between posts and categories
CREATE TABLE post_categories (
    post_id INT,
    category_id INT,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, category_id)
);

-- Table for blog subscribers
CREATE TABLE subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Table for admin users (simple for now)
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL -- Store hashed passwords
);

-- Insert sample data
INSERT INTO categories (name, slug) VALUES
('Programming', 'programming'),
('Philosophy', 'philosophy'),
('Projects', 'projects'),
('Reflections', 'reflections'),
('Tutorials', 'tutorials');

-- Insert a sample admin user (password is 'admin123')
-- In a real app, always hash passwords properly!
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$examplehashwhichisinvalidreplacewithrealhash'); -- REPLACE WITH REAL HASH

-- Insert sample posts
INSERT INTO posts (title, slug, content, excerpt) VALUES
('Demystifying Python Decorators', 'demystifying-python-decorators',
'<p>Decorators are one of Python''s most powerful features, yet they often intimidate newcomers. In this post, I break down how decorators work under the hood and show practical examples...</p>',
'Decorators are one of Python''s most powerful features, yet they often intimidate newcomers. In this post, I break down how decorators work under the hood and show practical examples...'),

('The Self in Advaita Vedanta', 'the-self-in-advaita-vedanta',
'<p>Exploring the concept of the Self (Atman) as described in the Upanishads and how Adi Shankaracharya''s non-dualistic approach transforms our understanding of identity and existence...</p>',
'Exploring the concept of the Self (Atman) as described in the Upanishads and how Adi Shankaracharya''s non-dualistic approach transforms our understanding of identity and existence...'),

('Building Retro Websites with Tailwind', 'building-retro-websites-with-tailwind',
'<p>A step-by-step guide to creating that nostalgic late 90s Japanese web aesthetic using modern tools. Includes code snippets and design tips...</p>',
'A step-by-step guide to creating that nostalgic late 90s Japanese web aesthetic using modern tools. Includes code snippets and design tips...'),

('My Second Year in Computer Engineering', 'my-second-year-in-computer-engineering',
'<p>Reflecting on my journey through the second year of my diploma program. Challenges faced, lessons learned, and insights gained about balancing technical skills with philosophical inquiry...</p>',
'Reflecting on my journey through the second year of my diploma program. Challenges faced, lessons learned, and insights gained about balancing technical skills with philosophical inquiry...');
