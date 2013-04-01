<?php

namespace Config\Install;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 14:21
 */ 
class Installer extends \Framework\EF\AbstractInstaller {
  private $options;

  public function install() {


    $version = "1.0";
    $options = $this->getOptions();
    if(file_exists($options['installFile'])) {
      $version = file_get_contents($options['installFile']);
      unlink($options['installFile']);
    }

    file_put_contents($options['installedFile'], $version);

  }

  public function setOptions($options) {
    $this->options = $options;
  }

  public function getOptions() {
    return $this->options;
  }
}
