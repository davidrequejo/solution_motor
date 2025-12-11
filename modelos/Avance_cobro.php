<?php

  require "../config/Conexion_v2.php";

  class Avance_cobro
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

    public function listar_tabla_principal(  $periodo,  $trabajador ) {    
      
      $filtro_periodo = ""; $filtro_trabajador_1 = ""; $filtro_trabajador_2 = "";    
      
      if ( empty($periodo) )    { } else { $filtro_periodo = "AND DATE_FORMAT( vd.periodo_pago_format, '%Y-%m') = '$periodo'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_1 = "WHERE pc.idpersona_trabajador = '$trabajador'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_2 = "AND pc.idpersona_trabajador = '$trabajador'"; } 

      $sql = "SELECT pco.idcentro_poblado, pco.centro_poblado, ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
       COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 
      (SELECT cp.idcentro_poblado, cp.nombre as centro_poblado, COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      $filtro_trabajador_1
      GROUP BY cp.idcentro_poblado
      order by COUNT(pc.idpersona_cliente) DESC) AS pco 

      LEFT JOIN

      (SELECT cp.idcentro_poblado, cp.nombre as centro_poblado, COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) 
      $filtro_periodo $filtro_trabajador_2
      GROUP BY cp.idcentro_poblado
      order by COUNT(v.idventa) DESC) as co ON pco.idcentro_poblado = co.idcentro_poblado
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $venta = ejecutarConsultaArray($sql); if ($venta['status'] == false) {return $venta; }

      return $venta;
    }

    public function mostrar_reporte($periodo,  $trabajador){
      $filtro_periodo = ""; $filtro_trabajador_1 = ""; $filtro_trabajador_2 = "";    
      
      if ( empty($periodo) )    { } else { $filtro_periodo = "AND DATE_FORMAT( vd.periodo_pago_format, '%Y-%m') = '$periodo'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_1 = "WHERE pc.idpersona_trabajador = '$trabajador'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_2 = "AND pc.idpersona_trabajador = '$trabajador'"; } 

      $sql = "SELECT ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
      COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 

      (SELECT pc.idpersona_trabajador, COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      $filtro_trabajador_1
      GROUP BY pc.idpersona_trabajador
      order by COUNT(pc.idpersona_cliente) DESC) AS pco 

      LEFT JOIN

      (SELECT pc.idpersona_trabajador, COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) $filtro_periodo $filtro_trabajador_2
      GROUP BY pc.idpersona_trabajador
      order by COUNT(v.idventa) DESC) as co ON pco.idpersona_trabajador = co.idpersona_trabajador
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $centro_poblado = ejecutarConsultaSimpleFila($sql); if ($centro_poblado['status'] == false) {return $centro_poblado; }

      $sql_plan = "SELECT pco.idplan, pco.plan, ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
       COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 
      (SELECT cp.idplan, cp.nombre as plan, COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN plan as cp ON cp.idplan = pc.idplan
      $filtro_trabajador_1
      GROUP BY cp.idplan
      order by COUNT(pc.idpersona_cliente) DESC) AS pco 

      LEFT JOIN

      (SELECT cp.idplan, cp.nombre as plan, COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN plan as cp ON cp.idplan = pc.idplan
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) $filtro_periodo $filtro_trabajador_2
      GROUP BY cp.idplan
      order by COUNT(v.idventa) DESC) as co ON pco.idplan = co.idplan
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $plan = ejecutarConsultaArray($sql_plan); if ($plan['status'] == false) {return $plan; }

      return ['status' => true, 'message' =>'todo okey', 
        'data'=>[
          'centro_poblado'  => $centro_poblado['data'],
          'plan'            => $plan['data'],
        ]
      ];
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
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE()) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '01' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta 
      WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '01' ) AS ventas_mes_anterior;";
      $factura_p = ejecutarConsultaSimpleFila($sql_01); if ($factura_p['status'] == false) {return $factura_p; }
      $sql_01 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
      INNER JOIN periodo_contable as pco ON v.idperiodo_contable = pco.idperiodo_contable
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '01' AND v.estado = '1' AND v.estado_delete = '1' $filtro_filtro_anio $filtro_periodo $filtro_cliente $filtro_comprobante;";
      $factura = ejecutarConsultaSimpleFila($sql_01); if ($factura['status'] == false) {return $factura; }

      $sql_03 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE()) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '03' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '03' ) AS ventas_mes_anterior;";
      $boleta_p = ejecutarConsultaSimpleFila($sql_03); if ($boleta_p['status'] == false) {return $boleta_p; }
      $sql_03 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
      INNER JOIN periodo_contable as pco ON v.idperiodo_contable = pco.idperiodo_contable
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '03' AND v.estado = '1' AND v.estado_delete = '1' $filtro_filtro_anio $filtro_periodo $filtro_cliente $filtro_comprobante;";
      $boleta = ejecutarConsultaSimpleFila($sql_03); if ($boleta['status'] == false) {return $boleta; }

      $sql_12 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE()) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE()) AND tipo_comprobante = '12' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND tipo_comprobante = '12' ) AS ventas_mes_anterior;";
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
      WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' )
      GROUP BY p.idpersona ORDER BY  count(v.idventa) desc, p.nombre_razonsocial asc ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_anio(){      
     
      $sql="SELECT  pco.periodo_year,  count(v.idventa) as cant_comprobante FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' )
      WHERE pco.estado = '1' and pco.estado_delete = '1'
      GROUP BY pco.periodo_year
      ORDER BY periodo DESC";
      return ejecutarConsultaArray($sql);
    }

    public function select2_periodo(){      
     
      $sql="SELECT pco.idperiodo_contable, pco.periodo_year, pco.periodo_month, count(v.idventa) as cant_comprobante FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' )
      WHERE pco.estado = '1' and pco.estado_delete = '1'
      GROUP BY pco.idperiodo_contable, pco.periodo_year, periodo_month
      ORDER BY periodo DESC";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_trabajador()	{
      $filtro_id_trabajador  = '';
      // if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
      //   $filtro_id_trabajador = "WHERE pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      // } 
      $sql = "SELECT LPAD(pt.idpersona_trabajador, 5, '0') as idtrabajador, pt.idpersona_trabajador, pt.idpersona,  per_t.nombre_razonsocial, COUNT(pc.idpersona_cliente) AS cant_cliente
      FROM persona_cliente as pc
      INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = pc.idpersona_trabajador
      INNER JOIN persona as per_t ON per_t.idpersona = pt.idpersona
      $filtro_id_trabajador
      GROUP BY pc.idpersona_trabajador
      ORDER BY  COUNT(pc.idpersona_cliente) desc, per_t.nombre_razonsocial asc;";
      return ejecutarConsulta($sql);
    }
  }
?>