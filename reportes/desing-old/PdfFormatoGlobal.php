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
use Dompdf\Dompdf;
use Dompdf\Options;
use Melihovv\Base64ImageDecoder\Base64ImageEncoder;


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

    $logo_empresa = "../assets/images/brand-logos/logo1.png";      
    $encoder = Base64ImageEncoder::fromFileName($logo_empresa, $allowedFormats = ['jpeg', 'png', 'gif']);    
    $encoder->getMimeType(); // image/jpeg for instance
    $encoder->getContent(); // base64 encoded image bytes.
    $logo_empresa_b64 = $encoder->getDataUri(); // a base64 data-uri to use in HTML or CSS attributes.

    // Emrpesa emisora ================================================================================
    $e_razon_social       = mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_razon_social'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_comercial          = mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_comercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_domicilio_fiscal   = mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'UTF-8', mb_detect_encoding($empresa_f['data']['domicilio_fiscal'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
    $e_numero_documento   = $empresa_f['data']['numero_documento'];
    $e_telefono1          = $empresa_f['data']['telefono1'];
    $e_telefono2          = $empresa_f['data']['telefono2'];
    $e_correo             = mb_convert_encoding($empresa_f['data']['correo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['correo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web                = mb_convert_encoding($empresa_f['data']['web'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web_consulta_cp    = mb_convert_encoding($empresa_f['data']['web_consulta_cp'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web_consulta_cp'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $e_distrito           = mb_convert_encoding($empresa_f['data']['distrito'], 'UTF-8', mb_detect_encoding($empresa_f['data']['distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_provincia          = mb_convert_encoding($empresa_f['data']['provincia'], 'UTF-8', mb_detect_encoding($empresa_f['data']['provincia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_departamento       = mb_convert_encoding($empresa_f['data']['departamento'], 'UTF-8', mb_detect_encoding($empresa_f['data']['departamento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_codubigueo         = mb_convert_encoding($empresa_f['data']['codubigueo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['codubigueo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    // Cliente receptor ================================================================================
    $c_nombre_completo    = mb_convert_encoding($venta_f['data']['venta']['cliente_nombre_completo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cliente_nombre_completo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_tipo_documento     = $venta_f['data']['venta']['tipo_documento'];
    $c_tipo_documento_name= $venta_f['data']['venta']['nombre_tipo_documento'];
    $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
    $c_direccion          = mb_convert_encoding($venta_f['data']['venta']['direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
    // Data comprobante ================================================================================
    $metodo_pago          = mb_convert_encoding($venta_f['data']['venta']['metodo_pago'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['metodo_pago'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $mp_serie_comprobante = $venta_f['data']['venta']['mp_serie_comprobante'] == null || $venta_f['data']['venta']['mp_serie_comprobante'] == '' ? '-': mb_convert_encoding($venta_f['data']['venta']['mp_serie_comprobante'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['mp_serie_comprobante'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

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

    $observacion_documento= mb_convert_encoding($venta_f['data']['venta']['observacion_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['observacion_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $sunat_hash           = mb_convert_encoding($venta_f['data']['venta']['sunat_hash'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['sunat_hash'], "UTF-8, ISO-8859-1, ISO-8859-15", true));


    // detalle x producto ================================================================================
    $html_venta = ''; $cont = 1; $cantidad = 0;
    
    foreach ($venta_f['data']['detalle'] as $key => $val) {      
    
      $html_venta .= '<tr class="border_top">'.       
       '<td align="center">' . floatval($val['cantidad'])  . '</td>' .
       '<td >' . ($val['codigo']) . '</td>' .
       '<td >' . ($val['nombre_producto']) . '</td>' .
       '<td style="text-align: right;">' . number_format( floatval($val['precio_venta']) , 2, '.', ',') . '</td>' .
       '<td style="text-align: right;">' . number_format( floatval($val['subtotal_no_descuento']) , 2, '.', ',') . '</td>' .
       '</tr>';
      $cantidad += floatval($val['cantidad']);
    }

    // Generar QR ================================================================================
    
    $dataTxt = $e_numero_documento . "|" . 6 . "|" . $serie_comprobante . "|" . $numero_comprobante . "|0.00|" . $venta_total . "|" . $fecha_emision_format . "|" . $c_tipo_documento_name . "|" . $c_numero_documento . "|";

    $filename = $serie_y_numero_comprobante . '.png';
    $qr_code = QrCode::create($dataTxt)->setEncoding(new Encoding('UTF-8'))->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)->setSize(600)->setMargin(10)->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));

    $label = Label::create( $serie_y_numero_comprobante)->setTextColor(new Color(255, 0, 0)); // Create generic label  
    $writer = new PngWriter(); // Create IMG
    $result = $writer->write($qr_code, label: $label); 
    $result->saveToFile(__DIR__.'/generador-qr/ticket/'.$filename); // Save it to a file  
    $logoQr = $result->getDataUri();// Generate a data URI

    //NUMERO A LETRA ================================================================================       
    $total_en_letra = $numero_a_letra->toInvoice( floatval($venta_f['data']['venta']['venta_total']) , 2, " SOLES" );     

    // NOMBRE DE COMPROBANTE ================================================================================    
    $nombre_archivo_pdf = $nombre_comprobante .'-'. $c_numero_documento .'-'.  $serie_y_numero_comprobante.'.pdf';

    ?>
    
    <?php 
      
      $dompdf  = new Dompdf();     
      
      $content = file_get_contents('A4FormatHtml.html');                           # Contenido HTML a convertir en PDF
      
      $content = str_replace('%logo_empresa%', $logo_empresa_b64, $content);      #
      $content = str_replace('%nombre_archivo%', $nombre_archivo_pdf, $content);  #   
      $content = str_replace('%tbody_producto%', $html_venta, $content);          #

      $dompdf->loadHtml($content);                                                # Cargar el contenido HTML en Dompdf       
      $dompdf->render();                                                          # Renderizar el contenido HTML como PDF

      // Establecer las cabeceras del PDF
      header('Content-type: application/pdf');
      header('Content-Disposition: inline; filename="'.$nombre_archivo_pdf.'"');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: ' . strlen($dompdf->output()));

      // Enviar el PDF al navegador
      echo $dompdf->output();
    ?>

    <?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>