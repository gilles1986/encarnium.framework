<?php

namespace Controller;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 29.01.13
 * Time: 22:01
 * To change this template use File | Settings | File Templates.
 */ 
class FatalError extends TplAbstractController {

  public function error404() {
   var_dump($this->getErrorMessages());
  }

}
