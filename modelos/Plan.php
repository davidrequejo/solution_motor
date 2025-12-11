<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Plan
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre_plan, $costo_plan) {
		$sql_0 = "SELECT * FROM plan  WHERE nombre = '$nombre_plan';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO plan(nombre, costo)VALUES('$nombre_plan', '$costo_plan')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan','".$insertar['data']."','Nueva plan registrado','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Costo: </b>'.$value['costo'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para editar registros
	public function editar($idplan, $nombre_plan, $costo_plan) {
		$sql_0 = "SELECT * FROM plan  WHERE nombre = '$nombre_plan' AND idplan <> '$idplan';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE plan SET nombre='$nombre_plan', costo ='$costo_plan' WHERE idplan='$idplan'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
		
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
			// VALUES ('plan','$idplan','plan editada','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Costo: </b>'.$value['costo'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para desactivar color
	public function desactivar($idplan) {
		$sql="UPDATE plan SET estado='0' WHERE idplan='$idplan'";
		$desactivar= ejecutarConsulta($sql, 'T');

		// if ($desactivar['status'] == false) {  return $desactivar; }
		
		// //add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan','".$idplan."','plan desactivada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $desactivar;
	}

	//Implementamos un método para activar plan
	public function activar($idplan) {
		$sql="UPDATE plan SET estado='1' WHERE idplan='$idplan'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar plan
	public function eliminar($idplan) {
		
		$sql="UPDATE plan SET estado_delete='0' WHERE idplan='$idplan'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora
		// $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan', '$idplan', 'plan Eliminada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idplan) {
		$sql="SELECT * FROM plan WHERE idplan='$idplan'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal_plan() {
		$sql="SELECT * FROM plan WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select() {
		$sql="SELECT * FROM plan where estado=1";
		return ejecutarConsulta($sql);		
	}
}
?>