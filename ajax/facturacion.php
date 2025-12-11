<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['facturacion'] == 1) {

    require_once "../modelos/Facturacion.php";
    require_once "../modelos/Producto.php";
    require_once "../modelos/Avance_cobro.php";

    require '../vendor/autoload.php';                   // CONEXION A COMPOSER
    $see = require '../sunat/SunatCertificado.php';   // EMISION DE COMPROBANTES

    $facturacion        = new Facturacion();      
    $productos          = new Producto(); 
    $avance_cobro        = new Avance_cobro();  

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../assets/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $f_idventa                = isset($_POST["f_idventa"]) ? limpiarCadena($_POST["f_idventa"]) : "";   
    $f_impuesto               = isset($_POST["f_impuesto"]) ? limpiarCadena($_POST["f_impuesto"]) : "";   
    $f_crear_y_emitir         = isset($_POST["f_crear_y_emitir"]) ? limpiarCadena($_POST["f_crear_y_emitir"]) : "NO"; 

    $f_idsunat_c01            = isset($_POST["f_idsunat_c01"]) ? limpiarCadena($_POST["f_idsunat_c01"]) : "";    
    $f_tipo_comprobante       = isset($_POST["f_tipo_comprobante"]) ? limpiarCadena($_POST["f_tipo_comprobante"]) : "";    
    $f_serie_comprobante      = isset($_POST["f_serie_comprobante"]) ? limpiarCadena($_POST["f_serie_comprobante"]) : "";    
    $f_idpersona_cliente      = isset($_POST["f_idpersona_cliente"]) ? limpiarCadena($_POST["f_idpersona_cliente"]) : "";         
    $f_observacion_documento  = isset($_POST["f_observacion_documento"]) ? limpiarCadena($_POST["f_observacion_documento"]) : "";    

    $f_usar_anticipo          = isset($_POST["f_usar_anticipo"]) ? limpiarCadena($_POST["f_usar_anticipo"]) : "";  
    $f_ua_monto_disponible    = isset($_POST["f_ua_monto_disponible"]) ? limpiarCadena($_POST["f_ua_monto_disponible"]) : "";  
    $f_ua_monto_usado         = isset($_POST["f_ua_monto_usado"]) ? limpiarCadena($_POST["f_ua_monto_usado"]) : "";  

    $f_venta_subtotal         = isset($_POST["f_venta_subtotal"]) ? limpiarCadena($_POST["f_venta_subtotal"]) : "";    
    $f_tipo_gravada           = isset($_POST["f_tipo_gravada"]) ? limpiarCadena($_POST["f_tipo_gravada"]) : "";
    $f_venta_descuento        = isset($_POST["f_venta_descuento"]) ? limpiarCadena($_POST["f_venta_descuento"]) : "";    
    $f_venta_igv              = isset($_POST["f_venta_igv"]) ? limpiarCadena($_POST["f_venta_igv"]) : "";            
    $f_venta_total            = isset($_POST["f_venta_total"]) ? limpiarCadena($_POST["f_venta_total"]) : "";   

    $f_nc_idventa             = isset($_POST["f_nc_idventa"]) ? limpiarCadena($_POST["f_nc_idventa"]) : "";    
    $f_nc_tipo_comprobante    = isset($_POST["f_nc_tipo_comprobante"]) ? limpiarCadena($_POST["f_nc_tipo_comprobante"]) : "";    
    $f_nc_serie_y_numero      = isset($_POST["f_nc_serie_y_numero"]) ? limpiarCadena($_POST["f_nc_serie_y_numero"]) : "";    
    $f_nc_motivo_anulacion    = isset($_POST["f_nc_motivo_anulacion"]) ? limpiarCadena($_POST["f_nc_motivo_anulacion"]) : "";    

    $f_tiempo_entrega         = isset($_POST["f_tiempo_entrega"]) ? limpiarCadena($_POST["f_tiempo_entrega"]) : "";    
    $f_validez_cotizacion     = isset($_POST["f_validez_cotizacion"]) ? limpiarCadena($_POST["f_validez_cotizacion"]) : "";    

    $f_vc_idventa             = isset($_POST["f_vc_idventa"]) ? limpiarCadena($_POST["f_vc_idventa"]) : "";
    $f_venta_cuotas           = isset($_POST["f_venta_cuotas"]) ? limpiarCadena($_POST["f_venta_cuotas"]) : "NO";    
    $f_idventa_cuotas           = isset($_POST["f_idventa_cuotas"]) ? $_POST["f_idventa_cuotas"] : [];  
    $f_pagar_cuota            = isset($_POST["f_pagar_cuota"]) ? $_POST["f_pagar_cuota"] : [];  

    $f_metodo_pago            = isset($_POST["f_metodo_pago"]) ? $_POST["f_metodo_pago"] : [];   
    $f_total_recibido         = isset($_POST["f_total_recibido"]) ? $_POST["f_total_recibido"] : [];   

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

        $rspta = ""; $mp_comprobante = ""; 
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; $sunat_ticket= ""; 

        if ( floatval($f_venta_total) > 699 ) {
          # code...
        } else {
          # code...
        }            

        $file_nombre_new = [];                        // Amacenar los nombres de los documentos             
        $file_nombre_old = [];                        // Amacenar los nombres de los documentos             
        $file_size = [];                        // Amacenar los nombres de los documentos             
        
        $comprobantes = $_POST["f_mp_comprobante"]; // Recibe el array de archivos
        $resultados = [];                           // Almacenar resultados para cada archivo       
        ksort($comprobantes);                       // Reorganizar el orden de indices
        //echo json_encode($comprobantes, true); die();
        foreach ($comprobantes as $key => $comprobante) {
          $mp_comprobante = json_decode($comprobante, true);
      
          if (!$mp_comprobante || empty($mp_comprobante['data'])) {
            $resultados[] = [  'index' => $key,  'status' => 'error',  'message' => 'El archivo no tiene datos válidos.'  ]; $file_nombre_new[] = ''; $file_nombre_old[] = ''; $file_size[] = '';  continue; // Saltar al siguiente archivo
          }
      
          $decoded_data = base64_decode($mp_comprobante['data']); // Decodificar el archivo base64
      
          if ($decoded_data === false) {
            $resultados[] = [ 'index' => $key, 'status' => 'error', 'message' => 'Error al decodificar el archivo base64.'  ]; $file_nombre_new[] = ''; $file_nombre_old[] = ''; $file_size[] = ''; continue; // Saltar al siguiente archivo
          }
      
          // Validar extensión del archivo
          $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'tiff', 'tif', 'svg', 'ico', 'pdf']; $file_info = pathinfo($mp_comprobante['name']);  $ext = strtolower($file_info['extension']);
      
          if (!in_array($ext, $allowed_extensions)) {
            $resultados[] = [   'index' => $key, 'status' => 'error',  'message' => 'Extensión de archivo no permitida.'  ]; $file_nombre_new[] = ''; $file_nombre_old[] = ''; $file_size[] = ''; continue; // Saltar al siguiente archivo
          }
      
          // Generar un nombre único para el archivo
          $mp_comprobante_name =$_POST["f_metodo_pago"][$key] . '__' . $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . $ext;
      
          // Ruta de destino
          $ruta_destino = realpath(dirname(__FILE__)) . '/../assets/modulo/facturacion/ticket/' . $mp_comprobante_name;
      
          // Guardar el archivo en el servidor
          if (file_put_contents($ruta_destino, $decoded_data) !== false) {
            $file_nombre_new[] =  $mp_comprobante_name   ;
            $file_nombre_old[] = limpiarCadena($mp_comprobante['name']);
            $file_size[] = $mp_comprobante['size'];
          } 
        }        
        
        $rspta = [];
        if (empty($f_idventa)) { // CREAMOS UN NUEVO REGISTRO
          
          $rspta = $facturacion->insertar( $f_impuesto, $f_crear_y_emitir,$f_idsunat_c01  ,$f_tipo_comprobante, $f_serie_comprobante, $f_idpersona_cliente, $f_observacion_documento,
          $f_metodo_pago,  $f_total_recibido,  $_POST["f_total_vuelto"],  $_POST["f_mp_serie_comprobante"], $file_nombre_new, $file_nombre_old, $file_size, $f_usar_anticipo, $f_ua_monto_disponible, $f_ua_monto_usado,   $f_venta_subtotal, $f_tipo_gravada, $f_venta_descuento, $f_venta_igv, $f_venta_total,
          # Nota de Venta
          $f_nc_idventa, $f_nc_tipo_comprobante, $f_nc_serie_y_numero, $f_nc_motivo_anulacion, 
          # Cotizacion
          $f_tiempo_entrega, $f_validez_cotizacion,
          # Detalle de venta
          $_POST["idproducto_presentacion"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"],  $_POST["pr_cantidad_presentacion"], $_POST["cantidad_venta"], $_POST["pr_cantidad_total"], 
          $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
          $_POST["f_descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"], $_POST["precio_por_mayor"],
          # Venta Cuotas 
          $f_vc_idventa, $f_venta_cuotas, $_POST["f_fecha_cuota"], $_POST["f_monto_cuota"], $f_idventa_cuotas, $f_pagar_cuota); 
          // echo json_encode($rspta, true); die();
          $f_idventa = $rspta['id_tabla'];

        } else {                // EDITAMOS EL REGISTRO

          $rspta = $facturacion->editar( $f_idventa, $f_impuesto, $f_crear_y_emitir,$f_idsunat_c01  ,$f_tipo_comprobante, $f_serie_comprobante, $f_idpersona_cliente, $f_observacion_documento,
          $f_metodo_pago,  $f_total_recibido,  $_POST["f_total_vuelto"],  $_POST["f_mp_serie_comprobante"], $file_nombre_new, $file_nombre_old, $file_size, $f_usar_anticipo, $f_ua_monto_disponible, $f_ua_monto_usado,   $f_venta_subtotal, $f_tipo_gravada, $f_venta_descuento, $f_venta_igv, $f_venta_total,
          # Nota de Venta
          $f_nc_idventa, $f_nc_tipo_comprobante, $f_nc_serie_y_numero, $f_nc_motivo_anulacion, 
          # Cotizacion
          $f_tiempo_entrega, $f_validez_cotizacion,
          # Detalle de venta
          $_POST["idproducto_presentacion"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["pr_cantidad_presentacion"], $_POST["cantidad_venta"], $_POST["pr_cantidad_total"], 
          $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
          $_POST["f_descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"], $_POST["precio_por_mayor"],
          # Venta Cuotas 
          $f_venta_cuotas, $_POST["f_fecha_cuota"], $_POST["f_monto_cuota"]); 
          
        }

        if ($rspta['status'] == true) {             // validacion de creacion de documento                         
        
          if ($f_tipo_comprobante == '12') {          // SUNAT TICKET     
            $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);
            echo json_encode($rspta, true);             

          } else {
            if ($f_crear_y_emitir == 'SI') {           // NO ENVIAR A SUNAT
              if ($f_tipo_comprobante == '01') {   // SUNAT FACTURA
                
                include( '../modelos/SunatFactura.php');
                $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);            
                if ( empty($sunat_observacion) && empty($sunat_error) ) {
                  echo json_encode($rspta, true); 
                } else {   
                  if ($sunat_estado == 111  ) {       // ENCASO DE HABR CONEXION SON SUNAT
                    $retorno = array( 'status' => 'no_conexion_sunat', 'titulo' => 'SUNAT en mantenimiento.', 'message' => 'No hay conexión a SUNAT, para seguir emitiendo dar click en: Enviar más tarde. De lo contrario tendra que pedir a su administrador para corregir el error.', 'user' =>  $_SESSION['user_nombre'], 'data' => 'Actual', 'id_tabla' => $f_idventa );
                    echo json_encode($retorno, true);
                  } else {           
                    $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                    echo json_encode($retorno, true); 
                  }
                }                
                
              } else if ($f_tipo_comprobante == '03') {   // SUNAT BOLETA 
                
                include( '../modelos/SunatBoleta.php');
                $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);
                if ( empty($sunat_observacion) && empty($sunat_error) ) {
                  echo json_encode($rspta, true); 
                } else {    
                  if ($sunat_estado == 111  ) {       // ENCASO DE HABR CONEXION SON SUNAT
                    $retorno = array( 'status' => 'no_conexion_sunat', 'titulo' => 'SUNAT en mantenimiento.', 'message' => 'No hay conexión a SUNAT, para seguir emitiendo dar click en: Enviar más tarde. De lo contrario tendra que pedir a su administrador para corregir el error.', 'user' =>  $_SESSION['user_nombre'], 'data' => 'Actual', 'id_tabla' => $f_idventa );
                    echo json_encode($retorno, true);
                  } else {
                    $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                    echo json_encode($retorno, true);
                  }                  
                } 
                
              } else if ($f_tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
                
                include( '../modelos/SunatNotaCredito.php');
                $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);
                
                if ( empty($sunat_observacion) && empty($sunat_error) ) {
                  $update_sunat = $facturacion->actualizar_doc_anulado_x_nota_credito( $f_nc_idventa); // CAMBIAMOS DE ESTADO EL DOC ANULADO
                  echo json_encode($rspta, true); 
                } else {     
                  if ($sunat_estado == 111  ) {       // ENCASO DE HABR CONEXION SON SUNAT
                    $retorno = array( 'status' => 'no_conexion_sunat', 'titulo' => 'SUNAT en mantenimiento.', 'message' => 'No hay conexión a SUNAT, para seguir emitiendo dar click en: Enviar más tarde. De lo contrario tendra que pedir a su administrador para corregir el error.', 'user' =>  $_SESSION['user_nombre'], 'data' => 'Actual', 'id_tabla' => $f_idventa );
                    echo json_encode($retorno, true);
                  } else {         
                    $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                    echo json_encode($retorno, true);
                  }
                }
                    
              } else {
                $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                echo json_encode($retorno, true);
              }
            } else {
              $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);              
              
              if ($update_sunat['status'] == false ) { 
                echo json_encode($update_sunat, true);
              } else {
                echo json_encode($rspta, true); 
              }              
            }
          }               
          
        } else{
          echo json_encode($rspta, true);
        }
    
      break; 

      case 'reenviar_sunat':

        $f_idventa          = isset($_GET["idventa"]) ? limpiarCadena($_GET["idventa"]) : "";    
        $tipo_comprobante   = isset($_GET["tipo_comprobante"]) ? limpiarCadena($_GET["tipo_comprobante"]) : "";    

        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; $sunat_ticket= ""; 

        if ($tipo_comprobante == '12') {          // SUNAT TICKET     
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Sin respuesta!!', 'message' => 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        } else if ($tipo_comprobante == '01') {   // SUNAT FACTURA         

          include( '../modelos/SunatFactura.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);
          
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion , 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true); 
          }              
          
        } else if ($tipo_comprobante == '03') {   // SUNAT BOLETA 
          
          include( '../modelos/SunatBoleta.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true);
          }            
          
        } else if ($tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
          include( '../modelos/SunatNotaCredito.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);
          if ( empty($sunat_observacion) && empty($sunat_error)  ) {
            $update_sunat = $facturacion->actualizar_doc_anulado_x_nota_credito( $f_idventa); // CAMBIAMOS DE ESTADO EL DOC ANULADO
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true);
          }
        } else {
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }
      break;  

      case 'status_documento_sunat':

        $f_idventa          = isset($_GET["idventa"]) ? limpiarCadena($_GET["idventa"]) : "";    
        $tipo_comprobante   =  isset($_GET["tipo_comprobante"]) ? limpiarCadena($_GET["tipo_comprobante"]) : "";  
          
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= "";  $sunat_ticket= "";         
          
        include( '../modelos/SunatGetStatus.php');

        $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error, $sunat_ticket);

        if ( empty($sunat_observacion) && empty($sunat_error) ) {
          echo json_encode($update_sunat, true); 
        } else {              
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Error en la validación de Estado', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }            
          
        
      break;  

      case 'cambiar_a_por_enviar':
        $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= "";  $sunat_ticket= ""; 
        $update_sunat = $facturacion->actualizar_respuesta_sunat( $_GET["idventa"], $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error,$sunat_ticket );              
        echo json_encode($update_sunat, true);
      break;

      case 'cambiar_a_aceptado':
        $sunat_estado = "ACEPTADA"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= isset($_GET["mensaje"]) ? limpiarCadena($_GET["mensaje"]) : ""; $sunat_error= "";  $sunat_ticket= ""; 
        $update_sunat = $facturacion->actualizar_respuesta_sunat( $_GET["idventa"], $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error,$sunat_ticket );              
        echo json_encode($update_sunat, true);
      break;

      // :::::::::::: S E C C I O N   V E N T A S ::::::::::::

      case 'listar_tabla_facturacion':

        $rspta = $facturacion->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_tipo_persona"], $_GET["filtro_comprobante"], $_GET["filtro_metodo_pago"], $_GET["filtro_centro_poblado"], $_GET["filtro_estado_sunat"] );
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
            
            $sunat_codigo = (int)$value['sunat_code'] ?? 0;
            $valores_sunat = ['RECHAZADA', 'POR ENVIAR']; // Lista de valores permitidos
            $valores_credito = ['pendiente', 'parcial']; // Lista de valores permitidos
            $array_tipo_comprobante = ['01', '03','07']; // Lista de valores permitidos

            $data[] = [
              "0" => $count++,
              "1" => '<div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">'.                  
                  ($value['venta_cuotas'] == 'SI' && in_array($value['vc_estado'], $valores_credito) ? '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="ver_pagar_venta(' . $value['idventa'] . ');" ><i class="bi bi-cash-coin"></i> Pagar cuota</a></li>' : '').
                  '<li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_venta(' . $value['idventa'] . ');" ><i class="bi bi-eye"></i> Ver</a></li>'.
                  ( in_array($value['sunat_estado'], $valores_sunat) || $value['tipo_comprobante'] == '12'  ? ( $_SESSION['user_cargo']=='ADMINISTRADOR'? '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="ver_editar_venta(' . $value['idventa'] . ');" ><i class="bi bi-pencil"></i> Editar</a></li>' :'') :'').
                  '<li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_ticket(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato Ticket</a></li>                
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_a4_completo(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato A4 completo</a></li>                
                  '.($value['venta_cuotas'] == 'NO' ? '' : '<li><a class="dropdown-item" href="javascript:void(0);" onclick="actualizar_estado_credito(' . $value['idventa'] .');" ><i class="bi bi-arrow-clockwise"></i> Actualizar estado crédito</a></li>').'
                  '.( $value['tipo_comprobante'] == '12' ? ( $_SESSION['user_cargo']=='ADMINISTRADOR'? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_papelera_venta(' . $value['idventa'] .', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\');" ><i class="bx bx-trash"></i> Eliminar o papelera </a></li>' : ''): '').'  
                  '.( in_array($value['tipo_comprobante'] , $array_tipo_comprobante) && ($value['sunat_estado'] <> 'ACEPTADA' && $value['sunat_estado'] <> 'POR ENVIAR' && $value['sunat_estado'] <> 'ANULADO') ? '<li><a class="dropdown-item text-warning" href="javascript:void(0);" onclick="cambiar_a_por_enviar(' . $value['idventa'] .', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\');" ><i class="bx bx-git-compare"></i> Cambiar a: Por Enviar </a></li>' : '').'  
                  '.( in_array($value['tipo_comprobante'] , $array_tipo_comprobante) && $sunat_codigo == 1033 ? '<li><a class="dropdown-item " href="javascript:void(0);" onclick="cambiar_a_aceptado(' . $value['idventa'] .', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] .'\', \'La '.$value['tipo_comprobante_v2'].' numero ' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . ',  ha sido aceptada\');" ><img src="../assets/images/company-logos/ico-sunat.svg" class="w-15px" alt="" > Cambiar a: Aceptado</a></li>' : '').'  
                  '.( in_array($value['tipo_comprobante'] , $array_tipo_comprobante) ? '<li><a class="dropdown-item " href="javascript:void(0);" onclick="status_documento_sunat(' . $value['idventa'] .', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\');" ><img src="../assets/images/company-logos/ico-sunat.svg" class="w-15px" alt="" > Validar Estado SUNAT </a></li>' : '').'  
                </ul>
              </div>',
              "2" =>  $value['idventa_v2'],
              "3" =>  $value['fecha_emision_format'],
              "4" =>  ($value['vc_estado'] == "pendiente" ?
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="mostrar_ventas_cuotas('. $value['idventa'] .', \''.$value['venta_cuotas'].'\', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\')" data-bs-toggle="tooltip" title="Ver cuotas"><i class="ri-time-line align-middle me-1 fs-11"></i>'.$value['vc_estado'].' '.($value['vc_cantidad_total'] - $value['vc_cantidad_pagada']).'</span>':
                ($value['vc_estado'] == "parcial" ? 
                '<span class="badge bg-warning-transparent cursor-pointer" onclick="mostrar_ventas_cuotas('. $value['idventa'] .', \''.$value['venta_cuotas'].'\', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\')" data-bs-toggle="tooltip" title="Ver cuotas"><i class="bi bi-circle-half align-middle me-1"></i>'.$value['vc_estado'].' '.($value['vc_cantidad_total'] - $value['vc_cantidad_pagada']).'</span>':
                ($value['vc_estado'] == "pagado" ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="mostrar_ventas_cuotas('. $value['idventa'] .', \''.$value['venta_cuotas'].'\', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\')" data-bs-toggle="tooltip" title="Ver cuotas"><i class="ri-check-fill align-middle me-1"></i>'.$value['vc_estado'].' '.( $value['venta_cuotas'] == 'SI' ?  $value['vc_cantidad_pagada'] : '' ).'</span>' : 
                '-')
                )                
              ),              
              "5" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/persona/perfil/' . $img_proveedor . '" alt="" onclick="ver_img_pefil(' .$value['idpersona_cliente'] . ')" onerror="'.$imagen_error.'"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary" data-bs-toggle="tooltip" title="'.$value['cliente_nombre_completo'] .'">'.$value['cliente_nombre_recortado'] .'</span>
                  <span class="text-muted"><b>'.$value['tipo_documento_abreviatura'] .'</b>: '. $value['numero_documento'].'</span>
                </div>
              </div>',
              "6" =>  '<b>'.$value['tipo_comprobante_v2'].'</b>' . ' <br> ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'],
              "7" =>  $value['venta_total_v2'] , 
              "8" =>  $value['user_en_atencion'],
              "9" => $value['tipo_comprobante'] == '01' || $value['tipo_comprobante'] == '03' || $value['tipo_comprobante'] == '07' ?
                (
                  $value['sunat_estado'] == 'ACEPTADA' ? 
                  '<a class="badge bg-outline-info fs-13 cursor-pointer m-r-5px" href="'.$url_xml.'" download data-bs-toggle="tooltip" title="Descargar XML" ><i class="bi bi-filetype-xml"></i></a>' . 
                  '<a class="badge bg-outline-info fs-13 cursor-pointer m-r-5px" href="'.$url_cdr.'" download data-bs-toggle="tooltip" title="Descargar CDR" ><i class="bi bi-journal-code"></i></a>' :
                  (
                    $value['sunat_estado'] == 'ANULADO'  ? '' :                  
                    '<span class="badge bg-outline-info fs-13 cursor-pointer m-r-5px" data-bs-toggle="tooltip" title="Enviar" onclick="reenviar_doc_a_sunat('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')"><i class="bi bi-upload"></i></span>'
                  )
                )
                : '' , 
              "10" => $value['cantidad_mp'] == 0 ? '' :  '<center><div class="svg-icon-background bg-warning-transparent cursor-pointer" onclick="ver_comprobante_pago('. $value['idventa'] .',\'&lt;b&gt;'.$value['tipo_comprobante_v2'].'&lt;/b&gt; ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'].'\');" data-bs-toggle="tooltip" title="Baucher: '. $value['metodos_pago_agrupado'] .'">                      
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-warning">
                  <!-- Documento -->
                  <path d="M11.5,20h-6a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1h5V7a3,3,0,0,0,3,3h3v3a1,1,0,0,0,2,0V9s0,0,0-.06a1.31,1.31,0,0,0-.06-.27l0-.09a1.07,1.07,0,0,0-.19-.28h0l-6-6h0a1.07,1.07,0,0,0-.28-.19.29.29,0,0,0-.1,0A1.1,1.1,0,0,0,11.56,2H5.5a3,3,0,0,0-3,3V19a3,3,0,0,0,3,3h5a1,1,0,0,0,0-2Zm1-14.59L15.09,8H13.5a1,1,0,0,1-1-1ZM7.5,14h6a1,1,0,0,0,0-2h-6a1,1,0,0,0,0,2Zm4,2h-4a1,1,0,0,0,0,2h4a1,1,0,0,0,0-2Zm-4-6h1a1,1,0,0,0,0-2h-1a1,1,0,0,0,0,2Z" />

                  <!-- Texto dinámico para el número -->
                  <text x="16" y="23" font-size="10" text-anchor="middle" fill="black" id="dynamic-number" class="svg-dark">
                    '.$value['cantidad_mp'].'
                  </text>
                </svg>
              </div></center>' , 
              "11" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
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

      case 'listar_tabla_ver_mas_detalle_facturacion':

        $rspta = $facturacion->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], null, $_GET["filtro_comprobante"], null, null, $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){           

            $data[] = [
              "0" => '<span class="text-nowrap fs-11">'. $value['idventa_v2'].'</span>',             
              "1" =>  $value['name_day'],
              "2" =>  $value['fecha_emision_format'],
              "3" => '<span class="text-nowrap fs-11">'. $value['name_month'] .'-'. $value['name_year'] .'</span>',
              "4" => '<span class="text-nowrap fs-11">'. $value['cliente_nombre_completo'].'</span>',
              "5" => '<span class="text-nowrap fs-11">'. $value['tipo_documento_abreviatura'] .'</span>',
              "6" => '<span class="text-nowrap fs-11">'. $value['numero_documento'].'</span>',
              "7" => '<span class="text-nowrap fs-11">'. $value['tipo_comprobante_v2'].'</span>',
              "8" => '<span class="text-nowrap fs-11">'. $value['serie_y_numero_comprobante'] .'</span>',
              "9" =>  $value['venta_total_v2'] ,
              "10" =>  $value['total_recibido'] ,
              "11" =>  $value['total_vuelto'] ,
              "12" => '<span class="text-nowrap fs-11">'. $value['metodos_pago_agrupado'] .'</span>',
              "13" => '<span class="text-nowrap fs-11">'. $value['user_created_v2'] .' '.$value['user_en_atencion'] .'</span>',
              "14" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
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

      case 'mostrar_metodo_pago':
        $rspta=$facturacion->mostrar_metodo_pago($_GET["idventa"]);
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
        $rspta = $facturacion->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $facturacion->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_producto':
        $rspta=$facturacion->mostrar_producto($_GET["search_producto"]);
        echo json_encode($rspta, true);
      break; 

      case 'mini_reporte':
        $rspta=$facturacion->mini_reporte($_GET["periodo_facturado"]);
        echo json_encode($rspta, true);
      break; 

      case 'mini_reporte_v2':
        $rspta = $facturacion->mini_reporte_v2($_GET["filtro_periodo"], $_GET["filtro_trabajador"]);
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

      case 'listar_producto_x_nombre':
        $rspta=$facturacion->listar_producto_x_nombre($_GET["search"]);
        echo json_encode($rspta, true);
      break;

      case 'validar_mes_cobrado':
        
        $periodo_pago = null;                                     // Inicializamos `periodo_pago` como vacío
        
        foreach ($_GET as $key => $value) {                       // Recorremos los parámetros GET para encontrar `valid_periodo_pago_X`
          if (preg_match('/^valid_periodo_pago_\d+$/', $key)) {   // Si el nombre del parámetro coincide
            $periodo_pago = $value;                               // Asignamos su valor a `periodo_pago`
            break;                                                // Salimos del bucle después de encontrar el primero
          }
        }

        $rspta=$facturacion->validar_mes_cobrado($_GET["idcliente"],$periodo_pago,$_GET["idventa_detalle"] );
        echo json_encode($rspta, true);
      break;

      case 'ver_meses_cobrado':
        $rspta=$facturacion->ver_meses_cobrado($_GET["idcliente"]);
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
        $rspta = $facturacion->listar_tabla_producto($_GET["tipo_producto"] ); 

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
              "3" =>  $value['precio_venta'],
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

      // ══════════════════════════════════════ C U O T A S  ══════════════════════════════════════
      case 'mostrar_cuotas':
        $rspta=$facturacion->mostrar_cuotas($_GET["id_venta"]);
        echo '
        <div class="table-responsive">
          <table class="table text-nowrap table-bordered">
            <thead>
              <tr>                
                <th scope="col"><i class="bi bi-gear"></i></th>
                <th scope="col">ID</th>
                <th scope="col">Cuota</th>
                <th scope="col">Estado</th>
                <th scope="col">Doc. Pago</th>
                <th scope="col">Programado</th>
                <th scope="col">Monto</th>
              </tr>
            </thead>
            <tbody>';
        foreach ($rspta['data'] as $key => $val) {
          echo '<tr >                
                <th class="py-1"><button class="btn btn-icon btn-sm btn-warning-light" onclick="actualizar_estado_cuota('.$val['idventa_cuotas'].','.$val['idventa'].')" data-bs-toggle="tooltip" title="Actualizar Estado"><i class="bi bi-arrow-clockwise"></i></button></th>
                <th class="py-1">'.$val['idventa_cuotas_v2'].'</th>
                <th class="py-1 text-center">'.$val['numero_cuota'].'</th>
                <td class="py-1 text-center"><span class="badge '. ($val['estado_cuota'] == 'pendiente' ? 'bg-danger-transparent' : 'bg-success-transparent') .'">'.$val['estado_cuota'].'</span></td>
                <td class="py-1 text-center">'.( empty($val['idventa']) ? '': '<span class="cursor-pointer" data-bs-toggle="tooltip" title="ID: '.$val['idventa_pago_v2'] . '" >'.$val['serie_comprobante'] . '-' . $val['numero_comprobante'] .'</span>').'</td>
                <td class="py-1">'.$val['fecha_vencimiento_format'].'</td>
                <td class="py-1 text-right">'. number_format( floatval($val['monto_cuota']) , 2, '.', ',').'</td>                
              </tr>';
        }               
        echo '</tbody>
          </table>
        </div> ';       
        
      break; 

      case 'actualizar_estado_credito':
        $rspta=$facturacion->actualizar_estado_credito($_GET["id_venta"]);
        echo json_encode($rspta, true);
      break;
      case 'actualizar_estado_cuota':
        $rspta=$facturacion->actualizar_estado_cuota($_GET["idventa_cuotas"], $_GET["id_venta"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════ U S A R   A N T I C I P O S ══════════════════════════════════════
      case 'mostrar_anticipos':
        $rspta=$facturacion->mostrar_anticipos($_GET["id_cliente"]);
        echo json_encode($rspta, true);
      break; 

      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      // ═══════                                         A G R E G A R   C L I E N T E                                                                    ═══════
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

      case 'guardar_editar_cliente':        

        if (empty($cli_idpersona)) {
          
          $rspta = $facturacion->agregar_nuevo_cliente(  $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
           $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo );

          echo json_encode($rspta, true);
        } else {

          $rspta = $facturacion->editar_nuevo_cliente( $cli_idpersona, $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
          $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo);
    
          echo json_encode($rspta, true);
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
        $rspta = $facturacion->select2_comprobantes_anular($_GET["tipo_comprobante"]); $cont = 1; $data = ""; #echo $rspta; die();
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

      case 'select2_codigo_x_anulacion_comprobante':
        $rspta = $facturacion->select2_codigo_x_anulacion_comprobante(); $cont = 1; $data = "";
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
        $rspta = $facturacion->select2_filtro_cliente(); $cont = 1; $data = "";
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
        $rspta = $facturacion->select2_banco();
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
        $rspta = $facturacion->select2_periodo_contable(); $cont = 1; $data = "";
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