<?php
#debes solo  c o m e n t a r  una de ellas

$message = "Hoy es Sábado, todos descanzan, el sistema también.";
$estado = "mantenimiento";

$message = "Estamos en mantenimiento, esperemos su comprension";
$estado = "es_sabado";

// if (false) {  
if (!function_exists('ejecutarConsulta')) {  

  function ejecutarConsulta($sql) {    
    global $estado; global $message;
    return array( 
      'status'        =>  $estado, 
      'code_error'    => '', 
      'message'       => $message, 
      'data'          => [], 
      'id_tabla'      => '',
      'affected_rows' => 0,
      'sqlstate'      => '',
      'field_count'   => '',
      'warning_count' => '', 
    );    
  }

  function ejecutarConsultaSimpleFila($sql) {
    global $estado; global $message;
    return array( 
      'status'        =>  $estado, 
      'code_error'    => '', 
      'message'       => $message, 
      'data'          => [], 
      'id_tabla'      => '',
      'affected_rows' => 0,
      'sqlstate'      => '',
      'field_count'   => '',
      'warning_count' => '', 
    );
  }

  function ejecutarConsultaArray($sql) {
    global $estado; global $message;
    return array( 
      'status'        =>  $estado, 
      'code_error'    => '', 
      'message'       => $message, 
      'data'          => [], 
      'id_tabla'      => '',
      'affected_rows' => 0,
      'sqlstate'      => '',
      'field_count'   => '',
      'warning_count' => '', 
    );
  }

  function ejecutarConsulta_retornarID($sql) {
    global $estado; global $message;
    return array( 
      'status'        =>  $estado, 
      'code_error'    => '', 
      'message'       => $message, 
      'data'          => [], 
      'id_tabla'      => '',
      'affected_rows' => 0,
      'sqlstate'      => '',
      'field_count'   => '',
      'warning_count' => '', 
    );
  }

  function limpiarCadena($str) {
    // htmlspecialchars($str);
    global $conexion;
    $str = mysqli_real_escape_string($conexion, trim($str));
    return $str;
  }

  function encodeCadenaHtml($str) {
    // htmlspecialchars($str);
    global $conexion;
    $encod = "UTF-8";
    $str = mysqli_real_escape_string($conexion, trim($str));
    return htmlspecialchars($str, ENT_QUOTES);
  }

  function decodeCadenaHtml($str) {
    $encod = "UTF-8";
    return htmlspecialchars_decode($str, ENT_QUOTES);
  }
}

?>
