<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['periodo_facturado'] == 1) {

    require_once "../modelos/Periodo_facturacion.php";

    $periodo_facturacion        = new Periodo_facturacion();      

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../assets/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $idperiodo_contable = isset($_POST["idperiodo_contable"]) ? limpiarCadena($_POST["idperiodo_contable"]) : "";   
    $periodo            = isset($_POST["periodo"]) ? limpiarCadena($_POST["periodo"]) : "";    
    $fecha_inicio       = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";    
    $fecha_fin          = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";    

    switch ($_GET["op"]){

      case 'guardar_y_editar_periodo':        


        if (empty($idperiodo_contable)) {
          $rspta = $periodo_facturacion->insertar_periodo( $periodo, $fecha_inicio, $fecha_fin );
          echo json_encode($rspta, true);
        } else {
          
          $rspta = $periodo_facturacion->editar_periodo($idperiodo_contable, $periodo, $fecha_inicio, $fecha_fin);
          echo json_encode($rspta, true);
        }
      break;

      case 'listar_tabla_principal':

        $rspta = $periodo_facturacion->listar_tabla_principal($_GET["filtro_anio"], $_GET["filtro_periodo"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"]);
        $data = []; $count = 1; //echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){            

            $data[] = [
              "0" => $count++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_detalle_periodo(' . $value['idperiodo_contable'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn btn-icon btn-sm btn-danger-light border-danger" onclick="eliminar_papelera_periodo(' . $value['idperiodo_contable'] . ')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>',
              "2" => $value['idventa_v2'] ,
              "3" => $value['periodo_year'] . '-'.  $value['periodo_month'],
              "4" => $value['fecha_inicio'],
              "5" => $value['fecha_fin'],
              "6" => $value['venta_total'] ,        
              "7" =>$value['cantidad_comprobante']     ,  
              "8" => '<!--<button class="btn btn-icon btn-sm btn-info-light border-info" onclick="asignar_nuevo_comprobante(' . $value['idperiodo_contable'] . ')" data-bs-toggle="tooltip" title="Detalle Factura"><i class="bi bi-list-ol"></i></button>-->
              <button class="btn btn-icon btn-sm btn-info-light border-info" onclick="reasignar_comprobante(' . $value['idperiodo_contable'] .', \'' . $value['periodo_year'] . '-'.  $value['periodo_month'] . '\')" data-bs-toggle="tooltip" title="Reasignar Periodo"><i class="bi bi-arrow-left-right"></i></button>'
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

      case 'mostrar_editar_periodo':
        $rspta=$periodo_facturacion->mostrar_editar_periodo( $_GET["idperiodo"] );
        echo json_encode($rspta, true);
      break; 
      
      case 'bloquear_fechas_usadas':
        $rspta=$periodo_facturacion->bloquear_fechas_usadas( $_GET["idperiodo"] );
        echo json_encode($rspta, true);
      break;   

      case 'eliminar':
        $rspta = $periodo_facturacion->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $periodo_facturacion->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;      

      case 'mini_reporte':
        $rspta=$periodo_facturacion->mini_reporte($_GET["filtro_anio"],$_GET["filtro_periodo"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"]);
        echo json_encode($rspta, true);
      break; 

      // ══════════════════════════════════════ DETALLE COMPROBANTE ══════════════════════════════════════
      case 'guardar_y_editar_reasignar':
        
        $json = file_get_contents('php://input'); // Obtén el contenido de la solicitud POST        
        $data = json_decode($json, true); // Decodifica el JSON
        // Ahora puedes acceder a los datos
        $idperiodo = $data['idperiodo'];
        $ventas = $data['venta'];

        if (empty($idperiodo_contable)) {
          $rspta = $periodo_facturacion->reasignar_periodo( $idperiodo,  $ventas );
          echo json_encode($rspta, true);
        } 
      break;

      case 'listar_tabla_comprobante':

        $rspta = $periodo_facturacion->listar_tabla_comprobante($_GET["filtro_idperiodo"],$_GET["filtro_mes_emision"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"]);
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $img_proveedor = empty($value['foto_perfil']) ? 'no-perfil.jpg' : $value['foto_perfil'];            

            $data[] = [
              "0" => $count++,
              "1" => '<div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" role="switch" id="switch-primary-'.$value['idventa'].'" onchange="reasignar_periodo('. $value['idventa'] .');" >
                <label class="form-check-label cursor-pointer" for="switch-primary-'.$value['idventa'].'">Cambiar</label>
              </div>',
              "2" =>  $value['fecha_emision_format']  ,
              "3" => '<span class="text-nowrap fs-11">'. $value['nombre_periodo'] .'</span>',
              "4" => '<span class="text-nowrap fs-11 fw-semibold text-primary" data-bs-toggle="tooltip" title="'.$value['cliente_nombre_completo'] .'">'.$value['cliente_nombre_recortado'] .'</span> <br> 
                  <span class="text-nowrap fs-11 text-muted"><b>'.$value['tipo_documento'] .'</b>: '. $value['numero_documento'].'</span> ',
              "5" => '<span class="text-nowrap fs-11">'. '<b>'.$value['tp_comprobante_v2'].'</b> ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'] .'</span>',
              "6" =>  $value['venta_total_v2'] ,               
              "7" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :                    
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>'                              
              ),              
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

      // ══════════════════════════════════════ U S A R   A N T I C I P O S ══════════════════════════════════════
     
      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════      

      case 'select2_filtro_tipo_comprobante':
        $rspta = $periodo_facturacion->select2_filtro_tipo_comprobante($_GET["tipos"]); $cont = 1; $data = "";
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
        $rspta = $periodo_facturacion->select2_filtro_cliente(); $cont = 1; $data = "";
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
        $rspta = $periodo_facturacion->select2_filtro_anio(); $cont = 1; $data = "";
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
        $rspta = $periodo_facturacion->select2_periodo(); $cont = 1; $data = "";
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