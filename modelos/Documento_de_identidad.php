<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Documento_de_identidad
  {
    //Implementamos nuestro constructor
    public $id_usr_sesion; 
    // public $id_empresa_sesion;
    //Implementamos nuestro constructor
    public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
    {
      $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    function listar_tabla(){
      $sql = "SELECT * FROM sunat_c06_doc_identidad WHERE estado = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    //Implementamos un método para insertar registros
	public function insertar($nombre, $abreviatura, $codigo) {
		$sql="INSERT INTO sunat_c06_doc_identidad(nombre, abreviatura, code_sunat)VALUES('$nombre', '$abreviatura', '$codigo')";

		$insertar =  ejecutarConsulta_retornarID($sql); 
		if ($insertar['status'] == false) {  return $insertar; } 
		
		//add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan','".$insertar['data']."','Nueva plan registrado','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $insertar;
	}

	//validar duplicado
	public function validar($nombre)	{
		$sql="SELECT * FROM sunat_c06_doc_identidad where nombre='$nombre'";
		return ejecutarConsultaArray($sql);
	}

	public function editar($idsunat_c06_doc_identidad, $nombre, $abreviatura, $codigo) {
		$sql="UPDATE sunat_c06_doc_identidad SET nombre='$nombre', abreviatura = '$abreviatura', code_sunat ='$codigo' WHERE idsunat_c06_doc_identidad='$idsunat_c06_doc_identidad'";
		$editar =  ejecutarConsulta($sql);
		if ( $editar['status'] == false) {return $editar; } 
	
		//add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
		// VALUES ('plan','$idsunat_tipo_tributo','plan editada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
	
		return $editar;
	}

  public function mostrar($id){
    $sql = "SELECT * FROM sunat_c06_doc_identidad Where idsunat_c06_doc_identidad = '$id'";
    return ejecutarConsultaSimpleFila($sql);
  }

  //Implementamos un método para desactivar color
	public function desactivar($id) {
		$sql="UPDATE sunat_c06_doc_identidad SET estado='0', user_trash= '" . $_SESSION['idusuario'] . "' WHERE idsunat_c06_doc_identidad='$id'";
		$desactivar= ejecutarConsulta($sql);

		// if ($desactivar['status'] == false) {  return $desactivar; }
		
		// //add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan','".$idsunat_tipo_tributo."','plan desactivada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $desactivar;
	}

	//Implementamos un método para eliminar plan
	public function eliminar($id) {
		
		$sql="UPDATE sunat_c06_doc_identidad SET estado_delete='0' WHERE idsunat_c06_doc_identidad='$id'";
		$eliminar =  ejecutarConsulta($sql);
		if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora
		// $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan', '$idsunat_tipo_tributo', 'plan Eliminada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
	}

  }
?>