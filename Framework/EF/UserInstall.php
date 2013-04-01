<?php

namespace Framework\EF;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 12:58
 */ 
class UserInstall implements \Framework\EF\InstallInterface {

    private $installedVersion;
    private $updateVersion;
    private $options;

   public function __construct($options) {
     $this->setOptions($options);
   }

    public function needsInstall() {
       $options = $this->getOptions();

        if(file_exists($options['installedFile'])) {
          $this->setInstalledVersion(floatval(file_get_contents($options['installedFile'])));
          if(file_exists($options['updateFile'])) {
            $this->setUpdateVersion(floatval(file_get_contents($options['updateFile'])));
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
    $options = $this->getOptions();
      $installer = new $options['installerClass'];
      $installer->setOptions($this->getOptions());
      $installer->install();
  }

  public function update()
  {
    $options = $this->getOptions();
    $installer = new $options['updaterClass'];
      $installer->setOptions($this->getOptions());
      $installer->install();

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
