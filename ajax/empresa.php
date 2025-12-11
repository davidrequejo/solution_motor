<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
  if ($_SESSION['empresa'] == 1) {

    require_once "../modelos/Empresa.php";
    $empresa = new Empresa();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idempresa      = isset($_POST["idempresa"]) ? limpiarCadena($_POST["idempresa"]) : "";
    $tipo_doc       = isset($_POST["tipo_doc"]) ? limpiarCadena($_POST["tipo_doc"]) : "";
    $documento      = isset($_POST["documento"]) ? limpiarCadena($_POST["documento"]) : "";
    $razon_social   = isset($_POST["razon_social"]) ? limpiarCadena($_POST["razon_social"]) : "";
    $nomb_comercial = isset($_POST["nomb_comercial"]) ? limpiarCadena($_POST["nomb_comercial"]) : "";
    $telefono1      = isset($_POST["telefono1"]) ? limpiarCadena($_POST["telefono1"]) : "";
    $telefono2      = isset($_POST["telefono2"]) ? limpiarCadena($_POST["telefono2"]) : "";
    $web            = isset($_POST["web"]) ? limpiarCadena($_POST["web"]) : "";
    $web_consulta   = isset($_POST["web_consulta"]) ? limpiarCadena($_POST["web_consulta"]) : "";
    $correo         = isset($_POST["correo"]) ? limpiarCadena($_POST["correo"]) : "";
    $logo_c_r       = isset($_POST["logo_c_r"]) ? limpiarCadena($_POST["logo_c_r"]) : "";
    $img_logo       = isset($_POST["doc_old_1"]) ? limpiarCadena($_POST["doc_old_1"]) : "";

    $banco1   = isset($_POST["banco1"]) ? limpiarCadena($_POST["banco1"]) : "";
    $cuenta1  = isset($_POST["cuenta1"]) ? limpiarCadena($_POST["cuenta1"]) : "";
    $cci1     = isset($_POST["cci1"]) ? limpiarCadena($_POST["cci1"]) : "";
    $banco2   = isset($_POST["banco2"]) ? limpiarCadena($_POST["banco2"]) : "";
    $cuenta2  = isset($_POST["cuenta2"]) ? limpiarCadena($_POST["cuenta2"]) : "";
    $cci2     = isset($_POST["cci2"]) ? limpiarCadena($_POST["cci2"]) : "";
    $banco3   = isset($_POST["banco3"]) ? limpiarCadena($_POST["banco3"]) : "";
    $cuenta3  = isset($_POST["cuenta3"]) ? limpiarCadena($_POST["cuenta3"]) : "";
    $cci3     = isset($_POST["cci3"]) ? limpiarCadena($_POST["cci3"]) : "";
    $banco4   = isset($_POST["banco4"]) ? limpiarCadena($_POST["banco4"]) : "";
    $cuenta4  = isset($_POST["cuenta4"]) ? limpiarCadena($_POST["cuenta4"]) : "";
    $cci4     = isset($_POST["cci4"]) ? limpiarCadena($_POST["cci4"]) : "";

    $codg_pais        = isset($_POST["codg_pais"]) ? limpiarCadena($_POST["codg_pais"]) : "";
    $domicilio_fiscal = isset($_POST["domicilio_fiscal"]) ? limpiarCadena($_POST["domicilio_fiscal"]) : "";
    $distrito         = isset($_POST["distrito"]) ? limpiarCadena($_POST["distrito"]) : "";
    $departamento     = isset($_POST["departamento"]) ? limpiarCadena($_POST["departamento"]) : "";
    $provincia        = isset($_POST["provincia"]) ? limpiarCadena($_POST["provincia"]) : "";
    $ubigeo           = isset($_POST["ubigeo"]) ? limpiarCadena($_POST["ubigeo"]) : "";
    $codg_ubigeo      = isset($_POST["codg_ubigeo"]) ? limpiarCadena($_POST["codg_ubigeo"]) : "";
    $referencia       = isset($_POST["referencia"]) ? limpiarCadena($_POST["referencia"]) : "";

    switch ($_GET["op"]){

      case 'listar_tabla':
        $rspta = $empresa->listar_tabla();
        $data = []; $count = 1;
        if($rspta['status'] == true){
          foreach($rspta['data'] as $key => $value){
            $data[]=[
              "0" => $count++,
              "1" =>  '<div class="hstack gap-2 fs-15">' .
                        '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_editar_empresa('.($value['idempresa']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                        '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_papelera_empresa('.$value['idempresa'].'.,\''.$value['nombre_razon_social'].'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
                        '<button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_detalles_empresa('.($value['idempresa']).')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button>'.
                      '</div>',
              "2" =>  '<div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                        <span class="text-center"> <img class="w-50px" src="../assets/modulo/empresa/logo/' . ($value['logo']) . '" alt="" onclick="ver_img(\'' . ($value['logo']) . '\', \'' . encodeCadenaHtml(($value['nombre_razon_social'])) . '\')"> </span>
                      </div>',
              "3" => '<div class="d-flex flex-fill align-items-center">
                        <div>
                          <span class="d-block fw-semibold text-primary">'.($value['nombre_razon_social']).'</span>
                          <span class="text-muted">'.($value['tipo_documento_v2']) .' '. ($value['numero_documento']) .'</span>
                        </div>
                      </div>',
              "4" => '<div >' .
                        '<b>Banco: </b> ' . $value['banco1'] . '<br>' .
                        '<b>Cuenta </b>: ' . $value['cuenta1'] . '<br>' .
                        '<b>CCI </b>: ' . $value['cci1'] . '<br>' .
                      '</div>',
              "5" => ($value['domicilio_fiscal']),
              "6" => '<div >' .
                        '<b>Telefono </b>: ' . $value['telefono1'] . '<br>' .
                        '<b>Correo </b>: ' . $value['correo'] . '<br>' .
                        '<b>web </b>: ' . $value['web'] . '<br>' .
                      '</div>',
              "7" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

      case 'guardar_editar':
        //guardar img_comprob fondo
        if ( !file_exists($_FILES['doc1']['tmp_name']) || !is_uploaded_file($_FILES['doc1']['tmp_name']) ) {
          $img_logo = $_POST["doc_old_1"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["doc1"]["name"]);
          $flat_img = true;
          $img_logo = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc1"]["tmp_name"], "../assets/modulo/empresa/logo/" . $img_logo);          
        }

        if ( empty($idempresa) ) { #Creamos el registro

          $rspta = $empresa->insertar($tipo_doc, $documento, $razon_social, $nomb_comercial, $telefono1, $telefono2, 
          $web, $web_consulta, $correo, $logo_c_r, $img_logo, $banco1,   $cuenta1,  $cci1, $banco2,   $cuenta2,  $cci2, $banco3,   
          $cuenta3,  $cci3, $banco4,   $cuenta4,  $cci4, $codg_pais, $domicilio_fiscal, $distrito, $departamento, $provincia, $ubigeo, 
          $codg_ubigeo, $referencia);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          $rspta = $empresa->editar($idempresa, $tipo_doc, $documento, $razon_social, $nomb_comercial, $telefono1, $telefono2, 
          $web, $web_consulta, $correo, $logo_c_r, $img_logo, $banco1,   $cuenta1,  $cci1, $banco2,   $cuenta2,  $cci2, $banco3,   
          $cuenta3,  $cci3, $banco4,   $cuenta4,  $cci4, $codg_pais, $domicilio_fiscal, $distrito, $departamento, $provincia, $ubigeo, 
          $codg_ubigeo, $referencia);
          echo json_encode($rspta, true);
        }
      break;

      case 'mostrar_empresa':
        $rspta = $empresa->mostrar_empresa($idempresa);
        echo json_encode($rspta, true);
      break;

      case 'desactivar':
        $rspta = $empresa->desactivar($_GET['id_tabla']);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $empresa->eliminar($_GET['id_tabla']);
        echo json_encode($rspta, true);
      break;

      case 'select_banco':
        $rspta = $empresa->select_banco();
        // echo json_encode($rspta, true); die;
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['nombre'] . '"  >' . $value['nombre']  . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );

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
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}
ob_end_flush();