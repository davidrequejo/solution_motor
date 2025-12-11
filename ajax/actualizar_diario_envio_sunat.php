<?php
require_once "../sunat/ConfigSunat.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

  require_once "../modelos/Facturacion.php";
  require_once "../modelos/Producto.php";
  require_once "../modelos/Avance_cobro.php";
  require_once "../modelos/Reporte_copia_estock.php";

  require '../vendor/autoload.php';                   // CONEXION A COMPOSER
  $see = require '../sunat/SunatCertificado.php';   // EMISION DE COMPROBANTES

  $facturacion          = new Facturacion();      
  $productos            = new Producto(); 
  $avance_cobro         = new Avance_cobro();  
  $reporte_copia_estock = new Reporte_copia_estock();

  date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
  $imagen_error = "this.src='../assets/svg/404-v2.svg'";
  $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

  // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

  $f_idventa                = isset($_POST["f_idventa"]) ? limpiarCadena($_POST["f_idventa"]) : "";   
  $f_impuesto               = isset($_POST["f_impuesto"]) ? limpiarCadena($_POST["f_impuesto"]) : "";   
  $f_crear_y_emitir         = isset($_POST["f_crear_y_emitir"]) ? limpiarCadena($_POST["f_crear_y_emitir"]) : "NO";   

  switch ($_GET["op"]){

    // :::::::::::: S E C C I O N  FACTURACION :::::::::::: 
   

    case 'reenviar_sunat':

      $data_no_enviada = $facturacion->comprobantes_no_enviado_a_sunat( );
      //echo json_encode($data_no_enviada, true); die();

      foreach ($data_no_enviada['data'] as $key => $val) {      

        $f_idventa          = $val["idventa"];
        $tipo_comprobante = $val["tipo_comprobante"];
        $nc_idventa       = $val["iddocumento_relacionado"];

        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
        $observacion_ejecucion= "";

        if ($tipo_comprobante == '12') {          // SUNAT TICKET     
          $observacion_ejecucion= "Documento que se queria actualizar es una 12-TIKET, lo cual no procede.";
          $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
        } else if ($tipo_comprobante == '01') {   // SUNAT FACTURA         

          include( '../modelos/SunatFactura.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            $observacion_ejecucion= "No hubo errores en el envio a sunat.";
            $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          } else {    
            $observacion_ejecucion= "Error en la insercion de los datos.";          
            $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          }              
          
        } else if ($tipo_comprobante == '03') {   // SUNAT BOLETA 
          
          include( '../modelos/SunatBoleta.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            $observacion_ejecucion= "No hubo errores en el envio a sunat.";
            $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          } else {      
            $observacion_ejecucion= "Error en la insercion de los datos.";        
            $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          }            
          
        } else if ($tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
          include( '../modelos/SunatNotaCredito.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error)  ) {
            $observacion_ejecucion= "No hubo errores en el envio a sunat.";
            $update_sunat = $facturacion->actualizar_doc_anulado_x_nota_credito( $f_idventa); // CAMBIAMOS DE ESTADO EL DOC ANULADO
            $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          } else {      
            $observacion_ejecucion= "Error en la insercion de los datos.";        
            $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          }
        } else {
          $observacion_ejecucion= "Tipo de Documeneto no especificado, porfavor tener encuenta.";
          $update_sunat = $facturacion->crear_bitacora_reenvio_sunat( $f_idventa, $observacion_ejecucion, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
         
        }
      }
    break;  

    case 'cambiar_a_por_enviar':
      $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
      $update_sunat = $facturacion->actualizar_respuesta_sunat( $_GET["idventa"], $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);        
    break;

    case 'guardar_editar_copia_stock':
      $rspta = $reporte_copia_estock->crear_copia_stock( date("Y-m-d") );      
    break;

    default: 
      $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
    break;

  }  

ob_end_flush();

?>