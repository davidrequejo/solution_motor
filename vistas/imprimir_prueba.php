<?php
  require '../vendor/autoload.php';  // Asegúrate de que Composer esté cargado

  use Dompdf\Dompdf;
  use Dompdf\Options;

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

  // Crear las opciones de configuración para Dompdf
  $options = new Options();
  $options->set('isHtml5ParserEnabled', true);
  $options->set('isPhpEnabled', true);

  // Crear una nueva instancia de Dompdf
  $dompdf = new Dompdf($options);

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
    
  // Aquí va tu diseño HTML, puedes incluir tu código HTML para la boleta
  $html = '
  <html>
      <head>
          <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
          <title>NOTA DE VENTA - TK001-44</title>       
          
          <!-- Style tiket -->   
          <style>
          body {            
            font-family: Arial, sans-serif !important; /* Cambiar a Arial */            
          }
          </style>     
      </head>
      <body style="background-color: white;  justify-content: center;  align-items: center;">
          
          <!-- codigo imprimir -->
          <div id="iframe-img-descarga" >
              <br>
              <!-- Detalle de empresa -->
              <table class="mx-3" border="0" align="center" width="230px">
                  <tbody>
                      <tr>
                          <td align="center">
                              <img src="https://libreria_sistema.test/assets/modulo/empresa/logo/22_03_2025__06_35_07_PM__17174268650839.png" width="150">
                          </td>
                      </tr>
                      <tr align="center">
                          <td style="font-size: 14px">
                              .::<strong>solution motors </strong>
                              ::.
                          </td>
                      </tr>
                      <tr align="center">
                          <td style="font-size: 10px">GRUPO NOVEDADES D & S S.A.C. </td>
                      </tr>
                      <tr align="center">
                          <td style="font-size: 14px">
                              <strong>R.U.C. 20611208694 </strong>
                          </td>
                      </tr>
                      <tr align="center">
                          <td style="font-size: 10px">
                              JR. ALONSO DE ALVARADO NRO. 415 SAN MARTIN SAN MARTIN TARAPOTO <br>999999999-042555555 
                          </td>
                      </tr>
                      <tr align="center">
                          <td style="font-size: 10px">novedadesdys@gmail.com </td>
                      </tr>
                      <tr>
                          <td style="text-align: center;">
                              <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                          </td>
                      </tr>
                      <tr>
                          <td align="center">
                              <strong style="font-size: 14px">NOTA DE VENTA ELECTRÓNICA </strong>
                              <br>
                              <b style="font-size: 14px">TK001-44 </b>
                          </td>
                      </tr>
                      <tr>
                          <td style="text-align: center;">
                              <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                          </td>
                      </tr>
                  </tbody>
              </table>
              <!-- Datos cliente -->
              <table border="0" align="center" width="230px" style="font-size: 12px">
                  <tbody>
                      <tr>
                          <td>
                              <strong>Emisión:</strong>
                              07/07/2025 
                          </td>
                          <td>
                              <strong>Hora:</strong>
                              11:52:22 PM 
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2">
                              <strong>Cliente:</strong>
                              CLIENTES VARIOS 
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2">
                              <strong>DNI/RUC:</strong>
                              00000000
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2">
                              <strong>Dir.:</strong>
                              TOCACHE S/N
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2">
                              <strong>Atención:</strong>
                              JDL JDL 
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2">
                              <strong>Observación:</strong>
                          </td>
                      </tr>
                  </tbody>
              </table>
              <!-- Mostramos los detalles de la venta en el documento HTML -->
              <table border="0" align="center" width="230px" style="font-size: 12px !important;">
                  <thead>
                      <tr>
                          <td colspan="4">
                              <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                          </td>
                      </tr>
                      <tr>
                          <th>Cant.</th>
                          <th>Descripción</th>
                          <th>P.U.</th>
                          <th>Importe</th>
                      </tr>
                      <tr>
                          <td colspan="4">
                              <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                          </td>
                      </tr>
                  </thead>
                  <tbody style="font-size: 11px !important;">
                      <tr>
                          <td colspan="4">BOLSA DE REGALO BEYBY SHAHUER  M - UNIDADADES</td>
                      </tr>
                      <tr>
                          <td>1</td>
                          <td></td>
                          <td style="text-align: right;">20.00</td>
                          <td style="text-align: right;">20.00</td>
                      </tr>
                  </tbody>
              </table>
              <!-- Division -->
              <table border="0" align="center" width="230px" style="font-size: 12px">
                  <tr>
                      <td>
                          <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                      </td>
                  </tr>
                  <tr></tr>
              </table>
              <!-- Detalles de totales sunat -->
              <table border="0" align="center" width="230px" style="font-size: 12px">
                  <tr>
                      <td style="text-align: right;">
                          <strong>Subtotal </strong>
                      </td>
                      <td>:</td>
                      <td style="text-align: right;">20.00 </td>
                  </tr>
                  <tr>
                      <td style="text-align: right;">
                          <strong>Descuento </strong>
                      </td>
                      <td>:</td>
                      <td style="text-align: right;">0.00 </td>
                  </tr>
                  <!-- <tr><td style="text-align: right;"><strong>Op. Gravada </strong></td>   <td>:</td> <td style="text-align: right;"> 0.00 </td></tr> -->
                  <tr>
                      <td style="text-align: right;">
                          <strong>Op. Exonerado </strong>
                      </td>
                      <td>:</td>
                      <td style="text-align: right;">20.00 </td>
                  </tr>
                  <!-- <tr><td style="text-align: right;"><strong>Op. Inafecto </strong></td>  <td>:</td> <td style="text-align: right;"> 0.00</td></tr> -->
                  <!-- <tr><td style="text-align: right;"><strong>ICBPER</strong></td>         <td>:</td> <td style="text-align: right;"> 0.00 </td></tr> -->
                  <tr>
                      <td style="text-align: right;">
                          <strong>IGV (0 %)</strong>
                      </td>
                      <td>:</td>
                      <td style="text-align: right;">0.00 </td>
                  </tr>
                  <tr>
                      <td style="text-align: right;">
                          <strong>TOTAL</strong>
                      </td>
                      <td>:</td>
                      <td style="text-align: right;">
                          <strong>20.00 </strong>
                      </td>
                  </tr>
              </table>
              <!-- Mostramos los totales de la venta en el documento HTML -->
              <table border="0" align="center" width="230px" style="font-size: 12px">
                  <tr>
                      <td colspan="3">
                          <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                      </td>
                  </tr>
                  <tr>
                      <td colspan="3">
                          <b>Son: </b>
                          VEINTE CON 00/100  SOLES 
                      </td>
                  </tr>
                  <tr>
                      <td colspan="3">
                          <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <b>EFECTIVO</b>
                      </td>
                      <td>:</td>
                      <td>20.00 </td>
                  </tr>
                  <tr>
                      <td colspan="3">
                          <div style=" margin-bottom: 5px;"></div>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <b>VUELTO</b>
                      </td>
                      <td>:</td>
                      <td>0.00 </td>
                  </tr>
              </table>
              <table border="0" align="center" width="230px" style="font-size: 12px">
                  <tr>
                      <td colspan="3">
                          <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <b>Nro. Operación</b>
                      </td>
                      <td>:</td>
                      <td>00098</td>
                  </tr>
                  <tr>
                      <td>
                          <b>Codigo Usuario</b>
                      </td>
                      <td>:</td>
                      <td></td>
                  </tr>
              </table>
              <table border="0" align="center" width="230px" style="font-size: 12px">
                  <tbody>
                      <tr>
                          <td>
                              <img src=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAmwAAAKGCAIAAAB4KFCWAAAACXBIWXMAAA7EAAAOxAGVKw4bAAASmklEQVR4nO3dW4hdVx3H8f8JB0yrtrQGKUGh9VKpChYpokXEC0qpYOehaDCiedBSvICUPvShzUOUUBREgnhptYiEUEuFKIJQokYUlVCr9kn6oKmoREiCSCpSjduHjM5MHJszv5lZZ82ez4c+JGf2mb322mvmy56UWZNhGAoAWLsd8x4AAGxVIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJAaDrjcZPJZFPHwf/TcoeA7C7bw2A5c7hcy+8bY/1K8b13Xma8X55EASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBIDTd1M/ecq/5/rXcob7luTL9j7Dl6u1/NjJj/Q7Q/3X1P8KWNvXry5MoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhKbzHsAqNnUX8g3R/67x2QizmW95v1rOfHZd/a+NTMu73P93gEz/a6P/me9wDj2JAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQGg67wGwKVruUJ/tNd9yhJmW15WdK9NyhP2vw0z/q5dmPIkCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJAaDrvAbAphmFodq7JZBK8Kxthy3OxXDbz/bOiWCdPogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABCaznsAq7Br/PpNJpPgXf3PfDbC/mcjG2FL/c98yznsf0Vl+h9hhzyJAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQGi6qZ+95V7zbAfZihqGwbnWea6xGusc9j/C0fAkCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKAKFJtrE7zMVkMgnelS3ylufqX/+zkY2wpbGujW3OkygAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACEJjNutt7/rvEtZTvUm8PlWs5hdq5MyxH2PxuZll8p/X8t9782xnquGXkSBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgNB03gPYMJu6d/mGaDnCbP/3TMvr6v8ut2Q2lut/Nvr/Ssm+b2zzc3kSBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgNB03gPYkrJ90ltquf97ZqxzmGk5G/1fV8sRZlp+ffU/Gy2vq8M59CQKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAoem8B7Al9b/XfEvZXvOZljOfXVf/I2x5rmw2Wl4Xy1m9y804G55EASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBIDTZ1K3MN3U/8S13rkz/e833f79aGut1ZcY6G/1/B2g5wpY6XBueRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASA0nfG4se6T3lLLHeqzc/U/wpbnGqv+57D/Efa/ovof4Wia4kkUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQpMZN0Dvf6/5lkazJ/tF3K+tpeX9Gut3gJbX1f8ctvxK6X9tzMiTKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAISmMx5nJ/flxjrC/ve1bznCTP9ro39jva7+10b/I2xpxtnwJAoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgCh6aZ+9myf9JY7ntvJfbmWs9FSy3WYvavlzI91zfd/Xf1/P8yM9S7PyJMoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhKbzHsCW1HJf+0zLEbY8V8s5bDkbmf7v11hZG/M6V2ZTr8uTKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIQmM27e3XLH8/6NdYf6zFhHmBnrdWV839harN6AJ1EACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAITUazvbg92efFzM9Ly5nP9H+/+p/DjJlfvxnn0JMoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhCabugF6tne5PdnXL5vDsV5XS/2v+f5HmGl5Xf3PYf8jHA1PogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABCazLiVef+7xvdvrLORXVem5Wz0f7/6n/mWc9hyNli/0awoT6IAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQmsy4eXf/e82PdYSZlvu/Z8z8cv2v3sxYr2usxvqVkplxhJ5EASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBIDSd9wC2pGx39Wwn90zL/d9b6nBf+znq/y5nc9j/dY2V71EBT6IAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQms57AKsYzY7nF2l5XXaoXy6bjf6vK9NybbRkza9fdl0tZ77D1etJFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAELTGY/b5nuXX6T/Efa/Q31mrOuw5f0a69roX/9zmK2Nbc6TKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIQmM262bsfzeZnxBl3E/Voum0PWL1uH/d+vltfV/7ky/c/GjDyJAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgAhEQWAkIgCQGi6qZ+9/x3qWxrrrvEttVxRY50N63Br6f+7aMsRtlxRM57LkygAhEQUAEIiCgAhEQWAkIgCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACEpvMewCr639e+/73mN3Un9w0x1rvccub7v8sttZzDTP9rvqXRrF5PogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABCaznsAdKTlXvPZu7IRttTyurJzZfofYUtjva7+dbgOPYkCQEhEASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJAaDrvAbDltdxrPntXNsKWsutqqeXM93+XW15XxppvxpMoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhKbzHsAqRrPj+TbR8n5NJpNm58quq/8RZrLr8rU8L/2v3pbnysw4h55EASAkogAQElEACIkoAIREFABCIgoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBIDTd1M/e/97lrF92l2fcNX5DZOeyepdreb9aznzLtdH/imp5lzMdfrfxJAoAIREFgJCIAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSgAhEQUAEIiCgChSf9bmQNAnzyJAkBIRAEgJKIAEBJRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAIiSj8j29+syaTS/z3xz8uHnzddSv++l+33VaTSd1334oXf/azeuc768or60Uvqve9r373u1XOPssxTz9dX/pS3XBDTSb17W8n1/i1ry1eyJkz6zoGtrfpvAcA/XnJS+r225f++vjjdfJk3XRTXXvt0ouXX/5cn+G+++o736lbb61PfWrpxe9/v265pXbsqLe+tc6dq0cfrWPH6sSJevnL13bMgw/WHXes6wL//Oe6++4NOAYYgOf24Q8PVcPXv776R6+9dqga/vCHpVceeWSoGq6/fvjLX1Ycef31w3Q6HDu2+NdDh4aq4b3vXfMxTz01HD48HD48vPGNQ9Vw9Oiar2jPnuHyy4ebbx6qhtOn82Ng2/PjXNhQv/517dtXV1xRR4/WlVcuvf6jH9VTT9X731/veMfiK5/4RN14Yx09uvTD0lmOqapXvrL27q29e1c8Gc/ue9+rhx+uj3+8rr56XccA/k0UNtKZM7WwUH//e33jG3XDDSs+dORIVdXCwooX3/OeevbZevTRNRyzTs88Ux/9aO3aVffcU3/9a34MUFX+TRQ2zD/+UbffXidP1v79ddttF3/0t7+tqrrpphUvvuENSx+a8Zh12r+/Tp6sw4frqqvq2WfzY4Cq8iQKG+aTn6zjx6uq3vKWVT566lRV1a5dK1688NcLH5rxmPX4xS/q0KG69dbau3ddxwD/IaKwER54oL74xcXg3XVXnT9/8QGnTtXOnXXZZStevPAvjn/60xqOiZ0/X3fcUTt31pe/vK5jgGVEFDbCgQN1xRV1/Hi9/e315JP10EOrHPOvf63+3h071nZM5vOfryeeqPvvr5e+dF3HAMv4N1HYCDt21JEj9ZrX1Oc+V69/fd17b+3ZUy984dIBu3fX6dP1zDP1/OcvvXj6dFXVNdes4ZhLOn++7r9/xSvvele9+MW1f3+9+c31sY/93zc+/fSljwFWElHYCAcP1rvfXVX1utfVvn310EN14EB99rNLB1xzTT35ZJ09uyKQZ89WVe3evYZjLumf/6x7713xygteUL//ff3tb/WTn9RkcvHxF34E/Zvf1AMPXPqYV71q1mHA9iCisBE+8IGlP3/60/XII3XoUN1559JvGnrFK+qxx+rEiRU/Kf35z6uqXvayNRxzSc97Xg3DxS9+61ur/KD44Yfr1Km6887aubOuvrpuvvnSxwAXmfdve4DurfU3Fg3DcODAUDUsLCy98tOfDlXDBz+44rAbbxx27hzOnl3DMcvt2RP+xqILLvzCo+f+bUSzHAPbmP+xCDbB3XfX7t119Gj98IeLr7zpTfXqV9eRI/XjHy++8pWv1K9+VQsLddVVazgG6Ikf58ImuOyyOniw9u2ru+6qX/5y8cUvfKFuuWXxv3Pn6gc/qF276uDBFW+85DFnztQ99yz++cSJxbd897tVVXv2LP2+QKAJEYXN8aEP1aFD9cQT9eCD9ZGPVFW97W11/Hjt31+PPVbTaS0s1Gc+U9ddt+Jdlzzm3Ln66ldXvOXYscU/vPa1IgqNTYb//X8QAIAZ+DdRAAiJKACERBQAQiIKACERBYCQiAJASEQBICSiABASUQAI/Rt8dovopK7ydQAAAABJRU5ErkJggg== width="100" height="100">
                              <br>
                          </td>
                          <td style="font-size: 11px;">
                              <span>Autorizado mediante resolución Nro: 182-2016/SUNAT Representación impresa del comprobante de venta electrónico, puede ser consultada en:</span>
                              <span class="fw-bold">
                                  <b>https://novedadesdys.jdl.pe</b>
                              </span>
                              <span style="font-size: 10px; margin-top: 5px;">Hash:    </span>
                          </td>
                      </tr>
                      <tr>
                          <td style="text-align: center;" colspan="2"></td>
                      </tr>
                      <tr>
                          <td style="font-size: 10px; text-align: center;" colspan="2">
                              <span>
                                  <strong>SERVICIOS TRANSFERIDOS EN LA REGIÓN AMAZÓNICA SELVA PARA SER CONSUMIDOS EN LA MISMA.</strong>
                              </span>
                          </td>
                      </tr>
                      <tr>
                          <td style="font-size: 10px; text-align: center;" colspan="2">https://novedadesdys.jdl.pe </td>
                      </tr>
                  </tbody>
                  <br>
              </table>
              <p>&nbsp;</p>
          </div>
          
      </body>
  </html>


  ';

  // Cargar el HTML al PDF
  $dompdf->loadHtml($html);

  // Configurar el tamaño de papel para impresora térmica (80mm x 297mm)
  $dompdf->setPaper([0, 0, 240, 994], 'portrait');  // Ancho 80mm, Alto 297mm (en milímetros)

  // Renderizar el PDF
  $dompdf->render();

  // Guardar el PDF en el servidor o mostrarlo directamente en el navegador
  $dompdf->stream("comprobante.pdf", array("Attachment" => false));  // Cambia a `true` si quieres forzar la descarga del PDF
?>
