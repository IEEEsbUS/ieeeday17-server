<?php
        require 'Meta.php';

        $equipo=htmlspecialchars($_GET["equipo"]);
        $pass=htmlspecialchars($_GET["clave"]);
	$prueba=htmlspecialchars($_GET["prueba"]);

        $retorno=Meta::iniciar($equipo,$pass,$prueba);
        $descr='';
        
        if(empty($retorno)){
        }
        else{
                $descr=$retorno["descripcion"];
        }
	header('Content-Type: text/html; charset=utf-8');
	$descr=Meta::traducir($descr);
	print json_encode(array('descr'=>$descr));
?>
