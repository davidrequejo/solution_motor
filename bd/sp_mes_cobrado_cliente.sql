CREATE PROCEDURE `sp_mes_cobrado_cliente` (
  IN idcliente INT
)
BEGIN

SELECT CASE WHEN lvd.idventa IS NULL THEN  'DEUDA' ELSE  'NO DEUDA' END as estado_pagado, 
  mes_c.*, fn_capitalize_texto(mes_c.name_month) as nombre_mes_capitalize,  fn_capitalize_texto(SUBSTRING( mes_c.name_month , 1, 3)) as nombre_mes_recortado, lvd.idventa, lvd.idventa_detalle, 
  CASE WHEN lvd.fecha_emision_format IS NOT NULL THEN lvd.fecha_emision_format ELSE  '' END as fecha_emision_format, lvd.subtotal, lvd.tipo, lvd.pr_nombre, lvd.tipo_comprobante, lvd.serie_comprobante, lvd.numero_comprobante, lvd.tipo_comprobante_v2,
  CASE WHEN lvd.tecnico_cobro IS NOT NULL THEN  lvd.tecnico_cobro ELSE  'NO PAGO' END as tecnico_cobro, lvd.foto_perfil_tecnico_cobro
  FROM ( 
    SELECT mc.* 
    FROM mes_calendario AS mc
    WHERE year_month_date 
    BETWEEN( 
      SELECT CASE WHEN vd.fecha_minima_cobro IS NOT NULL THEN vd.fecha_minima_cobro WHEN pc.fecha_afiliacion < '2024-05-01' THEN '2024-05-01' ELSE pc.fecha_afiliacion END AS min_date
      FROM persona_cliente AS pc
      LEFT JOIN (
        SELECT MIN(vd.periodo_pago_format) as fecha_minima_cobro, v.idpersona_cliente 
        FROM venta_detalle AS vd
        INNER JOIN venta AS v ON vd.idventa = v.idventa AND v.idpersona_cliente = idcliente
        WHERE vd.es_cobro='SI' AND v.estado_delete = 1 AND v.estado='1' AND  v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN ('01','03','12')
      ) AS vd on vd.idpersona_cliente = pc.idpersona_cliente
      WHERE pc.idpersona_cliente = idcliente
    ) AND 
    (
      SELECT 
      CASE
        WHEN vd.fecha_maxima_cobro > CURDATE() THEN vd.fecha_maxima_cobro
        WHEN pc.fecha_cancelacion > CURDATE() THEN  CURDATE() 
        ELSE 
          CASE
            WHEN DATE_FORMAT(CURDATE(), '%d') > DATE_FORMAT(pc.fecha_cancelacion, '%d') THEN CURDATE()
            ELSE  TIMESTAMPADD (MONTH, -1, CURDATE()) 
          END
      END AS mex_date
      FROM persona_cliente AS pc
      LEFT JOIN (
        SELECT MAX(vd.periodo_pago_format) as fecha_maxima_cobro, v.idpersona_cliente
        FROM venta_detalle AS vd
        INNER JOIN venta AS v ON vd.idventa = v.idventa AND v.idpersona_cliente = idcliente
        WHERE vd.es_cobro='SI' AND v.estado_delete = 1 AND v.estado='1' AND  v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN ('01','03','12')
      ) AS vd on vd.idpersona_cliente = pc.idpersona_cliente
      WHERE pc.idpersona_cliente = idcliente
    ) 
  ) AS mes_c
  LEFT JOIN( 
    SELECT vd.*, v.tipo_comprobante, v.serie_comprobante, v.numero_comprobante, DATE_FORMAT(v.fecha_emision, '%d, %b %Y - %h:%i %p') as fecha_emision_format,
    CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tipo_comprobante_v2,
    CONCAT( (SUBSTRING_INDEX(p1.nombre_razonsocial, ' ', 1)),' ', (SUBSTRING_INDEX(p1.apellidos_nombrecomercial, ' ', 1))) AS tecnico_asociado,
    CONCAT( (SUBSTRING_INDEX(pu.nombre_razonsocial, ' ', 1)),' ', (SUBSTRING_INDEX(pu.apellidos_nombrecomercial, ' ', 1))) AS tecnico_cobro,
    p1.foto_perfil as foto_perfil_tecnico, pu.foto_perfil as foto_perfil_tecnico_cobro
    FROM venta_detalle AS vd
    INNER JOIN venta AS v ON vd.idventa = v.idventa AND v.idpersona_cliente = idcliente         -- Datos de venta cabecera
    INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente              -- Datos del cliente
    INNER JOIN persona_trabajador as pt on pt.idpersona_trabajador = pc.idpersona_trabajador    -- Datos del Tecnico a cargo
    INNER JOIN persona as p1 on p1.idpersona = pt.idpersona                                     -- Datos del Tecnico a cargo
    LEFT JOIN usuario as u ON u.idusuario = v.user_created                                      -- Datos del Tecnico que cobro
    LEFT JOIN persona as pu ON pu.idpersona = u.idpersona                                       -- Datos del Tecnico que cobro
    INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01        -- Tipo de comprobane emitido
    WHERE vd.es_cobro='SI' AND v.estado_delete = 1 AND v.estado='1' AND  v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND
    v.tipo_comprobante IN ('01','03','12')
  ) AS lvd ON mes_c.year_month = lvd.periodo_pago 
  ORDER by mes_c.year_month DESC;

END