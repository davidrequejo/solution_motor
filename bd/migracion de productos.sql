 insert into producto_presentacion (idproducto, idsunat_c03_unidad_medida, nombre, cantidad)
 select idproducto, 58 as unidad_medida, 'UNIDADADES' AS Nnombre, 1 cantidad from producto;

 -- Crear unidad basica
 insert into producto_presentacion  (idproducto, idsunat_c03_unidad_medida, nombre, cantidad)
 select idproducto, 14 as unidad_medida, 'DOCENA' AS Nnombre, 12 cantidad from producto;


INSERT INTO `sucursal` (`idsucursal`, `nombre`, `codigo_sunat`, `igv`, `glosa_amazonica`, `direccion`, `telefono`, `correo`, `web`, `descripcion`, `estado`, `estado_delete`, `created_at`, `updated_at`, `user_trash`, `user_delete`, `user_created`, `user_updated`) VALUES
(1, 'PRINCIPAL', NULL, 0.00, 'SI', 'AV. PRINCIPAL', NULL, NULL, NULL, NULL, '1', '1', '2025-05-26 23:22:55', '2025-05-26 23:22:55', NULL, NULL, NULL, NULL);

 insert into producto_sucursal ( idsucursal, idproducto, stock, stock_minimo, precio_compra, precio_venta, precio_por_mayor)
 select 1 as idsucursal, idproducto, stock, stock_minimo, precio_compra, precio_venta, precioB as  precio_mayor  from producto_old;




-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
-- Migracion version 2
-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


UPDATE producto_presentacion AS pp
JOIN (
    SELECT ppo.idproducto_presentacion, ps.idproducto_sucursal
    FROM producto_presentacion_old ppo
    INNER JOIN producto AS p ON p.idproducto = ppo.idproducto
    INNER JOIN producto_sucursal AS ps ON ps.idproducto = ppo.idproducto
) AS sub
ON pp.idproducto_presentacion = sub.idproducto_presentacion
SET pp.idproducto_sucursal = sub.idproducto_sucursal;

-- presentacion: Docena
UPDATE producto_presentacion AS pp
JOIN (
    SELECT ppo.idproducto_presentacion, ps.idproducto_sucursal, ppo.cantidad, ps.precio_venta, ps.precio_por_mayor, ppo.idsunat_c03_unidad_medida
    FROM producto_presentacion_old ppo
    INNER JOIN producto AS p ON p.idproducto = ppo.idproducto
    INNER JOIN producto_sucursal AS ps ON ps.idproducto = ppo.idproducto where ppo.idsunat_c03_unidad_medida <> 58
) AS sub
ON pp.idproducto_presentacion = sub.idproducto_presentacion
SET pp.precio_venta = sub.precio_por_mayor, pp.precio_venta_total = sub.precio_por_mayor * sub.cantidad;

-- preentacion: UND
UPDATE producto_presentacion AS pp
JOIN (
    SELECT ppo.idproducto_presentacion, ps.idproducto_sucursal, ppo.cantidad, ps.precio_venta, ps.precio_por_mayor, ppo.idsunat_c03_unidad_medida
    FROM producto_presentacion_old ppo
    INNER JOIN producto AS p ON p.idproducto = ppo.idproducto
    INNER JOIN producto_sucursal AS ps ON ps.idproducto = ppo.idproducto where ppo.idsunat_c03_unidad_medida = 58
) AS sub
ON pp.idproducto_presentacion = sub.idproducto_presentacion
SET pp.precio_venta = sub.precio_venta, pp.precio_venta_total = sub.precio_venta * sub.cantidad;


-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
-- ACTUALIZACION DE STOCK EN BASE AL ULTIMO BACK UP
-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

SELECT ps.idproducto_sucursal, 
ps.stock as stock_hoy, 
pso.stock as  stock_anterior, 
(IFNULL(pso.stock,0) - IFNULL(vdp.cant_venta_hoy, 0)) stock_actual_calculado, vdp.cant_venta_hoy
from producto_sucursal as ps 
inner JOIN	producto_sucursal_old as pso on pso.idproducto_sucursal = ps.idproducto_sucursal
left JOIN (
    SELECT pp.idproducto_sucursal, SUM(vd.cantidad_total) as cant_venta_hoy 
    FROM venta_detalle as vd 
    inner JOIN venta as v on v.idventa = vd.idventa
    inner JOIN producto_presentacion as pp on pp.idproducto_presentacion = vd.idproducto_presentacion
    where DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') > '2025-10-13' and  v.tipo_comprobante in ('01', '03')
    GROUP BY pp.idproducto_sucursal
) as vdp on vdp.idproducto_sucursal = ps.idproducto_sucursal
where ps.stock is null or ps.stock = ''

SELECT ps.idproducto_sucursal, 
ps.stock as stock_hoy, 
pso.stock as  stock_anterior
from producto_sucursal as ps 
inner JOIN	producto_sucursal_old as pso on pso.idproducto_sucursal = ps.idproducto_sucursal

-- actualiza solo a los que no se vendio
UPDATE producto_sucursal AS pp
INNER JOIN (
    SELECT ps.idproducto_sucursal, pso.stock 
    from producto_sucursal as ps 
    inner JOIN	producto_sucursal_old as pso on pso.idproducto_sucursal = ps.idproducto_sucursal
    left JOIN (
        SELECT pp.idproducto_sucursal, SUM(vd.cantidad_total) as cantidad_total 
        FROM venta_detalle as vd 
        inner JOIN producto_presentacion as pp on pp.idproducto_presentacion = vd.idproducto_presentacion
        where DATE_FORMAT(vd.v_fecha_emision, '%Y-%m-%d') in ('2025-07-22', '2025-07-23') and vd.v_tipo_comprobante = 'ORDEN DE VENTA'
        GROUP BY pp.idproducto_sucursal
    ) as vdp on vdp.idproducto_sucursal = ps.idproducto_sucursal
    where vdp.idproducto_sucursal is null
) AS sub
ON pp.idproducto_sucursal = sub.idproducto_sucursal
SET pp.stock = sub.stock;

-- Actualizado solo a lo vendido
UPDATE producto_sucursal AS pp
INNER JOIN (
    SELECT ps.idproducto_sucursal, ps.stock as stock_hoy, pso.stock as  stock_anterior, 
    (pso.stock - vdp.cant_venta_hoy) stock_actual_calculado, vdp.cant_venta_hoy
    from producto_sucursal as ps 
    inner JOIN	producto_sucursal_old as pso on pso.idproducto_sucursal = ps.idproducto_sucursal
    inner JOIN (
        SELECT pp.idproducto_sucursal, SUM(vd.cantidad_total) as cant_venta_hoy 
        FROM venta_detalle as vd 
        inner JOIN producto_presentacion as pp on pp.idproducto_presentacion = vd.idproducto_presentacion
        where DATE_FORMAT(vd.v_fecha_emision, '%Y-%m-%d') in ('2025-07-22', '2025-07-23') and 
        vd.v_tipo_comprobante = 'ORDEN DE VENTA'
        GROUP BY pp.idproducto_sucursal
    ) as vdp on vdp.idproducto_sucursal = ps.idproducto_sucursal
) AS sub
ON pp.idproducto_sucursal = sub.idproducto_sucursal
SET pp.stock = sub.stock_actual_calculado;

-- Actualizamos solo los qu tiene STOCK NULL
UPDATE producto_sucursal AS ps
INNER JOIN producto_sucursal_old AS pso ON pso.idproducto_sucursal = ps.idproducto_sucursal
LEFT JOIN (
    SELECT pp.idproducto_sucursal, SUM(vd.cantidad_total) as cant_venta_hoy 
    FROM venta_detalle as vd 
    inner JOIN venta as v on v.idventa = vd.idventa
    inner JOIN producto_presentacion as pp on pp.idproducto_presentacion = vd.idproducto_presentacion
    where DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') > '2025-10-13' and  v.tipo_comprobante in ('01', '03')
    GROUP BY pp.idproducto_sucursal
) AS vdp ON vdp.idproducto_sucursal = ps.idproducto_sucursal
SET ps.stock = IFNULL(pso.stock, 0) - IFNULL(vdp.cant_venta_hoy, 0)
WHERE ps.stock IS NULL;





-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
-- ACTUALIZACION DE SUBTOTAL
-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

-- Validar cantidad total
SELECT idventa, cantidad_presentacion, cantidad_venta, cantidad_total,(cantidad_presentacion * cantidad_venta) AS cantidad_calculada
FROM venta_detalle
WHERE idventa = 9037  AND (cantidad_presentacion * cantidad_venta) <> cantidad_total;

-- Validar Sub Total
SELECT idventa, cantidad_total, precio_venta, subtotal,  ( (cantidad_total * precio_venta) - descuento ) AS subtotal_calculado
FROM venta_detalle
WHERE idventa = 9037  AND ( (cantidad_total * precio_venta) - descuento ) <> subtotal;

-- Actulizar solo a los que son diferentes
UPDATE venta_detalle
SET subtotal = ( (cantidad_total * precio_venta) - descuento ), subtotal_no_descuento = cantidad_total * precio_venta, subtotal_compra = (cantidad_total * precio_compra)
WHERE ( (cantidad_total * precio_venta) - descuento ) <> subtotal;

-- Actualizar solo subtotal compras
UPDATE venta_detalle SET subtotal_compra = (cantidad_total * precio_compra)


-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
-- ACTUALIZACION DE CABECERA
-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

-- Validamos antes de ejecutar
SELECT v.idventa, v.venta_total AS total_antes,  detalle_sumado.total_correcto
FROM venta v
JOIN (
    SELECT idventa, SUM(subtotal) AS total_correcto
    FROM venta_detalle
    GROUP BY idventa
) AS detalle_sumado ON v.idventa = detalle_sumado.idventa
WHERE v.venta_total != detalle_sumado.total_correcto;

UPDATE venta v
JOIN (
    SELECT idventa,  SUM(subtotal) AS total_correcto
    FROM venta_detalle
    GROUP BY idventa
) AS detalle_sumado ON v.idventa = detalle_sumado.idventa
SET v.venta_total = detalle_sumado.total_correcto
WHERE v.venta_total != detalle_sumado.total_correcto;

