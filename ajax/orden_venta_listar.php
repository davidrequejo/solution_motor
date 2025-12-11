<?php
  require_once "../sunat/ConfigSunat.php";
  
  use Mike42\Escpos\Printer;
  use Mike42\Escpos\EscposImage;
  use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['orden_venta_listar'] == 1) {

    require_once "../modelos/Orden_venta_listar.php";
    require_once "../modelos/Orden_venta.php";
    require_once "../modelos/Producto.php";
    require_once "../modelos/Avance_cobro.php";

    require '../vendor/autoload.php';                   // CONEXION A COMPOSER
    $see = require '../sunat/SunatCertificado.php';   // EMISION DE COMPROBANTES

    $orden_venta_listar   = new Orden_venta_listar();      
    $orden_venta   = new Orden_venta();      
    $productos            = new Producto(); 
    $avance_cobro         = new Avance_cobro();  

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../assets/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $o_idventa                = isset($_POST["o_idventa"]) ? limpiarCadena($_POST["o_idventa"]) : "";   
    $o_impuesto               = isset($_POST["o_impuesto"]) ? limpiarCadena($_POST["o_impuesto"]) : "";   

    $o_idsunat_c01            = isset($_POST["o_idsunat_c01"]) ? limpiarCadena($_POST["o_idsunat_c01"]) : "";    
    $o_tipo_comprobante       = isset($_POST["o_tipo_comprobante"]) ? limpiarCadena($_POST["o_tipo_comprobante"]) : "";    
    $o_serie_comprobante      = isset($_POST["o_serie_comprobante"]) ? limpiarCadena($_POST["o_serie_comprobante"]) : "";    
    $o_idcliente              = isset($_POST["o_idcliente"]) ? limpiarCadena($_POST["o_idcliente"]) : "";         
    $o_observacion_documento  = isset($_POST["o_observacion_documento"]) ? limpiarCadena($_POST["o_observacion_documento"]) : "";    

    $o_venta_subtotal         = isset($_POST["o_venta_subtotal"]) ? limpiarCadena($_POST["o_venta_subtotal"]) : "";    
    $o_venta_descuento        = isset($_POST["o_venta_descuento"]) ? limpiarCadena($_POST["o_venta_descuento"]) : "";    
    $o_venta_igv              = isset($_POST["o_venta_igv"]) ? limpiarCadena($_POST["o_venta_igv"]) : "";            
    $o_venta_total            = isset($_POST["o_venta_total"]) ? limpiarCadena($_POST["o_venta_total"]) : "";   

    // :::::::::::: S E C C I O N   N U E V O   C L I E N T E ::::::::::::
    $cli_idpersona                  = isset($_POST["cli_idpersona"]) ? limpiarCadena($_POST["cli_idpersona"]) : "";   
    $cli_tipo_persona_sunat         = isset($_POST["cli_tipo_persona_sunat"]) ? limpiarCadena($_POST["cli_tipo_persona_sunat"]) : "";   
    $cli_idtipo_persona             = isset($_POST["cli_idtipo_persona"]) ? limpiarCadena($_POST["cli_idtipo_persona"]) : "";   
    
    $cli_tipo_documento             = isset($_POST["cli_tipo_documento"]) ? limpiarCadena($_POST["cli_tipo_documento"]) : "";   
    $cli_numero_documento           = isset($_POST["cli_numero_documento"]) ? limpiarCadena($_POST["cli_numero_documento"]) : "";   
    $cli_nombre_razonsocial         = isset($_POST["cli_nombre_razonsocial"]) ? limpiarCadena($_POST["cli_nombre_razonsocial"]) : "";   
    $cli_apellidos_nombrecomercial  = isset($_POST["cli_apellidos_nombrecomercial"]) ? limpiarCadena($_POST["cli_apellidos_nombrecomercial"]) : "";   
    $cli_correo                     = isset($_POST["cli_correo"]) ? limpiarCadena($_POST["cli_correo"]) : "";   
    $cli_celular                    = isset($_POST["cli_celular"]) ? limpiarCadena($_POST["cli_celular"]) : "";   

    $cli_direccion                  = isset($_POST["cli_direccion"]) ? limpiarCadena($_POST["cli_direccion"]) : "";   
    $cli_direccion_referencia       = isset($_POST["cli_direccion_referencia"]) ? limpiarCadena($_POST["cli_direccion_referencia"]) : "";   
    $cli_centro_poblado             = isset($_POST["cli_centro_poblado"]) ? limpiarCadena($_POST["cli_centro_poblado"]) : "";   
    $cli_distrito                   = isset($_POST["cli_distrito"]) ? limpiarCadena($_POST["cli_distrito"]) : "";   
    $cli_departamento               = isset($_POST["cli_departamento"]) ? limpiarCadena($_POST["cli_departamento"]) : "";   
    $cli_provincia                  = isset($_POST["cli_provincia"]) ? limpiarCadena($_POST["cli_provincia"]) : "";   
    $cli_ubigeo                     = isset($_POST["cli_ubigeo"]) ? limpiarCadena($_POST["cli_ubigeo"]) : "";    

    switch ($_GET["op"]){

      // :::::::::::: S E C C I O N  FACTURACION ::::::::::::      

      case 'guardar_editar_facturacion':

        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= "";               
        
        $rspta = [];
        if (empty($o_idventa)) { // CREAMOS UN NUEVO REGISTRO
          
          $rspta = $orden_venta->insertar( $o_impuesto, $o_idsunat_c01, $o_tipo_comprobante, $o_serie_comprobante, $o_idcliente, $o_observacion_documento, $o_venta_subtotal, $o_venta_descuento, 
            $o_venta_igv, $o_venta_total,     
            # Detalle de venta
            $_POST["idproducto_presentacion"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"],  $_POST["cantidad_presentacion"], $_POST["cantidad_venta"], 
            $_POST["cantidad_total"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
            $_POST["descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_x_producto"], $_POST["subtotal_no_descuento_producto"], $_POST["precio_por_mayor"]
          ); 
          // echo json_encode($rspta, true); die();
          $o_idventa = $rspta['id_tabla'];

        } else {                // EDITAMOS EL REGISTRO

          $rspta = $orden_venta->editar( $o_idventa, $o_impuesto, $o_idsunat_c01, $o_tipo_comprobante, $o_serie_comprobante, $o_idcliente, $o_observacion_documento, $o_venta_subtotal, $o_venta_descuento, 
            $o_venta_igv, $o_venta_total,  
            # Detalle de venta
            $_POST["idproducto_presentacion"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"],  $_POST["cantidad_presentacion"], $_POST["cantidad_venta"], 
            $_POST["cantidad_total"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
            $_POST["descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_x_producto"], $_POST["subtotal_no_descuento_producto"], $_POST["precio_por_mayor"]
          ); 
          
        }

        if ($rspta['status'] == true) {             // validacion de creacion de documento         
          
          $update_sunat = $orden_venta_listar->actualizar_respuesta_sunat( $o_idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          echo json_encode($rspta, true); 
          
        } else{
          echo json_encode($rspta, true);
        }
    
      break;       

      case 'cambiar_a_por_enviar':
        $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
        $update_sunat = $orden_venta_listar->actualizar_respuesta_sunat( $_GET["idventa"], $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);              
        echo json_encode($update_sunat, true);
      break;

      // :::::::::::: S E C C I O N   V E N T A S ::::::::::::

      case 'listar_tabla_facturacion':

        $rspta = $orden_venta_listar->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_tipo_persona"], $_GET["filtro_comprobante"], $_GET["filtro_metodo_pago"], $_GET["filtro_centro_poblado"], $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $img_proveedor = empty($value['foto_perfil']) ? 'no-perfil.jpg' : $value['foto_perfil'];             
            
            $valores_sunat = ['RECHAZADA', 'POR ENVIAR', 'ACEPTADA']; // Lista de valores permitidos
            $valores_credito = ['pendiente', 'parcial']; // Lista de valores permitidos

            $data[] = [
              "0" => $count++,
              "1" => '<div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">'.                  
                  ($value['venta_cuotas'] == 'SI' && in_array($value['vc_estado'], $valores_credito) ? '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="ver_pagar_venta(' . $value['idventa'] . ');" ><i class="bi bi-cash-coin"></i> Pagar cuota</a></li>' : '').
                  '<li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_venta(' . $value['idventa'] . ');" ><i class="bi bi-eye"></i> Ver</a></li>'.
                  ( empty($value['iddocumento_relacionado']) ?  '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="ver_editar_venta(' . $value['idventa'] . ');" ><i class="bi bi-pencil"></i> Editar</a></li>' : '').                  
                  ( '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_papelera_venta(' . $value['idventa'] .', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\');" ><i class="bx bx-trash"></i> Eliminar o papelera </a></li>' )
                .'</ul>
              </div>',
              "2" =>  $value['idventa_v2'],
              "3" =>  $value['fecha_emision_format'],               
              "4" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/persona/perfil/' . $img_proveedor . '" alt="" onclick="ver_img_pefil(' .$value['idpersona_cliente'] . ')" onerror="'.$imagen_error.'"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary" data-bs-toggle="tooltip" title="'.$value['cliente_nombre_completo'] .'">'.$value['cliente_nombre_recortado'] .'</span>
                  <span class="text-muted"><b>'.$value['tipo_documento_abreviatura'] .'</b>: '. $value['numero_documento'].'</span>
                </div>
              </div>',
              "5" =>  '<b>'.$value['tipo_comprobante_v2'].'</b>' . ' <br> ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'],
              "6" =>  $value['venta_total_v2'] , 
              "7" =>  ($value['comprobante_relacionado_estado'] == 'pendiente' ? 
                '<span class="badge bg-warning-transparent cursor-pointer" ><i class="ri-close-fill align-middle me-1"></i>'.$value['comprobante_relacionado_estado'].'</span>' :                         
                '<span class="badge bg-success-transparent cursor-pointer" ><i class="ri-check-fill align-middle me-1"></i>'.$value['comprobante_relacionado_estado'].'</span> <br>'.                                      
                '<span class="badge bg-info-transparent cursor-pointer" ><i class="ri-check-fill align-middle me-1"></i>'.$value['comprobante_relacionado'].'</span>'
              ),    
              "8" =>  $value['user_en_atencion'],              
              "9" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :  
                ($value['sunat_estado'] == 'POR ENVIAR'     ?        
                '<span class="badge bg-warning-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' : 
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' 
                )                       
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

      case 'mostrar_venta':
        $rspta=$orden_venta_listar->mostrar_venta($_POST["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_metodo_pago':
        $rspta=$orden_venta_listar->mostrar_metodo_pago($_GET["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_cliente':
        $rspta=$orden_venta_listar->mostrar_cliente($_POST["idcliente"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_editar_detalles_venta':
        $rspta=$orden_venta_listar->mostrar_detalle_venta($_POST["idventa"]);
        echo json_encode($rspta, true);
      break;      

      case 'eliminar':
        $rspta = $orden_venta_listar->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $orden_venta_listar->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_producto':
        $rspta=$orden_venta_listar->mostrar_producto($_GET["search_producto"]);
        echo json_encode($rspta, true);
      break;      

      case 'ver_estado_documento':
        $rspta=$orden_venta_listar->mostrar_venta($_GET["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'listar_producto_x_codigo':
        $rspta=$orden_venta_listar->listar_producto_x_codigo($_POST["codigo"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_producto_x_nombre':
        $rspta=$orden_venta_listar->listar_producto_x_nombre($_GET["search"]);
        echo json_encode($rspta, true);
      break;      

      case 'ver_meses_cobrado':
        $rspta=$orden_venta_listar->ver_meses_cobrado($_GET["idcliente"]);
        echo '<div class="card-body">
          <ul class="list-unstyled timeline-widget mb-0 my-3">';
            foreach ($rspta['data'] as $key => $val) {               
              echo '<li class="timeline-widget-list">
                <div class="d-flex align-items-top">
                  <div class="me-5 text-center">
                    <span class="d-block fs-20 fw-semibold text-primary">'.$val['periodo_pago_month_recorte'].'</span>
                    <span class="d-block fs-12 text-muted">'.$val['periodo_pago_year'].'</span>
                  </div>
                  <div class="d-flex flex-wrap flex-fill align-items-top justify-content-between">
                    <div>
                      <p class="mb-1 text-truncate timeline-widget-content text-wrap">'.$val['user_en_atencion'].' - '.$val['tipo_comprobante_v2'].' <span class="badge bg-success-transparent fs-12">'.$val['serie_comprobante'].'-'.$val['numero_comprobante'].'</span></p>                    
                      <p class="mb-0 fs-10 lh-1 text-muted">'.$val['fecha_emision_format_v2'].'</p>'.
                      ($_GET["id_periodo"] == $val['periodo_pago'] ? '<p class="mt-1 fs-12 lh-1 text-muted">Mensaje: <span class="badge bg-warning-transparent ms-2">Este mes estas deseando pagar</span></p>' : '').
                    '</div>
                    <div class="dropdown">
                      <a aria-label="anchor" href="javascript:void(0);" class="p-2 fs-16 text-muted" data-bs-toggle="dropdown">
                        <i class="fe fe-more-vertical"></i>
                      </a>
                      <ul class="dropdown-menu">                      
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_ticket(' . $val['idventa'] .', \''.$val['tipo_comprobante'] . '\');"><i class="ti ti-checkup-list"></i> Formato Tiket</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_a4_completo(' . $val['idventa'] .', \''.$val['tipo_comprobante'] . '\');"><i class="ti ti-checkup-list"></i> Formato A4</a></li>
                        '.( $val['tipo_comprobante'] == '12' ? '<li><a class="dropdown-item text-danger text-nowrap" href="javascript:void(0);" onclick="eliminar_papelera_venta(' . $val['idventa'] .', \''. '<b>'.$val['tipo_comprobante_v2'].' </b>' .  $val['serie_comprobante'] . '-' . $val['numero_comprobante'] . '\');" ><i class="bx bx-trash"></i> Eliminar o papelera </a></li>' : '').'  
                      </ul>
                    </div>
                  </div>
                </div>
              </li>';
            }              
          echo'</ul>
        </div>';
      break;

      case 'listar_tabla_producto':
        $es_precio_por_mayor = isset($_GET["es_precio_por_mayor"]) ? limpiarCadena($_GET["es_precio_por_mayor"]) : "NO"; 
        $rspta = $orden_venta_listar->listar_tabla_producto($_GET["tipo_producto"] ); 

        $datas = []; 

        if ($rspta['status'] == true) {

          foreach($rspta['data'] as $key => $value){

            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data_btn_1 = 'btn-add-producto-1-'.$value['idproducto_presentacion']; $data_btn_2 = 'btn-add-producto-2-'.$value['idproducto_presentacion'];
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn_1.' mr-1 px-2 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto_presentacion'] . ', null, false )" data-bs-toggle="tooltip" title="Agregar"><span class="fa fa-plus"></span></button>' ,
              "1" => '<span class="fs-12 text-nowrap"> <i class="bi bi-upc"></i> '.$value['codigo'] .'</span>' ,
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre_producto'])) . '\')"> </span></div>
                <div>
                  <span class="d-block fs-11 fw-semibold text-primary nombre_producto_' . $value['idproducto_presentacion'] . '">'.$value['nombre_producto'] .'</span>
                  <span class="d-block fs-9 text-muted text-nowrap">Marca: <b>'.$value['marca'].'</b></span>   <span class="d-block fs-9 text-muted"> Categoría: <b>'.$value['categoria'].'</b></span> 
                </div>
              </div>',             
              "3" => $es_precio_por_mayor == 'SI' ?  $value['precio_por_mayor'] : $value['precio_venta'],
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

      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      // ═══════                                         A G R E G A R   C L I E N T E                                                                    ═══════
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

      case 'guardar_editar_cliente':        

        if (empty($cli_idpersona)) {
          
          $rspta = $orden_venta_listar->agregar_nuevo_cliente(  $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
           $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo );

          echo json_encode($rspta, true);
        } else {

          $rspta = $orden_venta_listar->editar_nuevo_cliente( $cli_idpersona, $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
          $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo);
    
          echo json_encode($rspta, true);
        }
    
      break;   

      case 'imprimir_tiket_orden_venta':           
        /*
        try {
          // Conexión con la impresora a través de IP y puerto 9100
          $ip = '192.168.1.100';  // Cambia a la IP de tu impresora
          $port = 9100;
          $connector = new NetworkPrintConnector($ip, $port);

          // Crea la impresora
          $printer = new Printer($connector);

          // Configuración de la fuente y tamaño
          $printer->setStyles([
              'font' => Printer::FONT_B, // Fuente B
              'align' => Printer::ALIGN_CENTER, // Alineación centrada
          ]);

          // Imprime el número de ticket
          $printer->text("Orden de Venta\n");
          $printer->text("####################\n");
          $printer->text("Nro de Ticket: 12345\n");
          $printer->feed();

          // Imprime los datos del cliente
          $printer->text("Cliente: Juan Pérez\n");
          $printer->text("Fecha: " . date('Y-m-d H:i:s') . "\n");
          $printer->text("Monto: $100.50\n");
          $printer->feed();

          // Imprimir un mensaje adicional
          $printer->text("Gracias por su compra!\n");

          // Cortar el papel
          $printer->cut();

          // Cerrar la conexión con la impresora
          $printer->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
          */

      break;

      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
      case 'select2_cliente':
        $rspta = $orden_venta_listar->select2_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $tipo_documento   = $value['tipo_documento'];
            $numero_documento = $value['numero_documento'];
            $direccion        = $value['direccion'];
            $data .= '<option tipo_documento="'.$tipo_documento.'" numero_documento="'.$numero_documento.'" direccion="'.$direccion.'" value="' . $value['idpersona_cliente']  . '">' . $value['cliente_nombre_completo']  . ' - '. $value['nombre_tipo_documento'].': '. $value['numero_documento'] .  '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option tipo_documento="0" dia_cancelacion="" numero_documento="00000000" direccion="" value="1" >CLIENTES VARIOS - 0000000</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;
      
      case 'select2_comprobantes_anular':
        $rspta = $orden_venta_listar->select2_comprobantes_anular($_GET["tipo_comprobante"]); $cont = 1; $data = ""; #echo $rspta; die();
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $idventa            = $value['idventa'];
            $tipo_comprobante   = $value['tipo_comprobante'];
            $serie_comprobante  = $value['serie_comprobante'];
            $numero_comprobante = $value['numero_comprobante'];
            $tp_comprobante_v2  = $value['nombre_tipo_comprobante_v2'];
            $fecha_emision_dif  = $value['fecha_emision_dif'];
            $data .= '<option idventa="'.$idventa.'" tipo_comprobante="'.$tipo_comprobante.'" title="'.$fecha_emision_dif.'"  value="' . $serie_comprobante.'-'. $numero_comprobante  . '">'  . $serie_comprobante.'-'. $numero_comprobante . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;

      case 'select2_series_comprobante':
        $rspta = $orden_venta_listar->select2_series_comprobante($_GET["tipo_comprobante"]); $cont = 1; $data = "";
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

      case 'select2_codigo_x_anulacion_comprobante':
        $rspta = $orden_venta_listar->select2_codigo_x_anulacion_comprobante(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['codigo']  . '">' . $value['codigo'].' - '. $value['nombre']  . '</option>';
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
        $rspta = $orden_venta_listar->select2_filtro_tipo_comprobante($_GET["tipos"]); $cont = 1; $data = "";
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
        $rspta = $orden_venta_listar->select2_filtro_cliente(); $cont = 1; $data = "";
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
            $data  .= '<option value="' . $value['idsunat_unidad_medida'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] .' - '. $value['abreviatura'] . '</option>';
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

      case 'select2_banco':
        $rspta = $orden_venta_listar->select2_banco();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['nombre'] . '" title ="' . $value['icono'] . '" >' . $value['nombre'] . '</option>';
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

      case 'select2_periodo_contable':
        $rspta = $orden_venta_listar->select2_periodo_contable(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['periodo'] . '"> '. $value['periodo_year'] .'-' .$value['periodo_month']. ' ('.$value['cant_comprobante']. ')'. '</option>';
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

      case 'salir':     
        session_unset();  //Limpiamos las variables de sesión  
        session_destroy(); //Destruìmos la sesión
        echo "<h5>Sesion cerrada con exito</h5>";        
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