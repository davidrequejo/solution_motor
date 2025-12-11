<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Reporte_contador
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
	public function tabla_reporte_detalle( $fecha_i, $fecha_f, $cliente, $tipo_persona, $centro_poblado, $estado_sunat, $comprobante ) {    


		$filtro_fecha = ""; $filtro_cliente = ""; $filtro_tipo_persona = ""; $filtro_comprobante = "";  $filtro_centro_poblado = ""; $filtro_estado_sunat = "";

		if (is_array($comprobante) && count($comprobante) > 0) {
			// Filtrar valores vacíos y sanitizar (solo valores alfanuméricos, guiones, etc)
			$comprobante = array_filter($comprobante, function ($v) {
				return preg_match('/^[a-zA-Z0-9-_]+$/', $v);
			});

			if (count($comprobante) > 0) {
				$comprobante_d = "'" . implode("','", array_map('addslashes', $comprobante)) . "'";
				$filtro_comprobante = "AND idsunat_c01 IN ($comprobante_d)";
			}
		}

		if (is_array($estado_sunat) && count($estado_sunat) > 0) {
			$estado_clean = [];

			foreach ($estado_sunat as $val) {
				$val = trim($val);
				if ($val === 'SIN ESTADO') {
					$estado_clean[] = "NULL";
					$estado_clean[] = "''";
				} elseif (preg_match('/^[a-zA-Z0-9\s\-_]+$/', $val)) {
					$estado_clean[] = "'" . addslashes($val) . "'";
				}
			}

			if (count($estado_clean) > 0) {
				$filtro_estado_sunat = "AND sunat_estado IN (" . implode(',', $estado_clean) . ")";
			}
		}

		if ( !empty($fecha_i) && !empty($fecha_f) ) { $filtro_fecha = "AND fecha_emision_format BETWEEN '$fecha_i' AND '$fecha_f'"; } 
		else if (!empty($fecha_i)) { $filtro_fecha = "AND fecha_emision_format = '$fecha_i'"; }
		else if (!empty($fecha_f)) { $filtro_fecha = "AND fecha_emision_format = '$fecha_f'"; }
		
		if ( empty($cliente) ) { } else {  $filtro_cliente = "AND idpersona_cliente = '$cliente'"; } 
		if ( empty($tipo_persona) ) { } else {  $filtro_tipo_persona = "AND tipo_persona_sunat = '$tipo_persona'"; } 
		if ( empty($centro_poblado) ) { } else {  $filtro_centro_poblado = "AND vw_f.idcentro_poblado = '$centro_poblado'"; } 

		

		$sql = "SELECT idventa_v2, name_day, fecha_emision_dmy, fecha_emision_format AS fecha_emision, fecha_emision_hora12 as hora_emision, CONCAT(name_month,'-', name_year) as periodo, cliente_nombre_completo, tipo_documento_abreviatura, 
		numero_documento, tipo_comprobante, tipo_comprobante_v2, serie_y_numero_comprobante, venta_total_v2, total_recibido, total_vuelto, documento_relacionado, metodos_pago_agrupado, 
		CONCAT(user_created_v2,' ', user_en_atencion) as user_en_atencion, sunat_estado
		FROM vw_facturacion WHERE estado_v = 1 AND estado_delete_v = 1 
		$filtro_cliente $filtro_tipo_persona $filtro_comprobante $filtro_centro_poblado $filtro_estado_sunat $filtro_fecha      
		ORDER BY fecha_emision DESC, nombre_razonsocial ASC;"; //return $sql;
		$venta = ejecutarConsultaArray($sql); if ($venta['status'] == false) {return $venta; }

		return $venta;
	}


	/* ════════════════════════════════════════════════════════════════════════════
		═══															S E L E C T 2
	/* ════════════════════════════════════════════════════════════════════════════ */

	public function select2_filtro_trabajador()
	{
		$filtro_id_trabajador  = '';
		
		$sql = "SELECT DISTINCT LPAD(u.idusuario, 5, '0') as idusuario_formato, u.idusuario, p.nombre_razonsocial
		FROM venta as v 
		INNER JOIN usuario as u on u.idusuario = v.user_created
		INNER JOIN persona as p on p.idpersona = u.idpersona
		ORDER BY p.nombre_razonsocial;";
		return ejecutarConsulta($sql);
	}


	public function select2_centro_poblado()
	{
		$filtro_id_trabajador  = '';
		
		$sql = "SELECT DISTINCT cp.idcentro_poblado, LPAD(cp.idcentro_poblado, 5, '0') as idcentro_poblado_v2, cp.nombre
		FROM venta as v
		INNER JOIN persona_cliente as pc on pc.idpersona_cliente = v.idpersona_cliente
		INNER JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado";
		return ejecutarConsulta($sql);
	}

	public function select2_estado_sunat()
	{
		$filtro_id_trabajador  = '';
		
		$sql = "SELECT DISTINCT CASE WHEN v.sunat_estado IS NULL or v.sunat_estado = '' THEN 'SIN ESTADO' ELSE v.sunat_estado END AS sunat_estado FROM venta as v";
		return ejecutarConsulta($sql);
	}

}
