<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    error_log("User not logged in", 0);
    header('Location: login.php');
    exit();
}

if (isset($_GET['uuid'])) {
    $uuid = $_GET['uuid'];
    $user_id = $_SESSION['user_id'];

    $conn->beginTransaction();

    try {
        $stmt = $conn->prepare("SELECT id FROM recipes WHERE uuid = :uuid AND user_id = :user_id");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $recipe = $stmt->fetch();

        if (!$recipe) {
            throw new Exception("Recipe not found or you do not have permission to delete this recipe.");
        }

        $recipe_id = $recipe['id'];

        $stmt = $conn->prepare("DELETE FROM recipe_likes WHERE recipe_id = :recipe_id");
        $stmt->bindParam(':recipe_id', $recipe_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting related recipe likes.");
        }

        $stmt = $conn->prepare("DELETE FROM recipes WHERE uuid = :uuid AND user_id = :user_id");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':user_id', $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting recipe.");
        }

        $conn->commit();
    } catch (Exception $e) {

        $conn->rollBack();
        error_log($e->getMessage(), 0);
        echo "Error: " . $e->getMessage();
        exit();
    }

    header('Location: dashboard.php');
    exit();
} else {
    echo "Invalid request.";
    exit();
}
?>
