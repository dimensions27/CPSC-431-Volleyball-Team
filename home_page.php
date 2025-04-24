<?php
require_once 'config.php';
require_once "$PROTECTED_PATH/Authenticate_and_Connect.php";
$db = authenticate_and_connect();

require_once 'config.php';

try
{
?>

<!DOCTYPE html>
<html>
  <head>
    <title>CPSC 431 HW-3</title>
  </head>
  <body>
    <h1 style="text-align:center">Cal State Fullerton Basketball Statistics</h1>

    <?php
      require_once 'Address.php';
      require_once 'PlayerStatistic.php';

      //////// TO-DO:  Begin Student Region ///////////
      /// Build query to retrieve player's name, address, and averaged statistics from the joined Team Roster and Statistics tables
      $query = "SELECT
                  People.ID,
                  People.Name_First,
                  People.Name_Last,
                  People.Street,
                  People.City,
                  People.State,
                  People.Country,
                  People.ZipCode,

                  COUNT( Statistics.Player              ),
                  ROUND( AVG(Statistics.PlayingTimeMin) ),
                  ROUND( AVG(Statistics.PlayingTimeSec) ),
                  ROUND( AVG(Statistics.Points)         ),
                  ROUND( AVG(Statistics.Assists)        ),
                  ROUND( AVG(Statistics.Rebounds)       )
                FROM People LEFT JOIN Statistics ON
                  Statistics.Player = People.ID
                GROUP BY
                  People.ID
                ORDER BY
                  People.Name_Last,
                  People.Name_First";


        /// Prepare, execute, store results, and bind results to local variables
        $stmt = $db->prepare($query);
        // no query parameters to bind
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($Name_ID,
                           $Name_First,
                           $Name_Last,
                           $Street,
                           $City,
                           $State,
                           $Country,
                           $ZipCode,

                           $GamesPlayed,
                           $PlayingTimeMin,
                           $PlayingTimeSec,
                           $Points,
                           $Assists,
                           $Rebounds);
        //////// END-TO-DO:  End Student Region ///////////
    ?>

    <table style="width: 100%; border:0px solid black; border-collapse:collapse;">
      <tr>
        <th style="width: 40%;">Name and Address</th>
        <th style="width: 60%;">Statistics</th>
      </tr>
      <tr>
        <td style="vertical-align:top; border:1px solid black;">
          <!-- FORM to enter Name and Address -->
          <form action="processAddressUpdate.php" method="post">
            <table style="margin: 0px auto; border: 0px; border-collapse:separate;">
              <tr>
                <td style="text-align: right; background: lightblue;">First Name</td>
                <td><input type="text" name="firstName" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Last Name</td>
               <td><input type="text" name="lastName" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Street</td>
               <td><input type="text" name="street" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">City</td>
                <td><input type="text" name="city" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">State</td>
                <td><input type="text" name="state" value="" size="35" maxlength="100"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Country</td>
                <td><input type="text" name="country" value="" size="20" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Zip</td>
                <td><input type="text" name="zipCode" value="" size="10" maxlength="10"/></td>
              </tr>

              <tr>
               <td colspan="2" style="text-align: center;"><input type="submit" value="Add Name and Address" /></td>
              </tr>
            </table>
          </form>
        </td>

        <td style="vertical-align:top; border:1px solid black;">
          <!-- FORM to enter game statistics for a particular player -->
          <form action="processStatisticUpdate.php" method="post">
            <table style="margin: 0px auto; border: 0px; border-collapse:separate;">
              <tr>
                <td style="text-align: right; background: lightblue;">Name (Last, First)</td>
<!--            <td><input type="text" name="name" value="" size="50" maxlength="500"/></td>  -->
                <td><select name="name_ID" required>
                  <option value="" selected disabled hidden>Choose player's name here</option>

                  <?php
                    //////// TO-DO:  Begin Student Region ///////////
                    /// for each row of data returned,
                    ///   construct an Address object providing first and last name
                    ///   emit an option for the pull down list such that
                    ///     the displayed name is retrieved from the Address object
                    ///     the value submitted is the unique ID for that player
                    /// for example:
                    ///     <option value="101">Duck, Daisy</option>
                    $stmt->data_seek(0);
                    while( $stmt->fetch() )
                    {
                      $player = new Address([$Name_First, $Name_Last]);
                      echo "<option value=\"$Name_ID\">".$player->name()."</option>\n";
                    }
                    //////// END-TO-DO:  End Student Region ///////////
                  ?>

                </select></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Playing Time (min:sec)</td>
               <td><input type="text" name="time" value="" size="5" maxlength="5"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Points Scored</td>
               <td><input type="text" name="points" value="" size="3" maxlength="3"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Assists</td>
                <td><input type="text" name="assists" value="" size="2" maxlength="2"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Rebounds</td>
                <td><input type="text" name="rebounds" value="" size="2" maxlength="2"/></td>
              </tr>

              <tr>
               <td colspan="2" style="text-align: center;"><input type="submit" value="Add Statistic" /></td>
              </tr>
            </table>
          </form>
        </td>
      </tr>
    </table>


    <h2 style="text-align:center">Player Statistics</h2>

    <?php

      //////// TO-DO:  Begin Student Region ///////////
      /// emit the number of rows (records) in the table
      echo "Number of Records:  ".$stmt->num_rows."<br/>";
      //////// END-TO-DO:  End Student Region ///////////

    ?>

    <table style="border:1px solid black; border-collapse:collapse;">
      <tr>
        <th colspan="1" style="vertical-align:top; border:1px solid black; background: lightgreen;"></th>
        <th colspan="2" style="vertical-align:top; border:1px solid black; background: lightgreen;">Player</th>
        <th colspan="1" style="vertical-align:top; border:1px solid black; background: lightgreen;"></th>
        <th colspan="4" style="vertical-align:top; border:1px solid black; background: lightgreen;">Statistic Averages</th>
      </tr>
      <tr>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;"></th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Name</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Address</th>

        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Games Played</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Time on Court</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Points Scored</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Number of Assists</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Number of Rebounds</th>
      </tr>

      <?php
        $fmt_style = 'style="vertical-align:top; border:1px solid black;"';


        //////// TO-DO:  Begin Student Region ///////////
        /// Initialize the loop by
        ///  1) seeking to the first row of data returned
        ///  2) initializing your displayed row number
        $stmt->data_seek(0);
        $row_number = 0;

        /// Now, for each row (record) of data retrieved from the database emit the html to populate a row in the table
        /// for example:
        ///  <tr>
        ///    <td  style="vertical-align:top; border:1px solid black;">1</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">Dog, Pluto</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">1313 S. Harbor Blvd.<br/>Anaheim, CA 92808-3232<br/>USA</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">1</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">10:0</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">18</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">2</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">4</td>
        ///  </tr>
        /// or if there exists no statistical data for the player
        ///  <tr>
        ///    <td  style="vertical-align:top; border:1px solid black;">2</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">Duck, Daisy</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">1180 Seven Seas Dr.<br/>Lake Buena Vista, FL 32830<br/>USA</td>
        ///    <td  style="vertical-align:top; border:1px solid black;">0</td>
        ///    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        ///    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        ///    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        ///    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        ///  </tr>
        ///
        /// 1.0 Loop until end of data
        while( $stmt->fetch() )
        {
          /// 1.1  Construct Address and PlayerStatistic objects supplying as constructor parameters the retrieved database columns
          $player = new Address([$Name_First, $Name_Last], $Street, $City, $State, $Country, $ZipCode);
          $stat   = new PlayerStatistic([$Name_First, $Name_Last], [$PlayingTimeMin, $PlayingTimeSec], $Points, $Assists, $Rebounds);

          /// 1.2  Emit table row data using appropriate getters from the Address and PlayerStatistic objects
          echo "      <tr>\n";
          echo "        <td  $fmt_style>".++$row_number."</td>\n";
          echo "        <td  $fmt_style>".$player->name()."</td>\n";
          echo "        <td  $fmt_style>".$player->street()."<br/>"
                                         .$player->city().', '.$player->state().' '.$player->zip().'<br/>'
                                         .$player->country()."</td>\n";
          echo "        <td  $fmt_style>".$GamesPlayed."</td>\n";

          /// 1.3  If games have been played
          if($GamesPlayed >0)
          {
            ///  1.3.1 Fill the table's columns with the average playing time, points scored, assists, and rebounds
            echo "        <td  $fmt_style>".$stat->playingTime()."</td>\n";
            echo "        <td  $fmt_style>".$stat->pointsScored()."</td>\n";
            echo "        <td  $fmt_style>".$stat->assists()."</td>\n";
            echo "        <td  $fmt_style>".$stat->rebounds()."</td>\n";
          }
          ///  1.4  Otherwise (no games have been played)
          else
          {
            ///  1.4.1 Fill the table's columns with a dark background (e.g., #e6e6e6) and no data
            echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>\n";
            echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>\n";
            echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>\n";
            echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>\n";
          }
          echo "      </tr>\n";
        }
//////// END-TO-DO:  End Student Region ///////////
      ?>

    </table>

    <?php
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

  </body>
</html>
