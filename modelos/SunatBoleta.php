<?php

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Ws\Reader\XmlReader;

use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Sale\Cuota;
use Luecano\NumeroALetras\NumeroALetras;

date_default_timezone_set('America/Lima');

require "../config/Conexion_v2.php";
$numero_a_letra = new NumeroALetras();

$empresa_f  = $facturacion->datos_empresa();
$venta_f    = $facturacion->mostrar_detalle_venta($f_idventa); ##echo $rspta['id_tabla']; echo  json_encode($venta_f , true);  die();

if (empty($venta_f['data']['venta'])) {
  # code...
} else {

  // Emrpesa emisora =============
  $e_razon_social       = mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_razon_social'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $e_comercial          = mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_comercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $e_domicilio_fiscal   = mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'UTF-8', mb_detect_encoding($empresa_f['data']['domicilio_fiscal'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
  $e_numero_documento   = $empresa_f['data']['numero_documento'];

  $e_distrito           = mb_convert_encoding($empresa_f['data']['distrito'], 'UTF-8', mb_detect_encoding($empresa_f['data']['distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $e_provincia          = mb_convert_encoding($empresa_f['data']['provincia'], 'UTF-8', mb_detect_encoding($empresa_f['data']['provincia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $e_departamento       = mb_convert_encoding($empresa_f['data']['departamento'], 'UTF-8', mb_detect_encoding($empresa_f['data']['departamento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $e_codubigueo         = mb_convert_encoding($empresa_f['data']['codubigueo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['codubigueo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

  // Cliente receptor =============
  $c_nombre_completo    = mb_convert_encoding($venta_f['data']['venta']['cliente_nombre_completo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cliente_nombre_completo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $c_tipo_documento     = $venta_f['data']['venta']['tipo_documento'];
  $c_tipo_documento_name= $venta_f['data']['venta']['tipo_documento_abreviatura'];
  $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
  $c_direccion          = mb_convert_encoding($venta_f['data']['venta']['direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

  $fecha_emision        = $venta_f['data']['venta']['fecha_emision'];
  $serie_comprobante    = $venta_f['data']['venta']['serie_comprobante'];
  $numero_comprobante   = $venta_f['data']['venta']['numero_comprobante'];
  $venta_total          = floatval($venta_f['data']['venta']['venta_total']);

  $venta_cuotas         = $venta_f['data']['venta']['venta_cuotas'];

  //NUMERO A LETRA =============
  $venta_total    = $venta_f['data']['venta']['venta_total'];
  $total_en_letra = $numero_a_letra->toInvoice($venta_total, 2, " SOLES");

  // if ($column['proigv'] == "Gravada") {

  //   $tipoafecto = "10";
  //   $igv = "18";
  //   $totalImpuestos = $column['Igv'];
  //   $setIgv = $column['Igv'];
  // } else if ($column['proigv'] == "No Gravada") {

    $tipoafecto = "20";
    $igv = "0";
    $totalImpuestos = "0";
    $setIgv = "0";
  // } 


  // Cliente
  $client = new Client();
  $client->setTipoDoc($c_tipo_documento)
    ->setNumDoc($c_numero_documento)
    ->setRznSocial($c_nombre_completo);

  // Emisor
  $address = (new Address())
    ->setUbigueo($e_codubigueo )
    ->setDepartamento($e_departamento)
    ->setProvincia($e_provincia)
    ->setDistrito($e_distrito)
    ->setUrbanizacion('-')
    ->setDireccion($e_domicilio_fiscal)
    ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

  // Emisor
  $company = (new Company())
    ->setRuc($e_numero_documento )
    ->setRazonSocial($e_razon_social)
    ->setNombreComercial($e_comercial)
    ->setAddress($address);

  // Venta
  $invoice = (new Invoice())
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101') // Catalog. 51
    ->setTipoDoc('03')
    ->setSerie($serie_comprobante)
    ->setCorrelativo($numero_comprobante)
    ->setFechaEmision(new DateTime($fecha_emision . '-05:00'))   
    ->setTipoMoneda('PEN')
    ->setClient($client)
    ->setMtoOperExoneradas($venta_total)
    ->setMtoIGV(0)
    ->setTotalImpuestos(0)
    ->setValorVenta($venta_total)
    ->setSubTotal($venta_total)
    ->setMtoImpVenta($venta_total)
    ->setCompany($company);

  

  $i = 0;
  $arrayItem = [];

  foreach ($venta_f['data']['detalle'] as $key => $val) {    

    $nombre_producto      = mb_convert_encoding($val['pr_nombre'], 'UTF-8', mb_detect_encoding($val['pr_nombre'] , "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $cantidad             = floatval($val['cantidad_total']);
    $precio_venta         = floatval($val['precio_venta']);  
    $precio_venta_dcto    = floatval($val['precio_venta_descuento']);  
    $descuento            = floatval($val['descuento']);  
    $descuento_pct        = floatval($val['descuento_porcentaje']);  
    $subtotal             = floatval($val['subtotal']);
    $subtotal_no_dcto     = floatval($val['subtotal_no_descuento']);

    $um_nombre        = mb_convert_encoding($val['um_nombre'], 'UTF-8', mb_detect_encoding($val['um_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $um_abreviatura   = mb_convert_encoding($val['um_abreviatura'], 'UTF-8', mb_detect_encoding($val['um_abreviatura'], "UTF-8, ISO-8859-1, ISO-8859-15", true));


    $item = (new SaleDetail())
      ->setCodProducto($val['codigo'])            # Código del producto (Ej: "CALC123")
      ->setUnidad($um_abreviatura)                # Unidad de medida (Ej: "NIU")
      ->setCantidad($cantidad );                  # Cantidad vendida (Ej: 2)

      if ($descuento > 0) {                       # Aplicamos descuento
        $item->setDescuentos([(new Charge())
          ->setCodTipo('00')                      # Catalog. 53 (00: Descuento que afecta la Base Imponible)
          ->setMontoBase($subtotal_no_dcto)       # Base antes del descuento (cantidad * valor unitario) (Ej: 2 x 50 = 100)
          ->setFactor($descuento_pct)             # Porcentaje de descuento 20% 
          ->setMonto($descuento)                  # Total descuento: S/ 20 (descuento numerico)
        ]);
      }

      $item->setDescripcion($nombre_producto)     # Descripción (Ej: "Calculadora CASIO FX-82")
      ->setMtoBaseIgv($subtotal)                  # Monto base para IGV (ya con descuento, Ej: 90.00)
      ->setPorcentajeIgv(0)                       # 18% o 0% (exonerado)
      ->setIgv(0)                                 # Monto IGV (0.00)
      ->setTipAfeIgv('20')                        # Exonerado Op. Onerosa - Catalog. 07
      ->setTotalImpuestos(0)                      # Total impuestos (0.00 en este caso)
      ->setMtoValorVenta($subtotal)               # Valor venta = cantidad * precio con descuento (Ej: 2 x 45 = 90.00)
      ->setMtoValorUnitario($precio_venta)        # Valor unitario sin IGV ni descuento (Ej: 50.00)
      ->setMtoPrecioUnitario($precio_venta_dcto); # (Valor venta + Total Impuestos) / Cantidad

    $arrayItem[$i] = $item;
    $i++;
  }

  $legend = (new Legend())
    ->setCode('1000')
    ->setValue($total_en_letra);

  $invoice->setDetails($arrayItem)
    ->setLegends([$legend]);

  /* 
    * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    * Envío a SUNAT
    + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    */

  $result = $see->send($invoice);

  // Guardar XML firmado digitalmente.
  $nombre_xml = '../assets/modulo/facturacion/boleta/' . $invoice->getName() . '.xml';
  file_put_contents($nombre_xml,  $see->getFactory()->getLastXml());  

  // Verificamos que la conexión con SUNAT fue exitosa.
  if (!$result->isSuccess()) {
    // Mostrar error al conectarse a SUNAT.
    $sunat_error = limpiarCadena("Codigo Error: " . $result->getError()->getCode() . " \n Mensaje Error: " . $result->getError()->getMessage());
    $code = (int)$result->getError()->getCode();
    $sunat_mensaje = limpiarCadena("Codigo Error: " . $result->getError()->getCode() . " \n Mensaje Error: " . $result->getError()->getMessage());
    if ($code === 0) {
      $sunat_estado = 'RECHAZADA' ;      
    } else if ($code >= 2000 && $code <= 3999) {
      $sunat_estado = 'RECHAZADA' ;
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $code;
    }
    $sunat_code = $code;
    // exit();
  } else {

    // Guardamos el CDR
    $nombre_de_cdr = '../assets/modulo/facturacion/boleta/R-' . $invoice->getName() . '.zip';
    file_put_contents($nombre_de_cdr, $result->getCdrZip());


    /* 
      * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
      * Lectura del CDR 
      + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
      */

    $cdr = $result->getCdrResponse();

    $code = (int)$cdr->getCode();

    if ($code === 0) {
      $sunat_estado = 'ACEPTADA' ;
      if (count($cdr->getNotes()) > 0) {
        $sunat_estado = 'ACEPTADA' ; // Es aceptado por sunat, pero corrgir en la siguientes emisiones
        // $sunat_observacion = $cdr->getNotes(); # Corregir estas observaciones en siguientes emisiones. var_dump()
        foreach ($cdr->getNotes() as $key => $val) {
          $sunat_observacion .= $val . "<br>";
        }
      }
    } else if ($code >= 2000 && $code <= 3999) {
      $sunat_estado = 'RECHAZADA' ;
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $code;
    }
    $sunat_code = $code;
    $sunat_mensaje = limpiarCadena($cdr->getDescription());

    $sunat_ticket= $cdr->getId() ; 

    /* 
      * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
      * Lectura del codgo Hash 
      + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
      */

    $parser = new XmlReader();
    $archivoXml = file_get_contents($nombre_xml);
    $documento = $parser->getDocument($archivoXml);
    $sunat_hash = $documento->getElementsByTagName('DigestValue')->item(0)->nodeValue;
    
  }
}
