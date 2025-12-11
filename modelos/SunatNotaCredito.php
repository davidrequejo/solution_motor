<?php

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Response\BillResult;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Ws\Reader\XmlReader;

use Luecano\NumeroALetras\NumeroALetras;

date_default_timezone_set('America/Lima');

require "../config/Conexion_v2.php";
$numero_a_letra = new NumeroALetras();

$empresa_f  = $facturacion->datos_empresa();
$venta_f    = $facturacion->mostrar_detalle_venta( $f_idventa );  # echo  json_encode($venta_f );  die();

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

  // Datos a Anular =============
  $nc_motivo_nota       = $venta_f['data']['venta']['nc_motivo_nota'];
  $nc_nombre_motivo     = mb_convert_encoding($venta_f['data']['venta']['nc_nombre_motivo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_nombre_motivo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $nc_tipo_comprobante  = mb_convert_encoding($venta_f['data']['venta']['nc_tipo_comprobante'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_tipo_comprobante'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $nc_serie_y_numero    = mb_convert_encoding($venta_f['data']['venta']['nc_serie_y_numero'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_serie_y_numero'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

  //NUMERO A LETRA ============= 
  $total_en_letra = $numero_a_letra->toInvoice($venta_total, 2, " SOLES");

  // Cliente
  $client = (new Client())
    ->setTipoDoc($c_tipo_documento)
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

  $note = new Note();
  $note->setUblVersion('2.1')
    ->setTipoDoc('07')                                            // Tipo Doc: Nota de Credito
    ->setSerie($serie_comprobante)                                // Serie NCR
    ->setCorrelativo($numero_comprobante)                         // Correlativo NCR
    ->setFechaEmision(new DateTime($fecha_emision . '-05:00'))    // Fecha emision NCR
    ->setTipDocAfectado($nc_tipo_comprobante)                     // Tipo Doc: Boleta o Factura
    ->setNumDocfectado($nc_serie_y_numero)                        // Boleta: Serie-Correlativo
    ->setCodMotivo($nc_motivo_nota)                               // Catalogo. 09: Code
    ->setDesMotivo($nc_nombre_motivo)                             // Catalogo. 09: Descripcion de motivo
    ->setTipoMoneda('PEN')
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperGravadas(0)
    ->setMtoOperExoneradas($venta_total)
    ->setMtoOperInafectas(0)
    ->setMtoIGV(0)
    ->setTotalImpuestos(0)
    ->setMtoImpVenta($venta_total);
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

    $um_nombre            = mb_convert_encoding($val['um_nombre'], 'UTF-8', mb_detect_encoding($val['um_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $um_abreviatura       = mb_convert_encoding($val['um_abreviatura'], 'UTF-8', mb_detect_encoding($val['um_abreviatura'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $detail = new SaleDetail();
    $detail->setCodProducto($val['codigo'])
      ->setUnidad($um_abreviatura)
      ->setCantidad($cantidad)
      ->setDescripcion($nombre_producto)
      ->setMtoBaseIgv($subtotal)
      ->setPorcentajeIgv(0)                       # 18% o 0%
      ->setIgv(0)
      ->setTipAfeIgv('20')                        # Exonerado Op. Onerosa - Catalog. 07
      ->setTotalImpuestos(0)                      # Suma de impuestos en el detalle
      ->setMtoValorVenta($subtotal)
      ->setMtoValorUnitario($precio_venta_dcto)
      ->setMtoPrecioUnitario($precio_venta_dcto); # (Valor venta + Total Impuestos) / Cantidad
    $arrayItem[$i] = $detail;
    $i++;
  }


  $note->setDetails($arrayItem)
    ->setLegends([
      (new Legend())
        ->setCode('1000') // Monto en letras - Catalog. 52
        ->setValue($total_en_letra)
    ]);

  /* 
  * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  * Envío a SUNAT
  + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  */

  $result = $see->send($note);
    
  // Guardar XML firmado digitalmente.
  $nombre_xml = '../assets/modulo/facturacion/nota_credito/' . $note->getName() . '.xml';
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
    $nombre_de_cdr = '../assets/modulo/facturacion/nota_credito/R-' . $note->getName() . '.zip';
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
