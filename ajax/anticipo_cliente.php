<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['cliente'] == 1) {

    require_once "../modelos/Anticipo_cliente.php";
    $anticipo = new Anticipo_cliente();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

    $idanticipo_cliente   = isset($_POST["idanticipo_cliente"]) ? limpiarCadena($_POST["idanticipo_cliente"]) : "";

    $idpersona_cliente    = isset($_POST["cliente"]) ? limpiarCadena($_POST["cliente"]) : "";
    $fecha                = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
    $descripcion          = isset($_POST["descrip"]) ? limpiarCadena($_POST["descrip"]) : "";
    $tipo                 = isset($_POST["tipo_ac"]) ? limpiarCadena($_POST["tipo_ac"]) : "";
    $total                = isset($_POST["monto"]) ? limpiarCadena($_POST["monto"]) : "";
    $serie                = isset($_POST["SerieReal"]) ? limpiarCadena($_POST["SerieReal"]) : "";
    $serie_edit           = isset($_POST["serie_ac_edit"]) ? limpiarCadena($_POST["serie_ac_edit"]) : "";
    $numero               = isset($_POST["numero_ac"]) ? limpiarCadena($_POST["numero_ac"]) : "";


    switch ($_GET["op"]){

      case 'tbla_pral_clientes':
        $rspta = $anticipo->tabla_clientes();
        $data = []; $count = 1;

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $data[]=[
              "0" => $count++,
              "1" =>  '<div class="d-flex flex-fill align-items-center">
                        <div><p class="d-block fw-semibold fs-11 text-primary">'.$value['nombres'] .' '.$value['apellidos'] .'</p> </div>
                      </div>',

              "2" =>  '<p class="d-block fs-11 fw-semibold text-primary">'.$value['total_anticipo'] .'</p>',
              "3" =>  '<div class="hstack gap-2 fs-11 text-center">' .
                        '<button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_tbla_anticipos(' . $value['idpersona_cliente'] . ', \'' . $value['nombres'] . '\', \'' . $value['apellidos'] . '\')" data-bs-toggle="tooltip" title="Mostrar Anticipos"><i class="ri-arrow-left-right-line"></i></button>'.
                      '</div>',
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'mostrar_tbla_anticipos':
        $rspta = $anticipo->tabla_anticipos($_GET["idpersona_cliente"]);
        $data = []; $count = 1;

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $data[]=[
              "0" => $count++,
              "1" =>  ' <div class="dropdown-center">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownCenterBtn" data-bs-toggle="dropdown" aria-expanded="false"> <i class="nav-icon fa-solid fa-gears"></i> </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownCenterBtn" style="">
                          <li><a class="dropdown-item" href="javascript:void(0);" onclick="mostrar_anticipo('.($value['idanticipo_cliente']).')">Editar</a></li>
                          <li><a class="dropdown-item" href="javascript:void(0);" onclick="eliminar_papelera_anticipo(' . $value['idanticipo_cliente'] . ', \'' . addslashes($value['sc_anticipo']) . '\', \'' . addslashes($value['nc_anticipo']) . '\')">Eliminar</a></li>
                          <li><a class="dropdown-item" href="javascript:void(0);" onclick="TickcetAnticipo_ciente('.($value['idanticipo_cliente']).')">Formato Ticket</a></li>
                          <!--<li><a class="dropdown-item" href="javascript:void(0);" target="_blank" href="../reportes/A4ComprimidoAnticipo_cliente.php?id='.$value['idanticipo_cliente'].'&idanticipo_cliente='.($value['idanticipo_cliente']).'"  >A4 Comprimido</a></li>-->
                          <li><a class="dropdown-item"  target="_blank" href="../reportes/A4CompletoAnticipo_cliente.php?id='.$value['idanticipo_cliente'].'&idanticipo_cliente='.($value['idanticipo_cliente']).'"  >Formato A4</a></li>
                        </ul>
                      </div> ',
              "2" =>  $value['tipo'] == 'INGRESO' ? '<span class="badge bg-success-transparent">'.$value['tipo'] .'</span>' : '<span class="badge bg-danger-transparent">'.$value['tipo'] .'</span>',
              "3" =>  (new DateTime($value['fecha_anticipo']))->format('d/m/Y'),
              "4" => '<span class="fs-11">'.$value['sc_anticipo'].'-'.$value['nc_anticipo'].'</span>',
              "5" => '<textarea class="textarea_datatable fs-11 bg-light" rows="1"  readonly>' .($value['descripcion']). '</textarea>',
              "6" => '<span class="fs-11">'.$value['sc_venta'].'-'.$value['nc_venta'].'</span>',
              "7" => $value['monto_anticipo'],
              "8" => $value['nombres'] .' '.$value['apellidos']
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'guardar_editar_anticipo':
        if (empty($idanticipo_cliente)) {
          $rspta = $anticipo->insertar($idpersona_cliente, $fecha, $descripcion, $tipo, $total, $serie);
          echo json_encode($rspta, true);
        } else {
          $rspta = $anticipo->editar($idanticipo_cliente, $idpersona_cliente, $fecha, $descripcion, $tipo, $total, $serie_edit, $numero);
          echo json_encode($rspta, true);
        }
      break;

      case 'mostrar_anticipo':
        $rspta = $anticipo->mostrar($idanticipo_cliente);
        echo json_encode($rspta, true);
      break;

      case 'desactivar':
        $rspta = $anticipo->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $anticipo->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'actualizar_numeracion':
        $ser = $_GET['ser'];
        $rspta = $anticipo->numeracion($ser);
        if ($rspta['status']) {
            foreach ($rspta['data'] as $row) {
                echo $row['numeracion_anticipo'];
            }
        } else {
            echo "Error en la consulta: " . $rspta['message'];
        }
    break;

      case 'selectSerie':
        $rspta = $anticipo->selectSerie();
        if ($rspta['status']) {
            foreach ($rspta['data'] as $row) {
                echo '<option value="' . $row['serie'] . '">' . $row['serie'] . '</option>';
            }
        } else {
            echo "Error en la consulta: " . $rspta['message'];
        }
    break;


      case 'selectChoice_cliente':
        $rspta = $anticipo->select_cliente();

        $data = [];

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {
            $data[] = [
              'value' => $value['idpersona_cliente'],
              'label' => $value['nombres'] . ' ' . $value['apellidos'],
              'disabled'  => false,
              'selected'  => false,];
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;


    }






  }else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }


}
ob_end_flush();