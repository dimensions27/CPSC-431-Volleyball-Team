<?php
  require_once( 'StartSession.php' );
  

  // Proceed only for authenticated users
  if( ! authenticatedUser() )
  {
    echo "You must be logged in to access this page<br/>";
    echo "-- display login form --<br/>";
    return;
  }
  
  
  echo "You are loggin as '" . $_SESSION['UserName'] . "' as '" . $_SESSION['UserRole'] . "'<br/>";
  echo "-- display login form --<br/>";
?>
 
