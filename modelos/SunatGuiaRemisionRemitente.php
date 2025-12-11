<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

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

use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Response\CdrResponse;

use Greenter\Model\Despatch\Vehicle;
use Greenter\Model\Despatch\Driver;

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
  $c_tipo_documento_nombre     = $venta_f['data']['venta']['tipo_documento_abreviatura'];
  $c_tipo_documento        = $venta_f['data']['venta']['tipo_documento'];
  $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
  $c_direccion          = $venta_f['data']['venta']['direccion'];

  $fecha_emision        = $venta_f['data']['venta']['fecha_emision'];
  $serie_comprobante    = $venta_f['data']['venta']['serie_comprobante'];
  $numero_comprobante   = $venta_f['data']['venta']['numero_comprobante'];
  $venta_total          = floatval($venta_f['data']['venta']['venta_total']);

  $gr_modalidad_traslado = empty($venta_f['data']['venta']['gr_cod_modalidad_traslado']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_cod_modalidad_traslado'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_cod_modalidad_traslado'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_motivo_traslado   = empty($venta_f['data']['venta']['gr_cod_motivo_traslado']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_cod_motivo_traslado'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_cod_motivo_traslado'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

  $gr_chofer_nombre_razonsocial               = empty($venta_f['data']['venta']['gr_chofer_nombre_razonsocial']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_nombre_razonsocial'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_nombre_razonsocial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_apellidos_nombrecomercial               = empty($venta_f['data']['venta']['gr_chofer_apellidos_nombrecomercial']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_apellidos_nombrecomercial'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_apellidos_nombrecomercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_nombre               = empty($venta_f['data']['venta']['gr_chofer_nombre']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_nombre'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_tipo_documento       = empty($venta_f['data']['venta']['gr_chofer_tipo_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_tipo_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_tipo_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_nombre_tipo_documento = empty($venta_f['data']['venta']['gr_chofer_nombre_tipo_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_nombre_tipo_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_nombre_tipo_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_numero_documento     = empty($venta_f['data']['venta']['gr_chofer_numero_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_numero_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_numero_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

  $gr_peso_total        = empty($venta_f['data']['venta']['gr_peso_total']) ? '0.00' : $venta_f['data']['venta']['gr_peso_total'];
  $gr_peso_total_um     = empty($venta_f['data']['venta']['gr_peso_total_um']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_peso_total_um'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_peso_total_um'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_placa             = empty($venta_f['data']['venta']['gr_placa_no_guion']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_placa_no_guion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_placa_no_guion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_numero_licencia   = empty($venta_f['data']['venta']['gr_numero_licencia']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_numero_licencia'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_numero_licencia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_partida_direccion = empty($venta_f['data']['venta']['gr_partida_direccion']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_partida_direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_partida_direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_partida_distrito  = empty($venta_f['data']['venta']['gr_partida_distrito']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_partida_distrito'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_partida_distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_partida_ubigeo    = empty($venta_f['data']['venta']['gr_partida_ubigeo']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_partida_ubigeo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_partida_ubigeo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_llegada_direccion = empty($venta_f['data']['venta']['gr_llegada_direccion']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_llegada_direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_llegada_direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_llegada_distrito  = empty($venta_f['data']['venta']['gr_llegada_distrito']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_llegada_distrito'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_llegada_distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_llegada_ubigeo    = empty($venta_f['data']['venta']['gr_llegada_ubigeo']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_llegada_ubigeo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_llegada_ubigeo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));



//   echo '<li class="list-group-item">fecha_emision: '. $fecha_emision .'</li>';
//   echo '<li class="list-group-item">serie_comprobante: '. $serie_comprobante .'</li>';
//   echo '<li class="list-group-item">numero_comprobante: '. $numero_comprobante .'</li>';
//   echo '<li class="list-group-item">venta_total: '. $venta_total .'</li>';
//   echo '<li class="list-group-item">gr_modalidad_traslado: '. $gr_modalidad_traslado .'</li>';
//   echo '<li class="list-group-item">gr_motivo_traslado: '. $gr_motivo_traslado .'</li>';
//   echo '<li class="list-group-item">gr_chofer_nombre_razonsocial: '. $gr_chofer_nombre_razonsocial .'</li>';
//   echo '<li class="list-group-item">gr_chofer_apellidos_nombrecomercial: '. $gr_chofer_apellidos_nombrecomercial .'</li>';
//   echo '<li class="list-group-item">gr_chofer_nombre: '. $gr_chofer_nombre .'</li>';
//   echo '<li class="list-group-item">gr_chofer_tipo_documento: '. $gr_chofer_tipo_documento .'</li>';
//   echo '<li class="list-group-item">gr_chofer_nombre_tipo_documento: '. $gr_chofer_nombre_tipo_documento .'</li>';
//   echo '<li class="list-group-item">gr_chofer_numero_documento: '. $gr_chofer_numero_documento .'</li>';
//   echo '<li class="list-group-item">gr_peso_total: '. $gr_peso_total .'</li>';
//   echo '<li class="list-group-item">gr_peso_total_um: '. $gr_peso_total_um .'</li>';
//   echo '<li class="list-group-item">gr_placa: '. $gr_placa .'</li>';
//   echo '<li class="list-group-item">gr_numero_licencia: '. $gr_numero_licencia .'</li>';
//   echo '<li class="list-group-item">gr_partida_direccion: '. $gr_partida_direccion .'</li>';
//   echo '<li class="list-group-item">gr_partida_ubigeo: '. $gr_partida_ubigeo .'</li>';
//   echo '<li class="list-group-item">gr_llegada_direccion: '. $gr_llegada_direccion .'</li>';
//   echo '<li class="list-group-item">gr_llegada_ubigeo: '. $gr_llegada_ubigeo .'</li>';

//   die();

  //NUMERO A LETRA ============= 
  $total_en_letra = $numero_a_letra->toInvoice($venta_total, 2, " SOLES");

  /* 
  * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  * Guia de Remision
  + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  */
  $util = Util::getInstance();

  $company = (new Company())
    ->setRuc($e_numero_documento)
    ->setRazonSocial($e_razon_social);
  $transp = new Transportist();
  $vehiculoPrincipal = new Vehicle();
  $chofer = new Driver();

  if ($gr_modalidad_traslado === "01") {

    $transp->setTipoDoc($gr_chofer_tipo_documento)
      ->setNumDoc($gr_chofer_numero_documento)
      ->setRznSocial($gr_chofer_nombre);
      // ->setNroMtc('0001'); // Establece el número del registro del transportista ante el Ministerio de Transportes y Comunicaciones (MTC) de Perú, si corresponde

  } else if ($gr_modalidad_traslado === "02") {

    $vehiculoPrincipal->setPlaca($gr_placa);
    $chofer->setTipo('Principal')
      ->setTipoDoc($gr_chofer_tipo_documento)
      ->setNroDoc($gr_chofer_numero_documento)
      ->setLicencia($gr_numero_licencia)
      ->setNombres($gr_chofer_nombre_razonsocial)
      ->setApellidos($gr_chofer_apellidos_nombrecomercial);
  }

  $envio = new Shipment();
  $envio
    ->setCodTraslado($gr_motivo_traslado) // Cat.20 - Venta
    ->setModTraslado($gr_modalidad_traslado) // Cat.18 - Transp. Publico
    ->setFecTraslado(new DateTime($fecha_emision . '-05:00'))
    ->setPesoTotal($gr_peso_total)
    ->setUndPesoTotal('KGM')
    //    ->setNumBultos(2) // Solo válido para importaciones
    ->setLlegada(new Direction($gr_partida_ubigeo, $gr_partida_direccion))
    ->setPartida(new Direction($gr_llegada_ubigeo, $gr_llegada_direccion)) ;

  if ($gr_modalidad_traslado === "01") {
    $envio->setTransportista($transp);
  } else if ($gr_modalidad_traslado === "02") {
    $envio->setVehiculo($vehiculoPrincipal)
      ->setChoferes([$chofer]);
  }
  

  $despatch = new Despatch();
  $despatch->setVersion('2022')
    ->setTipoDoc('09')
    ->setSerie($serie_comprobante)
    ->setCorrelativo( $numero_comprobante)
    ->setFechaEmision(new DateTime($fecha_emision. '-05:00'))
    ->setCompany($util->getGRECompany())
    ->setDestinatario((new Client())
      ->setTipoDoc($c_tipo_documento)
      ->setNumDoc($c_numero_documento)
      ->setRznSocial($c_nombre_completo))
    ->setEnvio($envio);

  $i = 0;
  $arrayItem = [];

  foreach ($venta_f['data']['detalle'] as $key => $val) {

    $codigo_producto      = empty($val['codigo']) ? '': mb_convert_encoding($val['codigo'], 'UTF-8', mb_detect_encoding($val['codigo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $nombre_producto      = empty($val['pr_nombre']) ? '':mb_convert_encoding($val['pr_nombre'], 'UTF-8', mb_detect_encoding($val['pr_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $cantidad             = floatval($val['cantidad_total']); 
    $um_abreviatura       = empty($val['um_abreviatura']) ? '': mb_convert_encoding($val['um_abreviatura'], 'UTF-8', mb_detect_encoding($val['um_abreviatura'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $detail = new DespatchDetail();
    $detail->setCantidad($cantidad)
      ->setUnidad($um_abreviatura)
      ->setDescripcion($nombre_producto)
      ->setCodigo($codigo_producto);

    $arrayItem[$i] = $detail;
    $i++;
  }

  $despatch->setDetails($arrayItem);
  /* 
  * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  * Envío a SUNAT
  + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  */
  $ruta_zip_xml = '../assets/modulo/facturacion/guia_remision_remitente/';
  // Envio a SUNAT.
  $api = $util->getSeeApi();
  $res = $api->send($despatch);

  $util->writeXml($ruta_zip_xml, $despatch, $api->getLastXml());  

  /**@var $res SummaryResult*/
  $ticket = $res->getTicket();
  //echo 'Ticket :<strong>' . $ticket . '</strong>' . '<br>'; //die();

  if (!$res->isSuccess()) {

    $errores = $util->getErrorResponse($res->getError());
    $sunat_error = limpiarCadena($errores['mensaje']);
    $sunat_code = (int)$errores['codigo'];
    $sunat_mensaje = limpiarCadena($errores['mensaje']);
    if ($sunat_code === 0) {
      $sunat_estado = 'RECHAZADA';
    } else if ($sunat_code >= 2000 && $sunat_code <= 3999) {
      $sunat_estado = 'RECHAZADA';
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*$sunat_code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $sunat_code;
    }
    // echo 'sunat_error: ' . $sunat_error . '<br>';
    // echo 'sunat_code: ' . $sunat_code . '<br>';
    // echo 'sunat_mensaje: ' . $sunat_mensaje . '<br>';
    // echo 'sunat_estado: ' . $sunat_estado . '<br>';
    // return;
  }else {
    $res = $api->getStatus($ticket);
    $cdr = $res->getCdrResponse();
    $util->writeCdr($ruta_zip_xml, $despatch, $res->getCdrZip());

    $sunat_code = (int)$cdr->getCode() ;
    if ($sunat_code === 0) {
      $sunat_estado = 'ACEPTADA';
      if (count($cdr->getNotes()) > 0) {
        $sunat_estado = 'ACEPTADA' ;  // Es aceptado por sunat, pero corrgir en la siguientes emisiones
        // $sunat_observacion = $cdr->getNotes(); # Corregir estas observaciones en siguientes emisiones. var_dump()
        foreach ($cdr->getNotes() as $key => $val) {
          $sunat_observacion .= $val == 'CDR de prueba' ? '' : $val . "<br>";
        }
      }
    } else if ($sunat_code >= 2000 && $sunat_code <= 3999) {
      $sunat_estado = 'RECHAZADA';
    } else {
      /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
      /*sunat_code: 0100 a 1999 */
      $sunat_estado = 'Excepción: ' . $sunat_code;
    }
  
    // $util->showResponse($despatch, $cdr);
    $sunat_mensaje = limpiarCadena('La Guia numero '.$cdr->getId().', ha sido'. $cdr->getDescription()) ;
  
    // echo 'getId:' . $cdr->getId() . '<br>';
    // echo 'sunat_estado:' . $sunat_estado . '<br>';
    // echo 'sunat_code:' . $sunat_code . '<br>';
    // echo 'sunat_mensaje:' . $sunat_mensaje . '<br>';
    // echo 'sunat_observacion:' . $sunat_observacion . '<br>';
    // echo 'getName:' . $despatch->getName() . '<br>'; 
    

    /* 
    * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    * Lectura del codgo Hash 
    + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    */

    $parser = new XmlReader();
    $archivoXml = file_get_contents($ruta_zip_xml . $despatch->getName().'.xml');
    $documento = $parser->getDocument($archivoXml);
    $sunat_hash = $documento->getElementsByTagName('DigestValue')->item(0)->nodeValue;
    // echo 'sunat_hash:' . $sunat_hash . '<br>';
  }
  
}
