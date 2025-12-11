<?php

  require "../config/Conexion_v2.php";

  class Compras
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

    public function listar_tabla_compra() {

      $sql = "SELECT c.*, vw_p.*, tc.abreviatura as tp_comprobante,  c.estado
      FROM compra AS c
      INNER JOIN vw_persona_all AS vw_p ON vw_p.idpersona = c.idproveedor   
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = c.tipo_comprobante
      WHERE c.estado = 1 AND c.estado_delete = 1 order by c.fecha_compra desc, c.idcompra desc";
      $compra = ejecutarConsulta($sql); if ($compra['status'] == false) {return $compra; }

      return $compra;
    }

    public function insertar(
      // DATOS TABLA COMPRA
      $idproveedor,  $tipo_comprobante, $serie, $impuesto, $descripcion,
      $subtotal_compra, $tipo_gravada, $igv_compra, $total_compra, $fecha_compra, $img_comprob,
      //DATOS TABLA COMPRA DETALLE
      $idproducto_sucursal, $unidad_medidacompra, $cantidad, $cantidad_x_medida_compra, $precio_sin_igv, $precio_igv, $precio_con_igv, 
      $descuento, $subtotal_producto, $actualizar_stock
    ){

      // BUSCAMOS UNA CAJA ABIERTA
      $sql_0 = "SELECT * FROM caja WHERE estado_caja = 'ABIERTO';";
      $caja = ejecutarConsultaSimpleFila($sql_0); if ( $caja['status'] == false) {return $caja; }

      if( empty($caja['data']) ) {      
        $falta_caja = '<li><div class="text-start">Por favor, aperture una nueva <b>Caja</b> antes de agregar un registro</div><div class="text-start mt-2">Módulo: <a target="_blank" href="caja.php">Caja</a></div></li>';
        return array( 'status' => 'no_caja', 'message' => 'caja cerrada', 'data' => '<ul>'.$falta_caja.'</ul>', 'id_tabla' => '' );
      }
      $idcaja = $caja['data']['idcaja'];

      $sql_1 = "INSERT INTO compra(idproveedor, idcaja, fecha_compra, tipo_comprobante, serie_comprobante, val_igv, descripcion, subtotal, igv, total, comprobante) 
      VALUES ('$idproveedor', '$idcaja', '$fecha_compra', '$tipo_comprobante', '$serie', '$impuesto', '$descripcion', '$subtotal_compra', '$igv_compra', '$total_compra', '$img_comprob')";
      $newdata = ejecutarConsulta_retornarID($sql_1, 'C'); if ($newdata['status'] == false) { return  $newdata;}
      $id = $newdata['data'];

      $i = 0;
      $detalle_new = "";

      if ( !empty($newdata['data']) ) {      
        while ($i < count($idproducto_sucursal)) {

          $total_unidades = $cantidad[$i] * $cantidad_x_medida_compra[$i]; 
          $total_x_unidad = $subtotal_producto[$i]/$total_unidades; 

          $sql_2 = "INSERT INTO compra_detalle(idcompra, idproducto_sucursal,idsunat_c03_unidad_compra, cantidad_compra, cantidad_compra_presentacion, cantidad, precio_sin_igv, igv, precio_con_igv, descuento, subtotal, precio_compra_por_unidad, actualizar_stock)
          VALUES ('$id','$idproducto_sucursal[$i]' ,'$unidad_medidacompra[$i]' , '$cantidad[$i]','$cantidad_x_medida_compra[$i]','$total_unidades', '$precio_sin_igv[$i]', '$precio_igv[$i]', '$precio_con_igv[$i]', '$descuento[$i]', '$subtotal_producto[$i]','$total_x_unidad', '$actualizar_stock[$i]');";
          $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
          $id_d = $detalle_new['data'];

          // Aumentamos el Stock
          if ( $actualizar_stock[$i] == 'SI') {
            $sql_2_1 = "UPDATE producto_sucursal set  stock = stock + $total_unidades,precio_compra='$total_x_unidad' where idproducto_sucursal = '$idproducto_sucursal[$i]' ;";
            $actualizar_stock =  ejecutarConsulta($sql_2_1); if ($actualizar_stock['status'] == false) { return  $actualizar_stock;}
          }          

          // Calculamos promedio de compra por producto
          /*$sql_3 = "SELECT AVG(precio_con_igv) AS promedio_precio FROM compra_detalle WHERE idproducto = '$idproducto[$i]';";
          $agv_resultado = ejecutarConsultaSimpleFila($sql_3); if ($agv_resultado['status'] == false) { return $agv_resultado; }

          $promedio_precio = $agv_resultado['data']['promedio_precio'];

          // Actualizamos precio_compra en tabla producto
          $sql_4 = "UPDATE producto_sucursal SET precio_compra = '$promedio_precio' WHERE idproducto = '$idproducto[$i]';";
          $actualizar_precio = ejecutarConsulta($sql_4); 
          if ($actualizar_precio['status'] == false) { return $actualizar_precio; }*/

          $i = $i + 1;
        }
      }
      return $detalle_new;
    }

    public function editar( 
      // DATOS TABLA COMPRA
      $idcompra, $idproveedor,  $tipo_comprobante, $serie, $impuesto, $descripcion,
      $subtotal_compra, $tipo_gravada, $igv_compra, $total_compra, $fecha_compra, $img_comprob,
      //DATOS TABLA COMPRA DETALLE
      $idproducto_sucursal, $unidad_medidacompra, $cantidad, $cantidad_x_medida_compra, $precio_sin_igv, $precio_igv, $precio_con_igv, 
      $descuento, $subtotal_producto, $actualizar_stock
    ) {

      $sql_1 = "UPDATE compra SET idproveedor = '$idproveedor', fecha_compra = '$fecha_compra', tipo_comprobante = '$tipo_comprobante', serie_comprobante = '$serie', 
      val_igv = '$impuesto', descripcion = '$descripcion', subtotal = '$subtotal_compra', igv = '$igv_compra', total = '$total_compra', comprobante = '$img_comprob'
      WHERE idcompra = '$idcompra'";
      $result_sql_1 = ejecutarConsulta($sql_1, 'U');if ($result_sql_1['status'] == false) { return $result_sql_1; }

      // Devolvemos el Stock
      
        foreach ($idproducto_sucursal as $key => $val) {
          if ( $actualizar_stock[$key] == 'SI') { 
            $sql_1_1 = "UPDATE producto_sucursal set  stock = stock - (select cantidad from compra_detalle where idproducto_sucursal = '$val' and idcompra = '$idcompra') where idproducto_sucursal = '$val' ;";
            $update_stock =  ejecutarConsulta($sql_1_1); if ($update_stock['status'] == false) { return  $update_stock;} 
          }
        }
      // Eliminamos los productos
      $sql_del = "DELETE FROM compra_detalle WHERE idcompra = '$idcompra'";
      ejecutarConsulta($sql_del);

      // Creamos los productos
      foreach ($idproducto_sucursal as $i => $producto) {

        $total_unidades = $cantidad[$i] * $cantidad_x_medida_compra[$i]; 
        $total_x_unidad = $subtotal_producto[$i]/$total_unidades; 

        $sql_2 = "INSERT INTO compra_detalle(idcompra, idproducto_sucursal,idsunat_c03_unidad_compra, cantidad_compra, cantidad_compra_presentacion, cantidad, precio_sin_igv, igv, precio_con_igv, descuento, subtotal, precio_compra_por_unidad, actualizar_stock)
        VALUES ('$idcompra','$idproducto_sucursal[$i]' ,'$unidad_medidacompra[$i]' , '$cantidad[$i]','$cantidad_x_medida_compra[$i]','$total_unidades', '$precio_sin_igv[$i]', '$precio_igv[$i]', '$precio_con_igv[$i]', '$descuento[$i]', '$subtotal_producto[$i]','$total_x_unidad', '$actualizar_stock[$i]');";
        $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
        $id_d = $detalle_new['data'];

        // Aumentamos el Stock
        if ( $actualizar_stock[$i] == 'SI') { 
          $sql_2_1 = "UPDATE producto_sucursal set  stock = stock + $total_unidades,precio_compra='$total_x_unidad' where idproducto_sucursal = '$idproducto_sucursal[$i]' ;";
          $update_stock =  ejecutarConsulta($sql_2_1); if ($update_stock['status'] == false) { return  $update_stock;}
        }
        

        // Calculamos promedio de compra por producto
        /* $sql_3 = "SELECT AVG(precio_con_igv) AS promedio_precio FROM compra_detalle WHERE idproducto = '$idproducto[$i]';";
        $agv_resultado = ejecutarConsultaSimpleFila($sql_3); if ($agv_resultado['status'] == false) { return $agv_resultado; }

        $promedio_precio = $agv_resultado['data']['promedio_precio'];

        // Actualizamos precio_compra en tabla producto
        $sql_4 = "UPDATE producto SET precio_compra = '$promedio_precio' WHERE idproducto = '$idproducto[$i]';";
        $actualizar_precio = ejecutarConsulta($sql_4); 
        if ($actualizar_precio['status'] == false) { return $actualizar_precio; }
        */
      }  
      
      return array('status' => true, 'message' => 'Datos actualizados correctamente.');
    }
  

    public function mostrar_detalle_compra($idcompra){

      $sql_1 = "SELECT c.*, p.*, tc.abreviatura as tp_comprobante, sdi.abreviatura as tipo_documento, c.estado
      FROM compra AS c
      INNER JOIN persona AS p ON c.idproveedor = p.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = c.tipo_comprobante
      WHERE c.idcompra = '$idcompra'
      AND c.estado = 1 AND c.estado_delete = 1";
      $compra = ejecutarConsultaSimpleFila($sql_1); if ($compra['status'] == false) {return $compra; }


      $sql_2 = "SELECT cd.*, pd.*,p.*
      FROM compra_detalle AS cd
      INNER JOIN producto_sucursal AS pd ON cd.idproducto_sucursal = pd.idproducto_sucursal
      INNER JOIN producto AS p ON pd.idproducto = p.idproducto
      WHERE  cd.idcompra = '$idcompra'
      AND cd.estado = 1 AND cd.estado_delete = 1";
      $detalle = ejecutarConsultaArray($sql_2); if ($detalle['status'] == false) {return $detalle; }

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => ['compra' => $compra['data'], 'detalle' => $detalle['data']]];

    }


    public function mostrar_compra($id){
      $sql = "SELECT * FROM compra WHERE idcompra = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrar_editar_detalles_compra($id){
      $sql = "SELECT * FROM compra WHERE idcompra = '$id'";
      $compra = ejecutarConsultaSimpleFila($sql);

      $sql = "SELECT cd.*, p.nombre as nombre_producto, p.codigo, p.codigo_alterno, p.imagen, s_c03.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM compra_detalle AS cd
        INNER JOIN producto_sucursal AS ps ON ps.idproducto_sucursal = cd.idproducto_sucursal
        INNER JOIN producto AS p ON p.idproducto = ps.idproducto
        INNER JOIN sunat_c03_unidad_medida AS s_c03 ON s_c03.idsunat_c03_unidad_medida = cd.idsunat_c03_unidad_compra
        INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria
        INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca
      WHERE cd.idcompra = '$id'";
      $compra_detalle = ejecutarConsultaArray($sql);      

      foreach ( $compra_detalle['data'] as &$detalle) {

        $idum = $detalle['idsunat_c03_unidad_compra'];

        $sql = "SELECT idsunat_c03_unidad_medida, nombre FROM sunat_c03_unidad_medida ";
        $unidad_medida = ejecutarConsultaArray($sql);

        $html_unidad_medida = '';
        foreach ($unidad_medida['data'] as $key => $val1) { $html_unidad_medida .= '<option value="'.$val1['idsunat_c03_unidad_medida'].'" '.( $idum == $val1['idsunat_c03_unidad_medida'] ? 'selected' : '' ).' >'.$val1['nombre'].'</option>'; }

        $detalle['unidad_medida_html_option'] = $html_unidad_medida;    

      }


      return ['status' => true, 'message' =>'todo okey', 'data'=>['compra' => $compra['data'], 'compra_detalle' => $compra_detalle['data'],]];
    }

    public function eliminar($id){
      $sql = "UPDATE compra SET estado_delete = 0
      WHERE idcompra = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function desactivar($id){
      $sql = "UPDATE compra SET estado = 0
      WHERE idcompra = '$id'";
      return ejecutarConsulta($sql, 'U');
    }


    public function listar_tabla_producto(){
      $sql = "SELECT p.* from vw_producto as p
      WHERE p.pro_estado = 1 AND p.pro_estado_delete = 1";
      return ejecutarConsulta($sql);
    }

    public function mostrar_producto($idproducto_sucursal){
      $sql = "SELECT p.* from vw_producto as p
      WHERE p.idproducto_sucursal = '$idproducto_sucursal'  AND p.pro_estado = 1 AND p.pro_estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_producto_x_codigo($codigo){
      $sql = "SELECT p.* from vw_producto as p
      WHERE (p.codigo like '%$codigo%' OR p.codigo_alterno like '%$codigo%' OR p.nombre_producto like '%$codigo%' ) AND p.pro_estado = 1 AND p.pro_estado_delete = 1 ORDER BY p.nombre_producto LIMIT 10;";
       return ejecutarConsultaArray($sql);      
      
    }

    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // ═══════                                         S E C C I O N   D E T A L L E   C O M P R A S   X   P R O U D U C T O S                          ═══════
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    public function listar_tabla_detalle_compra_x_producto($idproducto_sucursal){
      $sql = "SELECT c.idcompra, c.serie_comprobante, c.fecha_compra, cd.cantidad_compra, cd.cantidad_compra_presentacion, cd.cantidad
      from compra as  c
      inner join compra_detalle as cd on c.idcompra = cd.idcompra
      where c.estado = '1' and c.estado_delete = '1' and cd.idproducto_sucursal = '$idproducto_sucursal' order by c.fecha_compra desc, c.idcompra desc;";
      return ejecutarConsultaArray($sql);
    }

  }

  




?>