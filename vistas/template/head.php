<?php
$scheme_host  =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/solution_motor/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');
?>

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-3PQPPN872C"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-3PQPPN872C');
  </script>

<!-- Meta Data -->
<meta charset="UTF-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Language" content="es">

<title><?php echo $title_page; ?> | Facturación Libreria sistema</title>

<meta name="description" content="Novedades D&S: Tu tienda de útiles escolares, artículos para piñatas, pelotas, guitarritas de plástico y más. Encuentra todo para tus fiestas y escuela en un solo lugar. ¡Compra ahora y prepara celebraciones inolvidables!">
<meta name="keywords" content="Novedades D&S, útiles escolares, artículos para piñatas, pelotas, guitarritas de plástico, fiestas, cumpleaños, juguetes, tienda de fiestas, material escolar, productos para escuela">
<meta name="author" content="JDL TECNOLOGY SAC">
<meta name="robots" content="index, follow">
<!-- FACEBOOK -->
<meta property="og:title" content="Novedades D&S - Útiles escolares y artículos para fiestas">
<meta property="og:description" content="Descubre todo en útiles escolares, artículos para piñatas, pelotas y más en Novedades D&S. ¡Prepara las mejores fiestas y actividades escolares con nosotros!">
<meta property="og:image" content="http://novedadesdys.jdl.pe/assets/images/brand-logos/desktop-white.png">
<meta property="og:url" content="http://novedadesdys.jdl.pe">
<!-- TWITTER -->
<!-- <meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@nombre_de_usuario_de_twitter"> -->
<meta name="twitter:title" content="Novedades D&S - Útiles escolares y artículos para fiestas">
<meta name="twitter:description" content="Encuentra útiles escolares, artículos para piñatas, pelotas y guitarritas de plástico en Novedades D&S. Todo para tus celebraciones y escuela.">
<meta name="twitter:image" content="http://novedadesdys.jdl.pe/assets/images/brand-logos/desktop-white.png">

<script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Organization",
    "name": "Novedades D&C",
    "url": "http://novedadesdys.jdl.pe",
    "description": "Tienda especializada en útiles escolares, artículos para piñatas, pelotas, guitarritas de plástico y más. Todo para tus fiestas y escuela en un solo lugar.",
    "logo": "http://novedadesdys.jdl.pe/assets/images/brand-logos/desktop-white.png",
    "sameAs": [
      "https://www.facebook.com/novedadesdys",
      "https://www.instagram.com/novedadesdys"
    ],
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+51-921-487-276",
      "contactType": "Customer Service",
      "areaServed": "PE",
      "availableLanguage": "Spanish"
    },
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Av. Ejemplo 123",
      "addressLocality": "Lima",
      "addressRegion": "Lima",
      "postalCode": "15001",
      "addressCountry": "PE"
    },
    "openingHours": "Mo-Su 09:00-18:00",
    "paymentAccepted": "Cash, Credit Card, Debit Card",
    "priceRange": "$$",
    "product": {
      "@type": "Product",
      "name": "Artículos para fiestas y útiles escolares",
      "image": "http://novedadesdys.jdl.pe/assets/images/productos-fiesta-utiles.jpg",
      "description": "Amplia variedad de útiles escolares, artículos para piñatas, pelotas, guitarritas de plástico y más para todas tus necesidades escolares y de fiesta.",
      "brand": {
        "@type": "Brand",
        "name": "Novedades D&C"
      },
      "offers": {
        "@type": "Offer",
        "url": "http://novedadesdys.jdl.pe/productos",
        "priceCurrency": "PEN",
        "price": "Varía según el producto",
        "itemCondition": "http://schema.org/NewCondition",
        "availability": "http://schema.org/InStock"
      }
    }
  }
</script>

<link rel="canonical" href="http://novedadesdys.jdl.pe">

<meta name="msapplication-navbutton-color" content="#444">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<link rel="manifest" href="<?php echo $scheme_host; ?>assets/images/app-download/manifest.json?v=<?php echo date('ymd'); ?>">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="../assets/images/brand-logos/toggle-dark">
<meta name="theme-color" content="#ffffff">

<!-- Favicon -->
<link rel="icon" href="../assets/images/brand-logos/ico-novedades-dys.png" type="image/x-icon">

<!-- Font Awesome 6.2 -->
<link rel="stylesheet" href="../assets/libs/fontawesome-free-6.2.0/css/all.min.css" />

<!-- Choices JS -->
<script src="../assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- Main Theme Js -->
<script src="../assets/js/main.js"></script>

<!-- Bootstrap Css -->
<link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Style Css -->
<link href="../assets/css/styles.min.css" rel="stylesheet">
<!-- <link href="../assets/css/styles.css" rel="stylesheet"> -->

<!-- Icons Css -->
<link href="../assets/css/icons.css" rel="stylesheet">

<!-- Node Waves Css -->
<link href="../assets/libs/node-waves/waves.min.css" rel="stylesheet">

<!-- Simplebar Css -->
<link href="../assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

<!-- Color Picker Css -->
<link rel="stylesheet" href="../assets/libs/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css">

<!-- Choices Css -->
<link rel="stylesheet" href="../assets/libs/choices.js/public/assets/styles/choices.min.css">

<!-- :::::::::::::::::: P L U G I N   D I F E R E N T E S   A L   P R O Y E C T O :::::::::::::::::: -->

<!-- Data Table -->
<link href="../assets/libs/data-table/datatables.css" rel="stylesheet">
<!-- <link href="../assets/libs/data-table/colResize-1.7.2/jquery.dataTables.colResize.css" rel="stylesheet"> -->

<!-- Select2 -->
<link rel="stylesheet" href="../assets/libs/select2/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- Toastr -->
<link rel="stylesheet" href="../assets/libs/toastr/toastr.min.css">

<!-- Mi stylo -->
<link href="../assets/css/style_new.css" rel="stylesheet">