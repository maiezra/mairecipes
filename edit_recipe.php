<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if (isset($_GET['uuid'])) {
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE uuid = :uuid AND user_id = :user_id");
    $stmt->bindParam(':uuid', $_GET['uuid']);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        echo "<div class='alert alert-danger'>Recipe not found or you do not have permission to edit this recipe.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $title = trim($_POST['title']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $dietary_preferences = $_POST['dietary_preferences'];
    $cooking_skill = $_POST['cooking_skill'];
    $favorite_cuisine = $_POST['favorite_cuisine'];
    $dietary_restrictions = $_POST['dietary_restrictions'];
    $meal_preferences = implode(', ', $_POST['meal_preferences']);
    $cooking_time = trim($_POST['cooking_time']);

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($ingredients === '') {
        $errors[] = 'Ingredients are required.';
    }

    if ($instructions === '') {
        $errors[] = 'Instructions are required.';
    }

    if ($cooking_time === '' || $cooking_time <= 0) {
        $errors[] = 'Cooking time must be a positive number.';
    }

    // Handle file upload
    $photo = $_FILES['photo'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($photo["name"]);
    $upload_ok = 1;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($photo['error'] == UPLOAD_ERR_NO_FILE) {
        $target_file = $recipe['photo']; // Use existing photo if no new photo is uploaded
    } else if ($photo['error'] != UPLOAD_ERR_OK) {
        $errors[] = 'Error uploading the file.';
    } else {
        // Check if file is an actual image
        $check = getimagesize($photo["tmp_name"]);
        if ($check === false) {
            $errors[] = 'File is not an image.';
            $upload_ok = 0;
        }

        // Check file size
        if ($photo["size"] > 500000) {
            $errors[] = 'Sorry, your file is too large.';
            $upload_ok = 0;
        }

        // Allow certain file formats
        if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif") {
            $errors[] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            $upload_ok = 0;
        }

        if ($upload_ok && empty($errors)) {
            if (!move_uploaded_file($photo["tmp_name"], $target_file)) {
                $errors[] = 'Sorry, there was an error uploading your file.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE recipes SET title = :title, ingredients = :ingredients, instructions = :instructions, dietary_preferences = :dietary_preferences, cooking_skill = :cooking_skill, favorite_cuisine = :favorite_cuisine, dietary_restrictions = :dietary_restrictions, meal_preferences = :meal_preferences, cooking_time = :cooking_time, photo = :photo WHERE uuid = :uuid AND user_id = :user_id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':ingredients', $ingredients);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':dietary_preferences', $dietary_preferences);
        $stmt->bindParam(':cooking_skill', $cooking_skill);
        $stmt->bindParam(':favorite_cuisine', $favorite_cuisine);
        $stmt->bindParam(':dietary_restrictions', $dietary_restrictions);
        $stmt->bindParam(':meal_preferences', $meal_preferences);
        $stmt->bindParam(':cooking_time', $cooking_time);
        $stmt->bindParam(':photo', $target_file);
        $stmt->bindParam(':uuid', $_GET['uuid']);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Recipe updated successfully.</div>";
            // Refresh the recipe data
            $stmt = $conn->prepare("SELECT * FROM recipes WHERE uuid = :uuid AND user_id = :user_id");
            $stmt->bindParam(':uuid', $_GET['uuid']);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error_info = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Error: Could not update recipe. Error Info: " . htmlspecialchars($error_info[2]) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    }
}
?>

<div class="container">
    <h2>Edit Recipe</h2>
    <form id="editRecipeForm" method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Recipe Title:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="ingredients">Ingredients:</label>
            <textarea id="ingredients" name="ingredients" class="form-control" required><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="instructions">Instructions:</label>
            <textarea id="instructions" name="instructions" class="form-control" required><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="dietary_preferences">Special Tag:</label>
            <select id="dietary_preferences" name="dietary_preferences" class="form-control">
                <option value="none" <?php echo ($recipe['dietary_preferences'] == 'none') ? 'selected' : ''; ?>>None</option>
                <option value="vegetarian" <?php echo ($recipe['dietary_preferences'] == 'vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                <option value="vegan" <?php echo ($recipe['dietary_preferences'] == 'vegan') ? 'selected' : ''; ?>>Vegan</option>
                <option value="gluten_free" <?php echo ($recipe['dietary_preferences'] == 'gluten_free') ? 'selected' : ''; ?>>Gluten-Free</option>
                <option value="keto" <?php echo ($recipe['dietary_preferences'] == 'keto') ? 'selected' : ''; ?>>Keto</option>
                <option value="paleo" <?php echo ($recipe['dietary_preferences'] == 'paleo') ? 'selected' : ''; ?>>Paleo</option>
                <option value="pescatarian" <?php echo ($recipe['dietary_preferences'] == 'pescatarian') ? 'selected' : ''; ?>>Pescatarian</option>
                <option value="halal" <?php echo ($recipe['dietary_preferences'] == 'halal') ? 'selected' : ''; ?>>Halal</option>
                <option value="kosher" <?php echo ($recipe['dietary_preferences'] == 'kosher') ? 'selected' : ''; ?>>Kosher</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cooking_skill">Cooking Skill Level:</label>
            <select id="cooking_skill" name="cooking_skill" class="form-control">
                <option value="beginner" <?php echo ($recipe['cooking_skill'] == 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                <option value="intermediate" <?php echo ($recipe['cooking_skill'] == 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                <option value="advanced" <?php echo ($recipe['cooking_skill'] == 'advanced') ? 'selected' : ''; ?>>Advanced</option>
            </select>
        </div>

        <div class="form-group">
            <label for="favorite_cuisine">Cuisine:</label>
            <select id="favorite_cuisine" name="favorite_cuisine" class="form-control">
                <option value="italian" <?php echo ($recipe['favorite_cuisine'] == 'italian') ? 'selected' : ''; ?>>Italian</option>
                <option value="chinese" <?php echo ($recipe['favorite_cuisine'] == 'chinese') ? 'selected' : ''; ?>>Chinese</option>
                <option value="mexican" <?php echo ($recipe['favorite_cuisine'] == 'mexican') ? 'selected' : ''; ?>>Mexican</option>
                <option value="indian" <?php echo ($recipe['favorite_cuisine'] == 'indian') ? 'selected' : ''; ?>>Indian</option>
                <option value="japanese" <?php echo ($recipe['favorite_cuisine'] == 'japanese') ? 'selected' : ''; ?>>Japanese</option>
                <option value="mediterranean" <?php echo ($recipe['favorite_cuisine'] == 'mediterranean') ? 'selected' : ''; ?>>Mediterranean</option>
                <option value="french" <?php echo ($recipe['favorite_cuisine'] == 'french') ? 'selected' : ''; ?>>French</option>
                <option value="thai" <?php echo ($recipe['favorite_cuisine'] == 'thai') ? 'selected' : ''; ?>>Thai</option>
                <option value="american" <?php echo ($recipe['favorite_cuisine'] == 'american') ? 'selected' : ''; ?>>American</option>
                <option value="other" <?php echo ($recipe['favorite_cuisine'] == 'other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="dietary_restrictions">Diet:</label>
            <select id="dietary_restrictions" name="dietary_restrictions" class="form-control">
                <option value="none" <?php echo ($recipe['dietary_restrictions'] == 'none') ? 'selected' : ''; ?>>None</option>
                <option value="low_carb" <?php echo ($recipe['dietary_restrictions'] == 'low_carb') ? 'selected' : ''; ?>>Low Carb</option>
                <option value="low_fat" <?php echo ($recipe['dietary_restrictions'] == 'low_fat') ? 'selected' : ''; ?>>Low Fat</option>
                <option value="low_sodium" <?php echo ($recipe['dietary_restrictions'] == 'low_sodium') ? 'selected' : ''; ?>>Low Sodium</option>
                <option value="low_sugar" <?php echo ($recipe['dietary_restrictions'] == 'low_sugar') ? 'selected' : ''; ?>>Low Sugar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="meal_preferences">Types:</label>
            <select id="meal_preferences" name="meal_preferences[]" class="form-control" multiple>
                <option value="breakfast" <?php echo (strpos($recipe['meal_preferences'], 'breakfast') !== false) ? 'selected' : ''; ?>>Breakfast</option>
                <option value="lunch" <?php echo (strpos($recipe['meal_preferences'], 'lunch') !== false) ? 'selected' : ''; ?>>Lunch</option>
                <option value="dinner" <?php echo (strpos($recipe['meal_preferences'], 'dinner') !== false) ? 'selected' : ''; ?>>Dinner</option>
                <option value="snack" <?php echo (strpos($recipe['meal_preferences'], 'snack') !== false) ? 'selected' : ''; ?>>Snack</option>
                <option value="dessert" <?php echo (strpos($recipe['meal_preferences'], 'dessert') !== false) ? 'selected' : ''; ?>>Dessert</option>
                <option value="beverage" <?php echo (strpos($recipe['meal_preferences'], 'beverage') !== false) ? 'selected' : ''; ?>>Beverage</option>
            </select>
            <p class="comment">Select your preferred meal types (hold Ctrl or Cmd to select multiple).</p>
        </div>

        <div class="form-group">
            <label for="cooking_time">Cooking Time (in minutes):</label>
            <input type="number" id="cooking_time" name="cooking_time" class="form-control" value="<?php echo htmlspecialchars($recipe['cooking_time']); ?>" required>
        </div>

        <div class="form-group">
            <label for="photo">Upload New Photo:</label>
            <input type="file" name="photo" class="form-control">
            <small>If you don't upload a new photo, the existing photo will be used.</small>
        </div>

        <button type="submit" class="btn btn-primary">Update Recipe</button>
    </form>
</div>

<script>
document.getElementById('editRecipeForm').addEventListener('submit', function(event) {
    var form = event.target;
    var valid = true;
    var errorMessage = '';

    // Check title
    if (form.title.value.trim() === '') {
        valid = false;
        errorMessage += 'Title is required.<br>';
    }

    // Check ingredients
    if (form.ingredients.value.trim() === '') {
        valid = false;
        errorMessage += 'Ingredients are required.<br>';
    }

    // Check instructions
    if (form.instructions.value.trim() === '') {
        valid = false;
        errorMessage += 'Instructions are required.<br>';
    }

    // Check cooking time
    if (form.cooking_time.value.trim() === '' || form.cooking_time.value <= 0) {
        valid = false;
        errorMessage += 'Cooking time must be a positive number.<br>';
    }

    if (!valid) {
        event.preventDefault();
        var errorMessageElement = document.getElementById('errorMessage');
        errorMessageElement.innerHTML = errorMessage;
        errorMessageElement.style.display = 'block';
    }
});
</script>

<div id="errorMessage" class="alert alert-danger" style="display: none;"></div>

<?php include __DIR__ . '/includes/footer.php'; ?>
