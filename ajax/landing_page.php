<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['configuracion'] == 1) {

    require_once "../modelos/Landing_page.php";

    $landing_page = new landing_page();

    $idpersona_cliente      = isset($_POST["idpersona_cliente"]) ? limpiarCadena($_POST["idpersona_cliente"]) : "";
    $descripcion_comentario = isset($_POST["descripcion_comentario"]) ? limpiarCadena($_POST["descripcion_comentario"]) : "";
    $puntuacion             = isset($_POST["puntuacionc"]) ? limpiarCadena($_POST["puntuacionc"]) : "";
    $fecha                  = isset($_POST["fecha_comentarioc"]) ? limpiarCadena($_POST["fecha_comentarioc"]) : "";

    $idpersona_trabajador = isset($_POST["idpersona_trabajador"]) ? limpiarCadena($_POST["idpersona_trabajador"]) : "";
    $descripcion_trabj    = isset($_POST["descripcion_trabj"]) ? limpiarCadena($_POST["descripcion_trabj"]) : "";

    $idplan          = isset($_POST["idplan"]) ? limpiarCadena($_POST["idplan"]) : "";
    $caracteristicas = isset($_POST["caracteristicas"]) ? limpiarCadena($_POST["caracteristicas"]) : "";

    $idpreguntas_frecuentes = isset($_POST["idpreguntas_frecuentes"]) ? limpiarCadena($_POST["idpreguntas_frecuentes"]) : "";
    $pregunta_pf            = isset($_POST["pregunta_pf"]) ? limpiarCadena($_POST["pregunta_pf"]) : "";
    $respuesta_pf           = isset($_POST["respuesta_pf"]) ? limpiarCadena($_POST["respuesta_pf"]) : "";


    switch ($_GET["op"]) {

      // <<<<<<<<<<<<<<<<< C O M E N T A R I O   C L I E N T E >>>>>>>>>>>>>>>>>
      case 'guardar_editar_comentarioC':
        $rspta = $landing_page->guardar_editar_comentarioC($idpersona_cliente,$descripcion_comentario,$puntuacion, $fecha);
        echo json_encode($rspta, true);
      break;

      case 'tabla_de_comentariosC':
        $rspta = $landing_page->tabla_comentario_cliente();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $numeroEstrellas = $value['landing_puntuacion'];
            // Genera las estrellas en base al número obtenido
            $estrellasHTML = '';
            for ($i = 0; $i < 5; $i++) {
              if ($i < $numeroEstrellas) {
                $estrellasHTML .= '<i class="ri-star-fill text-warning"></i>'; // Estrella llena
              } else {
                $estrellasHTML .= '<i class="ri-star-line text-warning"></i>'; // Estrella vacía
              }
            }  

            $imagen_perfil = empty($value['foto_perfil']) ? 'no-perfil.jpg' :   $value['foto_perfil'];

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_cliente(' . $value['idpersona_cliente'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                      '<button class="btn btn-icon btn-sm btn-info-light product-btn" onclick="editar_estado_landing_ccomentario(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['landing_estado']) . '\')" >'.
                        (($value['landing_estado'] == '1') ? '</i> <i class="fe fe-eye" data-bs-toggle="tooltip" title="Visible"></i>' : '</i> <i class="fe fe-eye-off" data-bs-toggle="tooltip" title="Oculto"></i>') .
                      '</button>',
              "2" => $value['landing_fecha'],
              
              "3" =>'<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Cliente">
                          <span class="avatar"> <img src="../assets/modulo/persona/perfil/'.$imagen_perfil.'" alt=""> </span>
                        </div>
                        <div>
                          <span class="d-block fw-semibold text-primary">'. $value['nombre_completo'] .'</span>
                          <span class="text-muted">Centro Poblado: '. $value['centro_poblado'] .'</span>
                        </div>
                      </div>',
              "4" => ($value['landing_descripcion'] !== null && $value['landing_descripcion'] !== '') ? '<div style="overflow: auto; resize: vertical; height: 70px;">'. $value['landing_descripcion'] .'</div>' : '<span class="badge bg-danger-transparent">Sin comentarios</span>',
              "5" => '<span>'.$estrellasHTML.'</span>',
              "6" => ($value['landing_estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Visible</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Oculto</span>',
              "7" => $value['nombre_completo'],
              "8" => $value['centro_poblado'],
              "9" => $value['landing_descripcion'],
              "10" => $value['landing_puntuacion']

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

      case 'editar_comentarioVisible':
        $rspta = $landing_page->editar_comentarioVisible($_POST['idpersona_cliente'], $_POST['landing_estado']);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_cliente':
        $rspta = $landing_page->mostrar_clienteC($idpersona_cliente);
        echo json_encode($rspta, true);
      break;


      // <<<<<<<<<<<<<<<<<<<<<<<< T R A B A J A D O R E S >>>>>>>>>>>>>>>>>>>>>>

      case 'guardar_editar_trabj':
        $rspta = $landing_page->guardar_editar_trabj($idpersona_trabajador,$descripcion_trabj);
        echo json_encode($rspta, true);
      break;
      
      case 'tabla_de_trabj':

        $rspta = $landing_page->tabla_de_trabj();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_trabajador(' . $value['idpersona_trabajador'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                     ' <button class="btn btn-icon btn-sm btn-info-light product-btn" onclick="editar_estado_landing_trabj(' . $value['idpersona_trabajador'] . ', \'' . encodeCadenaHtml($value['landing_estado']) . '\')" >'.
                     (($value['landing_estado'] == '1') ? '</i> <i class="fe fe-eye" data-bs-toggle="tooltip" title="Visible"></i>' : '</i> <i class="fe fe-eye-off" data-bs-toggle="tooltip" title="Oculto"></i>') .
                     '</button>',         
              "2" => '<div class="d-flex flex-fill align-items-center">
              <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Brartnet">
                <span class="avatar"> <img src="../assets/images/brand-logos/logo-short.png" alt=""> </span>
              </div>
              <div>
                <span class="d-block fw-semibold text-primary">'. $value['nombre_completo'] .'</span>
                <span class="text-muted">Cargo: '. $value['cargo'] .'</span>
              </div>
            </div>',
              "3" => '<div style="overflow: auto; resize: vertical; height: 70px;">'. $value['landing_descripcion'] .'</div>',
              "4" => ($value['landing_estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Visible</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Oculto</span>',
              "5" => $value['nombre_completo'],
              "6" => $value['cargo'],
              "7" => $value['landing_descripcion']
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

      case 'editar_trabjVisible':
        $rspta = $landing_page->editar_trabjVisible($_POST['idpersona_trabajador'], $_POST['landing_estado']);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_trabj':
        $rspta = $landing_page->mostrar_trabj($idpersona_trabajador);
        echo json_encode($rspta, true);
      break;



      // <<<<<<<<<<<<<<<<<<<<<<<< P L A N E S >>>>>>>>>>>>>>>>>>>>>>

      case 'tabla_principal_plan':

        $rspta = $landing_page->tabla_planes();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_plan(' . $value['idplan'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                     ' <button class="btn btn-icon btn-sm btn-info-light product-btn" onclick="editar_estado_landing_plan(' . $value['idplan'] . ', \'' . encodeCadenaHtml($value['landing_estado']) . '\')" >'.
                     (($value['landing_estado'] == '1') ? '</i> <i class="fe fe-eye" data-bs-toggle="tooltip" title="Visible"></i>' : '</i> <i class="fe fe-eye-off" data-bs-toggle="tooltip" title="Oculto"></i>') .
                     '</button>',         
              "2" => $value['nombre'],
              "3" => $value['costo'],
              "4" => ($value['landing_estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Visible</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Oculto</span>'
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

      case 'editar_planVisible':
        $rspta = $landing_page->editar_planVisible($_POST['idplan'], $_POST['landing_estado']);
        echo json_encode($rspta, true);
      break;

      case 'guardar_editar_plan':
        $rspta = $landing_page->guardar_editar_plan($idplan, $caracteristicas);
        echo json_encode($rspta, true);
      break;


      // <<<<<<<<<<<<<<<<<<<<<<<< P R E G U N T A S   F R E C U E N T E S >>>>>>>>>>>>>>>>>>>>>>

      case 'guardar_y_editar_pregFrec':
        if (empty($idpreguntas_frecuentes)) {
          $rspta = $landing_page->insertar($pregunta_pf, $respuesta_pf);
          echo json_encode($rspta, true);
        } else {
          $rspta = $landing_page->editar($idpreguntas_frecuentes, $pregunta_pf, $respuesta_pf);
          echo json_encode($rspta, true);
        }
      break;

      case 'desactivar':
        $rspta = $landing_page->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $landing_page->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_pregFrec':
        $rspta = $landing_page->mostrar_pregFrec($idpreguntas_frecuentes);
        echo json_encode($rspta, true);
      break;

      case 'tabla_principal_PregFerct':
        $rspta = $landing_page->tabla_principal_PregFerct();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_pregFrec(' . $value['idpreguntas_frecuentes'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_pregFrec(' . $value['idpreguntas_frecuentes'] . ', \'' . encodeCadenaHtml($value['pregunta']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>',         
              "2" => $value['pregunta'],
              "3" => '<div style="overflow: auto; resize: vertical; height: 70px;">'. $value['respuesta'] .'</div>',
              "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'

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

      case 'salir':
        //Limpiamos las variables de sesión
        session_unset();
        //Destruìmos la sesión
        session_destroy();
        //Redireccionamos al login
        header("Location: ../index.php");
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
      break;

      }


  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}
ob_end_flush();
