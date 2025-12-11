var tbl_principal_periodo;

var chart_6_month ;
var chart_total_porcentaje ;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_tipo_documento = new Choices('#f_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

async function init(){

  // filtros(); // Listamos la tabla principal
  $(".btn-tiket").click();   // Selecionamos la BOLETA
  

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $("#guardar_registro_periodo").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-periodo").submit(); } });
  $("#guardar_registro_cambio_periodo").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cambio-periodo").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/cliente.php?op=select2_filtro_trabajador", '#filtro_trabajador', localStorage.getItem('nube_id_persona_trabajador'), '.charge_filtro_trabajador');

  // lista_select2("../ajax/avance_cobro.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante');
  // lista_select2("../ajax/avance_cobro.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente');

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
 
  $("#filtro_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione" });

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

  $.getJSON(`../ajax/avance_cobro.php?op=mini_reporte&filtro_anio=${filtro_anio}&filtro_periodo=${filtro_periodo}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}`,  function (e, textStatus, jqXHR) {

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



// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   P E R I O D O :::::::::::::::::::::::::::::::::::::::::::::

function listar_tabla_principal(filtro_periodo, filtro_trabajador){
  
  tbl_principal_periodo = $("#tabla-ventas").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbl_principal_periodo) { tbl_principal_periodo.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      // { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/avance_cobro.php?op=listar_tabla_principal&filtro_periodo=${filtro_periodo}&filtro_trabajador=${filtro_trabajador}`,
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
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Opciones
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 5 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      // $( api1.column( 3 ).footer() ).html( `<center >${(total1)}</center> ` );     
      
      var api2 = this.api(); var total2 = api2.column( 6 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api2.column( 3 ).footer() ).html( `<center >${total1}/${(total2)}</center> ` );    
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      // { targets: [4, 5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

       { targets: [5,6], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function mostrar_reporte(filtro_periodo, filtro_trabajador){
  
  if (chart_total_porcentaje) { chart_total_porcentaje.destroy(); } 
  $('#avance-plan tbody').html('<div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div>');

  $.getJSON(`../ajax/avance_cobro.php?op=mostrar_reporte`, { filtro_periodo:filtro_periodo, filtro_trabajador:filtro_trabajador }, function (e, textStatus, jqXHR) {
    
    $('.total_avance_cobrado').html(`${e.data.centro_poblado.cant_cobrado}`);
    $('.total_avance_cobrado_porcent').html(`${e.data.centro_poblado.avance}%`);
    $('.total_avance_por_cobrar').html(`${  (e.data.centro_poblado.cant_total - e.data.centro_poblado.cant_cobrado) }`);
    $('.total_avance_por_cobrar_porcent').html(`${ formato_miles( 100 - e.data.centro_poblado.avance) }%`);

    var options = {
      chart: { height: 229, type: "radialBar", },    
      series: [e.data.centro_poblado.avance],
      colors: ["rgb(132, 90, 223)"],
      plotOptions: {
        radialBar: {
          hollow: { margin: 0, size: "70%", background: "#fff", },
          track: {
            dropShadow: { enabled: true, top: 2, left: 0, blur: 2, opacity: 0.15, },
          },
          dataLabels: {
            name: { offsetY: -10,  color: "#4b9bfa", fontSize: "16px", show: false, },
            value: { color: "#4b9bfa", fontSize: "30px", show: true, },
          },
        },
      },
      stroke: { lineCap: "round", },
      labels: ["Cart"],
    };
    document.querySelector("#sale-value").innerHTML = "";
    chart_total_porcentaje = new ApexCharts(document.querySelector("#sale-value"), options);
    chart_total_porcentaje.render();

    $('#avance-plan tbody').html('');
    e.data.plan.forEach((val, key) => {
      $('#avance-plan tbody').append(`
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <span class="avatar avatar-rounded avatar-sm p-2 bg-light me-2">
                <i class="ri-radar-line fs-15 text-primary"></i>
              </span>
              <div class="fw-semibold fs-11">${val.plan} (${val.cant_cobrado}/${val.cant_total})</div>
            </div>
          </td>                          
          <td>
            <div class="progress progress-xs">
              <div class="progress-bar ${(val.avance == 100 ? 'bg-success' : 'bg-primary')}" role="progressbar" style="width: ${val.avance}%" aria-valuenow="${val.avance}" aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </td>
          <td>
            <span class="fs-10"><i class="ri-arrow-up-s-fill me-1 text-success align-middle "></i>${val.avance}%</span>
          </td>
        </tr>
      `);
    });

  }).fail( function(e) { ver_errores(e); } );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   R E A S I G N A R   P E R I O D O :::::::::::::::::::::::::::::::::::::::::::::


$(document).ready(function () {
  init(); 
  // filtros();
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

});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_periodo    = $("#filtro_periodo").val() == '' || $("#filtro_periodo").val() == null ? '' : $("#filtro_periodo").val();
  var filtro_trabajador = $("#filtro_trabajador").val() == '' || $("#filtro_trabajador").val() == null ? '' : $("#filtro_trabajador").val() ;
  
  // $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_periodo} ${nombre_filtro_cliente}...`);

  listar_tabla_principal( filtro_periodo, filtro_trabajador);
  mostrar_reporte( filtro_periodo, filtro_trabajador )
}

function reload_filtro_periodo(){ $('#filtro_periodo').val("").trigger("change") } 
function reload_filtro_trabajador(){ lista_select2("../ajax/cliente.php?op=select2_filtro_trabajador", '#filtro_trabajador', localStorage.getItem('nube_id_persona_trabajador'), '.charge_filtro_trabajador'); } 



function saleValue() {
  if (chart_total_porcentaje) {
    chart_total_porcentaje.updateOptions({ colors: ["rgb(" + myVarVal + ")"],  });
  }  
}

