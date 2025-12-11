var tabla_reporte_detalle;
var chart_line_comprobante;


//Función que se ejecuta al inicio
function init() {

  $("#bloc_Recurso").addClass("menu-open");

  $("#mRecurso").addClass("active");  

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 
  lista_select2("../ajax/reporte_contador.php?op=select2_centro_poblado", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado');
  lista_select2("../ajax/reporte_contador.php?op=select2_estado_sunat", '#filtro_estado_sunat', null, '.charge_filtro_estado_sunat');
  lista_select2("../ajax/reporte_contador.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#filtro_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_centro_poblado").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_estado_sunat").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_tipo_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  

}

//Función Listar
function tabla_principal_cliente(filtro_fecha_i, filtro_fecha_f, filtro_trabajador, filtro_tipo_persona, filtro_centro_poblado, filtro_estado_sunat, filtro_tipo_comprobante) {

  tabla_reporte_detalle = $('#tabla-reporte-detalle').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-7 col-xl-7 col-xxl-7 pt-2'f><'col-md-5 col-lg-5 col-xl-5 col-xxl-5 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_reporte_detalle) { tabla_reporte_detalle.ajax.reload(null, false); } } },
      { extend: 'excel', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14], }, title: 'Reporte de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_contador.php?op=tabla_reporte_detalle`,
      data: function (d) {
        d.filtro_fecha_i        = filtro_fecha_i;
        d.filtro_fecha_f        = filtro_fecha_f;
        d.filtro_trabajador     = filtro_trabajador;
        d.filtro_tipo_persona   = filtro_tipo_persona;
        d.filtro_centro_poblado = filtro_centro_poblado;
        d.filtro_estado_sunat   = filtro_estado_sunat;
        d.filtro_tipo_comprobante = filtro_tipo_comprobante;
      },
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
      // var api1 = this.api(); var total1 = api1.column( 3).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      // $( api1.column( 3 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` ); 
      
      // var api2 = this.api(); var total2 = api2.column( 4).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      // $( api2.column( 4 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total2)}</span> ` ); 
    },
    initComplete: function () {

      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 25,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
      //{ targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [7,8,9], visible: false, searchable: false, },
      // { targets: [3,4], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

function previsulizar_documentos() {
  cargando_search(); 
  delay(function(){filtros()}, 50 );
}


function exportar_documentos() {
  // Valores simples
  const filtro_fecha_i        = $("#filtro_fecha_i").val() || '';
  const filtro_fecha_f        = $("#filtro_fecha_f").val() || '';
  const filtro_trabajador     = $("#filtro_trabajador").val() || '';
  const filtro_tipo_persona   = $("#filtro_tipo_persona").val() || '';
  const filtro_centro_poblado = $("#filtro_centro_poblado").val() || '';

  // Valores múltiples (arrays)
  const filtro_estado_sunat     = $("#filtro_estado_sunat").val() || [];
  const filtro_tipo_comprobante = $("#filtro_tipo_comprobante").val() || [];

  // Armar querystring
  const params = new URLSearchParams();

  // Agregar parámetros simples
  params.append('filtro_fecha_i', filtro_fecha_i);
  params.append('filtro_fecha_f', filtro_fecha_f);
  params.append('filtro_trabajador', filtro_trabajador);
  params.append('filtro_tipo_persona', filtro_tipo_persona);
  params.append('filtro_centro_poblado', filtro_centro_poblado);

  // Agregar arrays correctamente (nombre[]=valor)
  filtro_estado_sunat.forEach(val => params.append('filtro_estado_sunat[]', val));
  filtro_tipo_comprobante.forEach(val => params.append('filtro_tipo_comprobante[]', val));

  if ( filtro_estado_sunat.length === 0  ) { params.append('filtro_estado_sunat', '')  }
  if ( filtro_tipo_comprobante.length === 0  ) { params.append('filtro_tipo_comprobante', '')  }

  // Redirigir
  // window.location.href = '../ajax/reporte_contador.php?op=exportar_excel_venta&' + params.toString();
  window.open('../ajax/reporte_contador.php?op=exportar_excel_venta&' + params.toString(), '_blank');
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

  var filtro_fecha_i        = $("#filtro_fecha_i").val()    == '' || $("#filtro_fecha_i").val() == null ? '' : $("#filtro_fecha_i").val();
  var filtro_fecha_f        = $("#filtro_fecha_f").val()    == '' || $("#filtro_fecha_f").val() == null ? '' : $("#filtro_fecha_f").val();
  var filtro_trabajador     = $("#filtro_trabajador").val() == '' || $("#filtro_trabajador").val() == null ? '' : $("#filtro_trabajador").val();  
  var filtro_tipo_persona     = $("#filtro_tipo_persona").val() == '' || $("#filtro_tipo_persona").val() == null ? '' : $("#filtro_tipo_persona").val();  
  var filtro_centro_poblado     = $("#filtro_centro_poblado").val() == '' || $("#filtro_centro_poblado").val() == null ? '' : $("#filtro_centro_poblado").val();  
  var filtro_estado_sunat     = $("#filtro_estado_sunat").val() == '' || $("#filtro_estado_sunat").val() == null ? '' : $("#filtro_estado_sunat").val();  
  var filtro_tipo_comprobante     = $("#filtro_tipo_comprobante").val() == '' || $("#filtro_tipo_comprobante").val() == null ? '' : $("#filtro_tipo_comprobante").val();  
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_trabajador  = ' ─ ' + $('#filtro_trabajador').find(':selected').text();
  
  if (filtro_fecha_i    == '' || filtro_fecha_i     == 0 || filtro_fecha_i    == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }       // filtro de trabajador  
  if (filtro_fecha_f    == '' || filtro_fecha_f     == 0 || filtro_fecha_f    == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }                 // filtro de dia pago  
  if (filtro_trabajador == '' || filtro_trabajador  == 0 || filtro_trabajador == null) { filtro_trabajador = ""; nombre_filtro_trabajador = ""; }                                     // filtro de plan

  $('#id_buscando_tabla th').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ${nombre_filtro_trabajador}...`);

  tabla_principal_cliente( filtro_fecha_i, filtro_fecha_f, filtro_trabajador, filtro_tipo_persona, filtro_centro_poblado, filtro_estado_sunat, filtro_tipo_comprobante);

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
