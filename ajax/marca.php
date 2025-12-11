<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['categoria_y_marca'] == 1) {
    
    require_once "../modelos/Marca.php";
    $marca = new Marca();

    $idproducto_marca  = isset($_POST["idproducto_marca"]) ? limpiarCadena($_POST["idproducto_marca"]) : "";
    $nombre       = isset($_POST["nombre_marca"]) ? limpiarCadena($_POST["nombre_marca"]) : "";
    $descripcion  = isset($_POST["descr_marca"]) ? limpiarCadena($_POST["descr_marca"]) : "";


    switch ($_GET["op"]) {
      
      case 'listar_tabla_marca':
        $rspta = $marca->listar_marca();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => ($value['idproducto_marca'] == 1 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' : '<button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_marca(' . $value['idproducto_marca'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light border-danger product-btn" onclick="eliminar_papelera_marca(' . $value['idproducto_marca'] . ', \'' . encodeCadenaHtml($value['nombre']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'),         
              "2" => $value['nombre'],
              "3" => $value['descripcion'],
              "4" =>  ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',
              "5" => $value['idproducto_marca'],
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

      case 'guardar_editar_marca':
        if (empty($idproducto_marca)) {
          $rspta = $marca->insertar($nombre, $descripcion);
          echo json_encode($rspta, true);
        } else {
          $rspta = $marca->editar($idproducto_marca, $nombre, $descripcion);
          echo json_encode($rspta, true);
        }
      break;

      case 'mostrar_marca':
        $rspta = $marca->mostrar($idproducto_marca);
        echo json_encode($rspta, true);
      break;

      case 'desactivar':
        $rspta = $marca->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $marca->eliminar($_GET["id_tabla"]);
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