<?php 

    echo "yo soy ejemplo.php";
    if(isset($_GET['url'])){
        $url = $_GET['url'];
        echo '<br> Datos que me llegan: '.$url;
    }
    var_dump($_GET);