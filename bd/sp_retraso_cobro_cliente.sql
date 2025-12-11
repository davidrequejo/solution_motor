CREATE PROCEDURE `sp_retraso_cobro_cliente` ( 
IN idtrabajador INT, 
IN anio_pago INT, 
IN pc_estado VARCHAR(1), 
IN pc_estado_delete VARCHAR(1)
)
BEGIN
  SELECT
    pco.idpersona_cliente_v2, pco.idpersona, pco.idpersona_cliente, pco.cliente_nombre_completo, pco.dia_cancelacion,
    CONCAT( YEAR(pco.primera_venta), '-',  UPPER( LEFT(MONTHNAME(pco.primera_venta), 1) ), SUBSTR(MONTHNAME(pco.primera_venta), 2) ) AS mes_inicio,
    ROUND( COALESCE( ( pco.cant_total_mes - co.cant_cobrado ), 0 ),  2 ) AS avance,
    COALESCE(co.cant_cobrado, 0) AS cant_cobrado,
    pco.cant_total_mes AS cant_total,
    CASE 
      WHEN( pco.cant_total_mes - co.cant_cobrado ) = 0 THEN 'SIN DEUDA' 
      WHEN( pco.cant_total_mes - co.cant_cobrado ) > 0 THEN 'DEUDA' 
      WHEN( pco.cant_total_mes - co.cant_cobrado ) < 0 THEN 'ADELANTO' ELSE '-'
    END AS estado_deuda,
    CASE 
      WHEN( pco.cant_total_mes - co.cant_cobrado ) < 0 THEN ABS( ( pco.cant_total_mes - co.cant_cobrado )) 
      ELSE( pco.cant_total_mes - co.cant_cobrado )
    END AS avance_v2,
    pco.tipo_documento_abrev_nombre,
    pco.numero_documento,
    pco.idpersona_trabajador,
    pco.trabajador_nombre,
    pco.estado_pc, pco.estado_delete_pc, pco.estado_p, pco.estado_delete_p
  FROM
  (
    SELECT
      MIN(ven.periodo_pago_format) AS primera_venta,
      CASE
        WHEN per.fecha_cancelacion > CURDATE() THEN DATE_FORMAT(per.fecha_cancelacion, '%d/%m/%Y')
        ELSE CONCAT ( DATE_FORMAT(per.fecha_cancelacion, '%d'),' de cada mes' )
      END AS dia_cancelacion,
      COALESCE( 
        CASE
          WHEN per.fecha_cancelacion > CURDATE() THEN TIMESTAMPDIFF (MONTH, MIN(ven.periodo_pago_format), CURDATE())
          ELSE CASE
            WHEN DATE_FORMAT(CURDATE(), '%d') > DATE_FORMAT(per.fecha_cancelacion, '%d') THEN TIMESTAMPDIFF (MONTH, MIN(ven.periodo_pago_format), CURDATE()) + 1
            ELSE TIMESTAMPDIFF (MONTH, MIN(ven.periodo_pago_format), CURDATE())
          END
        END, 
      0 ) AS cant_total_mes,
      per.idpersona, per.idpersona_cliente_v2, per.idpersona_cliente, per.cliente_nombre_completo, per.numero_documento, per.estado_pc, per.estado_delete_pc, per.estado_p,
      per.estado_delete_p, per.tipo_documento_abrev_nombre, per.idpersona_trabajador, per.trabajador_nombre
    FROM
    (
      SELECT LPAD (pc.idpersona_cliente, 5, '0') AS idpersona_cliente_v2, p.idpersona, p.tipo_persona_sunat, p.nombre_razonsocial, p.apellidos_nombrecomercial, p.numero_documento, p.estado AS estado_p, 
      p.estado_delete AS estado_delete_p,  pc.estado AS estado_pc, pc.estado_delete AS estado_delete_pc, pc.fecha_cancelacion, pc.idpersona_cliente, 
      pt.idpersona_trabajador, p1.nombre_razonsocial AS trabajador_nombre,  sc06.abreviatura AS tipo_documento_abrev_nombre,
      CASE
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT ( p.nombre_razonsocial,' ', p.apellidos_nombrecomercial )
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial
        ELSE '-'
      END AS cliente_nombre_completo
      FROM  persona_cliente AS pc
      INNER JOIN persona AS p ON p.idpersona = pc.idpersona
      INNER JOIN persona_trabajador AS pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
      INNER JOIN persona AS p1 ON pt.idpersona = p1.idpersona
      INNER JOIN sunat_c06_doc_identidad AS sc06 ON p.tipo_documento = sc06.code_sunat
      where pc.idpersona_trabajador = COALESCE(idtrabajador, year(pc.idpersona_trabajador)) 
    ) as per
    LEFT JOIN ( 
      SELECT vd.periodo_pago_format, v.idpersona_cliente FROM venta v 
      INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
      WHERE  vd.es_cobro = 'SI' AND v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN ('01', '03', '12') 
      -- Filtros
      and year(vd.periodo_pago_format) = COALESCE(anio_pago, year(vd.periodo_pago_format)) 
    ) AS ven ON ven.idpersona_cliente = per.idpersona_cliente 
    GROUP BY per.idpersona, per.idpersona_cliente
    ORDER BY per.idpersona_cliente, cant_total_mes
  ) AS pco
  LEFT JOIN(
    SELECT  pc.idpersona_cliente, COUNT(v.idventa) AS cant_cobrado
    FROM venta AS v
    INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
    INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
    WHERE vd.es_cobro = 'SI' AND v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN('01', '03', '12')
    -- Filtros
    and pc.idpersona_trabajador = COALESCE(idtrabajador, year(pc.idpersona_trabajador)) and year(vd.periodo_pago_format) = COALESCE(anio_pago, year(vd.periodo_pago_format))  
    GROUP BY pc.idpersona_cliente
    ORDER BY COUNT(v.idventa) DESC
  ) AS co ON pco.idpersona_cliente = co.idpersona_cliente
  where pco.estado_pc = COALESCE(pc_estado, pco.estado_pc) and pco.estado_delete_pc = COALESCE(pc_estado_delete, pco.estado_delete_pc)
  ORDER BY  avance DESC, pco.cliente_nombre_completo ;
END