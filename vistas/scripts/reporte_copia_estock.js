var tbla_resumen_estock;
var tbla_detalle_estock;
var chart_line_comprobante;


//Función que se ejecuta al inicio
function init() {

  $("#bloc_Recurso").addClass("menu-open");

  $("#mRecurso").addClass("active");  

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 
  lista_select2("../ajax/reporte_copia_estock.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');


  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#filtro_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_anio_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_mes_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_es_cobro").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  
  $("#filtro_tipo_comprob").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//Función Listar
function tabla_resumen_stock( filtro_fecha_i, filtro_fecha_f, filtro_trabajador) {

  tbla_resumen_estock = $('#tabla-resumen-copias').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-7 col-xl-7 col-xxl-7 pt-2'f><'col-md-5 col-lg-5 col-xl-5 col-xxl-5 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbla_resumen_estock) { tbla_resumen_estock.ajax.reload(null, false); } } },
      // { extend: 'excel', exportOptions: { columns: [0,6,7,8,2,3,4,5], }, title: 'Lista de Cobros', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_copia_estock.php?op=tabla_resumen_stock&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_trabajador=${filtro_trabajador}`,
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
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
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
      
    },
    initComplete: function () {

      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
      { targets: [1], render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY hh:mm:ss A'), },

    ],
  }).DataTable();
}

function tabla_detalle_stock( codigo_stock) {

  tbla_detalle_estock = $('#tabla-detalle-stock').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-7 col-xl-7 col-xxl-7 pt-2'f><'col-md-5 col-lg-5 col-xl-5 col-xxl-5 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbla_detalle_estock) { tbla_detalle_estock.ajax.reload(null, false); } } },
      { extend: 'excel', exportOptions: { columns: [0,6,7,8,2,3,4,5], }, title: 'Lista de Cobros', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_copia_estock.php?op=tabla_detalle_stock&codigo_stock=${codigo_stock}`,
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
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
      //{ targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [4,5], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

function guardar_editar_copia_stock(e) { 
  

  Swal.fire({
    title: "¿Está seguro de crear copia?",
    html: "Se creara la copia con los datos actuales de stock de cada producto",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Crear!",
    preConfirm: (input) => {
      const fecha = moment().format('YYYY-MM-DD hh:mm:ss a'); // con "am"/"pm"
      const formData = new FormData();
      formData.append('fecha_copia', fecha);
      return fetch("../ajax/reporte_copia_estock.php?op=guardar_editar_copia_stock", {
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
        if (tbla_resumen_estock) {tbla_resumen_estock.ajax.reload(null, false); }
        if (tbla_detalle_estock) {tbla_detalle_estock.ajax.reload(null, false); }      
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

function eliminar_copia_stock(codigo_stock, nombre, ) { 

  Swal.fire({
    title: "¿Está Seguro de Eliminar Permanente?",
    html: "Al Eliminarlo, no podra recuperarlo.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",    
    confirmButtonText: `<i class="fas fa-skull-crossbones"></i> Eliminar`,
    showLoaderOnConfirm: true,
    preConfirm: (input) => {       
      return fetch(`../ajax/reporte_copia_estock.php?op=eliminar_permanente&codigo_stock=${codigo_stock}`).then(response => {
        //console.log(response);
        if (!response.ok) { throw new Error(response.statusText) }
        return response.json();
      }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); })
    },
    allowOutsideClick: () => !Swal.isLoading()
  }).then((result) => {
    
    if (result.isConfirmed) {
      if (result.value.status) {
        Swal.fire("Eliminado!", "Tu registro ha sido ELIMINADO PERMANENTEMENTE.", "success");
        if (tbla_resumen_estock) {tbla_resumen_estock.ajax.reload(null, false); }
        if (tbla_detalle_estock) {tbla_detalle_estock.ajax.reload(null, false); }         
      }else{
        ver_errores(result.value);
      }
    }
  });
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
  var filtro_tipo_chart     = $("#filtro_tipo_chart").val() == '' || $("#filtro_tipo_chart").val() == null ? '' : $("#filtro_tipo_chart").val();  
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_trabajador  = ' ─ ' + $('#filtro_trabajador').find(':selected').text();
  
  if (filtro_fecha_i    == '' || filtro_fecha_i     == 0 || filtro_fecha_i    == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }       // filtro de trabajador  
  if (filtro_fecha_f    == '' || filtro_fecha_f     == 0 || filtro_fecha_f    == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }                 // filtro de dia pago  
  if (filtro_trabajador == '' || filtro_trabajador  == 0 || filtro_trabajador == null) { filtro_trabajador = ""; nombre_filtro_trabajador = ""; }                                     // filtro de plan

  $('#id_buscando_tabla th').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ${nombre_filtro_trabajador}...`);

  tabla_resumen_stock( filtro_fecha_i, filtro_fecha_f, filtro_trabajador);

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
      lista_select2("../ajax/reporte_copia_estock.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');
    break;    

    default:
      console.log('Caso no encontrado.');
  }
 
}
