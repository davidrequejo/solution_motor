<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Servicio
  {

    //Implementamos nuestro constructor
    public $id_usr_sesion; 
    // public $id_empresa_sesion;
    //Implementamos nuestro constructor
    public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
    {
      $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    function listar_tabla(){
      $sql= "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      WHERE p.tipo_producto = 'SR' AND p.estado = 1 AND p.estado_delete = 1 
      ORDER BY p.nombre ASC;";
      return ejecutarConsulta($sql);
    }

    public function insertar($tipo, $codigo_alterno, $idcategoria, $idsunat_unidad_medida, $idmarca, $idubicacion, $nombre, $descripcion, 
    $precio_v, $stock, $img_servicio)	{
      
      $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}      
    
      if ( empty($existe['data']) ) {
        $sql = "INSERT INTO producto(idsunat_unidad_medida, idproducto_categoria, idproducto_marca, idproducto_categoria_ubicacion, tipo_producto, codigo_alterno, nombre, stock, stock_minimo, precio_compra, precio_venta, precioB, 
        precioC, precioD, descripcion, imagen) 
        VALUES ('$idsunat_unidad_medida', '$idcategoria', '$idmarca', '$idubicacion', '$tipo', '$codigo_alterno', '$nombre', $stock, '2', '0', '$precio_v', '0', '0', '0', '$descripcion', '$img_servicio');";
        $id_new = ejecutarConsulta_retornarID($sql, 'C');	if ($id_new['status'] == false) {  return $id_new; }

        return $id_new;
      } else {
        $info_repetida = ''; 
  
        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>'.$value['nombre'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }		
    
	  }

    public function editar($idproducto, $tipo, $codigo_alterno, $idcategoria, $idsunat_unidad_medida, $idmarca, $idubicacion, $nombre, $descripcion, $precio_v, $stock, $img_servicio) {

      $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre' AND idproducto <> '$idproducto';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {

        $sql = "UPDATE producto SET idsunat_unidad_medida = '$idsunat_unidad_medida', idproducto_categoria = '$idcategoria', idproducto_marca = '$idmarca', idproducto_categoria_ubicacion = '$idubicacion',
        tipo_producto = '$tipo', codigo_alterno = '$codigo_alterno', nombre = '$nombre', stock = $stock, stock_minimo = '2', precio_compra = '0', precio_venta = '$precio_v', precioB = '0', precioC = '0', precioD = '0', 
        descripcion = '$descripcion', imagen = '$img_servicio'
        WHERE idproducto = '$idproducto'";
        $edit_user = ejecutarConsulta($sql, 'U'); if ($edit_user['status'] == false) {  return $edit_user; }

        return $edit_user;

      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>'.$value['nombre'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }	
	
    }

    function mostrar($id){
      $sql = "SELECT * FROM producto WHERE idproducto = '$id';";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function eliminar($id){
      $sql = "UPDATE producto SET estado_delete = 0
      WHERE idproducto = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function papelera($id){
      $sql = "UPDATE producto SET estado = 0
      WHERE idproducto = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

  }