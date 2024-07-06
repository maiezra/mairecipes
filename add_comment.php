<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipe_uuid = $_POST['recipe_uuid'];
    $user_id = $_SESSION['user_id'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO comments (recipe_uuid, user_id, comment) VALUES (:recipe_uuid, :user_id, :comment)");
    $stmt->bindParam(':recipe_uuid', $recipe_uuid);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':comment', $comment);

    if ($stmt->execute()) {
        header('Location: recipe.php?uuid=' . $recipe_uuid);
    } else {
        echo "Error adding comment.";
    }
}
?>
