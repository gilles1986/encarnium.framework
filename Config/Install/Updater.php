<?php
namespace Config\Install;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 15:19
 */ 
use Framework\EF\AbstractUpdater;

class Updater extends AbstractUpdater {
  public function install() {

    $options = $this->getOptions();
    $this->setUpdateDir($options['installDir']."/Update");
    $this->setInstalledFile(DATA);

    if(file_exists($options['updateFile']) && file_exists($options['installedFile'])) {

      $this->update();

      $version = file_get_contents($options['updateFile']);
      file_put_contents($options['installedFile'], $version);

      unlink($options['updateFile']);
    }
  }



}
