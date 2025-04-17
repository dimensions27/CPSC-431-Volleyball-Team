<?php
  define('DATA_BASE_NAME', 'VolleyBall');
  define('DATA_BASE_HOST', 'localhost');
  
  // Default role to use when not logged in, such as to register a new user or to get end-user credentials
  define('NO_ROLE', 'Executive Manager');

  // DB User name to password look up table
  $DBPasswords = ['Observer'          => 'Password1',
                  'Users'             => 'Password2',
                  'Executive Manager' => 'Password3'];
?>
