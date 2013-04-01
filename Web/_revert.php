<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 01.04.13
 * Time: 16:52
 * TODO: Remove before commit
 */
if(!defined('ROOT')) {
  //Rootverzeichnes ermitteln
  $up = '../';
  define('ROOT', realpath(dirname(__FILE__).'/'.$up).'/');
}
if(!defined('CONFIG')) {
  define('CONFIG', ROOT.'Config/');
}

if(!defined('DATA')) {
  define('DATA', ROOT.'Data/');
}

if(!defined('INSTALL')) {
  define('INSTALL', CONFIG.'Install/');
}

if(!defined('UPDATE')) {
  define('UPDATE', INSTALL.'Update/');
}



if(!defined('INSTALLED_FILE')) {
  define('INSTALLED_FILE', DATA.'.install');
}

if(!defined('INSTALL_FILE')) {
  define('INSTALL_FILE', CONFIG.'.install');
}

if(!defined('UPDATE_FILE')) {
  define('UPDATE_FILE', CONFIG.'.update');
}


if(!defined('ADDITIONAL_INSTALL')) {
  define('ADDITIONAL_INSTALL', CONFIG.'install.php');
}

if(!defined('CLASSES')) {
  define('CLASSES', ROOT.'Framework/');
}


if(!defined('EFINSTALL')) {
  define('EFINSTALL', CLASSES.'/Install/');
}

if(!defined('EFUPDATE')) {
  define('EFUPDATE', EFINSTALL.'Update/');
}

if(!defined('EFINSTALLED_FILE')) {
  define('EFINSTALLED_FILE', EFINSTALL.'Data/.installed');
}

if(!defined('EFUPDATE_FILE')) {
  define('EFUPDATE_FILE', EFINSTALL.'Config/.update');
}
if(!defined('EFINSTALL_FILE')) {
  define('EFINSTALL_FILE', EFINSTALL.'Config/.install');
}



if(!defined('CRUDS')) {
  define('CRUDS', ROOT.'Packages/CRUD/');
}
if(!defined('CONTROLLER')) {
  define('CONTROLLER', ROOT.'Packages/Controller/');
}
if(!defined('PHPLIBS')) {
  define('PHPLIBS', ROOT.'Framework/');
}
if(!defined('SMARTY_PLUGINS')) {
  define('SMARTY_PLUGINS', ROOT."Framework/Smarty/plugins/");
}
if(!defined('BASE_PATH')) {
  define('BASE_PATH', ROOT.'Packages/');
}

if(!defined('SMARTY_PATH')) {
  define('SMARTY_PATH', ROOT.'Framework/Smarty/');
}

if(!defined('WEB_PATH')) {
  define('WEB_PATH', ROOT.'Web/');
}
if(!defined('VIEW_PATH')) {
  define('VIEW_PATH', BASE_PATH.'Views/');
}
if(!defined('ACTION_CONFIGS')) {
  define('ACTION_CONFIGS', ROOT.'Config/Actions/');
}


function rrmdir($path)
{
  return is_file($path)?
    @unlink($path):
    array_map('rrmdir',glob($path.'/*'))==@rmdir($path)
    ;
}



rrmdir(DATA);
rrmdir(EFINSTALL."Data");
rrmdir(EFINSTALL."Update");
if(is_dir(EFUPDATE)) {
  rrmdir(EFUPDATE);
}
copy(EFINSTALL."Data/.installed", EFINSTALL."Config/.install");
rrmdir(EFINSTALL."Data/.installed");