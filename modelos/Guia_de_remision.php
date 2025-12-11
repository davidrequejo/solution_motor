<?php

  require "../config/Conexion_v2.php";

  class Guia_Remision_Remitente
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

    public function listar_tabla_guia() {

      $sql = "SELECT *
      FROM vw_facturacion AS vw_f
      WHERE vw_f.estado_v = '1' AND vw_f.estado_delete_v = '1' AND vw_f.tipo_comprobante IN( '09');";
      $compra = ejecutarConsulta($sql); if ($compra['status'] == false) {return $compra; }

      return $compra;
    }

    public function insertar(
      // DATOS TABLA VENTA
      $serie_comprobante, $modalidad_transporte, $motivo_traslado, $documento_asociado, $idcliente, $partida_direccion,
      $partida_distrito, $partida_ubigeo, $llegada_direccion, $llegada_distrito, $llegada_ubigeo, $peso_total, $idpersona_chofer,
      $numero_documento, $numero_licencia, $numero_placa, $nombre_razonsocial, $apellidos_nombrecomercial,
      $f_guia_subtotal, $f_guia_igv, $f_guia_descuento, $f_guia_total, $gr_observacion,
      //DATOS TABLA VENTA DETALLE
      $idproducto, $pr_marca, $pr_categoria,$pr_nombre, $um_nombre, $um_abreviatura, $cantidad, $precio_compra, $precio_sin_igv, $precio_igv, $precio_con_igv, 
      $precio_venta_descuento, $descuento, $descuento_porcentaje, $subtotal_producto, $subtotal_no_descuento  
    ){

      $gr_idconductor = ''; $fecha_actual_amd = date('Y-m-d');

      // BUSCAMOS EL ERROR ANTERIOR
      $sql = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante, 
      DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_format 
      FROM venta AS v 
      WHERE v.tipo_comprobante = '09' and ((v.sunat_error <> '' AND  v.sunat_error is not null  ) or (v.sunat_observacion <> '' AND  v.sunat_observacion is not null  ));";
      $buscando_error = ejecutarConsultaArray($sql); if ($buscando_error['status'] == false) {return $buscando_error; }

      // VALIDAMOS EL PERIODO CONTABLE
      $sql_periodo = "SELECT idperiodo_contable FROM periodo_contable WHERE estado = '1' AND estado_delete = '1' AND '$fecha_actual_amd' BETWEEN fecha_inicio AND fecha_fin;";
      $buscando_periodo = ejecutarConsultaSimpleFila($sql_periodo); if ($buscando_periodo['status'] == false) {return $buscando_periodo; }
      $idperiodo_contable = empty($buscando_periodo['data']) ? '' : (empty($buscando_periodo['data']['idperiodo_contable']) ? '' : $buscando_periodo['data']['idperiodo_contable'] ) ;
  
      // return $sql_periodo;
      if ( empty($idperiodo_contable) ) {  
        $retorno = array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'No existe periodo!!', 'message' => ' No cierre el módulo. <br> No existe un periodo contable del mes: <b>'. nombre_mes(date('Y-m-d')).'-'.date('Y'). '</b>. '. ($_SESSION['user_cargo'] == 'ADMINISTRADOR' ? 'Configure el período contable en el módulo siguiente: <a href="periodo_facturacion.php" target="_blank" >Periodo contable</a>' : 'Solicite a su administrador que configure el período contable para el mes actual.'), 'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_error['data'], 'id_tabla' => '' );
        return $retorno;
      }

      if ($modalidad_transporte == '01') {
        $gr_idconductor = $idpersona_chofer;
      } else if ($modalidad_transporte == '02') {
        $sql_chofer = "SELECT * FROM persona where numero_documento = '$numero_documento';";
        $buscando_chofer = ejecutarConsultaSimpleFila($sql_chofer); if ($buscando_chofer['status'] == false) {return $buscando_chofer; }

        if ( empty($buscando_chofer['data']) ) {
          $sql1 = "INSERT INTO persona(idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, 
          apellidos_nombrecomercial, tipo_documento, numero_documento, fecha_nacimiento, numero_licencia, placa_vehiculo  ) 
          VALUES ( '5', '1', '1', 'NATURAL', '$nombre_razonsocial', '$apellidos_nombrecomercial', '1', '$numero_documento', null, '$numero_licencia','$numero_placa')";
          $new_chofer = ejecutarConsulta_retornarID($sql1, 'C');if ($new_chofer['status'] == false) {return $new_chofer;}
          $gr_idconductor = $new_chofer['data'];
        }else {
          $gr_idconductor = $buscando_chofer['data']['idpersona'];
          $sql1 = "UPDATE persona SET nombre_razonsocial = '$nombre_razonsocial', apellidos_nombrecomercial = '$apellidos_nombrecomercial',  
          numero_documento = '$numero_documento', numero_licencia = '$numero_licencia', placa_vehiculo = '$numero_placa'  WHERE idpersona = '$gr_idconductor';";
          $update_chofer = ejecutarConsulta($sql1, 'U'); if ($update_chofer['status'] == false) {return $update_chofer;}
        }
      }

      if ( empty( $buscando_error['data'] ) ) {
        $sql_1 = "INSERT INTO venta(idpersona_cliente, idperiodo_contable,  crear_enviar_sunat, idsunat_c01, tipo_comprobante, serie_comprobante,  
        impuesto, venta_subtotal, venta_descuento, venta_igv, venta_total, venta_cuotas, vc_estado, usar_anticipo, 
        gr_idventa_asociada, gr_cod_modalidad_traslado, gr_cod_motivo_traslado, gr_peso_total,  gr_idconductor, gr_placa, 
        gr_numero_licencia, gr_partida_direccion, gr_partida_distrito, gr_partida_ubigeo, gr_llegada_direccion, gr_llegada_distrito, gr_llegada_ubigeo, observacion_documento) 
        VALUES ('$idcliente', '$idperiodo_contable', 'NO', '10', '09', '$serie_comprobante', '0', '$f_guia_subtotal', '$f_guia_descuento', '$f_guia_igv','$f_guia_total',
        'NO', 'pagado', 'NO', '$documento_asociado', '$modalidad_transporte', '$motivo_traslado', '$peso_total', '$gr_idconductor', '$numero_placa', '$numero_licencia', 
        '$partida_direccion',  '$partida_distrito', '$partida_ubigeo', '$llegada_direccion', '$llegada_distrito', '$llegada_ubigeo', '$gr_observacion')"; 
        $newdata = ejecutarConsulta_retornarID($sql_1, 'C'); if ($newdata['status'] == false) { return  $newdata;}
        $id = $newdata['data'];

        $i = 0;
        $detalle_new = "";
        $monto_recibido = 0;  
       
        if ( !empty($newdata['data']) ) {      
          while ($i < count($idproducto)) {

            $sql_2 = "INSERT INTO venta_detalle( idventa, idproducto, pr_nombre, pr_marca, pr_categoria, pr_unidad_medida, v_tipo_comprobante, cantidad, precio_compra, 
            precio_venta, precio_venta_descuento, descuento, descuento_porcentaje, subtotal, subtotal_no_descuento, um_nombre, um_abreviatura)
            VALUES ('$id', '$idproducto[$i]', '$pr_nombre[$i]', '$pr_marca[$i]', '$pr_categoria[$i]', '$um_nombre[$i]', 'GUIA REMISION REMITENTE', '$cantidad[$i]', 
            '$precio_compra[$i]',  '$precio_con_igv[$i]', '$precio_venta_descuento[$i]', '$descuento[$i]', '$descuento_porcentaje[$i]', '$subtotal_producto[$i]', 
            '$subtotal_no_descuento[$i]', '$um_nombre[$i]', '$um_abreviatura[$i]');";
            $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
            $id_d = $detalle_new['data'];            

            $i = $i + 1;
          }
        }

        return $newdata;
      } else {
        $info_repetida = ''; 

        foreach ($buscando_error['data'] as $key => $val) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-13px text-danger"><b>Fecha: </b>'.$val['fecha_emision_format'].'</span><br>
            <span class="font-size-13px text-danger"><b>Comprobante: </b>'.$val['serie_y_numero_comprobante'].'</span><br>
            <span class="font-size-13px text-danger"><b>Total: </b>'.$val['venta_total'].'</span><br>
            <span class="font-size-13px text-danger"><b>ID: </b>'.$val['idventa_v2'].'</span><br>
            <span class="font-size-13px text-danger"><b>Error: </b>'.$val['sunat_error'].'</span><br>
            <span class="font-size-13px text-danger"><b>Observación: </b>'.$val['sunat_observacion'].'</span><br>            
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }

        $retorno = array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'Errores anteriores!!', 'message' => 'No se podran generar comprobantes hasta corregir los errores anteriores a este: '. $info_repetida, 'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_error['data'], 'id_tabla' => '' );
        return $retorno;
      }      
    }

    public function editar(
      // DATOS TABLA VENTA
      $idventa, $serie_comprobante, $modalidad_transporte, $motivo_traslado, $documento_asociado, $idcliente, $partida_direccion,
      $partida_distrito, $partida_ubigeo, $llegada_direccion, $llegada_distrito, $llegada_ubigeo, $peso_total, $idpersona_chofer,
      $numero_documento, $numero_licencia, $numero_placa, $nombre_razonsocial, $apellidos_nombrecomercial,
      $f_guia_subtotal, $f_guia_igv, $f_guia_descuento, $f_guia_total, $gr_observacion,
      //DATOS TABLA VENTA DETALLE
      $idproducto, $pr_marca, $pr_categoria,$pr_nombre, $um_nombre, $um_abreviatura, $cantidad, $precio_compra, $precio_sin_igv, $precio_igv, $precio_con_igv, 
      $precio_venta_descuento, $descuento, $descuento_porcentaje, $subtotal_producto, $subtotal_no_descuento  ) {

      $gr_idconductor = ''; $fecha_actual_amd = date('Y-m-d');

      if ($modalidad_transporte == '01') {
        $gr_idconductor = $idpersona_chofer;
      } else if ($modalidad_transporte == '02') {
        $sql_chofer = "SELECT * FROM persona where numero_documento = '$numero_documento';";
        $buscando_chofer = ejecutarConsultaSimpleFila($sql_chofer); if ($buscando_chofer['status'] == false) {return $buscando_chofer; }

        if ( empty($buscando_chofer['data']) ) {
          $sql1 = "INSERT INTO persona(idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, 
          apellidos_nombrecomercial, tipo_documento, numero_documento, fecha_nacimiento, numero_licencia, placa_vehiculo  ) 
          VALUES ( '5', '1', '1', 'NATURAL', '$nombre_razonsocial', '$apellidos_nombrecomercial', '1', '$numero_documento', null, '$numero_licencia','$numero_placa')";
          $new_chofer = ejecutarConsulta_retornarID($sql1, 'C');if ($new_chofer['status'] == false) {return $new_chofer;}
          $gr_idconductor = $new_chofer['data'];
        }else {
          $gr_idconductor = $buscando_chofer['data']['idpersona'];
          $sql1 = "UPDATE persona SET nombre_razonsocial = '$nombre_razonsocial', apellidos_nombrecomercial = '$apellidos_nombrecomercial',  
          numero_documento = '$numero_documento', numero_licencia = '$numero_licencia', placa_vehiculo = '$numero_placa'  WHERE idpersona = '$gr_idconductor';";
          $update_chofer = ejecutarConsulta($sql1, 'U'); if ($update_chofer['status'] == false) {return $update_chofer;}
        }
      }

      $sql_1 = "UPDATE venta SET idpersona_cliente = '$idcliente',      
       venta_subtotal = '$f_guia_subtotal', venta_descuento = '$f_guia_descuento', venta_igv = '$f_guia_igv', 
      venta_total = '$f_guia_total', gr_idventa_asociada='$documento_asociado',gr_cod_modalidad_traslado='$modalidad_transporte',gr_cod_motivo_traslado='$motivo_traslado',gr_peso_total='$peso_total',
      gr_idconductor='$gr_idconductor',gr_placa='$numero_placa',gr_numero_licencia='$numero_licencia',gr_partida_direccion='$partida_direccion',gr_partida_distrito='$partida_distrito',gr_partida_ubigeo='$partida_ubigeo',
      gr_llegada_direccion='$llegada_direccion',gr_llegada_distrito='$llegada_distrito',gr_llegada_ubigeo='$llegada_ubigeo', observacion_documento = '$gr_observacion'     
      WHERE idventa = '$idventa'";
      $actualizar_venta = ejecutarConsulta($sql_1, 'U');if ($actualizar_venta['status'] == false) { return $actualizar_venta; }

      // Eliminamos los productos
      $sql_del1 = "DELETE FROM venta_detalle WHERE idventa = '$idventa'"; ejecutarConsulta($sql_del1);

      $i = 0;
      $detalle_new = "";
      $monto_recibido = 0;  
      
      
      while ($i < count($idproducto)) {

        $sql_2 = "INSERT INTO venta_detalle( idventa, idproducto, pr_nombre, pr_marca, pr_categoria, pr_unidad_medida, v_tipo_comprobante, cantidad, precio_compra, 
        precio_venta, precio_venta_descuento, descuento, descuento_porcentaje, subtotal, subtotal_no_descuento, um_nombre, um_abreviatura)
        VALUES ('$idventa', '$idproducto[$i]', '$pr_nombre[$i]', '$pr_marca[$i]', '$pr_categoria[$i]', '$um_nombre[$i]', 'GUIA REMISION REMITENTE', '$cantidad[$i]', 
        '$precio_compra[$i]',  '$precio_con_igv[$i]', '$precio_venta_descuento[$i]', '$descuento[$i]', '$descuento_porcentaje[$i]', '$subtotal_producto[$i]', 
        '$subtotal_no_descuento[$i]', '$um_nombre[$i]', '$um_abreviatura[$i]');";
        $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
        $id_d = $detalle_new['data'];            

        $i = $i + 1;
      }
      

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => $idventa, 'id_tabla' => $idventa,  ];
    }
  

    public function mostrar_detalle_guia($idcompra){

      $sql_1 = "SELECT c.*, p.*, tc.abreviatura as tp_comprobante, sdi.abreviatura as tipo_documento, c.estado
      FROM compra AS c
      INNER JOIN persona AS p ON c.idproveedor = p.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = c.tipo_comprobante
      WHERE c.idcompra = '$idcompra'
      AND c.estado = 1 AND c.estado_delete = 1";
      $compra = ejecutarConsultaSimpleFila($sql_1); if ($compra['status'] == false) {return $compra; }


      $sql_2 = "SELECT cd.*, pd.*
      FROM compra_detalle AS cd
      INNER JOIN producto AS pd ON cd.idproducto = pd.idproducto
      WHERE  cd.idcompra = '$idcompra'
      AND cd.estado = 1 AND cd.estado_delete = 1";
      $detalle = ejecutarConsultaArray($sql_2); if ($detalle['status'] == false) {return $detalle; }

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => ['compra' => $compra['data'], 'detalle' => $detalle['data']]];

    }


    public function mostrar_editar_guia($id){
      $sql = "SELECT * FROM vw_facturacion AS vw_f  WHERE vw_f.idventa = '$id'";
      $gr_cabecera = ejecutarConsultaSimpleFila($sql);
      $sql = "SELECT * FROM vw_facturacion_detalle as vw_fd where vw_fd.idventa = '$id'";
      $gr_detalle = ejecutarConsultaArray($sql);

      return ['status' => true, 'message' =>'todo okey', 'data'=>['guia_cabecera' => $gr_cabecera['data'], 'guia_detalle' => $gr_detalle['data'],]];
    }   

    public function eliminar($id){
      $sql = "UPDATE compra SET estado_delete = 0
      WHERE idcompra = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function desactivar($id){
      $sql = "UPDATE compra SET estado = 0
      WHERE idcompra = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function listar_tabla_producto(){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      WHERE p.idproducto_categoria <> 2  AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsulta($sql);
    }

    public function mostrar_producto($idproducto){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      WHERE p.idproducto = '$idproducto'  AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_producto_x_codigo($codigo){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      WHERE (p.codigo = '$codigo' OR p.codigo_alterno = '$codigo' ) AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);      
    }

    // ══════════════════════════════════════ S E A R C H   C H O F E R ══════════════════════════════════════
    public function agregar_chofer_publico( $cp_tipo_persona_sunat, $cp_idtipo_persona, $cp_tipo_documento, $cp_numero_documento, $cp_nombre_razonsocial, 
    $cp_apellidos_nombrecomercial,  $cp_correo, $cp_celular, $cp_numero_licencia, $cp_placa_vehiculo){
      $sql = "INSERT INTO persona( idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, apellidos_nombrecomercial, 
      tipo_documento, numero_documento, fecha_nacimiento, celular, direccion,  correo,  numero_licencia, placa_vehiculo) VALUES 
      ('$cp_idtipo_persona', '1','1','$cp_tipo_persona_sunat','$cp_nombre_razonsocial','$cp_apellidos_nombrecomercial','$cp_tipo_documento','$cp_numero_documento',null,
      '$cp_celular','','$cp_correo','$cp_numero_licencia', '$cp_placa_vehiculo')";
      return ejecutarConsulta_retornarID($sql);      
    }
    public function editar_chofer_publico($cp_idpersona, $cp_tipo_persona_sunat, $cp_idtipo_persona, $cp_tipo_documento, $cp_numero_documento, $cp_nombre_razonsocial, 
    $cp_apellidos_nombrecomercial, $cp_correo, $cp_celular, $cp_numero_licencia, $cp_placa_vehiculo){
      $sql = "UPDATE persona SET idtipo_persona='$cp_idtipo_persona',tipo_persona_sunat='$cp_tipo_persona_sunat',nombre_razonsocial='$cp_nombre_razonsocial',
      apellidos_nombrecomercial='$cp_apellidos_nombrecomercial',tipo_documento='$cp_tipo_documento',numero_documento='$cp_numero_documento',
      celular='$cp_celular', correo = '$cp_correo',numero_licencia='$cp_numero_licencia', placa_vehiculo='$cp_placa_vehiculo' WHERE idpersona  ='$cp_idpersona';";
      return ejecutarConsulta($sql);      
    }

    public function buscar_chofer($search){
      $sql = "SELECT p.*, tp.nombre as tipo_persona FROM persona as p
      INNER JOIN tipo_persona as tp on tp.idtipo_persona = p.idtipo_persona      
      WHERE (p.nombre_razonsocial like '%$search%' OR p.nombre_razonsocial like '%$search%' OR p.numero_documento like '%$search%' ) 
      AND p.estado = '1' AND p.estado_delete = '1' and tipo_documento = '1' limit 9;";
      return ejecutarConsultaArray($sql);      
    }

    // ::::::::::::::::: L I S T A   D E :   S E L E C T  2   :::::::::::::::::
    public function select2_modalidad_transporte(){
      $sql = "SELECT * from sunat_c18_codigo_modalidad_transporte";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_motivo_traslado(){
      $sql = "SELECT * from sunat_c20_codigo_motivo_traslado";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_chofer_publico(){
      $sql = "SELECT p.idpersona, p.nombre_razonsocial AS nombre, p.apellidos_nombrecomercial AS apellido, p.numero_documento, tp.nombre as tipo_persona,
      p.numero_licencia, placa_vehiculo
      FROM persona as p
      INNER JOIN tipo_persona as tp on tp.idtipo_persona = p.idtipo_persona 
      WHERE p.idtipo_persona in ( 3,4,5) AND p.numero_documento is not null AND p.tipo_documento in ( '6') AND p.estado_delete = 1 AND p.idpersona > 2 
      ORDER BY p.idtipo_persona desc, tp.nombre DESC, p.nombre_razonsocial ASC";
      return ejecutarConsultaArray($sql);     
    }

    function listar_cliente(){
      $sql = "SELECT vw_c.*
      FROM vw_cliente_all as vw_c     
      WHERE  vw_c.estado_pc = 1 AND vw_c.estado_delete_pc = 1 AND vw_c.idpersona > 1
      ORDER BY vw_c.cliente_nombre_completo  ASC";
      return ejecutarConsultaArray($sql);
    }

  }

?>