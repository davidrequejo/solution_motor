<?php 
require_once "../sunat/ConfigSunat.php";
require_once "../modelos/Permiso.php";

$permiso=new Permiso();

switch ($_GET["op"]){

	case 'listar':
		$rspta=$permiso->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta['data']->fetch_object()){
 			$data[]=array(
 				"0"=>$reg->nombre
 			);
 		}
 		$results = array(
			'status'=> true,
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

	default: 
    $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
  break;
}
?>