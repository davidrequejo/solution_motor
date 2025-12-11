
SELECT 
-- Datos venta cabecera
v.idventa, LPAD(v.idventa, 5, '0') AS idventa_v2, v.idperiodo_contable,v.iddocumento_relacionado,v.crear_enviar_sunat,v.idsunat_c01,v.tipo_comprobante,v.serie_comprobante,v.numero_comprobante, 
CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante,v.fecha_emision,v.name_day,v.name_month,v.name_year,v.impuesto,v.venta_subtotal,v.venta_descuento,v.venta_igv,
v.venta_total,v.venta_cuotas, v.vc_cantidad_total, v.vc_cantidad_pagada, v.vc_estado,v.total_recibido,v.total_vuelto,v.usar_anticipo,v.ua_monto_disponible,v.ua_monto_usado,v.nc_motivo_nota,
v.nc_tipo_comprobante,v.nc_serie_y_numero,v.cot_tiempo_entrega,v.cot_validez,v.cot_estado,v.sunat_estado,v.sunat_observacion,v.sunat_code,v.sunat_mensaje,v.sunat_hash,v.sunat_error,v.observacion_documento,
v.estado as v_estado,v.estado_delete as v_estado_delete,v.created_at as v_created_at,v.updated_at as v_updated_at,
v.user_trash as v_user_trash,v.user_delete as v_user_delete,v.user_created as v_user_created,v.user_updated as v_user_updated,
CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_amd,
DATE_FORMAT(v.fecha_emision, '%d/%m/%Y') AS fecha_emision_dma,
DATE_FORMAT(v.fecha_emision, '%h:%i:%s %p') AS fecha_emision_hora12, 
DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_dma_h12, 
DATE_FORMAT(v.fecha_emision, '%d, %b %Y - %h:%i %p') as fecha_emision_dma_h12_v2,
-- Tipo de comprobante
tc.abreviatura as nombre_comprobante,
CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS nombre_comprobante_v2, 
-- Datos venta detalle  
-- Datos Cliente
pc.idpersona_cliente, p.idpersona, p.tipo_persona_sunat,
p.nombre_razonsocial, p.apellidos_nombrecomercial, p.tipo_documento, 
p.numero_documento, case when p.foto_perfil is null then 'no-perfil.jpg' when p.foto_perfil = '' then 'no-perfil.jpg' else p.foto_perfil end as foto_perfil, p.direccion, p.celular, p.correo,
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
-- Tipo de documento cliente
sdi.abreviatura as tipo_documento_abreviatura, 
-- Documento relacionado
rel.*,
-- metodo de pago
vmp.*,
-- Utilidad
utl.*,
-- Usuario en atencion
uc.*
FROM venta AS v
INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
LEFT JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado
INNER JOIN persona AS p ON p.idpersona = pc.idpersona
INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
-- Usuario en atencion
LEFT JOIN ( 
  select u.idusuario as uc_idusuario, LPAD(u.idusuario, 3, '0') AS uc_idusuario_v2,   
  pu.nombre_razonsocial AS uc_nombre_razonsocial, 
  case when pu.foto_perfil is null then 'no-perfil.jpg' when pu.foto_perfil = '' then 'no-perfil.jpg' else pu.foto_perfil end as uc_foto_perfil
  from  usuario as u
  inner join  persona as pu ON pu.idpersona = u.idpersona
  inner join persona_trabajador as pt on pt.idpersona = pu.idpersona
)  as uc ON uc.uc_idusuario = v.user_created
-- Documento Relacionado
LEFT JOIN ( 
  select v.idventa as rel_idventa , v.tipo_comprobante as rel_tipo_comprobante, CONCAT(v.serie_comprobante,'-', v.numero_comprobante) as rel_serie_numero, 
  p.numero_documento as rel_cli_numero_documento, p.nombre_razonsocial as rel_cli_nombre_razonsocial
  from venta as v 
  INNER JOIN persona_cliente as pc on pc.idpersona_cliente = v.idpersona_cliente
  INNER JOIN persona as p on p.idpersona = pc.idpersona 
) as rel on rel.rel_idventa = v.iddocumento_relacionado
-- Metodo de pago agrupado
LEFT JOIN ( 
  select v.idventa as vmp_idventa, COALESCE(count(vmp.idventa_metodo_pago), 0) as vmp_cantidad, GROUP_CONCAT(vmp.metodo_pago ORDER BY vmp.metodo_pago SEPARATOR ', ') AS vmp_metodos_pago_agrupado 
  from venta_metodo_pago as vmp inner join venta as v on v.idventa = vmp.idventa group by v.idventa
) AS vmp on vmp.vmp_idventa = v.idventa
LEFT JOIN (
  SELECT idventa as utl_idventa, SUM(subtotal) as utl_subtotal, SUM(subtotal_compra) as utl_subtotal_compra, ( SUM(subtotal) - SUM(subtotal_compra) )  AS utl_utilidad
  FROM venta_detalle 
  GROUP BY idventa
) as utl on utl.utl_idventa = v.idventa
ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC
