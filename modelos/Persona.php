<?php 
  require "../config/Conexion_v2.php";

  class Persona
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

    public function mostrar_persona_id($id){
      $sql = "SELECT * FROM persona WHERE idpersona = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

  }
?>