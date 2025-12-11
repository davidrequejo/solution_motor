
SELECT 
-- Datos venta cabecera
v.idventa, LPAD(v.idventa, 5, '0') AS idventa_v2, v.idperiodo_contable,v.crear_enviar_sunat,v.idsunat_c01,v.tipo_comprobante,
v.serie_comprobante,v.numero_comprobante, CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante,
v.fecha_emision,v.name_day,v.name_month,v.name_year,v.impuesto,v.venta_subtotal,v.venta_descuento,v.venta_igv,
v.venta_total,v.venta_cuotas, v.vc_cantidad_total, v.vc_cantidad_pagada, v.vc_estado,
v.total_recibido,v.total_vuelto,v.usar_anticipo,v.ua_monto_disponible,v.ua_monto_usado,v.nc_motivo_nota,v.nc_tipo_comprobante,v.nc_serie_y_numero,
v.cot_tiempo_entrega,v.cot_validez,v.cot_estado,v.sunat_estado,v.sunat_observacion,v.sunat_code,v.sunat_mensaje,v.sunat_hash,v.sunat_error,
v.observacion_documento,v.estado as estado_v,v.estado_delete as estado_delete_v,v.created_at as created_at_v,v.updated_at as updated_at_v,
v.user_trash as user_trash_v,v.user_delete as user_delete_v,v.user_created as user_created_v,v.user_updated as user_updated_v,
CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format,
DATE_FORMAT(v.fecha_emision, '%d/%m/%Y') AS fecha_emision_dmy,
DATE_FORMAT(v.fecha_emision, '%h:%i:%s %p') AS fecha_emision_hora12, 
DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_format_dmy_h12, 
DATE_FORMAT(v.fecha_emision, '%d, %b %Y - %h:%i %p') as fecha_emision_format_v2,
CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tipo_comprobante_v2, 
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
-- Tipo de comprobante
tc.abreviatura as nombre_comprobante,
-- Tipo de documento cliente
sdi.abreviatura as tipo_documento_abreviatura, 
-- nota credito
IFNULL(cnc.nombre, '') as nc_nombre_motivo,
-- Guia remision
v.gr_idventa_asociada,  v.gr_peso_total, v.gr_peso_total_um, v.gr_idconductor, v.gr_placa, REPLACE(v.gr_placa, '-', '') as gr_placa_no_guion, v.gr_numero_licencia, v.gr_partida_direccion, v.gr_partida_distrito, v.gr_partida_ubigeo, 
v.gr_llegada_direccion, v.gr_llegada_distrito, v.gr_llegada_ubigeo, 
-- Guia remision - doc asociado
vg.gr_da_tipo_comprobante , vg.gr_da_serie_numero, vg.gr_da_numero_documento,
-- Modalidad transporte
v.gr_cod_modalidad_traslado, sc18.nombre as gr_modalidad_traslado,
-- Motivo transporte 
v.gr_cod_motivo_traslado, sc20.nombre as  gr_motivo_traslado,
-- Chofer guia
CASE 
  WHEN chofer.tipo_persona_sunat = 'NATURAL' THEN CONCAT(chofer.nombre_razonsocial, ' ', chofer.apellidos_nombrecomercial) 
  WHEN chofer.tipo_persona_sunat = 'JURÍDICA' THEN chofer.nombre_razonsocial 
  ELSE '-'
END AS gr_chofer_nombre, chofer.nombre_razonsocial as gr_chofer_nombre_razonsocial, chofer.apellidos_nombrecomercial as gr_chofer_apellidos_nombrecomercial, 
chofer.tipo_documento as gr_chofer_tipo_documento, case chofer.tipo_documento when '1' then 'DNI' when '6' then 'RUC' else '-' end as gr_chofer_nombre_tipo_documento,
chofer.numero_documento as gr_chofer_numero_documento, 
-- Metodo de pago
vmp.metodos_pago_agrupado, vmp.cantidad_mp,
-- Documento Relacionado
v.iddocumento_relacionado,
CONCAT(vr.serie_comprobante, '-', vr.numero_comprobante) as documento_relacionado,
-- Usuario en atencion
u.idusuario, u.idpersona_trabajador, u.user_created_v2, u.user_en_atencion, u.user_en_atencion_nombre, u.user_en_atencion_foto
FROM venta AS v
INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
LEFT JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado
INNER JOIN persona AS p ON p.idpersona = pc.idpersona
INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
LEFT JOIN sunat_c09_codigo_nota_credito AS cnc ON cnc.codigo = v.nc_motivo_nota
LEFT JOIN sunat_c18_codigo_modalidad_transporte AS sc18 on sc18.codigo = v.gr_cod_modalidad_traslado
LEFT JOIN sunat_c20_codigo_motivo_traslado AS sc20 on sc20.codigo = v.gr_cod_motivo_traslado 
-- Usuario en atencion
LEFT JOIN ( 
  select u.idusuario, pt.idpersona_trabajador, LPAD(u.idusuario, 3, '0') AS user_created_v2, 
  CONCAT( (SUBSTRING_INDEX(pu.nombre_razonsocial, ' ', 1)),' ', (SUBSTRING_INDEX(pu.apellidos_nombrecomercial, ' ', 1))) AS user_en_atencion, 
  pu.nombre_razonsocial AS user_en_atencion_nombre, case when pu.foto_perfil is null then 'no-perfil.jpg' when pu.foto_perfil = '' then 'no-perfil.jpg' else pu.foto_perfil end as user_en_atencion_foto
  from  usuario as u
  inner join  persona as pu ON pu.idpersona = u.idpersona
  inner join persona_trabajador as pt on pt.idpersona = pu.idpersona
)  as u ON u.idusuario = v.user_created
-- Chofer guia
LEFT JOIN persona as chofer ON chofer.idpersona = v.gr_idconductor
-- guia remision - doc asociado
LEFT JOIN ( 
  select v.idventa as gr_da_idventa , v.tipo_comprobante as gr_da_tipo_comprobante, CONCAT(v.serie_comprobante,'-', v.numero_comprobante) as gr_da_serie_numero, p.numero_documento as gr_da_numero_documento 
  from venta as v INNER JOIN persona_cliente as pc on pc.idpersona_cliente = v.idpersona_cliente
  INNER JOIN persona as p on p.idpersona = pc.idpersona 
) as vg on vg.gr_da_idventa = v.gr_idventa_asociada
-- Metodo de pago
LEFT JOIN ( 
  select v.idventa, COALESCE(count(vmp.idventa_metodo_pago), 0) as cantidad_mp, GROUP_CONCAT(vmp.metodo_pago ORDER BY vmp.metodo_pago SEPARATOR ', ') AS metodos_pago_agrupado 
  from venta_metodo_pago as vmp inner join venta as v on v.idventa = vmp.idventa group by v.idventa
) AS vmp on vmp.idventa = v.idventa
-- Documento Relacionado
LEFT JOIN venta as vr on vr.idventa = v.iddocumento_relacionado

ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC;