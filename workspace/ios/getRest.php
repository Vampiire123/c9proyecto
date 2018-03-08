<?php
require 'classes/AutoLoad.php';
require 'classes/vendor/autoload.php';

date_default_timezone_set('Europe/Madrid');

use \Firebase\JWT\JWT;

$rest = new Rest();
$cabeceraAutorizacion = $rest->getHeader('authorization');

$trozos = explode(' ', $cabeceraAutorizacion);
$key = 'izv';
$r = array('r' => 'no');
if(count($trozos) === 2) {
    if($trozos[0] === 'Basic') {
        $user = base64_decode($trozos[1]);
        $trozosUser = explode(':', $user);
        if(count($trozosUser) === 2) {
            if($trozosUser[0] === 'admin' && $trozosUser[1] === '1234') {
                $hora = new DateTime();
                $token = array(
                    'hora'    => $hora->getTimestamp() + 5,
                    'usuario' => $trozosUser[0]
                );
                $jwt = JWT::encode($token, $key);
                //1
                $r = array('r' => $jwt);
            }
        }
    } else  if ($trozos[0] === 'Bearer') {
        try {
            $decodedToken = JWT::decode($trozos[1], $key, array('HS256'));
        } catch (Exception $e) {
            exit;
        }
        $hora = new DateTime();
        if($hora->getTimestamp() < $decodedToken->hora) {
            $r = array('r' => 'a tiempo');
        } else {
            $r = array('r' => 'fuera de tiempo');
        }
    }
}
echo json_encode($r);

//no está: nada
//Bearer: token, descifrar y ver si sigue siendo válido
//Basic: usuario:clave, decode base 64, loguarte si sí y devolver token
exit;

//app -> admin, 1234; YWRtaW46MTIzNA==
//echo base64_encode('admin:1234');
//echo base64_decode('YWRtaW46MTIzNA==');
/*exit;




$token = array(
    'hora'    => '10:05:00',
    'usuario' => 'pepe'
);

$jwt = JWT::encode($token, $key);
echo 'Este es el token que voy a enviar:<br>';
echo $jwt;
$decodedToken = JWT::decode($jwt, $key, array('HS256'));
echo 'el token es valido hasta las: ' . $decodedToken->hora;
echo '<br>eres el usuario: ' . $decodedToken->usuario;
exit;*/

//header('Content-Type: application/json');
//$rest = new Rest();
//si estás autentificado -> bien
//si no lo estás -> mal
//$respuesta = array('r' => 'hemos terminado');
//echo json_encode($respuesta);

/* inicio: generar archivo con el 'log' de peticiones */
/*ob_flush();
ob_start();
echo 'metodo:' ;
print_r($rest->getMethod());
print PHP_EOL;
echo 'ruta:' ;
print_r($rest->getRoute());
print PHP_EOL;
echo 'acción:' ;
print_r($rest->getAction());
print PHP_EOL;
echo 'route parameters: ';
print_r($rest->getRouteParam());
print PHP_EOL;
echo 'querystring: ';
print_r($rest->getQueryString());
print PHP_EOL;
echo 'json: ';
print_r($rest->getJson());
print PHP_EOL;
echo 'headers: ';
print_r($rest->getHeader());
print PHP_EOL;
file_put_contents("peticiones", ob_get_flush(), FILE_APPEND | LOCK_EX);
ob_end_clean();*/
/* fin: generar archivo con el 'log' de peticiones */

/* procesar petición para generar respuesta en json */
/*$ruta = $rest->getRoute();
$respuesta = array();
if($ruta === 'persona') {
    if($rest->getMethod() === 'GET') {
        $respuesta = array(
            array('id' => 1, 'nombre' => 'pepe'),
            array('id' => 2, 'nombre' => 'paco')
        );
    } else if ($rest->getMethod() === 'POST') {
        $respuesta = array('id' => 3, 'nombre' => 'juana');
    }
}*/