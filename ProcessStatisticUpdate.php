<?php
require_once('config.php');

$playerID = (int)$_POST['name_ID'];
$gameID = (int)$_POST['game_ID'];

if ($playerID !== 0 && $gameID !== 0) {
    require_once('Adaptation.php');
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno != 0) {
        echo "Failed to connect to database. Try again.";
    } elseif ($_POST["action"] === "Add/Modify Statistic") {
        require_once('PlayerStatistic.php');

        $playerStat = new PlayerStatistic(
            NULL,
            $_POST['kills'],
            $_POST['blocks'],
            $_POST['serving_aces'],
            $_POST['assists'],
            $_POST['digs']
        );

        // Check if a stat record already exists for this player and game
        $check = $db->prepare("SELECT stat_id FROM PlayerStats WHERE player_id = ? AND game_id = ?");
        $check->bind_param('ii', $playerID, $gameID);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Update existing record
            $query = "UPDATE PlayerStats SET kills = ?, blocks = ?, serving_aces = ?, assists = ?, digs = ? WHERE player_id = ? AND game_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                'iiiiiii',
                $playerStat->kills(),
                $playerStat->blocks(),
                $playerStat->serving_aces(),
                $playerStat->assists(),
                $playerStat->digs(),
                $playerID,
                $gameID
            );
        } else {
            // Insert new stat record
            $query = "INSERT INTO PlayerStats (
                          game_id, player_id, kills, blocks, serving_aces, assists, digs
                      ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                'iiiiiii',
                $gameID,
                $playerID,
                $playerStat->kills(),
                $playerStat->blocks(),
                $playerStat->serving_aces(),
                $playerStat->assists(),
                $playerStat->digs()
            );
        }

        $stmt->execute();

    } elseif ($_POST["action"] === "Delete Statistic") {
        $query = "DELETE FROM PlayerStats WHERE player_id = ? AND game_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ii', $playerID, $gameID);
        $stmt->execute();
    }
}

require('home_page.php');
?>
