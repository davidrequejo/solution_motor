var tabla_empresa;
function init(){
  listar_tabla();
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-empresa").submit(); }  });

  lista_select2("../ajax/empresa.php?op=select_banco", '#banco1', null);
  lista_select2("../ajax/empresa.php?op=select_banco", '#banco2', null);
  lista_select2("../ajax/empresa.php?op=select_banco", '#banco3', null);
  lista_select2("../ajax/empresa.php?op=select_banco", '#banco4', null);
  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#distrito', null);


  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#tipo_doc").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#logo_c_r").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#banco1").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#banco2").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#banco3").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#banco4").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#distrito").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });


}

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', null, true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form(){
  $("#idempresa").val("");

	$("#documento").val("");
	$("#razon_social").val("");
	$("#nomb_comercial").val("");
	$("#telefono1").val("");
	$("#telefono2").val("");
	$("#web").val("");
	$("#werb_cosulta").val("");	
	$("#correo").val(""); 
	$("#logo_c_r").val(""); 

	$("#banco1").val(""); 
	$("#cuenta1").val(""); 
	$("#cci1").val(""); 
	$("#banco2").val(""); 
	$("#cuenta2").val(""); 
	$("#cci2").val(""); 
  $("#banco3").val(""); 
	$("#cuenta3").val(""); 
	$("#cci3").val("");
  $("#banco4").val(""); 
	$("#cuenta4").val(""); 
	$("#cci4").val("");  

	$("#codg_pais").val("PE"); 
	$("#domicilio_fiscal").val(""); 
	$("#distrito").val(""); 
	$("#departamento").val(""); 
	$("#provincia").val(""); 
	$("#ubigeo").val(""); 
	$("#codg_ubigeo").val(""); 
	$("#referencia").val("");

  doc1_eliminar();
   // Limpiamos las validaciones
   $(".form-control").removeClass('is-valid');
   $(".form-control").removeClass('is-invalid');
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


function listar_tabla(){
  tabla_empresa = $('#tabla-empresa').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_empresa) { tabla_empresa.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [1,2,3,4,5,6], }, title: 'Lista de usuarios', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [1,2,3,4,5,6], }, title: 'Lista de usuarios', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/empresa.php?op=listar_tabla',
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
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]]
  }).DataTable();
  
}

function guardar_editar(e){ 
  var formData = new FormData($("#formulario-empresa")[0]);
  $.ajax({
    url: "../ajax/empresa.php?op=guardar_editar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");
          tabla_empresa.ajax.reload(null, false);
          show_hide_form(1); limpiar_form();
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_empresa").html('Guardar Cambios').removeClass('disabled send-data');
    },
    beforeSend: function () {
      $("#guardar_registro_empresa").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_empresa").css({ width: "0%", });
      $("#barra_progress_empresa div").text("0%");
      $("#barra_progress_empresa_div").show();
    },
    complete: function () {
      $("#barra_progress_empresa").css({ width: "0%", });
      $("#barra_progress_empresa div").text("0%");
      $("#barra_progress_empresa_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

function mostrar_editar_empresa(idempresa){
  $.post("../ajax/empresa.php?op=mostrar_empresa", { idempresa: idempresa }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      $("#idempresa").val(e.data.idempresa);

      $("#tipo_doc").val(e.data.tipo_documento).trigger('change');
      $("#documento").val(e.data.numero_documento);
      $("#razon_social").val(e.data.nombre_razon_social);
      $("#nomb_comercial").val(e.data.nombre_comercial);
      $("#telefono1").val(e.data.telefono1);
      $("#telefono2").val(e.data.telefono2);
      $("#web").val(e.data.web);
      $("#werb_cosulta").val(e.data.web_consulta_cp);	
      $("#correo").val(e.data.correo); 
      $("#logo_c_r").val(e.data.logo_c_r).trigger('change'); 
    
      $("#banco1").val(e.data.banco1).trigger('change'); 
      $("#cuenta1").val(e.data.cuenta1); 
      $("#cci1").val(e.data.cci1); 
      $("#banco2").val(e.data.banco2).trigger('change'); 
      $("#cuenta2").val(e.data.cuenta2); 
      $("#cci2").val(e.data.cci2); 
      $("#banco3").val(e.data.banco3).trigger('change'); 
      $("#cuenta3").val(e.data.cuenta3); 
      $("#cci3").val(e.data.cci3);
      $("#banco4").val(e.data.banco4).trigger('change'); 
      $("#cuenta4").val(e.data.cuenta4); 
      $("#cci4").val(e.data.cci4);  
    
      $("#codg_pais").val(e.data.codigo_pais); 
      $("#domicilio_fiscal").val(e.data.domicilio_fiscal); 
      $("#distrito").val(e.data.distrito).trigger('change'); 
      $("#departamento").val(e.data.departamento); 
      $("#provincia").val(e.data.provincia); 
      $("#ubigeo").val(e.data.ubigueo); 
      $("#codg_ubigeo").val(e.data.codubigueo); 
      $("#referencia").val(e.data.texto_libre);
      // ------------ IMAGEN -----------
      if (e.data.logo == "" || e.data.logo == null) { } else {
        $("#doc_old_1").val(e.data.logo);
        $("#doc1_nombre").html(`<div class="row"> <div class="col-md-12"><i>imagen.${extrae_extencion(e.data.logo)}</i></div></div>`);
        // cargamos la imagen adecuada par el archivo
        $("#doc1_ver").html(doc_view_extencion(e.data.logo, 'assets/modulo/empresa/logo', '50%', '110'));   //ruta imagen          
      }

      show_hide_form(2);
    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_papelera_empresa(id, nombre){

  crud_eliminar_papelera(
    "../ajax/empresa.php?op=desactivar",
    "../ajax/empresa.php?op=eliminar",
    id,
    "!Elija una opción¡",
    `<b class="text-danger"><del> ${nombre} </del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`,
    function () { sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado.") },
    function () { sw_success('Eliminado!', 'Tu registro ha sido Eliminado.') },
    function () { tabla_empresa.ajax.reload(null, false); },
    false,
    false,
    false,
    false
  );
}

function mostrar_detalles_empresa(id){
  $("#modal-empresa").modal('show');
  $.post("../ajax/empresa.php?op=mostrar_empresa", { idempresa: id }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {

      $("#e_tp_doc").val(e.data.tipo_documento);
      $("#e_documento").val(e.data.numero_documento);
      $("#e_razon_social").val(e.data.nombre_razon_social);
      $("#e_nomb_comercial").val(e.data.nombre_comercial);
      $("#e_telefono1").val(e.data.telefono1);
      $("#e_telefono2").val(e.data.telefono2);
      $("#e_web").val(e.data.web);
      $("#e_correo").val(e.data.correo); 
    
      $("#e_banco1").val(e.data.banco1); 
      $("#e_cci1").val(e.data.cci1); 
      $("#e_cuenta1").val(e.data.cuenta1); 
      $("#e_banco2").val(e.data.banco2); 
      $("#e_cuenta2").val(e.data.cuenta2); 
      $("#e_cci2").val(e.data.cci2); 
      $("#e_banco3").val(e.data.banco3); 
      $("#e_cuenta3").val(e.data.cuenta3); 
      $("#e_cci3").val(e.data.cci3);
      $("#e_banco4").val(e.data.banco4); 
      $("#e_cuenta4").val(e.data.cuenta4); 
      $("#e_cci4").val(e.data.cci4);  
    
      $("#e_codg_pais").val(e.data.codigo_pais); 
      $("#e_domicilio_fiscal").val(e.data.domicilio_fiscal); 
      $("#e_distrito").val(e.data.distrito); 
      $("#e_departamento").val(e.data.departamento); 
      $("#e_provincia").val(e.data.provincia); 
      $("#e_codg_ubigeo").val(e.data.codubigueo); 
      $("#e_referencia").val(e.data.texto_libre);

      $('#logo').html(doc_view_extencion(e.data.logo, 'assets/modulo/empresa/logo', '80%', '100'));
      $(`.jq_image_zoom`).zoom({ on:'grab' });

      e.data.nombre_comercial ? $("#div-nomb-comercial").show() : null;
      e.data.telefono2 ? $("#div-telefono2").show() : null;
      e.data.cuenta2 ? $(".div-banco2").show() : null;
      e.data.cuenta3 ? $(".div-banco3").show() : null;
      e.data.cuenta4 ? $(".div-banco4").show() : null;
    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );

}

function ubicacion_geografica(input){
  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 

  if ($(input).select2("val") == null || $(input).select2("val") == '') { 
    $("#departamento").val(""); 
    $("#provincia").val(""); 
    $("#codg_ubigeo").val(""); 

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito =  $(input).select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {   
      e = JSON.parse(e); console.log(e);
      if (e.status == true) {
        $("#departamento").val(e.data.departamento); 
        $("#provincia").val(e.data.provincia); 
        $("#codg_ubigeo").val(e.data.ubigeo_inei); 

        $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      } else {
        ver_errores(e);
      }      
    }).fail( function(e) { ver_errores(e); } );
  } 
}

function ver_img(img, nombre) {
	$(".title-modal-img").html(`-${nombre}`);
  $('#modal-ver-img').modal("show");
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/empresa/logo', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}

function mayus(e) {
  e.value = e.value.toUpperCase();
}


$(document).ready(function () {
  init(); 
});

// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..
$(function () {
  $("#formulario-empresa").validate({
    rules: {
      tipo_doc: { required: true },
      documento: { required: true, minlength: 11, maxlength:11},
      razon_social: { required: true},
      nomb_comercial: { required: true},
      telefono1: { required: true, minlength: 9},
      codg_pais: { required: true},
      domicilio_fiscal: { required: true},
      distrito: { required: true},
      banco1: { required: true},

    },

    messages: {
      tipo_doc: { required: "Por favor selecciones un tipo de Doc" },
      documento: { required: "Por favor rellena el campo" },
      razon_social: { required: "Por favor rellena el campo" },
      nomb_comercial: { required: "Por favor rellena el campo" },
      telefono1: { required: "Por favor rellena el campo" },
      codg_pais: { required: "Por favor rellena el campo" },
      domicilio_fiscal: { required: "Por favor rellena el campo" },
      distrito: { required: "Por favor rellena el campo" },
      banco1: { required: "Por favor selecciones una opción" },
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
});