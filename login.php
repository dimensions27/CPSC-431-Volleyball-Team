<?php
session_start();
require_once('config.php');

if (isset($_POST['name']) && isset($_POST['password'])) {
    $username = $_POST['name'];
    $password = $_POST['password'];

    require_once('Adaptation.php');
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno != 0) {
        echo "Database connection failed.";
        exit();
    }

    // get the hashed password from the db
    $query = "SELECT user_id, role_id, password FROM Users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $role_id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $role_id;

            header("Location: home_page.php");
            exit();
        }
    }

    echo "<h3>Invalid credentials. Please try again.</h3>";
}

// for observer access
if (isset($_POST['observer'])) {
    $_SESSION['username'] = "observer";
    $_SESSION['role_id'] = 1;
    header("Location: home_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Volleyball Stats</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <p><label>Email:</label>
            <input type="text" name="name" required></p>
            <p><label>Password:</label>
            <input type="password" name="password" required></p>
            <p><button type="submit">Log In</button></p>
        </form>

        <form method="post">
            <button type="submit" name="observer">Just want to view stats (Observer)</button>
        </form>

        <p><a href="register.php">Create an account</a></p>

        <p><a href="forgot_password.php">Forgot password?</a></p>

    </div>
</body>
</html>
