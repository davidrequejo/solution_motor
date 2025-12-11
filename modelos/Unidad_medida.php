<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  Class Unidad_medida
  {
    //Implementamos nuestro constructor
    public function __construct()
    {

    }

    //Implementamos un método para insertar registros
    public function insertar($nombre, $abreviatura, $equivalencia, $descripcion) {		
      $sql_0 = "SELECT * FROM sunat_c03_unidad_medida  WHERE nombre = '$nombre';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {
        $sql="INSERT INTO sunat_c03_unidad_medida(nombre, abreviatura, equivalencia, descripcion)VALUES('$nombre', '$abreviatura', '$equivalencia', '$descripcion') ";
        $insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
        
        //add registro en nuestra bitacora
        // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('cargo_trabajador','".$insertar['data']."','Nueva cargo_trabajador registrado','" . $_SESSION['idusuario'] . "')";
        // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
        
        return $insertar;
      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message_guardar' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }		
    }

    //Implementamos un método para editar registros
    public function editar($idsunat_c03_unidad_medida, $nombre, $abreviatura, $equivalencia, $descripcion) {
      $sql_0 = "SELECT * FROM sunat_c03_unidad_medida  WHERE nombre = '$nombre' AND idsunat_c03_unidad_medida <> '$idsunat_c03_unidad_medida';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {
        $sql="UPDATE sunat_c03_unidad_medida SET nombre='$nombre', abreviatura = '$abreviatura', equivalencia = '$equivalencia', descripcion='$descripcion' WHERE idsunat_c03_unidad_medida='$idsunat_c03_unidad_medida';";
        $editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
      
        //add registro en nuestra bitacora
        // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
        // VALUES ('cargo_trabajador','$idsunat_c03_unidad_medida','cargo_trabajador editada','" . $_SESSION['idusuario'] . "')";
        // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
      
        return $editar;
      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado_editar', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }		
    }


    function listar(){
      $sql = "SELECT * FROM sunat_c03_unidad_medida WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsulta($sql);		
    }

    public function mostrar($idsunat_c03_unidad_medida) {
      $sql="SELECT * FROM sunat_c03_unidad_medida WHERE idsunat_c03_unidad_medida='$idsunat_c03_unidad_medida';";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function desactivar($idsunat_c03_unidad_medida) {
      $sql="UPDATE sunat_c03_unidad_medida SET estado='0' WHERE idsunat_c03_unidad_medida='$idsunat_c03_unidad_medida'";
      $desactivar= ejecutarConsulta($sql, 'T'); if ($desactivar['status'] == false) {  return $desactivar; }
      
      // //add registro en nuestra bitacora
      // $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('cargo_trabajador','".$idsunat_c03_unidad_medida."','cargo_trabajador desactivada','" . $_SESSION['idusuario'] . "')";
      // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
      
      return $desactivar;
    }

    public function eliminar($idsunat_c03_unidad_medida) {
		
      $sql="UPDATE sunat_c03_unidad_medida SET estado_delete='0' WHERE idsunat_c03_unidad_medida='$idsunat_c03_unidad_medida'";
      $eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
      
      //add registro en nuestra bitacora
      // $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('cargo_trabajador', '$idsunat_c03_unidad_medida', 'cargo_trabajador Eliminada','" . $_SESSION['idusuario'] . "')";
      // $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
      
      return $eliminar;
    }

    
  }
?>