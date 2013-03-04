<?php


declare(ENCODING = 'utf-8');
namespace Framework;

/**
 * User: Gilles
 * Date: 04.03.13
 * Time: 21:48
 */ 
use Framework\EF\Exception;

class Mysqli {

  private $mysqli;

  private $host;
  private $user;
  private $password;
  private $db;


  public function __construct($options){

    try {
     array_walk($options, function($key, $value) {
       if(property_exists("\\Framework\\Mysqli", $key)) {
         $this->{$key} = $value;
       }
     });
    } catch(Exception $e) {

    }

    $this->mysqli = new \Mysqli();
  }

  public function connect() {
    $this->mysqli->real_connect();
  }

  private function getType($var) {
    $type = array("is_bool" => "i", "is_int" => "i", "is_string" => "s", "is_float" => "d");
    foreach($type as $key => $value) {
      if($key($var)) {
        return $value;
      }
    }
  }


  public function insert($table, $data) {

    $this->connect();


    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }

    $params = "";
    $paramQuestionMarks = "";
    $first = true;
    foreach($data as $key => $value) {
      if($first) { $first = false; } else {
        $params .= ",";
        $paramQuestionMarks .= ",";
      }
      $params .= "".$key." ";
      $paramQuestionMarks .= "? ";
    }

    echo  'INSERT INTO `'.$table.'` ('.$params.') VALUES ('.$paramQuestionMarks.')';
    $statement = $this->mysqli->prepare('INSERT INTO '.$table.' ('.$params.') VALUES ('.$paramQuestionMarks.')');

    var_dump($statement);

    foreach($data as $key => $value) {
      $statement->bind_param($this->getType($value), $value);
    }

    $statement->execute();

  }

}
