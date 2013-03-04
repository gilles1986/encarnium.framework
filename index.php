<?php
/* Redirect auf das Web-Verzeichnis wenn nicht Web als aktuelles Web-Root Verzeichnis gesetzt ist */
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header('Location: http://'.$host.$uri.'/Web/');
exit;
?>
