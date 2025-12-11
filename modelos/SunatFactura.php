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
  $e_razon_social       = mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'ISO-8859-1', 'UTF-8');
  $e_comercial          = $empresa_f['data']['nombre_comercial'];
  $e_domicilio_fiscal   = $empresa_f['data']['domicilio_fiscal'];
  $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
  $e_numero_documento   = $empresa_f['data']['numero_documento'];

  $e_distrito           = $empresa_f['data']['distrito'];
  $e_provincia          = $empresa_f['data']['provincia'];
  $e_departamento       = $empresa_f['data']['departamento'];
  $e_codubigueo       = $empresa_f['data']['codubigueo'];

  // Cliente receptor =============
  $c_nombre_completo    = $venta_f['data']['venta']['cliente_nombre_completo'];
  $c_tipo_documento     = $venta_f['data']['venta']['tipo_documento_abreviatura'];
  $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
  $c_direccion          = $venta_f['data']['venta']['direccion'];

  $fecha_emision        = $venta_f['data']['venta']['fecha_emision'];
  $serie_comprobante    = $venta_f['data']['venta']['serie_comprobante'];
  $numero_comprobante   = $venta_f['data']['venta']['numero_comprobante'];
  $venta_total          = floatval($venta_f['data']['venta']['venta_total']);

  $venta_cuotas         = $venta_f['data']['venta']['venta_cuotas'];

  //NUMERO A LETRA ============= 
  $total_en_letra = $numero_a_letra->toInvoice($venta_total, 2, " SOLES");

  /* 
  * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  * Factura Electrónica
  + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  */

  // Cliente
  $client = (new Client())
    ->setTipoDoc('6')
    ->setNumDoc($c_numero_documento)
    ->setRznSocial($c_nombre_completo);

  // Emisor
  $address = (new Address())
    ->setUbigueo($e_codubigueo)
    ->setDepartamento($e_departamento)
    ->setProvincia($e_provincia)
    ->setDistrito($e_distrito)
    ->setUrbanizacion('-')
    ->setDireccion($e_domicilio_fiscal)
    ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

  // Emisor
  $company = (new Company())
    ->setRuc($e_numero_documento)
    ->setRazonSocial($e_razon_social)
    ->setNombreComercial($e_comercial)
    ->setAddress($address);

  // Venta
  $invoice = new Invoice();
  $invoice->setFecVencimiento(new DateTime($fecha_emision . '-05:00'))
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101')
    ->setTipoDoc('01')
    ->setSerie($serie_comprobante)
    ->setCorrelativo($numero_comprobante)
    ->setFechaEmision(new DateTime($fecha_emision . '-05:00'))
    ->setFormaPago(new FormaPagoContado())
    ->setTipoMoneda('PEN')
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperExoneradas($venta_total)
    ->setMtoIGV(0)
    ->setTotalImpuestos(0)
    ->setValorVenta($venta_total)
    ->setSubTotal($venta_total)
    ->setMtoImpVenta($venta_total);
  
  // Metodo de Pago 
  if ($venta_cuotas === "SI") {
    $cuotas = [];
    foreach ($venta_f['data']['venta_cuotas'] as $cuota) {
      $cuotas[] = (new Cuota())
        ->setMonto(floatval($cuota['monto_cuota']))
        ->setFechaPago(new DateTime($cuota['fecha_vencimiento']));
    }

    $invoice->setFormaPago(new FormaPagoCredito($venta_total)); // Obligatorio para SUNAT el monto total
    $invoice->setCuotas($cuotas);

  } else {
    $invoice->setFormaPago(new FormaPagoContado());
  }  

  $i = 0;
  $arrayItem = [];

  foreach ($venta_f['data']['detalle'] as $key => $val) {    

    $nombre_producto      = mb_convert_encoding($val['pr_nombre'], 'UTF-8', mb_detect_encoding($val['pr_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $cantidad             = floatval($val['cantidad_total']);
    $precio_venta         = floatval($val['precio_venta']);  
    $precio_venta_dcto    = floatval($val['precio_venta_descuento']);  
    $descuento            = floatval($val['descuento']);  
    $descuento_pct        = floatval($val['descuento_porcentaje']);  
    $subtotal             = floatval($val['subtotal']);
    $subtotal_no_dcto     = floatval($val['subtotal_no_descuento']);

    $um_nombre        = mb_convert_encoding($val['um_nombre'], 'UTF-8', mb_detect_encoding($val['um_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $um_abreviatura   = mb_convert_encoding($val['um_abreviatura'], 'UTF-8', mb_detect_encoding($val['um_abreviatura'], "UTF-8, ISO-8859-1, ISO-8859-15", true));


    $detail = new SaleDetail();
    $detail->setCodProducto($val['codigo'])
      ->setUnidad($um_abreviatura)                # Unidad - Catalog. 03
      ->setDescripcion($nombre_producto)          # Nombre de producto
      ->setCantidad($cantidad);                   # Cantidad Real
      if ($descuento > 0) {                       # Aplicamos descuento
        $detail->setDescuentos([(new Charge())
          ->setCodTipo('00')                      # Catalog. 53 (00: Descuento que afecta la Base Imponible)
          ->setMontoBase($subtotal_no_dcto)       # S/ 100 (cantidad * valor unitario)
          ->setFactor($descuento_pct)             # 20% (descuento porcentaje)
          ->setMonto($descuento)                  # S/ 20 (descuento numerico)
        ]);
      }
      $detail->setMtoValorUnitario($precio_venta)
      ->setMtoValorVenta($subtotal)               # cantidad * valor unitario - descuento (que afecta la base)
      ->setMtoBaseIgv($subtotal)
      ->setPorcentajeIgv(0)                       # 18% o 0%
      ->setIgv(0)
      ->setTipAfeIgv('20')                        # Exonerado Op. Onerosa - Catalog. 07
      ->setTotalImpuestos(0)                      # Suma de impuestos en el detalle
      ->setMtoPrecioUnitario($precio_venta_dcto); # (Valor venta + Total Impuestos) / Cantidad

    $arrayItem[$i] = $detail;
    $i++;
  }

  $invoice->setDetails($arrayItem)
    ->setLegends([
      (new Legend())
        ->setCode('1000')                       # Monto en letras - Catalog. 52
        ->setValue($total_en_letra)             # VEINTE CON 00/100 SOLES
    ]);

  /* 
  * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  * Envío a SUNAT
  + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  */

  $result = $see->send($invoice);

  // Guardar XML firmado digitalmente.
  $nombre_xml = '../assets/modulo/facturacion/factura/' . $invoice->getName() . '.xml';
  file_put_contents($nombre_xml,  $see->getFactory()->getLastXml());

  

  // Verificamos que la conexión con SUNAT fue exitosa.
  if (!$result->isSuccess()) {
    // Mostrar error al conectarse a SUNAT.
    $sunat_error = limpiarCadena("Codigo Error: " . $result->getError()->getCode() . " \n Mensaje Error: " . $result->getError()->getMessage());
    $code = (int)$result->getError()->getCode();
    $sunat_mensaje = limpiarCadena("Codigo Error: " . $result->getError()->getCode() . " \n Mensaje Error: " . $result->getError()->getMessage());
    if ($code === 0) {
      $sunat_estado = 'RECHAZADA';
    } else if ($code >= 2000 && $code <= 3999) {
      $sunat_estado = 'RECHAZADA';
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $code;
    }
    $sunat_code = $code;
    // exit();
  } else {

    // Guardamos el CDR
    $nombre_de_cdr = '../assets/modulo/facturacion/factura/R-' . $invoice->getName() . '.zip';
    file_put_contents($nombre_de_cdr, $result->getCdrZip());


    /* 
    * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    * Lectura del CDR 
    + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    */

    $cdr = $result->getCdrResponse();

    $code = (int)$cdr->getCode();

    if ($code === 0) {
      $sunat_estado = 'ACEPTADA';
      if (count($cdr->getNotes()) > 0) {
        $sunat_estado = 'ACEPTADA' ;  // Es aceptado por sunat, pero corrgir en la siguientes emisiones
        // $sunat_observacion = $cdr->getNotes(); # Corregir estas observaciones en siguientes emisiones. var_dump()
        foreach ($cdr->getNotes() as $key => $val) {
          $sunat_observacion .= $val . "<br>";
        }
      }
    } else if ($code >= 2000 && $code <= 3999) {
      $sunat_estado = 'RECHAZADA';
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $code;
    }
    $sunat_code = $code;
    $sunat_mensaje = limpiarCadena($cdr->getDescription()) ;

    $sunat_ticket= $cdr->getId(); 

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
