<?php
ob_start(); // Start output buffering
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $dietary_preferences = $_POST['dietary_preferences'];
    $allergies = $_POST['allergies'];
    $cooking_skill = $_POST['cooking_skill'];
    $cooking_frequency = $_POST['cooking_frequency'];
    $favorite_cuisine = $_POST['favorite_cuisine'];
    $dietary_restrictions = $_POST['dietary_restrictions'];
    $meal_preferences = implode(', ', $_POST['meal_preferences']);
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    // Validate password confirmation
    if ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, birthdate, dietary_preferences, allergies, cooking_skill, cooking_frequency, favorite_cuisine, dietary_restrictions, meal_preferences, newsletter) VALUES (:username, :password, :email, :birthdate, :dietary_preferences, :allergies, :cooking_skill, :cooking_frequency, :favorite_cuisine, :dietary_restrictions, :meal_preferences, :newsletter)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':dietary_preferences', $dietary_preferences);
        $stmt->bindParam(':allergies', $allergies);
        $stmt->bindParam(':cooking_skill', $cooking_skill);
        $stmt->bindParam(':cooking_frequency', $cooking_frequency);
        $stmt->bindParam(':favorite_cuisine', $favorite_cuisine);
        $stmt->bindParam(':dietary_restrictions', $dietary_restrictions);
        $stmt->bindParam(':meal_preferences', $meal_preferences);
        $stmt->bindParam(':newsletter', $newsletter);

        if ($stmt->execute()) {
            error_log("User registered: " . $username); // Debug message
            header("Location: registration_success.php");
            exit();
        } else {
            error_log("Registration failed: Could not register user."); // Debug message
            $error = "Could not register user. Please try again.";
        }
    }
}
ob_end_flush(); // Flush output buffer
?>


<div class="container">
    <h2>Register</h2>
    <form id="signUpForm" method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
            <p class="comment">Must be at least 3 characters long.</p>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <p class="comment">Must be a valid email address (e.g., user@example.com).</p>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <p class="comment">Must be at least 8 characters long and include an uppercase letter, a number, and a special character.</p>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            <p class="comment">Must match the password entered above.</p>
        </div>

        <div class="form-group">
            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" class="form-control" required>
            <p class="comment">You must be at least 13 years old to sign up.</p>
        </div>

        <p class="info-text"><b>The next questions are for getting to know you to match recipes just for you:</b></p>

        <div class="form-group">
            <label for="dietary_preferences">Dietary Preferences:</label>
            <select id="dietary_preferences" name="dietary_preferences" class="form-control">
                <option value="none">None</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="vegan">Vegan</option>
                <option value="gluten_free">Gluten-Free</option>
                <option value="keto">Keto</option>
                <option value="paleo">Paleo</option>
                <option value="pescatarian">Pescatarian</option>
                <option value="halal">Halal</option>
                <option value="kosher">Kosher</option>
            </select>
        </div>

        <div class="form-group">
            <label for="allergies">Allergies:</label>
            <input type="text" id="allergies" name="allergies" class="form-control" placeholder="E.g., peanuts, shellfish">
            <p class="comment">Optional: List any allergies you have.</p>
        </div>

        <div class="form-group">
            <label for="cooking_skill">Cooking Skill Level:</label>
            <select id="cooking_skill" name="cooking_skill" class="form-control">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cooking_frequency">How often do you cook?</label>
            <select id="cooking_frequency" name="cooking_frequency" class="form-control">
                <option value="daily">Daily</option>
                <option value="few_times_week">A few times a week</option>
                <option value="once_week">Once a week</option>
                <option value="few_times_month">A few times a month</option>
                <option value="rarely">Rarely</option>
            </select>
        </div>

        <div class="form-group">
            <label for="favorite_cuisine">Favorite Cuisine:</label>
            <select id="favorite_cuisine" name="favorite_cuisine" class="form-control">
                <option value="italian">Italian</option>
                <option value="chinese">Chinese</option>
                <option value="mexican">Mexican</option>
                <option value="indian">Indian</option>
                <option value="japanese">Japanese</option>
                <option value="mediterranean">Mediterranean</option>
                <option value="french">French</option>
                <option value="thai">Thai</option>
                <option value="american">American</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="dietary_restrictions">Do you have any dietary restrictions?</label>
            <select id="dietary_restrictions" name="dietary_restrictions" class="form-control">
                <option value="none">None</option>
                <option value="low_carb">Low Carb</option>
                <option value="low_fat">Low Fat</option>
                <option value="low_sodium">Low Sodium</option>
                <option value="low_sugar">Low Sugar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="meal_preferences">Preferred meal types:</label>
            <select id="meal_preferences" name="meal_preferences[]" class="form-control" multiple>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="dinner">Dinner</option>
                <option value="snack">Snack</option>
                <option value="dessert">Dessert</option>
                <option value="beverage">Beverage</option>
            </select>
            <p class="comment">Select your preferred meal types (hold Ctrl or Cmd to select multiple).</p>
        </div>

        <div class="form-group">
            <label for="newsletter">Subscribe to our newsletter:</label>
            <input type="checkbox" id="newsletter" name="newsletter" value="yes">
        </div>

        <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
