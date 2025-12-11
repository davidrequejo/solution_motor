var tabla_productos;
var tabla_productos_g;
var array_data_lp = [];
var detalles = 0;
var cont = 0;
var cantidad = 1;

var idproducto_ps_r = 0;

function init(){

  // listar_tabla();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-producto").submit(); }  });

	$("#guardar_registro_categoria").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-categoria").submit(); } });
	$("#guardar_registro_marca").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-marca").submit(); } });
	$("#guardar_registro_u_m").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-u-m").submit(); } });
	$("#guardar_registro_ubicacion").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-ubicacion").submit(); } });
	$("#guardar_registro_presentacion").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-ps").submit(); } });
	$("#enviar_impresion").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-imp").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', null);
  lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida', null);
  lista_select2("../ajax/producto.php?op=select_u_medida", '#um_presentacion', null);
  lista_select2("../ajax/producto.php?op=select_marca", '#marca', null);
  lista_select2("../ajax/producto_cat_ubicacion.php?op=select_prod_cat_ubicacion", '#ubicacion', null);

  lista_select2("../ajax/producto.php?op=select2_filtro_categoria", '#filtro_categoria', null);
  lista_select2("../ajax/producto.php?op=select2_filtro_u_medida", '#filtro_unidad_medida', null);
  lista_select2("../ajax/producto.php?op=select2_filtro_marca", '#filtro_marca', null);
  lista_select2("../ajax/producto_cat_ubicacion.php?op=select2_filtro_ubicacion", '#filtro_ubicacion', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════ 
  $("#filtro_categoria").select2({  theme: "bootstrap4", placeholder: "Seleccione categoria", allowClear: true, });
  $("#filtro_unidad_medida").select2({  theme: "bootstrap4", placeholder: "Seleccione unidad medida", allowClear: true, });
  $("#filtro_marca").select2({  theme: "bootstrap4", placeholder: "Seleccione marca", allowClear: true, });
  $("#filtro_ubicacion").select2({  theme: "bootstrap4", placeholder: "Seleccione ubicación", allowClear: true, });

  $("#categoria").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#u_medida").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#marca").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#ubicacion").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  //$("#um_presentacion").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  P R O D U C T O                                                                ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function limpiar_form_producto(){

	$('#idproducto').val('');
  
	$('#tipo').val('PR');
	$('#codigo').val('');
	$('#codigo_alterno').val('');
	$('#categoria').val('1').trigger('change');
	$('#ubicacion').val('1').trigger('change');
	$('#u_medida').val('58').trigger('change'); // por defecto: NIU
	$('#marca').val('1').trigger('change');
	$('#nombre').val('');
	$('#descripcion').val('');
	$('#stock').val('');
	$('#stock_min').val('1');
	$('#precio_v').val('');
	$('#precio_c').val('');
  $("#cant_um").val('1');
	$('#precio_x_mayor').val(''); 
	$('#idproducto_sucursal').val(''); 
	$('#idproducto_presentacion').val(''); 

  $("#imagenProducto").val("");
  $("#imagenactualProducto").val("");
  $("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png");
  $("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png").show();
  var imagenMuestra = document.getElementById('imagenmuestraProducto');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/productos/no-producto.png';
  }

  array_data_lp = [];
  $(".l_total_prod").html('<span>S/</span> 0.00');
  $("#l_total_prod").val(0.00);

  $(".filas").remove();
  cont = 0;


  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function show_hide_form(flag) {
	if (flag == 1) {
    $(".card-header").show();
		$("#div-tabla").show();
		$(".div-form").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) {
    $(".card-header").hide();
		$("#div-tabla").hide();
		$(".div-form").show();

		$(".btn-agregar").hide();
		$(".btn-guardar").show();
		$(".btn-cancelar").show();
		$("#list-productos").hide();

	} else if (flag == 3) {
    $(".card-header").hide();
		$("#div-tabla").hide();
		$(".div-form").show();

		$(".btn-agregar").hide();
		$(".btn-guardar").show();
		$(".btn-cancelar").show();
    $("#list-productos").show();
	}
}

function listar_tabla(filtro_categoria = '', filtro_unidad_medida = '', filtro_marca = '', filtro_ubicacion = ''){
  tabla_productos = $('#tabla-productos').dataTable({
    responsive: false, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    // dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-9 col-xxl-10 pt-2'f><'col-md-5 col-lg-4 col-xl-3 col-xxl-2 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>",
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,13,14,12,10,11,4,5,6,7,8], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,13,14,12,10,11,4,6,7,8], }, title: 'Lista de Productos', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,13,14,12,10,11,4,6,7,8], }, title: 'Lista de Productos', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: `../ajax/producto.php?op=listar_tabla&categoria=${filtro_categoria}&unidad_medida=${filtro_unidad_medida}&marca=${filtro_marca}&ubicacion=${filtro_ubicacion}`,
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			},
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-copy").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Copiar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-pdf").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'PDF');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('.buscando_tabla').hide()
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-nowrap text-center"); }
      // columna: #
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }      
      // columna: 5
      if (data[16] == 1 ) { $("td", row).eq(1).attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'No tienes opcion a modificar'); }
    },
    language: {
      lengthMenu: "_MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      search: "",
    },
    initComplete: function () {

      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs:[
      { targets: [10,11,12,13,14,15],  visible: false,  searchable: false,  },
    ],
  }).DataTable();
}

function guardar_editar_producto(e){
  var formData = new FormData($("#form-agregar-producto")[0]);

	$.ajax({
		url: "../ajax/producto.php?op=guardar_editar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (e) {
			try {
				e = JSON.parse(e);
        if (e.status == true) {	
					sw_success('Exito', 'Producto guardado correctamente.');
					tabla_productos.ajax.reload(null, false);          
					show_hide_form(1);
          limpiar_form_producto();
          tabla_productos_g = [];
				} else {
					ver_errores(e);
				}				
			} catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!",'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }      
      $(".btn-guardar").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar').removeClass('disabled send-data');
		},
		xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					$("#barra_progress_producto").css({ "width": percentComplete + '%' });
					$("#barra_progress_producto div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$(".btn-guardar").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
			$("#barra_progress_producto").css({ width: "0%", });
			$("#barra_progress_producto div").text("0%");
      $("#barra_progress_producto_div").show();
		},
		complete: function () {
			$("#barra_progress_producto").css({ width: "0%", });
			$("#barra_progress_producto div").text("0%");
      $("#barra_progress_producto_div").hide();
		},
		error: function (jqXhr, ajaxOptions, thrownError) {
			ver_errores(jqXhr);
		}
	});
}

function mostrar_producto(idproducto, duplicar = false){
  limpiar_form_producto(); show_hide_form(2);
	$('#cargando-1-fomulario').hide(); $('#cargando-2-fomulario').show(); 
	$.post("../ajax/producto.php?op=mostrar", { idproducto: idproducto }, function (e, status) {
		e = JSON.parse(e);

    if (duplicar == true) {  

    } else {
      $('#idproducto').val(e.data.producto.idproducto);
      $('#idproducto_sucursal').val(e.data.producto.idproducto_sucursal);
      $('#idproducto_presentacion').val(e.data.producto.idproducto_presentacion);
    }
		
    $('#categoria').val(e.data.producto.idproducto_categoria).trigger('change');
    $('#u_medida').val(e.data.producto.idsunat_c03_unidad_medida).trigger('change');
    $('#marca').val(e.data.producto.idproducto_marca).trigger('change');
    $('#ubicacion').val(e.data.producto.idproducto_categoria_ubicacion).trigger('change');

    $('#codigo').val(e.data.producto.codigo);
    $('#codigo_alterno').val(e.data.producto.codigo_alterno);
    $('#nombre').val(e.data.producto.nombre);
    $('#descripcion').val(e.data.producto.descripcion);
    $('#stock').val(e.data.producto.stock);
    $('#stock_min').val(e.data.producto.stock_minimo);
    $('#precio_v').val(e.data.producto.precio_venta);
    $('#precio_c').val(e.data.producto.precio_compra);
    $('#cant_um').val('1');
    $('#precio_x_mayor').val(e.data.producto.precio_por_mayor);

    $("#imagenmuestraProducto").show();
		$("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/" + e.data.producto.imagen);
		$("#imagenactualProducto").val(e.data.producto.imagen);

    /*if(Array.isArray(e.data.grupo) && e.data.grupo.length > 0){

      $('#idproducto_n').val(e.data.grupo[0].idproducto_n);
      $("#list-productos").show();

      e.data.grupo.forEach(function(producto_g) {
        var subtotal = cantidad * producto_g.precio_venta;          
        var img = producto_g.imagen == "" || producto_g.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${producto_g.imagen}` ;          
        
        var u_medida = '';
        var um_nombres = producto_g.unidad_medida.split(',').map(item => item.trim());
        var id_um = producto_g.idpresentacion.split(',').map(item => item.trim());
        var cantidad_pp = producto_g.cantidad_medida.split(',').map(item => parseFloat(item.trim()));

        if (um_nombres.length > 1) {
          u_medida += `<select class="form-control" name="um_presentation[]" id="um_presentation_${cont}">`;
          for (let i = 0; i < um_nombres.length; i++) {
            u_medida += `<option value="${cantidad_pp[i]}" data-idum="${id_um[i]}">${um_nombres[i]}</option>`;
          }
          u_medida += '</select>';
          let idum_selected =  id_um[0];
          u_medida += `<input type="hidden" name="idproducto_presentacion_set[]" id="idproducto_presentacion_set_${cont}" value="${idum_selected}">`;
        } else {
          u_medida = `
          <span class="fs-11 um_nombre_${cont}">${producto_g.unidad_medida}</span>
          <input type="hidden" name="um_presentation[]" id="um_presentation_${cont}" value="${cantidad_pp}">
          <input type="hidden" name="idproducto_presentacion_set[]" id="idproducto_presentacion_set_${cont}" value="${id_um[0]}">`;
        }

        var fila = `
        <tr class="filas" id="fila${cont}"> 

          <td class="py-1">
            <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarProdSeleccionado(${producto_g.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
          </td>

          <td class="py-1 text-nowrap">
            <span class="fs-11" ><i class="bi bi-upc"></i> ${producto_g.codigo} <br> <i class="bi bi-person"></i> ${producto_g.codigo_alterno}</span> 
          </td>

          <td class="py-1 px-0">         
            <input type="hidden" name="idproducto_set[]" id="idproducto_set[]" value="${producto_g.idproducto}">

            <div class="d-flex flex-fill align-items-center">
              <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(producto_g.nombre)}')"> </span></div>
              <div>
                <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${producto_g.nombre}</textarea>
                <div class="w-250px span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${producto_g.nombre}</span> </div>
                <span class="d-block fs-9 text-muted">M: <b>${producto_g.marca}</b> | C: <b>${producto_g.categoria}</b></span> 
              </div>
            </div>
          </td>

          <td class="py-1">
            ${u_medida}
          </td>             

          <td class="py-1 form-group">
            <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${producto_g.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${producto_g.cantidad_asociado}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); update_price(); ">
            <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${producto_g.cantidad_asociado}" min="0.01" required onkeyup="modificartotal();" onchange="modificartotal();" >            
          </td> 

          <td class="py-1 form-group">
            <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${producto_g.precio_venta}" min="0.01" required readonly>
            <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${producto_g.precio_venta}" onkeyup="modificartotal();" onchange="modificartotal();">              
            <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
            <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
            <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${producto_g.precio_compra}"  >
            <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${producto_g.precio_venta}"  >
            <input type="hidden" class="precio_venta_original_${cont}" name="precio_venta_original[]" value="${producto_g.precio_venta}"  >
          </td> 

          <td class="py-1 text-right">
            <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> 
            <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
          </td>
          <td class="py-1"><button type="button" onclick="modificartotal();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
          
        </tr>`;

        detalles = detalles + 1;
        $("#tabla-productos-seleccionados tbody").append(fila);
        array_data_lp.push({ id_cont: cont });
        cont++;
      });
      modificartotal();
    }*/

    $('#cargando-1-fomulario').show();	$('#cargando-2-fomulario').hide();
    $('#form-agregar-producto').valid();
	});	
}

function mostrar_detalle_producto(idproducto){
  $("#modal-ver-detalle-producto").modal('show');
  $.post("../ajax/producto.php?op=mostrar_detalle_producto", { idproducto: idproducto }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {

      $("#html-detalle-producto").html(e.data);
      $("#html-detalle-imagen").html(doc_view_download_expand(e.imagen, 'assets/modulo/productos/', e.nombre_doc, '100%', '400px'));
      
    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_papelera(idproducto, nombre){
  crud_eliminar_papelera(
    "../ajax/producto.php?op=dasactivar",
    "../ajax/producto.php?op=eliminar", 
    idproducto, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_productos.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function eliminar_papelera_producto(idproducto, nombre) {
  $(".tooltip").remove();

  $.post("../ajax/producto.php?op=mostrar_eliminar_papelera", { idproducto: idproducto }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      // Generar el HTML con los radios dinámicos
      let lista_presentacion = "";
      e.data.forEach((item, index) => {
        let label = index === 0 ? "Producto Base" : item.unidad_medida;
        lista_presentacion += `
          <div class="form-check mt-1" style="text-align:left; margin-left:20px;">
            <input class="form-check-input" type="radio" name="presentacion" 
              id="presentacion_${index}" 
              value="${item.idproducto_presentacion}||${label}" 
              ${index === 0 ? "checked" : ""}>
            <label class="form-check-label" for="presentacion_${index}"> ${label} </label>
          </div>
        `;
      });

      // Mostrar alerta con radios dinámicos
      Swal.fire({
        title: "!Elija una opción¡",
        html: `
          <b class="text-danger"><del>${nombre}</del></b><br>
          En <b>papelera</b> encontrará este registro.<br>
          Al <b>eliminar</b> no tendrá acceso a recuperar este registro.<br><br>
          <div class="alert alert-warning m-2" role="alert">
            <strong>Seleccione una presentación:</strong><br>
            ${lista_presentacion}
          </div>
        `,
        icon: "warning",
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: "#17a2b8",
        denyButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: `<i class="fas fa-times"></i> Papelera`,
        denyButtonText: `<i class="fas fa-skull-crossbones"></i> Eliminar`,
        showLoaderOnConfirm: true,
        preConfirm: () => {
          const selected = document.querySelector('input[name="presentacion"]:checked');
          if (!selected) {
            Swal.showValidationMessage('Debe seleccionar una presentación');
            return;
          }
          const [idproducto_presentacion, label] = selected.value.split("||");

          return fetch(`../ajax/producto.php?op=papelera&idproducto=${idproducto}&idproducto_presentacion=${idproducto_presentacion}&nombrepresentacion=${encodeURIComponent(label)}`)
            .then(response => {
              if (!response.ok) throw new Error(response.statusText);
              return response.json();
            })
            .catch(error => {
              Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`);
            });
        },
        showLoaderOnDeny: true,
        preDeny: () => {
          const selected = document.querySelector('input[name="presentacion"]:checked');
          if (!selected) {
            Swal.showValidationMessage('Debe seleccionar una presentación');
            return;
          }
          const [idproducto_presentacion, label] = selected.value.split("||");

          return fetch(`../ajax/producto.php?op=eliminar&idproducto=${idproducto}&idproducto_presentacion=${idproducto_presentacion}&nombrepresentacion=${encodeURIComponent(label)}`)
            .then(response => {
              if (!response.ok) throw new Error(response.statusText);
              return response.json();
            })
            .catch(error => {
              Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.isConfirmed || result.isDenied) {
          if (result.value.status === true) {
            if (result.isConfirmed) {
              sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado.");
            } else {
              sw_success('Eliminado!', 'Tu registro ha sido Eliminado.');
            }

            if (typeof tabla_productos !== "undefined") {
              tabla_productos.ajax.reload(null, false);
            }
            $(".tooltip").remove();
          } else {
            ver_errores(result.value);
          }
        }
      });

    } else {
      ver_errores(e);
    }
  }).fail(function (e) {
    ver_errores(e);
  });
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  I M P R I M I R  C O D I G O                                                   ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════


function mostrar_imprimir_codigo(idproducto){
  $("#modal-form-codigo").modal('show');

  $.post("../ajax/producto.php?op=mostrar_producto", { idproducto: idproducto }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {

      $("#idproducto").val(e.data.idproducto);
      $("#codg").val(e.data.codigo_alterno);

    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );

}

function limpiar_form_imprimir(){
  $("#enviar_impresion").html('Guardar Cambios').removeClass('disabled');

  $("#idproducto").val("");
  $("#codg").val("");
  $("#cant_cg").val("");
  $("#dim_x").val("");
  $("#dim_y").val("");

  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_imprimir_codigo(e) {

  // Obtener valores del formulario
  const codigo = $("#codg").val();
  const cantidad = parseInt($("#cant_cg").val());
  const dimX = parseFloat($("#dim_x").val()); // en cm
  const dimY = parseFloat($("#dim_y").val()); // en cm

  // Crear un canvas temporal para generar el código de barras
  const canvas = document.createElement("canvas");
  JsBarcode(canvas, codigo, {
    format: "CODE128",
    displayValue: true,
    width: 1,
    height: dimY * 37.8 // cm a px
  });
  const imageData = canvas.toDataURL("image/png");

  // Inicializar jsPDF
  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF({
    orientation: "portrait",
    unit: "cm",
    format: "a4"
  });

  // Márgenes y medidas
  const margenLateral = 1;
  const margenSuperior = 1;
  const separacionHorizontal = 2;
  const separacionVertical = 1;
  const anchoHoja = 21;
  const altoHoja = 29.7;

  const espacioDisponibleX = anchoHoja - 2 * margenLateral;
  const anchoTotalCB = dimX + separacionHorizontal;
  const cbPorFila = Math.floor((espacioDisponibleX + separacionHorizontal) / anchoTotalCB);

  let x = margenLateral;
  let y = margenSuperior;
  let contador = 0;

  for (let i = 0; i < cantidad; i++) {
    pdf.addImage(imageData, "PNG", x, y, dimX, dimY);
    contador++;

    if (contador % cbPorFila === 0) {
      // Pasar a la siguiente fila
      x = margenLateral;
      y += dimY + separacionVertical;

      if (y + dimY > altoHoja - margenSuperior) {
        pdf.addPage();
        y = margenSuperior;
      }
    } else {
      // Pasar a la siguiente columna
      x += dimX + separacionHorizontal;
    }
  }

  // Mostrar PDF en iframe del modal
  const blob = pdf.output("blob");
  const url = URL.createObjectURL(blob);
  $("#iframe-codigo").attr("src", url);
  $("#modal-codigo-preview").modal("show");
  limpiar_form_imprimir();
  $("#modal-form-codigo").modal("hide");

  $("#descargarPDF").off("click").on("click", function () {
    pdf.save(`codigos_barras_${codigo}.pdf`);
  });
}


// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C A T E G O R I A                                                              ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════


function modal_add_categoria() {
  $("#modal-agregar-categoria").modal('show');
}

function limpiar_form_cat(){
  $("#guardar_registro_categoria").html('Guardar Cambios').removeClass('disabled');
  
  $("#idcategoria").val("");
  $("#nombre_cat").val("");
  $("#descr_cat").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_categoria(e){
  var formData = new FormData($("#formulario-categoria")[0]);
  $.ajax({
    url: "../ajax/categoria.php?op=guardar_editar_cat",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', e.data, '.charge_idcategoria');
        Swal.fire("Correcto!", "Categoría registrada correctamente.", "success");
				limpiar_form_cat();
        $("#modal-agregar-categoria").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_categoria").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}


// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  U N I D A D   D E   M E D I D A                                                ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════


function modal_add_u_medida() {
  $("#modal-agregar-u-m").modal('show');
}

function limpiar_form_um() {
  $("#guardar_registro_u_m").html('Guardar Cambios').removeClass('disabled');
  
  $("#idsunat_c03_unidad_medida").val("");
  $("#nombre_um").val("");
  $("#descr_um").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_UM(e){
  var formData = new FormData($("#formulario-u-m")[0]);
  $.ajax({
    url: "../ajax/unidad_medida.php?op=guardar_editar_UM",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e); 
      if (e.status == true) {
        Swal.fire("Correcto!", "Unidad de medida registrado correctamente.", "success");
				limpiar_form_um();
        $("#modal-agregar-u-m").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_u_m").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M A R C A                                                                      ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════


function modal_add_marca() {
  $("#modal-agregar-marca").modal('show');
}

function limpiar_form_marca(){
  $("#guardar_registro_marca").html('Guardar Cambios').removeClass('disabled');

  $("#idmarca").val("");
  $("#nombre_marca").val("");
  $("#descr_marca").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_marca(e){
  var formData = new FormData($("#formulario-marca")[0]);
  $.ajax({
    url: "../ajax/marca.php?op=guardar_editar_marca",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        lista_select2("../ajax/producto.php?op=select_marca", '#marca', e.data, '.charge_idmarca');
        Swal.fire("Correcto!", "Marca registrada correctamente.", "success");
				limpiar_form_marca();
        $("#modal-agregar-marca").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_marca").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}


// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  P R O D U C T O   C A T E G O R I A   U B I C A C I O N                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function modal_add_ubicacion(){
  $("#modal-agregar-ubicacion").modal('show');
}

function limpiar_form_ubicacion(){
  $("#guardar_registro_ubicacion").html('Guardar Cambios').removeClass('disabled');

  $("#idproducto_categoria_ubicacion").val("");
  $("#nombre_ubi").val("");
  $("#descr_ubi").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_ubicacion(e){
  var formData = new FormData($("#formulario-ubicacion")[0]);
  $.ajax({
    url: "../ajax/producto_cat_ubicacion.php?op=guardar_editar_ubi",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        lista_select2("../ajax/producto_cat_ubicacion.php?op=select_prod_cat_ubicacion", '#ubicacion', e.data, '.charge_idubicacion');
        Swal.fire("Correcto!", "Ubicación registrada correctamente.", "success");
				limpiar_form_ubicacion();
        $("#modal-agregar-ubicacion").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_ubicacion").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function evaluar() {
  if (detalles > 0) {
    $(".btn-guardar").show();
  } else {
    $(".btn-guardar").hide();
  }
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  P R O D U C T O   A G R U P A D O                                              ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function listar_tabla_producto_g(tipo = 'PR'){
  $("#modal-list-producto").modal('show');

  tabla_productos_g = $("#tabla-productos-g").dataTable({
    responsive: false, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/producto.php?op=listar_tabla_producto_g&tipo_producto=${tipo}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $('[data-bs-toggle="tooltip"]').tooltip();
      },
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-nowrap text-center"); }
      // columna: #
      // if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      // if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      // { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function agregarProductoaGrupo(idproducto) {   
       
  $.post("../ajax/producto.php?op=mostrar_producto", { idproducto: idproducto }, function (e, status) {        
    e = JSON.parse(e);
    if (e.status == true) {      
      if (e.data == null) {
        toastr_warning('No existe!!', 'Proporcione un codigo existente o el producto pertenece a otra categoria.');          
      } else {         
        
        if ( $(`.producto_${e.data.idproducto}`).hasClass("producto_selecionado") ) {
          if (document.getElementsByClassName(`producto_${e.data.idproducto}`).length == 1) {
            var cant_producto = $(`.producto_${e.data.idproducto}`).val();
            var sub_total = parseInt(cant_producto, 10) + 1;
            $(`.producto_${e.data.idproducto}`).val(sub_total).trigger('change');
            toastr_success("Agregado!!",`Producto: ${$(`.nombre_producto_${e.data.idproducto}`).text()} agregado !!`, 700);
            modificartotal();          
          }  
                  
         
        } else { 
          
          var subtotal = cantidad * e.data.precio_venta;          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;          
          
          var u_medida = '';
          var um_nombres = e.data.unidad_medida.split(',').map(item => item.trim());
          var id_um = e.data.idpresentacion.split(',').map(item => item.trim());
          var cantidad_pp = e.data.cantidad_medida.split(',').map(item => parseFloat(item.trim()));

          if (um_nombres.length > 1) {
            u_medida += `<select class="form-control" name="um_presentation[]" id="um_presentation_${cont}">`;
            for (let i = 0; i < um_nombres.length; i++) {
              u_medida += `<option value="${cantidad_pp[i]}" data-idum="${id_um[i]}">${um_nombres[i]}</option>`;
            }
            u_medida += '</select>';
            let idum_selected =  id_um[0];
            u_medida += `<input type="hidden" name="idproducto_presentacion_set[]" id="idproducto_presentacion_set_${cont}" value="${idum_selected}">`;
          } else {
            u_medida = `
            <span class="fs-11">${e.data.unidad_medida}</span>
            <input type="hidden" name="um_presentation[]" id="um_presentation_${cont}" value="${cantidad_pp}">
            <input type="hidden" name="idproducto_presentacion_set[]" id="idproducto_presentacion_set_${cont}" value="${id_um[0]}">`;
          }

          var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1">
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarProdSeleccionado(${e.data.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
            </td>

            <td class="py-1 text-nowrap">
              <span class="fs-11" ><i class="bi bi-upc"></i> ${e.data.codigo} <br> <i class="bi bi-person"></i> ${e.data.codigo_alterno}</span> 
            </td>

            <td class="py-1 px-0">         
              <input type="hidden" name="idproducto_set[]" id="idproducto_set[]" value="${e.data.idproducto}">

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre)}')"> </span></div>
                <div>
                  <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${e.data.nombre}</textarea>
                  <div class="w-250px span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${e.data.nombre}</span> </div>
                  <span class="d-block fs-9 text-muted">M: <b>${e.data.marca}</b> | C: <b>${e.data.categoria}</b></span> 
                </div>
              </div>
            </td>

            <td class="py-1">
              ${u_medida}
            </td>             

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${e.data.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); update_price(); ">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="modificartotal();" onchange="modificartotal();" >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${e.data.precio_venta}" min="0.01" required readonly>
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${e.data.precio_venta}" onkeyup="modificartotal();" onchange="modificartotal();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
              <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${e.data.precio_compra}"  >
              <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${e.data.precio_venta}"  >
              <input type="hidden" class="precio_venta_original_${cont}" name="precio_venta_original[]" value="${e.data.precio_venta}"  >
            </td> 

            <td class="py-1 text-right">
              <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> 
              <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
            </td>
            <td class="py-1"><button type="button" onclick="modificartotal();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_lp.push({ id_cont: cont });
          modificartotal();        
          toastr_success("Agregado!!",`Producto: ${e.data.nombre} agregado !!`, 700);
          cont++;
        }
      }
    } else {
      ver_errores(e);
    }           
    
    $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
    $(`.btn-add-producto-1-${idproducto}`).html(`<span class="fa fa-plus"></span>`);        
    $(`.btn-add-producto-2-${idproducto}`).html(`<i class="fa-solid fa-list-ol"></i>`); 
    
  }).fail( function(e) { ver_errores(e); } ); 
  
}

function modificartotal() {

  if (array_data_lp.length == 0) {
  } else {
    array_data_lp.forEach((key, index) => {
      var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
      var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
      var subtotal_producto = 0;

      // Calculamos: IGV
      var precio_sin_igv = precio_con_igv;
      $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

      // Calculamos: precio + IGV
      var igv = 0;
      $(`.precio_igv_${key.id_cont}`).val(igv);

      // Calculamos: Subtotal de cada producto
      subtotal_producto = cantidad * parseFloat(precio_con_igv);
      
      $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
      $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
    });
  }

 
  var total = 0.0;

  array_data_lp.forEach((element, index) => {
    total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text()));
  });
  
  $(".l_total_prod").html("<span>S/</span> " + formato_miles(total));
  $("#l_total_prod").val(redondearExp(total, 2));
  total = 0.0;
}

function update_price() {
  toastr_success("Actualizado!!",`Precio Actualizado.`, 700);
}

function eliminarProdSeleccionado(idproducto, indice) {
  $("#fila" + indice).remove();
  array_data_lp.forEach(function (car, index, object) { if (car.id_cont === indice) { object.splice(index, 1); } });
  modificartotal();
  detalles = detalles - 1;
  toastr_warning("Removido!!","Producto removido", 700);
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  P R E S E N T A C I O N                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function limpiar_form_ps(){
  $("#guardar_registro_presentacion").html('<i class="bx bx-save bx-tada fs-12"></i> Guardar Cambios').removeClass('disabled');
 
  $("#idpresentacion").val("");
  $('#um_presentacion').val('58').trigger('change'); // por defecto: NIU  
  $("#nombre_presentacion").val('UNIDADES');
  $("#cant_ps").val("");  
  $("#precio_presentacion_und").val("");  
  $("#precio_presentacion_total").val("");  

  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function listar_presentacion(idproducto_sucursal){
  idproducto_ps_r = idproducto_sucursal;
  console.log('idproducto_sucursal : '+ idproducto_ps_r);
  
  $("#modal-agregar-presentacion").modal('show');
  $("#tabla-presentaciones").html('');

  $.post("../ajax/producto.php?op=listar_presentacion", { idproducto_sucursal: idproducto_sucursal }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {

      $("#idproducto_sucursal_ps").val(e.data.producto.idproducto_sucursal);
      $("#modal-agregar-presentacionLabel1").html(e.data.producto.nombre);

      var tabla_presentacion = `
        <table class="table table-sm table-bordered table-striped align-middle text-center" >
          <thead class="table-light--">
            <tr>
              <th style="width: 5%;">#</th>
              <th style="width: 15%;">Acciones</th>
              <th style="width: 40%;">Presentación</th>
              <th style="width: 20%;">Cant.</th>
              <th style="width: 20%;">Precio x Und</th>
            </tr>
          </thead>
          <tbody>
            ${
              e.data.presentacion.map((item, index) => {
                let unidad = (item.nombre || '').toUpperCase();
                let cantidad = parseFloat(item.cantidad) || 0;
                let precio_venta = parseFloat(item.precio_venta) || 0;
                let estado = item.estado == 1 
                  ? '<span class="badge bg-success">Activo</span>' 
                  : '<span class="badge bg-danger">Inactivo</span>';

                let botones = (item.nombre == 'UNIDADES' && cantidad == 1 )  ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' : 
                `<div class="d-flex justify-content-center gap-1">
                  <button class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center" onclick="mostrar_presentacion(${item.idproducto_presentacion})">
                    <i class="ri-edit-line"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" onclick="eliminar_papelera_presentacion(${item.idproducto_presentacion}, '${item.nombre}')">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>`;

                return `
                  <tr>
                    <td>${index + 1}</td>
                    <td class="text-center">${botones}</td>
                    <td class="text-start"><small>${unidad}</small></td>
                    <td>${cantidad}</td>
                    <td>${precio_venta}</td>
                  </tr>
                `;
              }).join('')
            }
          </tbody>
        </table>
      `;

      $("#tabla-presentaciones").append(tabla_presentacion);

    } else {
      ver_errores(e);
    }
  }).fail(function(e) {
    ver_errores(e);
  });
}

function mostrar_presentacion(idpresentacion){
  $("#modal-agregar-presentacion").modal('show');

  $.post("../ajax/producto.php?op=mostrar_presentacion", { idpresentacion: idpresentacion }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {

      $("#idpresentacion").val(e.data.idproducto_presentacion);
      $("#idproducto_sucursal_ps").val(e.data.idproducto);
	    $('#um_presentacion').val(e.data.idsunat_c03_unidad_medida).trigger('change');
      $("#nombre_presentacion").val(e.data.nombre_presentacion);
      $("#cant_ps").val(e.data.cantidad_presentacion);  

      $("#precio_presentacion_und").val(e.data.precio_venta);      
      $("#precio_presentacion_total").val(e.data.precio_venta_total);      
      
      $('#modal-agregar-presentacionLabel1').text("Editar Presentación"); 

    } else {
      ver_errores(e);
    }
  }).fail(function(e) {
    ver_errores(e);
  });
}

function guardar_presentacion(e){

  var formData = new FormData($("#formulario-ps")[0]);  
  $u_medida = $("#um_presentacion option:selected").text().split("-")[0].trim();
  $cantidad = $("#cant_ps").val();

  Swal.fire({
    title: "¿Está seguro que deseas guardar esta Presentacion?",
    html: `Cuando se realice una <b>venta</b> de este producto con la presentación de <b>${$u_medida}</b> se restará ${$cantidad} del Stock total.`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/producto.php?op=guardar_editar_presentacion", {
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
        Swal.fire("Correcto!", "Presentación guardada correctamente", "success");
        tabla_productos.ajax.reload(null, false);
        limpiar_form_ps();        
        listar_presentacion(idproducto_ps_r);
      } else {
        ver_errores(result.value);
      }      
    }
  });
}

function eliminar_papelera_presentacion(idproducto_presentacion, nombre){
  $("#modal-agregar-presentacion").modal('hide');
  crud_eliminar_papelera(
    "../ajax/producto.php?op=papelera_presentacion",
    "../ajax/producto.php?op=eliminar_presentacion", 
    idproducto_presentacion, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_productos.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

$("#um_presentacion").on('change', function () {  
  var selectedOption = $(this).find('option:selected');// Obtener el valor seleccionado  
  var nombre = selectedOption.attr('nombre');// Obtener el atributo "nombre" de la opción seleccionada  
  $("#nombre_presentacion").val( nombre );
});


// Detecta cambios en los tres campos
$('#precio_presentacion_und, #precio_presentacion_total, #cant_ps').on('input', function() {
    let precioUnd = parseFloat($('#precio_presentacion_und').val());
    let precioTotal = parseFloat($('#precio_presentacion_total').val());
    let cantidad = parseFloat($('#cant_ps').val());

    // Casuística 1: si hay precio por unidad y cantidad, calcula total
    if (!isNaN(precioUnd) && !isNaN(cantidad) && $(this).attr('id') !== 'precio_presentacion_total') {
        let totalCalculado = precioUnd * cantidad;
        $('#precio_presentacion_total').val(totalCalculado.toFixed(2));
    }

    // Casuística 2: si hay precio total y cantidad, calcula precio por unidad
    if (!isNaN(precioTotal) && !isNaN(cantidad) && cantidad > 0 && $(this).attr('id') !== 'precio_presentacion_und') {
        let unidadCalculada = precioTotal / cantidad;
        $('#precio_presentacion_und').val(unidadCalculada.toFixed(2));
    }
});

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  I N I T                                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

$(document).ready(function () {
  init();
});

function mayus(e) {
  e.value = e.value.toUpperCase();
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  G E N E R A R   C O D I G O                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function generarcodigonarti() {

  var name_producto = $("#nombre").val() == null || $("#nombre").val() == '' ? '' : $("#nombre").val() ;  
  if (name_producto == '') { toastr_warning('Vacio!!','El nombre esta vacio, digita para completar el codigo aletarorio.', 700); }
  name_producto = name_producto.substring(-3, 3);
  var cod_letra = Math.random().toString(36).substring(2, 5);  
  var cod_number = Math.floor(Math.random() * 10) +''+ Math.floor(Math.random() * 10);  
  $("#codigo_alterno").val(`${name_producto.toUpperCase()}${cod_number}${cod_letra.toUpperCase()}`);
}

function create_code_producto(pre_codigo) {
  $('.charge_codigo').html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
 
  $.getJSON(`../ajax/ajax_general.php?op=create_code_producto&pre_codigo=${pre_codigo}`, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $('#codigo').val(e.data.nombre_codigo);
      $('#codigo').attr('readonly', 'readonly').addClass('bg-light'); // Asegura que el campo esté como solo lectura
      add_tooltip_custom('#codigo', 'No se puede editar');            //  Agrega tooltip personalizado a un element
      $('.charge_codigo').html('')                                    // limpiamos la carga
    } else {
      ver_errores(e);
    }      
  }).fail( function(jqxhr, textStatus, error) { ver_errores(jqxhr); } );
  
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  F O R M   V A L D I A T E                                                      ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

$(function () {
  $('#categoria').on('change', function() { $(this).trigger('blur'); });
  $('#u_medida').on('change', function() { $(this).trigger('blur'); });
  $('#marca').on('change', function() { $(this).trigger('blur'); });

  //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  $("#form-agregar-producto").validate({
    ignore: "",
    rules: {           
      codigo:         { required: true, minlength: 2, maxlength: 20, },
      categaria:    	{ required: true },
      u_medida:    		{ required: true },
      marca:    			{ required: true },
      ubicacion: 			{ required: true },
      nombre:    			{ required: true, minlength: 2, maxlength: 250,  },
      descripcion:    { minlength: 2, maxlength: 500, },
      cant_um:        { required: true, min: 1, step: 0.01, },
      stock:          { required: true, min: 0, step: 0.01,},
      stock_min:      { required: true, min: 0, step: 0.01,},
      precio_v:       { required: true, min: 0, step: 0.01,},
      precio_c:       { required: true, min: 0, step: 0.01,},
      precio_x_mayor: {  min: 0, step: 0.01,},
      codigo_alterno: { required: true, minlength: 4, maxlength: 20,
        remote: {
          url: "../ajax/producto.php?op=validar_code_producto",
          type: "get",
          data: {
            action: function () { return "validar_codigo";  },
            idproducto: function() { var idproducto = $("#idproducto").val(); return idproducto; }
          }
        }
      },
    },
    messages: {     
      cogido:    			{ required: "Campo requerido", },
      categaria:    	{ required: "Seleccione una opción", },
      u_medida:    		{ required: "Seleccione una opción", },
      marca:    			{ required: "Seleccione una opción", },
      ubicacion: 			{ required: "Seleccione una opción", },
      nombre:    			{ required: "Campo requerido", }, 
      descripcion:    { minlength: "Minimo {0} caracteres.", },
      cant_um:        { required: "Campo requerido", step: 'Maximo 2 decimales.'},
      stock:          { required: "Campo requerido", step: 'Maximo 2 decimales.'},
      stock_min:      { required: "Campo requerido", step: 'Maximo 2 decimales.'},
      precio_v:       { required: "Campo requerido", step: 'Maximo 2 decimales.'},
      precio_c:       { required: "Campo requerido", step: 'Maximo 2 decimales.'},
      precio_x_mayor: { step: 'Maximo 2 decimales.'},
      codigo_alterno: { required: "Campo requerido", remote:"Código en uso."},
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
      if ($("#list-productos").is(':visible') && array_data_lp.length < 2) {
        toastr_warning("Advertencia!!", "Por favor seleccione dos a más Productos", 700);
        return false;
      } else {
        $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
        guardar_editar_producto(e);
      }
    },
  });

  //  :::::::::::::::::::: F O R M U L A R I O   C A T E G O R I A ::::::::::::::::::::

  $("#formulario-categoria").validate({
    rules: {
      nombre_cat: { required: true },
    },
    messages: {
      nombre_cat: {  required: "Campo requerido.", },
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600); 
      guardar_editar_categoria(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   U.   M E D I D A :::::::::::::::::::::::::::

  $("#formulario-u-m").validate({
    rules: {
      nombre_um: { required: true } ,
    },
    messages: {
      nombre_um: {  required: "Campo requerido.", },
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_UM(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   M A R C A :::::::::::::::::::::::::::

  $("#formulario-marca").validate({
    rules: {
      nombre_marca: { required: true } ,
    },
    messages: {
      nombre_marca: {  required: "Campo requerido.", },
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_marca(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   U B I C A C I O N :::::::::::::::::::::::::::

  $("#formulario-ubicacion").validate({
    rules: {
      nombre_ubi: { required: true } ,
      //descr_ubi:  { required: true }
    },
    messages: {
      nombre_ubi: {  required: "Campo requerido.", },
      //descr_ubi:  {  required: "Campo requerido.", },
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_ubicacion(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   P R E S E N T A C I O N :::::::::::::::::::::::::::

  $("#formulario-ps").validate({
    rules: {
      um_presentacion: {required: true},
      cant_ps: {required: true } ,
    },
    messages: {
      um_presentacion: { required: "Campo requerido.", },
      cant_ps: { required: "Campo requerido.", },
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_presentacion(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   I M P R I M I R :::::::::::::::::::::::::::

  $("#formulario-codg").validate({
    rules: {
      cant_cg: {required: true},
      dim_x: {required: true } ,
      dim_y: {required: true},
    },
    messages: {
      cant_cg: { required: "Campo requerido.", },
      dim_x: { required: "Campo requerido.", },
      dim_y: { required: "Campo requerido.", },
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_imprimir_codigo(e);      
    },

  });

  $('#categoria').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#u_medida').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#marca').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#ubicacion').rules('add', { required: true, messages: {  required: "Campo requerido" } });
});


// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  F U N C I O N E S    A L T E R N A S                                           ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_categoria      = $("#filtro_categoria").select2('val');
  var filtro_unidad_medida  = $("#filtro_unidad_medida").select2('val');  
  var filtro_marca          = $("#filtro_marca").select2('val');
  var filtro_ubicacion          = $("#filtro_ubicacion").select2('val');
  
  var nombre_categoria      = $('#filtro_categoria').find(':selected').text();
  var nombre_um             = ' ─ ' + $('#filtro_unidad_medida').find(':selected').text();
  var nombre_marca          = ' ─ ' + $('#filtro_marca').find(':selected').text();
  var nombre_ubicacion          = ' ─ ' + $('#filtro_ubicacion').find(':selected').text();

  // filtro de fechas
  if (filtro_categoria == '' || filtro_categoria == 0 || filtro_categoria == null) { filtro_categoria = ""; nombre_categoria = ""; }

  // filtro de proveedor
  if (filtro_unidad_medida == '' || filtro_unidad_medida == 0 || filtro_unidad_medida == null) { filtro_unidad_medida = ""; nombre_um = ""; }

  // filtro de trabajdor
  if (filtro_marca == '' || filtro_marca == 0 || filtro_marca == null) { filtro_marca = ""; nombre_marca = ""; }

  // filtro de ubicacion
  if (filtro_ubicacion == '' || filtro_ubicacion == 0 || filtro_ubicacion == null) { filtro_ubicacion = ""; nombre_ubicacion = ""; }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_categoria} ${nombre_um} ${nombre_marca}...`);

  listar_tabla(filtro_categoria, filtro_unidad_medida, filtro_marca, filtro_ubicacion);
}

function cambiarImagen() {
	var imagenInput = document.getElementById('imagenProducto');
	imagenInput.click();
}

function removerImagen() {
	$("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png");
	$("#imagenProducto").val("");
  $("#imagenactualProducto").val("");
}

document.addEventListener('DOMContentLoaded', function () {
	var imagenMuestra = document.getElementById('imagenmuestraProducto');
	var imagenInput = document.getElementById('imagenProducto');

	imagenInput.addEventListener('change', function () {
		if (imagenInput.files && imagenInput.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) { imagenMuestra.src = e.target.result;	}
			reader.readAsDataURL(imagenInput.files[0]);
		}
	});
});

$(document).on("change", "select[id^='um_presentation_']", function () {
  let selectElement = $(this);
  
  // Extraer el valor del índice `cont` desde el id del select
  let id_cont = selectElement.attr("id");
  let cont_2 = id_cont.split("_").pop();

  // Obtener el valor seleccionado (cantidad de presentación)
  let cantidad_presntacion = parseFloat(selectElement.val());

  // Obtener el precio original de venta desde el campo oculto
  let precio_venta_original = parseFloat($(`.precio_venta_original_${cont_2}`).val());
  
  // Obtener el id_um del option seleccionado
  let id_um_selected = selectElement.find(":selected").data("idum");

  // Asignar el id_um seleccionado al input hidden correspondiente
  $(`#idproducto_presentacion_set_${cont_2}`).val(id_um_selected);

  // Validar que ambos valores son números
  if (!isNaN(cantidad_presntacion) && !isNaN(precio_venta_original)) {
    // Calcular precio final
    let precioFinal = cantidad_presntacion * precio_venta_original;

    // Actualizar los campos relacionados a esa fila
    $(`#valid_precio_con_igv_${cont_2}`).val(precioFinal.toFixed(2));
    $(`#precio_con_igv_${cont_2}`).val(precioFinal.toFixed(2));
    $(`.precio_con_igv_${cont_2}`).val(precioFinal.toFixed(2));
    $(`.precio_venta_descuento_${cont_2}`).val(precioFinal.toFixed(2));
  } else {
    console.warn(`Valores inválidos en fila ${cont_2}:`, {
      cantidad_presntacion,
      precio_venta_original
    });
  }

  modificartotal();
});


function ver_img(img, nombre) {
	$(".title-modal-img").html(`${nombre}`);
  $('#modal-ver-img').modal("show");
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/productos', '80%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}

function reload_idcategoria(){ lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', null, '.charge_idcategoria'); }
function reload_idmarca(){ lista_select2("../ajax/producto.php?op=select_marca", '#marca', null, '.charge_idmarca'); }
function reload_idunidad_medida(){ lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida', null, '.charge_idunidad_medida'); }
function reload_idubicacion(){ lista_select2("../ajax/producto_cat_ubicacion.php?op=select_prod_cat_ubicacion", '#ubicacion', null, '.charge_idubicacion'); }

function reload_filtro_categoria() { lista_select2("../ajax/producto.php?op=select2_filtro_categoria", '#filtro_categoria', null, '.charge_filtro_categoria'); }
function reload_filtro_unidad_medida() { lista_select2("../ajax/producto.php?op=select2_filtro_u_medida", '#filtro_unidad_medida', null, '.charge_filtro_unidad_medida'); }
function reload_filtro_marca() { lista_select2("../ajax/producto.php?op=select2_filtro_marca", '#filtro_marca', null, '.charge_filtro_marca'); }
function reload_filtro_ubicacion() { lista_select2("../ajax/producto_cat_ubicacion.php?op=select2_filtro_ubicacion", '#filtro_ubicacion', null, '.charge_filtro_ubicacion'); }





