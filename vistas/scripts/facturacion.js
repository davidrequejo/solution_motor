
var tabla_principal_facturacion;
var tabla_productos;
var form_validate_facturacion;
var tabla_ver_mas_detalle_facturacion;

var array_data_venta = [];
var file_pond_mp_comprobante;
var file_pond_mp_comprobante_2mas;
var cambio_de_tipo_comprobante ;
const filePondInstances = [];

var filtro_estado_sunat = "" ;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_tipo_documento = new Choices('#cli_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_distrito       = new Choices('#cli_distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// var choice_centro_poblado = new Choices('#cli_centro_poblado',  { shouldSort: false, removeItemButton: true,noResultsText: 'No hay resultados.', } );

var myElement1 = document.getElementById('recent-jobs');
myElement1.style.height = '300px'; // Cambia esta altura según tus necesidades
new SimpleBar(myElement1, { autoHide: true });

// Inicializa Dragula en el contenedor de cuotas
dragula([document.querySelector('#cuotas-container')])
  .on('drag', function (el) {
    el.classList.add('dragging'); // Agrega clase cuando arrastra
  })
  .on('dragend', function (el) {
    el.classList.remove('dragging'); // Quita clase cuando suelta
  })
  .on('drop', function (el, target, source, sibling) {
    console.log("Se ha reorganizado una cuota.");
  });


async function init(){

  // filtros(); // Listamos la tabla principal
  $(".btn-tiket").click();   // Selecionamos la BOLETA
  //mini_reporte();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-venta").submit(); }  });  
  $("#guardar_registro_producto").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); } });
  $("#guardar_registro_nuevo_cliente").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-nuevo-cliente").submit(); } else {toastr_info('Espere!!', 'Por favor sea pasiente se estan procesando los datos...');} });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante');
  lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente');
  lista_select2("../ajax/facturacion.php?op=select2_banco", '#filtro_metodo_pago', null, '.charge_filtro_metodo_pago');
  lista_select2("../ajax/ajax_general.php?op=select2_centro_poblado_venta", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado');

  lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante');
  lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente');

  lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', null, '.charge_f_idpersona_cliente');
  lista_select2("../ajax/facturacion.php?op=select2_codigo_x_anulacion_comprobante", '#f_nc_motivo_anulacion', '01');  
  lista_select2("../ajax/facturacion.php?op=select2_banco", '#f_metodo_pago_1', null, 'charge_f_metodo_pago_1');  

  lista_select2("../ajax/facturacion.php?op=select2_periodo_contable", '#filtro-periodo-facturado', moment().format('YYYY-MM'));  

  lista_select2("../ajax/cliente.php?op=select2_filtro_trabajador", '#filtro-trabajador', localStorage.getItem('nube_id_persona_trabajador'), '.charge_filtro_trabajador');

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null); 
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_centro_poblado", choice_centro_poblado, null);  

  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#cli_distrito', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#f_idpersona_cliente").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_nc_tipo_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_nc_serie_y_numero").select2({ templateResult: templateSerieNumero, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_nc_motivo_anulacion").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_metodo_pago_1").select2({  templateResult: templateBanco, templateSelection: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  

  $("#filtro_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_tipo_persona").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, }); $("#filtro_tipo_persona").val('').trigger("change");
  $("#filtro_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_metodo_pago").select2({ templateResult: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_centro_poblado").select2({ templateResult:templateCentroPoblado, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_md_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_md_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#f_nc_tipo_comprobante").val(null).trigger('change'); // Limpiamos aqui para no generar un bucle infinito  

  $("#cli_distrito").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});
  $("#cli_tipo_documento").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});

  await activar_btn_agregar(); // Esperamos a al carga total de los datos para poder: CREAR
}

async function activar_btn_agregar() {
  $(".btn-agregar").show();
}

function templateCentroPoblado (state) {
  //console.log(state);
  if (!state.id) { return state.text; } 
  var $state = $(`<span class="fs-11" > <i class="bi bi-geo-alt"></i></span><span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function templateCliente (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
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

function templateComprobante (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../dist/docs/persona/perfil/${state.title}`: '../dist/svg/user_default.svg'; 
  var onerror = `onerror="this.src='../dist/svg/user_default.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function templateSerieNumero (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var dif_fecha_emision = state.title != '' ? `<span class="fs-9">${state.title}</span>`: '-';  
  var $state = $(`<span class="fs-12" >${state.text} &raquo; ${dif_fecha_emision}</span>`);
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

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M I N I   R E P O R T E                                                         ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
function mini_reporte() {

  $(".vw_total_factura").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $(".vw_total_boleta").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $(".vw_total_ticket").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  var periodo_facturado = $("#filtro-periodo-facturado").val();

  $.getJSON(`../ajax/facturacion.php?op=mini_reporte`, {periodo_facturado:periodo_facturado}, function (e, textStatus, jqXHR) {

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
      var options_ultimos_6_meses = {
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
      var chart_barra = new ApexCharts(document.querySelector("#invoice-list-stats"), options_ultimos_6_meses);
      chart_barra.render();

      
    } else {
      ver_errores(e);
    }

  }).fail( function(e) { ver_errores(e); } );  
}

function mini_reporte_v2() {
  $('#avance-plan tbody').html(`<tr><td colspan="3" class="" ><div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div></td></tr>`);

  $.getJSON(`../ajax/facturacion.php?op=mini_reporte_v2`, { filtro_periodo:$("#filtro-periodo-cobro").val(), filtro_trabajador:$("#filtro-trabajador").val() }, function (e, textStatus, jqXHR) {
    
    $('.total_avance_cobrado').html(`${e.data.total.cant_cobrado}`);
    $('.total_avance_cobrado_porcent').html(`${ redondearExp(e.data.total.avance, 1)}%`);
    $('.total_avance_por_cobrar').html(`${  (e.data.total.cant_total - e.data.total.cant_cobrado) }`);
    $('.total_avance_por_cobrar_porcent').html(`${ redondearExp( (100 - e.data.total.avance), 1) }%`);

    $('#avance-plan tbody').html('');
    e.data.centro_poblado.forEach((val, key) => {
      $('#avance-plan tbody').append(`
        <tr>
          <td class="py-1 ">
            <div class="d-flex align-items-center">
              <span class="avatar avatar-rounded avatar-sm p-2  me-2">
                <i class="ri-map-pin-line fs-15 text-primary"></i>
              </span>
              <div class="fw-semibold fs-10">${val.centro_poblado} (${val.cant_cobrado}/${val.cant_total})</div>
            </div>
          </td>                          
          <td class="py-1 ">
            <div class="progress progress-xs">
              <div class="progress-bar ${(val.avance == 100 ? 'bg-success' : 'bg-primary')}" role="progressbar" style="width: ${val.avance}%" aria-valuenow="${val.avance}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="fs-10"><i class="ri-arrow-up-s-fill me-1 text-success align-middle "></i>${val.avance}%</span>
          </td>
          
        </tr>
      `);
    });

  }).fail( function(e) { ver_errores(e); } );
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  F A C T U R A C I O N                                                          ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

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
  $("#f_idventa").val('');
  $("#f_nc_idventa").val('0');

  $('#f_crear_y_emitir').prop('checked', false)
  $('#f_tipo_comprobante12').prop('checked', true).focus().trigger('change'); 
  $("#f_idpersona_cliente").val('').trigger('change'); 
  $("#f_metodo_pago_1").val('EFECTIVO').trigger('change'); 
  $("#f_observacion_documento").val(''); 
  $("#f_periodo_pago").val('');
  $("#search_producto").val('');  
  
  $('#html-metodos-de-pagos').html('');
  $("#f_total_recibido").val(0);
  $("#f_total_vuelto").val(0);
  $("#f_ua_monto_usado").val('');
  $("#f_mp_serie_comprobante").val('');

  if (file_pond_mp_comprobante) {
    if (typeof file_pond_mp_comprobante.removeFiles === 'function') { // Si el método removeFiles existe, lo usamos        
      file_pond_mp_comprobante.removeFiles();
    } else { // Si no existe, manejamos el error o reiniciamos FilePond       
      console.warn('El método removeFiles no está disponible.');// Opcional: Reiniciar FilePond manualmente      
      file_pond_mp_comprobante.destroy(); // Destruye la instancia actual
      file_pond_mp_comprobante = FilePond.create( document.querySelector('#f_mp_comprobante_1'), FilePond_Facturacion_LabelsES );
    }
  }
  
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

  $(".filas_producto_agregado").remove();  

  cont = 0;
  
  count_cuotas = 1;
  $("#f_vc_idventa").val("");
  $('#cuotas-container').html('');
  agregar_cuota();
  $('#f_venta_cuotas').prop('checked', false).change();

  $('.input_cantidad_venta').each(function(e) { $(this).rules("remove"); });  
  $('.input_precio_con_igv').each(function(e) { $(this).rules("remove"); });  
  $('.input_f_descuento').each(function(e) { $(this).rules("remove"); });  

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function limpiar_form_venta_nc(){

  array_data_venta = [];
  $("#f_idventa").val('');
  $("#f_nc_idventa").val('0');
  
  $("#f_idpersona_cliente").val('').trigger('change'); 
  $("#f_metodo_pago_1").val('').trigger('change'); 
  $("#f_observacion_documento").val(''); 
  $("#f_periodo_pago").val('');
  $("#search_producto").val('');  
  
  $('#html-metodos-de-pagos').html('');
  $("#f_total_recibido").val(0);
  $("#f_total_vuelto").val(0);
  $("#f_ua_monto_usado").val('');
  $("#f_mp_serie_comprobante").val('');  

  if (file_pond_mp_comprobante) {
    if (typeof file_pond_mp_comprobante.removeFiles === 'function') { // Si el método removeFiles existe, lo usamos        
      file_pond_mp_comprobante.removeFiles();
    } else { // Si no existe, manejamos el error o reiniciamos FilePond       
      console.warn('El método removeFiles no está disponible.');// Opcional: Reiniciar FilePond manualmente      
      file_pond_mp_comprobante.destroy(); // Destruye la instancia actual
      file_pond_mp_comprobante = FilePond.create( document.querySelector('#f_mp_comprobante_1'), FilePond_Facturacion_LabelsES );
    }
  }
  
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

  $(".filas_producto_agregado").remove();

  cont = 0;

  count_cuotas = 1;
  $("#f_vc_idventa").val("");
  $('#cuotas-container').html('');
  agregar_cuota();
  $('#f_venta_cuotas').prop('checked', false).change();

  $('.input_cantidad_venta').each(function(e) { $(this).rules("remove"); });  
  $('.input_precio_con_igv').each(function(e) { $(this).rules("remove"); });  
  $('.input_f_descuento').each(function(e) { $(this).rules("remove"); });  

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_tipo_persona, filtro_comprobante, filtro_metodo_pago, filtro_centro_poblado, filtro_estado_sunat){
  
  tabla_principal_facturacion = $("#tabla-ventas").dataTable({
    responsive: false,     
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    // aProcessing: true, //Activamos el procesamiento del datatables
    // aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-8 col-xxl-9 pt-2'f><'col-md-5 col-lg-4 col-xl-4 col-xxl-3 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-sm px-2 btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_principal_facturacion) { tabla_principal_facturacion.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6,7,8,11], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-sm px-2 btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6,7,8,11], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-sm px-2 btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6,7,8,11], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-sm px-2 btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-sm px-2 btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/facturacion.php?op=listar_tabla_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_tipo_persona=${filtro_tipo_persona}&filtro_comprobante=${filtro_comprobante}&filtro_metodo_pago=${filtro_metodo_pago}&filtro_centro_poblado=${filtro_centro_poblado}&filtro_estado_sunat=${filtro_estado_sunat}`,
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
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap"); }
      // columna: Monto
      if (data[9] != '') { $("td", row).eq(9).addClass("text-nowrap text-center"); }
      // columna: Boucher
      if (data[10] != '') { $("td", row).eq(10).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "_MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      search: "",
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 7 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 7 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    initComplete: function () {

      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [3], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [7], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();

  
}

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
      return fetch("../ajax/facturacion.php?op=guardar_editar_facturacion", {
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
        limpiar_form_venta(); show_hide_form(1); reload_f_nc_serie_y_numero();
        if ($('#f_crear_y_mostrar').is(':checked')) {
          ver_formato_ticket(result.value.data, '12');
        }
      } else if ( result.value.status == 'error_personalizado'){        
        tabla_principal_facturacion.ajax.reload(null, false);
        limpiar_form_venta(); show_hide_form(1); reload_f_nc_serie_y_numero(); ver_errores(result.value);
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

function mostrar_detalle_venta(idventa){
  $("#modal-detalle-venta").modal("show");

  $.post("../ajax/facturacion.php?op=mostrar_detalle_venta", { idventa: idventa }, function (e, status) {          
      
    $('#custom-tabContent').html(e);      
    $('#custom-datos1_html-tab').click(); // click para ver el primer - Tab Panel
    $(".jq_image_zoom").zoom({ on: "grab" });      
    $("#excel_venta").attr("href",`../reportes/export_xlsx_venta_tours.php?id=${idventa}`);      
    $("#print_pdf_venta").attr("href",`../reportes/comprobante_venta_tours.php?id=${idventa}`);    
    
  }).fail( function(e) { ver_errores(e); } );

}

function eliminar_papelera_venta(idventa, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/facturacion.php?op=papelera",
    "../ajax/facturacion.php?op=eliminar", 
    idventa, 
    "!Elija una opción¡", 
    `Comnprobante: <b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function cambiar_a_por_enviar(idventa, nombre) {

  crud_simple_alerta(
    `../ajax/facturacion.php?op=cambiar_a_por_enviar&idventa=${idventa}`,
    '', 
    "Está Seguro?", 
    `Se cambiara el estado de: <b class="text-danger">${nombre}</b> <br> al hacerlo le permitirá crear mas Boletas o Facturas, en caso contrario desactive el envio a a SUNAT!`, 
    'Si, Cambiar',
    function(){ sw_success('Cambiado!!', "Tu registro ha actualizado." ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function cambiar_a_aceptado(idventa, nombre, mensaje) {

  crud_simple_alerta(
    `../ajax/facturacion.php?op=cambiar_a_aceptado&idventa=${idventa}&mensaje=${mensaje}`,
    '', 
    "Está Seguro?", 
    `Se cambiara el estado de: <b class="text-danger">${nombre}</b> <br> al hacerlo le permitirá crear mas Boletas o Facturas, en caso contrario desactive el envio a a SUNAT!`, 
    'Si, Cambiar',
    function(){ sw_success('Cambiado!!', "Tu registro ha actualizado." ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function status_documento_sunat(idventa, nombre) {

  crud_simple_alerta(
    `../ajax/facturacion.php?op=status_documento_sunat&idventa=${idventa}`,
    '', 
    "Está Seguro?", 
    `Validar Estado: <b class="text-danger">${nombre}</b> <br> al hacerlo le permitirá saber cual es el estado actual ante SUNAT!`, 
    'Si, validar',
    function(){ sw_success('Actualizad!!', "Tu registro ha actualizado." ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
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

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M O S T R A R   S E R I E S                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function ver_series_comprobante(input) {
  
  if (cambio_de_tipo_comprobante == '07') { limpiar_form_venta_nc(); } // Limpiamos si el comprobante anterior era: Nota de Credito

  $("#f_serie_comprobante").html('');
  $(".f_charge_serie_comprobante").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  var tipo_comprobante = $(input).val() == ''  || $(input).val() == null ? '' : $(input).val(); console.log(tipo_comprobante);  
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
      $("#f_metodo_pago_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });       
      $("#f_total_recibido_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });    
      
      if ($('#f_vc_idventa').val() == 0 || $('#f_vc_idventa').val() == null || $('#f_vc_idventa').val() == '' ) {        
        $(".div-cuotas-checkbox").show();
        if ($('#f_venta_cuotas').prop('checked') == true) { $(".div-cuotas-container").show(); } else { $(".div-cuotas-container").hide(); }
      } else {
        // No hacemos nada por ahroa
      }
      

    } else if ( tipo_comprobante == '03') {
      $("#f_idsunat_c01").val(3); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago_1").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_total_recibido_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });

      if ( $('#f_vc_idventa').val() == 0 || $('#f_vc_idventa').val() == null || $('#f_vc_idventa').val() == ''  ) {
        if ($('#f_venta_cuotas').prop('checked') == true) { $('#f_venta_cuotas').prop('checked', false).change(); } else { $(".div-cuotas-container").hide(); } 
        $(".div-cuotas-checkbox").hide();
      }
      

    } else if ( tipo_comprobante == '07') {
      var nc_tipo_comprobante = $('#f_nc_tipo_comprobante').val() == '' || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
      if (nc_tipo_comprobante == '01') {
        $("#f_idsunat_c01").val(7); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      } else if (nc_tipo_comprobante == '03') {
        $("#f_idsunat_c01").val(8); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      }
      
      var nc_tp = $('#f_nc_tipo_comprobante').val() == ''  || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
      $("#f_periodo_pago").rules('remove', 'required');
      $("#f_metodo_pago_1").rules('remove', 'required');
      $("#f_total_recibido_1").rules('remove', 'required');
      $('#f_es_cobro_inp').val('NO'); $('#f_usar_anticipo').val('NO');

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
      
      $(".div-cuotas-checkbox").hide();
      $(".div-cuotas-container").hide();

    } else if ( tipo_comprobante == '12') {
      $("#f_idsunat_c01").val(12); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante      
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago_1").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_total_recibido_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });
      
      $(".div-cuotas-checkbox").show();
      if ($('#f_venta_cuotas').prop('checked') == true) { $(".div-cuotas-container").show(); } else { $(".div-cuotas-container").hide(); }      
      
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

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M O S T R A R   C O B R O S                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function es_cobro_valid() { console.log($(".es_cobro").hasClass("on"));
  var tipo_comprobante = $('#f_tipo_comprobante_hidden').val() == ''  || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();
  if ($(".es_cobro").hasClass("on") == true) {
    $("#f_es_cobro_inp").val("SI");
    $(".datos-de-cobro-mensual").show("slow");   
    if (form_validate_facturacion) { 
      if ( tipo_comprobante == '07') { } else{
        $("#f_periodo_pago").rules('add', { required: true, messages: {  required: "Campo requerido" } });
      }
    }
  } else {
    $("#f_es_cobro_inp").val("NO");
    $(".datos-de-cobro-mensual").hide("slow");
    if (form_validate_facturacion) { $("#f_periodo_pago").rules('remove', 'required'); }
  }
  $("#form-facturacion").valid();
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M O S T R A R   A N T I C I P O S                                              ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

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

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C L I E N T E   V A L I D O                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
function es_valido_cliente() {

  var id_cliente = $('#f_idpersona_cliente').val() == ''  || $('#f_idpersona_cliente').val() == null ? '' : $('#f_idpersona_cliente').val();
  $(".span_dia_cancelacion").html(``);

  if (id_cliente != null && id_cliente != '') {

    var tipo_comprobante = $('#f_tipo_comprobante_hidden').val() == ''  || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();
    var tipo_documento    = $('#f_idpersona_cliente').select2('data')[0].element.attributes.tipo_documento.value;
    var numero_documento  = $('#f_idpersona_cliente').select2('data')[0].element.attributes.numero_documento.value;
    var direccion         = $('#f_idpersona_cliente').select2('data')[0].element.attributes.direccion.value;  
    var dia_cancelacion = '';  
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
              var id_cliente = $("#f_idpersona_cliente").select2('val') == null ? '' : $("#f_idpersona_cliente").select2('val');
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
            var id_cliente = $("#f_idpersona_cliente").select2('val') == null ? '' : $("#f_idpersona_cliente").select2('val');
            lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', id_cliente);
            sw_success('Datos Actualizado!!', 'Se actualizado el Nro Documento correctamente');
          }else{
            $("#f_idpersona_cliente").val('').trigger('change'); 
            $(".span_dia_cancelacion").html(``);
          }
        });
      } else {
        sw_cancelar('Cliente no permitido', `El cliente no cumple con los siguientes requsitos:  <ul class="pt-3 text-left font-size-13px"> ${campos_requeridos} </ul>`, 10000);
        $("#f_idpersona_cliente").val('').trigger('change'); 
        $(".span_dia_cancelacion").html(``);
      }
      
    }   
    
    console.log(tipo_comprobante, tipo_documento, numero_documento, direccion, es_valido);
  }
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C R E A R   C L I E N T E                                                      ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function crear_cliente() {
  limpiar_nuevo_cliente();
  $(`#modal-agregar-nuevo-cliente`).modal('show');
}

function limpiar_nuevo_cliente() {
  $('#cli_tipo_documento').val('0').trigger('change');  
  $('#cli_nombre_razonsocial').val('');
  $('#cli_apellidos_nombrecomercial').val('');
  $('#cli_correo').val('');
  $('#cli_celular').val('');
  $('#cli_direccion').val('');
  $('#cli_direccion_referencia').val('');
    $('#cli_distrito').val('TARAPOTO').trigger('change');  
}

function guardar_editar_nuevo_cliente(e){ 
  var formData = new FormData($("#form-agregar-nuevo-cliente")[0]);
  $.ajax({
    url: "../ajax/facturacion.php?op=guardar_editar_cliente",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); 
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");          
          lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', e.data, '.charge_f_idpersona_cliente');
          limpiar_nuevo_cliente();
          $('#modal-agregar-nuevo-cliente').modal('hide');
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_nuevo_cliente").html('Guardar Cambios').removeClass('disabled send-data');
    },
    xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					$("#barra_progress_nuevo_cliente").css({ "width": percentComplete + '%' });
					$("#barra_progress_nuevo_cliente div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
    beforeSend: function () {
      $("#guardar_registro_nuevo_cliente").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_nuevo_cliente").css({ width: "0%", });
      $("#barra_progress_nuevo_cliente div").text("0%");
      $("#barra_progress_nuevo_cliente_div").show();
    },
    complete: function () {
      $("#barra_progress_nuevo_cliente").css({ width: "0%", });
      $("#barra_progress_nuevo_cliente div").text("0%");
      $("#barra_progress_nuevo_cliente_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

$('#cli_tipo_documento').change(function () {
  var tipo = $(this).val() == '' ||  $(this).val() == null ? '' : $(this).val() ;

  if ( tipo == '0' ) { 
    $("#cli_numero_documento").val('00000000').addClass('bg-dark-transparent').prop('readonly', true).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('#cli_apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }else if (tipo == '1') {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }else if (tipo == '6') {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_apellidos_nombrecomercial').rules('remove', 'required');
    $('.label-nom-raz').html('Razón Social <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Nombre comercial <sup class="text-danger">*</sup>');
    $("#cli_numero_documento").rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_tipo_persona_sunat').val('JURÍDICA');
  } else {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false);
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }

  $("#form-agregar-nuevo-cliente").valid();

});

$('#cli_distrito').on('change', async function () {  

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);

  await esperar(1000);  // Espera 5 segundos  

  if ($('#cli_distrito').val()== null || $('#cli_distrito').val() == '') {
    $("#departamento").val("");
    $("#provincia").val("");
    $("#ubigeo").val("");
    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito = $('#cli_distrito').select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {
      e = JSON.parse(e); 
      $("#cli_departamento").val(e.data.departamento);
      $("#cli_provincia").val(e.data.provincia);
      $("#cli_ubigeo").val(e.data.ubigeo_inei);

      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      
      $("#form-agregar-nuevo-cliente").valid();
    });
  }
});

function esperar(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  F O R M A T O S   D E   I M P R E S I O N                                      ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function ver_formato_ticket(idventa, tipo_comprobante) {
  const tipos = {
    '01': 'FACTURA',
    '03': 'BOLETA',
    '07': 'NOTA DE CRÉDITO',
    '12': 'NOTA DE VENTA'
  };
  
  if (!tipos[tipo_comprobante]) {
    toastr_error('No Existe!!', 'Este tipo de documento no existe.');
    return;
  }

  const screenHeight = window.innerHeight;
  const height_calculado = screenHeight * (screenHeight > 700 ? 0.75 : 0.65);
  const ruta = `../reportes/TicketFormatoPDF.php?id=${idventa}`;
  
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl") .addClass("modal-md");
  
  $("#modal-imprimir-comprobante-label").html(`
    <button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" 
      data-bs-toggle="tooltip" title="Imprimir Ticket" 
      onclick="printIframe('iframe_format_ticket')">
      <i class="ri-printer-fill"></i>
    </button> FORMATO - ${tipos[tipo_comprobante]}
  `);
  
  $("#html-imprimir-comprobante").html(`
    <iframe name="iframe_format_ticket" id="iframe_format_ticket"
      src="${ruta}" border="0" frameborder="0" width="100%"
      style="height: ${height_calculado}px;" marginwidth="1">
    </iframe>
  `);

  $("#modal-imprimir-comprobante").modal("show");
}


// function ver_formato_ticket(idventa, tipo_comprobante) {
//   var screenHeight = window.innerHeight; 
//   var height_calculado = screenHeight > 700 ? screenHeight * 0.75 : screenHeight * 0.65;

//   $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl").addClass("modal-md");
//   if (tipo_comprobante == '01') {
//     var rutacarpeta = "../reportes/TicketFormatoPDF.php?id=" + idventa;
//     $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO - FACTURA`);
//     $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
//     $("#modal-imprimir-comprobante").modal("show");
//   } else if (tipo_comprobante == '03') {
//     var rutacarpeta = "../reportes/TicketFormatoPDF.php?id=" + idventa;
//     $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO - BOLETA`);
//     $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
//     $("#modal-imprimir-comprobante").modal("show");
//   } else if (tipo_comprobante == '07') {
//     var rutacarpeta = "../reportes/TicketFormatoPDF.php?id=" + idventa;
//     $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO - NOTA CREDITO`);
//     $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
//     $("#modal-imprimir-comprobante").modal("show");
//   } else if (tipo_comprobante == '12') {
//     var rutacarpeta = "../reportes/TicketFormatoPDF.php?id=" + idventa;
//     $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO - NOTA DE VENTA`);
//     $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
//     $("#modal-imprimir-comprobante").modal("show");

//   } else  {
//     // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
//     toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
//   }
// }

function ver_formato_a4_comprimido(idventa, tipo_comprobante) {
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/A4Comprimido.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/A4Comprimido.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/A4Comprimido.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
}

function ver_formato_a4_completo(idventa, tipo_comprobante) {  
  var screenHeight = window.innerHeight; 
  var height_calculado = screenHeight > 700 ? screenHeight * 0.75 : screenHeight * 0.65;
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }  
  
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  N O T A   D E   C R E D I T O                                                  ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════


function buscar_comprobante_anular() { 
  $('.charge_f_nc_serie_y_numero').html('<div class="spinner-border spinner-border-sm" role="status"></div>');
  var tipo_comprobante = $('#f_nc_tipo_comprobante').val() == '' || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
  var tipo_comprobante_hidden = $('#f_tipo_comprobante_hidden').val() == '' || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();

  if (tipo_comprobante_hidden == '07') {   
    ver_series_comprobante('#f_tipo_comprobante07');
  }  

  $.getJSON(`../ajax/facturacion.php?op=select2_comprobantes_anular`, {tipo_comprobante: tipo_comprobante}, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $("#f_nc_serie_y_numero").html(e.data);
      $("#f_nc_serie_y_numero").val(null).trigger('change');
      $('.charge_f_nc_serie_y_numero').html('');
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  V E R   E S T A D O   S U N A T                                                ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function ver_estado_documento(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' ) {
   
    $("#html-ver-estado").html(`<div class="row" >
      <div class="col-lg-12 text-center">
        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="bx-flashing">Cargando...</h4>
      </div>
    </div>`);
    $("#modal-ver-estado").modal('show');
    $.getJSON(`../ajax/facturacion.php?op=ver_estado_documento`, {idventa: idventa}, function (e, textStatus, jqXHR) {
      if (e.status == true) {
        $("#modal-ver-estado-label").html(`─ VER ESTADO: ${e.data.serie_comprobante}-${e.data.numero_comprobante}`);
        $("#html-ver-estado").html(`
          <b>Estado:</b> ${e.data.sunat_estado} <br>
          <b>Mensaje:</b> ${e.data.sunat_mensaje} <br>
          <b>Observacion:</b> ${e.data.sunat_observacion} <br>
          <b>Codigo:</b> ${e.data.sunat_code} <br>
          <b>N° ticket:</b> ${e.data.sunat_ticket} <br>
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

function reenviar_doc_a_sunat(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' ) {
    
    Swal.fire({
      title: "¿Está seguro de reenviar a SUNAT?",
      html: "Verifica que todos lo <b>campos</b> hayan sido actualizados o esten <b>conformes</b>!!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#28a745",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, Enviar!",
      preConfirm: (input) => {
        return fetch(`../ajax/facturacion.php?op=reenviar_sunat&idventa=${idventa}&tipo_comprobante=${tipo_comprobante}`).then(response => {
          //console.log(response);
          if (!response.ok) { throw new Error(response.statusText) }
          return response.json();
        }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); })        
      },
      showLoaderOnConfirm: true,
    }).then((result) => {
      if (result.isConfirmed) {
        if (result.value.status == true){        
          Swal.fire("Correcto!", "Documento actualizado correctamente.", "success");
          tabla_principal_facturacion.ajax.reload(null, false);               
        } else {
          ver_errores(result.value);
        }      
      }
    }); 
    
  } else {  
    toastr_warning('Sin respuesta!!', 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.');
  }
}

// ░▒▓════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  P R O D U C T O S                                                                ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
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
    responsive: false, 
    resize: true,    
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor    
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-9 col-xxl-10 pt-2'f><'col-md-5 col-lg-4 col-xl-3 col-xxl-2 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
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

/*
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
*/

$(document).ready(function () {
  init(); 
  filtros();
});

function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M A S   D E T A L L E S   F A C T U R A C I O N                                ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

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
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-9 col-xxl-10 pt-2'f><'col-md-5 col-lg-4 col-xl-3 col-xxl-2 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "thf buttons-reload btn btn-sm px-2 btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_ver_mas_detalle_facturacion) { tabla_ver_mas_detalle_facturacion.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-sm px-2 btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, title: 'Lista de ventas detallado', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-sm px-2 btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, title: 'Lista de ventas detallado', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-sm px-2 btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-sm px-2 btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/facturacion.php?op=listar_tabla_ver_mas_detalle_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}&filtro_estado_sunat=${filtro_estado_sunat}`,
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
      lengthMenu: "_MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      search: "",
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 9 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 9 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    initComplete: function () {
      var api = this.api();      
      $(api.table().container()).find('.dataTables_filter input').addClass('border border-primary bg-light ');// Agregar clase bg-light al input de búsqueda
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

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C U O T A S                                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

var count_cuotas = 1; 

$.validator.addMethod("FechaCuotaOrdenValido", function(value, element) {
  const $fechas = $('.fecha_cuota');
  const index = $fechas.index(element);
  // Si es la primera, no hay anterior para comparar → siempre válido
  if (index === 0) return true;
  const fechaActual = new Date(value);
  const fechaAnterior = new Date($fechas.eq(index - 1).val());
  // Si alguna de las fechas no es válida, no validar esta regla aún
  if (isNaN(fechaActual.getTime()) || isNaN(fechaAnterior.getTime())) return true;
  return fechaActual > fechaAnterior;

}, function(params, element) {
  return "Debe ser mayor que la anterior.";
});


// Regla personalizada para validar la suma de cuotas
$.validator.addMethod("CuadraCuotaConTotal", function(value, element, params) {
  const totalDocumento = parseFloat($('#f_venta_total').val()) || 0;
  let sumaCuotas = 0;

  $('.monto_cuota').each(function () {
    const val = parseFloat($(this).val());
    if (!isNaN(val)) sumaCuotas += val;
  });

  const isLast = $('.monto_cuota').last().is(element);
  if (isLast) {
    const diferencia = (totalDocumento - sumaCuotas).toFixed(2);
    $(element).data('diferencia-cuotas', diferencia);
  }
  return isLast ? (sumaCuotas === totalDocumento) : true;

}, function(params, element) {
  const diferencia = $(element).data('diferencia-cuotas') || 0; console.log(diferencia);
  
  const mensaje = parseFloat(diferencia) > 0  ? `Falta: ${diferencia}` : `Excedente de: ${Math.abs(diferencia)}`;
  return mensaje;
});

$(document).on('change', '.fecha_cuota', function () {
  $("#form-facturacion").valid();
});

$('#f_venta_cuotas').on('change', function () {  
  
  if ($(this).prop('checked')) {
    $(`.fecha_cuota`).each(function(e) { $(this).rules('add', { required: true, FechaCuotaOrdenValido: true, messages: { required: 'Campo requerido' } });  });
    $(`.monto_cuota`).each(function(e) { $(this).rules('add', { required: true, min: 0.01, CuadraCuotaConTotal: true, messages: { required: 'Campo requerido', min: "Monto minimo {0}",  } });  });  
  } else {
    // Si el checkbox no está marcado, eliminar la regla 'required'
    $('.fecha_cuota').each(function () { $(this).rules('remove'); });
    $('.monto_cuota').each(function () { $(this).rules('remove'); });
  }
});

function activar_cuotas() {
  
  if ($('#f_venta_cuotas').is(':checked')) {
    $('.div-cuotas-container').show();
  } else {
    $('.div-cuotas-container').hide();
  }
}

function agregar_cuota() {

  var id_cant = contarDivsArray('.fecha_cuota', 1);

  $('#cuotas-container').append(`<div class="col-lg-12 px-2 py-0 mb-2 html-div-cuota-${count_cuotas}">
    <div class="row">
      <div class="col-12 col-sm-12 col-md-2 col-lg-2 ">
        <div class="text-center text-nowrap">                                                                              
          <button type="button" class="btn btn-icon btn-sm btn-outline-danger rounded-pill btn-wave" onclick="eliminar_cuota(${count_cuotas});"><i class="ri-delete-bin-line"></i></button>
          <button type="button" class="btn btn-icon btn-sm btn-outline-info rounded-pill btn-wave span-numero-cuota">${id_cant+1}</button>          
        </div>
      </div>
      <!-- Fecha -->
      <div class="col-12 col-sm-12 col-md-6 col-lg-6 ">                
        <div class="form-group">       
          <input type="date" class="form-control form-control-sm fecha_cuota" name="f_fecha_cuota[${id_cant}]" min="${moment().format('YYYY-MM-DD')}">
        </div>         
      </div>
      <!-- Monto -->
      <div class="col-12 col-sm-12 col-md-4 col-lg-4 px-1">
        <div class="form-group">          
          <input type="number" class="form-control form-control-sm monto_cuota" name="f_monto_cuota[${id_cant}]">
        </div>
      </div>      
    </div>
  </div>`);
  // reglas de validación     
  $(`.fecha_cuota`).each(function(e) { $(this).rules('add', { required: true, FechaCuotaOrdenValido: true, messages: { required: 'Campo requerido' } });  });
  $(`.monto_cuota`).each(function(e) { $(this).rules('add', { required: true, min: 0.01, CuadraCuotaConTotal: true, messages: { required: 'Campo requerido', min: "Monto minimo {0}" } });  });  

  count_cuotas++;
}

function eliminar_cuota(id) {

  if (contarDivsArray('.fecha_cuota', 1) > 1) {
    $(`.html-div-cuota-${id}`).css({
      transition: 'transform 0.5s ease, opacity 0.5s ease',   // Transición suave para el cambio de tamaño y opacidad
      opacity: 0,                                            // Reduce la opacidad para desaparecer
      transform: 'scale(0.1)',                                 // Reduce el tamaño uniformemente en ambas direcciones (X y Y)
      transformOrigin: 'bottom'                               // El punto de escala es desde la parte inferior
    });

    setTimeout(function () {
      $(`.html-div-cuota-${id}`).remove();                     // Elimina el elemento después de la animación
      
      // Volvemos a renombrar los select
      renombrarInputsArray('.fecha_cuota', 'name', 'f_fecha_cuota');
      renombrarInputsArray('.monto_cuota', 'name', 'f_monto_cuota');
      renombrarDivArray('.span-numero-cuota', '');

      // reglas de validación     
      $(`.fecha_cuota`).each(function(e) { $(this).rules('add', { required: true, FechaCuotaOrdenValido: true, messages: { required: 'Campo requerido' } });  });
      $(`.monto_cuota`).each(function(e) { $(this).rules('add', { required: true, min: 0.01, CuadraCuotaConTotal: true, messages: { required: 'Campo requerido', min: "Monto minimo {0}" } });  });  
      
    }, 500);    // Tiempo de espera igual al de la animación
  } else {
    toastr_warning('🔔 Requiere cuota!!', 'Debes tener almenos una cuota, de lo contrario desactiva esta opcion.');
  } 
}

function mostrar_ventas_cuotas(id, estado_cuota, nro_doc) {
  if (estado_cuota == 'NO') {
    toastr_warning('No tiene Cuotas!', 'Este documento no tiene cuotas porque fue pagado al contado.');
  } else {
    $('.title-ver-cuotas').html(`Doc: ${nro_doc}`);    
    $('#html-detalle-cuotas-ventas').html(`<div class="row"  style="display: none;">
                          <div class="col-lg-12 text-center">
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div>`);
    $('#modal-ver-cuotas').modal('show');
    $.get(`../ajax/facturacion.php?op=mostrar_cuotas&id_venta=${id}`,     function (e, textStatus, jqXHR) {
      $('#html-detalle-cuotas-ventas').html(e);
      $('[data-bs-toggle="tooltip"]').tooltip({ html: true });
    });
  }
  
}

function actualizar_estado_credito(id) {
  $.getJSON(`../ajax/facturacion.php?op=actualizar_estado_credito`, {id_venta: id},    function (e, textStatus, jqXHR) {
    if (e.status == true) {
      sw_success('¡Actualizado!', 'El estado de crédito del documento se actualizó correctamente.', false);
      if (tabla_principal_facturacion) { tabla_principal_facturacion.ajax.reload(null, false); }
    } else {
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

function actualizar_estado_cuota(idventa_cuotas, id_venta) {
  $.getJSON(`../ajax/facturacion.php?op=actualizar_estado_cuota`, {idventa_cuotas: idventa_cuotas, id_venta: id_venta, },    function (e, textStatus, jqXHR) {
    if (e.status == true) {
      sw_success('¡Actualizado!', 'El estado de crédito del documento se actualizó correctamente.', false);
      if (tabla_principal_facturacion) { tabla_principal_facturacion.ajax.reload(null, false); }
      $('#modal-ver-cuotas').modal('hide');
    } else {
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         B U S Q U E D A   D E   P R O D U C T O S                                                        ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

$(document).ready(function () {
  $('#search_producto').on('keyup', function () {
    let query = $(this).val();
    

    if (query.length >= 2) {
      $.getJSON(`../ajax/facturacion.php?op=listar_producto_x_nombre`, { search: query }, function (e, textStatus, jqXHR) {
        
        let $resultsList = $('#searchResults');
        $resultsList.empty();

        if (e.data.length > 0) {
          e.data.forEach(function (val, key) {
            $resultsList.append(`<li class="list-group-item hover-text-success list-group-item-action bg-light cursor-pointer py-1" onclick="agregarDetalleComprobante(${val.idproducto_presentacion},null, false)">
              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/${val.imagen}" alt="" > </span></div>        
                <div> 
                  <span class="fs-12">${val.nombre_presentacion}</span> | <span class="fs-12">${val.nombre_producto}</span> <br>
                  <span class="fs-10">Cod: ${val.codigo_alterno} </span> |
                  <span class="fs-10">Stock: ${parseFloat(val.stock_presentacion_entero) || 0} </span> | <span class="fs-10">Venta: S/. ${ formato_miles(val.precio_venta)}</span>
                </div>
              </div>
            </li>`);
          });
        } else {
          $resultsList.append(`<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`);
        }

        $resultsList.show();
      });
    } else {
      $('#searchResults').hide();
    }
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#searchInput, #searchResults').length) {
      $('#searchResults').hide();
    }
  });
});


// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

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

  //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  
  $("#form-agregar-nuevo-cliente").validate({
    ignore: "",
    rules: {           
      cli_tipo_documento:           { required: true, minlength: 1, maxlength: 2, },       
      cli_numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      cli_nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      cli_apellidos_nombrecomercial:{ required: true, minlength: 4, maxlength: 200, },       
      cli_correo:    			          { minlength: 4, maxlength: 100, },       
      cli_celular:    			        { minlength: 8, maxlength: 9, },
      cli_direccion:    			      { minlength: 4, maxlength: 200, },       
      cli_direccion_referencia:     { minlength: 4, maxlength: 200, }, 	     
    },
    messages: {     
      cli_tipo_documento:    			  { required: "Campo requerido", },
      cli_numero_documento:    			{ required: "Campo requerido", }, 
      cli_nombre_razonsocial:    		{ required: "Campo requerido", }, 
      cli_apellidos_nombrecomercial:{ required: "Campo requerido", }, 
      cli_correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      cli_celular:    			        { minlength: "Mínimo {0} caracteres.", }, 
      cli_direccion:    			      { minlength: 4, maxlength: 200, },       
      cli_direccion_referencia:     { minlength: 4, maxlength: 200, },      
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
      guardar_editar_nuevo_cliente(e);      
    },
  });
 

  $("#form-agregar-producto").validate({
    ignore: "",
    rules: {           
      codigo:         { required: true, minlength: 2, maxlength: 20, },       
      categaria:    	{ required: true },       
      u_medida:    		{ required: true },       
      marca:    			{ required: true },       
      nombre:    			{ required: true, minlength: 2, maxlength: 20,  },       
      descripcion:    { required: true, minlength: 2, maxlength: 500, },       
      stock:          { required: true, min: 0,  },       
      stock_min:      { required: true, min: 0,  }, 
      precio_v:       { required: true, min: 0,  },       
      precio_c:       { required: true, min: 0,  },	
    },
    messages: {     
      cogido:    			{ required: "Campo requerido", },
      categaria:    	{ required: "Seleccione una opción", },
      u_medida:    		{ required: "Seleccione una opción", },
      marca:    			{ required: "Seleccione una opción", },
      nombre:    			{ required: "Campo requerido", }, 
      descripcion:    { required: "Campo requerido", },       
      stock:          { required: "Campo requerido", },       
      stock_min:      { required: "Campo requerido", }, 
      precio_v:       { required: "Campo requerido", },       
      precio_c:       { required: "Campo requerido", },	
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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_producto(e);      
    },
  });

});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_fecha_i        = $("#filtro_fecha_i").val();
  var filtro_fecha_f        = $("#filtro_fecha_f").val();  
  var filtro_cliente        = $("#filtro_cliente").select2('val');
  var filtro_tipo_persona   = $("#filtro_tipo_persona").select2('val');
  var filtro_comprobante    = $("#filtro_comprobante").select2('val');
  var filtro_metodo_pago    = $("#filtro_metodo_pago").select2('val');
  var filtro_centro_poblado = $("#filtro_centro_poblado").select2('val');
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_cliente     = ' ─ ' + $('#filtro_cliente').find(':selected').text();
  var nombre_filtro_comprobante = ' ─ ' + $('#filtro_comprobante').find(':selected').text();

  // filtro de fechas
  if (filtro_fecha_i        == '' || filtro_fecha_i         == 0 || filtro_fecha_i        == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  if (filtro_fecha_f        == '' || filtro_fecha_f         == 0 || filtro_fecha_f        == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }
  // filtro de cliente
  if (filtro_cliente        == '' || filtro_cliente         == 0 || filtro_cliente        == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }
  // filtro de filtro_tipo_persona
  if (filtro_tipo_persona   == '' || filtro_tipo_persona    == 0 || filtro_tipo_persona   == null) { filtro_tipo_persona = "";  }
  // filtro de comprobante
  if (filtro_comprobante    == '' || filtro_comprobante     == 0 || filtro_comprobante    == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }
  // filtro de metodo pago
  if (filtro_metodo_pago    == '' || filtro_metodo_pago     == 0 || filtro_metodo_pago    == null) { filtro_metodo_pago = ""; nombre_filtro_metodo_pago = ""; }
  // filtro de filtro_centro_poblado
  if (filtro_centro_poblado == '' || filtro_centro_poblado  == 0 || filtro_centro_poblado == null) { filtro_centro_poblado = "";  }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ${nombre_filtro_cliente}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_tipo_persona, filtro_comprobante, filtro_metodo_pago, filtro_centro_poblado, filtro_estado_sunat);
}

function filtrar_solo_estado_sunat(estado, etiqueta) {  
  $(".otros-filtros").find("li>a").removeClass("active");
  $(".otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat = estado; filtros();
}

function reload_f_idpersona_cliente(){ lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', null, '.charge_f_idpersona_cliente'); }
function reload_f_nc_serie_y_numero(){ buscar_comprobante_anular() }
function reload_f_nc_motivo_anulacion(){ lista_select2("../ajax/facturacion.php?op=select2_codigo_x_anulacion_comprobante", '#f_nc_motivo_anulacion', '01', '.charge_f_nc_motivo_anulacion'); }
function reload_f_metodo_pago(id){ lista_select2("../ajax/facturacion.php?op=select2_banco", `#f_metodo_pago_${id}`, null, `charge_f_metodo_pago_${id}`);   }

function reload_filtro_fecha_i(){ $('#filtro_fecha_i').val("").trigger("change") } 
function reload_filtro_fecha_f(){ $('#filtro_fecha_f').val("").trigger("change") } 
function reload_filtro_cliente(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente'); } 
function reload_filtro_comprobante(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante'); }
function reload_filtro_metodo_pago(){ lista_select2("../ajax/facturacion.php?op=select2_banco", '#filtro_metodo_pago', null, '.charge_filtro_metodo_pago');  }
function reload_filtro_centro_poblado_venta(){ lista_select2("../ajax/ajax_general.php?op=select2_centro_poblado_venta", '#filtro_centro_poblado', null, '.charge_filtro_centro_poblado');  }

function reload_filtro_md_fecha_i(){ $('#filtro_md_fecha_i').val("").trigger("change") } 
function reload_filtro_md_fecha_f(){ $('#filtro_md_fecha_f').val("").trigger("change") } 
function reload_filtro_md_cliente(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente'); } 
function reload_filtro_md_comprobante(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante'); }


function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
}

function ver_img_pefil(id_cliente) {
  $('#modal-ver-imgenes').modal('show');
  $(".html_modal_ver_imgenes").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/facturacion.php?op=mostrar_cliente", { idcliente: id_cliente },  function (e, status) {
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

function ver_comprobante_pago(id_venta, nombre_doc) {
  $('#modal-ver-metodo-pago').modal('show');
  $("#html-ver-metodo-pago").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);
  $(".title-ver-metodo-pago").html(`Doc: ${nombre_doc}`)
  $.getJSON("../ajax/facturacion.php?op=mostrar_metodo_pago", { idventa: id_venta },  function (e, status) {
    //console.log(e);
    $("#html-ver-metodo-pago").html('')
    if (e.status == true) {

      e.data.forEach((val, key) => {
        var img_mp = DocExist(`assets/modulo/facturacion/ticket/${val.comprobante_v2}`) == 200 ? val.comprobante_v2 : 'img_mp.png'  ;
        
        const isPDF = img_mp.endsWith('.pdf'); // Validar si es un archivo PDF
        const href = `../assets/modulo/facturacion/ticket/${img_mp}`;
        const galleryType = isPDF ? 'iframe' : 'image'; // Tipo de contenido según sea PDF o imagen

        $('#html-ver-metodo-pago').append(`<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12 text-center mb-3">
          <center>
          <a href="${href}" class="glightbox imagen-metodo-pago" data-gallery="gallery1" >
            ${isPDF 
              ? '<img src="../assets/images/company-logos/logo-pdf.jpg" class="card mb-2" alt="PDF">' 
              : `<img src="${href}" class="card mb-2" alt="image">`}
          </a> 
          </center>
          <span class="fs-11">${val.metodo_pago}</span> | <span class="fs-11">S/. ${formato_miles(val.monto)}</span> <br>
          <a class="badge bg-outline-secondary custom-badge fs-11 d-inline-flex align-items-center cursor-pointer" 
            download="${removeCaracterEspecial(nombre_doc)}" href="${href}">
            <i class="ti ti-cloud-download fs-13 me-1"></i>${ val.comprobante_size_bytes == null || val.comprobante_size_bytes == '' ? '0 Mb' : formatFileSize( parseFloat(val.comprobante_size_bytes))}</a>           
        </div>`);
               
      }); 

      var lightboxVideo = GLightbox({
          selector: '.glightbox',
          openEffect: 'zoom',    // Efecto de zoom al abrir
          closeEffect: 'fade',   // Efecto al cerrar
          zoomable: true         // Opcional: Si deseas imágenes ampliables
      });
      
      lightboxVideo.on('slide_changed', ({ current }) => {
        const { slideNode } = current;
      
        // Encontrar la imagen dentro de la diapositiva actual
        const image = slideNode.querySelector('img');
      
        if (image) {
          let scale = 1; // Escala inicial
      
          // Agregar evento de zoom con la rueda del mouse
          image.addEventListener('wheel', (e) => {
            e.preventDefault(); // Prevenir el scroll de la página
      
            // Calcular las coordenadas relativas del mouse en la imagen
            const rect = image.getBoundingClientRect();
            const offsetX = e.clientX - rect.left; // Coordenada X relativa al inicio de la imagen
            const offsetY = e.clientY - rect.top;  // Coordenada Y relativa al inicio de la imagen
            const percentX = (offsetX / rect.width) * 100; // Porcentaje X dentro de la imagen
            const percentY = (offsetY / rect.height) * 100; // Porcentaje Y dentro de la imagen
      
            // Calcular el nuevo nivel de zoom
            const zoomStep = 0.1;
            scale += e.deltaY < 0 ? zoomStep : -zoomStep; // Zoom in con rueda arriba, zoom out con rueda abajo
            scale = Math.max(1, Math.min(scale, 3)); // Limitar el zoom entre 1x y 3x
      
            // Establecer el punto de origen del zoom en la posición del mouse
            image.style.transformOrigin = `${percentX}% ${percentY}%`;
      
            // Aplicar el zoom a la imagen
            image.style.transform = `scale(${scale})`;
          });
        }
      });

      // Deshabilitar el cierre de GLightbox cuando está en zoom
      document.addEventListener('click', (e) => {
        if (e.target.closest('.disable-close')) {
          e.stopPropagation(); // Prevenir el cierre
        }
      });

    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
  
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
    allowMultiple: false, // Permitir subir múltiples archivos
    maxFiles: 1, // Máximo número de archivos permitidos
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
  //filePondInstances.push(file_pond_mp_comprobante); // Guarda la instancia en el arreglo
  // Ensure mediumZoom is available before using it
  // document.addEventListener("DOMContentLoaded", function() {
  //   file_pond_mp_comprobante.on('addfile', (error, file) => {
  //     if (!error) {
  //       setTimeout(() => {
  //         mediumZoom('.filepond--image-preview');
  //       }, 100); // Delay to ensure image is rendered
  //     }
  //   });
  // });

})();

var hola = [ 'hola1', 'hola2', 'hola3', 'hola4','hola5']


/* ===========  PLUGIN: COLUMNAS REDIMENCIONBLES  =========== */
function makeColsResizable(table) {
  const ths = table.querySelectorAll('th');
  ths.forEach((th, i) => {
    if (i === ths.length - 1) return;          // opcional: no poner grip en la última
    const grip = document.createElement('div');
    grip.className = 'grip';
    th.appendChild(grip);

    let startX, startWidth, nextWidth, nextTh;

    grip.addEventListener('pointerdown', e => {
      e.preventDefault();
      startX = e.clientX;
      startWidth = th.offsetWidth;
      nextTh = th.nextElementSibling;
      nextWidth = nextTh ? nextTh.offsetWidth : null;

      /* enganchar eventos globales mientras arrastro */
      document.addEventListener('pointermove', onMove);
      document.addEventListener('pointerup', stopDrag);
    });

    function onMove(e) {
      const dx = e.clientX - startX;
      /* si existe la columna de la derecha le quitamos ancho
         para que la tabla no crezca indefinidamente */
      if (nextTh) {
        th.style.width = (startWidth + dx) + 'px';
        nextTh.style.width = (nextWidth - dx) + 'px';
      } else {
        th.style.width = (startWidth + dx) + 'px';
      }
    }
    function stopDrag() {
      document.removeEventListener('pointermove', onMove);
      document.removeEventListener('pointerup', stopDrag);
    }
  });

  /* Opcional: fijar anchos iniciales explícitamente
     (evita que el navegador los cambie después).   */
  ths.forEach(th => th.style.width = th.offsetWidth + 'px');
}

