<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Ajax_general
{
	//Implementamos nuestro variable global
  public $id_usr_sesion;

  //Implementamos nuestro constructor
  public function __construct($id_usr_sesion = 0)
  {
    $this->id_usr_sesion = $id_usr_sesion;
  } 

  // ══════════════════════════════════════ RENIEC JDL - PAGA  ══════════════════════════════════════
  public function datos_reniec_jdl_paga($dni)	{ 
    $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzODY4NCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.PbAr1C2ChLTJNp3ZVCtJpSvI5Ji2mjqOwoE5PIrgjME';    

    // Iniciar llamada a API
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.factiliza.com/v1/dni/info/' . $dni,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array( 'Authorization: Bearer ' . $token ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);    
    return json_decode($response);
    
  }

  // ══════════════════════════════════════ RENIEC JDL ══════════════════════════════════════
  public function datos_reniec_jdl($dni) { 

    $url = "https://dniruc.apisperu.com/api/v1/dni/".$dni."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imp1bmlvcmNlcmNhZG9AdXBldS5lZHUucGUifQ.bzpY1fZ7YvpHU5T83b9PoDxHPaoDYxPuuqMqvCwYqsM";
    
    $curl = curl_init();                              //  Iniciamos curl    
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );  // Desactivamos verificación SSL    
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );  // Devuelve respuesta aunque sea falsa    
    curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Accept: application/json' ] );// Especificamo los MIME-Type que son aceptables para la respuesta.    
    curl_setopt( $curl, CURLOPT_URL, $url );          // Establecemos la URL    
    $json = curl_exec( $curl );                       // Ejecutmos curl    
    curl_close( $curl );                              // Cerramos curl
    return json_decode( $json, true );
  }

  // ══════════════════════════════════════ RENIEC WFACX ══════════════════════════════════════
  public function datos_reniec_otro($dni)	{ 
    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';    

    // Iniciar llamada a API
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $dni,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 2,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array( 'Referer: https://apis.net.pe/consulta-dni-api', 'Authorization: Bearer' . $token ),
    ));
    $response = curl_exec($curl); // Ejecutmos curl 
    curl_close($curl);            // Cerramos curl
    
    return json_decode($response);
  }

  // ══════════════════════════════════════ RENIEC CLOUD ══════════════════════════════════════
  public function datos_reniec_cloud($dni)	{ 
    $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxOTcsImV4cCI6MTc0MzQ2Mzc0N30.-oojENwNBpSBXREk-JoEXjyjBERkploTdx0dU-y20V0';    

    // Iniciar llamada a API
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://miapi.cloud/v1/dni/' . $dni,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array( 'Authorization: Bearer ' . $token ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);    
    return json_decode($response);
  }

  // ══════════════════════════════════════ SUNAT JDL PAGA ══════════════════════════════════════
  public function datos_sunat_jdl_paga($dni)	{ 
    $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzODY4NCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.PbAr1C2ChLTJNp3ZVCtJpSvI5Ji2mjqOwoE5PIrgjME';    

    // Iniciar llamada a API
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.factiliza.com/v1/ruc/info/' . $dni,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array( 'Authorization: Bearer ' . $token ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);    
    return json_decode($response);
    
  }

  // ══════════════════════════════════════ SUNAT JDL ══════════════════════════════════════
  public function datos_sunat_jdl($ruc)	{ 
    $url = "https://dniruc.apisperu.com/api/v1/ruc/".$ruc."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imp1bmlvcmNlcmNhZG9AdXBldS5lZHUucGUifQ.bzpY1fZ7YvpHU5T83b9PoDxHPaoDYxPuuqMqvCwYqsM";    
    $curl = curl_init();                              //  Iniciamos curl    
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );  // Desactivamos verificación SSL    
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );  // Devuelve respuesta aunque sea falsa    
    curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Accept: application/json' ] );// Especificamo los MIME-Type que son aceptables para la respuesta.    
    curl_setopt( $curl, CURLOPT_URL, $url );          // Establecemos la URL    
    $json = curl_exec( $curl );                       // Ejecutmos curl    
    curl_close( $curl );                              // Cerramos curl
    return json_decode( $json, true );
  }  
  

  // ══════════════════════════════════════ SUNAT WFACX ══════════════════════════════════════
  public function datos_sunat_otro($ruc)	{ 
    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';  

    // Iniciar llamada a API
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Referer: https://apis.net.pe/api-ruc',
        'Authorization: Bearer' . $token
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    // Datos listos para usar
    return json_decode($response);
  }

  /* ══════════════════════════════════════ S U N A T   ══════════════════════════════════════ */

  public function select2_tipo_documento()	{
    // $data = [];
		$sql="SELECT * FROM sunat_c06_doc_identidad";
		return ejecutarConsultaArray($sql);   
	}

  /* ══════════════════════════════════════ C L I E N T E   ══════════════════════════════════════ */
  public function update_nro_documento_cliente($idpersona_cliente, $numero_documento )	{
    // $data = [];
		$sql="UPDATE persona p
    JOIN persona_cliente pc ON p.idpersona = pc.idpersona
    SET p.numero_documento = '$numero_documento'
    WHERE pc.idpersona_cliente = '$idpersona_cliente';";
		return ejecutarConsulta($sql);   
	}

  // ══════════════════════════════════════ U S U A R I O - S E L E C T 2  ══════════════════════════════════════
	public function select2_usuario_trabajador($id)	{
    // $data = [];
		$sql="SELECT p.idpersona, p.nombre_razonsocial, p.apellidos_nombrecomercial, p.numero_documento,p.foto_perfil, ct.nombre as cargo 
		FROM persona p 
		LEFT JOIN usuario u ON p.idpersona = u.idpersona 
		INNER JOIN cargo_trabajador ct ON p.idcargo_trabajador = ct.idcargo_trabajador
		WHERE p.idtipo_persona = '2' and p.estado = '1' AND p.estado_delete = '1' AND u.idpersona IS NULL;";
		$select_1 = ejecutarConsultaArray($sql);		

    if ( empty($id) ) {
      return $select_1;
    }else{

      $sql="SELECT p.idpersona, p.nombre_razonsocial, p.apellidos_nombrecomercial, p.numero_documento,p.foto_perfil, ct.nombre as cargo 
      FROM persona p 
      LEFT JOIN usuario u ON p.idpersona = u.idpersona 
      INNER JOIN cargo_trabajador ct ON p.idcargo_trabajador = ct.idcargo_trabajador
      WHERE p.idtipo_persona = '2' and p.estado = '1' AND p.estado_delete = '1' AND u.idusuario = '$id';";
      $select_2 = ejecutarConsultaSimpleFila($sql);

      if ( empty($select_2['data']) ) {    }else{
        $data = [
          'idpersona'                 =>$select_2['data']['idpersona'],
          'nombre_razonsocial'        =>$select_2['data']['nombre_razonsocial'],
          'apellidos_nombrecomercial' =>$select_2['data']['apellidos_nombrecomercial'],
          'numero_documento'          =>$select_2['data']['numero_documento'],
          'foto_perfil'               =>$select_2['data']['foto_perfil'],
          'cargo'                     =>$select_2['data']['cargo'],
        ];
        array_push( $select_1['data'], $data);
      }
      
      return $retorno = ['status'=>true, 'mesage'=>'Todo bien', 'data'=>$select_1['data'], ]; 
    }
	}

  // ══════════════════════════════════════ U S U A R I O - S E L E C T 2  ══════════════════════════════════════
	public function select2_cargo()	{
    // $data = [];
		$sql="SELECT * FROM cargo_trabajador WHERE estado='1' AND estado_delete='1'";
		return ejecutarConsultaArray($sql);   
	}

  // ══════════════════════════════════════ B A N C O - S E L E C T 2  ══════════════════════════════════════
	public function select2_banco()	{
    // $data = [];
		$sql="SELECT * FROM bancos WHERE estado='1' AND estado_delete = '1' order by case when idbancos = 1 then 0 else 1 end, nombre asc";
		return ejecutarConsultaArray($sql);   
	}

  // ══════════════════════════════════════ C A T E G O R I A   O T R O S   G A S T O S - S E L E C T 2  ══════════════════════════════════════
	public function listar_ie_categoria()	{
		$sql="SELECT * FROM ingreso_egreso_categoria WHERE estado='1' AND estado_delete = '1'";
		return ejecutarConsultaArray($sql);   
	}
  // ══════════════════════════════════════ S E R I E   C O M P R O B A N T E - S E L E C T 2  ══════════════════════════════════════
  public function select2_series_comprobante( $codigo )	{
    // $data = [];
		$sql="SELECT stp.abreviatura,  stp.serie
      FROM sunat_usuario_comprobante as suc
      INNER JOIN sunat_c01_tipo_comprobante as stp ON stp.idtipo_comprobante = suc.idtipo_comprobante
      WHERE stp.codigo = '$codigo'   AND suc.idusuario = '$this->id_usr_sesion'";
		return ejecutarConsultaArray($sql);   
	}

  // ══════════════════════════════════════ L I S T A   C O M P R O B A N T E - S E L E C T 2  ══════════════════════════════════════
  public function select2_lista_comprobante(  )	{    
		$sql="SELECT v.idventa, CASE v.tipo_comprobante WHEN '01' THEN 'FACTURA' WHEN '03' THEN 'BOLETA' WHEN '12' THEN 'TICKET' ELSE '-' END AS nombre_tipo_comprobante,
    v.venta_total, v.serie_comprobante, v.numero_comprobante, case when vcp.idventa_cuota_pago is null then 'VENTA' ELSE 'PAGO' END AS es_pago_o_venta
    FROM venta AS v 
    LEFT JOIN venta_cuota_pago as vcp on vcp.idventa = v.idventa
    WHERE v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01', '03', '12') ";
		return ejecutarConsultaArray($sql);   
	}

  // ══════════════════════════════════════ P R O D U C T O  ══════════════════════════════════════

  public function create_code_producto($pre_codigo)	{
    
		// Consulta para obtener el último código que comienza con 'PR' de la tabla: where codigo like 'PR%'
    $sql = "SELECT max(idproducto) as last_code from producto";
    $result = ejecutarConsultaSimpleFila($sql);
    
    $last_code = $result['data']['last_code'];

    if ($last_code == NULL) {
      $new_num = 1;                               # SE INICIA EN EL NUMERO 1 SI NO HAY REGISTROS
    } else {      
      // $num_part = (int) substr($last_code, 2); # RECORTE EL PRE NOMBRE      
      $new_num  = $last_code + 1;                 # AUMENTAMOS +1
    }

    $new_code = $pre_codigo.str_pad($new_num, 5, "0", STR_PAD_LEFT); # CREAMOS EL CODIGO

    return ['status' => true,  'message' => 'Salió todo ok',  'data' => ['nombre_codigo' => $new_code, 'pre_codigo' => $pre_codigo, 'numeracion' => $new_num] ] ; 
	}

}
