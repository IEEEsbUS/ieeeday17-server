<?php 
	require 'Database.php'; 
	class Meta {
		const N_PRUEBAS='10';

    		function __construct()
    		{
    		}

		public static function valUsuarios($equipo,$pass){
			$consulta="SELECT * FROM equipos WHERE equipo=? AND password=?";
			try{
				$comando=Database::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($equipo,$pass));
				return $comando->fetch(PDO::FETCH_ASSOC);
			}catch (PDOException $e){
				return -1;
			}
		}

		public static function actualizar($equipo,$pass){
			$validar=self::valUsuarios($equipo,$pass);
			return $validar;
		}

		public static function fin($equipo,$pass){
			$consulta="SELECT n_resueltas FROM clasificacion WHERE equipo=?";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($equipo));
			$resp=$comando->fetch(PDO::FETCH_ASSOC);
			$resp=$resp["n_resueltas"];
			if(strcmp($resp,self::N_PRUEBAS)===0)
				return "si";
			else
				return "no";
		}

		public static function iniciar($equipo,$pass,$prueba){
			$misionAct=self::valUsuarios($equipo,$pass);
			$misionAct=$misionAct['m_actual'];
			if(strcmp($prueba,$misionAct)===0){
				if(self::checkIniciado($equipo,$prueba)==0)
					self::registrarRespuesta("I",$equipo,$pass,$prueba,"Comienzo");
				$consulta="SELECT descripcion FROM misiones WHERE n_mision=?";
				$comando=Database::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($prueba));
				return $comando->fetch(PDO::FETCH_ASSOC);
			}
		}

		public static function comprobarRespuesta($equipo,$pass,$respuesta,$prueba){
			$consulta="SELECT misiones.solucion,equipos.m_actual,equipos.m_inicial FROM misiones,equipos WHERE equipos.equipo=? AND equipos.password=? AND equipos.m_actual=misiones.n_mision";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($equipo,$pass));
			$resp=$comando->fetch(PDO::FETCH_ASSOC);

			$m_actual=$resp["m_actual"];
                        $m_actual=intval($m_actual);
                        $m_ini=$resp["m_inicial"];
			$m_ini=intval($m_ini);
			$resp=$resp["solucion"];

			$fin=Meta::fin($equipo,$pass);

			$prueba=intval($prueba);

                        if(strcmp($resp,$respuesta)===0){
				if($m_ini==1&&$m_actual==self::N_PRUEBAS){
					$correcto="fin";
				}else if($m_actual==$m_ini-1){
					$correcto="fin";
				}else{
					$correcto="ok";
					self::respuestaCorrecta($equipo,$pass);
				}
				if(strcmp($fin,"no")===0)
					self::registrarRespuesta($correcto,$equipo,$pass,$prueba,$respuesta);
				self::actualizarClasificacion($equipo,$pass);
			}else{
				$correcto="no";
				
				if($prueba==$m_actual){
					if(strcmp($fin,"no")===0)
						self::registrarRespuesta($correcto,$equipo,$pass,$prueba,$respuesta);
				}
			}

                        return $correcto;
                }

		public static function ranking(){
			$consulta="SELECT equipo,password FROM equipos";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array());
			$resp=$comando->fetchAll(PDO::FETCH_ASSOC);

			for($i=0;$i<count($resp);$i++){
				self::actualizarClasificacion($resp[$i]["equipo"],$resp[$i]["password"]);
			}

			$consulta="SELECT * FROM clasificacion";
                        $comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array());
                        $resp=$comando->fetchAll(PDO::FETCH_ASSOC);

			print json_encode($resp);
		}

		public static function traducir($texto){
			$texto=str_replace('á','u00e1',$texto);
                        $texto=str_replace('Á','u00c1',$texto);
                        $texto=str_replace('é','u00e9',$texto);
                        $texto=str_replace('É','u00c9',$texto);
                        $texto=str_replace('í','u00ed',$texto);
                        $texto=str_replace('Í','u00cd',$texto);
                        $texto=str_replace('ó','u00f3',$texto);
                        $texto=str_replace('Ó','u00d3',$texto);
                        $texto=str_replace('ú','u00fa',$texto);
                        $texto=str_replace('Ú','u00da',$texto);
                        $texto=str_replace('ü','u00fc',$texto);
                        $texto=str_replace('Ü','u00dc',$texto);
                        $texto=str_replace('ñ','u00f1',$texto);
                        $texto=str_replace('Ñ','u00d1',$texto);
                        $texto=str_replace('ç','u00e7',$texto);
                        $texto=str_replace('Ç','u00c7',$texto);
                        $texto=str_replace('¿','u00bf',$texto);
			$texto=str_replace('¡','u00a1',$texto);
			return $texto;
		}

		private static function respuestaCorrecta($equipo,$pass){
			$consulta="SELECT equipos.m_actual FROM equipos WHERE equipo=? AND password=?";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo,$pass));
                        $resp=$comando->fetch(PDO::FETCH_ASSOC);
			$resp=$resp["m_actual"];
			$resp=intval($resp);

			if($resp==self::N_PRUEBAS)
				$resp=1;
			else
				$resp=$resp+1;

			$consulta="UPDATE equipos SET m_actual='$resp' WHERE equipo=? AND password=?";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo,$pass));
                        //$resp=$comando->fetch(PDO::FETCH_ASSOC);

			/*$consulta="SELECT n_resueltas FROM clasificacion WHERE equipo=?;
                        $comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo));
                        $resp=$comando->fetch(PDO::FETCH_ASSOC);
			$resp=$resp["n_resueltas"];*/
		}

		private static function registrarRespuesta($correcto,$equipo,$password,$prueba,$respuesta){
			$hora = round(microtime(true) * 1000);

			if(strcmp($correcto,"fin")===0||strcmp($correcto,"ok")===0)
				$resp="S";
			else if(strcmp($correcto,"I")===0)
				$resp="I";
			else
				$resp="N";

			$consulta="INSERT INTO respuestas (hora,equipo,password,n_mision,respuesta,correcto) VALUES ('$hora','$equipo','$password','$prueba','$respuesta','$resp')";
	                $comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array());
                        //$resp=$comando->fetch(PDO::FETCH_ASSOC);
		}

		private static function actualizarClasificacion($equipo,$password){
			$consulta="SELECT equipos.m_inicial FROM equipos WHERE equipos.equipo=? AND equipos.password=?";
                        $comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo,$password));
                        $resp=$comando->fetch(PDO::FETCH_ASSOC);
                        $m_inicial=$resp["m_inicial"];
			$m_inicial=intval($m_inicial);

			$consulta="SELECT * FROM respuestas WHERE equipo=? AND password=? ORDER BY hora";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo,$password));
                        $resp=$comando->fetchAll(PDO::FETCH_ASSOC);
			$respondidas=0;
			$ultCheck=$m_inicial;
			$t_acumulado=0;
			$t_aux=0;

			$misionAux=$resp[0]["n_mision"];

			for($i=0;$i<count($resp);$i++){
				$misionAux=intval($resp[$i]["n_mision"]);
				if($misionAux==$ultCheck){
					if(strcmp($resp[$i]["correcto"],"I")===0){
						$t_aux=intval($resp[$i]["hora"]);
					}
					else if(strcmp($resp[$i]["correcto"],"S")===0){
						if($ultCheck==self::N_PRUEBAS)
							$ultCheck=0;
						$ultCheck++;
						$t_acumulado=$t_acumulado+(intval($resp[$i]["hora"])-$t_aux);
						$respondidas+=1;
					}else{
						$t_acumulado+=10*1000;
					}
				}
			}
			$consulta="UPDATE clasificacion SET tiempo=$t_acumulado,n_resueltas=$respondidas WHERE equipo=?";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo));

			return $respondidas;
		}

		private static function checkIniciado($equipo,$prueba){
			$consulta="SELECT correcto FROM respuestas WHERE equipo=? AND n_mision=?";
			$comando=Database::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($equipo,$prueba));
			$resp=$comando->fetchAll(PDO::FETCH_ASSOC);

			for($i=0;$i<count($resp);$i++){
				if(strcmp($resp[$i]["correcto"],"I")===0)
					return 1;
			}

			return 0;
		}
	}

?>
