var tbla_resumen_estock;
var tbla_detalle_estock;
var chart_line_comprobante;


//Función que se ejecuta al inicio
function init() {

  $("#bloc_Recurso").addClass("menu-open");

  $("#mRecurso").addClass("active");  

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 
  lista_select2("../ajax/reporte_validacion_estock.php?op=select2_filtro_copias_stock", '#filtro_copia_stock', null, '.charge_filtro_copia_stock');


  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  

  
  $("#filtro_copia_stock").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_estado_stock").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}


function tabla_detalle_stock( filtro_copia_stock, filtro_fecha_copia_stock, filtro_estado_stock) {

  tbla_detalle_estock = $('#tabla-detalle-stock').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-7 col-xl-7 col-xxl-7 pt-2'f><'col-md-5 col-lg-5 col-xl-5 col-xxl-5 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbla_detalle_estock) { tbla_detalle_estock.ajax.reload(null, false); } } },
      { extend: 'excel', exportOptions: { columns: [0,1,2,3,4,5,6,7], }, title: 'Lista de Stock', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_validacion_estock.php?op=tabla_detalle_stock&filtro_copia_stock=${filtro_copia_stock}&filtro_fecha_copia_stock=${filtro_fecha_copia_stock}&filtro_estado_stock=${filtro_estado_stock}`,
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
        $('.buscando_tabla').hide();
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
      info: "Mostrando _START_ a _END_ de _TOTAL_"
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
      { targets: [3,4,5,6,7], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

$(document).ready(function () {
  init();
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);

  var filtro_copia_stock                = $('#filtro_copia_stock').find(':selected').text();
  var filtro_estado_stock                = $('#filtro_estado_stock').find(':selected').text();
  
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_copia_stock}  ${filtro_estado_stock} ...`);
}

function filtros() {  

  var filtro_copia_stock        = $('#filtro_copia_stock').val() == '' || $('#filtro_copia_stock').val() == '' ? '' : $('#filtro_copia_stock').val()  ;
  var filtro_fecha_copia_stock  = ($("#filtro_copia_stock").find(":selected").attr("fecha_creacion") || "").trim()  ;
  var filtro_estado_stock       = $('#filtro_estado_stock').val() == '' || $('#filtro_estado_stock').val() == '' ? '' : $('#filtro_estado_stock').val()  ;

  tabla_detalle_stock( filtro_copia_stock, filtro_fecha_copia_stock, filtro_estado_stock );

}

function reload_select(r_text) {

  switch (r_text) {
    
    case 'filtro_copia_stock':
      lista_select2("../ajax/reporte_validacion_estock.php?op=select2_filtro_copias_stock", '#filtro_copia_stock', null, '.charge_filtro_copia_stock');
    break;    

    default:
      console.log('Caso no encontrado.');
  }
 
}
