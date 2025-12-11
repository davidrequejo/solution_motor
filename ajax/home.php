<?php
require_once "../sunat/ConfigSunat.php";

ob_start();

  require_once "../modelos/Home.php";
  $home = new Home();

  switch ($_GET["op"]) {
    
    case 'mostrar_comentarioC':
      $rspta = $home->mostrar_comentarioC();
      echo json_encode($rspta, true);

    break;

    case 'mostrar_tecnico_redes':
      $rspta = $home->mostrar_tecnico_redes();
      echo json_encode($rspta, true);

    break;

    case 'mostrar_planes':
      $rspta = $home->mostrar_planes();
      echo json_encode($rspta, true);

    break;

    case 'mostrar_preguntas_frecuentes':
      $rspta = $home->mostrar_preguntas_frecuentes();
      echo json_encode($rspta, true);

    break;

  }

ob_end_flush();