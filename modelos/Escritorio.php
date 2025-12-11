<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Escritorio
{
  //Implementamos nuestro constructor
  public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
	//Implementamos nuestro constructor
	public function __construct(){
    $this->id_usr_sesion        =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
    $this->id_persona_sesion    = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
    $this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
	}

  //Implementamos un método para insertar registros
	public function ver_reporte($anio, $mes, $cant_mes, $trabajador){

    $filtro_anio  = '';   $filtro_mes  = '';  $filtro_trabajador  = '';

    if ( empty($anio) )       { } else {  $filtro_anio = "AND YEAR(pco.periodo_format) = '$anio'";    }    
    if ( empty($mes) )        { } else {  $filtro_mes = "AND pco.periodo = '$mes'";    }    
    if ( empty($trabajador) ) { } else {  $filtro_trabajador = "AND v.user_created = '$trabajador'";    }    
    
    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                                           O B J E T I V O                                                                             ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */

    $sql_01 = "SELECT COUNT( DISTINCT CASE WHEN MONTH (v.fecha_emision) = MONTH (CURDATE()) AND v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') THEN v.idpersona_cliente END ) AS cobrado,
    COUNT(DISTINCT pc.idpersona_cliente) AS total,
    (
      COUNT( DISTINCT CASE WHEN MONTH (v.fecha_emision) = MONTH (CURDATE()) AND v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') THEN v.idpersona_cliente END ) / COUNT(DISTINCT pc.idpersona_cliente)
    ) * 100 AS porcentaje_este_mes
    FROM persona_cliente AS pc
    LEFT JOIN venta AS v ON pc.idpersona_cliente = v.idpersona_cliente 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') $filtro_trabajador $filtro_anio $filtro_mes ;";
    $objetivo = ejecutarConsultaSimpleFila($sql_01); if ($objetivo['status'] == false) { return $objetivo;}  

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                                           T O P   5   C L I E N T E S                                                                 ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */
    
    $sql_02 = "SELECT v.idpersona_cliente, SUM(v.venta_total) AS total_cobrado,
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
    END AS cliente_nombre_completo,
    sdi.abreviatura as nombre_tipo_documento, p.*
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
    INNER JOIN persona AS p ON p.idpersona = pc.idpersona
    INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
    WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY v.idpersona_cliente
    ORDER BY SUM(v.venta_total) DESC
    LIMIT 5; ";
    $top_5_cliente = ejecutarConsultaArray($sql_02); if ($top_5_cliente['status'] == false) { return $top_5_cliente;}  

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                            C O B R O S   P O R   D I A   D E   S E M A N A                                                            ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */

    $sql_031 = "SELECT CASE DAYOFWEEK(v.fecha_emision) WHEN 1 THEN 'Do'  WHEN 2 THEN 'Lu' WHEN 3 THEN 'Ma' WHEN 4 THEN 'Mi' WHEN 5 THEN 'Ju'  WHEN 6 THEN 'Vi' WHEN 7 THEN 'Sa' END AS DiaSemana, DAYOFWEEK(v.fecha_emision) AS dia, SUM(venta_total) AS VentasDia
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
    where tipo_comprobante = '01' and v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DAYOFWEEK(v.fecha_emision) ORDER BY DAYOFWEEK(v.fecha_emision);";
    $dia_semana_f = ejecutarConsultaArray($sql_031); if ($dia_semana_f['status'] == false) { return $dia_semana_f;}  

    $sql_032 = "SELECT CASE DAYOFWEEK(v.fecha_emision) WHEN 1 THEN 'Do'  WHEN 2 THEN 'Lu' WHEN 3 THEN 'Ma' WHEN 4 THEN 'Mi' WHEN 5 THEN 'Ju'  WHEN 6 THEN 'Vi' WHEN 7 THEN 'Sa' END AS DiaSemana, DAYOFWEEK(v.fecha_emision) AS dia, SUM(venta_total) AS VentasDia
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
    where tipo_comprobante = '03' and v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DAYOFWEEK(v.fecha_emision) ORDER BY DAYOFWEEK(v.fecha_emision);";
    $dia_semana_b = ejecutarConsultaArray($sql_032); if ($dia_semana_b['status'] == false) { return $dia_semana_b;}  

    $sql_033 = "SELECT CASE DAYOFWEEK(v.fecha_emision) WHEN 1 THEN 'Do'  WHEN 2 THEN 'Lu' WHEN 3 THEN 'Ma' WHEN 4 THEN 'Mi' WHEN 5 THEN 'Ju'  WHEN 6 THEN 'Vi' WHEN 7 THEN 'Sa' END AS DiaSemana, DAYOFWEEK(v.fecha_emision) AS dia, SUM(venta_total) AS VentasDia
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
    where tipo_comprobante = '12' and v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DAYOFWEEK(v.fecha_emision) ORDER BY DAYOFWEEK(v.fecha_emision);";
    $dia_semana_t = ejecutarConsultaArray($sql_033); if ($dia_semana_t['status'] == false) { return $dia_semana_t;}  

    $ds_total_f = []; $ds_total_b = []; $ds_total_t = []; $ds_dia = ['Do','Lu','Ma','Mi','Ju','Vi','Sá'];
    foreach ($dia_semana_f['data'] as $key => $val) { array_push($ds_total_f, $val['VentasDia']); }
    foreach ($dia_semana_b['data'] as $key => $val) { array_push($ds_total_b, $val['VentasDia']); }
    foreach ($dia_semana_t['data'] as $key => $val) { array_push($ds_total_t, $val['VentasDia']); }

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                                      C A R D   P O R   C O M P R O B A N T E                                                          ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */

    $sql_04 ="SELECT v.tipo_comprobante, COUNT( v.idventa ) as cantidad, sum(v.venta_total) as venta_total
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') AND v.estado = '1' AND v.estado_delete = '1' $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY v.tipo_comprobante;";
    $card_coun_comprobante = ejecutarConsultaArray($sql_04); if ($card_coun_comprobante['status'] == false) {return $card_coun_comprobante; }

    $sql_041 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '01' AND v.estado = '1' AND v.estado_delete = '1' $filtro_trabajador $filtro_anio $filtro_mes;";
    $card_factura = ejecutarConsultaSimpleFila($sql_041); if ($card_factura['status'] == false) {return $card_factura; }

    $sql_042 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '03' AND v.estado = '1' AND v.estado_delete = '1' $filtro_trabajador $filtro_anio $filtro_mes;";
    $card_boleta = ejecutarConsultaSimpleFila($sql_042); if ($card_boleta['status'] == false) {return $card_boleta; }
    
    $sql_043 = "SELECT IFNULL( SUM( v.venta_total), 0 ) as venta_total FROM venta as v 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '12' AND v.estado = '1' AND v.estado_delete = '1' $filtro_trabajador $filtro_anio $filtro_mes;";
    $card_ticket = ejecutarConsultaSimpleFila($sql_043); if ($card_ticket['status'] == false) {return $card_ticket; }
    
    $f_chart = []; $fm_chart = []; $b_chart = []; $bm_chart = []; $t_chart = []; $tm_chart = []; $fbt_chart = []; $fbtm_chart = [];
    $sql_044 = "SELECT CONCAT( DATE_FORMAT(v.fecha_emision, '%d'), ' - ', LEFT(v.name_month, 3) ) AS mes, IFNULL( SUM( v.venta_total), 0 ) as venta_total 
    FROM venta as v INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '01' AND v.estado = '1' AND v.estado_delete = '1' AND v.fecha_emision >= (CURDATE() - INTERVAL 30 DAY) $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DATE(v.fecha_emision)
    order by v.fecha_emision DESC;";
    $card_factura_chart = ejecutarConsultaArray($sql_044); if ($card_factura_chart['status'] == false) {return $card_factura_chart; }

    $sql_045 = "SELECT CONCAT( DATE_FORMAT(v.fecha_emision, '%d'), ' - ', LEFT(v.name_month, 3) ) AS mes, IFNULL( SUM( v.venta_total), 0 ) as venta_total 
    FROM venta as v 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '03' AND v.estado = '1' AND v.estado_delete = '1' AND v.fecha_emision >= (CURDATE() - INTERVAL 30 DAY) $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DATE(v.fecha_emision)
    order by v.fecha_emision DESC;";
    $card_boleta_chart = ejecutarConsultaArray($sql_045); if ($card_boleta_chart['status'] == false) {return $card_boleta_chart; }

    $sql_046 = "SELECT CONCAT( DATE_FORMAT(v.fecha_emision, '%d'), ' - ', LEFT(v.name_month, 3) ) AS mes, IFNULL( SUM( v.venta_total), 0 ) as venta_total 
    FROM venta as v 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante = '12' AND v.estado = '1' AND v.estado_delete = '1' AND v.fecha_emision >= (CURDATE() - INTERVAL 30 DAY) $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DATE(v.fecha_emision)
    order by v.fecha_emision DESC;";
    $card_ticket_chart = ejecutarConsultaArray($sql_046); if ($card_ticket_chart['status'] == false) {return $card_ticket_chart; }

    $sql_046 = "SELECT CONCAT( DATE_FORMAT(v.fecha_emision, '%d'), ' - ', LEFT(v.name_month, 3) ) AS mes, IFNULL( SUM( v.venta_total), 0 ) as venta_total 
    FROM venta as v 
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') AND v.estado = '1' AND v.estado_delete = '1' AND v.fecha_emision >= (CURDATE() - INTERVAL 30 DAY) $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY DATE(v.fecha_emision), v.name_month
    order by v.fecha_emision DESC;";
    $card_fbt_chart = ejecutarConsultaArray($sql_046); if ($card_fbt_chart['status'] == false) {return $card_fbt_chart; }

    foreach ($card_factura_chart['data'] as $key => $val) { array_push($f_chart, $val['venta_total']); array_push($fm_chart, $val['mes']); }
    foreach ($card_boleta_chart['data'] as $key => $val) { array_push($b_chart, $val['venta_total']); array_push($bm_chart, $val['mes']); }
    foreach ($card_ticket_chart['data'] as $key => $val) { array_push($t_chart, $val['venta_total']); array_push($tm_chart, $val['mes']); }
    foreach ($card_fbt_chart['data'] as $key => $val) { array_push($fbt_chart, $val['venta_total']); array_push($fbtm_chart, $val['mes']); }

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                            C H A R   L I N E A  P O R   C O M P R O B A N T E                                                         ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */


    $chart_line_f = []; $chart_line_b = []; $chart_line_t = []; $chart_line_fbt = [];  $chart_line_mes = empty($filtro_mes) ? ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', ] : json_decode($cant_mes, true) ;
    $sql_dia_o_mes = empty($filtro_mes) ? "MONTH(v.fecha_emision)" : "DAY(v.fecha_emision)";

    for ($i=1; $i <= count($chart_line_mes) ; $i++) { 

      # :::::::::::: FACTURA ::::::::::::
      $sql_51 = "SELECT IFNULL( SUM(v.venta_total), 0) as venta_total, MAX( LEFT(v.name_month, 3) ) AS name_month
      FROM venta as v  
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE $sql_dia_o_mes ='$i' AND v.tipo_comprobante = '01' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado='1' AND v.estado_delete='1' $filtro_trabajador $filtro_anio $filtro_mes;";
      $chart_line_factura = ejecutarConsultaSimpleFila($sql_51); if ($chart_line_factura['status'] == false) { return $chart_line_factura; }

      $chart_line_f[] = [
        'x' => $chart_line_mes[($i-1)] ,
        'y' => (empty($chart_line_factura['data']) ? 0 : (empty($chart_line_factura['data']['venta_total']) ? 0 : floatval($chart_line_factura['data']['venta_total']) ) )
      ];           

      # :::::::::::: BOLETA ::::::::::::
      $sql_52 = "SELECT IFNULL( SUM(v.venta_total), 0) as venta_total, MAX( LEFT(v.name_month, 3) ) AS name_month
      FROM venta as v  
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE $sql_dia_o_mes ='$i' AND v.tipo_comprobante = '03' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado='1' AND v.estado_delete='1' $filtro_trabajador $filtro_anio $filtro_mes;";
      $chart_line_boleta = ejecutarConsultaSimpleFila($sql_52); if ($chart_line_boleta['status'] == false) { return $chart_line_boleta; }

      $chart_line_b[] = [
        'x' => $chart_line_mes[($i-1)] ,
        'y' => (empty($chart_line_boleta['data']) ? 0 : (empty($chart_line_boleta['data']['venta_total']) ? 0 : floatval($chart_line_boleta['data']['venta_total']) ) )
      ];           

      # :::::::::::: TICKET ::::::::::::
      $sql_53 = "SELECT IFNULL( SUM(v.venta_total), 0) as venta_total, MAX( LEFT(v.name_month, 3) ) AS name_month
      FROM venta as v  
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE $sql_dia_o_mes ='$i' AND v.tipo_comprobante = '12' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado='1' AND v.estado_delete='1' $filtro_trabajador $filtro_anio $filtro_mes;";
      $chart_line_ticket = ejecutarConsultaSimpleFila($sql_53); if ($chart_line_ticket['status'] == false) { return $chart_line_ticket; }

      $chart_line_t[] = [
        'x' => $chart_line_mes[($i-1)] ,
        'y' => (empty($chart_line_ticket['data']) ? 0 : (empty($chart_line_ticket['data']['venta_total']) ? 0 : floatval($chart_line_ticket['data']['venta_total']) ) )
      ];    
      
      # :::::::::::: TOTAL ::::::::::::
      $sql_53 = "SELECT IFNULL( SUM(v.venta_total), 0) as venta_total, MAX( LEFT(v.name_month, 3) ) AS name_month
      FROM venta as v  
      INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE $sql_dia_o_mes ='$i' AND v.tipo_comprobante in ('01','03','12')  AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado='1' AND v.estado_delete='1' $filtro_trabajador $filtro_anio $filtro_mes;";
      $chart_line_total = ejecutarConsultaSimpleFila($sql_53); if ($chart_line_total['status'] == false) { return $chart_line_total; }

      $chart_line_fbt[] = [
        'x' => $chart_line_mes[($i-1)] ,
        'y' => (empty($chart_line_total['data']) ? 0 : (empty($chart_line_total['data']['venta_total']) ? 0 : floatval($chart_line_total['data']['venta_total']) ) )
      ];    

    }

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                            T A B L A   T O P   5   P R I M E R O S   P R O D U C T O S                                               ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */

    $sql_06 = "SELECT vd.idproducto_presentacion, pp.nombre_producto, pp.imagen, pp.precio_venta, pp.categoria, SUM(vd.cantidad_total) AS cantidad, SUM(vd.subtotal) AS subtotal
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
    inner join vw_producto_presentacion as pp on pp.idproducto_presentacion = vd.idproducto_presentacion    
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente    
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado = '1' AND v.estado_delete = '1'  AND v.tipo_comprobante in ('01','03','12') $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY vd.idproducto_presentacion, pp.nombre_producto, pp.imagen, pp.precio_venta, pp.categoria
    ORDER BY SUM(vd.subtotal) DESC
    LIMIT 5; ";
    $top_5_producto = ejecutarConsultaArray($sql_06); if ($top_5_producto['status'] == false) { return $top_5_producto;}  

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                                           T O P   5   T E C N I C O S                                                                 ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */
    
    $sql_07 = "SELECT v.user_created, pu.nombre_razonsocial, SUM(v.venta_total) AS total_cobrado      
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
    INNER JOIN usuario as u on v.user_created = u.idusuario
		INNER JOIN persona as pu on u.idpersona = pu.idpersona
    INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = pu.tipo_documento
    WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') $filtro_trabajador $filtro_anio $filtro_mes
    GROUP BY v.user_created, pu.nombre_razonsocial
    ORDER BY SUM(v.venta_total) DESC
    LIMIT 5; ";
    $top_5_tecnico = ejecutarConsultaArray($sql_07); if ($top_5_tecnico['status'] == false) { return $top_5_tecnico;}  

    $data_pay = [];
    foreach ($top_5_tecnico['data'] as $key => $val) { array_push($data_pay, $val['total_cobrado']);  }

    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                                           T O P   5   C E N T R O   P O B L A D O                                                     ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */
    
    $sql_08 = "SELECT cp.nombre as nombre_centro_poblado, SUM(v.venta_total) AS total_cobrado      
    FROM venta as v
    INNER JOIN periodo_contable AS pco ON pco.idperiodo_contable = v.idperiodo_contable 
    INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente    
		INNER JOIN persona as p on p.idpersona = pc.idpersona
    INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
    WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') $filtro_trabajador
    GROUP BY cp.nombre
    ORDER BY SUM(v.venta_total) DESC
    LIMIT 5; ";
    $top_5_centro_poblado = ejecutarConsultaArray($sql_08); if ($top_5_centro_poblado['status'] == false) { return $top_5_centro_poblado;} 
    
    /*
    ╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
    ║                                                                                                                                                       ║
    ║                                                           T O P   5   I N C I D E N C I A S   P E N D I E N T E S                                     ║
    ║                                                                                                                                                       ║
    ╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝ 
    */
    
    // $sql_09 = "SELECT  i.*,
    // CASE i.estado_revicion 
    //   WHEN 'CRÍTICO' THEN  'bg-danger-transparent' 
    //   WHEN 'ALTO' THEN  'bg-pink-transparent' 
    //   WHEN 'MEDIO' THEN  'bg-warning-transparent' 
    //   WHEN 'BAJO' THEN  'bg-success-transparent' 
    //   ELSE '-' 
    // END as estado_revicion_color,
    // CASE WHEN LENGTH(  i.actividad  ) <= 50 THEN  i.actividad ELSE CONCAT(LEFT( i.actividad, 50) , '...') END as actividad_v2,
    // CASE WHEN LENGTH(  i.actividad_detalle  ) <= 60 THEN  i.actividad_detalle ELSE CONCAT(LEFT( i.actividad_detalle, 60) , '...') END as actividad_detalle_v2,
    // ic.nombre as nombre_categoria
    // FROM incidencias AS i INNER JOIN incidencia_categoria as ic ON ic.idincidencia_categoria = i.idincidencia_categoria   
    // where i.estado_incidencia = '1' and i.estado='1' and i.estado_delete='1' ORDER BY i.created_at ASC LIMIT 7; ";
    // $top_5_incidencias = ejecutarConsultaArray($sql_09); if ($top_5_incidencias['status'] == false) { return $top_5_incidencias;} 

 

		return array( 
      'status' => true, 'message' => 'todo ok', 
      'data' => [
        'objetivo'              => $objetivo['data'],
        'top_5_cliente'         => $top_5_cliente['data'],
        'dia_semana'            => ['ds_total_f' =>$ds_total_f,'ds_total_b' =>$ds_total_b, 'ds_total_t' =>$ds_total_t, 'ds_dia' => $ds_dia ],
        'card_comprobante'      => [
          'cant'        => $card_coun_comprobante['data'],
          'factura'     => floatval($card_factura['data']['venta_total']),
          'boleta'      => floatval($card_boleta['data']['venta_total']),
          'ticket'      => floatval($card_ticket['data']['venta_total']),
          'f_chart'     => $f_chart,
          'fm_chart'    => $fm_chart,
          'b_chart'     => $b_chart,
          'bm_chart'    => $bm_chart,
          't_chart'     => $t_chart,
          'tm_chart'    => $tm_chart,
          'fbt_chart'   => $fbt_chart,
          'fbtm_chart'  => $fbtm_chart,
        ],
        'chart_comprobante'     => [          
          'factura'     => $chart_line_f,
          'boleta'      => $chart_line_b,
          'ticket'      => $chart_line_t,  
          'total'       => $chart_line_fbt,          
        ],
        'top_5_producto'        => $top_5_producto['data'],
        'top_5_tecnico'         => ['data_pay' => $data_pay, 'data' => $top_5_tecnico['data'], ],
        'top_5_centro_poblado'  =>  $top_5_centro_poblado['data'],
        // 'top_7_incidencias'     =>  $top_5_incidencias['data'],
      ], 
      'id_tabla' => '' 
    );

	}

  public function select2_filtro_anio_contable()	{   
   
    $sql = "SELECT year(periodo_format) as anio_contable FROM periodo_contable GROUP BY year(periodo_format) ORDER BY year(periodo_format) desc;";
    return ejecutarConsultaArray($sql);
  }

  public function select2_filtro_trabajador()	{
    $filtro_id_trabajador  = '';
    // if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
    //   $filtro_id_trabajador = "WHERE pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
    // } 
    $sql = "SELECT u.idusuario, LPAD(pt.idpersona_trabajador, 5, '0') as idtrabajador, pt.idpersona_trabajador, pt.idpersona, per_t.nombre_razonsocial
    FROM  persona_trabajador as pt 
    INNER JOIN persona as per_t ON per_t.idpersona = pt.idpersona
    INNER JOIN usuario as u on u.idpersona = per_t.idpersona
    $filtro_id_trabajador    
    ORDER BY per_t.nombre_razonsocial asc;";
    return ejecutarConsultaArray($sql);
  }

}