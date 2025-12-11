<?php

  require "../config/Conexion_v2.php";

  class Orden_venta_cobrar
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

    //Listar registros
    public function listarOrdenesCobrar($fecha_i,$fecha_f){

      $filtro_fecha = "";

      if ( !empty($fecha_i) && !empty($fecha_f) ) { $filtro_fecha = "AND fecha_emision_format BETWEEN '$fecha_i' AND '$fecha_f'"; } 
      else if (!empty($fecha_i)) { $filtro_fecha = "AND fecha_emision_format = '$fecha_i'"; }
      else if (!empty($fecha_f)) { $filtro_fecha = "AND fecha_emision_format = '$fecha_f'"; }

      $sql="SELECT idventa,tipo_comprobante,serie_y_numero_comprobante,fecha_emision,impuesto,venta_subtotal,
            venta_descuento,venta_igv,venta_total,idpersona_cliente,idpersona,tipo_persona_sunat,nombre_razonsocial,
            apellidos_nombrecomercial,tipo_documento,numero_documento,user_en_atencion_nombre,user_en_atencion_foto
          FROM vw_facturacion WHERE tipo_comprobante ='103' and iddocumento_relacionado = '0'  AND estado_v = 1 AND estado_delete_v = 1 $filtro_fecha ORDER BY idventa DESC;";

      $ordenes = ejecutarConsultaArray($sql);   if ( $ordenes['status'] == false) {return $ordenes; }

      foreach ( $ordenes['data'] as &$detalle) {
        $sql = "SELECT idventa_detalle, pr_nombre, pr_marca, pr_categoria, cantidad_presentacion, cantidad_total, cantidad_venta, subtotal 
        FROM venta_detalle WHERE idventa='".$detalle['idventa']."' ORDER BY idventa_detalle DESC ;";
        $detalleOrden = ejecutarConsultaArray($sql);  

        $detalle['detalleOrden'] = $detalleOrden['data'];    
      }
      return $ordenes;

    }

    public function insertar($idventa,$idsunat_c01,$tipo_comprobante,$serie_comprobante,$idpersona_cliente,$venta_total,$metodo_pago,$total_recibido,$total_vuelto,
    $mp_serie_comprobante, $file_nombre_new, $file_nombre_old, $file_size){  
      
      $tipo_v = ""; $cot_estado = ""; $fecha_actual_amd = date('Y-m-d');

      if ($tipo_comprobante == '100') {
          $tipo_v = "COTIZACIÓN";
          $cot_estado = "PENDIENTE"; $nc_motivo_anulacion = '';
        }else if ($tipo_comprobante == '12') {
          $tipo_v = "TICKET"; $nc_motivo_anulacion = '';
        }else if ($tipo_comprobante == '07') {
          $tipo_v = "NOTA DE CRÉDITO";         
          $metodo_pago= []; $total_recibido= [];  $total_vuelto= ''; $mp_serie_comprobante = [];$file_nombre_new = [];
          $usar_anticipo= "NO"; $ua_monto_disponible= ""; $ua_monto_usado= "";        
        }else if ($tipo_comprobante == '03') {
          $tipo_v = "BOLETA"; $nc_motivo_anulacion = '';
        }else if ($tipo_comprobante == '01') {
          $tipo_v = "FACTURA"; $nc_motivo_anulacion = '';
      }

      // Validamos datos del Cliente
      if ($tipo_comprobante == '03' || $tipo_comprobante == '01' ) {
        $sql = "SELECT cliente_nombre_completo, tipo_documento, numero_documento from vw_cliente_all where idpersona_cliente = '$idpersona_cliente'";
        $val_cliente = ejecutarConsultaSimpleFila($sql);
        if ( empty($val_cliente['data']) ) {
          return array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'Cliente no existe!!', 'message' => 'El cliente selecionado no es valido', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );          
        }else{
          if ( empty($val_cliente['data']['numero_documento']) ) {
            return array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'No tiene Numero Documento!!', 'message' => 'Este cliente: ' . $val_cliente['data']['cliente_nombre_completo'] . '------'.$val_cliente['data']['numero_documento'].' no cuenta con un Numero de Documento valido para generar BOLETA o FACTURA, porfavor actualice los datos' , 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );          
          }
        }
      }

      // BUSCAMOS UNA CAJA ABIERTA
      $sql_0 = "SELECT * FROM caja WHERE estado_caja = 'ABIERTO';";
      $caja = ejecutarConsultaSimpleFila($sql_0); if ( $caja['status'] == false) {return $caja; }

      if( empty($caja['data']) ) {      
        $falta_caja = '<li><div class="text-start">Por favor, aperture una nueva <b>Caja</b> antes de agregar un registro</div><div class="text-start mt-2">Módulo: <a target="_blank" href="caja.php">Caja</a></div></li>';
        return array( 'status' => 'no_caja', 'message' => 'caja cerrada', 'data' => '<ul>'.$falta_caja.'</ul>', 'id_tabla' => '' );
      }
      $idcaja = $caja['data']['idcaja'];

      // BUSCAMOS EL ERROR ANTERIOR
      $sql = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante, 
      DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_format 
      FROM venta AS v 
      WHERE v.tipo_comprobante = '$tipo_comprobante' and ((v.sunat_error <> '' AND  v.sunat_error is not null  ) or (v.sunat_observacion <> '' AND  v.sunat_observacion is not null  ));";
      $buscando_error = ejecutarConsultaArray($sql); if ($buscando_error['status'] == false) {return $buscando_error; }

      if ( !empty( $buscando_error['data'] ) ) {
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

      // VALIDAMOS EL PERIODO CONTABLE
      $sql_periodo = "SELECT idperiodo_contable FROM periodo_contable WHERE estado = '1' AND estado_delete = '1' AND '$fecha_actual_amd' BETWEEN fecha_inicio AND fecha_fin;";
      $buscando_periodo = ejecutarConsultaSimpleFila($sql_periodo); if ($buscando_periodo['status'] == false) {return $buscando_periodo; }
      $idperiodo_contable = empty($buscando_periodo['data']) ? '' : (empty($buscando_periodo['data']['idperiodo_contable']) ? '' : $buscando_periodo['data']['idperiodo_contable'] ) ;
      // return $sql_periodo;
      if ( empty($idperiodo_contable) ) {  
        $retorno = array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'No existe periodo!!', 'message' => ' No cierre el módulo. <br> No existe un periodo contable del mes: <b>'. nombre_mes(date('Y-m-d')).'-'.date('Y'). '</b>. '. ($_SESSION['user_cargo'] == 'ADMINISTRADOR' ? 'Configure el período contable en el módulo siguiente: <a href="periodo_facturacion.php" target="_blank" >Periodo contable</a>' : 'Solicite a su administrador que configure el período contable para el mes actual.'), 'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_error['data'], 'id_tabla' => '' );
        return $retorno;
      }
      
      // INSERTAMOS: VENTA
      $sql_1 = "INSERT INTO venta(idpersona_cliente, idcaja, idperiodo_contable, iddocumento_relacionado, crear_enviar_sunat, idsunat_c01, tipo_comprobante, serie_comprobante,  
      impuesto, venta_subtotal, venta_descuento, venta_igv, venta_total, venta_cuotas, usar_anticipo, observacion_documento)
      SELECT '$idpersona_cliente','$idcaja', '$idperiodo_contable', '$idventa', 'NO', '$idsunat_c01', '$tipo_comprobante', '$serie_comprobante',
          impuesto, venta_subtotal, venta_descuento, venta_igv, venta_total, 'NO', 'NO', observacion_documento
      FROM venta WHERE idventa = '$idventa';";
      $newdata = ejecutarConsulta_retornarID($sql_1, 'C', "Creando registro $tipo_v -- Orden Venta Cobrar"); if ($newdata['status'] == false) { return  $newdata;}

      $id = $newdata['data'];

      $sql_updaterelac = "UPDATE venta SET iddocumento_relacionado='$id'  WHERE  idventa = '$idventa';";
      $updaterelac = ejecutarConsulta($sql_updaterelac, 'U', "Actualizando documento relacionado -- Orden Venta Cobrar"); if ($updaterelac['status'] == false) { return  $updaterelac;}

      
      $i = 0;
      $detalle_new = "";
      $monto_recibido = 0;  


    
      if ( !empty($newdata['data']) ) {  

        $sql_detalle_orden ="SELECT 
        idventa_detalle,idventa,idproducto_presentacion,pr_nombre,pr_marca,pr_categoria,v_tipo_comprobante,v_fecha_emision,cantidad_presentacion,cantidad_venta,cantidad_total,
        precio_compra,precio_venta,precio_venta_descuento,descuento,descuento_porcentaje,subtotal,subtotal_compra,subtotal_no_descuento,um_nombre,um_abreviatura,precio_por_mayor
        FROM  venta_detalle WHERE idventa = '$idventa';";
 
        $detalle_orden =  ejecutarConsulta($sql_detalle_orden); if ($detalle_orden['status'] == false) { return  $detalle_orden;} 

        foreach ($detalle_orden['data'] as $row) {
          $sql_2 = "INSERT INTO venta_detalle(
            idventa, idproducto_presentacion, pr_nombre, pr_marca, pr_categoria, v_tipo_comprobante, cantidad_presentacion,
            cantidad_venta, cantidad_total, precio_compra, precio_venta, precio_venta_descuento, descuento, descuento_porcentaje,
            subtotal, subtotal_compra, subtotal_no_descuento, um_nombre, um_abreviatura, precio_por_mayor
          ) VALUES (
            '$id', '{$row['idproducto_presentacion']}', '{$row['pr_nombre']}', '{$row['pr_marca']}', '{$row['pr_categoria']}', '$tipo_v',
            '{$row['cantidad_presentacion']}', '{$row['cantidad_venta']}', '{$row['cantidad_total']}', '{$row['precio_compra']}', '{$row['precio_venta']}',
            '{$row['precio_venta_descuento']}', '{$row['descuento']}', '{$row['descuento_porcentaje']}', '{$row['subtotal']}', '{$row['subtotal_compra']}', '{$row['subtotal_no_descuento']}',
            '{$row['um_nombre']}', '{$row['um_abreviatura']}', '{$row['precio_por_mayor']}'
          );";
          $detalle_new = ejecutarConsulta_retornarID($sql_2, 'C');   if ($detalle_new['status'] == false) { return $detalle_new; }
          
          $id_d = $detalle_new['data'];
        }
      }

      if (isset($metodo_pago) && is_array($metodo_pago)) {  } else {
        $metodo_pago = [];
      }

      if (!empty($metodo_pago)) {
        foreach ($metodo_pago as $key => $val) {
          // Asegúrate de que todos los arrays tengan los mismos índices
          $monto_recibido += isset($total_recibido[$key]) ? floatval($total_recibido[$key]) : 0;
          $voucher = isset($mp_serie_comprobante[$key]) ? $mp_serie_comprobante[$key] : '';
          $size = isset($file_size[$key]) ? $file_size[$key] : 0;
          $nombre = isset($file_nombre_old[$key]) ? $file_nombre_old[$key] : '';

          $sql_3 = "INSERT INTO venta_metodo_pago(idventa, metodo_pago, monto, codigo_voucher, comprobante, comprobante_size_bytes, comprobante_nombre_original)
          VALUES ('$id', '$val', '$total_recibido[$key]', '$voucher', '', '$size', '$nombre');";
          $comprobante_new = ejecutarConsulta_retornarID($sql_3, 'C');  if ($comprobante_new['status'] == false) { return $comprobante_new;  }
        }
      }      
      
      // Actualizamos: total recibido y vuelto
      $monto_vuelto = $monto_recibido - $venta_total;
      $sql_4 = "UPDATE venta SET vc_estado  = 'pagado', vc_cantidad_total = '0', total_recibido = '$monto_recibido', total_vuelto = '$monto_vuelto' WHERE idventa = '$id';";
      $actulizando_vuelto = ejecutarConsulta($sql_4, 'U', 'Actualizando monto recibido y el vuelto -- Orden de Venta Cobrar'); if ($actulizando_vuelto['status'] == false) { return  $actulizando_vuelto;} 

      return $newdata;         
      
    }        


    public function actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error) {
      //echo json_encode( [$idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error]); die();
      $sql_1 = "UPDATE venta SET sunat_estado='$sunat_estado',sunat_observacion='$sunat_observacion',sunat_code='$sunat_code',
      sunat_hash='$sunat_hash',sunat_mensaje='$sunat_mensaje', sunat_error = '$sunat_error' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql_1, 'U', 'Actualizando estado de SUNAT -- Orden de Venta Cobrar');
    } 

    public function anularOrden($idventa) {

      // Paso 1: Obtener presentaciones
      $sql = "SELECT idproducto_presentacion FROM venta_detalle WHERE idventa='$idventa'";
      $devolverSt = ejecutarConsulta($sql); 
      if ($devolverSt['status'] == false) { return $devolverSt; } 

      $lista = [];
      while ($fila = $devolverSt['data']->fetch_assoc()) {
        $lista[] = $fila;
      }

      // Paso 2: Devolver stock
      foreach ($lista as $fila) {
        $idprod = $fila['idproducto_presentacion'];

        $sql_2_1 = "UPDATE producto_sucursal 
          SET stock = stock + (
            SELECT cantidad_total 
            FROM venta_detalle 
            WHERE idproducto_presentacion = '$idprod' AND idventa = '$idventa'
          ) 
          WHERE idproducto_sucursal = (
            SELECT DISTINCT idproducto_sucursal 
            FROM producto_presentacion 
            WHERE idproducto_presentacion = '$idprod'
          );
        ";

        $actualizar_stock = ejecutarConsulta($sql_2_1, 'U', 'Actualizando Estock -- Orden de Venta Cobrar'); if ($actualizar_stock['status'] == false) { return $actualizar_stock; } 
      }

      // Paso 3: Anular la venta
      $sql = "UPDATE venta SET estado = '0', estado_delete = '0' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql, 'U', 'Anulando orden de venta -- Orden de Venta Cobrar');
    }
    
    public function select2_series_comprobante($codigo){

      $sql = "SELECT stp.abreviatura,  stp.serie
      FROM sunat_usuario_comprobante as suc
      INNER JOIN sunat_c01_tipo_comprobante as stp ON stp.idtipo_comprobante = suc.idtipo_comprobante
      WHERE stp.codigo = '$codigo' AND suc.idusuario = '$this->id_usr_sesion';";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_banco(){
     
      $sql="SELECT * FROM bancos WHERE idbancos <> 1 and estado = '1' AND estado_delete = '1';";
      return ejecutarConsultaArray($sql);
    }


    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // ═══════                                         N U E V O   C L I E N T E                                                                 ═══════
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

    public function agregar_nuevo_cliente( $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
      $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo ){

      if ( empty($cli_centro_poblado) ) { $cli_centro_poblado = 1;}

      $sql_0 = "SELECT p.*  FROM vw_persona_all as p WHERE p.numero_documento = '$cli_numero_documento' AND '$cli_numero_documento' <> '00000000'";
      $buscando = ejecutarConsultaArray($sql_0);		

      if ( empty($buscando['data']) || $cli_tipo_documento == '0' ) {

        $sql = "INSERT INTO persona( idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, apellidos_nombrecomercial, tipo_documento, numero_documento, fecha_nacimiento, celular, direccion, 
        direccion_referencia, departamento, provincia, distrito, cod_ubigeo, correo) 
        VALUES ('$cli_idtipo_persona', '1', '1','$cli_tipo_persona_sunat','$cli_nombre_razonsocial', '$cli_apellidos_nombrecomercial','$cli_tipo_documento','$cli_numero_documento', null, '$cli_celular', '$cli_direccion', '$cli_direccion_referencia',
        '$cli_departamento', '$cli_provincia', '$cli_distrito', '$cli_ubigeo', '$cli_correo' )";
        $new_persona = ejecutarConsulta_retornarID($sql, 'C');if ($new_persona['status'] == false) {return $new_persona;}

        $id = $new_persona['data'];
       
        $sql2 = "INSERT INTO persona_cliente(idpersona,idcentro_poblado, nota) 

        VALUES ('$id','$cli_centro_poblado', '')";
        $new_cliente =  ejecutarConsulta_retornarID($sql2, 'C');	if ($new_cliente['status'] == false) {	return $new_cliente;	}

        return $new_cliente;      
      } else {
        $info_repetida = ''; 

        foreach ($buscando['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['persona_nombre_completo'].'</span><br>
            <b>'.$value['tipo_documento_abrev_nombre'].': </b>'.$value['numero_documento'].'<br>
            <b>Distrito: </b>'.$value['distrito'].'<br>
            <b>Tipo: </b>'.$value['tipo_persona'].'<br>
            <b>Cargo: </b>'.$value['cargo_trabajador'].'<br>
            <b>Papelera: </b>'.( $value['estado_pc']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete_pc']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }			
    }

    public function editar_nuevo_cliente($cli_idpersona, $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
          $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo){
      $sql = "";
      return ejecutarConsulta($sql, 'U');      
    }

        // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
    public function select2_cliente(){
      $filtro_id_trabajador  = '';
     
      $sql = "SELECT pc.*, LPAD(pc.idpersona_cliente, 5, '0') as idcliente, 
       pc.idpersona_cliente, p.idpersona,  p.nombre_razonsocial, p.apellidos_nombrecomercial,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo,  
      sc06.abreviatura as nombre_tipo_documento, IFNULL(p.tipo_documento, '') as tipo_documento, IFNULL(p.numero_documento,'') as numero_documento, IFNULL(p.direccion,'') as direccion      
      FROM persona_cliente as pc      
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sc06 ON sc06.code_sunat = p.tipo_documento
      WHERE p.estado = '1' and p.estado_delete = '1' and pc.estado = '1' and pc.estado_delete = '1' and p.idpersona > 2  ORDER BY p.nombre_razonsocial ASC;"; 
      return ejecutarConsultaArray($sql);
    }






  }

  




?>