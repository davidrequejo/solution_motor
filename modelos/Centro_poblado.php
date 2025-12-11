<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class CentroPoblado
{
	//Implementamos nuestro constructor
	public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
	// public $id_empresa_sesion;   
	public function __construct( )
	{
		$this->id_usr_sesion        =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
		$this->id_persona_sesion    = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
		$this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
		// $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
	}

	//Implementamos un método para insertar registros
	public function insertar($nombre, $descripcion) {		
		$sql_0 = "SELECT * FROM centro_poblado  WHERE nombre = '$nombre';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO centro_poblado(nombre, descripcion)VALUES('$nombre', '$descripcion')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('centro_poblado','".$insertar['data']."','Nueva centro_poblado registrado','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Descripción: </b>'.$value['descripcion'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

	//Implementamos un método para editar registros
	public function editar($idcentro_poblado, $nombre, $descripcion) {
		$sql_0 = "SELECT * FROM centro_poblado  WHERE nombre = '$nombre' AND idcentro_poblado <> '$idcentro_poblado';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE centro_poblado SET nombre='$nombre', descripcion ='$descripcion' WHERE idcentro_poblado='$idcentro_poblado'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
		
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
			// VALUES ('centro_poblado','$idcentro_poblado','centro_poblado editada','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Descripción: </b>'.$value['descripcion'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

	//Implementamos un método para desactivar color
	public function desactivar($idcentro_poblado) {
		$sql="UPDATE centro_poblado SET estado='0' WHERE idcentro_poblado='$idcentro_poblado'";
		$desactivar= ejecutarConsulta($sql, 'T'); if ($desactivar['status'] == false) {  return $desactivar; }
		
		// //add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('centro_poblado','".$idcentro_poblado."','centro_poblado desactivada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $desactivar;
	}

	//Implementamos un método para activar centro_poblado
	public function activar($idcentro_poblado) {
		$sql="UPDATE centro_poblado SET estado='1' WHERE idcentro_poblado='$idcentro_poblado'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar centro_poblado
	public function eliminar($idcentro_poblado) {
		
		$sql="UPDATE centro_poblado SET estado_delete='0' WHERE idcentro_poblado='$idcentro_poblado'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora
		// $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('centro_poblado', '$idcentro_poblado', 'centro_poblado Eliminada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcentro_poblado) {
		$sql="SELECT * FROM centro_poblado WHERE idcentro_poblado='$idcentro_poblado'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal_centro_poblado() {
		$sql="SELECT * FROM centro_poblado WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select2_centro_poblado() {
		$filtro_id_trabajador  = '';
		// if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {	$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";	} 
		// if ($_SESSION['user_cargo'] == 'PUNTO DE COBRO') { $filtro_id_trabajador = "AND (v.user_created = '$this->id_usr_sesion' OR pc.idpersona_trabajador = '$this->id_trabajador_sesion')";  } 
		$sql="SELECT COALESCE(cc.cant_cliente, 0) AS cant_cliente, COALESCE(cv.cant_venta, 0) AS cant_venta, cp.* FROM centro_poblado as cp
		left join (SELECT pc.idcentro_poblado, cp.nombre, COUNT(pc.idpersona_cliente) as cant_cliente FROM persona_cliente as pc INNER JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado WHERE pc.estado_delete='1' GROUP BY pc.idcentro_poblado, cp.nombre) as cc on cc.idcentro_poblado = cp.idcentro_poblado
		left join (SELECT pc.idcentro_poblado, cp.nombre, COUNT(v.idventa) as cant_venta FROM venta as v INNER JOIN persona_cliente as pc on pc.idpersona_cliente = v.idpersona_cliente INNER JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado 
		WHERE v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante in ('01', '03', '07', '12')  $filtro_id_trabajador GROUP BY pc.idcentro_poblado, cp.nombre ) as cv on cv.idcentro_poblado = cp.idcentro_poblado
		where cp.estado= '1' AND cp.estado_delete='1' ORDER BY cp.nombre;";
		return ejecutarConsulta($sql);		
	}
}
?>