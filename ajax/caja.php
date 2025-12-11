<?php
  require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['caja'] == 1) {
    
    require_once "../modelos/Caja.php";
    $caja = new Caja();

    $idcaja     = isset($_POST["idcaja"]) ? limpiarCadena($_POST["idcaja"]) : "";

    $f_inicio   = isset($_POST["f_inicio"]) ? limpiarCadena($_POST["f_inicio"]) : "";
    $m_inicio   = isset($_POST["m_inicio"]) ? limpiarCadena($_POST["m_inicio"]) : "";
    $f_cierre   = isset($_POST["f_cierre"]) ? limpiarCadena($_POST["f_cierre"]) : "";
    $sw_auto    = isset($_POST["sw_auto"]) ? limpiarCadena($_POST["sw_auto"]) : "";


    switch ($_GET["op"]) {

      case 'listar_tabla_caja':
        $rspta = $caja->listar_caja();

        $data = [];
        $cont = 1;

        $dias = ['Mon'=>'Lun','Tue'=>'Mar','Wed'=>'Mié','Thu'=>'Jue','Fri'=>'Vie','Sat'=>'Sáb','Sun'=>'Dom'];
        $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio', 'July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            // Formateamos la Fecha de INICIO
            $fecha_i = isset($value['fecha_inicio']) ? new DateTime($value['fecha_inicio']) : new DateTime();
            $dia_en = $fecha_i->format('D'); $mes_en = $fecha_i->format('F');
            $horaI_formateada  = $fecha_i->format('H\H : i\M');
            $solo_fecha_i = $fecha_i->format('Y-m-d'); $solo_hora_i  = $fecha_i->format('H:i:s');
            $fechaI_es = $dias[$dia_en] . ' ' . $fecha_i->format('d') . ', ' . $meses[$mes_en] . ' ' . $fecha_i->format('Y');

            // Formateamos la Fecha de FIN
            $fecha_f = isset($value['fecha_fin']) ? new DateTime($value['fecha_fin']) : new DateTime();
            $dia1_en = $fecha_f->format('D'); $mes1_en = $fecha_f->format('F');
            $horaF_formateada  = $fecha_f->format('H\H : i\M');
            $solo_fecha_f = $fecha_f->format('Y-m-d'); $solo_hora_f  = $fecha_f->format('H:i:s');
            $fechaF_es = $dias[$dia1_en] . ' ' . $fecha_f->format('d') . ', ' . $meses[$mes1_en] . ' ' . $fecha_f->format('Y');

            $data[] = array(
              "0" => $cont++,
              "1" => ($value['estado_caja'] == 'ABIERTO' ? ' <button  class="btn btn-sm btn-danger-light border-danger" onclick="mostrar_caja(' . $value['idcaja'] . '); show_hide_form(2);" data-bs-toggle="tooltip" title="Cerrar">Cerrar</button>' : 
                '<button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_caja(' . $value['idcaja'] . '); show_hide_form(3);" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light border-danger product-btn" onclick="eliminar_papelera_caja(' . $value['idusuario'] . ',' . $value['idcaja'] . ', \'' . encodeCadenaHtml($value['fecha_inicio']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'),         
              "2" => $fechaI_es . ' →  ' . $horaI_formateada,
              "3" => ($value['estado_caja'] == 'CERRADO' && !empty($value['fecha_fin']) && !is_null($value['fecha_fin'])
                        ? $fechaF_es . ' → ' . $horaF_formateada
                        : ($value['estado_monto_cierre'] == 'AUTOMATICO'
                            ? '<span class="badge bg-info-transparent text-info"><i class="ri-settings-3-line align-middle me-1"></i>Automático</span>'
                            : '<span class="badge bg-secondary-transparent text-secondary"><i class="ri-edit-line align-middle me-1"></i>Manual</span>'
                          )
                      ),
              "4" => $value['monto_apertura'],
              "5" => ($value['monto_cierre'] == '0.00' ? '<span class="badge bg-warning-transparent text-warning"><i class="ri-time-line align-middle me-1"></i>Pendiente</span>' : $value['monto_cierre']),
              "6" => ($value['estado_caja'] == 'ABIERTO') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Cerrado</span>',

              "7" => $value['nombre_completo'],
              "8" => $solo_fecha_i,
              "9" => $solo_hora_i,
              "10" => $solo_fecha_f,
              "11" => $solo_hora_f,
            );
          }
          $results = [
            'status'=> true,
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


      case 'guardar_editar_caja':
        if (empty($idcaja)) {
          $rspta = $caja->insertar_caja($_SESSION['idusuario'], $f_inicio, $m_inicio, $sw_auto);
          echo json_encode($rspta, true);
        } else {
          $rspta = $caja->editar_caja($idcaja, $_SESSION['idusuario'], $f_inicio, $m_inicio, $f_cierre);
          echo json_encode($rspta, true);
        }
      break;


      case 'mostrar_caja':
        $rspta = $caja->mostrar($idcaja);
        echo json_encode($rspta, true);
      break;

      case 'desactivar':
        $rspta = $caja->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $caja->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'ver_usuario':
        $rspta = $caja->ver_usuario($_POST['idusuario']);
        echo json_encode($rspta, true);
      break;
      
      case 'ver_estado_caja':
        $rspta = $caja->verificar_caja();
        echo json_encode($rspta, true);
      break;

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;

    }

  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  

}
ob_end_flush();