<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
    ob_end_clean(); 
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $user_stmt->bindParam(':user_id', $user_id);
    $user_stmt->execute();
    $user_preferences = $user_stmt->fetch(PDO::FETCH_ASSOC);

    $defaults = [
        'dietary_preferences' => '',
        'allergies' => '',
        'cooking_skill' => '',
        'cooking_frequency' => '',
        'favorite_cuisine' => '',
        'dietary_restrictions' => '',
        'meal_preferences' => '',
        'meal_type' => '',
        'max_cooking_time' => PHP_INT_MAX,
    ];

    $user_preferences = array_merge($defaults, $user_preferences);

    $recipes_stmt = $conn->prepare("SELECT * FROM recipes");
    $recipes_stmt->execute();
    $recipes = $recipes_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    die();
}

$query = "chicken";
$api_url = "https://www.themealdb.com/api/json/v1/1/search.php?s=" . urlencode($query);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$api_recipes = [];
if ($response) {
    $data = json_decode($response, true);
    if (isset($data['meals'])) {
        foreach ($data['meals'] as $meal) {
            $cooking_time = rand(20, 60); 
            $cooking_skill = 'Intermediate'; 
            $meal_type = 'Lunch'; 

            $api_recipes[] = [
                'id' => $meal['idMeal'],
                'title' => $meal['strMeal'],
                'ingredients' => implode(', ', array_filter([
                    $meal['strIngredient1'], $meal['strIngredient2'], $meal['strIngredient3'],
                    $meal['strIngredient4'], $meal['strIngredient5'], $meal['strIngredient6'],
                    $meal['strIngredient7'], $meal['strIngredient8'], $meal['strIngredient9'],
                    $meal['strIngredient10'], $meal['strIngredient11'], $meal['strIngredient12'],
                    $meal['strIngredient13'], $meal['strIngredient14'], $meal['strIngredient15'],
                    $meal['strIngredient16'], $meal['strIngredient17'], $meal['strIngredient18'],
                    $meal['strIngredient19'], $meal['strIngredient20']
                ])),
                'instructions' => $meal['strInstructions'],
                'dietary_preferences' => '', 
                'dietary_restrictions' => '',
                'favorite_cuisine' => $meal['strArea'],
                'cooking_time' => $cooking_time, 
                'cooking_skill' => $cooking_skill, 
                'meal_type' => $meal_type, 
                'photo' => $meal['strMealThumb'],
                'src' => $meal['strMealThumb'],
                'is_api' => 1
            ];
        }
    }
}

$all_recipes = array_merge($recipes, $api_recipes);

foreach ($all_recipes as &$recipe) {
    $matching = calculate_matching_percentage($recipe, $user_preferences);
    $recipe['matching_total'] = $matching;
}

usort($all_recipes, function($a, $b) {
    return $b['matching_total'] - $a['matching_total'];
});

function isLiked($conn, $user_id, $recipe_id, $is_api) {
    if ($is_api) {
        $stmt = $conn->prepare('SELECT * FROM recipe_likes WHERE user_id = ? AND recipe_api_id = ?');
        $stmt->execute([$user_id, $recipe_id]);
    } else {
        $stmt = $conn->prepare('SELECT * FROM recipe_likes WHERE user_id = ? AND recipe_uuid = ?');
        $stmt->execute([$user_id, $recipe_id]);
    }
    return $stmt->fetch() !== false;
}
?>

<div class="container">
    <h2>Recipes for You</h2>
    <div class="row">
        <?php foreach ($all_recipes as $recipe): ?>
            <?php 
                $is_api = isset($recipe['is_api']) && $recipe['is_api'] == 1;
                $recipe_id = $is_api ? $recipe['id'] : $recipe['uuid'];
                $is_liked = isLiked($conn, $user_id, $recipe_id, $is_api);
                $like_text = $is_liked ? 'Unlike' : 'Like';
            ?>
            <div class="col-md-4 mb-4">
                <div class="card recipe-card h-100">
                    <div class="image-container">
                        <img src="<?php echo htmlspecialchars($recipe['photo'] ? $recipe['photo'] : $recipe['src']); ?>" class="card-img-top" alt="Recipe Photo">
                        <div class="match-percentage">
                            Summ Match: <?php echo $recipe['matching_total']; ?>%
                        </div>
                        <a href='<?php echo $is_api ? 'api_recipe.php?id=' . $recipe['id'] : 'recipe.php?uuid=' . $recipe['uuid']; ?>' class='view-recipe-btn'>
                            View Full Recipe
                        </a>
                        <form method='POST' action='like_recipe.php' class="like-form">
                            <input type='hidden' name='recipe_id' value='<?php echo htmlspecialchars($recipe_id); ?>'>
                            <input type='hidden' name='is_api' value='<?php echo htmlspecialchars($is_api); ?>'>
                            <input type='hidden' name='redirect_url' value='<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>'>
                            <button type='submit' name='like_action' class='btn-like'>
                                <?php echo $like_text; ?>
                            </button>
                        </form>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text">
                            <strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?> minutes<br>
                            <strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients']); ?><br>
                            <strong>Instructions:</strong> <?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?><br>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ob_end_flush();?>

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

.recipe-card .match-percentage {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border-radius: 50%;
    padding: 10px;
    font-size: 1em;
    font-weight: bold;
    text-align: center;
    width: 84px;
    height: 84px;
    display: flex;
    justify-content: center;
    align-items: center;
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

.recipe-card .like-form {
    position: absolute;
    top: 10px;
    left: 10px;
}

.recipe-card .btn-like {
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    font-size: 0.9em;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.recipe-card .btn-like:hover {
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
