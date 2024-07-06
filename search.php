<?php
include __DIR__ . '/includes/header.php';

$query = isset($_GET['s']) ? urlencode($_GET['s']) : '';
$api_url = "https://www.themealdb.com/api/json/v1/1/search.php?s=" . $query;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$meals = [];

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['meals'])) {
        $meals = $data['meals'];
    }
}

?>

<div class="container">
    <h2>Search Recipes</h2>
    <form method="GET" action="search.php">
        <div class="form-group">
            <label for="search">Search for a recipe:</label>
            <input type="text" id="search" name="s" class="form-control" value="<?php echo htmlspecialchars($query); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <div class="row mt-4">
        <?php if (!empty($meals)): ?>
            <?php foreach ($meals as $meal): ?>
            <div class="col-md-6 mb-4">
                <div class="card recipe-card">
                    <img src="<?php echo htmlspecialchars($meal['strMealThumb']); ?>" class="card-img-top" alt="Recipe Photo">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($meal['strMeal']); ?></h5>
                        <p class="card-text">
                            <strong>Category:</strong> <?php echo htmlspecialchars($meal['strCategory']); ?><br>
                            <strong>Area:</strong> <?php echo htmlspecialchars($meal['strArea']); ?><br>
                            <strong>Instructions:</strong> <?php echo htmlspecialchars($meal['strInstructions']); ?><br>
                        </p>
                        <a href="api_recipe.php?id=<?php echo htmlspecialchars($meal['idMeal']); ?>" class="btn btn-primary btn-block mt-2">View Full Recipe</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No recipes found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>

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
}

.recipe-card:hover {
    transform: translateY(-10px);
}

.recipe-card img {
    border-bottom: 1px solid #ddd;
}

.recipe-card .card-body {
    padding: 15px;
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
