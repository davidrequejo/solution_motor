<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  Class Ingreso_egreso_categoria  
  {
    //Implementamos nuestro constructor
    public function __construct()
    {

    }

    //Implementamos un método para listar los registros
    public function listar_ie_categoria() {

      $sql="SELECT * FROM ingreso_egreso_categoria WHERE estado = 1  AND estado_delete = 1 ORDER BY nombre ASC";
      return ejecutarConsulta($sql);		

    }


    //Implementamos un método para insertar nuevos registros
    public function insertar_ie_categoria($nombre, $descripcion) {		

      $sql_0 = "SELECT * FROM ingreso_egreso_categoria  WHERE nombre = '$nombre';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {
        
        $sql="INSERT INTO ingreso_egreso_categoria(nombre, descripcion)VALUES('$nombre', '$descripcion')";
        $insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
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


    //Implementamos un método para editar un registro
    public function editar_ie_categoria($idcategoria, $nombre, $descripcion) {

      $sql_0 = "SELECT * FROM ingreso_egreso_categoria  WHERE nombre = '$nombre' AND idingreso_egreso_categoria <> '$idcategoria';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {
        $sql="UPDATE ingreso_egreso_categoria SET nombre='$nombre', descripcion='$descripcion' WHERE idingreso_egreso_categoria='$idcategoria';";
        $editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
      
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


    //Implementamos un método para mostrar los datos de un registro
    public function mostrar_ie_categoria($id) {

      $sql="SELECT * FROM ingreso_egreso_categoria WHERE idingreso_egreso_categoria='$id'";
      return ejecutarConsultaSimpleFila($sql);

    }


    //Implementamos un método para desactivar un registro
    public function desactivar_ie_categoria($id) {
      
      $sql="UPDATE ingreso_egreso_categoria SET estado='0' WHERE idingreso_egreso_categoria='$id'";
      $desactivar= ejecutarConsulta($sql, 'T');
      return $desactivar;

    }

    //Implementamos un método para eliminar un registro
    public function eliminar_ie_categoria($id) {
      
      $sql="UPDATE ingreso_egreso_categoria SET estado_delete='0' WHERE idingreso_egreso_categoria='$id'";
      $eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
      return $eliminar;

    }

  }

?>