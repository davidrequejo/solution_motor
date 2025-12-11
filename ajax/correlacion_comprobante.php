<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }
require_once "../modelos/Correlacion_comprobante.php";
$correlacion_compb = new Correlacion_comprobante();

date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
$imagen_error = "this.src='../dist/svg/404-v2.svg'";
$toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

$id = isset($_POST["idtipo_comprobante"]) ? limpiarCadena($_POST["idtipo_comprobante"]) : "";

$codigo   = isset($_POST["codg"]) ? limpiarCadena($_POST["codg"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$abreviatura = isset($_POST["abrt"]) ? limpiarCadena($_POST["abrt"]) : "";
$serie = isset($_POST["serie"]) ? limpiarCadena($_POST["serie"]) : "";
$numero = isset($_POST["numero"]) ? limpiarCadena($_POST["numero"]) : "";
$un1001 = isset($_POST["un1001"]) ? limpiarCadena($_POST["un1001"]) : "";


switch ($_GET["op"]){

  case 'listar_tabla':
    $rspta = $correlacion_compb->listar_tabla();
    $data = []; $count = 1;
    if($rspta['status'] == true){
      foreach($rspta['data'] as $key => $value){
        $data[]=[
          "0" => $count++,
          "1" =>  '<div class="hstack gap-2 fs-15">' .
            '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_correlacion_compb('.($value['idtipo_comprobante']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
            ($value['estado'] == '1' ? 
            '<button class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_papelera_correlacion_compb('.$value['idtipo_comprobante'].'.,\''.$value['nombre'].'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>' :
            ''
            ).
          '</div>',
          "2" => $value['idtipo_comprobante_v2'],
          "3" => $value['codigo'],
          "4" => $value['nombre'],
          "5" => $value['abreviatura'],
          "6" => $value['serie'],
          "7" => $value['numero'],
          "8" => $value['un1001'],
          "9" => $value['estado'] == '1' ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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
    $validar = $correlacion_compb->validar($nombre);

    if (empty($id)) {

      echo json_encode(['status' => 'error_personalizado',  'titulo' =>'No tienes permiso de crear', 'message' => 'Los permisso de creacion estan bloqueados, para no tener problemas con la facuracion electronica.', 'data' => [], 'id_tabla' => '']);      

    } else {
      $rspta = $correlacion_compb->editar($id, $codigo, $nombre, $abreviatura, $serie, $numero, $un1001);
      echo json_encode(['status' => 'modificado', 'data' => $rspta]);
    }
    
  break;

  case 'mostrar_correlacion_compb':
    $rspta = $correlacion_compb->mostrar($id);
    echo json_encode($rspta, true);
  break;

  case 'eliminar':
    $rspta = $correlacion_compb->eliminar($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  case 'desactivar':
    $rspta = $correlacion_compb->desactivar($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  case 'listar_crl_comprobante':
    $rspta = $correlacion_compb->listar_crl_comprobante($_GET["codigo_sunat"]); $cont = 1; $data = "";
    if($rspta['status'] == true){
      foreach ($rspta['data'] as $key => $value) {
        $data .= '<option  value=' . $value['idtipo_comprobante']  . '>' . $value['tipo_comprobante'] . '</option>';
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

ob_end_flush();