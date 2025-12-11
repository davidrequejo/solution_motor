var chart_objetivo;
var chart_dia_semana;
var card_chart_factura;
var card_chart_boleta;
var card_chart_ticket;
var card_chart_total;
var chart_line_comprobante;
var chart_pastel_tecnico; var chart_pastel_tecnico_data = [];

function init_b() {  

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/escritorio.php?op=select2_filtro_trabajador", '#filtro_trabajador', localStorage.getItem('nube_id_persona_trabajador'), '.charge_filtro_trabajador');


  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
 


  // Formato para telefono
  $("[data-mask]").inputmask();
}


//Función Listar
function tabla_principal_bancos() {

  tabla_bancos = $('#tabla-bancos').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_bancos) { tabla_bancos.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0, 5, 6, 7, 8, 9], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0, 5, 6, 7, 8, 9], }, title: 'Lista de bancos', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true, },
      { extend: 'pdf', exportOptions: { columns: [0, 5, 6, 7, 8, 9], }, title: 'Lista de bancos', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: '../ajax/escritorio.php?op=tabla_principal_bancos',
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
        if (e.status != true) { ver_errores(e); } return e.aaData;
      },
    },
    createdRow: function (row, data, ixdex) {
      // columna: #
      // if (data[6] != '') { $("td", row).eq(6).addClass("text-center"); }
    },
    language: {
      lengthMenu: "_MENU_ ", search: "Buscar:",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      loadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...', paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" },
      emptyTable: "Ningún dato disponible en esta tabla", zeroRecords: "No se encontraron resultados",
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]],//Ordenar (columna,orden)
    columnDefs: [
      { targets: [5], visible: false, searchable: false, },
      { targets: [6], visible: false, searchable: false, },
      { targets: [7], visible: false, searchable: false, },
      { targets: [8], visible: false, searchable: false, },
      { targets: [9], visible: false, searchable: false, },
    ]
  }).DataTable();
}


function reporte( filtro_mes, filtro_trabajador) {

  $(".tooltip").remove();

  $("#modal-agregar-bancos").modal("show");

  $('.card-cantidad-factura').html( 0); 
  $('.card-cantidad-boleta').html( 0 ); 
  $('.card-cantidad-ticket').html( 0 );

  var cant_mes = filtro_mes == '' ? '' : cant_dias_mes(filtro_mes.slice(0, 4), moment(`${filtro_mes}-01`).format('MM'), true );

  if (chart_objetivo) { chart_objetivo.destroy(); } 
  if (chart_dia_semana) { chart_dia_semana.destroy(); } 
  if (card_chart_factura) { card_chart_factura.destroy(); } 
  if (card_chart_boleta) { card_chart_boleta.destroy(); } 
  if (card_chart_ticket) { card_chart_ticket.destroy(); } 
  if (card_chart_total) { card_chart_total.destroy(); } 
  if (chart_line_comprobante) { chart_line_comprobante.destroy(); } 

  $.getJSON("../ajax/escritorio.php?op=ver_reporte", { filtro_anio: filtro_mes.slice(0, 4), filtro_mes:filtro_mes, cant_mes: JSON.stringify(cant_mes) , filtro_trabajador:filtro_trabajador}, function (e, textStatus, jqXHR) {

    if (e.status == true) {

      /* :::::::::::::::::::::::::::::  O B J E T I V O ::::::::::::::::::::::::::::: */
      $(".obj_total_cobrado").html(e.data.objetivo.cobrado);
      $(".obj_total_cliente").html(e.data.objetivo.total);
      var options_obj = {
        chart: { height: 127, width: 100, type: "radialBar", },
        series: [ redondearExp(e.data.objetivo.porcentaje_este_mes,1) ],
        colors: ["rgba(255,255,255,0.9)"],
        plotOptions: {
          radialBar: {
            hollow: { margin: 0, size: "55%", background: "#fff" },
            dataLabels: {
              name: { offsetY: -10, color: "#4b9bfa", fontSize: ".625rem", show: false },
              value: { offsetY: 5, color: "#4b9bfa", fontSize: ".875rem", show: true, fontWeight: 600 }
            }
          }
        },
        stroke: { lineCap: "round"  },
        labels: ["Status"]
      };
      document.querySelector("#crm-main").innerHTML = ""
      chart_objetivo = new ApexCharts(document.querySelector("#crm-main"), options_obj);
      chart_objetivo.render();
      
      /* :::::::::::::::::::::::::::::  T O P   5   C L I E N T E S ::::::::::::::::::::::::::::: */
      var html_top_5_clientes = "";
      e.data.top_5_cliente.forEach((val, key) => {
        var img_proveedor = val.foto_perfil == '' || val.foto_perfil == null ? 'no-perfil.jpg' : val.foto_perfil;
        html_top_5_clientes = html_top_5_clientes.concat(`
          <li>
            <div class="d-flex align-items-top flex-wrap">
              <div class="me-2">
                <span class="avatar avatar-sm avatar-rounded">
                  <img src="../assets/modulo/persona/perfil/${img_proveedor}" alt="">
                </span>
              </div>
              <div class="flex-fill">
                <p class="fw-semibold mb-0 fs-11">${val.cliente_nombre_recortado}</p>
                <span class="text-muted fs-10">Cel: <a href="tel:+51${val.celular}">${val.celular}</a></span>
              </div>
              <div class="fw-semibold fs-13 text-success">${ formato_miles(val.total_cobrado) }</div>
            </div>
          </li>
        `);
      });
      $(".top-5-clientes").html(html_top_5_clientes);

      /* :::::::::::::::::::::::::::::  C O B R O S   P O R   D I A   D E   S E M A N A ::::::::::::::::::::::::::::: */
      
      var options1 = {
        series: [{ name: 'Factura', data: e.data.dia_semana.ds_total_f, }, { name: 'Boleta',  data: e.data.dia_semana.ds_total_b, }, { name: 'Tiket',  data: e.data.dia_semana.ds_total_t, }],
        chart: { type: 'bar', height: 180, toolbar: { show: false, }},
        grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
        colors: ["#49b6f5", "#26bf94", '#f5b849'],
        plotOptions: {
          bar: {
            colors: { ranges: [{ from: -100, to: -46, color: '#ebeff5' }, { from: -45, to: 0, color: '#ebeff5' }] },
            columnWidth: '60%',
            borderRadius: 5,
          }
        },
        dataLabels: { enabled: false, },
        stroke: { show: true, width: 2, colors: undefined, },
        legend: { show: true, position: 'top', },
        yaxis: {
          title: {
            style: {  color: '#adb5be', fontSize: '13px', fontFamily: 'poppins, sans-serif', fontWeight: 600, cssClass: 'apexcharts-yaxis-label', },
          },
          labels: { formatter: function (y) { return y.toFixed(0) + "";  } }
        },
        xaxis: {
          type: 'week',
          categories: e.data.dia_semana.ds_dia,
          axisBorder: { show: true, color: 'rgba(119, 119, 142, 0.05)', offsetX: 0, offsetY: 0, },
          axisTicks: { show: true, borderType: 'solid', color: 'rgba(119, 119, 142, 0.05)', width: 6, offsetX: 0, offsetY: 0 },
          labels: { rotate: -90 }
        }
      };
      document.getElementById('crm-profits-earned').innerHTML = '';
      chart_dia_semana = new ApexCharts(document.querySelector("#crm-profits-earned"), options1);
      chart_dia_semana.render();

      // ::::::::::::::::::::::::::::  C A R D   P O R   C O M P R O B A N T E :::::::::::::::::::::::::::::
      $('.card-total-factura').html( formato_miles(e.data.card_comprobante.factura) );
      $('.card-total-boleta').html( formato_miles(e.data.card_comprobante.boleta) );
      $('.card-total-ticket').html( formato_miles(e.data.card_comprobante.ticket) );
      $('.card-total').html( formato_miles(e.data.card_comprobante.factura + e.data.card_comprobante.boleta + e.data.card_comprobante.ticket) );
      var card_total = 0;
      e.data.card_comprobante.cant.forEach((val, key) => {
        if (val.tipo_comprobante == '01') { $('.card-cantidad-factura').html( formato_miles(val.cantidad) ); card_total += parseFloat(val.cantidad); }
        if (val.tipo_comprobante == '03') { $('.card-cantidad-boleta').html( formato_miles(val.cantidad) ); card_total += parseFloat(val.cantidad); }
        if (val.tipo_comprobante == '12') { $('.card-cantidad-ticket').html( formato_miles(val.cantidad) ); card_total += parseFloat(val.cantidad); }       
      });
      $('.card-cantidad-total').html( formato_miles(card_total));

      /* Total chart - factura */
      var crm1 = {
        chart:  { type: 'line', height: 80, width: 120, sparkline: { enabled: true } },
        stroke: { show: true, curve: 'smooth', lineCap: 'butt', colors: undefined, width: 1.5, dashArray: 0, },
        fill:   { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.9, stops: [0, 98], } },
        series: [{ name: 'Factura', data: e.data.card_comprobante.f_chart }],
        yaxis:  { min: 0, show: false, axisBorder: { show: false },},
        xaxis:  { show: false, categories: e.data.card_comprobante.fm_chart, axisBorder: { show: false},},
        tooltip: { enabled: true, style: { fontSize: '10px', }, },
        colors: ["rgb(132, 90, 223)"],
        // title: { text: 'Últimos 30 días.', align: 'left', style: { fontSize: '.8125rem', fontWeight: 'semibold', color: '#8c9097' }, },
        
      }
      document.getElementById('crm-total-customers').innerHTML = '';
      card_chart_factura = new ApexCharts(document.querySelector("#crm-total-customers"), crm1);
      card_chart_factura.render();         

      /* Total chart - boleta */
      var crm2 = {
        chart: { type: 'line', height: 80, width: 120, sparkline: { enabled: true } },
        stroke: { show: true, curve: 'smooth', lineCap: 'butt', colors: undefined, width: 1.5, dashArray: 0, },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.9, stops: [0, 98], }},
        series: [{ name: 'Boleta', data: e.data.card_comprobante.b_chart }],
        yaxis: { min: 0, show: false, axisBorder: { show: false }, },
        xaxis: { show: false, categories: e.data.card_comprobante.bm_chart, axisBorder: { show: false }, },
        tooltip: { enabled: true, style: { fontSize: '10px', }, },
        colors: ["rgb(35, 183, 229)"],
        // title: { text: 'Últimos 30 días.', align: 'left', style: { fontSize: '.8125rem', fontWeight: 'semibold', color: '#8c9097' }, },
      }
      document.getElementById('crm-total-revenue').innerHTML = '';
      card_chart_boleta = new ApexCharts(document.querySelector("#crm-total-revenue"), crm2);
      card_chart_boleta.render();      

      /* Total chart - ticket */
      var crm3 = {
        chart: { type: 'line', height: 80, width: 120, sparkline: { enabled: true } },
        stroke: { show: true, curve: 'smooth', lineCap: 'butt', colors: undefined, width: 1.5, dashArray: 0, },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.9, stops: [0, 98], } },
        series: [{ name: 'Ticket', data: e.data.card_comprobante.t_chart }],
        yaxis: { min: 0, show: false, axisBorder: { show: false }, },
        xaxis: { show: false, categories: e.data.card_comprobante.tm_chart, axisBorder: { show: false }, },
        tooltip: { enabled: true, style: { fontSize: '10px', }, },
        colors: ["rgb(38, 191, 148)"],
      }
      document.getElementById('crm-conversion-ratio').innerHTML = '';
      card_chart_ticket = new ApexCharts(document.querySelector("#crm-conversion-ratio"), crm3);
      card_chart_ticket.render();      

      /* Total chart  */
      var crm4 = {
        chart: { type: 'line', height: 80, width: 120, sparkline: { enabled: true } },
        stroke: { show: true, curve: 'smooth', lineCap: 'butt', colors: undefined, width: 1.5, dashArray: 0, },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.9, stops: [0, 98],} },
        series: [{ name: 'Total', data: e.data.card_comprobante.fbt_chart }],
        yaxis: { min: 0, show: false, axisBorder: { show: false}, },
        xaxis: { show: false, categories: e.data.card_comprobante.fbtm_chart, axisBorder: { show: false }, },
        tooltip: { enabled: true, style: { fontSize: '10px', }, },
        colors: ["rgb(245, 184, 73)"],
      }
      document.getElementById('crm-total-deals').innerHTML = '';
      card_chart_total = new ApexCharts(document.querySelector("#crm-total-deals"), crm4);
      card_chart_total.render();
      
      // ::::::::::::::::::::::::::::  CHART LINE   P O R   C O M P R O B A N T E :::::::::::::::::::::::::::::
      /* Revenue Analytics Chart */
      var options = {
        series: [
          {
            type: 'bar',
            name: 'Factura',
            data: e.data.chart_comprobante.factura
          },
          {
            type: 'line',
            name: 'Boleta',            
            data: e.data.chart_comprobante.boleta
          },
          {
            type: 'line',
            name: 'Ticket',
            chart: { dropShadow: { enabled: true, enabledOnSeries: undefined, top: 5, left: 0, blur: 3, color: '#000', opacity: 0.1 }},
            data: e.data.chart_comprobante.ticket
          },
          {
            type: 'bar',
            name: 'Total',
            chart: { dropShadow: { enabled: true, enabledOnSeries: undefined, top: 5, left: 0, blur: 3, color: '#000', opacity: 0.1 } },
            data: e.data.chart_comprobante.total
          }
        ],
        chart: {
          height: 350,
          animations: { speed: 500 },
          dropShadow: { enabled: true, enabledOnSeries: undefined, top: 8, left: 0, blur: 3, color: '#000', opacity: 0.1 },
        },
        colors: ["rgb(73, 182, 245)", "rgb(38, 191, 148)", "rgb(245, 184, 73)", "rgba(119, 119, 142, 0.15)"],
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
      document.getElementById('crm-revenue-analytics').innerHTML = '';
      chart_line_comprobante = new ApexCharts(document.querySelector("#crm-revenue-analytics"), options);
      chart_line_comprobante.render();     
      
      // ::::::::::::::::::::::::::::  TOP 5 PRODUCTOS :::::::::::::::::::::::::::::
      var html_top_5_producto = "";
      e.data.top_5_producto.forEach((val, key) => {
        var img_producto = val.imagen == '' || val.imagen == null ? 'no-producto.png' : val.imagen;
        html_top_5_producto = html_top_5_producto.concat(`<tr>          
          <td class="py-1">
            <div class="d-flex align-items-center fw-semibold">
              <span class="avatar avatar-sm me-2 avatar-rounded">
                <img src="../assets/modulo/productos/${img_producto}" alt="img">
              </span>${val.nombre_producto}
            </div>
          </td>          
          <td class="py-1">${val.categoria}</td>
          <td class="py-1 text-right">${formato_miles(val.precio_venta)}</td>
          <td class="py-1 text-center">${ parseFloat(val.cantidad) }</td>
          <td class="py-1 text-success text-right">${formato_miles(val.subtotal)}</td>          
        </tr>`);
      });
      $(".tabla-top-5-productos tbody").html(html_top_5_producto);

      // ::::::::::::::::::::::::::::  TOP 5 TECNICOS :::::::::::::::::::::::::::::

      var html_top_5_tecnico = ""; var html_top_5_tecnico_total = 0; chart_pastel_tecnico_data = e.data.top_5_tecnico.data_pay;
      var chart_pastel_tecnico_color = [`rgb(${myVarVal})`, 'rgb(35, 183, 229)', 'rgb(38, 191, 148)', 'rgb(245, 184, 73)', 'rgb(0, 0, 255)', ] ;

      e.data.top_5_tecnico.data.forEach((val, key) => {
        
        html_top_5_tecnico = html_top_5_tecnico.concat(`<div class="col p-0">
          <div class="ps-3 py-3 pe-3 text-center border-end border-inline-end-dashed">
            <span class="text-muted fs-9 mb-1 d-inline-block text-nowrap"><i class="bi bi-record-fill" style="color: ${chart_pastel_tecnico_color[key]};" ></i> ${val.nombre_razonsocial}</span>
            <div><span class="fs-13 fw-semibold">${formato_miles(val.total_cobrado)}</span></div>
          </div>
        </div>`);
        html_top_5_tecnico_total += parseFloat(val.total_cobrado) ;
      });
      $(".border-block-start-dashed").html(html_top_5_tecnico);
      $(".cart_pastel_total").html(formato_miles(html_top_5_tecnico_total));

      if (chart_pastel_tecnico) { chart_pastel_tecnico.destroy(); }

      /* Leads By Source Chart */
      Chart.defaults.elements.arc.borderWidth = 0;
      Chart.defaults.datasets.doughnut.cutout = '85%';
      chart_pastel_tecnico = new Chart(document.getElementById("leads-source"), {
        type: 'doughnut',
        data: {
          datasets: [{ 
            label: 'My First Dataset', data: e.data.top_5_tecnico.data_pay, 
            backgroundColor: chart_pastel_tecnico_color
          }]
        },
        plugins: [{
          afterUpdate: function (chart) {
            const arcs = chart.getDatasetMeta(0).data;
            arcs.forEach(function (arc) {
              arc.round = {
                x: (chart.chartArea.left + chart.chartArea.right) / 2,
                y: (chart.chartArea.top + chart.chartArea.bottom) / 2,
                radius: (arc.outerRadius + arc.innerRadius) / 2,
                thickness: (arc.outerRadius - arc.innerRadius) / 2,
                backgroundColor: arc.options.backgroundColor
              }
            });
          },
          afterDraw: (chart) => {
            const { ctx, canvas } = chart;

            chart.getDatasetMeta(0).data.forEach(arc => {
              const startAngle = Math.PI / 2 - arc.startAngle;
              const endAngle = Math.PI / 2 - arc.endAngle;

              ctx.save();
              ctx.translate(arc.round.x, arc.round.y);
              ctx.fillStyle = arc.options.backgroundColor;
              ctx.beginPath();
              ctx.arc(arc.round.radius * Math.sin(endAngle), arc.round.radius * Math.cos(endAngle), arc.round.thickness, 0, 2 * Math.PI);
              ctx.closePath();
              ctx.fill();
              ctx.restore();
            });
          }
        }]
      });

      /* simple donut chart */

      // ::::::::::::::::::::::::::::  TOP 5 CENTRO POBLADO :::::::::::::::::::::::::::::

      var html_top_5_cp = ""; var html_top_5_cp_progress = ""; var html_top_5_cp_total = 0;
      var chart_pastel_cp_color = [`rgb(${myVarVal})`, 'rgb(35, 183, 229)', 'rgb(38, 191, 148)', 'rgb(245, 184, 73)', 'rgb(0, 0, 255)', ] ;

      e.data.top_5_centro_poblado.forEach((val, key) => {
        
        html_top_5_cp = html_top_5_cp.concat(`<li class="primary my-2">
          <div class="d-flex align-items-center justify-content-between">
            <div><i class="bi bi-record-fill" style="color: ${chart_pastel_tecnico_color[key]};" ></i> ${val.nombre_centro_poblado}</div>
            <div class="fs-12 text-muted">${formato_miles(val.total_cobrado)}</div>
          </div>
        </li>`);
        html_top_5_cp_total += parseFloat(val.total_cobrado) ;
      });
      $(".list-centro-poblado").html(html_top_5_cp);
      $(".total-centro-poblado").html(`S/ ${formato_miles(html_top_5_cp_total)}`);

      e.data.top_5_centro_poblado.forEach((val, key) => {
        var fraccion_pc = redondearExp(((val.total_cobrado/html_top_5_cp_total) * 100), 2) ;
        html_top_5_cp_progress = html_top_5_cp_progress.concat(`
        <div class="progress-bar cursor-pointer" role="progressbar" data-bs-toggle="tooltip" title="${val.nombre_centro_poblado}: ${formato_miles(val.total_cobrado)}" style="width: ${fraccion_pc}%; background-color: ${chart_pastel_tecnico_color[key]};" aria-valuenow="${fraccion_pc}" aria-valuemin="0" aria-valuemax="100"></div>
        `);        
      });
      $(".progress-centro-poblado").html(html_top_5_cp_progress);

      // ::::::::::::::::::::::::::::  TOP 7 INCIDENCIAS :::::::::::::::::::::::::::::

      var html_top_7_incidencias = "";   var chart_pastel_cp_color = [`rgb(${myVarVal})`, 'rgb(35, 183, 229)', 'rgb(38, 191, 148)', 'rgb(245, 184, 73)', 'rgb(0, 0, 255)', ] ;

      e.data.top_7_incidencias.forEach((val, key) => {
        
        html_top_7_incidencias = html_top_7_incidencias.concat(`<li class="crm-recent-activity-content">
        <div class="d-flex align-items-top">
          <div class="me-3">
            <span class="avatar avatar-xs avatar-rounded ${val.estado_revicion_color}" > <i class="bi bi-circle-fill fs-8"></i></span>
          </div>
          <div class="crm-timeline-content">
            <span>
              <span class="fw-semibold">${val.actividad_v2}</span> 
              <span class="badge bg-info-transparent">${val.nombre_categoria}</span>
            </span>
            <span class="badge ${val.estado_revicion_color}">${val.estado_revicion}</span></span>
            <span class="d-block fs-12 text-muted">${val.actividad_detalle_v2}</span>
          </div>
          <div class="flex-fill text-end">
            <span class="d-block text-muted fs-11 op-7">${moment(val.created_at).fromNow()}</span>
          </div>
        </div>
      </li>`);
      });
      $(".crm-recent-activity").html(`${html_top_7_incidencias} <div class="text-center my-1"> <a href="incidencias.php" class="btn btn-primary btn-sm btn-wave" type="button">Ver mas</a></div>`);

    
      
      $('[data-bs-toggle="tooltip"]').tooltip();
    } else {
      ver_errores(e);
    }
  }).fail(function (e) { ver_errores(e); });

}

function crmProfitsearned() {
  if (chart_dia_semana) {
    chart_dia_semana.updateOptions({ colors: ["rgba(" + myVarVal + ", 1)", "#ededed"], });
  }  
}

function crmtotalCustomers() {
  if (card_chart_factura) {
    card_chart_factura.updateOptions({ colors: ["rgb(" + myVarVal + ")"], });
  }  
}

function revenueAnalytics() {
  if (chart_line_comprobante) {
    chart_line_comprobante.updateOptions({ colors: ["rgb(73, 182, 245)", "rgb(38, 191, 148)", "rgb(245, 184, 73)", "rgba(119, 119, 142, 0.15)"], });
  }        
}

function leads(myVarVal) {

  if (chart_pastel_tecnico) {
    chart_pastel_tecnico.data.datasets[0] = {
      label: 'My First Dataset',
      data: chart_pastel_tecnico_data,
      backgroundColor: chart_pastel_tecnico_color
    }
    chart_pastel_tecnico.update();
  } 
}

function ver_imagen_banco(file, nombre) {
  $('.foto-banco').html(nombre);
  $(".tooltip").removeClass("show").addClass("hidde");
  $("#modal-ver-perfil-banco").modal("show");
  $('#perfil-banco').html(`<center><img src="../assets/modulo/bancos/${file}" alt="Perfil" width="100%"></center>`);
}

$(document).ready(function () {
  init_b();
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..


function reload_filtro_periodo(){ $('#filtro_periodo').val("").trigger("change") } 
function reload_filtro_trabajador(){ lista_select2("../ajax/escritorio.php?op=select2_filtro_trabajador", '#filtro_trabajador', localStorage.getItem('nube_id_persona_trabajador'), '.charge_filtro_trabajador'); } 


function filtros() {  

  var filtro_mes    = $("#filtro_mes_contable").val() == '' || $("#filtro_mes_contable").val() == null ? '' : $("#filtro_mes_contable").val();
  var filtro_trabajador = $("#filtro_trabajador").val() == '' || $("#filtro_trabajador").val() == null ? '' : $("#filtro_trabajador").val() ;
  
  // $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_periodo} ${nombre_filtro_cliente}...`);

  reporte(  filtro_mes, filtro_trabajador);
}