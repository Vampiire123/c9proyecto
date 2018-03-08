<?php
use \Firebase\JWT\JWT;
date_default_timezone_set('Europe/Madrid');

class RestAutorizacion{
    private $key='dam', $tiempoDeVida = 600;
    private $caberezaAutorizacion, $trozos;
    private $user, $password;
    
    function __construct($caberezaAutorizacion){
        $this->caberezaAutorizacion = $caberezaAutorizacion;
        $this->trozos = explode(' ',$caberezaAutorizacion);
    }
    
    function createToken(){
        $hora = new DateTime();
            $contenidoToken = array(
                'hora'    => $hora->getTimestamp() + $this->tiempoDeVida,
                'usuario' => $this->user
            );
        return JWT::encode($contenidoToken, $this->key);
    }
    
    function getDecodedToken(){
        try{
            return JWT::decode($this->trozos[1], $this->key, array('HS256'));
        }catch(Exception $e){
            return "";
        }
    }
    
    function getPassword(){
        return $this->password;
    }
    
    function getUser(){
        return $this->user;
    }
    
    function isBasic(){
        if($this->trozos[0]==='Basic'){
            $login = base64_decode($this->trozos[1]);
            $trozosUser = explode(':', $login);
            if(count($trozosUser)===2){
                $this->user=$trozosUser[0];
                $this->password=$trozosUser[1];
                return true;
            }
        }
        return false;
    }
    
    function isExpiredToken($token = null){
        $now = new DateTime();
        if($now->getTimestamp() < $token->hora){
            return false;
        }
        return true;
    }
    
    function isValid(){
        if(count($this->trozos) === 2 && ($this->trozos[0]==='Basic'|| $this->trozos[0]==='Bearer')) {
           return true; 
        }
        return false;
    }
}