<?php
  //////////////////////////////////////////////////////////////////////////////
  // This file contains sensitive information and *should* be stored outside the
  // web's document tree to prevent it from being seen or directly called by the
  // client.  But I didn't do that - yet.
  //////////////////////////////////////////////////////////////////////////////
  // Define constant path and location names
  $BASE_PATH      = $_SERVER['DOCUMENT_ROOT'].'/CPSC-431-Volleyball-Team';
  $DOC_PATH       = $BASE_PATH;                       // Let's put our html and php documents in the base path, for now
  $DATA_PATH      = $BASE_PATH.'/data';               // In practice you'd locate this outside the $DOCUMENT_ROOT so it's not accessible to bad actors
  $PROTECTED_PATH = $BASE_PATH.'/protected';          // In practice you'd locate this outside the $DOCUMENT_ROOT so it's not accessible to bad actors
?>
