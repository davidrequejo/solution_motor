var tabla_c_t;

function init_ct(){

  tabla_cargo_trabajador();

  $("#guardar_registro_ct").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-ct").submit(); }  });

}

function limpiar_form_ct(){
  $("#guardar_registro_ct").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled');
  //Mostramos los Materiales
  $("#idcargo_trabajador").val("");
  $("#nombre_ct").val("");

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}


function tabla_cargo_trabajador(){
  tabla_c_t = $('#tabla-cargo-trabajador').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla) { tabla.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3], }, title: 'Lista de cargos del trabajador', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3], }, title: 'Lista de cargos del trabajador', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/cargo_trabajador.php?op=tabla_cargo_trabajador',
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
      // columna: #
      if (data[6] != '') { $("td", row).eq(6).addClass("text-center"); }
    },
		language: {
      lengthMenu: "_MENU_ ", search: "Buscar:",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      loadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...', paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" },
      emptyTable: "Ningún dato disponible en esta tabla", zeroRecords: "No se encontraron resultados",
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]]//Ordenar (columna,orden)
  }).DataTable();
}


//Función para guardar o editar
function guardar_editar_cargo_trabajador(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-c-t")[0]);
 
  $.ajax({
    url: "../ajax/cargo_trabajador.php?op=guardar_editar_cargo_trabajador",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Cargo Trabajador registrado correctamente.", "success");
	      tabla_c_t.ajax.reload(null, false);         
				limpiar_form_ct();
        $("#modal-agregar-c-t").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_ct").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_cargo_trabajadpr").css({"width": percentComplete+'%'});
          $("#barra_progress_cargo_trabajadpr").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#guardar_registro_ct").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled');
      $("#barra_progress_cargo_trabajadpr").css({ width: "0%",  });
      $("#barra_progress_cargo_trabajadpr").text("0%");
    },
    complete: function () {
      $("#barra_progress_cargo_trabajadpr").css({ width: "0%", });
      $("#barra_progress_cargo_trabajadpr").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_cargo_trabajador(idcargo_trabajador) {
  $(".tooltip").remove();
  $("#cargando-9-fomulario").hide();
  $("#cargando-10-fomulario").show();
  
  limpiar_form_ct();

  $("#modal-agregar-c-t").modal("show")

  $.post("../ajax/cargo_trabajador.php?op=mostrar_ct", { idcargo_trabajador: idcargo_trabajador }, function (e, status) {

    e = JSON.parse(e);  console.log(e);  

    if (e.status) {
      $("#idcargo_trabajador").val(e.data.idcargo_trabajador);
      $("#nombre_ct").val(e.data.nombre);        

      $("#cargando-9-fomulario").show();
      $("#cargando-10-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}


function eliminar_cargo_trabajador(idcargo_trabajador, nombre) {

  crud_eliminar_papelera(
    "../ajax/cargo_trabajador.php?op=desactivar",
    "../ajax/cargo_trabajador.php?op=eliminar", 
    idcargo_trabajador, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_c_t.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );

}


$(document).ready(function () {
  init_ct();
});


function mayus(e) {
  e.value = e.value.toUpperCase();
}


$(function () {

  $("#form-agregar-c-t").validate({
    rules: {
      nombre_ct: { required: true, maxlength: 60, } ,     // terms: { required: true },
    },
    messages: {
      nombre_cp: {  required: "Campo requerido.", },
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
      guardar_editar_cargo_trabajador(e);      
    },

  });
});