<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }//Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['otro_gasto'] == 1) {

    require_once "../modelos/Otros_gastos.php";
    $otros_gastos = new Otros_gastos();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idotros_gastos  = isset($_POST["idotros_gastos"]) ? limpiarCadena($_POST["idotros_gastos"]) : "";
    $idproveedor        = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
    $idotros_gastos_categoria      = isset($_POST["idotros_gastos_categoria"]) ? limpiarCadena($_POST["idotros_gastos_categoria"]) : "";
    $tipo_comprobante  = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
    $serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
    $fecha             = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";    
    $mes             = isset($_POST["mes"]) ? limpiarCadena($_POST["mes"]) : "";    
    $precio_sin_igv    = isset($_POST["precio_sin_igv"]) ? limpiarCadena($_POST["precio_sin_igv"]) : "";
    $igv               = isset($_POST["igv"]) ? limpiarCadena($_POST["igv"]) : "";
    $val_igv           = isset($_POST["val_igv"]) ? limpiarCadena($_POST["val_igv"]) : "";
    $precio_con_igv    = isset($_POST["precio_con_igv"]) ? limpiarCadena($_POST["precio_con_igv"]) : "";
    $descr_comprobante = isset($_POST["descr_comprobante"]) ? limpiarCadena($_POST["descr_comprobante"]) : "";
    $img_comprob       = isset($_POST["doc_old_1"]) ? limpiarCadena($_POST["doc_old_1"]) : "";
    $idcaja       = isset($_POST["idcaja"]) ? limpiarCadena($_POST["idcaja"]) : "";
    $tipo_gasto_modulo       = isset($_POST["tipo_gasto_modulo"]) ? limpiarCadena($_POST["tipo_gasto_modulo"]) : "";


    switch ($_GET["op"]){

      case 'guardar_editar':
        //guardar img_comprob fondo
        if ( !file_exists($_FILES['doc1']['tmp_name']) || !is_uploaded_file($_FILES['doc1']['tmp_name']) ) {
          $img_comprob = $_POST["doc_old_1"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["doc1"]["name"]);
          $flat_img = true;
          $img_comprob = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc1"]["tmp_name"], "../assets/modulo/otros_gastos/" . $img_comprob);          
        }

        if ( empty($idotros_gastos) ) { #Creamos el registro

          $rspta = $otros_gastos->insertar( $idproveedor, $idotros_gastos_categoria, $tipo_gasto_modulo, $tipo_comprobante, $serie_comprobante, 
          $fecha, $mes, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img == true || empty($img_comprob)) {
            $datos_f1 = $otros_gastos->mostrar_editar($idotros_gastos);
            $img1_ant = $datos_f1['data']['comprobante'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/otros_gastos/" . $img1_ant); }         
          }  

          $rspta = $otros_gastos->editar($idotros_gastos, $idproveedor, $idcaja, $tipo_gasto_modulo, $idotros_gastos_categoria, $tipo_comprobante, $serie_comprobante, 
          $fecha, $mes, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob);
          echo json_encode($rspta, true);
        }


      break;

      case 'listar_tabla':
        $rspta = $otros_gastos->listar_tabla();
        $data = []; $count = 1;

        if($rspta['status'] == true){

          // foreach($rspta['data'] as $key => $value){
          while ($reg = $rspta['data']->fetch_object()) {

            // -------------- CONDICIONES --------------      
            $img = empty($reg->foto_perfil_proveedor) ? 'no-perfil.jpg'  : $reg->foto_perfil_proveedor;
            $data[] = [
              "0" => $count++,
              "1" =>  '<div class="hstack gap-2 fs-15">' .
                '<button class="btn btn-icon btn-sm border-warning btn-warning-light" onclick="mostrar_editar('.($reg->idingreso_egreso_interno).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                '<button  class="btn btn-icon btn-sm border-danger btn-danger-light product-btn" onclick="eliminar_gasto('.$reg->idingreso_egreso_interno.', \''.$reg->proveedor.'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
                '<button class="btn btn-icon btn-sm border-info btn-info-light" onclick="mostrar_detalles_gasto('.($reg->idingreso_egreso_interno).')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button>'.
              '</div>',
              "2" => ($reg->fecha_comprobante),
              "3" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img src="../assets/modulo/persona/perfil/'.$img.'" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml($reg->proveedor ) . '\')"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary text-nowrap">'.$reg->proveedor.'</span>
                  <span class="text-muted">'.$reg->tipo_documento_nombre .' '. $reg->numero_documento.'</span>
                </div>
              </div>',
              "4" => $reg->categoria ,
              "5" => $reg->tipo_comprobante .'<br>'. $reg->serie_comprobante,
              "6" => $reg->precio_con_igv,
              "7" => '<textarea class="textarea_datatable bg-light"  readonly>' .($reg->descripcion_comprobante). '</textarea>',
              "8" => !empty($reg->comprobante) ? '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_comprobante('.($reg->idingreso_egreso_interno).');" data-bs-toggle="tooltip" title="Ver"><i class="ti ti-file-dollar fs-lg"></i></button></div>' : 
                '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-danger-light" data-bs-toggle="tooltip" title="no encontrado"><i class="ti ti-file-alert fs-lg"></i></button></div>',
              
              "9" => $reg->proveedor,
              "10" => $reg->tipo_documento_nombre,
              "11" => $reg->numero_documento,
              "12" => $reg->periodo_gasto_day,
              "13" => $reg->periodo_gasto_month,
              "14" => $reg->tipo_comprobante,
              "15" => $reg->serie_comprobante,
              "16" => $reg->precio_sin_igv,
              "17" => $reg->precio_igv,
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results, true);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'desactivar':
        $rspta = $otros_gastos->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $otros_gastos->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_trabajador':
        $rspta = $otros_gastos->listar_trabajador(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona_trabajador']  . '>' . $value['nombre_razonsocial'] . ' '. $value['apellidos_nombrecomercial'] . ' - ' . $value['numero_documento']. '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;

      case 'listar_proveedor':
        $rspta = $otros_gastos->listar_proveedor(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona']  . '>' . $value['tipo_persona'] . ': '. $value['nombre'] . ' '. $value['apellido'] . ' - '. $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option  value="2" >PROVEEDOR VARIOS</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'mostrar_editar':
        $rspta = $otros_gastos->mostrar_editar($idotros_gastos);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_detalle_gasto':
        $rspta = $otros_gastos->mostrar_detalle_gasto($idotros_gastos);        
        $img_p = empty($rspta['data']['foto_perfil_proveedor']) ? 'no-perfil.jpg'  : $rspta['data']['foto_perfil_proveedor'];
        $nombre_doc = $rspta['data']['tipo_comprobante'] .' ' .$rspta['data']['serie_comprobante'];
        $html_table = '        
        <div class="my-3" ><span class="h6"> Datos del comprobante </span></div>
        <table class="table text-nowrap table-bordered">        
          <tbody>
            <tr>
              <th scope="col">Proveedor</th>
              <th scope="row">
                <div class="d-flex flex-fill align-items-center">
                  <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                    <span class="avatar"> <img src="../assets/modulo/persona/perfil/'.$img_p.'" alt="" onclick="ver_img(\'' . $img_p . '\', \'' . encodeCadenaHtml($rspta['data']['proveedor'] ) . '\')"> </span>
                  </div>
                  <div>
                    <span class="d-block fw-semibold text-primary">'.$rspta['data']['proveedor'] .'</span>
                    <span class="text-muted">'.$rspta['data']['tipo_documento_nombre'].' '. $rspta['data']['numero_documento'].'</span>
                  </div>
                </div>                
              </th>            
            </tr>    
            
            <tr>
              <th scope="col">Fecha</th>
              <th scope="row">'.$rspta['data']['fecha_comprobante_v2'].' | '.$rspta['data']['periodo_gasto_day'].' | '.$rspta['data']['periodo_gasto_month'].'</th>
            </tr>
            <tr>
              <th scope="col">Categoria</th>
              <th scope="row">'.$rspta['data']['categoria'].'</th>
            </tr>
            <tr>
              <th scope="col">'.$rspta['data']['tipo_comprobante'].'</th>
              <th scope="row">'.$rspta['data']['serie_comprobante'].'</th>
            </tr>                
            <tr>
              <th scope="col">Subtotal</th>
              <th scope="row">'. number_format($rspta['data']['precio_sin_igv'], 2, '.', ',') .'</th>
            </tr> 
            <tr>
              <th scope="col">IGV</th>
              <th scope="row">'.number_format($rspta['data']['precio_igv'], 2, '.', ',') .'</th>
            </tr>  
            <tr>
              <th scope="col">Total</th>
              <th scope="row">'.number_format($rspta['data']['precio_con_igv'], 2, '.', ',') .'</th>
            </tr>
            <tr>
              <th scope="col">Descripción</th>
              <th scope="row">'.$rspta['data']['descripcion_comprobante'].'</th>
            </tr>                 
          </tbody>
        </table> 
        <div class="my-3" ><span class="h6"> Comprobante </span></div>';
        $rspta = ['status' => true, 'message' => 'Todo bien', 'data' => $html_table, 'comprobante' => $rspta['data']['comprobante'], 'nombre_doc'=> $nombre_doc];
        echo json_encode($rspta, true);
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
      break;

      // case "select2TipoTrabajador":

      //   $rspta = $ajax_general->select2_tipo_trabajador(); $cont = 1; $data = "";

      //   if ($rspta['status'] == true) {

      //     foreach ($rspta['data'] as $key => $value) {

      //       $data .= '<option  value=' . $value['idtipo_trabajador']  . '>' . $value['nombre'] . '</option>';
      //     }

      //     $retorno = array(
      //       'status' => true, 
      //       'message' => 'Salió todo ok', 
      //       'data' => '<option  value="1" >NINGUNO</option>'.$data, 
      //     );

      //     echo json_encode($retorno, true);

      //   } else {

      //     echo json_encode($rspta, true); 
      //   }        
      // break;

    }

  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}
ob_end_flush();