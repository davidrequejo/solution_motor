var tabla_principal_facturacion;
var tabla_productos;
var form_validate_facturacion;
var tabla_ver_mas_detalle_facturacion;

var array_data_venta = [];
var file_pond_mp_comprobante;
var cambio_de_tipo_comprobante ;

var filtro_estado_sunat = "" ;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_tipo_documento = new Choices('#tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

async function init(){

  // filtros(); // Listamos la tabla principal
  $(".btn-boleta").click();   // Selecionamos la BOLETA
  mini_reporte();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-venta").submit(); }  });
  $("#guardar_registro_proveedor").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-proveedor").submit(); } });
  $("#guardar_registro_producto").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/cotizacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante');
  lista_select2("../ajax/cotizacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente');

  lista_select2("../ajax/cotizacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante');
  lista_select2("../ajax/cotizacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente');

  lista_select2("../ajax/cotizacion.php?op=select2_cliente", '#idpersona_cliente', null);

  lista_select2("../ajax/cotizacion.php?op=select_categoria", '#categoria', null);
  lista_select2("../ajax/cotizacion.php?op=select_u_medida", '#u_medida', null);
  lista_select2("../ajax/cotizacion.php?op=select_marca", '#marca', null);

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_tipo_documento", choice_tipo_documento, null);  
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_banco", choice_idbanco, null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#idpersona_cliente").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });  
  $("#metodo_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#tiempo_entrega").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#validez_cotizacion").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_md_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_md_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });  

  await activar_btn_agregar(); // Esperamos a al carga total de los datos para poder: CREAR
}

async function activar_btn_agregar() {
  $(".btn-agregar").show();
}

function templateCliente (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../dist/docs/persona/perfil/${state.title}`: '../dist/svg/user_default.svg'; 
  var onerror = `onerror="this.src='../dist/svg/user_default.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
  return $state;
}

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
    if (localStorage.getItem('nube_cargo') == 'TÉCNICO DE RED') {
      $("#div-tabla").show().removeClass('col-xl-9').addClass('col-xl-12');
      $("#div-mini-reporte").hide();
    } else {
      $("#div-tabla").show().removeClass('col-xl-12').addClass('col-xl-9');
      $("#div-mini-reporte").show();      
    }		
    
		$("#div-formulario").hide();
    $("#div-tabla-mas-detalles").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) { // FORMULARIO FACTURACION
		$("#div-tabla").hide();
    $("#div-mini-reporte").hide();
		$("#div-formulario").show();
    $("#div-tabla-mas-detalles").hide();

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();

  } else if (flag == 3) { // TABLA MAS DETALLE FACTURACION
		$("#div-tabla").hide();
    $("#div-mini-reporte").hide();
		$("#div-formulario").hide();
		$("#div-tabla-mas-detalles").show();

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();
	}
}

function mini_reporte() {

  $(".vw_total_vendido").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $(".vw_total_pendiente").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  $.getJSON(`../ajax/cotizacion.php?op=mini_reporte`, function (e, textStatus, jqXHR) {

    if (e.status == true) {      

      e.data.coun_comprobante.forEach((val, key) => {
        if (val.cot_estado == 'VENDIDO') { $(".vw_count_factura").html( val.cantidad ); }
        if (val.cot_estado == 'PENDIENTE') { $(".vw_count_boleta").html( val.cantidad ); }
      });
    
      $(".vw_total_vendido").html( `${formato_miles(e.data.vendido)}` ).addClass('count-up');
      $(".vw_total_vendido_p").html( `${e.data.vendido_p >= 0? '<i class="ri-arrow-up-s-line me-1 align-middle"></i>' : '<i class="ri-arrow-down-s-line me-1 align-middle"></i>'} ${(e.data.vendido_p)}%` );
      e.data.vendido_p >= 0? $(".vw_total_vendido_p").addClass('text-success').removeClass('text-danger') : $(".vw_total_vendido_p").addClass('text-danger').removeClass('text-success') ;

      $(".vw_total_pendiente").html( `${formato_miles(e.data.pendiente)}` ).addClass('count-up');
      $(".vw_total_pendiente_p").html( `${e.data.pendiente_p >= 0? '<i class="ri-arrow-up-s-line me-1 align-middle"></i>' : '<i class="ri-arrow-down-s-line me-1 align-middle"></i>'} ${(e.data.pendiente_p)}%` );
      e.data.pendiente_p >= 0? $(".vw_total_pendiente_p").addClass('text-success').removeClass('text-danger') : $(".vw_total_pendiente_p").addClass('text-danger').removeClass('text-success') ;

 
      // MINI CHART
      var options = {
        series: [
          { name: 'Vendido', data: e.data.vendido_line }, 
          { name: 'Pendinte', data: e.data.pendiente_line },          
        ],
        chart: { type: 'bar', height: 210, stacked: true },
        plotOptions: { bar: { horizontal: false, columnWidth: '25%', endingShape: 'rounded', }, },
        grid: { borderColor: '#f2f5f7', },
        dataLabels: { enabled: false },
        colors: ["#28d193", "#ffbe14"],
        stroke: { show: true, colors: ['transparent'] },
        xaxis: {
          categories: e.data.mes_nombre,
          labels: {
            show: true,
            style: { colors: "#8c9097", fontSize: '11px', fontWeight: 600, cssClass: 'apexcharts-xaxis-label', },
          }
        },
        yaxis: {
          title: { style: { color: "#8c9097", } },
          labels: {
            show: true,
            style: { colors: "#8c9097", fontSize: '11px', fontWeight: 600, cssClass: 'apexcharts-xaxis-label', },
          }
        },
        fill: { opacity: 1 },
        tooltip: { y: {  formatter: function (val) { return "S/ " + val ; } } }
      };
      var chart = new ApexCharts(document.querySelector("#invoice-list-stats"), options);
      chart.render();
    } else {
      ver_errores(e);
    }

  }).fail( function(e) { ver_errores(e); } );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   F A C T U R A C I O N :::::::::::::::::::::::::::::::::::::::::::::

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', '300px', true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form_venta(){

  array_data_venta = [];
  $("#idventa").val('');

  $("#idpersona_cliente").val('').trigger('change'); 
  $("#metodo_pago").val('').trigger('change'); 
  $("#tiempo_entrega").val('').trigger('change'); 
  $("#validez_cotizacion").val('').trigger('change'); 
  $("#observacion_documento").val('');   
  $("#codigob").val('');  
    
  $("#venta_total").val("");     
  $(".venta_total").html("0");

  $(".venta_subtotal").html("<span>S/</span> 0.00");
  $("#venta_subtotal").val("");

  $(".venta_descuento").html("<span>S/</span> 0.00");
  $("#venta_descuento").val("");

  $(".venta_igv").html("<span>S/</span> 0.00");
  $("#venta_igv").val("");

  $(".venta_total").html("<span>S/</span> 0.00");
  $("#venta_total").val("");

  $(".filas").remove();

  cont = 0;

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat){
  
  tabla_principal_facturacion = $("#tabla-ventas").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_principal_facturacion) { tabla_principal_facturacion.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cotizacion.php?op=listar_tabla_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}&filtro_estado_sunat=${filtro_estado_sunat}`,
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
      // columna: Opciones
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: Monto
      if (data[6] != '') { $("td", row).eq(6).addClass("text-nowrap"); }
      // columna: Monto
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap text-center"); }
      // columna: Boucher
      if (data[8] != '') { $("td", row).eq(8).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 5 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 5 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [5], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

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
      return fetch("../ajax/cotizacion.php?op=guardar_editar_facturacion", {
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

        if ($('#crear_y_mostrar').is(':checked')) {
          $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
          var rutacarpeta = "../reportes/A4FormatHtmlCotizacion.php?id=" + result.value.data;
          $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
          $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
          $("#modal-imprimir-comprobante").modal("show");
        } 

      } else if ( result.value.status == 'error_personalizado'){        
        tabla_principal_facturacion.ajax.reload(null, false);
        limpiar_form_venta(); show_hide_form(1); ver_errores(result.value);
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

function mostrar_detalle_venta(idventa){
  $("#modal-detalle-venta").modal("show");

  $.post("../ajax/cotizacion.php?op=mostrar_detalle_venta", { idventa: idventa }, function (e, status) {          
      
    $('#custom-tabContent').html(e);      
    $('#custom-datos1_html-tab').click(); // click para ver el primer - Tab Panel
    $(".jq_image_zoom").zoom({ on: "grab" });      
    $("#excel_venta").attr("href",`../reportes/export_xlsx_venta_tours.php?id=${idventa}`);      
    $("#print_pdf_venta").attr("href",`../reportes/comprobante_venta_tours.php?id=${idventa}`);    
    
  }).fail( function(e) { ver_errores(e); } );

}

function cambiar_estado_vendido(idventa, estado){  

  Swal.fire({
    title: "¿Está seguro de cambiar el estado?",
    html: "Verifica que este <b>cotización</b> haya sido <b>vendio</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, cambiar!",
    showLoaderOnConfirm: true,
    preConfirm: (value) => {
      console.log(value);
      if (!value) {
        Swal.showValidationMessage('La <i class="fw-bold">&nbsp;NOTA&nbsp;</i> es requerido.')
      }else{            
        return fetch(`../ajax/cotizacion.php?op=cambiar_estado_vendido&idventa=${idventa}&estado=${estado}`).then(response => {
          console.log(response);
          if (!response.ok) { throw new Error(response.statusText); }
          return response.json();
        }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); });
      }          
    },
    allowOutsideClick: () => !Swal.isLoading()   
  }).then((result) => {
    if (result.isConfirmed) {
      if (result.value.status == true){
        Swal.fire("Correcto!", "Estado actualizado", "success");
        tabla_principal_facturacion.ajax.reload(null, false);       
      } else if ( result.value.status == 'error_personalizado'){        
        tabla_principal_facturacion.ajax.reload(null, false);
        ver_errores(result.value);
      } else {
        ver_errores(result.value);
      }      
    }
  });  

}

function eliminar_papelera_venta(idventa, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/cotizacion.php?op=papelera",
    "../ajax/cotizacion.php?op=eliminar", 
    idventa, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>venta: ${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}


// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR SERIES :::::::::::::::::::::::::::::::::::::::::::::
function ver_series_comprobante(input) {
  if (cambio_de_tipo_comprobante == '07') { limpiar_form_venta(); } // Limpiamos si el comprobante anterior era: Nota de Credito

  $("#serie_comprobante").html('');
  $(".charge_serie_comprobante").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  var tipo_comprobante = $(input).val() == ''  || $(input).val() == null ? '' : $(input).val();
  var nc_tp = ""; // En caso de ser Nota de Credito
  $('#tipo_comprobante_hidden').val(tipo_comprobante);

  $(".div_idpersona_cliente").show();  
  $(".div_agregar_producto").show();
  $(".div_m_pagos").show();

  // VALIDANDO SEGUN: TIPO DE COMPROBANTE
  if (form_validate_facturacion) { // FORM-VALIDATE  
    if ( tipo_comprobante == '01') {   
      $("#idsunat_c01").val(2); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } });       
    } else if ( tipo_comprobante == '03') {
      $("#idsunat_c01").val(3); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } });    
    } else if ( tipo_comprobante == '12') {
      $("#idsunat_c01").val(12); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante      
      $("#periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
    }
  }

  $.getJSON("../ajax/facturacion.php?op=select2_series_comprobante", { tipo_comprobante: tipo_comprobante, nc_tp:nc_tp },  function (e, status) {    
    if (e.status == true) {      
      $("#serie_comprobante").html(e.data);
      $(".charge_serie_comprobante").html('');
      $("#form-facturacion").valid();
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );

  cambio_de_tipo_comprobante = tipo_comprobante;
}

// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR COBROS :::::::::::::::::::::::::::::::::::::::::::::


// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR ANTICIPOS :::::::::::::::::::::::::::::::::::::::::::::


// ::::::::::::::::::::::::::::::::::::::::::::: CLIENTE VALIDO :::::::::::::::::::::::::::::::::::::::::::::


// ::::::::::::::::::::::::::::::::::::::::::::: FORMATOS DE IMPRESION :::::::::::::::::::::::::::::::::::::::::::::

function ver_formato_a4_completo(idventa, tipo_comprobante) {  
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/A4FormatHtmlCotizacion.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/A4FormatHtmlCotizacion.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    var rutacarpeta = "../reportes/A4FormatHtmlCotizacion.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/A4FormatHtmlCotizacion.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '100') {
    var rutacarpeta = "../reportes/A4FormatHtmlCotizacion.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");


  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }  
  
}


// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   N O T A   D E   C R E D I T O :::::::::::::::::::::::::::::::::::::::::::::


// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   V E R   E S T A D O   S U N A T  :::::::::::::::::::::::::::::::::::::::::::::

function ver_estado_documento(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' ) {
   
    $("#html-ver-estado").html(`<div class="row" >
      <div class="col-lg-12 text-center">
        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="bx-flashing">Cargando...</h4>
      </div>
    </div>`);
    $("#modal-ver-estado").modal('show');
    $.getJSON(`../ajax/cotizacion.php?op=ver_estado_documento`, {idventa: idventa}, function (e, textStatus, jqXHR) {
      if (e.status == true) {
        $("#modal-ver-estado-label").html(`─ VER ESTADO: ${e.data.serie_comprobante}-${e.data.numero_comprobante}`);
        $("#html-ver-estado").html(`
          <b>Estado:</b> ${e.data.sunat_estado} <br>
          <b>Mensaje:</b> ${e.data.sunat_mensaje} <br>
          <b>Observacion:</b> ${e.data.sunat_observacion} <br>
          <b>Codigo:</b> ${e.data.sunat_code} <br>
          <b>Error:</b> ${e.data.sunat_error} <br>
        `);
      } else {
        ver_errores(e);
      }      
    }).fail( function(e) { ver_errores(e); } );
  } else {  
    toastr_warning('Sin estado SUNAT', 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.');
  }
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   P R O D U C T O S :::::::::::::::::::::::::::::::::::::::::::::
function limpiar_form_producto(){

	$('#idproducto').val('');
  
	$('#codigo').val('');
	$('#categoria').val('');
	$('#u_medida').val('58');
	$('#marca').val('');
	$('#nombre').val('');
	$('#descripcion').val('');
	$('#stock').val('');
	$('#stock_min').val('');
	$('#precio_v').val('');
	$('#precio_c').val('');
	$('#precio_x_mayor').val('');
	$('#precio_dist').val('');
	$('#precio_esp').val('');

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/productos/no-producto.png");
  $("#imagenmuestra").attr("src", "../assets/modulo/productos/no-producto.png").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/productos/no-producto.png';
  }


  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

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
      url: `../ajax/cotizacion.php?op=listar_tabla_producto&tipo_producto=${tipo}`,
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
					sw_success('Exito', 'producto guardado correctamente.');
					tabla_productos.ajax.reload(null, false);
          limpiar_form_producto();
          $("#modal-agregar-producto").modal('hide');
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

function cambiarImagenProducto() {
	var imagenInput = document.getElementById('imagenProducto');
	imagenInput.click();
}

function removerImagenProducto() {
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


$(document).ready(function () {
  init(); 
  filtros();
});

function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   MAS DETALLES FACTURACION :::::::::::::::::::::::::::::::::::::::::::::
var active_filtro_md = false;
var filtro_estado_sunat_md = '';
function view_mas_detalle() {
  active_filtro_md = true;
  var filtro_fecha_i      = $("#filtro_md_fecha_i").val();
  var filtro_fecha_f      = $("#filtro_md_fecha_f").val();  
  var filtro_cliente      = $("#filtro_md_cliente").select2('val') == null ? '' : $("#filtro_md_cliente").select2('val');
  var filtro_comprobante  = $("#filtro_md_comprobante").select2('val')== null ? '' : $("#filtro_md_comprobante").select2('val');
  show_hide_form(3);
  tbla_mas_detalle(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat_md)
}

function tbla_mas_detalle(filtro_fecha_i='', filtro_fecha_f='', filtro_cliente='', filtro_comprobante='', filtro_estado_sunat='') {
  tabla_ver_mas_detalle_facturacion = $("#tabla-facturacion-detalle").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', classtbla_mas_detalle: "thf buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_ver_mas_detalle_facturacion) { tabla_ver_mas_detalle_facturacion.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cotizacion.php?op=listar_tabla_ver_mas_detalle_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}&filtro_estado_sunat=${filtro_estado_sunat}`,
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
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: Opciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: Opciones
      if (data[2] != '') { $("td", row).eq(2).addClass("fs-11 text-nowrap text-center"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      // columna: Monto
      if (data[6] != '') { $("td", row).eq(6).addClass("text-nowrap"); }
      // columna: Monto
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 9 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 9 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 25,
    "order": [[0, "desc"]],
    columnDefs: [      
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [9,10,11], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function filtrar_solo_estado_sunat_md(estado, etiqueta) {  
  $(".md-otros-filtros").find("li>a").removeClass("active");
  $(".md-otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat_md = estado; filtros_md();
}

function filtros_md() {  
  if (active_filtro_md == true) {    
  
    var filtro_fecha_i      = $("#filtro_md_fecha_i").val();
    var filtro_fecha_f      = $("#filtro_md_fecha_f").val();  
    var filtro_cliente      = $("#filtro_md_cliente").select2('val');
    var filtro_comprobante  = $("#filtro_md_comprobante").select2('val');
    
    var nombre_filtro_fecha_i     = $('#filtro_md_fecha_i').val();
    var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_md_fecha_f').val();
    var nombre_filtro_cliente     = ' ─ ' + $('#filtro_md_cliente').find(':selected').text();
    var nombre_filtro_comprobante = ' ─ ' + $('#filtro_md_comprobante').find(':selected').text();

    // filtro de fechas
    if (filtro_fecha_i == '' || filtro_fecha_i == 0 || filtro_fecha_i == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
    if (filtro_fecha_f == '' || filtro_fecha_f == 0 || filtro_fecha_f == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }

    // filtro de cliente
    if (filtro_cliente == '' || filtro_cliente == 0 || filtro_cliente == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }

    // filtro de comprobante
    if (filtro_comprobante == '' || filtro_comprobante == 0 || filtro_comprobante == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }

    $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_fecha_i} ${filtro_fecha_f} ${nombre_filtro_cliente}...`);
    //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

    tbla_mas_detalle(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat_md);
  }
}

// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

  form_validate_facturacion = $("#form-facturacion").validate({
    ignore: '',
    rules: {
      idpersona_cliente:      { required: true },
      tipo_comprobante:       { required: true },
      serie_comprobante:      { required: true, },
      observacion_documento:  { minlength: 4 },      
      metodo_pago:            { required: true},   
    },
    messages: {
      idpersona_cliente:      { required: "Campo requerido", },
      tipo_comprobante:       { required: "Campo requerido", },      
      serie_comprobante:      { required: "Campo requerido", },
      observacion_documento:  { minlength: "Minimo {0} caracteres", }, 
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

  $('#distrito').on('change', function() { $(this).trigger('blur'); });
  $("#form-agregar-proveedor").validate({
    ignore: "",
    rules: {           
      tipo_documento:           { required: true, minlength: 1, maxlength: 2, },       
      numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      apellidos_nombrecomercial:{ required: true, minlength: 4, maxlength: 200, },       
      correo:    			          { minlength: 4, maxlength: 100, },       
      celular:    			        { minlength: 8, maxlength: 9, },       

      direccion:    			      { minlength: 4, maxlength: 200, },       
      distrito:    			        { required: true, },       
      departamento:    			    { required: true, },       
      provincia:    			      { required: true, },  
      ubigeo:    			          { required: true, },

      idbanco:    			        { required: true, },
      cuenta_bancaria:    			{ minlength: 4, maxlength: 45, },
      cci:    			            { minlength: 4, maxlength: 45, },
			
    },
    messages: {     
      tipo_documento:    			  { required: "Campo requerido", },
      numero_documento:    			{ required: "Campo requerido", }, 
      nombre_razonsocial:    		{ required: "Campo requerido", }, 
      apellidos_nombrecomercial:{ required: "Campo requerido", }, 
      correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      celular:    			        { minlength: "Mínimo {0} caracteres.", }, 

      direccion:    			      { minlength: "Mínimo {0} caracteres.", },
      distrito:    			        { required: "Campo requerido", }, 
      departamento:    			    { required: "Campo requerido", }, 
      provincia:    			      { required: "Campo requerido", }, 
      ubigeo:    			          { required: "Campo requerido", },

      idbanco:    			        { required: "Campo requerido", }, 
      cuenta_bancaria:    			{ minlength: "Mínimo {0} caracteres.", }, 
      cci:    			            { minlength: "Mínimo {0} caracteres.", }, 
      titular_cuenta:    			  { minlength: "Mínimo {0} caracteres.", },  

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
      guardar_proveedor(e);      
    },
  });
  $('#distrito').rules('add', { required: true, messages: {  required: "Campo requerido" } });

});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_fecha_i      = $("#filtro_fecha_i").val();
  var filtro_fecha_f      = $("#filtro_fecha_f").val();  
  var filtro_cliente      = $("#filtro_cliente").select2('val');
  var filtro_comprobante  = $("#filtro_comprobante").select2('val');
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_cliente     = ' ─ ' + $('#filtro_cliente').find(':selected').text();
  var nombre_filtro_comprobante = ' ─ ' + $('#filtro_comprobante').find(':selected').text();

  // filtro de fechas
  if (filtro_fecha_i == '' || filtro_fecha_i == 0 || filtro_fecha_i == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  if (filtro_fecha_f == '' || filtro_fecha_f == 0 || filtro_fecha_f == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }

  // filtro de cliente
  if (filtro_cliente == '' || filtro_cliente == 0 || filtro_cliente == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }

  // filtro de comprobante
  if (filtro_comprobante == '' || filtro_comprobante == 0 || filtro_comprobante == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_fecha_i} ${filtro_fecha_f} ${nombre_filtro_cliente}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat);
}

function filtrar_solo_estado_sunat(estado, etiqueta) {  
  $(".otros-filtros").find("li>a").removeClass("active");
  $(".otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat = estado; filtros();
}

function reload_idpersona_cliente(){ lista_select2("../ajax/cotizacion.php?op=select2_cliente", '#idpersona_cliente', null, '.charge_idpersona_cliente'); }

function reload_filtro_fecha_i(){ $('#filtro_fecha_i').val("").trigger("change") } 
function reload_filtro_fecha_f(){ $('#filtro_fecha_f').val("").trigger("change") } 
function reload_filtro_cliente(){ lista_select2("../ajax/cotizacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente'); } 
function reload_filtro_comprobante(){ lista_select2("../ajax/cotizacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante'); }

function reload_filtro_md_fecha_i(){ $('#filtro_md_fecha_i').val("").trigger("change") } 
function reload_filtro_md_fecha_f(){ $('#filtro_md_fecha_f').val("").trigger("change") } 
function reload_filtro_md_cliente(){ lista_select2("../ajax/cotizacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente'); } 
function reload_filtro_md_comprobante(){ lista_select2("../ajax/cotizacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante'); }


function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
}

function ver_img_pefil(id_cliente) {
  $('#modal-ver-imgenes').modal('show');
  $(".html_modal_ver_imgenes").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/cotizacion.php?op=mostrar_cliente", { idcliente: id_cliente },  function (e, status) {
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

function ver_comprobante_pago(id_venta) {
  $('#modal-ver-imgenes').modal('show');
  $(".html_modal_ver_imgenes").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/cotizacion.php?op=mostrar_venta", { idventa: id_venta },  function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      if (e.data.mp_comprobante == "" || e.data.mp_comprobante == null) { } else {
        var nombre_comprobante = `${e.data.metodo_pago} - ${e.data.mp_serie_comprobante}`;
        var file_comprobante = e.data.mp_comprobante ==''||  e.data.mp_comprobante == null ? '' : e.data.mp_comprobante;
        $('.title-ver-imgenes').html(nombre_comprobante);
        $(".html_modal_ver_imgenes").html(doc_view_download_expand(file_comprobante, 'assets/modulo/facturacion/ticket',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
  
}


