<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('config.php');
require_once('Adaptation.php');
require_once('PlayerInformation.php');
require_once('PlayerStatistic.php');

$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

if ($db->connect_errno) {
    echo "Failed to connect to database.";
    exit;
}

$query = "
    SELECT 
        p.first_name, p.last_name, p.street, p.city, p.state, p.country, p.zipcode,
        p.height, p.weight,
        COUNT(s.stat_id) AS games_played,
        ROUND(AVG(s.kills)) AS avg_kills,
        ROUND(AVG(s.blocks)) AS avg_blocks,
        ROUND(AVG(s.serving_aces)) AS avg_aces,
        ROUND(AVG(s.assists)) AS avg_assists,
        ROUND(AVG(s.digs)) AS avg_digs
    FROM Players p
    LEFT JOIN PlayerStats s ON p.player_id = s.player_id
    GROUP BY p.player_id
    ORDER BY p.last_name, p.first_name
";

$stmt = $db->prepare($query);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($first, $last, $street, $city, $state, $country, $zip, $height, $weight, $games, $kills, $blocks, $aces, $assists, $digs);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volleyball Stats Viewer</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: lightblue; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Cal State Fullerton Volleyball - Public Stats View</h1>
    <p style="text-align:center;"><a href="login.php">Return to Login</a></p>

    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Address</th>
            <th>Height (cm)</th>
            <th>Weight (lbs)</th>
            <th>Games</th>
            <th>Kills</th>
            <th>Blocks</th>
            <th>Aces</th>
            <th>Assists</th>
            <th>Digs</th>
        </tr>
        <?php
        $index = 1;
        while ($stmt->fetch()) {
            $fullName = "$last, $first";
            $fullAddress = "$street<br>$city, $state $zip<br>$country";

            echo "<tr>";
            echo "<td>$index</td>";
            echo "<td>$fullName</td>";
            echo "<td>$fullAddress</td>";
            echo "<td>$height</td>";
            echo "<td>$weight</td>";
            echo "<td>$games</td>";
            echo "<td>$kills</td>";
            echo "<td>$blocks</td>";
            echo "<td>$aces</td>";
            echo "<td>$assists</td>";
            echo "<td>$digs</td>";
            echo "</tr>";

            $index++;
        }
        ?>
    </table>
</body>
</html>
