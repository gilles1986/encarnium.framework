<?php

namespace Framework\Logic\Utils;

abstract class jsonHandler {
  
  
  /**
   * parseJson
   * parses a given json file and returns it's content formated as common php array
   * @param string $filename The file destination of the json file
   * @return array $return Returns the parsed json file formatted as php array
   */
  public static function parseJson($filename){
    \Framework\Logger::debug('json file "'.$filename.'" was called', 'jsonHandler\parse');
    $return = null;
    if(file_exists($filename)){
      \Framework\Logger::debug('File "'.$filename.'" exists.', 'jsonHandler\parse');
      
      $filecontent = file_get_contents($filename);
      if($filecontent===false){
        \Framework\Logger::error('Error loading file "'.$filename.'": file not readable?', 'jsonHandler\parse');
        throw new \Exception('Error loading file "'.$filename.'": file not readable?');
      } else {
        $return = json_decode($filecontent);
        if(!$return){
          \Framework\Logger::error('Error decoding json file "'.$filename.'": not a valid json file?', 'jsonHandler\parse');
          throw new \Exception('Error decoding json file "'.$filename.'": not a valid json file?');
        }
      }
      
    } else {
      \Framework\Logger::error('Error loading file "'.$filename.'": file does not exist?', 'jsonHandler\parse');
      throw new \Exception('Error loading file "'.$filename.'": file does not exist?');
    }
    
    return $return;
  }
  
  /**
   * writeJson
   * writes a given php array to the given json file. If the file does not exist, the method tries to create it.
   * @param array $array The array to json_encode and write to file
   * @param string $filename The file to write/create
   * @return boolean
   */
  public static function writeJson($array, $filename){
    \Framework\Logger::debug('json file "'.$filename.'" was called', 'jsonHandler\write');
    $return = false;
    
    if(is_array($array)){
      if(file_exists($filename)){
        \Framework\Logger::debug('File "'.$filename.'" exists.', 'jsonHandler\write');
      } else {
        \Framework\Logger::error('Error reading file "'.$filename.'": file does not exist?', 'jsonHandler\write');
      }
      
      $array = json_encode($array);
      
      if($array){
        \Framework\Logger::debug('Array successfully json_encoded: '.PHP_EOL.'##START'.PHP_EOL.print_r($array,true).PHP_EOL.'ENDE##', 'jsonHandler\write');
        $putfile = file_put_contents($filename, $array);
        if(!$putfile){
          \Framework\Logger::error('Error writing to file "'.$filename.'"', 'jsonHandler\write');
        } else {
          \Framework\Logger::debug('Successfully writen Data to File "'.$filename.'"', 'jsonHandler\write');
          $return = true;
        }
      } else {
        \Framework\Logger::error('Error json_encoding: '.PHP_EOL.'##START'.PHP_EOL.print_r($array,true).PHP_EOL.'ENDE##', 'jsonHandler\write');
      }
      
    } else {
      \Framework\Logger::warning('Given Array is not an array:'.PHP_EOL.'##START'.PHP_EOL.print_r($array,true).PHP_EOL.'ENDE##','jsonHandler\write');
    }
    
    return $return;
  }
  
}
