<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        echo "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $reset_request = $stmt->fetch();

        if ($reset_request) {
            $email = $reset_request['email'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);
            if ($stmt->execute()) {
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                $stmt->bindParam(':token', $token);
                $stmt->execute();
                echo "Password reset successful. You can now <a href='login.php'>login</a>.";
            } else {
                echo "Error: Could not reset password.";
            }
        } else {
            echo "Invalid token.";
        }
    }
} else if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    echo "No token provided.";
    exit();
}
?>

<div class="container">
    <h2>Reset Password</h2>
    <form method="POST" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
