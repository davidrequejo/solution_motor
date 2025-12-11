
SELECT 
-- Datos venta cabecera
v.idventa, LPAD(v.idventa, 5, '0') AS idventa_v2, v.idperiodo_contable,v.iddocumento_relacionado,v.crear_enviar_sunat,v.idsunat_c01,v.tipo_comprobante,v.serie_comprobante,
v.numero_comprobante,v.fecha_emision,v.name_day,v.name_month,v.name_year,v.impuesto,v.venta_subtotal,v.venta_descuento,v.venta_igv,
v.venta_total,v.venta_cuotas,
v.total_recibido,v.total_vuelto,v.usar_anticipo,v.ua_monto_disponible,v.ua_monto_usado,v.nc_motivo_nota,v.nc_tipo_comprobante,v.nc_serie_y_numero,
v.cot_tiempo_entrega,v.cot_validez,v.cot_estado,v.sunat_estado,v.sunat_observacion,v.sunat_code,v.sunat_mensaje,v.sunat_hash,v.sunat_error,
v.observacion_documento,v.estado as estado_v,v.estado_delete as estado_delete_v,v.created_at as created_at_v,v.updated_at as updated_at_v,
v.user_trash as user_trash_v,v.user_delete as user_delete_v,v.user_created as user_created_v,v.user_updated as user_updated_v,
 CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
 DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format,
 CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tipo_comprobante_v2,
 DATE_FORMAT(v.fecha_emision, '%d, %b %Y - %h:%i %p') as fecha_emision_format_v2,
-- Datos venta detalle  
vd.idventa_detalle, vd.idproducto_presentacion, vd.pr_nombre, vd.pr_marca, vd.pr_categoria, vd.v_tipo_comprobante, vd.v_fecha_emision, vd.cantidad_presentacion, vd.cantidad_venta, vd.cantidad_total, vd.precio_compra, 
vd.precio_venta, vd.precio_venta_descuento, vd.descuento, vd.descuento_porcentaje, vd.subtotal, vd.subtotal_no_descuento, vd.um_nombre, vd.um_abreviatura, vd.precio_por_mayor,
-- Datos producto
vw_pp.idproducto, vw_pp.codigo, vw_pp.codigo_alterno, vw_pp.nombre_producto, vw_pp.imagen,
-- Datos Cliente
pc.idpersona_cliente, p.idpersona, p.tipo_persona_sunat,
p.nombre_razonsocial, p.apellidos_nombrecomercial, p.tipo_documento, 
p.numero_documento, p.foto_perfil, 
CASE 
  WHEN p.tipo_persona_sunat = 'NATURAL' THEN 
    CASE 
      WHEN LENGTH(  CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial)  ) <= 27 THEN  CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
      ELSE CONCAT( LEFT(CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial ), 27) , '...')
    END         
  WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN 
    CASE 
      WHEN LENGTH(  p.nombre_razonsocial  ) <= 27 THEN  p.nombre_razonsocial 
      ELSE CONCAT(LEFT( p.nombre_razonsocial, 27) , '...')
    END
  ELSE '-'
END AS cliente_nombre_recortado, 
CASE 
  WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
  WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
  ELSE '-'
END AS cliente_nombre_completo, pc.idcentro_poblado, cp.nombre as centro_poblado,
-- Tipo de comprobante
tc.abreviatura as tipo_comprobante_v1, 
-- Tipo de documento cliente
sdi.abreviatura as tipo_documento_abreviatura, 
-- Usuario en atencion
u.idusuario, u.idpersona_trabajador, u.user_created_v2, u.user_en_atencion, u.user_en_atencion_nombre
FROM venta AS v
INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
INNER JOIN vw_producto_presentacion as vw_pp on vw_pp.idproducto_presentacion = vd.idproducto_presentacion
INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
LEFT JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado
INNER JOIN persona AS p ON p.idpersona = pc.idpersona
INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
LEFT JOIN ( 
  select u.idusuario, pt.idpersona_trabajador, LPAD(u.idusuario, 3, '0') AS user_created_v2, CONCAT( (SUBSTRING_INDEX(pu.nombre_razonsocial, ' ', 1)),' ', (SUBSTRING_INDEX(pu.apellidos_nombrecomercial, ' ', 1))) AS user_en_atencion, pu.nombre_razonsocial AS user_en_atencion_nombre
  from  usuario as u
  inner join  persona as pu ON pu.idpersona = u.idpersona
  inner join persona_trabajador as pt on pt.idpersona = pu.idpersona
)  as u ON u.idusuario = v.user_created
ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC;