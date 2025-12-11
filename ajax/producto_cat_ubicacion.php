<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

	if ($_SESSION['producto'] == 1) {

		require_once "../modelos/Producto_cat_ubicacion.php";
		$ubicacion = new producto_categoria_ubicacion();

		date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
		$imagen_error = "this.src='../dist/svg/404-v2.svg'";
		$toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idubicacion     = isset($_POST["idproducto_categoria_ubicacion"])? limpiarCadena($_POST["idproducto_categoria_ubicacion"]):"";

    $nombre_ubi     = isset($_POST["nombre_ubi"])? limpiarCadena($_POST["nombre_ubi"]):"";
    $descr_ubi     = isset($_POST["descr_ubi"])? limpiarCadena($_POST["descr_ubi"]):"";


		switch ($_GET["op"]){

			case 'guardar_editar_ubi':
        if (empty($idubicacion)) {
          $rspta = $ubicacion->insertar_ubi($nombre_ubi, $descr_ubi);
          echo json_encode($rspta, true);
        } else {
          $rspta = $ubicacion->editar_ubi($idubicacion, $nombre_ubi, $descr_ubi);
          echo json_encode($rspta, true);
        }
      break;

			case 'select_prod_cat_ubicacion':
        $rspta = $ubicacion->select_prod_cat_ubicacion();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria_ubicacion'] . '" title ="" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_ubicacion':
        $rspta = $ubicacion->select2_filtro_ubicacion();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria_ubicacion'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array('status' => true,'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

		}
		
	}else {
		$retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
		echo json_encode($retorno);
	}


}
ob_end_flush();
?>




