<?php
namespace Config\Install;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 15:19
 */ 
class Updater implements \Framework\EF\InstallerInterface {
  public function install() {

    if(file_exists(UPDATE_FILE) && file_exists(INSTALLED_FILE)) {

      $this->update();

      $version = file_get_contents(UPDATE_FILE);
      file_put_contents(INSTALLED_FILE, $version);

      unlink(UPDATE_FILE);
    }


  }

  public function update() {
    $updateVersion = floatval(file_get_contents(INSTALLED_FILE));
    if(is_dir(UPDATE)) {
      $updates = $this->readUpdateFiles();
      $updates = $this->sortArray($updates);
      $this->updateLoop($updateVersion, $updates);
    }
  }

  private function getVersion($update) {
    $filePrefix = "Update_";
    $filePostfix = ".php";
    $fileVersionSplit = "_";
    $updateVersion = str_replace($filePrefix, "", $update);
    $updateVersion = str_replace($filePostfix, "", $updateVersion);

    $updateVersion = str_replace($fileVersionSplit, ".", $updateVersion);

    return $updateVersion;


  }

  private function sortArray($updates) {
    usort($updates, function($a, $b) {
      $a = floatval($this->getVersion($a));
      $b = floatval($this->getVersion($b));

      if($a > $b) {
        return 1;
      } else {
        return -1;
      }

    });
    return $updates;
  }

  public function updateLoop($version, $updates) {

    $filePostfix = ".php";

    foreach($updates as $update) {

      $updateVersion = $this->getVersion($update);

      if($updateVersion >= $version) {
        $updateString = str_replace($filePostfix, "", $update);
        $updateString = "\\Config\\Install\\Update\\".$updateString;
        if(class_exists($updateString)) {
          $updater = new $updateString();
          $updater->install();
        }
      }
    }
  }

  private function readUpdateFiles() {
    if ($dir = opendir(UPDATE)) {
      $updates = array();
      while (false !== ($file = readdir($dir))) {
        if ($file != "." && $file != "..") {
          $updates[] = $file;
        }
      }
      closedir($dir);
      asort($updates);
      return $updates;
    }
    return array();
  }



}
