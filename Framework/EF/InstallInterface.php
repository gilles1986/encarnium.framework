<?php

namespace Framework\EF;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 13:31
 */
interface InstallInterface {

  public function needsInstall();
  public function needsUpdate();
  public function install();
  public function update();
  public function installUpdate();


  public function __construct($options);
  public function setInstalledVersion($installedVersion);
  public function getInstalledVersion();
  public function setUpdateVersion($updateVersion);
  public function getUpdateVersion();
  public function setOptions($options);
  public function getOptions();




}
