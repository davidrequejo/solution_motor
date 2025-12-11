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

  if ($_SESSION['reporte_stock_de_producto'] == 1) {

    require_once "../modelos/Reporte_validacion_estock.php";

    $reporte_val_estock = new Reporte_validacion_estock();

    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    //---id cliente no va 
    switch ($_GET["op"]) {
      // ══════════════════════════════════════ C R E A R   C O P I A   D E   S T O C K ══════════════════════════════════════

      case 'tabla_detalle_stock':
        $rspta = $reporte_val_estock->tabla_resumen_stock($_GET["filtro_copia_stock"], $_GET["filtro_fecha_copia_stock"], $_GET["filtro_estado_stock"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;
            

        if ($rspta['status'] == true) {
          //dia_cancelacion
          foreach ($rspta['data'] as $key => $value) {            

            $data[] = array(
              "0" => $cont++,              
              "1" =>  $value['codigo_alterno'],
              "2" =>  $value['nombre_producto'],
              "3" => $value['copia_stock']  ,
              "4" => $value['actual_stock']  ,
              "5" => $value['diff_copia_actual']  ,
              "6" => $value['venta_total']  ,
              "7" => $value['dif_venta']  ,

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
     
      // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

      case 'select2_filtro_copias_stock':

        $rspta = $reporte_val_estock->select2_filtro_copias_stock();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['codigo_stock']  . '" fecha_creacion="' . $value['fecha_creacion']  . '" >' . $value['codigo_stock_v2']. ' '.  $value['fecha_creacion_dmy_h12']  . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

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
