<?php
session_start();

// Errors immer anzeigen (jedenfalls solange in der Entwicklungsphase):
//error_reporting(E_ALL | E_STRICT);

// Core laden
require_once realpath(dirname(__FILE__).'/../Framework/EF/Core.php');

// Konstanten definieren
\Framework\EF\Core::setConstants();

require_once CONFIG."/application_options.php";
require_once CLASSES."/EF/config.php";

//@todo wird noch nicht eigenständig geladen
//include_once SMARTY_PLUGINS."block.translate.php";




// Core laden starten
$application = new \Framework\EF\Core($app_config ,$options);
$options = $application->getOptions();

// costum  Setup
if(is_string($options['costumInstall'])) {
  if(file_exists($options['costumInstall'])) {
    include_once $options['costumInstall'];
  }
} else if(is_array($options['costumInstall'])) {
  foreach($options['costumInstall'] as $key => $value) {
    if(file_exists($value)) {
      include_once $value;
    }
  }
}


$install = new \Framework\EF\Install($options);
if($install->needsInstall()) {
   $install->installUpdate();
}


if(class_exists($options['installClass'])) {
  if(in_array("Framework\\EF\\InstallInterface", class_implements($options['installClass']))) {
    $userinstall = new $options['installClass']($options);
  } else {
    throw new Exception("InstallClass ".$options['installClass']." does not implement InstallInterface");
  }
} else {
  throw new Exception("InstallClass ".$options['installClass']." does not exist. Check your config file");
}
if($userinstall->needsInstall()) {
  $userinstall->installUpdate();
}



$application->run();


?>
