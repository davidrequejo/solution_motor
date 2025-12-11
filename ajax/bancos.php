<?php
  require_once "../sunat/ConfigSunat.php";

  ob_start();
  if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

  if (!isset($_SESSION["user_nombre"])) {
    $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
  } else {

    if ($_SESSION['general'] == 1) {

      require_once "../modelos/Bancos.php";
      $bancos = new Bancos();

      date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
      $imagen_error = "this.src='../assets/svg/user_default.svg'";
      $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';
      $scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/fun_route/admin/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/admin/');


      $idbancos = isset($_POST["idbancos"]) ? limpiarCadena($_POST["idbancos"]) : "";
      $nombre_b = isset($_POST["nombre_b"]) ? limpiarCadena($_POST["nombre_b"]) : "";
      $alias = isset($_POST["alias"]) ? limpiarCadena($_POST["alias"]) : "";

      $formato_cci = isset($_POST["formato_cci"]) ? limpiarCadena($_POST["formato_cci"]) : "";
      $formato_cta = isset($_POST["formato_cta"]) ? limpiarCadena($_POST["formato_cta"]) : "";
      $formato_detracciones = isset($_POST["formato_detracciones"]) ? limpiarCadena($_POST["formato_detracciones"]) : "";

      $imagen = isset($_POST["imagen1"]) ? limpiarCadena($_POST["imagen1"]) : "";


      switch ($_GET["op"]) {

        case 'guardar_editar_bancos':

          // imgen
          if (!file_exists($_FILES['imagen1']['tmp_name']) || !is_uploaded_file($_FILES['imagen1']['tmp_name'])) {

            $imagen = $_POST["imagen1_actual"];
            $flat_img = false;

          } else {

            $ext = explode(".", $_FILES["imagen1"]["name"]);
            $flat_img = true;
            $imagen = $date_now .'__'. random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
            move_uploaded_file($_FILES["imagen1"]["tmp_name"], "../assets/modulo/bancos/" . $imagen);
          
          }

          if (empty($idbancos)) {      

            $rspta = $bancos->insertar_banco($nombre_b, $alias, $formato_cta, $formato_cci, $formato_detracciones, $imagen);
            echo json_encode( $rspta, true);

          } else {

            // validamos si existe LA IMG para eliminarlo
            if ($flat_img == true) {

              $datos_f = $bancos->obtenerImg($idbancos);
              $img_ant = $datos_f['data']['icono'];
              if ($img_ant != "") {  unlink("../assets/modulo/bancos/" . $img_ant); }
            }

            $rspta = $bancos->editar_banco($idbancos, $nombre_b, $alias, $formato_cta, $formato_cci, $formato_detracciones, $imagen);
            echo json_encode( $rspta, true);
          }
        break;

        case 'desactivar_banco':
          $rspta = $bancos->desactivar($_GET["id_tabla"]);
          echo json_encode( $rspta, true) ;
        break;

        case 'eliminar_banco':
          $rspta = $bancos->eliminar($_GET["id_tabla"]);
          echo json_encode( $rspta, true) ;
        break;

        case 'mostrar_banco':
          $rspta = $bancos->mostrar($idbancos);
          echo json_encode( $rspta, true) ;
        break;

        case 'tabla_principal_bancos':
          $rspta = $bancos->listar();
          $data = [];

          $cta = "00000000000000000000000000000"; $cci = "00000000000000000000000000000"; $detraccion = "00000000000000000000000000000";
          $cont=1;
          $imagen_error = "this.src='../assets/images/default/logo-sin-banco.svg'";
          $imagen = '';
          $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';
          
          if ($rspta['status'] == true) {

            foreach ($rspta['data'] as $key => $value) {

              if (empty($value['icono'])) { $imagen = 'logo-sin-banco.svg';  } else { $imagen = $value['icono'];   }

              $data[] = [
                "0"=>$cont++,
                "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_banco(' . $value['idbancos'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                      '<button class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_banco(' . $value['idbancos'] .', \''.encodeCadenaHtml($value['nombre']).'\')" data-bs-toggle="tooltip" title="Eliminar o papelera"><i class="ri-delete-bin-line"></i></button>',

                "2" =>  '<div class="d-flex flex-fill align-items-center">
                          <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/bancos/' . $imagen . '" alt="" onclick="ver_imagen_banco(\'' . $imagen . '\', \'' . encodeCadenaHtml($value['nombre']) . '\')"> </span></div>
                          <div>
                            <span class="d-block fw-semibold text-primary">'.$value['nombre'].'</span>
                          </div>
                        </div>',
                "3" => '<div class="textarea_datatable bg-light" style="overflow: auto; resize: vertical; height: 45px;">'.
                  '<span> <b>Formato CTA :</b>' . $value['formato_cta'] . '<br><b>Ej. cta: </b>' . darFormatoBanco($cta, ($value['formato_cta'])) .'</span> <br>'. 
                  '<span> <b>Formato CCI :</b>' . $value['formato_cci'] . '<br> <b>Ej. cci: </b>' . darFormatoBanco($cci, ($value['formato_cci'])) . '</span><br>'.
                  '<span> <b>Formato Detrac. :</b>' . $value['formato_detracciones'] . '<br>  
                  <b>Ej. cci: </b>' . darFormatoBanco($detraccion, ($value['formato_detracciones'])) . '</span>'. 
                '</div>',
                "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',
                "5" => $value['nombre'],
                "6" => $value['alias'],
                "7" => $value['formato_cta'],
                "8" => $value['formato_cci'],
                "9" => $value['formato_detracciones'],
              ];
            }
            $results = [
              'status'=> true,
              "sEcho" => 1, //Información para el datatables
              "iTotalRecords" => count($data), //enviamos el total registros al datatable
              "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
              "aaData" => $data,
            ];
            echo json_encode($results, true) ;
          } else {
            echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data'];
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
          $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
        break;
      }

    } else {
      $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
      echo json_encode($retorno);
    }  

  }


  function darFormatoBanco($numero, $formato) {
    $format_array = explode("-", $formato);
    $format_cuenta = "";
    $cont_format = 0;
    $indi = 0;

    foreach ($format_array as $indice => $key) {
      if ($key == "__" || $key == "0_" || $key == "1_" || $key == "2_" || $key == "3_" || $key == "4_" || $key == "5_" || $key == "6_" || $key == "7_" || $key == "8_" || $key == "9_") {
        $cont_format = $cont_format + 0;
      } else {
        if (intval($key) == 0) {
          $format_cuenta .= substr($numero, $cont_format, $key);

          $cont_format = $cont_format + intval($key); //$indi = $indice;
        } else {
          $format_cuenta .= substr($numero, $cont_format, $key) . '-';

          $cont_format = $cont_format + intval($key);
        }
      }
    }
    return substr($format_cuenta, 0, -1);
  }


  ob_end_flush();
?>