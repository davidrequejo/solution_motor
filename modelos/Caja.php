<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  Class Caja
  {
    //Implementamos nuestro constructor
    public function __construct()
    {

    }


    function listar_caja(){
      $sql = "SELECT c.*, u.idusuario, 
        CASE 
          WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial)
          WHEN p.tipo_persona_sunat = 'JURIDICA' THEN p.nombre_razonsocial
          ELSE p.nombre_razonsocial -- por si viene un valor inesperado
        END AS nombre_completo
        FROM caja AS c
        INNER JOIN usuario AS u ON c.idusuario = u.idusuario
        INNER JOIN persona AS p ON u.idpersona = p.idpersona
        WHERE c.estado = 1 AND c.estado_delete = 1
        ORDER BY c.fecha_inicio DESC;";
      return ejecutarConsulta($sql);		
    }

    
    public function insertar_caja($idusuario, $f_inicio, $m_inicio, $sw_auto) {

      $fechaI = str_replace("T", " ", $f_inicio) . ":00";
      if ($sw_auto == 2) {
        $fecha_sola = explode("T", $f_inicio)[0];
        $fechaC = str_replace("-", ":", $fecha_sola) . " 23:59:59";
      }

      $sql_0 = "SELECT * FROM caja  WHERE fecha_inicio = '$f_inicio';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {
        $sql="INSERT INTO caja(idusuario, fecha_inicio, fecha_fin, monto_apertura, estado_caja, estado_monto_cierre)
            VALUES('$idusuario', '$fechaI', " . ($sw_auto == 2 ? "'$fechaC'" : "NULL") . ", '$m_inicio', '1', $sw_auto)";
        $insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
        
        //add registro en nuestra bitacora
        // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('cargo_trabajador','".$insertar['data']."','Nueva cargo_trabajador registrado','" . $_SESSION['idusuario'] . "')";
        // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
        
        return $insertar;
      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>Fecha: </b>'.$value['fecha_inicio'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message_guardar' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }		
    }


    public function editar_caja($idcaja, $idusuario, $f_inicio, $m_inicio, $f_cierre) {

      $sql = "SELECT (
        ( SUM(CASE WHEN iei.tipo_gasto_modulo = 'INGRESOS' THEN iei.precio_con_igv ELSE 0 END) + SUM(CASE WHEN v.venta_total IS NOT NULL THEN v.venta_total ELSE 0 END) )
        -
        ( SUM(CASE WHEN iei.tipo_gasto_modulo IN ('GASTO', 'GASTO TRABAJADOR') THEN iei.precio_con_igv ELSE 0 END) + SUM(CASE WHEN cp.total IS NOT NULL THEN cp.total ELSE 0 END) )
      ) AS total
      FROM caja AS c
      LEFT JOIN ingreso_egreso_interno AS iei ON c.idcaja = iei.idcaja
      LEFT JOIN venta AS v ON c.idcaja = v.idcaja
      LEFT JOIN compra AS cp ON c.idcaja = cp.idcaja
      WHERE c.idcaja = '$idcaja';";
      $total_cierre = ejecutarConsultaSimpleFila($sql); if ($total_cierre['status'] == false) { return $total_cierre;}

      $sql_0 = "SELECT * FROM caja  WHERE fecha_inicio = '$f_inicio' AND idcaja <> '$idcaja';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}

      $fechaI = str_replace("T", " ", $f_inicio) . ":00";
      $fechaC = str_replace("T", " ", $f_cierre) . ":00";
      $total_caja = $total_cierre['data']['total'];
        
      if ( empty($existe['data']) ) {
        $sql="UPDATE caja SET idusuario='$idusuario', fecha_inicio='$fechaI', monto_apertura='$m_inicio', fecha_fin='$fechaC', monto_cierre='$total_caja', estado_caja='CERRADO' WHERE idcaja='$idcaja';";
        $editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
      
        //add registro en nuestra bitacora
        // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
        // VALUES ('cargo_trabajador','$idcaja','cargo_trabajador editada','" . $_SESSION['idusuario'] . "')";
        // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
      
        return $editar;
      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>Fecha: </b>'.$value['fecha_inicio'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado_editar', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }		
    }

    public function mostrar($idcaja) {
      $sql_0 = "SELECT * FROM caja WHERE idcaja = '$idcaja';";
      $existe = ejecutarConsultaSimpleFila($sql_0); if ($existe['status'] == false) { return $existe;}

      // if($existe['data']['estado_caja'] == 'ABIERTO' && $existe['data']['estado_monto_cierre'] == 'AUTOMATICO'){
      //   $fecha_formateada = date('d-M-Y', strtotime($existe['data']['fecha_fin']));

      //   $mensaje_caja = '<li><div class="text-start">La caja se cerrará automáticamente a las 23:59 del día <b> '. $fecha_formateada .' </b> por favor espere.</div></li>';
      //   return array( 'status' => 'caja_automatica', 'message' => 'La caja esta en modo automático', 'data' => '<ul>'.$mensaje_caja.'</ul>', 'id_tabla' => '' );
        
      // }else {
      // }

      $sql="SELECT c.idcaja, c.idusuario, c.monto_apertura, c.estado_monto_cierre,
        DATE_FORMAT(c.fecha_inicio, '%Y-%m-%dT%H:%i') AS fecha_inicio_format,
        DATE_FORMAT(c.fecha_fin, '%Y-%m-%dT%H:%i') AS fecha_fin_format,
        ifnull( i.ingresos, 0) as ingresos,
        ifnull(v.venta_total, 0)  as facturacion,
        ifnull(gt.gasto_trabajador, 0)  as gasto_trabajador,
        ifnull(g.otro_gasto, 0)  as otro_gasto,
        ifnull(cp.compra, 0)  as compra,
        ROUND( (
          ( ifnull( i.ingresos, 0) + ifnull(v.venta_total, 0)  )
          -
          ( ifnull(gt.gasto_trabajador, 0) + ifnull(g.otro_gasto, 0) + ifnull(cp.compra, 0)  )
        ), 2) AS monto_cierre

      FROM caja AS c
      LEFT JOIN ( 
        select i.idcaja, sum( i.precio_con_igv) as ingresos from ingreso_egreso_interno as i where i.tipo_gasto_modulo = 'INGRESOS' and i.estado = '1' and i.estado_delete = '1'  group by i.idcaja 
      ) AS i ON c.idcaja = i.idcaja
      LEFT JOIN ( 
        select i.idcaja, sum( i.precio_con_igv) as gasto_trabajador from ingreso_egreso_interno as i where i.tipo_gasto_modulo = 'GASTO TRABAJADOR' and i.estado = '1' and i.estado_delete = '1'  group by i.idcaja 
      ) AS gt ON c.idcaja = gt.idcaja
      LEFT JOIN ( 
        select i.idcaja, sum( i.precio_con_igv) as otro_gasto from ingreso_egreso_interno as i where i.tipo_gasto_modulo = 'GASTOS' and i.estado = '1' and i.estado_delete = '1'  group by i.idcaja 
      ) AS g ON c.idcaja = g.idcaja       
      LEFT JOIN ( 
        select v.idcaja, sum( v.venta_total) as venta_total from venta as v where v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante in ('01', '03', '12') and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') group by v.idcaja 
      )  AS v ON c.idcaja = v.idcaja
      LEFT JOIN ( 
        select cp.idcaja, sum(cp.total) as compra from compra as cp where cp.estado = '1' and cp.estado_delete = '1'  group by cp.idcaja 
      ) AS cp ON c.idcaja = cp.idcaja         
      WHERE c.idcaja = '$idcaja'
      GROUP BY c.idcaja;";
      return ejecutarConsultaSimpleFila($sql);
      
      
    }

    public function desactivar($idcaja) {
      $sql="UPDATE caja SET estado='0' WHERE idcaja='$idcaja'";
      $desactivar= ejecutarConsulta($sql, 'T'); if ($desactivar['status'] == false) {  return $desactivar; }
      
      // //add registro en nuestra bitacora
      // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('cargo_trabajador','".$idcaja."','cargo_trabajador desactivada','" . $_SESSION['idusuario'] . "')";
      // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
      
      return $desactivar;
    }

    public function eliminar($idcaja) {
		
      $sql="UPDATE caja SET estado_delete='0' WHERE idcaja='$idcaja'";
      $eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
      
      //add registro en nuestra bitacora
      // $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('cargo_trabajador', '$idcaja', 'cargo_trabajador Eliminada','" . $_SESSION['idusuario'] . "')";
      // $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
      
      return $eliminar;
    }

    //solo el cajero y el administrador pueden editar el registro de CAJA
    public function ver_usuario($idusuario){
      $sql = "SELECT u.idusuario
        FROM usuario AS u
        INNER JOIN usuario_permiso AS up ON u.idusuario = up.idusuario
        INNER JOIN permiso AS p ON up.idpermiso = p.idpermiso
        WHERE p.idpermiso = 14;";
      return ejecutarConsulta($sql);
    }

    public function verificar_caja(){
      $sql = "SELECT * FROM caja WHERE estado_caja = 'ABIERTO';";
      return ejecutarConsultaSimpleFila($sql);
    }

  }
?>