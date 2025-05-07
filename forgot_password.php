<?php
require_once('config.php');
require_once('Adaptation.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno != 0) {
        $message = "Database connection failed.";
    } else {
        $stmt = $db->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // generates a new random password
            $newPassword = bin2hex(random_bytes(4));
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // updates user password in user table in database
            $update = $db->prepare("UPDATE Users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $hashedPassword, $email);
            $update->execute();

            // just simulates password sent to email.
            $message = "A new password has been set. Simulated email: <strong>$newPassword</strong>";
        } else {
            $message = "No account found with that email.";
        }

        $stmt->close();
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - CSUF Volleyball</title>
</head>
<body>
    <h2>Reset Your Password</h2>

    <?php if ($message): ?>
        <p style="color:blue;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" action="forgot_password.php">
        <p><label>Email:</label>
        <input type="email" name="email" required></p>
        <p><button type="submit">Send Reset Password</button></p>
    </form>

    <p><a href="login.php">Back to login</a></p>
</body>
</html>
