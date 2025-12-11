
<?php 

// :::::::::: CONEXION A SUNAT -- TEST ::::::::::

defined('SUNAT_MODO')         || define('SUNAT_MODO', 'TEST');
defined('SUNAT_CERTIFICADO')  || define('SUNAT_CERTIFICADO', '../assets/certificado/certificado_demo.pem');

defined('SUNAT_RUC')          || define('SUNAT_RUC', '20000000001D');
defined('SUNAT_RAZON_SOCIAL') || define('SUNAT_RAZON_SOCIAL', 'GREEN SAC');
defined('SUNAT_USUARIO_SOL')  || define('SUNAT_USUARIO_SOL', 'NOMBLOI');
defined('SUNAT_CLAVE_SOL')    || define('SUNAT_CLAVE_SOL', 'psdlbmrt');

defined('SUNAT_API_USER')     || define('SUNAT_API_USER', 'aad1-85e5b0ae-255c-4891-a595-0b98c65c9854');
defined('SUNAT_API_CLAVE')    || define('SUNAT_API_CLAVE', 'Hty/M6QshYvPgItX2P0+Kw==');

// :::::::::: CONEXION A SUNAT -- PRODUCCION ::::::::::

// defined('SUNAT_MODO')         || define('SUNAT_MODO', 'PRODUCCION');
// defined('SUNAT_CERTIFICADO')  || define('SUNAT_CERTIFICADO', '../assets/certificado/certificate.pem');

// defined('SUNAT_RUC')          || define('SUNAT_RUC', '20611208694');
// defined('SUNAT_RAZON_SOCIAL') || define('SUNAT_RAZON_SOCIAL', 'GRUPO NOVEDADES D & S S.A.C.');
// defined('SUNAT_USUARIO_SOL')  || define('SUNAT_USUARIO_SOL', 'JDLTEC25');
// defined('SUNAT_CLAVE_SOL')    || define('SUNAT_CLAVE_SOL', 'JdlTec2025');

// defined('SUNAT_API_USER')     || define('SUNAT_API_USER', '');
// defined('SUNAT_API_CLAVE')    || define('SUNAT_API_CLAVE', '');

?>