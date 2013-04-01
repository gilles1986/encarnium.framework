<?php

namespace Framework\EF;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 12:58
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
        if(file_exists(EFINSTALLED_FILE)) {
          $this->setInstalledVersion(floatval(file_get_contents(EFINSTALLED_FILE)));
          if(file_exists(UPDATE_FILE)) {
            $this->setUpdateVersion(floatval(file_get_contents(EFUPDATE_FILE)));
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
      $installer = new \Framework\Install\Installer();
      $installer->setOptions($this->getOptions());
      $installer->install();
  }

  public function update()
  {
      $installer = new \Framework\Install\Updater();
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
