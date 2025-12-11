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

use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\SoapClient as SunatSoapClient;
use Greenter\Ws\Services\SunatEndpoints; // tiene la constante CONSULT_CDR
use Greenter\Ws\Services\WsdlProvider;

$wsdlPath = WsdlProvider::getConsultPath();             // WSDL local
$soap     = new SunatSoapClient($wsdlPath);
$soap->setService(SunatEndpoints::FE_CONSULTA_CDR);     // location producción
$soap->setCredentials($userSol, $passSol);


date_default_timezone_set('America/Lima');

require "../config/Conexion_v2.php";
$numero_a_letra = new NumeroALetras();

$empresa_f  = $facturacion->datos_empresa();
$venta_f    = $facturacion->mostrar_detalle_venta($f_idventa); ##echo $rspta['id_tabla']; echo  json_encode($venta_f , true);  die();

// === CONFIGURA EL CLIENTE SEE (mismo que usas al enviar) ===


// === DATOS DEL COMPROBANTE YA ENVIADO ===
$rucEmisor          = $empresa_f['data']['numero_documento'];
$tipoComprobante    = $venta_f['data']['venta']['tipo_comprobante']; // Boleta
$serie              = $venta_f['data']['venta']['serie_comprobante'];
$correlativo        = (int)$venta_f['data']['venta']['numero_comprobante'];

try {
    if (in_array($tipoComprobante, ['01','07','08'], true)) {
        // ===== FACTURA/NC/ND: consulta por documento (SOLO PRODUCCIÓN) =====
        // Usuario SOL de producción: "<RUC><USUARIO_SOL>", clave SOL.
        $userSol = SUNAT_RUC . SUNAT_USUARIO_SOL;
        $passSol = SUNAT_CLAVE_SOL;

        $wsdlPath = WsdlProvider::getConsultPath();             // WSDL local
        $soap     = new SunatSoapClient($wsdlPath);
        $soap->setService(SunatEndpoints::FE_CONSULTA_CDR);     // location producción
        $soap->setCredentials($userSol, $passSol);
       
        // $soap = new SunatSoapClient(SunatEndpoints::FE_CONSULTA_CDR); // https prod
        // $soap->setCredentials($userSol, $passSol);

        $service = new ConsultCdrService();
        $service->setClient($soap);

        // Puedes usar getStatus() o getStatusCdr(); este último intenta devolverte el ZIP.
        $result = $service->getStatusCdr($rucEmisor, $tipoComprobante, $serie, $correlativo);

    } elseif ($tipoComprobante === '03') {
        // ===== BOLETA: consulta por ticket del Resumen Diario (RC) =====
        // Debiste guardar el ticket al enviar el RC.
        $ticket = $venta_f['data']['venta']['ticket_rc'] ?? '';
        if (empty($ticket)) {
            throw new \RuntimeException('No se encontró el ticket del Resumen Diario (RC) para esta boleta.');
        }

        // Tu objeto $see ya está configurado (SunatCertificado.php). Aquí SÍ se usa getStatus(ticket).
        $result = $see->getStatus($ticket);

    } else {
        throw new \RuntimeException("Tipo de comprobante $tipoComprobante no soportado para consulta.");
    }

    // ===== EVALUACIÓN DE RESULTADO =====
    if ($result->isSuccess()) {
        $cdr = $result->getCdrResponse();
        $sunat_code    = (int)$cdr->getCode();
        $sunat_mensaje = $cdr->getDescription();

        if ($sunat_code === 0) {
            $sunat_estado = 'ACEPTADA';
        } elseif ($sunat_code >= 2000 && $sunat_code <= 3999) {
            $sunat_estado = 'RECHAZADA';
        } else {
            $sunat_estado = 'EXCEPCIÓN: ' . $sunat_code;
        }

        if (!empty($cdr->getNotes())) {
            foreach ($cdr->getNotes() as $n) {
                $sunat_observacion .= $n . "<br>";
            }
        }

        // Hash (si tienes el XML guardado)
        $nombre_xml = '';
        if ($tipoComprobante == '01') {
            $nombre_xml = "../assets/modulo/facturacion/factura/{$rucEmisor}-$tipoComprobante-$serie-$correlativo.xml";
        } elseif ($tipoComprobante == '03') {
            $nombre_xml = "../assets/modulo/facturacion/boleta/{$rucEmisor}-$tipoComprobante-$serie-$correlativo.xml";
        } elseif ($tipoComprobante == '07') {
            $nombre_xml = "../assets/modulo/facturacion/nota_credito/{$rucEmisor}-$tipoComprobante-$serie-$correlativo.xml";
        }

        if ($nombre_xml && file_exists($nombre_xml)) {
            $parser    = new \Greenter\Ws\Reader\XmlReader();
            $documento = $parser->getDocument(file_get_contents($nombre_xml));
            $sunat_hash = $documento->getElementsByTagName('DigestValue')->item(0)->nodeValue ?? '';
        }

    } else {
        $error        = $result->getError();
        $sunat_code   = $error->getCode();
        $sunat_error  = "Codigo Error: {$error->getCode()} - {$error->getMessage()}";
        $sunat_estado = ($sunat_code == 1033) ? 'DUPLICADO' : 'ERROR';
        $sunat_mensaje = $error->getMessage();
    }
} catch (\Throwable $e) {
    // helper mínimo para escapar a HTML
    $esc = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

    // datos básicos
    $tipo    = get_class($e);
    $codigo  = $e->getCode();
    $mensaje = $e->getMessage();
    $archivo = $e->getFile();
    $linea   = $e->getLine();

    // previous (solo 1 nivel, opcional)
    $prevTxt = '';
    if ($e->getPrevious()) {
        $p = $e->getPrevious();
        $prevTxt = 'Causa: ' . get_class($p) . ' (#' . $p->getCode() . '): ' . $p->getMessage();
    }

    // trace compacto (primeras 2 líneas)
    $traceLines = explode("\n", $e->getTraceAsString());
    $traceShort = implode("\n", array_slice($traceLines, 0, 2));

    // construir para UI (con <br>)
    $sunat_error =
          'Excepción: ' . $esc($tipo)
        . ' (#' . $esc($codigo) . ')<br>'
        . 'Mensaje: ' . $esc($mensaje) . '<br>'
        . 'Ubicación: ' . $esc($archivo) . ':' . $esc($linea) . '<br>'
        . (!empty($prevTxt) ? $esc($prevTxt) . '<br>' : '')
        . 'Trace: ' . nl2br($esc($traceShort));
    $sunat_estado = 'ERROR';
}