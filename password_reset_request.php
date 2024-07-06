<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        if ($stmt->execute()) {
            $reset_link = "http://maiez.mtacloud.co.il/password_reset.php?token=$token";
            mail($email, "Password Reset", "Click here to reset your password: $reset_link");
            echo "Password reset link has been sent to your email.";
        } else {
            echo "Error: Could not generate reset link.";
        }
    } else {
        echo "No account found with that email address.";
    }
}
?>

<div class="container">
    <h2>Reset Password</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
