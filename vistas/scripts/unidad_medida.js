var tabla_u_medida;
function init(){
  listar_tabla();
	$("#guardar_registro_u_m").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-u-m").submit(); } });
}

function listar_tabla(){
  tabla_u_medida = $('#tabla-u-m').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_u_medida) { tabla_u_medida.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5], }, text: `<i class="fas fa-copy"></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5], }, title: 'Lista de unidades de medida', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5], }, title: 'Lista de unidades de medida', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/unidad_medida.php?op=listar_tabla_um',
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
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      // if (data[7] != '') { $("td", row).eq(7).addClass("text-center"); }
      // columna: 5
      if (data[7] <= 63 ) { $("td", row).eq(1).attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'No tienes opcion a modificar'); }
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
      { targets: [7],  visible: false,  searchable: false,  },
    ],
  }).DataTable();
  
}

function guardar_editar_UM(e){
  var formData = new FormData($("#formulario-u-m")[0]);
  $.ajax({
    url: "../ajax/unidad_medida.php?op=guardar_editar_UM",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Unidad de medida registrado correctamente.", "success");
	      tabla_u_medida.ajax.reload(null, false);         
				limpiar_form_um();
        $("#modal-agregar-u-m").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_u_m").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_u_m(idsunat_c03_unidad_medida){
  $(".tooltip").remove();
  $("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
  
  limpiar_form_um();

  $("#modal-agregar-u-m").modal("show");

  $.post("../ajax/unidad_medida.php?op=mostrar_u_m", { idsunat_c03_unidad_medida: idsunat_c03_unidad_medida }, function (e, status) {
    e = JSON.parse(e); 
    if (e.status) {
      $("#idsunat_c03_unidad_medida").val(e.data.idsunat_c03_unidad_medida);
      $("#nombre_um").val(e.data.nombre);        
      $("#abreviatura_um").val(e.data.abreviatura);        
      $("#equivalencia_um").val(e.data.equivalencia);        
      $("#descr_um").val(e.data.descripcion);    

      $("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
    } else { ver_errores(e); }
    
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_papelera_u_m(idsunat_c03_unidad_medida, nombre){
  crud_eliminar_papelera(
    "../ajax/unidad_medida.php?op=desactivar",
    "../ajax/unidad_medida.php?op=eliminar", 
    idsunat_c03_unidad_medida, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_u_medida.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function limpiar_form_um() {
  $("#guardar_registro_u_m").html('Guardar Cambios').removeClass('disabled');
  //Mostramos los Materiales
  $("#idsunat_c03_unidad_medida").val("");
  $("#nombre_um").val("");
  $("#abreviatura_um").val("");
  $("#equivalencia_um").val("");
  $("#descr_um").val("");
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function mayus(e) {
  e.value = e.value.toUpperCase();
}

$(document).ready(function () {
  init();
});

$(function () {

  $("#formulario-u-m").validate({
    rules: {
      nombre_um: { required: true } ,     // terms: { required: true },
      equivalencia_um: { required: true } ,     // terms: { required: true },
      abreviatura_um: { required: true }      // terms: { required: true },
    },
    messages: {
      nombre_um: {  required: "Campo requerido.", },
      equivalencia_um: {  required: "Campo requerido.", },
      abreviatura_um: {  required: "Campo requerido.", },
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
      guardar_editar_UM(e);      
    },

  });
});