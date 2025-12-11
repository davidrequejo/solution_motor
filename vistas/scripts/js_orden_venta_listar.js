//Declaración de variables necesarias para trabajar
var impuesto = 18;
var cont = 0;
var detalles = 0;
var conNO = 1;

function agregarDetalleComprobante(idproducto_presentacion, codigo_barras, individual) {   
  
  // var precio_venta = 0;
  var precio_sin_igv =0;
  var cantidad = 1;
  var descuento = 0;
  var precio_igv = 0;
  var precio_venta = 0;

  var search_producto = ''; 

  if (idproducto_presentacion == "" || idproducto_presentacion == null ) {   
    if ( codigo_barras == null || codigo_barras == ''  ) {
      toastr_info('¡Campo vacío!', 'Por favor ingrese almenor un codigo e intente nuevamente.');
      return;
    } else {
      search_producto = codigo_barras == "OK" ? $("#search_producto").val() : codigo_barras;      
      $(`.buscar_x_code`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
    }    
  } else {    
    $(`.btn-add-producto-1-${idproducto_presentacion}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);  
    $(`.btn-add-producto-2-${idproducto_presentacion}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`); 
    search_producto = idproducto_presentacion;    
  }

  if (search_producto == null || search_producto == '') {  
    $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
    toastr_info('¡Campo vacío!', 'Por favor ingrese almenor un codigo e intente nuevamente.'); return;  
  }
       
  $.getJSON("../ajax/orden_venta_listar.php?op=mostrar_producto", {'search_producto': search_producto}, function (e, textStatus, jqXHR) {        
    
    if (e.status == true) {      
      if (e.data == null) {
        toastr_warning('No existe!!', 'Proporcione un codigo existente o el producto pertenece a otra categoria.');          
      } else {         
        
        if ( $(`#tabla-productos-seleccionados tr.idproducto_presentacion_${e.data.idproducto_presentacion}`).length >= 1 && individual == false ) {

          if ( $(`#tabla-productos-seleccionados tr.idproducto_presentacion_${e.data.idproducto_presentacion}`).length >= 1 ) {
            var cant_producto =  parseInt($(`.idproducto_presentacion_${e.data.idproducto_presentacion} .input_cantidad_venta`).val()) || 0;
            var sub_total = cant_producto + 1;
            $(`.idproducto_presentacion_${e.data.idproducto_presentacion} .input_cantidad_venta`).val(sub_total).trigger('change');
            toastr_success("Agregado!!",`Producto: ${e.data.nombre_producto_presentacion} agregado !!`, 700);
            modificarSubtotales();          
          }                  
         
        } else { 
          var es_precio_por_mayor = $('#precio_por_mayor').is(':checked') ? 'SI' : 'NO';
          var id_cant = contarDivsArray('.filas_producto_agregado', 1);    
          var cantidad_presentacion = parseFloat(e.data.cantidad_presentacion) || 0;
          precio_venta = es_precio_por_mayor == 'SI' ? (parseFloat(e.data.precio_por_mayor) || 0) : (parseFloat(e.data.precio_venta) || 0);
          var subtotal = cantidad_presentacion * precio_venta;          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;          
          
          var fila = `<tr class="filas_producto_agregado idproducto_presentacion_${e.data.idproducto_presentacion}" id="fila${cont}"> 

            <td class="py-1">
              <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${e.data.idproducto_presentacion}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${e.data.idproducto_presentacion}, ${cont});"><i class="fas fa-times"></i></button>
            </td>

            <td class="py-1 text-nowrap td-codigo-producto" style="display: none;" >
              <span class="fs-11" ><i class="bi bi-upc"></i> ${e.data.codigo} <br> <i class="bi bi-person"></i> ${e.data.codigo_alterno}</span> 
            </td>

            <td class="py-1">         
              <input type="hidden" name="idproducto_presentacion[]" value="${e.data.idproducto_presentacion}">

              <input type="hidden" name="pr_marca[]" value="${e.data.marca}">
              <input type="hidden" name="pr_categoria[]" value="${e.data.categoria}">              
              <input type="hidden" name="precio_por_mayor[]" value="${es_precio_por_mayor}">              

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre_producto_presentacion)}')"> </span></div>
                <div>
                  <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${e.data.nombre_producto_presentacion}</textarea>
                  <div class="span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${e.data.nombre_producto_presentacion}</span> </div>
                  <span class="text-nowrap fs-9 text-muted">M: <b>${e.data.marca}</b> | C: <b>${e.data.categoria}</b></span> 
                </div>
              </div>
            </td>           
            
            <td class="py-1">
              <span class="fs-11 um_nombre_${cont}">NIU</span> 
              <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="UNIDADES">
              <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="NIU">
            </td>    

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control form-control-sm input_cantidad_venta" name="cantidad_venta[${id_cant}]" value="${cantidad}" min="1" max="${e.data.stock_presentacion_entero}" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price(); ">
              <input type="hidden" class="input_cantidad_presentacion" name="cantidad_presentacion[]" value="${cantidad_presentacion}">
              <input type="hidden" class="input_cantidad_total" name="cantidad_total[]" value="0">
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control form-control-sm input_precio_con_igv" name="precio_con_igv[${id_cant}]"  value="${precio_venta}" min="0.01" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price();">
              <input type="hidden" class="input_precio_sin_igv" name="precio_sin_igv[]"  value="0" min="0" >
              <input type="hidden" class="input_precio_igv" name="precio_igv[]"  value="0"  >
              <input type="hidden" class="input_precio_compra" name="precio_compra[]"  value="${e.data.precio_compra}"  >
              <input type="hidden" class="input_precio_venta_descuento" name="precio_venta_descuento[]" value="${precio_venta}"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control form-control-sm input_descuento" name="descuento[${id_cant}]" value="0" min="0.00" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price();">              
              <input type="hidden" class="input_descuento_porcentaje" name="descuento_porcentaje[]" value="0" >
            </td>

            <td class="py-1 text-right">
              <span class="text-right fs-11 span_subtotal_x_producto" data-value="${subtotal}" >${subtotal}</span> 
              <input type="hidden" class="input_subtotal_x_producto" name="subtotal_x_producto[]"  value="0" > 
              <input type="hidden" class="input_subtotal_no_descuento_producto" name="subtotal_no_descuento_producto[]" value="0" >
            </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
         
          modificarSubtotales();        
          toastr_success("Agregado!!",`Producto: ${e.data.nombre_producto_presentacion} agregado !!`, 700);

          // reglas de validación     
          $('.input_precio_con_igv').each(function(ex) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min: 0.01,  messages: { min:"Mínimo {0}" } }); 
          });
          $('.input_cantidad_venta').each(function(ex) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min: 1, messages: { min:"Mínimo {0}", max:"Stock máximo {0}" } }); 
          });
          $('.input_descuento').each(function(ex) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });          

          cont++;   
          evaluar();
        }
      }
    } else {
      ver_errores(e);
    }           
    
    $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
    $(`.btn-add-producto-1-${idproducto_presentacion}`).html(`<span class="fa fa-plus"></span>`);        
    $(`.btn-add-producto-2-${idproducto_presentacion}`).html(`<i class="fa-solid fa-list-ol"></i>`); 
    
  }).fail( function(e) { ver_errores(e); } ); 
  
  
}

$('#search_producto').on('keydown', function (e) {   
  if (e.key === 'Enter') {
    e.preventDefault(); // Evita que se envíe un formulario si lo hubiera
    const codigo = $(this).val().trim();
    console.log(codigo);    
  }
});

function ver_venta(idventa) {
  

  if (idventa == '') {
    toastr_info('No Encontrado!!','Documento no encontrado, porfavor valide nuevamente los datos.');
  } else {     
    show_hide_form(2);
    limpiar_form_venta();      

    $("#cargando-1-formulario").hide();
    $("#cargando-2-formulario").show();    

    $.post("../ajax/orden_venta_listar.php?op=mostrar_editar_detalles_venta", {'idventa': idventa }, function (e, status) {

      e = JSON.parse(e); //console.log(e);
      if (e.status == true) {    

        $("#o_idcliente").val(e.data.venta.idpersona_cliente).trigger('change');             
        $("#o_observacion_documento").val(e.data.venta.observacion_documento);         

        $.each(e.data.detalle, function(index, val1) {
          var img = val1.imagen == "" || val1.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${val1.imagen}` ;          
          var id_cant = contarDivsArray('.filas_producto_agregado', 1); 
          var fila = `
            <tr class="filas_producto_agregado" id="fila${cont}"> 

              <td class="py-1">
                <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${val1.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
                <!-- <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${val1.idproducto}, ${cont});"><i class="fas fa-times"></i></button> -->
              </td>

              <td class="py-1 text-nowrap td-codigo-producto" style="display: none;" >
                <span class="fs-11" ><i class="bi bi-upc"></i> ${val1.codigo} <br> <i class="bi bi-person"></i> ${val1.codigo_alterno}</span>                
              </td>

              <td class="py-1">         
                <input type="hidden" name="idproducto[]" value="${val1.idproducto}">

                <input type="hidden" name="pr_marca[]" value="${val1.marca}">
                <input type="hidden" name="pr_categoria[]" value="${val1.categoria}">
                <input type="hidden" name="pr_nombre[]" value="${val1.nombre}">

                <div class="d-flex flex-fill align-items-center">
                  <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(val1.nombre_producto)}')"> </span></div>
                  <div>
                    <span class="d-block fs-11 fw-semibold text-nowrap text-primary">${val1.nombre_producto}</span>
                    <span class="d-block fs-10 text-muted">M: <b>${val1.marca}</b> | C: <b>${val1.categoria}</b></span> 
                  </div>
                </div>
              </td>

              <td class="py-1">
                <span class="fs-11 um_nombre_${cont}">NIU</span> 
                <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="UNIDADES">
                <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="NIU">
              </td>             

              <td class="py-1 form-group">
                <input type="number" class="w-100px form-control form-control-sm input_cantidad_venta" name="cantidad_venta[${id_cant}]" value="${val1.cantidad_venta}" min="0.01" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price(); ">
                <input type="hidden" class="input_cantidad_presentacion" name="cantidad_presentacion[]" value="${val1.cantidad_presentacion}">
                <input type="hidden" class="input_cantidad_total" name="cantidad_total[]" value="${val1.cantidad_total}">
              </td> 

              <td class="py-1 form-group">
                <input type="number" class="w-135px form-control form-control-sm input_precio_con_igv" name="precio_con_igv[${id_cant}]"  value="${val1.precio_venta}" min="0.01" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price();">
                <input type="hidden" class="input_precio_sin_igv" name="precio_sin_igv[]"  value="0" min="0" >
                <input type="hidden" class="input_precio_igv" name="precio_igv[]"  value="0"  >
                <input type="hidden" class="input_precio_compra" name="precio_compra[]"  value="${val1.precio_compra}"  >
                <input type="hidden" class="input_precio_venta_descuento" name="precio_venta_descuento[]" value="${val1.precio_venta}"  >
              </td> 

              <td class="py-1 form-group">
                <input type="number" class="w-100px form-control form-control-sm input_descuento" name="descuento[${id_cant}]" value="0" min="0.00" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price();">              
                <input type="hidden" class="input_descuento_porcentaje" name="descuento_porcentaje[]" value="0" >
              </td>

              <td class="py-1 text-right">
                <span class="text-right fs-11 span_subtotal_x_producto" data-value="${val1.subtotal}" >${val1.subtotal}</span> 
                <input type="hidden" class="input_subtotal_x_producto" name="subtotal_x_producto[]"  value="0" > 
                <input type="hidden" class="input_subtotal_no_descuento_producto" name="subtotal_no_descuento_producto[]" value="0" >
              </td>
              <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
              
            </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          
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
        $("#cargando-2-formulario").hide();
      } else{ ver_errores(e); }
      
    }).fail( function(e) { ver_errores(e); } );

  }
}

function ver_editar_venta(idventa) {

  if (idventa == '') {
    toastr_info('No Encontrado!!','Documento no encontrado, porfavor valide nuevamente los datos.');
  } else {
    show_hide_form(2);
    limpiar_form_venta();  

    $("#cargando-1-formulario").hide();
    $("#cargando-2-formulario").show();    

    $.post("../ajax/orden_venta_listar.php?op=mostrar_editar_detalles_venta", {'idventa': idventa }, function (e, status) {

      e = JSON.parse(e); //console.log(e);
      if (e.status == true) {    
        $("#o_idventa").val(e.data.venta.idventa);
        $("#o_impuesto").val(e.data.venta.impuesto);
        

        $("#o_idcliente").val(e.data.venta.idpersona_cliente).trigger('change');             
        $("#o_observacion_documento").val(e.data.venta.observacion_documento);            
        
        if (e.data.venta.tipo_comprobante == '01') {
          $("#o_tipo_comprobante01").prop("checked", true).trigger('change');;
        } else if (e.data.venta.tipo_comprobante == '03') {
          $("#o_tipo_comprobante03").prop("checked", true).trigger('change');;
        } else if (e.data.venta.tipo_comprobante == '07') {
          $("#o_tipo_comprobante07").prop("checked", true).trigger('change');;
        } else if (e.data.venta.tipo_comprobante == '12') {
          $("#o_tipo_comprobante12").prop("checked", true).trigger('change');;
        }

        $.each(e.data.detalle, function(index, val1) {
          var img = val1.imagen == "" || val1.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${val1.imagen}` ;          
          var id_cant = contarDivsArray('.filas_producto_agregado', 1);
          
          var fila = `<tr class="filas_producto_agregado idproducto_presentacion_${val1.idproducto_presentacion}" id="fila${cont}"> 

            <td class="py-1">
              <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${val1.idproducto_presentacion}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${val1.idproducto_presentacion}, ${cont});"><i class="fas fa-times"></i></button>
            </td>

            <td class="py-1 text-nowrap td-codigo-producto" style="display: none;" >
              <span class="fs-11" ><i class="bi bi-upc"></i> ${val1.codigo} <br> <i class="bi bi-person"></i> ${val1.codigo_alterno}</span> 
            </td>

            <td class="py-1">         
              <input type="hidden" name="idproducto_presentacion[]" value="${val1.idproducto_presentacion}">

              <input type="hidden" name="pr_marca[]" value="${val1.pr_marca}">
              <input type="hidden" name="pr_categoria[]" value="${val1.pr_categoria}">              
              <input type="hidden" name="precio_por_mayor[]" value="${val1.precio_por_mayor}">              

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(val1.pr_nombre)}')"> </span></div>
                <div>
                  <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${val1.pr_nombre}</textarea>
                  <div class="span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${val1.pr_nombre}</span> </div>
                  <span class="text-nowrap fs-9 text-muted">M: <b>${val1.pr_marca}</b> | C: <b>${val1.pr_categoria}</b></span> 
                </div>
              </div>
            </td>

            <td class="py-1">
              <span class="fs-11 um_nombre_${cont}">NIU</span> 
              <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="UNIDADES">
              <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="NIU">
            </td>             

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control form-control-sm input_cantidad_venta" name="cantidad_venta[${id_cant}]" value="${val1.cantidad_venta}" min="0.01" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price(); ">
              <input type="hidden" class="input_cantidad_presentacion" name="cantidad_presentacion[]" value="${val1.cantidad_presentacion}">
              <input type="hidden" class="input_cantidad_total" name="cantidad_total[]" value="${val1.cantidad_total}">
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control form-control-sm input_precio_con_igv" name="precio_con_igv[${id_cant}]"  value="${val1.precio_venta}" min="0.01" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price();">
              <input type="hidden" class="input_precio_sin_igv" name="precio_sin_igv[]"  value="0" min="0" >
              <input type="hidden" class="input_precio_igv" name="precio_igv[]"  value="0"  >
              <input type="hidden" class="input_precio_compra" name="precio_compra[]"  value="${val1.precio_compra}"  >
              <input type="hidden" class="input_precio_venta_descuento" name="precio_venta_descuento[]" value="${val1.precio_venta}"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control form-control-sm input_descuento" name="descuento[${id_cant}]" value="0" min="0.00" required onkeyup="modificarSubtotales(); update_price();" onchange="modificarSubtotales(); update_price();">              
              <input type="hidden" class="input_descuento_porcentaje" name="descuento_porcentaje[]" value="0" >
            </td>

            <td class="py-1 text-right">
              <span class="text-right fs-11 span_subtotal_x_producto" data-value="${val1.subtotal}" >${val1.subtotal}</span> 
              <input type="hidden" class="input_subtotal_x_producto" name="subtotal_x_producto[]"  value="0" > 
              <input type="hidden" class="input_subtotal_no_descuento_producto" name="subtotal_no_descuento_producto[]" value="0" >
            </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          
          modificarSubtotales();        
          
          // reglas de validación     
          $('.input_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.input_cantidad_venta').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}", max:"Stock máximo {0}" } }); 
          });
          $('.input_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });          

          cont++;
          evaluar();
          
        });        
        
        $("#form-facturacion").valid();

        $("#cargando-1-formulario").show();
        $("#cargando-2-formulario").hide();
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
    $(".o_venta_subtotal").html("<span>S/</span> 0.00");
    $("#o_venta_subtotal").val(0);

    $(".o_venta_descuento").html("<span>S/</span> 0.00");
    $("#o_venta_descuento").val(0);

    $(".o_venta_igv").html("<span>S/</span> 0.00");
    $("#o_venta_igv").val(0);

    $(".o_venta_total").html("<span>S/</span> 0.00");
    $("#o_venta_total").val(0);
    
  }
}

function default_val_igv() { if ($('#o_tipo_comprobante').val() == "01") { $("#o_impuesto").val(0); } } // FACTURA

function modificarSubtotales() {  

  var val_igv = parseFloat($("#o_impuesto").val()) || 0;
  var o_tipo_comprobante = $('#o_tipo_comprobante').val();

  if ( o_tipo_comprobante == null) {    

    toastr_warning('Tipo comprobante vacio', 'Porfavor seleccione un <b>tipo de documento</b> para calcular los totales.');
    
  } else if ( o_tipo_comprobante == "103"  ) { // FACTURA - BOLETA - NOTA DE VENTA

    $(".hidden").show(); //Mostramos: IGV, PRECIO SIN IGV
    $("#colspan_subtotal").attr("colspan", 7); //cambiamos el: colspan    
    $("#val_igv").prop("readonly",false);
    
    if (val_igv == '' || val_igv <= 0) {
      $("#o_tipo_gravada").val('NO GRAVADA');
      $(".o_tipo_gravada").html('NO GRAVADA');
      $(".val_igv").html(`IGV (0%)`);
    } else {
      $("#o_tipo_gravada").val('GRAVADA');
      $(".o_tipo_gravada").html('GRAVADA');
      $(".val_igv").html(`IGV (${(parseFloat(val_igv) * 100).toFixed(2)}%)`);
    }    
      
    $('#tabla-productos-seleccionados tbody tr').each(function () {
      // Obtener los valores de cantidad y precio_unitario de los inputs
      let cantidad_venta                  = parseFloat($(this).find('.input_cantidad_venta').val()) || 0;
      let cantidad_presentacion     = parseFloat($(this).find('.input_cantidad_presentacion').val()) || 0;
      let precio_con_igv            =  parseFloat($(this).find('.input_precio_con_igv').val()) || 0;
      let descuento                 =  parseFloat($(this).find('.input_descuento').val()) || 0;
      var subtotal_x_producto         = 0;
      var subtotal_x_producto_no_dcto = 0;

      var precio_sin_igv = 0;
      var igv = 0; 
            
      if (val_igv > 0) {          
        precio_sin_igv = ( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal')).toFixed(2);   // Calculamos: precio sin IGV          
        igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);                 // Calculamos:  IGV            
      } else {                 
        precio_sin_igv = precio_con_igv;                                                            // Calculamos: precio sin IGV    
      }  

      $(this).find('.input_precio_sin_igv').val(precio_sin_igv);
      $(this).find('.input_precio_igv').val(igv);

      // Calculamos: Subtotal de cada producto
      subtotal_x_producto_no_dcto = (cantidad_venta * cantidad_presentacion) * parseFloat(precio_con_igv);
      subtotal_x_producto = (cantidad_venta * cantidad_presentacion) * parseFloat(precio_con_igv) - descuento;

      // Calculamos: precio unitario descontado
      var precio_unitario_dscto = subtotal_x_producto / (cantidad_venta * cantidad_presentacion);      
      $(this).find('.input_precio_venta_descuento').val(redondearExp(precio_unitario_dscto, 2 ));

      // Calculamos: porcentaje descuento
      var porcentaje_monto = descuento / subtotal_x_producto_no_dcto;      
      $(this).find('.input_descuento_porcentaje').val(redondearExp(porcentaje_monto, 2 ));       
      
      $(this).find('.span_subtotal_x_producto').html(formato_miles(subtotal_x_producto)).attr('data-value', subtotal_x_producto);
      $(this).find('.input_subtotal_x_producto').val(redondearExp(subtotal_x_producto, 2 ));
      $(this).find('.input_subtotal_no_descuento_producto').val(redondearExp(subtotal_x_producto_no_dcto, 2 ));
      $(this).find('.input_cantidad_total').val( (cantidad_venta * cantidad_presentacion) );

    });

    if (val_igv > 0) {        
      calcularTotalesConIgv(); 
    } else {  
      calcularTotalesSinIgv(); 
    }   
  } 

  if (form_validate_facturacion) { $("#form-facturacion").valid();}
}

function calcularTotalesSinIgv() {
  var total = 0.0;
  var igv = 0;
  var descuento = 0;

  if ( $('#tabla-productos-seleccionados tbody tr').length >= 1  ) {

    total = $('.span_subtotal_x_producto').get().reduce((s, el) => s + Number(el.dataset.value || 0), 0);
    descuento = $('.input_descuento').get().reduce((s, el) => s + Number(el.value || 0) , 0);    
  }

  $(".o_venta_subtotal").html("<span>S/</span> " + formato_miles(total));
  $("#o_venta_subtotal").val(redondearExp(total, 2));

  $(".o_venta_descuento").html("<span>S/</span> " + formato_miles(descuento));
  $("#o_venta_descuento").val(redondearExp(descuento, 2));

  $(".o_venta_igv").html("<span>S/</span> 0.00");
  $("#o_venta_igv").val(0.0);
  $(".val_igv").html('IGV (0%)');

  $(".o_venta_total").html("<span>S/</span> " + formato_miles(total));
  $("#o_venta_total").val(redondearExp(total, 2));
  
  
}

function calcularTotalesConIgv() {
  var val_igv = $('#o_impuesto').val();
  var igv = 0;
  var total = 0.0;
  var descuento = 0.0;

  var subotal_sin_igv = 0;

  total = $('.span_subtotal_x_producto').get().reduce((s, el) => s + Number(el.dataset.value || 0), 0);
  descuento = $('.input_descuento').get().reduce((s, el) => s + Number(el.value || 0) , 0);    

  //console.log(total); 

  subotal_sin_igv = redondearExp(quitar_igv_del_precio(total, val_igv, 'entero') , 2);
  igv = (parseFloat(total) - parseFloat(subotal_sin_igv)).toFixed(2);

  $(".o_venta_subtotal").html(`<span>S/</span> ${formato_miles(subotal_sin_igv)}`);
  $("#o_venta_subtotal").val(redondearExp(subotal_sin_igv, 2));

  $(".o_venta_descuento").html("<span>S/</span> " + formato_miles(descuento));
  $("#o_venta_descuento").val(redondearExp(descuento, 2));

  $(".o_venta_igv").html("<span>S/</span> " + formato_miles(igv));
  $("#o_venta_igv").val(igv);

  $(".o_venta_total").html("<span>S/</span> " + formato_miles(total));
  $("#o_venta_total").val(redondearExp(total, 2));
  
  total = 0.0;
}

function eliminarDetalle(idproducto, indice) {
  $("#fila" + indice).remove();
  
  modificarSubtotales();
  detalles = detalles - 1;
  toastr_warning("Removido!!","Producto removido", 700);
  evaluar();
  renombrarInputsArray('.input_cantidad_venta','name', 'cantidad_venta');
  renombrarInputsArray('.input_precio_con_igv','name', 'precio_con_igv');
  renombrarInputsArray('.input_descuento','name', 'descuento');
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
          $("#o_numero_documento").val($("#" + id).attr("ndocumento"));
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

function quitasuge1() {
  if ($("#o_numero_documento").val() == "") {
    $("#suggestions").fadeOut();
  }
  $("#suggestions").fadeOut();
}

function quitasuge2() {
  if ($("#razon_social").val() == "") {
    $("#suggestions2").fadeOut();
  }
  $("#suggestions2").fadeOut();
}

function quitasuge3() {
  $("#suggestions3").fadeOut();
}

function update_price() {
  toastr_success("Actualizado!!",`Precio Actualizado.`, 700);
}

function hide_show_codigo_prodcuto(estado) {

  const firstTd = document.querySelector( '#tabla-productos-seleccionados tfoot tr td:first-child'  );
  
  if ( estado == true) {
    $('.td-codigo-producto').hide();
    $('.span-codigo-producto-show').show();
    $('.span-codigo-producto-hide').hide();   
    firstTd.colSpan = 5;   
  } else {   
    $('.td-codigo-producto').show();     
    $('.span-codigo-producto-show').hide();
    $('.span-codigo-producto-hide').show();   
    firstTd.colSpan = 6;   
  }
  
}

// .....::::::::::::::::::::::::::::::::::::: S E C C I O N   M E T O D O   D E   P A G O   :::::::::::::::::::::::::::::::::::::::..
