var tabla_bancos;

function init_b(){

  tabla_principal_bancos();
  
  $("#guardar_registro_banco").on("click", function(e){if($(this).hasClass('send-data')==false){$("#submit-form-bancos").submit();}});
  // Formato para telefono
  $("[data-mask]").inputmask();
}


// abrimos el navegador de archivos
$("#imagen1_i").click(function () { $("#imagen1").trigger("click"); });
$("#imagen1").change(function (e) { addImage(e, $("#imagen1").attr("id"), "../assets/images/default/img_defecto_banco.png"); });


function limpiar_imagen_banco() {
  
  $("#imagen1").val("");
  $("#imagen1_i").attr("src", "../assets/images/default/img_defecto_banco.png");
  $("#imagen1_nombre").html("");

}


function limpiar_banco(){

  $("#guardar_registro_banco").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled');
  //Mostramos los Materiales
  $("#idbancos").val("");
  $("#nombre_b").val("");
  $("#alias").val("");
  $("#formato_cta").val("");
  $("#formato_cci").val("");
  $("#formato_detracciones").val("");

  limpiar_imagen_banco();

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}


//Función Listar
function tabla_principal_bancos() {

  tabla_bancos = $('#tabla-bancos').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_bancos) { tabla_bancos.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,5,6,7,8,9], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,5,6,7,8,9], }, title: 'Lista de bancos', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,5,6,7,8,9], }, title: 'Lista de bancos', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/bancos.php?op=tabla_principal_bancos',
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
      // if (data[6] != '') { $("td", row).eq(6).addClass("text-center"); }
    },
		language: {
      lengthMenu: "_MENU_ ", search: "Buscar:",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      loadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...', paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" },
      emptyTable: "Ningún dato disponible en esta tabla", zeroRecords: "No se encontraron resultados",
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]],//Ordenar (columna,orden)
    columnDefs: [
      { targets: [5], visible: false, searchable: false, },
      { targets: [6], visible: false, searchable: false, },
      { targets: [7], visible: false, searchable: false, }, 
      { targets: [8], visible: false, searchable: false, },
      { targets: [9], visible: false, searchable: false, },      
    ]
  }).DataTable();
}


//Función para guardar o editar
function guardar_editar_banco(e) {

  var formData = new FormData($("#form-agregar-bancos")[0]);
 
  $.ajax({
    url: "../ajax/bancos.php?op=guardar_editar_bancos",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Banco registrado correctamente.", "success");
	      tabla_bancos.ajax.reload(null, false);         
				limpiar_centro_poblado();
        $("#modal-agregar-bancos").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_banco").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_banco").css({"width": percentComplete+'%'});
          $("#barra_progress_banco").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#guardar_registro_banco").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled');
      $("#barra_progress_banco").css({ width: "0%",  });
      $("#barra_progress_banco").text("0%");
    },
    complete: function () {
      $("#barra_progress_banco").css({ width: "0%", });
      $("#barra_progress_banco").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}


function mostrar_banco(idbancos) {
  $(".tooltip").remove();
  $("#cargando-7-fomulario").hide();
  $("#cargando-8-fomulario").show();
  
  limpiar_banco();

  $("#modal-agregar-bancos").modal("show")

  $.post("../ajax/bancos.php?op=mostrar_banco", { idbancos: idbancos }, function (e, status) {

    e = JSON.parse(e);  console.log(e);  

    if (e.status) {
      $("#idbancos").val(e.data.idbancos);
      $("#nombre_b").val(e.data.nombre);        
      $("#alias").val(e.data.alias);
      $("#formato_cta").val(e.data.formato_cta);
      $("#formato_cci").val(e.data.formato_cci);
      $("#formato_detracciones").val(e.data.formato_detracciones); 

      if (e.data.icono != "") {
        $("#imagen1_i").attr("src", "../assets/modulo/bancos/" + e.data.icono);  
        $("#imagen1_actual").val(e.data.icono);
      }

      $("#cargando-7-fomulario").show();
      $("#cargando-8-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

function ver_imagen_banco(file, nombre) {
  $('.foto-banco').html(nombre);
  $(".tooltip").removeClass("show").addClass("hidde");
  $("#modal-ver-perfil-banco").modal("show");
  $('#perfil-banco').html(`<center><img src="../assets/modulo/bancos/${file}" alt="Perfil" width="100%"></center>`);
}


//Función para desactivar registros
function eliminar_banco(idbancos, nombre) {

  crud_eliminar_papelera(
    "../ajax/bancos.php?op=desactivar_banco",
    "../ajax/bancos.php?op=eliminar_banco", 
    idbancos, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_bancos.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );

}


$(document).ready(function () {
  init_b();
});


function mayus(e) {
  e.value = e.value.toUpperCase();
}


$(function () {

  $("#form-agregar-bancos").validate({
    rules: {
      nombre_b:     { required: true, minlength:2, maxlength:65},    
      alias:      { minlength:2, maxlength:65 },    
      formato_cta:{ required: true, minlength:8 },
      formato_cci:{ required: true, minlength:8 },
      formato_detracciones: { required: true, minlength:8 },
    },
    messages: {
      nombre_b:     { required: "Campo requerido. ", minlength:"MINIMO 2 carecteres", maxlength: "MÁXIMO 65 carecteres." },
      alias:      { minlength:"Ingrese almenos 2 carecteres", maxlength: "Máximo 65 carecteres" },
      formato_cta:{ required: "Campo requerido", minlength:"MINIMO 8 dígitos." },
      formato_cci:{ required: "Campo requerido", minlength:"MINIMO 8 dígitos." },
      formato_detracciones: { required: "Campo requerido", minlength:"MINIMO 8 dígitos." }
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
      guardar_editar_banco(e);      
    },

  });
});