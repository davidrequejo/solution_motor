var tabla_proveedores;
function init(){
  listar_proveedores();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-proveedor").submit(); }  });

	// ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/ajax_general.php?op=select2_tipo_documento", '#tipo_documento', null);
  lista_select2("../ajax/ajax_general.php?op=select2_banco", '#idbanco', null);
  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#distrito', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#tipo_documento").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idbanco").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#distrito").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//Función limpiar
function limpiar_form() {
	$('#idpersona').val('');
  $('#tipo_persona_sunat').val('NATURAL');
  $('#idtipo_persona').val('4');

  $('#tipo_documento').val(null).trigger("change");
  $('#numero_documento').val('');
  $('#nombre_razonsocial').val('');
  $('#apellidos_nombrecomercial').val('');
  $('#correo').val('');
  $('#celular').val('');
  
  $('#direccion').val('');
  $("#distrito").val('TOCACHE').trigger("change");
  $('#departamento').val('');
  $('#provincia').val(''); 
  $('#ubigeo').val('');
  $('#idbanco').val(null).trigger("change")
  $('#cuenta_bancaria').val('');
  $('#cci').val(''); 

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/proveedor/no-proveedor.png");
  $("#imagenmuestra").attr("src", "../assets/modulo/proveedor/no-proveedor.png").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/proveedor/no-proveedor.png';
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

function listar_proveedores(){
  tabla_proveedores = $('#tabla-proveedores').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_proveedores) { tabla_proveedores.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [7,8,9,10,11,12,13,14,15,16,17,18,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [7,8,9,10,11,12,13,14,15,16,17,18,6], }, title: 'Lista de Proveedores', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [7,8,9,10,11,12,13,14,15,16,17,18,6], }, title: 'Lista de Proveedores', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/proveedores.php?op=listar_tabla',
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
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs:[
      { targets: [7,8,9,10,11,12,13,14,15,16,17,18],  visible: false,  searchable: false,  },
    ],
  }).DataTable();
}

function guardar_editar_proveedor(e) {

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
					tabla_proveedores.ajax.reload(null, false);          
					show_hide_form(1)
					sw_success('Exito', 'proveedor guardado correctamente.');
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
					$("#barra_progress_proveedor").css({ "width": percentComplete + '%' });
					$("#barra_progress_proveedor div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$(".btn-guardar").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
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

function mostrar_proveedor(idpersona){
  limpiar_form();
	show_hide_form(2);
	$('#cargando-1-fomulario').hide();	$('#cargando-2-fomulario').show(); 
	$.post("../ajax/proveedores.php?op=mostrar", { idpersona: idpersona }, function (e, status) {
		e = JSON.parse(e);

		$('#idpersona').val(e.data.idpersona);
		$('#idpersona_trabajador').val(e.data.idpersona_trabajador);
    $('#idtipo_persona').val(e.data.idtipo_persona);

    $('#tipo_documento').val(e.data.code_sunat).trigger("change");
    $('#numero_documento').val(e.data.numero_documento);
    $('#nombre_razonsocial').val(e.data.nombre_razonsocial);
    $('#apellidos_nombrecomercial').val(e.data.apellidos_nombrecomercial);
    $('#correo').val(e.data.correo);
    $('#celular').val(e.data.celular);
    
    $('#direccion').val(e.data.direccion);
    $('#distrito').val(e.data.distrito).trigger("change");
    $('#departamento').val(e.data.departamento);
    $('#provincia').val(e.data.provincia);
    $('#ubigeo').val(e.data.cod_ubigeo);
    $('#idbanco').val(e.data.idbancos).trigger("change")
    $('#cuenta_bancaria').val(e.data.cuenta_bancaria);
    $('#cci').val(e.data.cci);

    $("#imagenmuestra").show();
		$("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/" + e.data.foto_perfil);
		$("#imagenactual").val(e.data.foto_perfil);

    $('#cargando-1-fomulario').show();	$('#cargando-2-fomulario').hide();
    $('#form-agregar-proveedor').valid();
	});	
}

function eliminar_papelera_proveedor(idpersona, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/proveedores.php?op=papelera",
    "../ajax/proveedores.php?op=eliminar", 
    idpersona, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_proveedores.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

$(document).ready(function () {
  init();
});
$(function () {
  $('#tipo_documento').on('change', function() { $(this).trigger('blur'); });
  $('#distrito').on('change', function() { $(this).trigger('blur'); });
  $('#idbanco').on('change', function() { $(this).trigger('blur'); });
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
      guardar_editar_proveedor(e);      
    },
  });
  $('#tipo_documento').rules('add', { required: true, messages: {  required: "Campo requerido" } });
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
	$("#imagenmuestra").attr("src", "../assets/proveedor/no-proveedor.png");
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
      $("#departamento").val(e.data.departamento); 
      $("#provincia").val(e.data.provincia); 
      $("#ubigeo").val(e.data.ubigeo_inei); 

      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      $("#form-agregar-proveedor").valid();
    });
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
