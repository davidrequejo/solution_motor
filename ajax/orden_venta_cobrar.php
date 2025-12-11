<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['orden_venta_cobrar'] == 1) {

    require_once "../modelos/Orden_venta_cobrar.php";

    $Orden_venta_cobrar = new Orden_venta_cobrar();  

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idventa              = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";   
    $f_idsunat_c01        = isset($_POST["f_idsunat_c01"]) ? limpiarCadena($_POST["f_idsunat_c01"]) : "";   
    $f_tipo_comprobante   = isset($_POST["f_tipo_comprobante"]) ? limpiarCadena($_POST["f_tipo_comprobante"]) : "";   
    $f_serie_comprobante  = isset($_POST["f_serie_comprobante"]) ? limpiarCadena($_POST["f_serie_comprobante"]) : "";   
    $f_idpersona_cliente  = isset($_POST["f_idpersona_cliente"]) ? limpiarCadena($_POST["f_idpersona_cliente"]) : "";  
    $f_venta_total        = isset($_POST["f_venta_total"]) ? limpiarCadena($_POST["f_venta_total"]) : "";  
     
    
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


      case 'guardar_editar_OrdenVenta':

        $rspta = ""; $mp_comprobante = ""; 
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= "";       

        $file_nombre_new = [];                        // Amacenar los nombres de los documentos             
        $file_nombre_old = [];                        // Amacenar los nombres de los documentos             
        $file_size = [];                        // Amacenar los nombres de los documentos             
        
        $comprobantes = isset($_POST['f_mp_comprobante'])  ?? []; // Recibe el array de archivos
        $resultados = [];                           // Almacenar resultados para cada archivo       
        //ksort($comprobantes);                       // Reorganizar el orden de indices
        //echo json_encode($comprobantes, true); die();
       /* foreach ($comprobantes as $key => $comprobante) {
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
        }*/        
        
        $rspta = [];
        if (empty($f_idventa)) { // CREAMOS UN NUEVO REGISTRO
          
          $rspta = $Orden_venta_cobrar->insertar( 
            $idventa,$f_idsunat_c01,$f_tipo_comprobante,$f_serie_comprobante,$f_idpersona_cliente,$f_venta_total,$_POST["f_metodo_pago"],$_POST["f_total_recibido"],$_POST["f_total_vuelto"],
            $_POST["f_mp_serie_comprobante"], $file_nombre_new, $file_nombre_old, $file_size); 
          // echo json_encode($rspta, true); die();
          $f_idventa = $rspta['id_tabla'];

        } 

        if ($rspta['status'] == true) {             // validacion de creacion de documento                         
        
          if ($f_tipo_comprobante == '12') {          // SUNAT TICKET     
            $update_sunat = $Orden_venta_cobrar->actualizar_respuesta_sunat( $f_idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
            echo json_encode($rspta, true);             

          } else {

            $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
            $update_sunat = $Orden_venta_cobrar->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);              
            
            if ($update_sunat['status'] == false ) { 
              echo json_encode($update_sunat, true);
            } else {
              echo json_encode($rspta, true); 
            }              
            
          }               
          
        } else{
          echo json_encode($rspta, true);
        }
    
      break; 

      case 'anularOrden':
        $rspta = $Orden_venta_cobrar->anularOrden($_GET['idventa']);
        echo json_encode($rspta, true);
      break;

      case 'listarOrdenesCobrar':
        $rspta = $Orden_venta_cobrar->listarOrdenesCobrar($_GET['filtro_fecha_i'],$_GET['filtro_fecha_f']);
        echo json_encode($rspta, true);
      break;

      
      case 'select2_series_comprobante':
        $rspta = $Orden_venta_cobrar->select2_series_comprobante($_GET["tipo_comprobante"]); $cont = 1; $data = "";
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
    
      case 'select2_banco':
        $rspta = $Orden_venta_cobrar->select2_banco();
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

      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
      // ═══════                                         A G R E G A R   C L I E N T E                                                                    ═══════
      // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

      case 'guardar_editar_cliente':        

        if (empty($cli_idpersona)) {
          
          $rspta = $Orden_venta_cobrar->agregar_nuevo_cliente(  $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
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
        $rspta = $Orden_venta_cobrar->select2_cliente(); $cont = 1; $data = "";
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