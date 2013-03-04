<?php 
declare(ENCODING = 'utf-8');
namespace Framework;

/**
 * class LDAP
 *
 * @internal     
 *
 * @subpackage	Models
 */

class LDAP
{
  private $hostname;
  private $basedn;
  private $user; 
  private $password; 
  private $connectionResource; 
  private $bindId; 
  private $searchResult; 
  private $resultEntry; 
  private $error; 
  private $result = array ();
 
  /**
   * Konstruktor der Klasse
   * 
   * @return 
   * @param object $config
   */
  public function __construct($config) {
    \Framework\Logger::debug("LDAP::construct","LDAP");
    $this->user = $config['user'];
    $this->password = $config['password'];
    $this->hostname = $config['host'];
  }
 
  
  
  /**
   * Stellt eine Verbindung zum Server her
   * 
   * @return 
   */
  public function connect() {
    \Framework\Logger::debug("verbinden zu ".$this->hostname,"LDAP");
    if (!$this->connectionResource) {
      if (!$this->connectionResource= ldap_connect($this->hostname)) {
      	\Framework\Logger::warning("Es konnte keine Verbindung mit dem LDAP-Server hergestellt werden");
        return false;
      }
    } else {
      return true;
    }
  }
 
 
 
  /**
   * Meldet sich mit Benutzer Daten am Server an
   * 
   * @return 
   */
  public function bind() {
    \Framework\Logger::debug("Per LDAP anmelden","LDAP");
    $this->bindId = @ldap_bind($this->connectionResource, $this->user, $this->password);
    if ($this->bindId == false) {
      
			return false;
    } else {
      return true;
    }
  }
 
 
 
  /**
   * Meldet sich anonym am Server an
   * 
   * @return 
   */
  public function anonymBind() {
    if ($this->bindId = ldap_bind($this->connectionResource)) {
      return ($this->bindId);
    } else {
      return FALSE;
    }
  }
 
 
 
  /**
   * Meldet sich vom Server ab
   * 
   * @return 
   */
  public function unbind() {
    \Framework\Logger::debug("Per LDAP abmelden","LDAP");
    if (! empty($this->bindId)) {
      ldap_unbind($this->connectionResource);
      return true;
    } else {
      return false;
    }
  }
  
  
 
  /**
   * Unterbricht die Verbindung zum LDAP Server
   * 
   * @return 
   */
  public function disconnect() {
    \Framework\Logger::debug("Disconnect vom Server","LDAP");
    if (ldap_close($this->connectionResource)) {
      return true;
    } else {
      return false;
    }
  }
  
  public function close() {
    \Framework\Logger::debug("Close/Disconnect vom Server","LDAP");
    if (ldap_close($this->connectionResource)) {
      return true;
    } else {
      return false;
    }
  }
 
 
 
  /**
   * Sucht im Active Directory
   * 
   * @return 
   * @param object $basedn
   * @param object $filter
   */
  public function search($basedn, $filter) {
    \Framework\Logger::debug("Suchen: $basedn","LDAP");
    $result = array ();
    if (!$this->connect()) {
      return (0);
    }
   
    $this->sr = ldap_search($this->connectionResource, $basedn, $filter);
    return ($this->sr);
  }
 
 
 
  /**
   * Löscht die im Cache gespeicherten Suchergebnisse
   * 
   * @return 
   * @param object $rs
   */
  public function freeResult($rs) {
    \Framework\Logger::debug("Suchergebnisse l�schen","LDAP");
    $result = ldap_free_result($rs);
    return ($result);
  }
 
 
 
  /**
   * Liest die gesuchten Begriffe aus
   * 
   * @return 
   * @param object $filter
   */
  public function getEntries($filter) {
    \Framework\Logger::debug("Ergebnisse auslesen","LDAP");
    $this->result = ldap_get_entries($this->connectionResource, $filter);
    if ($this->result["count"] == 0)
      return false;
    else
      return ($this->result);
  }
 
 
 
  /**
   * Zählt die Einträge, die gefunden wurden
   * 
   * @return 
   * @param object $sr
   */
  public function countEntries($sr) {
    \Framework\Logger::debug("Anzahl der Eintr�ge auslesen","LDAP");
    $result = ldap_count_entries($this->connectionResource, $sr);
    $this->error();
    return ($result);
  }
}
  
?>