var tbl_principal_periodo;
var tabla_comprobante;

var array_data_venta = []; 
var cambio_de_tipo_comprobante ;

var filtro_estado_sunat = "" ;

var inp_fecha_inicio, inp_fecha_fin;

var chart_6_month ;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_tipo_documento = new Choices('#f_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

async function init(){

  // filtros(); // Listamos la tabla principal
  $(".btn-tiket").click();   // Selecionamos la BOLETA
  
  bloquear_dias_usados()

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $("#guardar_registro_periodo").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-periodo").submit(); } });
  $("#guardar_registro_cambio_periodo").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cambio-periodo").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante');
  lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente');
  lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_anio", '#filtro_anio', moment().format('YYYY'), '.charge_filtro_anio');

  lista_select2("../ajax/periodo_facturacion.php?op=select2_periodo", '#t_idperiodo', null, '.charge_filtro_anio');
  lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_t_comprobante', null, '.charge_filtro_t_comprobante');
  lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_cliente", '#filtro_t_cliente', null, '.charge_filtro_t_cliente');

  // lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante');
  // lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente');

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
 
  $("#filtro_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_anio").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#t_idperiodo").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_t_cliente").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_t_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#f_nc_tipo_comprobante").val(null).trigger('change'); // Limpiamos aqui para no generar un bucle infinito

  inp_fecha_inicio = flatpickr("#fecha_inicio", {
    dateFormat: "Y-m-d", // Formato para la base de datos
    altInput: true,
    altFormat: "d/m/Y", // Formato para mostrar al usuario
    "locale": "es"
  });

  inp_fecha_fin = flatpickr("#fecha_fin", {
    dateFormat: "Y-m-d", // Formato para la base de datos
    altInput: true,
    altFormat: "d/m/Y", // Formato para mostrar al usuario
    "locale": "es"
  });

  // flatpickr("#fecha_fin", {
  //   "locale": "es",
  //   altInput: true,
  //   altFormat: "F Y",
  //   dateFormat: "Y-m",
  //   plugins: [new monthSelectPlugin({
  //     shorthand: true, //defaults to false
  //     dateFormat: "Y-m", //defaults to "F Y"
  //     altFormat: "F Y", //defaults to "F Y"
  //     theme: "light" // defaults to "light"
  //   })]
  // });

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
		$(".guardar_registro_periodo").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) { // DETALLE COMPROBANTE
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

function mini_reporte(filtro_anio, filtro_periodo, filtro_cliente, filtro_comprobante) {

  $(".vw_total_factura").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $(".vw_total_boleta").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $(".vw_total_ticket").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  if (chart_6_month) { chart_6_month.destroy(); } 

  $.getJSON(`../ajax/periodo_facturacion.php?op=mini_reporte&filtro_anio=${filtro_anio}&filtro_periodo=${filtro_periodo}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}`,  function (e, textStatus, jqXHR) {

    if (e.status == true) {      

      e.data.coun_comprobante.forEach((val, key) => {
        if (val.tipo_comprobante == '01') { $(".vw_count_factura").html( val.cantidad ); }
        if (val.tipo_comprobante == '03') { $(".vw_count_boleta").html( val.cantidad ); }
        if (val.tipo_comprobante == '12') { $(".vw_count_ticket").html( val.cantidad ); }
      });
    
      $(".vw_total_factura").html( `${formato_miles(e.data.factura)}` ).addClass('count-up');
      $(".vw_total_factura_p").html( `${e.data.factura_p >= 0? '<i class="ri-arrow-up-s-line me-1 align-middle"></i>' : '<i class="ri-arrow-down-s-line me-1 align-middle"></i>'} ${(e.data.factura_p)}%` );
      e.data.factura_p >= 0? $(".vw_total_factura_p").addClass('text-success').removeClass('text-danger') : $(".vw_total_factura_p").addClass('text-danger').removeClass('text-success') ;

      $(".vw_total_boleta").html( `${formato_miles(e.data.boleta)}` ).addClass('count-up');
      $(".vw_total_boleta_p").html( `${e.data.boleta_p >= 0? '<i class="ri-arrow-up-s-line me-1 align-middle"></i>' : '<i class="ri-arrow-down-s-line me-1 align-middle"></i>'} ${(e.data.boleta_p)}%` );
      e.data.boleta_p >= 0? $(".vw_total_boleta_p").addClass('text-success').removeClass('text-danger') : $(".vw_total_boleta_p").addClass('text-danger').removeClass('text-success') ;

      $(".vw_total_ticket").html( `${formato_miles(e.data.ticket)}` ).addClass('count-up');
      $(".vw_total_ticket_p").html( `${e.data.ticket_p >= 0? '<i class="ri-arrow-up-s-line me-1 align-middle"></i>' : '<i class="ri-arrow-down-s-line me-1 align-middle"></i>'} ${(e.data.ticket_p)}%` );
      e.data.ticket_p >= 0? $(".vw_total_ticket_p").addClass('text-success').removeClass('text-danger') : $(".vw_total_ticket_p").addClass('text-danger').removeClass('text-success') ;

      // MINI CHART
      var options = {
        series: [
          { name: 'Factura', data: e.data.factura_line }, 
          { name: 'Boleta', data: e.data.boleta_line }, 
          { name: 'Ticket', data: e.data.ticket_line }
        ],
        chart: { type: 'bar', height: 210, stacked: true },
        plotOptions: { bar: { horizontal: false, columnWidth: '25%', endingShape: 'rounded', }, },
        grid: { borderColor: '#f2f5f7', },
        dataLabels: { enabled: false },
        colors: ["#4b9bfa", "#28d193", "#ffbe14", "#f3f6f8"],
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
      chart_6_month = new ApexCharts(document.querySelector("#invoice-list-stats"), options);
      chart_6_month.render();
    } else {
      ver_errores(e);
    }

  }).fail( function(e) { ver_errores(e); } );
}

function bloquear_dias_usados() {
  $.getJSON(`../ajax/periodo_facturacion.php?op=bloquear_fechas_usadas`, { idperiodo: '' }, function (e, textStatus, jqXHR) {
    console.log(e);
    
    inp_fecha_inicio = flatpickr("#fecha_inicio", {
      dateFormat: "Y-m-d", // Formato para la base de datos
      altInput: true,
      altFormat: "d/m/Y", // Formato para mostrar al usuario
      "locale": "es",
      disable: e.data,
      onDayCreate: function(dObj, dStr, fp, dayElem) {
        const date = moment(dayElem.dateObj);
        const disabledRanges = e.data;
        for (const range of disabledRanges) {
          const fromDate =moment(range.from);
          const toDate = moment(range.to);
          // console.log(` | ${ moment(date).format('DD/MM/YY') } | `);
          if (date.isBetween(fromDate, toDate, null, '[]')) { //console.log(`${ moment(fromDate).format('DD/MM/YY') } | ${moment(date).format('DD/MM/YY')} | ${moment(toDate).format('DD/MM/YY')}`);
            dayElem.classList.add('custom-disabled');
            dayElem.addEventListener('click', function(e) {
              e.preventDefault();
              toastr_info('Ocupado!!', `Esta fecha esta utilizada por el periodo: <b>${range.periodo}</b>.`);
            });
          }
        }
      },      
    });

    inp_fecha_fin = flatpickr("#fecha_fin", {
      dateFormat: "Y-m-d", // Formato para la base de datos
      altInput: true,
      altFormat: "d/m/Y", // Formato para mostrar al usuario
      "locale": "es",
      disable: e.data,
      onDayCreate: function(dObj, dStr, fp, dayElem) {
        const date = dayElem.dateObj;
        const disabledRanges = e.data;
        for (const range of disabledRanges) {
          const fromDate = new Date(range.from);
          const toDate = new Date(range.to);
          if (date >= fromDate && date <= toDate) {
            dayElem.classList.add('custom-disabled');
            dayElem.addEventListener('click', function(e) {
              e.preventDefault();
              toastr_info('Ocupado!!', `Esta fecha esta utilizada por el periodo: <b>${range.periodo}</b>.`);
            });
          }
        }
      }
    });

  });
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   P E R I O D O :::::::::::::::::::::::::::::::::::::::::::::

function limpiar_form_periodo(){
  
  $("#idperiodo_contable").val(''); 
  $("#periodo").val('');
  $("#periodo_anio").val('0000');    
  inp_fecha_inicio.clear();
  inp_fecha_fin.clear();
 
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function listar_tabla_principal(filtro_anio, filtro_periodo, filtro_cliente, filtro_comprobante){
  
  tbl_principal_periodo = $("#tabla-ventas").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 12, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbl_principal_periodo) { tbl_principal_periodo.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      // { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/periodo_facturacion.php?op=listar_tabla_principal&filtro_anio=${filtro_anio}&filtro_periodo=${filtro_periodo}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}`,
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
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap text-center"); }
      // columna: Monto
      if (data[8] != '') { $("td", row).eq(8).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 6 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 6 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );     
      
      var api1 = this.api(); var total1 = api1.column( 7 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 7 ).footer() ).html( `<span class="text-center">${formato_miles(total1)}</span> ` );    
    },
    "bDestroy": true,
    "iDisplayLength": 12,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [4, 5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 14, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function guardar_editar_periodo(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-periodo")[0]);

  $.ajax({
    url: "../ajax/periodo_facturacion.php?op=guardar_y_editar_periodo",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "Periodo registrado correctamente.", "success");
          tbl_principal_periodo.ajax.reload(null, false);
          limpiar_form_periodo();
          $("#modal-agregar-periodo").modal("hide");
          bloquear_dias_usados();
        } else {
          ver_errores(e);
        }
      } catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!",'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }      
      $(".guardar_registro_periodo").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar').removeClass('disabled send-data');
     
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total) * 100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_periodo").css({ "width": percentComplete + '%' }).text(percentComplete.toFixed(2) + " %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $(".guardar_registro_periodo").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> <i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_periodo").css({ width: "0%", }).text("0%");
      $("#barra_progress_periodo_div").show();
    },
    complete: function () {
      $("#barra_progress_periodo").css({ width: "0%", }).text("0%");
      $("#barra_progress_periodo_div").hide();
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_detalle_periodo(idperiodo){
  $("#cargando-3-formulario").hide(); 
  $("#cargando-4-fomulario").show();

  $("#modal-agregar-periodo").modal("show"); 

  $.getJSON(`../ajax/periodo_facturacion.php?op=bloquear_fechas_usadas`, { idperiodo: idperiodo }, function (e, textStatus, jqXHR) {
    
    
    inp_fecha_inicio = flatpickr("#fecha_inicio", {
      dateFormat: "Y-m-d", // Formato para la base de datos
      altInput: true,
      altFormat: "d/m/Y", // Formato para mostrar al usuario
      "locale": "es",
      disable: e.data,
      onDayCreate: function(dObj, dStr, fp, dayElem) {
        const date = moment(dayElem.dateObj);
        const disabledRanges = e.data;
        for (const range of disabledRanges) {
          const fromDate =moment(range.from);
          const toDate = moment(range.to);
          console.log(` | ${ moment(date).format('DD/MM/YY') } | `);
          if (date.isBetween(fromDate, toDate, null, '[]')) { console.log(`${ moment(fromDate).format('DD/MM/YY') } | ${moment(date).format('DD/MM/YY')} | ${moment(toDate).format('DD/MM/YY')}`);
            dayElem.classList.add('custom-disabled');
            dayElem.addEventListener('click', function(e) {
              e.preventDefault();
              toastr_info('Ocupado!!', `Esta fecha esta utilizada por el periodo: <b>${range.periodo}</b>.`);
            });
          }
        }
      },      
    });

    inp_fecha_fin = flatpickr("#fecha_fin", {
      dateFormat: "Y-m-d", // Formato para la base de datos
      altInput: true,
      altFormat: "d/m/Y", // Formato para mostrar al usuario
      "locale": "es",
      disable: e.data,
      onDayCreate: function(dObj, dStr, fp, dayElem) {
        const date = dayElem.dateObj;
        const disabledRanges = e.data;
        for (const range of disabledRanges) {
          const fromDate = new Date(range.from);
          const toDate = new Date(range.to);
          if (date >= fromDate && date <= toDate) {
            dayElem.classList.add('custom-disabled');
            dayElem.addEventListener('click', function(e) {
              e.preventDefault();
              toastr_info('Ocupado!!', `Esta fecha esta utilizada por el periodo: <b>${range.periodo}</b>.`);
            });
          }
        }
      }
    });

    $.getJSON("../ajax/periodo_facturacion.php?op=mostrar_editar_periodo", { idperiodo: idperiodo }, function (e, status) {    

      if (e.status == true) {
        inp_fecha_inicio.setDate(e.data.fecha_inicio);
        inp_fecha_fin.setDate(e.data.fecha_fin);
        $("#idperiodo_contable").val(e.data.idperiodo_contable); 
        $("#periodo").val(e.data.periodo);
        $("#periodo_anio").val(e.data.periodo_year);

        $("#cargando-3-formulario").show(); 
        $("#cargando-4-fomulario").hide();
      } else {
        ver_errores(e);
      }
      
    }).fail( function(e) { ver_errores(e); } );

  });
}

function eliminar_papelera_periodo(idventa, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/periodo_facturacion.php?op=papelera",
    "../ajax/periodo_facturacion.php?op=eliminar", 
    idventa, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>venta: ${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tbl_principal_periodo.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   R E A S I G N A R   P E R I O D O :::::::::::::::::::::::::::::::::::::::::::::
var array_cambio_periodo_venta = []; var activar_filtro_reasignar; var filtro_emision_mes = '';

function limpiar_form_reasignar() {
  $("#t_idperiodo").val('').trigger('change'); 
  array_cambio_periodo_venta = [];  
 
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function reasignar_comprobante(idperiodo, periodo_actual) {
  limpiar_form_reasignar();
  show_hide_form(2);
  $('#idperiodo_ver').val(idperiodo);
  $('#periodo-actual').html(periodo_actual);
  activar_filtro_reasignar = true;

  tabla_detalle_comprobante(idperiodo, filtro_emision_mes, '', '');
}

function tabla_detalle_comprobante(idperiodo, mes_emision, idcliente, tipo_comprobante) {
  tabla_comprobante = $("#tabla-comprobantes").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_comprobante) { tabla_comprobante.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      // { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/periodo_facturacion.php?op=listar_tabla_comprobante&filtro_idperiodo=${idperiodo}&filtro_mes_emision=${mes_emision}&filtro_cliente=${idcliente}&filtro_comprobante=${tipo_comprobante}`,
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
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      // columna: Monto
      if (data[6] != '') { $("td", row).eq(6).addClass("text-nowrap"); }
      // columna: Monto
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap text-center"); }
      // columna: Boucher
      if (data[8] != '') { $("td", row).eq(8).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 6 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 6 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function reasignar_periodo(idventa) {

  if ($(`#switch-primary-${idventa}`).is(':checked') == true) {
       
    array_cambio_periodo_venta.push({ 'idventa': idventa, });
    console.log(array_cambio_periodo_venta);  
    
  } else {   
      
    // eliminamos el indice elegido
    array_cambio_periodo_venta.forEach(function (car, index, object) {
      if (car.idventa === idventa) { object.splice(index, 1); }
    }); 
    console.log(array_cambio_periodo_venta);    
  }  
}

function guardar_editar_reasignar_periodo(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-periodo")[0]);

  if (array_cambio_periodo_venta.length === 0 ) {
    toastr_error('Vacio!!', 'Seleccione un documentos para reasignarlo.'); return ;
  }

  var periodo = $('#t_idperiodo').val(); console.log(array_cambio_periodo_venta);

  Swal.fire({
    title: "¿Está seguro que deseas reasignar?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/periodo_facturacion.php?op=guardar_y_editar_reasignar", {
        method: 'POST', // or 'PUT'
        body: JSON.stringify({venta: array_cambio_periodo_venta, idperiodo: periodo}) , // data can be `string` or {object}!        
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
        Swal.fire("Correcto!", "Periodo reasignado correctamente.", "success");
        tbl_principal_periodo.ajax.reload(null, false);
        tabla_comprobante.ajax.reload(null, false);
        limpiar_form_reasignar();    
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

$(document).ready(function () {
  init(); 
  filtros();
});

// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

  $("#form-agregar-periodo").validate({
    ignore: '',
    rules: {
      periodo:      { required: true },
      fecha_inicio: { required: true },
      fecha_fin:    { required: true, },
    },
    messages: {
      periodo:      { required: "Campo requerido", },
      fecha_inicio: { required: "Campo requerido", },      
      fecha_fin:    { required: "Campo requerido", },
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
      guardar_editar_periodo(form);
    },
  });   

  $("#form-cambio-periodo").validate({
    ignore: '',
    rules: {
      t_idperiodo:      { required: true },
    },
    messages: {
      t_idperiodo:      { required: "Campo requerido", },
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
      guardar_editar_reasignar_periodo(form);
    },
  });   

});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_anio         = $("#filtro_anio").val() == '' || $("#filtro_anio").val() == null ? '' : $("#filtro_anio").val();
  var filtro_periodo      = $("#filtro_periodo").val();
  var filtro_cliente      = $("#filtro_cliente").select2('val');
  var filtro_comprobante  = $("#filtro_comprobante").select2('val');
  
  var nombre_filtro_periodo     = $('#filtro_periodo').val();
  var nombre_filtro_cliente     = ' ─ ' + $('#filtro_cliente').find(':selected').text();
  var nombre_filtro_comprobante = ' ─ ' + $('#filtro_comprobante').find(':selected').text();

  // filtro de fechas
  if (nombre_filtro_periodo == '' || nombre_filtro_periodo == 0 || nombre_filtro_periodo == null) { nombre_filtro_periodo = ""; nombre_filtro_periodo = ""; }

  // filtro de cliente
  if (filtro_cliente == '' || filtro_cliente == 0 || filtro_cliente == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }

  // filtro de comprobante
  if (filtro_comprobante == '' || filtro_comprobante == 0 || filtro_comprobante == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_periodo} ${nombre_filtro_cliente}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla_principal(filtro_anio, filtro_periodo, filtro_cliente, filtro_comprobante);
  mini_reporte(filtro_anio, filtro_periodo, filtro_cliente, filtro_comprobante);
}

function filtrar_solo_estado_sunat(estado, etiqueta) {  
  $(".otros-filtros").find("li>a").removeClass("active");
  $(".otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat = estado; filtros();
}

function filtros_2() {  

  var filtro_periodo      = $("#filtro_t_mes_emision").val() == '' || $("#filtro_t_mes_emision").val() == null ? '' : $("#filtro_t_mes_emision").val() ;
  var filtro_cliente      = $("#filtro_t_cliente").select2('val') == '' || $("#filtro_t_cliente").select2('val') == null ? '' : $("#filtro_t_cliente").select2('val') ;
  var filtro_comprobante  = $("#filtro_t_comprobante").select2('val') == '' || $("#filtro_t_comprobante").select2('val') == null ? '' : $("#filtro_t_comprobante").select2('val');  
  filtro_emision_mes = filtro_periodo;
  // $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_periodo} ${nombre_filtro_cliente}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);
  if (activar_filtro_reasignar == true) {
    tabla_detalle_comprobante($('#idperiodo_ver').val(), filtro_emision_mes, filtro_cliente, filtro_comprobante);

  }
  
}

function reload_filtro_anio(){ lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_anio", '#filtro_anio', null, '.charge_filtro_anio'); } 
function reload_filtro_periodo(){ $('#filtro_periodo').val("").trigger("change") } 
function reload_filtro_cliente(){ lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente'); } 
function reload_filtro_comprobante(){ lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante'); }

function reload_filtro_t_mes_emision(){ $('#filtro_t_mes_emision').val("").trigger("change") } 
function reload_filtro_t_cliente(){ lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_cliente", '#filtro_t_cliente', null, '.charge_filtro_t_cliente'); } 
function reload_filtro_t_comprobante(){ lista_select2("../ajax/periodo_facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_t_comprobante', null, '.charge_filtro_t_comprobante'); }

function reload_periodo(){ lista_select2("../ajax/periodo_facturacion.php?op=select2_periodo", '#t_idperiodo', null, '.charge_t_idperiodo'); }


// Estramos el año del input: periodo
document.getElementById('periodo').addEventListener('change', function() {
  const monthValue = `${this.value}-01`; console.log(monthValue);
  if (monthValue == '' || monthValue == null ) {
    document.getElementById('periodo_anio').value = '0000';
    document.getElementById('periodo_anio_small').textContent  = '(Selecione el periodo)';
  } else {    
    document.getElementById('periodo_anio').value = moment(monthValue).format('YYYY');;
    document.getElementById('periodo_anio_small').textContent  = '';
  }  
});


