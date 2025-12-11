<?php

require "../config/Conexion_v2.php";

class Papelera{

  public $id_usr_sesion;

  public function __construct($id_usr_sesion = 0)
  {
    $this->id_usr_sesion = $id_usr_sesion;
  }


  
  public function tabla_papelera(){

    $data = [];

    $sql_1 = "SELECT * FROM venta WHERE estado = 0 AND estado_delete = 1 and tipo_comprobante = '12';";
    $ventas = ejecutarConsultaArray($sql_1); if ($ventas['status'] == false) { return $ventas; }

    if (!empty($banco['data'])) {
      foreach($ventas['data'] as $value1){
        $data[] = [
          'nombre_tabla'    => 'venta',
          'nombre_id_tabla' => 'idventa',
          'id_tabla'        => $value1['idventa'],
          'modulo'          => 'FACTURACION',
          'nombre_archivo'  => '<b>Doc: </b>'.$value1['serie_comprobante'].'-'.$value1['numero_comprobante '].'<br>'.     
          '<b>Fecha emision: </b>'.$value1['fecha_emision '].'<br>'. 
          '<b>Total venta: </b>'.$value1['venta_total'].'<br>'. 
          '<b>Metodo Pago: </b>'.$value1['metodo_pago '].'<br>' ,
          'descripcion'     => $value1['observacion_documento '], 
          'created_at'      => $value1['created_at'],
          'updated_at'      => $value1['updated_at'],
          'list_archivo'     => $value1['serie_comprobante'].'-'.$value1['numero_comprobante '],
        ];
      }
    }

    $sql_1 = "SELECT * FROM bancos WHERE estado = 0 AND estado_delete = 1;";
    $bancos = ejecutarConsultaArray($sql_1); if ($bancos['status'] == false) { return $bancos; }

    if (!empty($banco['data'])) {
      foreach($bancos['data'] as $value1){
        $data[] = [
          'nombre_tabla'    => 'bancos',
          'nombre_id_tabla' => 'idbancos',
          'id_tabla'        => $value1['idbancos'],
          'modulo'          => 'BANCOS',
          'nombre_archivo'  => '<b>Banco: </b>'.$value1['nombre'].'<br>'. 
          '<b>Alias: </b>'.$value1['alias'].'<br>'.
          '<b>Formato Cta: </b>'.$value1['formato_cta'].'<br>'. 
          '<b>Formato Cci: </b>'.$value1['formato_cci'].'<br>'. 
          '<b>Formato Dtrac: </b>'.$value1['formato_detracciones'].'<br>' ,
          'descripcion'     => '- - -',
          'created_at'      => $value1['created_at'],
          'updated_at'      => $value1['updated_at'],
          'list_archivo'     => $value1['nombre'],
        ];
      }
    }


    $sql_2 = "SELECT * FROM cargo_trabajador WHERE estado = 0 AND estado_delete = 1;";
    $cargo_trabajador = ejecutarConsultaArray($sql_2);
    if ($cargo_trabajador['status'] == false) { return $cargo_trabajador; }

    if (!empty($cargo_trabajador['data'])) {
      foreach($cargo_trabajador['data'] as $value2){
        $data[] = [
          'nombre_tabla'    => 'cargo_trabajador',
          'nombre_id_tabla' => 'idcargo_trabajador',
          'id_tabla'        => $value2['idcargo_trabajador'],
          'modulo'          => 'CONTABILIDAD',
          'nombre_archivo'  => $value2['nombre'],
          'descripcion'     => '- - -',
          'created_at'      => $value2['created_at'],
          'updated_at'      => $value2['updated_at'],
          'list_archivo'     => $value2['nombre'],
        ];
      }
    }


    $sql_3 = "SELECT * FROM producto_categoria WHERE estado = 0 AND estado_delete = 1;";
    $categoria = ejecutarConsultaArray($sql_3); 
    if ($categoria['status'] == false) { return $categoria; }

    if (!empty($categoria['data'])) {
      foreach ($categoria['data'] as $value3) {
          $data[] = [
            'nombre_tabla'    => 'producto_categoria',
            'nombre_id_tabla' => 'idproducto_categoria',
            'id_tabla'        => $value3['idproducto_categoria'],
            'modulo'          => 'ARTICULOS',
            'nombre_archivo'  => $value3['nombre'],
            'descripcion'     => $value3['descripcion'],
            'created_at'      => $value3['created_at'],
            'updated_at'      => $value3['updated_at'],
            'list_archivo'     => $value3['nombre'],
          ];
      }
    }


    $sql_4 = "SELECT * FROM centro_poblado WHERE estado = 0 AND estado_delete = 1;";
    $centro_poblado = ejecutarConsultaArray($sql_4); 
    if ($centro_poblado['status'] == false) { return $centro_poblado; }

    if (!empty($centro_poblado['data'])) {
      foreach ($centro_poblado['data'] as $value4) {
          $data[] = [
            'nombre_tabla'    => 'centro_poblado',
            'nombre_id_tabla' => 'idcentro_poblado',
            'id_tabla'        => $value4['idcentro_poblado'],
            'modulo'          => 'SIN MODULO',
            'nombre_archivo'  => $value4['nombre'],
            'descripcion'     => $value4['descripcion'],
            'created_at'      => $value4['created_at'],
            'updated_at'      => $value4['updated_at'],
            'list_archivo'     => $value4['nombre'],
          ];
      }
    }


    $sql_5 = "SELECT c.idcompra, c.serie_comprobante, c.descripcion, c.created_at, c.updated_at, tc.abreviatura as tp_comprobante
    FROM compra AS c 
    INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = c.tipo_comprobante
    WHERE c.estado = 0 AND c.estado_delete = 1;";
    $compra = ejecutarConsultaArray($sql_5); 
    if ($compra['status'] == false) { return $compra; }

    if (!empty($compra['data'])) {
      foreach ($compra['data'] as $value5) {
          $data[] = [
            'nombre_tabla'    => 'compra',
            'nombre_id_tabla' => 'idcompra',
            'id_tabla'        => $value5['idcompra'],
            'modulo'          => 'LOGISTICA',
            'nombre_archivo'  =>  '<b>Compra</b><br>'. 
                                 '<b>Tipo Comb.: </b>'.$value5['tp_comprobante'].'<br>'. 
                                  '<b>Seris Comb.: </b>'.$value5['serie_comprobante'].'<br>',
            'descripcion'     => $value5['descripcion'],
            'created_at'      => $value5['created_at'],
            'updated_at'      => $value5['updated_at'],
            'list_archivo'     => 'COMPRA: Tipo -> '.$value5['tp_comprobante'].'-- Serie -> '.$value5['serie_comprobante'],
          ];
      }
    }

    
    $sql_6 = "SELECT * FROM empresa WHERE estado = 0 AND estado_delete = 1;";
    $empresa = ejecutarConsultaArray($sql_6); 
    if ($empresa['status'] == false) { return $empresa; }

    if (!empty($empresa['data'])) {
      foreach ($empresa['data'] as $value6) {
          $data[] = [
            'nombre_tabla'    => 'empresa',
            'nombre_id_tabla' => 'idempresa',
            'id_tabla'        => $value6['idempresa'],
            'modulo'          => 'EMPRESA',
            'nombre_archivo'  => '<b>Razon Social: </b>'.$value6['nombre_razon_social'].'<br/>'.
                                 '<b>Doc.: </b>'.$value6['numero_documento'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value6['created_at'],
            'updated_at'      => $value6['updated_at'],
            'list_archivo'     => $value6['nombre_razon_social'],
          ];
      }
    }


    $sql_7 = "SELECT * FROM experiencias WHERE estado = 0 AND estado_delete = 1;";
    $experiencia = ejecutarConsultaArray($sql_7); 
    if ($experiencia['status'] == false) { return $experiencia; }

    if (!empty($experiencia['data'])) {
      foreach ($experiencia['data'] as $value7) {
          $data[] = [
            'nombre_tabla'    => 'experiencia',
            'nombre_id_tabla' => 'idexperiencia',
            'id_tabla'        => $value7['idexperiencia'],
            'modulo'          => 'SIN MODULO',
            'nombre_archivo'  => $value7['nombre'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value7['created_at'],
            'updated_at'      => $value7['updated_at'],
            'list_archivo'     => $value7['nombre'],
          ];
      }
    }


    $sql_8 = "SELECT gdt.idingreso_egreso_interno, gdt.descripcion_comprobante, gdt.created_at, gdt.updated_at, p.nombre_razonsocial, p.apellidos_nombrecomercial
    FROM ingreso_egreso_interno as gdt
    INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = gdt.idpersona_trabajador 
    INNER JOIN persona as p ON p.idpersona = pt.idpersona 
    WHERE gdt.estado = '0' AND gdt.estado_delete = '1';";

    $gasto_trabajador = ejecutarConsultaArray($sql_8); 
    if ($gasto_trabajador['status'] == false) { return $gasto_trabajador; }

    if (!empty($gasto_trabajador['data'])) {
      foreach ($gasto_trabajador['data'] as $value8) {
          $data[] = [
            'nombre_tabla'    => 'ingreso_egreso_interno',
            'nombre_id_tabla' => 'idingreso_egreso_interno',
            'id_tabla'        => $value8['idingreso_egreso_interno'],
            'modulo'          => 'GASTO DE TRABAJADOR',
            'nombre_archivo'  => $value8['nombre_razonsocial'].' '.$value8['apellidos_nombrecomercial'],
            'descripcion'     => $value8['descripcion_comprobante'],
            'created_at'      => $value8['created_at'],
            'updated_at'      => $value8['updated_at'],
            'list_archivo'     => $value8['nombre_razonsocial'].' '.$value8['apellidos_nombrecomercial'],
          ];
      }
    }


    $sql_9 = "SELECT * FROM producto_marca WHERE estado = '0' AND estado_delete = '1';";
    $marca = ejecutarConsultaArray($sql_9);  
    if ($marca['status'] == false) { return $marca; }

    if (!empty($marca['data'])) {
      foreach ($marca['data'] as $value9) {
          $data[] = [
            'nombre_tabla'    => 'producto_marca',
            'nombre_id_tabla' => 'idproducto_marca',
            'id_tabla'        => $value9['idproducto_marca'],
            'modulo'          => 'MARCA',
            'nombre_archivo'  => $value9['nombre'],
            'descripcion'     => $value9['descripcion'],
            'created_at'      => $value9['created_at'],
            'updated_at'      => $value9['updated_at'],
            'list_archivo'     => $value9['nombre'],
          ];
      }
    }


    // $sql_10 = "SELECT * FROM noticias_inicio WHERE estado = '0' AND estado_delete = '1';";
    // $noticias_inicio = ejecutarConsultaArray($sql_10);  
    // if ($noticias_inicio['status'] == false) { return $noticias_inicio; }

    // if (!empty($noticias_inicio['data'])) {
    //   foreach ($noticias_inicio['data'] as $value10) {
    //       $data[] = [
    //         'nombre_tabla'    => 'noticias_inicio',
    //         'nombre_id_tabla' => 'idnoticias_inicio',
    //         'id_tabla'        => $value10['idnoticias_inicio'],
    //         'modulo'          => 'GASTO DE TRABAJADOR',
    //         'nombre_archivo'  => '../assets/modulo/noticias_inicio/'.$value10['imagen'],
    //         'descripcion'     => $value10['titulo'],
    //         'created_at'      => $value10['created_at'],
    //         'updated_at'      => $value10['updated_at'],
    //         'list_archivo'     => $value10['titulo'],
    //       ];
    //   }
    // }


    $sql_11 = "SELECT p.nombre_razonsocial, p.apellidos_nombrecomercial, pgt.descripcion, pgt.monto, mpgt.mes_nombre, mpgt.anio
    FROM pago_trabajador AS pgt
    INNER JOIN mes_pago_trabajador AS mpgt ON pgt.idmes_pago_trabajador = mpgt.idmes_pago_trabajador
    INNER JOIN persona AS p ON mpgt.idpersona = p.idpersona
    WHERE pgt.estado = '0' AND pgt.estado_delete = '1';";
    $pago_trabajador = ejecutarConsultaArray($sql_11);  
    if ($pago_trabajador['status'] == false) { return $pago_trabajador; }

    if (!empty($pago_trabajador['data'])) {
      foreach ($pago_trabajador['data'] as $value11) {
          $data[] = [
            'nombre_tabla'    => 'pago_trabajador',
            'nombre_id_tabla' => 'idpago_trabajador',
            'id_tabla'        => $value11['idpago_trabajador'],
            'modulo'          => 'PAGO TRABAJADOR',
            'nombre_archivo'  => '<b>Trabajador: </b>'.$value11['nombre_razonsocial'].' '.$value11['apellidos_nombrecomercial'] .'<br/>'.
                                 $value11['mes_nombre'].'-'.$value11['anio'].'<br/>'.
                                 '<b>S/ </b>'.$value11['monto'],
            'descripcion'     => $value11['descripcion'],
            'created_at'      => $value11['created_at'],
            'updated_at'      => $value11['updated_at'],
            'list_archivo'     => '<b>Trabajador: </b>'.$value11['nombre_razonsocial'].' '.$value11['apellidos_nombrecomercial'] .' -> <b>S/ </b>'.$value11['monto'],
          ];
      }
    }


    $sql_12 = "SELECT * FROM permiso WHERE estado = '0' AND estado_delete = '1';";
    $permiso = ejecutarConsultaArray($sql_12);  
    if ($permiso['status'] == false) { return $permiso; }

    if (!empty($permiso['data'])) {
      foreach ($permiso['data'] as $value12) {
          $data[] = [
            'nombre_tabla'    => 'permiso',
            'nombre_id_tabla' => 'idpermiso',
            'id_tabla'        => $value12['idpermiso'],
            'modulo'          => 'PERMISO',
            'nombre_archivo'  => '<b> Modulo: </b>'.$value12['modulo'].'<br/>'.
                                 '<b>Sub Modulo: </b>'.$value12['submodulo'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value12['created_at'],
            'updated_at'      => $value12['updated_at'],
            'list_archivo'     => 'Permiso para el Modulo: '.$value12['modulo'],
          ];
      }
    }


    $sql_13 = "SELECT p.idpersona, p.nombre_razonsocial, p.apellidos_nombrecomercial, p.numero_documento, sdi.abreviatura, p.created_at, p.updated_at
    FROM persona p
    INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
    WHERE p.idtipo_persona = 4 
    AND p.estado = '0' AND p.estado_delete = '1';";
    $proveedor = ejecutarConsultaArray($sql_13);  
    if ($proveedor['status'] == false) { return $proveedor; }

    if (!empty($proveedor['data'])) {
      foreach ($proveedor['data'] as $value13) {
          $data[] = [
            'nombre_tabla'    => 'persona',
            'nombre_id_tabla' => 'idpersona',
            'id_tabla'        => $value13['idpersona'],
            'modulo'          => 'PROVEEDORES',
            'nombre_archivo'  => '<b> Razon Social: </b>'.$value13['nombre_razonsocial'].' '.$value13['apellidos_nombrecomercial'] .'<br/>'.
                                 '<b>'.$value13['abreviatura'].': </b>'.$value13['numero_documento'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value13['created_at'],
            'updated_at'      => $value13['updated_at'],
            'list_archivo'     => '<b> Razon Social: </b>'.$value13['nombre_razonsocial'].' '.$value13['apellidos_nombrecomercial'],
          ];
      }
    }


    $sql_14 = "SELECT pc.idpersona_cliente, p.nombre_razonsocial, p.apellidos_nombrecomercial, sc06.abreviatura as tipo_documento_abreviatura, p.numero_documento, p.direccion, pc.created_at, pc.updated_at
    FROM persona_cliente AS pc
    INNER JOIN persona AS p ON p.idpersona = pc.idpersona    
    INNER JOIN sunat_c06_doc_identidad AS sc06 ON sc06.code_sunat = p.tipo_documento    
    WHERE pc.estado = '0' AND pc.estado_delete = '1';";
    $persona_cliente = ejecutarConsultaArray($sql_14);  
    if ($persona_cliente['status'] == false) { return $persona_cliente; }

    if (!empty($persona_cliente['data'])) {
      foreach ($persona_cliente['data'] as $value14) {
          $data[] = [
            'nombre_tabla'    => 'persona_cliente',
            'nombre_id_tabla' => 'idpersona_cliente',
            'id_tabla'        => $value14['idpersona_cliente'],
            'modulo'          => 'CLIENTES',
            'nombre_archivo'  => '<b> Cliente: </b>'.$value14['nombre_razonsocial'].' '.$value14['apellidos_nombrecomercial'] .'<br/>'.                               
                                 '<b>'.$value14['tipo_documento_abreviatura'].': </b>'.$value14['numero_documento'].
                                 '<b>Direccion: </b>'.$value14['direccion'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value14['created_at'],
            'updated_at'      => $value14['updated_at'],
            'list_archivo'     => '<b> Cliente: </b>'.$value14['nombre_razonsocial'].' '.$value14['apellidos_nombrecomercial'],
          ];
      }
    }


    $sql_15 = "SELECT pt.idpersona_trabajador, pt.ruc, pt.clave_sol, p.nombre_razonsocial, p.apellidos_nombrecomercial, pt.created_at, pt.updated_at
    FROM persona_trabajador AS pt
    INNER JOIN persona AS p ON p.idpersona = pt.idpersona
    WHERE pt.estado = '0' AND pt.estado_delete = '1';";
    $persona_trabajador = ejecutarConsultaArray($sql_15);  
    if ($persona_trabajador['status'] == false) { return $persona_trabajador; }

    if (!empty($persona_trabajador['data'])) {
      foreach ($persona_trabajador['data'] as $value15) {
          $data[] = [
            'nombre_tabla'    => 'persona_trabajador',
            'nombre_id_tabla' => 'idpersona_trabajador',
            'id_tabla'        => $value15['idpersona_trabajador'],
            'modulo'          => 'TRABAJADORES',
            'nombre_archivo'  => '<b>Trabajador: </b>'.$value15['nombre_razonsocial'].' '.$value15['apellidos_nombrecomercial'] .'<br/>'.
                                 '<b>ruc: </b>'.$value15['ruc']. '</br>'.
                                 '<b>Clave Sol: </b>'.$value15['clave_sol'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value15['created_at'],
            'updated_at'      => $value15['updated_at'],
            'list_archivo'     => '<b>Trabajador: </b>'.$value15['nombre_razonsocial'].' '.$value15['apellidos_nombrecomercial'],
          ];
      }
    }

    $sql_17 = "SELECT p.idproducto, p.nombre AS producto, ct.nombre AS categoria, m.nombre AS marca, p.descripcion, p.created_at, p.updated_at
    FROM producto AS p
    INNER JOIN producto_categoria AS ct ON ct.idproducto_categoria = p.idproducto_categoria
    INNER JOIN producto_marca AS m ON m.idproducto_marca = p.idproducto_marca
    WHERE p.estado = '0' AND p.estado_delete = '1';";
    $producto = ejecutarConsultaArray($sql_17);  
    if ($producto['status'] == false) { return $producto; }

    if (!empty($producto['data'])) {
      foreach ($producto['data'] as $value17) {
          $data[] = [
            'nombre_tabla'    => 'producto',
            'nombre_id_tabla' => 'idproducto',
            'id_tabla'        => $value17['idproducto'],
            'modulo'          => 'PRODUCTO',
            'nombre_archivo'  => '<b>Nombre: </b>'.$value17['producto'].'<br/> '.
                                 '<b>Cateforia: </b>'.$value17['categoria'].' | <b>Marca: </b>'.$value17['marca'],
            'descripcion'     => $value17['descripcion'],
            'created_at'      => $value17['created_at'],
            'updated_at'      => $value17['updated_at'],
            'list_archivo'     => '<b>Nombre: </b>'.$value17['producto'],
          ];
      }
    }


    $sql_18 = "SELECT * FROM sunat_c01_tipo_comprobante WHERE estado = '0' AND estado_delete = '1';";
    $tipo_comprobante = ejecutarConsultaArray($sql_18);  
    if ($tipo_comprobante['status'] == false) { return $tipo_comprobante; }

    if (!empty($tipo_comprobante['data'])) {
      foreach ($tipo_comprobante['data'] as $value18) {
          $data[] = [
            'nombre_tabla'    => 'sunat_c01_tipo_comprobante',
            'nombre_id_tabla' => 'idtipo_comprobante',
            'id_tabla'        => $value18['idtipo_comprobante'],
            'modulo'          => 'SUNAT',
            'nombre_archivo'  => $value18['nombre'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value18['created_at'],
            'updated_at'      => $value18['updated_at'],
            'list_archivo'     => $value18['nombre'],
          ];
      }
    }


    $sql_19 = "SELECT * FROM sunat_c06_doc_identidad WHERE estado = '0' AND estado_delete = '1';";
    $tipo_doc_identidad = ejecutarConsultaArray($sql_19);  
    if ($tipo_doc_identidad['status'] == false) { return $tipo_doc_identidad; }

    if (!empty($tipo_doc_identidad['data'])) {
      foreach ($tipo_doc_identidad['data'] as $value19) {
          $data[] = [
            'nombre_tabla'    => 'sunat_c06_doc_identidad',
            'nombre_id_tabla' => 'idsunat_c06_doc_identidad',
            'id_tabla'        => $value19['idsunat_c06_doc_identidad'],
            'modulo'          => 'SUNAT',
            'nombre_archivo'  => $value19['nombre'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value19['created_at'],
            'updated_at'      => $value19['updated_at'],
            'list_archivo'     => $value19['nombre'],
          ];
      }
    }

    /*$sql_20 = "SELECT * FROM sunat_tipo_tributo WHERE estado = '0' AND estado_delete = '1';";
    $tipo_tributo = ejecutarConsultaArray($sql_20);  
    if ($tipo_tributo['status'] == false) { return $tipo_tributo; }

    if (!empty($tipo_tributo['data'])) {
      foreach ($tipo_tributo['data'] as $value20) {
          $data[] = [
            'nombre_tabla'    => 'sunat_tipo_tributo',
            'nombre_id_tabla' => 'idsunat_tipo_tributo',
            'id_tabla'        => $value20['idsunat_tipo_tributo'],
            'modulo'          => 'SUNAT',
            'nombre_archivo'  => $value20['nombre'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value20['created_at'],
            'updated_at'      => $value20['updated_at'],
            'list_archivo'     => $value20['nombre'],
          ];
      }
    }*/


    $sql_21 = "SELECT suc.idsunat_usuario_comprobante, p.nombre_razonsocial, p.apellidos_nombrecomercial, scc.abreviatura AS tp_comprobante, suc.created_at, suc.updated_at
    FROM sunat_usuario_comprobante AS suc
    INNER JOIN usuario AS u ON u.idusuario = suc.idusuario
    INNER JOIN persona AS p ON p.idpersona = u.idpersona
    INNER JOIN sunat_c01_tipo_comprobante AS scc ON scc.idtipo_comprobante = suc.idtipo_comprobante
    WHERE suc.estado = '0' AND suc.estado_delete = '1';";
    $user_comprob = ejecutarConsultaArray($sql_21);  
    if ($user_comprob['status'] == false) { return $user_comprob; }

    if (!empty($user_comprob['data'])) {
      foreach ($user_comprob['data'] as $value21) {
          $data[] = [
            'nombre_tabla'    => 'sunat_usuario_comprobante',
            'nombre_id_tabla' => 'idsunat_usuario_comprobante',
            'id_tabla'        => $value21['idsunat_usuario_comprobante'],
            'modulo'          => 'SUNAT',
            'nombre_archivo'  => '<b>Usuario: </b>'.$value21['nombre_razonsocial'].' '.$value21['apellidos_nombrecomercial'] .'<br/>'.
                                 '<b>Tipo Comb.: </b>'.$value21['tp_comprobante'],
            'descripcion'     => '- - - - -',
            'created_at'      => $value21['created_at'],
            'updated_at'      => $value21['updated_at'],
            'list_archivo'     => '<b>Usuario: </b>'.$value21['nombre_razonsocial'].' '.$value21['apellidos_nombrecomercial'] .' -> <b>Tipo Comb.: </b>'.$value21['tp_comprobante'],
          ];
      }
    }


    $sql_22 = "SELECT * FROM tipo_persona WHERE estado = '0' AND estado_delete = '1';";
    $tipo_persona = ejecutarConsultaArray($sql_22);  
    if ($tipo_persona['status'] == false) { return $tipo_persona; }

    if (!empty($tipo_persona['data'])) {
      foreach ($tipo_persona['data'] as $value22) {
          $data[] = [
            'nombre_tabla'    => 'tipo_persona',
            'nombre_id_tabla' => 'idtipo_persona',
            'id_tabla'        => $value22['idtipo_persona'],
            'modulo'          => 'GENERAL',
            'nombre_archivo'  => $value22['nombre'],
            'descripcion'     => $value22['descripcion'],
            'created_at'      => $value22['created_at'],
            'updated_at'      => $value22['updated_at'],
            'list_archivo'     => $value22['nombre'],
          ];
      }
    }


    // $sql_23 = "SELECT * FROM ubigeo_departamento WHERE estado = '0' AND estado_delete = '1';";
    // $departamento = ejecutarConsultaArray($sql_23);  
    // if ($departamento['status'] == false) { return $departamento; }

    // if (!empty($departamento['data'])) {
    //   foreach ($departamento['data'] as $value23) {
    //       $data[] = [
    //         'nombre_tabla'    => 'ubigeo_departamento',
    //         'nombre_id_tabla' => 'idubigeo_departamento',
    //         'id_tabla'        => $value23['idubigeo_departamento'],
    //         'modulo'          => 'DEPARTAMENTO',
    //         'nombre_archivo'  => $value23['nombre'],
    //         'descripcion'     => $value23['region'],
    //         'created_at'      => $value23['created_at'],
    //         'updated_at'      => $value23['updated_at'],
    //         'list_archivo'     => $value23['nombre'],
    //       ];
    //   }
    // }


    // $sql_24 = "SELECT * FROM ubigeo_distrito WHERE estado = '0' AND estado_delete = '1';";
    // $distrito = ejecutarConsultaArray($sql_24);  
    // if ($distrito['status'] == false) { return $distrito; }

    // if (!empty($distrito['data'])) {
    //   foreach ($distrito['data'] as $value24) {
    //       $data[] = [
    //         'nombre_tabla'    => 'ubigeo_distrito',
    //         'nombre_id_tabla' => 'idubigeo_distrito',
    //         'id_tabla'        => $value24['idubigeo_distrito'],
    //         'modulo'          => 'DISTRITO',
    //         'nombre_archivo'  => $value24['nombre'],
    //         'descripcion'     => 'Ubigeo Reniec: '.$value24['ubigeo_reniec'],
    //         'created_at'      => $value24['created_at'],
    //         'updated_at'      => $value24['updated_at'],
    //         'list_archivo'     => $value24['nombre'],
    //       ];
    //   }
    // }


    // $sql_25 = "SELECT * FROM ubigeo_provincia WHERE estado = '0' AND estado_delete = '1';";
    // $provincia = ejecutarConsultaArray($sql_25);  
    // if ($provincia['status'] == false) { return $provincia; }

    // if (!empty($provincia['data'])) {
    //   foreach ($provincia['data'] as $value25) {
    //       $data[] = [
    //         'nombre_tabla'    => 'ubigeo_provincia',
    //         'nombre_id_tabla' => 'idubigeo_provincia',
    //         'id_tabla'        => $value25['idubigeo_provincia'],
    //         'modulo'          => 'PROVINCIA',
    //         'nombre_archivo'  => $value25['nombre'],
    //         'descripcion'     => '- - - - -',
    //         'created_at'      => $value25['created_at'],
    //         'updated_at'      => $value25['updated_at'],
    //         'list_archivo'     => $value25['nombre'],
    //       ];
    //   }
    // }
  

    return ['status' => true, 'message' =>'todo okey', 'data'=>$data];
  }

  public function recuperar($nombre_tabla, $nombre_id_tabla, $id_tabla){

    $sql = "UPDATE $nombre_tabla SET estado = 1 WHERE $nombre_id_tabla ='$id_tabla'";
    $recuperar= ejecutarConsulta($sql); if ($recuperar['status'] == false) {  return $recuperar; }
		
		//add registro en nuestra bitacora
    // $sql_d ="Archivo recuperado desde papelera";
		// $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (1,'$nombre_tabla','$id_tabla','$sql_d','$this->id_usr_sesion')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $recuperar;
  }

  public function eliminar_permanente($nombre_tabla, $nombre_id_tabla, $id_tabla){

    $sql = "UPDATE $nombre_tabla SET estado_delete = 0 WHERE $nombre_id_tabla ='$id_tabla'";
		$eliminar =  ejecutarConsulta($sql); if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora
    // $sql_d ="Archivo eliminado desde papelera";
		// $sql = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (4,'$nombre_tabla','$id_tabla','$sql_d', '$this->id_usr_sesion')";
		// $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
  }


}

?>