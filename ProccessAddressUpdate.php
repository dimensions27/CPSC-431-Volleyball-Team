<?php
require_once 'config.php';
require_once "$PROTECTED_PATH/Authenticate_and_Connect.php";
$db = authenticate_and_connect();

try
{
  // create short variable names
  $firstName     = trim( preg_replace("/\t|\R/",' ',$_POST['firstName']) );
  $lastName      = trim( preg_replace("/\t|\R/",' ',$_POST['lastName'])  );
  $street        = trim( preg_replace("/\t|\R/",' ',$_POST['street'])    );
  $city          = trim( preg_replace("/\t|\R/",' ',$_POST['city'])      );
  $state         = trim( preg_replace("/\t|\R/",' ',$_POST['state'])     );
  $country       = trim( preg_replace("/\t|\R/",' ',$_POST['country'])   );
  $zipCode       = trim( preg_replace("/\t|\R/",' ',$_POST['zipCode'])   );

  if( empty($firstName) ) $firstName = null;
  if( empty($lastName)  ) $lastName  = null;
  if( empty($street)    ) $street    = null;
  if( empty($city)      ) $city      = null;
  if( empty($state)     ) $state     = null;
  if( empty($country)   ) $country   = null;
  if( empty($zipCode)   ) $zipCode   = null;


  if( ! empty($lastName) ) // Verify required fields are present
  {
    $query = "INSERT INTO People SET
                Name_First = ?,
                Name_Last  = ?,
                Street     = ?,
                City       = ?,
                State      = ?,
                Country    = ?,
                ZipCode    = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sssssss', $firstName, $lastName, $street, $city, $state, $country, $zipCode);
    @$stmt->execute();
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
