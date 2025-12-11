<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['avance_de_cobro'] == 1) {

    require_once "../modelos/Avance_cobro.php";

    $avance_cobro        = new Avance_cobro();      

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

        $rspta = $avance_cobro->listar_tabla_principal($_GET["filtro_periodo"], $_GET["filtro_trabajador"]);
        $data = []; $count = 1; //echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){            

            $data[] = [
              "0" => $count++,
              "1" => '<span class="fs-11">'.$value['centro_poblado'] .'</span>' ,
              "2" => $value['avance'] == 100 ? '<div class="d-flex align-items-center w-200px">
                <div class="progress progress-animate progress-xs w-100" role="progressbar" aria-valuenow="'.$value['avance'].'" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: '.$value['avance'].'%"></div>
                </div>
                <div class="ms-2 fs-10">'.$value['avance'].'%</div>
              </div>' : '<div class="d-flex align-items-center w-200px">
                <div class="progress progress-animate progress-xs w-100" role="progressbar" aria-valuenow="'.$value['avance'].'" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: '.$value['avance'].'%"></div>
                </div>
                <div class="ms-2 fs-10">'.$value['avance'].'%</div>
              </div>',              
              "3" => $value['cant_cobrado'] .'/'.  $value['cant_total'] ,
              "4" => '',
              "5" => $value['cant_cobrado'],
              "6" => $value['cant_total']
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
        $rspta=$avance_cobro->mostrar_reporte( $_GET["filtro_periodo"], $_GET["filtro_trabajador"] );
        echo json_encode($rspta, true);
      break; 
      
    

      case 'mini_reporte':
        $rspta=$avance_cobro->mini_reporte($_GET["filtro_anio"],$_GET["filtro_periodo"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"]);
        echo json_encode($rspta, true);
      break; 

      // ══════════════════════════════════════ DETALLE COMPROBANTE ══════════════════════════════════════
     

      // ══════════════════════════════════════ U S A R   A N T I C I P O S ══════════════════════════════════════
     
      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════      

      case 'select2_filtro_tipo_comprobante':
        $rspta = $avance_cobro->select2_filtro_tipo_comprobante($_GET["tipos"]); $cont = 1; $data = "";
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
        $rspta = $avance_cobro->select2_filtro_cliente(); $cont = 1; $data = "";
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
        $rspta = $avance_cobro->select2_filtro_anio(); $cont = 1; $data = "";
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
        $rspta = $avance_cobro->select2_periodo(); $cont = 1; $data = "";
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

        $rspta = $avance_cobro->select2_filtro_trabajador();        
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