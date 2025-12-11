<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Reporte_venta_x_trabajador
{
	public $id_usr_sesion;
	public $id_persona_sesion;
	public $id_trabajador_sesion;
	//Implementamos nuestro constructor
	public function __construct()
	{
		$this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
		$this->id_persona_sesion = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
		$this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
	}
	/*T-- paepelera --desacctivar
	C-- crear
	R-- read
	U-- actualizar
	D-- delete -- eliminar*/

	

	//Implementar un método para listar los registros
	public function tabla_reporte_detalle($cobrado, $fecha_i, $fecha_f, $filtro_trabajador)
	{

		$sql_cobrado  = ''; $sql_fecha  = '';	$sql_filtro_trabajador  = '';

		if ($cobrado == 'COBRADO') {	$sql_cobrado	= "AND vw_roc.iddocumento_relacionado > 1";		} 
		else if ($cobrado == 'POR COBRADO') { $sql_cobrado	= "AND (vw_roc.iddocumento_relacionado is null or vw_roc.iddocumento_relacionado = '' )";	 }

		if (empty($filtro_trabajador) || $filtro_trabajador == 'TODOS') {	} else {	$sql_filtro_trabajador	= "AND vw_roc.uc_idusuario = '$filtro_trabajador'";		}

		if ( !empty($fecha_i) && !empty($fecha_f) ) { $sql_fecha = "AND vw_roc.fecha_emision_amd BETWEEN '$fecha_i' AND '$fecha_f'"; } 
    else if (!empty($fecha_i)) { $sql_fecha = "AND vw_roc.fecha_emision_amd = '$fecha_i'"; }
    else if (!empty($fecha_f)) { $sql_fecha = "AND vw_roc.fecha_emision_amd = '$fecha_f'"; }		

		$sql = "SELECT vw_roc.* FROM vw_reporte_orden_venta as vw_roc
		where vw_roc.tipo_comprobante in ('103') and vw_roc.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and vw_roc.v_estado = '1' and vw_roc.v_estado_delete = '1'
		$sql_cobrado $sql_fecha $sql_filtro_trabajador 
		order by vw_roc.fecha_emision DESC";
		return ejecutarConsulta($sql);
	}

	/* ════════════════════════════════════════════════════════════════════════════
		═══															CARD MONTOS TOTALES
	/* ════════════════════════════════════════════════════════════════════════════ */

	public function resumen_reporte($cobrado, $fecha_i, $fecha_f, $filtro_trabajador, $temporalidad)
	{

		$sql_cobrado  = ''; $sql_fecha  = '';	$sql_filtro_trabajador  = '';		

		if ($cobrado == 'COBRADO') {	$sql_cobrado	= "AND v.iddocumento_relacionado > 1";		} 
		else if ($cobrado == 'POR COBRADO') { $sql_cobrado	= "AND (v.iddocumento_relacionado is null or v.iddocumento_relacionado = '' )";	 }

		if (empty($filtro_trabajador) || $filtro_trabajador == 'TODOS') {	} else {	$sql_filtro_trabajador	= "AND v.user_created = '$filtro_trabajador'";		}

		if ( !empty($fecha_i) && !empty($fecha_f) ) { $sql_fecha = "AND  DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') BETWEEN '$fecha_i' AND '$fecha_f'"; } 
    else if (!empty($fecha_i)) { $sql_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') = '$fecha_i'"; }
    else if (!empty($fecha_f)) { $sql_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d')= '$fecha_f'"; }		

		$sql = "SELECT count(*) as cantidad FROM venta as v
		where  v.tipo_comprobante in ('103') and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado = '1' and v.estado_delete = '1'
		$sql_cobrado $sql_fecha $sql_filtro_trabajador  ";
		$cant_comprobante = ejecutarConsultaSimpleFila($sql);	if ($cant_comprobante['status'] == false) {return $cant_comprobante;}

		$sql = "SELECT sum(vd.subtotal) as subtotal_venta, sum(vd.subtotal_compra) as subtotal_compra, (sum(vd.subtotal) -  sum(vd.subtotal_compra)) as utilidad
		FROM venta as v
		INNER JOIN venta_detalle as vd on vd.idventa = v.idventa
		where  v.tipo_comprobante in ('103') and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado = '1' and v.estado_delete = '1' 
		$sql_cobrado $sql_fecha $sql_filtro_trabajador ";
		$sum_totales = ejecutarConsultaSimpleFila($sql);	if ($sum_totales['status'] == false) {	return $sum_totales;	}

		$sql = "SELECT vd.pr_nombre AS nombre_producto, SUM(vd.cantidad_total) AS total_cantidad_vendida, SUM(vd.subtotal) AS total_vendido_soles
		FROM venta AS v
		INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
		WHERE v.tipo_comprobante = '103' AND v.sunat_estado IN ('ACEPTADA', 'POR ENVIAR') AND v.estado = '1' AND v.estado_delete = '1'		
		$sql_cobrado $sql_fecha $sql_filtro_trabajador 
		GROUP BY vd.pr_nombre
		ORDER BY total_vendido_soles DESC LIMIT 10;";
		$top_10_monto = ejecutarConsultaArray($sql);	if ($top_10_monto['status'] == false) {	return $top_10_monto;	}

		$sql = "SELECT vd.pr_nombre AS nombre_producto, SUM(vd.cantidad_total) AS total_cantidad_vendida, SUM(vd.subtotal) AS total_vendido_soles
		FROM venta AS v
		INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
		WHERE v.tipo_comprobante = '103' AND v.sunat_estado IN ('ACEPTADA', 'POR ENVIAR') AND v.estado = '1' AND v.estado_delete = '1'		
		$sql_cobrado $sql_fecha $sql_filtro_trabajador 
		GROUP BY vd.pr_nombre
		ORDER BY total_cantidad_vendida DESC LIMIT 10;";
		$top_10_cant = ejecutarConsultaArray($sql);	if ($top_10_cant['status'] == false) {	return $top_10_cant;	}

		$new_fi = empty($fecha_i) ? date('Y-m-d') : $fecha_i ;
		$new_ff = empty($fecha_f) ? date('Y-m-d') : $fecha_f ;
		$sql_temporalidad = "";

		if ($temporalidad == 'HORA') {
			$sql_temporalidad = "DATE_FORMAT(v.fecha_emision, '%d/%m %h:00') AS x_label,";
		}else if ($temporalidad == 'DIA') {
			$sql_temporalidad = "DATE_FORMAT(v.fecha_emision, '%d/%m') AS x_label,";
		}else if ($temporalidad == 'MES') {
			$sql_temporalidad = "CONCAT(ELT(MONTH(v.fecha_emision),
										'Ene','Feb','Mar','Abr','May','Jun',
										'Jul','Ago','Sep','Oct','Nov','Dic'),
								'-', DATE_FORMAT(v.fecha_emision, '%Y')) AS x_label,";
		}

		$sql = "SELECT $sql_temporalidad
			-- 👇 Elige qué sumar
			SUM(vd.subtotal) AS total_venta,		
			SUM(vd.subtotal_compra) AS total_compra,			
			(SUM(vd.subtotal) - SUM(vd.subtotal_compra)) AS utilidad			
		FROM venta AS v
		JOIN venta_detalle AS vd ON vd.idventa = v.idventa
		WHERE   DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') <= '$new_ff' AND  DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') >= '$new_fi'
			AND v.tipo_comprobante = '103'
			AND v.sunat_estado IN ('ACEPTADA', 'POR ENVIAR')
			AND v.estado = '1'
			AND v.estado_delete = '1'		
		$sql_cobrado $sql_filtro_trabajador 
		GROUP BY x_label
		ORDER BY v.fecha_emision, x_label ASC;	";
		$chart_barra = ejecutarConsultaArray($sql);	if ($chart_barra['status'] == false) {	return $chart_barra;	}

		$chart_venta = [];
		$chart_compra = [];
		$chart_utilidad = [];

		foreach ($chart_barra['data'] as $key => $val) {
			$chart_venta[] = ['x' => $val['x_label'], 'y' => $val['total_venta'] ];
		}
		foreach ($chart_barra['data'] as $key => $val) {
			$chart_compra[] = ['x' => $val['x_label'], 'y' => $val['total_compra'] ];
		}
		foreach ($chart_barra['data'] as $key => $val) {
			$chart_utilidad[] = ['x' => $val['x_label'], 'y' => $val['utilidad'] ];
		}

		$data = array(
			'cant_comprobante'		=> ( empty($cant_comprobante['data']) ? 0 : (empty($cant_comprobante['data']['cantidad']) 	? 0 : floatval($cant_comprobante['data']['cantidad']) ) ) ,
			'sum_subtotal_venta'  => ( empty($sum_totales['data']) 			? 0 : (empty($sum_totales['data']['subtotal_venta']) 	? 0 : floatval($sum_totales['data']['subtotal_venta']) ) ) ,
			'sum_subtotal_compra' => ( empty($sum_totales['data']) 			? 0 : (empty($sum_totales['data']['subtotal_compra']) ? 0 : floatval($sum_totales['data']['subtotal_compra']) ) ) ,
			'sum_utilidad'   			=> ( empty($sum_totales['data']) 			? 0 : (empty($sum_totales['data']['utilidad']) 				? 0 : floatval($sum_totales['data']['utilidad']) ) ) ,
			'top_10_monto'    		=> $top_10_monto['data'],
			'top_10_cant'     		=> $top_10_cant['data'],
			'chart_venta'     		=> $chart_venta,
			'chart_compra'     		=> $chart_compra,
			'chart_utilidad'     	=> $chart_utilidad,
		);

		return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' => $data, 'affected_rows' => 0,];
	}

	/**============================================================================ */
	/**============================================================================ */
	public function grafico_pay($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago, $filtro_tipo_comprob,$filtro_p_all_es_cobro)
	{
		//$dataarray  = array();
		$array_pay_total  = array();
		$array_pay_nombre  = array();

		$filtro_sql_trab  = '';
		$filtro_sql_ap  = '';
		$filtro_sql_mp  = '';
		$filtro_sql_tc  = '';
		$filtro_sql_trab_pend  = '';
		$filtro_sql_es_c = '';

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) 	   || $filtro_trabajador 	   == 'TODOS') {
		} else {
			$filtro_sql_trab	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
		}
		if (empty($filtro_anio_pago) 	   || $filtro_anio_pago 		 == 'TODOS') {
		} else {
			$filtro_sql_ap 	= "AND v.name_year             = '$filtro_anio_pago'";
		}
		if (empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
		} else {
			$filtro_sql_mp 		= "AND v.name_month            = '$filtro_p_all_mes_pago'";
		}
		if (empty($filtro_tipo_comprob)   || $filtro_tipo_comprob   == 'TODOS') {
		} else {
			$filtro_sql_tc 		= "AND v.tipo_comprobante      = '$filtro_tipo_comprob'";
		}
		if (empty($filtro_p_all_es_cobro)   || $filtro_p_all_es_cobro   == 'TODOS') {
		} else {
			$filtro_sql_es_c 		= "AND v.es_cobro      = '$filtro_p_all_es_cobro'";
		}

		// ------------------------ pendiente
		array_push($array_pay_nombre, "PENDIENTE");
		$total_comprob_tp = 0;

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab_pend = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) || $filtro_trabajador == 'TODOS' || empty($filtro_anio_pago) || $filtro_anio_pago == 'TODOS' || empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
			$total_comprob_tp = 0;
		} else {
			$filtro_sql_trab_pend	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
			$sql_pendiente = "SELECT SUM(pl.costo) as total_pendiente
					FROM persona_cliente AS pc
					LEFT JOIN venta AS v ON pc.idpersona_cliente = v.idpersona_cliente AND v.name_year='$filtro_anio_pago' AND v.name_month='$filtro_p_all_mes_pago'
					JOIN persona_trabajador as pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
					INNER JOIN plan AS pl ON pc.idplan = pl.idplan
					INNER JOIN persona as p ON pc.idpersona = p.idpersona
					INNER JOIN persona as p1 ON pt.idpersona = p1.idpersona
					INNER JOIN sunat_c06_doc_identidad as i on p.tipo_documento=i.code_sunat  
					WHERE v.idpersona_cliente IS NULL  $filtro_sql_trab_pend
					AND pc.estado='1' AND pc.estado_delete = '1';";

			$sqlpendiente = ejecutarConsultaSimpleFila($sql_pendiente);
			if ($sqlpendiente['status'] == false) {
				return $sqlpendiente;
			}

			$total_comprob_tp       = (empty($sqlpendiente['data']) ? 0 : (empty($sqlpendiente['data']['total_pendiente']) ? 0 : floatval($sqlpendiente['data']['total_pendiente'])));
		};

		// Modificamos la cantidad y el total a 0
		array_push($array_pay_total, $total_comprob_tp);


		$sql = "SELECT v.user_created,pu.nombre_razonsocial, SUM(vd.subtotal) as total 
		FROM venta as v
    INNER JOIN venta_detalle as vd on v.idventa = vd.idventa
		INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
		INNER JOIN persona_trabajador as pt on pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona as p2 on pt.idpersona = p2.idpersona
		INNER JOIN usuario as u on v.user_created = u.idusuario
		INNER JOIN persona as pu on u.idpersona = pu.idpersona
		WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado='1' and v.estado_delete ='1'  
		and vd.um_nombre='SERVICIOS' AND  v.tipo_comprobante !='07'
		 $filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
    GROUP by v.user_created,pu.nombre_razonsocial;";

		$totales  = ejecutarConsultaArray($sql);

		foreach ($totales['data'] as $key => $value) {
			array_push($array_pay_total, (empty($value['total']) ? 0 : floatval($value['total'])));
			array_push($array_pay_nombre, $value['nombre_razonsocial']);
		}

		return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' => ['series' => $array_pay_total, 'labels' => $array_pay_nombre],];
	}

	public function ventas_por_producto($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago, $filtro_tipo_comprob,$filtro_p_all_es_cobro)
	{
		$data = [];
		$filtro_sql_trab  = '';
		$filtro_sql_ap  = '';
		$filtro_sql_mp  = '';
		$filtro_sql_tc  = '';
		$filtro_sql_es_c  = '';
		$filtro_sql_trab_pend  = '';

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) 	  || $filtro_trabajador 	 	== 'TODOS') {
		} else {
			$filtro_sql_trab	= "AND pc.idpersona_trabajador = '$filtro_trabajador'";
		}
		if (empty($filtro_anio_pago) 	   	|| $filtro_anio_pago 		 	== 'TODOS') {
		} else {
			$filtro_sql_ap 		= "AND v.name_year             = '$filtro_anio_pago'";
		}
		if (empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago	== 'TODOS') {
		} else {
			$filtro_sql_mp 		= "AND v.name_month            = '$filtro_p_all_mes_pago'";
		}
		if (empty($filtro_tipo_comprob)   || $filtro_tipo_comprob   == 'TODOS') {
		} else {
			$filtro_sql_tc 		= "AND v.tipo_comprobante      = '$filtro_tipo_comprob'";
		}
		if (empty($filtro_p_all_es_cobro)   || $filtro_p_all_es_cobro   == 'TODOS') {
		} else {
			$filtro_sql_es_c 		= "AND v.es_cobro      = '$filtro_p_all_es_cobro'";
		}

		$sql_0 = "SELECT vd.idproducto, pro.nombre as nombre_producto, SUM(vd.cantidad) as cantidad, SUM(vd.subtotal) as subtotal
		FROM venta_detalle as vd
    INNER JOIN venta as v ON v.idventa = vd.idventa
		INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
		INNER JOIN producto as pro ON pro.idproducto = vd.idproducto
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado='1' and v.estado_delete ='1' 
		and v.tipo_comprobante !='07'
		$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
		GROUP BY vd.idproducto ORDER BY SUM(vd.cantidad) DESC;";
		$producto  = ejecutarConsultaArray($sql_0);

		foreach ($producto['data'] as $key => $val) {
			$id = $val['idproducto'];
			$sql_1 = "SELECT v.user_created, pu.nombre_razonsocial, pu.foto_perfil
			FROM venta as v
			INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
			INNER JOIN venta_detalle as vd on vd.idventa = v.idventa
			INNER JOIN usuario as u on v.user_created = u.idusuario
			INNER JOIN persona as pu on u.idpersona = pu.idpersona
			WHERE vd.idproducto = '$id' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and  v.estado='1' and v.estado_delete ='1' 
			and v.tipo_comprobante !='07'
			$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
			GROUP BY v.user_created, pu.nombre_razonsocial, pu.foto_perfil;";
			$user  = ejecutarConsultaArray($sql_1);
			$data[] = [
				'idproducto' => $val['idproducto'],
				'nombre_producto' => $val['nombre_producto'],
				'cantidad' => $val['cantidad'],
				'subtotal' => $val['subtotal'],
				'user' => $user['data'],
			];
		}

		return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' => $data,];
	}

	// ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════
	public function select2_filtro_trabajador()
	{
		$filtro_id_trabajador  = '';
		
		$sql = "SELECT DISTINCT LPAD(u.idusuario, 5, '0') as idusuario_formato, u.idusuario, p.nombre_razonsocial
		FROM venta as v 
		INNER JOIN usuario as u on u.idusuario = v.user_created
		INNER JOIN persona as p on p.idpersona = u.idpersona
		where v.tipo_comprobante = '103' 
		ORDER BY p.nombre_razonsocial;";
		return ejecutarConsulta($sql);
	}


	public function select2_filtro_anio_pago()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT DISTINCT v.name_year as anio_cancelacion
		FROM venta as v 
    INNER JOIN persona_cliente as pc on v.idpersona_cliente = pc.idpersona_cliente
		$filtro_id_trabajador
		ORDER BY v.name_year DESC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_mes_pago()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT DISTINCT v.name_month as mes_cancelacion
		FROM venta as v 
    INNER JOIN persona_cliente as pc on v.idpersona_cliente = pc.idpersona_cliente
		$filtro_id_trabajador
		ORDER BY v.name_month DESC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_tipo_comprob()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador =  "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT DISTINCT v.tipo_comprobante, tc.abreviatura
		FROM venta as v 
    INNER JOIN sunat_c01_tipo_comprobante as tc on v.tipo_comprobante = tc.codigo
    INNER JOIN persona_cliente as pc on v.idpersona_cliente = pc.idpersona_cliente
		where tc.codigo != '07'
    $filtro_id_trabajador
		ORDER BY tc.abreviatura DESC;";
		return ejecutarConsulta($sql);
	}
}
