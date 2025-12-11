
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


  // CONSULTAR DATOS
  require_once "../modelos/Anticipo_cliente.php"; 

  $anticipo= new Anticipo_cliente();
  $numero_a_letra = new NumeroALetras();

  $rspta = $anticipo->imprimir_anticipo($_GET['id']);
  $datos = $anticipo->empresa();

  if (empty($rspta['data']['idanticipo_cliente'])) { echo "Comprobante no existe"; die(); }
  
  // Generar QR
  $dataTxt = "
    Cliente: " . $rspta['data']['nombre_razonsocial'] .' ' . $rspta['data']['apellidos_nombrecomercial']. "
    Fecha Enisión: " . $rspta['data']['fecha_anticipo'] . "
    Total: " . $rspta['data']['total'] . "
    Contáctanos: ".$datos['data']['telefono1']."
  ";
  $filename = $rspta['data']['serie_comprobante'] . '-' . $rspta['data']['numero_comprobante'] . '.png';
  $qr_code = QrCode::create($dataTxt)->setEncoding(new Encoding('UTF-8'))->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)->setSize(600)->setMargin(10)->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
  
  $label = Label::create( $rspta['data']['serie_comprobante'] . '-' . $rspta['data']['numero_comprobante'])->setTextColor(new Color(255, 0, 0)); // Create generic label  
  $writer = new PngWriter(); // Create IMG
  $result = $writer->write($qr_code, label: $label); 
  $result->saveToFile(__DIR__.'/generador-qr/anticipos_de_cliente/'.$filename); // Save it to a file  
  $dataUri = $result->getDataUri();// Generate a data URI


  //NUMERO A LETRA
  $numero = $rspta['data']['total'];
  $numeroALetras = new NumeroALetras();
  $texto = $numeroALetras->toWords($numero)
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>

  <!-- Meta Data -->
  <meta charset="UTF-8">
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> <?php echo $rspta['data']['serie_comprobante'] . '-' . $rspta['data']['numero_comprobante']; ?> | Anticipo Cliente </title>
  <meta name="Description" content="Anticipo del cliente - CORPORACION BRARTNET Y ASOCIADOS S.A.C.">
  <meta name="Author" content="CORPORACION BRARTNET Y ASOCIADOS S.A.C.">
  <meta name="keywords" content="Anticipo Cliente,cliente,admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

  <!-- Favicon -->
  <link rel="icon" href="../assets/images/brand-logos/favicon.ico" type="image/x-icon">
  <!-- Main Theme Js -->
  <script src="../assets/js/authentication-main.js"></script>
  <!-- Bootstrap Css -->
  <link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Style Css -->
  <link href="../assets/css/styles.min.css" rel="stylesheet">
  <!-- Icons Css -->
  <link href="../assets/css/icons.min.css" rel="stylesheet">
  <!-- Style propio -->
  <link rel="stylesheet" href="../assets/css/style_new.css">

</head>

<body onload="window.print();" style="background-color: white !important;">

  <!-- End Switcher -->
  <div class="container-lg" >
    <div class="row justify-content-center">
      <div class="row gy-4 justify-content-center">
        <div class="col-xl-9">
          <div class="row">
            <div class="col-xxl-7 col-xl-6 col-lg-6 col-md-6 col-sm-6">    
              <div class="d-flex flex-fill flex-wrap gap-4">
                <div class="avatar avatar-lg avatar-rounded"><img src="../assets/modulo/empresa/logo/<?php echo $datos['data']['logo']; ?>" alt="" style="width: 100px; height: auto;"></div>
                <div >
                  <h6 class="mb-1 fw-semibold" style="max-width: 250px;"><?php echo $datos['data']['nombre_razon_social']; ?></h6>                  
                  <div class="fs-10 mb-0" style="max-width: 210px;"><?php echo $datos['data']['domicilio_fiscal']; ?></div>
                  <div class="fs-10 mb-0 text-muted contact-mail text-truncate"><?php echo $datos['data']['correo']; ?></div>
                  <div class="fs-10 mb-0 text-muted"><?php echo $datos['data']['telefono1']; ?> - <?php echo $datos['data']['telefono2']; ?></div>
                </div>
              </div>              
            </div>
            <div class="d-flex justify-content-end text-center col-xxl-5 col-xl-4 col-lg-4 col-md-6 col-sm-6">
              <div class="border border-dark">
                <div class="m-2">
                  <h6 class="text-muted mb-2"> RUC: <?php echo $datos['data']['numero_documento']; ?> </h6>
                  <h6>ANTICIPO DE CLIENTE ELECTRONICO</h6>
                  <h5><?php echo $rspta['data']['serie_comprobante'] . '-' . $rspta['data']['numero_comprobante']; ?></h5>
                </div>                
              </div>              
            </div>
          </div>
        </div>
        <div class="col-xl-9">
          <table class="font-size-10px">
            <tr>
              <th style="font-size: 12px;">Fecha de Emisión</th>
              <td style="font-size: 12px;">: <?php echo $rspta['data']['fecha_anticipo']; ?></td>
            </tr>
            <tr>
              <th style="font-size: 12px;">Señor(a)</th>
              <td style="font-size: 12px;">: <?php echo $rspta['data']['nombre_razonsocial'].' '.$rspta['data']['apellidos_nombrecomercial']; ?></td>
            </tr>
            <tr>
              <th style="font-size: 12px;">N° Documento</th>
              <td style="font-size: 12px;">: <?php echo $rspta['data']['numero_documento']; ?></td>
            </tr>
            <tr>
              <th style="font-size: 12px;">Dirección</th>
              <td style="font-size: 12px;">: <?php echo $rspta['data']['direccion']; ?></td>
            </tr>            
            <tr>
              <th style="font-size: 12px;">Observación</th>
              <td style="font-size: 12px;">: -</td>
            </tr>
          </table>
        </div>
        
        <div class="col-xl-9">
          <div class="table-responsive">
            <table class="text-nowrap border border-dark mt-1 w-100">
              <thead class="border border-dark">
                <tr >
                  <th class="celda-b-r-1px text-center">#</th>
                  <th class="celda-b-r-1px text-center">DESCRIPTION</th>
                  <th class="celda-b-r-1px text-center">UM</th>
                  <th class="celda-b-r-1px text-center">CANTIDAD</th>
                  <th class="celda-b-r-1px text-center">SUB TOTAL</th>
                </tr>
              </thead>
              <tbody >
                <tr>
                  <td class="px-1 celda-b-r-1px text-center" >1</td>
                  <td class="px-1 celda-b-r-1px" >Pago Anticipo de Cliente</td>
                  <td class="px-1 celda-b-r-1px text-align">NIU</td>
                  <td class="px-1 celda-b-r-1px text-center" >1.00</td>
                  <td class="px-1 celda-b-r-1px text-center" ><?php echo $rspta['data']['total']; ?></td>
                </tr>
                                       
              </tbody>
            </table>
          </div>
        </div>        

        <div class="col-xl-9">             
          
          <table  style="width: 100% !important;">            
            <tr>   
              <td class="font-size-12px">
                <span class="">SON: <b><?php echo $texto; ?> 00/100</b>  </span><br>
                <span class="text-muted">Representación impresa del Anticipo de Cliente Electrónica, puede ser consultada en <?php echo $datos['data']['nombre_razon_social']; ?></span> <br>
                <span class="text-muted">No valido para SUNAT</span>
              </td>  
              <td>
                <table class="text-nowrap w-100 table-bordered font-size-10px">
                  <tbody>
                    <tr><th class="text-center" colspan="3">CUENTAS BANCARIAS</th></tr>
                    <!-- filtramos los datos <<< SI la cuenta existe ENTONCES se muestran sus datos >>>> de lo contrario se ocultan :) ------>
                    <?php if (!empty($datos['data']['cuenta1'])) : ?>
                        <tr>
                            <td class="px-1"><?php echo $datos['data']['banco1']; ?></td>
                            <td class="px-1">Cta: <?php echo $datos['data']['cuenta1']; ?></td>
                            <td class="px-1">CCI: <?php echo $datos['data']['cci1']; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($datos['data']['cuenta2'])) : ?>
                        <tr>
                            <td class="px-1"><?php echo $datos['data']['banco2']; ?></td>
                            <td class="px-1">Cta: <?php echo $datos['data']['cuenta2']; ?></td>
                            <td class="px-1">CCI: <?php echo $datos['data']['cci2']; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($datos['data']['cuenta3'])) : ?>
                        <tr>
                            <td class="px-1"><?php echo $datos['data']['banco3']; ?></td>
                            <td class="px-1">Cta: <?php echo $datos['data']['cuenta3']; ?></td>
                            <td class="px-1">CCI: <?php echo $datos['data']['cci3']; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($datos['data']['cuenta4'])) : ?>
                        <tr>
                            <td class="px-1"><?php echo $datos['data']['banco4']; ?></td>
                            <td class="px-1">Cta: <?php echo $datos['data']['cuenta4']; ?></td>
                            <td class="px-1">CCI: <?php echo $datos['data']['cci4']; ?></td>
                        </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </td>                 
              <td align="center"><img src=<?php echo $dataUri; ?> width="90" height="auto"></td>
              <td>
                <div class="border border-dark rounded-1">
                  <div class="m-1">
                    <table class="text-nowrap w-100">
                      <tbody>
                        <tr>
                          <td scope="row"><p class="mb-0 font-size-12px">Sub Total</p></td> <th>:</th>
                          <td align="right"><p class="mb-0 "><?php echo $rspta['data']['total']; ?></p></td>
                        </tr>            
                        <tr>
                          <td scope="row"><p class="mb-0 font-size-12px">Descuento </p></td><th>:</th>
                          <td align="right"><p class="mb-0 ">0.00</p></td>
                        </tr>  
                        <tr>
                          <td scope="row"><p class="mb-0 font-size-12px">IGV <span class="text-danger">(0%)</span> </p></td> <th>:</th>
                          <td align="right"><p class="mb-0 ">0.00</p></td>
                        </tr>            
                        <tr>
                          <th scope="row"><p class="mb-0 fs-16">Total</p></th> <th>:</th>
                          <td align="right"><p class="mb-0 fw-semibold fs-16"><?php echo $rspta['data']['total']; ?></p></td>
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

  <!-- Bootstrap JS -->
  <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Show Password JS -->
  <script src="../assets/js/show-password.js"></script>

</body>

</html>