<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Reporte_copia_estock
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
	public function tabla_resumen_stock( $fecha_i, $fecha_f, $filtro_trabajador)
	{

		$sql_cobrado  = ''; $sql_fecha  = '';	$sql_filtro_trabajador  = '';

		

		if (empty($filtro_trabajador) || $filtro_trabajador == 'TODOS') {	} else {	$sql_filtro_trabajador	= "AND csp.user_created = '$filtro_trabajador'";		}

		if ( !empty($fecha_i) && !empty($fecha_f) ) { $sql_fecha = "AND DATE_FORMAT(csp.fecha_creacion, '%Y-%m-%d') BETWEEN '$fecha_i' AND '$fecha_f'"; } 
    else if (!empty($fecha_i)) { $sql_fecha = "AND DATE_FORMAT(csp.fecha_creacion, '%Y-%m-%d') = '$fecha_i'"; }
    else if (!empty($fecha_f)) { $sql_fecha = "AND DATE_FORMAT(csp.fecha_creacion, '%Y-%m-%d') = '$fecha_f'"; }		

		$sql = "SELECT DISTINCT csp.fecha_creacion, csp.codigo_stock, LPAD(csp.codigo_stock, 5, '0') as codigo_stock_v2,  csp.user_created, p.nombre_razonsocial
		FROM copia_stock_producto as csp 
		INNER JOIN usuario as u on u.idusuario = csp.user_created
		INNER JOIN persona as p on p.idpersona = u.idpersona
		$sql_cobrado $sql_fecha $sql_filtro_trabajador 
		order by csp.fecha_creacion DESC";
		return ejecutarConsulta($sql);
	}

	public function tabla_detalle_stock($codigo_stock)
	{
		$sql = "SELECT p.codigo, p.codigo_alterno, p.nombre, csp.stock, csp.precio_compra, csp.precio_venta, csp.estado
		FROM copia_stock_producto AS csp
		INNER JOIN producto as p on p.idproducto = csp.idproducto 
		WHERE csp.codigo_stock = '$codigo_stock' order by p.codigo";
		return ejecutarConsultaArray($sql);
	}

	public function crear_copia_stock($fecha)
	{
		$fecha_actual = date("Y-m-d H:i:s");
		$sql = "INSERT INTO copia_stock_producto(fecha_creacion, codigo_stock, idproducto_sucursal, idsucursal, idproducto, stock, stock_minimo, precio_compra, precio_venta, precio_por_mayor, estado, estado_delete)
		SELECT '$fecha_actual' as fecha_creacion, ( SELECT IFNULL(MAX(codigo_stock), 0) +1   FROM copia_stock_producto ) as codigo_stock,  ps.idproducto_sucursal, ps.idsucursal, ps.idproducto, ps.stock, ps.stock_minimo, 
		ps.precio_compra, ps.precio_venta, ps.precio_por_mayor, ps.estado, ps.estado_delete 
		FROM producto_sucursal as ps 
		INNER JOIN producto as p on p.idproducto = ps.idproducto where p.tipo_producto = 'PR'";
		return ejecutarConsulta($sql);
	}

	public function eliminar_permanente($codigo_stock)
	{
		$sql = "DELETE FROM copia_stock_producto WHERE codigo_stock = '$codigo_stock' ";
		return ejecutarConsulta($sql);
	}

	/* ════════════════════════════════════════════════════════════════════════════
		═══															CARD MONTOS TOTALES
	/* ════════════════════════════════════════════════════════════════════════════ */



	// ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════
	public function select2_filtro_trabajador()
	{
		$filtro_id_trabajador  = '';
		
		$sql = "SELECT DISTINCT LPAD(u.idusuario, 5, '0') as idusuario_formato,  csp.user_created as idusuario, p.nombre_razonsocial
		FROM copia_stock_producto as csp 
		INNER JOIN usuario as u on u.idusuario = csp.user_created
		INNER JOIN persona as p on p.idpersona = u.idpersona";
		return ejecutarConsulta($sql);
	}


}
