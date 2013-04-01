<?php



namespace Framework;

/**
 * User: Gilles
 * Date: 04.03.13
 * Time: 21:48
 *
 */


use Framework\EF\Exception;


// @TODO : Logging einbauen und keine Exceptions mehr werfen

class Mysqli {

  private $fetchStyle = MYSQL_ASSOC;

  private $mysqli;

  private $host;
  private $user;
  private $password;
  private $db;


  public function __construct($options){
     try {

     array_walk($options, function($value, $key, $object) {
       if(property_exists("\\Framework\\Mysqli", $key)) {

         $func = "set".ucfirst($key);
         $object->$func($value);

       }
     }, $this);
    } catch(Exception $e) {

    }

    $this->mysqli = new \Mysqli($this->host, $this->user, $this->password, $this->db);


    /* check connection */
    if (mysqli_connect_errno()) {
      throw new Exception($this->mysqli->error);
    }

    /* change db to world db */
    $this->mysqli->select_db($this->db);

  }


  public function connect() {
    $this->mysqli->real_connect($this->host, $this->user, $this->password, $this->db);
    if (mysqli_connect_errno()) {
      throw new Exception($this->mysqli->error);
    } else {
      return true;
    }
  }

  public function close() {
    if(!$this->mysqli->close()) {
      throw new Exception($this->mysqli->error);
    } else {
      return true;
    }
  }


  private function getType($var) {
    $type = array("is_bool" => "i", "is_int" => "i", "is_string" => "s", "is_float" => "d");
    foreach($type as $key => $value) {
      if($key($var)) {
        return $value;
      }
    }
  }





  public function select($query) {
    $result = $this->mysqli->query($query);
    if ($this->mysqli->error) {
      throw new Exception($this->mysqli->error);
    }

    if(!$result) {
      return false;
    }

    $results = $result->fetch_all($this->fetchStyle);
    $result->close();
    return $results;

  }

  public function andDelete($table,$conditions) {
    if(!is_array($conditions)) {
      if(is_string($conditions)) {
        $conditions = array($conditions);
      } else {
        throw new Exception("Condition must be String or Array");
      }
    }

    $condition = implode(" AND ", $conditions );
    $result = $this->mysqli->query("DELETE FROM `$table` WHERE ".$condition);
    if ($this->mysqli->error) {
      throw new Exception($this->mysqli->error);

    }

    if(!$result) {
      throw new Exception($this->mysqli->error);
    } else {
      return true;
    }

  }


  public function delete($table,$condition) {

      if(!is_string($condition)) {
        throw new Exception("Condition must be String");
      }


    $result = $this->mysqli->query("DELETE FROM `$table` WHERE ".$condition);
    if ($this->mysqli->error) {
      throw new Exception($this->mysqli->error);
    }

    if(!$result) {
      throw new Exception($this->mysqli->error);
    } else {
      return true;
    }

  }


  public function orDelete($table,$conditions) {
    if(!is_array($conditions)) {
      if(is_string($conditions)) {
        $conditions = array($conditions);
      } else {
        throw new Exception("Condition must be String or Array");
      }
    }

    $condition = implode(" OR ", $conditions );
    $result = $this->mysqli->query("DELETE FROM `$table` WHERE ".$condition);
    if ($this->mysqli->error) {
      throw new Exception($this->mysqli->error);

    }

    if(!$result) {
      throw new Exception($this->mysqli->error);
    } else {
      return true;
    }

  }

  public function insert($table, $data) {

   if (mysqli_connect_errno()) {
     throw new Exception($this->mysqli->error);

    }

    $params = "";
    $paramQuestionMarks = "";
    $first = true;
    foreach($data as $key => $value) {
      if($first) { $first = false; } else {
        $params .= ",";
        $paramQuestionMarks .= ",";
      }
      $params .= "`".$key."` ";
      $paramQuestionMarks .= "? ";
    }

   // echo  'INSERT INTO `'.$table.'` ('.$params.') VALUES ('.$paramQuestionMarks.')';
    $statement = $this->mysqli->prepare('INSERT INTO '.$table.' ('.$params.') VALUES ('.$paramQuestionMarks.')');
    if($statement) {
      $values = array();
      $types = "";
      foreach($data as $key => $value) {
        array_push($values, $value);
        $types .= $this->getType($value);
        //$statement->bind_param($this->getType($value), $value);
      }

      call_user_func_array(array($statement, 'bind_param'), array_merge(array($types),$this->refValues($values) ));
      //$statement->bind_param($params)
      if(!$statement->execute()) {
        throw new Exception($statement->error);
      } else {
        return true;
      }
    } else {
      throw new Exception($this->mysqli->error);
    }
  }


  public function update($table, $data, $condition) {

    if (mysqli_connect_errno()) {
      throw new Exception($this->mysqli->error);

    }

    $params = "";
    $first = true;
    foreach($data as $key => $value) {
      if($first) { $first = false; } else {
        $params .= ",";

      }
      $params .= "`".$key."` = ? ";

    }

    // echo  'UPDATE '.$table.' Set '.$params.' WHERE '.$condition;
    $statement = $this->mysqli->prepare('UPDATE '.$table.' Set '.$params.' WHERE '.$condition);
    if($statement) {
      $values = array();
      $types = "";
      foreach($data as $key => $value) {
        array_push($values, $value);
        $types .= $this->getType($value);
        //$statement->bind_param($this->getType($value), $value);
      }

      call_user_func_array(array($statement, 'bind_param'), array_merge(array($types),$this->refValues($values) ));
      //$statement->bind_param($params)
      if(!$statement->execute()) {
        throw new Exception($statement->error);
      } else {
        return true;
      }
    } else {
      var_dump($this->mysqli->error);
    }
  }



  public function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
      $refs = array();
      foreach($arr as $key => $value)
        $refs[$key] = &$arr[$key];
      return $refs;
    }
    return $arr;
  }

  public function setFetchStyle($fetchStyle)
  {
    $this->fetchStyle = $fetchStyle;
  }

  public function getFetchStyle()
  {
    return $this->fetchStyle;
  }


  public function setDb($db)
    {
      $this->db = $db;
    }

    public function getDb()
    {
      return $this->db;
    }

    public function setHost($host)
    {
      $this->host = $host;
    }

    public function getHost()
    {
      return $this->host;
    }

    public function setMysqli($mysqli)
    {
      $this->mysqli = $mysqli;
    }

    public function getMysqli()
    {
      return $this->mysqli;
    }

    public function setPassword($password)
    {
      $this->password = $password;
    }

    public function getPassword()
    {
      return $this->password;
    }

    public function setUser($user)
    {
      $this->user = $user;
    }

    public function getUser()
    {
      return $this->user;
    }




}
