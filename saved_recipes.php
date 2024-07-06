<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $saved_recipes_stmt = $conn->prepare("SELECT recipes.*, recipe_likes.created_at as saved_at 
                                          FROM recipes 
                                          JOIN recipe_likes ON recipes.uuid = recipe_likes.recipe_uuid 
                                          WHERE recipe_likes.user_id = :user_id 
                                          ORDER BY recipe_likes.created_at DESC");
    $saved_recipes_stmt->bindParam(':user_id', $user_id);
    $saved_recipes_stmt->execute();
    $saved_recipes = $saved_recipes_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching saved recipes: " . $e->getMessage();
    die();
}

try {
    $api_saved_recipes_stmt = $conn->prepare("SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_api_id IS NOT NULL ORDER BY created_at DESC");
    $api_saved_recipes_stmt->bindParam(':user_id', $user_id);
    $api_saved_recipes_stmt->execute();
    $api_saved_recipes = $api_saved_recipes_stmt->fetchAll(PDO::FETCH_ASSOC);

    $api_recipes = [];
    foreach ($api_saved_recipes as $api_saved_recipe) {
        $api_id = $api_saved_recipe['recipe_api_id'];
        $url = "https://www.themealdb.com/api/json/v1/1/lookup.php?i=" . urlencode($api_id);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $recipe_data = json_decode($response, true);
        if ($recipe_data && isset($recipe_data['meals'][0])) {
            $recipe = $recipe_data['meals'][0];
            $recipe['saved_at'] = $api_saved_recipe['created_at'];
            $api_recipes[] = $recipe;
        }
    }
} catch (PDOException $e) {
    echo "Error fetching saved API recipes: " . $e->getMessage();
    die();
}

$all_saved_recipes = array_merge($saved_recipes, $api_recipes);

usort($all_saved_recipes, function($a, $b) {
    return strtotime($b['saved_at']) - strtotime($a['saved_at']);
});

?>

<div class="container">
    <h2>Saved Recipes</h2>
    <div class="row">
        <?php foreach ($all_saved_recipes as $recipe): ?>
            <div class="col-md-4 mb-4">
                <div class="card recipe-card h-100">
                    <div class="image-container">
                        <img src="<?php echo htmlspecialchars($recipe['photo'] ?? $recipe['strMealThumb']); ?>" class="card-img-top" alt="Recipe Photo">
                        <a href="<?php echo isset($recipe['uuid']) ? 'recipe.php?uuid=' . $recipe['uuid'] : 'api_recipe.php?id=' . $recipe['idMeal']; ?>" class="view-recipe-btn">
                            View Full Recipe
                        </a>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title'] ?? $recipe['strMeal']); ?></h5>
                        <p class="card-text">
                            <strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time'] ?? 'N/A'); ?> minutes<br>
                            <strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients'] ?? ($recipe['strIngredient1'] . ', ' . $recipe['strIngredient2'] . ', ...')); ?><br>
                            <strong>Instructions:</strong> <?php echo nl2br(htmlspecialchars($recipe['instructions'] ?? $recipe['strInstructions'])); ?><br>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<style>
body {
    margin-top: 9rem !important;
}

#mainNav a {
    color: #fff !important;
}

#mainNav {
    background-color: #375755 !important;
}

.recipe-card {
    background-color: #f5f5f5;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
    max-height: 400px;
}

.recipe-card:hover {
    transform: translateY(-10px);
}

.recipe-card .image-container {
    position: relative;
}

.recipe-card img {
    border-bottom: 1px solid #ddd;
    max-height: 200px;
    object-fit: cover;
    width: 100%;
}

.recipe-card .view-recipe-btn {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border-radius: 5px;
    padding: 10px 15px;
    font-size: 0.9em;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
}

.recipe-card .view-recipe-btn:hover {
    background: rgba(255, 255, 255, 0.8);
    color: #000;
}

.recipe-card .card-body {
    padding: 15px;
    flex: 1;
}

.recipe-card .matching-percentage {
    font-size: 1.2em;
}

.recipe-card .badge-info {
    font-size: 0.9em;
}

.recipe-card .btn-block {
    margin-top: 10px;
}
</style>
