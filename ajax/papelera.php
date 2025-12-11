<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['empresa'] == 1) {
    
    require_once "../modelos/Papelera.php";
    $papelera = new Papelera();

    date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/user_default.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';
    $scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/fun_route/admin/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/admin/');


    // DATA
    $nombre_tabla     = isset($_GET["nombre_tabla"])? limpiarCadena($_GET["nombre_tabla"]):"";
    $nombre_id_tabla 	= isset($_GET["nombre_id_tabla"])? limpiarCadena($_GET["nombre_id_tabla"]):""; 
    $id_tabla 		    = isset($_GET["id_tabla"])? limpiarCadena($_GET["id_tabla"]):"";

    switch ($_GET["op"]) {

      case 'listar_tbla_papelera':       

        $rspta = $papelera->tabla_papelera();
    
        $data = [];
        $cont = 1; 

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';
    
          if ($rspta['status'] == true) {
            foreach ($rspta['data'] as $key => $value) {            
              $info = "'" . $value['nombre_tabla'] . "', '" . $value['nombre_id_tabla'] . "', '" . $value['id_tabla'] . "'";
              
              // Determinamos si nombre_archivo es text o URL
              $nombre_arc = '';
              $imgSize = @getimagesize($value['nombre_archivo']);
              if ($imgSize !== false) {
                  // SI nombre_archivo es equivalente a una URL
                  $nombre_arc = '<img src="' . $value['nombre_archivo'] . '" width="100px" height="auto" alt="Image">';
              } else {
                  // SI nombre_archivo es equivalente a TEXTO
                  $nombre_arc = '<div class="bg-light" style="overflow: auto; resize: vertical; height: 45px;"> ' . $value['nombre_archivo'] . '</div>';
              }
      
              $data[] = [
                  "0" => $cont++,
                  "1" => '<button class="btn btn-icon btn-sm btn-success-light" onclick="recuperar(' . $info . ')" data-toggle="tooltip" data-original-title="Recuperar"><i class="ri-refresh-line"></i></button>' .
                        ' <button class="btn btn-icon btn-sm btn-danger-light" onclick="eliminar_permanente(' . $info . ')" data-toggle="tooltip" data-original-title="Eliminar Permanente"><i class="ri-delete-bin-line"></i></button>', 
                  "2" => '<span class="text-bold">' . $value['modulo'] . '</span>',  
                  "3" => $nombre_arc,  
                  "4" => $value['descripcion'],
                  "5" => nombre_dia_semana(date("Y-m-d", strtotime($value['created_at']))) . ', <br>' . date("d/m/Y", strtotime($value['created_at'])) . ' - ' . date("g:i a", strtotime($value['created_at'])),
                  "6" => nombre_dia_semana(date("Y-m-d", strtotime($value['updated_at']))) . ', <br>' . date("d/m/Y", strtotime($value['updated_at'])) . ' - ' . date("g:i a", strtotime($value['updated_at'])) . $toltip,
                  "7" => $value['list_archivo']
              ];
          }
      
          $results = [
              'status' => true,
              "sEcho" => 1, //Información para el datatables
              "iTotalRecords" => count($data), //enviamos el total registros al datatable
              "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
              "aaData" => $data,
          ];
      
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }
      break;
    
      case 'recuperar':
        $rspta=$papelera->recuperar($nombre_tabla, $nombre_id_tabla, $id_tabla);
        echo json_encode( $rspta, true) ;
      break;

      case 'eliminar_permanente':
        $rspta=$papelera->eliminar_permanente( $nombre_tabla, $nombre_id_tabla, $id_tabla );
        echo json_encode($rspta, true) ;
      break;



    }

  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  

}
ob_end_flush();