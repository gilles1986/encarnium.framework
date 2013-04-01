<?php
/**
* Die Cache-Klasse kann ihr übergebene Daten speichern, laden und aus dem Cache löschen.
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
*/


namespace Framework;

class Cache {

    /**
    * Definiert den Pfad für das Speichern der Cachedateien (vom Rootverzeichnis aus)
    * @var String
    */
    static $savePath = 'Data/Cache/';

    /**
     * Speichert $data unter den Namen $name ab. Gibt bei Erfolg true, bei
     * Misserfolg false zurück und loggt den Fehler.
     *
     * @param String $name
     * @param Mixed $data
     * @return Boolean
     */
    public static function save($name, $data){
        $savePath = self::getSavePath($name);

        file_put_contents($savePath, serialize($data));
        if (!file_exists($savePath)){
            \Framework\Logger::warning('Cache konnte nicht gespeichert werden in ' . $savePath);
            return false;
        }
        return true;
    }

    /**
     * Versucht die Daten mit den Speichernamen $name zu laden. Gibt false bei
     * Misserfolg zurück, die Daten bei Erfolg.
     *
     * @param String $name
     * @return Mixed
     */
    public static function load($name){
        $fileContent = @file_get_contents(self::getSavePath($name));
        if ($fileContent === false){
            return false;
        }
        $data = @unserialize($fileContent);
        
        return $data;
    }

    /**
     * Löscht die Daten, die unter den Namen $name gespeichert wurden. Bei Erfolg
     * gibt die Funktion true zurück, ansonsten false. Existiert die Datei, kann
     * aber dennoch nicht gelöscht werden, wird der Fehler geloggt.
     *
     * @param String $name
     * @return Boolean
     */
    public static function delete($name){
        $savePath = self::getSavePath($name);

        if (file_exists($savePath)){
            if (is_writable($savePath)){
                unlink($savePath);
                return true;
            }else{
                \Framework\Logger::warning('Cache konnte nicht gelöscht werden - ' . $savePath);
            }
        }

        return false;

    }

    /**
     * Überprüft ob Daten unter den Namen $name gespeichert wurden. Gibt
     * dementsprechend true oder false zurück
     *
     * @param String $name
     * @return Boolean
     */
    public static function exists($name){
        $savePath = self::getSavePath($name);

        if (!file_exists($savePath) || !is_readable($savePath)){
            return false;
        }
        return true;
    }

    /**
     * Löscht den gesamten Cache. Sobald eine oder mehrere Dateien nicht gelöscht
     * werden konnten, wird false zurück gegeben und die Fehler geloggt.
     *
     * @return Boolean
     */
    public static function clear(){
        $success = true;
        $files = glob(self::getSavePath() . '*');

        foreach ($files as $filename){
            if (is_writable($filename)){
                unlink($filename);
            }else{
                \Framework\Logger::warning('Cache konnte nicht gelöscht werden - ' . $savePath);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Ermittelt den Pfad, unter dem die Daten gespeichert werden. Sobald
     * $name angegeben wird, wird der Pfad inklusive des Dateinamens zurückgegeben.
     *
     * @param String $name
     * @return String
     */
    private static function getSavePath($name = null){
        return ROOT . '../' . self::$savePath . ($name === null ? '' : md5($name));
    }

}
?>
