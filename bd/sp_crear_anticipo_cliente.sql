CREATE PROCEDURE `sp_crear_anticipo_cliente` (
	IN id_persona_cliente int,
    IN id_venta int,
    IN tipo_venta varchar(15),
    IN serie_y_numero_venta varchar(20),    
    IN total_anticipo decimal(20,2)	
)
BEGIN
	DECLARE serie_v varchar(15); DECLARE correlativo_v INT;
    SELECT ( numero + 1) AS correlativo INTO correlativo_v FROM sunat_c01_tipo_comprobante WHERE codigo = '102';
    SELECT serie INTO serie_v FROM sunat_c01_tipo_comprobante WHERE codigo = '102';
    UPDATE sunat_c01_tipo_comprobante SET numero = (numero + 1) WHERE codigo = '102';    
    
	INSERT INTO anticipo_cliente( idpersona_cliente, idventa, tipo, fecha_anticipo,  tipo_comprobante, serie_comprobante, numero_comprobante, total, descripcion) 
	VALUES (id_persona_cliente,id_venta,tipo_venta,CURRENT_TIMESTAMP,'102',serie_v, correlativo_v, total_anticipo,
	CONCAT( 'Este es un anticipo de egreso, registrado con venta: ', serie_y_numero_venta));
END