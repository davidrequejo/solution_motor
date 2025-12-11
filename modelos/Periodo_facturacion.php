<?php

  require "../config/Conexion_v2.php";

  class Periodo_facturacion
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

    public function listar_tabla_principal( $anio, $periodo,  $cliente, $comprobante ) {    
      
      $filtro_anio = "";  $filtro_periodo = ""; $filtro_cliente = ""; $filtro_comprobante = "";
    
      if ( empty($anio) )      { } else { $filtro_anio = "AND pco.periodo_year = '$anio'"; } 
      if ( empty($periodo) )      { } else { $filtro_periodo = "AND pco.periodo = '$periodo'"; } 
      if ( empty($cliente) )      { } else { $filtro_cliente = "AND v.idpersona_cliente = '$cliente'"; } 
      if ( empty($comprobante) )  { } else { $filtro_comprobante = "AND v.idsunat_c01 = '$comprobante'"; } 

      $sql = "SELECT pco.*, LPAD(pco.idperiodo_contable, 5, '0') AS idventa_v2,
      COALESCE(SUM(CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END), 0) AS venta_total, count(v.idventa) as cantidad_comprobante
      FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12')
      WHERE pco.estado = '1' and pco.estado_delete = '1' 
      $filtro_anio $filtro_cliente $filtro_comprobante $filtro_periodo    
      GROUP BY pco.periodo   
      ORDER BY pco.periodo DESC;"; #return $sql;
      $venta = ejecutarConsulta($sql); if ($venta['status'] == false) {return $venta; }

      return $venta;
    }

    public function insertar_periodo(  $periodo, $fecha_inicio, $fecha_fin ){
     

      $sql = "SELECT DATE_FORMAT(fecha_inicio, '%d-%m-%Y') AS fecha_inicio, DATE_FORMAT(fecha_fin, '%d-%m-%Y') AS fecha_fin,
      periodo, periodo_format, periodo_month, periodo_year, estado, estado_delete
      FROM periodo_contable
      WHERE ('$fecha_inicio' BETWEEN fecha_fin AND fecha_inicio ) OR ( '$fecha_fin' BETWEEN fecha_fin AND fecha_inicio ) OR periodo = '$periodo';";
      $buscando_error = ejecutarConsultaArray($sql); if ($buscando_error['status'] == false) {return $buscando_error; }

      if ( empty( $buscando_error['data'] ) ) {

        $sql_1 = " INSERT INTO periodo_contable( fecha_inicio, fecha_fin, periodo) 
        VALUES ('$fecha_inicio', '$fecha_fin', '$periodo')"; 
        $newdata = ejecutarConsulta_retornarID($sql_1, 'C'); if ($newdata['status'] == false) { return  $newdata;}      
        return $newdata;

      } else {

        $info_repetida = ''; 

        foreach ($buscando_error['data'] as $key => $val) {
          $info_repetida .= '<li class="text-left font-size-13px">
           <span class="font-size-13px text-danger"><b>Periodo: </b>'.$val['periodo_year'] .'-'.$val['periodo_month'].'</span><br>
            <span class="font-size-13px text-danger"><b>Fecha inicio: </b>'.$val['fecha_inicio'].'</span><br>
            <span class="font-size-13px text-danger"><b>Fecha fin: </b>'.$val['fecha_fin'].'</span><br>
            <b>Papelera: </b>'.( $val['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($val['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }

        $retorno = array( 'status' => 'error_usuario', 'titulo' => 'Errores anteriores!!', 'message' => 'No se podran generar comprobantes hasta corregir los errores anteriores a este: '. $info_repetida, 'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_error['data'], 'id_tabla' => '' );
        return $retorno;
      }      
    }

    public function editar_periodo($idperiodo_contable, $periodo, $fecha_inicio, $fecha_fin ) {

      $sql_1 = "UPDATE periodo_contable SET fecha_inicio='$fecha_inicio',fecha_fin='$fecha_fin',
      periodo='$periodo' WHERE idperiodo_contable='$idperiodo_contable'";
      return ejecutarConsulta($sql_1, 'U');
      
    }

    public function mostrar_editar_periodo($id){
      $sql = "SELECT * FROM  periodo_contable WHERE idperiodo_contable = '$id'; ";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function bloquear_fechas_usadas( $id ){
      $data = []; $omitir_periodo = '';
      if ( !empty( $id ) ) {  $omitir_periodo = "AND idperiodo_contable <> '$id'";  }
      
      $sql = "SELECT fecha_inicio, fecha_fin, periodo_year, periodo_month FROM  periodo_contable where  estado = '1' and estado_delete = '1' $omitir_periodo;";
      $fechas = ejecutarConsultaArray($sql);

      foreach ($fechas['data'] as $key => $val) {
        $data[] = [
          'from'    => $val['fecha_inicio'],
          'to'      => $val['fecha_fin'],
          'periodo' => $val['periodo_year'] .'-'. $val['periodo_month'],
        ];
      }

      return array( 'status' => true, 'message' => 'todo ok', 'user' =>  $_SESSION['user_nombre'], 'data' => $data, 'id_tabla' => '' );
         
    }

    public function eliminar($id){
      $sql = "UPDATE periodo_contable SET estado_delete = '0' WHERE idperiodo_contable = '$id'";
      return ejecutarConsulta($sql, 'D');
    }

    public function papelera($id){
      $sql = "UPDATE periodo_contable SET estado = '0'  WHERE idperiodo_contable = '$id'";
      return ejecutarConsulta($sql, 'T');
    }   

    Public function mini_reporte($filtro_anio, $periodo,  $cliente, $comprobante){

      $filtro_filtro_anio = ""; $filtro_periodo = ""; $filtro_cliente = ""; $filtro_comprobante = "";
    
      if ( empty($filtro_anio) )  { } else { $filtro_filtro_anio = "AND pco.periodo_year = '$filtro_anio'"; } 
      if ( empty($periodo) )      { } else { $filtro_periodo = "AND pco.periodo = '$periodo'"; } 
      if ( empty($cliente) )      { } else { $filtro_cliente = "AND v.idpersona_cliente = '$cliente'"; } 
      if ( empty($comprobante) )  { } else { $filtro_comprobante = "AND v.idsunat_c01 = '$comprobante'"; } 

      $meses_espanol = array( 1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic" );

      $sql_00 ="SELECT v.tipo_comprobante, COUNT( v.idventa ) as cantidad
      FROM venta as v
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      INNER JOIN periodo_contable as pco ON v.idperiodo_contable = pco.idperiodo_contable
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado = '1' AND v.estado_delete = '1' $filtro_filtro_anio $filtro_periodo $filtro_cliente $filtro_comprobante
      GROUP BY v.tipo_comprobante;";
      $coun_comprobante = ejecutarConsultaArray($sql_00); if ($coun_comprobante['status'] == false) {return $coun_comprobante; }

      $sql_01 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE()) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '01' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta 
      WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '01' ) AS ventas_mes_anterior;";
      $factura_p = ejecutarConsultaSimpleFila($sql_01); if ($factura_p['status'] == false) {return $factura_p; }
      $sql_01 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
      INNER JOIN periodo_contable as pco ON v.idperiodo_contable = pco.idperiodo_contable
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '01' AND v.estado = '1' AND v.estado_delete = '1' $filtro_filtro_anio $filtro_periodo $filtro_cliente $filtro_comprobante;";
      $factura = ejecutarConsultaSimpleFila($sql_01); if ($factura['status'] == false) {return $factura; }

      $sql_03 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE()) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '03' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '03' ) AS ventas_mes_anterior;";
      $boleta_p = ejecutarConsultaSimpleFila($sql_03); if ($boleta_p['status'] == false) {return $boleta_p; }
      $sql_03 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
      INNER JOIN periodo_contable as pco ON v.idperiodo_contable = pco.idperiodo_contable
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '03' AND v.estado = '1' AND v.estado_delete = '1' $filtro_filtro_anio $filtro_periodo $filtro_cliente $filtro_comprobante;";
      $boleta = ejecutarConsultaSimpleFila($sql_03); if ($boleta['status'] == false) {return $boleta; }

      $sql_12 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE()) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '12' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (fecha_emision) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (fecha_emision) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '12' ) AS ventas_mes_anterior;";
      $ticket_p = ejecutarConsultaSimpleFila($sql_12); if ($ticket_p['status'] == false) {return $ticket_p; }
      $sql_12 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
      INNER JOIN periodo_contable as pco ON v.idperiodo_contable = pco.idperiodo_contable
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '12' AND v.estado = '1' AND v.estado_delete = '1' $filtro_filtro_anio $filtro_periodo $filtro_cliente $filtro_comprobante;";
      $ticket = ejecutarConsultaSimpleFila($sql_12); if ($ticket['status'] == false) {return $ticket; }

      $mes_factura = []; $mes_nombre = []; $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $nro_mes = floatval( date("m", strtotime($fecha_actual)) );
        $sql_mes = "SELECT MONTHNAME(v.fecha_emision) AS fecha_emision , COALESCE(SUM(v.venta_total), 0) AS venta_total FROM venta as v
        WHERE MONTH(v.fecha_emision) = '$nro_mes' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '01' AND v.estado = '1' AND v.estado_delete = '1' $filtro_cliente $filtro_comprobante;";
        $mes_f = ejecutarConsultaSimpleFila($sql_mes); if ($mes_f['status'] == false) {return $mes_f; }
        array_push($mes_factura, floatval($mes_f['data']['venta_total']) ); array_push($mes_nombre, $meses_espanol[$nro_mes] );
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }

      $mes_boleta = [];  $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $sql_mes = "SELECT MONTHNAME(v.fecha_emision) AS fecha_emision , COALESCE(SUM(v.venta_total), 0) AS venta_total FROM venta as v
        WHERE MONTH(v.fecha_emision) = '".date("m", strtotime($fecha_actual))."' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '03' AND v.estado = '1' AND v.estado_delete = '1' $filtro_cliente $filtro_comprobante;";
        $mes_b = ejecutarConsultaSimpleFila($sql_mes); if ($mes_b['status'] == false) {return $mes_b; }
        array_push($mes_boleta, floatval($mes_b['data']['venta_total']) ); 
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }

      $mes_ticket = [];  $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $sql_mes = "SELECT MONTHNAME(v.fecha_emision) AS fecha_emision , COALESCE(SUM(v.venta_total), 0) AS venta_total FROM venta as v
        WHERE MONTH(v.fecha_emision) = '".date("m", strtotime($fecha_actual))."' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '12' AND v.estado = '1' AND v.estado_delete = '1' $filtro_cliente $filtro_comprobante;";
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

    // ══════════════════════════════════════ DETALLE COMPROBANTE ══════════════════════════════════════

    public function reasignar_periodo(  $idperiodo, $venta ){        

      // $sql_periodo = "SELECT * FROM periodo_contable WHERE periodo ='$periodo';";
      // $buscar_periodo = ejecutarConsultaSimpleFila($sql_periodo); if ($buscar_periodo['status'] == false) {return $buscar_periodo; }
      $actualizando = '';
      foreach ( $venta as $key => $val ) {
        
        $idventa = $val['idventa'];
        
        $sql_1 = "UPDATE venta SET idperiodo_contable='$idperiodo' WHERE idventa='$idventa';"; 
        $actualizando = ejecutarConsulta($sql_1, 'U');  if ($actualizando['status'] == false) {return $actualizando; }
      }   
      
      return $actualizando;
    }

    public function listar_tabla_comprobante( $idperiodo,  $mes_emision, $cliente, $comprobante ) {    
     
      $filtro_periodo = ""; $filtro_mes_emision = ""; $filtro_cliente = ""; $filtro_comprobante = "";       
      
      if ( empty($idperiodo) ) { } else {  $filtro_periodo = "AND v.idperiodo_contable = '$idperiodo'"; }
      if ( empty($mes_emision) ) { } else {  $filtro_mes_emision = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m') = '$mes_emision'"; }  
      if ( empty($cliente) ) { } else {  $filtro_cliente = "AND v.idpersona_cliente = '$cliente'"; } 
      if ( empty($comprobante) ) { } else {  $filtro_comprobante = "AND v.idsunat_c01 = '$comprobante'"; } 

      $sql = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
      CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,
      DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, LEFT(v.name_month, 3) as periodo_pago_month_v2,
      p.nombre_razonsocial, p.apellidos_nombrecomercial, p.tipo_documento, 
      p.numero_documento, p.foto_perfil, tc.abreviatura as tp_comprobante_v1, sdi.abreviatura as tipo_documento, v.estado,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN 
          CASE 
            WHEN LENGTH(  CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial)  ) <= 27 THEN  CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
            ELSE CONCAT( LEFT(CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial ), 27) , '...')
          END         
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN 
          CASE 
            WHEN LENGTH(  p.nombre_razonsocial  ) <= 27 THEN  p.nombre_razonsocial 
            ELSE CONCAT(LEFT( p.nombre_razonsocial, 27) , '...')
          END
        ELSE '-'
      END AS cliente_nombre_recortado,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo, pu.nombre_razonsocial as user_en_atencion, LPAD(v.user_created, 3, '0') AS user_created_v2,
      CONCAT(pco.periodo_month, '-', pco.periodo_year) as nombre_periodo
      FROM venta AS v
      INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN persona AS p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
      LEFT JOIN usuario as u ON u.idusuario = v.user_created
      LEFT JOIN persona as pu ON pu.idpersona = u.idpersona
      LEFT JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12') $filtro_periodo $filtro_mes_emision $filtro_cliente $filtro_comprobante 
      ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC;"; #return $sql;
      $venta = ejecutarConsulta($sql); if ($venta['status'] == false) {return $venta; }

      return $venta;
    }


    // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
    
    public function select2_filtro_tipo_comprobante($tipos){
      $sql="SELECT idtipo_comprobante, codigo, abreviatura AS tipo_comprobante, serie,
      CASE idtipo_comprobante WHEN '3' THEN 'BOLETA' WHEN '7' THEN 'NOTA CRED. FACTURA' WHEN '8' THEN 'NOTA CRED. BOLETA' ELSE abreviatura END AS nombre_tipo_comprobante_v2
      FROM sunat_c01_tipo_comprobante WHERE codigo in ($tipos) ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_cliente(){      
     
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
      WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante <> '100'
      GROUP BY p.idpersona ORDER BY  count(v.idventa) desc, p.nombre_razonsocial asc ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_anio(){      
     
      $sql="SELECT  pco.periodo_year,  count(v.idventa) as cant_comprobante FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante <> '100'
      WHERE pco.estado = '1' and pco.estado_delete = '1'
      GROUP BY pco.periodo_year
      ORDER BY periodo DESC";
      return ejecutarConsultaArray($sql);
    }

    public function select2_periodo(){      
     
      $sql="SELECT pco.idperiodo_contable, pco.periodo_year, pco.periodo_month, count(v.idventa) as cant_comprobante FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante <> '100'
      WHERE pco.estado = '1' and pco.estado_delete = '1'
      GROUP BY pco.idperiodo_contable, pco.periodo_year, periodo_month
      ORDER BY periodo DESC";
      return ejecutarConsultaArray($sql);
    }
  }
?>