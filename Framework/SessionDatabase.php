<?php
/*
 DB TABLE STRUCTURE

 CREATE TABLE IF NOT EXISTS `EF_SESSION_DB` (
 `session_id` varchar(32) NOT NULL,
 `value` blob,
 `update_timestamp` datetime DEFAULT NULL,
 PRIMARY KEY (`session_id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

 */

declare(ENCODING = 'utf-8');
namespace Framework;

/**
 * SessionDatabase Class
 * 
 * @author Domac
 * @link http://www.php.net/manual/de/function.session-set-save-handler.php
 */
class SessionDatabase {

  /**
   * Enthält mögliche Werte für die Session
   * 
   * @var Mixed
   */
  private $data = null;
  
  /**
   * Enhält einen 32 Zeichen langen String (die Session ID)
   * 
   * @var String
   */
  private $session_id = null;
  
  /**
   * Gültigkeitsdauer in der Datenbank
   * 
   * @var Integer
   */
  private $minutes2expire = 3600;


  /**
   * Konstruktor
   * 
   * @global $SESSION
   */
  public function __construct() {
    if(isset($GLOBALS['SESSION']) !== true) {
      $GLOBALS['SESSION'] = null;
    }
    
    if(isset($_COOKIE['session_id']) === true) {
      
      // alte session id vorhanden?
      $this->session_id = $_COOKIE['session_id'];
      
    } else {
      
      // neue eindeutige session id generieren
      $this->session_id = md5(rand(1,9999999).rand(1,9999999).microtime().rand(1,9999999).rand(1,9999999));
      // neue session id setzen
      setcookie('session_id', $this->session_id);

      $sql = "INSERT INTO `EF_SESSION_DB` (`session_id`, `update_timestamp`)
              VALUES ('".mysql_real_escape_string($this->session_id)."', NOW())";
      
      mysql_query($sql);
      
    }
     
    $sql = "SELECT `value` FROM `EF_SESSION_DB` WHERE `session_id` = '{$this->session_id}'";
    $query = mysql_query($sql);
    
    // holt einen eintrag mit der session id (es sollte nur einen geben ^^)
    $this->data = unserialize(mysql_result($query, 0, 'value'));
    
    $GLOBALS['SESSION'] = $this->data;
  }
  
  /**
   * Destruktor
   * 
   * @global $SESSION
   */
  public function __destruct() {
    $this->data = serialize($GLOBALS['SESSION']);
     
    $sql = "UPDATE `EF_SESSION_DB` SET `value` = '{$this->data}', `updated_timestamp` = NOW()
            WHERE `session_id` = '{$this->session_id}'";
    mysql_query($sql);
     
    $this->expire();
  }
  
  /**
   * Ungültige Session ID's aus der Datenbank entfernen
   * 
   * @access private
   */
  private function expire() {
    $date2delete = date("Y-m-d H:i:s", time()-(60*$this->minutes2expire));
    $sql = "DELETE FROM `EF_SESSION_DB` WHERE `update_timestamp` <= '$date2delete'";
    
    mysql_query($sql);
  }
  
}

?>