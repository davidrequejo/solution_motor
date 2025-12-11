<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Cliente
{
	public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
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

	

	//Implementamos un método para insertar registros
	public function insertar_cliente(	$idtipo_persona, $idbancos, $idcargo_trabajador,  $tipo_persona_sunat,$tipo_documento, $numero_documento, 
	$nombre_razonsocial, $apellidos_nombrecomercial, $fecha_nacimiento, $celular, $direccion, $direccion_referencia, $distrito, 
	$provincia , $departamento, $ubigeo, $correo, $centro_poblado, $nota,  $img_perfil	) {
		
		$sql_0 = "SELECT p.*  FROM vw_persona_all as p WHERE p.numero_documento = '$numero_documento' AND '$numero_documento' <> '00000000'";
		$buscando = ejecutarConsultaArray($sql_0);		

		if ( empty($buscando['data']) || $tipo_documento == '0' ) {
			$sql1 = "INSERT INTO persona(idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, 
			apellidos_nombrecomercial, tipo_documento, numero_documento, fecha_nacimiento, celular, direccion, direccion_referencia, departamento, provincia, 
			distrito, cod_ubigeo, correo,foto_perfil) 
			VALUES ( '$idtipo_persona', '$idbancos', '$idcargo_trabajador', '$tipo_persona_sunat', '$nombre_razonsocial', 
			'$apellidos_nombrecomercial', '$tipo_documento', '$numero_documento', '$fecha_nacimiento', '$celular', '$direccion', '$direccion_referencia', '$departamento', '$provincia', 
			'$distrito', '$ubigeo', '$correo','$img_perfil')";
			$inst_persona = ejecutarConsulta_retornarID($sql1, 'C');if ($inst_persona['status'] == false) {return $inst_persona;}

			$id = $inst_persona['data'];

			$sql2 = "INSERT INTO persona_cliente(idpersona,idcentro_poblado, nota) 
			VALUES ('$id','$centro_poblado', '$nota')";
			$insertar =  ejecutarConsulta($sql2, 'C');	if ($inst_persona['status'] == false) {	return $inst_persona;	}

			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($buscando['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['cliente_nombre_completo'].'</span><br>
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

	//Implementamos un método para editar registros
	public function editar_cliente(	$idpersona, $idtipo_persona, $idbancos, $idcargo_trabajador,  $idpersona_cliente, 
	$tipo_persona_sunat,$tipo_documento, $numero_documento, $nombre_razonsocial, $apellidos_nombrecomercial, $fecha_nacimiento, $celular, $direccion, 
	$direccion_referencia, $distrito, $provincia , $departamento, $ubigeo, $correo, $centro_poblado, $nota,  $img_perfil) {

		$sql1 = "UPDATE persona SET 		
						idtipo_persona='$idtipo_persona',
						idbancos='$idbancos',
						idcargo_trabajador='$idcargo_trabajador',
						tipo_persona_sunat='$tipo_persona_sunat',
						nombre_razonsocial='$nombre_razonsocial',
						apellidos_nombrecomercial='$apellidos_nombrecomercial',
						tipo_documento='$tipo_documento',
						numero_documento='$numero_documento',
						fecha_nacimiento='$fecha_nacimiento',
						celular='$celular',
						direccion='$direccion',
						direccion_referencia='$direccion_referencia',
						departamento='$departamento',
						provincia='$provincia',
						distrito='$distrito',
						cod_ubigeo='$ubigeo',
						correo='$correo',		
						foto_perfil='$img_perfil'	
				WHERE idpersona='$idpersona';";
		$editar1 =  ejecutarConsulta($sql1, 'U');	if ($editar1['status'] == false) {return $editar1;}

		$null_cp = empty($centro_poblado) ? "null" : "$centro_poblado";
		$sql = "UPDATE persona_cliente SET idpersona ='$idpersona',idcentro_poblado=$null_cp, nota = '$nota'	WHERE idpersona_cliente='$idpersona_cliente';"; 		
		$editar =  ejecutarConsulta($sql, 'U');	if ($editar['status'] == false) {	return $editar;	}

		return $editar;
	}

	//Implementamos un método para desactivar color
	public function desactivar_cliente($idpersona_cliente, $descripcion)	{
		$sql = "UPDATE persona_cliente SET estado='0', nota ='$descripcion' WHERE idpersona_cliente='$idpersona_cliente'";
		$desactivar = ejecutarConsulta($sql, 'T');
		return $desactivar;
	}

	//Implementamos un método para desactivar color
	public function activar_cliente($idpersona_cliente, $descripcion)	{
		$sql = "UPDATE persona_cliente SET estado='1', nota ='$descripcion' WHERE idpersona_cliente='$idpersona_cliente'";
		$desactivar = ejecutarConsulta($sql, 'T');
		return $desactivar;
	}

	//Implementamos un método para eliminar persona_cliente
	public function eliminar_cliente($idpersona_cliente)	{
		$sql = "UPDATE persona_cliente SET estado_delete='0' WHERE idpersona_cliente='$idpersona_cliente'";
		$eliminar =  ejecutarConsulta($sql, 'D');		if ($eliminar['status'] == false) {	return $eliminar;	}
		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar_cliente($idpersona_cliente)	{
		$sql = "SELECT c.* FROM vw_cliente_all as c WHERE c.idpersona_cliente='$idpersona_cliente';";

		return ejecutarConsultaSimpleFila($sql);
	}

	public function cant_tab_cliente($filtro_tipo_persona, $filtro_centro_poblado)	{ 

		$filtro_sql_trab  = '';  $filtro_sql_tp  = ''; $filtro_sql_dp  = ''; $filtro_sql_p  = ''; $filtro_sql_cp  = ''; $filtro_sql_za  = '';

		if ( empty($filtro_tipo_persona) 		|| $filtro_tipo_persona 	== 'TODOS' ) { } else{	$filtro_sql_tp		= "AND pc.tipo_persona_sunat = '$filtro_tipo_persona'";	}
		if ( empty($filtro_centro_poblado)	|| $filtro_centro_poblado == 'TODOS' ) { } else{	$filtro_sql_cp 		= "AND pc.idcentro_poblado = '$filtro_centro_poblado'";	}

		$sq1 = "SELECT COUNT(DISTINCT CASE WHEN v.vc_estado = 'pendiente' AND v.venta_cuotas = 'SI' THEN c.idpersona_cliente END) AS clientes_con_deuda_pendiente,
		COUNT(DISTINCT CASE WHEN v.vc_estado = 'parcial' AND v.venta_cuotas = 'SI' THEN c.idpersona_cliente END) AS clientes_con_deuda_parcial,
		COUNT(DISTINCT CASE WHEN v.vc_estado = 'pagado' AND v.venta_cuotas = 'SI' THEN c.idpersona_cliente END) AS clientes_con_deuda_pagada,
		COUNT(DISTINCT c.idpersona_cliente) AS total_clientes
		FROM persona_cliente AS c
		LEFT JOIN venta AS v ON c.idpersona_cliente = v.idpersona_cliente AND v.estado = '1' AND v.estado_delete = '1'
		where c.estado = '1' AND c.estado_delete = '1';";
		$count_total = ejecutarConsultaSimpleFila($sq1); if ($count_total['status'] == false) {return $count_total; }

		return [
			'status' => true, 'message' => 'Todo ok', 
			'data' => [
				'count_sinpagos'=> floatval($count_total['data']['clientes_con_deuda_pendiente']),
				'count_pagos_parciales'=> floatval($count_total['data']['clientes_con_deuda_parcial']),
				'count_con_pagostotal'	=> floatval($count_total['data']['clientes_con_deuda_pagada']),				
				'count_total'	=> floatval($count_total['data']['total_clientes']),				
			]
		];
	}

	//Implementar un método para listar los registros
	public function tabla_principal_cliente($filtro_tipo_persona, $filtro_centro_poblado)	{

		$filtro_sql_tp  = '';  $filtro_sql_cp  = ''; 
		
		if ( empty($filtro_tipo_persona) 	|| $filtro_tipo_persona 	== 'TODOS' ) { } else{	$filtro_sql_tp		= "AND vw_c.tipo_persona_sunat = '$filtro_tipo_persona'";	}
		if ( empty($filtro_centro_poblado)|| $filtro_centro_poblado == 'TODOS' ) { } else{	$filtro_sql_cp 		= "AND vw_c.idcentro_poblado = '$filtro_centro_poblado'";	}

		$sql = "SELECT vw_c.*	FROM vw_cliente_all as vw_c		
		where  vw_c.estado_delete_pc='1' $filtro_sql_tp  $filtro_sql_cp 
		ORDER BY vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
	}

	public function tabla_principal_cliente_deudor(  $filtro_tipo_persona,  $filtro_centro_poblado )	{

		$filtro_sql_tp  = '';  $filtro_sql_cp  = ''; 	
	
		if ( empty($filtro_tipo_persona) 	|| $filtro_tipo_persona 	== 'TODOS' ) { } else{	$filtro_sql_tp		= "AND vw_c.tipo_persona_sunat = '$filtro_tipo_persona'";	}		
		if ( empty($filtro_centro_poblado)|| $filtro_centro_poblado == 'TODOS' ) { } else{	$filtro_sql_cp 		= "AND vw_c.idcentro_poblado = '$filtro_centro_poblado'";	}
		
		$sql = "SELECT 	 vw_rc.avance, vw_rc.avance_v2, vw_rc.vc_cuota_total, vw_rc.vc_cuota_total_cobrado, vw_c.*,
		CASE WHEN LENGTH(  vw_c.cliente_nombre_completo  ) <= 22 THEN  vw_c.cliente_nombre_completo ELSE CONCAT(  LEFT(vw_c.cliente_nombre_completo, 22) , '...') END  as cliente_nombre_completo_recorte
		FROM vw_cliente_all as vw_c		
		inner join vw_retraso_cobro_cliente as vw_rc on vw_rc.idpersona_cliente = vw_c.idpersona_cliente
		where vw_c.estado_pc = '1' and vw_c.estado_delete_pc='1' and vw_rc.categoria_deuda = 'SIN PAGOS'  
		$filtro_sql_tp   $filtro_sql_cp 
		ORDER BY vw_rc.avance DESC, vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
	}

	public function tabla_principal_cliente_deudor_parcial(  $filtro_tipo_persona,  $filtro_centro_poblado )	{

		$filtro_sql_tp  = '';  $filtro_sql_cp  = '';

		if ( empty($filtro_tipo_persona) 	|| $filtro_tipo_persona 	== 'TODOS' ) { } else{	$filtro_sql_tp		= "AND vw_c.tipo_persona_sunat = '$filtro_tipo_persona'";	}
		if ( empty($filtro_centro_poblado)|| $filtro_centro_poblado == 'TODOS' ) { } else{	$filtro_sql_cp 		= "AND vw_c.idcentro_poblado = '$filtro_centro_poblado'";	}
		$sql = "SELECT 	 vw_rc.avance, vw_rc.avance_v2, vw_rc.vc_cuota_total, vw_rc.vc_cuota_total_cobrado, vw_c.*,
		CASE WHEN LENGTH(  vw_c.cliente_nombre_completo  ) <= 22 THEN  vw_c.cliente_nombre_completo ELSE CONCAT(  LEFT(vw_c.cliente_nombre_completo, 22) , '...') END  as cliente_nombre_completo_recorte
		FROM vw_cliente_all as vw_c		
		inner join vw_retraso_cobro_cliente as vw_rc on vw_rc.idpersona_cliente = vw_c.idpersona_cliente
		where vw_c.estado_pc = '1' and vw_c.estado_delete_pc='1' and vw_rc.categoria_deuda in ('PARCIAL')
		 $filtro_sql_tp $filtro_sql_cp 
		ORDER BY vw_rc.avance , vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
	}

	public function tabla_principal_cliente_no_deuda(  $filtro_tipo_persona,  $filtro_centro_poblado )	{

		  $filtro_sql_tp  = '';  $filtro_sql_cp  = ''; 

		if ( empty($filtro_tipo_persona) 	|| $filtro_tipo_persona 	== 'TODOS' ) { } else{	$filtro_sql_tp		= "AND vw_c.tipo_persona_sunat = '$filtro_tipo_persona'";	}
		if ( empty($filtro_centro_poblado)|| $filtro_centro_poblado == 'TODOS' ) { } else{	$filtro_sql_cp 		= "AND vw_c.idcentro_poblado = '$filtro_centro_poblado'";	}
		
		$sql = "SELECT 	 vw_rc.avance, vw_rc.avance_v2, vw_rc.vc_cuota_total, vw_rc.vc_cuota_total_cobrado, vw_c.*,
		CASE WHEN LENGTH(  vw_c.cliente_nombre_completo  ) <= 22 THEN  vw_c.cliente_nombre_completo ELSE CONCAT(  LEFT(vw_c.cliente_nombre_completo, 22) , '...') END  as cliente_nombre_completo_recorte
		FROM vw_cliente_all as vw_c		
		inner join vw_retraso_cobro_cliente as vw_rc on vw_rc.idpersona_cliente = vw_c.idpersona_cliente
		where vw_c.estado_pc = '1' and vw_c.estado_delete_pc='1' and vw_rc.categoria_deuda in ( 'PAGADO')
		$filtro_sql_tp  $filtro_sql_cp
		ORDER BY vw_rc.avance , vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
	}

	// ══════════════════════════════════════   DETALLE CLIENTE DEUDOR   ══════════════════════════════════════ 
	public function detalle_cliente_deudor( $idpersona_cliente )	{
		
		
		$sql = "SELECT 	 vw_rc.avance, vw_rc.avance_v2, vw_rc.vc_cuota_total, vw_rc.vc_cuota_total_cobrado, vw_c.*,
		CASE WHEN LENGTH(  vw_c.cliente_nombre_completo  ) <= 22 THEN  vw_c.cliente_nombre_completo ELSE CONCAT(  LEFT(vw_c.cliente_nombre_completo, 22) , '...') END  as cliente_nombre_completo_recorte
		FROM vw_cliente_all as vw_c		
		inner join vw_retraso_cobro_cliente as vw_rc on vw_rc.idpersona_cliente = vw_c.idpersona_cliente
		where vw_c.idpersona_cliente = '$idpersona_cliente'";
		$cliente = ejecutarConsultaSimpleFila($sql); if ($cliente['status'] == false) {return $cliente; }

		$sql2 = "call sp_mes_cobrado_cliente($idpersona_cliente)";
		// $cliente_mes = ejecutarConsultaArray($sql2); if ($cliente_mes['status'] == false) {return $cliente_mes; }
		$cliente_mes = [ 'data' =>  []];
		return [
			'status' => true, 'message' => 'Todo ok', 
			'data' => [
				'cliente'=> $cliente['data'],
				'cliente_mes'=> $cliente_mes['data'],				
			]
		];
	}

	// ══════════════════════════════════════  PAGOS POR CLIENTES ══════════════════════════════════════

	public function ver_pagos_x_cliente($idcliente)	{		
		$sql_0 = "SELECT 
		pc.idpersona_cliente, LPAD(pc.idpersona_cliente, 5, '0') as idcliente, pc.idpersona_trabajador, pc.idzona_antena, pc.idplan , pc.ip_personal, DAY(pc.fecha_cancelacion) AS dia_cancelacion, 
		pc.fecha_cancelacion, DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y') AS fecha_cancelacion_format, 	pc.fecha_afiliacion, pc.descuento,pc.estado_descuento,
		cp.nombre as centro_poblado, pc.nota, pc.usuario_microtick, COUNT(DISTINCT vd.periodo_pago_year) AS total_anios_pago,
		CASE 
			WHEN pc.fecha_cancelacion  > CURDATE() THEN DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y')
			ELSE CONCAT( DATE_FORMAT(pc.fecha_cancelacion, '%d'), ' de cada mes' )
		END AS dia_cancelacion_v2,
		CASE 
			WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
			WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
			ELSE '-'
		END AS cliente_nombre_completo, 
		p.tipo_documento, p.numero_documento, p.celular, p.foto_perfil, p.direccion,p.distrito,p1.nombre_razonsocial AS trabajador_nombre, 
		pl.nombre as nombre_plan,pl.costo,za.nombre as zona, za.ip_antena,pc.estado, i.abreviatura as tipo_doc
		FROM persona_cliente as pc
		INNER JOIN persona AS p on pc.idpersona=p.idpersona
		INNER JOIN persona_trabajador AS pt on pc.idpersona_trabajador= pt.idpersona_trabajador
		INNER JOIN persona as p1 on pt.idpersona=p1.idpersona
		INNER JOIN plan as pl on pc.idplan=pl.idplan
		INNER JOIN zona_antena as za on pc.idzona_antena=za.idzona_antena
		INNER JOIN sunat_c06_doc_identidad as i on p.tipo_documento=i.code_sunat  
		INNER JOIN centro_poblado as cp on pc.idcentro_poblado=cp.idcentro_poblado  
		LEFT JOIN venta as v on pc.idpersona_cliente = v.idpersona_cliente
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
		where pc.estado_delete='1' AND pc.idpersona_cliente = '$idcliente'
		ORDER BY pc.idpersona_cliente DESC";
		$cliente = ejecutarConsultaSimpleFila($sql_0); if ($cliente['status'] == false) {return $cliente; }

		$sql_1 = "SELECT pc.idpersona_cliente, vd.periodo_pago_year,
		SUM(CASE WHEN vd.periodo_pago_month = 'Enero'  AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_enero,
		SUM(CASE WHEN vd.periodo_pago_month = 'Febrero' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_febrero,
		SUM(CASE WHEN vd.periodo_pago_month = 'Marzo' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_marzo,
		SUM(CASE WHEN vd.periodo_pago_month = 'Abril' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_abril,
		SUM(CASE WHEN vd.periodo_pago_month = 'Mayo' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_mayo,
		SUM(CASE WHEN vd.periodo_pago_month = 'Junio' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_junio,
		SUM(CASE WHEN vd.periodo_pago_month = 'Julio' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_julio,
		SUM(CASE WHEN vd.periodo_pago_month = 'Agosto' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_agosto,
		SUM(CASE WHEN vd.periodo_pago_month = 'Septiembre' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_septiembre,
		SUM(CASE WHEN vd.periodo_pago_month = 'Octubre' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_octubre,
		SUM(CASE WHEN vd.periodo_pago_month = 'Noviembre' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_noviembre,
		SUM(CASE WHEN vd.periodo_pago_month = 'Diciembre' AND vd.es_cobro = 'SI' THEN vd.subtotal ELSE null END) AS venta_diciembre

		FROM persona_cliente as pc
		INNER JOIN venta AS v ON pc.idpersona_cliente = v.idpersona_cliente 
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
		where v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') AND v.estado_delete='1' AND v.estado_delete='1' and pc.estado_delete='1' AND pc.idpersona_cliente = '$idcliente'
		GROUP BY vd.periodo_pago_year
		ORDER BY vd.periodo_pago_year DESC";
		$pagos = ejecutarConsulta($sql_1); if ($pagos['status'] == false) {return $pagos; }


		return ['status' => true, 'message' => 'Todo ok', 'data' => ['cliente' => $cliente['data'], 'pagos' => $pagos['data']]];
	}

	// ══════════════════════════════════════  PAGOS ALL CLIENTES ══════════════════════════════════════

	public function ver_pagos_all_cliente($filtro_trabajador, $filtro_dia_pago, $filtro_anio_pago, $filtro_plan, $filtro_zona_antena)	{

		$filtro_sql_trab  = ''; $filtro_sql_dp  = ''; $filtro_sql_ap = ''; $filtro_sql_p  = ''; $filtro_sql_za  = '';

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') { $filtro_sql_trab = "AND vw_c.idpersona_trabajador = '$this->id_trabajador_sesion'";	}

		if ( empty($filtro_trabajador) 	|| $filtro_trabajador 	== 'TODOS' ) { } else{	$filtro_sql_trab	= "AND vw_c.idpersona_trabajador = '$filtro_trabajador'";	}
		if ( empty($filtro_dia_pago) 		|| $filtro_dia_pago 		== 'TODOS' ) { } else{ 	$filtro_sql_dp 		= "AND DAY(vw_c.fecha_cancelacion)  = '$filtro_dia_pago'";	}
		if ( empty($filtro_anio_pago) 	|| $filtro_anio_pago    == 'TODOS' ) { } else{ 	$filtro_sql_ap 		= "AND YEAR(vd.periodo_pago_format)  = '$filtro_anio_pago'";	}
		if ( empty($filtro_plan) 				|| $filtro_plan 				== 'TODOS' ) { } else{	$filtro_sql_p 		= "AND vw_c.idplan = '$filtro_plan'";	}
		if ( empty($filtro_zona_antena) || $filtro_zona_antena 	== 'TODOS' ) { } else{	$filtro_sql_za 		= "AND vw_c.idzona_antena = '$filtro_zona_antena'";	}
		
		$sql = "SELECT vw_c.idpersona_cliente, vw_c.idpersona_cliente_v2, vw_c.idpersona_trabajador, vw_c.idzona_antena, vw_c.ip_personal, vw_c.dia_cancelacion, 
		vw_c.fecha_cancelacion, vw_c.fecha_cancelacion_format, vw_c.fecha_afiliacion, vw_c.descuento,vw_c.estado_descuento, vw_c.centro_poblado, vw_c.nota, 
		vw_c.usuario_microtick,	vw_c.dia_cancelacion_v2, vw_c.cliente_nombre_completo, vw_c.tipo_documento, vw_c.numero_documento, vw_c.celular, vw_c.foto_perfil, 
		vw_c.direccion,vw_c.distrito,	vw_c.trabajador_nombre, vw_c.nombre_plan,	vw_c.costo,vw_c.zona, vw_c.ip_antena,vw_c.estado_pc, vw_c.tipo_documento_abrev_nombre, 
		vw_c.cliente_primera_letra, vw_c.cliente_tiene_pefil,
		ven.periodo_pago_year, ven.periodo_pago,
		SUM(CASE WHEN ven.periodo_pago_month = 'Enero'  AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_enero,
		SUM(CASE WHEN ven.periodo_pago_month = 'Febrero' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_febrero,
		SUM(CASE WHEN ven.periodo_pago_month = 'Marzo' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_marzo,
		SUM(CASE WHEN ven.periodo_pago_month = 'Abril' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_abril,
		SUM(CASE WHEN ven.periodo_pago_month = 'Mayo' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_mayo,
		SUM(CASE WHEN ven.periodo_pago_month = 'Junio' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_junio,
		SUM(CASE WHEN ven.periodo_pago_month = 'Julio' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_julio,
		SUM(CASE WHEN ven.periodo_pago_month = 'Agosto' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_agosto,
		SUM(CASE WHEN ven.periodo_pago_month = 'Septiembre' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_septiembre,
		SUM(CASE WHEN ven.periodo_pago_month = 'Octubre' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_octubre,
		SUM(CASE WHEN ven.periodo_pago_month = 'Noviembre' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_noviembre,
		SUM(CASE WHEN ven.periodo_pago_month = 'Diciembre' AND ven.es_cobro = 'SI' THEN ven.subtotal ELSE null END) AS venta_diciembre
			
		FROM vw_cliente_all AS vw_c		
		LEFT JOIN (
			SELECT v.idpersona_cliente, vd.periodo_pago, vd.periodo_pago_format, vd.periodo_pago_year, vd.periodo_pago_month, vd.subtotal, vd.es_cobro
			FROM venta AS v 
			INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
			WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01','03','12') AND v.estado_delete='1' AND v.estado_delete='1' $filtro_sql_ap
		) as ven on ven.idpersona_cliente = vw_c.idpersona_cliente
		WHERE vw_c.estado_delete_pc='1' $filtro_sql_trab $filtro_sql_dp $filtro_sql_p $filtro_sql_za
		GROUP BY vw_c.idpersona_cliente
		ORDER BY vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
		// return $sql;
	}

	// ══════════════════════════════════════  PAGOS POR MES  ══════════════════════════════════════
	public function pago_cliente_x_mes($id, $mes, $filtroA, $filtroB, $filtroC, $filtroD, $filtroE){

		$filtro_sql_trab  = ''; $filtro_sql_dp  = ''; $filtro_sql_ap = ''; $filtro_sql_p  = ''; $filtro_sql_za  = '';

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') { $filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";	}
		if ( empty($filtroA)	|| $filtroA	== 'TODOS' ) { } else{	$filtro_sql_trab	= "AND pt.idpersona_trabajador = '$filtroA'";	}
		if ( empty($filtroB) 	|| $filtroB == 'TODOS' ) { } else{ 	$filtro_sql_dp 		= "AND DAY(pc.fecha_cancelacion) = '$filtroB'";	}
		if ( empty($filtroC) 	|| $filtroC == 'TODOS' ) { } else{ 	$filtro_sql_ap 		= "AND YEAR(vd.periodo_pago_format) = '$filtroC'";	}
		if ( empty($filtroD) 	|| $filtroD == 'TODOS' ) { } else{	$filtro_sql_p 		= "AND pc.idplan = '$filtroD'";	}
		if ( empty($filtroE)  || $filtroE == 'TODOS' ) { } else{	$filtro_sql_za 		= "AND pc.idzona_antena = '$filtroE'";	}

		$sql = "SELECT LPAD(v.idventa, 5, '0') as idventa_v2, v.idventa, DATE_FORMAT(v.fecha_emision, '%d/%m/%Y') AS fecha_emision, DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y') AS fecha_cancelacion, 
			CONCAT(v.serie_comprobante, '-', v.numero_comprobante) AS SNCompb, v.venta_total,
			CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2,
			CONCAT(v.periodo_pago_month, '-', v.periodo_pago_year) AS periodo_pago,
			v.tipo_comprobante, v.sunat_estado, v.estado, v.estado_delete, v.observacion_documento,
			GROUP_CONCAT( CASE vd.es_cobro WHEN 'SI' THEN CONCAT( LEFT(vd.periodo_pago_month, 3), '-',  vd.periodo_pago_year, ',<br>') ELSE '' END SEPARATOR ' ') AS periodo_pago_mes_anio
		FROM venta v
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
			INNER JOIN persona_cliente as pc ON v.idpersona_cliente = pc.idpersona_cliente
			INNER JOIN persona_trabajador AS pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
			INNER JOIN persona AS p1 ON pc.idpersona = p1.idpersona
			INNER JOIN persona AS p2 ON pt.idpersona = p2.idpersona
		WHERE v.estado_delete = '1' AND  v.idpersona_cliente = '$id'
		AND vd.periodo_pago = '$mes' $filtro_sql_trab $filtro_sql_dp $filtro_sql_ap $filtro_sql_p $filtro_sql_za
		GROUP BY v.idventa";
		return ejecutarConsulta($sql);

	}

	// ══════════════════════════════════════ R E A L I Z A R  P A G O   C L I E N T E  ══════════════════════════════════════ 

	public function listar_producto_x_precio($precio){
		$sql = "SELECT p.*, um.nombre AS unidad_medida, um.abreviatura as um_abreviatura, cat.nombre AS categoria, mc.nombre AS marca
		FROM producto AS p
		INNER JOIN sunat_unidad_medida AS um ON p.idsunat_unidad_medida = um.idsunat_unidad_medida
		INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
		INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
		WHERE p.precio_venta = '$precio' and p.nombre like '%INTERNET%' ;";
			return ejecutarConsultaSimpleFila($sql);
		
	}

	// ══════════════════════════════════════  IMPRIMIR PAGOS  ══════════════════════════════════════

	public function imprimirTicket_pagoCliente($id){
		$sql_1 = "SELECT v.*, CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante, DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_format, 
      v.estado, p.idpersona, pc.idpersona_cliente, p.nombre_razonsocial, p.apellidos_nombrecomercial, 
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo, 
      p.tipo_documento, p.numero_documento, p.direccion, 
      tc.abreviatura as nombre_comprobante, sdi.abreviatura as nombre_tipo_documento,
      pu.nombre_razonsocial as user_en_atencion
      FROM venta AS v
      INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN persona AS p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = v.tipo_comprobante
      LEFT JOIN usuario as u ON u.idusuario = v.user_created
      LEFT JOIN persona as pu ON pu.idpersona = u.idpersona
      WHERE v.idventa = '$id'";
      $venta = ejecutarConsultaSimpleFila($sql_1); if ($venta['status'] == false) {return $venta; }


      $sql_2 = "SELECT vc.*, p.idproducto, p.idsunat_unidad_medida, p.idcategoria, p.idmarca, p.nombre as nombre_producto, p.codigo, p.codigo_alterno, p.imagen, 
      um.nombre AS unidad_medida, um.abreviatura as um_abreviatura, cat.nombre AS categoria, mc.nombre AS marca
      FROM venta_detalle AS vc
      INNER JOIN producto AS p ON p.idproducto = vc.idproducto
      INNER JOIN sunat_unidad_medida AS um ON p.idsunat_unidad_medida = um.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE vc.idventa = '$id';";
      $detalle = ejecutarConsultaArray($sql_2); if ($detalle['status'] == false) {return $detalle; }

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => ['venta' => $venta['data'], 'detalle' => $detalle['data']]];

	}

	public function empresa(){
		$sql = "SELECT * FROM empresa WHERE numero_documento = '20610630431'";
		return ejecutarConsultaSimpleFila($sql);
	}

	// ══════════════════════════════════════ RESUMEN VENTA PRODUCTO  ══════════════════════════════════════ 

	public function resumen_producto_venta($id_cliente){
		$sql = "SELECT  vd.pr_nombre, SUM(vd.cantidad) as cant_vendida, AVG(vd.precio_venta) as precio_venta_promedio
		FROM venta_detalle as vd 
		INNER JOIN venta as v on v.idventa = vd.idventa 
		INNER JOIN sunat_c01_tipo_comprobante AS sc01 ON sc01.idtipo_comprobante = v.idsunat_c01
		WHERE  v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado IN ( 'ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) and v.idpersona_cliente = '$id_cliente'
		GROUP BY vd.pr_nombre";
		return ejecutarConsultaArray($sql);
	}

	public function resumen_producto_venta_todos($id_cliente){
		$sql = "SELECT CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE sc01.abreviatura END AS tp_comprobante_v2, v.serie_comprobante, v.numero_comprobante,
		DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format,  vd.* FROM venta_detalle as vd 
		INNER JOIN venta as v on v.idventa = vd.idventa 
		INNER JOIN sunat_c01_tipo_comprobante AS sc01 ON sc01.idtipo_comprobante = v.idsunat_c01
		WHERE  v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado IN ( 'ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) and v.idpersona_cliente = '$id_cliente'
		ORDER BY vd.pr_nombre";
		return ejecutarConsultaArray($sql);
	}



	// ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════
	public function select2_filtro_trabajador()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			// $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT LPAD(pt.idpersona_trabajador, 5, '0') as idtrabajador, pt.idpersona_trabajador, pt.idpersona,  per_t.nombre_razonsocial
		FROM  persona_trabajador as pt 
		INNER JOIN persona as per_t ON per_t.idpersona = pt.idpersona
		WHERE pt.estado_delete = '1' $filtro_id_trabajador		
		ORDER BY  per_t.nombre_razonsocial asc;";
		return ejecutarConsulta($sql);
	}
	
	public function select2_filtro_dia_pago()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "WHERE pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT DAY(pc.fecha_cancelacion) as dia_cancelacion, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM persona_cliente as pc $filtro_id_trabajador
		GROUP BY DAY(pc.fecha_cancelacion)
		ORDER BY DAY(pc.fecha_cancelacion) ASC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_anio_pago()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT YEAR(vd.periodo_pago_format) as anio_cancelacion, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM venta as v
		INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
		INNER JOIN persona_cliente as pc on pc.idpersona_cliente = v.idpersona_cliente
		where v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) $filtro_id_trabajador
		GROUP BY YEAR(vd.periodo_pago_format)
		ORDER BY YEAR(vd.periodo_pago_format) DESC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_plan()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT pl.idplan, pl.nombre, pl.costo, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM persona_cliente as pc
		INNER JOIN plan as pl ON pl.idplan = pc.idplan
		WHERE pl.estado = '1' and pl.estado_delete = '1' $filtro_id_trabajador
		GROUP BY pc.idplan ORDER BY COUNT(idpersona_cliente) desc, pl.nombre asc;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_zona_antena()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT za.idzona_antena, za.nombre, za.ip_antena, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM persona_cliente as pc
		INNER JOIN zona_antena as za ON za.idzona_antena = pc.idzona_antena
		WHERE za.estado = '1' and za.estado_delete = '1' $filtro_id_trabajador
		GROUP BY pc.idzona_antena ORDER BY COUNT(idpersona_cliente) desc, za.nombre asc;";
		return ejecutarConsulta($sql);
	}
	
	public function select2_plan()	{
		$sql = "SELECT idplan, nombre, costo FROM plan WHERE estado='1' and estado_delete='1';";
		return ejecutarConsulta($sql);
	}

	public function select2_zona_antena()	{
		$sql = "SELECT idzona_antena, nombre, ip_antena FROM zona_antena WHERE estado='1' and estado_delete='1';";
		return ejecutarConsulta($sql);
	}

	public function select2_trabajador(){
		$sql = "SELECT pt.idpersona_trabajador, p.idpersona, 
		CASE 
		WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
		WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
		ELSE '-'
		END AS nombre_completo, 
		p.tipo_documento, p.numero_documento 
		FROM persona_trabajador pt 
		INNER JOIN persona AS p ON pt.idpersona = p.idpersona 
		WHERE pt.estado = '1' AND pt.estado_delete = '1' AND p.idtipo_persona = '2';";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function perfil_trabajador($id)	{
		$sql = "SELECT p.foto_perfil	FROM persona as p WHERE p.idpersona = '$id' ;";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function selec_centroProbl(){
		$sql="SELECT idcentro_poblado, nombre FROM centro_poblado WHERE estado='1' and estado_delete='1';";
		return ejecutarConsulta($sql);
	}
}
