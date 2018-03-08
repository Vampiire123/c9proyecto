<?php

class ticket{
    public $date, $idmember;
    
    function __construct($date, $idmember){
        $this->date = $date;
        $this->idmember = $idmember;
    }
}

?>