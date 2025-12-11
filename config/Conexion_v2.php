<?php
require_once "global_local.php";
//require_once "global_nube.php";
require "../config/funcion_general.php";

$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

$id_usr_sesion 			=  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
$id_empresa_sesion 	= isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;

mysqli_query($conexion, 'SET NAMES "' . DB_ENCODE . '"');         # Para el tipo de datos, ejemlo: UTF8
mysqli_query($conexion, "SET @id_usr_sesion ='$id_usr_sesion' "); # Para saber quien hizo el CRUD
mysqli_query($conexion, "SET time_zone = '-05:00';");       # Cambia el horario local: America/Lima
mysqli_query($conexion, "SET lc_time_names = 'es_ES';");          # Cambia el idioma a español en fechas

//Si tenemos un posible error en la conexión lo mostramos
if (mysqli_connect_errno()) {
  printf("Falló conexión a la base de datos: %s\n", mysqli_connect_error());
  exit();
}

if (!function_exists('ejecutarConsulta')) {

  function ejecutarConsulta($sql, $crud = 'R', $crud_observacion = '' ) {
    global $conexion; mysqli_query($conexion, "SET @crud ='$crud' ");  mysqli_query($conexion, "SET @crud_observacion ='$crud_observacion' ");
    $query = $conexion->query($sql);
    if ($conexion->error) {
      try {
        throw new Exception("MySQL error <b> $conexion->error </b> Query:<br> $query", $conexion->errno);
      } catch (Exception $e) {
        //echo "Error No: " . $e->getCode() . " - " . $e->getMessage() . "<br >"; echo nl2br($e->getTraceAsString());
        return array( 
          'status'      => false, 
          'code_error'  => $e->getCode(), 
          'message'     => $e->getMessage(), 
          'data'        => '<br><b>Rutas de errores:</b> <br>'.nl2br($e->getTraceAsString()),
        );          
      }
    } else {
      return array( 
        'status'        => true, 
        'code_error'    => $conexion->errno, 
        'message'       => 'Salió todo ok, en ejecutarConsulta()', 
        'data'          => $query, 
        'id_tabla'      => $conexion->insert_id,
        'affected_rows' => $conexion->affected_rows,
        'sqlstate'      => $conexion->sqlstate,
        'field_count'   => $conexion->field_count,
        'warning_count' => $conexion->warning_count, 
      );
    }
    $query->close(); // Cerrar la consulta   
    $conexion->close();  // Cerrar la conexión
  }

  function ejecutarConsultaSimpleFila($sql) {
    global $conexion;
    $query = $conexion->query($sql);
    if ($conexion->error) {
      try {
        throw new Exception("MySQL error <b> $conexion->error </b> Query:<br> $query", $conexion->errno);
      } catch (Exception $e) {
        //echo "Error No: " . $e->getCode() . " - " . $e->getMessage() . "<br >"; echo nl2br($e->getTraceAsString());
        $data_errores = array( 
          'status'      => false, 
          'code_error'  => $e->getCode(), 
          'message'     => $e->getMessage(), 
          'data'        => '<br><b>Rutas de errores:</b> <br>'.nl2br($e->getTraceAsString()),
        );
        return $data_errores;
      }

    } else {
      $row = $query->fetch_assoc();
      return array( 
        'status'        => true, 
        'code_error'    => $conexion->errno, 
        'message'       => 'Salió todo ok, en ejecutarConsultaSimpleFila()', 
        'data'          => $row, 
        'id_tabla'      => '',
        'affected_rows' => $conexion->affected_rows,
        'sqlstate'      => $conexion->sqlstate,
        'field_count'   => $conexion->field_count,
        'warning_count' => $conexion->warning_count, 
      );
    }
    $query->close(); // Cerrar la consulta   
    $conexion->close();  // Cerrar la conexión
  }

  function ejecutarConsultaArray($sql) {
    global $conexion;  //$data= Array();	$i = 0;

    $query = $conexion->query($sql);

    if ($conexion->error) {
      try {
        throw new Exception("MySQL error <b> $conexion->error </b> Query:<br> $query", $conexion->errno);
      } catch (Exception $e) {
        //echo "Error No: " . $e->getCode() . " - " . $e->getMessage() . "<br >"; echo nl2br($e->getTraceAsString());
        return array( 
          'status' => false, 
          'code_error'  => $e->getCode(), 
          'message'     => $e->getMessage(), 
          'data'        => '<br><b>Rutas de errores:</b> <br>'.nl2br($e->getTraceAsString()),
        );          
      }
    } else {
      for ($data = []; ($row = $query->fetch_assoc()); $data[] = $row);
      return  array( 
        'status'        => true, 
        'code_error'    => $conexion->errno, 
        'message'       => 'Salió todo ok, en ejecutarConsultaArray()', 
        'data'          => $data, 
        'id_tabla'      => '',
        'affected_rows' => $conexion->affected_rows,
        'sqlstate'      => $conexion->sqlstate,
        'field_count'   => $conexion->field_count,
        'warning_count' => $conexion->warning_count, 
      );
    }
    $query->close(); // Cerrar la consulta   
    $conexion->close();  // Cerrar la conexión
  }

  function ejecutarConsulta_retornarID($sql, $crud = 'R', $crud_observacion = '') {
    global $conexion; mysqli_query($conexion, "SET @crud ='$crud' "); mysqli_query($conexion, "SET @crud_observacion ='$crud_observacion' ");
    $query = $conexion->query($sql);
    if ($conexion->error) {
      try {
        throw new Exception("MySQL error <b> $conexion->error </b> Query:<br> $query", $conexion->errno);
      } catch (Exception $e) {
        //echo "Error No: " . $e->getCode() . " - " . $e->getMessage() . "<br >"; echo nl2br($e->getTraceAsString());
        return array( 
          'status' => false, 
          'code_error'  => $e->getCode(), 
          'message'     => $e->getMessage(), 
          'data'        => '<br><b>Rutas de errores:</b> <br>'.nl2br($e->getTraceAsString()),
        );          
      }
    } else {
      return  array( 
        'status' => true, 
        'code_error'    => $conexion->errno, 
        'message'       => 'Salió todo ok, en ejecutarConsulta_retornarID()', 
        'data'          => $conexion->insert_id, 
        'id_tabla'      => $conexion->insert_id,
        'affected_rows' => $conexion->affected_rows,
        'sqlstate'      => $conexion->sqlstate,
        'field_count'   => $conexion->field_count,
        'warning_count' => $conexion->warning_count, 
      );
    }
    $query->close(); // Cerrar la consulta   
    $conexion->close();  // Cerrar la conexión
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
