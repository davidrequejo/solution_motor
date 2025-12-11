<?php
require_once "../sunat/ConfigSunat.php";

ob_start();

if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

require_once "../modelos/Usuario.php";
require_once "../modelos/Permiso.php";
require_once "../modelos/Numeracion.php";
require_once "../modelos/Facturacion.php";

$usuario      = new Usuario();
$permisos      = new Permiso();
$numeracion   = new Numeracion();
$facturacion  = new Facturacion();   

date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
$imagen_error = "this.src='../dist/svg/404-v2.svg'";
$toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

# ══════════════════════════════════════ D A T O S   U S U A R I O ══════════════════════════════════════ 
$idusuario  = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$idpersona  = isset($_POST["idpersona"]) ? limpiarCadena($_POST["idpersona"]) : "";
$login      = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave      = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";

$permiso    = isset($_POST["permiso"]) ? $_POST['permiso'] : "";
$serie      = isset($_POST["serie"]) ? $_POST['serie'] : "";

switch ($_GET["op"]) {
  case 'guardaryeditar':
    
    if (empty($clave)) { #Extraemos la clave antigua     
      $usuario_actual = $usuario->mostrar_clave($idusuario);
      $clavehash = $usuario_actual['data']['password'];
    } else {  # Encriptamos la clave      
      $clavehash = hash("SHA256", $clave);
    }

    if (empty($idusuario)) {
      $rspta = $usuario->insertar($idpersona, $login, $clavehash, $permiso , $serie );
      echo json_encode($rspta, true);
    } else {
      $rspta = $usuario->editar($idusuario, $idpersona, $login, $clavehash, $permiso , $serie );
      echo json_encode($rspta, true);
    }
  break;

  case 'papelera':
    $rspta = $usuario->papelera($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  case 'eliminar':
    $rspta = $usuario->eliminar($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  case 'activar':
    $rspta = $usuario->activar($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  case 'cargo_persona':
    $rspta = $usuario->cargo_persona($_POST["idpersona"]);
    //Codificar el resultado utilizando json
    echo json_encode($rspta, true);
  break;

  case 'mostrar':
    $rspta = $usuario->mostrar($idusuario);
    //Codificar el resultado utilizando json
    echo json_encode($rspta, true);
  break;

  case 'validar_usuario':
    $rspta = $usuario->validar_usuario($_GET["idusuario"],$_GET["login"]);
    //Codificar el resultado utilizando json
    echo json_encode($rspta, true);
  break;

  case 'historial_sesion':
    $rspta = $usuario->historial_sesion($_GET["id"]);
    $data = array();
    foreach ($rspta['data'] as $key => $val) {
      $data[] = array(
        "0" => $key +1  ,        
        "1" => $val['last_sesion'],
        "2" => $val['nombre_dia'],
        "3" => $val['nombre_mes'],
      );
    }
    $results = array(
      'status'=> true,
      "sEcho" => 1, //Información para el datatables
      "iTotalRecords" => count($data),  //enviamos el total registros al datatable
      "iTotalDisplayRecords" => count($data),  //enviamos el total registros a visualizar
      "aaData" => $data
    );
    echo json_encode($results, true);
  break;

  case 'listar':
    $rspta = $usuario->listar();
    //Vamos a declarar un array

    $data = array(); $count =1;

    while ($reg = $rspta['data']->fetch_object()) {
      // Mapear el valor numérico a su respectiva descripción      

      $img = empty($reg->foto_perfil) ? 'no-perfil.jpg' : $reg->foto_perfil ;

      $data[] = array(
        "0" => $count++,
        "1" => '<div class="hstack gap-2 fs-15">' .
          '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar(' . $reg->idusuario . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
          ($reg->estado ? '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="desactivar(' . $reg->idusuario . ', \'' . encodeCadenaHtml($reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>':
          '<button class="btn btn-icon btn-sm btn-success-light product-btn" onclick="activar(' . $reg->idusuario . ')" data-bs-toggle="tooltip" title="Activar"><i class="fa fa-check"></i></button>'
          ).
        '</div>',        
        "2" =>'<div class="d-flex flex-fill align-items-center">
          <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/persona/perfil/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml($reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial) . '\')"> </span></div>
          <div>
            <span class="d-block fw-semibold text-primary">'.$reg->nombre_razonsocial .' '. $reg->apellidos_nombrecomercial.'</span>
            <span class="text-muted">'.$reg->tipo_documento .' '. $reg->numero_documento .' | <i class="ti ti-fingerprint fs-18"></i> '. zero_fill($reg->idusuario, 5).'</span>
          </div>
        </div>',
        "3" => $reg->login,
        "4" => $reg->cargo_trabajador,
        "5" => '<a href="tel:+51'.$reg->celular.'">'.$reg->celular.'</a>',
        "6" => '<span class="cursor-pointer" data-bs-toggle="tooltip" title="Ver historial" onclick="historial_sesion(' . $reg->idusuario . ')" >'.$reg->last_sesion.'</span>',
        "7" => ($reg->estado) ? '<span class="badge bg-success-transparent">Activado</span>' : '<span class="badge bg-danger-transparent">Inhabilitado</span>'
      );
    }
    $results = array(
      'status'=> true,
      "sEcho" => 1, //Información para el datatables
      "iTotalRecords" => count($data),  //enviamos el total registros al datatable
      "iTotalDisplayRecords" => count($data),  //enviamos el total registros a visualizar
      "aaData" => $data
    );
    echo json_encode($results);

  break;

  case 'permisos':
    
    $rspta = $permisos->listar_todos_permisos();

    $id = $_GET['id'];
    $marcados = $usuario->listarmarcados($id); # Obtener los permisos asignados al usuario

    $valores = array(); # Declaramos el array para almacenar todos los permisos marcados

    foreach ($marcados['data'] as $key => $val) { array_push($valores, $val['idpermiso']); } # Almacenar los permisos asignados al usuario en el array

    //Mostramos la lista de permisos en la vista y si están o no marcados
    echo '<div class="row gy-2" >';
    foreach ($rspta['data']['agrupado'] as $key => $val1) {   
      echo '<div class="col-lg-4 col-xl-3 col-xxl-3 mt-3" >';
      echo '<span >'.$val1['modulo'].'</span>';
      foreach ($val1['submodulo'] as $key => $val2) {
        $sw = in_array($val2['idpermiso'], $valores) ? 'checked' : '';
        echo '<div class="custom-toggle-switch d-flex align-items-center mt-2 mb-2">
          <input id="permiso_' . $val2['idpermiso'] . '" name="permiso[]" type="checkbox" ' . $sw . ' value="' . $val2['idpermiso'] . '">
          <label for="permiso_' . $val2['idpermiso'] . '" class="label-primary"></label><span class="ms-3">' . $val2['submodulo'] . '</span>
        </div>';
      }  
      echo '</div>';
    }
    echo '</div>';
  break;

  case 'permisosEmpresa':
    
    $rspta = $permisos->listarEmpresa();
    
    $id = $_GET['id'];
    $marcados = $usuario->listarmarcadosEmpresa($id); # Obtener los permisos asignados al usuario
  
    $valores = array(); # Declaramos el array para almacenar todos los permisos marcados

    while ($per = $marcados['data']->fetch_object()) { array_push($valores, $per->idempresa); } # Almacenar los permisos asignados al usuario en el array

    //Mostramos la lista de permisos en la vista y si están o no marcados
    echo '<div class="row gy-2" >';
    foreach ($rspta['data'] as $key => $val) {
     
      if ($key % 3 === 0) {   echo '<div class="col-lg-3" >';   } # abrimos el: col-lg-2
     
      $sw = in_array($val['idempresa'], $valores) ? 'checked' : '';
      echo '<div class="custom-toggle-switch d-flex align-items-center mb-1">
        <input id="empresa_' . $val['idempresa'] . '" name="empresa[]" type="checkbox" ' . $sw . ' value="' . $val['idempresa'] . '">
        <label for="empresa_' . $val['idempresa'] . '" class="label-primary"></label><span class="ms-3">' . $val['nombre_razon_social'] . '</span>
      </div>';
     
      if (($key + 1) % 3 === 0 || $key === count($rspta['data']) - 1) { echo "</div>"; } # cerramos el: col-lg-2
    }
    echo '</div>';
  break;

  case 'permisosEmpresaTodos':
    
    $rspta = $permisos->listarEmpresa();
    $marcados = $usuario->listarmarcadosEmpresaTodos();
    //Declaramos el array para almacenar todos los permisos marcados
    $valores = array();

    //Almacenar los permisos asignados al usuario en el array
    while ($per = $marcados['data']->fetch_object()) {
      array_push($valores, $per->idempresa);
    }

    //Mostramos la lista de permisos en la vista y si están o no marcados
    echo '<div class="row gy-2" >';
    foreach ($rspta['data'] as $key => $val) {
      if ($key % 3 === 0) {   echo '<div class="col-lg-3" >';   } # abrimos el: col-lg-2
      echo '<div class="custom-toggle-switch d-flex align-items-center mb-1">
        <input id="empresa_' . $val['idempresa'] . '"  name="empresa[]" value="' . $val['idempresa'] . '" type="checkbox" >
        <label for="empresa_' . $val['idempresa'] . '" class="label-primary"></label><span class="ms-3">' . $val['nombre_razon_social'] . '</span>
      </div>';
      if (($key + 1) % 3 === 0 || $key === count($rspta['data']) - 1) { echo "</div>"; } # cerramos el: col-lg-2
    }
    echo '</div>';
  break;

  case 'series':
    
    $rspta = $numeracion->listarSeries();

    //Obtener los permisos asignados al usuario
    $id = $_GET['id'];
    $marcados = $usuario->listarmarcadosNumeracion($id);
    //Declaramos el array para almacenar todos los permisos marcados
    $series_array = array();

    //Almacenar los permisos asignados al usuario en el array
    while ($per = $marcados['data']->fetch_object()) {
      array_push($series_array, $per->idtipo_comprobante);
    }

    //Mostramos la lista de permisos en la vista y si están o no marcados
    echo '<div class="row gy-2" >';
    foreach ($rspta['data'] as $key => $val) {

      if ($key % 3 === 0) {   echo '<div class="col-lg-4 col-xl-3 col-xxl-3" >';   } # abrimos el: col-lg-2      
      
      $sw = in_array($val['idtipo_comprobante'], $series_array) ? 'checked' : '';

      echo '<div class="custom-toggle-switch d-flex align-items-center mb-2 mt-2">
        <input id="serie_' . $val['idtipo_comprobante'] . '" name="serie[]" value="' . $val['idtipo_comprobante'] . '" type="checkbox" ' . $sw . '>
        <label for="serie_' . $val['idtipo_comprobante'] . '" class="label-primary"></label><span class="ms-3">' . $val['abreviatura'] .': <b>'.  $val['serie'] . '-' . $val['numero'] . '</b></span>
      </div>';
      if (($key + 1) % 3 === 0 || $key === count($rspta['data']) - 1) { echo "</div>"; } # cerramos el: col-lg-2
    }
    echo '</div>';
  break;

  case 'seriesnuevo':
    
    $rspta = $numeracion->listarSeriesNuevo();
    
    while ($reg = $rspta['data']->fetch_object()) { 
      echo '<li> <input type="checkbox" name="serie[]" value="' . $reg->idtipo_comprobante . '">' . $reg->serie . '-' . $reg->numero . ' </li>';
    }
  break;

  case 'verificar':

    $logina   = $_POST['logina'];
    $clavea   = $_POST['clavea'];
    $st       = $_POST['st'];

    //Hash SHA256 en la contraseña
    //$clavehash=$clavea;
    $clavehash = hash("SHA256", $clavea);

    $rspta  = $usuario->verificar($logina, $clavehash);    
    // $rspta2 = $usuario->onoffTempo($st);
    // $rspta3 = $usuario->consultatemporizador();    

    if (!empty($rspta['data']['usuario'])) {

      
      $rspta2 = $usuario->last_sesion($rspta['data']['usuario']['idusuario']); # Ultima sesion

      $empresa_f  = $facturacion->datos_empresa(); # Datos de empresa: para uso global del sistema
      
      $e_razon_social       = mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_razon_social'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
      $e_nombre_comercial   = mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_comercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
      $e_domicilio_fiscal   = mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'UTF-8', mb_detect_encoding($empresa_f['data']['domicilio_fiscal'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
      $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
      $e_numero_documento   = $empresa_f['data']['numero_documento'];
    
      $e_distrito           = mb_convert_encoding($empresa_f['data']['distrito'], 'UTF-8', mb_detect_encoding($empresa_f['data']['distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
      $e_provincia          = mb_convert_encoding($empresa_f['data']['provincia'], 'UTF-8', mb_detect_encoding($empresa_f['data']['provincia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
      $e_departamento       = mb_convert_encoding($empresa_f['data']['departamento'], 'UTF-8', mb_detect_encoding($empresa_f['data']['departamento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
      $e_codubigueo         = mb_convert_encoding($empresa_f['data']['codubigueo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['codubigueo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));    
      
      //Declaramos las variables de sesión
      $_SESSION['idusuario']            = $rspta['data']['usuario']['idusuario'];
      $_SESSION['idpersona']            = $rspta['data']['usuario']['idpersona'];
      $_SESSION['idpersona_trabajador'] = $rspta['data']['usuario']['idpersona_trabajador'];
      $_SESSION['user_nombre']          = $rspta['data']['usuario']['nombre_razonsocial'];
      $_SESSION['user_apellido']        = $rspta['data']['usuario']['apellidos_nombrecomercial'];
      $_SESSION['user_tipo_doc']        = $rspta['data']['usuario']['tipo_documento'];
      $_SESSION['user_num_doc']         = $rspta['data']['usuario']['numero_documento'];
      $_SESSION['user_cargo']           = $rspta['data']['usuario']['cargo'];
      $_SESSION['user_imagen']          = $rspta['data']['usuario']['foto_perfil'];
      $_SESSION['user_login']           = $rspta['data']['usuario']['login'];
      $_SESSION['user_update_sistema']  = $rspta['data']['usuario']['estado_update_sistema'];

      // $_SESSION['idusuario_empresa'] = $rspta['data']['sucursal']['idusuario_empresa'];
      // $_SESSION['idempresa']         = $rspta['data']['sucursal']['idempresa'];
      $_SESSION['empresa_nrs']          = $e_razon_social;        # Razon Social
      $_SESSION['empresa_nc']           = $e_nombre_comercial;    # Nombre Comercial
      $_SESSION['empresa_td']           = $e_tipo_documento;      # Tipo Documento: codigo sunat
      $_SESSION['empresa_nd']           = $e_numero_documento;    # Numero de documento
      $_SESSION['empresa_df']           = $e_domicilio_fiscal;    # Domicilio Fiscal
      $_SESSION['empresa_impuesto']     = 0;

      $_SESSION['empresa_distrito']     = $e_distrito;
      $_SESSION['empresa_provincia']    = $e_provincia;
      $_SESSION['empresa_departamento'] = $e_departamento;
      $_SESSION['empresa_codubigueo']   = $e_codubigueo;

      // $_SESSION['estadotempo']        = $rspta3['data']['estado'];      
      
      $marcados = $usuario->listarmarcados($rspta['data']['usuario']['idusuario']);         # Obtenemos los permisos del usuario
      $grupo    = $usuario->listar_grupo_marcados($rspta['data']['usuario']['idusuario']);  # Obtenemos los permisos del usuario
      // $usuario->savedetalsesion($rspta['data']['usuario']['idusuario']);                 # Guardamos los datos del usuario al iniciar sesion.

      $valores = array();           # Declaramos el array para almacenar todos los permisos marcados
      $valores_agrupado = array();  # Declaramos el array para almacenar todos los permisos marcados

      foreach ($marcados['data'] as $key => $val) { array_push($valores, $val['idpermiso']);  } # Almacenamos los permisos marcados en el array      
      
      foreach ($grupo['data'] as $key => $val) { array_push($valores_agrupado, $val['modulo']);  }  # Almacenamos los permisos marcados en el array
      
      // PERMISOS GRUPALES
      in_array('Compras', $valores_agrupado)            ? $_SESSION['compra'] = 1             : $_SESSION['compra']           = 0;        
      in_array('Articulo', $valores_agrupado)           ? $_SESSION['articulo'] = 1           : $_SESSION['articulo']         = 0;         
      in_array('Venta', $valores_agrupado)              ? $_SESSION['ventas'] = 1        : $_SESSION['ventas']      = 0;       
      in_array('Ordenes', $valores_agrupado)            ? $_SESSION['ordenes'] = 1            : $_SESSION['ordenes']          = 0;       
      // in_array('Caja', $valores_agrupado)               ? $_SESSION['caja'] = 1               : $_SESSION['caja']             = 0;    
      in_array('Ingresos/Egreso', $valores_agrupado)    ? $_SESSION['ingreso_egreso'] = 1     : $_SESSION['ingreso_egreso']   = 0;    
      in_array('Reporte', $valores_agrupado)            ? $_SESSION['reporte'] = 1            : $_SESSION['reporte']          = 0;         
      in_array('Administracion', $valores_agrupado)     ? $_SESSION['administracion'] = 1     : $_SESSION['administracion']   = 0;         
      in_array('Planilla Personal', $valores_agrupado)  ? $_SESSION['planilla_personal'] = 1  : $_SESSION['planilla_personal']= 0;        
      in_array('SUNAT', $valores_agrupado)              ? $_SESSION['SUNAT'] = 1              : $_SESSION['SUNAT']            = 0;        
      in_array('Configuracion', $valores_agrupado)            ? $_SESSION['configuracion'] = 1            : $_SESSION['configuracion']          = 0;           

      // PERMISOS INDIVIDUALES

      in_array(1, $valores) ? $_SESSION['dashboard']                  = 1 : $_SESSION['dashboard']                 = 0;
      in_array(2, $valores) ? $_SESSION['proveedores']                = 1 : $_SESSION['proveedores']               = 0;
      in_array(3, $valores) ? $_SESSION['lista_de_compras']           = 1 : $_SESSION['lista_de_compras']          = 0;
      in_array(4, $valores) ? $_SESSION['producto']                   = 1 : $_SESSION['producto']                  = 0;
      in_array(5, $valores) ? $_SESSION['servicio']                   = 1 : $_SESSION['servicio']                  = 0;
      in_array(6, $valores) ? $_SESSION['categoria_y_marca']          = 1 : $_SESSION['categoria_y_marca']         = 0;
      in_array(7, $valores) ? $_SESSION['unidad_de_medida']           = 1 : $_SESSION['unidad_de_medida']          = 0;
      in_array(8, $valores) ? $_SESSION['producto_pasillo']           = 1 : $_SESSION['producto_pasillo']          = 0;
      in_array(9, $valores) ? $_SESSION['orden_venta']                = 1 : $_SESSION['orden_venta']               = 0;
      in_array(10, $valores) ? $_SESSION['orden_venta_listar']        = 1 : $_SESSION['orden_venta_listar']        = 0;
      in_array(11, $valores) ? $_SESSION['orden_venta_cobrar']        = 1 : $_SESSION['orden_venta_cobrar']        = 0;
      in_array(12, $valores) ? $_SESSION['facturacion']               = 1 : $_SESSION['facturacion']               = 0;
      in_array(13, $valores) ? $_SESSION['guia_de_remision']          = 1 : $_SESSION['guia_de_remision']          = 0;
      in_array(14, $valores) ? $_SESSION['cotizacion']                = 1 : $_SESSION['cotizacion']                = 0;
      in_array(15, $valores) ? $_SESSION['cliente']                   = 1 : $_SESSION['cliente']                   = 0;
      in_array(16, $valores) ? $_SESSION['anticipos']                 = 1 : $_SESSION['anticipos']                 = 0;
      in_array(17, $valores) ? $_SESSION['periodo_facturado']         = 1 : $_SESSION['periodo_facturado']         = 0;
      in_array(18, $valores) ? $_SESSION['caja']                      = 1 : $_SESSION['caja']                      = 0;
      in_array(19, $valores) ? $_SESSION['gasto_trabajador']          = 1 : $_SESSION['gasto_trabajador']          = 0;
      in_array(20, $valores) ? $_SESSION['otro_gasto']                = 1 : $_SESSION['otro_gasto']                = 0;
      in_array(21, $valores) ? $_SESSION['ingresos']                  = 1 : $_SESSION['ingresos']                  = 0;
      in_array(22, $valores) ? $_SESSION['reporte_contador']          = 1 : $_SESSION['reporte_contador']          = 0;
      in_array(23, $valores) ? $_SESSION['reporte_venta_x_trabajador']= 1 : $_SESSION['reporte_venta_x_trabajador']= 0;
      in_array(24, $valores) ? $_SESSION['reporte_stock_de_producto'] = 1 : $_SESSION['reporte_stock_de_producto'] = 0;
      in_array(25, $valores) ? $_SESSION['reporte_copia_estock']      = 1 : $_SESSION['reporte_copia_estock']      = 0;
      in_array(26, $valores) ? $_SESSION['reporte_bitacora_sistema']  = 1 : $_SESSION['reporte_bitacora_sistema']  = 0;
      in_array(27, $valores) ? $_SESSION['registrar_trabajador']      = 1 : $_SESSION['registrar_trabajador']      = 0;
      in_array(28, $valores) ? $_SESSION['tipo_de_seguro']            = 1 : $_SESSION['tipo_de_seguro']            = 0;
      in_array(29, $valores) ? $_SESSION['boleta_de_pago']            = 1 : $_SESSION['boleta_de_pago']            = 0;
      in_array(30, $valores) ? $_SESSION['catalogo_de_codigo']        = 1 : $_SESSION['catalogo_de_codigo']        = 0;
      in_array(31, $valores) ? $_SESSION['correlativo_numeracion']    = 1 : $_SESSION['correlativo_numeracion']    = 0;
      in_array(32, $valores) ? $_SESSION['usuario']                   = 1 : $_SESSION['usuario']                   = 0;
      in_array(33, $valores) ? $_SESSION['general']                   = 1 : $_SESSION['general']                   = 0;
      in_array(34, $valores) ? $_SESSION['empresa']                   = 1 : $_SESSION['empresa']                   = 0;
      in_array(35, $valores) ? $_SESSION['papelera']                  = 1 : $_SESSION['papelera']                  = 0;
      in_array(36, $valores) ? $_SESSION['landing_page']              = 1 : $_SESSION['landing_page']              = 0;
     

      $data = [ 'status'=>true, 'message'=>'todo okey','data'=> $rspta['data']  ];
      echo json_encode($data, true);
    }else{
      $data = [ 'status'=>true, 'message'=>'todo okey','data'=>[]   ];
      echo json_encode($data, true);
    }
    
  break;

  case 'update_sistema':
    $rspta = $usuario->update_sistema($_GET["id_tabla"]);
    if ($rspta['status'] == true) {  $_SESSION['user_update_sistema'] = 1;  } # actualizamos el estado de sesion
    echo json_encode($rspta, true);
  break;

  case 'salir':     
    session_unset();  //Limpiamos las variables de sesión  
    session_destroy(); //Destruìmos la sesión
    // header("Location: ../index.php"); 
    header("Location: index.php?file=".(isset($_GET["file"]) ? $_GET["file"] : "")); //Redireccionamos al login
  break;    
}

ob_end_flush();

// switch ($reg->tipo_documento) {
//   case '01': $nombres = "FACTURA";   break;
//   case '03': $nombres = "BOLETA";   break;
//   case '07': $nombres = "NOTA DE CRÉDITO"; break;
//   case '08': $nombres = "NOTA DE DEBITO"; break;
//   case '09': $nombres = "GUIA REMISION REMITENTE"; break;
//   case '12': $nombres = "TICKET DE MAQUINA REGISTRADORA"; break;
//   case '13': $nombres = "DOCUM. EMIT. POR BANC. & SEG."; break;
//   case '18': $nombres = "SBS"; break;
//   case '31': $nombres = "DOC. EMIT. POR AFP"; break;
//   case '50': $nombres = "NOTA DE PEDIDO"; break;
//   case '56': $nombres = "GUIA REMISION TRANSPOR."; break;
//   case '99': $nombres = "ORDEN DE SERVICIO"; break;
//   case '20': $nombres = "COTIZACION"; break;
//   case '30': $nombres = "DOCUMENTO COBRANZA"; break;
//   case '90': $nombres = "BOLETAS DE PAGO"; break;
//   default:  break;
// }
