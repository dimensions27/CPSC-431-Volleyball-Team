<?php 
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

require_once('config.php');
?>

<!DOCTYPE html>
<html>
<head>
  <title>CSUF Volleyball</title>
</head>
<body>
  <h1 style="text-align:center">Cal State Fullerton Volleyball Statistics</h1>

  <?php
    require_once('PlayerInformation.php');
    require_once('PlayerStatistic.php');
    require_once('Adaptation.php');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno != 0) {
      echo "Unable to connect to the database. Please try again.";
      exit(1);
    } else {
      $query = "SELECT
            p.player_id,
            p.first_name,
            p.last_name,
            p.street,
            p.city,
            p.state,
            p.country,
            p.zipcode,
            p.height,
            p.weight,
            t.team_name,
            COUNT(ps.stat_id),
            SUM(ps.kills),
            SUM(ps.blocks),
            SUM(ps.serving_aces),
            SUM(ps.assists),
            SUM(ps.digs)
            FROM Players p
            LEFT JOIN PlayerStats ps ON ps.player_id = p.player_id
            LEFT JOIN Teams t ON p.team_id = t.team_id
            GROUP BY p.player_id
            ORDER BY p.last_name, p.first_name";
      $stmt = $db->prepare($query);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result(
        $player_id, $first_name, $last_name, $street, $city, $state,
        $country, $zipcode, $height, $weight, $team_name,
        $games_played, $kills, $blocks, $serving_aces, $assists, $digs
      );
    }
  ?>

  <!-- Top Section: Side-by-Side Forms -->
  <div style="display: flex; justify-content: space-between; gap: 40px; padding: 20px;">
    <!-- Player Info Form -->
    <div style="flex: 1; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f0f8ff;">
      <h2 style="text-align:center;">Player Info Form</h2>
      <form action="processInformationUpdate.php" method="post">
        <table>
          <tr><td>First Name:</td><td><input type="text" name="first_name"></td></tr>
          <tr><td>Last Name:</td><td><input type="text" name="last_name"></td></tr>
          <tr><td>Street:</td><td><input type="text" name="street"></td></tr>
          <tr><td>City:</td><td><input type="text" name="city"></td></tr>
          <tr><td>State:</td><td><input type="text" name="state"></td></tr>
          <tr><td>Country:</td><td><input type="text" name="country"></td></tr>
          <tr><td>Zip Code:</td><td><input type="text" name="zipCode"></td></tr>
          <tr><td>Height:</td><td><input type="text" name="height"></td></tr>
          <tr><td>Weight:</td><td><input type="text" name="weight"></td></tr>
          <tr>
            <td>Team:</td>
            <td>
              <select name="team_id">
                <option disabled selected>Select team</option>
                <?php
                  $teamQuery = "SELECT team_id, team_name FROM Teams ORDER BY team_name";
                  $teamStmt = $db->prepare($teamQuery);
                  $teamStmt->execute();
                  $teamStmt->bind_result($tid, $tname);
                  while ($teamStmt->fetch()) {
                    echo "<option value=\"$tid\">$tname</option>";
                  }
                  $teamStmt->close();
                ?>
              </select>
            </td>
          </tr>
          <tr><td colspan="2" style="text-align:center;">
            <input type="submit" name="action" value="Add/Modify Name and Address">
            <input type="submit" name="action" value="Delete Name and Address">
          </td></tr>
        </table>
      </form>
    </div>

    <!-- Player Stats Form -->
    <div style="flex: 1; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fffaf0;">
      <h2 style="text-align:center;">Player Statistics Form</h2>
      <form action="processStatisticUpdate.php" method="post">
        <table>
          <tr>
            <td>Player:</td>
            <td>
              <select name="name_ID" required>
                <option disabled selected>Select player</option>
                <?php
                  $stmt->data_seek(0);
                  while ($stmt->fetch()) {
                    $info = new PlayerInformation($first_name, $last_name);
                    echo "<option value=\"$player_id\">".$info->name()."</option>\n";
                  }
                  $stmt->data_seek(0);
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Game:</td>
            <td>
              <select name="game_ID" required>
                <option disabled selected>Select game</option>
                <?php
                  $gameQuery = "SELECT game_id, opponent, game_date FROM Games ORDER BY game_date DESC";
                  $gameStmt = $db->prepare($gameQuery);
                  $gameStmt->execute();
                  $gameStmt->bind_result($gid, $opponent, $gdate);
                  while ($gameStmt->fetch()) {
                    echo "<option value=\"$gid\">$gdate vs $opponent</option>";
                  }
                  $gameStmt->close();
                ?>
              </select>
            </td>
          </tr>
          <tr><td>Kills:</td><td><input type="text" name="kills"></td></tr>
          <tr><td>Blocks:</td><td><input type="text" name="blocks"></td></tr>
          <tr><td>Serving Aces:</td><td><input type="text" name="serving_aces"></td></tr>
          <tr><td>Assists:</td><td><input type="text" name="assists"></td></tr>
          <tr><td>Digs:</td><td><input type="text" name="digs"></td></tr>
          <tr><td colspan="2" style="text-align:center;">
            <input type="submit" name="action" value="Add/Modify Statistic">
            <input type="submit" name="action" value="Delete Statistic">
          </td></tr>
        </table>
      </form>
    </div>
  </div>

  <!-- Overview Tables -->
  <hr>
  <h2 style="text-align:center;">Player Statistics Overview</h2>
  <p style="text-align:center;">Number of Records: <?= $stmt->num_rows ?></p>

  <table border="1" cellpadding="5" cellspacing="0" style="margin:auto;">
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Team</th>
      <th>Address</th>
      <th>Height (cm)</th>
      <th>Weight (lbs)</th>
      <th>Games Played</th>
      <th>Total Kills</th>
      <th>Total Blocks</th>
      <th>Total Serving Aces</th>
      <th>Total Assists</th>
      <th>Total Digs</th>
    </tr>
    <?php
      $row_number = 0;
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $info = new PlayerInformation($first_name, $last_name, $street, $city, $state, $country, $zipcode, $height, $weight, $team_name);
        $stats = new PlayerStatistic(null, $kills, $blocks, $serving_aces, $assists, $digs);
        echo "<tr>";
        echo "<td>".++$row_number."</td>";
        echo "<td>".$info->name()."</td>";
        echo "<td>".$info->teamName()."</td>";
        echo "<td>".$info->street()."<br>".$info->city().", ".$info->state()." ".$info->zipCode()."<br>".$info->country()."</td>";
        echo "<td>".$info->height()."</td>";
        echo "<td>".$info->weight()."</td>";
        echo "<td>".$games_played."</td>";
        echo "<td>".$stats->kills()."</td>";
        echo "<td>".$stats->blocks()."</td>";
        echo "<td>".$stats->serving_aces()."</td>";
        echo "<td>".$stats->assists()."</td>";
        echo "<td>".$stats->digs()."</td>";
        echo "</tr>";
      }
    ?>
  </table>

  <hr>
  <h2 style="text-align:center;">Game-by-Game Player Statistics</h2>

  <?php
  $gameQuery = "SELECT
                  g.game_date,
                  g.opponent,
                  g.location,
                  t.team_name,
                  p.first_name,
                  p.last_name,
                  ps.kills,
                  ps.blocks,
                  ps.serving_aces,
                  ps.assists,
                  ps.digs
                FROM PlayerStats ps
                JOIN Players p ON ps.player_id = p.player_id
                JOIN Games g ON ps.game_id = g.game_id
                JOIN Teams t ON p.team_id = t.team_id
                ORDER BY g.game_date DESC, p.last_name";
  $gameStmt = $db->prepare($gameQuery);
  $gameStmt->execute();
  $gameStmt->store_result();
  $gameStmt->bind_result($game_date, $opponent, $location, $team_name, $first, $last, $kills, $blocks, $aces, $assists, $digs);
  ?>

  <table border="1" cellpadding="5" cellspacing="0" style="margin:auto;">
    <tr>
      <th>Date</th>
      <th>Opponent</th>
      <th>Location</th>
      <th>Team</th>
      <th>Player</th>
      <th>Kills</th>
      <th>Blocks</th>
      <th>Aces</th>
      <th>Assists</th>
      <th>Digs</th>
    </tr>
    <?php while ($gameStmt->fetch()): ?>
      <tr>
        <td><?= htmlspecialchars($game_date) ?></td>
        <td><?= htmlspecialchars($opponent) ?></td>
        <td><?= htmlspecialchars($location) ?></td>
        <td><?= htmlspecialchars($team_name) ?></td>
        <td><?= htmlspecialchars("$last, $first") ?></td>
        <td><?= $kills ?></td>
        <td><?= $blocks ?></td>
        <td><?= $aces ?></td>
        <td><?= $assists ?></td>
        <td><?= $digs ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <form action="logout.php" method="post" style="text-align:center; margin-top:20px;">
    <button type="submit">Logout</button>
  </form>
</body>
</html>
