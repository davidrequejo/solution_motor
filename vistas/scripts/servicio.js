var tabla_servicios;

function init(){

  listar_tabla();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-servicio").submit(); }  });

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════ 
  $("#idcategoria").select2({  theme: "bootstrap4", placeholder: "Seleccione", });

}

//  :::::::::::::::: S E R V I C I O :::::::::::::::: 

function limpiar_form_servicio(){

	$('#idproducto').val('');
  
	$('#codigo').val('');
	$('#codigo_alterno').val('');
	$('#stock').val('1000');
	$('#nombre').val('');
	$('#descripcion').val('');
	$('#precio_v').val('');

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/servicios/no-servicio.png");
  $("#imagenmuestra").attr("src", "../assets/modulo/servicios/no-servicio.png").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/servicios/no-servicio.png';
  }

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function show_hide_form(flag) {
	if (flag == 1) {
		$("#div-tabla").show();
		$(".div-form").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) {
		$("#div-tabla").hide();
		$(".div-form").show();

		$(".btn-agregar").hide();
		$(".btn-guardar").show();
		$(".btn-cancelar").show();
	}
}

function listar_tabla(){
  tabla_servicios = $('#tabla-servicios').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    // dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    dom:"<'row'<'col-md-7 col-lg-8 col-xl-9 col-xxl-10 pt-2'f><'col-md-5 col-lg-4 col-xl-3 col-xxl-2 pt-2 d-flex justify-content-end align-items-center'<'length'l><'buttons'B>>>r t <'row'<'col-md-6'i><'col-md-6'p>>",
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_servicios) { tabla_servicios.ajax.reload(null, false); } } },
      // { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de Servicios', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de Servicios', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      // { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/servicio.php?op=listar_tabla',
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
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: #
      // if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
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
  }).DataTable();
}

function guardar_editar_servicio(e){
  var formData = new FormData($("#form-agregar-servicio")[0]);

	$.ajax({
		url: "../ajax/servicio.php?op=guardar_editar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (e) {
			try {
				e = JSON.parse(e);
        if (e.status == true) {	
					sw_success('Exito', 'Servicio guardado correctamente.');
					tabla_servicios.ajax.reload(null, false);          
					show_hide_form(1);
          limpiar_form_servicio();
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
					$("#barra_progress_servicio").css({ "width": percentComplete + '%' });
					$("#barra_progress_servicio div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$(".btn-guardar").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
			$("#barra_progress_servicio").css({ width: "0%", });
			$("#barra_progress_servicio div").text("0%");
      $("#barra_progress_servicio_div").show();
		},
		complete: function () {
			$("#barra_progress_servicio").css({ width: "0%", });
			$("#barra_progress_servicio div").text("0%");
      $("#barra_progress_servicio_div").hide();
		},
		error: function (jqXhr, ajaxOptions, thrownError) {
			ver_errores(jqXhr);
		}
	});
}

function mostrar_servicio(idproducto){
  limpiar_form_servicio();
	show_hide_form(2);
	$('#cargando-1-fomulario').hide();	$('#cargando-2-fomulario').show(); 
	$.post("../ajax/servicio.php?op=mostrar", { idproducto: idproducto }, function (e, status) {
		e = JSON.parse(e);

		$('#idproducto').val(e.data.idproducto);
    $('#codigo').val(e.data.codigo);
    $('#codigo_alterno').val(e.data.codigo_alterno);
    $('#nombre').val(e.data.nombre);
    $('#stock').val(e.data.stock);
    $('#descripcion').val(e.data.descripcion);
    $('#precio_v').val(e.data.precio_venta);

    $("#imagenmuestra").show();
		$("#imagenmuestra").attr("src", "../assets/modulo/servicios/" + e.data.imagen);
		$("#imagenactual").val(e.data.imagen);

    $('#cargando-1-fomulario').show();	$('#cargando-2-fomulario').hide();
    $('#form-agregar-servicio').valid();
	});	
}


function eliminar_papelera_servicio(idproducto, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/servicio.php?op=papelera",
    "../ajax/servicio.php?op=eliminar", 
    idproducto, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_servicios.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}


$(document).ready(function () {
  init();
});

function mayus(e) {
  e.value = e.value.toUpperCase();
}

function generarcodigonarti() {

  var name_producto = $("#nombre").val() == null || $("#nombre").val() == '' ? '' : $("#nombre").val() ;  
  if (name_producto == '') { toastr_warning('Vacio!!','El nombre esta vacio, digita para completar el codigo aletarorio.', 700); }
  name_producto = name_producto.substring(-3, 3);
  var cod_letra = Math.random().toString(36).substring(2, 5);  
  var cod_number = Math.floor(Math.random() * 10) +''+ Math.floor(Math.random() * 10);  
  $("#codigo_alterno").val(`${name_producto.toUpperCase()}${cod_number}${cod_letra.toUpperCase()}`);
  $('#form-agregar-servicio').valid();
}

function create_code_producto(pre_codigo) {
  $('.charge_codigo').html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
 
  $.getJSON(`../ajax/ajax_general.php?op=create_code_producto&pre_codigo=${pre_codigo}`, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $('#codigo').val(e.data.nombre_codigo);
      $('#codigo').attr('readonly', 'readonly').addClass('bg-light'); // Asegura que el campo esté como solo lectura
      add_tooltip_custom('#codigo', 'No se puede editar');            //  Agrega tooltip personalizado a un element
      $('.charge_codigo').html('');                                    // limpiamos la carga
      $('#form-agregar-servicio').valid();
    } else {
      ver_errores(e);
    }      
  }).fail( function(jqxhr, textStatus, error) { ver_errores(jqxhr); } );
  
}

//  :::::::::::::::::::: F O R M U L A R I O   S E R V I C I O ::::::::::::::::::::

$(function () {
  $("#form-agregar-servicio").validate({
    ignore: "",
    rules: {           
      codigo:         { required: true, minlength: 2, maxlength: 20, },       
      categaria:    	{ required: true },       
      nombre:    			{ required: true, minlength: 2, maxlength: 200,  },       
      descripcion:    { minlength: 2, maxlength: 500, }, 
      precio_v:       { required: true, min: 0, },       
      stock:          { required: true, min: 0, },       
      codigo_alterno: { required: true, minlength: 4, maxlength: 20,
        remote: {
          url: "../ajax/servicio.php?op=validar_code_producto",
          type: "get",
          data: {
            action: function () { return "validar_codigo";  },
            idproducto: function() { var idproducto = $("#idproducto").val(); return idproducto; }
          }
        }
      },
    },
    messages: {     
      cogido:    			{ required: "Campo requerido", },
      categaria:    	{ required: "Campo requerido", },
      nombre:    			{ required: "Campo requerido", },       
      descripcion:    { minlength: "Minimo {0} caracteres.", }, 
      precio_v:       { required: "Campo requerido", },      
      codigo_alterno: { required: "Campo requerido", remote:"Código en uso."}, 
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
      guardar_editar_servicio(e);      
    },
  });
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cambiarImagen() {
	var imagenInput = document.getElementById('imagen');
	imagenInput.click();
}

function removerImagen() {
	$("#imagenmuestra").attr("src", "../assets/modulo/servicios/no-servicio.png");
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

function ver_img(img, nombre) {
	$(".title-modal-img").html(`-${nombre}`);
  $('#modal-ver-img').modal("show");
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/servicios', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}