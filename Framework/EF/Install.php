<?php

namespace Framework\EF;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 12:58
 *
 */ 
class Install implements \Framework\EF\InstallInterface {

    private $installedVersion;
    private $updateVersion;
    private $options;

   public function __construct($options) {
     $this->setOptions($options);
   }

    public function needsInstall() {
      if(is_dir(DATA)) {
        if(file_exists(INSTALLED_FILE)) {
          $this->setInstalledVersion(floatval(file_get_contents(INSTALLED_FILE)));
          if(file_exists(UPDATE_FILE)) {
            $this->setUpdateVersion(floatval(file_get_contents(UPDATE_FILE)));
          } else {
            return false;
          }
          if($this->needsUpdate()) {
            return true;
          } else {
            return false;
          }
        } else {
           return true;
        }
      } else {
          return true;
      }
    }

    public function needsUpdate() {
      if($this->getUpdateVersion() > $this->getInstalledVersion()) {
        return true;
      }
      return false;
    }

  public function installUpdate() {
    if($this->needsUpdate()) {
      $this->update();
    } else {
      $this->install();
    }
  }

  public function install()
  {
    $installFile = $this->getOptions();
    if(in_array("Framework\\EF\\InstallerInterface", class_implements($installFile['installerClass']))) {
      $installer = new $installFile['installerClass']();
      $installer->install();
    } else {
      throw new Exception("Installer Class ".$installFile['installerClass']." must implement Framework\\EF\\InstallerInterface");
    }
  }

  public function update()
  {
    // TODO: Implement update() method.
  }


  public function setInstalledVersion($installedVersion)
    {
        $this->installedVersion = $installedVersion;
    }

    public function getInstalledVersion()
    {
        return $this->installedVersion;
    }

    public function setUpdateVersion($updateVersion)
    {
        $this->updateVersion = $updateVersion;
    }

    public function getUpdateVersion()
    {
        return $this->updateVersion;
    }

  public function setOptions($options)
  {
    $this->options = $options;
  }

  public function getOptions()
  {
    return $this->options;
  }



}
