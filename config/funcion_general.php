<?php

require_once "../modelos/Fechas.php";

// validamos la repeticion de funciones
if (!function_exists('ejecutarConsulta')) {  

  /*  ══════════════════════════════════════════ - F E C H A S - ══════════════════════════════════════════ */

  function nombre_dia_mes_anio( $fecha_entrada ) {

    $fecha_parse = new FechaEs($fecha_entrada);
    $dia = $fecha_parse->getDDDD().PHP_EOL;
    $mun_dia = $fecha_parse->getdd().PHP_EOL;
    $mes = $fecha_parse->getMMMM().PHP_EOL;
    $anio = $fecha_parse->getYYYY().PHP_EOL;
    $fecha_nombre_completo = "$dia, <br> $mun_dia de <b>$mes</b>  del $anio";

    return $fecha_nombre_completo;
  }

  function nombre_mes( $fecha_entrada ) {

    $fecha_parse = new FechaEs($fecha_entrada);
    
    $mes_nombre = $fecha_parse->getMMMM().PHP_EOL;

    return $mes_nombre;
  }

  // NOMBRE DIA DE SEMANA
  function nombre_dia_semana($fecha) {

    $nombre_dia_semana = "";

    if (!empty($fecha) || $fecha != '0000-00-00') {

      $fechas = new FechaEs($fecha);

      $dia = $fechas->getDDDD().PHP_EOL;

      $nombre_dia_semana = $dia;
    }

    return $nombre_dia_semana;
  }

  function sumar_dias( $cant, $fecha )  {    
    return date("Y-m-d",strtotime( "$cant days" , strtotime( $fecha ) ) ); 
  }

  function validar_fecha_menor_que($fecha_menor, $fecha_mayor) {
    $fecha_1 = strtotime( $fecha_menor );
    $fecha_2 = strtotime( $fecha_mayor );
    if ($fecha_1 < $fecha_2) { return true; }    
    return false;
  }

  function validar_fecha_menor_igual_que($fecha_menor, $fecha_mayor) {
    $fecha_1 = strtotime( $fecha_menor );
    $fecha_2 = strtotime( $fecha_mayor );
    if ($fecha_1 <= $fecha_2) { return true; }    
    return false;
  }

  // convierte de una fecha(dd-mm-aa): 23-12-2021 a una fecha(aa-mm-dd): 2021-12-23
  function format_a_m_d( $fecha ) {
    $fecha_convert = "";
    if (empty($fecha) || $fecha == '0000-00-00') { }else{
      $fecha_expl = explode("-", $fecha);
      $fecha_convert =  $fecha_expl[2]."-".$fecha_expl[1]."-".$fecha_expl[0];
    }
    return $fecha_convert;
  }

  // convierte de una fecha(aa-mm-dd): 2021-12-23 a una fecha(dd-mm-aa): 23-12-2021
  function format_d_m_a( $fecha ) {
    $fecha_convert = "";
    if (empty($fecha) || $fecha == '0000-00-00') { }else{
      $fecha_expl = explode("-", $fecha);
      $fecha_convert =  $fecha_expl[2]."-".$fecha_expl[1]."-".$fecha_expl[0];
    }
    return $fecha_convert;
  }

  function diferencia_days_months_years($fecha_1, $fecha_2, $tipo = 'days') {

    $diferencia = 0;

    if (empty($fecha_1) || $fecha_1 == '0000-00-00' || empty($fecha_2) || $fecha_2 == '0000-00-00' ) { }else{      
      $fecha_2 = new DateTime($fecha_2);
      $fecha_1 = new DateTime($fecha_1);
      $diferencia = $fecha_2->diff($fecha_1);
      return $diferencia->days;      
    }    
    return $diferencia;
  }
  function diferencia_days_now($fecha) {

    $diferencia = 0;

    if (empty($fecha) || $fecha == '0000-00-00'  ) { }else{      
      $hoy = new DateTime();
      $fecha = new DateTime($fecha);
      $diferencia = $hoy->diff($fecha);
      return $diferencia->days;      
    }    
    return $diferencia;
  }

  function diferencia_days($fecha1, $fecha2) {

    $diferencia = 0;

    if (empty($fecha1) || $fecha1 == '0000-00-00' || empty($fecha2) || $fecha2 == '0000-00-00'  ) { }else{      
      $hoy = new DateTime($fecha2);
      $fecha1 = new DateTime($fecha1);
      $diferencia = $hoy->diff($fecha1);
      return $diferencia->days;      
    }    
    return $diferencia;
  }

  function extr_fecha_creacion($reg_fecha) {
    
    $fecha = "";
    if ($reg_fecha == '' || $reg_fecha == null || $reg_fecha == '0000-00-00') { $fecha = "-"; } else {
  
      $fechaEntera = strtotime($reg_fecha);
  
      $anio = date("Y", $fechaEntera);
      $mes = date("m", $fechaEntera);
      $dia = date("d", $fechaEntera);
      
      $fecha = "$dia-$mes-$anio";
    } 
  
    return $fecha;
  }

  function calculaedad($fechanacimiento){
    $ano_diferencia = '-';
    if (empty($fechanacimiento) || $fechanacimiento=='0000-00-00') { } else{
      list($ano,$mes,$dia) = explode("-",$fechanacimiento);
      $ano_diferencia  = date("Y") - $ano;
      $mes_diferencia = date("m") - $mes;
      $dia_diferencia   = date("d") - $dia;
      if ($dia_diferencia < 0 || $mes_diferencia < 0)
        $ano_diferencia--;
    } 
    
    return $ano_diferencia;
  }

  function calcular_edad($fecha)  {
    if (empty($fecha) || $fecha=='0000-00-00') {  return 0;  } else{   
      
      $fecha_nacimiento = new DateTime($fecha); // Crear un objeto DateTime para la fecha de nacimiento      
      $fecha_actual = new DateTime(); // Crear un objeto DateTime para la fecha actual      
      $diferencia = $fecha_nacimiento->diff($fecha_actual); // Calcular la diferencia entre las dos fechas      
      return $diferencia->y; // Obtener la edad
    } 
    return 0;
  }

  /*  ══════════════════════════════════════════ - N U M E R I C O S - ══════════════════════════════════════════ */

  function multiplo_number($numero, $multiplo) {  if($numero%$multiplo == 0){ return true; }else{ return false; } }

  function quitar_formato_miles($number) {

    $sin_format = 0;

    if ( !empty($number) ) { $sin_format = floatval(str_replace(",", "", $number)); }
    
    return $sin_format;
  }

  function zero_fill ($valor, $long = 0) { return str_pad($valor, $long, '0', STR_PAD_LEFT); }

  /*  ══════════════════════════════════════════ - S T R I N G - ══════════════════════════════════════════ */
  
  function quitar_guion($numero) { return str_replace("-", "", $numero); }

  function removeSpecialChar($str) {
    $res = preg_replace('/[@\.\+\*\"\/\#\%\°\;\$\'\}\{]+/', '', $str);
    return $res;
  }

  function cortar_string($cadena, $limite='25', $sufijo='...'){
    if ( !empty($cadena) ) {
      // Si la longitud es mayor que el límite...
      if(strlen($cadena) > $limite){
        // Entonces corta la cadena y ponle el sufijo
        return substr($cadena, 0, $limite) . $sufijo;
      }    
    }    
    // Si no, entonces devuelve la cadena normal
    return $cadena;
  }

  /*  ══════════════════════════════════════════ - S U B I R   D O C S  - ══════════════════════════════════════════ */
  /*  ══════════════════════════════════════════ - A P I S - ══════════════════════════════════════════ */
  /*  ══════════════════════════════════════════ - M E N S A J E S - ══════════════════════════════════════════ */

  /*  ══════════════════════════════════════════ - O T R O S - ══════════════════════════════════════════ */

  function validar_url( $host, $ruta, $file )  {
    clearstatcache();
    $armar_ruta = $host . $ruta . $file;

    if (empty($armar_ruta)) { return false; }

    // get_headers() realiza una petición GET por defecto,
    // cambiar el método predeterminadao a HEAD
    // Ver http://php.net/manual/es/function.get-headers.php
    stream_context_set_default([
      'http' => [
        'method' => 'HEAD',
      ],
    ]);
    $headers = @get_headers($armar_ruta);
    sscanf($headers[0], 'HTTP/%*d.%*d %d', $httpcode);

    // Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
    $accepted_response = [200, 301, 302];
    if (in_array($httpcode, $accepted_response)) {
      return true;
    } else {
      return false;
    } 
  }

  // function validar_url_completo( $ruta )  {   
  //   stream_context_set_default(['http' => ['method' => 'HEAD',],]); $headers = @get_headers($ruta); sscanf($headers[0], 'HTTP/%*d.%*d %d', $httpcode);
  //   // Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
  //   return $httpcode;
  // }

  function validar_url_completo( $ruta ) { clearstatcache(); if (is_readable($ruta)) { return true; } else { return false; }  }
  /*  ══════════════════════════════════════════ - M O D U L O S - ══════════════════════════════════════════ */

  function suma_totales_modulos($idproyecto, $fecha_1, $fecha_2) {

    $data = Array(); $total = 0; $subtotal = 0; $igv = 0;
  
    // SUMAS TOTALES - COMPRA INSUMO --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND cpp.fecha_compra BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND cpp.fecha_compra = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND cpp.fecha_compra = '$fecha_2'";
    }    
  
    $sql = "SELECT SUM(cpp.total) AS total, SUM(cpp.subtotal) AS subtotal, SUM(cpp.igv) AS igv
    FROM compra_por_proyecto AS cpp, proveedor p
    WHERE cpp.idproveedor = p.idproveedor AND cpp.estado = '1' AND cpp.estado_delete = '1'  AND  cpp.idproyecto = $idproyecto $filtro_fecha ;";
    $compra = ejecutarConsultaSimpleFila($sql);
  
    if ($compra['status'] == false) { return $compra; }
  
    $total    += (empty($compra['data'])) ? 0 : ( empty($compra['data']['total']) ? 0 : floatval($compra['data']['total']) );
    $subtotal += (empty($compra['data'])) ? 0 : ( empty($compra['data']['subtotal']) ? 0 : floatval($compra['data']['subtotal']) );
    $igv      += (empty($compra['data'])) ? 0 : ( empty($compra['data']['igv']) ? 0 : floatval($compra['data']['igv']) );
  
    // SUMAS TOTALES - COMPRAS DE ACTIVO FIJO --------------------------------------------------------------------------------
    // $filtro_fecha = "";
  
    // if ( !empty($fecha_1) && !empty($fecha_2) ) {
    //   $filtro_fecha = "AND cafg.fecha_compra BETWEEN '$fecha_1' AND '$fecha_2'";
    // } else if (!empty($fecha_1)) {
    //   $filtro_fecha = "AND cafg.fecha_compra = '$fecha_1'";
    // }else if (!empty($fecha_2)) {
    //   $filtro_fecha = "AND cafg.fecha_compra = '$fecha_2'";
    // }    
  
    // $sql = "SELECT SUM(cafg.total) AS total, SUM(cafg.subtotal) AS subtotal, SUM(cafg.igv) AS igv
    // FROM compra_af_general  AS cafg, proveedor p
    // WHERE cafg.idproveedor = p.idproveedor AND cafg.estado = '1' AND cafg.estado_delete = '1' $filtro_fecha ;";
    // $compra = ejecutarConsultaSimpleFila($sql);
  
    // if ($compra['status'] == false) { return $compra; }
  
    // $total    += (empty($compra['data'])) ? 0 : ( empty($compra['data']['total']) ? 0 : floatval($compra['data']['total']) );
    // $subtotal += (empty($compra['data'])) ? 0 : ( empty($compra['data']['subtotal']) ? 0 : floatval($compra['data']['subtotal']) );
    // $igv      += (empty($compra['data'])) ? 0 : ( empty($compra['data']['igv']) ? 0 : floatval($compra['data']['igv']) );
      
    // SUMAS TOTALES - MAQUINARIA EQUIPO --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND f.fecha_emision BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND f.fecha_emision = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND f.fecha_emision = '$fecha_2'";
    }    
  
    $sql2 = "SELECT SUM(f.monto) AS total , SUM(f.subtotal) AS subtotal, SUM(f.igv) AS igv
    FROM factura as f, proyecto as p, maquinaria as mq, proveedor as prov
    WHERE f.idmaquinaria=mq.idmaquinaria AND mq.idproveedor=prov.idproveedor AND f.idproyecto=p.idproyecto 
    AND f.estado = '1' AND f.estado_delete = '1'  AND f.idproyecto = $idproyecto $filtro_fecha;";
    $maquinaria = ejecutarConsultaSimpleFila($sql2);
  
    if ($maquinaria['status'] == false) { return $maquinaria; } 
  
    $total    += (empty($maquinaria['data'])) ? 0 : ( empty($maquinaria['data']['total']) ? 0 : floatval($maquinaria['data']['total']) );
    $subtotal += (empty($maquinaria['data'])) ? 0 : ( empty($maquinaria['data']['subtotal']) ? 0 : floatval($maquinaria['data']['subtotal']) );
    $igv      += (empty($maquinaria['data'])) ? 0 : ( empty($maquinaria['data']['igv']) ? 0 : floatval($maquinaria['data']['igv']) );
  
    // SUMAS TOTALES - SUB CONTRATO --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND s.fecha_subcontrato BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND s.fecha_subcontrato = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND s.fecha_subcontrato = '$fecha_2'";
    }    
  
    $sql3 = "SELECT SUM(s.subtotal) as subtotal, SUM(s.igv) as igv, SUM(s.costo_parcial) as total
    FROM subcontrato AS s, proveedor as p
    WHERE s.idproveedor = p.idproveedor and s.estado = '1' AND s.estado_delete = '1'  AND  idproyecto = $idproyecto $filtro_fecha;";
    $otro_gasto = ejecutarConsultaSimpleFila($sql3);
  
    if ($otro_gasto['status'] == false) { return $otro_gasto; } 
    
    $total    += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['total']) ? 0 : floatval($otro_gasto['data']['total']) );
    $subtotal += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['subtotal']) ? 0 : floatval($otro_gasto['data']['subtotal']) );
    $igv      += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['igv']) ? 0 : floatval($otro_gasto['data']['igv']) );
  
    // SUMAS TOTALES - PLANILLA SEGURO --------------------------------------------------------------------------------
  
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND ps.fecha_p_s BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND ps.fecha_p_s = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND ps.fecha_p_s = '$fecha_2'";
    }    
  
    $sql3 = "SELECT SUM(ps.subtotal) AS subtotal, SUM(ps.igv) AS igv, SUM(ps.costo_parcial) AS total
    FROM planilla_seguro as ps, proyecto as p
    WHERE ps.idproyecto = p.idproyecto and ps.estado ='1' and ps.estado_delete = '1' 
      AND  ps.idproyecto = $idproyecto $filtro_fecha;";
    $otro_gasto = ejecutarConsultaSimpleFila($sql3);
  
    if ($otro_gasto['status'] == false) { return $otro_gasto; } 
    
    $total    += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['total']) ? 0 : floatval($otro_gasto['data']['total']) );
    $subtotal += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['subtotal']) ? 0 : floatval($otro_gasto['data']['subtotal']) );
    $igv      += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['igv']) ? 0 : floatval($otro_gasto['data']['igv']) );
  
    // SUMAS TOTALES - OTRO GASTO --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND fecha_g BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND fecha_g = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND fecha_g = '$fecha_2'";
    }    
  
    $sql3 = "SELECT SUM(costo_parcial) as total, SUM(subtotal) AS subtotal, SUM(igv) AS igv
    FROM otro_gasto  
    WHERE estado = '1' AND estado_delete = '1'  AND  idproyecto = $idproyecto $filtro_fecha;";
    $otro_gasto = ejecutarConsultaSimpleFila($sql3);
  
    if ($otro_gasto['status'] == false) { return $otro_gasto; } 
    
    $total    += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['total']) ? 0 : floatval($otro_gasto['data']['total']) );
    $subtotal += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['subtotal']) ? 0 : floatval($otro_gasto['data']['subtotal']) );
    $igv      += (empty($otro_gasto['data'])) ? 0 : ( empty($otro_gasto['data']['igv']) ? 0 : floatval($otro_gasto['data']['igv']) );
  
    // SUMAS TOTALES - TRASNPORTE --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND t.fecha_viaje BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND t.fecha_viaje = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND t.fecha_viaje = '$fecha_2'";
    }    
  
    $sql4 = "SELECT SUM(t.precio_parcial) AS total, SUM(t.subtotal) AS subtotal, SUM(t.igv) AS igv
    FROM transporte AS t, proveedor AS p
    WHERE t.idproveedor = p.idproveedor  AND t.estado = '1' AND t.estado_delete = '1' AND t.idproyecto = $idproyecto  $filtro_fecha;";
    $transporte = ejecutarConsultaSimpleFila($sql4);
  
    if ($transporte['status'] == false) { return $transporte; }
    
    $total    += (empty($transporte['data'])) ? 0 : ( empty($transporte['data']['total']) ? 0 : floatval($transporte['data']['total']) );
    $subtotal += (empty($transporte['data'])) ? 0 : ( empty($transporte['data']['subtotal']) ? 0 : floatval($transporte['data']['subtotal']) );
    $igv      += (empty($transporte['data'])) ? 0 : ( empty($transporte['data']['igv']) ? 0 : floatval($transporte['data']['igv']) );
  
    // SUMAS TOTALES - HOSPEDAJE --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND fecha_comprobante BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND fecha_comprobante = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND fecha_comprobante = '$fecha_2'";
    }    
  
    $sql5 = "SELECT SUM(precio_parcial) as total , SUM(subtotal) AS subtotal, SUM(igv) AS igv
    FROM hospedaje WHERE estado = '1' AND estado_delete = '1' AND idproyecto = $idproyecto  $filtro_fecha
    ORDER BY fecha_comprobante DESC;";
    $hospedaje = ejecutarConsultaSimpleFila($sql5);
  
    if ($hospedaje['status'] == false) { return $hospedaje; }
    
    $total    += (empty($hospedaje['data'])) ? 0 : ( empty($hospedaje['data']['total']) ? 0 : floatval($hospedaje['data']['total']) );
    $subtotal += (empty($hospedaje['data'])) ? 0 : ( empty($hospedaje['data']['subtotal']) ? 0 : floatval($hospedaje['data']['subtotal']) );
    $igv      += (empty($hospedaje['data'])) ? 0 : ( empty($hospedaje['data']['igv']) ? 0 : floatval($hospedaje['data']['igv']) );
  
    // SUMAS TOTALES - FACTURA PENSION --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND dp.fecha_emision BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND dp.fecha_emision = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND dp.fecha_emision = '$fecha_2'";
    }    
  
    $sql6 = "SELECT SUM(dp.precio_parcial) AS total, SUM(dp.subtotal) AS subtotal, SUM(dp.igv) AS igv
    FROM detalle_pension as dp, pension as p, proveedor as prov
    WHERE dp.idpension = p.idpension AND prov.idproveedor = p.idproveedor  AND p.estado = '1' AND p.estado_delete = '1' AND  p.idproyecto = $idproyecto
    AND dp.estado = '1' AND dp.estado_delete = '1' $filtro_fecha ;";
    $factura_pension = ejecutarConsultaSimpleFila($sql6);
  
    if ($factura_pension['status'] == false) { return $factura_pension; }
    
    $total    += (empty($factura_pension['data'])) ? 0 : ( empty($factura_pension['data']['total']) ? 0 : floatval($factura_pension['data']['total']) );
    $subtotal += (empty($factura_pension['data'])) ? 0 : ( empty($factura_pension['data']['subtotal']) ? 0 : floatval($factura_pension['data']['subtotal']) );
    $igv      += (empty($factura_pension['data'])) ? 0 : ( empty($factura_pension['data']['igv']) ? 0 : floatval($factura_pension['data']['igv']) );
  
    // SUMAS TOTALES - FACTURA BREACK --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND fb.fecha_emision BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND fb.fecha_emision = '$fecha_1'";
    }else if (!empty($fecha_2)) {
      $filtro_fecha = "AND fb.fecha_emision = '$fecha_2'";
    }    
  
    $sql7 = "SELECT SUM(fb.monto) AS total, SUM(fb.subtotal) AS subtotal, SUM(fb.igv) AS igv
    FROM factura_break as fb, semana_break as sb
    WHERE  fb.idsemana_break = sb.idsemana_break AND fb.estado = '1' AND fb.estado_delete = '1' AND sb.estado = '1'  AND  sb.idproyecto = $idproyecto
    AND sb.estado_delete = '1' $filtro_fecha ;";
    $factura_break = ejecutarConsultaSimpleFila($sql7);
  
    if ($factura_break['status'] == false) { return $factura_break; }
    
    $total    += (empty($factura_break['data'])) ? 0 : ( empty($factura_break['data']['total']) ? 0 : floatval($factura_break['data']['total']) );
    $subtotal += (empty($factura_break['data'])) ? 0 : ( empty($factura_break['data']['subtotal']) ? 0 : floatval($factura_break['data']['subtotal']) );
    $igv      += (empty($factura_break['data'])) ? 0 : ( empty($factura_break['data']['igv']) ? 0 : floatval($factura_break['data']['igv']) );
  
    // SUMAS TOTALES - COMIDA EXTRA --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND fecha_comida BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {
      $filtro_fecha = "AND fecha_comida = '$fecha_1'";
    }else if (!empty($fecha_2)) {      
      $filtro_fecha = "AND fecha_comida = '$fecha_2'";
    }    
  
    $sql8 = "SELECT SUM(costo_parcial) AS total, SUM(subtotal) AS subtotal, SUM(igv) AS igv
    FROM comida_extra
    WHERE  estado = '1' AND estado_delete = '1' AND  idproyecto = $idproyecto $filtro_fecha;";
    $comida_extra = ejecutarConsultaSimpleFila($sql8);
  
    if ($comida_extra['status'] == false) { return $comida_extra; }
    
    $total    += (empty($comida_extra['data'])) ? 0 : ( empty($comida_extra['data']['total']) ? 0 : floatval($comida_extra['data']['total']) );
    $subtotal += (empty($comida_extra['data'])) ? 0 : ( empty($comida_extra['data']['subtotal']) ? 0 : floatval($comida_extra['data']['subtotal']) );
    $igv      += (empty($comida_extra['data'])) ? 0 : ( empty($comida_extra['data']['igv']) ? 0 : floatval($comida_extra['data']['igv']) );
  
    // SUMAS TOTALES - OTRO INGRESO --------------------------------------------------------------------------------
  
    // $filtro_fecha = "";
  
    // if ( !empty($fecha_1) && !empty($fecha_2) ) {
    //   $filtro_fecha = "AND oi.fecha_i BETWEEN '$fecha_1' AND '$fecha_2'";
    // } else if (!empty($fecha_1)) {
    //    $filtro_fecha = "AND oi.fecha_i = '$fecha_1'";
    // }else if (!empty($fecha_2)) {     
    //    $filtro_fecha = "AND oi.fecha_i = '$fecha_2'";
    // }    
  
    // $sql9 = "SELECT SUM(oi.subtotal) as subtotal, SUM(oi.igv) as igv, SUM(oi.costo_parcial) as total
    // FROM otro_ingreso as oi, proyecto as p
    // WHERE oi.idproyecto = p.idproyecto AND oi.estado = '1' AND oi.estado_delete = '1' AND  oi.idproyecto = $idproyecto $filtro_fecha";
    // $otra_factura = ejecutarConsultaSimpleFila($sql9);
  
    // if ($otra_factura['status'] == false) { return $otra_factura; } 
    
    // $total    += (empty($otra_factura['data'])) ? 0 : ( empty($otra_factura['data']['total']) ? 0 : floatval($otra_factura['data']['total']) );
    // $subtotal += (empty($otra_factura['data'])) ? 0 : ( empty($otra_factura['data']['subtotal']) ? 0 : floatval($otra_factura['data']['subtotal']) );
    // $igv      += (empty($otra_factura['data'])) ? 0 : ( empty($otra_factura['data']['igv']) ? 0 : floatval($otra_factura['data']['igv']) );
  
    // SUMAS TOTALES - OTRA FACTURA --------------------------------------------------------------------------------
    // $filtro_fecha = "";
  
    // if ( !empty($fecha_1) && !empty($fecha_2) ) {
    //   $filtro_fecha = "AND of.fecha_emision BETWEEN '$fecha_1' AND '$fecha_2'";
    // } else {
    //   if (!empty($fecha_1)) {
    //     $filtro_fecha = "AND of.fecha_emision = '$fecha_1'";
    //   }else{
    //     if (!empty($fecha_2)) {
    //       $filtro_fecha = "AND of.fecha_emision = '$fecha_2'";
    //     }     
    //   }      
    // }    
  
    // $sql9 = "SELECT SUM(of.costo_parcial) AS total, SUM(of.subtotal) AS subtotal, SUM(of.igv) AS igv
    // FROM otra_factura AS of, proveedor p
    // WHERE of.idproveedor = p.idproveedor AND of.estado = '1' AND of.estado_delete = '1' $filtro_fecha";
    // $otra_factura = ejecutarConsultaSimpleFila($sql9);
  
    // if ($otra_factura['status'] == false) { return $otra_factura; } 
    
    // $total    += (empty($otra_factura['data'])) ? 0 : ( empty($otra_factura['data']['total']) ? 0 : floatval($otra_factura['data']['total']) );
    // $subtotal += (empty($otra_factura['data'])) ? 0 : ( empty($otra_factura['data']['subtotal']) ? 0 : floatval($otra_factura['data']['subtotal']) );
    // $igv      += (empty($otra_factura['data'])) ? 0 : ( empty($otra_factura['data']['igv']) ? 0 : floatval($otra_factura['data']['igv']) );
    
    // SUMAS TOTALES - PAGO ADMINISTRADOR --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND pxma.fecha_pago BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {    
      $filtro_fecha = "AND pxma.fecha_pago = '$fecha_1'";
    }else if (!empty($fecha_2)) {      
      $filtro_fecha = "AND pxma.fecha_pago = '$fecha_2'";
    }   
    $sql11 = "SELECT SUM(pxma.monto) total, SUM(pxma.monto) AS subtotal
    FROM pagos_x_mes_administrador as pxma, fechas_mes_pagos_administrador as fmpa, trabajador_por_proyecto as tpp, trabajador t
    WHERE pxma.idfechas_mes_pagos_administrador = fmpa.idfechas_mes_pagos_administrador AND fmpa.idtrabajador_por_proyecto = tpp.idtrabajador_por_proyecto  AND tpp.idtrabajador = t.idtrabajador
    AND pxma.estado = '1' AND pxma.estado_delete = '1'  AND tpp.idproyecto = '$idproyecto' $filtro_fecha;";
    $pago_administrador = ejecutarConsultaSimpleFila($sql11);
  
    if ($pago_administrador['status'] == false) { return $pago_administrador; }
    
    $total    += (empty($pago_administrador['data'])) ? 0 : ( empty($pago_administrador['data']['total']) ? 0 : floatval($pago_administrador['data']['total']) );
    $subtotal += (empty($pago_administrador['data'])) ? 0 : ( empty($pago_administrador['data']['subtotal']) ? 0 : floatval($pago_administrador['data']['subtotal']) );
    $igv      += 0;
  
    // SUMAS TOTALES - PAGO OBRERO --------------------------------------------------------------------------------
    $filtro_fecha = "";
  
    if ( !empty($fecha_1) && !empty($fecha_2) ) {
      $filtro_fecha = "AND pqso.fecha_pago BETWEEN '$fecha_1' AND '$fecha_2'";
    } else if (!empty($fecha_1)) {    
      $filtro_fecha = "AND pqso.fecha_pago = '$fecha_1'";
    }else if (!empty($fecha_2)) {      
      $filtro_fecha = "AND pqso.fecha_pago = '$fecha_2'";        
    }
    $sql12 = "SELECT SUM(pqso.monto_deposito) total, SUM(pqso.monto_deposito) AS subtotal
    FROM pagos_q_s_obrero as pqso, resumen_q_s_asistencia as rqsa, trabajador_por_proyecto as tpp, trabajador t
    WHERE pqso.idresumen_q_s_asistencia = rqsa.idresumen_q_s_asistencia AND rqsa.idtrabajador_por_proyecto = tpp.idtrabajador_por_proyecto AND tpp.idtrabajador = t.idtrabajador
    AND pqso.estado = '1' AND pqso.estado_delete = '1' AND tpp.idproyecto = '$idproyecto' $filtro_fecha;";
    $pago_obrero = ejecutarConsultaSimpleFila($sql12);
  
    if ($pago_obrero['status'] == false) { return $pago_obrero; }
    
    $total    += (empty($pago_obrero['data'])) ? 0 : ( empty($pago_obrero['data']['total']) ? 0 : floatval($pago_obrero['data']['total']) );
    $subtotal += (empty($pago_obrero['data'])) ? 0 : ( empty($pago_obrero['data']['subtotal']) ? 0 : floatval($pago_obrero['data']['subtotal']) );
    $igv      += 0;
  
  
    $data = array( 
      "status"=> true,
      "message"=> 'todo oka',
      "data"=> [
        "total" => $total, 
        "subtotal" => $subtotal, 
        "igv" => $igv,  
      ]      
    );
  
    return $total ;
  }
}
  

?>