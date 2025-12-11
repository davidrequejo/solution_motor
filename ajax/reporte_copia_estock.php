<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) {
  session_start();
} //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['reporte_copia_estock'] == 1) {

    require_once "../modelos/Reporte_copia_estock.php";

    $reporte_copia_estock = new Reporte_copia_estock();
    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    //---id cliente no va 
    switch ($_GET["op"]) {
      // ══════════════════════════════════════ C R E A R   C O P I A   D E   S T O C K ══════════════════════════════════════

      case 'guardar_editar_copia_stock':
        $rspta = $reporte_copia_estock->crear_copia_stock($_POST["fecha_copia"]);
        echo json_encode($rspta, true);
      break;
      
      case 'tabla_resumen_stock':
        $rspta = $reporte_copia_estock->tabla_resumen_stock($_GET["filtro_fecha_i"],$_GET["filtro_fecha_f"],$_GET["filtro_trabajador"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;
            

        if ($rspta['status'] == true) {
          //dia_cancelacion
          foreach ($rspta['data'] as $key => $value) {            

            $data[] = array(
              "0" => $cont++,              
              "1" =>  $value['fecha_creacion'],
              "2" => $value['codigo_stock_v2']  ,
              "3" => '<button class="btn btn-icon btn-sm btn-danger-light" onclick="eliminar_copia_stock('.($value['codigo_stock']).')" data-bs-toggle="tooltip" title="Eliminar"><i class="ti ti-trash-x"></i></button>
              <button class="btn btn-icon btn-sm btn-info-light" onclick="tabla_detalle_stock('.($value['codigo_stock']).')" data-bs-toggle="tooltip" title="Ver detalle"><i class="ri-eye-line"></i></button>',
              "4" => $value['nombre_razonsocial'] , 

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

      case 'tabla_detalle_stock':
        $rspta = $reporte_copia_estock->tabla_detalle_stock($_GET["codigo_stock"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;
            

        if ($rspta['status'] == true) {
          //dia_cancelacion
          foreach ($rspta['data'] as $key => $value) {            

            $data[] = array(
              "0" => $cont++,              
              "1" =>  $value['codigo_alterno'],
              "2" =>  $value['nombre'],
              "3" => $value['stock']  ,
              "4" => $value['precio_compra']  ,
              "5" => $value['precio_venta']  ,
              "6" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'  ,

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
      
      case 'eliminar_permanente':
        $rspta = $reporte_copia_estock->eliminar_permanente($_GET["codigo_stock"]);
        echo json_encode($rspta, true);
      break;      
     
      // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

      case 'select2_filtro_trabajador':

        $rspta = $reporte_copia_estock->select2_filtro_trabajador();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idusuario']  . '">' . $value['idusuario_formato']. ' '.  $value['nombre_razonsocial']  . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
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
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => [], 'aaData' => []];
        echo json_encode($rspta, true);
      break;
    }
  } else {
    $retorno = ['status' => 'nopermiso', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
    echo json_encode($retorno);
  }
}

ob_end_flush();
