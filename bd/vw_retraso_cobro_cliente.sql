
SELECT
  pco.idpersona_cliente_v2, pco.idpersona, pco.idpersona_cliente, pco.tipo_persona_sunat, pco.cliente_nombre_completo,    
  ROUND( COALESCE( ( co.vc_cuota_total - co.vc_cuota_total_cobrado ), 0 ),  2 ) AS avance,
  COALESCE(co.vc_cuota_total_cobrado, 0) AS vc_cuota_total_cobrado,
  COALESCE(co.vc_cuota_total, 0) AS vc_cuota_total, 
  CASE 
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) = 0 THEN 'SIN DEUDA' 
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) > 0 THEN 'DEUDA'     
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) < 0 THEN 'ADELANTO' ELSE '-'
	END AS estado_deuda,
  CASE 
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) = 0 THEN 'PAGADO' 
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) = co.vc_cuota_total THEN 'SIN PAGOS' 
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) > 0 THEN 'PARCIAL'     
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) < 0 THEN 'ADELANTO' ELSE '-'
	END AS categoria_deuda,
	CASE 
    WHEN( co.vc_cuota_total - co.vc_cuota_total_cobrado ) < 0 THEN ABS( ( co.vc_cuota_total - co.vc_cuota_total_cobrado )) 
    ELSE( co.vc_cuota_total - co.vc_cuota_total_cobrado )
	END AS avance_v2,
	pco.tipo_documento_abrev_nombre,	pco.numero_documento,  pco.idcentro_poblado, pco.centro_poblado,
  pco.estado_pc, pco.estado_delete_pc, pco.estado_p, pco.estado_delete_p
FROM
(
  SELECT LPAD (pc.idpersona_cliente, 5, '0') AS idpersona_cliente_v2, p.idpersona, p.tipo_persona_sunat, p.nombre_razonsocial, p.apellidos_nombrecomercial, 
  p.numero_documento,  p.estado AS estado_p, p.estado_delete AS estado_delete_p,  pc.estado AS estado_pc, pc.estado_delete AS estado_delete_pc, 
  pc.idpersona_cliente,  sc06.abreviatura AS tipo_documento_abrev_nombre,
  CASE
    WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT ( p.nombre_razonsocial,' ', p.apellidos_nombrecomercial )
    WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial
    ELSE '-'
  END AS cliente_nombre_completo, pc.idcentro_poblado, cp.nombre as centro_poblado
  FROM  persona_cliente AS pc
  INNER JOIN persona AS p ON p.idpersona = pc.idpersona   
  INNER JOIN sunat_c06_doc_identidad AS sc06 ON p.tipo_documento = sc06.code_sunat
  LEFT JOIN centro_poblado as cp on cp.idcentro_poblado = pc.idcentro_poblado
  ORDER BY pc.idpersona_cliente
) AS pco
LEFT JOIN(
  SELECT  pc.idpersona_cliente, SUM(v.vc_cantidad_total) as vc_cuota_total, SUM(v.vc_cantidad_pagada) AS vc_cuota_total_cobrado
  FROM venta AS v  
  INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
  WHERE v.venta_cuotas = 'SI' AND v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN('01', '03', '12')
  GROUP BY pc.idpersona_cliente
  ORDER BY SUM(v.vc_cantidad_pagada) DESC
) AS co ON pco.idpersona_cliente = co.idpersona_cliente
ORDER BY avance DESC;

