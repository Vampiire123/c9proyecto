<?php

class detailticket{
    public $idticket, $idproduct, $quantity, $price;
    
    function __construct($idticket, $idproduct, $quantity, $price){
        $this->idticket = $idticket;
        $this->idproduct = $idproduct;
        $this->quantity = $quantity;
        $this->price = $price;
    }
}

?>