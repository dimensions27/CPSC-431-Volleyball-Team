<?php
require_once('config.php');
require_once('Adaptation.php');
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = intval($_POST['role']);

    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno != 0) {
        $message = "Failed to connect to database.";
    } else {
        $check = $db->prepare("SELECT user_id FROM Users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "An account with this email already exists.";
        } else {
            $stmt = $db->prepare("INSERT INTO Users (first_name, last_name, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $first, $last, $email, $password, $role);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Volleyball App</title>
</head>
<body>
    <h2>Create an Account</h2>

    <?php if ($message): ?>
        <p style="color:red;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" action="register.php">
        <p><label>First Name:</label>
        <input type="text" name="first_name" required></p>

        <p><label>Last Name:</label>
        <input type="text" name="last_name" required></p>

        <p><label>Email:</label>
        <input type="email" name="email" required></p>

        <p><label>Password:</label>
        <input type="password" name="password" required></p>

        <p><label>Role:</label>
        <select name="role" required>
            <option value="" disabled selected>Select a role</option>
            <option value="2">Player</option>
            <option value="3">Coach</option>
            <option value="4">Manager</option>
        </select></p>

        <button type="submit">Register</button>
    </form>

    <p><a href="login.php">Already have an account? Log in here.</a></p>
</body>
</html>
