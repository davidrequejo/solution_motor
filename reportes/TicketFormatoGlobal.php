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
    $metodo_pago_f    = $facturacion->datos_metodo_pago_venta($_GET["id"]);   

    if ( empty($venta_f['data']['venta']) ) { echo "Comprobante no existe"; die();  }

    $logo_empresa = "../assets/images/brand-logos/desktop-white.png";          

    // Emrpesa emisora ================================================================================
    $e_razon_social       = empty($empresa_f['data']['nombre_razon_social']) ? "" : mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_razon_social'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_comercial          = empty($empresa_f['data']['nombre_comercial']) ? "" : mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_comercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_domicilio_fiscal   = empty($empresa_f['data']['domicilio_fiscal']) ? "" : mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'UTF-8', mb_detect_encoding($empresa_f['data']['domicilio_fiscal'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
    $e_numero_documento   = $empresa_f['data']['numero_documento'];
    $e_telefono1          = $empresa_f['data']['telefono1'];
    $e_telefono2          = $empresa_f['data']['telefono2'];
    $e_correo             = empty($empresa_f['data']['correo']) ? "" : mb_convert_encoding($empresa_f['data']['correo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['correo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web                = empty($empresa_f['data']['web']) ? "" : mb_convert_encoding($empresa_f['data']['web'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web_consulta_cp    = empty($empresa_f['data']['web_consulta_cp']) ? "" : mb_convert_encoding($empresa_f['data']['web_consulta_cp'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web_consulta_cp'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_logo_empresa       = empty($empresa_f['data']['logo']) ? $logo_empresa : '../assets/modulo/empresa/logo/' . $empresa_f['data']['logo'];

    $e_distrito           = empty($empresa_f['data']['distrito']) ? "" : mb_convert_encoding($empresa_f['data']['distrito'], 'UTF-8', mb_detect_encoding($empresa_f['data']['distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_provincia          = empty($empresa_f['data']['provincia']) ? "" : mb_convert_encoding($empresa_f['data']['provincia'], 'UTF-8', mb_detect_encoding($empresa_f['data']['provincia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_departamento       = empty($empresa_f['data']['departamento']) ? "" : mb_convert_encoding($empresa_f['data']['departamento'], 'UTF-8', mb_detect_encoding($empresa_f['data']['departamento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_codubigueo         = empty($empresa_f['data']['codubigueo']) ? "" : mb_convert_encoding($empresa_f['data']['codubigueo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['codubigueo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    // Cliente receptor ================================================================================
    $c_nombre_completo    = empty($venta_f['data']['venta']['cliente_nombre_completo']) ? "" : mb_convert_encoding($venta_f['data']['venta']['cliente_nombre_completo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cliente_nombre_completo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_tipo_documento     = $venta_f['data']['venta']['tipo_documento'];
    $c_tipo_documento_name= $venta_f['data']['venta']['tipo_documento_abreviatura'];
    $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
    $c_direccion          = empty($venta_f['data']['venta']['direccion']) ? "" : mb_convert_encoding($venta_f['data']['venta']['direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $c_nc_serie_y_numero  = empty($venta_f['data']['venta']['nc_serie_y_numero']) ? "" : mb_convert_encoding($venta_f['data']['venta']['nc_serie_y_numero'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_serie_y_numero'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_nc_nombre_motivo   = empty($venta_f['data']['venta']['nc_nombre_motivo']) ? "" : mb_convert_encoding($venta_f['data']['venta']['nc_nombre_motivo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_nombre_motivo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
    $c_landing_user       = empty($venta_f['data']['venta']['landing_user']) ? "" : mb_convert_encoding($venta_f['data']['venta']['landing_user'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['landing_user'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_idventa_v2         = $venta_f['data']['venta']['idventa_v2'];
    
    // Data comprobante ================================================================================

    $user_en_atencion     = mb_convert_encoding($venta_f['data']['venta']['user_en_atencion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['user_en_atencion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));   

    $fecha_emision        = $venta_f['data']['venta']['fecha_emision'];
    $fecha_emision_format = $venta_f['data']['venta']['fecha_emision_format'];
    $fecha_emision_dmy    = $venta_f['data']['venta']['fecha_emision_dmy'];
    $fecha_emision_hora12 = $venta_f['data']['venta']['fecha_emision_hora12'];
    $serie_comprobante    = $venta_f['data']['venta']['serie_comprobante'];
    $numero_comprobante   = $venta_f['data']['venta']['numero_comprobante'];
    $serie_y_numero_comprobante   = $venta_f['data']['venta']['serie_y_numero_comprobante'];
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
    $sunat_hash           = mb_convert_encoding($venta_f['data']['venta']['sunat_hash'] ?? '', 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['sunat_hash'] ?? '', "UTF-8, ISO-8859-1, ISO-8859-15", true) ?: 'UTF-8');

    $venta_cuotas         = $venta_f['data']['venta']['venta_cuotas'];
    $html_cuotas = '';
    foreach ($venta_f['data']['venta_cuotas'] as $key => $val) {
      $html_cuotas .= '<tr><td > '.$val['numero_cuota'].' </td>   <td>'.$val['fecha_vencimiento_format'].'</td> <td style="text-align: right;"> '. number_format(floatval($val['monto_cuota']) , 2, '.', ',').'</td> </tr>';
    }

    // detalle x producto ================================================================================
    $html_venta = ''; $cont = 1; $cantidad = 0;
    
    foreach ($venta_f['data']['detalle'] as $key => $val) {      
    
      $html_venta .= '<tr >'.
        '<td colspan="4">' . ($val['pr_nombre'] ) . '</td>' .
      '</tr>
      <tr >'.       
        '<td>' . floatval($val['cantidad_total'])  . '</td>' .
        '<td ></td>' .
        '<td style="text-align: right;">' . number_format( floatval($val['precio_venta']) , 2, '.', ',') . '</td>' .
        '<td style="text-align: right;">' . number_format( floatval($val['subtotal_no_descuento']) , 2, '.', ',') . '</td>' .
      '</tr>';
      $cantidad += floatval($val['cantidad_total']);
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

    ?>
    <html>

    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <title><?php echo $nombre_comprobante .' - '. $serie_y_numero_comprobante ; ?></title>
      
      <!-- Bootstrap Css -->
      <link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

      <!-- Style Css -->
      <link href="../assets/css/styles.min.css" rel="stylesheet">
      <link href="../assets/css/style_new.css" rel="stylesheet">

      <!-- Style tiket -->      
      <style> 
        @media print {  .tm_hide_print {  display: none !important;  }  }
        .lds-spinner {
          display: block;
          margin: auto;
          width: 24px;
          height: 24px;
        }        
      </style>
    </head>

    <body style="background-color: white; display: flex;  justify-content: center;  align-items: center;">
      
      <div class="d-block align-items-center justify-content-between tm_hide_print">
        <a  type="button" class="btn btn-outline-success p-1 mb-2 m-l-5px w-40px" href="javascript:window.print()" data-bs-toggle="tooltip" title="Imprimir Ticket">          
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
        </a>

        <button type="button" class="btn btn-outline-warning p-1 mb-2 m-l-5px w-40px" id="btn-descargar" data-bs-toggle="tooltip" title="Descargar Imagen" onclick="descargar_imagen();">          
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v6.5" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.653 -.629 1.413 -.815 2.13 -.559" /><path d="M19 16v6" /><path d="M22 19l-3 3l-3 -3" /></svg>
        </button>      

        <button type="button" class="btn btn-outline-info p-1 mb-2 m-l-5px w-40px" id="btn-compartir" data-bs-toggle="tooltip" title="Compartir Imagen" onclick="compartir_imagen();">         
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-share"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M8.7 10.7l6.6 -3.4" /><path d="M8.7 13.3l6.6 3.4" /></svg>
        </button>    
        
        <button type="button" class="btn btn-outline-danger p-1 mb-2 m-l-5px w-40px" id="btn-descargar-pdf" data-bs-toggle="tooltip" title="Compartir Imagen" onclick="descargar_pdf();">          
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>
        </button>   
      </div>    

      <!-- codigo imprimir -->
      <div id="iframe-img-descarga" style="background-color: #f0f1f7; border-radius: 5px;" >      
        
        <br>
        <!-- Detalle de empresa -->
        <table class="mx-3" border="0" align="center" width="270px">
          <tbody>
            <tr><td align="center"><img src="<?php echo $e_logo_empresa; ?>" width="<?php echo ($empresa_f['data']['logo_c_r'] == 0 ? 150 : 100);?>"></td></tr>
            <tr align="center"><td style="font-size: 14px">.::<strong> <?php echo $e_comercial; ?> </strong>::.</td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_razon_social; ?> </td></tr>
            <tr align="center"><td style="font-size: 14px"> <strong> R.U.C. <?php echo $e_numero_documento; ?> </strong> </td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_domicilio_fiscal . ' <br> ' . $e_telefono1 . "-" . $e_telefono2; ?> </td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_correo; ?> </td></tr>            
            <tr><td style="text-align: center;"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
            <tr><td align="center"> <strong style="font-size: 14px"> <?php echo $nombre_comprobante ; ?> ELECTRÓNICA </strong> <br> <b style="font-size: 14px"><?php echo $serie_y_numero_comprobante ; ?> </b></td></tr>
            <tr><td style="text-align: center;"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          </tbody>
        </table>

        <!-- Datos cliente -->
        <table border="0" align="center" width="270px" style="font-size: 12px">
          <tbody>
            <tr ><td>             <strong>Emisión:</strong> <?php echo $fecha_emision_dmy ; ?> </td> <td><strong>Hora:</strong> <?php echo $fecha_emision_hora12 ; ?> </td></tr>
            <tr ><td colspan="2"><strong>Cliente:</strong> <?php echo $c_nombre_completo ; ?> </td> </tr>
            <tr ><td colspan="2"><strong>DNI/RUC:</strong> <?php echo $c_numero_documento ; ?></td> </tr>
            <tr ><td colspan="2"><strong>Dir.:</strong> <?php echo $c_direccion ; ?></td></tr> 
            <?php if( $venta_f['data']['venta']['tipo_comprobante'] == '07' ) { ?>
            <tr ><td colspan="2"><strong>Doc. Baja:</strong> <?php echo $c_nc_serie_y_numero; ?> </td></tr>
            <?php }?>
            <tr ><td colspan="2"><strong>Atención:</strong> <?php echo $user_en_atencion; ?> </td> </tr>            
            <tr ><td colspan="2"><strong>Observación:</strong> <?php echo $observacion_documento ; ?> </td></tr>
          </tbody>
        </table>         

        <!-- Mostramos los detalles de la venta en el documento HTML -->
        <table border="0" align="center" width="270px" style="font-size: 12px !important;">
          <thead>
            <tr><td colspan="4"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td> </tr>
            <tr><th>Cant.</th> <th>Descripción</th> <th>P.U.</th> <th>Importe</th></tr>
            <tr><td colspan="4"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          </thead>        
          <tbody style="font-size: 11px !important;">
            <?php  echo $html_venta;  ?>
          </tbody>        
        </table>      
        
        <!-- Division -->
        <table border='0'  align='center' width='270px' style='font-size: 12px' >
          <tr><td><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          <tr></tr>
        </table>
        
        <!-- Detalles de totales sunat -->
        <table border='0'  align="center" width='270px' style='font-size: 12px'>
          <tr><td style="text-align: right;"><strong>Subtotal </strong></td>      <td>:</td> <td style="text-align: right;"> <?php echo $venta_subtotal_no_dcto; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>Descuento </strong></td>     <td>:</td> <td style="text-align: right;"> <?php echo $venta_descuento; ?> </td></tr>
          <!-- <tr><td style="text-align: right;"><strong>Op. Gravada </strong></td>   <td>:</td> <td style="text-align: right;"> <?php echo $gravada; ?> </td></tr> -->
          <tr><td style="text-align: right;"><strong>Op. Exonerado </strong></td> <td>:</td> <td style="text-align: right;"> <?php echo $exonerado; ?> </td></tr>
          <!-- <tr><td style="text-align: right;"><strong>Op. Inafecto </strong></td>  <td>:</td> <td style="text-align: right;"> 0.00</td></tr> -->
          <!-- <tr><td style="text-align: right;"><strong>ICBPER</strong></td>         <td>:</td> <td style="text-align: right;"> 0.00 </td></tr> -->
          <tr><td style="text-align: right;"><strong>IGV (<?php echo $impuesto; ?>)</strong></td>         <td>:</td> <td style="text-align: right;"> <?php echo $venta_igv; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>TOTAL</strong></td>          <td>:</td> <td style="text-align: right;"><strong> <?php echo $venta_total ?> </strong></td></tr>          
        </table>      

        <!-- Mostramos los totales de la venta en el documento HTML -->
        <table border='0' align="center" width='270px' style='font-size: 12px' >                
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          <tr><td colspan="3"><b>Son: </b> <?php echo $total_en_letra; ?> </td></tr>
          <?php if( $venta_f['data']['venta']['tipo_comprobante'] == '07' ) { ?>
           <!-- No nmostramos metodos de pagos -->
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          <tr><td > <b>MOTIVO</b></td>          <td>:</td> <td> <?php echo $c_nc_nombre_motivo; ?> </td></tr>  
          <?php }else{?>          
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          <?php foreach ($metodo_pago_f['data'] as $key => $val) { ?>
          <tr><td > <b><?php echo $val['metodo_pago']; ?></b></td>   <td>:</td> <td> <?php echo $val['monto']; ?> </td></tr>         
          <?php if($val['metodo_pago'] != 'EFECTIVO'){?>
          <tr><td > <b> Nro. Baucher </b></td>  <td>:</td> <td> <?php echo $val['codigo_voucher']; ?></td> </tr>
          <?php }?>
          <tr><td colspan="3"><div style=" margin-bottom: 5px;" ></div></td></tr>
          <?php }?>
          <tr><td > <b>VUELTO</b></td>          <td>:</td> <td> <?php echo $total_vuelto; ?> </td></tr>  
          <?php }?>           
        </table>   
        
        <?php if( $venta_cuotas  == 'SI'){ ?>
        <table border='0' align="center" width='270px' style='font-size: 12px' >          
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>   
          <tr><th > # Cuota</th>         <th>Fecha</th> <th> Monto </th> </tr>
          <?php echo $html_cuotas;?>
        </table> 
        <?php } ?>

        <table border='0' align="center" width='270px' style='font-size: 12px' >          
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>   
          <tr><td> <b>Nro. Operación</b> </td><td>:</td> <td> <?php echo $c_idventa_v2; ?></td> </tr>
          <tr><td> <b>Codigo Usuario</b> </td><td>:</td> <td> <?php echo $c_landing_user; ?></td> </tr>             
        </table>   

        <table border='0' align="center" width='270px' style='font-size: 12px'>
          <tbody>
            <tr>
              <td>
                <img src=<?php echo $logoQr; ?> width="100" height="100"><br>
                
              </td>
              <td style="font-size: 11px;">
                <span>Autorizado mediante resolución Nro: 182-2016/SUNAT Representación impresa del comprobante de venta electrónico, puede ser consultada en:</span>
                <span class="fw-bold"><b><?php echo $e_web; ?></b></span>
                <span style="font-size: 10px; margin-top: 5px;">Hash:  <?php echo $sunat_hash; ?>  </span>
              </td>
            </tr>
            <tr>
              <td style="text-align: center;" colspan="2" ></td>              
            </tr>
            <tr>
              <td style='font-size: 10px; text-align: center;' colspan="2"> <span><strong>SERVICIOS TRANSFERIDOS EN LA REGIÓN AMAZÓNICA SELVA PARA SER CONSUMIDOS EN LA MISMA.</strong></span></td>                            
            </tr>
            <tr ><td style="font-size: 10px; text-align: center;" colspan="2" > <?php echo $e_web; ?> </td></tr>
          </tbody>            
          <br>          
        </table>
        <p>&nbsp;</p>
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
              
              const img = new Image();        // Crear una imagen para obtener las dimensiones originales
              img.src = reader.result;

              img.onload = function() {
                
                const imgWidth = img.width;   // Obtener dimensiones originales de la imagen
                const imgHeight = img.height; // Obtener dimensiones originales de la imagen

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