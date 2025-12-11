<?php
require_once "../sunat/ConfigSunat.php";

ob_start();

  if (strlen(session_id()) < 1) { session_start(); }

  require_once "../modelos/Catalogos_y_codigos.php";
  require_once "../modelos/Tipo_de_tributos.php";
  require_once "../modelos/Documento_de_identidad.php";


  $catalogo_y_codigo  = new Catalogo_y_codigo();  
  $tp_tributo         = new Tipo_de_tributos();
  $doc_identidad      = new Documento_de_identidad();

  date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
  $imagen_error = "this.src='../dist/svg/404-v2.svg'";
  $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

  $nombre       = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
  $abreviatura  = isset($_POST["abrt"]) ? limpiarCadena($_POST["abrt"]) : "";
  $codigo       = isset($_POST["codg"]) ? limpiarCadena($_POST["codg"]) : "";

  switch ($_GET["op"]){    

    case 'listar_tabla_tipo_tributo':
      $rspta = $tp_tributo->listar_tabla();
      $data = []; $count = 1;
      if($rspta['status'] == true){
        foreach($rspta['data'] as $key => $value){
          $data[]=[
            "0" => $count++,
            "1" => $value['nombre'] ,
            "2" => $value['code_sunat'],
            "3" => $value['abreviatura'],
            "4" => $value['unece5153'],
            "5" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

    case 'listar_tabla_documento_identidad':
      $rspta = $doc_identidad->listar_tabla();
      $data = []; $count = 1;
      if($rspta['status'] == true){
        foreach($rspta['data'] as $key => $value){
          $data[]=[
            "0" => $count++,
            "1" => $value['nombre'],
            "2" => $value['code_sunat'],
            "3" => $value['abreviatura'],            
            "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

    case 'listar_tabla_afeccion_igv':
      $rspta = $catalogo_y_codigo->listar_tabla_afeccion_igv();
      $data = []; $count = 1;
      if($rspta['status'] == true){
        foreach($rspta['data'] as $key => $value){
          $data[]=[
            "0" => $count++,
            "1" => $value['nombre'],
            "2" => $value['codigo'],
            "3" => $value['codigo_tributario'],            
            "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

    case 'listar_tabla_codigo_nota_credito':
      $rspta = $catalogo_y_codigo->listar_codigo_nota_credito();
      $data = []; $count = 1;
      if($rspta['status'] == true){
        foreach($rspta['data'] as $key => $value){
          $data[]=[
            "0" => $count++,
            "1" => $value['nombre'],
            "2" => $value['codigo'],        
            "3" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

    case 'listar_tabla_codigo_nota_debito':
      $rspta = $catalogo_y_codigo->listar_codigo_nota_debito();
      $data = []; $count = 1;
      if($rspta['status'] == true){
        foreach($rspta['data'] as $key => $value){
          $data[]=[
            "0" => $count++,
            "1" => $value['nombre'],
            "2" => $value['codigo'],        
            "3" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

    case 'listar_tabla_codigo_valor_venta':
      $rspta = $catalogo_y_codigo->listar_codigo_valor_venta();
      $data = []; $count = 1;
      if($rspta['status'] == true){
        foreach($rspta['data'] as $key => $value){
          $data[]=[
            "0" => $count++,
            "1" => $value['nombre'],
            "2" => $value['codigo'],        
            "3" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
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

    default: 
      $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
    break;
  }

ob_end_flush();