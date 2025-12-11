var tabla_marca;

function init_mar(){

  listar_marca();

	$("#guardar_registro_marca").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-marca").submit(); } });

}

function limpiar_form_marca(){
  $("#guardar_registro_marca").html('Guardar Cambios').removeClass('disabled');
  //Mostramos los Materiales
  $("#idproducto_marca").val("");
  $("#nombre_marca").val("");
  $("#descr_marca").val("");
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function listar_marca(){
  tabla_marca = $('#tabla-marca').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_marca) { tabla_marca.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4], }, title: 'Lista de planes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4], }, title: 'Lista de planes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/marca.php?op=listar_tabla_marca',
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
      if (data[5] == 1) { $("td", row).eq(1).attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'No tienes opcion a modificar'); }
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
      { targets: [5],  visible: false,  searchable: false,  },
    ],
  }).DataTable();
  
}

function guardar_editar_marca(e){
  var formData = new FormData($("#formulario-marca")[0]);
  $.ajax({
    url: "../ajax/marca.php?op=guardar_editar_marca",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Marca registrada correctamente.", "success");
	      tabla_marca.ajax.reload(null, false);         
				limpiar_form_marca();
        $("#modal-agregar-marca").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_marca").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_marca(idproducto_marca){
  $(".tooltip").remove();
  $("#cargando-3-fomulario").hide();
  $("#cargando-4-fomulario").show();
  
  limpiar_form_cat();

  $("#modal-agregar-marca").modal("show");

  $.post("../ajax/marca.php?op=mostrar_marca", { idproducto_marca: idproducto_marca }, function (e, status) {
    e = JSON.parse(e); 
    if (e.status) {
      $("#idproducto_marca").val(e.data.idproducto_marca);
      $("#nombre_marca").val(e.data.nombre);        
      $("#descr_marca").val(e.data.descripcion);    

      $("#cargando-3-fomulario").show();
      $("#cargando-4-fomulario").hide();
    } else { ver_errores(e); }
    
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_papelera_marca(idproducto_marca, nombre){
  crud_eliminar_papelera(
    "../ajax/marca.php?op=desactivar",
    "../ajax/marca.php?op=eliminar", 
    idproducto_marca, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_marca.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function mayus(e) {
  e.value = e.value.toUpperCase();
}

$(document).ready(function () {
  init_mar();
});


//  :::::::::::::::::::::: F O R M U L A R I O   M A R C A :::::::::::::::::::::::::::

$(function () {

  $("#formulario-marca").validate({
    rules: {
      nombre_marca: { required: true } ,     // terms: { required: true },
    },
    messages: {
      nombre_marca: {  required: "Campo requerido.", },
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
      guardar_editar_marca(e);      
    },

  });
});