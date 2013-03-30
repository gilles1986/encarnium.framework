<?php

namespace Config\Install;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 14:21
 */ 
class Installer implements \Framework\EF\InstallerInterface {

  public function install() {
    $this->createDirs();
    $version = "1.0";

    if(file_exists(INSTALL_FILE)) {
      $version = file_get_contents(INSTALL_FILE);
      unlink(INSTALL_FILE);
    }

    file_put_contents(INSTALLED_FILE, $version);

  }

  private function createDirs() {
    mkdir(DATA);
    mkdir(DATA."/Smarty");
    mkdir(DATA."/Smarty/compiled");
    mkdir(DATA."/Smarty/cached");
    mkdir(DATA."/Log");
    mkdir(DATA."/Log/archive");
    if(!is_dir(UPDATE)) {
      mkdir(UPDATE);
    }
  }

}
