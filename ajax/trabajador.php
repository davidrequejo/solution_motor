<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }//Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['registrar_trabajador'] == 1) {
    
    require_once "../modelos/Trabajador.php";

    $trabajador = new Trabajador();
    
    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';
    $scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/front_jdl/admin/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/');

    // :::::::::::::::::::::::::::::::::::: D A T O S   E M P R E S A ::::::::::::::::::::::::::::::::::::::

    $idpersona            = isset($_POST["idpersona"])? limpiarCadena($_POST["idpersona"]):"";
    $tipo_persona_sunat   = isset($_POST["tipo_persona_sunat"])? limpiarCadena($_POST["tipo_persona_sunat"]):"";
    $idtipo_persona       = isset($_POST["idtipo_persona"])? limpiarCadena($_POST["idtipo_persona"]):"";
    $idpersona_trabajador = isset($_POST["idpersona_trabajador"])? limpiarCadena($_POST["idpersona_trabajador"]):"";

    $tipo_documento       = isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
    $numero_documento     = isset($_POST["numero_documento"])? limpiarCadena($_POST["numero_documento"]):"";
    $idcargo_trabajador   = isset($_POST["idcargo_trabajador"])? limpiarCadena($_POST["idcargo_trabajador"]):"";
    $nombre_razonsocial   = isset($_POST["nombre_razonsocial"])? limpiarCadena($_POST["nombre_razonsocial"]):"";
    $apellidos_nombrecomercial = isset($_POST["apellidos_nombrecomercial"])? limpiarCadena($_POST["apellidos_nombrecomercial"]):"";
    $correo               = isset($_POST["correo"])? limpiarCadena($_POST["correo"]):"";
    $celular              = isset($_POST["celular"])? limpiarCadena($_POST["celular"]):"";
    $fecha_nacimiento     = isset($_POST["fecha_nacimiento"])? limpiarCadena($_POST["fecha_nacimiento"]):"";
    $ruc                  = isset($_POST["ruc"])? limpiarCadena($_POST["ruc"]):"";
    $usuario_sol          = isset($_POST["usuario_sol"])? limpiarCadena($_POST["usuario_sol"]):"";
    $clave_sol            = isset($_POST["clave_sol"])? limpiarCadena($_POST["clave_sol"]):"";
    $direccion            = isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
    $distrito             = isset($_POST["distrito"])? limpiarCadena($_POST["distrito"]):"";
    $departamento         = isset($_POST["departamento"])? limpiarCadena($_POST["departamento"]):"";
    $provincia            = isset($_POST["provincia"])? limpiarCadena($_POST["provincia"]):"";
    $ubigeo               = isset($_POST["ubigeo"])? limpiarCadena($_POST["ubigeo"]):"";
    $sueldo_mensual       = isset($_POST["sueldo_mensual"])? limpiarCadena($_POST["sueldo_mensual"]):"";
    $sueldo_diario        = isset($_POST["sueldo_diario"])? limpiarCadena($_POST["sueldo_diario"]):"";
    $idbanco              = isset($_POST["idbanco"])? limpiarCadena($_POST["idbanco"]):"";
    $cuenta_bancaria      = isset($_POST["cuenta_bancaria"])? limpiarCadena($_POST["cuenta_bancaria"]):"";
    $cci                  = isset($_POST["cci"])? limpiarCadena($_POST["cci"]):"";
    $titular_cuenta       = isset($_POST["titular_cuenta"])? limpiarCadena($_POST["titular_cuenta"]):"";    

    switch ($_GET["op"]) {   
      
      // :::::::::::::::::::::::::: S E C C I O N   T R A B A J A D O R   ::::::::::::::::::::::::::

      case 'guardar_y_editar':
        //guardar f_img_fondo fondo
        if ( !file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']) ) {
          $img_perfil = $_POST["imagenactual"];
          $flat_img1 = false; 
        } else {          
          $ext1 = explode(".", $_FILES["imagen"]["name"]);
          $flat_img1 = true;
          $img_perfil = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext1);
          move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/modulo/persona/perfil/" . $img_perfil);          
        }        

        if ( empty($idpersona) ) { #Creamos el registro

          $rspta = $trabajador->insertar( $tipo_persona_sunat, $idtipo_persona, $tipo_documento, $numero_documento, $idcargo_trabajador, 
          $nombre_razonsocial, $apellidos_nombrecomercial, $correo, $celular, $fecha_nacimiento, $ruc, $usuario_sol, $clave_sol, $direccion, $distrito, 
          $departamento, $provincia, $ubigeo, $sueldo_mensual, $sueldo_diario, $idbanco, $cuenta_bancaria, $cci, $titular_cuenta, $img_perfil );
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img1 == true || empty($img_perfil)) {
            $datos_f1 = $trabajador->perfil_trabajador($idpersona);
            $img1_ant = $datos_f1['data']['foto_perfil'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/persona/perfil/" . $img1_ant); }         
          }  
         
          $rspta = $trabajador->editar($idpersona, $tipo_persona_sunat, $idtipo_persona,  $idpersona_trabajador, $tipo_documento, $numero_documento, $idcargo_trabajador, 
          $nombre_razonsocial, $apellidos_nombrecomercial, $correo, $celular, $fecha_nacimiento, $ruc, $usuario_sol, $clave_sol, $direccion, $distrito, 
          $departamento, $provincia, $ubigeo, $sueldo_mensual, $sueldo_diario, $idbanco, $cuenta_bancaria, $cci, $titular_cuenta, $img_perfil);
          echo json_encode($rspta, true);
        }        

        
      break;      

      case 'mostrar_trabajador':
        $rspta = $trabajador->mostrar_trabajdor($_POST["idpersona"]);
        echo json_encode($rspta);
      break;    
      
      case 'eliminar':
        $rspta = $trabajador->eliminar($_GET["id_tabla"], $_GET["idpersona"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $trabajador->papelera($_GET["id_tabla"], $_GET["idpersona"]);
        echo json_encode($rspta, true);
      break;

      case 'activar':
        $rspta = $trabajador->activar($_GET["id_tabla"], $_GET["idpersona"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_tabla_principal':
        $rspta = $trabajador->listar_tabla_principal();
        //Vamos a declarar un array
    
        $data = array(); $count =1;
    
        while ($reg = $rspta['data']->fetch_object()) {
          // Mapear el valor numérico a su respectiva descripción      
    
          $img = empty($reg->foto_perfil) ? 'no-perfil.jpg' : $reg->foto_perfil ;
    
          $data[] = array(
            "0" => $count++,
            "1" => '<div class="hstack gap-2 fs-15">' .
              '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar(' . $reg->idpersona . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
              ($reg->estado ? '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="desactivar(' . $reg->idpersona_trabajador. ', '. $reg->idpersona . ', \'' . encodeCadenaHtml($reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>':
              '<button class="btn btn-icon btn-sm btn-success-light product-btn" onclick="activar(' . $reg->idpersona_trabajador. ', '. $reg->idpersona . ')" data-bs-toggle="tooltip" title="Activar"><i class="fa fa-check"></i></button>'
              ).
            '</div>',        
            "2" =>'<div class="d-flex flex-fill align-items-center">
              <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/persona/perfil/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml($reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial) . '\')"> </span></div>
              <div>
                <span class="d-block fw-semibold text-primary">'.$reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial.'</span>
                <span class="text-muted"><b>'.$reg->tipo_documento .'</b>: '. $reg->numero_documento .' | <i class="ti ti-fingerprint fs-18"></i> '. zero_fill($reg->idpersona_trabajador, 5).'</span>
              </div>
            </div>',
            "3" =>  '<div class="text-start">
              <span class="d-block text-primary fw-semibold">'.date('d/m/Y', strtotime($reg->fecha_nacimiento)).'</span>
              <span class="text-muted">'.calcular_edad($reg->fecha_nacimiento).' Años</span>
            </div>',
            "4" => $reg->cargo_trabajador,
            "5" => '<a href="tel:+51'.$reg->celular.'">'.$reg->celular.'</a>',
            "6" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light" readonly="">'.$reg->direccion.'</textarea>',
            "7" =>  '<span class="badge bg-outline-warning cursor-pointer font-size-12px" onclick="clientes_x_trabajador('.$reg->idpersona_trabajador.');" data-bs-toggle="tooltip" title="Ver ventas">'.$reg->cant_ventas.'</span>',
            
            "8" => $reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial,
            "9" => $reg->tipo_documento,
            "10" => $reg->numero_documento,
            "11" => $reg->fecha_nacimiento,
            "12" => calcular_edad($reg->fecha_nacimiento),
          );
        }
        $results = array(
          'status'=> true,
          "sEcho" => 1, //Información para el datatables
          "iTotalRecords" => count($data),  //enviamos el total registros al datatable
          "iTotalDisplayRecords" => count($data),  //enviamos el total registros a visualizar
          "aaData" => $data
        );
        echo json_encode($results);
    
      break;

      case 'clientes_x_trabajador':
        $rspta = $trabajador->clientes_x_trabajador($_GET["idtrabajador"]);
        //Vamos a declarar un array
    
        $data = array(); $count =1;
    
        while ($reg = $rspta['data']->fetch_object()) {
          // Mapear el valor numérico a su respectiva descripción      
    
          $img = empty($reg->foto_perfil) ? 'no-perfil.jpg' : $reg->foto_perfil ;
    
          $data[] = array(
            "0" => $count++,                        
            "1" =>  '<span class="text-primary">'.$reg->nombre_completo.'</span> <br> <span class="text-muted">'.$reg->tipo_doc .': '. $reg->numero_documento.'</span> ',
            "2" => '<div class="text-start font-size-12px" >
              <span class="d-block text-primary fw-semibold"> <i class="bx bx-broadcast bx-burst fa-1x" ></i> ' . $reg->ip_antena . '</span>
              <span class="text-muted"><i class="bx bx-wifi bx-burst" ></i>' . $reg->ip_personal . '</span>
            </div>',
            "3" => $reg->fecha_cancelacion,
          );
        }
        $results = array(
          'status'=> true,
          "sEcho" => 1, //Información para el datatables
          "iTotalRecords" => count($data),  //enviamos el total registros al datatable
          "iTotalDisplayRecords" => count($data),  //enviamos el total registros a visualizar
          "aaData" => $data
        );
        echo json_encode($results);
    
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
?>
