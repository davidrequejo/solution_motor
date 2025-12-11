var tabla_cliente_todos;
var tabla_cliente_deudor;
var tabla_cliente_no_deudor;
var tabla_pagos_all_cliente;
var tabla_resumen_producto_venta;
var tabla_todos_producto_venta;

var form_validate_facturacion;
var array_data_venta = [];
var cambio_de_tipo_comprobante ;
var file_pond_mp_comprobante;
const filePondInstances = [];

var ubicacion_cliente;

var opcion_r = 'tabla_todos', filtro_trabajador_r = '', filtro_tipo_persona_r =  '', filtro_dia_pago_r = '', filtro_plan_r = '', filtro_centro_poblado_r = '', filtro_zona_antena_r = '';

//Función que se ejecuta al inicio
function init() {  

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });
  $(".btn-guardar-cobro").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-venta").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 
  lista_select2("../ajax/cliente.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');
  lista_select2("../ajax/ajax_general.php?op=select2_centro_poblado_cliente", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado');

  lista_select2("../ajax/facturacion.php?op=select2_banco", '#f_metodo_pago_1', null, 'charge_f_metodo_pago_1');
  lista_select2("../ajax/cliente.php?op=selec_centroProbl", '#centro_poblado', null);
  lista_select2("../ajax/ajax_general.php?op=select2_tipo_documento", '#tipo_documento', null);
  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#distrito', null);
  
  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#filtro_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_tipo_persona").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, }); $("#filtro_tipo_persona").val('').trigger("change");
  $("#filtro_dia_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_plan").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_centro_poblado").select2({ templateResult:templateCentroPoblado, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_zona_antena").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_p_all_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_anio_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_dia_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_plan").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_zona_antena").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  
  $("#tipo_documento").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#distrito").select2({ templateResult: templateDistrito, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#centro_poblado").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#tipo_persona_sunat").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#f_metodo_pago_1").select2({ templateResult: templateBanco, templateSelection: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
}

function templateDistrito (state) {
  //console.log(state);
  if (!state.id) { return state.text; } 
  var $state = $(`<span class="fs-11" > ${state.text}</span><span class="fs-9" > (${state.title})</span>`);
  return $state;
}

function templateCentroPoblado (state) {
  //console.log(state);
  if (!state.id) { return state.text; } 
  var $state = $(`<span class="fs-11" > <i class="bi bi-geo-alt"></i></span><span class="fs-11" > ${state.text}</span>`);
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

//Función limpiar
function limpiar_cliente() {

  $("#guardar_registro_cliente").html('Guardar Cambios').removeClass('disabled');

  $("#tipo_persona_sunat").val('').trigger("change");
  $("#tipo_documento").val('').trigger("change");
  $("#numero_documento").val("");
  $("#nombre_razonsocial").val("");
  $("#apellidos_nombrecomercial").val("");
  $("#fecha_nacimiento").val("");
  $("#celular").val("");
  $("#direccion").val("");
  $("#distrito").val('TARAPOTO').trigger("change");
  $("#centro_poblado").val('1').trigger("change");

  $("#correo").val("");

  $("#idpersona").val('');
  $("#idpersona_cliente").val('');

  $("#nota").val("");

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg");
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/usuario/perfil/no-perfil.jpg';
  }


  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function show_hide_form(flag) {

  if (flag == 1) {                     // LISTA CLIENTES   
    $("#div-tabla-principal").show();
    $("#div-form-cliente").hide();
    $("#div-ver-cobro-x-cliente").hide();
    $("#div-ver-cobro-all-cliente").hide();
    $("#div-realizar-pago").hide();

    $(".btn-agregar").show();
    $(".btn-pagos-all").show();
    $(".btn-guardar").hide();
    $(".btn-guardar-cobro").hide();
    $(".btn-cancelar").hide();
    limpiar_cliente();
    limpiar_form_venta();
    $('.title-body-pagina').html(`Lista de clientes!`);

  } else if (flag == 2) {               // EDITAR O REGISTRA UN CLIENTE   
    $("#div-tabla-principal").hide();
    $("#div-form-cliente").show();
    $("#div-ver-cobro-x-cliente").hide();
    $("#div-ver-cobro-all-cliente").hide();
    $("#div-realizar-pago").hide();

    $(".btn-agregar").hide();
    $(".btn-pagos-all").hide();
    $(".btn-guardar").show();
    $(".btn-guardar-cobro").hide();
    $(".btn-cancelar").show();
  } else if (flag == 3) {               // VER PAGOS POR CLIENTE
    $("#div-tabla-principal").hide();
    $("#div-form-cliente").hide();
    $("#div-ver-cobro-x-cliente").show();
    $("#div-ver-cobro-all-cliente").hide();
    $("#div-realizar-pago").hide();

    $(".btn-agregar").hide();
    $(".btn-pagos-all").hide();
    $(".btn-guardar").hide();
    $(".btn-guardar-cobro").hide();
    $(".btn-regresar").show();
  } else if (flag == 4) {               // VER PAGOS TODOS CLIENTE
    $("#div-tabla-principal").hide();
    $("#div-form-cliente").hide();
    $("#div-ver-cobro-x-cliente").hide();
    $("#div-ver-cobro-all-cliente").show();
    $("#div-realizar-pago").hide();

    $(".btn-agregar").hide();
    $(".btn-pagos-all").hide();
    $(".btn-guardar").hide();
    $(".btn-cancelar").show();
  } else if (flag == 5) {               // REALIZAR PAGO
    $("#div-tabla-principal").hide();
    $("#div-form-cliente").hide();
    $("#div-ver-cobro-x-cliente").hide();
    $("#div-ver-cobro-all-cliente").hide();
    $("#div-realizar-pago").show();

    $(".btn-agregar").hide();
    $(".btn-pagos-all").hide();
    $(".btn-guardar").hide();
    $(".btn-guardar-cobro").show();
    $(".btn-cancelar").show();
  }  

}

//nombres segun el tipo de doc
$('#tipo_documento').change(function () {
  var tipo = $(this).val() == '' ||  $(this).val() == null ? '' : $(this).val() ;

  if ( tipo == '0' ) { 
    $("#numero_documento").val('00000000').addClass('bg-dark-transparent').prop('readonly', true).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('#apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.nombre_razon').html('Nombres <sup class="text-danger">*</sup>');
    $('.apellidos_nombrecomer').html('Apellidos <sup class="text-danger">*</sup>');
  }else if (tipo == '1') {
    $("#numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.nombre_razon').html('Nombres <sup class="text-danger">*</sup>');
    $('.apellidos_nombrecomer').html('Apellidos <sup class="text-danger">*</sup>');
  }else if (tipo == '6') {
    $("#numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#apellidos_nombrecomercial').rules('remove', 'required');
    $('.nombre_razon').html('Razón Social <sup class="text-danger">*</sup>');
    $('.apellidos_nombrecomer').html('Nombre comercial <sup class="text-danger">*</sup>');
    $("#numero_documento").rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 

  } else {
    $("#numero_documento").removeClass('bg-dark-transparent').prop('readonly', false);
  }

  $("#form-agregar-cliente").valid();

});

function cant_tab_cliente(filtro_tipo_persona,filtro_centro_poblado) {
  $.getJSON(`../ajax/cliente.php?op=cant_tab_cliente`, {'filtro_tipo_persona':filtro_tipo_persona, 'filtro_centro_poblado': filtro_centro_poblado, }, function (e, textStatus, jqXHR) {
    $('.cant-span-sinpagos').html(e.data.count_sinpagos);
    $('.cant-span-conpagosparciales').html(e.data.count_pagos_parciales);
    $('.cant-span-conpagostotal').html(e.data.count_con_pagostotal);
    $('.cant-span-total').html(e.data.count_total);
      
  });
}

function llenar_dep_prov_ubig(input) {

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);

  if ($(input).select2("val") == null || $(input).select2("val") == '') {
    $("#departamento").val("");
    $("#provincia").val("");
    $("#ubigeo").val("");

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito = $(input).select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {
      e = JSON.parse(e); 
      $("#departamento").val(e.data.departamento);
      $("#provincia").val(e.data.provincia);
      $("#ubigeo").val(e.data.ubigeo_inei);

      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      $("#form-agregar-cliente").valid();
    });
  }
}

function funtion_switch() {
  $("#toggleswitchSuccess").val(0);
  var isChecked = $('#toggleswitchSuccess').prop('checked');
  if (isChecked) {
    toastr_success("Estado", "Descuento Activado", 700);
    $("#estado_descuento").val(1);
    $('#descuento').removeAttr('readonly');
  } else {
    toastr_warning("Estado", "Descuento Desactivado", 700);
    $("#estado_descuento").val(0);
    $("#descuento").val('0');
    $("#monto_descuento").val('0.00');
    $('#descuento').attr('readonly', 'readonly');
  }
}

//Función Listar
function tabla_principal_cliente(opcion, filtro_trabajador, filtro_tipo_persona, filtro_dia_pago, filtro_plan, filtro_centro_poblado, filtro_zona_antena) {
  // console.log(opcion, filtro_trabajador, filtro_dia_pago, filtro_plan, filtro_zona_antena);

  filtro_trabajador_r = filtro_trabajador; filtro_dia_pago_r = filtro_dia_pago; filtro_plan_r = filtro_plan; filtro_zona_antena_r = filtro_zona_antena;

  tabla_cliente_todos = $('#tabla-cliente').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-8 col-xxl-9 pt-2'f><'col-md-5 col-lg-4 col-xl-4 col-xxl-3 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_cliente_todos) { tabla_cliente_todos.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,5,6,7,8,9,3,4], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0,5,6,7,8,9,3,4], }, title: 'Lista de Clientes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      { extend: 'pdf', exportOptions: { columns: [0,5,6,7,8,9,3,4], }, title: 'Lista de Clientes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_principal_cliente&filtro_trabajador=${filtro_trabajador}&filtro_tipo_persona=${filtro_tipo_persona}&filtro_dia_pago=${filtro_dia_pago}&filtro_plan=${filtro_plan}&filtro_centro_poblado=${filtro_centro_poblado}&filtro_zona_antena=${filtro_zona_antena}`,
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
        $('#id_buscando_tabla').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
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
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [      
      // { targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [5,6,7,8,9], visible: false, searchable: false, },
    ],
  }).DataTable();
}

//Función Listar
function tabla_principal_cliente_deudor(opcion, filtro_trabajador, filtro_tipo_persona, filtro_dia_pago, filtro_plan, filtro_centro_poblado, filtro_zona_antena) {

  filtro_trabajador_r = filtro_trabajador; filtro_dia_pago_r = filtro_dia_pago; filtro_plan_r = filtro_plan; filtro_zona_antena_r = filtro_zona_antena;

  tabla_cliente_deudor = $('#tabla-cliente-deudor').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_cliente_deudor) { tabla_cliente_deudor.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, title: 'Lista de Clientes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      // { extend: 'pdf', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, title: 'Lista de Clientes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_deudor&filtro_trabajador=${filtro_trabajador}&filtro_tipo_persona=${filtro_tipo_persona}&filtro_dia_pago=${filtro_dia_pago}&filtro_plan=${filtro_plan}&filtro_centro_poblado=${filtro_centro_poblado}&filtro_zona_antena=${filtro_zona_antena}`,
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
        $('#id_buscando_tabla').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "_MENU_", search: "", info: "",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [      
      // { targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function tabla_principal_cliente_deudor_parcial(opcion, filtro_trabajador, filtro_tipo_persona, filtro_dia_pago, filtro_plan, filtro_centro_poblado, filtro_zona_antena) {

  filtro_trabajador_r = filtro_trabajador; filtro_dia_pago_r = filtro_dia_pago; filtro_plan_r = filtro_plan; filtro_zona_antena_r = filtro_zona_antena;

  tabla_cliente_deudor = $('#tabla-cliente-deudor-parcial').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_cliente_deudor) { tabla_cliente_deudor.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, title: 'Lista de Clientes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      // { extend: 'pdf', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, title: 'Lista de Clientes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_deudor_pacial&filtro_trabajador=${filtro_trabajador}&filtro_tipo_persona=${filtro_tipo_persona}&filtro_dia_pago=${filtro_dia_pago}&filtro_plan=${filtro_plan}&filtro_centro_poblado=${filtro_centro_poblado}&filtro_zona_antena=${filtro_zona_antena}`,
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
        $('#id_buscando_tabla').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "_MENU_", search: "", info: "",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [      
      // { targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function tabla_principal_cliente_no_deudor(opcion, filtro_trabajador, filtro_tipo_persona, filtro_dia_pago, filtro_plan, filtro_centro_poblado, filtro_zona_antena) {

  filtro_trabajador_r = filtro_trabajador; filtro_dia_pago_r = filtro_dia_pago; filtro_plan_r = filtro_plan; filtro_zona_antena_r = filtro_zona_antena;

  tabla_cliente_no_deudor = $('#tabla-cliente-no-deudor').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_cliente_no_deudor) { tabla_cliente_no_deudor.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, title: 'Lista de Clientes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      // { extend: 'pdf', exportOptions: { columns: [0,9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 8], }, title: 'Lista de Clientes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_no_deudores&filtro_trabajador=${filtro_trabajador}&filtro_tipo_persona=${filtro_tipo_persona}&filtro_dia_pago=${filtro_dia_pago}&filtro_plan=${filtro_plan}&filtro_centro_poblado=${filtro_centro_poblado}&filtro_zona_antena=${filtro_zona_antena}`,
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
        $('#id_buscando_tabla').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "_MENU_", search: "", info: "",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [      
      // { targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function detalle_tab_cliente( id_html, idpersona_cliente) {  

  toastr_info('No disponible!!', 'Por favor, tenga paciencia. La visualización de pagos en este módulo de clientes estará disponible muy pronto.');

  return;
 
  $(`#${id_html}`).html(`<div class="col-lg-12 text-center"><div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div><h4 class="bx-flashing">Cargando...</h4></div>`); 

  $.get(`../ajax/cliente.php?op=detalle_cliente_deudor`, {idpersona_cliente:idpersona_cliente},  function (e, textStatus, jqXHR) {
    
    $(`#detalle-cliente-deudor`).html(''); 
    $(`#detalle-cliente-no-deudor`).html(''); 

    $(`#${id_html}`).html(e);   

    var myElement1 = document.getElementById('profile-posts-scroll');
    new SimpleBar(myElement1, { autoHide: true });

  });  

}

function reset_ubicacion_cliente(latitud, longitud) {
  // Restablecer el mapa eliminando los marcadores existentes
  if (ubicacion_cliente) { ubicacion_cliente.removeMarkers(); }

  // Volver a instanciar el mapa
  ubicacion_cliente = new GMaps({ 
    el: '#map-markers', 
    lat: -8.141857, 
    lng: -76.590359, 
    mapType: 'satellite',
    zoom: 18.5
  });

  // Añadir de nuevo el marcador inicial
  ubicacion_cliente.addMarker({ 
    lat: -8.141857, 
    lng: -76.590359, 
    title: 'Click para ver detalles.', 
    infoWindow: { title: 'Ubicacion', content: '<p class="text-light" >HTML Content</p>' } 
  });

}

//Función para guardar o editar
function guardar_y_editar_cliente(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-cliente")[0]);

  $.ajax({
    url: "../ajax/cliente.php?op=guardar_y_editar_cliente",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "Color registrado correctamente.", "success");
          if (tabla_cliente_todos) { tabla_cliente_todos.ajax.reload(null, false); } 
          if (tabla_cliente_deudor) { tabla_cliente_deudor.ajax.reload(null, false); } 
          if (tabla_cliente_no_deudor) { tabla_cliente_no_deudor.ajax.reload(null, false); } 
          limpiar_cliente();
          show_hide_form(1);
          $("#guardar_registro_cliente").html('Guardar Cambios').removeClass('disabled');
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
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_cliente").css({ "width": percentComplete + '%' });
          $("#barra_progress_cliente").text(percentComplete.toFixed(2) + " %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $(".btn-guardar").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> <i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_cliente").css({ width: "0%", });
      $("#barra_progress_cliente").text("0%");
    },
    complete: function () {
      $("#barra_progress_cliente").css({ width: "0%", });
      $("#barra_progress_cliente").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_cliente(idpersona_cliente) {

  $("#cargando-1-formulario").hide();
  $("#cargando-2-formulario").show();

  limpiar_cliente();
  show_hide_form(2);

  $.post("../ajax/cliente.php?op=mostrar_cliente", { idpersona_cliente: idpersona_cliente }, function (e, status) {

    e = JSON.parse(e); console.log(e);

    if (e.status == true) {

      $("#idpersona").val(e.data.idpersona);
      $("#idtipo_persona").val(e.data.idtipo_persona);
      $("#idbancos").val(e.data.idbancos);
      $("#idcargo_trabajador").val(e.data.idcargo_trabajador);

      $("#idpersona_cliente").val(e.data.idpersona_cliente);

      $("#tipo_persona_sunat").val(e.data.tipo_persona_sunat).trigger("change");
      $("#tipo_documento").val(e.data.tipo_documento).trigger("change");
      $("#numero_documento").val(e.data.numero_documento);
      $("#nombre_razonsocial").val(e.data.nombre_razonsocial);
      $("#apellidos_nombrecomercial").val(e.data.apellidos_nombrecomercial);
      $("#fecha_nacimiento").val(e.data.fecha_nacimiento);
      $("#celular").val(e.data.celular);
      $("#direccion").val(e.data.direccion);
      $("#direccion_referencia").val(e.data.direccion_referencia);
      $("#distrito").val(e.data.distrito).trigger("change");
      $("#centro_poblado").val(e.data.idcentro_poblado).trigger("change");
      $("#departamento").val(e.data.departamento);
      $("#provincia").val(e.data.provincia);
      $("#ubigeo").val(e.data.cod_ubigeo);
      $("#correo").val(e.data.correo);

      $("#nota").val(e.data.nota);     

      $("#imagenmuestra").show();
      $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/" + e.data.foto_perfil);
      $("#imagenactual").val(e.data.foto_perfil);


      $("#cargando-1-formulario").show();
      $("#cargando-2-formulario").hide();

    } else {
      ver_errores(e);
    }

  }).fail(function (e) { ver_errores(e); });
}

//Función para activar registros
function activar(idusuario, nombre) {
	crud_simple_alerta(
		`../ajax/cliente.php?op=activar_cliente&descripcion=`,
    idusuario, 
    "!Reactivar¡", 
    `<b class="text-success">${nombre}</b> <br> Se <b>eliminara</b> la NOTA que ha sido registrado!`, 
		`Aceptar`,
    function(){ sw_success('Recuperado', "Tu cliente ha sido restaurado." ) }, 
    function(){ tabla_cliente_todos.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

//Función para desactivar registros
function eliminar_cliente(idtrabajador, nombre) {
  $(".tooltip").removeClass("show").addClass("hidde");
  Swal.fire({
    title: "!Elija una opción¡",
    html: `<b class="text-danger"><del>${nombre}</del></b> <br> Al <b>dar de baja</b> Padrá encontrar el registro en la papelera! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`,
    icon: "warning",
    showCancelButton: true,
    showDenyButton: true,
    confirmButtonColor: "#17a2b8",
    denyButtonColor: "#d33",
    cancelButtonColor: "#6c757d",    
    confirmButtonText: `<i class="fas fa-times"></i> Dar de Baja`,
    denyButtonText: `<i class="fas fa-skull-crossbones"></i> Eliminar`,    
    showLoaderOnDeny: true,
    preDeny: (input) => {       
      return fetch(`../ajax/cliente.php?op=eliminar_cliente&id_tabla=${idtrabajador}`).then(response => {
        console.log(response);
        if (!response.ok) { throw new Error(response.statusText) }
        return response.json();
      }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); })
    },
    allowOutsideClick: () => !Swal.isLoading()
  }).then((result) => {
    
    if (result.isConfirmed) {    
      Swal.fire({
        icon: "warning",
        title: 'Antes de dar de baja ingrese una Observación',
        input: 'textarea',        
        inputAttributes: { autocapitalize: 'off', Class: 'form-control', Placeholder: 'ejemp: Cliente registrado por error.',  },
        customClass: {
          validationMessage: 'my-validation-message',
        },
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonText: 'Si, dar de baja!',
        confirmButtonColor: "#28a745",
        showLoaderOnConfirm: true,
        preConfirm: (value) => {
          console.log(value);
          if (!value) {
            Swal.showValidationMessage('La <i class="fw-bold">&nbsp;NOTA&nbsp;</i> es requerido.')
          }else{            
            return fetch(`../ajax/cliente.php?op=desactivar_cliente&id_tabla=${idtrabajador}&descripcion=${value}`).then(response => {
              console.log(response);
              if (!response.ok) { throw new Error(response.statusText); }
              return response.json();
            }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); });
          }          
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {       
        if (result.isConfirmed) {
          if (result.value.status) {
            Swal.fire("Expulsado!", "Tu trabajador ha sido expulsado.", "success");
            tabla_cliente_todos.ajax.reload(null, false); 
          }else{
            ver_errores(result.value);
          }     
        }
      });

    }else if (result.isDenied) {
      //op=eliminar
      if (result.value.status) {
        Swal.fire("Eliminado!", "Tu trabajador ha sido Eliminado.", "success");
        tabla_cliente_todos.ajax.reload(null, false); 
      }else{
        ver_errores(result.value);
      }      
    }
  });
}

// .....::::::::::::::::::::::::::::::::::::: V E R   P A G O S  X  C L I E N T E   :::::::::::::::::::::::::::::::::::::::..
function ver_pagos_x_cliente(idcliente) {
  show_hide_form(3);
  $('#div_tabla_x_cliente').html(`<div class="pt-5" ><div class="col-lg-12 text-center"><div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div></div>`);
  
  $.get(`../ajax/cliente.php?op=ver_pagos_x_cliente&idcliente=${idcliente}`,  function (e, textStatus, jqXHR) {
    $('#div_tabla_x_cliente').html(e);
    
    $('[data-bs-toggle="tooltip"]').tooltip();
  });
}

// .....::::::::::::::::::::::::::::::::::::: V E R   P A G O S  ALL  C L I E N T E   :::::::::::::::::::::::::::::::::::::::..


// .....::::::::::::::::::::::::::::::::::::: V E R   P A G O S  C L I E N T E  P O R   M E S :::::::::::::::::::::::::::::::::::::::..

function pagos_cliente_x_mes(idpersona_cliente, mes, anio){

  console.log("Cliente ID: " + idpersona_cliente + ", Mes: " + mes);
  var filtroTrabajador = $('#filtro_p_all_trabajador').select2('val');
  var filtroDiaPago    = $('#filtro_p_all_dia_pago').select2('val');
  var filtroAnioPago   = anio || $('#filtro_p_all_anio_pago').select2('val');
  var filtroPlan       = $('#filtro_p_all_plan').select2('val');
  var filtroZonaAntena = $('#filtro_p_all_zona_antena').select2('val');

  if (filtroTrabajador  == '' || filtroTrabajador == 0 || filtroTrabajador  == null) { filtroTrabajador = ""; nombre_trabajador  = ""; }
  if (filtroDiaPago     == '' || filtroDiaPago    == 0 || filtroDiaPago     == null) { filtroDiaPago    = ""; nombre_dia_pago    = ""; }
  if (filtroAnioPago    == '' || filtroAnioPago   == 0 || filtroAnioPago    == null) { filtroAnioPago   = ""; nombre_anio_pago   = ""; }
  if (filtroPlan        == '' || filtroPlan       == 0 || filtroPlan        == null) { filtroPlan       = ""; nombre_plan        = ""; }
  if (filtroZonaAntena  == '' || filtroZonaAntena == 0 || filtroZonaAntena  == null) { filtroZonaAntena = ""; nombre_zona_antena = ""; }

  $("#pago-cliente-mes").modal("show");
  $('#div_tabla_pagos_Cx_mes').html(`<div class="pt-5" ><div class="col-lg-12 text-center"><div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div></div>`);

  $.get(`../ajax/cliente.php?op=pagos_cliente_x_mes&id=${idpersona_cliente}&mes=${mes}&filtroA=${filtroTrabajador}&filtroB=${filtroDiaPago}&filtroC=${filtroAnioPago}&filtroD=${filtroPlan}&filtroE=${filtroZonaAntena}`,  function (e, textStatus, jqXHR) {
    $('#div_tabla_pagos_Cx_mes').html(e);
    $('#id_buscando_tabla_pago_xmes').hide();
    $('[data-bs-toggle="tooltip"]').tooltip();
  });
  
}


// .....::::::::::::::::::::::::::::::::::::: I M P R I M I R   T I C K E T :::::::::::::::::::::::::::::::::::::::..

function TickcetPagoCliente(idventa, tipo_comprobante){

  $("#pago-cliente-mes").modal('hide');
  // console.log(idventa);
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-Label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-Label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-Label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
  
}

/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  :::::                                                R E A L I Z A R   P A G O 
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

function reload_pago_individual(id) {
  $('#div_tabla_x_cliente_pagar').html(`<div class="pt-5" ><div class="col-lg-12 text-center"><div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div></div>`);
  $.get(`../ajax/cliente.php?op=ver_pagos_x_cliente&idcliente=${id}`,  function (e, textStatus, jqXHR) {
    $('#div_tabla_x_cliente_pagar').html(e);    
    $('[data-bs-toggle="tooltip"]').tooltip();
  });
}

function realizar_pago(id) {

  $("#cargando-3-formulario").hide();
  $("#cargando-4-formulario").show();
  
  $('#f_crear_y_emitir').prop('checked', false)
  show_hide_form(5);
  usar_anticipo_valid();
 
  $('.reload-reload-pago-individual').attr('onclick', `reload_pago_individual(${id})`);
  
  $('.title-body-pagina').html(`<div class="spinner-border me-4" role="status"></div>`);
  $('#f_idpersona_cliente').val(id)

  $('#div_tabla_x_cliente_pagar').html(`<div class="pt-5" ><div class="col-lg-12 text-center"><div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div></div>`);  
  $.get(`../ajax/cliente.php?op=ver_pagos_x_cliente&idcliente=${id}`,  function (e, textStatus, jqXHR) {
    $('#div_tabla_x_cliente_pagar').html(e);    
    $('[data-bs-toggle="tooltip"]').tooltip();

    $.getJSON(`../ajax/cliente.php?op=mostrar_datos_cliente`, {idpersona_cliente: id}, function (e, textStatus, jqXHR) {

      $('.title-body-pagina').html(e.data.cliente_nombre_completo);
      $('#f_tipo_documento').val(e.data.tipo_documento);
      $('#f_numero_documento').val(e.data.numero_documento);
      $('#f_direccion').val(e.data.direccion); 
      $('#f_dia_cancelacion').val(e.data.dia_cancelacion_v2);  
      listar_producto_x_precio(e.data.plan_costo)
    });
  });  

  
}

function limpiar_form_venta(){

  array_data_venta = [];
  $("#f_idventa").val('');
  
  $('#f_crear_y_emitir').prop('checked', false)
  $('#f_tipo_comprobante12').prop('checked', true).focus().trigger('change'); 
  $("#f_idpersona_cliente").val('').trigger('change'); 
  $("#f_metodo_pago_1").val('').trigger('change'); 
  $("#f_observacion_documento").val(''); 
  $("#f_periodo_pago").val('');
  $("#f_codigob").val('');  
  
  $("#f_total_recibido").val(0);
  $("#f_mp_monto").val(0);
  $("#f_total_vuelto").val(0);
  $("#f_ua_monto_usado").val('');
  $("#f_mp_serie_comprobante").val('');
  file_pond_mp_comprobante.removeFiles();
  $("#f_mp_comprobante_old").val('');

  $(".span_dia_cancelacion").html(``);

  $("#f_venta_total").val("");     
  $(".f_venta_total").html("0");

  $(".f_venta_subtotal").html("<span>S/</span> 0.00");
  $("#f_venta_subtotal").val("");

  $(".f_venta_descuento").html("<span>S/</span> 0.00");
  $("#f_venta_descuento").val("");

  $(".f_venta_igv").html("<span>S/</span> 0.00");
  $("#f_venta_igv").val("");

  $(".f_venta_total").html("<span>S/</span> 0.00");
  $("#f_venta_total").val("");

  $(".filas").remove();

  cont = 0;

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function ver_meses_cobrado(idcont) {
  console.log(idcont);
  $("#modal-ver-meses-cobrados").modal("show");
  var id_cliente = $("#f_idpersona_cliente").val() == null || $("#f_idpersona_cliente").val() == '' ? 0 : $("#f_idpersona_cliente").val();
  var id_periodo = $(`#valid_periodo_pago_${idcont}`).val() == null || $(`#valid_periodo_pago_${idcont}`).val() == '' ? 0 : $(`#valid_periodo_pago_${idcont}`).val();
  $.get(`../ajax/facturacion.php?op=ver_meses_cobrado`, {idcliente:id_cliente, id_periodo: id_periodo}, function (e, textStatus, jqXHR) {
    $('#ver-meses-cobrados').html(e);
      
  });
}

// ::::::::::::::::::::::::::::::::::::::::::::: LISTA PRODUCTO :::::::::::::::::::::::::::::::::::::::::::::

function listar_tabla_producto(tipo = 'PR'){
  $("#modal-producto").modal('show');
  $("#title-modal-producto-label").html( (tipo == 'PR' ? 'Seleccionar Producto' : 'Seleccionar Servicio') );
  tabla_productos = $("#tabla-productos").dataTable({
    responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/facturacion.php?op=listar_tabla_producto&tipo_producto=${tipo}`,
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

// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR SERIES :::::::::::::::::::::::::::::::::::::::::::::

function ver_series_comprobante(input) {
  if (cambio_de_tipo_comprobante == '07') { limpiar_form_venta(); } // Limpiamos si el comprobante anterior era: Nota de Credito

  $("#f_serie_comprobante").html('');
  $(".f_charge_serie_comprobante").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  var tipo_comprobante = $(input).val() == ''  || $(input).val() == null ? '' : $(input).val();
  var nc_tp = ""; // En caso de ser Nota de Credito
  $('#f_tipo_comprobante_hidden').val(tipo_comprobante);

  $(".div_idpersona_cliente").show();
  $(".div_nc_tipo_comprobante").hide();
  $(".div_nc_serie_y_numero").hide();
  $(".div_nc_motivo_anulacion").hide();
  $(".div_es_cobro").show();
  $(".datos-de-cobro-mensual").show();
  $(".div_agregar_producto").show();
  $(".div_pago_rapido").show();
  $(".div_m_pagos").show();
  $(".div_usar_anticipo").show();
  $(".datos-de-saldo").hide();

  // VALIDANDO SEGUN: TIPO DE COMPROBANTE
  if (form_validate_facturacion) { // FORM-VALIDATE
  
    if ( tipo_comprobante == '01') {   
      $("#f_idsunat_c01").val(2); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } });       
    } else if ( tipo_comprobante == '03') {
      $("#f_idsunat_c01").val(3); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
    } else if ( tipo_comprobante == '07') {
      var nc_tipo_comprobante = $('#f_nc_tipo_comprobante').val() == '' || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
      if (nc_tipo_comprobante == '01') {
        $("#f_idsunat_c01").val(7); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      } else if (nc_tipo_comprobante == '03') {
        $("#f_idsunat_c01").val(8); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      }
      
      var nc_tp = $('#f_nc_tipo_comprobante').val() == ''  || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
      $("#f_periodo_pago").rules('remove', 'required');
      $("#f_metodo_pago").rules('remove', 'required');
      $('##f_es_cobro_inp').val('NO'); $('#f_usar_anticipo').val('NO');

      $(".div_idpersona_cliente").hide();
      $(".div_nc_tipo_comprobante").show();
      $(".div_nc_serie_y_numero").show();
      $(".div_nc_motivo_anulacion").show();
      $(".div_es_cobro").hide();
      $(".datos-de-cobro-mensual").hide();
      $(".div_agregar_producto").hide();
      $(".div_pago_rapido").hide();
      $(".div_m_pagos").hide();
      $(".div_usar_anticipo").hide();
      $(".datos-de-saldo").hide();
    } else if ( tipo_comprobante == '12') {
      $("#f_idsunat_c01").val(12); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante      
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
    }
  }

  $.getJSON("../ajax/facturacion.php?op=select2_series_comprobante", { tipo_comprobante: tipo_comprobante, nc_tp:nc_tp },  function (e, status) {    
    if (e.status == true) {      
      $("#f_serie_comprobante").html(e.data);
      $(".f_charge_serie_comprobante").html('');
      $("#form-facturacion").valid();
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );

  cambio_de_tipo_comprobante = tipo_comprobante;
}

// ::::::::::::::::::::::::::::::::::::::::::::: CLIENTE VALIDO :::::::::::::::::::::::::::::::::::::::::::::
function es_valido_cliente() {

  var id_cliente = $('#f_idpersona_cliente').val() == ''  || $('#f_idpersona_cliente').val() == null ? '' : $('#f_idpersona_cliente').val();
  $(".span_dia_cancelacion").html(``);

  if (id_cliente != null && id_cliente != '') {

    var tipo_comprobante  = $('#f_tipo_comprobante_hidden').val() == ''  || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();
    var tipo_documento    = $('#f_tipo_documento').val();
    var numero_documento  = $('#f_numero_documento').val();
    var direccion         = $('#f_direccion').val();  
    var dia_cancelacion   = $('#f_dia_cancelacion').val();  
    var campos_requeridos = ""; 
    var es_valido = true; 
    
    if (id_cliente != '1') {
      $(".span_dia_cancelacion").html(`(${dia_cancelacion} de cada mes.)`); // obtenemos la fecha de cancelacion
    }    

    if (tipo_comprobante == '01') {       // FACTURA
      
      if ( tipo_documento == '6'  ) { }else{ campos_requeridos = campos_requeridos.concat(`<li>Tipo de Documento: RUC</li>`);  }
      if ( numero_documento != '' ) { }else{ campos_requeridos = campos_requeridos.concat(`<li>Numero de Documento</li>`);  }
      if ( direccion != '' ) {    }else{  campos_requeridos = campos_requeridos.concat(`<li>Direccion</li>`);  }
      if (tipo_documento == '6' && numero_documento != '' && direccion != '' ) {  es_valido = true;  } else {   es_valido = false; }

    } else if (tipo_comprobante == '03' || id_cliente == '1') {  // BOLETA
      
      if ( tipo_documento == '1' || tipo_documento == '6' ) {  }else{  campos_requeridos = campos_requeridos.concat(`<li>Tipo de Documento: DNI o RUC</li>`);  }
      if ( numero_documento != '' ) {  }else{  campos_requeridos = campos_requeridos.concat(`<li>Numero de Documento</li>`);  }
      if ( direccion == '' || direccion == null ) {  campos_requeridos = campos_requeridos.concat(`<li>Direccion</li>`);  }else{    }
      if ( (tipo_documento == '1' || tipo_documento == '6' || tipo_documento == '0' ) && numero_documento != ''  ) { es_valido = true; } else {  es_valido = false; }

    } else if (tipo_comprobante == '12' ) { // TICKET
      es_valido = true;
    }

    if (es_valido == true) {
     
    } else {

      $('#f_tipo_comprobante12').prop('checked', false)
      $('#f_tipo_comprobante03').prop('checked', false)
      $('#f_tipo_comprobante01').prop('checked', false)

      if (tipo_comprobante == '03' && tipo_documento == '0' ) {
        Swal.fire({
          title: "Desea emitir Boleta?",
          html: "Si deseas emitir Boleta sin DNI, actualiza el numero con 8 ceros: 00000000, o ingrese el DNI correcto del cliente.",
          input: "text",
          inputValue: '00000000',
          inputAttributes: { autocapitalize: "off" },
          showCancelButton: true,
          confirmButtonText: "Actualizar DNI",
          showLoaderOnConfirm: true,
          preConfirm: async (numero_documento) => {
            try {              
              const UrlUpdate_client = `../ajax/ajax_general.php?op=update_nro_documento_cliente&idpersona_cliente=${id_cliente}&numero_documento=${numero_documento}`;
              const response = await fetch(UrlUpdate_client);
              if (!response.ok) {
                return Swal.showValidationMessage(` ${JSON.stringify(await response.json())} `);
              }
              return response.json();
            } catch (error) {
              Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`);
            }
          },
          allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
          if (result.isConfirmed) {    
            $.getJSON(`../ajax/cliente.php?op=mostrar_datos_cliente`, {idpersona_cliente: id_cliente}, function (e, textStatus, jqXHR) {

              $('.title-body-pagina').html(e.data.cliente_nombre_completo);
              $('#f_tipo_documento').val(e.data.tipo_documento);
              $('#f_numero_documento').val(e.data.numero_documento);
              $('#f_direccion').val(e.data.direccion); 
              $('#f_dia_cancelacion').val(e.data.dia_cancelacion_v2);  

              $('#f_tipo_comprobante03').prop('checked', true).focus().trigger('change');    
              setTimeout(function() {
                $("#form-facturacion").valid();  
                $('#f_tipo_comprobante03').focus(); 
              }, 500); // 3000 milisegundos = 3 segundos         
              sw_success('Datos Actualizado!!', 'Se actualizado el Nro Documento correctamente');

            });
           
          }else{
            $('#f_tipo_comprobante12').prop('checked', true).focus().trigger('change'); 
            setTimeout(function() {
              $("#form-facturacion").valid();  
              $('#f_tipo_comprobante12').focus(); 
            }, 500); // 3000 milisegundos = 3 segundos 
          }
        });        
      } else {

        // sw_cancelar('Cliente no permitido', `El cliente no cumple con los siguientes requsitos:  <ul class="pt-3 text-left font-size-13px"> ${campos_requeridos} </ul>`, 10000);      
        Swal.fire({
          title: "Cliente no permitido",
          html: `El cliente no cumple con los siguientes requsitos:  <ul class="pt-3 text-left font-size-13px"> ${campos_requeridos} </ul>`,
          icon: "info",       
          confirmButtonColor: "#3085d6",        
          confirmButtonText: "Ok"
        }).then((result) => {               
          $('#f_tipo_comprobante12').prop('checked', true).focus().trigger('change'); 
          setTimeout(function() {
            $("#form-facturacion").valid();  
            $('#f_tipo_comprobante12').focus(); 
          }, 500); // 3000 milisegundos = 3 segundos        
        });   
      }   
    }   
    
    console.log(tipo_comprobante, tipo_documento, numero_documento, direccion, es_valido);
  }    
}

// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR ANTICIPOS :::::::::::::::::::::::::::::::::::::::::::::
function usar_anticipo_valid() { 
  $("#f_ua_monto_usado").val('');

  var id_cliente = $('#f_idpersona_cliente').val() == ''  || $('#f_idpersona_cliente').val() == null ? '' : $('#f_idpersona_cliente').val(); 

  if ($(".f_usar_anticipo").hasClass("on") == true) {

    $("#f_usar_anticipo").val("SI");
    $(".datos-de-saldo").show("slow");
    if (id_cliente != '') {
      $.getJSON(`../ajax/facturacion.php?op=mostrar_anticipos`, {id_cliente:id_cliente}, function (e, textStatus, jqXHR) {
        $("#f_ua_monto_disponible").val(e.data.total_anticipo);
        if (form_validate_facturacion) { $("#f_ua_monto_usado").rules('add', { required: true, max: parseFloat(e.data.total_anticipo) , messages: {  required: "Campo requerido", max: "Saldo disponible: {0}" } }); }
        $("#form-facturacion").valid();
      }).fail( function(e) { ver_errores(e); } );
    }
  } else {
    $("#f_usar_anticipo").val("NO");
    $(".datos-de-saldo").hide("slow");
    if (form_validate_facturacion) { $("#f_ua_monto_usado").rules('remove', 'required'); }
    $("#form-facturacion").valid();
  }
}

// ::::::::::::::::::::::::::::::::::::::::::::: GUARDAR COBRO :::::::::::::::::::::::::::::::::::::::::::::

function guardar_editar_facturacion(e) {

  renombrarInputsArrayContenedor('.f_mp_comprobante_validar', 'name', 'f_mp_comprobante'); // Ajustamos la numeracion de Array en los inputs: File

  var formData = new FormData($("#form-facturacion")[0]);  

 // Seleccionar los divs con la clase f_mp_comprobante_validar
  const divs = document.querySelectorAll("div.f_mp_comprobante_validar");
  const emptyFileKeys = [];

  // Iterar sobre los divs para encontrar los inputs dentro
  divs.forEach((div) => {
    const input = div.querySelector("input[type='file']"); // Encontrar el input dentro del div

    if (input == '' || input == null) { } else{
      if (input.name == '' || input.name == null) { } else{
        const inputName = input.name; // Obtener el nombre del input
        const fileList = input.files; // Obtener los archivos seleccionados

        // console.log(input);
        // console.log(`Nombre: ${inputName}`);
        // console.log(`Archivos:`, fileList);

        // Verificar si no tiene archivos seleccionados
        if (fileList.length === 0) {
          emptyFileKeys.push(inputName); // Registrar el nombre para procesamiento
        }
      }       
    }
  });

  console.log("Claves vacías:", emptyFileKeys);

  // Eliminar y reemplazar entradas vacías en FormData
  emptyFileKeys.forEach((key) => {
    formData.delete(key); // Eliminar la entrada original
    formData.append(key, ""); // Agregar un valor vacío como texto
  });

  // Verificar qué datos se enviarán
  // for (let [key, value] of formData.entries()) {
  //   if (key == 'f_mp_comprobante[]') {
  //     console.log(`${key}: ${value}`);
  //   }    
  // }

  Swal.fire({
    title: "¿Está seguro que deseas guardar esta Venta?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/cliente.php?op=guardar_editar_facturacion", {
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
        // if (tabla_cliente_todos)      { tabla_cliente_todos.ajax.reload(null, false);     }
        if (tabla_cliente_deudor)     { tabla_cliente_deudor.ajax.reload(null, false);    } 
        // if (tabla_cliente_no_deudor)  { tabla_cliente_no_deudor.ajax.reload(null, false); } 
        limpiar_form_venta(); show_hide_form(1);
        if ($('#f_crear_y_mostrar').is(':checked')) {
          $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl").addClass("modal-md");          
          var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + result.value.data;
          $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);
          $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
          $("#modal-imprimir-comprobante").modal("show");
        }
      } else if ( result.value.status == 'error_personalizado'){        
        // if (tabla_cliente_todos)      { tabla_cliente_todos.ajax.reload(null, false);     }
        if (tabla_cliente_deudor)     { tabla_cliente_deudor.ajax.reload(null, false);    } 
        // if (tabla_cliente_no_deudor)  { tabla_cliente_no_deudor.ajax.reload(null, false); } 
        limpiar_form_venta(); show_hide_form(1); ver_errores(result.value);
      } else if ( result.value.status == 'error_usuario'){    
        ver_errores(result.value);

      } else if ( result.value.status == 'no_conexion_sunat'){    
        Swal.fire({
          title: result.value.titulo,
          icon: 'info',
          html: result.value.message,
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Salir!',
          confirmButtonAriaLabel: 'Enviar más tarde.',
          cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
          cancelButtonAriaLabel: 'Dejar como esta.'
        }).then((result) => {
          if (result.isConfirmed) {
           
            $.getJSON(`../ajax/facturacion.php?op=cambiar_a_por_enviar&idventa=${result.value.id_tabla}`, data, function (e, textStatus, jqXHR) {
              if (e.status == true) {
                sw_success('Cambiado!!', "Tu registro ha actualizado." );   
              } else {
                ver_errores(e);
              }
            }).fail( function(e) { ver_errores(e); } );
          } else {
                  
          }
        });
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  :::::                                         F I N   R E A L I Z A R   P A G O 
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/


/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  :::::                                         R E S U M E N   D E   P R O D U C T O S   V E N D I D O S
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

function resumen_producto_comprado(id_cliente) {
  $('#modal_ver_productos_vendidos').modal('show');

  tabla_resumen_producto_venta = $("#tabla-resumen-producto-venta").dataTable({
    responsive: false, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_resumen_producto_venta) { tabla_resumen_producto_venta.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_resumen_producto_venta&id_cliente=${id_cliente}`,
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
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap") }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: #
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      
    },
    language: {
      lengthMenu: "Mostrar: _MENU_",
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

  tabla_todos_producto_venta = $("#tabla-todos-producto-venta").dataTable({
    responsive: false, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_todos_producto_venta) { tabla_todos_producto_venta.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_todos_producto_venta&id_cliente=${id_cliente}`,
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
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap") }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: #
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      
    },
    language: {
      lengthMenu: "Mostrar: _MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [2], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

$(document).ready(function () { 

  init();
});

$(function () {
  $('#tipo_persona_sunat').on('change', function () { $(this).trigger('blur'); });
  $('#tipo_documento').on('change', function () { $(this).trigger('blur'); });
  $('#distrito').on('change', function () { $(this).trigger('blur'); });

  $("#form-agregar-cliente").validate({
    rules: {

      tipo_persona_sunat:   { required: true },
      tipo_documento:       { required: true, minlength: 1, maxlength: 2, },
      numero_documento:     { required: true, minlength: 8, maxlength: 20, number: true, },
      nombre_razonsocial:   { required: true, minlength: 2, maxlength: 200, },
      apellidos_nombrecomercial: { required: true, minlength: 4, maxlength: 200, },
      correo:    			      { minlength: 4, maxlength: 100, },       
      celular:    			    { minlength: 8, maxlength: 9, },       
      fecha_nacimiento:    	{  },  

      direccion:    			  { minlength: 4, maxlength: 200, },
      direccion_referencia: { minlength: 4, maxlength: 200, },
      distrito:             { required: true },
      departamento:         { required: true },
      provincia:            { required: true },
      ubigeo:               { required: true },

      nota:                 { minlength: 4, maxlength: 400, },

    },
    messages: {

      tipo_persona_sunat:   { required: "Campo requerido.", },
      tipo_documento:       { required: "Campo requerido.", },
      numero_documento:     { required: "Campo requerido.", },
      nombre_razonsocial:   { required: "Campo requerido.", },
      apellidos_nombrecomercial: { required: "Campo requerido.", },
      distrito:             { required: "Campo requerido.", },
      departamento:         { required: "Campo requerido.", },
      provincia:            { required: "Campo requerido.", },
      ubigeo:               { required: "Campo requerido.", },
      idpersona_trabajador: { required: "Campo requerido.", },      
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
      guardar_y_editar_cliente(e);
    },

  });

  form_validate_facturacion = $("#form-facturacion").validate({
    ignore: '',
    rules: {
      f_idpersona_cliente:      { required: true },
      f_tipo_comprobante:       { required: true },
      f_serie_comprobante:      { required: true, },
      f_observacion_documento:  { minlength: 4 },            
      f_total_recibido:         { required: true, min: 0, step: 0.01},           
      f_total_vuelto:           { required: true, step: 0.01},
      f_ua_monto_usado:         { required: true, min: 1, step: 0.01},
      f_mp_serie_comprobante:   { minlength: 4},
      // mp_comprobante:         { extension: "png|jpg|jpeg|webp|svg|pdf",  }, 
    },
    messages: {
      f_idpersona_cliente:      { required: "Campo requerido", },
      f_tipo_comprobante:       { required: "Campo requerido", },      
      f_serie_comprobante:      { required: "Campo requerido", },
      f_observacion_documento:  { minlength: "Minimo {0} caracteres", },
      // mp_comprobante:         { extension: "Ingrese imagenes validas ( {0} )", },
      f_total_recibido:         { step: "Solo 2 decimales."},       
      f_total_vuelto:           { step: "Solo 2 decimales."},
      f_ua_monto_usado:         { step: "Solo 2 decimales."},
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
  $('#f_metodo_pago_1').rules('add', { required: true, messages: {  required: "Campo requerido" } });

  $('#tipo_persona_sunat').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#tipo_documento').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#distrito').rules('add', { required: true, messages: { required: "Campo requerido" } });

  $('#idpersona_trabajador').rules('add', { required: true, messages: { required: "Campo requerido" } });
  
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cargando_search() {
  if ($('#id_buscando_tabla').length) { } else {
    $('.buscando_tabla').prepend(`<tr id="id_buscando_tabla"> 
      <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
    </tr>`);
  }  
}

function filtros() {  

  var filtro_trabajador     = $("#filtro_trabajador").select2('val');
  var filtro_tipo_persona   = $("#filtro_tipo_persona").select2('val');
  var filtro_dia_pago       = $("#filtro_dia_pago").select2('val');   
  var filtro_plan           = $("#filtro_plan").select2('val');
  var filtro_centro_poblado = $("#filtro_centro_poblado").select2('val');
  var filtro_zona_antena    = $("#filtro_zona_antena").select2('val');
  
  var nombre_trabajador     = $('#filtro_trabajador').find(':selected').text();
  var nombre_dia_pago       = ' ─ ' + $('#filtro_unidad_medida').find(':selected').text();
  var nombre_plan           = ' ─ ' + $('#filtro_plan').find(':selected').text();
  var nombre_zona_antena    = ' ─ ' + $('#filtro_zona_antena').find(':selected').text();

  
  if (filtro_trabajador == '' || filtro_trabajador == 0 || filtro_trabajador == null) { filtro_trabajador = ""; nombre_trabajador = ""; }       // filtro de trabajador  
  if (filtro_tipo_persona == '' || filtro_tipo_persona == 0 || filtro_tipo_persona == null) { filtro_tipo_persona = "";  }                 // filtro de dia pago  
  if (filtro_dia_pago == '' || filtro_dia_pago == 0 || filtro_dia_pago == null) { filtro_dia_pago = ""; nombre_dia_pago = ""; }                 // filtro de dia pago  
  if (filtro_plan == '' || filtro_plan == 0 || filtro_plan == null) { filtro_plan = ""; nombre_plan = ""; }                                     // filtro de plan
  if (filtro_centro_poblado == '' || filtro_centro_poblado == 0 || filtro_centro_poblado == null) { filtro_centro_poblado = "";  }              // filtro de plan
  if (filtro_zona_antena == '' || filtro_zona_antena == 0 || filtro_zona_antena == null) { filtro_zona_antena = ""; nombre_zona_antena = ""; }  // filtro de zona antena

  $('#id_buscando_tabla').html(`<th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_trabajador} ${nombre_dia_pago} ${nombre_plan}...</th>`);
  //console.log(filtro_categoria, fecha_2, filtro_plan, comprobante);

  filtro_trabajador_r = filtro_trabajador; filtro_tipo_persona_r = filtro_tipo_persona;  filtro_dia_pago_r = filtro_dia_pago; 
  filtro_plan_r = filtro_plan; filtro_centro_poblado_r = filtro_centro_poblado; filtro_zona_antena_r = filtro_zona_antena;
  cant_tab_cliente(filtro_tipo_persona,filtro_centro_poblado);  
  filtrar_grupo(opcion_r);
}

function filtrar_grupo(opcion ) {
  opcion_r = opcion; console.log(opcion);
  
  if (opcion == 'tabla_deudores') {    
    tabla_principal_cliente_deudor(opcion, filtro_trabajador_r, filtro_tipo_persona_r, filtro_dia_pago_r, filtro_plan_r, filtro_centro_poblado_r, filtro_zona_antena_r);
  } else if (opcion == 'tabla_deudores_parcial'){
    tabla_principal_cliente_deudor_parcial(opcion, filtro_trabajador_r, filtro_tipo_persona_r, filtro_dia_pago_r, filtro_plan_r, filtro_centro_poblado_r, filtro_zona_antena_r)
  } else if (opcion == 'tabla_no_deuda'){
    tabla_principal_cliente_no_deudor(opcion, filtro_trabajador_r, filtro_tipo_persona_r, filtro_dia_pago_r, filtro_plan_r, filtro_centro_poblado_r, filtro_zona_antena_r);    
  } else if (opcion == 'tabla_todos'){
    tabla_principal_cliente(opcion, filtro_trabajador_r, filtro_tipo_persona_r, filtro_dia_pago_r, filtro_plan_r, filtro_centro_poblado_r, filtro_zona_antena_r);
  }  
}

function cargando_search_pago_all() {
  $('#id_buscando_tabla_pago_all').show();  
}

function filtros_pago_all() {  

  var filtro_trabajador   = $("#filtro_p_all_trabajador").select2('val');
  var filtro_dia_pago     = $("#filtro_p_all_dia_pago").select2('val');  
  var filtro_anio_pago     = $("#filtro_p_all_anio_pago").select2('val');  
  var filtro_plan         = $("#filtro_p_all_plan").select2('val');
  var filtro_zona_antena  = $("#filtro_p_all_zona_antena").select2('val');
  
  var nombre_trabajador   = $('#filtro_p_all_trabajador').find(':selected').text();
  var nombre_dia_pago     = ' ─ ' + $('#filtro_p_all_dia_pago').find(':selected').text();
  var nombre_anio_pago     = ' ─ ' + $('#filtro_p_all_anio_pago').find(':selected').text();
  var nombre_plan         = ' ─ ' + $('#filtro_p_all_plan').find(':selected').text();
  var nombre_zona_antena  = ' ─ ' + $('#filtro_p_all_zona_antena').find(':selected').text();

  
  if (filtro_trabajador == '' || filtro_trabajador == 0 || filtro_trabajador == null) { filtro_trabajador = ""; nombre_trabajador = ""; }       // filtro de trabajador  
  if (filtro_dia_pago == '' || filtro_dia_pago == 0 || filtro_dia_pago == null) { filtro_dia_pago = ""; nombre_dia_pago = ""; }                 // filtro de dia pago  
  if (filtro_anio_pago == '' || filtro_anio_pago == 0 || filtro_anio_pago == null) { filtro_anio_pago = ""; nombre_anio_pago = ""; }                 // filtro de dia pago  
  if (filtro_plan == '' || filtro_plan == 0 || filtro_plan == null) { filtro_plan = ""; nombre_plan = ""; }                                     // filtro de plan
  if (filtro_zona_antena == '' || filtro_zona_antena == 0 || filtro_zona_antena == null) { filtro_zona_antena = ""; nombre_zona_antena = ""; }  // filtro de zona antena

  // $('#id_buscando_tabla').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_trabajador} ${nombre_dia_pago} ${nombre_plan}...`);
  //console.log(filtro_categoria, fecha_2, filtro_plan, comprobante);

  ver_pagos_all_cliente(filtro_trabajador,  filtro_dia_pago, filtro_anio_pago, filtro_plan, filtro_zona_antena);
}

function cambiarImagen() {
  var imagenInput = document.getElementById('imagen');
  imagenInput.click();
}

function removerImagen() {
  // var imagenMuestra = document.getElementById('imagenmuestra');
  // var imagenActualInput = document.getElementById('imagenactual');
  // var imagenInput = document.getElementById('imagen');
  // imagenMuestra.src = '../assets/images/faces/9.jpg';
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg");
  // imagenActualInput.value = '';
  // imagenInput.value = '';
  $("#imagen").val("");
  $("#imagenactual").val("");
}

// Esto se encarga de mostrar la imagen cuando se selecciona una nueva
document.addEventListener('DOMContentLoaded', function () {
  var imagenMuestra = document.getElementById('imagenmuestra');
  var imagenInput = document.getElementById('imagen');

  imagenInput.addEventListener('change', function () {
    if (imagenInput.files && imagenInput.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) { imagenMuestra.src = e.target.result; }
      reader.readAsDataURL(imagenInput.files[0]);
    }
  });
});



function ver_img(img, nombre) {
  $(".title-ver-imgenes").html(`- ${nombre}`);
  $('#modal-ver-imgenes').modal("show");
  $('.html_modal_ver_imgenes').html(doc_view_extencion(img, 'assets/modulo/persona/perfil', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on: 'grab' });
}

function reload_select(r_text) {

  switch (r_text) {
    case 'filtro_trabajador':
      lista_select2("../ajax/cliente.php?op=select2_filtro_trabajador", '#filtro_trabajador',     null, '.charge_filtro_trabajador');
    break;  
    case 'filtro_tipo_persona':
      
    break; 
    case 'filtro_dia':
      lista_select2("../ajax/cliente.php?op=select2_filtro_dia_pago",   '#filtro_dia_pago',       null, '.charge_filtro_dia_pago');
    break;
    case 'filtro_plan':
      lista_select2("../ajax/cliente.php?op=select2_filtro_plan",       '#filtro_plan',           null, '.charge_filtro_plan');
    break;
    case 'filtro_centro_poblado':
      lista_select2("../ajax/ajax_general.php?op=select2_centro_poblado_cliente", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado');
    break;
    case 'filtro_zona_antena':
      lista_select2("../ajax/cliente.php?op=select2_filtro_zona_antena",'#filtro_zona_antena',    null, '.charge_filtro_zona_antena');
    break;    
    

    case 'filtro_p_all_trabajador':
      lista_select2("../ajax/cliente.php?op=select2_filtro_trabajador", '#filtro_p_all_trabajador', null, '.charge_filtro_p_all_trabajador');
    break;
    case 'filtro_dia_pago':
      lista_select2("../ajax/cliente.php?op=select2_filtro_dia_pago",   '#filtro_p_all_dia_pago',   null, '.charge_filtro_p_all_dia_pago');
    break;
    case 'filtro_anio_pago':
      lista_select2("../ajax/cliente.php?op=select2_filtro_anio_pago",  '#filtro_p_all_anio_pago',  moment().format('YYYY'), '.charge_filtro_p_all_anio_pago');
    break;    
    case 'filtro_p_all_plan':
      lista_select2("../ajax/cliente.php?op=select2_filtro_plan",       '#filtro_p_all_plan',       null, '.charge_filtro_p_all_plan');
    break;
    case 'filtro_p_all_zona_antena':
      lista_select2("../ajax/cliente.php?op=select2_filtro_zona_antena",'#filtro_p_all_zona_antena',null, '.charge_filtro_p_all_zona_antena');
    break;    
    
    default:
      console.log('Caso no encontrado.');
  }
 
}

function crear_dias_pagos(input) {
  $(input).append(`<option value="TODOS">TODOS</option>`);
  for (let index = 1; index <= 31; index++) {
    $(input).append(`<option value="${index}">Día ${index}</option>`); 
  }
  
}

function crear_anio_pagos(input) {
  var anio =parseFloat(moment().format('YYYY')) + (1);
  var html = "";  
  for (let i = 1; i < 6; i++) { anio--; html = html.concat(`<option value="${anio}">${anio}</option> `); }
  $(input).html(html);
}

function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
}


(function () {
  "use strict"  

  // UPLOADS ===================================

  /* filepond */
  FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateSize,
    FilePondPluginFileEncode,
    FilePondPluginImageEdit,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
      
  );

  // Configura opciones globales para FilePond
  FilePond.setOptions({
    allowMultiple: true, // Permitir subir múltiples archivos
    maxFiles: 6, // Máximo número de archivos permitidos
    maxFileSize: '3MB', // Tamaño máximo por archivo
    acceptedFileTypes: ['image/*', 'application/pdf'], // Tipos permitidos
    // server: {
    //     process: '/ruta-del-servidor', // URL donde se enviarán los archivos
    //     revert: null, // URL para revertir la subida (opcional)
    //     headers: {
    //         'X-CSRF-TOKEN': csrfToken // Si usas CSRF, asegúrate de pasar el token aquí
    //     }
    // }
  });

  /* multiple upload */
  const MultipleElement = document.querySelector('.multiple-filepond');
  file_pond_mp_comprobante = FilePond.create(MultipleElement, FilePond_Facturacion_LabelsES );
  filePondInstances.push(file_pond_mp_comprobante); // Guarda la instancia en el arreglo

})();

function ver_comprobante_emitido(idventa, tipo_comprobante, num_comprobante) { 

  $("#modal_ver_comprobante_deudor").modal("show");
  $(".btn_formato_ticket").attr("onclick",`ver_formato_ticket(${idventa}, ${tipo_comprobante})`);
  $(".btn_formato_a4").attr("onclick",`ver_formato_a4_completo(${idventa}, ${tipo_comprobante})`);

  $(".btn_formato_ticket").click();
  //$(".btn_formato_a4").click();

  $(".serie_comp").text(num_comprobante);
  
}

// ::::::::::::::::::::::::::::::::::::::::::::: FORMATOS DE IMPRESION :::::::::::::::::::::::::::::::::::::::::::::

function ver_formato_ticket(idventa, tipo_comprobante) {
  $("#modal_ver_comprobante_deudor .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl").addClass("modal-md");
  
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $(".formato_ticket").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);

  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $(".formato_ticket").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);

  } else if (tipo_comprobante == '07') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $(".formato_ticket").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);

  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
     $(".formato_ticket").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
}

function ver_formato_a4_completo(idventa, tipo_comprobante) {  
  $("#modal_ver_comprobante_deudor .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
     $(".formato_a4").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
   
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
     $(".formato_a4").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
   
  } else if (tipo_comprobante == '07') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
     $(".formato_a4").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
   
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $(".formato_a4").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
  
}
