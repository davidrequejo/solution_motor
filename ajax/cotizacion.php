<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['facturacion'] == 1) {

    require_once "../modelos/Cotizacion.php";
    require_once "../modelos/Facturacion.php";
    require_once "../modelos/Producto.php";    

    $cotizacion        = new Cotizacion();    
    $facturacion        = new Facturacion();    
    $productos          = new Producto();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../assets/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $idventa                = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";   
    $impuesto               = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";   
    $crear_y_emitir         = isset($_POST["crear_y_emitir"]) ? ( empty($_POST["crear_y_emitir"]) ? 'NO' : 'SI' ) : ""; 

    $idsunat_c01            = isset($_POST["idsunat_c01"]) ? limpiarCadena($_POST["idsunat_c01"]) : "";    
    $tipo_comprobante       = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";    
    $serie_comprobante      = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";    
    $idpersona_cliente      = isset($_POST["idpersona_cliente"]) ? limpiarCadena($_POST["idpersona_cliente"]) : "";         
    $observacion_documento  = isset($_POST["observacion_documento"]) ? limpiarCadena($_POST["observacion_documento"]) : "";    
    $es_cobro               = isset($_POST["es_cobro_inp"]) ? limpiarCadena($_POST["es_cobro_inp"]) : "";    
    $periodo_pago_format           = isset($_POST["periodo_pago_format"]) ? limpiarCadena($_POST["periodo_pago_format"]) : "";    
    
    $metodo_pago            = isset($_POST["metodo_pago"]) ? limpiarCadena($_POST["metodo_pago"]) : "";  
    $total_recibido         = isset($_POST["total_recibido"]) ? limpiarCadena($_POST["total_recibido"]) : "";  
    $mp_monto               = isset($_POST["mp_monto"]) ? limpiarCadena($_POST["mp_monto"]) : "";  
    $total_vuelto           = isset($_POST["total_vuelto"]) ? limpiarCadena($_POST["total_vuelto"]) : "";  

    $usar_anticipo          = isset($_POST["usar_anticipo"]) ? limpiarCadena($_POST["usar_anticipo"]) : "";  
    $ua_monto_disponible    = isset($_POST["ua_monto_disponible"]) ? limpiarCadena($_POST["ua_monto_disponible"]) : "";  
    $ua_monto_usado         = isset($_POST["ua_monto_usado"]) ? limpiarCadena($_POST["ua_monto_usado"]) : "";  

    $mp_serie_comprobante   = isset($_POST["mp_serie_comprobante"]) ? limpiarCadena($_POST["mp_serie_comprobante"]) : "";       

    $venta_subtotal         = isset($_POST["venta_subtotal"]) ? limpiarCadena($_POST["venta_subtotal"]) : "";    
    $tipo_gravada           = isset($_POST["tipo_gravada"]) ? limpiarCadena($_POST["tipo_gravada"]) : "";
    $venta_descuento        = isset($_POST["venta_descuento"]) ? limpiarCadena($_POST["venta_descuento"]) : "";    
    $venta_igv              = isset($_POST["venta_igv"]) ? limpiarCadena($_POST["venta_igv"]) : "";            
    $venta_total            = isset($_POST["venta_total"]) ? limpiarCadena($_POST["venta_total"]) : "";   

    $nc_idventa             = isset($_POST["nc_idventa"]) ? limpiarCadena($_POST["nc_idventa"]) : "";    
    $nc_tipo_comprobante    = isset($_POST["nc_tipo_comprobante"]) ? limpiarCadena($_POST["nc_tipo_comprobante"]) : "";    
    $nc_serie_y_numero      = isset($_POST["nc_serie_y_numero"]) ? limpiarCadena($_POST["nc_serie_y_numero"]) : "";    
    $nc_motivo_anulacion    = isset($_POST["nc_motivo_anulacion"]) ? limpiarCadena($_POST["nc_motivo_anulacion"]) : "";    

    $tiempo_entrega         = isset($_POST["tiempo_entrega"]) ? limpiarCadena($_POST["tiempo_entrega"]) : "";    
    $validez_cotizacion     = isset($_POST["validez_cotizacion"]) ? limpiarCadena($_POST["validez_cotizacion"]) : "";    
     
    $mp_comprobante_old     = isset($_POST["mp_comprobante_old"]) ? limpiarCadena($_POST["mp_comprobante_old"]) : "";     
    
    switch ($_GET["op"]){

      // :::::::::::: S E C C I O N  FACTURACION ::::::::::::      

      case 'guardar_editar_facturacion':

        $rspta = ""; $mp_comprobante = ""; 
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 

        if ( floatval($venta_total) > 699 ) {
          # code...
        } else {
          # code...
        }      

        $file_nombre_new = [];                        // Amacenar los nombres de los documentos             
        $file_nombre_old = [];                        // Amacenar los nombres de los documentos             
        $file_size = [];                        // Amacenar los nombres de los documentos       

        if ($metodo_pago == 'EFECTIVO' ) {
          # code...
        } else {
          
          if ( empty($_POST["mp_comprobante"]) || isset($_FILES['mp_comprobante']) && $_FILES['mp_comprobante']['name'] ) {
            # code...
          } else {          
            $mp_comprobante = json_decode($_POST["mp_comprobante"], true);
            $decoded_data = base64_decode($mp_comprobante['data']);  // Decodificar el archivo base64
            $ext = explode(".", $mp_comprobante["name"]);          
            $mp_comprobante = $metodo_pago . '__' . $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);          
            $ruta_destino = '../assets/modulo/facturacion/ticket/'.$mp_comprobante; // Ruta donde deseas guardar el archivo en el servidor
            
            if (file_put_contents($ruta_destino, $decoded_data) !== false) { // Guardar el archivo decodificado en el servidor
              # El archivo se ha guardado correctamente en el servidor
            } else {
              $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Archivo no aceptado!!', 'message' => 'El archivo de baucher de pago esta corroido, porfavor cambie de archivo', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true); die();
            }
          }      
        }          

        if (empty($idventa)) {          
          
          $rspta = $cotizacion->insertar( 
          // Venta  
          $crear_y_emitir,$idsunat_c01  ,$tipo_comprobante, $serie_comprobante, $idpersona_cliente, $observacion_documento, $metodo_pago, $total_recibido, $file_nombre_new, $file_nombre_old, $file_size, $venta_subtotal, $tipo_gravada, 
          $venta_descuento, $venta_igv, $venta_total,  $tiempo_entrega, $validez_cotizacion,
          // Venta Detalle
          $_POST["idproducto"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["es_cobro"], $_POST["periodo_pago"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_sin_igv"], 
          $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], $_POST["descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"]); 

          $idventa = $rspta['id_tabla'];

          if ($rspta['status'] == true) {             // validacion de creacion de documento
        
            if ($tipo_comprobante == '100') {          // COTIZACION
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
              echo json_encode($rspta, true);
            }else {
              $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true);
            }
          } else{
            echo json_encode($rspta, true);
          }

        } else {

          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Datos incompletos', 'message' => 'No se enviaron los datos completos: idventa', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }
    
      break; 
  
      // :::::::::::: S E C C I O N   V E N T A S ::::::::::::

      case 'listar_tabla_facturacion':

        $rspta = $cotizacion->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"],  $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $img_proveedor = empty($value['foto_perfil']) ? 'no-perfil.jpg' : $value['foto_perfil'];

            $url_xml = ""; $url_cdr = "";

            if ($value['tipo_comprobante'] == '12') {          // SUNAT TICKET           
            } else if ($value['tipo_comprobante'] == '01') {   // SUNAT FACTURA             
              $url_xml = '../assets/modulo/facturacion/factura/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/factura/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';
            } else if ($value['tipo_comprobante'] == '03') {   // SUNAT BOLETA              
              $url_xml = '../assets/modulo/facturacion/boleta/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/boleta/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';
            } else if ($value['tipo_comprobante'] == '07') {   // SUNAT NOTA DE CREDITO 
              $url_xml = '../assets/modulo/facturacion/nota_credito/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/nota_credito/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';
            } else {            
            }

            $data[] = [
              "0" => $count++,
              "1" => '<div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">                
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_editar_venta(' . $value['idventa'] . ');" ><i class="bi bi-eye"></i> Ver</a></li>
                  '.( $value['cot_estado'] == 'PENDIENTE' ? '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="cambiar_estado_vendido(' . $value['idventa'] .', \'VENDIDO\');" ><i class="bx bxl-shopify fs-16"></i> Cambiar a vendido </a></li>' : '<li><a class="dropdown-item text-warning" href="javascript:void(0);" onclick="cambiar_estado_vendido(' . $value['idventa'] .', \'PENDIENTE\');" ><i class="bx bx-message-rounded-x"></i> Cambiar a Pendiente </a></li>').'  
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_a4_completo(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato A4 completo</a></li>
                  '.( $value['tipo_comprobante'] == '100' ? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_papelera_venta(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="bx bx-trash"></i> Eliminar o papelera </a></li>' : '').'  
                </ul>
              </div>',
              "2" =>  $value['fecha_emision_format'],
              "3" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/persona/perfil/' . $img_proveedor . '" alt="" onclick="ver_img_pefil(' .$value['idpersona_cliente'] . ')" onerror="'.$imagen_error.'"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary" data-bs-toggle="tooltip" title="'.$value['cliente_nombre_completo'] .'">'.$value['cliente_nombre_recortado'] .'</span>
                  <span class="text-muted"><b>'.$value['tipo_documento'] .'</b>: '. $value['numero_documento'].'</span>
                </div>
              </div>',
              "4" =>  '<b>'.$value['tp_comprobante_v2'].'</b>' . ' <br> ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'],
              "5" =>  $value['venta_total_v2'] , 
              "6" =>   ($value['cot_estado'] == 'PENDIENTE' ? 
                '<span class="badge bg-warning-transparent cursor-pointer"><i class="ri-close-fill align-middle me-1"></i>'.$value['cot_estado'].'</span>' :                    
                '<span class="badge bg-success-transparent cursor-pointer" ><i class="ri-check-fill align-middle me-1"></i>'.$value['cot_estado'].'</span>'                              
              ),              
              "7" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :                    
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>'                              
              ),              
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

      case 'listar_tabla_ver_mas_detalle_facturacion':

        $rspta = $cotizacion->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"], $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){           

            $data[] = [
              "0" => '<span class="text-nowrap fs-11">'. $value['idventa_v2'].'</span>',
              "1" => '<span class="text-nowrap fs-11">'. $value['es_cobro'].'</span>',
              "2" =>  $value['fecha_emision_format'],
              "3" => '<span class="text-nowrap fs-11">'. $value['periodo_pago_month_v2'] .'-'. $value['periodo_pago_year'].'</span>',
              "4" => '<span class="text-nowrap fs-11">'. $value['cliente_nombre_completo'].'</span>',
              "5" => '<span class="text-nowrap fs-11">'. $value['tipo_documento'] .'</span>',
              "6" => '<span class="text-nowrap fs-11">'. $value['numero_documento'].'</span>',
              "7" => '<span class="text-nowrap fs-11">'. $value['tp_comprobante_v2'].'</span>',
              "8" => '<span class="text-nowrap fs-11">'. $value['serie_comprobante'] . '-' . $value['numero_comprobante'].'</span>',
              "9" =>  $value['venta_total_v2'] ,
              "10" =>  $value['total_recibido'] ,
              "11" =>  $value['total_vuelto'] ,
              "12" => '<span class="text-nowrap fs-11">'. $value['metodo_pago'] .'</span>',
              "13" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :                    
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>'                              
              ),              
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

      case 'mostrar_detalle_venta':
        $rspta=$facturacion->mostrar_detalle_venta($idventa);
        

        echo '<div class="tab-pane fade active show" id="rol-venta-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">          
            <tbody>
              <tr> <th>Proveedor</th>        <td>'.$rspta['data']['venta']['nombre_razonsocial'].'  '.$rspta['data']['venta']['apellidos_nombrecomercial'].'
              <div class="font-size-12px" >Cel: <a href="tel:+51'.$rspta['data']['venta']['celular'].'">'.$rspta['data']['venta']['celular'].'</a></div> 
              <div class="font-size-12px" >E-mail: <a href="mailto:'.$rspta['data']['venta']['correo'].'">'.$rspta['data']['venta']['correo'].'</a></div> </td> </tr>            
              <tr> <th>Total venta</th>      <td>'.$rspta['data']['venta']['total'].'</td> </tr>             
              <tr> <th>Fecha</th>         <td>'.$rspta['data']['venta']['fecha_venta'].'</td> </tr>                
              <tr> <th>Comprobante</th>   <td>'.$rspta['data']['venta']['tp_comprobante']. ' | '.$rspta['data']['venta']['serie_comprobante'].'</td> </tr>
              <tr> <th>Observacion</th>   <td>'.$rspta['data']['venta']['descripcion'].'</td> </tr>         
            </tbody>
          </table>
        </div>';
        echo '</div>'; # div-content

        echo'<div class="tab-pane fade" id="rol-detalle-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">  
            <thead>
              <tr> <th>#</th> <th>Nombre</th> <th>Cantidad</th> <th>P/U</th> <th>Dcto.</th>  <th>Subtotal</th> </tr>
            </thead>        
            <tbody>';
            foreach ($rspta['data']['detalle'] as $key => $val) {
              echo '<tr> <td>'. $key + 1 .'</td> <td>'.$val['nombre'].'</td> <td class="text-center">'.$val['cantidad'].'</td> <td class="text-right">'.$val['precio_con_igv'].'</td> <td class="text-right">'.$val['descuento'].'</td> <td class="text-right" >'.$val['subtotal'].'</td> </tr>';
            }
        echo '</tbody>
            <tfoot>
              <td colspan="4"></td>

              <th class="text-right">
                <h6 class="tipo_gravada">SUBTOTAL</h6>
                <h6 class="val_igv">IGV (18%)</h6>
                <h5 class="font-weight-bold">TOTAL</h5>
              </th>
              <th class="text-right text-nowrap"> 
                <h6 class="font-weight-bold venta_subtotal">S/ '.$rspta['data']['venta']['subtotal'].'</h6> 
                <h6 class="font-weight-bold venta_igv">S/ '.$rspta['data']['venta']['igv'].'</h6>                 
                <h5 class="font-weight-bold venta_total">S/ '.$rspta['data']['venta']['total'].'</h5>                 
              </th>              
            </tfoot>
          </table>
        </div>';
        echo'</div>';# div-content
      break; 

      case 'mostrar_venta':
        $rspta=$facturacion->mostrar_venta($_POST["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_cliente':
        $rspta=$facturacion->mostrar_cliente($_POST["idcliente"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_editar_detalles_venta':
        $rspta=$facturacion->mostrar_detalle_venta($_POST["idventa"]);
        echo json_encode($rspta, true);
      break;      

      case 'eliminar':
        $rspta = $cotizacion->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $cotizacion->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'cambiar_estado_vendido':
        $rspta = $cotizacion->cambiar_estado_vendido($_GET["idventa"], $_GET["estado"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_producto':
        $rspta=$facturacion->mostrar_producto($_POST["idproducto"]);
        echo json_encode($rspta, true);
      break; 

      case 'mini_reporte':
        $rspta=$cotizacion->mini_reporte();
        echo json_encode($rspta, true);
      break; 

      case 'ver_estado_documento':
        $rspta=$facturacion->mostrar_venta($_GET["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'listar_producto_x_codigo':
        $rspta=$facturacion->listar_producto_x_codigo($_POST["codigo"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_tabla_producto':
          
        $rspta = $facturacion->listar_tabla_producto($_GET["tipo_producto"]); 

        $datas = []; 

        if ($rspta['status'] == true) {

          foreach($rspta['data'] as $key => $value){

            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data_btn_1 = 'btn-add-producto-1-'.$value['idproducto']; $data_btn_2 = 'btn-add-producto-2-'.$value['idproducto'];
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn_1.' mr-1 px-2 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto'] . ', false)" data-bs-toggle="tooltip" title="Agregar"><span class="fa fa-plus"></span></button>' ,
              "1" => '<span class="fs-12"> <i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno'] .'</span>' ,
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                <div>
                  <span class="d-block fs-12 fw-semibold text-primary nombre_producto_' . $value['idproducto'] . '">'.$value['nombre'] .'</span>
                  <span class="d-block fs-10 text-muted">Marca: <b>'.$value['marca'].'</b> | Categoría: <b>'.$value['categoria'].'</b></span> 
                </div>
              </div>',             
              "3" => ($value['precio_venta']),
              "4" => '<textarea class="textarea_datatable bg-light"  readonly>' .($value['descripcion']). '</textarea>' . $toltip
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
      
      
      

      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
      case 'select2_cliente':
        $rspta = $facturacion->select2_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $tipo_documento   = $value['tipo_documento'];
            $numero_documento = $value['numero_documento'];
            $direccion        = $value['direccion'];
            $data .= '<option tipo_documento="'.$tipo_documento.'" numero_documento="'.$numero_documento.'" value="' . $value['idpersona_cliente']  . '">' . $value['cliente_nombre_completo']  . ' - '. $value['nombre_tipo_documento'].': '. $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option tipo_documento="0" numero_documento="00000000" direccion="" value="1" >CLIENTES VARIOS - 0000000</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;     

      case 'select2_series_comprobante':
        $rspta = $facturacion->select2_series_comprobante($_GET["tipo_comprobante"], $_GET["nc_tp"]); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option title="' . $value['abreviatura'] . '" value="' . $value['serie']  . '">' . $value['serie']  . '</option>';
          }

          $retorno = array(
            'status'  => true, 
            'message' => 'Salió todo ok', 
            'data'    => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'select2_filtro_tipo_comprobante':
        $rspta = $facturacion->select2_filtro_tipo_comprobante($_GET["tipos"]); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idtipo_comprobante']  . '" >' . $value['nombre_tipo_comprobante_v2'] . '</option>';
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select2_filtro_cliente':
        $rspta = $cotizacion->select2_filtro_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idpersona_cliente']  . '">' . $cont. '. '. $value['cliente_nombre_completo'] .' - '. $value['nombre_tipo_documento'] .': '. $value['numero_documento'] .' (' .$value['cantidad'].')'. '</option>';
            $cont++;
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select_categoria':
        $rspta = $productos->select_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
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

      case 'select_u_medida':
        $rspta = $productos->select_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_c03_unidad_medida'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] .' - '. $value['abreviatura'] . '</option>';
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

      case 'select_marca':
        $rspta = $productos->select_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_marca'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
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

  }else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }
}
ob_end_flush();

?>