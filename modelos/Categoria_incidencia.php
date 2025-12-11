<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Categoria_incidencia
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre ) {
		$sql_0 = "SELECT * FROM incidencia_categoria  WHERE nombre = '$nombre';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO incidencia_categoria(nombre)VALUES('$nombre')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para editar registros
	public function editar($idincidencia_categoria, $nombre, ) {
		$sql_0 = "SELECT * FROM incidencia_categoria  WHERE nombre = '$nombre' AND idincidencia_categoria <> '$idincidencia_categoria';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE incidencia_categoria SET nombre='$nombre' WHERE idincidencia_categoria='$idincidencia_categoria'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para desactivar color
	public function desactivar($idincidencia_categoria) {
		$sql="UPDATE incidencia_categoria SET estado='0' WHERE idincidencia_categoria='$idincidencia_categoria'";
		$desactivar= ejecutarConsulta($sql, 'T');
		return $desactivar;
	}

	//Implementamos un método para eliminar incidencia_categoria
	public function eliminar($idincidencia_categoria) {
		
		$sql="UPDATE incidencia_categoria SET estado_delete='0' WHERE idincidencia_categoria='$idincidencia_categoria'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  

		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idincidencia_categoria) {
		$sql="SELECT * FROM incidencia_categoria WHERE idincidencia_categoria='$idincidencia_categoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal_inc_categoria() {
		$sql="SELECT * FROM incidencia_categoria WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}


}
?>