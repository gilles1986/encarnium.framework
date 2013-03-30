<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gilles
 * Date: 30.01.13
 * Time: 22:33
 *
 * Enthält alle Optionen für das Framework.
 * Bitte nicht anfassen. Diese Option wird mit der application_config zusammengefasst
 */

  $app_config = array(
    "reservedActions" => array(
      "404"
    ),
    "defaultAction" => "main",
    "actionName" => "action",
    "installClass" => "\\Framework\\EF\\Install",
    "costumInstall" => array(),
    "installerClass" => "\\Config\\Install\\Installer",
    "updaterClass" => "\\Config\\Install\\Updater",
    "updateClassPrefix" => "\\Config\\Install\\Update\\"
  );







?>