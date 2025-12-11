<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Orden_venta
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

    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // ═══════                                         N U E V O   O R D E N   D E   V E N T A                                                              ═══════
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    public function insertar(
      // DATOS TABLA: venta
      $o_impuesto, $o_idsunat_c01, $o_tipo_comprobante, $o_serie_comprobante, $o_idcliente, $o_observacion_documento, $o_venta_subtotal, $o_venta_descuento, $o_venta_igv, $o_venta_total,    
      //DATOS TABLA: venta detalle
      $idproducto_presentacion, $pr_marca, $pr_categoria,$pr_nombre, $um_nombre,$um_abreviatura,  $cantidad_presentacion, $cantidad_venta, $cantidad_total, $precio_compra, $precio_sin_igv, $precio_igv, 
      $precio_con_igv,  $precio_venta_descuento, 
      $f_descuento, $descuento_porcentaje, $subtotal_producto, $subtotal_no_descuento_producto, $precio_por_mayor     
    ){  
      
      $tipo_v = "ORDEN DE VENTA";  $fecha_actual_amd = date('Y-m-d');     

      // Validamos serie comprobante
      if ( empty($o_serie_comprobante) ) {
        return array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'Serie no seleccionada!!', 'message' => 'Seleccione una serie válida, o pida a su administrador permiso para seleccionar alguna.', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );          
      }
      

      // BUSCAMOS UNA CAJA ABIERTA
      $sql_0 = "SELECT * FROM caja WHERE estado_caja = 'ABIERTO';";
      $caja = ejecutarConsultaSimpleFila($sql_0); if ( $caja['status'] == false) {return $caja; }

      if( empty($caja['data']) ) {      
        $falta_caja = '<li><div class="text-start">Por favor, aperture una nueva <b>Caja</b> antes de agregar un registro</div><div class="text-start mt-2">Módulo: <a target="_blank" href="caja.php">Caja</a></div></li>';
        return array( 'status' => 'no_caja', 'message' => 'caja cerrada', 'data' => '<ul>'.$falta_caja.'</ul>', 'id_tabla' => '' );
      }
      $idcaja = $caja['data']['idcaja'];       

      // VALIDAMOS EL PERIODO CONTABLE
      $sql_periodo = "SELECT idperiodo_contable FROM periodo_contable WHERE estado = '1' AND estado_delete = '1' AND '$fecha_actual_amd' BETWEEN fecha_inicio AND fecha_fin;";
      $buscando_periodo = ejecutarConsultaSimpleFila($sql_periodo); if ($buscando_periodo['status'] == false) {return $buscando_periodo; }
      $idperiodo_contable = empty($buscando_periodo['data']) ? '' : (empty($buscando_periodo['data']['idperiodo_contable']) ? '' : $buscando_periodo['data']['idperiodo_contable'] ) ;
      // return $sql_periodo;
      if ( empty($idperiodo_contable) ) {  
        $retorno = array( 'status' => 'error_usuario', 'code_error' => '', 'titulo' => 'No existe periodo!!', 
        'message' => ' No cierre el módulo. <br> No existe un periodo contable del mes: <b>'. nombre_mes(date('Y-m-d')).'-'.date('Y'). '</b>. '. ($_SESSION['user_cargo'] == 'ADMINISTRADOR' ? 'Configure el período contable en el módulo siguiente: <a href="periodo_facturacion.php" target="_blank" >Periodo contable</a>' : 'Solicite a su administrador que configure el período contable para el mes actual.'), 
        'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_periodo['data'], 'id_tabla' => '' );
        return $retorno;
      }
      
      // INSERTAMOS: VENTA
      $sql_1 = "INSERT INTO venta(idpersona_cliente, idcaja, idperiodo_contable,  crear_enviar_sunat, idsunat_c01, tipo_comprobante, serie_comprobante,  
      impuesto, venta_subtotal, venta_descuento, venta_igv, venta_total, venta_cuotas, usar_anticipo,  observacion_documento) 
      VALUES ('$o_idcliente', null, '$idperiodo_contable', 'NO', '$o_idsunat_c01', '$o_tipo_comprobante', '$o_serie_comprobante', '$o_impuesto', '$o_venta_subtotal', '$o_venta_descuento',
      '$o_venta_igv','$o_venta_total','NO', 'NO',   '$o_observacion_documento')"; 
      $newdata = ejecutarConsulta_retornarID($sql_1, 'C', 'Creando registro -- Orden de Venta'); if ($newdata['status'] == false) { return  $newdata;}
      $id = $newdata['data'];

      $i = 0;
      $detalle_new = [];
      $monto_recibido = 0;  
    
      if ( !empty($newdata['data']) ) {      
        while ($i < count($idproducto_presentacion)) {
          $subtotal_compra = $cantidad_total[$i] * $precio_compra[$i];
          $sql_2 = "INSERT INTO venta_detalle( idventa, idproducto_presentacion, pr_nombre, pr_marca, pr_categoria, v_tipo_comprobante,  cantidad_presentacion, cantidad_venta, cantidad_total, precio_compra, precio_venta, 
          precio_venta_descuento, descuento, descuento_porcentaje, subtotal, subtotal_compra, subtotal_no_descuento, um_nombre, um_abreviatura, precio_por_mayor)
          VALUES ('$id', '$idproducto_presentacion[$i]', '$pr_nombre[$i]', '$pr_marca[$i]', '$pr_categoria[$i]', '$tipo_v', '$cantidad_presentacion[$i]', '$cantidad_venta[$i]', '$cantidad_total[$i]', '$precio_compra[$i]',  '$precio_con_igv[$i]', 
          '$precio_venta_descuento[$i]', '$f_descuento[$i]', '$descuento_porcentaje[$i]', '$subtotal_producto[$i]', '$subtotal_compra', '$subtotal_no_descuento_producto[$i]', '$um_nombre[$i]', '$um_abreviatura[$i]', '$precio_por_mayor[$i]');";
          $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
          $id_d = $detalle_new['data'];            

          // Reducimos el Stock
          $sql_2_1 = "UPDATE producto_sucursal set  stock = stock - $cantidad_total[$i] where idproducto_sucursal = (SELECT DISTINCT idproducto_sucursal FROM producto_presentacion where idproducto_presentacion ='$idproducto_presentacion[$i]') ;";
          $actualizar_stock =  ejecutarConsulta($sql_2_1, 'U', 'Actualizando Estock -- Orden de Venta'); if ($actualizar_stock['status'] == false) { return  $actualizar_stock;} 

          $i = $i + 1;
        }
      }    

      // Actualizamos: total recibido y vuelto
      $monto_vuelto = $monto_recibido -floatval($o_venta_total) ;
      $sql_4 = "UPDATE venta SET vc_estado  = 'pagado', vc_cantidad_total = '0', total_recibido = '$monto_recibido', total_vuelto = '$monto_vuelto' WHERE idventa = '$id';";
      $actulizando_vuelto = ejecutarConsulta($sql_4, 'U', 'Actualizando monto recibido y el vuelto -- Orden de Venta'); if ($actulizando_vuelto['status'] == false) { return  $actulizando_vuelto;} 

      return $newdata;      
    }

    public function editar( // SI SE EDITA UNA VENTA, SE EDITA EN DIFERENTE CAJA??
      // DATOS TABLA venta
      $idventa, $o_impuesto, $o_idsunat_c01, $o_tipo_comprobante, $o_serie_comprobante, $o_idcliente, $o_observacion_documento, $o_venta_subtotal, $o_venta_descuento, $o_venta_igv, $o_venta_total,    
      //DATOS TABLA: venta detalle
      $idproducto_presentacion, $pr_marca, $pr_categoria,$pr_nombre, $um_nombre,$um_abreviatura,  $cantidad_presentacion, $cantidad_venta, $cantidad_total, $precio_compra, $precio_sin_igv, $precio_igv, $precio_con_igv,  $precio_venta_descuento, 
      $f_descuento, $descuento_porcentaje, $subtotal_producto, $subtotal_no_descuento_producto, $precio_por_mayor    
      
    ) {

      $tipo_v = "ORDEN DE VENTA";  $fecha_actual_amd = date('Y-m-d');     
      

      $sql_1 = "UPDATE venta SET idpersona_cliente = '$o_idcliente', impuesto = '$o_impuesto', venta_subtotal = '$o_venta_subtotal', venta_descuento = '$o_venta_descuento', venta_igv = '$o_venta_igv', 
      venta_total = '$o_venta_total', observacion_documento = '$o_observacion_documento'     
      WHERE idventa = '$idventa'";
      $actualizar_venta = ejecutarConsulta($sql_1, 'U', 'Actualizando datos Orden de Venta');if ($actualizar_venta['status'] == false) { return $actualizar_venta; }

      // Devolvemos el Stock
      foreach ($idproducto_presentacion as $key => $val) {
        $sql_2_1 = "UPDATE producto_sucursal set  stock = stock + (select cantidad_total from venta_detalle where idproducto_presentacion = '$val' and idventa = '$idventa') 
        where idproducto_sucursal = (SELECT DISTINCT idproducto_sucursal FROM producto_presentacion where idproducto_presentacion ='$val');";
        $actualizar_stock =  ejecutarConsulta($sql_2_1, 'U', 'Devolviendo Stock -- Orden de Venta'); if ($actualizar_stock['status'] == false) { return  $actualizar_stock;} 
      }
      
      // Eliminamos los productos
      $sql_del1 = "DELETE FROM venta_detalle WHERE idventa = '$idventa'"; ejecutarConsulta($sql_del1);
      

      $i = 0;
      $detalle_new = "";
      $monto_recibido = 0;        
      
      while ($i < count($idproducto_presentacion)) {
        $subtotal_compra = $cantidad_total[$i] * $precio_compra[$i];
        $sql_2 = "INSERT INTO venta_detalle( idventa, idproducto_presentacion, pr_nombre, pr_marca, pr_categoria, v_tipo_comprobante,  cantidad_presentacion, cantidad_venta, cantidad_total, precio_compra, precio_venta, 
          precio_venta_descuento, descuento, descuento_porcentaje, subtotal, subtotal_compra, subtotal_no_descuento, um_nombre, um_abreviatura, precio_por_mayor)
          VALUES ('$idventa', '$idproducto_presentacion[$i]', '$pr_nombre[$i]', '$pr_marca[$i]', '$pr_categoria[$i]', '$tipo_v', '$cantidad_presentacion[$i]', '$cantidad_venta[$i]', '$cantidad_total[$i]', '$precio_compra[$i]',  '$precio_con_igv[$i]', 
          '$precio_venta_descuento[$i]', '$f_descuento[$i]', '$descuento_porcentaje[$i]', '$subtotal_producto[$i]', '$subtotal_compra', '$subtotal_no_descuento_producto[$i]', '$um_nombre[$i]', '$um_abreviatura[$i]', '$precio_por_mayor[$i]');";
          $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
          $id_d = $detalle_new['data'];     
        // Reducimos el Stock        
        $sql_2_1 = "UPDATE producto_sucursal set  stock = stock - $cantidad_total[$i] where idproducto_sucursal = (SELECT DISTINCT idproducto_sucursal FROM producto_presentacion where idproducto_presentacion ='$idproducto_presentacion[$i]') ;";
        $actualizar_stock =  ejecutarConsulta($sql_2_1, 'U', 'Actualizando Estock -- Orden de Venta'); if ($actualizar_stock['status'] == false) { return  $actualizar_stock;} 

        $i = $i + 1;
      }    
     

      // Actualizamos: total recibido y vuelto
      $monto_vuelto = $monto_recibido - $o_venta_total;
      $sql_4 = "UPDATE venta SET total_recibido = '$monto_recibido', total_vuelto = '$monto_vuelto' WHERE idventa = '$idventa';";
      $actulizando_vuelto = ejecutarConsulta($sql_4, 'U', 'Actualizando monto recibido y el vuelto -- Orden de Venta'); if ($actulizando_vuelto['status'] == false) { return  $actulizando_vuelto;} 

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => $idventa, 'id_tabla' => $idventa,  ];

    }   

    
    public function actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error) {
      //echo json_encode( [$idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error]); die();
      $sql_1 = "UPDATE venta SET sunat_estado='$sunat_estado',sunat_observacion='$sunat_observacion',sunat_code='$sunat_code',
      sunat_hash='$sunat_hash',sunat_mensaje='$sunat_mensaje', sunat_error = '$sunat_error' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql_1, 'U', 'Actualizando estado de SUNAT -- Orden de Venta');
    } 
    
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // ═══════                                         PRODUCTO                                                                 ═══════
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

    public function listar_producto_x_nombre($search){
      $sql = "SELECT p.* from vw_producto_presentacion as p
      WHERE (p.codigo like '%$search%' OR p.codigo_alterno like '%$search%' OR p.nombre_producto like '%$search%' ) AND p.pro_estado = 1 AND p.pro_estado_delete = 1 and pp_estado='1' and pp_estado_delete='1'
      ORDER BY p.nombre_producto asc, p.cantidad_presentacion asc LIMIT 20;";
      return ejecutarConsultaArray($sql);      
    }

    public function listar_producto_select_pedido($precio_por_mayor,$idproducto_sucursal,$idproducto_presentacion ){

      $sql = "SELECT  p.*,
      CASE WHEN '$precio_por_mayor' = 'SI' THEN p.precio_por_mayor ELSE p.precio_venta END AS precio,imagen
      FROM vw_producto_presentacion as p
      where p.idproducto_sucursal='$idproducto_sucursal' and p.idproducto_presentacion='$idproducto_presentacion';";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function select2_cliente() {
      $sql="SELECT idpersona_cliente, cliente_nombre_completo, tipo_documento_abrev_nombre, numero_documento 
      FROM vw_cliente_all where tipo_persona ='CLIENTE' order by case when idpersona_cliente = 1 then 0 else 1 end asc, cliente_nombre_completo asc ";

      return ejecutarConsultaArray($sql);
      
    }

        
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // ═══════                                         N U E V O   C L I E N T E                                                                 ═══════
    // ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

    public function agregar_nuevo_cliente( $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
      $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo ){

      if ( empty($cli_centro_poblado) ) { $cli_centro_poblado = 1;}

      $sql_0 = "SELECT p.*  FROM vw_persona_all as p WHERE p.numero_documento = '$cli_numero_documento' AND '$cli_numero_documento' <> '00000000'";
      $buscando = ejecutarConsultaArray($sql_0);		

      if ( empty($buscando['data']) || $cli_tipo_documento == '0' ) {

        $sql = "INSERT INTO persona( idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, apellidos_nombrecomercial, tipo_documento, numero_documento, fecha_nacimiento, celular, direccion, 
        direccion_referencia, departamento, provincia, distrito, cod_ubigeo, correo) 
        VALUES ('$cli_idtipo_persona', '1', '1','$cli_tipo_persona_sunat','$cli_nombre_razonsocial', '$cli_apellidos_nombrecomercial','$cli_tipo_documento','$cli_numero_documento', null, '$cli_celular', '$cli_direccion', '$cli_direccion_referencia',
        '$cli_departamento', '$cli_provincia', '$cli_distrito', '$cli_ubigeo', '$cli_correo' )";
        $new_persona = ejecutarConsulta_retornarID($sql, 'C');if ($new_persona['status'] == false) {return $new_persona;}

        $id = $new_persona['data'];
       
        $sql2 = "INSERT INTO persona_cliente(idpersona,idcentro_poblado, nota)
        VALUES ('$id','$cli_centro_poblado', '')";
        $new_cliente =  ejecutarConsulta_retornarID($sql2, 'C');	if ($new_cliente['status'] == false) {	return $new_cliente;	}

        return $new_cliente;      
      } else {
        $info_repetida = ''; 

        foreach ($buscando['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['persona_nombre_completo'].'</span><br>
            <b>'.$value['tipo_documento_abrev_nombre'].': </b>'.$value['numero_documento'].'<br>
            <b>Distrito: </b>'.$value['distrito'].'<br>
            <b>Tipo: </b>'.$value['tipo_persona'].'<br>
            <b>Cargo: </b>'.$value['cargo_trabajador'].'<br>
            <b>Papelera: </b>'.( $value['estado_pc']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete_pc']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }			
    }

    public function editar_nuevo_cliente($cli_idpersona, $cli_tipo_persona_sunat, $cli_idtipo_persona, $cli_tipo_documento, $cli_numero_documento, $cli_nombre_razonsocial, $cli_apellidos_nombrecomercial, $cli_correo,$cli_celular,
          $cli_direccion, $cli_direccion_referencia, $cli_centro_poblado, $cli_distrito, $cli_departamento, $cli_provincia, $cli_ubigeo){
      $sql = "";
      return ejecutarConsulta($sql, 'U');      
    }


  }



    