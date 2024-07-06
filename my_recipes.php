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

$stmt = $conn->prepare("SELECT * FROM recipes WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>My Recipes</h2>
    <div class="row">
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $recipe): ?>
                <div class="col-md-6 mb-4">
                    <div class="card recipe-card">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($recipe['photo']); ?>" class="card-img-top" alt="Recipe Photo">
                            <div class="button-container">
                                <a href="recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-primary">View</a>
                                <a href="edit_recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete_recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text">
                                <strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?> minutes<br>
                                <strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients']); ?><br>
                                <strong>Instructions:</strong> <?php echo htmlspecialchars($recipe['instructions']); ?><br>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recipes found.</p>
        <?php endif; ?>
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
    position: relative;
    background-color: #f5f5f5;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
}

.recipe-card:hover {
    transform: translateY(-10px);
}

.image-container {
    position: relative;
}

.image-container img {
    border-bottom: 1px solid #ddd;
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.button-container {
    position: absolute;
    bottom: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

.button-container .btn {
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    transition: background 0.2s, color 0.2s;
}

.button-container .btn:hover {
    background: rgba(255, 255, 255, 0.8);
    color: #000;
}

.card-body {
    padding: 15px;
    flex: 1;
}

.card-title {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.card-text {
    font-size: 0.9rem;
    line-height: 1.5;
}
</style>
