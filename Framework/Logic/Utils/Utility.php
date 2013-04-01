<?php

namespace Framework\Logic\Utils;

class Utility {
  
  public static function splitArrayIntoKeyValue($array, $except=array()) {
    $keys = array();
    $values = array();
    
    foreach($array as $key=>$value) {
      if(!in_array($key, $except)) {
        array_push($keys, $key);
        array_push($values, $value);
      }
    }
    
    return array("keys"=>$keys, "values"=>$values);
  }
  
  public static function filterArray($array, $crits, $check=true, $negate=false ) {
    $newArray = array();
  
    foreach($array as $key=>$value) {
      // Wenn check true, dann alle Values Filtern
      if($check === true) {
        $checking = true;
      } else {
        if(in_array($key, $check)) {
          $checking = true;
        } else {
          $checking = false;
        }
      }
      
      if($checking) {
        if(is_string($crits)) {
          if($value == $crits) {
            $add = false;
          } else {
            $add = true;
          }
        } else if(is_array($crits)) {
          $add = true;
          for($i=0; $i < count($crits); $i++) {
            if($value == $cirts[$i]) {
              $add = false; 
            }
          }
        } else {
          throw new Exception("Falsche Art von Kriterien für filterArray");
        }
        
        if($negate) {
          $add = ($add) ? false : true;
        }
        
        if($add) {
          $newArray[$key] = $value;
        }
        
      } else {
        $newArray[$key] = $value;
      }
      
      
    }
  
    return $newArray;
  }
  
  public static function removeArrayEntries($array, $remove) {
    
    $newArray = array();
    
    foreach($array as $key=>$value) {
      if(!in_array($key, $remove)) {
        $newArray[$key] = $value;
      } 
    }
  }

  public static function array_merge_recursive_unique($array0, $array1)
  {
    $arrays = func_get_args();
    $remains = $arrays;

    // We walk through each arrays and put value in the results (without
    // considering previous value).
    $result = array();

    // loop available array
    foreach($arrays as $array) {

      // The first remaining array is $array. We are processing it. So
      // we remove it from remaing arrays.
      array_shift($remains);

      // We don't care non array param, like array_merge since PHP 5.0.
      if(is_array($array)) {
        // Loop values
        foreach($array as $key => $value) {
          if(is_array($value)) {
            // we gather all remaining arrays that have such key available
            $args = array();
            foreach($remains as $remain) {
              if(array_key_exists($key, $remain)) {
                array_push($args, $remain[$key]);
              }
            }

            if(count($args) > 2) {
              // put the recursion
              $result[$key] = call_user_func_array(__FUNCTION__, $args);
            } else {
              foreach($value as $vkey => $vval) {
                $result[$key][$vkey] = $vval;
              }
            }
          } else {
            // simply put the value
            $result[$key] = $value;
          }
        }
      }
    }
    return $result;
  }

}

?>