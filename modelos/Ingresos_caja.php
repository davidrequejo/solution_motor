<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Ingresos_caja
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

    function insertar($idtrabajador, $tipo_gasto_modulo, $idcategoria, $tipo_comprobante, $serie_comprobante, $fecha, $mes, $idproveedor, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob){
      $sql_0 = "SELECT * FROM caja WHERE estado_caja = 'ABIERTO';";
      $caja = ejecutarConsultaSimpleFila($sql_0); if ( $caja['status'] == false) {return $caja; }

      if( !empty($caja['data']) ) {
        $idcaja = $caja['data']['idcaja'];
        $sql = "INSERT INTO ingreso_egreso_interno (idpersona_trabajador, tipo_gasto_modulo, idcaja, idingreso_egreso_categoria, tipo_comprobante, serie_comprobante, fecha_comprobante, periodo_gasto, idpersona, precio_sin_igv, precio_igv, val_igv, precio_con_igv, descripcion_comprobante, comprobante)
        VALUES ('$idtrabajador', '$tipo_gasto_modulo', '$idcaja', '$idcategoria', '$tipo_comprobante', '$serie_comprobante', '$fecha', '$mes', '$idproveedor', '$precio_sin_igv', '$igv', '$val_igv', '$precio_con_igv', '$descr_comprobante', '$img_comprob')";
        return ejecutarConsulta_retornarID($sql, 'C');
      }else{
        $falta_caja = '<li>
                        <div class="text-start">Por favor, aperture una nueva <b>Caja</b> antes de agregar un registro</div>
                        <div class="text-start mt-2">Módulo: <a href="caja.php">Caja</a></div>
                      </li>';

        return array( 'status' => 'no_caja', 'message' => 'caja cerrada', 'data' => '<ul>'.$falta_caja.'</ul>', 'id_tabla' => '' );
      }
      
    }

    function editar($id, $idtrabajador, $tipo_gasto_modulo, $idcaja, $idcategoria, $tipo_comprobante, $serie_comprobante, $fecha, $mes, $idproveedor, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob){
      $sql = "UPDATE ingreso_egreso_interno  SET idpersona_trabajador = '$idtrabajador', tipo_gasto_modulo = '$tipo_gasto_modulo', idcaja = '$idcaja', idingreso_egreso_categoria = '$idcategoria', tipo_comprobante = '$tipo_comprobante', serie_comprobante = '$serie_comprobante',
      fecha_comprobante = '$fecha', periodo_gasto = '$mes', idpersona = '$idproveedor', precio_sin_igv = '$precio_sin_igv', precio_igv = '$igv', val_igv = '$val_igv', precio_con_igv = '$precio_con_igv', descripcion_comprobante = '$descr_comprobante', comprobante = '$img_comprob'
      WHERE idingreso_egreso_interno = '$id' ";
      return ejecutarConsulta($sql, 'U');
    }

    function mostrar_detalle_ingresos($id){

      $sql_2 = "SELECT igi.idingreso_egreso_interno, igi.idpersona as idproveedor, igi.tipo_comprobante, igi.serie_comprobante, igi.fecha_comprobante,  DATE_FORMAT(igi.fecha_comprobante, '%d/%m/%Y') as fecha_comprobante_f, 
      igi.periodo_gasto_day, igi.periodo_gasto_month, igi.periodo_gasto_year, igi.precio_sin_igv, igi.precio_igv, igi.val_igv, igi.precio_con_igv, igi.descripcion_comprobante, igi.comprobante,  igi.estado, iec.nombre as ie_categoria,
      CASE p.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN p.nombre_razonsocial
      END AS proveedor, p.foto_perfil as foto_perfil_proveedor, p.numero_documento as numero_documento_p, sdi_p.abreviatura as tipo_documento_nombre_p,
      CASE t.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(t.nombre_razonsocial, ' ', t.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN t.nombre_razonsocial
      END AS trabajador, t.foto_perfil as foto_perfil_trabajador, t.numero_documento as numero_documento_t, sdi.abreviatura as tipo_documento_nombre_t
      FROM ingreso_egreso_interno as igi
      INNER JOIN persona as p ON p.idpersona = igi.idpersona 
      INNER JOIN sunat_c06_doc_identidad as sdi_p ON sdi_p.code_sunat = p.tipo_documento
      INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = igi.idpersona_trabajador
      INNER JOIN persona as t ON t.idpersona = pt.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = t.tipo_documento
      INNER JOIN ingreso_egreso_categoria as iec ON igi.idingreso_egreso_categoria = iec.idingreso_egreso_categoria
      WHERE igi.estado = '1' AND igi.estado_delete = '1' AND igi.idingreso_egreso_interno = '$id';";
      return ejecutarConsultaSimpleFila($sql_2); 

    }

    function listar_tabla(){
      $sql = "SELECT igi.idingreso_egreso_interno, igi.idpersona, igi.tipo_comprobante, igi.serie_comprobante, igi.fecha_comprobante, igi.periodo_gasto_day, igi.periodo_gasto_month, 
      igi.periodo_gasto_year, igi.precio_sin_igv, igi.precio_igv, igi.val_igv, igi.precio_con_igv, igi.descripcion_comprobante, igi.comprobante,  igi.estado,
      CASE p.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN p.nombre_razonsocial
      END AS proveedor, p.foto_perfil as foto_perfil_proveedor, 
      CASE t.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(t.nombre_razonsocial, ' ', t.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN t.nombre_razonsocial
      END AS trabajador, t.foto_perfil as foto_perfil_trabajador, t.tipo_documento, t.numero_documento, sdi.abreviatura as tipo_documento_nombre
      FROM ingreso_egreso_interno as igi
      INNER JOIN persona as p ON p.idpersona = igi.idpersona 
      INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = igi.idpersona_trabajador
      INNER JOIN persona as t ON t.idpersona = pt.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = t.tipo_documento
      WHERE igi.tipo_gasto_modulo = 'INGRESOS' AND igi.estado = '1' AND igi.estado_delete = '1';";
      return ejecutarConsulta($sql);
    }

    public function desactivar($id){
      $sql="UPDATE ingreso_egreso_interno SET estado='0',user_trash= '$this->id_usr_sesion' WHERE idingreso_egreso_interno='$id'";
      $desactivar =  ejecutarConsulta($sql, 'U'); if ( $desactivar['status'] == false) {return $desactivar; }  
      
      //add registro en nuestra bitacora
      // $sql_d = $id;
      // $sql = "INSERT INTO bitacora_bd(idcodigo,nombre_tabla, id_tabla, sql_d, id_user) VALUES (2,'gasto_de_trabajador','.$id.','$sql_d','$this->id_usr_sesion')";
      // $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  

      return $desactivar;
    }

    public function eliminar($id) {
      $sql="UPDATE ingreso_egreso_interno SET estado_delete='0',user_delete= '$this->id_usr_sesion' WHERE idingreso_egreso_interno='$id'";
      $eliminar =  ejecutarConsulta($sql); if ( $eliminar['status'] == false) {return $eliminar; }  

      //add registro en nuestra bitacora
      // $sql_d = $id;
      // $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (4,'gasto_de_trabajador','$id','$sql_d','$this->id_usr_sesion')";
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
      INNER JOIN tipo_persona as tp on tp.idtipo_persona = p.idtipo_persona 
      WHERE p.idtipo_persona in ( 3 ,4 ) AND p.estado_delete = 1 AND p.idpersona > 2 
      ORDER BY tp.nombre DESC, p.nombre_razonsocial ASC";
      return ejecutarConsultaArray($sql);
    }

    function mostrar_editar_ic($id){
      $sql = "SELECT * FROM ingreso_egreso_interno WHERE idingreso_egreso_interno = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    //Implementamos un método para Listar y seleccionar un método
    public function seleccionar_ie_categoria()	{

      $sql="SELECT * FROM ingreso_egreso_categoria WHERE idingreso_egreso_categoria <> 1 AND estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   

    }

  }
?>