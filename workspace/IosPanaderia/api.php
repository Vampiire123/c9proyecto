<?php
require 'Clases/Rest.php';
require 'Clases/RestAutorizacion.php';
require 'Clases/vendor/autoload.php';
require 'connectvars.php';
require 'Clases/ClasesTicket/detailticket.php';
require 'Clases/ClasesTicket/ticket.php';
//header('Content-Type: application/json');

//durante el desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

//entrada al servidor REST

//paso 1: obtener todos los datos de la petición (request)
$peticion = new Rest;


//paso 2: realizar la authorization
//echo json_encode($peticion->getHeader('authorization'));
//echo json_encode($peticion->getUrlParam(1));
//echo json_encode($peticion->getJson());

//return;
$autorizacion = new RestAutorizacion($peticion->getHeader('authorization'));
//YWRtaW46MTIzNA==
$ok = false;
if($peticion->getRoute() !== "productwp" && $peticion->getRoute() !== "familywp"){
    if($autorizacion->isValid()) {
        if($autorizacion->isBasic()) {
            if(isCorrectLogin($autorizacion->getUser(),$autorizacion->getPassword())) {
                $tokenJwt = $autorizacion->createToken();
                $respuesta['token'] = "º".$tokenJwt."º";
                $respuesta['userid'] = getIDUser($autorizacion->getUser());
                $ok = true;
            }
        } else{
            try {
                $decodedToken =$autorizacion->getDecodedToken() ;
                if(!$autorizacion->isExpiredToken($decodedToken)){
                    $tokenJwt = $autorizacion->createToken();
                    $respuesta['token'] = "º".$tokenJwt."º";
                    $ok = true;
                }else{
                    $respuesta['expired'] = "";
                    $ok = false;
                }
            } catch (Exception $e) {
                echo json_encode("fallo");
            }
        }
    }
} else {
    $ok=true;
}

if($ok) {
    if($peticion->getMethod() === 'GET' && ($peticion->getRoute() === 'product'||$peticion->getRoute() === 'productwp')) {
        $respuesta["products"] = getProducts();
    }else if($peticion->getMethod() === 'GET' && ($peticion->getRoute() === 'product'||$peticion->getRoute() === 'productwp')) {
        $respuesta["products"] = getProducts();
    } else if($peticion->getMethod() === 'GET' && ($peticion->getRoute() === 'family'||$peticion->getRoute() === 'familywp')) {
        $respuesta["families"] = getFamilies();
    }else if($peticion->getMethod() === 'GET' && ($peticion->getRoute() === 'ticket')) {
        $respuesta["ticket"] = getTickets();
        $respuesta["ticketdetail"] = getTicketDetails();
    }else if($peticion->getMethod() === 'GET' && ($peticion->getRoute() === 'lastticket')) {
        $respuesta["lastticket"] = getLastTicket();
    }else if($peticion->getMethod() === 'POST' && ($peticion->getRoute() === 'ticket')){
        $rest = $peticion;
        ob_flush();
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
        echo 'json: ';
        print_r($rest->getJson());
        print PHP_EOL;
        file_put_contents("peticiones", ob_get_flush(), FILE_APPEND | LOCK_EX);
        ob_end_clean();
        
        $ticket = $peticion->getJson('ticket');
        $ticketdetail = $peticion->getJson('ticketdetail');
        
        if($ticket != null){
            insertTicket($ticket);
        }else if($ticketdetail != null){
            insertTicketDetail($ticketdetail);
        }
    }
    
    if($peticion->getMethod() === 'GET'){
        $respuesta = utf8_converter($respuesta);
        echo json_encode($respuesta);
    }
}else{
    
        echo json_encode($respuesta);
}

function getIDUser($username){
    $query = "select id from member where login = '".$username."'";
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    $resultset = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($resultset);
    return $row['id'];
}

function insertTicket($ticket){
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    
    $ticketClase = new ticket($ticket['date'], $ticket['idmember'], $ticket['idclient']);
    $query = "insert into ticket(`id`, `date`, `idmember`, `idclient`) values(null, '".$ticketClase->date."', ".$ticketClase->idmember.", null)";
    echo $query;
    mysqli_query($dbc, $query);
    
    mysqli_close($dbc);
}

function getLastTicket(){
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    
    $query="select MAX(id) as 'id' from ticket";
    $resultset = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($resultset);
    $lastid = $row['id'];
    
    return $lastid;
    
    mysqli_close($dbc);
}

function insertTicketDetail($ticket){
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    
    $ticketdetail = new detailticket($ticket['idticket'], $ticket['idproduct'], $ticket['quantity'], $ticket['price']);
    $query = "insert into ticketdetail(`id`, `idticket`, `idproduct`, `quantity`, `price`) values(null, ".$ticketdetail->idticket.", ".$ticketdetail->idproduct.", ".$ticketdetail->quantity.", ".$ticketdetail->price.")";
    mysqli_query($dbc, $query);
    
    mysqli_close($dbc);
}

function utf8_converter($array){
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    });
    return $array;
}

function getProducts() {
    //return array("productos" => array("producto1", "producto2"));
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    $query="select * from product";
    $salida="";
    $resultset=mysqli_query($dbc,$query);
    while($row=mysqli_fetch_array($resultset)){
        $salida .= '-'.$row['product']."-".$row['price']."-".$row['description']."-".$row['idfamily']."-".$row['id'].'-FIN-';
    }
    mysqli_close($dbc);
    return $salida;
}

function getFamilies() {
    //return array("productos" => array("producto1", "producto2"));
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    $query="select * from family";
    $salida="";
    $resultset=mysqli_query($dbc,$query);
    while($row=mysqli_fetch_array($resultset)){
        $salida .='-'. $row['family'] . '-' . $row['id'] . '-FIN-';
    }
    mysqli_close($dbc);
    return $salida;
}

function getTickets() {
    //return array("productos" => array("producto1", "producto2"));
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    $query="select * from ticket";
    $salida="";
    $resultset=mysqli_query($dbc,$query);
    while($row=mysqli_fetch_array($resultset)){
        $salida .='º'. $row['id'] . 'º' . $row['date']. 'º' . $row['idmember'] . 'ºFINº';
    }
    mysqli_close($dbc);
    return $salida;
}

function getTicketDetails() {
    //return array("productos" => array("producto1", "producto2"));
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    $query="select * from ticketdetail";
    $salida="";
    $resultset=mysqli_query($dbc,$query);
    while($row=mysqli_fetch_array($resultset)){
        $salida .='-'. $row['id'] .'-'. $row['idticket'] . '-' . $row['idproduct']. '-' . $row['quantity']. '-' . $row['price']  . '-FIN-';
    }
    mysqli_close($dbc);
    return $salida;
}

function isCorrectLogin($user, $password) {
    //return array("productos" => array("producto1", "producto2"));
    $dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    $query="select * from member";
    $resultset=mysqli_query($dbc,$query);
    while($row=mysqli_fetch_array($resultset)){
        if($row['login']===$user && $row['password']===$password){
            $respuesta['user'] ="-".$row['id']."-";
            echo json_decode($row['login']." ".$row['password']);
            mysqli_close($dbc);
            return true;
        }
        
    }
    mysqli_close($dbc);
    return false;
}

