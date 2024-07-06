<?php
require _DIR_ . '/includes/db.php';
require _DIR_ . '/includes/functions.php';
include _DIR_ . '/includes/header.php';

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
                <div class="col-md-4 mb-4">
                    <div class="card recipe-card h-100">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($recipe['photo']); ?>" class="card-img-top" alt="Recipe Photo">
                            <div class="action-buttons">
                                <a href="recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-primary">View</a>
                                <a href="edit_recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete_recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-danger">Delete</a>
                            </div>
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
        <?php else: ?>
            <p>No recipes found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include _DIR_ . '/includes/footer.php'; ?>

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

.recipe-card .action-buttons {
    position: absolute;
    bottom: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

.recipe-card .action-buttons .btn {
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    font-size: 0.9em;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
}

.recipe-card .action-buttons .btn:hover {
    background: rgba(255, 255, 255, 0.8);
    color: #000;
}

.recipe-card .card-body {
    padding: 15px;
    flex: 1;
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
    max-height: 140px;
}
</style>
