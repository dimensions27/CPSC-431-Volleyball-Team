<?php
/*
--   Run this script after "HW3 DDL.sql" so that the encrypted passwords get populated
--     php password_generation.php
--
--   The password is the user name prefixed with "!", for example
--   password_hash ("!donald.duck", PASSWORD_DEFAULT)
*/

@$db = new mysqli('localhost', 'Manager_Role', '!manager', 'CPSC_431_HW3');
// if connection was successful
if( $db->connect_errno != 0)
{
  echo "Error: Failed to make a MySQL connection, here is why:\n";
  echo "Errno: " . $db->connect_errno . "\n";
  echo "Error: " . $db->connect_error . "\n";
} else // Connection succeeded
{
  $query = "SELECT ID, UserName FROM Accounts ORDER BY ID";
  $stmt  = $db->prepare($query);
  @$stmt->execute(); // ignore errors, for now.
  $stmt->store_result();
  $stmt->bind_result($ID, $UserName);

  $query2 = "UPDATE Accounts SET Password = ? WHERE ID = ?";
  $stmt2 = $db->prepare($query2);

  $stmt->data_seek(0);
  while ($stmt->fetch())
  {
    $PW_Hash = password_hash ("!".$UserName, PASSWORD_DEFAULT );
    echo "$ID:  $UserName ($PW_Hash)\n";
    $stmt2->bind_param('sd', $PW_Hash, $ID );
    @$stmt2->execute(); // ignore errors, for now.
  }
}
?>
