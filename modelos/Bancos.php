<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Bancos
{
	//Implementamos nuestro constructor
	public function __construct(){
	}

  //Implementamos un método para insertar registros
	public function insertar_banco($nombre_b, $alias, $formato_cta, $formato_cci, $formato_detracciones, $imagen){

    $sql_0 = "SELECT * FROM bancos  WHERE nombre = '$nombre_b';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {

      $sql="INSERT INTO bancos (nombre, alias, formato_cta, formato_cci, formato_detracciones, icono)
      VALUES ('$nombre_b', '$alias', '$formato_cta', '$formato_cci', '$formato_detracciones', '$imagen')";
      $insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 

      //add registro en nuestra bitacora
      // $sql_d = $nombre_b.', '.$alias.', '.$formato_cta.', '.$formato_cci.', '.$formato_detracciones.', '.$imagen;
      // $sql_bit = "INSERT INTO bitacora_bd( idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (5,'bancos','".$insertar['data']."','$sql_d','$this->id_usr_sesion')";
      // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  

      return $insertar;

    } else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Descripción: </b>'.$value['alias'].'<br>
					<b>Papelera: </b>'.( $value['estado']== 0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']== 0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

  //Implementamos un método para editar registros
	public function editar_banco($idbancos, $nombre_b, $alias, $formato_cta, $formato_cci, $formato_detracciones, $imagen){

    $sql_0 = "SELECT * FROM bancos  WHERE nombre = '$nombre_b' AND idbancos <> '$idbancos';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {

		$sql="UPDATE bancos SET nombre='$nombre_b', alias ='$alias', formato_cta='$formato_cta', 
		formato_cci='$formato_cci', formato_detracciones='$formato_detracciones', icono='$imagen'
		WHERE idbancos='$idbancos'";
		$editar =  ejecutarConsulta($sql, 'U'); if ( $editar['status'] == false) {return $editar; } 
	
		//add registro en nuestra bitacora
		// $sql_d = $idbancos.', '.$nombre_b.', '.$alias.', '.$formato_cta.', '.$formato_cci.', '.$formato_detracciones.', '.$imagen;
		// $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (6,'bancos','$idbancos','$sql_d','$this->id_usr_sesion')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
	
		return $editar;

  } else {
    $info_repetida = ''; 

    foreach ($existe['data'] as $key => $value) {
      $info_repetida .= '<li class="text-left font-size-13px">
        <span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
        <b>Alias: </b>'.$value['alias'].'<br>
        <b>Papelera: </b>'.( $value['estado']== 0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
        <b>Eliminado: </b>'. ($value['estado_delete']== 0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
        <hr class="m-t-2px m-b-2px">
      </li>'; 
    }
    return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
  }		
	}

  //Implementamos un método para desactivar bancos
	public function desactivar($idbancos){

		$sql="UPDATE bancos SET estado='0' WHERE idbancos='$idbancos'";
		$desactivar= ejecutarConsulta($sql); if ($desactivar['status'] == false) {  return $desactivar; }
		
		//add registro en nuestra bitacora
		// $sql_d = $idbancos;
		// $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (2,'persona','$idbancos','$sql_d','$this->id_usr_sesion')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $desactivar;
	}

	//Implementamos un método para activar bancos
	public function activar($idbancos){
		$sql="UPDATE bancos SET estado='1' WHERE idbancos='$idbancos'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar bancos
	public function eliminar($idbancos){

		$sql="UPDATE bancos SET estado_delete='0' WHERE idbancos='$idbancos'";
		$eliminar =  ejecutarConsulta($sql);
		if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora.', '.
		// $sql_d = $idbancos;
		// $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (4,'bancos','$idbancos','$sql_d','$this->id_usr_sesion')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idbancos){
		$sql="SELECT * FROM bancos WHERE idbancos='$idbancos'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar(){
		$sql="SELECT * FROM bancos WHERE idbancos > 1 	AND estado=1  AND estado_delete=1 ORDER BY idbancos asc,  nombre ASC";
		return ejecutarConsulta($sql);
	}
	
	//Implementar un método para listar los registros y mostrar en el select
	public function obtenerImg($id){
		$sql="SELECT icono FROM bancos where idbancos = '$id' ";
		return ejecutarConsultaSimpleFila($sql);		
	}





}