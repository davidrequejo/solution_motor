
<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

$see = new See();
$see->setCertificate(file_get_contents(SUNAT_CERTIFICADO));
if (SUNAT_MODO === 'TEST') {
  $see->setService(SunatEndpoints::FE_BETA);
} else if (SUNAT_MODO === 'PRODUCCION') {
  $see->setService(SunatEndpoints::FE_PRODUCCION);
}
$see->setClaveSOL(SUNAT_RUC, SUNAT_USUARIO_SOL, SUNAT_CLAVE_SOL);

return $see;