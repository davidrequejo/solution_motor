<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['unidad_de_medida'] == 1) {
    
    require_once "../modelos/Unidad_medida.php";
    $unidad_medida = new Unidad_medida();

    $idsunat_c03_unidad_medida   = isset($_POST["idsunat_c03_unidad_medida"]) ? limpiarCadena($_POST["idsunat_c03_unidad_medida"]) : "";
    $nombre             = isset($_POST["nombre_um"]) ? limpiarCadena($_POST["nombre_um"]) : "";
    $abreviaruta             = isset($_POST["abreviatura_um"]) ? limpiarCadena($_POST["abreviatura_um"]) : "";
    $equivalencia             = isset($_POST["equivalencia_um"]) ? limpiarCadena($_POST["equivalencia_um"]) : "";
    $descripcion             = isset($_POST["descr_um"]) ? limpiarCadena($_POST["descr_um"]) : "";


    switch ($_GET["op"]) {

      case 'listar_tabla_um':
        $rspta = $unidad_medida->listar();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => ($value['idsunat_c03_unidad_medida'] <= 63 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' : '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_u_m(' . $value['idsunat_c03_unidad_medida'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_papelera_u_m(' . $value['idsunat_c03_unidad_medida'] . ', \'' . encodeCadenaHtml($value['nombre']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'),         
              "2" => $value['nombre'],
              "3" => $value['abreviatura'],
              "4" => $value['equivalencia'],
              "5" => $value['descripcion'],
              "6" =>  ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',
              
              "7" => $value['idsunat_c03_unidad_medida'],
            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;

      case 'guardar_editar_UM':
        if (empty($idsunat_c03_unidad_medida)) {
          $rspta = $unidad_medida->insertar($nombre, $abreviaruta, $equivalencia, $descripcion);
          echo json_encode($rspta, true);
        } else {
          $rspta = $unidad_medida->editar($idsunat_c03_unidad_medida, $nombre, $abreviaruta, $equivalencia, $descripcion);
          echo json_encode($rspta, true);
        }
      break;

      case 'mostrar_u_m':
        $rspta = $unidad_medida->mostrar($idsunat_c03_unidad_medida);
        echo json_encode($rspta, true);
      break;

      case 'desactivar':
        $rspta = $unidad_medida->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $unidad_medida->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;
      
      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;

    }

  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  

}
ob_end_flush();