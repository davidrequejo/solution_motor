
SELECT 
-- Datos producto
p.idproducto, p.tipo_producto, p.codigo, p.codigo_alterno, p.nombre as nombre_producto, p.descripcion, 
CASE WHEN p.imagen IS NULL OR p.imagen = '' THEN 'no-producto_pret.png' ELSE p.imagen END AS imagen, 
p.estado as pro_estado, p.estado_delete as pro_estado_delete, p.created_at as pro_created_at, p.updated_at as pro_updated_at, p.user_trash as pro_user_trash, p.user_delete as pro_user_delete, 
p.user_created as pro_user_created, p.user_updated as pro_user_updated,
-- Datos Presentacion
pp.idproducto_presentacion, pp.idsunat_c03_unidad_medida, pp.nombre as nombre_presentacion, pp.cantidad as cantidad_presentacion,
CASE WHEN  pp.nombre = 'UNIDADES' then p.nombre else CONCAT( p.nombre, ' - ',  pp.nombre) end as nombre_producto_presentacion,
pp.estado as pp_estado,  pp.estado_delete as pp_estado_delete,
-- Datos Unidad Medida
um.nombre as unidad_medida, um.abreviatura,
-- Datos Producto Sucursal
ps.idproducto_sucursal, ps.idsucursal, ps.stock_minimo, ps.precio_compra, pp.precio_venta, pp.precio_venta_total, ps.precio_por_mayor, ps.stock as stock_total, 
CASE WHEN ps.stock = 0 OR ps.stock IS NULL THEN 0 ELSE ROUND( (ps.stock / pp.cantidad), 2 ) END AS stock_presentacion,
CASE WHEN ps.stock = 0 OR ps.stock IS NULL THEN 0 ELSE FLOOR( (ps.stock / pp.cantidad) ) END AS stock_presentacion_entero,
-- Datos Sucursal
s.nombre as sucursal_nombre, s.direccion as sucursal_direccion,
-- Datos Marca
p.idproducto_marca, pm.nombre as marca, 
-- Datos Categoria
p.idproducto_categoria, pc.nombre as categoria,
-- Datos Ubicacion
p.idproducto_categoria_ubicacion, pcu.nombre as categoria_ubicacion
FROM producto_presentacion as pp 
INNER JOIN producto_sucursal as ps ON ps.idproducto_sucursal = pp.idproducto_sucursal
INNER JOIN producto as p ON p.idproducto = ps.idproducto
INNER JOIN sunat_c03_unidad_medida as um ON pp.idsunat_c03_unidad_medida = um.idsunat_c03_unidad_medida
INNER JOIN producto_categoria as pc ON p.idproducto_categoria = pc.idproducto_categoria
INNER JOIN producto_categoria_ubicacion as pcu ON pcu.idproducto_categoria_ubicacion = p.idproducto_categoria_ubicacion
INNER JOIN producto_marca as pm ON p.idproducto_marca = pm.idproducto_marca
INNER JOIN sucursal as s ON s.idsucursal = ps.idsucursal
ORDER BY p.nombre