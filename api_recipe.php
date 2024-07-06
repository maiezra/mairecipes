<?php
ob_start();
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

if (isset($_GET['id'])) {
    $api_id = $_GET['id'];
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
        $recipe_id = $recipe['idMeal'];

        $user_stmt = $conn->prepare("SELECT dietary_preferences, dietary_restrictions, favorite_cuisine FROM users WHERE id = :user_id");
        $user_stmt->bindParam(':user_id', $user_id);
        $user_stmt->execute();
        $user_preferences = $user_stmt->fetch(PDO::FETCH_ASSOC);


        // Check if the recipe is already liked by the user
        $like_stmt = $conn->prepare("SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_api_id = :recipe_id");
        $like_stmt->bindParam(':user_id', $user_id);
        $like_stmt->bindParam(':recipe_id', $recipe_id);
        $like_stmt->execute();
        $is_liked = $like_stmt->rowCount() > 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_action'])) {
            if ($is_liked) {
                $stmt = $conn->prepare("DELETE FROM recipe_likes WHERE user_id = :user_id AND recipe_api_id = :recipe_id");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':recipe_id', $recipe_id);
                $stmt->execute();
                $is_liked = false;
            } else {
                $stmt = $conn->prepare("INSERT INTO recipe_likes (user_id, recipe_api_id) VALUES (:user_id, :recipe_id)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':recipe_id', $recipe_id);
                $stmt->execute();
                $is_liked = true;
            }
            ob_end_clean();
            header("Location: saved_recipes.php");
            exit();
        }
?>

<div class="container">
    <h2><?php echo htmlspecialchars($recipe['strMeal']); ?></h2>
    <img src="<?php echo htmlspecialchars($recipe['strMealThumb']); ?>" class="img-fluid" alt="Recipe Photo">
    <p><strong>Category:</strong> <?php echo htmlspecialchars($recipe['strCategory']); ?></p>
    <p><strong>Area:</strong> <?php echo htmlspecialchars($recipe['strArea']); ?></p>
    <p><strong>Instructions:</strong><br><?php echo nl2br(htmlspecialchars($recipe['strInstructions'])); ?></p>
    

    <?php if (is_logged_in()): ?>
        <form method="POST" action="">
            <button type="submit" name="like_action" class="btn <?php echo $is_liked ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <?php echo $is_liked ? 'Remove Like' : 'Like'; ?>
            </button>
        </form>
    <?php endif; ?>

    <div class="comments-section">
        <h3>Comments</h3>
        <?php
        $comments_stmt = $conn->prepare("SELECT api_comments.*, users.username, users.email FROM api_comments JOIN users ON api_comments.user_id = users.id WHERE api_comments.recipe_api_id = :recipe_api_id ORDER BY api_comments.created_at DESC");
        $comments_stmt->bindParam(':recipe_api_id', $recipe_id);
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
        <form action="add_api_comment.php" method="POST">
            <div class="form-group">
                <label for="comment">Add a comment:</label>
                <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
            </div>
            <input type="hidden" name="recipe_api_id" value="<?php echo $recipe_id; ?>">
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
ob_end_flush(); // Завершаем и очищаем буфер вывода
?> 
