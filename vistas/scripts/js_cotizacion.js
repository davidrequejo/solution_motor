//Declaración de variables necesarias para trabajar
var impuesto = 18;
var cont = 0;
var detalles = 0;
var conNO = 1;

function agregarDetalleComprobante(idproducto, individual) {
  
  $(`.btn-add-producto-1-${idproducto}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);  
  $(`.btn-add-producto-2-${idproducto}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);  
  
  // var precio_venta = 0;
  var precio_sin_igv =0;
  var cantidad = 1;
  var descuento = 0;
  var precio_igv = 0;

  if (idproducto != "") {    

    if ($(`.producto_${idproducto}`).hasClass("producto_selecionado") && individual == false ) {    
      if (document.getElementsByClassName(`producto_${idproducto}`).length == 1) {
        var cant_producto = $(`.producto_${idproducto}`).val();
        var sub_total = parseInt(cant_producto, 10) + 1;
        $(`.producto_${idproducto}`).val(sub_total).trigger('change');
        toastr_success("Agregado!!",`Producto: ${$(`.nombre_producto_${idproducto}`).text()} agregado !!`, 700);
        modificarSubtotales();          
      }  
      $(`.btn-add-producto-1-${idproducto}`).html(`<span class="fa fa-plus"></span>`);        
      $(`.btn-add-producto-2-${idproducto}`).html(`<i class="fa-solid fa-list-ol"></i>`);          
    } else {         
      $.post("../ajax/cotizacion.php?op=mostrar_producto", {'idproducto': idproducto}, function (e, textStatus, jqXHR) {          
        
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {         

          if ($("#tipo_comprobante").select2("val") == "01") {
            var subtotal = cantidad * e.data.precio_venta;
          }else{
            var subtotal = cantidad * e.data.precio_venta;
          }
          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;          

          var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1">
              <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${e.data.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${e.data.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
            </td>

            <td class="py-1 text-nowrap">
              <span class="fs-11" ><i class="bi bi-upc"></i> ${e.data.codigo} <br> <i class="bi bi-person"></i> ${e.data.codigo_alterno}</span> 
            </td>

            <td class="py-1 text-nowrap">         
              <input type="hidden" name="idproducto[]" value="${e.data.idproducto}">

              <input type="hidden" class="form-control form-control-sm" name="periodo_pago[]"  value="">
              <input type="hidden"  name="es_cobro[]" id="es_cobro[]" value="NO">  
              <input type="hidden" name="pr_marca[]" value="${e.data.marca}">
              <input type="hidden" name="pr_categoria[]" value="${e.data.categoria}">

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre)}')"> </span></div>
                <div>                  
                  <textarea name="pr_nombre[]" class="form-control text-primary" rows="2" placeholder="ejemp: INTERNET ESTANDAR.">${e.data.nombre}</textarea>
                  <span class="d-block fs-10 text-muted">M: <b>${e.data.marca}</b> | C: <b>${e.data.categoria}</b></span>                   
                </div>
              </div>
            </td>

            <td class="py-1">
              <span class="fs-11 um_nombre_${cont}">${e.data.um_abreviatura}</span> 
              <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="${e.data.unidad_medida}">
              <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="${e.data.um_abreviatura}">
            </td>            

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${e.data.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); update_price(); ">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${e.data.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${e.data.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
              <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${e.data.precio_compra}"  >
              <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${e.data.precio_venta}"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control form-control-sm valid_descuento" name="valid_descuento_${cont}" value="0" min="0.00" required onkeyup="replicar_value_input(this, '.descuento_${cont}' ); update_price(); " onchange="replicar_value_input( this, '.descuento_${cont}'); update_price(); ">
              <input type="hidden" class="descuento_${cont}" name="descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
              <input type="hidden" class="descuento_porcentaje_${cont}" name="descuento_porcentaje[]" value="0" >
            </td>

            <td class="py-1 text-right">
              <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> 
              <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
              <input type="hidden" name="subtotal_no_descuento_producto[]" id="subtotal_no_descuento_producto_${cont}" value="0" >
            </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_venta.push({ id_cont: cont });
          modificarSubtotales();        
          toastr_success("Agregado!!",`Producto: ${e.data.nombre} agregado !!`, 700);

          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });

          cont++;   
          evaluar();
        } else {
          ver_errores(e);
        }           
        
        $(`.btn-add-producto-1-${idproducto}`).html(`<span class="fa fa-plus"></span>`);        
        $(`.btn-add-producto-2-${idproducto}`).html(`<i class="fa-solid fa-list-ol"></i>`);
        
      }).fail( function(e) { ver_errores(e); } ); 
    }
  } else {
    // alert("Error al ingresar el detalle, revisar los datos del artículo");
    toastr_error("Error!!",`Error al ingresar el detalle, revisar los datos del producto.`, 700);
  }
}

function listar_producto_x_codigo() {
 
  var codigo = document.getElementById("codigob").value;
  if (codigo == null || codigo == '') { toastr_info('Vacio!!', 'El campo de codigo esta vacío.'); return;  }
  var cantidad = 1; 
  $(`.buscar_x_code`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $.post("../ajax/facturacion.php?op=listar_producto_x_codigo", { codigo: codigo }, function (e, status) {
    e = JSON.parse(e); console.log(e);
    if (e.status == true) {         
      if (e.data == null) {
        toastr_warning('No existe', 'Proporcione un codigo existente o el producto pertenece a otra categoria.');
      } else {
        if ($(`.producto_${e.data.idproducto}`).hasClass("producto_selecionado")) {
          if (document.getElementsByClassName(`producto_${e.data.idproducto}`).length == 1) {
            var cant_producto = $(`.producto_${e.data.idproducto}`).val();
            var sub_total = parseInt(cant_producto, 10) + 1;
            $(`.producto_${e.data.idproducto}`).val(sub_total).trigger('change');
            toastr_success("Agregado!!",`Producto: ${$(`.nombre_producto_${e.data.idproducto}`).text()} agregado !!`, 700);
            modificarSubtotales();          
          }  
                  
          $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
        } else {      
        

          if ($("#tipo_comprobante").select2("val") == "01") {
            var subtotal = cantidad * e.data.precio_venta;
          }else{
            var subtotal = cantidad * e.data.precio_venta;
          }
          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;          

          var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1">
            <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${e.data.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button>-->
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${e.data.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
            </td>
            <td class="py-1 text-nowrap">
              <span class="fs-11" ><i class="bi bi-upc"></i> ${e.data.codigo} <br> <i class="bi bi-person"></i> ${e.data.codigo_alterno}</span>             
            </td>
            <td class="py-1">         
              <input type="hidden" name="idproducto[]" value="${e.data.idproducto}">

              <input type="hidden" class="form-control form-control-sm" name="periodo_pago[]"  value="">
              <input type="hidden"  name="es_cobro[]" id="es_cobro[]" value="NO"> 
              <input type="hidden" name="pr_marca[]" value="${e.data.marca}">
              <input type="hidden" name="pr_categoria[]" value="${e.data.categoria}"> 

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre)}')"> </span></div>
                <div>
                  <span class="d-block fs-11 fw-semibold text-nowrap text-primary">${e.data.nombre}</span>
                  <textarea name="pr_nombre[]" class="form-control text-primary" rows="2" placeholder="ejemp: INTERNET ESTANDAR.">${e.data.nombre}</textarea>
                  <span class="d-block fs-10 text-muted">M: <b>${e.data.marca}</b> | C: <b>${e.data.categoria}</b></span> 
                </div>
              </div>
            </td>
            
            <td class="py-1">
              <span class="fs-11 um_nombre_${cont}">${e.data.um_abreviatura}</span> 
              <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="${e.data.unidad_medida}">
              <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="${e.data.um_abreviatura}">
            </td>

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${e.data.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input(this, '#cantidad_${cont}'); update_price(); ">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${cantidad}" min="0.01" required  >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${e.data.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${e.data.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
              <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${e.data.precio_compra}"  >
              <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${e.data.precio_venta}"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control form-control-sm valid_descuento" name="valid_descuento_${cont}" value="0" min="0.00" required  onkeyup="replicar_value_input(this, '.descuento_${cont}' ); update_price(); " onchange="replicar_value_input( this, '.descuento_${cont}'); update_price(); ">
              <input type="hidden" class="descuento_${cont}" name="descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
              <input type="hidden" class="descuento_porcentaje_${cont}" name="descuento_porcentaje[]" value="0" >
            </td>

            <td class="py-1 text-right">
              <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> 
              <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
              <input type="hidden" name="subtotal_no_descuento_producto[]" id="subtotal_no_descuento_producto_${cont}" value="0" >
            </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_venta.push({ id_cont: cont });
          modificarSubtotales();        
          toastr_success("Agregado!!",`Producto: ${e.data.nombre} agregado !!`, 700);

          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0.01, messages: { min:"Mínimo 0.01" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0.01, messages: { min:"Mínimo 0.01" } }); 
          });
          $('.valid_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });

          cont++;  
          evaluar();
        }
      }
      $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
      $(`.tooltip`).remove();
      
    } else {
      ver_errores(e);
    } 
  }).fail( function(e) { ver_errores(e); } );

}

function ver_editar_venta(idventa) {
  show_hide_form(2);
  limpiar_form_venta();  

  if (idventa == '') {
    
  } else {         

    $("#cargando-1-formulario").hide();
    $("#cargando-2-fomulario").show();    

    $.post("../ajax/facturacion.php?op=mostrar_editar_detalles_venta", {'idventa': idventa }, function (e, status) {

      e = JSON.parse(e); console.log(e);
      if (e.status == true) {    

        $("#idpersona_cliente").val(e.data.venta.idpersona_cliente).trigger('change');             
        $("#observacion_documento").val(e.data.venta.observacion_documento); 
        $("#periodo_pago").val(e.data.venta.periodo_pago); 

        $("#total_recibido").val(e.data.venta.total_recibido);
        $("#metodo_pago").val(e.data.venta.metodo_pago).trigger('change');     
        $("#mp_monto").val(e.data.venta.mp_monto);  
        
        if (e.data.venta.tipo_comprobante == '01') {
          $("#tipo_comprobante01").prop("checked", true);
        } else if (e.data.venta.tipo_comprobante == '03') {
          $("#tipo_comprobante03").prop("checked", true);
        } else if (e.data.venta.tipo_comprobante == '07') {
          $("#tipo_comprobante07").prop("checked", true);
        } else if (e.data.venta.tipo_comprobante == '12') {
          $("#tipo_comprobante12").prop("checked", true);
        }

        $.each(e.data.detalle, function(index, val1) {
          var img = val1.imagen == "" || val1.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${val1.imagen}` ;          

          var fila = `
            <tr class="filas" id="fila${cont}"> 

              <td class="py-1">
                <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${val1.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
                <!-- <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${val1.idproducto}, ${cont});"><i class="fas fa-times"></i></button> -->
              </td>

              <td class="py-1 text-nowrap">
                <span class="fs-11" ><i class="bi bi-upc"></i> ${val1.codigo} <br> <i class="bi bi-person"></i> ${val1.codigo_alterno}</span>                
              </td>

              <td class="py-1">         
                <input type="hidden" name="idproducto[]" value="${val1.idproducto}">

                <input type="hidden" class="form-control form-control-sm" name="periodo_pago[]"  value="">
                <input type="hidden"  name="es_cobro[]" id="es_cobro[]" value="NO">  

                <div class="d-flex flex-fill align-items-center">
                  <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(val1.nombre_producto)}')"> </span></div>
                  <div>
                    <span class="d-block fs-11 fw-semibold text-nowrap text-primary">${val1.nombre_producto}</span>
                    <span class="d-block fs-10 text-muted">M: <b>${val1.marca}</b> | C: <b>${val1.categoria}</b></span> 
                  </div>
                </div>
              </td>

              <td class="py-1">
                <span class="fs-11 um_nombre_${cont}">${val1.um_abreviatura}</span> 
                <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="${val1.um_nombre}">
                <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="${val1.um_abreviatura}">
              </td>

              <td class="py-1 form-group">
                <input type="number" class="w-100px valid_cantidad form-control-sm form-control producto_${val1.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${val1.cantidad}" min="0.01" required readonly onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input(this, '#cantidad_${cont}'); update_price(); ">
                <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${val1.cantidad}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
              </td> 

              <td class="py-1 form-group">
                <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${val1.precio_venta}" min="0.01" required readonly onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
                <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${val1.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
                <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
                <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
                <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" value="${val1.precio_compra}"  >
                <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${val1.precio_venta_descuento}"  >
              </td> 

              <td class="py-1 form-group">
                <input type="number" class="w-100px form-control form-control-sm valid_descuento" name="valid_descuento_${cont}" value="${val1.descuento}" min="0.00" required readonly onkeyup="replicar_value_input(this, '.descuento_${cont}' ); update_price(); " onchange="replicar_value_input( this, '.descuento_${cont}'); update_price(); ">
                <input type="hidden" class="descuento_${cont}" name="descuento[]" value="${val1.descuento}" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
                <input type="hidden" class="descuento_porcentaje_${cont}" name="descuento_porcentaje[]" value="${val1.descuento_porcentaje}" >
              </td>

              <td class="py-1 text-right">
                <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${val1.subtotal}</span> 
                <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="${val1.subtotal}" >
                <input type="hidden" name="subtotal_no_descuento_producto[]" id="subtotal_no_descuento_producto_${cont}" value="${val1.subtotal_no_descuento}" > 
              </td>
              <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
              
            </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_venta.push({ id_cont: cont });
          modificarSubtotales();        
          
          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });

          cont++;
          evaluar();
          
          
        });

        $(".btn-guardar").hide();
        $("#form-facturacion").valid();

        $("#cargando-1-formulario").show();
        $("#cargando-2-fomulario").hide();
      } else{ ver_errores(e); }
      
    }).fail( function(e) { ver_errores(e); } );

  }
}

function evaluar() {
  if (detalles > 0) {
    $(".btn-guardar").show();
  } else {
    $(".btn-guardar").hide();
    cont = 0;
    $(".venta_subtotal").html("<span>S/</span> 0.00");
    $("#venta_subtotal").val(0);

    $(".venta_descuento").html("<span>S/</span> 0.00");
    $("#venta_descuento").val(0);

    $(".venta_igv").html("<span>S/</span> 0.00");
    $("#venta_igv").val(0);

    $(".venta_total").html("<span>S/</span> 0.00");
    $("#venta_total").val(0);
    

  }
}

function default_val_igv() { if ($("#tipo_comprobante").select2("val") == "01") { $("#impuesto").val(0); } } // FACTURA

function modificarSubtotales() {  

  var val_igv = $("#impuesto").val();

  if ($("#tipo_comprobante").select2("val") == null) {    

    $("#impuesto").val(0);
    $(".val_igv").html('IGV (0%)');

    $("#tipo_gravada").val('SUBTOTAL');
    $(".tipo_gravada").html('SUBTOTAL');

    if (array_data_venta.length == 0) {
    } else {
      array_data_venta.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: IGV
        var precio_sin_igv = precio_con_igv;
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: precio + IGV
        var igv = 0;
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));
        
        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });
      calcularTotalesSinIgv();
    }
  } else if ($("#tipo_comprobante").select2("val") == "12") {      // TICKET 

    if (array_data_venta.length === 0) {
      if (val_igv == '' || val_igv <= 0) {
        $("#tipo_gravada").val('SUBTOTAL');
        $(".tipo_gravada").html('SUBTOTAL');
        $(".val_igv").html(`IGV (0%)`);
      } else {
        $("#tipo_gravada").val('GRAVADA');
        $(".tipo_gravada").html('GRAVADA');
        $(".val_igv").html(`IGV (${redondearExp((val_igv * 100), 2)}%)`);
      }
      
    } else {
      // validamos el valor del igv ingresado        

      array_data_venta.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: Precio sin IGV
        var precio_sin_igv = redondearExp( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal'), 2);
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: IGV
        var igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));

        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });

      calcularTotalesConIgv();
    }
  } else if ($("#tipo_comprobante").select2("val") == "01" || $("#tipo_comprobante").select2("val") == "03" ) { // FACTURA O BOLETA 

    $(".hidden").show(); //Mostramos: IGV, PRECIO SIN IGV
    $("#colspan_subtotal").attr("colspan", 7); //cambiamos el: colspan    
    $("#val_igv").prop("readonly",false);

    if (array_data_venta.length === 0) {
      if (val_igv == '' || val_igv <= 0) {
        $("#tipo_gravada").val('NO GRAVADA');
        $(".tipo_gravada").html('NO GRAVADA');
        $(".val_igv").html(`IGV (0%)`);
      } else {
        $("#tipo_gravada").val('GRAVADA');
        $(".tipo_gravada").html('GRAVADA');
        $(".val_igv").html(`IGV (${(parseFloat(val_igv) * 100).toFixed(2)}%)`);
      }
      
    } else {
      // validamos el valor del igv ingresado        

      array_data_venta.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: Precio sin IGV
        var precio_sin_igv = ( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal')).toFixed(2);
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: IGV
        var igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));

        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto.toFixed(2)));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });

      calcularTotalesConIgv();
    }
  } else {

    $("#impuesto").val(0);    
    $(".val_igv").html('IGV (0%)');

    $("#tipo_gravada").val('SUBTOTAL');
    $(".tipo_gravada").html('SUBTOTAL');

    if (array_data_venta.length === 0) {
    } else {
      array_data_venta.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: IGV
        var precio_sin_igv = precio_con_igv;
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: precio + IGV
        var igv = 0;
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));

        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });

      calcularTotalesSinIgv();
    }
  }
  
  if (form_validate_facturacion) { $("#form-facturacion").valid();}
}

function calcularTotalesSinIgv() {
  var total = 0.0;
  var igv = 0;
  var descuento = 0;

  if (array_data_venta.length === 0) {
  } else {
    array_data_venta.forEach((element, index) => {
      total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text()));
      descuento += parseFloat( $(`.descuento_${element.id_cont}`).val() );
    });

    $(".venta_subtotal").html("<span>S/</span> " + formato_miles(total));
    $("#venta_subtotal").val(redondearExp(total, 2));

    $(".venta_descuento").html("<span>S/</span> " + formato_miles(descuento));
    $("#venta_descuento").val(redondearExp(descuento, 2));

    $(".venta_igv").html("<span>S/</span> 0.00");
    $("#venta_igv").val(0.0);
    $(".val_igv").html('IGV (0%)');

    $(".venta_total").html("<span>S/</span> " + formato_miles(total));
    $("#venta_total").val(redondearExp(total, 2));     
  }
}

function calcularTotalesConIgv() {
  var val_igv = $('#impuesto').val();
  var igv = 0;
  var total = 0.0;
  var descuento = 0.0;

  var subotal_sin_igv = 0;

  array_data_venta.forEach((element, index) => {
    total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text()));
    descuento += parseFloat( $(`.descuento_${element.id_cont}`).val() );
  });

  //console.log(total); 

  subotal_sin_igv = redondearExp(quitar_igv_del_precio(total, val_igv, 'entero') , 2);
  igv = (parseFloat(total) - parseFloat(subotal_sin_igv)).toFixed(2);

  $(".venta_subtotal").html(`<span>S/</span> ${formato_miles(subotal_sin_igv)}`);
  $("#venta_subtotal").val(redondearExp(subotal_sin_igv, 2));

  $(".venta_descuento").html("<span>S/</span> " + formato_miles(descuento));
  $("#venta_descuento").val(redondearExp(descuento, 2));

  $(".venta_igv").html("<span>S/</span> " + formato_miles(igv));
  $("#venta_igv").val(igv);

  $(".venta_total").html("<span>S/</span> " + formato_miles(total));
  $("#venta_total").val(redondearExp(total, 2));  
  
  total = 0.0;
}

function eliminarDetalle(idproducto, indice) {
  $("#fila" + indice).remove();
  array_data_venta.forEach(function (car, index, object) { if (car.id_cont === indice) { object.splice(index, 1); } });
  modificarSubtotales();
  detalles = detalles - 1;
  toastr_warning("Removido!!","Producto removido", 700);
  evaluar();
}

$(document).ready(function () {
  $("#razon_social").on("keyup", function () {
    $("#suggestions").fadeOut();
    $("#suggestions3").fadeOut();
    var key = $(this).val();
    var dataString = "key=" + key;
    $.ajax({
      type: "POST",
      url: "../ajax/persona.php?op=buscarclienteDomicilio",
      data: dataString,
      success: function (data) {
        //Escribimos las sugerencias que nos manda la consulta
        $("#suggestions2").fadeIn().html(data);
        // autocomplete(document.getElementById(".suggest-element"),  data);
        //Al hacer click en algua de las sugerencias
        $(".suggest-element").on("click", function () {
          //Obtenemos la id unica de la sugerencia pulsada
          var id = $(this).attr("id");
          //Editamos el valor del input con data de la sugerencia pulsada
          $("#numero_documento").val($("#" + id).attr("ndocumento"));
          $("#razon_social").val($("#" + id).attr("ncomercial"));
          $("#domicilio_fiscal").val($("#" + id).attr("domicilio"));
          $("#idpersona").val(id);
          //$("#resultado").html("<p align='center'><img src='../public/images/spinner.gif' /></p>");
          //Hacemos desaparecer el resto de sugerencias
          $("#suggestions2").fadeOut();
          //alert('Has seleccionado el '+id+' '+$('#'+id).attr('data'));
          return false;
        });
      },
    });
  });
});

function update_price() {
  toastr_success("Actualizado!!",`Precio Actualizado.`, 700);
}


