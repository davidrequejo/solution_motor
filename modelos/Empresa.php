<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Empresa
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
      $sql = "SELECT CASE e.tipo_documento WHEN 1 THEN 'DNI' WHEN 6 THEN 'RUC' END AS tipo_documento_v2,  e.* FROM empresa as e WHERE e.estado = 1 AND e.estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    function insertar($tipo_doc, $documento, $razon_social, $nomb_comercial, $telefono1, $telefono2, 
    $web, $web_consulta, $correo, $logo_c_r, $img_logo, $banco1,   $cuenta1,  $cci1, $banco2,   $cuenta2,  $cci2, $banco3,   
    $cuenta3,  $cci3, $banco4,   $cuenta4,  $cci4, $codg_pais, $domicilio_fiscal, $distrito, $departamento, $provincia, $ubigeo, 
    $codg_ubigeo, $referencia){
      $sql = "INSERT INTO empresa (tipo_documento, numero_documento, nombre_razon_social, nombre_comercial, telefono1, telefono2, 
      web, web_consulta_cp, correo, logo_c_r, logo, banco1,   cuenta1,  cci1, banco2,   cuenta2,  cci2, banco3,   
      cuenta3,  cci3, banco4,   cuenta4,  cci4, codigo_pais, domicilio_fiscal, distrito, departamento, provincia, ubigueo, 
      codubigueo, texto_libre)
      VALUES ('6', '$documento', '$razon_social', '$nomb_comercial', '$telefono1', '$telefono2', 
      '$web', '$web_consulta', '$correo', '$logo_c_r', '$img_logo', '$banco1',   '$cuenta1',  '$cci1', '$banco2',   '$cuenta2',  '$cci2', '$banco3',   
      '$cuenta3',  '$cci3', '$banco4',   '$cuenta4',  '$cci4', '$codg_pais', '$domicilio_fiscal', '$distrito', '$departamento', '$provincia', '$ubigeo', 
      '$codg_ubigeo', '$referencia')";
      return ejecutarConsulta_retornarID($sql, 'C');
    }

    function editar($idempresa, $tipo_doc, $documento, $razon_social, $nomb_comercial, $telefono1, $telefono2, 
    $web, $web_consulta, $correo, $logo_c_r, $img_logo, $banco1,   $cuenta1,  $cci1, $banco2,   $cuenta2,  $cci2, $banco3,   
    $cuenta3,  $cci3, $banco4,   $cuenta4,  $cci4, $codg_pais, $domicilio_fiscal, $distrito, $departamento, $provincia, $ubigeo, 
    $codg_ubigeo, $referencia){
      $sql = "UPDATE empresa  SET tipo_documento = '6', numero_documento = '$documento', nombre_razon_social = '$razon_social', 
      nombre_comercial = '$nomb_comercial', telefono1 = '$telefono1', telefono2 = '$telefono2', web = '$web', web_consulta_cp = '$web_consulta', 
      correo = '$correo', logo_c_r = '$logo_c_r', logo = '$img_logo', banco1 = '$banco1',   cuenta1 = '$cuenta1',  cci1 = '$cci1', banco2 = '$banco2',  
      cuenta2 = '$cuenta2',  cci2 = '$cci2', banco3 = '$banco3', cuenta3 = '$cuenta3',  cci3 = '$cci3', banco4 = '$banco4',   cuenta4 = '$cuenta4',  
      cci4 = '$cci4', codigo_pais = '$codg_pais', domicilio_fiscal = '$domicilio_fiscal', distrito = '$distrito', departamento = '$departamento', 
      provincia = '$provincia', ubigueo = '$ubigeo', 
      codubigueo = '$codg_ubigeo', texto_libre = '$referencia'
      WHERE idempresa = '$idempresa' ";
      return ejecutarConsulta($sql, 'U');

    }

    function mostrar_empresa($idempresa){
      $sql = "SELECT * FROM empresa WHERE idempresa = '$idempresa';";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function consulta_banco($id)	{
      $sql="SELECT nombre FROM bancos WHERE idbancos = '$id' AND estado = 1 AND estado_delete = 1";
      return ejecutarConsulta($sql);   
    }

    public function desactivar($id){
      $sql = "UPDATE empresa SET estado = 0 WHERE idempresa = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function eliminar($id){
      $sql = "UPDATE empresa SET estado_delete = 0 WHERE idempresa = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function select_banco()	{
      $sql="SELECT * FROM bancos WHERE estado='1' AND estado_delete = '1'";
      return ejecutarConsultaArray($sql);   
    }
  }
?>