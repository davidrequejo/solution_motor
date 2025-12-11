CREATE PROCEDURE `sp_insertar_pago_y_actualizar` (
	idventa int , 
	idventa_cuotas int, 
	monto_documento DECIMAL(10,2)
)
BEGIN
	
  DECLARE v_fecha_pago DATETIME;    
  SET time_zone = '-05:00';
	SET v_fecha_pago = CURRENT_TIMESTAMP;
    
  INSERT INTO venta_cuota_pago( idventa, idventa_cuotas, monto_documento, monto_cuota, fecha_cuota, fecha_pago, numero_cuota) 
  VALUES ( idventa , idventa_cuotas, monto_documento, (select monto_cuota from venta_cuotas where idventa_cuotas = idventa_cuotas), 
  (select fecha_vencimiento from venta_cuotas where idventa_cuotas = idventa_cuotas), CURRENT_TIMESTAMP,  
  (select numero_cuota from venta_cuotas where idventa_cuotas = idventa_cuotas) );
    
	UPDATE venta_cuotas SET estado_cuota='pagado', fecha_pago= v_fecha_pago WHERE idventa_cuotas = idventa_cuotas;
END