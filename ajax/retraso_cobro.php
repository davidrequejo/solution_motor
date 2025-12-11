<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['retraso_de_cobro'] == 1) {

    require_once "../modelos/Retraso_cobro.php";

    $retraso_cobro        = new Retraso_cobro();      

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../assets/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $idperiodo_contable = isset($_POST["idperiodo_contable"]) ? limpiarCadena($_POST["idperiodo_contable"]) : "";   
    $periodo            = isset($_POST["periodo"]) ? limpiarCadena($_POST["periodo"]) : "";    
    $fecha_inicio       = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";    
    $fecha_fin          = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";    

    switch ($_GET["op"]){     

      case 'listar_tabla_principal':

        $rspta = $retraso_cobro->listar_tabla_principal($_GET["filtro_periodo"], $_GET["filtro_trabajador"]);
        $data = []; $count = 1; //echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){            

            $data[] = [
              "0" => $count++,
              "1" =>  $value['mes_inicio'] .'<br> <i class="">'. $value['dia_cancelacion'] . '</i>' ,
              "2" => '<span class="fs-11 text-primary">'.$value['cliente_nombre_completo'].'</span> <br>' . 
              '<span class="fs-11">'.$value['tipo_documento_abrev_nombre'] .': '.$value['numero_documento'] .'</span> | '.
              '<span class="fs-11"><i class="ti ti-fingerprint fs-15"></i> '.$value['idpersona_cliente_v2'] .'</span>' ,              
              "3" => $value['cant_cobrado'] .'/'.  $value['cant_total']  ,
              "4" => $value['estado_deuda'] == 'SIN DEUDA' ? '<button type="button" class="btn btn-sm btn-outline-success my-1 me-2" data-bs-toggle="tooltip" title="Ver cobros" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" >'.$value['estado_deuda'].' <span class="badge ms-2 fs-11">'.$value['avance_v2'].'</span></button>' : 
              ( $value['estado_deuda'] == 'DEUDA' ? '<button type="button" class="btn btn-sm btn-outline-danger my-1 me-2" data-bs-toggle="tooltip" title="Ver cobros" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" >'.$value['estado_deuda'].' <span class="badge ms-2 fs-11">'.$value['avance_v2'].'</span></button>' :
                ($value['estado_deuda'] == 'ADELANTO' ? '<button type="button" class="btn btn-sm btn-outline-info my-1 me-2" data-bs-toggle="tooltip" title="Ver cobros" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" >'.$value['estado_deuda'].' <span class="badge ms-2 fs-11">'.$value['avance_v2'].'</span></button>' : '-')
              ),
              "5" =>  $value['cant_cobrado'],
              "6" => $value['cant_total'],
              "7" => $value['avance']
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;      

      case 'mostrar_reporte':
        $rspta=$retraso_cobro->mostrar_reporte( $_GET["filtro_periodo"], $_GET["filtro_trabajador"] );
        echo json_encode($rspta, true);
      break; 
      
    

      case 'mini_reporte':
        $rspta=$retraso_cobro->mini_reporte($_GET["filtro_anio"],$_GET["filtro_periodo"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"]);
        echo json_encode($rspta, true);
      break; 

      // ══════════════════════════════════════ DETALLE COMPROBANTE ══════════════════════════════════════
     

      // ══════════════════════════════════════ U S A R   A N T I C I P O S ══════════════════════════════════════
     
      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════      

      case 'select2_filtro_tipo_comprobante':
        $rspta = $retraso_cobro->select2_filtro_tipo_comprobante($_GET["tipos"]); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idtipo_comprobante']  . '" >' . $value['nombre_tipo_comprobante_v2'] . '</option>';
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select2_filtro_cliente':
        $rspta = $retraso_cobro->select2_filtro_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idpersona_cliente']  . '">' . $cont. '. '. $value['cliente_nombre_completo'] .' - '. $value['nombre_tipo_documento'] .': '. $value['numero_documento'] .' (' .$value['cantidad'].')'. '</option>';
            $cont++;
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select2_filtro_anio':
        $rspta = $retraso_cobro->select2_filtro_anio(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['periodo_year'] . '"> '. $value['periodo_year'] .' (' .$value['cant_comprobante'].')'. '</option>';
            $cont++;
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select2_periodo':
        $rspta = $retraso_cobro->select2_periodo(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idperiodo_contable'] . '"> '. $value['periodo_year'] .'-' .$value['periodo_month']. ' ('.$value['cant_comprobante']. ')'. '</option>';
            $cont++;
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select2_filtro_trabajador':

        $rspta = $retraso_cobro->select2_filtro_trabajador();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['idpersona_trabajador']  . '">' . $value['idtrabajador']. ' '.  $value['nombre_razonsocial'] . ' ('.$cant_cliente.')' . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;  

      case 'select2_filtro_anio_cobro':

        $rspta = $retraso_cobro->select2_filtro_anio_cobro();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['anio_cancelacion']  . '">' . $value['anio_cancelacion'] . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;
     

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;

    }

  }else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }
}
ob_end_flush();

?>