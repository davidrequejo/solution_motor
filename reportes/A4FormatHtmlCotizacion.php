<?php
require '../vendor/autoload.php';
use Luecano\NumeroALetras\NumeroALetras;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

// use Melihovv\Base64ImageDecoder\Base64ImageEncoder;


date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
$imagen_error = "this.src='../dist/svg/404-v2.svg'";
$toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';    
$scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/venta_romero/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/');

//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1){session_start();} 

if (!isset($_SESSION["user_nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['facturacion'] == 1) { 
    
    require_once "../modelos/Facturacion.php";                                        // Incluímos la clase Venta
    
    $facturacion    = new Facturacion();                                              // Instanciamos a la clase con el objeto venta
    $numero_a_letra = new NumeroALetras();                                            // Instanciamos a la clase con el objeto venta
   

    if (!isset($_GET["id"])) { echo "Datos incompletos (indefinido)"; die(); }        // Validamos la existencia de la variable
    if (empty($_GET["id"])) {  echo "Datos incompletos (".$_GET["id"].")"; die(); }   // validamos el valor de la variable
    
    $empresa_f        = $facturacion->datos_empresa();    
    $venta_f          = $facturacion->mostrar_detalle_venta($_GET["id"]);   

    if ( empty($venta_f['data']['venta']) ) { echo "Comprobante no existe"; die();  }

    $logo_empresa = "../assets/modulo/facturacion/cotizacion/proform-cabecera.png";      
    // $encoder = Base64ImageEncoder::fromFileName($logo_empresa, $allowedFormats = ['jpeg', 'png', 'gif']);    
    // $encoder->getMimeType(); // image/jpeg for instance
    // $encoder->getContent(); // base64 encoded image bytes.
    // $logo_empresa_b64 = $encoder->getDataUri(); // a base64 data-uri to use in HTML or CSS attributes.

    // Emrpesa emisora ================================================================================
    $e_razon_social       = empty($empresa_f['data']['nombre_razon_social']) ? '' : mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_razon_social'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_comercial          = empty($empresa_f['data']['nombre_comercial']) ? '' : mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_comercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_domicilio_fiscal   = empty($empresa_f['data']['domicilio_fiscal']) ? '' : mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'UTF-8', mb_detect_encoding($empresa_f['data']['domicilio_fiscal'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
    $e_numero_documento   = $empresa_f['data']['numero_documento'];
    $e_telefono1          = $empresa_f['data']['telefono1'];
    $e_telefono2          = $empresa_f['data']['telefono2'];
    $e_correo             = empty($empresa_f['data']['correo']) ? '' : mb_convert_encoding($empresa_f['data']['correo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['correo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web                = empty($empresa_f['data']['web']) ? '' : mb_convert_encoding($empresa_f['data']['web'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web_consulta_cp    = empty($empresa_f['data']['web_consulta_cp']) ? '' : mb_convert_encoding($empresa_f['data']['web_consulta_cp'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web_consulta_cp'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $e_distrito           = empty($empresa_f['data']['distrito']) ? '' : mb_convert_encoding($empresa_f['data']['distrito'], 'UTF-8', mb_detect_encoding($empresa_f['data']['distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_provincia          = empty($empresa_f['data']['provincia']) ? '' : mb_convert_encoding($empresa_f['data']['provincia'], 'UTF-8', mb_detect_encoding($empresa_f['data']['provincia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_departamento       = empty($empresa_f['data']['departamento']) ? '' : mb_convert_encoding($empresa_f['data']['departamento'], 'UTF-8', mb_detect_encoding($empresa_f['data']['departamento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_codubigueo         = empty($empresa_f['data']['codubigueo']) ? '' : mb_convert_encoding($empresa_f['data']['codubigueo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['codubigueo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    // Cliente receptor ================================================================================
    $c_nombre_completo    = empty($venta_f['data']['venta']['cliente_nombre_completo']) ? '' : mb_convert_encoding($venta_f['data']['venta']['cliente_nombre_completo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cliente_nombre_completo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_tipo_documento     = $venta_f['data']['venta']['tipo_documento'];
    $c_tipo_documento_name= $venta_f['data']['venta']['nombre_tipo_documento'];
    $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
    $c_celular            = empty($venta_f['data']['venta']['celular']) ? '' : mb_convert_encoding($venta_f['data']['venta']['celular'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['celular'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_correo             = empty($venta_f['data']['venta']['correo']) ? '' : mb_convert_encoding($venta_f['data']['venta']['correo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['correo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_direccion          = empty($venta_f['data']['venta']['direccion']) ? '' : mb_convert_encoding($venta_f['data']['venta']['direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_nc_serie_y_numero  = empty($venta_f['data']['venta']['nc_serie_y_numero']) ? '' : mb_convert_encoding($venta_f['data']['venta']['nc_serie_y_numero'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_serie_y_numero'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
    // Data comprobante ================================================================================
    $user_en_atencion     = mb_convert_encoding($venta_f['data']['venta']['user_en_atencion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['user_en_atencion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $fecha_emision        = $venta_f['data']['venta']['fecha_emision'];
    $fecha_emision_format = $venta_f['data']['venta']['fecha_emision_format'];
    $fecha_emision_dmy    = $venta_f['data']['venta']['fecha_emision_dmy'];
    $fecha_emision_hora12 = $venta_f['data']['venta']['fecha_emision_hora12'];
    $serie_comprobante    = $venta_f['data']['venta']['serie_comprobante'];
    $numero_comprobante   = $venta_f['data']['venta']['numero_comprobante'];
    $serie_y_numero_comprobante   = $venta_f['data']['venta']['serie_y_numero_comprobante'];
    $tipo_comprobante     = $venta_f['data']['venta']['tipo_comprobante'];
    $nombre_comprobante   = $venta_f['data']['venta']['tipo_comprobante'] == '12' ? 'NOTA DE VENTA' : ( $venta_f['data']['venta']['tipo_comprobante'] == '07' ? 'NOTA DE CRÉDITO' : $venta_f['data']['venta']['nombre_comprobante']);

    $venta_subtotal       = number_format( floatval($venta_f['data']['venta']['venta_subtotal']), 2, '.', ',' );
    $venta_subtotal_no_dcto = number_format( (floatval($venta_f['data']['venta']['venta_subtotal']) + floatval($venta_f['data']['venta']['venta_descuento'])), 2, '.', ',' );
    $venta_descuento      = number_format( floatval($venta_f['data']['venta']['venta_descuento']), 2, '.', ',' );
    $venta_igv            = number_format( floatval($venta_f['data']['venta']['venta_igv']), 2, '.', ',' );
    $venta_total          = number_format( floatval($venta_f['data']['venta']['venta_total']), 2, '.', ',' );
    $impuesto             = floatval($venta_f['data']['venta']['impuesto']). " %";
    $total_recibido       = number_format( floatval($venta_f['data']['venta']['total_recibido']), 2, '.', ',' );
    $total_vuelto         = number_format( floatval($venta_f['data']['venta']['total_vuelto']), 2, '.', ',' );

    $gravada              = "0.00";
    $exonerado            = number_format( floatval($venta_f['data']['venta']['venta_subtotal']), 2, '.', ',' );  

    $observacion_documento=  empty($venta_f['data']['venta']['observacion_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['observacion_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['observacion_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $sunat_hash           =  empty($venta_f['data']['venta']['sunat_hash']) ? '' : mb_convert_encoding($venta_f['data']['venta']['sunat_hash'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['sunat_hash'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
    $cot_tiempo_entrega   =  empty($venta_f['data']['venta']['cot_tiempo_entrega']) ? '' : mb_convert_encoding($venta_f['data']['venta']['cot_tiempo_entrega'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cot_tiempo_entrega'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $cot_validez          =  empty($venta_f['data']['venta']['cot_validez']) ? '' : mb_convert_encoding($venta_f['data']['venta']['cot_validez'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cot_validez'], "UTF-8, ISO-8859-1, ISO-8859-15", true));


    // detalle x producto ================================================================================
    $html_venta = ''; $cont = 1; $cantidad = 0;
    
    foreach ($venta_f['data']['detalle'] as $key => $val) {      
    
      $html_venta .= '<tr class="item-list">'.       
        '<td style="text-align: center; padding-left: 8px; font-size: 10px;">' .  floatval($val['cantidad'])  . '</td>' .
        '<td style="text-align: center; padding-left: 8px; font-size: 10px;">' .  $val['um_nombre_a']  . '</td>' .
        '<td style="padding: 0.5rem; text-align: left; font-size: 10px; word-break: break-all;">' . ($val['codigo']) . '</td>' .
        '<td style="padding: 0.5rem; text-align: left;  font-size: 10px; overflow-wrap: break-word;">' . ($val['pr_nombre']) . '</td>' .
        '<td style="padding: 0.5rem; text-align: right; font-size: 10px;">' .     number_format( floatval($val['precio_venta']) , 2, '.', ',') . '</td>' .
        '<td style="padding: 0.5rem; text-align: right; font-size: 10px;">' .     number_format( floatval($val['descuento']) , 2, '.', ',') . '</td>' .
        '<td style="text-align: right; padding-right: 8px; font-size: 10px;">' .  number_format( floatval($val['subtotal_no_descuento']) , 2, '.', ',') . '</td>' .
        '</tr>';
      $cantidad += floatval($val['cantidad']);
    }

    // Generar QR ================================================================================
    // ===== Datos y ruta =====
    $dataTxt = $e_numero_documento . "|" . 6 . "|" . $serie_comprobante . "|" . $numero_comprobante . "|0.00|" . $venta_total . "|" . $fecha_emision_format . "|" . $c_tipo_documento_name . "|" . $c_numero_documento . "|";

    $filename = $serie_y_numero_comprobante . '.png';
    $dir = __DIR__ . '/generador-qr/ticket';
    if (!is_dir($dir)) { mkdir($dir, 0775, true); }

    // ===== Crear QR (API v6) =====
    $qrCode = new QrCode(
        data: $dataTxt,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::Low,
        size: 600,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        foregroundColor: new Color(0, 0, 0),
        backgroundColor: new Color(255, 255, 255)
    );

    // Etiqueta (API v6 usa constructor, no ::create)
    $label = new Label(
        text: $serie_y_numero_comprobante,
        textColor: new Color(255, 0, 0)
    );

    // Escribir PNG y guardar
    $writer = new PngWriter();
    $result = $writer->write($qrCode, null, $label);

    $result->saveToFile($dir . '/' . $filename);
    $logoQr = $result->getDataUri(); // para <img src="...">

    //NUMERO A LETRA ================================================================================       
    $total_en_letra = $numero_a_letra->toInvoice( floatval($venta_f['data']['venta']['venta_total']) , 2, " SOLES" );     

    // NOMBRE DE COMPROBANTE ================================================================================    
    $nombre_archivo_pdf = $nombre_comprobante .'-'. $c_numero_documento .'-'.  $serie_y_numero_comprobante;

    ?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title> <?php echo $nombre_archivo_pdf;?> </title>
  
  <style>
    @page { size: A4; }

    * {
      -webkit-print-color-adjust: exact !important;/* Chrome, Safari */        
      color-adjust: exact !important;/*Firefox*/        
    }

    .flex { display: flex; }
    .header-primary {align-items: start; justify-content: center;  }
    .image-logo {     
      height: 320px; 
      width: 100%;
      background-repeat: no-repeat; 
      /* background-position: center;  */
      background-size: contain; 
      /* filter: grayscale(100%); */
    }
    .image-logo-container {
      height: 120px; width: 100%;
      /* background-color: #a5a5a5 !important; 
      border: 1px solid gray; */
      border-radius: 5px;
      text-align: center;
    }

    .document-header {
      width: 70%;
      /* border: 1px solid gray; */
      /* padding: 0em 1em 0 1em; */
      text-align: center;
      border-radius: 7px;
      /* background-color: #dedede; */
      -webkit-print-color-adjust: exact;
      line-height: 0.5;
      align-self: stretch;
      /* padding-top: 17px; */
      font-size: 15px;
    }

    .document-header-content {       
        padding-top: 17px;        
      }

    .table-products thead th { padding: 5px 8px;  }
    .table-products tbody tr:nth-of-type(odd) {  background-color: #F4F4F5; -webkit-print-color-adjust: exact; }
    .table-products { border-collapse: collapse; width: 100%; font-size: 12px; }
    .table-footer { width: 40%; float: right; font-size: 12px; padding: 8px; }
    .text-nowrap { white-space: nowrap !important; }

    .lds-spinner {
      display: block;
      margin: auto;
      width: 24px;
      height: 24px;
    }

    /* min-width = como minimo ─|──────── */
    @media (min-width: 992px) {

      .justify-a4-documento{ display: flex;  justify-content: center;  align-items: start; }

    }

    /* max-width = como maximo ────────| */
    @media (max-width:991.98px) {
      .justify-a4-btn{ display: flex;  justify-content: center;  align-items: start; }
    }

    @media print {
      @page { size: A4; }

      * {
        -webkit-print-color-adjust: exact !important;/* Chrome, Safari */        
        color-adjust: exact !important;/*Firefox*/        
      }
      .tm_hide_print {  display: none !important;  }
      .flex { display: flex; }
      .header-primary {align-items: start; justify-content: center;  }
      .image-logo {     
        height: 320px; 
        width: 100%;
        /* background-repeat: no-repeat;  */
        background-position: center; background-size: contain; 
        filter: grayscale(100%);
      }
      .image-logo-container {
        height: 120px; width: 100%;
        /* background-color: #a5a5a5 !important; 
        border: 1px solid gray; */
        border-radius: 5px;
        text-align: center;
      }

      .document-header {
        width: 70%;
        /* border: 1px solid gray; */
        /* padding: 0em 1em 0 1em; */
        text-align: center;
        border-radius: 7px;
        /* background-color: #dedede; */
        -webkit-print-color-adjust: exact;
        line-height: 0.5;
        align-self: stretch;
        /* padding-top: 17px; */
        font-size: 15px;
      }

      .document-header-content {       
        padding-top: 17px;        
      }

      .table-products thead th { padding: 5px 8px;  }
      .table-products tbody tr:nth-of-type(odd) {  background-color: #F4F4F5; -webkit-print-color-adjust: exact; }
      .table-products { border-collapse: collapse; width: 100%; font-size: 12px; }
      .table-footer { width: 40%; float: right; font-size: 12px; padding: 8px; }
    }
  </style>
</head>

<body class="justify-a4-documento" style="background-color: white !important; "><!---->
  <div class="tm_hide_print justify-a4-btn">    
    
    <button type="button" style="margin-bottom: 5px;">
      <a  type="button" class="btn btn-outline-info p-1 mb-2 m-l-5px w-40px" href="javascript:window.print()" data-bs-toggle="tooltip" title="Imprimir Ticket">
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#08a62f"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
      </a>
    </button>
    <br>
    <button type="button" style="margin-bottom: 5px; cursor: pointer;" class="btn btn-warning p-1 mb-2 m-l-5px w-40px" id="btn-descargar" data-bs-toggle="tooltip" title="Descargar imagen" onclick="descargar_imagen();" style="cursor: pointer;">
      <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#c76a00"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v6.5" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.653 -.629 1.413 -.815 2.13 -.559" /><path d="M19 16v6" /><path d="M22 19l-3 3l-3 -3" /></svg>
    </button>  
    <br>  
    <button type="button" style="margin-bottom: 5px; cursor: pointer;" class="btn btn-outline-danger p-1 mb-2 m-l-5px w-40px" id="btn-compartir" data-bs-toggle="tooltip" title="Compartir Imagen" onclick="compartir_imagen();">
      <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#014cbc"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-share"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M8.7 10.7l6.6 -3.4" /><path d="M8.7 13.3l6.6 3.4" /></svg>
    </button>  
    <br>  
    <button type="button" style="margin-bottom: 5px; cursor: pointer;" class="btn btn-outline-danger p-1 mb-2 m-l-5px w-40px" id="btn-descargar-pdf" data-bs-toggle="tooltip" title="Descargar PDF" onclick="descargar_pdf();">      
      <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#990000"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>
    </button>  
  </div>   

  <div id="iframe-img-descarga" style="background-color: #f1f1f1; font-family: sans-serif; font-size: 12px; width: 780px !important; border-radius: 5px;">
    <div style="padding: 10px;"> 
    
      <div class="flex header-primary" style="margin-bottom: 15px;">
        
        <div style="width: 30%; padding-left: 0px; padding-right: 10px;">         
          <div class="image-logo-container"> 
             <div class="image-logo" style="background-image: url(&quot;<?php echo $logo_empresa;?>&quot;);"></div>
          </div>
        </div>
        
        <div class="document-header">
          <div class="document-header-content ">
            <table style="width: 100%; padding: 0px; font-size: 12px;">
              <tbody>
                <tr>
                  <td style="width: 60%; padding-left: 15px; padding-right: 15px;" >
                    <strong style="margin-bottom: 0px;">Oficina: </strong> <?php echo $e_domicilio_fiscal;?> <br>
                    <strong style="margin-bottom: 0px;">Teléfono: </strong> <?php echo $e_telefono1 .' - ' . $e_telefono2;?> <br>
                    <strong style="margin-bottom: 0px;">Correo: </strong> <?php echo $e_correo;?> <br>
                  </td>
                  <td>
                    <strong style="margin-bottom: 0px; font-size: 35px; color: #1554a0;"> PROFORMA </strong>
                    <div style="text-align: center;" ><strong style="display: block; font-size: 25px;"> <?php echo $serie_y_numero_comprobante;?></strong></div> 
                    <p style="font-size: 15px;">R.U.C. N° <?php echo $e_numero_documento;?></p>
                    <strong>Fecha:</strong> <?php echo $fecha_emision_dmy;?>
                  </td>
                </tr>                
              </tbody>
            </table>            
            
          </div>
        </div>
      </div>
      <div style="margin-top: 4em; text-align: center;">
        <strong style="font-size: 20px; color: #1554a0;"><?php echo $e_comercial;?></strong>
      </div>
      <div style="margin-top: 1rem; padding-right: 2rem; padding-left: 2rem;" >
        <div style="display: inline-block; vertical-align: top; width: 45%; border: 3px solid #8080804d; border-radius: 2px; margin-right: 16px; margin-left: 16px;">
          <table style="width: 100%; padding-bottom: 15px; font-size: 12px;">
            <tbody>
              <tr>
                <td><strong style="margin-bottom: 0px;">EMISOR:</strong> <?php echo $e_razon_social;?></td>
              </tr>               
              <tr><td><b>RUC:</b> <?php echo $e_numero_documento;?></td></tr>
              <tr><td><b>E-MAIL:</b> <?php echo $e_correo;?></td></tr>
              <tr><td style="padding: 10px;"></td></tr>
              <tr><td></td></tr>
            </tbody>
          </table>
        </div>
        <div style="display: inline-block; vertical-align: top; width: 45%; border: 3px solid #8080804d; border-radius: 2px; margin-bottom: 15px;">
          <table style="width: 100%; padding: 0.5em; font-size: 12px;">
            <tbody>              
              <tr>
                <td width="30%"><strong>CLIENTE:</strong></td> <td style="width: 1rem; text-align: right;">:</td> <td> <?php echo $c_nombre_completo;?> </td>
              </tr>
              <tr>
                <td width="30%"><strong><?php echo $c_tipo_documento_name;?></strong></td> <td style="width: 1rem; text-align: right;">:</td> <td><span><?php echo $c_numero_documento;?></span></td>
              </tr>
              <tr>
                <td width="30%"><strong>E-MAIL:</strong></td> <td style="width: 1rem; text-align: right;">:</td> <td> <?php echo $c_correo;?></td>
              </tr>  
              <tr>
                <td width="30%"><strong>DIRECC:</strong></td> <td style="width: 1rem; text-align: right;">:</td> <td> <?php echo $c_direccion;?></td>
              </tr>             
              <tr>
                <td width="30%"><strong>TELEFONO:</strong></td> <td style="width: 1rem; text-align: right;">:</td> <td> <?php echo $c_celular;?></td>
              </tr>               
            </tbody>
          </table>
        </div>
      </div>
      <div style="padding-right: 2rem; padding-left: 2rem;">      
        <div style="border: 3px solid #8080804d; border-radius: 2px;">
          <table role="grid" class="table-products" style="table-layout: fixed;">
            <thead>
              <tr role="row">
                <th role="columnheader" style="text-align: center; width: 25px;">Cant.</th>
                <th role="columnheader" style="text-align: center; width: 60px;">Unidad</th>
                <th role="columnheader" style="text-align: left; width: 50px;"> Código </th>
                <th role="columnheader" style="text-align: left; width: auto;">Descripción</th>
                <th role="columnheader" style="text-align: right; width: 50px;">P.U.</th> 
                <th role="columnheader" style="text-align: right; width: 50px;">Dcto.</th> 
                <th role="columnheader" style="text-align: right; width: 80px;"> Total  </th>
              </tr>
            </thead>
            <tbody  style=" border-top: 3px solid #8080804d;">
              
              <?php echo $html_venta;?>

              <tr class="item-list">                
                <td colspan="6" style="padding: 5px; text-align: center;"> <strong>TOTAL</strong>  </td>                
                <td style="padding: 5px; text-align: right; padding-right: 8px; font-size: 12px;"> <strong><?php echo $venta_total;?></strong>  
              </td>

              <tr class="item-list">                
                <td colspan="7" style="font-size: 12px; padding: 5px;"> <strong>IMPORTE EN LETRAS: <?php echo $total_en_letra;?></strong>   </td>                
                
              </td>

              <!-- <tr class="item-list">
                <td style="text-align: center; padding-left: 8px; font-size: 10px;"> 1.00 </td>
                <td style="padding: 0.5rem; text-align: center; font-size: 10px;"> UNIDAD </td>
                <td style="padding: 0.5rem; text-align: left; font-size: 10px; word-break: break-all;"> PIURA &nbsp;  </td>
                <td style="padding: 0.5rem; text-align: left; min-width: 200px; font-size: 10px; overflow-wrap: break-word;">
                  FACTURACION POR SERVICIO DE CORRESPONSALI CORRESPONDIENTE AL MES DE ABRIL 2024 <br></td>
                <td style="padding: 0.5rem; text-align: right; font-size: 10px;"> 136.40</td> 
                <td style="text-align: right; padding-right: 8px; font-size: 10px;"> 136.40 </td>
              </tr> -->
            </tbody>
          </table>
          
        </div>
      </div>
      <div style="padding-right: 2rem; padding-left: 2rem;">

      
        <div style="margin-top: 15px;">
          <div style="border: 1px solid gray; padding: 7px; font-size: 12px; border-radius: 7px;">
            <strong>OBSERVACIONES</strong>: <br> <span style="white-space: pre-wrap;"><?php echo $observacion_documento;?></span><br>
          </div> <!---->
          <div style="border: 1px solid gray; padding: 7px; font-size: 12px; border-radius: 7px; margin-top: 15px;">
            <div>
              <strong>FORMA DE PAGO: </strong> <span>Contado</span> <br>              
              <strong><span>TIEMPO DE ENTREGA:</strong> <span><?php echo $cot_tiempo_entrega;?></span> <br>
              <strong>VALIDEZ:</strong> <span><?php echo $cot_validez;?></span>
              
            </div> <!---->
          </div> <!---->
          <div style="text-align: center; font-size: 11px; margin-top: 15px;">
            Representación impresa de la <b style=" text-transform: lowercase;"><?php echo $nombre_comprobante;?></b>  electrónica. Consulte su documento en <strong> <?php echo $e_web;?> </strong>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Popper JS -->
  <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- DomToImg JS -->
  <script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script>

  <!-- JsPdf -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

  <script>

    const mi_btn_compartir = document.getElementById("btn-compartir");
    const mi_btn_descargar = document.getElementById("btn-descargar");
    const mi_btn_descargar_pdf = document.getElementById("btn-descargar-pdf");
    const spinnerSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-spinner" style="background: none;"><circle cx="50" cy="50" fill="none" stroke="#000" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(180 50 50)"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform></circle></svg>`;    
    const sharedSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2"><circle cx="18" cy="5" r="3"></circle> <circle cx="6" cy="12" r="3"></circle> <circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line> <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>`;      
    const imgenSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`;              
    const pdfSVG = `<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#990000"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>`;              

    var titulo = document.title; // Obtener el título de la página

    // ══════════════════════════════════════ DESCARGAR FORMATO IMAGEN ══════════════════════════════════════

    function descargar_imagen() {       

      mi_btn_descargar.innerHTML = spinnerSVG; // Agregar el SVG del spinner al div
      setTimeout(function() {
        extraccion_de_datos_imagen();
      }, 2000); // 3000 milisegundos = 3 segundos          
    }

    function extraccion_de_datos_imagen() {
      domtoimage.toJpeg(document.getElementById('iframe-img-descarga'), { quality: 0.95 }).then(function (dataUrl) {
        var link = document.createElement('a');
        link.download = `${titulo}.jpeg`;
        link.href = dataUrl;
        link.click();
        mi_btn_descargar.innerHTML = imgenSVG; // Agregar el SVG del spinner al div
      });
    }

    // ══════════════════════════════════════ COMPARTIR FORMATO IMAGEN ══════════════════════════════════════
    function compartir_imagen() {                           
      mi_btn_compartir.innerHTML = spinnerSVG; // Agregar el SVG del spinner al div
      setTimeout(function() {
        extraccion_de_datos_compartir();
      }, 2000); // 3000 milisegundos = 3 segundos
    }

    function extraccion_de_datos_compartir() {      

      const fileName = "comprobante.png";          

      domtoimage.toBlob(document.getElementById('iframe-img-descarga'), { quality: 0.95 }).then(function (dataBlob) {           
        
        const blob = dataBlob;
        const file = new File([blob], fileName, { type: blob.type });

        // Verificar si la API de Web Share es compatible y compartir la imagen
        if (navigator.canShare && navigator.canShare({ files: [file] })) {
          navigator.share({
            title: `Comprobante: ${titulo}`,
            text: 'Guarda este comprobante en un lugar seguro.',
            files: [file]
          }).then(() => {
            console.log('Compartido exitosamente');
            mi_btn_compartir.innerHTML = sharedSVG; // Agregar el SVG del spinner al div
          }).catch((error) => {
            console.error('Error al compartir:', error);
            mi_btn_compartir.innerHTML = sharedSVG; // Agregar el SVG del spinner al div
          });
        } else {
          alert('La API de compartir no es soportada en este navegador.');
          mi_btn_compartir.innerHTML = sharedSVG; // Agregar el SVG del spinner al div
        }
      });
    }

    // ══════════════════════════════════════ DESCARGAR FORMATO PDF ══════════════════════════════════════
    function descargar_pdf() {       
      mi_btn_descargar_pdf.innerHTML = spinnerSVG; // Agregar el SVG del spinner al div
      setTimeout(function() {
        extraccion_de_datos_pdf();
      }, 2000); // 3000 milisegundos = 3 segundos          
    }

    function extraccion_de_datos_pdf() {
      domtoimage.toBlob(document.getElementById('iframe-img-descarga'), { quality: 0.95 }).then(function (dataBlob) {           
        
        const blob = dataBlob;
        const reader = new FileReader();

        reader.onloadend = function() {
          const { jsPDF } = window.jspdf;
          const pdf = new jsPDF();         
          
          // Crear una imagen para obtener las dimensiones originales
          const img = new Image();
          img.src = reader.result;

          img.onload = function() {
              
            const imgWidth = img.width;                                     // Obtener dimensiones originales de la imagen
            const imgHeight = img.height;                                   // Obtener dimensiones originales de la imagen

            // Crear un nuevo documento PDF con las dimensiones de la imagen
            const pdf = new jsPDF({ orientation: imgWidth > imgHeight ? 'landscape' : 'portrait', unit: 'px', format: [imgWidth +20, imgHeight +20] });
            
            const x = (pdf.internal.pageSize.getWidth() - imgWidth) / 2;    // Calcular las coordenadas para centrar la imagen
            const y = (pdf.internal.pageSize.getHeight() - imgHeight) / 2;  // Calcular las coordenadas para centrar la imagen
            
            pdf.addImage(reader.result, 'PNG', x, y, imgWidth, imgHeight);  // Añadir la imagen al PDF            
            pdf.save(`${titulo}.pdf`);                                      // Descargar el PDF
          };                                     
        };
        
        reader.readAsDataURL(dataBlob);                                     // Leer el Blob como una URL de datos
        mi_btn_descargar_pdf.innerHTML = pdfSVG;                            // Agregar el SVG del spinner al div
      });
    }   

  </script>
</body>

</html>

<?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>