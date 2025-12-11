var tabla_ie_categoria;

//Función que se ejecuta al inicio
function init_ie_categoria() {
  
  $("#bloc_Recurso").addClass("menu-open");

  $("#mRecurso").addClass("active");

  tabla_principal_ie_categoria();

  $("#guardar_registro_ie_categoria").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-ie-categoria").submit(); }  });

}

//Función para limpiar el formulario
function limpiar_form_ie_categoria() {
  
  $("#guardar_registro_ie_categoria").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled');

  $("#id_ie_categoria").val("");
  $("#nombre_ie_cat").val("");
  $("#descr_ie_cat").val("");

  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

//Función para listar la tabla de ingreso_egreso_categoria
function tabla_principal_ie_categoria() {

  tabla_ie_categoria = $('#tabla-ie-categoria').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],
    "aProcessing": true,
    "aServerSide": true,
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (categoria_otros_gastos) { categoria_otros_gastos.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3], }, title: 'Lista de planes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3], }, title: 'Lista de planes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/ingreso_egreso_categoria.php?op=tabla_principal_ie_categoria',
      type : "get",
      dataType : "json",						
      error: function(e){
        console.log(e.responseText);	ver_errores(e);
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
      if (data[1] != '') { $("td", row).eq(1).addClass("text-center"); }
    },
		language: {
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 5,
    "order": [[0, "asc"]]
  }).DataTable();

}

//Función para guardar o editar
function guardar_y_editar_ie_categoria(e) {

  var formData = new FormData($("#form-agregar-ie-categoria")[0]);
 
  $.ajax({
    url: "../ajax/ingreso_egreso_categoria.php?op=guardar_editar_ie_categoria",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Categoría de gasto registrado correctamente.", "success");
	      tabla_ie_categoria.ajax.reload(null, false);         
				limpiar_form_ie_categoria();
        $("#modal-agregar-ie-categoria").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_ie_categoria").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
    }
  });
}

//Función para mostrar un registro en el formulario
function mostrar_ie_categoria(id) {

  $(".tooltip").remove();
  $("#cargando-13-fomulario").hide();
  $("#cargando-14-fomulario").show();
  
  limpiar_form_ie_categoria();

  $("#modal-agregar-ie-categoria").modal("show")

  $.getJSON("../ajax/ingreso_egreso_categoria.php?op=mostrar_datos_ie_categoria", { id: id }, function (e, status) {    

    if (e.status == true) {      
      
      $("#id_ie_categoria").val(e.data.idingreso_egreso_categoria);
      $("#nombre_ie_cat").val(e.data.nombre);
      $("#descr_ie_cat").val(e.data.descripcion);

      $("#cargando-13-fomulario").show();
      $("#cargando-14-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
  
}

//Función para desactivar registros
function eliminar_ie_categoria(idingreso_egreso_categoria, nombre) {

  crud_eliminar_papelera(
    "../ajax/categoria_otros_gastos.php?op=desactivar",
    "../ajax/categoria_otros_gastos.php?op=eliminar", 
    idingreso_egreso_categoria, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_ie_categoria.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );

}

// Ejecutamos la función de inicialización
$(document).ready(function () {

  init_ie_categoria();
  
});

// Configuramos la validación del formulario
$(function () {

  $("#form-agregar-ie-categoria").validate({

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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_y_editar_ie_categoria(e);      
    },

  });

});

