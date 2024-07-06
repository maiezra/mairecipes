<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipe_api_id = $_POST['recipe_api_id'];
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($comment === '') {
        echo "Comment cannot be empty.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO api_comments (recipe_api_id, user_id, comment) VALUES (:recipe_api_id, :user_id, :comment)");
    $stmt->bindParam(':recipe_api_id', $recipe_api_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':comment', $comment);

    if ($stmt->execute()) {
        header("Location: api_recipe.php?id=" . $recipe_api_id);
        exit();
    } else {
        echo "Failed to add comment.";
    }
} else {
    echo "Invalid request.";
}
?>
