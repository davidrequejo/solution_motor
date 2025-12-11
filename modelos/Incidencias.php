<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Incidencias
  {
    //Implementamos nuestro constructor
    public $id_usr_sesion; 

    //Implementamos nuestro constructor
    public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
    {
      $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    function insertar($actividad, $creacionfecha, $prioridad,$id_trabajador,$categoria,$actividad_detalle){
      //id_trabajador es un arr       // var_dump( $fecha );die();
      $fecha= new DateTime($creacionfecha); $fecha_str = $fecha->format('Y-m-d');

      $sql = "INSERT INTO incidencias(idincidencia_categoria, actividad, actividad_detalle, fecha_creacion,estado_revicion) 
      VALUES ('$categoria','$actividad','$actividad_detalle','$fecha_str','$prioridad')";
      $newdata =  ejecutarConsulta_retornarID($sql, 'C'); if ( $newdata['status'] == false) {return $newdata; }  
      $id = $newdata['data'];
      $i = 0;
      $detalle_new = "";

      if ( !empty($newdata['data']) ) { 

        while ($i < count($id_trabajador)) {

          $sql_2 = "INSERT INTO incidencia_trabajador(idpersona_trabajador, idincidencias) VALUES ('$id_trabajador[$i]','$id');";
          $detalle_new =  ejecutarConsulta($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          

          $i = $i + 1;

        }
      }
      return $detalle_new;

    }

    function insertarfecha_fin($id_incidenciaupdate, $fecha_fin){
      $fecha= new DateTime($fecha_fin); $fecha_str = $fecha->format('Y-m-d');
      $sql = "UPDATE incidencias SET fecha_fin='$fecha_str',estado_incidencia='0' WHERE idincidencias='$id_incidenciaupdate'";
      return ejecutarConsulta($sql, 'U'); 
      
    }

    function editar($idincidencia,$actividad, $creacionfecha, $prioridad,$id_trabajador,$categoria,$actividad_detalle,$fecha_fin_inc,$estado_inc){
      
      $fecha= new DateTime($creacionfecha); $fecha_str = $fecha->format('Y-m-d');
      $fecha_fin= new DateTime($fecha_fin_inc); $fecha_fin_str = $fecha_fin->format('Y-m-d');

      $sql = "UPDATE `incidencias` SET 
      `idincidencia_categoria`='$categoria',`actividad`='$actividad',`actividad_detalle`='$actividad_detalle',
      `fecha_creacion`='$fecha_str',`fecha_fin`='$fecha_fin_str',`estado_revicion`='$prioridad',`estado_incidencia`='$estado_inc'
       WHERE `idincidencias`='$idincidencia'";  
      $upddata= ejecutarConsulta($sql, 'U'); if ( $upddata['status'] == false) {return $upddata; }

      //eliminamos los trabajadores para asignar los nuevos
      $_sql1="DELETE FROM `incidencia_trabajador` WHERE `idincidencias`='$idincidencia'";
      $deletedata= ejecutarConsulta($_sql1, 'U'); if ( $deletedata['status'] == false) {return $deletedata; }


      $i = 0;
      $detalle_new = "";



        while ($i < count($id_trabajador)) {

          $sql_2 = "INSERT INTO incidencia_trabajador(idpersona_trabajador, idincidencias) VALUES ('$id_trabajador[$i]','$idincidencia');";
          $detalle_new =  ejecutarConsulta($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          

          $i = $i + 1;

        }

      return $detalle_new;

    }

    function mostrar($id){
      $data = Array(); $array_trabadores = [];

        $sql = "SELECT  idincidencias,idincidencia_categoria,actividad,actividad_detalle,fecha_creacion,fecha_fin,estado_revicion,estado_incidencia,estado 
        FROM incidencias where idincidencias='$id';";
        // var_dump($sql); die();
        $incidencias = ejecutarConsultaSimpleFila ($sql); if ( $incidencias['status'] == false) {return $incidencias ; } 

          $trabajardorhtml="";

          $idincidencias = $incidencias['data']['idincidencias'];
          $sql2 = "SELECT it.idincidencia_trabajador,it.idpersona_trabajador, p.idpersona, p.nombre_razonsocial, p.foto_perfil
          FROM incidencia_trabajador as it
          inner join persona_trabajador as pt on it.idpersona_trabajador = pt.idpersona_trabajador
          INNER JOIN persona as p on pt.idpersona = p.idpersona
          where idincidencias='$idincidencias';";

          $trabadores = ejecutarConsultaArray($sql2); if ($trabadores['status'] == false) { return $trabadores; }

          foreach ($trabadores['data'] as $key => $valor) {
            $trabajardorhtml .='<span class="avatar avatar-sm avatar-rounded"> <img src="../assets/modulo/persona/perfil/'.$valor['foto_perfil'].'" alt="img" data-toggle="tooltip" data-placement="top" title="'.$valor['nombre_razonsocial'].'"> </span>';
          };
          foreach ($trabadores['data'] as $key => $value) { array_push($array_trabadores, $value['idpersona_trabajador'] ); }
          
          $data = [

            'idincidencias'           =>  $incidencias['data']['idincidencias'],
            'idincidencia_categoria'  =>  $incidencias['data']['idincidencia_categoria'],
            'actividad'               =>  $incidencias['data']['actividad'],
            'actividad_detalle'       =>  $incidencias['data']['actividad_detalle'],
            'fecha_creacion'          =>  $incidencias['data']['fecha_creacion'],
            'fecha_fin'               =>  $incidencias['data']['fecha_fin'],
            'estado_revicion'         =>  $incidencias['data']['estado_revicion'],
            'estado_incidencia'       =>  $incidencias['data']['estado_incidencia'],
            'estado'                  =>  $incidencias['data']['estado'],
            'trabadores'              => $array_trabadores,
            'trabajadoreshtml'        => $trabajardorhtml
          ];
        
    
        return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' =>$data, 'affected_rows' =>$incidencias['affected_rows'],  ] ;



    }

    function view_incidencias($categoria,$prioridad,$estado_inc){

      //  var_dump($estado_inc); die();
      $data = Array();
      // $data_trab = Array();

      $filtro_categoria = ""; $filtro_prioridad = "";  $filtro_incidencia = ""; 
  
      if (empty($categoria) || $categoria=="TODOS" ) {  $filtro_categoria = ""; } else { $filtro_categoria = "AND idincidencia_categoria = '$categoria'"; }
      if (empty($prioridad) || $prioridad=="TODOS" ) {  $filtro_prioridad = ""; } else { $filtro_prioridad = "AND estado_revicion = '$prioridad'"; }
      if ($estado_inc=="TODOS" ) { $filtro_incidencia = ""; } else { $filtro_incidencia = "AND estado_incidencia = '$estado_inc'"; }
  
        // var_dump(empty($estado_inc) .'------'.$filtro_incidencia); die();
        $sql = "SELECT  idincidencias,idincidencia_categoria,actividad,actividad_detalle,fecha_creacion,fecha_fin,estado_revicion,estado_incidencia,estado 
        FROM incidencias where estado='1' and estado_delete='1' $filtro_categoria $filtro_prioridad $filtro_incidencia";
        // var_dump($sql); die();
        $incidencias =  ejecutarConsultaArray($sql); if ( $incidencias['status'] == false) {return $incidencias ; } 

        foreach ($incidencias['data'] as $key => $value) {
          $trabajardorhtml="";
          $idincidencias = $value['idincidencias'];
          $sql2 = "SELECT it.idincidencia_trabajador,it.idpersona_trabajador, p.idpersona, p.nombre_razonsocial, p.foto_perfil
          FROM incidencia_trabajador as it
          inner join persona_trabajador as pt on it.idpersona_trabajador = pt.idpersona_trabajador
          INNER JOIN persona as p on pt.idpersona = p.idpersona
          where idincidencias='$idincidencias';";

          $trabadores = ejecutarConsultaArray($sql2); if ($trabadores['status'] == false) { return $trabadores; }

          foreach ($trabadores['data'] as $key => $valor) {
            $trabajardorhtml .='<span class="avatar avatar-sm avatar-rounded"> <img src="../assets/modulo/persona/perfil/'.$valor['foto_perfil'].'" alt="img" data-toggle="tooltip" data-placement="top" title="'.$valor['nombre_razonsocial'].'"> </span>';
          };

          $data[] = [

            'idincidencias'           => $value['idincidencias'],
            'idincidencia_categoria'  => $value['idincidencia_categoria'],
            'actividad'               => $value['actividad'],
            'actividad_detalle'       => $value['actividad_detalle'],
            'fecha_creacion'          => $value['fecha_creacion'],
            'fecha_fin'               => $value['fecha_fin'],
            'estado_revicion'         => $value['estado_revicion'],
            'estado_incidencia'       => $value['estado_incidencia'],
            'estado'                  => $value['estado'],
            'trabadores'              => $trabadores['data'],
            'trabajadoreshtml'        => $trabajardorhtml
          ];
        }
    
        return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' =>$data, 'affected_rows' =>$incidencias['affected_rows'],  ] ;



    }

    public function desactivar($id){

      $sql="UPDATE gasto_de_trabajador SET estado='0',user_trash= '$this->id_usr_sesion' WHERE idgasto_de_trabajador='$id'";
      $desactivar =  ejecutarConsulta($sql, 'U'); if ( $desactivar['status'] == false) {return $desactivar; }  

      return $desactivar;
    }

    public function eliminar($id) {
      $sql_0 = "DELETE FROM incidencia_trabajador WHERE idincidencias='$id'";
      $eliminar_t =  ejecutarConsulta($sql_0,'D'); if ( $eliminar_t['status'] == false) {return $eliminar_t; }  

      $sql="DELETE FROM incidencias WHERE idincidencias='$id'";
      $eliminar =  ejecutarConsulta($sql,'D'); if ( $eliminar['status'] == false) {return $eliminar; }  
		  return $eliminar;
    }

    function listar_trabajador(){
      $sql = "SELECT pt.idpersona_trabajador, p.nombre_razonsocial 
      FROM persona_trabajador as pt
      inner JOIN persona as p on pt.idpersona=p.idpersona  WHERE pt.estado = 1 AND pt.estado_delete = 1;";
      return ejecutarConsultaArray($sql);
    }

    function mostrar_editar_gdt($id){
      $sql = "SELECT * FROM gasto_de_trabajador WHERE idgasto_de_trabajador = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    function categorias_incidencias(){

      $sql = "SELECT idincidencia_categoria,nombre FROM incidencia_categoria WHERE estado='1' and estado_delete= '1';";
      return ejecutarConsultaArray($sql);
      
    }

  }
?>