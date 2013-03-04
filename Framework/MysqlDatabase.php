<?php
/**
 * MysqlDatabase
 *
 * Developed under PHP Version 5.3.1
 *
 * LICENSE: GPL License 3
 *
 * @package      Database
 * @subpackage   Mysql
 * @category     Includes_Classes
 * @copyright    Encarnium Group since 2010
 * @link         http://encarnium.de/
 * @see          MysqlDatabase.php
 *
 * @since        2008-11-21
 * @author
 *
 */

declare(ENCODING = 'utf-8');
namespace Framework;

/**
 * MysqlDatabase
 * <p>
 * Stellt eine Verbindung mit der Datenbank her, kommunziert mit dieser und
 * verwaltet sie
 *
 * @internal     -
 *
 */
class MysqlDatabase {


  /**
   * The Host
   * @var String
   */
  private $host;
  
  /**
   * The Password
   * @var String
   */
  private $password;
  
  /**
   * The User
   * @var String
   */
  private $user;
  
  /**
   * The Database
   * @var String
   */
  private $db;
  
  
  /**
   * Beinhaltet den momentanen Status der Datenbank.
   * @var String
   */
  private $active;
  
  /**
   * Besagt, ob Logger ein- oder ausgeschaltet ist.
   * @var boolean $debug
   * @access public
   */
  public  $debug;
  
  /**
   * @var resource|false $dbObj  Eine MySQL Verbindungs-Kennung im Erfolgsfall
   *                             oder FALSE im Fehlerfall.
   */
  private $dbObj;

  /**
   * Id des letzten Inserts
   * @var Int
   */
  public $insertId = null;


  /**
   * Konstruktor der Klasse
   *
   * @param array $dbData  Das Array beinhaltet die Anmeldedaten für die Datenbank.
   * @access public
   * @return void
   */
  public function __construct(Array $dbData = array()) {
    if(!$dbData['host'] && !$dbData['user'] && !$dbData['db']) {
      \Framework\Logger::warning("DB Daten sind nicht vorhanden", "db");
      return;
    }
    
    $this->host     = $dbData['host'];
    $this->password = $dbData['password'];
    $this->user     = $dbData['user'];
    $this->db       = $dbData['db'];
    $this->debug    = (boolean)$dbData['debug'];
    $this->active   = true;
  }


  /**
   * Verbindet mit der Datenbank, wenn möglich.
   *
   * @access public
   */
  public function connect() {
    if($this->active) {
      if($this->debug) {
        \Framework\Logger::debug("connect :: connect to DataBase", "db");
      }

      // Stellt die Verbindung mit der Datenbank her
      $this->dbObj = mysql_connect($this->host, $this->user, $this->password);

      // Wenn die Verbindung fehlgeschlagen ist
      if(!$this->dbObj) {
        echo "Verbindung nicht moeglich";

        if($this->debug) {
          \Framework\Logger::warning("Verbindung zum Datenbank-Server konnte nicht hergestellt werden", "db");
        }
      }
      else {
        // Wählt die Datenbank aus, wenn das nicht klappt...
        if(!mysql_select_db($this->db)) {
          // ... dann gebe einen Fehler aus...
          echo "Verbindung nicht moeglich";

          // ... und logge das ggf.
          if($this->debug) {
            \Framework\Logger::warning("Verbindung zur Datenbank '{$this->db}' nicht moeglich", "db");
          }
        }
        else {
          \Framework\Logger::debug("Verbindung zur Datenbank '{$this->db}' aufgebaut", "db");
          // Verbindung ist nun aktiv
          $this->active = "connect";
        }
      }
    }
  }


  /**
   * Sendet eine Anfrage an MySQL.
   *
   * @param string $query  Ein SQL-String.
   * @access public
   * @return resource|false
   */
  public function query($query) {
    if($this->active === "connect") {
      return mysql_query($query);
    }
    else {
      \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden","db");
      return false;
    }
  }


  /**
   * Holt Daten aus der Datenbank und gibt diese als assoziatives Array zur�ck.
   *
   * @param string $statement  Ein SQL-String.
   * @access public
   * @return array|boolean [associative|false]
   */
  public function select($statement) {
    if($this->active === "connect") {
      // Wenn debug Modus an ist
      if($this->debug) \Framework\Logger::debug("getData() :: Statement ist: {$statement}", "db");

      // Statement abschicken
      $query = mysql_query($statement); // or die(mysql_error());
      // die() wird in methoden nicht benutzt
          // Absenden
          if(!$query){
              if($this->debug) \Framework\Logger::debug("setData() :: Query failed: ".mysql_error(), "db");
              return array();
          }

      // Ergebnis auslesen und in ein Array eintragen
                    
      // $arrayAusgabe als Array deklarieren
      $arrayAusgabe = array();

      // MYSQL_BOTH liefert numerische und assoziative Ergebnisse
      // mysql_fetch_array optionale Parameter: MYSQL_BOTH (default), MYSQL_NUM, MYSQL_ASSOC
      while($row = @mysql_fetch_array($query, MYSQL_BOTH)) {
        // füllt die jeweiligen Arrays automatisch in numerische Arrays
        $arrayAusgabe[] = $row;
      }
      return $arrayAusgabe;
    }
    else {
      \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden", "db");
      return false;
    }
  }

  
  /**
   * 
   * @param Array [associative|false]
   * @access public
   * @return Array|Boolean [associative|false]
   */
  public function selectByArray(Array $statement) {
    $selectState = '';
    
    $selectState .= (isset($statement['SELECT']))  ? 'SELECT '  . $statement['SELECT'] : 'SELECT *';
    $selectState .= (isset($statement['FROM']))    ? ' FROM '    . $statement['FROM'] : '';
    $selectState .= (isset($statement['WHERE']))   ? ' WHERE '   . $statement['WHERE'] : ' WHERE (1=1)';
    $selectState .= (isset($statement['ORDERBY'])) ? ' ORDERBY ' . $statement['ORDERBY'] : '';
    $selectState .= (isset($statement['LIMIT'])) ? ' LIMIT ' . $statement['LIMIT'] : '';
    
    return $this->select($selectState);
  }

  /**
   * Verändert Felder in einer Tabelle.
   *
   * @param string $table                 Der spezifizierte Tabellenname.
   * @param array $fields                 Die Felder der Tabelle.
   * @param array $values                 Die Werte fürr die Spaltenwerte.
   * @param string $condition [optional]  Mögliche Bedingung.
   * @return boolean
   */
  public function update($table, array $fields, array $values, $condition = '') {
    if($this->active === "connect") {
      // Schauen ob fields und values auch gleich viele Eintr�ge haben
      $anzahlFeld    = count($fields);
      $anzahlWert    = count($values);

      if($anzahlFeld != $anzahlWert) {
        if($anzahlFeld > $anzahlWert) {
          $anzahl = $anzahlWert;
        }
        else {
          $anzahl = $anzahlFeld;
        }
      }
      else {
        $anzahl = $anzahlFeld;
      }

      // Post-Dekrementierung von $anzahl
      $anzahl--;

      // SQL Statement zusammen bauen
      $statement = "UPDATE {$table} SET ";

      for($i=0;$i<$anzahl;$i++) {
        $statement .= "`".$fields[$i]."` = '{$values[$i]}' ,";
      }

      $statement .= "`".$fields[$anzahl]."` = '{$values[$anzahl]}' WHERE ".$condition;

      // Wenn debug Modus an ist
      if($this->debug) \Framework\Logger::debug("changeData :: Statement ist: ".$statement, "db");

      // Absenden
      if(mysql_query($statement)){
          return true;
      }else {
          if($this->debug) \Framework\Logger::debug("setData() :: Query failed: ".mysql_error(), "db");
          return false;
      }
    }
    else {
      \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden","db");
      return false;
    }
  }


  /**
   * Fügt Werte in eine Tabelle ein
   *
   * @param string $table  Der spezifizierte Tabellenname.
   * @param array $fields  Die Felder der Tabelle.
   * @param array $values  Die Werte f�r die Spaltenwerte.
   * @access public
   * @return boolean
   */
  public function insert($table, array $fields, array $values) {
    if($this->active === "connect") {
      // Schauen ob fields und array_wert auch gleich viele Eintr�ge haben
      $anzahlFeld    = count($fields);
      $anzahlWert    = count($values);

      if($anzahlFeld != $anzahlWert) {
        if($anzahlFeld > $anzahlWert) {
          $anzahl = $anzahlWert;
        }
        else {
          $anzahl = $anzahlFeld;
        }
      }
      else {
        $anzahl = $anzahlFeld;
      }

      // Post-Dekrementierung von $anzahl
      $anzahl--;

      // SQL Statement zusammen bauen
      $statement = "INSERT INTO `{$table}` (";

      for($i=0;$i<$anzahl;$i++) {
        $statement .= "`{$fields[$i]}`, ";
      }

      $statement .= "`{$fields[$anzahl]}`) VALUES ('";

      for($i=0;$i<$anzahl;$i++) {
        $statement .= "{$values[$i]}', '";
      }

      $statement .= $values[$anzahl]."')";

      // Wenn debug Modus an ist
      if($this->debug) \Framework\Logger::debug("setData() :: Statement ist: ".$statement, "db");

      // Absenden
      if(mysql_query($statement)){
          $this->insertId = mysql_insert_id();
          return true;
      }else {
          if($this->debug) \Framework\Logger::debug("setData() :: Query failed: ".mysql_error(), "db");
          return false;
      }
    }
    else {
      \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden","db");
      return false;
    }
  }


  /**
   * Fügt mehrere Werte auf einmal in eine Tabelle ein
   *
   * @param string $table  Der spezifizierte Tabellenname.
   * @param array $fields  Die Felder der Tabelle.
   * @param array $values  Empf�ngt die Werte als Multidimensionales Array
   *                       (zweifache Verschachtelung).
   * @access public
   * @return boolean
   */
  public function insertMore($table, array $fields, array $values) {
    if($this->active === "connect") {
      // Schauen ob fields und array_wert auch gleich viele Eintr�ge haben

      // SQL Statement zusammen bauen
      $statement = "INSERT INTO {$table} (";

      for($j=0; $j < count($values); $j++) {
        $anzahlFeld = count($fields);
        $anzahlWert = count($values[$j]);

        if($anzahlFeld != $anzahlWert) {
          if($anzahlFeld > $anzahlWert) {
            $anzahl = $anzahlWert;
          }
          else {
            $anzahl = $anzahlFeld;
          }
        }
        else {
          $anzahl = $anzahlFeld;
        }

        // Post-Dekrementierung von $anzahl
        $anzahl--;

        // Nur beim ersten mal die Values setzen
        if($j == 0) {
          for($i=0;$i<$anzahl;$i++) {
            $statement .= "`{$fields[$i]}`, ";
          }
          
          $statement .= "`{$fields[$anzahl]}`) VALUES ";
        }

        $statement .= "('";

        for($i=0;$i<$anzahl;$i++) {
          $statement .= "{$values[$j][$i]}', '";
        }

        $statement .= $values[$j][$anzahl]."')";

        if($j != (count($values)-1)) {
          $statement .= " , ";
        }
      }

      // Wenn debug Modus an ist
      if($this->debug) \Framework\Logger::debug("setMoreData() :: Statement ist: ".$statement, "db");

      // Absenden
      if(mysql_query($statement)){
          $this->insertId = mysql_insert_id();
          return true;
      }else {
          if($this->debug) \Framework\Logger::debug("setData() :: Query failed: ".mysql_error(), "db");
          return false;
      }

    }
    else {
      \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden","db");
      return false;
    }
  }


  /**
   * Löscht Datensätze aus der Datenbank
   *
   * @param string $table      Der spezifizierte Tabellenname.
   * @param string $condition  Die Bedingung im SQL-String.
   * @access public
   * @return boolean
   */
  public function delete($table, $condition) {
   	if($this->active === "connect") {
   	  $statement = "DELETE FROM {$table} WHERE ".$condition;

          //Wenn debug Modus an ist
          if($this->debug) \Framework\Logger::debug("deleteData :: Statement ist: ".$statement, "db");

          // Absenden
          if(mysql_query($statement)){
              return true;
          }else {
              if($this->debug) \Framework\Logger::debug("setData() :: Query failed: ".mysql_error(), "db");
              return false;
          }
   	}
   	else {
   	  \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden", "db");
   	  return false;
   	}
  }


  /**
   * Löscht mehrere Datensätze aus der Datenbank
   *
   * @param object $table          Der spezifizierte Tabellenname.
   * @param array $conditionArray  Die Bedingungen im SQL-String.
   * @return boolean
   */
  public function deleteMore($table, array $conditionArray) {
    if($this->active === "connect") {
   	  $statement = "DELETE FROM {$table} WHERE ".$conditionArray[0];

          // Zus�tzliche S�tze l�schen
          for($i=1;$i<count($conditionArray);$i++) {
            $statement .= " OR ".$conditionArray[$i];
          }

          //Wenn debug Modus an ist
          if($this->debug) \Framework\Logger::debug("deleteData :: Statement ist: ".$statement, "db");

          // Absenden
          if(mysql_query($statement)){
              return true;
          }else {
              if($this->debug) \Framework\Logger::debug("setData() :: Query failed: ".mysql_error(), "db");
              return false;
          }
   	}
   	else {
   	  \Framework\Logger::warning("Es wurde nicht mit der Datenbank verbunden","db");
   	  return false;
   	}
  }


  /**
   * 
   * Enter description here ...
   * @param unknown_type $table
   * @author Nys Standop
   */
  public function describe($table) {
    return $this->select('DESCRIBE `' . $table . '`');
  }
  
  
  /**
   * Schließt die Datenbank Verbindung
   *
   * @access public
   * @return boolean
   */
  public function close() {
    if($this->active === "connect") {
      \Framework\Logger::debug("Verbindung zur Datenbank '{$this->db}' unterbrochen \r\n", "db");
      return @mysql_close($this->dbObj);
    }
    return false;
  }
  
  /**
   * <u>Desctructor</u>
   * Schließt die Datenbankverbindung
   * @return void
   */
  public function __destruct() {
  	$this->close();
  }


}
?>
