<?php
// Carlos Lopez
class PlayerInformation {

// VARIABLES
  //private $name = array('FIRST'=>"", 'LAST'=>null);

  private $firstName    = "";
  private $lastName = "";
  private $street       = "";
  private $city         = "";
  private $state        = "";
  private $country      = "";
  private $zipCode          = "";
  private $weight         = 0;
  private $height         = 0;
  private $teamName     = "";

// CONSTRUCT

  public function __construct($firstName="", $lastName = "", $street="", $city="", $state="", $country="", $zipCode="", $height="", $weight="", $teamName="") {
      //$this->name($name);
      $this->firstName($firstName);
      $this->lastName($lastName);
      $this->street($street);
      $this->city($city);
      $this->state($state);
      $this->country($country);
      $this->zipCode($zipCode);
      $this->height($height);
      $this->weight($weight);
      $this->teamName($teamName);
  }

// GETTERS/SETTERS

  function name() {
    return $this->lastName().", ".$this->firstName();
  }

  function firstName() {
    if( func_num_args() == 0 )
     {
       return $this->firstName;
     }
     
     // void pointsScored($value)
     else if( func_num_args() == 1 )
     {
      if (is_string(func_get_arg(0))) {
        $this->firstName = htmlspecialchars(trim(func_get_arg(0)));
      }
     }
     
     return $this;
  }

  function lastName() {
    if( func_num_args() == 0 )
     {
       return $this->lastName;
     }
     
     // void pointsScored($value)
     else if( func_num_args() == 1 )
     {
      if (is_string(func_get_arg(0))) {
        $this->lastName = htmlspecialchars(trim(func_get_arg(0)));
    };
     }
     
     return $this;
  }

  function street() {
    if (func_num_args() == 0) {
      return $this->street;
    }
    else if (func_num_args() == 1) {
      if (is_string(func_get_arg(0))) {
        $this->street = htmlspecialchars(trim(func_get_arg(0)));
      }
    }
    return $this;
  }

  function city() {
    if (func_num_args() == 0) {
      return $this->city;
    }
    else if (func_num_args() == 1) {
      if (is_string(func_get_arg(0))) {
        $this->city = htmlspecialchars(trim(func_get_arg(0)));
      }
    }
    return $this;
  }

  function state() {
    if (func_num_args() == 0) {
      return $this->state;
    }
    else if (func_num_args() == 1) {
      if (is_string(func_get_arg(0))) {
        $this->state = htmlspecialchars(trim(func_get_arg(0)));
      }
    }
    return $this;
  }

  function country() {
    if (func_num_args() == 0) {
      return $this->country;
    }
    else if (func_num_args() == 1) {
      if (is_string(func_get_arg(0))) {
        $this->country = htmlspecialchars(trim(func_get_arg(0)));
      }
    }
    return $this;
  }

  function zipCode() {
    if (func_num_args() == 0) {
      return $this->zipCode;
    }
    else if (func_num_args() == 1) {
      if (is_string(func_get_arg(0))) {
        $this->zipCode = htmlspecialchars(trim(func_get_arg(0)));
      }
    }
    return $this;
  }

  function weight() 
   {  
     if( func_num_args() == 0 )
     {
       return $this->weight;
     }
     
     // void assists($value)
     else if( func_num_args() == 1 )
     {
       $this->weight = (int)func_get_arg(0);
     }
     
     return $this;
   }

   function height() 
   {  
     if( func_num_args() == 0 )
     {
       return $this->height;
     }
     
     // void assists($value)
     else if( func_num_args() == 1 )
     {
       $this->height = (int)func_get_arg(0);
     }
     
     return $this;
   }

   function teamName() {
    if (func_num_args() == 0) return $this->teamName;
    if (func_num_args() == 1) {
      if (is_string(func_get_arg(0))) {
        $this->teamName = htmlspecialchars(trim(func_get_arg(0)));
      }
    }
    return $this;
   }

  function __toString() {
    return (var_export($this, true));
  }

  function toTsv() {
    return implode("\t", [$this->name(), $this->street(), $this->city(),
                          $this->state(), $this->country(), $this->zipCode(), $this->height(), $this->weight(), $this->teamName()]);
  }

  function fromTSV(string $tsvString) {
    list($firstName, $lastName, $street, $city, $state, $country, $zipCode, $height, $weight, $teamName) = explode("\t", $tsvString);
    $this->firstName($firstName);
    $this->lastName($lastName);
    $this->street($street);
    $this->city($city);
    $this->state($state);
    $this->country($country);
    $this->zipCode($zipCode);
    $this->height($height);
    $this->weight($weight);
    $this->teamName($teamName);
  }

}