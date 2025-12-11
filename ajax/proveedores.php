<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['proveedores'] == 1) {
   

    require_once "../modelos/Proveedores.php";
    $proveedores = new Proveedores();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idpersona            = isset($_POST["idpersona"])? limpiarCadena($_POST["idpersona"]):"";
    $tipo_persona_sunat   = isset($_POST["tipo_persona_sunat"])? limpiarCadena($_POST["tipo_persona_sunat"]):"";
    $idtipo_persona       = isset($_POST["idtipo_persona"])? limpiarCadena($_POST["idtipo_persona"]):"";

    $tipo_documento       = isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
    $numero_documento     = isset($_POST["numero_documento"])? limpiarCadena($_POST["numero_documento"]):"";
    $nombre_razonsocial   = isset($_POST["nombre_razonsocial"])? limpiarCadena($_POST["nombre_razonsocial"]):"";
    $apellidos_nombrecomercial = isset($_POST["apellidos_nombrecomercial"])? limpiarCadena($_POST["apellidos_nombrecomercial"]):"";
    $correo               = isset($_POST["correo"])? limpiarCadena($_POST["correo"]):"";
    $celular              = isset($_POST["celular"])? limpiarCadena($_POST["celular"]):"";
    $direccion            = isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
    $distrito             = isset($_POST["distrito"])? limpiarCadena($_POST["distrito"]):"";
    $departamento         = isset($_POST["departamento"])? limpiarCadena($_POST["departamento"]):"";
    $provincia            = isset($_POST["provincia"])? limpiarCadena($_POST["provincia"]):"";
    $ubigeo               = isset($_POST["ubigeo"])? limpiarCadena($_POST["ubigeo"]):"";
    $idbanco              = isset($_POST["idbanco"])? limpiarCadena($_POST["idbanco"]):"";
    $cuenta_bancaria      = isset($_POST["cuenta_bancaria"])? limpiarCadena($_POST["cuenta_bancaria"]):"";
    $cci                  = isset($_POST["cci"])? limpiarCadena($_POST["cci"]):"";

    switch ($_GET["op"]){

      case 'listar_tabla':
        $rspta = $proveedores->listar_tabla();
        $data = []; $count = 1;
        if($rspta['status'] == true){
          foreach($rspta['data'] as $key => $value){
            $img = empty($value['foto_perfil']) ? 'no-proveedor.png' : $value['foto_perfil'];
            $data[]=[
              "0" => $count++,
              "1" =>  '<div class="hstack gap-2 fs-15">' .
                        ($value['idpersona'] == 2 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' : 
                        '<button class="btn btn-icon btn-sm border-light btn-warning-light" onclick="mostrar_proveedor('.($value['idpersona']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                        '<button  class="btn btn-icon btn-sm border-danger btn-danger-light product-btn" onclick="eliminar_papelera_proveedor('.$value['idpersona'].'.,\''.$value['nombre_razonsocial'].'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
                        ) .
                        '</div>',
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/persona/perfil/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre_razonsocial']) .' '. ($value['apellidos_nombrecomercial'])) . '\')"> </span></div>
                        <div>
                          <span class="d-block fw-semibold text-primary">'.$value['nombre_razonsocial'] .' '. $value['apellidos_nombrecomercial'].'</span>
                          <span class="text-muted"><b>'.$value['tipo_documento'] .'</b>: '. $value['numero_documento'].'</span>
                        </div>
                      </div>',
              "3" => '<div >' .
                        '<b>Telefono </b>: ' . $value['celular'] . '<br>' .
                        '<b>Correo </b>: ' . $value['correo'] . '<br>' .
                        '<b>Dirección </b>: ' . $value['direccion'] . '<br>' .
                      '</div>',
              "4" => ($value['idbancos'] == '1') ? '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Sin Banco</span>' :
                    '<div >' .
                      '<b>Banco: </b> ' . $value['banco'] . '<br>' .
                      '<b>Cuenta </b>: ' . $value['cuenta_bancaria'] . '<br>' .
                      '<b>CCI </b>: ' . $value['cci'] . '<br>' .
                    '</div>',
              "5" => '<div >' .
                        '<b>Provincia </b>: ' . $value['provincia'] . '<br>' .
                        '<b>Departamento </b>: ' . $value['departamento'] . '<br>' .
                        '<b>Distrito </b>: ' . $value['distrito'] . '<br>' .
                      '</div>',
              "6" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',

              "7" => ($value['nombre_razonsocial']) .' '. ($value['apellidos_nombrecomercial']),
              "8" => ($value['tipo_documento']),
              "9" => ($value['numero_documento']),
              "10" => ($value['celular']),
              "11" => ($value['correo']),
              "12" => ($value['banco']),
              "13" => ($value['cuenta_bancaria']),
              "14" => ($value['cci']),
              "15" => ($value['direccion']),
              "16" => ($value['provincia']),
              "17" => ($value['departamento']),
              "18" => ($value['distrito'])
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

      case 'guardar_editar':
        //guardar f_img_fondo fondo
        if ( !file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']) ) {
          $img_perfil = $_POST["imagenactual"];
          $flat_img1 = false; 
        } else {          
          $ext1 = explode(".", $_FILES["imagen"]["name"]);
          $flat_img1 = true;
          $img_perfil = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext1);
          move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/modulo/persona/perfil/" . $img_perfil);          
        }        

        if ( empty($idpersona) ) { #Creamos el registro

          $rspta = $proveedores->insertar( $tipo_persona_sunat, $idtipo_persona, $tipo_documento, $numero_documento,  
          $nombre_razonsocial, $apellidos_nombrecomercial, $correo, $celular, $direccion, $distrito, 
          $departamento, $provincia, $ubigeo, $idbanco, $cuenta_bancaria, $cci, $img_perfil );
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img1 == true || empty($img_perfil)) {
            $datos_f1 = $proveedores->mostrar($idpersona);
            $img1_ant = $datos_f1['data']['foto_perfil'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/persona/perfil/" . $img1_ant); }         
          }  
        
          $rspta = $proveedores->editar($idpersona, $tipo_persona_sunat, $idtipo_persona, $tipo_documento, $numero_documento,
          $nombre_razonsocial, $apellidos_nombrecomercial, $correo, $celular, $direccion, $distrito, 
          $departamento, $provincia, $ubigeo, $idbanco, $cuenta_bancaria, $cci, $img_perfil);
          echo json_encode($rspta, true);
        }        

        
      break; 

      case 'eliminar':
        $rspta = $proveedores->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break; 

      case 'papelera':
        $rspta = $proveedores->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar' :
        $rspta = $proveedores->mostrar($idpersona);
        echo json_encode($rspta, true);
      break;

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;

    }
  
  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}



ob_end_flush();