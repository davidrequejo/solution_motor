<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['general'] == 1) {

    require_once "../modelos/Centro_poblado.php";

    $centro_poblado = new CentroPoblado();

    $idcentro_poblado   = isset($_POST["idcentro_poblado"]) ? limpiarCadena($_POST["idcentro_poblado"]) : "";
    $nombre_cp             = isset($_POST["nombre_cp"]) ? limpiarCadena($_POST["nombre_cp"]) : "";
    $descripcion_cp        = isset($_POST["descripcion_cp"]) ? limpiarCadena($_POST["descripcion_cp"]) : "";

    switch ($_GET["op"]) {
      case 'guardar_y_editar_centro_poblado':
        if (empty($idcentro_poblado)) {
          $rspta = $centro_poblado->insertar($nombre_cp, $descripcion_cp);
          echo json_encode($rspta, true);
        } else {
          $rspta = $centro_poblado->editar($idcentro_poblado, $nombre_cp, $descripcion_cp);
          echo json_encode($rspta, true);
        }
      break;

      case 'desactivar':
        $rspta = $centro_poblado->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $centro_poblado->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_centro_poblado':
        $rspta = $centro_poblado->mostrar($idcentro_poblado);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;

      case 'tabla_principal_centro_poblado':
        $rspta = $centro_poblado->tabla_principal_centro_poblado();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_centro_poblado(' . $value['idcentro_poblado'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_centro_poblado(' . $value['idcentro_poblado'] . ', \'' . encodeCadenaHtml($value['nombre']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>',         
              "2" => $value['nombre'],
              "3" => $value['descripcion'],
              "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'

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

      case 'salir':
        //Limpiamos las variables de sesión
        session_unset();
        //Destruìmos la sesión
        session_destroy();
        //Redireccionamos al login
        header("Location: ../index.php");
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
      break;
    }
    
  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}
ob_end_flush();
