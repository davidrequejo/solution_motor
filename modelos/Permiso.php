<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Permiso
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}
	
	public function listar() {
		$sql="SELECT * from permiso where not idpermiso in('6','7') ";
		return ejecutarConsulta($sql);		
	}

	public function listar_todos_permisos() {

		$data_permiso = [];
		$sql = "SELECT * from permiso ";	$todos = ejecutarConsultaArray($sql); 
		$sql = "SELECT MIN(idpermiso) AS idpermiso, estado, modulo, count(modulo) from permiso GROUP BY estado, modulo ORDER BY  count(modulo) DESC";	
		$agrupado = ejecutarConsultaArray($sql);

		foreach ($agrupado['data'] as $key => $val) {

			$modulo = $val['modulo'];
			$sql = "SELECT * from permiso where modulo = '$modulo'";	$agrupado = ejecutarConsultaArray($sql);

			$data_permiso[] = [
				'idpermiso'	=> $val['idpermiso'],
				'modulo'		=> $val['modulo'],
				'estado'		=> $val['estado'],	
				'submodulo'	=> $agrupado['data']			
			];
		}
		$data = [ 'status'=>true, 'message'=>'todo okey','data'=> ['todos' => $todos['data'], 'agrupado' => $data_permiso ]  ];
    return $data; 	
	}

	public function listarEmpresa()	{
		$sql="SELECT * from empresa";
		return ejecutarConsultaArray($sql);		
	}
	
}

?>