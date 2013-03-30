<?php
namespace Framework\EF;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.03.13
 * Time: 14:09

 */
interface InstallerInterface {
   public function install();
   public function setOptions($options);
  public function getOptions();

}
