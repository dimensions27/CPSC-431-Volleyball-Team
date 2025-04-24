<?php
require_once 'config.php';
require_once "$PROTECTED_PATH/Authenticate_and_Connect.php";
$db = authenticate_and_connect();

try
{
  $playerID = (int) $_POST['name_ID'];  // Database unique ID for player's name


  if( $playerID != 0 )  // Verify required fields are present
  {
    require_once('PlayerStatistic.php');

    // Create new object delegating parameter sanitization to class constructor
    $playerStat = new PlayerStatistic( NULL, $_POST['time'], $_POST['points'], $_POST['assists'], $_POST['rebounds']);

    $query = "INSERT INTO Statistics SET
                Player          = ?,
                PlayingTimeMin  = ?,
                PlayingTimeSec  = ?,
                Points          = ?,
                Assists         = ?,
                Rebounds        = ?";

    @$stmt = $db->prepare($query);

    list($minutes, $seconds) = explode(':', $playerStat->playingTime());
    @$stmt->bind_param('dddddd', $playerID,
                                $minutes,
                                $seconds,
                                $playerStat->pointsScored(),
                                $playerStat->assists(),
                                $playerStat->rebounds() );
    @$stmt->execute(); // ignore errors, for now.
  }

  header("Location: home_page.php");
  exit;
} // try block

catch( mysqli_sql_exception $ex )
{
  printf( "Internal MySQLi Error: %s<br>In file '%s' @line %d<br>See site administrator<br>", $ex->getMessage(), $ex->getFile(), $ex->getLine() );
  exit;
}

catch( Exception $ex )
{
  printf( "Internal Error: %s<br>In file '%s' @line %d<br>See site administrator<br>", $ex->getMessage(), $ex->getFile(), $ex->getLine() );
  exit;
}
?>
