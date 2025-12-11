<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Catalogo_y_codigo
  {
    //Implementamos nuestro constructor
    public $id_usr_sesion; 
    //Implementamos nuestro constructor
    public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
    {
      $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    function listar_tabla_afeccion_igv(){
      $sql = "SELECT * FROM sunat_c07_afeccion_de_igv WHERE estado = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    function listar_codigo_nota_credito(){
      $sql = "SELECT * FROM sunat_c09_codigo_nota_credito WHERE estado = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    function listar_codigo_nota_debito(){
      $sql = "SELECT * FROM sunat_c10_codigo_nota_debito WHERE estado = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    function listar_codigo_valor_venta(){
      $sql = "SELECT * FROM sunat_c11_codigo_valor_venta WHERE estado = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);
    }

  }
?>