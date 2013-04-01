<?php

namespace Framework\EF;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 16:53
 */ 
abstract class AbstractUpdater {
  private $options;
  private $updateDir;
  private $installedFile;

  public function install() {

    $this->setUpdateDir(UPDATE);
    $this->setInstalledFile(INSTALLED);

    if(file_exists(EFUPDATE_FILE) && file_exists(EFINSTALLED_FILE)) {

      $this->update();

      $version = file_get_contents(UPDATE_FILE);
      file_put_contents(EFINSTALLED_FILE, $version);

      unlink(EFUPDATE_FILE);
    }


  }

  public function update() {
    $updateVersion = floatval(file_get_contents($this->getInstalledFile()));
    if(is_dir($this->getUpdateDir())) {
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
        $options = $this->getOptions();
        $updateString = ($options['updateClassPrefix']).$updateString;
        if(class_exists($updateString)) {
          $updater = new $updateString();
          $updater->install();
        }
      }
    }
  }

  private function readUpdateFiles() {
    if ($dir = opendir($this->getUpdateDir())) {
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

  public function setOptions($options) {
    $this->options = $options;
  }

  public function getOptions() {
    return $this->options;
  }

  public function setInstalledFile($installedFile) {
    $this->installedFile = $installedFile;
  }

  public function getInstalledFile() {
    return $this->installedFile;
  }

  public function setUpdateDir($updateDir) {
    $this->updateDir = $updateDir;
  }

  public function getUpdateDir() {
    return $this->updateDir;
  }



}
