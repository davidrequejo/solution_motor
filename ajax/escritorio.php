<?php
require_once "../sunat/ConfigSunat.php";

  ob_start();
  if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

  if (!isset($_SESSION["user_nombre"])) {
    $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
  } else {

    require_once "../modelos/Usuario.php";
    $usuario    = new Usuario();

    if ($_SESSION['dashboard'] == 1) {

      require_once "../modelos/Escritorio.php";     
      
      $escritorio = new Escritorio();      

      date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
      $imagen_error = "this.src='../assets/svg/user_default.svg'";
      $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';
      $scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/fun_route/admin/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/admin/');

      $formato_cci = isset($_POST["formato_cci"]) ? limpiarCadena($_POST["formato_cci"]) : "";
      $formato_cta = isset($_POST["formato_cta"]) ? limpiarCadena($_POST["formato_cta"]) : "";
      $formato_detracciones = isset($_POST["formato_detracciones"]) ? limpiarCadena($_POST["formato_detracciones"]) : "";  

      switch ($_GET["op"]) {       

        case 'ver_reporte':
          $rspta = $escritorio->ver_reporte($_GET["filtro_anio"], $_GET["filtro_mes"], $_GET["cant_mes"], $_GET["filtro_trabajador"]);
          echo json_encode( $rspta, true) ;
        break;     
        
        case 'select2_filtro_anio_contable':

          $rspta = $escritorio->select2_filtro_anio_contable();        
          $data = "";
          if ($rspta['status'] == true) {
            foreach ($rspta['data'] as $key => $value) {             
              $data .= '<option  value="' . $value['anio_contable']  . '">' . $value['anio_contable'].  '</option>';
            }
  
            $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
            echo json_encode($retorno, true);
          } else {
            echo json_encode($rspta, true);
          }
  
        break; 
        
        case 'select2_filtro_trabajador':

          $rspta = $escritorio->select2_filtro_trabajador();        
          $data = "";
          if ($rspta['status'] == true) {
            foreach ($rspta['data'] as $key => $value) {              
              $data .= '<option  value="' . $value['idusuario']  . '">' . $value['idtrabajador']. ' '.  $value['nombre_razonsocial']  . '</option>';
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

    } else {
      $marcados = $usuario->listarmarcados($_SESSION['idusuario']); 
      $html_permisos = '';
      foreach ($marcados['data'] as $key => $val) {
        $html_permisos .= '<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">          
          <a href="'.$val['file_name_php'].'.php">
            <div class="d-flex align-items-center">
              <div class="me-2"> <i class="ri-star-s-line fs-16"></i> </div>
              <span class="flex-fill text-nowrap text-start"> '.$val['submodulo'].' </span>
            </div>
          </a>
        </div>';
      }
      $retorno = ['status'=>'nopermiso', 'message'=>'Aqui tienes una lista <i>Sirvase a escoger</i> <div class="row mt-2 mb-2">' . $html_permisos .'</div>', 'data' => [], 'aaData' => [] ];
      echo json_encode($retorno);
    }  

  }

  ob_end_flush();
?>