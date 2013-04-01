<?php

namespace Framework\Install;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 14:21
 */ 
class Installer implements \Framework\EF\InstallerInterface {

  private $options;

  public function install() {
    $this->createDirs();
    $version = "1.0";

    if(file_exists(EFINSTALL_FILE)) {
      $version = file_get_contents(EFINSTALL_FILE);
      unlink(EFINSTALL_FILE);
    }

    file_put_contents(EFINSTALLED_FILE, $version);

  }

  private function createDirs() {
    mkdir(DATA);
    mkdir(DATA."/Smarty");
    mkdir(DATA."/Smarty/compiled");
    mkdir(DATA."/Smarty/cached");
    mkdir(DATA."/Log");
    mkdir(DATA."/Log/archive");
    mkdir(EFINSTALL."Data");
    mkdir(EFINSTALL."Update");
    if(!is_dir(EFUPDATE)) {
      mkdir(EFUPDATE);
    }
  }

  public function setOptions($options) {
    $this->options = $options;
  }

  public function getOptions() {
    return $this->options;
  }




}
