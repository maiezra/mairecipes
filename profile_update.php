<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

session_start();

if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dietary_preferences = $_POST['dietary_preferences'];
    $allergies = $_POST['allergies'];
    $cooking_skill = $_POST['cooking_skill'];
    $cooking_frequency = $_POST['cooking_frequency'];
    $favorite_cuisine = $_POST['favorite_cuisine'];
    $dietary_restrictions = $_POST['dietary_restrictions'];
    $meal_preferences = implode(', ', $_POST['meal_preferences']);

    $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, dietary_preferences = :dietary_preferences, allergies = :allergies, cooking_skill = :cooking_skill, cooking_frequency = :cooking_frequency, favorite_cuisine = :favorite_cuisine, dietary_restrictions = :dietary_restrictions, meal_preferences = :meal_preferences WHERE id = :user_id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':dietary_preferences', $dietary_preferences);
    $stmt->bindParam(':allergies', $allergies);
    $stmt->bindParam(':cooking_skill', $cooking_skill);
    $stmt->bindParam(':cooking_frequency', $cooking_frequency);
    $stmt->bindParam(':favorite_cuisine', $favorite_cuisine);
    $stmt->bindParam(':dietary_restrictions', $dietary_restrictions);
    $stmt->bindParam(':meal_preferences', $meal_preferences);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        echo "Error: Could not update profile.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch();
}
?>

<div class="container">
    <h2>Update Profile</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="dietary_preferences">Dietary Preferences:</label>
            <select id="dietary_preferences" name="dietary_preferences" class="form-control">
                <option value="none" <?php if ($user['dietary_preferences'] == 'none') echo 'selected'; ?>>None</option>
                <option value="vegetarian" <?php if ($user['dietary_preferences'] == 'vegetarian') echo 'selected'; ?>>Vegetarian</option>
                <option value="vegan" <?php if ($user['dietary_preferences'] == 'vegan') echo 'selected'; ?>>Vegan</option>
                <option value="gluten_free" <?php if ($user['dietary_preferences'] == 'gluten_free') echo 'selected'; ?>>Gluten-Free</option>
                <option value="keto" <?php if ($user['dietary_preferences'] == 'keto') echo 'selected'; ?>>Keto</option>
                <option value="paleo" <?php if ($user['dietary_preferences'] == 'paleo') echo 'selected'; ?>>Paleo</option>
                <option value="pescatarian" <?php if ($user['dietary_preferences'] == 'pescatarian') echo 'selected'; ?>>Pescatarian</option>
                <option value="halal" <?php if ($user['dietary_preferences'] == 'halal') echo 'selected'; ?>>Halal</option>
                <option value="kosher" <?php if ($user['dietary_preferences'] == 'kosher') echo 'selected'; ?>>Kosher</option>
            </select>
        </div>

        <div class="form-group">
            <label for="allergies">Allergies:</label>
            <input type="text" id="allergies" name="allergies" class="form-control" value="<?php echo htmlspecialchars($user['allergies']); ?>" placeholder="E.g., peanuts, shellfish">
        </div>

        <div class="form-group">
            <label for="cooking_skill">Cooking Skill Level:</label>
            <select id="cooking_skill" name="cooking_skill" class="form-control">
                <option value="beginner" <?php if ($user['cooking_skill'] == 'beginner') echo 'selected'; ?>>Beginner</option>
                <option value="intermediate" <?php if ($user['cooking_skill'] == 'intermediate') echo 'selected'; ?>>Intermediate</option>
                <option value="advanced" <?php if ($user['cooking_skill'] == 'advanced') echo 'selected'; ?>>Advanced</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cooking_frequency">How often do you cook?</label>
            <select id="cooking_frequency" name="cooking_frequency" class="form-control">
                <option value="daily" <?php if ($user['cooking_frequency'] == 'daily') echo 'selected'; ?>>Daily</option>
                <option value="few_times_week" <?php if ($user['cooking_frequency'] == 'few_times_week') echo 'selected'; ?>>A few times a week</option>
                <option value="once_week" <?php if ($user['cooking_frequency'] == 'once_week') echo 'selected'; ?>>Once a week</option>
                <option value="few_times_month" <?php if ($user['cooking_frequency'] == 'few_times_month') echo 'selected'; ?>>A few times a month</option>
                <option value="rarely" <?php if ($user['cooking_frequency'] == 'rarely') echo 'selected'; ?>>Rarely</option>
            </select>
        </div>

        <div class="form-group">
            <label for="favorite_cuisine">Favorite Cuisine:</label>
            <select id="favorite_cuisine" name="favorite_cuisine" class="form-control">
                <option value="italian" <?php if ($user['favorite_cuisine'] == 'italian') echo 'selected'; ?>>Italian</option>
                <option value="chinese" <?php if ($user['favorite_cuisine'] == 'chinese') echo 'selected'; ?>>Chinese</option>
                <option value="mexican" <?php if ($user['favorite_cuisine'] == 'mexican') echo 'selected'; ?>>Mexican</option>
                <option value="indian" <?php if ($user['favorite_cuisine'] == 'indian') echo 'selected'; ?>>Indian</option>
                <option value="japanese" <?php if ($user['favorite_cuisine'] == 'japanese') echo 'selected'; ?>>Japanese</option>
                <option value="mediterranean" <?php if ($user['favorite_cuisine'] == 'mediterranean') echo 'selected'; ?>>Mediterranean</option>
                <option value="french" <?php if ($user['favorite_cuisine'] == 'french') echo 'selected'; ?>>French</option>
                <option value="thai" <?php if ($user['favorite_cuisine'] == 'thai') echo 'selected'; ?>>Thai</option>
                <option value="american" <?php if ($user['favorite_cuisine'] == 'american') echo 'selected'; ?>>American</option>
                <option value="other" <?php if ($user['favorite_cuisine'] == 'other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="dietary_restrictions">Do you have any dietary restrictions?</label>
            <select id="dietary_restrictions" name="dietary_restrictions" class="form-control">
                <option value="none" <?php if ($user['dietary_restrictions'] == 'none') echo 'selected'; ?>>None</option>
                <option value="low_carb" <?php if ($user['dietary_restrictions'] == 'low_carb') echo 'selected'; ?>>Low Carb</option>
                <option value="low_fat" <?php if ($user['dietary_restrictions'] == 'low_fat') echo 'selected'; ?>>Low Fat</option>
                <option value="low_sodium" <?php if ($user['dietary_restrictions'] == 'low_sodium') echo 'selected'; ?>>Low Sodium</option>
                <option value="low_sugar" <?php if ($user['dietary_restrictions'] == 'low_sugar') echo 'selected'; ?>>Low Sugar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="meal_preferences">Preferred meal types:</label>
            <select id="meal_preferences" name="meal_preferences[]" class="form-control" multiple>
                <option value="breakfast" <?php if (strpos($user['meal_preferences'], 'breakfast') !== false) echo 'selected'; ?>>Breakfast</option>
                <option value="lunch" <?php if (strpos($user['meal_preferences'], 'lunch') !== false) echo 'selected'; ?>>Lunch</option>
                <option value="dinner" <?php if (strpos($user['meal_preferences'], 'dinner') !== false) echo 'selected'; ?>>Dinner</option>
                <option value="snack" <?php if (strpos($user['meal_preferences'], 'snack') !== false) echo 'selected'; ?>>Snack</option>
                <option value="dessert" <?php if (strpos($user['meal_preferences'], 'dessert') !== false) echo 'selected'; ?>>Dessert</option>
                <option value="beverage" <?php if (strpos($user['meal_preferences'], 'beverage') !== false) echo 'selected'; ?>>Beverage</option>
            </select>
            <p class="comment">Select your preferred meal types (hold Ctrl or Cmd to select multiple).</p>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
