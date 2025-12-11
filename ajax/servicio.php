<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['servicio'] == 1) {
   
    require_once "../modelos/Producto.php";
    require_once "../modelos/Servicio.php";

    $productos = new Producto();    
    $servicios = new Servicio();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idproducto     = isset($_POST["idproducto"])? limpiarCadena($_POST["idproducto"]):"";

    $idsunat_unidad_medida  = isset($_POST["idsunat_unidad_medida"])? limpiarCadena($_POST["idsunat_unidad_medida"]):"";
    $idcategoria    = isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
    $idmarca        = isset($_POST["idmarca"])? limpiarCadena($_POST["idmarca"]):"";
    $idubicacion        = isset($_POST["idubicacion"])? limpiarCadena($_POST["idubicacion"]):"";

    $codigo_alterno = isset($_POST["codigo_alterno"])? limpiarCadena($_POST["codigo_alterno"]):"";
    $tipo           = isset($_POST["tipo"])? limpiarCadena($_POST["tipo"]):"";
    $idcategoria    = isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
    $nombre         = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
    $descripcion    = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
    $precio_v       = isset($_POST["precio_v"])? limpiarCadena($_POST["precio_v"]):"";
    $stock          = isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"";
    
    switch ($_GET["op"]){

      case 'listar_tabla':
        $rspta = $servicios->listar_tabla();
        $data = []; $count = 1;
        if($rspta['status'] == true){
          foreach($rspta['data'] as $key => $value){
            $img = empty($value['imagen']) ? 'no-servicio.png' : $value['imagen'];
            $data[]=[
              "0" => $count++,
              "1" =>  '<div class="hstack gap-2 fs-15 text-center">' .
                        '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_servicio('.($value['idproducto']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                        '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_papelera_servicio('.$value['idproducto'].'.,\''.$value['nombre'].'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
                      '</div>',
              "2" =>  ('<i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno']),
              "3" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/servicios/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                <div>
                  <h6 class="d-block fw-semibold text-primary">'.$value['nombre'] .'</h6>
                  <span class="d-block fs-12 text-muted">UM: <b>'.$value['unidad_medida'].'</b></span> 
                </div>
              </div>',
              "4" => ($value['stock']),
              "5" => ($value['precio_venta']),
              "6" => '<textarea class="textarea_datatable bg-light"  readonly>' .($value['descripcion']). '</textarea>',
              "7" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',
              
              "8" => ($value['nombre']),
              "8" => ($value['unidad_medida']),
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
        //guardar f_img_fondo fondo
        if ( !file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']) ) {
          $img_servicio = $_POST["imagenactual"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["imagen"]["name"]);
          $flat_img = true;
          $img_servicio = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/modulo/servicios/" . $img_servicio);          
        }        

        if ( empty($idproducto) ) { #Creamos el registro

          $rspta = $servicios->insertar($tipo, $codigo_alterno, $idcategoria, $idsunat_unidad_medida, $idmarca, $idubicacion, $nombre, $descripcion, 
          $precio_v, $stock, $img_servicio);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img == true || empty($img_servicio)) {
            $datos_f1 = $servicios->mostrar($idproducto);
            $img1_ant = $datos_f1['data']['imagen'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/servicios/" . $img1_ant); }         
          }  
        
          $rspta = $servicios->editar($idproducto, $tipo, $codigo_alterno, $idcategoria, $idsunat_unidad_medida, $idmarca, $idubicacion, $nombre, $descripcion, $precio_v, $stock, $img_servicio);
          echo json_encode($rspta, true);
        }        

      break; 

      case 'mostrar' :
        $rspta = $servicios->mostrar($idproducto);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $servicios->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break; 

      case 'papelera':
        $rspta = $servicios->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
      case 'validar_code_producto':
        $rspta = $productos->validar_code_producto($_GET["idproducto"], $_GET["codigo_alterno"]);
        echo json_encode($rspta, true);
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