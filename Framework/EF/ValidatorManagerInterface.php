<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 14.03.13
 * Time: 20:44
 * To change this template use File | Settings | File Templates.
 */

declare(ENCODING = 'utf-8');
namespace Framework\EF;

interface ValidatorManagerInterface {
  public function validate($action, $request);
  public function isValid();
  public function getErrorMessages();
  public function getRequest();

}
