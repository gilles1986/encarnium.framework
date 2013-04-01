<?php


namespace Models;

/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 20.01.13
 * Time: 14:03
 * To change this template use File | Settings | File Templates.
 */ 
class Login {

  private $username;
  private $password;

  private $rightUsername = "admin";
  private $rightPassword = "admin";

  public function __construct($request) {
    if(isset($request['username'])) {
      $this->username = $request['username'];
    }

    if(isset($request['password'])) {
      $this->password = $request['password'];
    }
  }

  public function checkLogin() {
    if(strtolower($this->username) === $this->rightUsername && $this->password === $this->rightPassword) {
      return true;
    } else {
      return false;
    }
  }

}

?>