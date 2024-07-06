<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch random recipes from the API
$api_url = "https://www.themealdb.com/api/json/v1/1/search.php?s=";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

$recipes = [];
if (isset($data['meals'])) {
    $recipes = $data['meals'];
    shuffle($recipes); // Shuffle the recipes to get random ones
}

// Select first 3 recipes for the featured section
$featured_recipes = array_slice($recipes, 0, 3);
?>

<div class="container mt-5">
    <!-- Logo -->
    <div class="text-center mb-4">
        <img src="assets/logo.jpg" alt="Website Logo" class="img-fluid" style="max-width: 200px;">
    </div>

    <!-- Featured Recipes -->
    <h2 class="mt-5">Featured Recipes</h2>
    <div class="row">
        <?php foreach ($featured_recipes as $recipe): ?>
            <div class="col-md-4 mb-4">
                <div class="card recipe-card">
                    <img src="<?php echo htmlspecialchars($recipe['strMealThumb']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['strMeal']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['strMeal']); ?></h5>
                        <p class="card-text"><?php echo substr(htmlspecialchars($recipe['strInstructions']), 0, 100); ?>...</p>
                        <a href="api_recipe.php?id=<?php echo $recipe['idMeal']; ?>" class="btn btn-primary">View Recipe</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Healthy Eating Tips Section -->
    <div class="healthy-eating-section mt-5">
        <h2>Healthy Eating Tips</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card tip-card">
                    <img src="assets/tips.jpeg" class="card-img-top" alt="Tip 1">
                    <div class="card-body">
                        <h5 class="card-title">Tip 1</h5>
                        <p class="card-text">Include a variety of fruits and vegetables in your diet to ensure you get a range of nutrients.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card tip-card">
                    <img src="assets/tips.jpeg" class="card-img-top" alt="Tip 2">
                    <div class="card-body">
                        <h5 class="card-title">Tip 2</h5>
                        <p class="card-text">Stay hydrated by drinking plenty of water throughout the day. Aim for at least 8 cups of water daily.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card tip-card">
                    <img src="assets/tips.jpeg" class="card-img-top" alt="Tip 3">
                    <div class="card-body">
                        <h5 class="card-title">Tip 3</h5>
                        <p class="card-text">Choose whole grains over refined grains to benefit from more fiber and nutrients.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
