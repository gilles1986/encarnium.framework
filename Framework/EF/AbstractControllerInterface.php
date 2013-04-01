<?php

namespace Framework\EF;
/**
 * User: Gilles
 * Date: 16.03.13
 * Time: 11:37
 */
interface AbstractControllerInterface {
  public function __construct(\Framework\EF\ValidatorManagerInterface $validator,$options, $action='', $realAction='', $params=false, $app_config=array());
  public function getRequest();
  public function getErrorMessages();
  public function getSession($value=false);
  public function setSession($key, $value);
  public function isValid();
  public function getSmarty();
  public function doAction($action);
  public function assign($variable, $value);


  public function main();
  public function _prepare($action);
  public function _finalise($action);

  public function getAction();
  public function getOptions();
  public function getParams();
}
