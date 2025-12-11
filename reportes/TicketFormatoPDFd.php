<?php
  require '../vendor/autoload.php';  // Asegúrate de que Composer esté cargado



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


 require_once "../modelos/Facturacion.php";                                        // Incluímos la clase Venta
    
  $facturacion    = new Facturacion();                                              // Instanciamos a la clase con el objeto venta
  $numero_a_letra = new NumeroALetras();                                            // Instanciamos a la clase con el objeto venta

  if (!isset($_GET["id"])) { echo "Datos incompletos (indefinido)"; die(); }        // Validamos la existencia de la variable
  if (empty($_GET["id"])) {  echo "Datos incompletos (".$_GET["id"].")"; die(); }   // validamos el valor de la variable
  
  $empresa_f        = $facturacion->datos_empresa();    
  $venta_f          = $facturacion->mostrar_detalle_venta($_GET["id"]);   
  $metodo_pago_f    = $facturacion->datos_metodo_pago_venta($_GET["id"]);   

  if ( empty($venta_f['data']['venta']) ) { echo "Comprobante no existe"; die();  }

  $url_logo_empresa = "../assets/images/brand-logos/desktop-white.png";        
  $data_logo_empresa = file_get_contents($url_logo_empresa);    
  $base64_logo_empresa = base64_encode($data_logo_empresa);// Convierte los datos de la imagen en Base64  
  $base64_src_logo_empresa = 'data:image/jpeg;base64,' . $base64_logo_empresa;  // Crea la URL en formato data URI


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
  $html_venta = ''; $cont = 1; $cantidad = 0; $tamanio_hoja = 0;
  
  foreach ($venta_f['data']['detalle'] as $key => $val) {      
  
    $html_venta .= '<tr >'.
      '<td colspan="4" style="font-size: 11px">' . ($val['pr_nombre'] ) . '</td>' .
    '</tr>
    <tr >'.       
      '<td>' . floatval($val['cantidad_total'])  . '</td>' .
      '<td ></td>' .
      '<td style="text-align: right;">' . number_format( floatval($val['precio_venta']) , 2, '.', ',') . '</td>' .
      '<td style="text-align: right;">' . number_format( floatval($val['subtotal_no_descuento']) , 2, '.', ',') . '</td>' .
    '</tr>';
    $cantidad += floatval($val['cantidad_total']);
    $tamanio_hoja += 50;
  }    

  $html_metodo_pago = '';
  foreach ($metodo_pago_f['data'] as $key => $val) { 
    $html_metodo_pago .= '<tr><td > <b>'. $val['metodo_pago'].'</b></td>   <td>:</td> <td>'. $val['monto'].'</td></tr>';         
    if($val['metodo_pago'] != 'EFECTIVO'){
      $html_metodo_pago .= '<tr><td > <b> Nro. Baucher </b></td>  <td>:</td> <td>'.$val['codigo_voucher'].'</td> </tr>';
    }
    // $html_metodo_pago .= '<tr><td colspan="3"><div style=" margin-bottom: 5px;" ></div></td></tr>';
    $tamanio_hoja += 20;
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
    
  // Aquí va tu diseño HTML, puedes incluir tu código HTML para la boleta
  $html = '
  <html>
      <head>
          <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
          <title>'.$nombre_comprobante .' - '. $serie_y_numero_comprobante.'</title>       
          
          <!-- Style tiket -->   
          <style>
          body {            
            font-family: Arial, sans-serif !important; /* Cambiar a Arial */            
          }
          </style>     
      </head>
      <body style="background-color: white;">
          
          <!-- codigo imprimir -->
          <div id="iframe-img-descarga" >              
              <!-- Detalle de empresa -->
              <table class="mx-3" border="0" align="center" width="250px">
                  <tbody>
                      <tr><td align="center"><img src="'.$base64_src_logo_empresa.'" width="150"></td></tr>
                      <tr align="center"><td style="font-size: 14px">.::<strong>'.$e_comercial.'</strong>::.</td></tr>
                      <tr align="center"><td style="font-size: 10px">'.$e_razon_social.'</td></tr>
                      <tr align="center"><td style="font-size: 14px"><strong>R.U.C. '.$e_numero_documento.' </strong></td></tr>
                      <tr align="center"><td style="font-size: 10px">'.$e_domicilio_fiscal . ' <br> ' . $e_telefono1 . "-" . $e_telefono2.'</td></tr>
                      <!-- <tr align="center"><td style="font-size: 10px">'.$e_correo.'</td></tr> -->
                      <tr>
                        <td style="text-align: center;">
                          <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 2px;"></div>
                        </td>
                      </tr>
                      <tr>
                        <td align="center">
                          <strong style="font-size: 14px">'.$nombre_comprobante.' ELECTRÓNICA</strong><br><b style="font-size: 14px">'.$serie_y_numero_comprobante.'</b>
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">
                          <div style="border-bottom: 1px dotted black; margin-top: 2px; margin-bottom: 8px;"></div>
                        </td>
                      </tr>
                  </tbody>
              </table>
              <!-- Datos cliente -->
              <table border="0" align="center" width="250px" style="font-size: 12px">
                  <tbody>
                      <tr>
                        <td><strong>Emisión:</strong>'.$fecha_emision_dmy.'</td>
                        <td><strong>Hora:</strong>'.$fecha_emision_hora12.'</td>
                      </tr>
                      <tr>
                        <td colspan="2"><strong>Cliente: </strong>'.$c_nombre_completo.'</td>
                      </tr>
                      <tr>
                        <td colspan="2"><strong>DNI/RUC: </strong>'.$c_numero_documento.'</td>
                      </tr>
                      <tr>
                        <td colspan="2"><strong>Dir.: </strong>'.$c_direccion.'</td>
                      </tr>'.
                      ( 
                        $venta_f['data']['venta']['tipo_comprobante'] == '07' ? 
                        '<tr>
                          <td colspan="2"><strong>Doc. Baja: </strong>'.$c_nc_serie_y_numero.'</td>
                        </tr>' 
                        : 
                        ''
                      ).
                      '<tr>
                        <td colspan="2"><strong>Atención: </strong>'.$user_en_atencion.'</td>
                      </tr>
                      <tr>
                        <td colspan="2"><strong>Observación: '.$observacion_documento.'</strong></td>
                      </tr>
                  </tbody>
              </table>
              <!-- Mostramos los detalles de la venta en el documento HTML -->
              <table border="0" align="center" width="250px" style="font-size: 12px !important;">
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
                      '. $html_venta .'
                  </tbody>
              </table>
              <!-- Division -->
              <table border="0" align="center" width="250px" style="font-size: 12px">
                  <tr>
                      <td>
                          <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                      </td>
                  </tr>
                  <tr></tr>
              </table>
              <!-- Detalles de totales sunat -->
              <table border="0" align="center" width="250px" style="font-size: 12px">
                <tr>
                    <td style="text-align: right;"> <strong>Subtotal </strong>  </td>  <td>:</td> <td style="text-align: right;">'.$venta_subtotal_no_dcto.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;"> <strong>Descuento </strong> </td> <td>:</td> <td style="text-align: right;">'.$venta_descuento.'</td>
                </tr>
                <!-- <tr><td style="text-align: right;"><strong>Op. Gravada </strong></td>   <td>:</td> <td style="text-align: right;"> '.$gravada.' </td></tr> -->
                <tr>
                    <td style="text-align: right;"> <strong>Op. Exonerado </strong></td> <td>:</td> <td style="text-align: right;">'.$exonerado.'</td>
                </tr>
                <!-- <tr><td style="text-align: right;"><strong>Op. Inafecto </strong></td>  <td>:</td> <td style="text-align: right;"> 0.00</td></tr> -->
                <!-- <tr><td style="text-align: right;"><strong>ICBPER</strong></td>         <td>:</td> <td style="text-align: right;"> 0.00 </td></tr> -->
                <tr>
                    <td style="text-align: right;"> <strong>IGV ('.$impuesto.' %)</strong> </td><td>:</td><td style="text-align: right;">'.$venta_igv.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>TOTAL</strong></td> <td>:</td> <td style="text-align: right;"> <strong>'.$venta_total.'</strong> </td>
                </tr>
              </table>
              <!-- Mostramos los totales de la venta en el documento HTML -->
              <table border="0" align="center" width="250px" style="font-size: 12px">
                <tr> <td colspan="3"> <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div> </td> </tr>
                <tr><td colspan="3"> <b>Son: </b> '.$total_en_letra.' </td> </tr>'.
                (
                  $venta_f['data']['venta']['tipo_comprobante'] == '07' ? 
                  '<tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
                  <tr><td > <b>MOTIVO</b></td> <td>:</td> <td>'.$c_nc_nombre_motivo.'</td></tr> ' : 
                  '<tr><td colspan="3"> <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div> </td> </tr>
                  '.$html_metodo_pago.'                  
                  <tr><td><b>VUELTO</b></td><td>:</td><td>'.$total_vuelto.'</td></tr>'
                ).                  
              '</table>
              <table border="0" align="center" width="250px" style="font-size: 12px">
                  <tr>
                    <td colspan="3">
                      <div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;"></div>
                    </td>
                  </tr>
                  <tr>
                    <td><b>Nro. Operación</b></td><td>:</td><td>'.$c_idventa_v2.'</td>
                  </tr>
                  <tr>
                    <td><b>Codigo Usuario</b></td><td>:</td><td>'.$c_landing_user.'</td>
                  </tr>
              </table>
              <table border="0" align="center" width="250px" style="font-size: 12px">
                  <tbody>
                      <tr>
                          <td>
                              <img src="'.$logoQr.'" width="100" height="100">
                              <br>
                          </td>
                          <td style="font-size: 9px;">
                              <span>Autorizado mediante resolución Nro: 182-2016/SUNAT Representación impresa del comprobante de venta electrónico, puede ser consultada en:</span>
                              <span class="fw-bold">
                                  <b>https://novedadesdys.jdl.pe</b>
                              </span>
                              <span style="font-size: 10px; margin-top: 5px;">Hash: '.$sunat_hash.'</span>
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
                          <td style="font-size: 10px; text-align: center;" colspan="2">'.$e_web.'</td>
                      </tr>
                  </tbody>
                  <br>
              </table>              
          </div>
          
      </body>
  </html>


  ';

  echo $html;
?>
