<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Zona
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar_zona($nombre_zona,$ip_antena) {
		$sql_0 = "SELECT * FROM zona_antena  WHERE nombre = '$nombre_zona' and ip_antena = '$ip_antena';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO zona_antena(nombre, ip_antena)VALUES('$nombre_zona', '$ip_antena')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('zona_antena','".$insertar['data']."','Nueva zona_antena registrado','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>IP: </b>'.$value['ip_antena'].'</span><br>
					<b>Nombre: </b>'.$value['nombre'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

	//Implementamos un método para editar registros
	public function editar_zona($idzona_antena, $nombre_zona, $ip_antena) {
		
		$sql_0 = "SELECT * FROM zona_antena  WHERE ip_antena = '$ip_antena' AND idzona_antena <> '$idzona_antena';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE zona_antena SET nombre='$nombre_zona', ip_antena ='$ip_antena' WHERE idzona_antena='$idzona_antena'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
		
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
			// VALUES ('zona_antena','$idzona_antena','zona_antena editada','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>IP: </b>'.$value['ip_antena'].'</span><br>
					<b>Nombre: </b>'.$value['nombre'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

	//Implementamos un método para desactivar color
	public function desactivar_zona($idzona_antena) {
		$sql="UPDATE zona_antena SET estado='0' WHERE idzona_antena='$idzona_antena'";
		$desactivar= ejecutarConsulta($sql, 'T'); if ($desactivar['status'] == false) {  return $desactivar; }
		
		// //add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('zona_antena','".$idzona_antena."','zona_antena desactivada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $desactivar;
	}

	//Implementamos un método para activar zona_antena
	public function activar_zona($idzona_antena) {
		$sql="UPDATE zona_antena SET estado='1' WHERE idzona_antena='$idzona_antena'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar zona_antena
	public function eliminar_zona($idzona_antena) {
		
		$sql="UPDATE zona_antena SET estado_delete='0' WHERE idzona_antena='$idzona_antena'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora
		// $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('zona_antena', '$idzona_antena', 'zona_antena Eliminada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar_zona($idzona_antena) {
		$sql="SELECT * FROM zona_antena WHERE idzona_antena='$idzona_antena'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal_zona() {
		$sql="SELECT * FROM zona_antena WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select() {
		$sql="SELECT * FROM zona_antena where estado=1";
		return ejecutarConsulta($sql);		
	}
}
?>