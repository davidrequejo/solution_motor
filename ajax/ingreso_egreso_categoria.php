<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['general'] == 1) {
    
    require_once "../modelos/Ingreso_egreso_categoria.php";
    $ie_categoria = new Ingreso_egreso_categoria();

    $idcategoria  = isset($_POST["id_ie_categoria"]) ? limpiarCadena($_POST["id_ie_categoria"]) : "";
    $nombre       = isset($_POST["nombre_ie_cat"]) ? limpiarCadena($_POST["nombre_ie_cat"]) : "";
    $descripcion  = isset($_POST["descr_ie_cat"]) ? limpiarCadena($_POST["descr_ie_cat"]) : "";


    switch ($_GET["op"]) {
    
      case 'tabla_principal_ie_categoria':
        $rspta = $ie_categoria->listar_ie_categoria();
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => ($value['idingreso_egreso_categoria'] == 1 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' : '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_ie_categoria(' . $value['idingreso_egreso_categoria'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                     ' <button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_ie_categoria(' . $value['idingreso_egreso_categoria'] . ', \'' . encodeCadenaHtml($value['nombre']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'),
              "2" => $value['nombre'],
              "3" => $value['descripcion'],
              "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }
      break;
      
      case 'guardar_editar_ie_categoria':
        if (empty($idcategoria)) {
          $rspta = $ie_categoria->insertar_ie_categoria($nombre, $descripcion);
          echo json_encode($rspta, true);
        } else {
          $rspta = $ie_categoria->editar_ie_categoria($idcategoria, $nombre, $descripcion);
          echo json_encode($rspta, true);
        }
      break;

      case 'mostrar_datos_ie_categoria':
        $rspta = $ie_categoria->mostrar_ie_categoria($_GET["id"]);
        echo json_encode($rspta, true);
      break;

      case 'desactivar_ie_categoria':
        $rspta = $ie_categoria->desactivar_ie_categoria($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar_ie_categoria':
        $rspta = $ie_categoria->eliminar_ie_categoria($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

    }

  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  

}
ob_end_flush();