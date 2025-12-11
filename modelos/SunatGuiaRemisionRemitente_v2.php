<?php

require '../vendor/autoload.php';                   // CONEXION A COMPOSER

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

use Greenter\Data\DocumentGeneratorInterface;
use Greenter\Data\GeneratorFactory;
use Greenter\Data\SharedStore;
use Greenter\Model\DocumentInterface;

use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\Report\XmlUtils;
use Greenter\See;

date_default_timezone_set('America/Lima');

require "../config/Conexion_v2.php";
require_once "../modelos/Facturacion.php";
require '../sunat/Util.php'; 

$facturacion    = new Facturacion();
$numero_a_letra = new NumeroALetras();

$empresa_f  = $facturacion->datos_empresa();
$venta_f    = $facturacion->mostrar_detalle_venta(4); ##echo $rspta['id_tabla']; echo  json_encode($venta_f , true);  die();

$util = Util::getInstance();

if (empty($venta_f['data']['venta'])) {
  echo 'No existe venta';
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

  $gr_idventa_asociada    = empty($venta_f['data']['venta']['gr_idventa_asociada']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_idventa_asociada'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_idventa_asociada'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_modalidad_traslado  = empty($venta_f['data']['venta']['gr_cod_modalidad_traslado']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_cod_modalidad_traslado'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_cod_modalidad_traslado'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_motivo_traslado     = empty($venta_f['data']['venta']['gr_cod_motivo_traslado']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_cod_motivo_traslado'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_cod_motivo_traslado'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

  $gr_chofer_nombre_razonsocial         = empty($venta_f['data']['venta']['gr_chofer_nombre_razonsocial']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_nombre_razonsocial'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_nombre_razonsocial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_apellidos_nombrecomercial  = empty($venta_f['data']['venta']['gr_chofer_apellidos_nombrecomercial']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_apellidos_nombrecomercial'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_apellidos_nombrecomercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_nombre                     = empty($venta_f['data']['venta']['gr_chofer_nombre']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_nombre'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_tipo_documento             = empty($venta_f['data']['venta']['gr_chofer_tipo_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_tipo_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_tipo_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_nombre_tipo_documento      = empty($venta_f['data']['venta']['gr_chofer_nombre_tipo_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_nombre_tipo_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_nombre_tipo_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_chofer_numero_documento           = empty($venta_f['data']['venta']['gr_chofer_numero_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_chofer_numero_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_chofer_numero_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

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
  
  $gr_da_numero_documento = empty($venta_f['data']['venta']['gr_da_numero_documento']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_da_numero_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_da_numero_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_da_serie_numero     = empty($venta_f['data']['venta']['gr_da_serie_numero']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_da_serie_numero'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_da_serie_numero'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
  $gr_da_tipo_comprobante = empty($venta_f['data']['venta']['gr_da_tipo_comprobante']) ? '' : mb_convert_encoding($venta_f['data']['venta']['gr_da_tipo_comprobante'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['gr_da_tipo_comprobante'], "UTF-8, ISO-8859-1, ISO-8859-15", true));


  echo '<li class="list-group-item">e_razon_social: '. $e_razon_social .'</li>';
  echo '<li class="list-group-item">e_comercial: '. $e_comercial .'</li>';
  echo '<li class="list-group-item">e_domicilio_fiscal: '. $e_domicilio_fiscal .'</li>';
  echo '<li class="list-group-item">e_tipo_documento: '. $e_tipo_documento .'</li>';
  echo '<li class="list-group-item">e_numero_documento: '. $e_numero_documento .'</li>';

  echo '<li class="list-group-item">e_distrito: '. $e_distrito .'</li>';
  echo '<li class="list-group-item">e_provincia: '. $e_provincia .'</li>';
  echo '<li class="list-group-item">e_departamento: '. $e_departamento .'</li>';
  echo '<li class="list-group-item">e_codubigueo: '. $e_codubigueo .'</li>';

  echo '<li class="list-group-item">c_nombre_completo: '. $c_nombre_completo .'</li>';
  echo '<li class="list-group-item">c_tipo_documento_nombre: '. $c_tipo_documento_nombre .'</li>';
  echo '<li class="list-group-item">c_tipo_documento: '. $c_tipo_documento .'</li>';
  echo '<li class="list-group-item">c_numero_documento: '. $c_numero_documento .'</li>';
  echo '<li class="list-group-item">c_direccion: '. $c_direccion .'</li>';

  echo '<li class="list-group-item">fecha_emision: '. $fecha_emision .'</li>';
  echo '<li class="list-group-item">serie_comprobante: '. $serie_comprobante .'</li>';
  echo '<li class="list-group-item">numero_comprobante: '. $numero_comprobante .'</li>';
  echo '<li class="list-group-item">venta_total: '. $venta_total .'</li>';

  echo '<li class="list-group-item">gr_modalidad_traslado: '. $gr_modalidad_traslado .'</li>';
  echo '<li class="list-group-item">gr_motivo_traslado: '. $gr_motivo_traslado .'</li>';

  echo '<li class="list-group-item">gr_chofer_nombre_razonsocial: '. $gr_chofer_nombre_razonsocial .'</li>';
  echo '<li class="list-group-item">gr_chofer_apellidos_nombrecomercial: '. $gr_chofer_apellidos_nombrecomercial .'</li>';
  echo '<li class="list-group-item">gr_chofer_nombre: '. $gr_chofer_nombre .'</li>';
  echo '<li class="list-group-item">gr_chofer_tipo_documento: '. $gr_chofer_tipo_documento .'</li>';
  echo '<li class="list-group-item">gr_chofer_nombre_tipo_documento: '. $gr_chofer_nombre_tipo_documento .'</li>';
  echo '<li class="list-group-item">gr_chofer_numero_documento: '. $gr_chofer_numero_documento .'</li>';

  echo '<li class="list-group-item">gr_peso_total: '. $gr_peso_total .'</li>';
  echo '<li class="list-group-item">gr_peso_total_um: '. $gr_peso_total_um .'</li>';
  echo '<li class="list-group-item">gr_placa: '. $gr_placa .'</li>';
  echo '<li class="list-group-item">gr_numero_licencia: '. $gr_numero_licencia .'</li>';
  echo '<li class="list-group-item">gr_partida_direccion: '. $gr_partida_direccion .'</li>';
  echo '<li class="list-group-item">gr_partida_distrito: '. $gr_partida_distrito .'</li>';
  echo '<li class="list-group-item">gr_partida_ubigeo: '. $gr_partida_ubigeo .'</li>';
  echo '<li class="list-group-item">gr_llegada_direccion: '. $gr_llegada_direccion .'</li>';
  echo '<li class="list-group-item">gr_llegada_distrito: '. $gr_llegada_distrito .'</li>';
  echo '<li class="list-group-item">gr_llegada_ubigeo: '. $gr_llegada_ubigeo .'</li>';

  echo '<li class="list-group-item">gr_idventa_asociada: '. $gr_idventa_asociada .'</li>';
  echo '<li class="list-group-item">gr_da_numero_documento: '. $gr_da_numero_documento .'</li>';
  echo '<li class="list-group-item">gr_da_serie_numero: '. $gr_da_serie_numero .'</li>';
  echo '<li class="list-group-item">gr_da_tipo_comprobante: '. $gr_da_tipo_comprobante .'</li>';

  

  //NUMERO A LETRA ============= 
  $total_en_letra = $numero_a_letra->toInvoice($venta_total, 2, " SOLES");

  /* 
  * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  * Guia de Remision
  + ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  */  

  $vehiculoPrincipal = (new Vehicle())->setPlaca('PS8016');

  $chofer = (new Driver())
      ->setTipo('Principal')
      ->setTipoDoc('1')
      ->setNroDoc('45559090')
      ->setLicencia('Y455590901')
      ->setNombres('BREINER')
      ->setApellidos('TAPIA HUAMANTA');

  $envio = new Shipment();
  $envio
      ->setCodTraslado('01') // Cat.20 - Venta
      ->setModTraslado('02') // Cat.18 - Transp. Privado
      ->setFecTraslado(new DateTime())
      ->setPesoTotal(10)
      ->setUndPesoTotal('KGM')
      ->setVehiculo($vehiculoPrincipal)
      ->setChoferes([$chofer])
      ->setLlegada(new Direction('220901', 'JR. SAN MARTIN NRO 207 TARAPOTO - SAN MARTIN'))
      ->setPartida(new Direction('220910', 'JR. INTEGRACION Mz. A LOTE 6'));

  $despatch = new Despatch();
  $despatch->setVersion('2022')
      ->setTipoDoc('09')
      ->setSerie('T001')
      ->setCorrelativo('1')
      ->setFechaEmision(new DateTime())
      ->setCompany($util->getGRECompany())
      ->setDestinatario((new Client())
          ->setTipoDoc('6')
          ->setNumDoc('20542262762')
          ->setRznSocial('AUTOSERVICIOS BIGOTE E.I.R.L'))
      ->setEnvio($envio); 

  $i = 0;
  $arrayItem = [];

  foreach ($venta_f['data']['detalle'] as $key => $val) {

    $codigo_producto      = empty($val['codigo']) ? '': mb_convert_encoding($val['codigo'], 'UTF-8', mb_detect_encoding($val['codigo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $nombre_producto      = empty($val['pr_nombre']) ? '':mb_convert_encoding($val['pr_nombre'], 'UTF-8', mb_detect_encoding($val['pr_nombre'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $cantidad             = floatval($val['cantidad_total']); 
    $um_abreviatura       = empty($val['um_abreviatura']) ? '': mb_convert_encoding($val['um_abreviatura'], 'UTF-8', mb_detect_encoding($val['um_abreviatura'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    echo '<br>';
    echo '<li class="list-group-item">cantidad: '. $cantidad .'</li>';
    echo '<li class="list-group-item">um_abreviatura: '. $um_abreviatura .'</li>';
    echo '<li class="list-group-item">nombre_producto: '. $nombre_producto .'</li>';
    echo '<li class="list-group-item">codigo_producto: '. $codigo_producto .'</li>';

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

/** @var $res SummaryResult */
$ticket = $res->getTicket();
echo 'Ticket :<strong>' . $ticket . '</strong>' . '<br>'; 

// Verificar si la respuesta fue exitosa
if (!$res->isSuccess()) {
    // Si la respuesta no es exitosa, obtén el código de error y mensaje de SUNAT
    $errores = $util->getErrorResponse($res->getError());
    $sunat_error = limpiarCadena($errores['mensaje']);
    $sunat_code = (int)$errores['codigo'];
    $sunat_mensaje = limpiarCadena($errores['mensaje']);
    
    // Manejar los posibles códigos de error
    if ($sunat_code === 0) {
        $sunat_estado = 'RECHAZADA';
    } else if ($sunat_code >= 2000 && $sunat_code <= 3999) {
        $sunat_estado = 'RECHAZADA';
    } else {
        $sunat_estado = 'Excepción: ' . $sunat_code;
    }

    echo 'sunat_error: ' . $sunat_error . '<br>';
    echo 'sunat_code: ' . $sunat_code . '<br>';
    echo 'sunat_mensaje: ' . $sunat_mensaje . '<br>';
    echo 'sunat_estado: ' . $sunat_estado . '<br>';
} else {
    // Si la respuesta es exitosa, revisa el estado del ticket
    $cdrResponse = null;
    $sunat_estado = '';
    
    // Verificar si la respuesta aún está en proceso
    while (true) {
        $res = $api->getStatus($ticket);
        $error = $res->getError();
        
        // Si hay un error, muestra el código y el mensaje
        if ($error !== null) {
            echo 'Código de error: ' . $error->getCode() . '<br>';
            echo 'Mensaje de error: ' . $error->getMessage() . '<br>';
        }
        
        // Verificar si la respuesta está en proceso (estado 98)
        if ($res->getError() && $res->getError()->getCode() == '98') {
            echo 'La solicitud está en proceso, esperando respuesta...<br>';
            sleep(30); // Esperar 30 segundos antes de reintentar
            continue;  // Volver a intentar obtener el estado
        } else {
            // Si la respuesta ya está disponible, procesa el CDR
            $cdrResponse = $res->getCdrResponse();
            if ($cdrResponse !== null) {
                // Si se recibe el CDR, procesar el código
                $sunat_code = (int)$cdrResponse->getCode();
                echo 'Código SUNAT: ' . $sunat_code . '<br>';
                $cdr = $res->getCdrResponse();
                $util->writeCdr($ruta_zip_xml, $despatch, $res->getCdrZip());
                if (count($cdr->getNotes()) > 0) {
                  $sunat_estado = 'ACEPTADA' ;  // Es aceptado por sunat, pero corrgir en la siguientes emisiones
                  // $sunat_observacion = $cdr->getNotes(); # Corregir estas observaciones en siguientes emisiones. var_dump()
                  foreach ($cdr->getNotes() as $key => $val) {
                    $sunat_observacion .= $val == 'CDR de prueba' ? '' : $val . "<br>";
                  }
                }
                break; // Salir del bucle
            } else {
                // Si el CDR no está disponible, muestra el mensaje de error
                echo 'El CDR aún no está disponible o está en proceso.<br>';
                break;
            }
        }
    }
}
 
}
