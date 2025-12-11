<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }//Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['gastos_trabajador'] == 1) {

    require_once "../modelos/Incidencias.php";
    $incidencias = new Incidencias();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';


    $idincidencia       = isset($_POST["idincidencia"]) ? limpiarCadena($_POST["idincidencia"]) : "";
    $actividad          = isset($_POST["actividad"]) ? limpiarCadena($_POST["actividad"]) : "";
    $creacionfecha      = isset($_POST["creacionfecha"]) ? limpiarCadena($_POST["creacionfecha"]) : "";
    $prioridad          = isset($_POST["prioridad"]) ? limpiarCadena($_POST["prioridad"]) : "";
    $categoria          = isset($_POST["categoria"]) ? limpiarCadena($_POST["categoria"]) : "";
    $actividad_detalle  = isset($_POST["actividad_detalle"]) ? limpiarCadena($_POST["actividad_detalle"]) : "";
    //---------------------------
    $id_incidenciaupdate  = isset($_POST["id_incidenciaupdate"]) ? limpiarCadena($_POST["id_incidenciaupdate"]) : "";
    $fecha_fin            = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";


    $fecha_fin_inc         = isset($_POST["fecha_fin_inc"]) ? limpiarCadena($_POST["fecha_fin_inc"]) : "";
    $estado_inc            = isset($_POST["estado_inc"]) ? limpiarCadena($_POST["estado_inc"]) : "";

    
     //fecha_fin_inc,     //estado_inc
    // idincidencia: 
    // actividad: 
    // id_trabajador[]:
    // creacionfecha: 
    // prioridad: 

    switch ($_GET["op"]){

      case 'guardar_editar':

        if ( empty($idincidencia) ) { #Creamos el registro

          $rspta = $incidencias->insertar($actividad, $creacionfecha, $prioridad,$_POST["id_trabajador"],$categoria,$actividad_detalle);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          $rspta = $incidencias->editar($idincidencia,$actividad, $creacionfecha, $prioridad,$_POST["id_trabajador"],$categoria,$actividad_detalle,$fecha_fin_inc,$estado_inc);
          echo json_encode($rspta, true);
        }


      break;
      case 'guardar_fecha_fin':

        if ( !empty($id_incidenciaupdate) ) {

          $rspta = $incidencias->insertarfecha_fin($id_incidenciaupdate, $fecha_fin);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          $rspta = $incidencias->editar($idincidencia,$actividad, $creacionfecha, $prioridad,$_POST["id_trabajador"],$categoria,$actividad_detalle);
          echo json_encode($rspta, true);
        }


      break;

      // case 'listar_tabla':
      //   $rspta = $incidencias->listar_tabla();
      //   $data = []; $count = 1;

      //   if($rspta['status'] == true){

      //     // foreach($rspta['data'] as $key => $value){
      //     while ($reg = $rspta['data']->fetch_object()) {

      //       // -------------- CONDICIONES --------------      
      //       $img = empty($reg->foto_perfil_trabajador) ? 'no-perfil.jpg'  : $reg->foto_perfil_trabajador;
      //       $data[] = [
      //         "0" => $count++,
      //         "1" =>  '<div class="hstack gap-2 fs-15">' .
      //           '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_editar_gdt('.($reg->idincidencia).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
      //           '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_gasto('.$reg->idincidencia.', \''.$reg->trabajador.'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
      //           '<button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_detalles_gasto('.($reg->idincidencia).')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button>'.
      //         '</div>',
      //         "2" => ($reg->fecha_ingreso),
      //         "3" => '<div class="d-flex flex-fill align-items-center">
      //           <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
      //             <span class="avatar"> <img src="../assets/modulo/persona/perfil/'.$img.'" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml($reg->trabajador ) . '\')"> </span>
      //           </div>
      //           <div>
      //             <span class="d-block fw-semibold text-primary">'.$reg->trabajador.'</span>
      //             <span class="text-muted">'.$reg->tipo_documento_nombre .' '. $reg->numero_documento.'</span>
      //           </div>
      //         </div>',
      //         "4" => $reg->prioridad .': '. $reg->serie_comprobante,
      //         "5" => $reg->precio_con_igv,
      //         "6" => '<textarea class="textarea_datatable bg-light"  readonly>' .($reg->descripcion_comprobante). '</textarea>',
      //         "7" => !empty($reg->comprobante) ? '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_comprobante('.($reg->idincidencia).');" data-bs-toggle="tooltip" title="Ver"><i class="ti ti-file-dollar fs-lg"></i></button></div>' : 
      //           '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-danger-light" data-bs-toggle="tooltip" title="no encontrado"><i class="ti ti-file-alert fs-lg"></i></button></div>',
              
      //         "8" => $reg->trabajador,
      //         "9" => $reg->tipo_documento_nombre,
      //         "10" => $reg->numero_documento,
      //         "11" => $reg->proveedor,
      //         "12" => $reg->day_name,
      //         "13" => $reg->month_name,
      //         "14" => $reg->precio_sin_igv,
      //         "15" => $reg->precio_igv,
      //         "16" => $reg->descripcion_gasto,
      //         "17" => $reg->descripcion_comprobante,
      //       ];
      //     }
      //     $results =[
      //       'status'=> true,
      //       "sEcho" => 1,
      //       "iTotalRecords" => count($data),
      //       "iTotalDisplayRecords" => count($data),
      //       "aaData" => $data
      //     ];
      //     echo json_encode($results, true);

      //   } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      // break;

      case 'desactivar':
        $rspta = $incidencias->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $incidencias->eliminar($_POST["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      // case 'listar_trabajador':
      //   $rspta = $incidencias->listar_trabajador(); 
      //   echo json_encode($rspta, true);
 
      // break;

      case 'listar_trabajador':
        $rspta = $incidencias->listar_trabajador(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona_trabajador']  . '>' . $value['nombre_razonsocial'] .'</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'mostrar':
        $rspta = $incidencias->mostrar( $_POST['idincidencia']);
        echo json_encode($rspta, true);
      break;

      case 'view_incidencias': 
        // todos_cat,todos_prio
        $rspta = $incidencias->view_incidencias($_POST['id_categoria'],$_POST['prioridad'],$_POST['estado_inc']); 
        echo json_encode($rspta, true);
      break;

      case 'categorias_incidencias':
        $rspta = $incidencias->categorias_incidencias();
        echo json_encode($rspta, true);
      break;

      case "select2_cat_inc":
        $rspta = $incidencias->categorias_incidencias();  $cont = 1; $data = "";

        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idincidencia_categoria']  . '>' . $value['nombre'] .'</option>';
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