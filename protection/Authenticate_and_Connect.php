<?php
function authenticate_and_connect()
{
  // Let's enable error handling with exceptions
  // Starting with PHP 8.1, the default behavior of mysqli_report was changed from MYSQLI_REPORT_OFF to MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
  // To be backward compatible, let's explicity turn on exception handling.  Assuming this function is called at the top of every PHP file, this
  // will Of course turn on exception handling everywhere.
  mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );      // default behavior since PHP 8.1


  try
  {
    // This function may be called many times while processing a single request, but we want to verify credentials only the first time
    // called and to have only one connection to the database per request.  So let's make sure we only create one connection and reuse
    // it when called again.  (The singleton design pattern)

    static $my_db_connection = null;    // initialized only the first time execution passes over this statement
                                        // Something not null means we've already been here, validated credentials, and connected to the database
    if( isset($my_db_connection) )  return $my_db_connection;







    // If the server was configured to use Basic Authentication, initialize the user and password.  This might have been done through a .htaccess file, for example.
    if( !isset($_SERVER['PHP_AUTH_USER'])  &&  !isset($_SERVER['PHP_AUTH_PW'])  &&  substr($_SERVER['HTTP_AUTHORIZATION'], 0, 6) == 'Basic ' )
    {
      list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    }

    // If either username or password has not yet been provided, ask for them
    if( !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) )  throw new Exception("Authentication required", 0);

    $PHP_AUTH_USER = trim($_SERVER['PHP_AUTH_USER']);
    $PHP_AUTH_PW   = trim($_SERVER['PHP_AUTH_PW']);

    // Design decision:  Neither the username nor the password can be blank
    if( $PHP_AUTH_USER === ''  ||  $PHP_AUTH_PW === '' )  throw new Exception("Invalid username and password combination", 0);






    // Now, let's validate what we got
    // Connect to the database as a Visitor then query for this visitor's credentials, which may not exist.
    require_once 'Adaptation.php';
    @$db_authentication_connection = new mysqli( DATA_BASE_HOST, Visitor, Accounts[Visitor], DATA_BASE_NAME );

    $query = "SELECT Accounts.Password, Roles.Name, Roles.DBAccountName  FROM Accounts, Roles WHERE Username = ? AND Accounts.Role = Roles.ID";

    @$stmt  = $db_authentication_connection->prepare($query);
    @$stmt->bind_param('s', $PHP_AUTH_USER);
    @$stmt->execute();

    @$stmt->store_result();
    @$stmt->bind_result($PW_Hash, $Role, $DBAccountName);
    @$stmt->fetch();

    // username verified by a successful query (a single row returned)
    if( $stmt->num_rows != 1  ||  !password_verify($PHP_AUTH_PW, $PW_Hash) )  throw new Exception("Invalid username and password combination", 0);

    // Visitor has been authenticated, now reconnect them to their role
    $stmt->close();
    $db_authentication_connection->close();

    @$my_db_connection = new mysqli( DATA_BASE_HOST, $DBAccountName, Accounts[$DBAccountName], DATA_BASE_NAME );
    printf("<h2>User \"%s\" logged in as a \"%s\"</h2><br>", $PHP_AUTH_USER, $Role);

    return $my_db_connection;
  }



  catch( mysqli_sql_exception $ex )
  {
    printf("Internal MySQLi Error: %s<br>In file '%s' @line %d<br>See site administrator<br>", $ex->getMessage(), $ex->getFile(), $ex->getLine());
  }

  catch( Exception $ex )
  {
    header('WWW-Authenticate: Basic realm="HW3 - Adding Minimal Security"');      // Prompt for authentication
    header('HTTP/1.0 401 Unauthorized');                                          // Deny access
    printf("Unauthorized: %s<br>In file '%s' @line %d<br>", $ex->getMessage(), $ex->getFile(), $ex->getLine());
  }

  exit;
} // function authenticate_and_connect()
?>
