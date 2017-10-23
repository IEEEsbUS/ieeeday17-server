<?php
	require 'Meta.php';
	
	$equipo=htmlspecialchars($_GET["equipo"]);
	$pass=htmlspecialchars($_GET["pass"]);
	
	if(1)
		$retorno=Meta::actualizar($equipo,$pass);
	$status='no';
	$pruebaIni='0';
	$pruebaAct='0';
	$fin='no';
	//print 'hola';
	if(empty($retorno)){
	}
	else{
		$status='ok';
		$pruebaAct=$retorno['m_actual'];
		$pruebaIni=$retorno['m_inicial'];
		$fin=Meta::fin($equipo,$pass);
	}
	print json_encode(array('status'=>$status,'pruebaIni'=>$pruebaIni,'pruebaAct'=>$pruebaAct,'fin'=>$fin));
?>
