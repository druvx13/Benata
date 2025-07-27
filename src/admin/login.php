<?php
// Admin Login Page
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // Fetch admin user
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Benata Matrix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Orbitron:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/style.css">
    <style>
        body {
            background-color: #000;
            color: #00ff41;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            border: 2px solid #00ff41;
            background: rgba(0, 20, 0, 0.8);
        }
        input {
            width: 100%;
            padding: 0.5rem;
            margin: 0.5rem 0 1rem 0;
            background: #000;
            border: 1px solid #00ff41;
            color: #00ff41;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background: #003300;
            border: 1px solid #00ff41;
            color: #00ff41;
            cursor: pointer;
        }
        button:hover {
            background: #005500;
        }
        .error { color: #ff5555; }
    </style>
</head>
<body>
    <div class="login-container terminal-text">
        <h1 class="text-2xl text-center mb-6 retro-heading">ADMIN LOGIN</h1>
        <?php if ($error): ?>
            <p class="error mb-4"><?= escape($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">LOGIN</button>
        </form>
        <p class="mt-4 text-center"><a href="../index.php" class="retro-link">Back to Blog</a></p>
    </div>
</body>
</html>
