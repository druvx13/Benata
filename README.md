# Benata Matrix Blog

A simple, lightweight, and customizable PHP blog with a retro-futuristic "matrix" theme. This project is designed to be easy to set up and use, even for those with minimal PHP experience.

## Features

*   **Simple and clean design:** A retro-futuristic "matrix" theme that is easy to customize.
*   **Admin panel:** A simple admin panel to manage posts, categories, and subscribers.
*   **SEO-friendly URLs:** SEO-friendly URLs for posts.
*   **RSS feed:** An RSS feed for your blog.
*   **Subscriber management:** A simple subscriber management system.

## Requirements

*   PHP 7.4 or higher
*   MySQL 5.6 or higher
*   Apache with `mod_rewrite` enabled

## Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/druvx13/Benata-Matrix-PHP-Blog.git
    ```

2.  **Create a database:**

    Create a new MySQL database for your blog.

3.  **Import the database schema:**

    Import the `db/schema.sql` file into your database. This will create the necessary tables and sample data.

4.  **Configure the application:**

    Rename the `src/config.example.php` file to `src/config.php` and update the database credentials and `BASE_URL`.

5.  **Set up the web server:**

    Configure your web server to use the `public` directory as the document root.

6.  **Log in to the admin panel:**

    Log in to the admin panel at `http://your-domain.com/admin` with the default credentials:

    *   **Username:** `admin`
    *   **Password:** `admin123`

    **Note:** It is highly recommended to change the default admin password after logging in.

## Usage

### Admin Panel

The admin panel allows you to:

*   Create, edit, and delete posts
*   Manage categories
*   Manage subscribers

### Customization

You can customize the theme by editing the `public/style.css` file. The HTML templates are located in the `public` directory.

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
