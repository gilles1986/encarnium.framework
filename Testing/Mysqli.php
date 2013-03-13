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

include_once "../Framework/Logic/Mysqli.php";




$mysqli = new \Framework\Mysqli(array(
  "host" => "localhost",
  "user" => "root",
  "password" => "",
  "db" => "test"
));



function insert($mysqli) {
  $mysqli->insert("user", array(
    "name" => "Johnny",
    "nachname" => "Walker"
  ));
}


function select($mysqli) {
 var_dump($mysqli->select("Select * from user"));
}


function update($mysqli) {
  var_dump($mysqli->update("user", array(
    "nachname" => "Mulder"
  ), "id='2'" ));
}


function delete($mysqli) {
  var_dump($mysqli->delete("user", "id=3"));
}


update($mysqli);


?>
