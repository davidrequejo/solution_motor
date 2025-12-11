<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['facturacion'] == 1) {

    require_once "../modelos/Guia_de_remision.php";
    require_once "../modelos/Gasto_de_trabajador.php";
    require_once "../modelos/Correlacion_comprobante.php";
    require_once "../modelos/Producto.php";
    require_once "../modelos/Facturacion.php";
    require_once "../modelos/Persona.php";

    require '../vendor/autoload.php';                   // CONEXION A COMPOSER
    require '../sunat/Util.php';    
    $see = require '../sunat/SunatCertificado.php';   // EMISION DE COMPROBANTES
    $util = Util::getInstance();

   

    $guia_remision_remitente  = new Guia_Remision_Remitente();    
    $gasto_trab               = new Gasto_de_trabajador();    
    $correlacion_compb        = new Correlacion_comprobante();    
    $productos                = new Producto();
    $facturacion              = new Facturacion();
    $persona                  = new Persona();

  

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $f_crear_y_emitir         = isset($_POST["f_crear_y_emitir"]) ? limpiarCadena($_POST["f_crear_y_emitir"]) : "NO"; 
    $f_tipo_comprobante         = isset($_POST["f_tipo_comprobante"]) ? limpiarCadena($_POST["f_tipo_comprobante"]) : ""; 

    $f_idventa               = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";    
    $serie_comprobante    = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";    
    $modalidad_transporte = isset($_POST["modalidad_transporte"]) ? limpiarCadena($_POST["modalidad_transporte"]) : "";    
    $motivo_traslado      = isset($_POST["motivo_traslado"]) ? limpiarCadena($_POST["motivo_traslado"]) : "";    
    $documento_asociado   = isset($_POST["documento_asociado"]) ? limpiarCadena($_POST["documento_asociado"]) : "";    
    $idcliente            = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";    
    $partida_direccion    = isset($_POST["partida_direccion"]) ? limpiarCadena($_POST["partida_direccion"]) : "";    
    $partida_distrito     = isset($_POST["partida_distrito"]) ? limpiarCadena($_POST["partida_distrito"]) : "";    
    $partida_ubigeo       = isset($_POST["partida_ubigeo"]) ? limpiarCadena($_POST["partida_ubigeo"]) : "";    
    $llegada_direccion    = isset($_POST["llegada_direccion"]) ? limpiarCadena($_POST["llegada_direccion"]) : "";    
    $llegada_distrito     = isset($_POST["llegada_distrito"]) ? limpiarCadena($_POST["llegada_distrito"]) : "";    
    $llegada_ubigeo       = isset($_POST["llegada_ubigeo"]) ? limpiarCadena($_POST["llegada_ubigeo"]) : "";    
    $peso_total           = isset($_POST["peso_total"]) ? limpiarCadena($_POST["peso_total"]) : "";    
    $idpersona_chofer     = isset($_POST["idpersona_chofer"]) ? limpiarCadena($_POST["idpersona_chofer"]) : "";    
    $numero_documento     = isset($_POST["numero_documento"]) ? limpiarCadena($_POST["numero_documento"]) : "";    
    $numero_licencia      = isset($_POST["numero_licencia"]) ? limpiarCadena($_POST["numero_licencia"]) : "";    
    $numero_placa         = isset($_POST["numero_placa"]) ? limpiarCadena($_POST["numero_placa"]) : "";    
    $nombre_razonsocial   = isset($_POST["nombre_razonsocial"]) ? limpiarCadena($_POST["nombre_razonsocial"]) : "";    
    $apellidos_nombrecomercial = isset($_POST["apellidos_nombrecomercial"]) ? limpiarCadena($_POST["apellidos_nombrecomercial"]) : "";  
     
    $impuesto         = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : ""; 

    $f_guia_subtotal         = isset($_POST["f_guia_subtotal"]) ? limpiarCadena($_POST["f_guia_subtotal"]) : "";    
    $f_tipo_gravada           = isset($_POST["f_tipo_gravada"]) ? limpiarCadena($_POST["f_tipo_gravada"]) : "";
    $f_guia_descuento        = isset($_POST["f_guia_descuento"]) ? limpiarCadena($_POST["f_guia_descuento"]) : "";    
    $f_guia_igv              = isset($_POST["f_guia_igv"]) ? limpiarCadena($_POST["f_guia_igv"]) : "";            
    $f_guia_total            = isset($_POST["f_guia_total"]) ? limpiarCadena($_POST["f_guia_total"]) : "";
    
    $gr_observacion         = isset($_POST["gr_observacion"]) ? limpiarCadena($_POST["gr_observacion"]) : ""; 
    
    
    // :::::::::::: S E C C I O N   C H O F E R ::::::::::::
    $cp_idpersona                 = isset($_POST["cp_idpersona"]) ? limpiarCadena($_POST["cp_idpersona"]) : "";   
    $cp_tipo_persona_sunat        = isset($_POST["cp_tipo_persona_sunat"]) ? limpiarCadena($_POST["cp_tipo_persona_sunat"]) : "";   
    $cp_idtipo_persona            = isset($_POST["cp_idtipo_persona"]) ? limpiarCadena($_POST["cp_idtipo_persona"]) : "";   
    
    $cp_tipo_documento            = isset($_POST["cp_tipo_documento"]) ? limpiarCadena($_POST["cp_tipo_documento"]) : "";   
    $cp_numero_documento          = isset($_POST["cp_numero_documento"]) ? limpiarCadena($_POST["cp_numero_documento"]) : "";   
    $cp_nombre_razonsocial        = isset($_POST["cp_nombre_razonsocial"]) ? limpiarCadena($_POST["cp_nombre_razonsocial"]) : "";   
    $cp_apellidos_nombrecomercial = isset($_POST["cp_apellidos_nombrecomercial"]) ? limpiarCadena($_POST["cp_apellidos_nombrecomercial"]) : "";   
    $cp_correo                    = isset($_POST["cp_correo"]) ? limpiarCadena($_POST["cp_correo"]) : "";   
    $cp_celular                   = isset($_POST["cp_celular"]) ? limpiarCadena($_POST["cp_celular"]) : "";   
    $cp_numero_licencia           = isset($_POST["cp_numero_licencia"]) ? limpiarCadena($_POST["cp_numero_licencia"]) : "";   
    $cp_placa_vehiculo            = isset($_POST["cp_placa_vehiculo"]) ? limpiarCadena($_POST["cp_placa_vehiculo"]) : "";   

    
    switch ($_GET["op"]){

      // :::::::::::: S E C C I O N   C O M P R A S ::::::::::::

      case 'guardar_editar_guia':     
        
        $rspta = ""; $mp_comprobante = ""; 
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 

        if (empty($f_idventa)) {
          
          $rspta = $guia_remision_remitente->insertar( $serie_comprobante, $modalidad_transporte, $motivo_traslado, $documento_asociado, $idcliente, $partida_direccion,
          $partida_distrito, $partida_ubigeo, $llegada_direccion, $llegada_distrito, $llegada_ubigeo, $peso_total, $idpersona_chofer,
          $numero_documento, $numero_licencia, $numero_placa, $nombre_razonsocial, $apellidos_nombrecomercial, 
          $f_guia_subtotal, $f_guia_igv, $f_guia_descuento, $f_guia_total,$gr_observacion,
          // Detalle Guia
          $_POST["idproducto"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
          $_POST["f_descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"]);

          $f_idventa =  $rspta['id_tabla'];
        } else {

          $rspta = $guia_remision_remitente->editar( $f_idventa, $serie_comprobante, $modalidad_transporte, $motivo_traslado, $documento_asociado, $idcliente, $partida_direccion,
          $partida_distrito, $partida_ubigeo, $llegada_direccion, $llegada_distrito, $llegada_ubigeo, $peso_total, $idpersona_chofer,
          $numero_documento, $numero_licencia, $numero_placa, $nombre_razonsocial, $apellidos_nombrecomercial, 
          $f_guia_subtotal, $f_guia_igv, $f_guia_descuento, $f_guia_total,$gr_observacion,
          // Detalle Guia
          $_POST["idproducto"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
          $_POST["f_descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"]);
    
          
        }

        if ($rspta['status'] == true) {               // validacion de creacion de documento       
          if ($f_crear_y_emitir == 'SI') {            // NO ENVIAR A SUNAT
            if ($f_tipo_comprobante == '09') {        // GUIA REMISION REMITENTE
              include( '../modelos/SunatGuiaRemisionRemitente.php');
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);            
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
            }else {
              $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true);
            }
          } else {
            $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
            $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);              
            
            if ($update_sunat['status'] == false ) { 
              echo json_encode($update_sunat, true);
            } else {
              echo json_encode($rspta, true); 
            }
          }
        }else {
          echo json_encode($rspta, true);
        }
    
      break;       
      
      case 'listar_tabla_guia':

        $rspta = $guia_remision_remitente->listar_tabla_guia($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $img_proveedor = empty($value['foto_perfil']) ? 'no-perfil.jpg' : $value['foto_perfil'];

            $url_xml = ""; $url_cdr = "";

            if ($value['tipo_comprobante'] == '12') {          // SUNAT TICKET           
            } else if ($value['tipo_comprobante'] == '01') {   // SUNAT FACTURA             
            } else if ($value['tipo_comprobante'] == '03') {   // SUNAT BOLETA              
            } else if ($value['tipo_comprobante'] == '07') {   // SUNAT NOTA DE CREDITO 
            } else if ($value['tipo_comprobante'] == '09') {   // GUIA REMISION REMITENTE
              $url_xml = '../assets/modulo/facturacion/guia_remision_remitente/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/guia_remision_remitente/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';

            } else {            
            }            
            
            $valores_sunat = ['RECHAZADA', 'POR ENVIAR']; // Lista de valores permitidos
            $valores_credito = ['pendiente', 'parcial']; // Lista de valores permitidos

            $data[] = [
              "0" => $count++,
              "1" => '<div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">'.                  
                  '<!--<li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_venta(' . $value['idventa'] . ');" ><i class="bi bi-eye"></i> Ver</a></li>--> '.
                  ( in_array($value['sunat_estado'], $valores_sunat) || $value['tipo_comprobante'] == '12'  ? '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="mostrar_editar_guia(' . $value['idventa'] . ');" ><i class="bi bi-pencil"></i> Editar</a></li>':'').
                  '<!-- <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_ticket(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato Ticket</a></li>-->                
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_a4_completo(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato A4 completo</a></li>                
                  '.( ($value['tipo_comprobante'] == '09' ) && ($value['sunat_estado'] <> 'ACEPTADA' && $value['sunat_estado'] <> 'POR ENVIAR' && $value['sunat_estado'] <> 'ANULADO') ? '<li><a class="dropdown-item text-warning" href="javascript:void(0);" onclick="cambiar_a_por_enviar(' . $value['idventa'] .', \''. '<b>'.$value['tipo_comprobante_v2'].' </b>' .  $value['serie_comprobante'] . '-' . $value['numero_comprobante'] . '\');" ><i class="bx bx-git-compare"></i> Cambiar a: Por Enviar </a></li>' : '').'  
                </ul>
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
              "6" =>   '<b>'.$value['gr_chofer_nombre'] .'</b>' . ' <br> ' . $value['gr_chofer_nombre_tipo_documento'] .': '. $value['gr_chofer_numero_documento'] . ' | Placa: '. $value['gr_placa_no_guion'] ,
              "7" =>   $value['gr_cod_modalidad_traslado'] . ' ' . $value['gr_modalidad_traslado'], 
              "8" =>  $value['user_en_atencion'],
              "9" => $value['tipo_comprobante'] == '09'  ?
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
              "10" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
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

      case 'mostrar_detalle_guia':
        $rspta=$guia_remision_remitente->mostrar_detalle_guia($idcompra);
        

        echo '<div class="tab-pane fade active show" id="rol-compra-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">          
            <tbody>
              <tr> <th>Proveedor</th>        <td>'.$rspta['data']['compra']['nombre_razonsocial'].'  '.$rspta['data']['compra']['apellidos_nombrecomercial'].'
              <div class="font-size-12px" >Cel: <a href="tel:+51'.$rspta['data']['compra']['celular'].'">'.$rspta['data']['compra']['celular'].'</a></div> 
              <div class="font-size-12px" >E-mail: <a href="mailto:'.$rspta['data']['compra']['correo'].'">'.$rspta['data']['compra']['correo'].'</a></div> </td> </tr>            
              <tr> <th>Total compra</th>      <td>'.$rspta['data']['compra']['total'].'</td> </tr>             
              <tr> <th>Fecha</th>         <td>'.$rspta['data']['compra']['fecha_compra'].'</td> </tr>                
              <tr> <th>Comprobante</th>   <td>'.$rspta['data']['compra']['tp_comprobante']. ' | '.$rspta['data']['compra']['serie_comprobante'].'</td> </tr>
              <tr> <th>Observacion</th>   <td>'.$rspta['data']['compra']['descripcion'].'</td> </tr>         
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
                <h6 class="font-weight-bold ">S/ '.$rspta['data']['compra']['subtotal'].'</h6> 
                <h6 class="font-weight-bold ">S/ '.$rspta['data']['compra']['igv'].'</h6>                 
                <h5 class="font-weight-bold ">S/ '.$rspta['data']['compra']['total'].'</h5>                 
              </th>              
            </tfoot>
          </table>
        </div>';
        echo'</div>';# div-content
      break; 

      case 'mostrar_editar_guia':
        $rspta=$guia_remision_remitente->mostrar_editar_guia($_GET["idguia"]);
        echo json_encode($rspta, true);
      break;       

      case 'listar_producto_x_codigo':
        $rspta=$guia_remision_remitente->listar_producto_x_codigo($_POST["codigo"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $guia_remision_remitente->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $guia_remision_remitente->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'reenviar_sunat':

        $f_idventa          = $_GET["idventa"];
        $tipo_comprobante = $_GET["tipo_comprobante"];
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 

        if ($tipo_comprobante == '12') {          // SUNAT TICKET     
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Sin respuesta!!', 'message' => 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        } else if ($tipo_comprobante == '01') {   // SUNAT FACTURA         

          include( '../modelos/SunatFactura.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion , 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true); 
          }              
          
        } else if ($tipo_comprobante == '03') {   // SUNAT BOLETA 
          
          include( '../modelos/SunatBoleta.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true);
          }            
          
        } else if ($tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
          include( '../modelos/SunatNotaCredito.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true);
          }     
        } else if ($tipo_comprobante == '09') {   // SUNAT NOTA DE CREDITO 
          include( '../modelos/SunatGuiaRemisionRemitente.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else { 
            if ( !empty($sunat_observacion) ) {
              $retorno = array( 'status' => 'error_personalizado', 'icon' => 'info', 'titulo' => 'Hay algunas observaciones!!', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true);
            } else if ( !empty($sunat_error) ) {
              $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true);
            }            
          }  
        } else {
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }
      break;

      case 'ver_estado_documento':
        $rspta=$facturacion->mostrar_venta($_GET["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'cambiar_a_por_enviar':
        $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
        $update_sunat = $facturacion->actualizar_respuesta_sunat( $_GET["idventa"], $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);              
        echo json_encode($update_sunat, true);
      break;

      // ══════════════════════════════════════ P R O D U C T O ══════════════════════════════════════

      case 'mostrar_producto':
        $rspta=$facturacion->mostrar_producto($_GET["search"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_producto_x_nombre':
        $rspta=$facturacion->listar_producto_x_nombre($_GET["search"]);
        echo json_encode($rspta, true);
      break; 

      case 'listar_tabla_producto':
          
        $rspta = $guia_remision_remitente->listar_tabla_producto(); 

        $datas = []; 

        if ($rspta['status'] == true) {

          foreach($rspta['data'] as $key => $value){

            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data_btn_1 = 'btn-add-producto-1-'.$value['idproducto']; $data_btn_2 = 'btn-add-producto-2-'.$value['idproducto'];
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn_1.' mr-1 px-1 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto'] . ', false)" data-toggle="tooltip" data-original-title="Agregar continuo"><span class="fa fa-plus"></span></button>
              <button class="btn btn-success '.$data_btn_2.' px-1 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto'] . ', true)" data-toggle="tooltip" data-original-title="Agregar individual"><i class="fa-solid fa-list-ol"></i></button>',
              "1" => ('<i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno']) ,
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                        <div>
                          <span class="d-block fs-11 mb-0 fw-semibold text-primary nombre_producto_' . $value['idproducto'] . '">'.$value['nombre'] .'</span>
                          <span class="d-block fs-10 text-muted">Marca: <b>'.$value['marca'].'</b> | Cat: <b>'.$value['categoria'].'</b></span> 
                        </div>
                      </div>',             
              "3" => ($value['precio_venta']),
              "4" => '<textarea class="textarea_datatable fs-11 bg-light"  readonly>' .($value['descripcion']). '</textarea>'
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

      // ══════════════════════════════════════ O B T E N E R   V E N T A ══════════════════════════════════════

      case 'mostrar_detalle_venta':
        $rspta=$facturacion->mostrar_detalle_venta($_GET["idventa"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════ A G R E G A R   C H O F E R ══════════════════════════════════════   
      case 'guardar_editar_chofer_publico':        

        if (empty($cp_idpersona)) {
          
          $rspta = $guia_remision_remitente->agregar_chofer_publico(  $cp_tipo_persona_sunat, $cp_idtipo_persona, $cp_tipo_documento, $cp_numero_documento, $cp_nombre_razonsocial, $cp_apellidos_nombrecomercial, $cp_correo, $cp_celular, $cp_numero_licencia, $cp_placa_vehiculo);

          echo json_encode($rspta, true);
        } else {

          $rspta = $guia_remision_remitente->editar_chofer_publico( $cp_idpersona, $cp_tipo_persona_sunat, $cp_idtipo_persona, $cp_tipo_documento, $cp_numero_documento, $cp_nombre_razonsocial, $cp_apellidos_nombrecomercial, $cp_correo, $cp_celular, $cp_numero_licencia, $cp_placa_vehiculo);
    
          echo json_encode($rspta, true);
        }
    
      break;
      // ══════════════════════════════════════ S E A R C H   C H O F E R ══════════════════════════════════════     

      case 'buscar_chofer':
        $rspta=$guia_remision_remitente->buscar_chofer($_GET["search_nombre"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_un_chofer':
        $rspta=$persona->mostrar_persona_id($_GET["id"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
      case 'select2_modalidad_transporte':
        $rspta = $guia_remision_remitente->select2_modalidad_transporte();
        $data = "";
  
        if ($rspta['status']) {  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['codigo'] . '"  >' . $value['nombre'] . '</option>';
          }  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_motivo_traslado':
        $rspta = $guia_remision_remitente->select2_motivo_traslado();
        $data = "";
  
        if ($rspta['status']) {  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['codigo'] . '"  >' . $value['nombre'] . '</option>';
          }  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_chofer_publico':
        $rspta = $guia_remision_remitente->select2_chofer_publico(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option placa_vehiculo="' . $value['placa_vehiculo']  . ' " value="' . $value['idpersona']  . '">' . $value['tipo_persona'] . ': ' . $value['nombre'] . ' '. $value['apellido'] . ' - '. $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'listar_cliente':
        $rspta = $guia_remision_remitente->listar_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona_cliente']  . '>' . $value['cliente_nombre_completo'] . ' - '. $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option  value="1" >CLIENTE VARIOS</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'listar_crl_comprobante':
        $rspta = $correlacion_compb->listar_crl_comprobante($_GET["tipos"]); $cont = 1; $data = "";
          if($rspta['status'] == true){
            foreach ($rspta['data'] as $key => $value) {
              $data .= '<option  value=' . $value['codigo']  . '>' . $value['tipo_comprobante'] . '</option>';
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