<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['orden_venta'] == 1) {
   

    require_once "../modelos/Orden_venta.php";
    require_once "../modelos/Persona.php";
    $pre_ticket = new Orden_venta();
    $persona    = new Persona();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
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

      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      // ═══════                                         A G R E G A R   O R D E N   D E   V E N T A                                                      ═══════
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      case 'guardar_editar_orden':
        $rspta = [];
         $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
        if (empty($o_idventa)) { // CREAMOS UN NUEVO REGISTRO
          
          $rspta = $pre_ticket->insertar( $o_impuesto, $o_idsunat_c01, $o_tipo_comprobante, $o_serie_comprobante, $o_idcliente, $o_observacion_documento, $o_venta_subtotal, $o_venta_descuento, 
            $o_venta_igv, $o_venta_total,          
            # Detalle de venta
            $_POST["idproducto_presentacion"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"],  $_POST["cantidad_presentacion"], $_POST["cantidad_venta"], 
            $_POST["cantidad_total"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
            $_POST["descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_x_producto"], $_POST["subtotal_no_descuento_producto"], $_POST["precio_por_mayor"]
          ); 
          // echo json_encode($rspta, true); die();
          $o_idventa = $rspta['id_tabla'];

        } else {                // EDITAMOS EL REGISTRO

          $rspta = $pre_ticket->editar( $o_idventa, $o_impuesto, $o_idsunat_c01, $o_tipo_comprobante, $o_serie_comprobante, $o_idcliente, $o_observacion_documento, $o_venta_subtotal, $o_venta_descuento, 
            $o_venta_igv, $o_venta_total,            
            # Detalle de venta
            $_POST["idproducto_presentacion"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["cantidad_presentacion"], $_POST["cantidad_venta"], 
            $_POST["cantidad_total"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
            $_POST["descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_x_producto"], $_POST["subtotal_no_descuento_producto"], $_POST["precio_por_mayor"]
          ); 
          
        }
        if ($rspta['status'] == true) {
          $update_sunat = $pre_ticket->actualizar_respuesta_sunat( $o_idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          echo json_encode($rspta, true);
        } else {
          echo json_encode($rspta, true);
        }
      break; 
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      // ═══════                                          P R O D U C T O                                                                                 ═══════
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

      case 'listar_producto_x_nombre':
        $rspta=$pre_ticket->listar_producto_x_nombre($_GET["search"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_producto_select_pedido':
        $rspta=$pre_ticket->listar_producto_select_pedido($_GET["precio_por_mayor"],$_GET["idproducto_sucursal"],$_GET["idproducto_presentacion"]);
       
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
      case 'select2_cliente':
        $rspta = $pre_ticket->select2_cliente();
        $data = "";

        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idpersona_cliente'] . '" title ="' . $value['cliente_nombre_completo'] . '" >' . $value['cliente_nombre_completo'] . '- '.$value['numero_documento'].'</option>';
          }
  
          $retorno = array( 'status' => true,  'message' => 'Salió todo ok', 'data' => $data, );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      // ═══════                                         A G R E G A R   C L I E N T E                                                                    ═══════
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

      case 'guardar_editar_cliente':        

        if (empty($cli_idpersona)) {
          
          $rspta = $pre_ticket->agregar_nuevo_cliente(  $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
           $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo );

          echo json_encode($rspta, true);
        } else {

          $rspta = $pre_ticket->editar_nuevo_cliente( $cli_idpersona, $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
          $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo);
    
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