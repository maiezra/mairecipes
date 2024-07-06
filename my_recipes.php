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
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($recipe['photo']); ?>" class="card-img-top" alt="Recipe Photo">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text">
                                <strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?> minutes<br>
                                <strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients']); ?><br>
                                <strong>Instructions:</strong> <?php echo htmlspecialchars($recipe['instructions']); ?><br>
                            </p>
                            <a href="recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-primary">View Full Recipe</a>
                            <a href="edit_recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-secondary">Edit</a>
                            <a href="delete_recipe.php?uuid=<?php echo htmlspecialchars($recipe['uuid']); ?>" class="btn btn-danger">Delete</a>
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
</style>
