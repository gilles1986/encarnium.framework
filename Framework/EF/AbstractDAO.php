<?php
/**
* AbstractController
*
* Developed under PHP Version 5.3.1
*
* LICENSE: GPL License 3
*
* @package      Encarnium Framework
* @category     Cache
* @copyright    Encarnium Group since 2010
* @link         http://encarnium.de/
*
* @since        2008-11-21
* @author       Felix H. <felix@encarnium.de>
*
*
*
* @revision     $Revision: 353 $
* @modifiedby   $Author: g.meyer $
*
*/


namespace Framework\EF;

/**
 * Abstract DAO
 * 
 * Bindet die Datenbank ein und Grundfunktionalitäten
 * 
 * @author Nys Standop
 *
 */
abstract class AbstractDAO {
  
  /**
   * Database-Object
   * @var \Framework\MysqlDatabase
   * @access Protected
   */
  protected $db;
   
  /**
   * Reflection Class
   * @var \ReflectionClass
   * @access Protected
   */
  protected $reflectionClass;
  
  /**
   * Class Name
   * @var String
   * @access Protected
   */
  protected $className;
    
  /**
   * Tabellendefinition
   * @var Array $tableDef
   * @access Protected
   */
  protected $tableDef = array();
  
  /**
   * Constructor
   * @author Nys Standop
   * @access Public
   * @return void
   */
  public function __construct() {

    //Ermittelt die Datenbank-Config
    $dbConf = $this->getDatabaseConfig();
    
    //Stellt die Datenbankverbindung her
    $this->db = new \Framework\MysqlDatabase($dbConf);
    $this->db->connect();
    
    //initialisiert die ReflectionClass
    $this->reflectionClass = new \ReflectionClass($this);
    $this->className = strtolower($this->reflectionClass->getShortName());

    //Setzt die Tabledef
    $this->setTableDef();
  }
  
  /**
   * Gibt die Datenbank Config des ini-files aus
   * @author Nys Standop
   * @access Private
   * @return Array Database Config
   */
  private function getDatabaseConfig() {
    return parse_ini_file(CONFIG . 'db.ini');   
  }
  
  /**
   * Setzt die TableDef
   * @author Nys Standop
   * @access Protected
   * @return void
   */
  protected function setTableDef() {
    $this->tableDef = $this->db->describe($this->className);
  }
  
  
  /**
   * Überprüft ob TableDef exestiert
   * @author Nys Standop
   * @access Protected
   * @return Boolean 
   */
  protected function existsTableDef() {
    if(count($this->tableDef) > 0) { return true; }
    return false;
  }
  
  /**
   * Gibt das PrimärFeld zurrück
   * @author Nys Standop
   * @access Protected
   * @return boolean|string $field
   */
  protected function getPrimaryField() {
    //Wenn TableDef existiert
    if($this->existsTableDef() === true) {
      //Elemente Loopen
      foreach ($this->tableDef as $col) {
        //Auf primärKey Prüfen
        if($col['Key'] == 'PRI') {
          return $col['Field'];
        }
      }
      return false;     
    }    
    return false;
  }
  
  /**
   * Gibt einen Datensatz anhand des UID zurrück
   * @param Integer $id
   * @todo optimieren
   * @author Nys Standop
   * @access Protected
   * @return Array
   */
  protected function getByUID($id) {
    if (is_int($id) && existsTableDef == true) {
      
      $select = array(
        'SELECT' => '*',
      	'FROM' => $this->className,
        'WHERE' => $this->getPrimaryField() ." = ". $id
      );
            
      $rs = $this->db->selectByArray($select);
      return ($rs != false) ? $rs : array();
    }  
    return array();  
  }
  
  /**
   * Gibt alle Datensätze der Tabelle zurrück
   * @author Nys Standop
   * @access Protected
   * @return Array
   */
  protected function getAll() {
    $select = array(
        'SELECT' => '*',
      	'FROM' => $this->className
      );
            
      $rs = $this->db->selectByArray($select);
      return ($rs != false) ? $rs : array();
  }
  
  
}