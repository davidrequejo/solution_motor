<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Otros_gastos
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

    function insertar( $idproveedor, $idotros_gastos_categoria, $tipo_gasto_modulo, $tipo_comprobante, $serie_comprobante, 
      $fecha, $mes, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob){

      $sql_0 = "SELECT * FROM caja WHERE estado_caja = 'ABIERTO';";
      $caja = ejecutarConsultaSimpleFila($sql_0); if ( $caja['status'] == false) {return $caja; }

      if( !empty($caja['data']) ) {

        $idcaja = $caja['data']['idcaja'];
        $sql = "INSERT INTO ingreso_egreso_interno ( idpersona, idcaja, idingreso_egreso_categoria, tipo_gasto_modulo, tipo_comprobante, serie_comprobante, fecha_comprobante, periodo_gasto,  precio_sin_igv, precio_igv, val_igv, precio_con_igv, descripcion_comprobante, comprobante)
        VALUES ( '$idproveedor', $idcaja, '$idotros_gastos_categoria', '$tipo_gasto_modulo', '$tipo_comprobante', '$serie_comprobante', '$fecha', '$mes', '$precio_sin_igv', '$igv', '$val_igv', '$precio_con_igv', '$descr_comprobante', '$img_comprob')";
        return ejecutarConsulta_retornarID($sql, 'C');

      }else{
      $falta_caja = '<li>
                      <div class="text-start">Por favor, aperture una nueva <b>Caja</b> antes de agregar un registro</div>
                      <div class="text-start mt-2">Módulo: <a href="caja.php">Caja</a></div>
                    </li>';

      return array( 'status' => 'no_caja', 'message' => 'caja cerrada', 'data' => '<ul>'.$falta_caja.'</ul>', 'id_tabla' => '' );
      }
    }

    function editar($idgasto_de_trabajador, $idproveedor, $idcaja, $tipo_gasto_modulo, $idotros_gastos_categoria, $tipo_comprobante, $serie_comprobante, 
    $fecha, $mes, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob){
      $sql = "UPDATE ingreso_egreso_interno  SET  idingreso_egreso_categoria = '$idotros_gastos_categoria', tipo_comprobante = '$tipo_comprobante', serie_comprobante = '$serie_comprobante', fecha_comprobante = '$fecha', periodo_gasto = '$mes',
      idpersona = '$idproveedor', idcaja = '$idcaja', tipo_gasto_modulo = '$tipo_gasto_modulo', precio_sin_igv = '$precio_sin_igv', precio_igv = '$igv', val_igv = '$val_igv', precio_con_igv = '$precio_con_igv', descripcion_comprobante = '$descr_comprobante', comprobante = '$img_comprob'
      WHERE idingreso_egreso_interno = '$idgasto_de_trabajador' ";
      return ejecutarConsulta($sql, 'U');
    }

    function mostrar_detalle_gasto($id){

      $sql_2 = "SELECT iei.*,  DATE_FORMAT(iei.fecha_comprobante, '%d/%m/%Y') as fecha_comprobante_v2, ogc.nombre as categoria,
      CASE p.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN p.nombre_razonsocial
      END AS proveedor, p.foto_perfil as foto_perfil_proveedor, p.numero_documento,
       sdi.abreviatura as tipo_documento_nombre
      FROM ingreso_egreso_interno as iei
      INNER JOIN persona as p ON p.idpersona = iei.idpersona      
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN ingreso_egreso_categoria AS ogc on ogc.idingreso_egreso_categoria = iei.idingreso_egreso_categoria
      WHERE iei.estado = '1' AND iei.estado_delete = '1' AND iei.idingreso_egreso_interno = '$id' ;";
      return ejecutarConsultaSimpleFila($sql_2); 

    }

    function listar_tabla(){
      $sql = "SELECT iei.*, iec.nombre as categoria,
      CASE p.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN p.nombre_razonsocial
      END AS proveedor, p.foto_perfil as foto_perfil_proveedor, p.numero_documento,
       sdi.abreviatura as tipo_documento_nombre
      FROM ingreso_egreso_interno as iei
      INNER JOIN persona as p ON p.idpersona = iei.idpersona      
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN ingreso_egreso_categoria AS iec on iec.idingreso_egreso_categoria = iei.idingreso_egreso_categoria
      WHERE iEi.tipo_gasto_modulo = 'GASTOS' AND iei.estado = '1' AND iei.estado_delete = '1';";
      return ejecutarConsulta($sql);
    }

    public function desactivar($id){
      $sql="UPDATE ingreso_egreso_interno SET estado='0',user_trash= '$this->id_usr_sesion' WHERE idingreso_egreso_interno='$id'";
      $desactivar =  ejecutarConsulta($sql, 'U'); if ( $desactivar['status'] == false) {return $desactivar; }  
      
      //add registro en nuestra bitacora
      // $sql_d = $id;
      // $sql = "INSERT INTO bitacora_bd(idcodigo,nombre_tabla, id_tabla, sql_d, id_user) VALUES (2,'otros_gastos','.$id.','$sql_d','$this->id_usr_sesion')";
      // $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  

      return $desactivar;
    }

    public function eliminar($id) {
      $sql="UPDATE ingreso_egreso_interno SET estado_delete='0',user_delete= '$this->id_usr_sesion' WHERE idingreso_egreso_interno='$id'";
      $eliminar =  ejecutarConsulta($sql); if ( $eliminar['status'] == false) {return $eliminar; }  

      //add registro en nuestra bitacora
      // $sql_d = $id;
      // $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (4,'otros_gastos','$id','$sql_d','$this->id_usr_sesion')";
		  // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		  return $eliminar;
    }
    
    function listar_trabajador(){
      $sql = "SELECT p.*, pt.idpersona_trabajador, sdi.nombre as nombre_tipo_documento, pt.sueldo_mensual, c.nombre as cargo
      FROM persona_trabajador as pt      
      INNER JOIN persona AS p ON pt.idpersona = p.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
      WHERE p.idtipo_persona = 2 AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaArray($sql);
    }

    function listar_proveedor(){
      $sql = "SELECT p.idpersona, p.nombre_razonsocial AS nombre, p.apellidos_nombrecomercial AS apellido, p.numero_documento, tp.nombre as tipo_persona 
      FROM persona as p 
      LEFT JOIN persona_cliente as pc ON pc.idpersona = p.idpersona AND pc.estado = 1 AND pc.estado_delete = 1 
      LEFT JOIN persona_trabajador as pt ON pt.idpersona = p.idpersona AND pt.estado = 1 AND pt.estado_delete = 1 
      INNER JOIN tipo_persona as tp on tp.idtipo_persona = p.idtipo_persona 
      WHERE p.estado = 1 and p.estado_delete = 1 AND p.idpersona > 2 
      ORDER BY CASE WHEN tp.nombre LIKE 'P%' THEN 0 ELSE 1 END asc, tp.nombre desc, p.nombre_razonsocial asc;";
      return ejecutarConsultaArray($sql);
    }

    function mostrar_editar($id){
      $sql = "SELECT * FROM ingreso_egreso_interno WHERE idingreso_egreso_interno = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }
  }
?>

