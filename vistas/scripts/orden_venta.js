
var array_data_filas = [];
var precio_por_mayor ="NO";

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_tipo_documento = new Choices('#cli_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_distrito       = new Choices('#cli_distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// var choice_centro_poblado = new Choices('#cli_centro_poblado',  { shouldSort: false, removeItemButton: true,noResultsText: 'No hay resultados.', } );


function init() {
  // listar_tabla();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass("send-data") == false) { $("#submit-form-producto").submit(); }});

  $("#guardar_registro_categoria").on("click", function (e) { if ($(this).hasClass("send-data") == false) {$("#submit-form-categoria").submit();}});
  $("#guardar_registro_nuevo_cliente").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-nuevo-cliente").submit(); } else {toastr_info('Espere!!', 'Por favor sea pasiente se estan procesando los datos...');} });
  $("#guardar_registro_nuevo_orden").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-nueva_orden").submit(); } else {toastr_info('Espere!!', 'Por favor sea pasiente se estan procesando los datos...');} });
  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/orden_venta.php?op=select2_cliente","#o_idcliente",'1', '.charge_o_idcliente');
  lista_select2("../ajax/ajax_general.php?op=select2_series_comprobante&tipo_comprobante=103", '#o_serie_comprobante', 'OV001');  

  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#cli_distrito', null);
  

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════
  $("#o_idcliente").select2({theme: "bootstrap4",placeholder: "Seleccione cliente",allowClear: true,});

  $("#cli_distrito").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});
  $("#cli_tipo_documento").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);     
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_centro_poblado", choice_centro_poblado, null);
  
}



// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         C R E A R   O R D E N   D E   V E N T A                                                   ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function limpiar_form_orden(){

  
  $("#o_idventa").val('');  
  $("#o_idcliente").val(1).trigger('change');   
  $("#o_observacion_documento").val('');  
  
  $('#productos-seleccionados').html(''); 

  $("#o_venta_total").val("");     
  $(".o_venta_total").html("0");

  $(".o_venta_subtotal").html("<span>S/</span> 0.00");
  $("#o_venta_subtotal").val("");

  $(".o_venta_descuento").html("<span>S/</span> 0.00");
  $("#o_venta_descuento").val("");

  $(".o_venta_igv").html("<span>S/</span> 0.00");
  $("#o_venta_igv").val("");

  $(".o_venta_total").html("<span>S/</span> 0.00");
  $("#o_venta_total").val("");

  $(".filas_producto_agregado").remove();  

  cont = 0;  

  // $('.input_cantidad_venta').each(function(e) { $(this).rules("remove"); });  
  // $('.input_precio_con_igv').each(function(e) { $(this).rules("remove"); });  
  // $('.input_f_descuento').each(function(e) { $(this).rules("remove"); });  

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function guardar_editar_orden_venta(e) {

  if ($('#productos-seleccionados li').length == 0) {
    toastr_info('Vacio', 'Porfavor selecione algun producto para guardar la orden de venta.');
    return;
  }

  var formData = new FormData($("#form-agregar-nueva-orden")[0]);  

  Swal.fire({
    title: "¿Está seguro que deseas guardar esta ORDEN?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/orden_venta.php?op=guardar_editar_orden", {
        method: 'POST', // or 'PUT'
        body: formData, // data can be `string` or {object}!        
      }).then(response => {
        //console.log(response);
        if (!response.ok) { throw new Error(response.statusText) }
        return response.json();
      }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); });
    },
    showLoaderOnConfirm: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (result.value.status == true){
        Swal.fire("Correcto!", "Orden registrado correctamente", "success");        
        limpiar_form_orden(); 
        
      } else if ( result.value.status == 'error_personalizado'){        
        
        limpiar_form_orden(); ver_errores(result.value);
      } else if ( result.value.status == 'error_usuario'){    
        ver_errores(result.value);      
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         A G R E G A R   N U E V O   C L I E N T E                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function limpiar_nuevo_cliente() {
  // choice_tipo_documento.setChoiceByValue('1').passedElement.element.dispatchEvent(new Event('change'));
  $('#cli_tipo_documento').val('0').trigger('change');  
  $('#cli_nombre_razonsocial').val('');
  $('#cli_apellidos_nombrecomercial').val('');
  $('#cli_correo').val('');
  $('#cli_celular').val('');
  $('#cli_direccion').val('');
  $('#cli_direccion_referencia').val('');
  $('#cli_distrito').val('TARAPOTO').trigger('change');  

}

function modal_add_nuevo_cliente() {
  limpiar_nuevo_cliente();
  $('#modal-agregar-nuevo-cliente').modal('show');
}

function guardar_editar_nuevo_cliente(e){ 
  var formData = new FormData($("#form-agregar-nuevo-cliente")[0]);
  $.ajax({
    url: "../ajax/orden_venta.php?op=guardar_editar_cliente",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); 
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");          
          lista_select2("../ajax/orden_venta.php?op=select2_cliente", '#o_idcliente', e.data, '.charge_o_idcliente');
          limpiar_nuevo_cliente();
          $('#modal-agregar-nuevo-cliente').modal('hide');
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_nuevo_cliente").html('Guardar Cambios').removeClass('disabled send-data');
    },
    xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					$("#barra_progress_nuevo_cliente").css({ "width": percentComplete + '%' });
					$("#barra_progress_nuevo_cliente div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
    beforeSend: function () {
      $("#guardar_registro_nuevo_cliente").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_nuevo_cliente").css({ width: "0%", });
      $("#barra_progress_nuevo_cliente div").text("0%");
      $("#barra_progress_nuevo_cliente_div").show();
    },
    complete: function () {
      $("#barra_progress_nuevo_cliente").css({ width: "0%", });
      $("#barra_progress_nuevo_cliente div").text("0%");
      $("#barra_progress_nuevo_cliente_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

$('#cli_tipo_documento').change(function () {
  var tipo = $(this).val() == '' ||  $(this).val() == null ? '' : $(this).val() ;

  if ( tipo == '0' ) { 
    $("#cli_numero_documento").val('00000000').addClass('bg-dark-transparent').prop('readonly', true).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('#cli_apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }else if (tipo == '1') {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }else if (tipo == '6') {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_apellidos_nombrecomercial').rules('remove', 'required');
    $('.label-nom-raz').html('Razón Social <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Nombre comercial <sup class="text-danger">*</sup>');
    $("#cli_numero_documento").rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_tipo_persona_sunat').val('JURÍDICA');
  } else {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false);
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }

  $("#form-agregar-nuevo-cliente").valid();

});

$('#cli_distrito').on('change', async function () {  

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);

  await esperar(1000);  // Espera 5 segundos  

  if ($('#cli_distrito').val()== null || $('#cli_distrito').val() == '') {
    $("#departamento").val("");
    $("#provincia").val("");
    $("#ubigeo").val("");
    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito = $('#cli_distrito').select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {
      e = JSON.parse(e); 
      $("#cli_departamento").val(e.data.departamento);
      $("#cli_provincia").val(e.data.provincia);
      $("#cli_ubigeo").val(e.data.ubigeo_inei);

      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      
      $("#form-agregar-nuevo-cliente").valid();
    });
  }
});


function generarcodigonarti() {
  var name_producto =
    $("#nombre").val() == null || $("#nombre").val() == ""
      ? ""
      : $("#nombre").val();
  if (name_producto == "") {
    toastr_warning(
      "Vacio!!",
      "El nombre esta vacio, digita para completar el codigo aletarorio.",
      700
    );
  }
  name_producto = name_producto.substring(-3, 3);
  var cod_letra = Math.random().toString(36).substring(2, 5);
  var cod_number =
    Math.floor(Math.random() * 10) + "" + Math.floor(Math.random() * 10);
  $("#codigo_alterno").val(
    `${name_producto.toUpperCase()}${cod_number}${cod_letra.toUpperCase()}`
  );
}

function create_code_producto(pre_codigo) {
  $(".charge_codigo").html(
    `<div class="spinner-border spinner-border-sm" role="status"></div>`
  );

  $.getJSON(
    `../ajax/ajax_general.php?op=create_code_producto&pre_codigo=${pre_codigo}`,
    function (e, textStatus, jqXHR) {
      if (e.status == true) {
        $("#codigo").val(e.data.nombre_codigo);
        $("#codigo").attr("readonly", "readonly").addClass("bg-light"); // Asegura que el campo esté como solo lectura
        add_tooltip_custom("#codigo", "No se puede editar"); //  Agrega tooltip personalizado a un element
        $(".charge_codigo").html(""); // limpiamos la carga
      } else {
        ver_errores(e);
      }
    }
  ).fail(function (jqxhr, textStatus, error) {
    ver_errores(jqxhr);
  });
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         A G R E G A R   P R O D U C T O                                                                  ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function seleccionar_producto(idproducto_sucursal,idproducto_presentacion) {

  $.getJSON(`../ajax/orden_venta.php?op=listar_producto_select_pedido`,  { precio_por_mayor:precio_por_mayor,idproducto_sucursal:idproducto_sucursal,idproducto_presentacion:idproducto_presentacion },  function (e, textStatus, jqXHR) {
    
    if (e.status == true) {
      if ( e.data.stock_presentacion_entero >= 1 ) {

        if ( $(`#productos-seleccionados li.idproducto_presentacion_${e.data.idproducto_presentacion}`).length >= 1  ) { 
          
          toastr_success("Agregado!!",`Producto: ${e.data.nombre_presentacion} agregado !!`, 700);
          cambiarCantidad('mas', 1,`${e.data.idproducto}_${e.data.idproducto_presentacion}`);           

        } else { 
          toastr_success("Agregado!!", `Producto Agregado Correctamente.`, 50 );

          var es_precio_por_mayor   = $('#precio_por_mayor').is(':checked') ? 'SI' : 'NO';
          var precio_venta          = parseFloat(e.data.precio_venta) || 0 ;
          var precio_por_mayor      = parseFloat(e.data.precio_por_mayor) || 0 ;
          var precio                = es_precio_por_mayor == 'SI' ? precio_por_mayor : precio_venta ;
          var cantidad_presentacion = parseFloat(e.data.cantidad_presentacion) || 0;
          var subtotal              = precio * cantidad_presentacion;
         

          $("#productos-seleccionados").append(`<li class="list-group-item border-top-0 border-start-0 border-end-0 py-3 filas_producto_agregado idproducto_presentacion_${e.data.idproducto_presentacion}" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-start">

              <input type="hidden" name="idproducto_presentacion[]" value="${e.data.idproducto_presentacion}">

              <input type="hidden" name="pr_marca[]"          value="${e.data.marca}">
              <input type="hidden" name="pr_categoria[]"      value="${e.data.categoria}">              
              <input type="hidden" name="precio_por_mayor[]"  value="${es_precio_por_mayor}">
              <input type="hidden" name="um_nombre[]"         value="UNIDADES">
              <input type="hidden" name="um_abreviatura[]"    value="NIU">


              <input type="hidden" class="input_stock"                  name="stock[]"                  value="${e.data.stock_presentacion_entero}">
              <input type="hidden" class="input_cantidad_presentacion"  name="cantidad_presentacion[]"  value="${e.data.cantidad_presentacion}">
              
              <input type="hidden" class="input_precio_sin_igv"         name="precio_sin_igv[]"         value="${precio}">
              <input type="hidden" class="input_precio_igv"             name="precio_igv[]"             value="0">
              <input type="hidden" class="input_precio_compra"          name="precio_compra[]"          value="${e.data.precio_compra}">
              <input type="hidden" class="input_precio_venta_descuento" name="precio_venta_descuento[]" value="${precio}">
              <input type="hidden" class="input_precio_por_mayor"       name="precio_por_mayor[]"       value="${es_precio_por_mayor}">

              <input type="hidden" class="input_descuento"              name="descuento[]"               value="0">
              <input type="hidden" class="input_descuento_porcentaje"   name="descuento_porcentaje[]"    value="0">

              <!-- Imagen y datos -->
              <div class="d-flex">
                <div class="me-3" data-bs-toggle="tooltip" title="Ver imagen" style="cursor: pointer;">
                  <span class="avatar">
                    <img src="../assets/modulo/productos/${e.data.imagen}" alt="" class="rounded" style="width: 400px; height: 40px; object-fit: cover;">
                  </span>
                </div>
                <div>
                  <div >
                    <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${e.data.idproducto_presentacion}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${e.data.nombre_producto_presentacion}</textarea>                      
                    <div class="span_pr_nombre_${e.data.idproducto_presentacion}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${e.data.idproducto_presentacion}', '.textarea_pr_nombre_${e.data.idproducto_presentacion}')" >${e.data.nombre_producto_presentacion}</span> </div>
                  </div>
                  <div class="text-muted fs-10">
                      Marca: <b>${e.data.marca}</b> | Precio Und: S/. <b class="text-primary fw-semibold" >${e.data.precio}</b>
                  </div>
                </div>
              </div>
              <!-- Botón de eliminar -->
              <div>
                <span class="p-1 badge bg-danger m-r-4px cursor-pointer" onclick="eliminarItem('.idproducto_presentacion_${e.data.idproducto_presentacion}')" title="Quitar">
                  <i class="bi bi-trash3"></i>
                </span>
              </div>
            </div>
            <div class="mt-2 d-flex justify-content-between">
              <div class="input-group input-group-sm me-2" style="width: 150px;">
                <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad('menos', -1,'${e.data.idproducto_presentacion}')">-</button>
                <input type="text" class="form-control px-1 input_cantidad_venta" name="cantidad_venta[]"  value="1" min="1" onkeyup="cambiarCantidad('mas', null,'${e.data.idproducto_presentacion}');" style="width: 50px;">
                <input type="hidden" class="input_cantidad_total" name="cantidad_total[]" value="${e.data.cantidad_presentacion}">
                <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad('mas', 1,'${e.data.idproducto_presentacion}')">+</button>
              </div>
              
              <input type="number" class="input_precio_con_igv"         name="precio_con_igv[]"         value="${precio}" onkeyup="modificarSubtotales();">

              <span class="fw-semibold fs-14 text-dark span_subtotal_x_producto text-right w-100px" data-value="${subtotal}">S/.  ${ formato_miles(subtotal) }</span>
              <input type="hidden" class="input_subtotal_x_producto"  name="subtotal_x_producto[]" value="${ formato_miles(subtotal)}">
              <input type="hidden" class="input_subtotal_no_descuento_producto"  name="subtotal_no_descuento_producto[]" value="${ formato_miles(subtotal)}">
            </div>
          </li>`);

          
          modificartotales();
        }
      } else {
        toastr_warning( "Alerta!!", `No Tienes Sufuciente Stock .`, 700 );
      }

    }
  });

}

function cambiarCantidad(btn, delta,id) {
  console.log(btn, delta,id);
  
  var stock                 = parseFloat($(`li.idproducto_presentacion_${id} .input_stock`).val()) || 0;
  var cantidad_venta        = parseFloat($(`li.idproducto_presentacion_${id} .input_cantidad_venta`).val()) || 0;
  var cantidad_presentacion = parseFloat($(`li.idproducto_presentacion_${id} .input_cantidad_presentacion`).val()) || 0;
  var cantidad_total        = 0;
  var nueva_cantidad_venta  = 0;

  if (delta == null) {
    nueva_cantidad_venta = cantidad_venta;
    cantidad_total = (cantidad_venta * cantidad_presentacion);
  } else {
    nueva_cantidad_venta = (cantidad_venta + delta) ;
    cantidad_total = ( nueva_cantidad_venta * cantidad_presentacion);
  }

  console.log(`stock: ${stock}`);
  console.log(`cantidad_venta: ${cantidad_venta}`);
  console.log(`cantidad_presentacion: ${cantidad_presentacion}`);
  console.log(`nueva_cantidad_venta: ${nueva_cantidad_venta}`);
  console.log(`cantidad_total: ${cantidad_total}`);
   
  if ( nueva_cantidad_venta == 0 ) {
    toastr_warning("Alerta!!", `No puedes tener cantidad 0, ingresa una cantidad mayor o igual a 1.`, 50);
  } else if (stock >= nueva_cantidad_venta) {
    $(`li.idproducto_presentacion_${id} .input_cantidad_venta`).val(nueva_cantidad_venta);
    $(`li.idproducto_presentacion_${id} .input_cantidad_total`).val(cantidad_total);
  } else {
    toastr_warning("Alerta!!", `No tienes suficiente Stock.`, 50);
    $(`li.idproducto_presentacion_${id} .input_cantidad_venta`).val(stock)
    
  }
  modificarSubtotales();
}

function modificarSubtotales() {
  var val_igv = parseFloat($("#o_impuesto").val()) || 0;

  $('#productos-seleccionados li').each(function () {
    // Obtener los valores de cantidad y precio_unitario de los inputs
    let cantidad_venta            = parseFloat($(this).find('.input_cantidad_venta').val()) || 0;
    let cantidad_presentacion     = parseFloat($(this).find('.input_cantidad_presentacion').val()) || 0;
    let precio_con_igv            =  parseFloat($(this).find('.input_precio_con_igv').val()) || 0; console.log(precio_con_igv);    
    let descuento                 =  parseFloat($(this).find('.input_descuento').val()) || 0;
    let stock                     =  parseFloat($(this).find('.input_stock').val()) || 0;
    var subtotal_no_descuento_producto         = 0;
    var subtotal_producto_no_dcto = 0;
    var cantidad_total            = 0;

    var precio_sin_igv = 0;
    var igv = 0;     
          
    if (val_igv > 0) {          
      precio_sin_igv = ( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal')).toFixed(2);   // Calculamos: precio sin IGV          
      igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);                 // Calculamos:  IGV            
    } else {                 
      precio_sin_igv = precio_con_igv;                                                            // Calculamos: precio sin IGV    
    }     

    console.log(precio_sin_igv);    

    $(this).find('.input_precio_sin_igv').val(precio_sin_igv);
    $(this).find('.input_precio_igv').val(igv);

    // Calculamos: Subtotal de cada producto
    subtotal_producto_no_dcto = (cantidad_venta * cantidad_presentacion) * parseFloat(precio_con_igv);
    subtotal_producto = (cantidad_venta * cantidad_presentacion) * parseFloat(precio_con_igv) - descuento;

    // Calculamos: precio unitario descontado
    var precio_unitario_dscto = subtotal_producto / (cantidad_venta * cantidad_presentacion);      
    $(this).find('.input_precio_venta_descuento').val(redondearExp(precio_unitario_dscto, 2 ));

    // Calculamos: porcentaje descuento
    var porcentaje_monto = descuento / subtotal_producto_no_dcto;      
    $(this).find('.input_descuento_porcentaje').val(redondearExp(porcentaje_monto, 2 ));       
    
    $(this).find('.span_subtotal_x_producto').html(formato_miles(subtotal_producto)).attr('data-value', subtotal_producto);
    $(this).find('.input_subtotal_x_producto').val(redondearExp(subtotal_producto, 2 ));
    $(this).find('.input_subtotal_no_descuento_producto').val(redondearExp(subtotal_producto_no_dcto, 2 ));
    $(this).find('.input_cantidad_total').val( (cantidad_venta * cantidad_presentacion) );

  });

  modificartotales();
}

function eliminarItem(index) {

  $(index).remove();  
  toastr_success("Eliminado!!",`Producto Eliminado Correctamente.`, 50);
  modificartotales();

}

function modificartotales() {

  var total = 0.0;
  var igv = 0;
  var descuento = 0;

  if ( $('#productos-seleccionados li').length >= 1  ) {
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

  $(".pago_rapido").html(formato_miles(total));
  $(".pago_rapido").html(formato_miles(total));
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         F O R M   V A L I D A T E                                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

$(document).ready(function () {
  init();
});

$(function () {
  $("#categoria").on("change", function () {$(this).trigger("blur");});
  $("#u_medida").on("change", function () {$(this).trigger("blur");});
  $("#marca").on("change", function () {$(this).trigger("blur");});

  //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  $("#form-agregar-nuevo-cliente").validate({
    ignore: "",
    rules: {           
      cli_tipo_documento:           { required: true, minlength: 1, maxlength: 2, },       
      cli_numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      cli_nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      cli_apellidos_nombrecomercial:{ required: true, minlength: 4, maxlength: 200, },       
      cli_correo:    			          { minlength: 4, maxlength: 100, },       
      cli_celular:    			        { minlength: 8, maxlength: 9, },       

      cli_direccion:    			      { minlength: 4, maxlength: 200, },       
      cli_direccion_referencia:     { minlength: 4, maxlength: 200, }, 			      
      cli_distrito:    			        { required: true, }, 			
     
    },
    messages: {     
      cli_tipo_documento:    			  { required: "Campo requerido", },
      cli_numero_documento:    			{ required: "Campo requerido", }, 
      cli_nombre_razonsocial:    		{ required: "Campo requerido", }, 
      cli_apellidos_nombrecomercial:{ required: "Campo requerido", }, 
      cli_correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      cli_celular:    			        { minlength: "Mínimo {0} caracteres.", }, 

      cli_direccion:    			      { minlength: 4, maxlength: 200, },       
      cli_direccion_referencia:     { minlength: 4, maxlength: 200, }, 			      
      cli_distrito:    			        { required: "Campo requerido", }, 
    },
        
    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");   
    },
    submitHandler: function (e) {
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600); // Scrollea hasta abajo de la página
      guardar_editar_nuevo_cliente(e);      
    },
  });

  $("#form-agregar-nueva-orden").validate({
    ignore: "",
    rules: {           
      o_idcliente:           { required: true, minlength: 1, maxlength: 2, },      
    },
    messages: {     
      o_idcliente:    			  { required: "Campo requerido", },      
    },
        
    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");   
    },
    submitHandler: function (e) {
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600); // Scrollea hasta abajo de la página
      guardar_editar_orden_venta(e);      
    },
  });

  $("#categoria").rules("add", { required: true,messages: { required: "Campo requerido" },});
  $("#u_medida").rules("add", {required: true,messages: { required: "Campo requerido" },});
  $("#marca").rules("add", {required: true,messages: { required: "Campo requerido" },});
  $("#ubicacion").rules("add", {required: true,messages: { required: "Campo requerido" },});
  
});


// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         F U N C I O N E S   A L T E R N A S                                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function reload_filtro_ubicacion() {
  lista_select2(
    "../ajax/producto_cat_ubicacion.php?op=select2_filtro_ubicacion",
    "#filtro_ubicacion",
    null,
    ".charge_filtro_ubicacion"
  );
}

$(document).ready(function () {
  $("#search_producto").on("keyup", function () {
    let query = $(this).val();
    precio_por_mayor = $("#precio_por_mayor").is(":checked") ? "SI" : "NO";

    if (query.length >= 2) {
      $.getJSON(
        `../ajax/orden_venta.php?op=listar_producto_x_nombre`,
        { search: query },
        function (e, textStatus, jqXHR) {
          let $resultsList = $("#searchResults");
          $resultsList.empty();

          if (e.data.length > 0) {
            e.data.forEach(function (val, key) {
              $resultsList.append(`<div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-2" style="cursor: pointer;">
                      <div class="card custom-card product-card" onclick="seleccionar_producto(${val.idproducto_sucursal},${val.idproducto_presentacion}, '${val.imagen}')">
                        <div class="card-body">
                          <div class="product-image h-200px text-center" >
                            <img src="../assets/modulo/productos/${val.imagen}" class="card-img mb-3" alt="..." style="max-width: 100%; max-height: 100%; object-fit: contain;">
                          </div>
                          <p class="product-name text-muted  mb-0 d-flex align-items-center justify-content-between">${ val.nombre_presentacion}<span class="float-end text-warning fs-12"><i class="ri-star-s-fill align-middle ms-1 d-inline-block"></i></span></p>
                          <p class="product-description fs-11 fw-semibold mb-2">${val.nombre_producto}</p>
                          <p class="mb-1 fw-semibold fs-16 precioxmayor"><span>S/. ${formato_miles(val.precio_por_mayor* val.cantidad_presentacion)}</span></p>
                          <p class="mb-1 fw-semibold fs-16 precioxmenor"><span>S/. ${ formato_miles(val.precio_venta * val.cantidad_presentacion) }</span></p>
                          <p class="fs-9 text-success fw-semibold mb-0 d-flex align-items-center">
                            Marca : ${val.marca} 
                          </p>
                          <p class="fs-11 mt-2 text-success fw-semibold mb-0 d-flex align-items-center">
                            Stock : ${parseFloat(val.stock_total)} - ${parseFloat(val.stock_presentacion_entero)} ${val.abreviatura} 
                          </p>
                        </div>
                      </div>
                    </div>
            `);
            });

            // Mostrar solo el precio correcto
            if (precio_por_mayor === "SI") {
              $(".precioxmayor").show();
              $(".precioxmenor").hide();
            } else {
              $(".precioxmayor").hide();
              $(".precioxmenor").show();
            }
          } else {
            $resultsList.append(
              `<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`
            );
          }

          $resultsList.show();
        }
      );
    } else {
      $("#searchResults").hide();
    }
  });

  // ✅ Cambio dinámico al marcar/desmarcar el checkbox
  $("#precio_por_mayor").on("change", function () {
    precio_por_mayor="SI";
    if ($(this).is(":checked")) {
      $(".precioxmayor").show();
      $(".precioxmenor").hide();
    } else {
      precio_por_mayor="NO";
      $(".precioxmayor").hide();
      $(".precioxmenor").show();
    }
  });
});



function mayus(e) {
  e.value = e.value.toUpperCase();
}

function esperar(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function reload_o_idcliente(){ lista_select2("../ajax/orden_venta.php?op=select2_cliente","#o_idcliente",'1', '.charge_o_idcliente'); }


