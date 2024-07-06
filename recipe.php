<?php
ob_start(); 
error_reporting(0);
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
$is_liked = false;

if (isset($_GET['uuid'])) {
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE uuid = :uuid");
    $stmt->bindParam(':uuid', $_GET['uuid']);
    $stmt->execute();
    $recipe = $stmt->fetch();

    if ($recipe) {
        $like_stmt = $conn->prepare("SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_uuid = :recipe_uuid");
        $like_stmt->bindParam(':user_id', $user_id);
        $like_stmt->bindParam(':recipe_uuid', $_GET['uuid']);
        $like_stmt->execute();
        $is_liked = $like_stmt->rowCount() > 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_action'])) {
            if ($is_liked) {
                $stmt = $conn->prepare("DELETE FROM recipe_likes WHERE user_id = :user_id AND recipe_uuid = :recipe_uuid");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':recipe_uuid', $_GET['uuid']);
                $stmt->execute();
                $is_liked = false;
            } else {
                $stmt = $conn->prepare("INSERT INTO recipe_likes (user_id, recipe_uuid) VALUES (:user_id, :recipe_uuid)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':recipe_uuid', $_GET['uuid']);
                $stmt->execute();
                $is_liked = true;
            }
            ob_end_clean();
            header("Location: saved_recipes.php");
            exit();
        }

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

        $matching = calculate_matching_percentage($recipe, $user_preferences);
?>

<div class="container">
    <h2><?php echo htmlspecialchars($recipe['title']); ?></h2>
    <img src="<?php echo htmlspecialchars($recipe['photo'] ? $recipe['photo'] : $recipe['src']); ?>" class="img-fluid" alt="Recipe Photo">
    <p><strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?> minutes</p>
    <p><strong>Ingredients:</strong><br><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
    <p><strong>Instructions:</strong><br><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
    <div class="matching-percentage">
        <span class="badge badge-success" style="font-size: 1.5em;">Summ Match: <?php echo $matching; ?>%</span>
    </div>
    
    <?php if (is_logged_in()): ?>
        <form method="POST" action="">
            <button type="submit" name="like_action" class="btn <?php echo $is_liked ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <?php echo $is_liked ? 'Remove Like' : 'Like'; ?>
            </button>
        </form>
        <?php if ($recipe['user_id'] == $user_id): ?>
            <a href="edit_recipe.php?uuid=<?php echo $recipe['uuid']; ?>" class="btn btn-warning">Edit</a>
            <a href="delete_recipe.php?uuid=<?php echo $recipe['uuid']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</a>
        <?php endif; ?>
    <?php endif; ?>
    <a href="index.php" class="btn btn-secondary">Back to Recipes</a>

    <div class="comments-section">
        <h3>Comments</h3>
        <?php
        $comments_stmt = $conn->prepare("SELECT comments.*, users.username, users.email FROM comments JOIN users ON comments.user_id = users.id WHERE comments.recipe_uuid = :recipe_uuid ORDER BY comments.created_at DESC");
        $comments_stmt->bindParam(':recipe_uuid', $recipe['uuid']);
        $comments_stmt->execute();
        $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if ($comments): ?>
            <ul class="list-unstyled">
                <?php foreach ($comments as $comment): ?>
                    <li class="media mb-3">
                        <img class="mr-3 rounded-circle" src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($comment['email']))); ?>?s=64" alt="">
                        <div class="media-body">
                            <h5 class="mt-0 mb-1"><?php echo htmlspecialchars($comment['username']); ?></h5>
                            <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                            <div class="text-muted small"><?php echo htmlspecialchars($comment['created_at']); ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
        <form action="add_comment.php" method="POST">
            <div class="form-group">
                <label for="comment">Add a comment:</label>
                <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
            </div>
            <input type="hidden" name="recipe_uuid" value="<?php echo $recipe['uuid']; ?>">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php
    } else {
        echo "<div class='container'><h2>Recipe not found.</h2></div>";
    }
} else {
    echo "<div class='container'><h2>Invalid request.</h2></div>";
}

include __DIR__ . '/includes/footer.php';
ob_end_flush();
?>
