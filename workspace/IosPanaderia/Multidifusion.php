<?php
/*require 'classes/AutoLoad.php';
require 'classes/vendor/autoload.php';*/
$rest = new Rest();

/* inicio: generar archivo con el 'log' de peticiones */
ob_flush();
ob_start();

print_r($rest->getJson());

file_put_contents("peticiones", ob_get_flush(), FILE_APPEND | LOCK_EX);
ob_end_clean();