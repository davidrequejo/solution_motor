

SELECT
	-- ::::::::::::::: DATOS CLIENTE ::::::::::::::: 
	per.idpersona_cliente_v2,	per.idpersona_cliente, per.idcentro_poblado, per.centro_poblado, per.nota, per.estado_pc, per.estado_delete_pc, per.cliente_nombre_completo,		
	-- ::::::::::::::: DATOS PERSONA CLIENTE ::::::::::::::: 
	per.idpersona, per.idtipo_persona, per.idbancos, per.idcargo_trabajador, per.tipo_persona_sunat, per.nombre_razonsocial, per.apellidos_nombrecomercial, per.tipo_documento, 
	per.numero_documento, per.fecha_nacimiento, per.celular, per.direccion, per.departamento, per.provincia, per.distrito, per.cod_ubigeo, per.correo, per.cuenta_bancaria, 
	per.cci, per.titular_cuenta, per.foto_perfil, per.estado_p, per.estado_delete_p,	
	case when per.foto_perfil is null then LEFT(per.nombre_razonsocial, 1) when per.foto_perfil = '' then LEFT(per.nombre_razonsocial, 1) else null end cliente_primera_letra,
	case when per.foto_perfil is null then 'NO' when per.foto_perfil = '' then 'NO' else 'SI' end cliente_tiene_pefil,
	per.landing_user, per.landing_descripcion, per.landing_puntuacion, per.landing_fecha, per.landing_estado,	
	-- ::::::::::::::: DATOS SUNAT ::::::::::::::: 
	per.tipo_documento_abrev_nombre
FROM 
	(
		SELECT 
			LPAD (pc.idpersona_cliente, 5, '0') as idpersona_cliente_v2,
			pc.idpersona_cliente,				
			pc.idcentro_poblado, cp.nombre as centro_poblado,
			pc.nota,			
			pc.landing_user, pc.landing_descripcion, pc.landing_puntuacion, pc.landing_fecha, pc.landing_estado, 
			pc.estado as estado_pc,	pc.estado_delete as estado_delete_pc,
			CASE
				WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT (p.nombre_razonsocial,' ',p.apellidos_nombrecomercial	)
				WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial
				ELSE '-'
			END AS cliente_nombre_completo,
			p.idpersona, p.idtipo_persona, p.idbancos, p.idcargo_trabajador, p.tipo_persona_sunat, p.nombre_razonsocial, p.apellidos_nombrecomercial, 
			p.tipo_documento, p.numero_documento, p.fecha_nacimiento, p.celular, p.direccion, p.departamento, p.provincia, p.distrito, p.cod_ubigeo, p.correo, 
			p.cuenta_bancaria, p.cci, p.titular_cuenta, p.foto_perfil, p.estado as estado_p, p.estado_delete as estado_delete_p,		
			sc06.abreviatura as tipo_documento_abrev_nombre
		FROM persona_cliente as pc
		INNER JOIN persona AS p on pc.idpersona = p.idpersona		
		INNER JOIN sunat_c06_doc_identidad as sc06 on p.tipo_documento = sc06.code_sunat
		LEFT JOIN centro_poblado as cp on pc.idcentro_poblado = cp.idcentro_poblado 
	) AS per
	LEFT JOIN ( 
		SELECT v.idpersona_cliente FROM venta v 
		INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
		WHERE  v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN ('01', '03', '12')
		GROUP BY v.idpersona_cliente
  ) AS ven ON ven.idpersona_cliente = per.idpersona_cliente 
ORDER BY	per.idpersona_cliente DESC