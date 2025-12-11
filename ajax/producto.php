<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['producto'] == 1) {
   

    require_once "../modelos/Producto.php";
    $productos = new Producto();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idproducto     = isset($_POST["idproducto"])? limpiarCadena($_POST["idproducto"]):"";

    $tipo           = isset($_POST["tipo"])? limpiarCadena($_POST["tipo"]):"";
    $codigo_alterno = isset($_POST["codigo_alterno"])? limpiarCadena($_POST["codigo_alterno"]):"";
    $idsucursal     = isset($_POST["idsucursal"])? limpiarCadena($_POST["idsucursal"]):"";
    $categoria      = isset($_POST["categoria"])? limpiarCadena($_POST["categoria"]):"";
    $u_medida       = isset($_POST["u_medida"])? limpiarCadena($_POST["u_medida"]):"";
    $marca          = isset($_POST["marca"])? limpiarCadena($_POST["marca"]):"";
    $ubicacion      = isset($_POST["ubicacion"])? limpiarCadena($_POST["ubicacion"]):"";
    $nombre         = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
    $descripcion    = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
    $cant_um        = isset($_POST["cant_um"])? limpiarCadena($_POST["cant_um"]):"";
    $stock          = isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"";
    $stock_min      = isset($_POST["stock_min"])? limpiarCadena($_POST["stock_min"]):"";
    $precio_v       = isset($_POST["precio_v"])? limpiarCadena($_POST["precio_v"]):"";
    $precio_c       = isset($_POST["precio_c"])? limpiarCadena($_POST["precio_c"]):"";
    $precio_x_mayor = isset($_POST["precio_x_mayor"])? limpiarCadena($_POST["precio_x_mayor"]):"";
    
    $idproducto_sucursal = isset($_POST["idproducto_sucursal"])? limpiarCadena($_POST["idproducto_sucursal"]):"";
    $idproducto_presentacion = isset($_POST["idproducto_presentacion"])? limpiarCadena($_POST["idproducto_presentacion"]):"";

    // PRESENTACION
    $idpresentacion          = isset($_POST["idpresentacion"])? limpiarCadena($_POST["idpresentacion"]):"";
    $idproducto_sucursal_ps        = isset($_POST["idproducto_sucursal_ps"])? limpiarCadena($_POST["idproducto_sucursal_ps"]):"";
    $umedida_ps              = isset($_POST["um_presentacion"])? limpiarCadena($_POST["um_presentacion"]):"";
    $cant_ps                 = isset($_POST["cant_ps"])? limpiarCadena($_POST["cant_ps"]):"";
    $nombre_presentacion     = isset($_POST["nombre_presentacion"])? limpiarCadena($_POST["nombre_presentacion"]):"";

    $precio_presentacion_und    = isset($_POST["precio_presentacion_und"])? limpiarCadena($_POST["precio_presentacion_und"]):"";
    $precio_presentacion_total  = isset($_POST["precio_presentacion_total"])? limpiarCadena($_POST["precio_presentacion_total"]):"";

    
    switch ($_GET["op"]){

      case 'listar_tabla':
        $rspta = $productos->listar_tabla($_GET["categoria"], $_GET["unidad_medida"], $_GET["marca"], $_GET["ubicacion"]);
        $data = []; $count = 2;
        if($rspta['status'] == true){
          foreach($rspta['data'] as $key => $value){
            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data[]=[
              "0" => $value['idproducto'] == 1 ? 1 : $count++,
              "1" => ($value['idproducto'] == 1 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' :
              '<div class="hstack gap-2 fs-15 text-center"> 
                <button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_producto('.($value['idproducto']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.              
              '<div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1 px-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">                
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="mostrar_detalle_producto(' . $value['idproducto'] . ');" ><i class="bi bi-eye"></i> Ver</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="mostrar_producto(' . $value['idproducto'] .',true);" ><i class="ri-file-copy-2-line fs-12"></i> Duplicar registro</a></li>                  
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="listar_presentacion(' . $value['idproducto_sucursal'] . '); limpiar_form_ps();" ><i class="bi bi-file-earmark-plus"></i> Presentaciones</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="mostrar_imprimir_codigo(' . $value['idproducto'] . ');" ><i class="bi bi-upc"></i> Imprimir Código</a></li>
                  <li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_papelera('.$value['idproducto'].',\''.$value['nombre'].'\');" ><i class="ri-delete-bin-line"></i> Eliminar</a></li>                  
                </ul>
              </div>'. 
              '</div>') ,
              "2" =>  ('<i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno']),
              "3" => '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                        <div>
                          <span class="d-block fw-semibold text-primary fs-13">'.$value['nombre'] .'</span>
                          <span class="text-nowrap fs-11 text-muted">Marca: <b>'.$value['marca'].'</b> | Categoría: <b>'.$value['categoria'].'</b></span> 
                        </div>
                      </div>',
              "4" =>   '<div class="fs-10 text-muted">' . $value['unidad_presentacion'] . '</div>',
                      
              "5" => ($value['stock']),
              "6" => ($value['precio_compra']),
              "7" => ($value['precio_venta']),
              "8" => '<textarea class="textarea_datatable bg-light"  readonly>' .($value['descripcion']). '</textarea>',
              "9" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',
              
              "10" =>($value['categoria']),
              "11" =>($value['marca']),
              "12" =>($value['nombre']),
              "13" =>($value['codigo']),
              "14" =>($value['codigo_alterno']),
              "15" =>($value['idproducto'])
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
        if ( !file_exists($_FILES['imagenProducto']['tmp_name']) || !is_uploaded_file($_FILES['imagenProducto']['tmp_name']) ) {
          $img_producto = $_POST["imagenactualProducto"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["imagenProducto"]["name"]);
          $flat_img = true;
          $img_producto = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["imagenProducto"]["tmp_name"], "../assets/modulo/productos/" . $img_producto);          
        }        

        //Verificamos la existencia del grupo de productos
        $idproducto_pp_set = isset($_POST["idproducto_presentacion_set"]) ? $_POST["idproducto_presentacion_set"] : null;
        $idproducto_p_set = isset($_POST["idproducto_set"]) ? $_POST["idproducto_set"] : null;
        $um_presentation = isset($_POST["um_presentation"]) ? $_POST["um_presentation"] : null;
        $cantidad = isset($_POST["cantidad"]) ? $_POST["cantidad"] : null;

        if ( empty($idproducto) ) { #Creamos el registro
          
          $rspta = $productos->insertar($tipo, $codigo_alterno, $idsucursal, $categoria, $u_medida, $marca, $ubicacion, $nombre, $descripcion, $cant_um, $stock, 
            $stock_min, $precio_v, $precio_c, $precio_x_mayor, $img_producto,
            // lista de productos agrupados
            $idproducto_pp_set, $idproducto_p_set, $cantidad, $um_presentation
          );
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img == true || empty($img_producto)) {
            $datos_f1 = $productos->mostrar($idproducto);
            $img1_ant = $datos_f1['data']['producto']['imagen'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/productos/" . $img1_ant); }         
          }  

          $rspta = $productos->editar($idproducto, $idproducto_sucursal,$idproducto_presentacion, $tipo, $codigo_alterno, $idsucursal, $categoria, $u_medida, $marca, $ubicacion, $nombre, $descripcion,
            $cant_um, $stock, $stock_min, $precio_v, $precio_c, $precio_x_mayor, $img_producto,
            // lista de productos agrupados
            $idproducto_pp_set, $idproducto_p_set, $cantidad, $um_presentation
          );
          echo json_encode($rspta, true);
        }        

      break; 

      case 'mostrar' :
        $rspta = $productos->mostrar($idproducto);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_detalle_producto':
        $rspta = $productos->mostrar_detalle_producto($idproducto);
        $nombre_doc = $rspta['data']['producto']['imagen'];
        $count = 1;

        $html_table = '
          <div class="my-3"><span class="h6"> Datos del Producto </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr><th scope="col">Nombre</th><th scope="row">'.$rspta['data']['producto']['nombre'].'</th></tr>              
              <tr><th scope="col">Código</th><th scope="row">'.$rspta['data']['producto']['codigo'].'</th></tr> 
              <tr><th scope="col">Descripción</th><th scope="row">'.$rspta['data']['producto']['descripcion'].'</th></tr>                  
            </tbody>
          </table>

          <div class="my-3"><span class="h6"> Detalles </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr><th scope="col">Categoria</th><th scope="row">'.$rspta['data']['producto']['categoria'].'</th></tr> 
              <tr><th scope="col">Marca</th><th scope="row">'.$rspta['data']['producto']['marca'].'</th></tr>              
              <tr><th scope="col">U. Medida</th><th scope="row">'.$rspta['data']['producto']['unidad_medida'].'</th></tr> 
              <tr><th scope="col">Stock</th><th scope="row">'.$rspta['data']['producto']['stock'].'</th></tr>   
              <tr><th scope="col">Stock Minimo</th><th scope="row">'.$rspta['data']['producto']['stock_minimo'].'</th></tr>               
            </tbody>
          </table>

          <div class="my-3"><span class="h6"> Precio </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr><th scope="col">Precio Compra</th><th scope="row"> S/ '.$rspta['data']['producto']['precio_compra'].'</th></tr> 
              <tr><th scope="col">Precio Venta</th><th scope="row">S/ '.$rspta['data']['producto']['precio_venta'].'</th></tr>              
              <tr><th scope="col">Precio por Mayor</th><th scope="row">S/ '.$rspta['data']['producto']['precio_por_mayor'].'</th></tr>            
            </tbody>
          </table>';

        // Mostrar productos agrupados si existen
        if (!empty($rspta['data']['grupo'])) {
          $html_table .= '<div class="my-3"><span class="h6"> Productos Agrupados </span></div>
          <table class="table text-nowrap table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Presentación</th>
                <th>Cantidad</th>
                <th>Descripción</th>
              </tr>
            </thead>
            <tbody>';
          foreach ($rspta['data']['grupo'] as $g) {
            $img = empty($g['imagen']) ? 'no-producto.png' : $g['imagen'];
            $html_table .= '
              <tr>
                <td>'.$count++.'</td>
                <td> 
                  <div class="d-flex flex-fill align-items-center">
                    <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($g['nombre'])) . '\')"> </span></div>
                    <div>
                      <span class="d-block fs-11 fw-semibold text-primary nombre_producto_' . $g['idproducto_presentacion'] . '">'.$g['nombre'] .'</span>
                      <span class="d-block fs-9 text-muted">Marca: <b>'.$g['marca'].'</b> | Categoría: <b>'.$g['categoria'].'</b></span> 
                    </div>
                  </div>
                </td>
                <td>'.$g['unidad_medida'].'</td>
                <td>'.$g['cantidad'].'</td>
                <td>'.$g['descripcion'].'</td>
              </tr>';
          }
          $html_table .= '</tbody></table>';
        }

        // Mostrar imagen
        $html_table .= '<div class="my-3"><span class="h6"> Imagen </span></div>';

        $rspta = [
          'status' => true,
          'message' => 'Todo bien',
          'data' => $html_table,
          'imagen' => $rspta['data']['producto']['imagen'],
          'nombre_doc' => $nombre_doc
        ];

        echo json_encode($rspta, true);
      break;

      case 'mostrar_eliminar_papelera':
        $rspta = $productos->mostrar_eliminar_papelera($_POST["idproducto"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $productos->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break; 

      case 'dasactivar':
        $rspta = $productos->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      
      // ═════════════════════════════════  PRODUCTOS AGRUPADOS  ══════════════════════════════════════
      case 'listar_tabla_producto_g':
          
        $rspta = $productos->listar_tabla_producto_g($_GET["tipo_producto"]); 

        $datas = []; 

        if ($rspta['status'] == true) {

          foreach($rspta['data'] as $key => $value){

            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data_btn_1 = 'btn-add-producto-1-'.$value['idproducto']; $data_btn_2 = 'btn-add-producto-2-'.$value['idproducto'];
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn_1.' mr-1 px-2 py-1" onclick="agregarProductoaGrupo(' . $value['idproducto'] . ')" data-bs-toggle="tooltip" title="Agregar"><span class="fa fa-plus"></span></button>' ,
              "1" => '<span class="fs-12 text-nowrap"> <i class="bi bi-upc"></i> '.$value['codigo'] .'</span>' ,
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                <div>
                  <span class="d-block fs-11 fw-semibold text-primary nombre_producto_' . $value['idproducto'] . '">'.$value['nombre'] .'</span>
                  <span class="d-block fs-9 text-muted">Marca: <b>'.$value['marca'].'</b> | Categoría: <b>'.$value['categoria'].'</b></span> 
                </div>
              </div>',             
              "3" => ($value['precio_venta']),
              "4" => '<textarea class="textarea_datatable fs-11 bg-light"  readonly>' .($value['descripcion']). '</textarea>' . $toltip
            ];
          }
  
          $results = [
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datas), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datas), //enviamos el total registros a visualizar
            "aaData" => $datas,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data'];
        }
    
      break;

      case 'mostrar_producto':
        $rspta=$productos->mostrar_producto($idproducto);
        echo json_encode($rspta, true);
      break;


      // ══════════════════════════════════════  PRESENTACION  ══════════════════════════════════════
      case 'guardar_editar_presentacion':
        if (empty($idpresentacion)){
          $rspta = $productos->insertar_presentacion($idproducto_sucursal_ps, $umedida_ps, $cant_ps, $nombre_presentacion,$precio_presentacion_und,$precio_presentacion_total);
          echo json_encode($rspta, true);
        } else {
          $rspta = $productos->editar_presentacion($idpresentacion, $idproducto_sucursal_ps, $umedida_ps, $cant_ps, $nombre_presentacion,$precio_presentacion_und,$precio_presentacion_total);
          echo json_encode($rspta, true);
        }
        
      break;

      case 'listar_presentacion':
        $rspta=$productos->listar_presentacion($_POST["idproducto_sucursal"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_presentacion':
        $rspta=$productos->mostrar_presentacion($idpresentacion);
        echo json_encode($rspta, true);
      break;

      case 'eliminar_presentacion':
        $rspta = $productos->eliminar_presentacion($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break; 

      case 'papelera_presentacion':
        $rspta = $productos->papelera_presentacion($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break; 

      // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
      case 'validar_code_producto':
        $rspta = $productos->validar_code_producto($_GET["idproducto"], $_GET["codigo_alterno"]);
        echo json_encode($rspta, true);
      break;


      // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

      case 'select_categoria':
        $rspta = $productos->select_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select_u_medida':
        $rspta = $productos->select_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_c03_unidad_medida'] . '" title="' . $value['nombre'] . '" nombre="' . $value['nombre'] . '" >' . $value['nombre'] .' - '. $value['abreviatura']. '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select_marca':
        $rspta = $productos->select_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_marca'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;


      // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
      case 'select2_filtro_categoria':
        $rspta = $productos->select2_filtro_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true,  'message' => 'Salió todo ok', 'data' => $data, );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_u_medida':
        $rspta = $productos->select2_filtro_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_c03_unidad_medida'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] .' - '. $value['abreviatura']. '</option>';
          }
  
          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data, );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_marca':
        $rspta = $productos->select2_filtro_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_marca'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array('status' => true,'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
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