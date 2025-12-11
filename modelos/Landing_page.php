<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Landing_page
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

  // ----------------COMENTARIO - CLIENTE----------------
  public function tabla_comentario_cliente(){
    $sql = "SELECT pc.idpersona_cliente, DATE_FORMAT(pc.landing_fecha, '%d-%m-%Y') AS landing_fecha, 
              CASE 
                WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
                WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
                ELSE '-'
              END AS nombre_completo, p.foto_perfil, cp.nombre AS centro_poblado,
              pc.landing_descripcion, pc.landing_puntuacion, pc.landing_estado
            FROM persona_cliente AS pc 
            INNER JOIN persona AS p ON pc.idpersona = p.idpersona
            INNER JOIN centro_poblado AS cp ON pc.idcentro_poblado = cp.idcentro_poblado
            WHERE pc.estado = 1 AND pc.estado_delete = 1 AND p.estado = 1 AND p.estado_delete = 1
            ORDER BY pc.landing_estado DESC, pc.landing_fecha DESC;";
    return ejecutarConsulta($sql);
  }

  public function mostrar_clienteC($idpersona_cliente){
    $sql = "SELECT pc.idpersona_cliente, DATE_FORMAT(pc.landing_fecha, '%d/%m/%Y') AS landing_fecha, 
              CASE 
                WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
                WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
                ELSE '-'
              END AS nombre_completo, cp.nombre AS centro_poblado,
              pc.landing_descripcion, pc.landing_puntuacion
            FROM persona_cliente AS pc 
            INNER JOIN persona AS p ON pc.idpersona = p.idpersona
            INNER JOIN centro_poblado AS cp ON pc.idcentro_poblado = cp.idcentro_poblado
            WHERE pc.idpersona_cliente = '$idpersona_cliente' AND pc.estado_delete = 1 AND pc.estado_delete = 1;";
    return ejecutarConsultaSimpleFila($sql);
  }

  public function guardar_editar_comentarioC($idpersona_cliente, $descripcion_comentario, $puntuacion, $fecha){
    // $fecha_actual = date("Y-m-d H:i:s");
    $sql = "UPDATE persona_cliente SET landing_descripcion = '$descripcion_comentario', landing_puntuacion = '$puntuacion', landing_fecha = '$fecha' WHERE idpersona_cliente = '$idpersona_cliente'";
    $editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
    return $editar;
  }
  

  public function editar_comentarioVisible($idpersona_cliente, $landing_estado){
    if ($landing_estado == 1) {
      $sql_1 = "UPDATE persona_cliente SET landing_estado = 0 WHERE idpersona_cliente = '$idpersona_cliente'";
      $visible = ejecutarConsulta($sql_1);
    } else if ($landing_estado == 0) {
      $sql_2 = "UPDATE persona_cliente SET landing_estado = 1 WHERE idpersona_cliente = '$idpersona_cliente'";
      $oculto = ejecutarConsulta($sql_2);
    }
      
    return $retorno=['status'=>true, 'message'=>'todo okey'];
  }

  // --------------------TRABAJADORES--------------------
  public function tabla_de_trabj(){
      $sql = "SELECT pt.idpersona_trabajador, ct.nombre AS cargo, pt.landing_descripcion, pt.landing_estado,
                CASE 
                  WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
                  WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
                  ELSE '-'
                END AS nombre_completo 
              FROM persona_trabajador AS pt
              INNER JOIN persona AS p ON pt.idpersona = p.idpersona
              INNER JOIN cargo_trabajador AS ct ON p.idcargo_trabajador = ct.idcargo_trabajador
              WHERE pt.estado = 1 AND pt.estado_delete = 1
              ORDER BY pt.landing_estado DESC;";
      return ejecutarConsulta($sql);
  }

  public function editar_trabjVisible($idpersona_trabajador, $landing_estado){
    if ($landing_estado == 1) {
      $sql_1 = "UPDATE persona_trabajador SET landing_estado = 0 WHERE idpersona_trabajador = '$idpersona_trabajador'";
      $visible = ejecutarConsulta($sql_1);
    } else if ($landing_estado == 0) {
        $sql_2 = "UPDATE persona_trabajador SET landing_estado = 1 WHERE idpersona_trabajador = '$idpersona_trabajador'";
        $oculto = ejecutarConsulta($sql_2);
    }
    
    return $retorno=['status'=>true, 'message'=>'todo okey'];

  }

  public function mostrar_trabj($idpersona_trabajador){
    $sql = "SELECT pt.idpersona_trabajador, ct.nombre AS cargo, pt.landing_descripcion,
              CASE 
                WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
                WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
                ELSE '-'
              END AS nombre_completo 
            FROM persona_trabajador AS pt
            INNER JOIN persona AS p ON pt.idpersona = p.idpersona
            INNER JOIN cargo_trabajador AS ct ON p.idcargo_trabajador = ct.idcargo_trabajador
            WHERE pt.idpersona_trabajador = '$idpersona_trabajador' AND pt.estado = 1 AND pt.estado_delete = 1";
    return ejecutarConsultaSimpleFila($sql);
  }

  public function guardar_editar_trabj($idpersona_trabajador,$descripcion_trabj){
    $sql = "UPDATE persona_trabajador SET landing_descripcion = '$descripcion_trabj' WHERE idpersona_trabajador = '$idpersona_trabajador';";
    $editar1 = ejecutarConsulta($sql, 'U'); if ( $editar1['status'] == false) { return $editar1; }
    return $editar1;
  }
  

  // --------------------PLANES--------------------------
  public function tabla_planes(){
    $sql="SELECT idplan, nombre, costo, landing_caracteristica, landing_estado
    FROM plan 
    WHERE estado = '1' AND estado_delete = '1' 
    ORDER BY landing_estado DESC;";
    return ejecutarConsulta($sql);
  }
  public function guardar_editar_plan($idplan, $caracteristicas){
      $sql = "UPDATE plan SET landing_caracteristica = '$caracteristicas' WHERE idplan = '$idplan'";
      $editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
      return $editar;
  }

  public function editar_planVisible($idplan, $landing_estado){
      if ($landing_estado == 1) {
        $sql_1 = "UPDATE plan SET landing_estado = 0 WHERE idplan = '$idplan'";
        $visible = ejecutarConsulta($sql_1);
      } else if ($landing_estado == 0) {
        $sql_2 = "UPDATE plan SET landing_estado = 1 WHERE idplan = '$idplan'";
        $oculto = ejecutarConsulta($sql_2);
      }
        
      return $retorno=['status'=>true, 'message'=>'todo okey'];
  }



  // ---------------PREGUNTAS FRECUENTES-----------------------
  public function tabla_principal_PregFerct(){
      $sql="SELECT * FROM preguntas_frecuentes WHERE estado = '1' AND estado_delete = '1';";
      return ejecutarConsulta($sql);
  }

  public function insertar($pregunta_pf, $respuesta_pf) {
		$sql_0 = "SELECT * FROM preguntas_frecuentes  WHERE pregunta = '$pregunta_pf';";
        $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
        if ( empty($existe['data']) ) {
                $sql="INSERT INTO preguntas_frecuentes(pregunta, respuesta)VALUES('$pregunta_pf', '$respuesta_pf')";
                $insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
                
                //add registro en nuestra bitacora
                // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('plan','".$insertar['data']."','Nueva plan registrado','" . $_SESSION['idusuario'] . "')";
                // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
                
                return $insertar;
            } else {
                $info_repetida = ''; 

                foreach ($existe['data'] as $key => $value) {
                    $info_repetida .= '<li class="text-left font-size-13px">
                        <span class="font-size-15px text-danger"><b>Esta Pregunta Ya Existe</b></span><br>
                        <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
                        <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
                        <hr class="m-t-2px m-b-2px">
                    </li>'; 
                }
                return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
            }			
	}

  public function editar($idpreguntas_frecuentes, $pregunta_pf, $respuesta_pf) {
      $sql_0 = "SELECT * FROM preguntas_frecuentes  WHERE pregunta = '$pregunta_pf' AND idpreguntas_frecuentes <> '$idpreguntas_frecuentes';";
          $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
          
          if ( empty($existe['data']) ) {
        $sql="UPDATE preguntas_frecuentes SET pregunta='$pregunta_pf', respuesta ='$respuesta_pf' WHERE idpreguntas_frecuentes='$idpreguntas_frecuentes'";
        $editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
      
        //add registro en nuestra bitacora
        // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
        // VALUES ('plan','$idplan','plan editada','" . $_SESSION['idusuario'] . "')";
        // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
      
        return $editar;
      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
                      <span class="font-size-15px text-danger"><b>Esta Pregunta Ya Existe</b></span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }			
	}

  public function mostrar_pregFrec($idpreguntas_frecuentes){
      $sql ="SELECT * FROM preguntas_frecuentes WHERE idpreguntas_frecuentes = '$idpreguntas_frecuentes'";
      return ejecutarConsultaSimpleFila($sql);
  }

  public function desactivar($id){
      $sql = "UPDATE preguntas_frecuentes SET estado = 0 WHERE idpreguntas_frecuentes = '$id'";
      return ejecutarConsulta($sql);
  }

  public function eliminar($id){
      $sql = "UPDATE preguntas_frecuentes SET estado_delete = 0 WHERE idpreguntas_frecuentes = '$id'";
      $eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
      return $eliminar;
  }


}
?>