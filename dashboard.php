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
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password !== $confirm_new_password) {
        $error_message = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = :new_password WHERE id = :id");
            $update_stmt->bindParam(':new_password', $new_password_hashed);
            $update_stmt->bindParam(':id', $user_id);

            if ($update_stmt->execute()) {
                $success_message = "Password successfully updated.";
            } else {
                $error_message = "Failed to update password. Please try again.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <span data-feather="home"></span>
                            My Account <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/saved_recipes.php">
                            <span data-feather="file"></span>
                            Saved Recipes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile_update.php">
                            <span data-feather="shopping-cart"></span>
                            Update Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/my_recipes.php">
                            <span data-feather="users"></span>
                            My Recipes
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Account</h1>
            </div>

            <!-- Change Password Section -->
            <div class="container">
                <h2>Change Password</h2>
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </main>
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

#sidebarMenu {
    height: 100vh;
    position: fixed;
}

#sidebarMenu .nav-link {
    color: #333;
}

#sidebarMenu .nav-link.active {
    color: #007bff;
}
</style>

<!-- Include Bootstrap JS and Feather Icons -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
</script>
