<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT recipes.uuid, recipes.title, recipes.ingredients, recipes.instructions, recipes.cooking_time, recipes.photo
    FROM recipes
    JOIN recipe_likes ON recipes.uuid = recipe_likes.recipe_uuid
    WHERE recipe_likes.user_id = :user_id AND recipe_likes.is_api = 0
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$saved_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

error_log("Saved Recipes: " . print_r($saved_recipes, true));

$stmt = $conn->prepare("
    SELECT api_recipes.uuid, api_recipes.title, api_recipes.instructions, api_recipes.photo, NULL as cooking_time, NULL as ingredients
    FROM api_recipes
    JOIN recipe_likes ON api_recipes.uuid = recipe_likes.recipe_uuid
    WHERE recipe_likes.user_id = :user_id AND recipe_likes.is_api = 1
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$saved_api_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

error_log("Saved API Recipes: " . print_r($saved_api_recipes, true));

$saved_recipes = array_merge($saved_recipes, $saved_api_recipes);

header('Content-Type: application/json');
echo json_encode($saved_recipes);
?>
