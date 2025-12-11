var tabla_reporte_detalle;
var chart_line_comprobante;


//Función que se ejecuta al inicio
function init() {

  $("#bloc_Recurso").addClass("menu-open");

  $("#mRecurso").addClass("active");  

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 
  lista_select2("../ajax/reporte_venta_x_trabajador.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');
  // lista_select2("../ajax/reporte_venta_x_trabajador.php?op=select2_filtro_anio_pago", '#filtro_p_all_anio_pago', null, '.charge_filtro_p_all_anio_pago');
  // lista_select2("../ajax/reporte_venta_x_trabajador.php?op=select2_filtro_mes_pago", '#filtro_p_all_mes_pago', null, '.charge_filtro_p_all_mes_pago');
  // lista_select2("../ajax/reporte_venta_x_trabajador.php?op=select2_filtro_tipo_comprob", '#filtro_tipo_comprob', null, '.charge_filtro_tipo_comprob');

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#filtro_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_anio_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_mes_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_es_cobro").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  
  $("#filtro_tipo_comprob").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//Función Listar
function tabla_principal_cliente(filtro_estado_cobrado, filtro_fecha_i, filtro_fecha_f, filtro_trabajador) {

  tabla_reporte_detalle = $('#tabla-reporte-detalle-ov').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-7 col-xl-7 col-xxl-7 pt-2'f><'col-md-5 col-lg-5 col-xl-5 col-xxl-5 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_reporte_detalle) { tabla_reporte_detalle.ajax.reload(null, false); } } },
      { extend: 'excel', exportOptions: { columns: [0,6,7,8,2,3,4,5], }, title: 'Lista de Cobros', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_venta_x_trabajador.php?op=tabla_reporte_detalle&filtro_estado_cobrado=${filtro_estado_cobrado}&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_trabajador=${filtro_trabajador}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_buscando_tabla').hide();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[0] != '') { $("td", row).eq(0).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "_MENU_ ", search: "",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      paginate: {  first: "<<",  last: ">>", next: ">",  previous: "<" },
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 3).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 3 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` ); 
      
      var api2 = this.api(); var total2 = api2.column( 4).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api2.column( 4 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total2)}</span> ` ); 
    },
    initComplete: function () {

      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
      //{ targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [7,8,9], visible: false, searchable: false, },
      { targets: [3,4], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

function resumen_reporte(filtro_estado_cobrado, filtro_fecha_i, filtro_fecha_f, filtro_trabajador, filtro_tipo_chart) {
  $.getJSON(`../ajax/reporte_venta_x_trabajador.php?op=resumen_reporte`, { filtro_estado_cobrado, filtro_fecha_i, filtro_fecha_f, filtro_trabajador, filtro_tipo_chart }, function (e, textStatus, jqXHR) {
    if (e.status == true) {

      $('.total_cantidad_doc').html( e.data.cant_comprobante );
      $('.total_venta').html( `S/ ${formato_miles(e.data.sum_subtotal_venta)}` );
      $('.total_compra').html( `S/ ${formato_miles(e.data.sum_subtotal_compra)}` );
      $('.total_utilidad').html( `S/ ${formato_miles(e.data.sum_utilidad)}` );

      const topMonto = e.data.top_10_monto;
      const topCant = e.data.top_10_cant;

      const $tablaCant = $('#tabla-top-10-cantidad tbody');
      const $tablaMonto = $('#tabla-to-10-precio tbody');

      $tablaCant.empty();
      $tablaMonto.empty();

      // Mensaje vacío con colspan de 4 columnas
      const mostrarMensajeVacio = ($tbody) => {
        $tbody.append(`<tr><td colspan="4" class="text-center text-muted">📭 No hay resultados para mostrar.</td></tr>`);
      };

      // Llenar tabla de top por CANTIDAD
      if (topCant.length > 0) {
        topCant.forEach((item, index) => {
          $tablaCant.append( `<tr>
            <td class="py-1" >${index + 1}</td>
            <td class="py-1 " >${item.nombre_producto}</td>
            <td class="py-1 text-right" >${parseFloat(item.total_cantidad_vendida)}</td>
            <td class="py-1"><span class="float-start">S/</span> <span class="float-end">${formato_miles(item.total_vendido_soles)}</span> </td>
          </tr>`);
          
        });
      } else {
        mostrarMensajeVacio($tablaCant);
      }
      
      if (topMonto.length > 0) {
        // Llenar tabla de top por MONTO
        topMonto.forEach((item, index) => {
          $tablaMonto.append(`<tr>
            <td class="py-1">${index + 1}</td>
            <td class="py-1">${item.nombre_producto}</td>
            <td  class="py-1 text-right">${parseFloat(item.total_cantidad_vendida)}</td>
            <td class="py-1"><span class="float-start">S/</span> <span class="float-end">${formato_miles(item.total_vendido_soles)}</span> </td>
          </tr>`);
         
        });
      } else {
        mostrarMensajeVacio($tablaMonto);
      }

      // :::::::::::::::::::::::::::::: CHART ::::::::::::::::::::::::::::::

      if (chart_line_comprobante) { chart_line_comprobante.destroy(); } 
      var options = {
        series: [
          {
            type: 'bar',
            name: 'Venta',
            data: e.data.chart_venta
          },
          {
            type: 'bar',
            name: 'Compra',            
            data: e.data.chart_compra
          },
          {
            type: 'bar',
            name: 'Utilidad',
            // chart: { dropShadow: { enabled: true, enabledOnSeries: undefined, top: 5, left: 0, blur: 3, color: '#000', opacity: 0.1 }},
            data: e.data.chart_utilidad
          },         
        ],
        chart: {
          height: 350,
          animations: { speed: 500 },
          dropShadow: { enabled: true, enabledOnSeries: undefined, top: 8, left: 0, blur: 3, color: '#000', opacity: 0.1 },
        },
        colors: ["rgb(73, 182, 245)", "rgb(38, 191, 148)", "rgb(245, 184, 73)"],
        dataLabels: { enabled: false },
        grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
        stroke: { curve: 'smooth', width: [2, 2, 2, 1], dashArray: [0, 0, 5, 0],},
        xaxis: { axisTicks: { show: false, },},
        yaxis: { labels: { formatter: function (value) { return "S/ " + formato_miles(value) ; } }, },
        tooltip: {
          y: [{ formatter: function(e) { return void 0 !== e ? "S/ " + formato_miles(e.toFixed(0)) : e } }, 
            { formatter: function(e) { return void 0 !== e ? "S/ " + formato_miles(e.toFixed(0)) : e } }, 
            { formatter: function(e) { return void 0 !== e ? "S/ " + formato_miles(e.toFixed(0)) : e } }, 
            { formatter: function(e) { return void 0 !== e ? "S/ " +formato_miles(e.toFixed(0)) : e } }
          ]
        },
        legend: { show: true, /*customLegendItems: ['Profit', 'Revenue', 'Sales'],inverseOrder: true*/},
        title: { text: 'Cruce de comprobantes', align: 'left', style: { fontSize: '.8125rem', fontWeight: 'semibold', color: '#8c9097' }, },
        markers: { hover: { sizeOffset: 5 } }
      };
      document.getElementById('chart-ventas').innerHTML = '';
      chart_line_comprobante = new ApexCharts(document.querySelector("#chart-ventas"), options);
      chart_line_comprobante.render();
      

    } else {
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}
  


$(document).ready(function () {
  init();
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cargando_search() {
  $('#id_buscando_tabla').show();
  $('#id_buscando_tabla th').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando...`);  
}

function filtros() {  

  var filtro_estado_cobrado = $("#filtro_estado_cobrado").val() == '' || $("#filtro_estado_cobrado").val() == null ? '' : $("#filtro_estado_cobrado").val();
  var filtro_fecha_i        = $("#filtro_fecha_i").val()    == '' || $("#filtro_fecha_i").val() == null ? '' : $("#filtro_fecha_i").val();
  var filtro_fecha_f        = $("#filtro_fecha_f").val()    == '' || $("#filtro_fecha_f").val() == null ? '' : $("#filtro_fecha_f").val();
  var filtro_trabajador     = $("#filtro_trabajador").val() == '' || $("#filtro_trabajador").val() == null ? '' : $("#filtro_trabajador").val();  
  var filtro_tipo_chart     = $("#filtro_tipo_chart").val() == '' || $("#filtro_tipo_chart").val() == null ? '' : $("#filtro_tipo_chart").val();  
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_trabajador  = ' ─ ' + $('#filtro_trabajador').find(':selected').text();
  
  if (filtro_fecha_i    == '' || filtro_fecha_i     == 0 || filtro_fecha_i    == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }       // filtro de trabajador  
  if (filtro_fecha_f    == '' || filtro_fecha_f     == 0 || filtro_fecha_f    == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }                 // filtro de dia pago  
  if (filtro_trabajador == '' || filtro_trabajador  == 0 || filtro_trabajador == null) { filtro_trabajador = ""; nombre_filtro_trabajador = ""; }                                     // filtro de plan

  $('#id_buscando_tabla th').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ${nombre_filtro_trabajador}...`);

  tabla_principal_cliente(filtro_estado_cobrado, filtro_fecha_i, filtro_fecha_f, filtro_trabajador);
  resumen_reporte(filtro_estado_cobrado, filtro_fecha_i, filtro_fecha_f, filtro_trabajador, filtro_tipo_chart);
  // calculando_totales_pay(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob, filtro_p_all_es_cobro);
  // calculando_totales_producto(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob, filtro_p_all_es_cobro);  

}

function reload_select(r_text) {

  switch (r_text) {
    case 'filtro_fecha_i':
      $("#filtro_fecha_i").val('').trigger('change');
    break; 
    case 'filtro_fecha_f':
      $("#filtro_fecha_f").val('').trigger('change');
    break;    
    case 'filtro_trabajador':
      lista_select2("../ajax/reporte_venta_x_trabajador.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');
    break;    

    default:
      console.log('Caso no encontrado.');
  }
 
}
