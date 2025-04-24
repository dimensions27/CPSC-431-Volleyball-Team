<?php
  //////////////////////////////////////////////////////////////////////////////
  // This file contains sensitive information and *should* be stored outside the
  // web's document tree to prevent it from being seen or directly called by the
  // client.  But I didn't do that - yet.
  //////////////////////////////////////////////////////////////////////////////

  // Remember, unlike variables, defined constants are used without the $ prefix
  define('DATA_BASE_NAME', 'VolleyballTeam');
  define('DATA_BASE_HOST', 'localhost');

  define('Visitor', 'No_Role');
  define('Accounts', array(  Visitor      =>'',                  // Keep this consistent with "HW3 DDL.sql"
                            'Player_Role' =>'!player',
                            'Coach_Role'  =>'!coach',
                            'Manager_Role'=>'!manager') );
?>
