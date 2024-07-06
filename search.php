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
            <div class="col-md-4 mb-4">
                <div class="card recipe-card h-100">
                    <img src="<?php echo htmlspecialchars($meal['strMealThumb']); ?>" class="card-img-top" alt="Recipe Photo">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($meal['strMeal']); ?></h5>
                        <p class="card-text">
                            <strong>Category:</strong> <?php echo htmlspecialchars($meal['strCategory']); ?><br>
                            <strong>Area:</strong> <?php echo htmlspecialchars($meal['strArea']); ?><br>
                            <strong>Instructions:</strong> <?php echo nl2br(htmlspecialchars($meal['strInstructions'])); ?><br>
                        </p>
                        <a href="api_recipe.php?id=<?php echo htmlspecialchars($meal['idMeal']); ?>" class="btn btn-primary mt-auto">View Full Recipe</a>
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
    display: flex;
    flex-direction: column;
    max-height: 400px; /* Set max height */
}

.recipe-card:hover {
    transform: translateY(-10px);
}

.recipe-card img {
    border-bottom: 1px solid #ddd;
    max-height: 200px;
    object-fit: cover;
}

.recipe-card .card-body {
    padding: 15px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.recipe-card .card-title {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.recipe-card .card-text {
    flex: 1;
    font-size: 0.9rem;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 140px; /* Adjust height to ensure content fits within the max height */
}

.recipe-card .btn {
    margin-top: 10px;
    align-self: center;
}
</style>
