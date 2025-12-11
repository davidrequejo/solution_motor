<?php
require_once "../sunat/ConfigSunat.php";

ob_start();

if (strlen(session_id()) < 1) {
  session_start();
} //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {

  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => []];
  echo json_encode($retorno, true);  //Validamos el acceso solo a los usuarios logueados al sistema.

} else {

  require_once "../modelos/Ajax_general.php";
  require_once "../modelos/Ubigeo.php";
  require_once "../modelos/Centro_poblado.php";

  $ajax_general         = new Ajax_general($_SESSION['idusuario']);
  $ajax_ubigeo          = new Ubigeo();
  $ajax_centro_poblado  = new CentroPoblado();

  $scheme_host  =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/solution_motor/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');
  $imagen_error = "this.src='../dist/svg/404-v2.svg'";
  $toltip       = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

  switch ($_GET["op"]) {

    /* ══════════════════════════════════════ A P I S  ══════════════════════════════════════ */

    // RENIEC JDL
    case 'reniec_jdl':
      $dni = $_POST["dni"] ?? '';
      $dni = preg_replace('/\D/', '', $dni); // solo números

      // (opcional) valida DNI
      if (strlen($dni) !== 8) {
        echo json_encode([]);
        break;
      }

      // Normaliza todas las respuestas al mismo shape
      $normalize = function (string $version, array $d): array {
        return [
          "version"          => $version,
          "success"          => true,
          "dni"              => $d['dni'] ?? '',
          "nombres"          => $d['nombres'] ?? '',
          "apellidoPaterno"  => $d['apellidoPaterno'] ?? '',
          "apellidoMaterno"  => $d['apellidoMaterno'] ?? '',
          "codVerifica"      => $d['codVerifica'] ?? '',
          "codVerificaLetra" => $d['codVerificaLetra'] ?? '',
          "direccion"        => $d['direccion'] ?? '',
          "distrito"         => $d['distrito'] ?? '',
          "provincia"        => $d['provincia'] ?? '',
          "departamento"     => $d['departamento'] ?? '',
          "ubigeo"           => $d['ubigeo'] ?? '',
        ];
      };

      // Proveedores en orden de prioridad
      $providers = [

        // vP: JDL paga (objeto con ->success)
        function () use ($ajax_general, $dni, $normalize) {
          $r = $ajax_general->datos_reniec_jdl_paga($dni);
          if ($r && ($r->success ?? false) === true) {
            return $normalize('vp', [
              'dni'              => $r->data->numero            ?? '',
              'nombres'          => $r->data->nombres           ?? '',
              'apellidoPaterno'  => $r->data->apellido_paterno  ?? '',
              'apellidoMaterno'  => $r->data->apellido_materno  ?? '',
              'codVerifica'      => $r->data->codigo_verificacion ?? '',
              'codVerificaLetra' => '',
              'direccion'        => $r->data->direccion_completa ?? '',
              'distrito'         => $r->data->distrito          ?? '',
              'provincia'        => $r->data->provincia         ?? '',
              'departamento'     => $r->data->departamento      ?? '',
              'ubigeo'           => $r->data->ubigeo_reniec     ?? '',
            ]);
          }
          return [];
        },

        // v1: JDL (array con ['success'])
        function () use ($ajax_general, $dni, $normalize) {
          $r = $ajax_general->datos_reniec_jdl($dni);
          if (is_array($r) && array_key_exists('success', $r) && $r['success'] === true) {
            return $normalize('v1', [
              'dni'              => $r['dni'] ?? '',
              'nombres'          => $r['nombres'] ?? '',
              'apellidoPaterno'  => $r['apellidoPaterno'] ?? '',
              'apellidoMaterno'  => $r['apellidoMaterno'] ?? '',
              'codVerifica'      => $r['codVerifica'] ?? '',
              'codVerificaLetra' => $r['codVerificaLetra'] ?? '',
            ]);
          }
          return [];
        },

        // v2: otro (objeto simple)
        function () use ($ajax_general, $dni, $normalize) {
          $r = $ajax_general->datos_reniec_otro($dni);
          return $r;
          if (!empty($r) && array_key_exists('nombres', $r)) {
            return $normalize('v2', [
              'dni'              => $r->numeroDocumento ?? '',
              'nombres'          => $r->nombres ?? '',
              'apellidoPaterno'  => $r->apellidoPaterno ?? '',
              'apellidoMaterno'  => $r->apellidoMaterno ?? '',
              'direccion'        => $r->direccion ?? '',
              'distrito'         => $r->distrito ?? '',
              'provincia'        => $r->provincia ?? '',
              'departamento'     => $r->departamento ?? '',
              'ubigeo'           => $r->ubigeo ?? '',
            ]);
          }
          return [];
        },

        // v3: cloud (objeto con ->success y ->datos)
        function () use ($ajax_general, $dni, $normalize) {
          $r = $ajax_general->datos_reniec_cloud($dni);
          if ($r && isset($r->success) && $r->success) {
            $d = $r->datos ?? (object)[];
            $dom = $d->domiciliado ?? (object)[];
            return $normalize('v3', [
              'dni'              => $d->dni ?? '',
              'nombres'          => $d->nombres ?? '',
              'apellidoPaterno'  => $d->ape_paterno ?? '',
              'apellidoMaterno'  => $d->ape_materno ?? '',
              'direccion'        => $dom->direccion ?? '',
              'distrito'         => $dom->distrito ?? '',
              'provincia'        => $dom->provincia ?? '',
              'departamento'     => $dom->departamento ?? '',
              'ubigeo'           => $dom->ubigeo ?? '',
            ]);
          }
          return [];
        },
      ];

      // Ejecuta en orden y retorna el primero que funcione
      $result = [];
      foreach ($providers as $provider) {
        try {
          $result = $provider();
          if (!empty($result)) break;
        } catch (\Throwable $e) {
          echo json_encode([]);
          // opcional: loguea y sigue con el siguiente proveedor
          // error_log($e);
        }
      }

      if (array_key_exists('success', (array)$result)) {
        echo json_encode($result ?? []);
        die();
      }

      echo json_encode([]);
    break;

    // RENIEC 
    case 'reniec_jdl_paga':
      $dni = $_POST["dni"];
      $rspta = $ajax_general->datos_reniec_jdl_paga($dni);
      $data_api = [];

      if ($rspta && ($rspta->success ?? false) === true) {
        $data_api = [
          'dni'              => $rspta->data->numero              ?? '',
          'nombres'          => $rspta->data->nombres             ?? '',
          'apellidoPaterno'  => $rspta->data->apellido_paterno    ?? '',
          'apellidoMaterno'  => $rspta->data->apellido_materno    ?? '',
          'codVerifica'      => $rspta->data->codigo_verificacion ?? '',
          'codVerificaLetra' => '',
          'direccion'        => $rspta->data->direccion_completa  ?? '',
          'distrito'         => $rspta->data->distrito            ?? '',
          'provincia'        => $rspta->data->provincia           ?? '',
          'departamento'     => $rspta->data->departamento        ?? '',
          'ubigeo'           => $rspta->data->ubigeo_reniec       ?? '',
        ];
      }
      echo json_encode($data_api, true);
    break;

    case 'sunat_jdl_paga':
      $ruc = $_POST["ruc"];
      $rspta = $ajax_general->datos_sunat_jdl_paga($ruc);
      $data_api = [];

      if ($rspta && ($rspta->success ?? false) === true) {
        $data_api = [
          'ruc'               => $rspta->data->numero            ?? '',
          'razonSocial'       => $rspta->data->nombre_o_razon_social       ?? '',
          'nombreComercial'   => '',
          'estado'            => $rspta->data->estado            ?? '',
          'condicion'         => $rspta->data->condicion         ?? '',
          'direccion'         => $rspta->data->direccion         ?? '',
          'direccion_completa'=> $rspta->data->direccion_completa ?? '',
          'distrito'          => $rspta->data->distrito          ?? '',
          'provincia'         => $rspta->data->provincia         ?? '',
          'departamento'      => $rspta->data->departamento      ?? '',
          'ubigeo'            => $rspta->data->ubigeo_sunat      ?? '',
        ];
      }
      echo json_encode($data_api, true);
    break;

    // SUNAT JDL
    case 'sunat_jdl':
      $ruc = $_POST["ruc"];
      $rspta = $ajax_general->datos_sunat_jdl($ruc);
      echo json_encode($rspta, true);
    break;
    // SUNAT WFACX
    case 'sunat_otro':
      $ruc = $_POST["ruc"];
      $rspta = $ajax_general->datos_sunat_otro($ruc);
      echo json_encode($rspta);
    break;

    /* ══════════════════════════════════════ S U N A T   ══════════════════════════════════════ */

    case 'select2_tipo_documento':
      $rspta = $ajax_general->select2_tipo_documento();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['code_sunat'] . '" title ="' . $value['nombre'] . '" >' . $value['abreviatura'] . '</option>';
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

    case 'selectChoice_tipo_documento':
      $rspta = $ajax_general->select2_tipo_documento();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = ['value' => $value['code_sunat'], 'label' => $value['abreviatura'], 'disabled'  => false, 'selected'  => false,];
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

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2    D E P A R T A M E N T O ══════════════════════════════════════
    case 'select2_departamento':
      $rspta = $ajax_ubigeo->select2_departamento();
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" iddepartamento = "' . $reg->iddepartamento . '" macroregion_minsa = "' . $reg->macroregion_minsa . '" iso_3166_2 = "' . $reg->iso_3166_2 . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_departamento_id':
      $rspta = $ajax_ubigeo->select2_departamento_id($_GET['id']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2    P R O V I N C I A ══════════════════════════════════════
    case 'select2_provincia':
      $rspta = $ajax_ubigeo->select2_provincia();
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" idprovincia = "' . $reg->idprovincia . '" iddepartamento = "' . $reg->iddepartamento . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_provincia_departamento':
      $rspta = $ajax_ubigeo->select2_provincia_departamento($_GET['id']);
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value=' . $reg->nombre . ' idprovincia = "' . $reg->idprovincia . '" iddepartamento = "' . $reg->iddepartamento . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_provincia_id':
      $rspta = $ajax_ubigeo->select2_provincia_id($_GET['id']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2    D I S T R I T O ══════════════════════════════════════
    case 'select2_distrito':
      $rspta = $ajax_ubigeo->select2_distrito();
      $data = "";
      if ($rspta['status'] == true) {
        foreach ($rspta['data'] as $key => $reg) {
          $data .= '<option value="' . $reg['nombre'] . '" title="' . $reg['provincia'] . '" data-iddistrito="' . $reg['idubigeo_distrito'] . '"  iddistrito="' . $reg['idubigeo_distrito'] . '" iddepartamento= "' . $reg['idubigeo_departamento'] . '" ubigeo_inei="' . $reg['ubigeo_inei'] . '" latitud="' . $reg['latitud'] . '" longitud="' . $reg['longitud'] . '" Frontera="' . $reg['frontera'] . '" >' . $reg['nombre'] .' | '. $reg['provincia'] .' | '. $reg['departamento'] . '</option>';
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

    case 'selectChoice_distrito':
      $rspta = $ajax_ubigeo->select2_distrito();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = [
            'value' => $value['nombre'], 'label' => '📌 ' . $value['nombre'] . ' | ' .  $value['provincia'] . ' | ' .  $value['departamento'], 'disabled'  => false, 'selected'  =>  false , 
            'customProperties' => [
              'idubigeo_distrito' => $value['idubigeo_distrito'],
              'idubigeo_provincia' => $value['idubigeo_provincia'],
              'idubigeo_departamento' => $value['idubigeo_departamento']
            ]
          ];
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
    
    // ══════════════════════════════════════ U B I G E O - S E L E C T 2   C E N T R O   P O B L A D O ══════════════════════════════════════
    case 'selectChoice_centro_poblado':
      $rspta = $ajax_centro_poblado->select2_centro_poblado();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = [
            'value' => $value['nombre']  , 'label' => '📌 ' . $value['nombre'] , 'disabled'  => false, 'selected'  =>  false , 
            'customProperties' => [
              'idubigeo_distrito' => 0,
              'idubigeo_provincia' => 0,
              'idubigeo_departamento' => 0
            ]
          ];
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

    case 'select2_distrito_departamento':
      $rspta = $ajax_ubigeo->select2_distrito_departamento($_GET['id']);
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" iddistrito="' . $reg->iddistrito . '" iddepartamento= "' . $reg->iddepartamento . '" ubigeo_inei="' . $reg->ubigeo_inei . '" latitud="' . $reg->latitud . '" longitud="' . $reg->longitud . '" Frontera="' . $reg->Frontera . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_distrito_provincia':
      $rspta = $ajax_ubigeo->select2_distrito_provincia($_GET['id']);
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" iddistrito="' . $reg->iddistrito . '" iddepartamento= "' . $reg->iddepartamento . '" ubigeo_inei="' . $reg->ubigeo_inei . '" latitud="' . $reg->latitud . '" longitud="' . $reg->longitud . '" Frontera="' . $reg->Frontera . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_distrito_id':
      $rspta = $ajax_ubigeo->select2_distrito_id($_GET['id']);
      echo json_encode($rspta, true);
    break;

    case 'select2_distrito_nombre':
      $rspta = $ajax_ubigeo->select2_distrito_nombre($_GET['search_nombre']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2   C E N T R O   P O B L A D O ══════════════════════════════════════
    case 'select2_centro_poblado_cliente':
      $rspta = $ajax_centro_poblado->select2_centro_poblado(); $data = "";
      while ($reg = $rspta['data']->fetch_object()) {
        $data .= '<option value="' . $reg->idcentro_poblado . '"   >' . $reg->nombre .'('.$reg->cant_cliente .')'. '</option>';
      }

      $retorno = array(
        'status' => true,
        'message' => 'Salió todo ok',
        'data' => $data,
      );
      echo json_encode($retorno, true);
    break;

    case 'select2_centro_poblado_venta':
      $rspta = $ajax_centro_poblado->select2_centro_poblado(); $data = "";
      while ($reg = $rspta['data']->fetch_object()) {
        $data .= '<option value="' . $reg->idcentro_poblado . '"   >' . $reg->nombre .'('.$reg->cant_venta .')'. '</option>';
      }

      $retorno = array(
        'status' => true,
        'message' => 'Salió todo ok',
        'data' => $data,
      );
      echo json_encode($retorno, true);
    break;

    // ══════════════════════════════════════ C L I E N T E  ══════════════════════════════════════
    case 'update_nro_documento_cliente':
      $rspta = $ajax_general->update_nro_documento_cliente($_GET['idpersona_cliente'], $_GET['numero_documento']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U S U A R I O - S E L E C T 2  ══════════════════════════════════════
    case 'select2_usuario_trabajador':
      $rspta = $ajax_general->select2_usuario_trabajador($_GET['id']);
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value=' . $value['idpersona'] . ' title="' . $value['foto_perfil'] . '" cargo="' . $value['cargo'] . '">' . $value['nombre_razonsocial'] . ' ' . $value['apellidos_nombrecomercial'] . ' - ' . $value['numero_documento'] . '</option>';
        }

        $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );

        echo json_encode($retorno, true);
      } else {
        echo json_encode($rspta, true);
      }
    break;

    // ══════════════════════════════════════ C A R G O - S E L E C T 2  ══════════════════════════════════════
    case 'select2_cargo':
      $rspta = $ajax_general->select2_cargo();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['idcargo_trabajador'] . '"  >' . $value['nombre']  . '</option>';
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

    // ══════════════════════════════════════ B A N C O - S E L E C T 2  ══════════════════════════════════════
    case 'select2_banco':
      $rspta = $ajax_general->select2_banco();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['idbancos'] . '"  >' . $value['nombre']  . '</option>';
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

    case 'selectChoice_banco':
      $rspta = $ajax_general->select2_banco();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = ['value' => $value['idbancos'], 'label' => $value['nombre'], 'disabled'  => false, 'selected'  => false,];
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

    // ══════════════════════════════════════ C A T E G O R I A   G A S T O S - S E L E C T  ══════════════════════════════════════
    case 'listar_ie_categoria':
      $rspta = $ajax_general->listar_ie_categoria();
      $data = "";

      if ($rspta['status']) {
        if (!empty($rspta['data'])){

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idingreso_egreso_categoria'] . '"  >' . $value['nombre']  . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );

          echo json_encode($retorno, true);

        }else {

          $data = '<option value="0" disabled selected>Categoría Vacía</option>';
          $retorno = array(
              'status' => true,
              'message' => 'Categoría Vacía',
              'data' => $data,
            );

            echo json_encode($retorno, true);
            
        }

          
      } else {
        echo json_encode($rspta, true);
      }
    break;

    // ══════════════════════════════════════ S E R I E   C O M P R O B A N T E  -  S E L E C T 2  ══════════════════════════════════════
    case 'select2_series_comprobante':
      $rspta = $ajax_general->select2_series_comprobante($_GET["tipo_comprobante"] ); $cont = 1; $data = "";
      if($rspta['status'] == true){
        foreach ($rspta['data'] as $key => $value) {
          $data .= '<option title="' . $value['abreviatura'] . '" value="' . $value['serie']  . '">' . $value['serie']  . '</option>';
        }

        $retorno = array(
          'status'  => true, 
          'message' => 'Salió todo ok', 
          'data'    => $data, 
        );
        echo json_encode($retorno, true);

      } else { echo json_encode($rspta, true); }      
    break; 

    // ══════════════════════════════════════ L I S T A   C O M P R O B A N T E  -  S E L E C T 2  ══════════════════════════════════════
    case 'select2_lista_comprobante':
      $rspta = $ajax_general->select2_lista_comprobante( ); $cont = 1; $data = "";
      if($rspta['status'] == true){
        foreach ($rspta['data'] as $key => $value) {
          $data .= '<option  value="' . $value['idventa']  . '">' . $value['nombre_tipo_comprobante'] .': '. $value['serie_comprobante'] .'-'. $value['numero_comprobante'] .' &boxh; S/. '. $value['venta_total'] .' &boxh; '. $value['es_pago_o_venta']. '</option>';
        }
        $retorno = array( 'status'  => true, 'message' => 'Salió todo ok', 'data'    => $data, );
        echo json_encode($retorno, true);
      } else { echo json_encode($rspta, true); }      
    break; 

    // ══════════════════════════════════════ P R O D U C T O  ══════════════════════════════════════
    case 'create_code_producto' :
      $rspta = $ajax_general->create_code_producto($_GET["pre_codigo"]);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ DEFAULT ══════════════════════════════════════
    default:
      $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
      echo json_encode($rspta, true);
    break;
  }
}

ob_end_flush();
