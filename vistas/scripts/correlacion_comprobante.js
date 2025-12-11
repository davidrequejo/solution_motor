var tabla_correlacion;
function init(){
  listar_tabla();
	$("#guardar_registro_correlacion_compb").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-correlacion-compb").submit(); } });
}



function listar_tabla(){
  tabla_correlacion = $('#tabla-correlacion-compb').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_correlacion) { tabla_correlacion.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [1,2,3,4,5,6], }, title: 'Lista de usuarios', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [1,2,3,4,5,6], }, title: 'Lista de usuarios', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/correlacion_comprobante.php?op=listar_tabla',
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
  var formData = new FormData($("#formulario-correlacion-compb")[0]);
  $.ajax({
    url: "../ajax/correlacion_comprobante.php?op=guardar_editar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      try {
        e = JSON.parse(e);
        if (e.status == 'registrado') {
          sw_success("Excelente", "Correlación Registrado", 3000);
          tabla_correlacion.ajax.reload(null, false);
          $('#modal-corrlacion-compb').modal('hide');
          
        } else if (e.status == 'modificado') {
          sw_success("Excelente", "Correlacion Actualizado", 3000);
          tabla_correlacion.ajax.reload(null, false);
          $('#modal-corrlacion-compb').modal('hide');
        } 
        
        else { ver_errores(e); }
      } catch (err) { console.log("Error: ", err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }

    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function limpiar_form() {
  $("#guardar_registro_correlacion_compb").html('Guardar Cambios').removeClass('disabled');
  //Mostramos los Materiales
  $("#idtipo_comprobante").val("");
  $("#codg").val("");
  $("#nombre").val("");
  $("#abrt").val("");
  $("#serie").val("");
  $("#numero").val("");
  $("#un1001").val("");
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function mostrar_correlacion_compb(idtipo_comprobante){
  $(".tooltip").remove();
  $("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
  
  limpiar_form();

  $("#modal-corrlacion-compb").modal("show");

  $.post("../ajax/correlacion_comprobante.php?op=mostrar_correlacion_compb", { idtipo_comprobante: idtipo_comprobante }, function (e, status) {
    e = JSON.parse(e); 
    if (e.status) {
      $("#idtipo_comprobante").val(e.data.idtipo_comprobante);
      $("#codg").val(e.data.codigo);        
      $("#nombre").val(e.data.nombre);        
      $("#abrt").val(e.data.abreviatura);        
      $("#serie").val(e.data.serie);        
      $("#numero").val(e.data.numero);        
      $("#un1001").val(e.data.un1001);   

      $("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
    } else { ver_errores(e); }
    
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_papelera_correlacion_compb(idtipo_comprobante, nombre){
  crud_eliminar_papelera(
    "../ajax/correlacion_comprobante.php?op=desactivar",
    "../ajax/correlacion_comprobante.php?op=eliminar", 
    idtipo_comprobante, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_correlacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function mayus(e) {
  e.value = e.value.toUpperCase();
}

init();

$(function () {

  $("#formulario-correlacion-compb").validate({
    rules: {      
      serie: { required: true, minlength: 4 } , 
      numero: { required: true, min: 0, step: 1 } ,      
    },
    messages: {      
      serie: {  required: "Campo requerido.", },
      numero: {  required: "Campo requerido.", step: 'Solo numeros enteros' },      
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
      guardar_editar(e);      
    },

  });
});