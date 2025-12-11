
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

    $html_venta = ''; $cont = 1;

    if ( empty($venta_f['data']['venta']) ) { echo "Comprobante no existe"; die();  }

    // $logo_empresa = "../files\logo\\" . $empresa_f['data']['logo'];
    $logo_empresa = "../assets/images/brand-logos/logo1.png";

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
    $c_nc_serie_y_numero  = mb_convert_encoding($venta_f['data']['venta']['nc_serie_y_numero'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_serie_y_numero'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
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


    foreach ($venta_f['data']['detalle'] as $key => $reg) {
      $html_venta .= '<tr>
        <td class="px-1 celda-b-r-1px text-center" >'.$cont++.'</td>
        <td class="px-1 celda-b-r-1px text-center" >'.$reg['codigo'].'</td>
        <td class="px-1 celda-b-r-1px text-align">'.$reg['nombre_producto'].'</td>
        <td class="px-1 celda-b-r-1px text-center" >'.$reg['um_abreviatura'].'</td>
        <td class="px-1 celda-b-r-1px text-center" >'.$reg['cantidad'].'</td>
        <td class="px-1 celda-b-r-1px text-right" >'.number_format( floatval($reg['precio_venta']) , 2, '.',',').'</td>
        <td class="px-1 celda-b-r-1px text-right" >'.number_format(floatval($reg['descuento']) , 2, '.',',').'</td>
        <td class="px-1 celda-b-r-1px text-right">'.number_format( floatval($reg['subtotal']) , 2, '.',',').'</td>
      </tr>';
    }

    // Generar QR ================================================================================
      
    $dataTxt = $empresa_f['data']['numero_documento'] . "|" . 6 . "|" . $venta_f['data']['venta']['serie_comprobante'] . "|" . 
    $venta_f['data']['venta']['numero_comprobante'] . "|0.00|" . $venta_f['data']['venta']['venta_total'] . "|" . $venta_f['data']['venta']['fecha_emision_format'] . "|" . 
    $venta_f['data']['venta']['nombre_tipo_documento'] . "|" . $venta_f['data']['venta']['numero_documento'] . "|";

    $filename = $venta_f['data']['venta']['serie_y_numero_comprobante'] . '.png';
    $qr_code = QrCode::create($dataTxt)->setEncoding(new Encoding('UTF-8'))->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)->setSize(600)->setMargin(10)->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));

    $label = Label::create( $venta_f['data']['venta']['serie_y_numero_comprobante'])->setTextColor(new Color(255, 0, 0)); // Create generic label  
    $writer = new PngWriter(); // Create IMG
    $result = $writer->write($qr_code, label: $label); 
    $result->saveToFile(__DIR__.'/generador-qr/ticket/'.$filename); // Save it to a file  
    $logoQr = $result->getDataUri();// Generate a data URI

    //NUMERO A LETRA ================================================================================
    $venta_total = $venta_f['data']['venta']['venta_total'];
    $total_en_letra = $numero_a_letra->toInvoice( $venta_total , 2, " SOLES" );  
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>

  <!-- Meta Data -->
  <meta charset="UTF-8">
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $venta_f['data']['venta']['nombre_comprobante'] .' - '. $venta_f['data']['venta']['serie_y_numero_comprobante'] ; ?></title>
  <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
  <meta name="Author" content="Spruko Technologies Private Limited">
  <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

  <!-- Bootstrap Css -->
  <link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Style Css -->
  <link href="../assets/css/styles.min.css" rel="stylesheet">
  <!-- Style propio -->
  <link rel="stylesheet" href="../assets/css/style_new.css">

  <!-- Style -->      
  <style> @media print {  .tm_hide_print {  display: none !important;  }  } </style>

</head>

<body style="background-color: white !important; display: flex;  justify-content: center;  align-items: center;">
  <div class="d-block align-items-center justify-content-between tm_hide_print">
    <a  type="button" class="btn btn-outline-info p-1 mb-2 m-l-5px w-40px" href="javascript:window.print()" data-bs-toggle="tooltip" title="Imprimir Ticket">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer">
        <polyline points="6 9 6 2 18 2 18 9"></polyline>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
        <rect x="6" y="14" width="12" height="8"></rect>
      </svg>
    </a>

    <button type="button" class="btn btn-warning p-1 mb-2 m-l-5px w-40px" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="decargar_imagen();">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
    </button>      
  </div>      
  <!-- End Switcher -->
  <div class="container-lg" >
    <div class="row justify-content-center" id="iframe-img-descarga">
      <div class="row gy-4 justify-content-center">
        <div class="col-xl-9">
          <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-6 col-sm-6">     
              <div class="d-flex flex-fill flex-wrap gap-4">
                <div class="avatar avatar-xl" style="width: 120px !important;" ><img src="<?php echo $logo_empresa; ?>" alt="" style="width: 100px; height: auto;"></div>
                <div>
                  <h6 class="mb-1 fw-semibold"><?php echo mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'ISO-8859-1', 'UTF-8'); ?></h6>                  
                  <div class="fs-10 mb-0 "><?php echo mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'ISO-8859-1', 'UTF-8'); ?></div>
                  <div class="fs-10 mb-0 text-muted contact-mail text-truncate"><?php echo mb_convert_encoding($empresa_f['data']['correo'], 'ISO-8859-1', 'UTF-8'); ?></div>
                  <div class="fs-10 mb-0 text-muted"><?php echo mb_convert_encoding($empresa_f['data']['telefono1'], 'ISO-8859-1', 'UTF-8'); ?> - <?php echo mb_convert_encoding($empresa_f['data']['telefono2'], 'ISO-8859-1', 'UTF-8'); ?></div>
                </div>
              </div>              
            </div>
            <div class="text-center col-xl-4 col-lg-4 col-md-6 col-sm-6 ms-auto mt-sm-0 mt-3">
              <div class="border border-dark">
                <div class="m-2">                  
                  <h6 class="text-muted mb-2"> RUC: <?php echo $empresa_f['data']['numero_documento']; ?> </h6>
                  <h6><?php echo $nombre_comprobante;?> ELECTRONICA</h6>
                  <h5><?php echo $venta_f['data']['venta']['serie_y_numero_comprobante']; ?></h5>
                </div>                
              </div>              
            </div>
          </div>
        </div>
        <div class="col-xl-9">
          <table class="font-size-10px">
            <tr>
              <th style="font-size: 12px;">Fecha de Emisión</th>
              <td style="font-size: 12px;">: <?php echo $venta_f['data']['venta']['fecha_emision_format']; ?></td>
            </tr>
            <tr>
              <th style="font-size: 12px;">Señor(a)</th>
              <td style="font-size: 12px;">: <?php echo $venta_f['data']['venta']['cliente_nombre_completo']; ?></td>
            </tr>
            <tr>
              <th style="font-size: 12px;">N° Documento</th>
              <td style="font-size: 12px;">: <?php echo $venta_f['data']['venta']['nombre_tipo_documento'] . ' - ' . $venta_f['data']['venta']['numero_documento']; ?></td>
            </tr>
            <tr>
              <th style="font-size: 12px;">Dirección</th>
              <td style="font-size: 12px;">: <?php echo $venta_f['data']['venta']['direccion']; ?></td>
            </tr>            
            <tr>
              <th style="font-size: 12px;">Observación</th>
              <td style="font-size: 12px;">: <?php echo $venta_f['data']['venta']['observacion_documento'] ; ?></td>
            </tr>
          </table>
        </div>
        
        <div class="col-xl-9">
          <div class="table-responsive">
            <table class="border border-dark mt-1 w-100">
              <thead class="border border-dark">
                <tr >
                  <th class="celda-b-r-1px text-center">#</th>
                  <th class="celda-b-r-1px text-center">CODIGO</th>
                  <th class="celda-b-r-1px text-center">DESCRIPTION</th>
                  <th class="celda-b-r-1px text-center">UM</th>
                  <th class="celda-b-r-1px text-center">CANT.</th>
                  <th class="celda-b-r-1px text-center">P/U</th>
                  <th class="celda-b-r-1px text-center">DCTO.</th>
                  <th class="celda-b-r-1px text-center">SUBTOTAL</th>
                </tr>
              </thead>
              <tbody >
              <?php echo $html_venta; ?>
                                       
              </tbody>
            </table>
          </div>
        </div>        

        <div class="col-xl-9">             
          
          <table  style="width: 100% !important;">            
            <tr>   
              <td class="font-size-12px">
                <span class="">SON: <b><?php echo $total_en_letra; ?> </b>  </span><br>
                <span class="text-muted">Representación impresa de la Nota de Venta Electrónica, puede ser consultada en <?php echo mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'ISO-8859-1', 'UTF-8'); ?></span>
              </td>  
              <td>
                <table class="text-nowrap w-100 table-bordered font-size-10px">
                  <tbody>
                    <tr><th class="text-center" colspan="2">CUENTAS BANCARIAS</th></tr>
                    <!-- filtramos los datos <<< SI la cuenta existe ENTONCES se muestran sus datos >>>> de lo contrario se ocultan :) ------>
                    <?php if (!empty($empresa_f['data']['cuenta1'])) : ?>
                      <tr>
                        <td class="px-1" rowspan="2"><?php echo $empresa_f['data']['banco1']; ?></td>
                        <td class="px-1">Cta: <?php echo $empresa_f['data']['cuenta1']; ?></td>                          
                      </tr>
                      <tr>                          
                        <td class="px-1">CCI: <?php echo $empresa_f['data']['cci1']; ?></td>
                      </tr>
                    <?php endif; ?>

                    <?php if (!empty($empresa_f['data']['cuenta2'])) : ?>
                      <tr>
                        <td class="px-1" rowspan="2"><?php echo $empresa_f['data']['banco2']; ?></td>
                        <td class="px-1">Cta: <?php echo $empresa_f['data']['cuenta2']; ?></td>                        
                      </tr>
                      <tr>                        
                        <td class="px-1">CCI: <?php echo $empresa_f['data']['cci2']; ?></td>
                      </tr>
                    <?php endif; ?>

                    <?php if (!empty($empresa_f['data']['cuenta3'])) : ?>
                      <tr>
                        <td class="px-1" rowspan="2"><?php echo $empresa_f['data']['banco3']; ?></td>
                        <td class="px-1">Cta: <?php echo $empresa_f['data']['cuenta3']; ?></td>                        
                      </tr>
                      <tr>                        
                        <td class="px-1">CCI: <?php echo $empresa_f['data']['cci3']; ?></td>
                      </tr>
                    <?php endif; ?>

                    <?php if (!empty($empresa_f['data']['cuenta4'])) : ?>
                      <tr>
                        <td class="px-1" rowspan="2"><?php echo $empresa_f['data']['banco4']; ?></td>
                        <td class="px-1">Cta: <?php echo $empresa_f['data']['cuenta4']; ?></td>                        
                      </tr>
                      <tr>                        
                        <td class="px-1">CCI: <?php echo $empresa_f['data']['cci4']; ?></td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </td>                 
              <td align="center"><img src=<?php echo $logoQr; ?> width="90" height="auto"></td>
              <td>
                <div class="border border-dark rounded-1">
                  <div class="m-1">
                    <table class="text-nowrap w-100">
                      <tbody>
                        <tr>
                          <td scope="row"><p class="mb-0 font-size-12px">Sub Total</p></td> <th>:</th>
                          <td align="right"><p class="mb-0 "><?php echo $venta_f['data']['venta']['venta_subtotal']; ?></p></td>
                        </tr>            
                        <tr>
                          <td scope="row"><p class="mb-0 font-size-12px">Descuento </p></td><th>:</th>
                          <td align="right"><p class="mb-0 "><?php echo $venta_f['data']['venta']['venta_descuento']; ?></p></td>
                        </tr>  
                        <tr>
                          <td scope="row"><p class="mb-0 font-size-12px">IGV <span class="text-danger">(0%)</span> </p></td> <th>:</th>
                          <td align="right"><p class="mb-0 "><?php echo $venta_f['data']['venta']['impuesto']; ?></p></td>
                        </tr>            
                        <tr>
                          <th scope="row"><p class="mb-0 fs-16">Total</p></th> <th>:</th>
                          <td align="right"><p class="mb-0 fw-semibold fs-16"><?php echo $venta_total; ?></p></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                
              </td>
            </tr>    

          </table>                         
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row .gy-3 -->
    </div>
    <!-- /.row .justify-->
  </div>  
  <!-- Popper JS -->
  <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Dropzone JS -->
  <script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script>

  <!-- <script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script> -->
  <script>
    function decargar_imagen() {
      
      var titulo = document.title; // Obtener el título de la página

      domtoimage.toJpeg(document.getElementById('iframe-img-descarga'), { quality: 0.95 }).then(function (dataUrl) {
        var link = document.createElement('a');
        link.download = `${titulo}.jpeg`;
        link.href = dataUrl;
        link.click();
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