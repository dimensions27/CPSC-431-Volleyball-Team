<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('config.php');

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

if (!empty($last_name)) {
    require_once('Adaptation.php');
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
            // Check if player exists
            $checkExistence = "SELECT player_id FROM Players WHERE first_name = ? AND last_name = ?";
            $checkStmt = $db->prepare($checkExistence);
            $checkStmt->bind_param('ss', $first_name, $last_name);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                // update existing player
                $query = "UPDATE Players SET
                            street = ?, city = ?, state = ?, country = ?, zipcode = ?,
                            height = ?, weight = ?, team_id = ?
                          WHERE first_name = ? AND last_name = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param(
                    'sssssiiiss',
                    $street, $city, $state, $country, $zipCode,
                    $height, $weight, $team_id, $first_name, $last_name
                );
                $stmt->execute();
            } else {
                // create a new user
                $role_id = 2; //automatic role (player)
                $email = strtolower($first_name . "." . $last_name . "@example.com");
                $password = password_hash("changeme123", PASSWORD_DEFAULT);

                $userStmt = $db->prepare("INSERT INTO Users (first_name, last_name, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
                $userStmt->bind_param('ssssi', $first_name, $last_name, $email, $password, $role_id);
                $userStmt->execute();
                $user_id = $db->insert_id;

                // Create new player
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
    } elseif ($_POST["action"] == "Delete Name and Address") {
        $query = "DELETE FROM Players WHERE first_name = ? AND last_name = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss', $first_name, $last_name);
        $stmt->execute();
    }
}

require('home_page.php');
?>
