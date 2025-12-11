var tabla_cuenta_bancarias;

function init(){

  // listar_tabla();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-cuenta-bancaria").submit(); }  });	

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════  

  lista_select2("../ajax/reporte_bitacora_sistema.php?op=select2_usuario", '#filtro_usuario', null, '.charge_filtro_usuario');
  lista_select2("../ajax/reporte_bitacora_sistema.php?op=select2_modulo", '#filtro_modulo', null, '.charge_filtro_modulo');

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════ 
  $("#filtro_usuario").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_modulo").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  

}

function templateBanco (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span><img src="${baseUrl}" class="img-circle mr-2 w-25px" ${onerror} />${state.text}</span>`);
  return $state;
}

//  :::::::::::::::: P R O D U C T O :::::::::::::::: 

function limpiar_form_cuenta_bancaria(){

	$('#idcuenta_bancaria').val('');  		
	$('#tipo_cuenta').val('').trigger('change'); 
	$('#moneda').val('').trigger('change');
	$('#idbancos').val('').trigger('change'); 
	
	$('#cta_cte').val('');
	$('#cci').val('');
	$('#saldo_inicial').val('0');	

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
	}
}

function listar_tabla(filtro_fecha_i = '', filtro_fecha_f = '', filtro_usuario = '', filtro_modulo = '' ){
  tabla_cuenta_bancarias = $('#tabla-bitacora-sistema').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-9 col-xxl-10 pt-2'f><'col-md-5 col-lg-4 col-xl-3 col-xxl-2 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-sm px-2 btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_cuenta_bancarias) { tabla_cuenta_bancarias.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-sm px-2 btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de Bitacora', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-sm px-2 btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de Bitacora', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-sm px-2 btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: `../ajax/reporte_bitacora_sistema.php?op=listar_tabla_principal&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_usuario=${filtro_usuario}&filtro_modulo=${filtro_modulo}`,
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
      // columna: #
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      
    },
    language: {
      lengthMenu: "_MENU_", search: "",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },    
    initComplete: function () {
      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 25,
    "order": [[0, "asc"]],
    columnDefs:[
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY hh:mm:ss A'), },
      // { targets: [10,11,12,13,14],  visible: false,  searchable: false,  },
      // { targets: [9,10], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

function mostrar_cuenta_bancaria(idcuenta_bancaria, duplicar = false){
  limpiar_form_cuenta_bancaria();
	show_hide_form(2);
	$('#cargando-1-fomulario').hide();	$('#cargando-2-fomulario').show(); 
	$.post("../ajax/reporte_bitacora_sistema.php?op=mostrar_editar", { idcuenta_bancaria: idcuenta_bancaria }, function (e, status) {
		e = JSON.parse(e);

    if (duplicar == true) {  } else {
      $('#idcuenta_bancaria').val(e.data.idcuenta_bancaria);
    }

    $('#tipo_cuenta').val(e.data.tipo_cuenta).trigger('change'); 
    $('#moneda').val(e.data.moneda).trigger('change');
    $('#idbancos').val(e.data.idbancos).trigger('change'); 
    
    $('#cta_cte').val(e.data.cta_cte);
    $('#cci').val(e.data.cci);
    $('#saldo_inicial').val(e.data.saldo_inicial);
    

    $('#cargando-1-fomulario').show();	$('#cargando-2-fomulario').hide();
    $('#form-agregar-cuenta_bancaria').valid();
	});	
}

// ::::::::::::   I N I T   :::::::::::::::::::

$(document).ready(function () {
  init();
});

function mayus(e) {
  e.value = e.value.toUpperCase();
}

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_fecha_i        = $("#filtro_fecha_i").val() == '' || $("#filtro_fecha_i").val() == null ? '' : $("#filtro_fecha_i").val() ;
  var filtro_fecha_f        = $("#filtro_fecha_f").val() == '' || $("#filtro_fecha_f").val() == null ? '' : $("#filtro_fecha_f").val() ;
  var filtro_usuario        = $("#filtro_usuario").val() == '' || $("#filtro_usuario").val() == null ? '' : $("#filtro_usuario").val() ;
  var filtro_modulo         = $("#filtro_modulo").val() == '' || $("#filtro_modulo").val() == null ? '' : $("#filtro_modulo").val() ;
  
  var nombre_filtro_fecha_i = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_usuario        = ' ─ ' + $('#filtro_usuario').find(':selected').text();
  var nombre_modulo         = ' ─ ' + $('#filtro_modulo').find(':selected').text();

  // filtro de fechas
  if (filtro_fecha_i == '' || filtro_fecha_i == 0 || filtro_fecha_i == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  // filtro de fechas
  if (filtro_fecha_f == '' || filtro_fecha_f == 0 || filtro_fecha_f == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }
  // filtro de user
  if (filtro_usuario == '' || filtro_usuario == 0 || filtro_usuario == null) { filtro_usuario = ""; nombre_usuario = ""; }  
  // filtro de user
  if (filtro_modulo == '' || filtro_modulo == 0 || filtro_modulo == null) { filtro_modulo = ""; nombre_modulo = ""; }  

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ${nombre_usuario} ${nombre_modulo}...`);
 
  listar_tabla(filtro_fecha_i, filtro_fecha_f, filtro_usuario, filtro_modulo);
}

function ver_img(img, nombre) {
	$(".title-modal-img").html(`-${nombre}`);
  $('#modal-ver-img').modal("show");
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/cuenta_bancarias', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}

function reload_filtro_usuario() { lista_select2("../ajax/reporte_bitacora_sistema.php?op=select2_usuario", '#filtro_usuario', null, '.charge_filtro_usuario'); }
function reload_filtro_modulo() { lista_select2("../ajax/reporte_bitacora_sistema.php?op=select2_modulo", '#filtro_modulo', null, '.charge_filtro_modulo'); }

function reload_filtro_fecha_i() {  $('#filtro_fecha_i').val('').trigger('change'); }
function reload_filtro_fecha_f() {  $('#filtro_fecha_f').val('').trigger('change'); }
