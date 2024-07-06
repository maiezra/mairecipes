<?php
include 'includes/db.php';
include 'includes/functions.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Validate input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $recipe_id = $_POST['recipe_id'];
    $is_api = $_POST['is_api'];
    $redirect_url = $_POST['redirect_url']; 
    if (!empty($recipe_id)) {
        // Check if the recipe is already liked by the user
        if ($is_api) {
            $stmt = $conn->prepare('SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_api_id = :recipe_id');
        } else {
            $stmt = $conn->prepare('SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_uuid = :recipe_id');
        }
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':recipe_id', $recipe_id);
        $stmt->execute();
        $like = $stmt->fetch();

        if ($like) {
            // Remove like if it already exists
            if ($is_api) {
                $stmt = $conn->prepare('DELETE FROM recipe_likes WHERE user_id = :user_id AND recipe_api_id = :recipe_id');
            } else {
                $stmt = $conn->prepare('DELETE FROM recipe_likes WHERE user_id = :user_id AND recipe_uuid = :recipe_id');
            }
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':recipe_id', $recipe_id);
            $stmt->execute();
        } else {
            // Add like if it doesn't exist
            if ($is_api) {
                $stmt = $conn->prepare('INSERT INTO recipe_likes (user_id, recipe_api_id) VALUES (:user_id, :recipe_id)');
            } else {
                $stmt = $conn->prepare('INSERT INTO recipe_likes (user_id, recipe_uuid) VALUES (:user_id, :recipe_id)');
            }
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':recipe_id', $recipe_id);
            $stmt->execute();
        }
        header('Location: ' . $redirect_url);
        exit();
    } else {
        header('Location: ' . $redirect_url . '?error=invalid_recipe_id');
        exit();
    }
}
?>
