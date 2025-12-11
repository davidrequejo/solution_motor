<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Producto
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

    function listar_tabla($categoria, $unidad_medida, $marca, $ubicacion){
      $filtro_categoria = ""; $filtro_unidad_medida = ""; $filtro_marca = ""; $filtro_ubicacion = "";

      if ( empty($categoria) ) { } else {  $filtro_categoria = "AND p.idproducto_categoria = '$categoria'"; } 
      if ( empty($unidad_medida) ) { } else {  $filtro_unidad_medida = "AND idsunat_c03_unidad_medida = '$unidad_medida'"; } 
      if ( empty($marca) ) { } else {  $filtro_marca = "AND p.idproducto_marca = '$marca'"; } 
      if (empty($ubicacion)){ } else { $filtro_ubicacion = "AND pcu.idproducto_categoria_ubicacion = '$ubicacion'"; }

      $sql= "SELECT p.idproducto, p.nombre, p.descripcion, p.codigo, p.codigo_alterno, cat.nombre AS categoria, mc.nombre AS marca,       
      pp.unidad_presentacion,pp.idproducto_sucursal,
      pcu.nombre AS ubicacion, ps.stock, ps.stock_minimo, ps.precio_compra, ps.precio_venta, ps.precio_por_mayor, p.estado, p.imagen
      FROM producto AS p
      INNER JOIN producto_sucursal AS ps ON ps.idproducto = p.idproducto 
      INNER JOIN ( 
        select idproducto_sucursal, GROUP_CONCAT( concat(nombre, ' (', cantidad, ')' ) SEPARATOR ', ') AS unidad_presentacion 
        from producto_presentacion where estado = 1 AND estado_delete = 1 $filtro_unidad_medida group by idproducto_sucursal order by case when nombre = 'UNIDADES' THEN 0 ELSE 1 END ASC, nombre ASC
      ) AS pp ON pp.idproducto_sucursal = ps.idproducto_sucursal           
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      INNER JOIN producto_categoria_ubicacion AS pcu ON p.idproducto_categoria_ubicacion = pcu.idproducto_categoria_ubicacion
      WHERE p.tipo_producto = 'PR' AND p.estado = 1 AND p.estado_delete = 1 
      $filtro_categoria $filtro_marca $filtro_ubicacion      
      ORDER BY p.codigo ASC;";
      return ejecutarConsulta($sql);
    }

    public function insertar($tipo, $codigo_alterno, $idsucursal, $categoria, $u_medida, $marca, $ubicacion, $nombre, $descripcion, $cant_um,
     $stock, $stock_min, $precio_v, $precio_c, $precio_x_mayor, $img_producto, $idproducto_pp_set, $idproducto_p_set, $cantidad, $um_presentation)	{
      
      $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre'";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}      
    
      if ( empty($existe['data']) ) {
        $sql_i = "INSERT INTO producto (idproducto_categoria, idproducto_marca, idproducto_categoria_ubicacion, tipo_producto, codigo_alterno, nombre, descripcion, imagen) VALUES 
        ('$categoria', '$marca', '$ubicacion', '$tipo', '$codigo_alterno', '$nombre', '$descripcion', '$img_producto');";
        $newdata = ejecutarConsulta_retornarID($sql_i, 'C');	if ($newdata['status'] == false) {  return $newdata; } $idproducto = $newdata['data'];
        

        $sql_i3 = "INSERT INTO producto_sucursal ( idsucursal, idproducto, stock, stock_minimo, precio_compra, precio_venta, precio_por_mayor) VALUES 
        ('$idsucursal', '$idproducto', '$stock', '$stock_min', '$precio_c', '$precio_v', '$precio_x_mayor');";
        $newdata2 = ejecutarConsulta_retornarID($sql_i3, 'C');	if ($newdata2['status'] == false) {  return $newdata2; }

        $idproducto_sucursal = $newdata2['data'];

        $sql_i1 = "SELECT * FROM sunat_c03_unidad_medida WHERE idsunat_c03_unidad_medida = '$u_medida'";
        $umedida = ejecutarConsultaSimpleFila($sql_i1); if ( $umedida['status'] == false ){ return $umedida; }
        $nombre_um = $umedida['data']['nombre'];

        $sql_i2 = "INSERT INTO producto_presentacion (idproducto_sucursal, idsunat_c03_unidad_medida, nombre, cantidad,precio_venta,precio_venta_total) VALUES 
        ('$idproducto_sucursal', '$u_medida', '$nombre_um', '$cant_um','$precio_v','$precio_v');";
        $newdata1 = ejecutarConsulta_retornarID($sql_i2, 'C');	if ($newdata1['status'] == false) {  return $newdata1; }


        $i = 0;
        $set_prod_new = "";

        if ( !empty($newdata['data']) && !empty($idproducto_pp_set) ) {      

          $sql_1 = "SELECT idproducto FROM producto WHERE codigo_alterno = '$codigo_alterno' AND nombre = '$nombre';";
          $buscar_producto = ejecutarConsultaSimpleFila($sql_1); if ($buscar_producto['status'] == false) {  return $buscar_producto; }
          $id = $buscar_producto['data']['idproducto'];

          while ($i < count($idproducto_pp_set)) {

            $sql_2 = "INSERT INTO producto_agrupado( idproducto, idproducto_presentacion, cantidad )
            VALUES ('$id', '$idproducto_pp_set[$i]', '$cantidad[$i]');";
            $set_prod_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($set_prod_new['status'] == false) { return  $set_prod_new;}          
            $id_d = $set_prod_new['data'];            

            // Reducimos el Stock
            $sql_2_1 = "UPDATE producto_sucursal set  stock = stock - ('$cantidad[$i]' * '$um_presentation[$i]') where idproducto = '$idproducto_p_set[$i]';";
            $actualizar_stock =  ejecutarConsulta($sql_2_1); if ($actualizar_stock['status'] == false) { return  $actualizar_stock;} 

            $i = $i + 1;
          }
        }

        return $newdata;
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

    public function editar($idproducto,$idproducto_sucursal,$idproducto_presentacion, $tipo, $codigo_alterno, $idsucursal, $categoria, $u_medida, $marca, $ubicacion, $nombre, $descripcion, $cant_um,
     $stock, $stock_min, $precio_v, $precio_c, $precio_x_mayor, $img_producto, $idproducto_pp_set, $idproducto_p_set, $cantidad, $um_presentation) {

      $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre' AND idproducto <> '$idproducto';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {

        $sql_1 = "UPDATE producto SET idproducto_categoria = '$categoria', idproducto_marca = '$marca', idproducto_categoria_ubicacion = '$ubicacion',
        tipo_producto = '$tipo', codigo_alterno = '$codigo_alterno', nombre = '$nombre', descripcion = '$descripcion', imagen = '$img_producto'
        WHERE idproducto = '$idproducto'";
        $producto = ejecutarConsulta($sql_1, 'U'); if ($producto['status'] == false) {  return $producto; }

        $sql_2 = "UPDATE producto_presentacion SET cantidad = '$cant_um',  precio_venta='$precio_v' , precio_venta_total='$precio_v'
        WHERE idproducto_presentacion = '$idproducto_presentacion' AND idsunat_c03_unidad_medida = '$u_medida';";
        $presentacion = ejecutarConsulta($sql_2, 'U'); if ($presentacion['status'] == false) {  return $presentacion; }

        $sql_3 = "UPDATE producto_sucursal SET idsucursal = '$idsucursal', stock = '$stock', stock_minimo = '$stock_min', 
        precio_compra = '$precio_c', precio_venta = '$precio_v', precio_por_mayor = '$precio_x_mayor'
        WHERE idproducto_sucursal = '$idproducto_sucursal';";
        $sucursal = ejecutarConsulta($sql_3, 'U'); if ($sucursal['status'] == false) {  return $sucursal; }

        $i = 0;
        $ii = 0;
        $set_prod_new = "";

       /* if ( !empty($producto['data']) && !empty($idproducto_pp_set) ) {      

          $id = [];
          $cant = [];

          //Buscamos el grupo
          $sql_4 = "SELECT idproducto_presentacion, cantidad FROM producto_agrupado WHERE idproducto = '$idproducto';";
          $buscar_grupo = ejecutarConsultaArray($sql_4); if ($buscar_grupo['status'] == false) {  return $buscar_grupo; }

          foreach($buscar_grupo['data'] as $fila){
            $id[] = $fila['idproducto_presentacion'];
            $cant[] = $fila['cantidad'];
          }

          while ($ii < count($id)){
            //Restautamos el Stock
            $sql_5 = "UPDATE producto_sucursal set  stock = stock + $cant[$ii] WHERE idproducto = '$id[$ii]' ;";
            $retornar_stock =  ejecutarConsulta($sql_5); if ($retornar_stock['status'] == false) { return  $retornar_stock;} 
            $ii++;
          }

          // eliminamos el grupo anterior
          $sql_6 = "DELETE FROM producto_agrupado WHERE idproducto = '$idproducto';";
          $del_grupo = ejecutarConsulta($sql_6);

          while ($i < count($idproducto_pp_set)) {

            // Insertamos nuevos productos al grupo
            $sql_7 = "INSERT INTO producto_agrupado( idproducto, idproducto_presentacion, cantidad )
            VALUES ('$idproducto', '$idproducto_pp_set[$i]', '$cantidad[$i]');";
            $set_prod_new =  ejecutarConsulta_retornarID($sql_7, 'C'); if ($set_prod_new['status'] == false) { return  $set_prod_new;}          
            $id_d = $set_prod_new['data'];            

            // Reducimos el Stock
            $sql_8 = "UPDATE producto_sucursal set  stock = stock - ('$cantidad[$i]' * '$um_presentation[$i]') where idproducto = '$idproducto_p_set[$i]';";
            $actualizar_stock =  ejecutarConsulta($sql_8); if ($actualizar_stock['status'] == false) { return  $actualizar_stock;} 

            $i++;
          }
        }*/

        return $producto;

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

    public function mostrar($id){

      $sql = "SELECT 
                p.idproducto, 
                p.idproducto_categoria, 
                p.idproducto_marca, 
                p.idproducto_categoria_ubicacion, 
                p.tipo_producto, 
                p.codigo, 
                p.codigo_alterno, 
                p.nombre, 
                p.descripcion, 
                p.imagen, 
                ps.idproducto_sucursal, 
                ps.idsucursal, 
                ps.idproducto, 
                ps.stock, 
                ps.stock_minimo, 
                ps.precio_compra, 
                ps.precio_venta, 
                ps.precio_por_mayor,
                ( SELECT pp.idsunat_c03_unidad_medida FROM producto_presentacion AS pp WHERE pp.idproducto_sucursal = ps.idproducto_sucursal LIMIT 1 ) AS idsunat_c03_unidad_medida,
                ( SELECT pp.idproducto_presentacion FROM producto_presentacion AS pp WHERE pp.idproducto_sucursal = ps.idproducto_sucursal LIMIT 1 ) AS idproducto_presentacion
            FROM producto AS p
            INNER JOIN producto_sucursal AS ps ON p.idproducto = ps.idproducto
            WHERE p.idproducto = '$id';";
      $producto = ejecutarConsultaSimpleFila($sql); if ($producto['status'] == false) { return $producto;}
      
      /*$sql_1 = "SELECT pa.idproducto AS idproducto_n, pa.cantidad as cantidad_asociado, pp.idproducto, p.nombre, p.descripcion, p.codigo, p.codigo_alterno, um.abreviatura AS um_abreviatura, pp.cantidad,
      GROUP_CONCAT(DISTINCT um.nombre ORDER BY pp.created_at SEPARATOR ', ') AS unidad_medida, 
      GROUP_CONCAT(DISTINCT pp.cantidad ORDER BY pp.created_at SEPARATOR ', ') AS cantidad_medida, 
      GROUP_CONCAT(DISTINCT pp.idproducto_presentacion ORDER BY pp.created_at SEPARATOR ', ') AS idpresentacion,
      cat.nombre AS categoria, mc.nombre AS marca, pcu.nombre AS ubicacion, ps.stock, ps.stock_minimo, ps.precio_compra, ps.precio_venta, ps.precio_por_mayor, p.imagen, p.estado
      FROM producto_agrupado AS pa
      LEFT JOIN producto_presentacion AS pp ON pa.idproducto_presentacion = pp.idproducto_presentacion
      LEFT JOIN producto AS p ON pp.idproducto = p.idproducto
      INNER JOIN producto_sucursal AS ps ON p.idproducto = ps.idproducto
      INNER JOIN sunat_c03_unidad_medida AS um ON pp.idsunat_c03_unidad_medida = um.idsunat_c03_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      INNER JOIN producto_categoria_ubicacion AS pcu ON p.idproducto_categoria_ubicacion = pcu.idproducto_categoria_ubicacion
      WHERE pa.idproducto = '$id' AND p.estado = 1 AND p.estado_delete = 1
      GROUP BY p.idproducto, p.nombre, cat.nombre, mc.nombre;";
      $grupo = ejecutarConsultaArray($sql_1); if ($grupo['status'] == false) { return $grupo;}*/

      return $datos = [ 
        'status' => true, 
        'message' => 'Todo okey', 
        'data' => [ 'producto' => $producto['data'], 
                    // 'grupo'    => isset($grupo['data']) ? $grupo['data'] : [] 
                  ]
      ];
            
    }

    public function mostrar_detalle_producto($id){

      $sql = "SELECT p.idproducto, p.nombre, p.descripcion, p.codigo, p.codigo_alterno, um.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca, 
      pcu.nombre AS ubicacion, ps.stock, ps.stock_minimo, ps.precio_compra, ps.precio_venta, ps.precio_por_mayor, p.imagen, p.estado
      FROM producto AS p
      INNER JOIN producto_presentacion AS pp ON p.idproducto = pp.idproducto
      INNER JOIN producto_sucursal AS ps ON p.idproducto = ps.idproducto
      INNER JOIN sunat_c03_unidad_medida AS um ON pp.idsunat_c03_unidad_medida = um.idsunat_c03_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      INNER JOIN producto_categoria_ubicacion AS pcu ON p.idproducto_categoria_ubicacion = pcu.idproducto_categoria_ubicacion
      WHERE p.idproducto = '$id';";
      $producto_p = ejecutarConsultaSimpleFila($sql); if ($producto_p['status'] == false) { return $producto_p;}

      $sql_0 = "SELECT * FROM producto_agrupado WHERE idproducto = '$id';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}

      if(!empty($existe['data'])){
        $sql_1 = "SELECT pa.idproducto_presentacion, p.nombre, p.descripcion, p.imagen, pc.nombre as categoria, pm.nombre as marca, um.nombre AS unidad_medida, pcu.nombre as ubicacion, pa.cantidad
        FROM producto_agrupado as pa
        INNER JOIN producto_presentacion as pp ON pa.idproducto_presentacion = pp.idproducto_presentacion
        INNER JOIN producto as p ON pp.idproducto = p.idproducto
        INNER JOIN producto_categoria as pc ON p.idproducto_categoria = pc.idproducto_categoria
        INNER JOIN producto_marca as pm ON p.idproducto_marca = pm.idproducto_marca
       INNER JOIN sunat_c03_unidad_medida AS um ON pp.idsunat_c03_unidad_medida = um.idsunat_c03_unidad_medida
        INNER JOIN producto_categoria_ubicacion as pcu ON p.idproducto_categoria_ubicacion = pcu.idproducto_categoria_ubicacion
        WHERE pa.idproducto = '$id';";
        $grupo_p = ejecutarConsulta($sql_1); if ($grupo_p['status'] == false) { return $grupo_p;}
      }

      return $datos = [ 
        'status' => true, 
        'message' => 'Todo okey', 
        'data' => [ 
          'producto' => isset($producto_p['data']) ? $producto_p['data'] : [],
          'grupo'    => isset($grupo_p['data']) ? $grupo_p['data'] : [] 
        ]
      ];
      
    }

    public function mostrar_eliminar_papelera($idproducto){
      $sql = "SELECT pp.idproducto_presentacion, um.nombre as unidad_medida
      FROM producto as p
      INNER JOIN producto_sucursal AS ps ON p.idproducto = ps.idproducto
      WHERE p.idproducto = '$id'; AND ps.estado = 1 AND ps.estado_delete = 1 AND p.estado = 1 AND p.estado_delete = 1 ORDER BY pp.created_at";
      return ejecutarConsultaArray($sql);
    }

    public function eliminar($id){
      $sql = "UPDATE producto SET estado_delete = 0 WHERE idproducto = '$id'";
      return ejecutarConsulta($sql, 'U');
      
      /* $sql_1 = "SELECT * FROM producto_presentacion WHERE idproducto = '$id' ORDER BY created_at";
      $presentacion = ejecutarConsultaSimpleFila($sql_1); $idpresentacion = $presentacion['data']['idproducto_presentacion'];
      
      $sql_2 = "UPDATE producto_presentacion SET estado_delete = 0 WHERE idproducto_presentacion = '$idpresentacion'";
      return ejecutarConsulta($sql_2, 'U');*/
    }

    public function papelera($id){
      $sql = "UPDATE producto SET estado = 0 WHERE idproducto = '$id'";
      return ejecutarConsulta($sql, 'U');

      /*$sql_1 = "SELECT * FROM producto_presentacion WHERE idproducto = '$id' ORDER BY created_at";
      $presentacion = ejecutarConsultaSimpleFila($sql_1); $idpresentacion = $presentacion['data']['idproducto_presentacion'];
      
      $sql_2 = "UPDATE producto_presentacion SET estado = 0 WHERE idproducto_presentacion = '$idpresentacion'";
      return ejecutarConsulta($sql_2, 'U');*/
    }

    // ═════════════════════════════════  PRODUCTOS AGRUPADOS  ══════════════════════════════════════
    public function listar_tabla_producto_g($tipo_producto){
      $sql = "SELECT p.idproducto, p.nombre, p.descripcion, p.codigo, p.codigo_alterno, cat.nombre AS categoria, mc.nombre AS marca, 
      GROUP_CONCAT(DISTINCT um.nombre ORDER BY pp.created_at SEPARATOR ', ') AS unidad_medida,
      pcu.nombre AS ubicacion, ps.stock, ps.stock_minimo, ps.precio_compra, ps.precio_venta, ps.precio_por_mayor, p.imagen, p.estado
      FROM producto AS p
      INNER JOIN producto_presentacion AS pp ON p.idproducto = pp.idproducto
      INNER JOIN producto_sucursal AS ps ON p.idproducto = ps.idproducto
      INNER JOIN sunat_c03_unidad_medida AS um ON pp.idsunat_c03_unidad_medida = um.idsunat_c03_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      INNER JOIN producto_categoria_ubicacion AS pcu ON p.idproducto_categoria_ubicacion = pcu.idproducto_categoria_ubicacion
      WHERE p.tipo_producto = '$tipo_producto' AND p.estado = 1 AND p.estado_delete = 1 AND pp.estado = 1 AND pp.estado_delete = 1
      GROUP BY p.idproducto, p.nombre, cat.nombre, mc.nombre;";
      return ejecutarConsulta($sql);
    }

    public function mostrar_producto($idproducto){
      $sql = "SELECT p.idproducto, p.nombre, p.descripcion, p.codigo, p.codigo_alterno, um.abreviatura AS um_abreviatura, pp.cantidad,
      GROUP_CONCAT(DISTINCT um.nombre ORDER BY pp.created_at SEPARATOR ', ') AS unidad_medida, 
      GROUP_CONCAT(DISTINCT pp.cantidad ORDER BY pp.created_at SEPARATOR ', ') AS cantidad_medida, 
      GROUP_CONCAT(DISTINCT pp.idproducto_presentacion ORDER BY pp.created_at SEPARATOR ', ') AS idpresentacion,
      cat.nombre AS categoria, mc.nombre AS marca, pcu.nombre AS ubicacion, ps.stock, ps.stock_minimo, ps.precio_compra, ps.precio_venta, ps.precio_por_mayor, p.imagen, p.estado
      FROM producto AS p
      INNER JOIN producto_presentacion AS pp ON p.idproducto = pp.idproducto
      INNER JOIN producto_sucursal AS ps ON p.idproducto = ps.idproducto
      INNER JOIN sunat_c03_unidad_medida AS um ON pp.idsunat_c03_unidad_medida = um.idsunat_c03_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      INNER JOIN producto_categoria_ubicacion AS pcu ON p.idproducto_categoria_ubicacion = pcu.idproducto_categoria_ubicacion
      WHERE p.idproducto = '$idproducto' AND p.estado = 1 AND p.estado_delete = 1 AND pp.estado = 1 AND pp.estado_delete = 1
      GROUP BY p.idproducto, p.nombre, cat.nombre, mc.nombre;";
      return ejecutarConsultaSimpleFila($sql);
    }

    // ═══════════════════════════════  PRODUCTOS PRESENTACION  ══════════════════════════════════════
    public function listar_presentacion($idproducto_sucursal){
      $sql = "SELECT ps.idproducto_sucursal,p.* FROM producto_sucursal as ps INNER JOIN producto as p on ps.idproducto = p.idproducto WHERE ps.idproducto_sucursal = '$idproducto_sucursal';";
      $producto = ejecutarConsultaSimpleFila($sql); if ($producto['status'] == false) { return $producto;}

      $sql_1 = "SELECT pp.* 
      FROM producto_presentacion as pp
      inner join producto_sucursal as ps on ps.idproducto_sucursal = pp.idproducto_sucursal
      WHERE ps.idproducto_sucursal = '$idproducto_sucursal' AND pp.estado = 1 AND pp.estado_delete = 1 order by case when pp.nombre = 'UNIDADES' THEN 0 ELSE 1 END ASC, pp.nombre ASC ; ";
      $presentacion = ejecutarConsultaArray($sql_1); if ($presentacion['status'] == false) { return $presentacion;}

      return $datos = [
        'status' => true, 
        'message' => 'Todo ok', 
        'data' => ['producto' => $producto['data'], 'presentacion' => $presentacion['data']]
      ];
    }

    public function mostrar_presentacion($idpresentacion){
      $sql = "SELECT pp.* FROM vw_producto_presentacion as pp WHERE pp.idproducto_presentacion = '$idpresentacion';";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function insertar_presentacion($idproducto_sucursal_ps, $umedida_ps, $cant_ps, $nombre_presentacion,$precio_presentacion_und,$precio_presentacion_total){

      $sql_0 = "SELECT * FROM producto_presentacion WHERE idproducto_sucursal = '$idproducto_sucursal_ps' AND nombre = '$nombre_presentacion' and cantidad = '$cant_ps' ";
      $existe = ejecutarConsultaArray($sql_0); if ( $existe['status'] == false ) { return $existe; }

      if ( empty( $existe['data'] ) ){

        $sql_1 = "SELECT * FROM sunat_c03_unidad_medida WHERE idsunat_c03_unidad_medida = '$umedida_ps'";
        $umedida = ejecutarConsultaSimpleFila($sql_1); if ( $umedida['status'] == false ){ return $umedida; }
        $nombre_um = $umedida['data']['nombre'];

        $sql_2 = "INSERT INTO producto_presentacion (idproducto_sucursal, idsunat_c03_unidad_medida, nombre, cantidad,precio_venta,precio_venta_total) VALUES 
        ('$idproducto_sucursal_ps', '$umedida_ps', '$nombre_presentacion', '$cant_ps','$precio_presentacion_und','$precio_presentacion_total');";
        $newdata = ejecutarConsulta($sql_2, 'C');	if ($newdata['status'] == false) {  return $newdata; }
        return $newdata;

      } else {
        $info_repetida = ''; 
  
        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">'.
            '<span class="font-size-15px text-danger"><b>'.$value['nombre'].'</b></span><br>'.
            '<span class="font-size-15px" ><b>Cantidad: </b>'.$value['cantidad'].'</span><br>'.
            '<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }
    }

    public function editar_presentacion($idpresentacion, $idproducto_sucursal_ps, $umedida_ps, $cant_ps, $nombre_presentacion,$precio_presentacion_und,$precio_presentacion_total){

      $sql_0 = "SELECT * FROM producto_presentacion WHERE idproducto_sucursal = '$idproducto_sucursal_ps' AND nombre = '$nombre_presentacion' and cantidad = '$cant_ps' AND idproducto_presentacion <> '$idpresentacion';";
      $existe = ejecutarConsultaArray($sql_0); if ( $existe['status'] == false ) { return $existe; }

      if ( empty( $existe['data'] ) ){

        $sql = "UPDATE producto_presentacion SET idsunat_c03_unidad_medida = '$umedida_ps', cantidad = '$cant_ps', nombre = '$nombre_presentacion',
        precio_venta='$precio_presentacion_und',precio_venta_total='$precio_presentacion_total'
        WHERE idproducto_presentacion = '$idpresentacion';";
        $newdata = ejecutarConsulta($sql, 'U');	if ($newdata['status'] == false) {  return $newdata; }
        return $newdata;

      } else {
        $info_repetida = ''; 
  
        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">'.
            '<span class="font-size-15px text-danger"><b>'.$value['nombre'].'</b></span><br>'.
            '<span class="font-size-15px" ><b>Cantidad: </b>'.$value['cantidad'].'</span><br>'.
            '<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }
    }

    public function eliminar_presentacion($idproducto_presentacion){
      $sql = "UPDATE producto_presentacion SET estado_delete = 0 WHERE idproducto_presentacion = '$idproducto_presentacion'";
      return ejecutarConsulta($sql, 'U');
    }

    public function papelera_presentacion($idproducto_presentacion){
      $sql = "UPDATE producto_presentacion SET estado = 0 WHERE idproducto_presentacion = '$idproducto_presentacion'";
      return ejecutarConsulta($sql, 'U');
    }

    // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
    public function validar_code_producto($id, $code){
      $validar_id = empty($id) ? "" : "AND p.idproducto != '$id'" ;
      $sql = "SELECT p.idproducto, p.codigo_alterno, p.estado FROM producto AS p WHERE p.codigo_alterno = '$code' $validar_id;";
      $buscando =  ejecutarConsultaArray($sql); if ( $buscando['status'] == false) {return $buscando; }

      if (empty($buscando['data'])) { return true; }else { return false; }
    }

    // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════
    public function select_categoria()	{
      $sql="SELECT * FROM producto_categoria 
      WHERE estado = 1 AND estado_delete = 1
      order by case when idproducto_categoria = 1 then 0 else 1 end asc, nombre asc ;";
      return ejecutarConsultaArray($sql);   
    }

    public function select_marca()	{
      $sql="SELECT * FROM producto_marca WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }

    public function select_u_medida()	{
      $sql="SELECT * FROM sunat_c03_unidad_medida WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }
    // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
    public function select2_filtro_categoria()	{
      $sql="SELECT c.*
      FROM producto as p
      INNER JOIN producto_categoria as c ON c.idproducto_categoria = p.idproducto_categoria
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY c.idproducto_categoria ORDER BY c.idproducto_categoria ASC ;";
      return ejecutarConsultaArray($sql);   
    }

    public function select2_filtro_u_medida()	{
      $sql="SELECT um.*
      FROM producto_presentacion as p
      INNER JOIN sunat_c03_unidad_medida as um ON um.idsunat_c03_unidad_medida = p.idsunat_c03_unidad_medida
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY um.idsunat_c03_unidad_medida ORDER BY um.idsunat_c03_unidad_medida ASC;";
      return ejecutarConsultaArray($sql);   
    }

    public function select2_filtro_marca()	{
      $sql="SELECT m.*
      FROM producto as p
      INNER JOIN producto_marca as m ON m.idproducto_marca = p.idproducto_marca
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY m.idproducto_marca ORDER BY m.idproducto_marca ASC;";
      return ejecutarConsultaArray($sql);   
    }

  }