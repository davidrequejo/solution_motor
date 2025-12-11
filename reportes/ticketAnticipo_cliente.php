<?php
require '../vendor/autoload.php';
use Luecano\NumeroALetras\NumeroALetras;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);
} else {
  if ($_SESSION['anticipos'] == 1) {
?>

    <?php

      $numero_a_letra = new NumeroALetras();

      require_once "../modelos/Anticipo_cliente.php";
      $anticipo = new Anticipo_cliente();

      $rspta = $anticipo->imprimir_anticipo($_GET["id"]);
      $datos = $anticipo->empresa();

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
      
    ?>

    <html>

    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <title><?php echo 'ANTICIPO DE CLIENTE - '. $rspta['data']['serie_comprobante'] .'-'. $rspta['data']['numero_comprobante'] ; ?></title>

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
        <a  type="button" class="btn btn-outline-info p-1 mb-2 m-l-5px w-40px" href="javascript:window.print()" data-bs-toggle="tooltip" title="Imprimir Ticket">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
          </svg>
        </a>

        <button type="button" class="btn btn-outline-warning p-1 mb-2 m-l-5px w-40px" id="btn-descargar" data-bs-toggle="tooltip" title="Descargar Imagen" onclick="decargar_imagen();">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <circle cx="8.5" cy="8.5" r="1.5"></circle>
            <polyline points="21 15 16 10 5 21"></polyline>
          </svg>
        </button>      

        <button type="button" class="btn btn-outline-danger p-1 mb-2 m-l-5px w-40px" id="btn-compartir" data-bs-toggle="tooltip" title="Compartir Imagen" onclick="compartir_imagen();">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2">
            <circle cx="18" cy="5" r="3"></circle>
            <circle cx="6" cy="12" r="3"></circle>
            <circle cx="18" cy="19" r="3"></circle>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
          </svg>
        </button>      
      </div>
      <div id="iframe-img-descarga" style="background-color: #f0f1f7; border-radius: 5px;">
      
        <br>
        <table class="mx-3" border="0" align="center" width="300px" style="font-size: 12px">          

          <tbody>
            <tr align="center"><td><img src="../assets/modulo/empresa/logo/<?php echo ($datos['data']['logo']); ?>" width="<?php echo ($datos['data']['logo_c_r'] == 0 ? 150 : 100); ?>"> </td> </tr>
            
            <tr align="center">
              <td style="font-size: 14px"> 
                .::<strong> <?php echo utf8_decode(htmlspecialchars_decode($datos['data']['nombre_comercial'])); ?> </strong>::. 
              </td> 
            </tr>

            <tr align="center"><td style="font-size: 10px"><?php echo $datos['data']['nombre_razon_social']; ?></td></tr>
            <tr align="center"><td style="font-size: 14px"><strong> R.U.C. <?php echo $datos['data']['numero_documento']; ?></strong></td></tr>
            <tr align="center"><td style="font-size: 10px"><?php echo utf8_decode($datos['data']['domicilio_fiscal']) . ' <br> ' . $datos['data']['telefono1'] . "-" . $datos['data']['telefono2']; ?></td></tr>
            <tr align="center"><td style="font-size: 10px"><?php echo utf8_decode(strtolower($datos['data']['correo'])); ?></td></tr>
            <!-- <tr align="center"><td style="font-size: 10px"><?php echo utf8_decode(strtolower($datos['data']['web'])); ?></td></tr> -->

            <tr><td ><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 1px;" ></div></td> </tr>
            <tr><td ><div style="border-bottom: 1px dotted black; margin-top: 1px; margin-bottom: 8px;" ></div></td> </tr>
            <tr><td align="center"><strong> ANTICIPO DE CLIENTE </strong></br> <b style="font-size: 14px"><?php echo $rspta['data']['serie_comprobante']; ?>-<?php echo $rspta['data']['numero_comprobante'];?></b> </td></tr>
            
            <tr><td ><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 1px;" ></div></td> </tr>
            <tr><td ><div style="border-bottom: 1px dotted black; margin-top: 1px; margin-bottom: 8px;" ></div></td> </tr>
          </tbody>
        </table>

        <table border="0" align="center" width="300px" style="font-size: 12px">
          <tbody>
            <tr><td><strong>Cliente:</strong></td><td><?php echo $rspta['data']['nombre_razonsocial'];?> <?php echo $rspta['data']['apellidos_nombrecomercial'];?></td></tr>
            <tr><td><strong>RUC/DNI:</strong></td><td><?php echo $rspta['data']['numero_documento']; ?></td></tr>
            <tr><td><strong>Dirección:</strong></td><td><?php echo $rspta['data']['direccion']; ?></td></tr>
            <tr><td><strong>Emisión:</strong></td><td><?php echo $rspta['data']['fecha_anticipo'] ?></td></tr>
            <tr><td><strong>Moneda:</strong></td><td>SOLES</td></tr>
            <tr><td colspan="2"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td> </tr>
          </tbody>
        </table>
        <br>
        <!-- Mostramos los detalles de la venta en el documento HTML -->
        <table border="0" align="center" width="300px" style="font-size: 12px;">
          <tr><td align="center"><h2><b>MONTO ANTICIPO </br>s/ <?php echo $rspta['data']['total']?></b></h2></td></tr>
          <tr><td><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td> </tr>
        </table>
        <?php $num_total = $numero_a_letra->toInvoice( $rspta['data']['total'], 2, " SOLES" ); ?>
        
        <table border="0" align="center" width="300px" style='font-size: 12px' >
          <tr><td></br><strong>Pagaste: </strong> <?php echo $num_total; ?></td></tr>
        </table>   

       
        <table border='0' align="center" width='300px' style='font-size: 12px'>
          <tbody>
            <tr>
              <td>
                <img src=<?php echo $dataUri; ?> width="100" height="100"><br>
                
              </td>
              <td style="font-size: 11px;">
                <span> 
                  La versión impresa del comprobante está disponible para su consulta en el siguiente enlace. 
                  Puede acceder a ella en cualquier momento para revisar los detalles de su transacción.
                </span>
                <span class="fw-bold"><b><?php echo utf8_decode(strtolower($datos['data']['web'])); ?></b></span>                
              </td>
            </tr>
            <tr>
              <td style="text-align: center;" colspan="2" ></td>              
            </tr>
            <tr>
              <td style='font-size: 10px; text-align: center;' colspan="2"> <span><strong>SERVICIOS TRANSFERIDOS EN LA REGIÓN AMAZÓNICA SELVA PARA SER CONSUMIDOS EN LA MISMA.</strong></span></td>              
            </tr>
          </tbody>            
          <br>          
        </table>
        <br>
        <table border="0" align="center" width="300px" style='font-size: 12px; margin-top: 10px;' align="center">
          <tr><td colspan="5" align="center"><small class="text-muted">Ticket emitido. No valido para SUNAT</small></td></tr>        
        </table>
      </div>
      <!-- Popper JS -->
      <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>
      <!-- Bootstrap JS -->
      <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

      <!-- Dropzone JS -->
      <script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script>

      <script>
        const mi_btn_compartir = document.getElementById("btn-compartir");
        const mi_btn_descargar = document.getElementById("btn-descargar");
        const spinnerSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-spinner" style="background: none;"><circle cx="50" cy="50" fill="none" stroke="#000" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(180 50 50)"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform></circle></svg>`;    
        const sharedSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2"><circle cx="18" cy="5" r="3"></circle> <circle cx="6" cy="12" r="3"></circle> <circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line> <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>`;      
        const imgenSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`;              

        var titulo = document.title; // Obtener el título de la página

        // ══════════════════════════════════════ DESCARGAR FORMATO IMAGEN ══════════════════════════════════════

        function decargar_imagen() {          
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