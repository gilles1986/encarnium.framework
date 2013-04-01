<?php


namespace Framework;

class Logger {

  protected static $config;
  protected static $stack;
  
  private static $mail;


  private function __construct() {}
  private function __clone() {}

  /**
   * Magic-Methode CallStatic standart Fals zurückgeben
   * @param String $method
   * @param String|Array $args
   * @return Boolean false
   */
  public static function __callstatic($method, $args) {return false;}

  /**
   * Initialisiert den Logger
   *
   * @static
   * @param String $logName [optional]
   * @param Boolean $sendMail [optional]
   */
  public static function init($configFile = null, $sendMail = false) {
    self::$config = \Framework\Logic\Utils\jsonHandler::parseJson(is_readable($configFile) ? $configFile : CONFIG.'/configlogger.json');
    self::$mail = false;
    
    if($sendMail===true) {
      self::$mail = true;
    }
    
    // überprüfen, ob die Konfiguration geladen werden konnte
    if(self::$config === false) {
      echo '<strong>Logger Konfiguration konnte nicht geladen werden</strong>';
      exit(-1);
    }
  }
  
  /**
   * Schreibt eine Debug Nachricht
   *
   * @static
   * @param String $message
   * @param String $logname [optional]
   */
  public static function debug($message, $logname = null) {
    self::writeLog($message, $logname, 'DEBUG');
  }

  /**
   * Schreibt eine Warnung Nachricht
   *
   * @static
   * @param String $message
   * @param String $logname [optional]
   */
  public static function warning($message, $logname = null) {
    self::writeLog($message, $logname, 'WARNING');
  }
	
	/**
   * Schreibt eine Info Nachricht
   *
   * @static
   * @param String $message
   * @param String $logname [optional]
   */
  public static function info($message, $logname = null) {
    self::writeLog($message, $logname, 'INFO');
  }
  
  /**
   * Schreibt eine Fehler Nachricht
   *
   * @static
   * @param String $message
   * @param String $logname [optional]
   */
  public static function error($message, $logname = null) {
    self::writeLog($message, $logname, 'ERROR');
  }

  /**
   * Schreibt Nachrichten in eine Log Datei, welche ab einer bestimmten Größe
   * archiviert wird
   *
   * @static
   * @param String|Exception $message
   * @param String $logname [optional]
   * @param String $logLevel
   * @return true on success|false on failure
   */
  protected static function writeLog($message, $logname = null, $logLevel) {
    // überprüfe, ob der Logger ausgeschaltet ist
    if(strtolower(self::$config->debugging) !== 'true') {
      return false;
    }
    
    // LOG STRING MESSAGES
    if(is_string($message)) {
      // überprüfe, ob das log level zulässig ist und geloggt werden kann
      if(self::isValid(strtoupper(self::$config->loglevel), strtoupper($logLevel)) === false) {
        return false;
      }
      
      // lognamen ermitteln
      $logname = is_null($logname)||empty($logname) ? self::$config['stlogname'] : $logname;
      
      // schreibe die log nachricht in die log datei
      $date = date("dmY");
      $msg = date('(d/m/Y)(H:i:s) ').basename($_SERVER['PHP_SELF'])." [{$logLevel}]: {$message}\r\n";
      
      $logname = str_replace('\\', '_', $logname);
      $res = file_put_contents(ROOT.self::$config->logpath."/".$logname.'_'.$date.'.log', $msg, FILE_APPEND);
      
      // überprüfe, ob ein log file archiviert werden muss
      self::checkRotating();
      
      return $res !== false ? true : false;
    }
    // LOG EXCEPTION MESSAGES
    elseif($message instanceof Exception) {
      // überprüfe, ob das log level zulässig ist und geloggt werden kann
      if(self::isValid(strtoupper(self::$config->loglevel), strtoupper($logLevel)) === false) {
        return false;
      }
      
      // lognamen ermitteln
      $logname = is_null($logname)||empty($logname) ? self::$config->stlogname : $logname;
      
      // schreibe die log nachricht in die log datei
      $date = date("dmY");
      $msg  = date('(d/m/Y)(H:i:s) ').basename($_SERVER['PHP_SELF'])." [{$logLevel}]: ".$message->getMessage().
        " (Fehlercode: ".$message->getCode().") \r\n Stacktrace: \r\n ".$message->getTraceAsString()." \r\n";
      $res = file_put_contents(ROOT.self::$config->logpath."/".$logname.'_'.$date.'.log', $msg, FILE_APPEND);
      
      // überprüfe, ob ein log file archiviert werden muss
      self::checkRotating();
      
      return $res !== false ? true : false;
    }
    else {return false;}
  }

  /**
   * Validiert das gesetzte Konfigurations Log Level mit dem einkommendem Log Level
   *
   * @static
   * @param String $debugLevel
   * @param String $inputLogLevel
   * @return Boolean
   */
  protected static function isValid($logLevel, $inputLogLevel) {
    switch($logLevel) {
      // ERROR
      case 'ERROR':
        return $inputLogLevel==='ERROR' ? true : false;
        break;

      // WARNING
      case 'WARNING':
        return $inputLogLevel==='ERROR'||$inputLogLevel==='WARNING' ? true : false;
        break;

      // DEBUG
      case 'DEBUG':
        return $inputLogLevel==='ERROR'||$inputLogLevel==='WARNING'||$inputLogLevel==='DEBUG' ? true : false;
        break;

      // INFO
      case 'INFO':
        return $inputLogLevel==='ERROR' || $inputLogLevel==='WARNING'
          || $inputLogLevel==='INFO' || $inputLogLevel==='DEBUG' ? true : false;
        break;
        
      default:
        return false;
        break;
    }
  }

  /**
   * Prüft ob Log Dateien vorhanden sind, die nicht vom heutigen Tag sind, zusätzlich
   * wenn der Log- oder Archivordner nicht existiert wird dieser erstellt und die
   * alten Log Dateien werden dann ins Archiv verschoben
   *
   * @static
   * @return true on rotate|false on non-rotation
   */
  protected static function checkRotating() {
    // erstelle log ordner wenn nicht vorhanden
    if(is_dir(ROOT.self::$config->logpath) === false) {
      // volle Rechte auf den _log_ ordner; bei windows der oktalwert: 0777
      mkdir(ROOT.self::$config->logpath, 0777);
    }

    // erstelle archiv ordner wenn nicht vorhanden
    if(!is_dir(ROOT.self::$config->logpath."/archive/")) {
      // volle Rechte auf den _archive_ ordner; bei windows der oktalwert: 0777
      echo ROOT.self::$config->logpath."/"."archive";
      mkdir(ROOT.self::$config->logpath."/"."archive", 0777);
    }
    
    // bereite überprüfung für archivierung vor:
    $logFiles = glob(ROOT.self::$config->logpath."/".'*.log');

    foreach($logFiles as $file) {
      // holt sich die einzelnen Teile des Dateinamens
      $split = explode("_", basename($file));
			$split = array_reverse($split);
      // $split[1] enthält das eindeutige und zuverlässig Erstelldatum der Datei (falls Datei geändert wurde, etc.)
      if($split[0] !== date('dmY').'.log'||(filesize($file)/1024)>=self::$config->maxlogsize) {
        return self::logRotate($file);
      }
    }

  }

  /**
   * Archiviert die Log Dateien und nummeriert diese mit einem automatischen Index,
   * falls der Name einer Log Datei doppelt vorkommen sollte.
   *
   * @static
   * @param String $file
   * @return true on success|false on failure
   */
  protected static function logRotate($file) {     
    $sameNamedFiles = glob(ROOT.self::$config->logpath.'/archive/'.'*'.basename($file));
    
    // Anzahl der Dateien mit dem gleichem Namen (im archive ordner)
    $counter = ((count($sameNamedFiles)) + 1);

    // mail funktion bei archivierung kann explizit an- und ausgeschaltet werden
    if(self::$mail===true) {
      /**
       * @link http://php.net/manual/de/function.mail.php
       * @link http://www.php.net/manual/de/function.filesize.php
       */
      mail(self::$config['mail'], basename($file).' wurde in das Archiv verschoben ('.(filesize($file)/1024).'kB)'
        , file_get_contents($file), 'From: logger@encarnium.de');
    }
    
    // Log ins Archiv schieben
    return rename(ROOT.self::$config->logpath."/".basename($file), ROOT.self::$config->logpath.'/archive/'.$counter.'_'.basename($file));
  }

}

?>
