<?php
require_once('config.php');
require_once('Adaptation.php');
session_start();

$first_name  = $_POST['first_name'];
$last_name   = $_POST['last_name'];
$street      = $_POST['street'];
$city        = $_POST['city'];
$state       = $_POST['state'];
$country     = $_POST['country'];
$zipCode     = $_POST['zipCode'];
$height      = isset($_POST['height']) ? (int)$_POST['height'] : null;
$weight      = isset($_POST['weight']) ? (int)$_POST['weight'] : null;
$team_id     = isset($_POST['team_id']) ? (int)$_POST['team_id'] : null;

foreach (['first_name', 'last_name', 'street', 'city', 'state', 'country', 'zipCode'] as $field) {
    if (empty($$field)) $$field = null;
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

@$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);
if ($db->connect_errno != 0) {
    echo "Failed to connect to database. Try again.";
    exit(1);
}

if ($_POST["action"] == "Add/Modify Name and Address") {
    if (!empty($first_name) && !empty($last_name) &&
        !empty($street) && !empty($city) && !empty($state) &&
        !empty($country) && !empty($zipCode) &&
        $height !== null && $weight !== null &&
        $team_id !== null) {

        if ($role_id == 2) {
            // Check if the name matches the logged-in user
            $nameCheck = $db->prepare("SELECT first_name, last_name FROM Users WHERE user_id = ?");
            $nameCheck->bind_param("i", $user_id);
            $nameCheck->execute();
            $nameCheck->bind_result($myFirst, $myLast);
            $nameCheck->fetch();
            $nameCheck->close();

            if (strcasecmp(trim($first_name), trim($myFirst)) !== 0 || strcasecmp(trim($last_name), trim($myLast)) !== 0) {
                echo "<p style='color:red; text-align:center; font-weight:bold;'>⚠️ You cannot edit someone else's personal information.</p>";
                echo "<p style='text-align:center;'><a href='home_page.php'>Go Back</a></p>";
                exit();
            }

            // Proceed with modifying own info
            $check = $db->prepare("SELECT player_id FROM Players WHERE user_id = ?");
            $check->bind_param("i", $user_id);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $query = "UPDATE Players SET
                            street = ?, city = ?, state = ?, country = ?, zipcode = ?,
                            height = ?, weight = ?, team_id = ?
                          WHERE user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param(
                    'sssssiiii',
                    $street, $city, $state, $country, $zipCode,
                    $height, $weight, $team_id, $user_id
                );
                $stmt->execute();
            } else {
                $query = "INSERT INTO Players (
                            user_id, team_id, first_name, last_name, street, city, state, country, zipcode, height, weight
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bind_param(
                    'iissssssssi',
                    $user_id, $team_id, $first_name, $last_name,
                    $street, $city, $state, $country, $zipCode, $height, $weight
                );
                $stmt->execute();
            }
        } else {
            // Manager/Coach logic
            $checkExistence = "SELECT player_id, user_id FROM Players WHERE first_name = ? AND last_name = ?";
            $checkStmt = $db->prepare($checkExistence);
            $checkStmt->bind_param('ss', $first_name, $last_name);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $checkStmt->bind_result($player_id, $target_user_id);
                $checkStmt->fetch();

                $query = "UPDATE Players SET
                            street = ?, city = ?, state = ?, country = ?, zipcode = ?,
                            height = ?, weight = ?, team_id = ?
                          WHERE user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param(
                    'sssssiiii',
                    $street, $city, $state, $country, $zipCode,
                    $height, $weight, $team_id, $target_user_id
                );
                $stmt->execute();
            } else {
                // Check for existing user by name
                $existingUserStmt = $db->prepare("SELECT user_id FROM Users WHERE first_name = ? AND last_name = ?");
                $existingUserStmt->bind_param("ss", $first_name, $last_name);
                $existingUserStmt->execute();
                $existingUserStmt->store_result();

                if ($existingUserStmt->num_rows > 0) {
                    $existingUserStmt->bind_result($existing_user_id);
                    $existingUserStmt->fetch();
                    $user_id = $existing_user_id;
                } else {
                    // Create new user
                    $role_id = 2;
                    $email = strtolower($first_name . "." . $last_name . "@example.com");
                    $password = password_hash("changeme123", PASSWORD_DEFAULT);
                    $userStmt = $db->prepare("INSERT INTO Users (first_name, last_name, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
                    $userStmt->bind_param('ssssi', $first_name, $last_name, $email, $password, $role_id);
                    $userStmt->execute();
                    $user_id = $db->insert_id;
                }

                // Insert into Players
                $query = "INSERT INTO Players (
                            user_id, team_id, first_name, last_name, street, city, state, country, zipcode, height, weight
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bind_param(
                    'iissssssssi',
                    $user_id, $team_id, $first_name, $last_name,
                    $street, $city, $state, $country, $zipCode, $height, $weight
                );
                $stmt->execute();
            }
        }
    }
} elseif ($_POST["action"] == "Delete Name and Address") {
    if ($role_id != 2) {
        $query = "DELETE FROM Players WHERE first_name = ? AND last_name = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss', $first_name, $last_name);
        $stmt->execute();
    } else {
        echo "Players are not allowed to delete records.";
    }
}

header("Location: home_page.php");
exit();
?>
