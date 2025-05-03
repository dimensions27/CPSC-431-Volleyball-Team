<?php
class PlayerStatistic
{
   // Instance attributes
   private $name         = array('FIRST'=>"", 'LAST'=>null); 
   private $kills = 0;
   private $blocks = 0;
   private $serving_aces     = 0;
   private $assists     = 0;
   private $digs = 0;
   
   // Operations
   
   // name() prototypes:
   //   string name()                          returns name in "Last, First" format.
   //                                          If no first name assigned, then return in "Last" format.
   //                                         
   //   void name(string $value)               set object's $name attribute in "Last, First" 
   //                                          or "Last" format.
   //                                         
   //   void name(array $value)                set object's $name attribute in [first, last] format
   //
   //   void name(string $first, string $last) set object's $name attribute
   function name() 
   {
     // string name()
     if( func_num_args() == 0 )
     {
       if( empty($this->name['FIRST']) ) return $this->name['LAST'];
       else                              return $this->name['LAST'].', '.$this->name['FIRST']; 
     }
     
     // void name($value)
     else if( func_num_args() == 1 )
     {
       $value = func_get_arg(0);
       
       if( is_string($value) ) 
       {
         $value = explode(',', $value); // convert string to array 
         
         if ( count($value) >= 2 ) $this->name['FIRST'] = htmlspecialchars(trim($value[1]));
         else                      $this->name['FIRST'] = '';
         
         $this->name['LAST']  = htmlspecialchars(trim($value[0]));          
       }
       
       else if( is_array ($value) )
       {
         if ( count($value) >= 2 ) $this->name['LAST'] = htmlspecialchars(trim($value[1]));
         else                      $this->name['LAST'] = '';
         
         $this->name['FIRST']  = htmlspecialchars(trim($value[0])); 
       }         
     }
     
     // void name($first_name, $last_name)
     else if( func_num_args() == 2 )
     {
         $this->name['FIRST'] = htmlspecialchars(trim(func_get_arg(0)));
         $this->name['LAST']  = htmlspecialchars(trim(func_get_arg(1))); 
     }
     
     return $this;
   }

   function kills() {
    if ( func_num_args() == 0 ) {
      return $this->kills;
    }
    else if (func_num_args() == 1) {
      $this->kills = (int)func_get_arg(0);
    }
    return $this;
   }

   // assists() prototypes:
   //   int assists()               returns the number of scoring assists.
   //                                         
   //   void assists(int $value)    set object's $assists attribute
   function assists() 
   {  
     // int assists()
     if( func_num_args() == 0 )
     {
       return $this->assists;
     }
     
     // void assists($value)
     else if( func_num_args() == 1 )
     {
       $this->assists = (int)func_get_arg(0);
     }
     
     return $this;
   }
   
   function blocks() 
   {
     if( func_num_args() == 0 )
     {
       return $this->blocks;
     }
     else if( func_num_args() == 1 )
     {
       $this->blocks = (int)func_get_arg(0);
     }
     
     return $this;
   }

   function serving_aces() 
   {
     if( func_num_args() == 0 )
     {
       return $this->serving_aces;
     }
     else if( func_num_args() == 1 )
     {
       $this->serving_aces = (int)func_get_arg(0);
     }
     
     return $this;
   }
   
   
   function digs() 
   {  
     if( func_num_args() == 0 )
     {
       return $this->digs;
     }
     else if( func_num_args() == 1 )
     {
       $this->digs = (int)func_get_arg(0);
     }
     
     return $this;
   }
   
   
   
   
   
   function __construct($name="", $kills=0, $blocks=0, $serving_aces=0, $assists=0, $digs=0)
   {
     // if $name contains at least one tab character, assume all attributes are provided in 
     // a tab separated list.  Otherwise assume $name is just the player's name.
     if (is_array($name)) {
      $name = implode(" ", $name); // This could join the array elements with a space
     }
     if (!is_null($name) && strpos($name, "\t") !== false) // Note, can't check for "true" because strpos() only returns the boolean value "false", never "true"
     {
       // assign each argument a value from the tab delineated string respecting relative positions
       list($name, $kills, $blocks, $serving_aces, $assists, $digs) = explode("\t", $name);
     }
     
     // delegate setting attributes so validation logic is applied
     $this->name($name);
     $this->kills($kills);
     $this->blocks($blocks);
     $this->serving_aces($serving_aces);
     $this->assists($assists);
     $this->digs($digs);
   }
   
   
   
   
   
   
   
   
   function __toString()
   {
     return (var_export($this, true));
   }
   
   
   
   
   
   
   

   // Returns a tab separated value (TSV) string containing the contents of all instance attributes   
   function toTSV()
   {
       return implode("\t", [$this->name(), $this->kills(), $this->blocks(), $this->serving_aces(), $this->assists(), $this->digs()]);
   }
   
   
   
   
   
   
   

   // Sets instance attributes to the contents of a string containing ordered, tab separated values 
   function fromTSV(string $tsvString)
   {
     // assign each argument a value from the tab delineated string respecting relative positions
     list($name, $kills, $blocks, $serving_aces, $assists, $digs) = explode("\t", $tsvString);
     $this->name($name);
     $this->kills($kills);
     $this->blocks($blocks);
     $this->serving_aces($serving_aces);
     $this->assists($assists);
     $this->digs($digs);
   }
} // end class PlayerStatistic

?>

