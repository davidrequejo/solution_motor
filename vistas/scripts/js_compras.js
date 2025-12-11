//Declaración de variables necesarias para trabajar
var impuesto = 18;
var cont = 0;
var detalles = 0;
var conNO = 1;


function agregarDetalleComprobante(idproducto_sucursal,codigo_barras, individual) {
  
  $(`.btn-add-producto-1-${idproducto_sucursal}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);  
  $(`.btn-add-producto-2-${idproducto_sucursal}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);  
  
  // var precio_venta = 0;
  var precio_sin_igv =0;
  var cantidad = 1;
  var descuento = 0;
  var precio_igv = 0;

  if (idproducto_sucursal != "") {    

    if ($(`.producto_${idproducto_sucursal}`).hasClass("producto_selecionado") && individual == false ) {    
      if (document.getElementsByClassName(`producto_${idproducto_sucursal}`).length == 1) {
        var cant_producto = $(`.producto_${idproducto_sucursal}`).val();
        var sub_total = parseInt(cant_producto, 10) + 1;
        $(`.producto_${idproducto_sucursal}`).val(sub_total).trigger('change');
        toastr_success("Agregado!!",`Producto: ${$(`.nombre_producto_${idproducto_sucursal}`).text()} agregado !!`, 700);
        modificarSubtotales();          
      }  
      $(`.btn-add-producto-1-${idproducto_sucursal}`).html(`<span class="fa fa-plus"></span>`);        
      $(`.btn-add-producto-2-${idproducto_sucursal}`).html(`<i class="fa-solid fa-list-ol"></i>`);          
    } else {         
      $.post("../ajax/compras.php?op=mostrar_producto", {'idproducto_sucursal': idproducto_sucursal}, function (e, textStatus, jqXHR) {          
        
        e = JSON.parse(e); 
        if (e.status == true) {  

          $('#searchResults').hide();   

          if ($("#tipo_comprobante").select2("val") == "01") {
            var subtotal = cantidad * e.data.precio_venta;
          }else{
            var subtotal = cantidad * e.data.precio_venta;
          }
          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;    
        

          var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1 text-nowrap">
              <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${e.data.idproducto_sucursal}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
              <button type="button" class="btn btn-danger py-0 btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${e.data.idproducto_sucursal}, ${cont});"><i class="fas fa-times"></i></button>
              <button type="button" class="btn btn-info py-0 btn-sm btn-file-delete-${cont}" onclick="ver_compra_x_producto(${e.data.idproducto_sucursal});"><i class="bi bi-cart-dash"></i></button>
              <div class="form-check form-switch">
                <input  type="hidden" name="actualizar_stock[]" id="actualizar_stock_${cont}" value="NO">
                <input class="form-check-input" type="checkbox" role="switch" name="check_actualizar_stock[]" id="check_actualizar_stock_${cont}" value="SI"  >
                <label class="form-check-label" for="check_actualizar_stock_${cont}">Stock</label>
              </div>
            </td>

            <td class="py-1 fs-11 text-nowrap">
              <i class="bi bi-upc"></i> ${e.data.codigo} <br> <i class="bi bi-person"></i> ${e.data.codigo_alterno}
            </td>

            <td class="py-1">         
              <input type="hidden" name="idproducto_sucursal[]" value="${e.data.idproducto_sucursal}">

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre_producto)}')"> </span></div>
                <div>
                  <h6 class="d-block fs-11 fw-semibold text-primary">${e.data.nombre_producto}</h6>
                  <span class="text-nowrap fs-10 text-muted">Marca: <b>${e.data.marca}</b> | Categoría: <b>${e.data.categoria}</b></span> 
                </div>
              </div>
            </td>

            <td class="py-1 form-group">
             <select class="form-control  w-100 fs-11 unidad_medida_compra_${cont}" id="unidad_medida_compra_${cont}" name="unidad_medidacompra[]" required ><option value="">Seleccionar</option></select>
            </td>

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control producto_${e.data.idproducto_sucursal} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}');">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad_x_medida_compra form-control producto_${e.data.idproducto_sucursal} producto_selecionado" name="cantidad_x_medida_compra[${cont}]" id="cantidad_x_medida_compra_${cont}" min="0.01" required >           
            </td> 



            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${e.data.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}');">
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${e.data.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control valid_descuento" name="valid_descuento_${cont}" value="0" min="0.00" required onkeyup="replicar_value_input(this, '.descuento_${cont}' ); update_price(); " onchange="replicar_value_input( this, '.descuento_${cont}');">
              <input type="hidden" class="descuento_${cont}" name="descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
            </td>

            <td class="py-1 text-right"><span class="text-right subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          let $select = $(`#unidad_medida_compra_${cont}`); // usa cont si estás dentro de un bucle

          $.post("../ajax/compras.php?op=select_u_medida_compra", {}, function (ee, textStatus, jqXHR) {
            ee = JSON.parse(ee);

            if (ee.status === true) {

              ee.data.forEach(function (opt) { $select.append(`<option value="${opt.idsunat_c03_unidad_medida}">${opt.nombre}</option>`); });

              $select.val('58');

            } else {
              ver_errores(ee);
            }
          });

          array_data_compra.push({ id_cont: cont });
          modificarSubtotales();        
          // toastr_success("Agregado!!",`Producto: ${e.data.nombre_producto} agregado !!`, 700);
                    
          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });

          cont++;   
          evaluar();
        } else {
          ver_errores(e);
        }           
        
        $(`.btn-add-producto-1-${idproducto_sucursal}`).html(`<span class="fa fa-plus"></span>`);        
        $(`.btn-add-producto-2-${idproducto_sucursal}`).html(`<i class="fa-solid fa-list-ol"></i>`);
        
      });  
    }
  } else {
    // alert("Error al ingresar el detalle, revisar los datos del artículo");
    toastr_error("Error!!",`Error al ingresar el detalle, revisar los datos del producto.`, 700);
  }
}


$('#search_producto').on('keydown', function (e) {   
  if (e.key === 'Enter') {
    e.preventDefault(); // Evita que se envíe un formulario si lo hubiera
    const codigo = $(this).val().trim();    
  }
});



function mostrar_editar_detalles_compra(idcompra) {
  $("#cargando-1-formulario").hide();
  $("#cargando-2-fomulario").show();
  limpiar_form_compra();
  show_hide_form(2);
  $.post("../ajax/compras.php?op=mostrar_editar_detalles_compra", {'idcompra': idcompra}, function (e, status) {

    e = JSON.parse(e); 
    if (e.status == true) {    

      $("#idcompra").val(e.data.compra.idcompra);
      $("#idproveedor").val(e.data.compra.idproveedor).trigger('change');
      $("#tipo_comprobante").val(e.data.compra.tipo_comprobante).trigger('change');
      $("#serie").val(e.data.compra.serie_comprobante);
      $("#descripcion").val(e.data.compra.descripcion);
      $("#fecha_compra").val(e.data.compra.fecha_compra);
        
      
      // ------------ IMAGEN -----------
      if (e.data.compra.comprobante == "" || e.data.compra.comprobante == null) { } else {
        $("#doc_old_1").val(e.data.compra.comprobante);
        $("#doc1_nombre").html(`<div class="row"> <div class="col-md-12"><i>imagen.${extrae_extencion(e.data.compra.comprobante)}</i></div></div>`);
        // cargamos la imagen adecuada par el archivo
        $("#doc1_ver").html(doc_view_extencion(e.data.compra.comprobante, 'assets/modulo/comprobante_compra', '100%', '210'));   //ruta imagen          
      }

      $("#impuesto").val(e.data.compra.val_igv);

      e.data.compra_detalle.forEach((val, key) => {
      
        var img = val.imagen == "" || val.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${val.imagen}` ;          

        var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1">
              <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${val.idproducto_sucursal}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
              <button type="button" class="btn btn-danger btn-sm py-0 btn-file-delete-${cont}" onclick="eliminarDetalle(${val.idproducto_sucursal}, ${cont});"><i class="fas fa-times"></i></button>
              <button type="button" class="btn btn-info py-0 btn-sm btn-file-delete-${cont}" onclick="ver_compra_x_producto(${val.idproducto_sucursal});"><i class="bi bi-cart-dash"></i></button>
              <div class="form-check form-switch">
                <input  type="hidden" name="actualizar_stock[]" id="actualizar_stock_${cont}" value="NO">
                <input class="form-check-input" type="checkbox" role="switch" name="check_actualizar_stock[]" id="check_actualizar_stock_${cont}" value="SI" ${val.actualizar_stock == 'SI' ? 'checked' : ''} >
                <label class="form-check-label" for="check_actualizar_stock_${cont}">Stock</label>
              </div>
            </td>

            <td class="py-1 fs-11 text-nowrap">
              <i class="bi bi-upc"></i> ${val.codigo} <br> <i class="bi bi-person"></i> ${val.codigo_alterno}
            </td>

            <td class="py-1">         
              <input type="hidden" name="idproducto_sucursal[]" value="${val.idproducto_sucursal}">

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(val.nombre_producto)}')"> </span></div>
                <div>
                  <h6 class="d-block fs-11 fw-semibold text-primary">${val.nombre_producto}</h6>
                  <span class="text-nowrap fs-10 text-muted">Marca: <b>${val.marca}</b> | Categoría: <b>${val.categoria}</b></span> 
                </div>
              </div>
            </td>

            <td class="py-1 form-group">
             <select class="form-control  w-100 fs-11 unidad_medida_compra_${cont}" id="unidad_medida_compra_${cont}" name="unidad_medidacompra[]" required ><option value="">Seleccionar</option>${val.unidad_medida_html_option}</select>
            </td>

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control producto_${val.idproducto_sucursal} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${val.cantidad_compra}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); ">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${val.cantidad_compra}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad_x_medida_compra form-control producto_${val.idproducto_sucursal} producto_selecionado" name="cantidad_x_medida_compra[${cont}]" value="${val.cantidad_compra_presentacion}" id="cantidad_x_medida_compra_${cont}" min="0.01" required >           
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${val.precio_con_igv}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}');">
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${val.precio_con_igv}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control valid_descuento" name="valid_descuento_${cont}" value="${val.descuento}" min="0.00" required onkeyup="replicar_value_input(this, '.descuento_${cont}' ); update_price(); " onchange="replicar_value_input( this, '.descuento_${cont}');">
              <input type="hidden" class="descuento_${cont}" name="descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
            </td>

            <td class="py-1 text-right"><span class="text-right subtotal_producto_${cont}" id="subtotal_producto">${val.subtotal}</span> <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

        detalles = detalles + 1;
        $("#tabla-productos-seleccionados tbody").append(fila);
        array_data_compra.push({ id_cont: cont });
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
      
      $("#cargando-1-formulario").show();
      $("#cargando-2-fomulario").hide();
    } else{ ver_errores(e); }
    
  });

}

function evaluar() {
  if (detalles > 0) {
    $(".btn-guardar").show();
  } else {
    $(".btn-guardar").hide();
    cont = 0;
    $(".subtotal_compra").html("<span>S/</span> 0.00");
    $("#subtotal_compra").val(0);

    $(".descuento_compra").html("<span>S/</span> 0.00");
    $("#descuento_compra").val(0);

    $(".igv_compra").html("<span>S/</span> 0.00");
    $("#igv_compra").val(0);

    $(".total_venta").html("<span>S/</span> 0.00");
    $("#total_compra").val(0);

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

    if (array_data_compra.length == 0) {
    } else {
      array_data_compra.forEach((element, index) => {
        var cantidad = parseFloat($(`.cantidad_${element.id_cont}`).val());
        var precio_con_igv = parseFloat($(`.precio_con_igv_${element.id_cont}`).val());
        var deacuento = parseFloat($(`.descuento_${element.id_cont}`).val());
        var subtotal_producto = 0;

        // Calculamos: IGV
        var precio_sin_igv = precio_con_igv;
        $(`.precio_sin_igv_${element.id_cont}`).val(precio_sin_igv);

        // Calculamos: precio + IGV
        var igv = 0;
        $(`.precio_igv_${element.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - deacuento;
        $(`.subtotal_producto_${element.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${element.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
      });
      calcularTotalesSinIgv();
    }
  } else if ($("#tipo_comprobante").select2("val") == "12") {      // TICKET 

    if (array_data_compra.length === 0) {
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

      array_data_compra.forEach((element, index) => {
        var cantidad = parseFloat($(`.cantidad_${element.id_cont}`).val());
        var precio_con_igv = parseFloat($(`.precio_con_igv_${element.id_cont}`).val());
        var deacuento = parseFloat($(`.descuento_${element.id_cont}`).val());
        var subtotal_producto = 0;

        // Calculamos: Precio sin IGV
        var precio_sin_igv = redondearExp( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal'), 2);
        $(`.precio_sin_igv_${element.id_cont}`).val(precio_sin_igv);

        // Calculamos: IGV
        var igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);
        $(`.precio_igv_${element.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - deacuento;
        $(`.subtotal_producto_${element.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${element.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
      });

      calcularTotalesConIgv();
    }
  } else if ($("#tipo_comprobante").select2("val") == "01" || $("#tipo_comprobante").select2("val") == "03" ) { // FACTURA O BOLETA 

    $(".hidden").show(); //Mostramos: IGV, PRECIO SIN IGV
    $("#colspan_subtotal").attr("colspan", 7); //cambiamos el: colspan    
    $("#val_igv").prop("readonly",false);

    if (array_data_compra.length === 0) {
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

      array_data_compra.forEach((key, index) => {
        var cantidad = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var deacuento = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;

        // Calculamos: Precio sin IGV
        var precio_sin_igv = ( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal')).toFixed(2);
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: IGV
        var igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - deacuento;
        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto.toFixed(2)));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
      });

      calcularTotalesConIgv();
    }
  } else {

    $("#impuesto").val(0);    
    $(".val_igv").html('IGV (0%)');

    $("#tipo_gravada").val('SUBTOTAL');
    $(".tipo_gravada").html('SUBTOTAL');

    if (array_data_compra.length === 0) {
    } else {
      array_data_compra.forEach((element, index) => {
        var cantidad = parseFloat($(`.cantidad_${element.id_cont}`).val());
        var precio_con_igv = parseFloat($(`.precio_con_igv_${element.id_cont}`).val());
        var deacuento = parseFloat($(`.descuento_${element.id_cont}`).val());
        var subtotal_producto = 0;

        // Calculamos: IGV
        var precio_sin_igv = precio_con_igv;
        $(`.precio_sin_igv_${element.id_cont}`).val(precio_sin_igv);

        // Calculamos: precio + IGV
        var igv = 0;
        $(`.precio_igv_${element.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - deacuento;
        $(`.subtotal_producto_${element.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${element.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
      });

      calcularTotalesSinIgv();
    }
  }
}

function calcularTotalesSinIgv() {
  var total = 0.0;
  var igv = 0;
  var descuento = 0;

  if (array_data_compra.length === 0) {
  } else {
    array_data_compra.forEach((element, index) => {
      total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text()));
      descuento += parseFloat( $(`.descuento_${element.id_cont}`).val() );
    });

    $(".subtotal_compra").html("<span>S/</span> " + formato_miles(total));
    $("#subtotal_compra").val(redondearExp(total, 2));

    $(".descuento_compra").html("<span>S/</span> " + formato_miles(descuento));
    $("#descuento_compra").val(redondearExp(descuento, 2));

    $(".igv_compra").html("<span>S/</span> 0.00");
    $("#igv_compra").val(0.0);
    $(".val_igv").html('IGV (0%)');

    $(".total_compra").html("<span>S/</span> " + formato_miles(total));
    $("#total_compra").val(redondearExp(total, 2));
    $(".pago_rapido").html(redondearExp(total, 2));
  }
}

function calcularTotalesConIgv() {
  var val_igv = $('#impuesto').val();
  var igv = 0;
  var total = 0.0;
  var descuento = 0.0;

  var subotal_sin_igv = 0;

  array_data_compra.forEach((element, index) => {
    total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text()));
    descuento += parseFloat( $(`.descuento_${element.id_cont}`).val() );
  }); 

  subotal_sin_igv = redondearExp(quitar_igv_del_precio(total, val_igv, 'entero') , 2);
  igv = (parseFloat(total) - parseFloat(subotal_sin_igv)).toFixed(2);

  $(".subtotal_compra").html(`<span>S/</span> ${formato_miles(subotal_sin_igv)}`);
  $("#subtotal_compra").val(redondearExp(subotal_sin_igv, 2));

  $(".descuento_compra").html("<span>S/</span> " + formato_miles(descuento));
  $("#descuento_compra").val(redondearExp(descuento, 2));

  $(".igv_compra").html("<span>S/</span> " + formato_miles(igv));
  $("#igv_compra").val(igv);

  $(".total_compra").html("<span>S/</span> " + formato_miles(total));
  $("#total_compra").val(redondearExp(total, 2));
  $(".pago_rapido").html(redondearExp(total, 2));

  total = 0.0;
}

function eliminarDetalle(idproducto_sucursal_sucursal, indice) {
  $("#fila" + indice).remove();
  array_data_compra.forEach(function (car, index, object) { if (car.id_cont === indice) { object.splice(index, 1); } });
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

function quitasuge1() {
  if ($("#numero_documento").val() == "") {
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

$(document).on('change', '[id^="check_actualizar_stock_"]', function () {
  const cont = $(this).attr('id').replace('check_actualizar_stock_', '');
  const valor = $(this).is(':checked') ? 'SI' : 'NO';
  $(`#actualizar_stock_${cont}`).val(valor);
  console.log(`Fila ${cont}: ${valor}`);
});