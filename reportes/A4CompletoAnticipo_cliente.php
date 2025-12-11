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



<html lang="es">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../bd/exBoletaCompleto_files/style.css" rel="stylesheet" type="text/css">

</head>
<!-- onload="window.print();" -->

<body>


  <div class="tm_container">
    <div class="tm_invoice_wrap">
      <div class="tm_invoice tm_style2 tm_type1 tm_accent_border tm_radius_0 tm_small_border" id="tm_download_section"
        style="background-repeat: no-repeat;background-position: center, center;
       background-size: cover; ">
        <div class="tm_invoice_in">
          <div class="tm_invoice_head tm_mb15 tm_m0_md">
            <div class="tm_invoice_left">
            <div class="tm_logo" style="display: flex; align-items: center;"> 
              <img src="../assets/modulo/empresa/logo/<?php echo $datos['data']['logo']; ?>" style="width: 55px;  margin-right: 9px;">
              <h6 class="mb-1 fw-semibold" style="font-size: 14px; font-weight: bold; max-width: 200px; margin: 0 auto;">
                <?php echo $datos['data']['nombre_razon_social']; ?>
              </h6>
            </div>
            </div>
            <div class="tm_invoice_right">
              <div class="tm_grid_row tm_col_3">
                <div class="tm_text_center">
                  <p class="tm_accent_color tm_mb0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"
                      fill="currentColor">
                      <path
                        d="M424 80H88a56.06 56.06 0 00-56 56v240a56.06 56.06 0 0056 56h336a56.06 56.06 0 0056-56V136a56.06 56.06 0 00-56-56zm-14.18 92.63l-144 112a16 16 0 01-19.64 0l-144-112a16 16 0 1119.64-25.26L256 251.73l134.18-104.36a16 16 0 0119.64 25.26z">
                      </path>
                    </svg>
                  </p>
                  <?php echo $datos['data']['correo']; ?>
                </div>
                <div class="tm_text_center">
                  <p class="tm_accent_color tm_mb0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"
                      fill="currentColor">
                      <path
                        d="M391 480c-19.52 0-46.94-7.06-88-30-49.93-28-88.55-53.85-138.21-103.38C116.91 298.77 93.61 267.79 61 208.45c-36.84-67-30.56-102.12-23.54-117.13C45.82 73.38 58.16 62.65 74.11 52a176.3 176.3 0 0128.64-15.2c1-.43 1.93-.84 2.76-1.21 4.95-2.23 12.45-5.6 21.95-2 6.34 2.38 12 7.25 20.86 16 18.17 17.92 43 57.83 52.16 77.43 6.15 13.21 10.22 21.93 10.23 31.71 0 11.45-5.76 20.28-12.75 29.81-1.31 1.79-2.61 3.5-3.87 5.16-7.61 10-9.28 12.89-8.18 18.05 2.23 10.37 18.86 41.24 46.19 68.51s57.31 42.85 67.72 45.07c5.38 1.15 8.33-.59 18.65-8.47 1.48-1.13 3-2.3 4.59-3.47 10.66-7.93 19.08-13.54 30.26-13.54h.06c9.73 0 18.06 4.22 31.86 11.18 18 9.08 59.11 33.59 77.14 51.78 8.77 8.84 13.66 14.48 16.05 20.81 3.6 9.53.21 17-2 22-.37.83-.78 1.74-1.21 2.75a176.49 176.49 0 01-15.29 28.58c-10.63 15.9-21.4 28.21-39.38 36.58A67.42 67.42 0 01391 480z">
                      </path>
                    </svg>
                  </p>
                  <?php echo $datos['data']['telefono1']; ?> <br>

                </div>
                <div class="tm_text_center">
                  <p class="tm_accent_color tm_mb0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"
                      fill="currentColor">
                      <circle cx="256" cy="192" r="32"></circle>
                      <path
                        d="M256 32c-88.22 0-160 68.65-160 153 0 40.17 18.31 93.59 54.42 158.78 29 52.34 62.55 99.67 80 123.22a31.75 31.75 0 0051.22 0c17.42-23.55 51-70.88 80-123.22C397.69 278.61 416 225.19 416 185c0-84.35-71.78-153-160-153zm0 224a64 64 0 1164-64 64.07 64.07 0 01-64 64z">
                      </path>
                    </svg>
                  </p>
                  <?php echo $datos['data']['distrito'] .' - '. $datos['data']['departamento']; ?>
                </div>
              </div>
            </div>
            <div class="tm_shape_bg tm_accent_bg_10 tm_border tm_accent_border_20"></div>
          </div>
          <div class="tm_invoice_info tm_mb10 tm_align_center">
            <div class="tm_invoice_info_left tm_mb20_md">
              <p class="tm_mb0">
                <b class="tm_primary_color">Comprobante: </b><?php echo $rspta['data']['serie_comprobante'] .'-'. $rspta['data']['numero_comprobante']; ?> <br>
                <b class="tm_primary_color">Fecha emisión: </b><?php echo $rspta['data']['fecha_anticipo']; ?><br>
              </p>
            </div>
            <div class="tm_invoice_info_right">
              <div class="tm_border tm_accent_border_20 tm_radius_0 tm_accent_bg_10 tm_curve_35 tm_text_center">
                <div>
                  <b class="tm_accent_color tm_f26 tm_medium tm_body_lineheight">Anticipo de Cliente electrónica</b>
                </div>
              </div>
            </div>
          </div>
          <h2 class="tm_f16 tm_section_heading tm_accent_border_20 tm_mb0"><span
              class="tm_accent_bg_10 tm_radius_0 tm_curve_35 tm_border tm_accent_border_20 tm_border_bottom_0 tm_accent_color"><span>Dirigido
                a</span></span></h2>
          <div class="tm_table tm_style1 tm_mb20">
            <div class="tm_border  tm_accent_border_20 tm_border_top_0">
              <div class="tm_table_responsive">
                <table style="font-size:12px;">
                  <tbody>
                    <tr>
                      <td class="tm_width_6 tm_border_top_0">
                        <b class="tm_primary_color tm_medium">Cliente: </b><?php echo $rspta['data']['nombre_razonsocial'];?> <?php echo $rspta['data']['apellidos_nombrecomercial'];?>
                      </td>
                      <td class="tm_width_6 tm_border_top_0 tm_border_left tm_accent_border_20">
                        <b class="tm_primary_color tm_medium">DNI/RUC: </b> <?php echo $rspta['data']['numero_documento']; ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="tm_width_6 tm_accent_border_20">
                        <b class="tm_primary_color tm_medium">Dirección: </b><?php echo $datos['data']['domicilio_fiscal']; ?>
                      </td>
                      <td class="tm_width_6 tm_border_left tm_accent_border_20">
                        <b class="tm_primary_color tm_medium">Moneda: </b>SOLES
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="tm_table tm_style1">
            <div class="tm_border tm_accent_border_20">
              <div class="tm_table_responsive">
                <table>
                  <thead>
                    <tr>
                      <th class="tm_width_1 tm_semi_bold tm_accent_color tm_accent_bg_10 tm-centrado">Nro</th>
                      <th class="tm_width_4 tm_semi_bold tm_accent_color tm_accent_bg_10 tm-centrado">Descripción</th>
                      <th class="tm_width_2 tm_semi_bold tm_accent_color tm_accent_bg_10 tm-centrado">Unidad</th>
                      <th class="tm_width_1 tm_semi_bold tm_accent_color tm_accent_bg_10">Cant.</th>
                      <th class="tm_width_1 tm_semi_bold tm_accent_color tm_accent_bg_10">Precio</th>
                      <th class="tm_width_1 tm_semi_bold tm_accent_color tm_accent_bg_10">Dscto.</th>
                      <th class="tm_width_2 tm_semi_bold tm_accent_color tm_accent_bg_10 tm-centrado">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="tm_texto_font_11">

                    <tr>
                      <td class="tm_width_1 tm_accent_border_20 tm-centrado">1</td>
                      <td class="tm_width_4 tm_accent_border_20">Pago Anticipo de Cliente</td>
                      <td class="tm_width_2 tm_accent_border_20 tm-centrado">NIU</td>
                      <td class="tm_width_1 tm_accent_border_20 tm-centrado">1.00</td>
                      <td class="tm_width_1 tm_accent_border_20 tm-centrado">0.00</td>
                      <td class="tm_width_1 tm_accent_border_20 tm-centrado">0.00</td>
                      <td class="tm_width_2 tm_accent_border_20 tm-centrado"><?php echo $rspta['data']['total']; ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tm_invoice_footer tm_mb15 tm_m0_md">

              <div class="tm_left_footer">

                <p class="tm_mb2"><b class="tm_primary_color">Monto Anticipo:</b></p>
                <p class="tm_m0">s/ <?php echo $rspta['data']['total']; ?> </p>

              </div>
              <div class="tm_right_footer tm_pt0">
                <!-- tm_mb15 tm_m0_md -->
                <table class="">

                  <tbody>
                    <tr>
                    </tr>
                    <tr>
                      <td class="tm_width_3 tm_primary_color tm_border_none tm_medium tm_pt0">Op. Gravada</td>
                      <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_medium tm_pt0">0.00</td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                      <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">I.G.V.</td>
                      <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0">0.00</td>
                    </tr>
                    <tr>
                      <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">Importe pagado</td>
                      <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0"><?php echo $rspta['data']['total']; ?></td>
                    </tr>
                    <tr>
                      <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">Vuelto</td>
                      <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0">0.00</td>
                    </tr>
                    <tr class="tm_accent_border_20 tm_border">
                      <td class="tm_width_3 tm_bold tm_f16 tm_border_top_0 tm_accent_color tm_accent_bg_10">Total a
                        pagar
                      </td>
                      <td
                        class="tm_width_3 tm_bold tm_f16 tm_border_top_0 tm_accent_color tm_text_right tm_accent_bg_10">
                        <?php echo $rspta['data']['total']; ?></td>
                    </tr>

                    <tr>
                      <td class="tm_width_del100" style="width: 100%;font-size: smaller;">SON: <?php echo $texto; ?> 00/100</b></td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="tm_invoice_footer tm_type1">
              <div class="tm_left_footer">
                <p class="tm_mb2"><b class="tm_primary_color">Cuentas Bancarias:</b></p>
                <p class="tm_m0"><?php echo $datos['data']['banco1']; ?>: <?php echo $datos['data']['cuenta1']; ?> - CCI : <?php echo $datos['data']['cci1']; ?></p>
                <p class="tm_m0"><?php echo $datos['data']['banco2']; ?>: <?php echo $datos['data']['cuenta2']; ?> - CCI : <?php echo $datos['data']['cci2']; ?></p>
                <p class="tm_m0"><?php echo $datos['data']['banco3']; ?>: <?php echo $datos['data']['cuenta3']; ?> - CCI : <?php echo $datos['data']['cci3']; ?></p>
                <p class="tm_m0"><?php echo $datos['data']['banco4']; ?>: <?php echo $datos['data']['cuenta4']; ?> - CCI : <?php echo $datos['data']['cci4']; ?></p>
              </div>
              <div class="tm_right_footer">
                <div class="tm_signqr tm_text_center">
                <img src=<?php echo $dataUri; ?> width="90" height="auto">
                  
                  
                  <br>
                  <label class="tm_m0 tm_f12">
                    Representación impresa del Anticipo de Cliente Electrónica, puede ser consultada en <?php echo $datos['data']['nombre_razon_social']; ?>
                  </label>

                </div>
              </div>
            </div>
          </div>
          <div class="tm_bottom_invoice tm_accent_border_20">
            <div class="tm_bottom_invoice_left">
              <p class="tm_m0 tm_f18 tm_accent_color tm_mb5">::.GRACIAS POR SU APORTE.::</p>
              <p class="tm_primary_color tm_f12 tm_m0 tm_bold">El comprobante <?php echo $rspta['data']['serie_comprobante'] .'-'. $rspta['data']['numero_comprobante']; ?> a sido Emitido. No valido para SUNAT</p>
            </div>
            <div class="tm_bottom_invoice_right tm_mobile_hide">
              <div class="tm_logo"><img src="../assets/modulo/empresa/logo/<?php echo $datos['data']['logo']; ?>" style="width: 55px;  margin-right: 9px;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="tm_invoice_btns tm_hide_print">
        <a href="javascript:window.print()" class="tm_invoice_btn tm_color1">
          <span class="tm_btn_icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
              <path
                d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24"
                fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"></path>
              <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none" stroke="currentColor"
                stroke-linejoin="round" stroke-width="32"></rect>
              <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none"
                stroke="currentColor" stroke-linejoin="round" stroke-width="32"></path>
              <circle cx="392" cy="184" r="24" fill="currentColor"></circle>
            </svg>
          </span>
          <span class="tm_btn_text">Imprimir</span>
        </a>

      </div>
    </div>
  </div>




</body>

</html>