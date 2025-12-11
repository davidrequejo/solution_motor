var tabla;

// var select_idbanco = new Choices('#idbanco', { allowHTML: true,  removeItemButton: true, });

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
const choice_tipo_documento = new Choices('#tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

function init() {

  listar_tabla();
  
  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-gasto").submit(); } });
  $("#guardar_registro_proveedor").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-proveedor").submit(); } });
  $("#guardar_registro_categoria").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-categoria").submit(); }  });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/otros_gastos.php?op=listar_proveedor", '#idproveedor', null);  

  lista_select2("../ajax/ajax_general.php?op=listar_ie_categoria", '#idotros_gastos_categoria', null, '.charge_idotros_gastos_categoria');  

  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);
  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_tipo_documento", choice_tipo_documento, null);  
  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_banco", choice_idbanco, null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  

  $("#idotros_gastos_categoria").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#tipo_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idproveedor").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', '300px', true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form() {
  $("#idotros_gastos").val("");

  $("#idproveedor").val(2).trigger("change"); 
  $("#idcaja").val("");
  $("#tipo_gasto_modulo").val("GASTOS");

  $("#descr_gastos").val("");
  $("#idotros_gastos_categoria").val("").trigger("change");  
  $("#tipo_comprobante").val("NINGUNO").trigger("change");  
  $("#serie_comprobante").val("");
  $("#fecha").val("");
  $("#mes").val("");
  $("#precio_sin_igv").val("");
  $("#igv").val("");
  $("#precio_con_igv").val("");
  $("#descr_comprobante").val("");
  
  //limpiamos imagen
  doc1_eliminar();
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".form-select").removeClass('is-valid');
  $(".form-select").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function show_hide_form(flag) {
  if (flag == 1) {
    $("#div-tabla").show();
    $("#div-formulario").hide();

    $(".btn-agregar").show();
    $(".btn-guardar").hide();
    $(".btn-cancelar").hide();

  } else if (flag == 2) {
    $("#div-tabla").hide();
    $("#div-formulario").show();

    $(".btn-agregar").hide();
    $(".btn-guardar").show();
    $(".btn-cancelar").show();
  }
}

function guardar_editar(e) {
  var formData = new FormData($("#formulario-gasto")[0]);
  $.ajax({
    url: "../ajax/otros_gastos.php?op=guardar_editar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");
          tabla.ajax.reload(null, false);
          show_hide_form(1); limpiar_form();
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_gasto").html('Guardar Cambios').removeClass('disabled send-data');
    },
    beforeSend: function () {
      $("#guardar_registro_gasto").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_gasto").css({ width: "0%", });
      $("#barra_progress_gasto div").text("0%");
      $("#barra_progress_gasto_div").show();
    },
    complete: function () {
      $("#barra_progress_gasto").css({ width: "0%", });
      $("#barra_progress_gasto div").text("0%");
      $("#barra_progress_gasto_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

function listar_tabla() {
  tabla = $('#tabla-gastos').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla) { tabla.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,12,13,9,10,11,4,14,15,16,17,6,7], }, title:'', text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0,2,12,13,9,10,11,4,14,15,16,17,6,7], }, title: 'Lista de gasto', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      { extend: 'pdf', exportOptions: { columns: [0,2,12,13,9,10,11,4,14,15,16,17,6,7], }, title: 'Lista de gasto', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    colReorder: true, // Activar el reordenamiento de columnas
    "ajax": {
      url: '../ajax/otros_gastos.php?op=listar_tabla',
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
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 6 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 6 ).footer() ).html( `S/ ${formato_miles(total1)}` );       
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]],//Ordenar (columna,orden)
    columnDefs: [
      { targets: [ 9, 10, 11, 12, 13, 14, 15, 16, 17], visible: false, searchable: false, }, 
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

function eliminar_gasto(idotros_gastos, nombre_razonsocial) {

  crud_eliminar_papelera(
    "../ajax/otros_gastos.php?op=desactivar",
    "../ajax/otros_gastos.php?op=eliminar",
    idotros_gastos,
    "!Elija una opción¡",
    `<b class="text-danger"><del> ${nombre_razonsocial} </del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`,
    function () { sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado.") },
    function () { sw_success('Eliminado!', 'Tu registro ha sido Eliminado.') },
    function () { tabla.ajax.reload(null, false); },
    false,
    false,
    false,
    false
  );
}

//liStamos datos para EDITAR
function mostrar_editar(idotros_gastos) {
  show_hide_form(2);
  $('#cargando-1-fomulario').hide();	
  $('#cargando-2-fomulario').show();
  limpiar_form();
  $.post("../ajax/otros_gastos.php?op=mostrar_editar", { idotros_gastos: idotros_gastos }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      $("#idotros_gastos").val(e.data.idingreso_egreso_interno);

      $("#idproveedor").val(e.data.idpersona).trigger("change");
      $("#idotros_gastos_categoria").val(e.data.idingreso_egreso_categoria).trigger("change");
      $("#idcaja").val(e.data.idcaja);
      $("#tipo_gasto_modulo").val(e.data.tipo_gasto_modulo);
      
      $("#tipo_comprobante").val(e.data.tipo_comprobante);
      $("#serie_comprobante").val(e.data.serie_comprobante);
      $("#fecha").val(e.data.fecha_comprobante);
      $("#mes").val(e.data.periodo_gasto);
      
      $("#precio_sin_igv").val(e.data.precio_sin_igv);
      $("#igv").val(e.data.precio_igv);
      $("#val_igv").val(e.data.val_igv);
      $("#precio_con_igv").val(e.data.precio_con_igv);
      $("#descr_comprobante").val(e.data.descripcion_comprobante);      

      // ------------ IMAGEN -----------
      if (e.data.comprobante == "" || e.data.comprobante == null) { } else {
        $("#doc_old_1").val(e.data.comprobante);
        $("#doc1_nombre").html(`<div class="row"> <div class="col-md-12"><i>imagen.${extrae_extencion(e.data.comprobante)}</i></div></div>`);
        // cargamos la imagen adecuada par el archivo
        $("#doc1_ver").html(doc_view_extencion(e.data.comprobante, 'assets/modulo/otros_gastos', '50%', '110'));   //ruta imagen          
      }
      $('#cargando-1-fomulario').show();	
      $('#cargando-2-fomulario').hide();
      
    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

//listamos los datos para MOSTRAR TODO
function mostrar_detalles_gasto(idotros_gastos) {
  $("#modal-ver-detalle").modal('show');
  $("#html-detalle-comprobante").html('');
  $.post("../ajax/otros_gastos.php?op=mostrar_detalle_gasto", { idotros_gastos: idotros_gastos }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
     
      $("#html-detalle-compra").html(e.data);
      $("#html-detalle-comprobante").html(doc_view_download_expand(e.comprobante, 'assets/modulo/otros_gastos/', e.nombre_doc, '100%', '400px'));
      
    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

function mostrar_comprobante(idotros_gastos) {
  $('#modal-ver-comprobante').modal('show');
  $("#comprobante-container").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/otros_gastos.php?op=mostrar_editar", { idotros_gastos: idotros_gastos },  function (e, status) {

    e = JSON.parse(e);
    if (e.status == true) {
      if (e.data.comprobante == "" || e.data.comprobante == null) { } else {
        // $("#comprobante-container").html(doc_view_extencion(e.data.comprobante, 'assets/modulo/otros_gastos', '100%', '100%'));
        var nombre_comprobante = `${e.data.tipo_comprobante} ${e.data.serie_comprobante}`;
        $('.title-modal-comprobante').html(nombre_comprobante);
        $("#comprobante-container").html(doc_view_download_expand(e.data.comprobante, 'assets/modulo/otros_gastos',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
}

// MOSTRAR LISTA
$('#tipo_comprobante').change(function () {
  $('.proveedor').toggle($('#tipo_comprobante').val() === 'FACTURA' || $('#tipo_comprobante').val() === 'NOTA_DE_VENTA');
  $('#formulario-gasto').valid();  
  comprob_factura();
  validando_igv();
});

//segun tipo de comprobante
function comprob_factura() {

  var precio_con_igv = $("#precio_con_igv").val(); 
  
  if ($("#tipo_comprobante").select2('val') == "" || $("#tipo_comprobante").select2('val') == null) {

    $(".nro_comprobante").html("Núm. Comprobante");

    $("#val_igv").val(""); $("#tipo_gravada").val(""); 

    if (precio_con_igv == null || precio_con_igv == "") {
      $("#precio_sin_igv").val(0);
      $("#igv").val(0);    
    } else {
      $("#precio_sin_igv").val(parseFloat(precio_con_igv).toFixed(2));
      $("#igv").val(0);    
    }   

  } else if ($("#tipo_comprobante").select2('val') == "NINGUNO") {     

    $(".nro_comprobante").html("Núm. de Operación");
    $("#val_igv").prop("readonly",true);

    if (precio_con_igv == null || precio_con_igv == "") {
      $("#precio_sin_igv").val(0);
      $("#igv").val(0);
      
      $("#val_igv").val("0"); 
      $("#tipo_gravada").val("NO GRAVADA");  

    } else {
      $("#precio_sin_igv").val(parseFloat(precio_con_igv).toFixed(2));
      $("#igv").val(0); 

      $("#val_igv").val("0"); 
      $("#tipo_gravada").val("NO GRAVADA"); 

    }   

  } else if ($("#tipo_comprobante").select2("val") == "FACTURA") {          

    $(".nro_comprobante").html("Núm. Comprobante");
    $(".div_ruc").show(); $(".div_razon_social").show();      
    calculandototales_fact();     
  
  } else if ($("#tipo_comprobante").select2("val") == "BOLETA") {       

    
    $("#val_igv").prop("readonly",true);
    $(".nro_comprobante").html("Núm. Comprobante");

    $(".div_ruc").show(); $(".div_razon_social").show();
    
    if (precio_con_igv == null || precio_con_igv == "") {
      $("#precio_sin_igv").val(0);
      $("#igv").val(0); 
      $("#val_igv").val("0");   
    } else {
              
      $("#precio_sin_igv").val("");
      $("#igv").val("");

      $("#precio_sin_igv").val(parseFloat(precio_con_igv).toFixed(2));
      $("#igv").val(0); 
      
      $("#val_igv").val("0"); 
      $("#tipo_gravada").val("NO GRAVADA"); 
    } 
      
  } else {
    $("#val_igv").prop("readonly",true);    
    $(".nro_comprobante").html("Núm. Comprobante");
    $(".div_ruc").hide(); $(".div_razon_social").hide();
    $("#ruc").val(""); $("#razon_social").val("");

    if (precio_con_igv == null || precio_con_igv == "") {
      
      $("#precio_sin_igv").val(0);
      $("#igv").val(0);

      $("#val_igv").val("0"); 
      $("#tipo_gravada").val("NO GRAVADA");  

    } else {

      $("#precio_sin_igv").val(parseFloat(precio_con_igv).toFixed(2));
      $("#igv").val(0); 

      $("#val_igv").val("0"); 
      $("#tipo_gravada").val("NO GRAVADA");  

    } 
    
  }   
}

function validando_igv() {
  if ($("#tipo_comprobante").select2("val") == "FACTURA") {
    $("#val_igv").prop("readonly",false);
    $("#val_igv").val(18); 
  }else {
    $("#val_igv").val(0); 
  }  
}

function calculandototales_fact() {
  //----------------
  $("#tipo_gravada").val("GRAVADA");         
  $(".nro_comprobante").html("Núm. Comprobante");
  var precio_con_igv = $("#precio_con_igv").val();
  var val_igv = $('#val_igv').val();

  if (precio_con_igv == null || precio_con_igv == "") {

    $("#precio_sin_igv").val(0);
    $("#igv").val(0); 

  } else {
 
    var precio_sin_igv = 0;
    var igv = 0;

    if (val_igv == null || val_igv == "") {

      $("#precio_sin_igv").val(parseFloat(precio_con_igv));
      $("#igv").val(0);

    }else{

      $("precio_sin_igv").val("");
      $("#igv").val("");

      precio_sin_igv = quitar_igv_del_precio(precio_con_igv, val_igv, 'entero');
      igv = precio_con_igv - precio_sin_igv;

      $("#precio_sin_igv").val(parseFloat(precio_sin_igv).toFixed(2));
      $("#igv").val(parseFloat(igv).toFixed(2));

    }
  }  
}

function quitar_igv_del_precio(precio , igv, tipo ) {
  console.log(precio , igv, tipo);
  var precio_sin_igv = 0;

  switch (tipo) {
    case 'decimal':

      if (parseFloat(precio) != NaN && igv > 0 && igv <= 1 ) {
        precio_sin_igv = ( parseFloat(precio) * 100 ) / ( ( parseFloat(igv) * 100 ) + 100 )
      }else{
        precio_sin_igv = precio;
      }
    break;

    case 'entero':

      if (parseFloat(precio) != NaN && igv > 0 && igv <= 100 ) {
        precio_sin_igv = ( parseFloat(precio) * 100 ) / ( parseFloat(igv)  + 100 )
      }else{
        precio_sin_igv = precio;
      }
    break;
  
    default:
      $(".val_igv").html('IGV (0%)');
      toastr.success('No has difinido un tipo de calculo de IGV.')
    break;
  } 
  
  return precio_sin_igv; 
}

// .....:::::::::::::::::::::::::::::::::::::::::: P R O V E E D O R :::::::::::::::::::::::::::::::::::::::::::..
function modal_add_trabajador() {
  $("#modal-agregar-proveedor").modal('show');
}

function llenar_dep_prov_ubig(input) {

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 

  // if ($(input).select2("val") == null || $(input).select2("val") == '') { 
  if ($('#distrito').val() == null || $('#distrito').val() == '') { 
    $("#departamento").val(""); 
    $("#provincia").val(""); 
    $("#ubigeo").val(""); 

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    // var iddistrito =  $(input).select2('data')[0].element.attributes.iddistrito.value;
    var iddistrito = choice_distrito.getValue().customProperties.idubigeo_distrito;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {   
      e = JSON.parse(e); console.log(e);
      if (e.status == true) {
        $("#departamento").val(e.data.departamento); 
        $("#provincia").val(e.data.provincia); 
        $("#ubigeo").val(e.data.ubigeo_inei);       
      } else {
        ver_errores(e);
      }
      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      $("#form-agregar-proveedor").valid();
      
    });
  }  
}

function limpiar_proveedor() {

	$('#idpersona').val('');
  $('#tipo_persona_sunat').val('NATURAL');
  $('#idtipo_persona').val('4');

  choice_tipo_documento.setChoiceByValue('1');
  $('#numero_documento').val('');
  $('#nombre_razonsocial').val('');
  $('#apellidos_nombrecomercial').val('');
  $('#correo').val('');
  $('#celular').val('');
  
  $('#direccion').val('');
  choice_distrito.setChoiceByValue('TOCACHE').passedElement.element.dispatchEvent(new Event('change'));
  $('#departamento').val('');
  $('#provincia').val('');
  $('#ubigeo').val('');
  choice_idbanco.setChoiceByValue('1');
  $('#cuenta_bancaria').val('');
  $('#cci').val(''); 

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_proveedor(e) {

	var formData = new FormData($("#form-agregar-proveedor")[0]);

	$.ajax({
		url: "../ajax/proveedores.php?op=guardar_editar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (e) {
			try {
				e = JSON.parse(e);  //console.log(e); 
        if (e.status == true) {	
					sw_success('Exito', 'proveedor guardado correctamente.');
          $("#modal-agregar-proveedor").modal('hide'); limpiar_proveedor();
          lista_select2("../ajax/otros_gastos.php?op=listar_proveedor", '#idproveedor', e.data, '.charge_idproveedor');  
				} else {
					ver_errores(e);
				}				
			} catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!",'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }      
      $("#guardar_registro_trabajador").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar').removeClass('disabled send-data');
		},
		xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					/*console.log(percentComplete + '%');*/
					$("#barra_progress_proveedor").css({ "width": percentComplete + '%' });
					$("#barra_progress_proveedor div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$("#guardar_registro_trabajador").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
			$("#barra_progress_proveedor").css({ width: "0%", });
			$("#barra_progress_proveedor div").text("0%");
      $("#barra_progress_proveedor_div").show();
		},
		complete: function () {
			$("#barra_progress_proveedor").css({ width: "0%", });
			$("#barra_progress_proveedor div").text("0%");
      $("#barra_progress_proveedor_div").hide();
		},
		error: function (jqXhr, ajaxOptions, thrownError) {
			ver_errores(jqXhr);
		}
	});
}

$('#tipo_documento').change(function() {
  var tipo = $(this).val();

  if (tipo !== null && tipo !== '' && tipo == '6') {
    $('.label-nom-raz').html('Razón Social <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Nombre comercial <sup class="text-danger">*</sup>');
  }else{
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
  }

});

function cambiarImagen() {
	var imagenInput = document.getElementById('imagen');
	imagenInput.click();
}

function removerImagen() {
	$("#imagenmuestra").attr("src", "../assets/proveedor/no-proveedor.png");
	$("#imagen").val("");
  $("#imagenactual").val("");
}

document.addEventListener('DOMContentLoaded', function () {
	var imagenMuestra = document.getElementById('imagenmuestra');
	var imagenInput = document.getElementById('imagen');

	imagenInput.addEventListener('change', function () {
		if (imagenInput.files && imagenInput.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) { imagenMuestra.src = e.target.result;	}
			reader.readAsDataURL(imagenInput.files[0]);
		}
	});
});

// .....::::::::::::::::::::::::::::::::::::::::::  CATEGORIA :::::::::::::::::::::::::::::::::::::::::::..
function modal_add_categoria() {
  $("#modal-agregar-categoria-otros-gastos").modal('show');
}

function limpiar_form_categoria_otros_gastos() {

	$('#idotros_gastos_categoria').val('');
  $('#nombre_ie_cat').val('');
  $('#descr_ie_cat').val('');

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_categoria(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-categoria-otros-gastos")[0]);
 
  $.ajax({
    url: "../ajax/ingreso_egreso_categoria.php?op=guardar_editar_ie_categoria",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        lista_select2("../ajax/ajax_general.php?op=listar_ie_categoria", '#idotros_gastos_categoria', e.data, '.charge_idotros_gastos_categoria');
        Swal.fire("Correcto!", "Categoría registrado correctamente.", "success");	        
				limpiar_form_categoria_otros_gastos();
        $("#modal-agregar-categoria-otros-gastos").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_categoria_otros_gastos").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
    }
  });
}

$(function () {
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


  // .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  C A T E G O R I A  G A S T O    :::::::::::::::::::::::::::::::::::::::..
  $("#form-agregar-categoria-otros-gastos").validate({
    rules: {
      nombre_ie_cat: { required: true,  minlength: 4,  maxlength: 100,  } ,
    },
    messages: {
      nombre_ie_cat: {  required: "Campo requerido.", },
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
      guardar_editar_categoria(e);      
    },

  });


  // .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  T R A B A J A D O R    :::::::::::::::::::::::::::::::::::::::..

  $('#idotros_gastos_categoria').on('change', function() { $(this).trigger('blur'); });
  $('#idproveedor').on('change', function() { $(this).trigger('blur'); });
  $('#tipo_comprobante').on('change', function() { $(this).trigger('blur'); });
  $("#formulario-gasto").validate({
    rules: {     
      idproveedor:              { required: true },
      idotros_gastos_categoria: { required: true },
      fecha:            { required: true },
      mes:            { required: true },
      precio_con_igv:   { required: true, min: 1, },
      val_igv:          { required: true, minlength: 1, maxlength: 100 },
      val_igv:          { required: true, minlength: 1, maxlength: 100 },
      serie_comprobante:{
        required: function (element) {
          return $("#tipo_comprobante").val() !== "NINGUNO";
        }
      },
      descr_comprobante: {minlength: 4,}
    },

    messages: {

     
      serie_comprobante:{ required: "Campo requerido" },
      fecha:            { required: "Campo requerido" },
      mes:            { required: "Campo requerido" },
      precio_con_igv:      { required: "Campo requerido" },
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
      window.scroll({ top: document.body.scrollHeight, left: document.body.scrollHeight, behavior: "smooth", });
      guardar_editar(e);
    },
  });
  $('#idotros_gastos_categoria').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#idproveedor').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#tipo_comprobante').rules('add', { required: true, messages: {  required: "Campo requerido" } });
});

$(document).ready(function () {
  init();
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function mayus(e) {
  e.value = e.value.toUpperCase();
}

function reload_idproveedor(){ lista_select2("../ajax/otros_gastos.php?op=listar_proveedor", '#idproveedor', null, '.charge_idproveedor'); }
function reload_idotros_gastos_categoria(){ lista_select2("../ajax/ajax_general.php?op=listar_ie_categoria", '#idotros_gastos_categoria', null, '.charge_idotros_gastos_categoria'); }
  
function ver_img(img, nombre) {
	$(".title-modal-img").html(`-${nombre}`);
  $('#modal-ver-img').modal("show");
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/persona/perfil', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}