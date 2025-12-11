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

  if ($_SESSION['reporte_venta_x_trabajador'] == 1) {

    require_once "../modelos/Reporte_venta_x_trabajador.php";

    $reporte_x_trabajador = new reporte_venta_x_trabajador();
    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    //---id cliente no va 
    switch ($_GET["op"]) {
      // ══════════════════════════════════════ VALIDAR SESION CLIENTE ══════════════════════════════════════
      
      case 'tabla_reporte_detalle':
        $rspta = $reporte_x_trabajador->tabla_reporte_detalle($_GET["filtro_estado_cobrado"],$_GET["filtro_fecha_i"],$_GET["filtro_fecha_f"],$_GET["filtro_trabajador"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;
            

        if ($rspta['status'] == true) {
          //dia_cancelacion
          foreach ($rspta['data'] as $key => $value) {            

            $data[] = array(
              "0" => $cont++,              
              "1" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img class="w-30px h-auto" src="../assets/modulo/persona/perfil/' . $value['foto_perfil'] . '" alt="" onclick="ver_img(\'' . $value['foto_perfil'] . '\')"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary">' . $value['cliente_nombre_recortado'] . '</span>
                  <span class="text-muted text-nowrap">' . $value['tipo_documento_abreviatura'] . ' : ' . $value['numero_documento'] . '</span>                   
                </div>
              </div>',
              "2" => $value['serie_y_numero_comprobante'] . ' <br> ' . $value['rel_serie_numero'] ,
              "3" => $value['venta_total'] ,              
              "4" => $value['utl_utilidad'] ,              
              "5" => $value['uc_nombre_razonsocial'] ,              
              "6" => $value['fecha_emision_amd'] ,

              "7" => $value['cliente_nombre_completo'] ,
              "8" => $value['tipo_documento_abreviatura'] . ' : ' . $value['numero_documento'] ,
              "9" => $value['celular'] ,

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


      case 'resumen_reporte':
        $rspta = $reporte_x_trabajador->resumen_reporte($_GET["filtro_estado_cobrado"],$_GET["filtro_fecha_i"],$_GET["filtro_fecha_f"],$_GET["filtro_trabajador"], $_GET["filtro_tipo_chart"]);
        echo json_encode($rspta, true);
      break;
      
      
     
      // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

      case 'select2_filtro_trabajador':

        $rspta = $reporte_x_trabajador->select2_filtro_trabajador();        
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
      
      case 'select2_filtro_anio_pago':

        $rspta = $reporte_x_trabajador->select2_filtro_anio_pago();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['anio_cancelacion']  . '">' . $value['anio_cancelacion'] . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_mes_pago':

        $rspta = $reporte_x_trabajador->select2_filtro_mes_pago();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['mes_cancelacion']  . '">' . $value['mes_cancelacion'] . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;
      
      case 'select2_filtro_tipo_comprob':

        $rspta = $reporte_x_trabajador->select2_filtro_tipo_comprob();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['tipo_comprobante']  . '">' . $value['abreviatura'] . '</option>';
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
