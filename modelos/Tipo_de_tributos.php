<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Tipo_de_tributos
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
      $sql = "SELECT * FROM sunat_c05_tipo_tributo WHERE ESTADO = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    //Implementamos un método para insertar registros
		public function insertar($codg_sunat, $nombre, $codg) {
			$sql="INSERT INTO sunat_c05_tipo_tributo(code_sunat, nombre, unece5153)VALUES('$codg_sunat', '$nombre', '$codg')";
			$insertar =  ejecutarConsulta_retornarID($sql); if ($insertar['status'] == false) {  return $insertar; } 
			
			return $insertar;
		}

		//validar duplicado
		public function validar($nombre)	{
			$sql="SELECT * from sunat_c05_tipo_tributo where nombre='$nombre'";
			return ejecutarConsultaArray($sql);
		}

		public function editar($idsunat_c05_tipo_tributo, $codg_sunat, $nombre, $codg) {
			$sql="UPDATE sunat_c05_tipo_tributo SET code_sunat = '$codg_sunat', nombre='$nombre', unece5153 ='$codg' WHERE idsunat_c05_tipo_tributo='$idsunat_c05_tipo_tributo'";
			$editar =  ejecutarConsulta($sql);if ( $editar['status'] == false) {return $editar; } 
		
			return $editar;
		}

		public function mostrar($id){
			$sql = "SELECT * FROM sunat_c05_tipo_tributo Where idsunat_c05_tipo_tributo = '$id'";
			return ejecutarConsultaSimpleFila($sql);
		}

		//Implementamos un método para desactivar color
		public function desactivar($id) {
			$sql="UPDATE sunat_c05_tipo_tributo SET estado='0', user_trash= '" . $_SESSION['idusuario'] . "' WHERE idsunat_c05_tipo_tributo='$id'";
			$desactivar= ejecutarConsulta($sql);		
			return $desactivar;
		}

		//Implementamos un método para activar plan
		public function activar($id) {
			$sql="UPDATE sunat_c05_tipo_tributo SET estado='1' WHERE idsunat_c05_tipo_tributo='$id'";
			return ejecutarConsulta($sql);
		}

		//Implementamos un método para eliminar plan
		public function eliminar($id) {
			
			$sql="UPDATE sunat_c05_tipo_tributo SET estado_delete='0' WHERE idsunat_c05_tipo_tributo='$id'";
			$eliminar =  ejecutarConsulta($sql);if ( $eliminar['status'] == false) {return $eliminar; }  
			
			return $eliminar;
	}

  }
?>