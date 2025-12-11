<?php

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;

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
$venta_f    = $facturacion->mostrar_detalle_venta($rspta['id_tabla']);  # echo  json_encode($venta_f );  die();

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
  $c_tipo_documento_name = $venta_f['data']['venta']['tipo_documento_abreviatura'];
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
  $note
    ->setUblVersion('2.1')
    ->setTipoDoc('07')
    ->setSerie('BC03')
    ->setCorrelativo('123')
    ->setFechaEmision(new DateTime())
    ->setTipDocAfectado('03') // Tipo Doc: Factura
    ->setNumDocfectado('B001-111') // Factura: Serie-Correlativo
    ->setCodMotivo('07') // Catalogo. 09
    ->setDesMotivo('DEVOLUCION POR ITEM')
    ->setTipoMoneda('PEN')
    
    ->setCompany($company)
    ->setClient($client )
    ->setMtoOperGravadas(200)
    ->setMtoIGV(36)
    ->setTotalImpuestos(36)
    ->setMtoImpVenta(236);

  $detail1 = new SaleDetail();
  $detail1
    ->setCodProducto('C023')
    ->setUnidad('NIU')
    ->setCantidad(2)
    ->setDescripcion('PROD 1')
    ->setMtoBaseIgv(100)
    ->setPorcentajeIgv(18.00)
    ->setIgv(18)
    ->setTipAfeIgv('10')
    ->setTotalImpuestos(18)
    ->setMtoValorVenta(100)
    ->setMtoValorUnitario(50)
    ->setMtoPrecioUnitario(56);

  $detail2 = new SaleDetail();
  $detail2
    ->setCodProducto('C02')
    ->setUnidad('NIU')
    ->setCantidad(2)
    ->setDescripcion('PROD 2')
    ->setMtoBaseIgv(100)
    ->setPorcentajeIgv(18.00)
    ->setIgv(18)
    ->setTipAfeIgv('10')
    ->setTotalImpuestos(18)
    ->setMtoValorVenta(100)
    ->setMtoValorUnitario(50)
    ->setMtoPrecioUnitario(56);

  $legend = new Legend();
  $legend->setCode('1000')
    ->setValue('SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES');

  $note->setDetails([$detail1, $detail2])
    ->setLegends([$legend]);

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
    if ($code === 0) {
      $sunat_estado = 'ACEPTADA' . PHP_EOL;
    } else if ($code >= 2000 && $code <= 3999) {
      $sunat_estado = 'RECHAZADA' . PHP_EOL;
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
      $sunat_estado = 'ACEPTADA' . PHP_EOL;
      if (count($cdr->getNotes()) > 0) {
        $sunat_estado = 'OBSERVACIONES' . PHP_EOL;
        // $sunat_observacion = $cdr->getNotes(); # Corregir estas observaciones en siguientes emisiones. var_dump()
        foreach ($cdr->getNotes() as $key => $val) {
          $sunat_observacion .= $val . "<br>";
        }
      }
    } else if ($code >= 2000 && $code <= 3999) {
      $sunat_estado = 'RECHAZADA' . PHP_EOL;
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $code;
    }
    $sunat_code = $code;
    $sunat_mensaje = $cdr->getDescription() . PHP_EOL;

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
