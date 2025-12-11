
var tabla_principal_facturacion;
var tabla_productos;
var form_validate_facturacion;
var tabla_ver_mas_detalle_facturacion;

var array_data_venta = [];


var cambio_de_tipo_comprobante ;
const filePondInstances = [];

var filtro_estado_sunat = "" ;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_tipo_documento = new Choices('#cli_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_distrito       = new Choices('#cli_distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// var choice_centro_poblado = new Choices('#cli_centro_poblado',  { shouldSort: false, removeItemButton: true,noResultsText: 'No hay resultados.', } );


async function init(){

  // filtros(); // Listamos la tabla principal
  $(".btn-tiket").click();   // Selecionamos la BOLETA  

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-venta").submit(); }  });  
  $("#guardar_registro_producto").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); } });
  $("#guardar_registro_nuevo_cliente").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-nuevo-cliente").submit(); } else {toastr_info('Espere!!', 'Por favor sea pasiente se estan procesando los datos...');} });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/orden_venta_listar.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante');
  lista_select2("../ajax/orden_venta_listar.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente');
  lista_select2("../ajax/orden_venta_listar.php?op=select2_banco", '#filtro_metodo_pago', null, '.charge_filtro_metodo_pago');
  lista_select2("../ajax/ajax_general.php?op=select2_centro_poblado_venta", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado'); 

  lista_select2("../ajax/orden_venta_listar.php?op=select2_cliente", '#o_idcliente', null, '.charge_o_idcliente');  

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null); 
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_centro_poblado", choice_centro_poblado, null);  

    lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#cli_distrito', null);

  // Listar serie
  ver_series_comprobante();

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#o_idcliente").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, }); 

  $("#filtro_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_tipo_persona").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, }); $("#filtro_tipo_persona").val('').trigger("change");
  $("#filtro_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_metodo_pago").select2({ templateResult: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_centro_poblado").select2({ templateResult:templateCentroPoblado, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#cli_distrito").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});
  $("#cli_tipo_documento").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});

  await activar_btn_agregar(); // Esperamos a al carga total de los datos para poder: CREAR
}

async function activar_btn_agregar() {
  $(".btn-agregar").show();
}

function templateCentroPoblado (state) {
  //console.log(state);
  if (!state.id) { return state.text; } 
  var $state = $(`<span class="fs-11" > <i class="bi bi-geo-alt"></i></span><span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function templateCliente (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function templateBanco (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span><img src="${baseUrl}" class="img-circle mr-2 w-25px" ${onerror} />${state.text}</span>`);
  return $state;
};

function templateComprobante (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../dist/docs/persona/perfil/${state.title}`: '../dist/svg/user_default.svg'; 
  var onerror = `onerror="this.src='../dist/svg/user_default.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function show_hide_form(flag) {
	if (flag == 1) {        // TABLA PRINCIPAL    
    
    $("#div-tabla").show();    
		$("#div-formulario").hide();    

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) { // FORMULARIO FACTURACION

		$("#div-tabla").hide();    
		$("#div-formulario").show();
    
		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();

  } else if (flag == 3) { // TABLA MAS DETALLE FACTURACION

		$("#div-tabla").hide();    
		$("#div-formulario").hide();		

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();
	}
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  F A C T U R A C I O N                                                          ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', '300px', true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form_venta(){
  
  $("#o_idventa").val('');
  $("#o_nc_idventa").val('0');

  $('#o_crear_y_emitir').prop('checked', false)
  $('#o_tipo_comprobante12').prop('checked', true).focus().trigger('change'); 
  $("#o_idcliente").val('').trigger('change'); 

  $("#o_observacion_documento").val(''); 
  $("#o_periodo_pago").val('');
  $("#search_producto").val('');  
  
  $('#html-metodos-de-pagos').html('');
  $("#o_total_recibido").val(0);
  $("#o_total_vuelto").val(0);
  $("#o_ua_monto_usado").val('');
  $("#o_mp_serie_comprobante").val('');
  
  $("#o_mp_comprobante_old").val('');

  $(".span_dia_cancelacion").html(``);

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


  $('.input_cantidad_venta').each(function(e) { $(this).rules("remove"); });  
  $('.input_precio_con_igv').each(function(e) { $(this).rules("remove"); });  
  $('.input_descuento').each(function(e) { $(this).rules("remove"); });  

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_tipo_persona, filtro_comprobante, filtro_metodo_pago, filtro_centro_poblado, filtro_estado_sunat){
  
  tabla_principal_facturacion = $("#tabla-ventas").dataTable({
    responsive: false,     
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    // aProcessing: true, //Activamos el procesamiento del datatables
    // aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-8 col-xxl-9 pt-2'f><'col-md-5 col-lg-4 col-xl-4 col-xxl-3 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-sm px-2 btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_principal_facturacion) { tabla_principal_facturacion.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6,7,8,11], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-sm px-2 btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6,7,8,11], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-sm px-2 btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6,7,8,11], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-sm px-2 btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-sm px-2 btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/orden_venta_listar.php?op=listar_tabla_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_tipo_persona=${filtro_tipo_persona}&filtro_comprobante=${filtro_comprobante}&filtro_metodo_pago=${filtro_metodo_pago}&filtro_centro_poblado=${filtro_centro_poblado}&filtro_estado_sunat=${filtro_estado_sunat}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-copy").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Copiar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-pdf").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'PDF');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: Opciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[5] != '') { $("td", row).eq(5).addClass("text-nowrap"); }
      // columna: Monto
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap"); }
      // columna: Monto
      if (data[8] != '') { $("td", row).eq(9).addClass("text-nowrap text-center"); }
      
    },
    language: {
      lengthMenu: "_MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      search: "",
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 6 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 6 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    initComplete: function () {

      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [3], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();

  
}

function guardar_editar_facturacion(e) {  

  var formData = new FormData($("#form-facturacion")[0]);  
 
  Swal.fire({
    title: "¿Está seguro que deseas guardar esta Venta?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/orden_venta_listar.php?op=guardar_editar_facturacion", {
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
        Swal.fire("Correcto!", "Venta guardada correctamente", "success");
        tabla_principal_facturacion.ajax.reload(null, false);
        limpiar_form_venta(); show_hide_form(1); 
        
      } else if ( result.value.status == 'error_personalizado'){        
        tabla_principal_facturacion.ajax.reload(null, false);
        limpiar_form_venta(); show_hide_form(1);  ver_errores(result.value);
      } else if ( result.value.status == 'error_usuario'){    
        ver_errores(result.value);

      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

function mostrar_detalle_venta(idventa){
  $("#modal-detalle-venta").modal("show");

  $.post("../ajax/orden_venta_listar.php?op=mostrar_detalle_venta", { idventa: idventa }, function (e, status) {          
      
    $('#custom-tabContent').html(e);      
    $('#custom-datos1_html-tab').click(); // click para ver el primer - Tab Panel
    $(".jq_image_zoom").zoom({ on: "grab" });      
    $("#excel_venta").attr("href",`../reportes/export_xlsx_venta_tours.php?id=${idventa}`);      
    $("#print_pdf_venta").attr("href",`../reportes/comprobante_venta_tours.php?id=${idventa}`);    
    
  }).fail( function(e) { ver_errores(e); } );

}

function eliminar_papelera_venta(idventa, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/orden_venta_listar.php?op=papelera",
    "../ajax/orden_venta_listar.php?op=eliminar", 
    idventa, 
    "!Elija una opción¡", 
    `Comnprobante: <b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M O S T R A R   S E R I E S                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function ver_series_comprobante() {  
  var tipo_comprobante = $('#o_tipo_comprobante').val();
  $.getJSON("../ajax/orden_venta_listar.php?op=select2_series_comprobante", { tipo_comprobante: tipo_comprobante },  function (e, status) {    
    if (e.status == true) {      
      $("#o_serie_comprobante").html(e.data);
      $(".o_charge_serie_comprobante").html('');
      $("#form-facturacion").valid();
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );

  cambio_de_tipo_comprobante = tipo_comprobante;
}


// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C R E A R   C L I E N T E                                                      ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function crear_cliente() {
  limpiar_nuevo_cliente();
  $(`#modal-agregar-nuevo-cliente`).modal('show');
}

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

function guardar_editar_nuevo_cliente(e){ 
  var formData = new FormData($("#form-agregar-nuevo-cliente")[0]);
  $.ajax({
    url: "../ajax/orden_venta_listar.php?op=guardar_editar_cliente",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); 
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");          
          lista_select2("../ajax/orden_venta_listar.php?op=select2_cliente", '#o_idcliente', e.data, '.charge_o_idcliente');
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

function esperar(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
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



// ░▒▓════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  P R O D U C T O S                                                                ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function listar_tabla_producto(tipo = 'PR'){
  $("#modal-producto").modal('show');
  $("#title-modal-producto-label").html( (tipo == 'PR' ? 'Seleccionar Producto' : 'Seleccionar Servicio') );
  var es_precio_por_mayor = $('#precio_por_mayor').is(':checked') ? 'SI' : 'NO';
  tabla_productos = $("#tabla-productos").dataTable({
    responsive: false, 
    resize: true,    
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor    
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-9 col-xxl-10 pt-2'f><'col-md-5 col-lg-4 col-xl-3 col-xxl-2 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/orden_venta_listar.php?op=listar_tabla_producto&tipo_producto=${tipo}&es_precio_por_mayor=${es_precio_por_mayor}`,
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
    columnDefs: [      
      // { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}


$(document).ready(function () {
  init(); 
  filtros();
});

function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}




// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         B U S Q U E D A   D E   P R O D U C T O S                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

$(document).ready(function () {
  $('#search_producto').on('keyup', function () {
    let query = $(this).val();
    var es_precio_por_mayor = $('#precio_por_mayor').is(':checked') ? 'SI' : 'NO';

    if (query.length >= 2) {
      $.getJSON(`../ajax/orden_venta_listar.php?op=listar_producto_x_nombre`, { search: query }, function (e, textStatus, jqXHR) {
        
        let $resultsList = $('#searchResults');
        $resultsList.empty();

        if (e.data.length > 0) {
          e.data.forEach(function (val, key) {
            $resultsList.append(`<li class="list-group-item hover-text-success list-group-item-action bg-light cursor-pointer py-1" onclick="agregarDetalleComprobante(${val.idproducto_presentacion},null, false)">
              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/${val.imagen}" alt="" > </span></div>        
                <div> 
                  <span class="fs-12">${val.nombre_presentacion}</span> | <span class="fs-12">${val.nombre_producto}</span> <br>
                  <span class="fs-10">Cod: ${val.codigo_alterno} </span> |
                  <span class="fs-10">Stock: ${parseFloat(val.stock_presentacion_entero) || 0} </span> ${es_precio_por_mayor == 'NO' ? `| <span class="fs-10">Venta: S/. ${ formato_miles(val.precio_venta)}</span>` : `| <span class="fs-10">Mayor: S/. ${ formato_miles(val.precio_por_mayor)}</span>`}
                </div>
              </div>
            </li>`);
          });
        } else {
          $resultsList.append(`<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`);
        }

        $resultsList.show();
      });
    } else {
      $('#searchResults').hide();
    }
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#searchInput, #searchResults').length) {
      $('#searchResults').hide();
    }
  });
});


// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

  form_validate_facturacion = $("#form-facturacion").validate({
    ignore: '',
    rules: {
      o_idcliente:      { required: true },
      o_tipo_comprobante:       { required: true },
      o_serie_comprobante:      { required: true, },
      o_observacion_documento:  { minlength: 4 },            
      o_total_recibido:         { required: true, min: 0, step: 0.01},           
      o_total_vuelto:           { required: true, step: 0.01},
      o_ua_monto_usado:         { required: true, min: 1, step: 0.01},
      o_mp_serie_comprobante:   { minlength: 4},
      // mp_comprobante:         { extension: "png|jpg|jpeg|webp|svg|pdf",  }, 
    },
    messages: {
      o_idcliente:      { required: "Campo requerido", },
      o_tipo_comprobante:       { required: "Campo requerido", },      
      o_serie_comprobante:      { required: "Campo requerido", },
      o_observacion_documento:  { minlength: "Minimo {0} caracteres", },
      // mp_comprobante:         { extension: "Ingrese imagenes validas ( {0} )", },
      o_total_recibido:         { step: "Solo 2 decimales."},       
      o_total_vuelto:           { step: "Solo 2 decimales."},
      o_ua_monto_usado:         { step: "Solo 2 decimales."},
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

    submitHandler: function (form) {      
      guardar_editar_facturacion(form);
    },
  });   
  

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

});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_fecha_i        = $("#filtro_fecha_i").val();
  var filtro_fecha_f        = $("#filtro_fecha_f").val();  
  var filtro_cliente        = $("#filtro_cliente").select2('val');
  var filtro_tipo_persona   = $("#filtro_tipo_persona").select2('val');
  var filtro_comprobante    = $("#filtro_comprobante").select2('val');
  var filtro_metodo_pago    = $("#filtro_metodo_pago").select2('val');
  var filtro_centro_poblado = $("#filtro_centro_poblado").select2('val');
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_cliente     = ' ─ ' + $('#filtro_cliente').find(':selected').text();
  var nombre_filtro_comprobante = ' ─ ' + $('#filtro_comprobante').find(':selected').text();

  // filtro de fechas
  if (filtro_fecha_i        == '' || filtro_fecha_i         == 0 || filtro_fecha_i        == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  if (filtro_fecha_f        == '' || filtro_fecha_f         == 0 || filtro_fecha_f        == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }
  // filtro de cliente
  if (filtro_cliente        == '' || filtro_cliente         == 0 || filtro_cliente        == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }
  // filtro de filtro_tipo_persona
  if (filtro_tipo_persona   == '' || filtro_tipo_persona    == 0 || filtro_tipo_persona   == null) { filtro_tipo_persona = "";  }
  // filtro de comprobante
  if (filtro_comprobante    == '' || filtro_comprobante     == 0 || filtro_comprobante    == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }
  // filtro de metodo pago
  if (filtro_metodo_pago    == '' || filtro_metodo_pago     == 0 || filtro_metodo_pago    == null) { filtro_metodo_pago = ""; nombre_filtro_metodo_pago = ""; }
  // filtro de filtro_centro_poblado
  if (filtro_centro_poblado == '' || filtro_centro_poblado  == 0 || filtro_centro_poblado == null) { filtro_centro_poblado = "";  }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ${nombre_filtro_cliente}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_tipo_persona, filtro_comprobante, filtro_metodo_pago, filtro_centro_poblado, filtro_estado_sunat);
}

function filtrar_solo_estado_sunat(estado, etiqueta) {  
  $(".otros-filtros").find("li>a").removeClass("active");
  $(".otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat = estado; filtros();
}

function reload_o_idcliente(){ lista_select2("../ajax/orden_venta_listar.php?op=select2_cliente", '#o_idcliente', null, '.charge_o_idcliente'); }

function reload_filtro_fecha_i(){ $('#filtro_fecha_i').val("").trigger("change") } 
function reload_filtro_fecha_f(){ $('#filtro_fecha_f').val("").trigger("change") } 
function reload_filtro_cliente(){ lista_select2("../ajax/orden_venta_listar.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente'); } 
function reload_filtro_comprobante(){ lista_select2("../ajax/orden_venta_listar.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante'); }
function reload_filtro_metodo_pago(){ lista_select2("../ajax/orden_venta_listar.php?op=select2_banco", '#filtro_metodo_pago', null, '.charge_filtro_metodo_pago');  }
function reload_filtro_centro_poblado_venta(){ lista_select2("../ajax/ajax_general.php?op=select2_centro_poblado_venta", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado');  }


function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
}

function ver_img_pefil(id_cliente) {
  $('#modal-ver-imgenes').modal('show');
  $(".html_modal_ver_imgenes").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/orden_venta_listar.php?op=mostrar_cliente", { idcliente: id_cliente },  function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      // if (e.data.foto_perfil == "" || e.data.foto_perfil == null) { } else {
        var nombre_comprobante = `${e.data.cliente_nombre_completo} - ${e.data.numero_documento}`;
        var file_comprobante = e.data.foto_perfil ==''||  e.data.foto_perfil == null ? 'no-perfil.jpg' : e.data.foto_perfil;
        $('.title-ver-imgenes').html(nombre_comprobante);
        $(".html_modal_ver_imgenes").html(doc_view_download_expand(file_comprobante, 'assets/modulo/persona/perfil',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      // }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
}





