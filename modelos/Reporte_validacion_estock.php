<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Reporte_validacion_estock
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
	public function tabla_resumen_stock( $filtro_copia_stock, $filtro_fecha_copia_stock, $filtro_estado_stock)
	{

		$sql_copia_stock  = ''; $sql_fecha_copia_stock  = '';	$sql_estado_stock  = '';	
		$CSV_filtro = 'left';

		if (empty($filtro_copia_stock) ) {	} else {	$sql_copia_stock	= "AND csp.codigo_stock = '$filtro_copia_stock'";		}		
    if (empty($filtro_fecha_copia_stock)) {	} else { $sql_fecha_copia_stock = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') = '$filtro_fecha_copia_stock'"; } 

    if (empty($filtro_estado_stock)) {	} 
		else if ($filtro_estado_stock == 'TODOS' )  {  }    
		else if ($filtro_estado_stock == 'CMa0' )  { $sql_estado_stock = "AND sk.copia_stock > 0"; }    
		else if ($filtro_estado_stock == 'AMa0' )  { $sql_estado_stock = "AND sk.actual_stock > 0"; }    
		else if ($filtro_estado_stock == 'CAMa0' )  { $sql_estado_stock = "AND sk.diff_copia_actual > 0"; }    
		else if ($filtro_estado_stock == 'CAMe0' )  { $sql_estado_stock = "AND sk.diff_copia_actual < 0"; }    
		else if ($filtro_estado_stock == 'CAD' )  { $sql_estado_stock = "AND sk.diff_copia_actual <> 0"; }    
		else if ($filtro_estado_stock == 'VSV' )  { $CSV_filtro = 'inner';  }    
		else if ($filtro_estado_stock == 'SVD' )  { $sql_estado_stock = "AND COALESCE((diff_copia_actual - vk.venta_total), 0) <> 0"; }    
		else if ($filtro_estado_stock == 'SVMa0' )  { $sql_estado_stock = "AND COALESCE((diff_copia_actual - vk.venta_total), 0) > 0"; }    
		else if ($filtro_estado_stock == 'SVMe0' )  { $sql_estado_stock = "AND COALESCE((diff_copia_actual - vk.venta_total), 0) < 0"; }    

		$sql = "SELECT p.codigo_alterno, p.nombre as nombre_producto, sk.*, vk.venta_total, COALESCE((diff_copia_actual - vk.venta_total), 0) as dif_venta
		FROM producto as p
		inner join (
			SELECT csp.idproducto_sucursal, csp.idproducto,  csp.codigo_stock, csp.stock as copia_stock  , ps.stock as actual_stock, COALESCE((csp.stock - ps.stock), 0) as diff_copia_actual 
			from copia_stock_producto as csp
			inner JOIN producto_sucursal as ps on ps.idproducto_sucursal = csp.idproducto_sucursal 
			where csp.idproducto > 0  $sql_copia_stock
		) as sk on sk.idproducto = p.idproducto
		$CSV_filtro join (
			SELECT pp.idproducto_sucursal, sum(vd.cantidad_total) as venta_total
			from venta_detalle as vd 
			INNER JOIN venta as v on v.idventa = vd.idventa
			INNER JOIN producto_presentacion as pp on pp.idproducto_presentacion = vd.idproducto_presentacion
			WHERE v.estado = '1' and v.estado_delete = '1' and v.tipo_comprobante in ('01','03', '12')  $sql_fecha_copia_stock
			GROUP BY pp.idproducto_sucursal
		) as vk on vk.idproducto_sucursal = sk.idproducto_sucursal
		WHERE p.idproducto > 0 $sql_estado_stock";
		return ejecutarConsultaArray($sql);
	}



	/* ════════════════════════════════════════════════════════════════════════════
		═══															CARD MONTOS TOTALES
	/* ════════════════════════════════════════════════════════════════════════════ */



	// ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════
	public function select2_filtro_copias_stock()
	{		
		
		$sql = "SELECT DISTINCT DATE_FORMAT(csp.fecha_creacion, '%d/%m/%Y %h:%i %p') as fecha_creacion_dmy_h12, DATE_FORMAT(csp.fecha_creacion, '%Y-%m-%d') as fecha_creacion, 
		csp.codigo_stock, LPAD(csp.codigo_stock, 5, '0') as codigo_stock_v2
		FROM copia_stock_producto as csp 
		INNER JOIN usuario as u on u.idusuario = csp.user_created
		INNER JOIN persona as p on p.idpersona = u.idpersona		
		order by csp.fecha_creacion DESC";
		return ejecutarConsultaArray($sql);
	}


}
