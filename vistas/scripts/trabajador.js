var tabla_trabajador;
var tabla_historial;
var modoDemo = false;
//Función que se ejecuta al inicio
function init() {

	listar();

	// ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-trabajador").submit(); }  });

	// ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/ajax_general.php?op=select2_cargo", '#idcargo_trabajador', null);
  lista_select2("../ajax/ajax_general.php?op=select2_tipo_documento", '#tipo_documento', null);
  lista_select2("../ajax/ajax_general.php?op=select2_banco", '#idbanco', null);
  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#distrito', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#tipo_documento").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idcargo_trabajador").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idbanco").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#distrito").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//Función limpiar
function limpiar_form() {
	$('#idpersona').val('');
  $('#idpersona_trabajador').val('');
  $('#tipo_persona_sunat').val('NATURAL');
  $('#idtipo_persona').val('2');

  $('#tipo_documento').val(null).trigger("change");
  $('#numero_documento').val('');
  $('#idcargo_trabajador').val(null).trigger("change");
  $('#nombre_razonsocial').val('');
  $('#apellidos_nombrecomercial').val('');
  $('#correo').val('');
  $('#celular').val('');
  $('#fecha_nacimiento').val(null).trigger("change")
  
  $('#ruc').val('');
  $('#usuario_sol').val('');
  $('#clave_sol').val('');
  $('#direccion').val('');
  $('#distrito').val('').trigger("change");
  $('#departamento').val('');
  $('#provincia').val('');
  $('#ubigeo').val('');
  $('#sueldo_mensual').val('');
  $('#sueldo_diario').val('');
  $('#idbanco').val(null).trigger("change")
  $('#cuenta_bancaria').val('');
  $('#cci').val('');
  $('#titular_cuenta').val('');    

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg");
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/usuario/perfil/no-perfil.jpg';
  }

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function show_hide_form(flag) {
	if (flag == 1) {
		$("#div-tabla").show();
		$("#div-form").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) {
		$("#div-tabla").hide();
		$("#div-form").show();

		$(".btn-agregar").hide();
		$(".btn-guardar").show();
		$(".btn-cancelar").show();
	}
}

//Función Listar
function listar() {
	tabla_trabajador = $('#tabla-usuario').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_trabajador) { tabla_trabajador.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,8,9,10,4,5,11,12,6,7], }, title: '', text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,8,9,10,4,5,11,12,6,7], }, title: 'Lista de Trabajadores', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,8,9,10,4,5,11,12,6,7], }, title: 'Lista de Trabajadores', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
		"ajax":	{
			url: '../ajax/trabajador.php?op=listar_tabla_principal',
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
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: #
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: #
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: #
      if (data[7] != '') { $("td", row).eq(7).addClass("text-center"); }
    },
		language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]],//Ordenar (columna,orden)
    columnDefs: [
      // { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = 'numero_positivos'; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },      
      // { targets: [8], render: $.fn.dataTable.render.number( ',', '.', 2) },
      { targets: [8,9, 10,11,12,],  visible: false,  searchable: false,  },
    ],
	}).DataTable();
}
//Función para guardar o editar

function guardar_y_editar_trabajador(e) {
	//e.preventDefault(); //No se activará la acción predeterminada del evento
	
	var formData = new FormData($("#form-agregar-trabajador")[0]);

	$.ajax({
		url: "../ajax/trabajador.php?op=guardar_y_editar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (e) {
			try {
				e = JSON.parse(e);  //console.log(e); 
        if (e.status == true) {	
					tabla_trabajador.ajax.reload(null, false);          
					show_hide_form(1)
					sw_success('Exito', 'Trabajador guardado correctamente.');
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
					/*console.log(percentComplete + '%');*/
					$("#barra_progress_trabajador").css({ "width": percentComplete + '%' });
					$("#barra_progress_trabajador div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$(".btn-guardar").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
			$("#barra_progress_trabajador").css({ width: "0%", });
			$("#barra_progress_trabajador div").text("0%");
      $("#barra_progress_trabajador_div").show();
		},
		complete: function () {
			$("#barra_progress_trabajador").css({ width: "0%", });
			$("#barra_progress_trabajador div").text("0%");
      $("#barra_progress_trabajador_div").hide();
		},
		error: function (jqXhr, ajaxOptions, thrownError) {
			ver_errores(jqXhr);
		}
	});
}

function mostrar(idpersona) {
  limpiar_form();
	show_hide_form(2);
	$('#cargando-1-fomulario').hide();	$('#cargando-2-fomulario').show(); 
	
	$.post("../ajax/trabajador.php?op=mostrar_trabajador", { idpersona: idpersona }, function (e, status) {
		e = JSON.parse(e);
    if (e.status == true) {      
      
      $('#idpersona').val(e.data.idpersona);
      $('#idpersona_trabajador').val(e.data.idpersona_trabajador);
      // $('#tipo_persona_sunat').val(e.data.tipo_persona_sunat);
      $('#idtipo_persona').val(e.data.idtipo_persona);

      $('#tipo_documento').val(e.data.code_sunat).trigger("change");
      $('#numero_documento').val(e.data.numero_documento);
      $('#idcargo_trabajador').val(e.data.idcargo_trabajador).trigger("change");
      $('#nombre_razonsocial').val(e.data.nombre_razonsocial);
      $('#apellidos_nombrecomercial').val(e.data.apellidos_nombrecomercial);
      $('#correo').val(e.data.correo);
      $('#celular').val(e.data.celular);
      $('#fecha_nacimiento').val(e.data.fecha_nacimiento).trigger("change")
      
      $('#ruc').val(e.data.ruc);
      $('#usuario_sol').val(e.data.usuario_sol);
      $('#clave_sol').val(e.data.clave_sol);
      $('#direccion').val(e.data.direccion);
      $('#distrito').val(e.data.distrito).trigger("change");
      $('#departamento').val(e.data.departamento);
      $('#provincia').val(e.data.provincia);
      $('#ubigeo').val(e.data.cod_ubigeo);
      $('#sueldo_mensual').val(e.data.sueldo_mensual);
      $('#sueldo_diario').val(e.data.sueldo_diario);
      $('#idbanco').val(e.data.idbancos).trigger("change")
      $('#cuenta_bancaria').val(e.data.cuenta_bancaria);
      $('#cci').val(e.data.cci);
      $('#titular_cuenta').val(e.data.titular_cuenta);

      $("#imagenmuestra").show();
      $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/" + e.data.foto_perfil);
      $("#imagenactual").val(e.data.foto_perfil);

      $('#cargando-1-fomulario').show();	$('#cargando-2-fomulario').hide();
      $('#form-agregar-trabajador').valid();
    } else {
      ver_errores(e)
    }
	}).fail( function(e) { ver_errores(e); } );
}

//Función para desactivar registros
function desactivar(idtrabajador, idpersona, nombre) {
	$('.tooltip').remove();
	crud_eliminar_papelera(
    `../ajax/trabajador.php?op=papelera&idpersona=${idpersona}`,
    `../ajax/trabajador.php?op=eliminar&idpersona=${idpersona}`, 
    idtrabajador,  
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_trabajador.ajax.reload(null, false);     },
    false, 
    false, 
    false,
    false
  );
}

//Función para activar registros
function activar(idtrabajador, idpersona, nombre) {
	crud_simple_alerta(
		`../ajax/trabajador.php?op=activar&idpersona=${idpersona}`,
    idtrabajador,  
    "!Elija una opción¡", 
    `<b class="text-success">${nombre}</b> <br> Este trabajador sera visible en el sistema.`, 
		`Aceptar`,
    function(){ sw_success('Recuperado', "Tu registro ha sido restaurado." ) }, 
    function(){ tabla_trabajador.ajax.reload(null, false);     },
    false, 
    false, 
    false,
    false
  );
}

function clientes_x_trabajador(id) {
  $('#modal-ver-cliente').modal('show');
  tabla_historial = $('#tabla-cliente').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-2'B><'col-md-3 float-left'l><'col-md-7'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_historial) { tabla_historial.ajax.reload(null, false); } } },      
    ],
		"ajax":	{
			url: `../ajax/trabajador.php?op=clientes_x_trabajador&idtrabajador=${id}`,
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			},
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');        
        $('[data-bs-toggle="tooltip"]').tooltip();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != "") { $("td", row).eq(0).addClass("text-center"); }
      // columna: sub total
      if (data[1] != "") { $("td", row).eq(1).addClass("text-nowrap"); } 
    },
		language: {
      lengthMenu: "_MENU_",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      emptyTable: "No hay datos"
    },
    columnDefs: [
      { targets: [3], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = 'numero_positivos'; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },      
      // { targets: [8], render: $.fn.dataTable.render.number( ',', '.', 2) },
      // { targets: [8,9, 10,11,12,],  visible: false,  searchable: false,  },
    ],
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]]//Ordenar (columna,orden)
	}).DataTable();

}

$(document).ready(function () {
  init();
});

// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function () {
  $('#tipo_documento').on('change', function() { $(this).trigger('blur'); });
  $('#idcargo_trabajador').on('change', function() { $(this).trigger('blur'); });
  $('#distrito').on('change', function() { $(this).trigger('blur'); });
  $('#idbanco').on('change', function() { $(this).trigger('blur'); });
  $("#form-agregar-trabajador").validate({
    ignore: "",
    rules: {           
      tipo_documento:           { required: true, minlength: 1, maxlength: 2, },       
      numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      idcargo_trabajador:    		{ required: true, },       
      nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      apellidos_nombrecomercial:{ required: true, minlength: 4, maxlength: 200, },       
      correo:    			          { minlength: 4, maxlength: 100, },       
      celular:    			        { minlength: 8, maxlength: 9, },       
      fecha_nacimiento:    			{  },  

      ruc:    			            { minlength: 4, maxlength: 11, },       
      usuario_sol:    			    { minlength: 4, maxlength: 20, },       
      clave_sol:    			      { minlength: 4, maxlength: 20, },       
      direccion:    			      { minlength: 4, maxlength: 200, },       
      distrito:    			        { required: true, },       
      departamento:    			    { required: true, },       
      provincia:    			      { required: true, },  
      ubigeo:    			          { required: true, },

      sueldo_mensual:    			  { min: 0, },
      sueldo_diario:    			  { min: 0, },
      idbanco:    			        { required: true, },
      cuenta_bancaria:    			{ minlength: 4, maxlength: 45, },
      cci:    			            { minlength: 4, maxlength: 45, },
      titular_cuenta:    			  { minlength: 4, maxlength: 45, },

			
    },
    messages: {     
      tipo_documento:    			  { required: "Campo requerido", },
      numero_documento:    			{ required: "Campo requerido", }, 
      idcargo_trabajador:    		{ required: "Campo requerido", }, 
      nombre_razonsocial:    		{ required: "Campo requerido", }, 
      apellidos_nombrecomercial:{ required: "Campo requerido", }, 
      correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      celular:    			        { minlength: "Mínimo {0} caracteres.", }, 
      fecha_nacimiento:    			{  }, 

      ruc:    			            { minlength: "Mínimo {0} caracteres.", }, 
      usuario_sol:    			    { minlength: "Mínimo {0} caracteres.", }, 
      clave_sol:    			      { minlength: "Mínimo {0} caracteres.", }, 
      direccion:    			      { minlength: "Mínimo {0} caracteres.", },
      distrito:    			        { required: "Campo requerido", }, 
      departamento:    			    { required: "Campo requerido", }, 
      provincia:    			      { required: "Campo requerido", }, 
      ubigeo:    			          { required: "Campo requerido", },

      sueldo_mensual:    			  { min: "Mínimo {0}.", }, 
      sueldo_diario:    			  { min: "Mínimo {0}.", }, 
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
      guardar_y_editar_trabajador(e);      
    },
  });
  $('#tipo_documento').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#idcargo_trabajador').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#distrito').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#idbanco').rules('add', { required: true, messages: {  required: "Campo requerido" } });
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cambiarImagen() {
	var imagenInput = document.getElementById('imagen');
	imagenInput.click();
}
function removerImagen() {
	// var imagenMuestra = document.getElementById('imagenmuestra');
	// var imagenActualInput = document.getElementById('imagenactual');
	// var imagenInput = document.getElementById('imagen');
	// imagenMuestra.src = '../assets/images/faces/9.jpg';
	$("#imagenmuestra").attr("src", "../assets/images/faces/9.jpg");
	// imagenActualInput.value = '';
	// imagenInput.value = '';
	$("#imagen").val("");
  $("#imagenactual").val("");
}

// Esto se encarga de mostrar la imagen cuando se selecciona una nueva
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
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/persona/perfil', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}

function llenar_dep_prov_ubig(input) {

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 

  if ($(input).select2("val") == null || $(input).select2("val") == '') { 
    $("#departamento").val(""); 
    $("#provincia").val(""); 
    $("#ubigeo").val(""); 

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito =  $(input).select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {   
      e = JSON.parse(e); console.log(e);
      if (e.status == true) {
        $("#departamento").val(e.data.departamento); 
        $("#provincia").val(e.data.provincia); 
        $("#ubigeo").val(e.data.ubigeo_inei); 

        $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
        $("#form-agregar-trabajador").valid();
      } else {
        ver_errores(e);
      }      
    }).fail( function(e) { ver_errores(e); } );
  }  
}

// Modificar nombre segun  el tipo de documento
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
  


function reload_cargo(){ lista_select2("../ajax/ajax_general.php?op=select2_cargo", '#idcargo_trabajador', null, '.charge_idcargo'); }

function reload_banco(){ lista_select2("../ajax/ajax_general.php?op=select2_banco", '#idbanco', null, '.charge_idbanco'); }
