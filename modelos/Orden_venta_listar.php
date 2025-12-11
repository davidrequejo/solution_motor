<?php

  require "../config/Conexion_v2.php";

  class Orden_venta_listar
  {

    //Implementamos nuestro constructor
    public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
    // public $id_empresa_sesion;   
    public function __construct( )
    {
      $this->id_usr_sesion        =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      $this->id_persona_sesion    = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
      $this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    public function listar_tabla_facturacion( $fecha_i, $fecha_f, $cliente, $tipo_persona, $comprobante, $metodo_pago, $centro_poblado, $estado_sunat ) {    

      $filtro_id_trabajador  = ''; $filtro_id_punto ='';
      $filtro_fecha = ""; $filtro_cliente = ""; $filtro_tipo_persona = ""; $filtro_comprobante = ""; $filtro_metodo_pago = ""; $filtro_centro_poblado = ""; $filtro_estado_sunat = "";

      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {  $filtro_id_trabajador = "AND vw_f.idpersona_trabajador = '$this->id_trabajador_sesion'";    } 
      if ($_SESSION['user_cargo'] == 'PUNTO DE COBRO') { $filtro_id_punto = "AND (vw_f.user_created_v = '$this->id_usr_sesion' OR vw_f.idpersona_trabajador = '$this->id_trabajador_sesion')";  } 

      if ( !empty($fecha_i) && !empty($fecha_f) ) { $filtro_fecha = "AND vw_f.fecha_emision_format BETWEEN '$fecha_i' AND '$fecha_f'"; } 
      else if (!empty($fecha_i)) { $filtro_fecha = "AND vw_f.fecha_emision_format = '$fecha_i'"; }
      else if (!empty($fecha_f)) { $filtro_fecha = "AND vw_f.fecha_emision_format = '$fecha_f'"; }
      
      if ( empty($cliente) ) { } else {  $filtro_cliente = "AND vw_f.idpersona_cliente = '$cliente'"; } 
      if ( empty($tipo_persona) ) { } else {  $filtro_tipo_persona = "AND vw_f.tipo_persona_sunat = '$tipo_persona'"; } 
      if ( empty($comprobante) ) { } else {  $filtro_comprobante = "AND vw_f.idsunat_c01 = '$comprobante'"; } 
      if ( empty($metodo_pago) ) { } else { $filtro_metodo_pago = "AND vw_f.metodos_pago_agrupado like '%$metodo_pago%'"; }
      if ( empty($estado_sunat) ) { } else if ( $estado_sunat == 'NO ENVIADO') { $filtro_estado_sunat = "AND ( vw_f.sunat_estado is null or vw_f.sunat_estado = '' or vw_f.sunat_estado IN ('RECHAZADA') )"; } 
      else {  $filtro_estado_sunat = "AND vw_f.sunat_estado = '$estado_sunat'"; } 
      if ( empty($centro_poblado) ) { } else {  $filtro_centro_poblado = "AND vw_f.idcentro_poblado = '$centro_poblado'"; } 

      $sql = "SELECT vw_f.*, 
      case when v.serie_comprobante is null then null else concat(v.serie_comprobante,'-',v.numero_comprobante) end as comprobante_relacionado, 
      case when v.serie_comprobante is null then 'pendiente' else 'pagado' end as comprobante_relacionado_estado
      FROM vw_facturacion AS vw_f     
      left join venta as v on v.idventa = vw_f.iddocumento_relacionado
      WHERE vw_f.estado_v = 1 AND vw_f.estado_delete_v = 1 AND vw_f.tipo_comprobante in ('103') $filtro_id_trabajador $filtro_id_punto $filtro_cliente $filtro_tipo_persona $filtro_comprobante $filtro_metodo_pago $filtro_centro_poblado $filtro_estado_sunat $filtro_fecha      
      ORDER BY vw_f.fecha_emision DESC, vw_f.nombre_razonsocial ASC;"; #return $sql;
      $venta = ejecutarConsulta($sql); if ($venta['status'] == false) {return $venta; }

      return $venta;
    }  

    public function actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error) {
      //echo json_encode( [$idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error]); die();
      $sql_1 = "UPDATE venta SET sunat_estado='$sunat_estado',sunat_observacion='$sunat_observacion',sunat_code='$sunat_code',
      sunat_hash='$sunat_hash',sunat_mensaje='$sunat_mensaje', sunat_error = '$sunat_error' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql_1, 'U', 'Actualizando estado de SUNAT -- Orden de venta Listar');
    } 

    public function crear_bitacora_reenvio_sunat( $idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error) {
      $sql_1 = "INSERT INTO  bitacora_reenvio_sunat (  idventa, observacion_de_ejecucion, sunat_estado, sunat_observacion, sunat_code, sunat_mensaje, sunat_hash, sunat_error  )
      values ( $idventa, '$observacion_ejecucion', '$sunat_estado' , '$sunat_observacion', '$sunat_code', '$sunat_mensaje', '$sunat_hash',  '$sunat_error' );";
      return ejecutarConsulta($sql_1, 'C');
    } 

    public function actualizar_doc_anulado_x_nota_credito( $idventa) {
      
      $sql_1 = "UPDATE venta SET sunat_estado='ANULADO' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql_1, 'U', 'Actualizando estado anulado -- Orden de venta Listar');     

    } 

    public function mostrar_venta($id){
      $sql = "SELECT * FROM venta WHERE idventa = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function comprobantes_no_enviado_a_sunat(){
      $sql = "SELECT  * FROM venta where tipo_comprobante in ('01', '03', '07') and ( sunat_estado is null or sunat_estado = '' or sunat_estado IN ('RECHAZADA', 'POR ENVIAR', 'NULL', 'null' ))";
      return ejecutarConsultaArray($sql);
    }      

    public function mostrar_metodo_pago($id){
      $sql = "SELECT vmp.*,
      CASE 
          WHEN vmp.metodo_pago = 'EFECTIVO' THEN 'icono-efectivo.jpg'
          WHEN vmp.comprobante IS NULL OR vmp.comprobante = '' THEN 'img_mp.png'
          ELSE vmp.comprobante
      END AS comprobante_v2
      FROM venta_metodo_pago AS vmp WHERE vmp.idventa = '$id'";
      return ejecutarConsultaArray($sql);
    }

    public function mostrar_cliente($id){
      $sql = "SELECT p.*, 
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo,  pc.idcentro_poblado, pc.fecha_afiliacion, pc.ip_personal
      FROM persona_cliente as pc
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      WHERE pc.idpersona_cliente = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrar_detalle_venta($idventa){

      $sql_1 = "SELECT * FROM vw_facturacion AS vw_f WHERE vw_f.idventa = '$idventa'";
      $venta = ejecutarConsultaSimpleFila($sql_1); if ($venta['status'] == false) {return $venta; }

      $sql_2 = "SELECT vw_fd.*
      FROM vw_facturacion_detalle AS vw_fd      
      WHERE vw_fd.idventa = '$idventa' order by vw_fd.idventa_detalle asc;";
      $detalle = ejecutarConsultaArray($sql_2); if ($detalle['status'] == false) {return $detalle; }

      $sql_3 = "SELECT vmp.*,
      CASE 
          WHEN vmp.metodo_pago = 'EFECTIVO' THEN 'icono-efectivo.jpg'
          WHEN vmp.comprobante IS NULL OR vmp.comprobante = '' THEN 'img_mp.png'
          ELSE vmp.comprobante
      END AS comprobante_v2
      FROM venta_metodo_pago AS vmp WHERE vmp.idventa = '$idventa' order by vmp.idventa_metodo_pago asc;";
      $vmp = ejecutarConsultaArray($sql_3); if ($vmp['status'] == false) {return $vmp; }

      $sql_4 = "SELECT DATE_FORMAT(vc.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento_format, vc.* from venta_cuotas as vc where vc.idventa = '$idventa' and vc.estado = '1' and vc.estado_delete = '1'  ORDER BY vc.numero_cuota ASC ;";
      $vc = ejecutarConsultaArray($sql_4); if ($vc['status'] == false) {return $vc; }

      return $datos = [
        'status' => true, 'message' => 'Todo ok', 
          'data' => ['venta' => $venta['data'], 'detalle' => $detalle['data'], 'metodo_pago' => $vmp['data'],  'venta_cuotas' => $vc['data'],]
      ];

    }

    public function eliminar($id){

      $sql_1 = "SELECT CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,  DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, v.* 
      from venta as v INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01 where v.vc_estado in ('pagado', 'parcial') and venta_cuotas = 'SI' and  v.idventa = '$id' ;";
      $busca_estado = ejecutarConsultaSimpleFila($sql_1); if ($busca_estado['status'] == false) {return $busca_estado; }   
      
      $sql_1 = "SELECT  DATE_FORMAT(vc.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento_v2, vc.* from venta_cuotas as vc where vc.idventa = '$id'  and vc.estado = '1' and vc.estado_delete = '1' order by vc.numero_cuota asc;";
      $busca_cuota = ejecutarConsultaArray($sql_1); if ($busca_cuota['status'] == false) {return $busca_cuota; }   

      if ( empty($busca_estado['data']) ) {
        $sql = "UPDATE venta SET sunat_estado = 'ANULADO', estado_delete = '0' WHERE idventa = '$id'";
        return ejecutarConsulta($sql, 'D');
      }else{
        $html_tbl_body ='';
        foreach ($busca_cuota['data'] as $key => $val) {
          $html_tbl_body .= '<tr>
            <th class="py-1">'.$val['numero_cuota'].'</th>
            <td class="py-1"><span class="badge bg-light text-dark">'.$val['fecha_vencimiento_v2'].'</span> </td>
            <td class="py-1">'.$val['monto_cuota'].'</td>
            <td class="py-1"><span class="badge '.($val['estado_cuota'] == 'pendiente' ? 'bg-danger-transparent' : 'bg-success-transparent').' ">'.$val['estado_cuota'].'</span></td>
          </tr>';
        }
        $html_titulo = 'El doc: <span class="badge bg-outline-secondary custom-badge fs-12 d-inline-flex align-items-center">'.$busca_estado['data']['tp_comprobante_v2'] . ' '. $busca_estado['data']['serie_comprobante'] .'-'. $busca_estado['data']['numero_comprobante'] . '</span> tiene cuotas pagadas.' ;
        
        $html_message = ' lista de las cuotas pendientes <div class="table-responsive text-center pt-1">
          <center>
            <table class="table text-nowrap table-bordered border-success w-300px" >
              <thead><tr><th class="py-1">#</th><th class="py-1">Fecha</th><th class="py-1">Monto</th><th class="py-1">Estado</th></tr></thead>
              <tbody>'.$html_tbl_body.'</tbody>
            </table>
          </center>
        </div> si desea eliminar o enviar a papelera elimine los pagos primero.' ;

        return $datos = [
          'status' => 'error_personalizado', 'message' => $html_message, 'titulo' => $html_titulo ,  'user' => $_SESSION['user_nombre'], 
          'data' => []
        ];
      }
     
    }

    public function papelera($id){

      $sql_1 = "SELECT CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,  DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, v.* 
      from venta as v INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01 where v.vc_estado in ('pagado', 'parcial') and venta_cuotas = 'SI' and  v.idventa = '$id' ;";
      $busca_estado = ejecutarConsultaSimpleFila($sql_1); if ($busca_estado['status'] == false) {return $busca_estado; }   
      
      $sql_1 = "SELECT  DATE_FORMAT(vc.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento_v2, vc.* from venta_cuotas as vc where vc.idventa = '$id'  and vc.estado = '1' and vc.estado_delete = '1' order by vc.numero_cuota asc;";
      $busca_cuota = ejecutarConsultaArray($sql_1); if ($busca_cuota['status'] == false) {return $busca_cuota; }   

      if ( empty($busca_estado['data']) ) {
        $sql = "UPDATE venta SET sunat_estado = 'ANULADO', estado = '0'  WHERE idventa = '$id'";
        return ejecutarConsulta($sql, 'T');
      }else{
        $html_tbl_body ='';
        foreach ($busca_cuota['data'] as $key => $val) {
          $html_tbl_body .= '<tr>
            <th class="py-1">'.$val['numero_cuota'].'</th>
            <td class="py-1"><span class="badge bg-light text-dark">'.$val['fecha_vencimiento_v2'].'</span> </td>
            <td class="py-1">'.$val['monto_cuota'].'</td>
            <td class="py-1"><span class="badge '.($val['estado_cuota'] == 'pendiente' ? 'bg-danger-transparent' : 'bg-success-transparent').' ">'.$val['estado_cuota'].'</span></td>
          </tr>';
        }
        $html_titulo = 'El doc: <span class="badge bg-outline-secondary custom-badge fs-12 d-inline-flex align-items-center">'.$busca_estado['data']['tp_comprobante_v2'] . ' '. $busca_estado['data']['serie_comprobante'] .'-'. $busca_estado['data']['numero_comprobante'] . '</span> tiene cuotas pagadas.' ;
        
        $html_message = ' lista de las cuotas <div class="table-responsive text-center pt-1">
          <center>
            <table class="table text-nowrap table-bordered border-success w-300px" >
              <thead><tr><th class="py-1">#</th><th class="py-1">Fecha</th><th class="py-1">Monto</th><th class="py-1">Estado</th></tr></thead>
              <tbody>'.$html_tbl_body.'</tbody>
            </table>
          </center>
        </div> si desea eliminar o enviar a papelera elimine los pagos primero.' ;

        return $datos = [
          'status' => 'error_personalizado', 'message' => $html_message, 'titulo' => $html_titulo ,  'user' => $_SESSION['user_nombre'], 
          'data' => []
        ];
      }      
    }  
    
    public function actualizar_estado_credito($id){
      $sql = "UPDATE venta v
        SET vc_estado = (
          CASE 
            -- Si NO existen cuotas 'pendiente' ni 'vencido', significa que todas están pagadas
            WHEN NOT EXISTS (SELECT 1 FROM venta_cuotas c WHERE c.idventa = v.idventa AND c.estado = '1' and c.estado_delete = '1' AND c.estado_cuota IN ('pendiente', 'vencido')) 
              THEN 'pagado'
            -- Si hay al menos una cuota 'pagado' y al menos una 'pendiente' o 'vencido', es parcial
            WHEN EXISTS (SELECT 1 FROM venta_cuotas c WHERE c.idventa = v.idventa AND c.estado = '1' and c.estado_delete = '1' AND c.estado_cuota = 'pagado') 
              AND EXISTS (SELECT 1 FROM venta_cuotas c WHERE c.idventa = v.idventa AND c.estado = '1' and c.estado_delete = '1' AND c.estado_cuota IN ('pendiente', 'vencido')) 
              THEN 'parcial'
            -- Si todas las cuotas están en 'pendiente' o 'vencido', es pendiente
            ELSE 'pendiente'
          END
        ), 
        -- Cantidad de cuotas acivas
        vc_cantidad_total = ifnull((SELECT count(*) as cantidad FROM venta_cuotas as c WHERE c.idventa = v.idventa AND c.estado = '1' and c.estado_delete = '1' ), 0),
        -- Cantidad de cuotas acivas y pagadas
        vc_cantidad_pagada = ifnull((SELECT count(*) as cantidad FROM venta_cuotas as c WHERE c.idventa = v.idventa AND estado_cuota = 'pagado' AND c.estado = '1' and c.estado_delete = '1' ), 0)
        WHERE v.idventa = '$id' ;";
      return ejecutarConsulta($sql, 'U', 'Actualizando estado de Cuota -- Orden de venta Listar');
    }

    public function actualizar_estado_cuota($idventa_cuotas, $id_venta ){

      // 1. Consultar si hay pagos válidos
      $sql_check_pago = "SELECT 1 FROM venta_cuota_pago as vcp 
      INNER JOIN venta as v on v.idventa = vcp.idventa 
      WHERE vcp.idventa_cuotas = '$idventa_cuotas' AND vcp.estado = '1' and vcp.estado_delete = '1' and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado = 'ACEPTADA'";
      $existe_pago = ejecutarConsultaSimpleFila($sql_check_pago);
      $estado_cuota = empty($existe_pago['data']) ? 'pendiente' : 'pagado';

      $sql = "UPDATE venta_cuotas SET estado_cuota = '$estado_cuota'  WHERE idventa_cuotas = '$idventa_cuotas' ;";
      $estado = ejecutarConsulta($sql, 'U', 'Actualizando estado de cuota -- Orden de venta Listar'); if ($estado['status'] == false) {return $estado; } 
      $this->actualizar_estado_credito($id_venta);
      return $estado;
    }

    public function listar_tabla_producto($tipo_producto){
      $sql = "SELECT p.*
      FROM vw_producto_presentacion as p
      WHERE p.tipo_producto = '$tipo_producto' AND p.pro_estado = 1 AND p.pro_estado_delete = 1 ORDER BY p.nombre_producto_presentacion;";
      return ejecutarConsulta($sql);
    }    

    Public function mini_reporte($periodo_facturacion){

      $meses_espanol = array( 1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic" );

      $filtro_id_trabajador  = ''; $filtro_id_user  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') { $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";  } 
      if ($_SESSION['user_cargo'] == 'PUNTO DE COBRO') { $filtro_id_user = "AND (v.user_created = '$this->id_usr_sesion' OR pc.idpersona_trabajador = '$this->id_trabajador_sesion')";  } 

      $sql_00 ="SELECT v.tipo_comprobante, COUNT( v.idventa ) as cantidad
      FROM venta as v
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable and pco.periodo = '$periodo_facturacion'
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado = '1' AND v.estado_delete = '1' $filtro_id_trabajador $filtro_id_user
      GROUP BY v.tipo_comprobante;";
      $coun_comprobante = ejecutarConsultaArray($sql_00); if ($coun_comprobante['status'] == false) {return $coun_comprobante; }

      $sql_01 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE()) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '01' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '01' ) AS ventas_mes_anterior;";
      $factura_p = ejecutarConsultaSimpleFila($sql_01); if ($factura_p['status'] == false) {return $factura_p; }
      $sql_01 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total 
      FROM venta as v 
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable and pco.periodo = '$periodo_facturacion'
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '01' AND v.estado = '1' AND v.estado_delete = '1' $filtro_id_trabajador $filtro_id_user;";
      $factura = ejecutarConsultaSimpleFila($sql_01); if ($factura['status'] == false) {return $factura; }

      $sql_03 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE()) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '03' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '03' ) AS ventas_mes_anterior;";
      $boleta_p = ejecutarConsultaSimpleFila($sql_03); if ($boleta_p['status'] == false) {return $boleta_p; }
      $sql_03 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total 
      FROM venta as v     
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable and pco.periodo = '$periodo_facturacion'  
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '03' AND v.estado = '1' AND v.estado_delete = '1' $filtro_id_trabajador $filtro_id_user;";
      $boleta = ejecutarConsultaSimpleFila($sql_03); if ($boleta['status'] == false) {return $boleta; }

      $sql_12 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE()) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '12' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '12' ) AS ventas_mes_anterior;";
      $ticket_p = ejecutarConsultaSimpleFila($sql_12); if ($ticket_p['status'] == false) {return $ticket_p; }
      $sql_12 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total 
      FROM venta as v 
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable and pco.periodo = '$periodo_facturacion'
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '12' AND v.estado = '1' AND v.estado_delete = '1' $filtro_id_trabajador $filtro_id_user;";
      $ticket = ejecutarConsultaSimpleFila($sql_12); if ($ticket['status'] == false) {return $ticket; }

      $mes_factura = []; $mes_nombre = []; $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $nro_mes = floatval( date("m", strtotime($fecha_actual)) );
        $sql_mes = "SELECT MONTHNAME(fecha_emision) AS fecha_emision , COALESCE(SUM(venta_total), 0) AS venta_total FROM venta WHERE MONTH(fecha_emision) = '$nro_mes' AND sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND tipo_comprobante = '01' AND estado = '1' AND estado_delete = '1';";
        $mes_f = ejecutarConsultaSimpleFila($sql_mes); if ($mes_f['status'] == false) {return $mes_f; }
        array_push($mes_factura, floatval($mes_f['data']['venta_total']) ); array_push($mes_nombre, $meses_espanol[$nro_mes] );
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }

      $mes_boleta = [];  $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $sql_mes = "SELECT MONTHNAME(fecha_emision) AS fecha_emision , COALESCE(SUM(venta_total), 0) AS venta_total FROM venta WHERE MONTH(fecha_emision) = '".date("m", strtotime($fecha_actual))."' AND sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND tipo_comprobante = '03' AND estado = '1' AND estado_delete = '1';";
        $mes_b = ejecutarConsultaSimpleFila($sql_mes); if ($mes_b['status'] == false) {return $mes_b; }
        array_push($mes_boleta, floatval($mes_b['data']['venta_total']) ); 
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }

      $mes_ticket = [];  $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $sql_mes = "SELECT MONTHNAME(fecha_emision) AS fecha_emision , COALESCE(SUM(venta_total), 0) AS venta_total FROM venta WHERE MONTH(fecha_emision) = '".date("m", strtotime($fecha_actual))."' AND sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND tipo_comprobante = '12' AND estado = '1' AND estado_delete = '1';";
        $mes_t = ejecutarConsultaSimpleFila($sql_mes); if ($mes_t['status'] == false) {return $mes_t; }
        array_push($mes_ticket, floatval($mes_t['data']['venta_total']) );
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }

      return ['status' => true, 'message' =>'todo okey', 
        'data'=>[
          'mes_nombre'        => $mes_nombre,
          'coun_comprobante'  => $coun_comprobante['data'],
          'factura'           => floatval($factura['data']['venta_total']), 'factura_p' => floatval($factura_p['data']['porcentaje']) , 'factura_line'  => $mes_factura ,
          'boleta'            => floatval($boleta['data']['venta_total']), 'boleta_p'   => floatval($boleta_p['data']['porcentaje']) , 'boleta_line'    => $mes_boleta ,
          'ticket'            => floatval($ticket['data']['venta_total']), 'ticket_p'   => floatval($ticket_p['data']['porcentaje']) , 'ticket_line'    => $mes_ticket ,
        ]
      ];

    }

    Public function mini_reporte_v2($periodo,  $trabajador){ 
      $filtro_periodo = ""; $filtro_trabajador_1 = ""; $filtro_trabajador_2 = "";    
      
      if ( empty($periodo) )    { } else { $filtro_periodo = "AND DATE_FORMAT( vd.v_fecha_emision, '%Y-%m') = '$periodo'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_1 = "WHERE pc.idpersona_trabajador = '$trabajador'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_2 = "AND pc.idpersona_trabajador = '$trabajador'"; } 

      $sql = "SELECT pco.idcentro_poblado, pco.centro_poblado, ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
       COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 
      (SELECT cp.idcentro_poblado, cp.nombre as centro_poblado, COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      GROUP BY cp.idcentro_poblado
      order by COUNT(pc.idpersona_cliente) DESC) AS pco 

      LEFT JOIN

      (SELECT cp.idcentro_poblado, cp.nombre as centro_poblado, COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) 
      $filtro_periodo 
      GROUP BY cp.idcentro_poblado
      order by COUNT(v.idventa) DESC) as co ON pco.idcentro_poblado = co.idcentro_poblado
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $centro_poblado = ejecutarConsultaArray($sql); if ($centro_poblado['status'] == false) {return $centro_poblado; }

      $sql = "SELECT ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
      COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 

      (SELECT COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado      
      ) AS pco 

      LEFT JOIN

      (SELECT  COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) $filtro_periodo 
      ) as co ON 1 = 1
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $total = ejecutarConsultaSimpleFila($sql); if ($total['status'] == false) {return $total; }

      return ['status' => true, 'message' =>'todo okey', 
        'data'=>[
          'total'  => $total['data'],
          'centro_poblado'    => $centro_poblado['data'],
        ]
      ];
    }

    public function mostrar_producto($search_producto){
      $sql = "SELECT vw_pp.*
      FROM vw_producto_presentacion as vw_pp   
      WHERE ( vw_pp.idproducto_presentacion = '$search_producto' OR vw_pp.codigo = '$search_producto' OR vw_pp.codigo_alterno = '$search_producto' ) AND vw_pp.pro_estado = 1 AND vw_pp.pro_estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_producto_x_codigo($codigo){
      $sql = "SELECT p.*, um.nombre AS unidad_medida, um.abreviatura as um_abreviatura, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS um ON p.idsunat_unidad_medida = um.idsunat_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      WHERE (p.codigo = '$codigo' OR p.codigo_alterno = '$codigo' ) AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);      
    }

    public function listar_producto_x_nombre($search){
      $sql = "SELECT vw_pp.*
      FROM vw_producto_presentacion as vw_pp       
      WHERE (vw_pp.codigo like '%$search%' OR vw_pp.codigo_alterno like '%$search%' OR vw_pp.nombre_producto like '%$search%' ) 
      AND vw_pp.pro_estado = 1 AND vw_pp.pro_estado_delete = 1 ORDER BY vw_pp.nombre_producto LIMIT 10;";
      return ejecutarConsultaArray($sql);      
    }

    public function validar_mes_cobrado($idcliente, $periodo_pago, $idventa_detalle){
      $sql = "SELECT v.idventa, vd.idventa_detalle, v.serie_comprobante, v.numero_comprobante, v.tipo_comprobante, 
      v.fecha_emision, vd.periodo_pago_format, vd.periodo_pago, vd.pr_nombre,  vd.cantidad, vd.subtotal
      from venta as v
      INNER JOIN venta_detalle as vd on vd.idventa = v.idventa
      WHERE v.idpersona_cliente = '$idcliente' and vd.periodo_pago = '$periodo_pago' and vd.es_cobro='SI' AND v.estado_delete = 1 
      AND v.estado='1' AND  v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN ('01','03','12') AND idventa_detalle <> '$idventa_detalle'";
      $buscando =  ejecutarConsultaArray($sql); if ( $buscando['status'] == false) {return $buscando; }

      if (empty($buscando['data'])) { return true; }else { return false; }
      
    }

    public function ver_meses_cobrado($idcliente){
      $sql = "SELECT vw_f.*
      from vw_facturacion_detalle as vw_f
      WHERE vw_f.idpersona_cliente = $idcliente  and vw_f.es_cobro='SI' AND vw_f.estado_delete_v = 1 AND vw_f.estado_v='1' 
      AND  vw_f.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND vw_f.tipo_comprobante IN ('01','03','12') ";
      return ejecutarConsultaArray($sql);       
    }

    // ══════════════════════════════════════ C O M P R O B A N T E ══════════════════════════════════════

    public function datos_empresa(){
      $sql = "SELECT * FROM empresa;";
      return ejecutarConsultaSimpleFila($sql);      
    }

    public function datos_metodo_pago_venta($id){
      $sql = "SELECT * FROM venta_metodo_pago where idventa = $id;";
      return ejecutarConsultaArray($sql);      
    }

    // ══════════════════════════════════════ C U O T A S ══════════════════════════════════════
    public function mostrar_cuotas($id){
      $sql = "SELECT DATE_FORMAT(vc.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento_format, LPAD(vc.idventa_cuotas, 5, '0') AS idventa_cuotas_v2, vc.*,
      v.idventa as idventa_pago, LPAD(v.idventa, 5, '0') AS idventa_pago_v2, v.serie_comprobante, v.numero_comprobante
      from venta_cuotas as vc 
      LEFT JOIN venta_cuota_pago as vcp on vcp.idventa_cuotas = vc.idventa_cuotas
      LEFT JOIN venta as v on v.idventa = vcp.idventa
      where vc.idventa = '$id' and vc.estado = '1' and vc.estado_delete = '1'  ORDER BY vc.numero_cuota ASC ; ";
      return ejecutarConsultaArray($sql);      
    }

    // ══════════════════════════════════════ U S A R   A N T I C I P O ══════════════════════════════════════
    public function mostrar_anticipos($idcliente){
      $sql = "SELECT  pc.idpersona_cliente, p.nombre_razonsocial AS nombres,  p.apellidos_nombrecomercial AS apellidos,
        (
          IFNULL( (SELECT  SUM( CASE  WHEN ac.tipo = 'EGRESO' THEN ac.total * -1 ELSE ac.total END )
          FROM anticipo_cliente AS ac
          WHERE ac.idpersona_cliente = pc.idpersona_cliente
          GROUP BY ac.idpersona_cliente) , 0)
        ) AS total_anticipo
      FROM persona_cliente AS pc  
      INNER JOIN persona AS p ON pc.idpersona = p.idpersona
      WHERE pc.idpersona_cliente = '$idcliente';";
      return ejecutarConsultaSimpleFila($sql);      
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

    public function select2_comprobantes_anular($tipo_comprobante){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql = "SELECT v.idventa, v.tipo_comprobante, v.serie_comprobante, v.numero_comprobante,  
      CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS nombre_tipo_comprobante_v2,
      CASE
        WHEN TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()) = 1 THEN 'hace 1 día'
        WHEN TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()) > 1 THEN CONCAT('hace ', TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()), ' días')
        ELSE 'hoy'
      END AS fecha_emision_dif
      FROM venta as v
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = v.tipo_comprobante
      WHERE v.tipo_comprobante = '$tipo_comprobante' AND v.sunat_estado ='ACEPTADA' and v.vc_estado = case when v.venta_cuotas = 'SI' THEN 'pendiente' ELSE 'pagado' END AND  v.fecha_emision >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)  $filtro_id_trabajador 
      ORDER BY CONVERT(v.numero_comprobante, SIGNED) DESC;";  #return $sql;
      return ejecutarConsultaArray($sql); 
    }

    public function select2_series_comprobante($codigo){     

      $sql = "SELECT stp.abreviatura,  stp.serie
      FROM sunat_usuario_comprobante as suc
      INNER JOIN sunat_c01_tipo_comprobante as stp ON stp.idtipo_comprobante = suc.idtipo_comprobante
      WHERE stp.codigo = '$codigo' AND suc.idusuario = '$this->id_usr_sesion';";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_codigo_x_anulacion_comprobante(){
      $sql = "SELECT idsunat_c09_codigo_nota_credito as idsunat_c09, codigo, nombre, estado FROM sunat_c09_codigo_nota_credito;";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_filtro_tipo_comprobante($tipos){
      $sql="SELECT idtipo_comprobante, codigo, abreviatura AS tipo_comprobante, serie,
      CASE idtipo_comprobante WHEN '3' THEN 'BOLETA' WHEN '7' THEN 'NOTA CRED. FACTURA' WHEN '8' THEN 'NOTA CRED. BOLETA' ELSE abreviatura END AS nombre_tipo_comprobante_v2
      FROM sunat_c01_tipo_comprobante WHERE codigo in ($tipos) ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_cliente(){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql="SELECT p.idpersona, pc.idpersona_cliente, 
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo, p.numero_documento, sc06.abreviatura as nombre_tipo_documento,
      count(v.idventa) as cantidad
      FROM venta as v 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sc06 on p.tipo_documento=sc06.code_sunat
      WHERE v.estado = '1' AND v.estado_delete = '1' $filtro_id_trabajador
      GROUP BY p.idpersona, pc.idpersona_cliente, p.numero_documento, sc06.abreviatura ORDER BY  count(v.idventa) desc, p.nombre_razonsocial asc ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_banco(){
     
      $sql="SELECT * FROM bancos WHERE idbancos <> 1 and estado = '1' AND estado_delete = '1';";
      return ejecutarConsultaArray($sql);
    }

    public function select2_periodo_contable(){      
     
      $sql="SELECT pco.periodo, pco.idperiodo_contable, pco.periodo_year, pco.periodo_month, count(v.idventa) as cant_comprobante 
      FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante <> '100'
      WHERE pco.estado = '1' and pco.estado_delete = '1'
      GROUP BY pco.idperiodo_contable, pco.periodo_year, periodo_month
      ORDER BY periodo DESC";
      return ejecutarConsultaArray($sql);
    }
  }
?>