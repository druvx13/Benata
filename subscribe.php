<?php
// Simple endpoint to handle AJAX subscription
require 'includes/db.php';
require 'includes/functions.php';

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $result = add_subscriber($pdo, $email);
    echo $result; // Outputs 'success' or an error message
} else {
    echo 'Invalid request.';
}
?>
