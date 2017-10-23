<?php
        require 'Meta.php';

        $equipo=htmlspecialchars($_GET["equipo"]);
        $pass=htmlspecialchars($_GET["clave"]);
        $respuesta=htmlspecialchars($_GET["respuesta"]);
	$prueba=htmlspecialchars($_GET["prueba"]);

	$respuesta=strtolower($respuesta);

        $retorno=Meta::comprobarRespuesta($equipo,$pass,$respuesta,$prueba);
        $descr='';

        if(empty($retorno)){
        }
        else{
                $descr=$retorno;
        }
        print json_encode(array('correcto'=>$descr));
?>
