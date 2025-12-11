var tbla_comentarioC;
var tabla_trabj;
var tabla_plan;
var tabl_preguntas_frecuentes;

//Función que se ejecuta al inicio
function init_landingPage() {
  
  $("#bloc_Recurso").addClass("menu-open");
  $("#mRecurso").addClass("active");

  tabla_de_comentariosC();
  tabla_principal_plan();
  tabla_preguntasFrecuentes();
  tabla_principal_trabj();

  $("#guardar_comentarioC").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-comentarioC").submit(); }  });
  $("#guardar_trabj").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-trabj").submit(); }  });
  $("#guardar_plan").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-plan").submit(); }  });
  $("#guardar_registro_preguntas").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-preguntas").submit(); }  });

}

/*==========================================================================================
----------------------------------- C L I E N T E S ---------------------------------------
==========================================================================================*/
function show_hide_form_comentarioC(flag) {
	if (flag == 1) {
		$("#div-tabla-comentarioC").show();
		$("#div-form-comentarioC").hide();
		$("#guardar_comentarioC").hide();
		$("#cancelar_comentarioC").hide();
    $("#footer-comentarioC").addClass("d-none");
		
	} else if (flag == 2) {
		$("#div-tabla-comentarioC").hide();
		$("#div-form-comentarioC").show();
		$("#guardar_comentarioC").show();
		$("#cancelar_comentarioC").show();
    $("#footer-comentarioC").removeClass("d-none");
	}
}

function limpiar_form_comentarioC() {

  $("#idpersona_cliente").val("");
  $("#nombre_comentarioC").val("");
  $("#centro_poblado").val("");
  $(".ql-editor").html("");
  $("#puntuacionc").val("");
  $("#fecha_comentarioc").val("");

  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function tabla_de_comentariosC(){
  tbla_comentarioC = $('#tabla-comentarioC').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbla_comentarioC) { tbla_comentarioC.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,7,8,9,10], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,7,8,9,10], }, title: 'Lista de comentarios cliente', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,7,8,9,10], }, title: 'Lista de comentarios cliente', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/landing_page.php?op=tabla_de_comentariosC',
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
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      if (data[1] != '') { $("td", row).eq(1).addClass("text-center"); }
      if (data[5] != '') { $("td", row).eq(5).addClass("text-center"); }
      if (data[6] != '') { $("td", row).eq(6).addClass("text-center"); }
    },
		language: {
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]],//Ordenar (columna,orden)
    columnDefs: [
      { targets: [7, 8, 9, 10], visible: false, searchable: false, }, 
    ],
  }).DataTable();

}

function guardar_y_editar_comentarioC(e) {

  transferirContenido_comentario();
  var formData = new FormData($("#form-agregar-comentarioC")[0]);
 
  $.ajax({
    url: "../ajax/landing_page.php?op=guardar_editar_comentarioC",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Comentario del Cliente registrado correctamente.", "success");
	      tbla_comentarioC.ajax.reload(null, false);         
				limpiar_form_comentarioC();
        show_hide_form_comentarioC(1);        
			}else{
				ver_errores(e);
			}
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_comentarioC").css({"width": percentComplete+'%'});
          $("#barra_progress_comentarioC").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#barra_progress_comentarioC").css({ width: "0%",  });
      $("#barra_progress_comentarioC").text("0%");
    },
    complete: function () {
      $("#barra_progress_comentarioC").css({ width: "0%", });
      $("#barra_progress_comentarioC").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function editar_estado_landing_ccomentario(idpersona_cliente, landing_estado) {
  let mensaje = "";
  
    if (landing_estado == 0) {
      mensaje = "Este comentario es visible en la página principal";
    } else if (landing_estado == 1) {
      mensaje = "Este comentario está oculto";
    }

    $.post("../ajax/landing_page.php?op=editar_comentarioVisible", { idpersona_cliente: idpersona_cliente, landing_estado: landing_estado }, function (e) {
      try {
        e = JSON.parse(e);
        if (e.status == true) {
          Swal.fire({
            title: "Actualizado",
            text: mensaje,
            icon: "success"
          });
          tbla_comentarioC.ajax.reload(null, false);
        } else {
          ver_errores(e);
        }
      } catch (e) {
        ver_errores(e);
      }
    }).fail(function (e) {
      ver_errores(e);
    });
}

function mostrar_cliente(idpersona_cliente){

  $(".tooltip").remove();
  $("#guardar_comentarioC").hide();
  $("#cargando-9-fomulario").hide();
  $("#cargando-10-fomulario").show();
  
  limpiar_form_comentarioC();

  show_hide_form_comentarioC(2);
  estrellas();

  $.post("../ajax/landing_page.php?op=mostrar_cliente", { idpersona_cliente: idpersona_cliente }, function (e, status) {

    e = JSON.parse(e);  console.log(e);
    
    var estrellas_seleccionadas = e.data.landing_puntuacion;  

    if (e.status == true) {
      $("#idpersona_cliente").val(e.data.idpersona_cliente);
      $("#nombre_cliente").val(e.data.nombre_completo);        
      $("#centro_poblado").val(e.data.centro_poblado); 
      $("#editor2 .ql-editor").html(e.data.landing_descripcion);  
      $("#puntuacionc").val(estrellas_seleccionadas);

      // Mostrar fecha formato ISO
      if (e.data.landing_fecha !== null) {
        var fechaParts = e.data.landing_fecha.split('/');
        var fechaISO = fechaParts[2] + '-' + fechaParts[1] + '-' + fechaParts[0];
        $("#fecha_comentarioc").val(fechaISO);
      } else {
        $("#fecha_comentarioc").val("");
      }

      // Mostrar puntuación formato estrellas
      $(".puntuacion-star").each(function() {
        if ($(this).attr("data-value") <= estrellas_seleccionadas) {
          $(this).removeClass("ri-star-line").addClass("ri-star-fill");
        } else {
          $(this).removeClass("ri-star-fill").addClass("ri-star-line");
        }
      });

      $("#cargando-9-fomulario").show();
      $("#cargando-10-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

function transferirContenido_comentario(){
  var comentario = document.querySelector('#editor2 .ql-editor').innerHTML;  // Obtiene el contenido HTML del div dentro de #editor1
  document.getElementById('descripcion_comentario').value = comentario;  // Coloca el contenido en el textarea
}

function estrellas(){
  $(".puntuacion-star").click(function() {
    var value = $(this).attr("data-value");
    $(".puntuacion-star").each(function() {
        if ($(this).attr("data-value") <= value) {
            $(this).removeClass("ri-star-line").addClass("ri-star-fill");
        } else {
            $(this).removeClass("ri-star-fill").addClass("ri-star-line");
        }
    });
    $("#puntuacionc").val(value);
});
}




/*==========================================================================================
--------------------------------T R A B A J A D O R E S -----------------------------------
==========================================================================================*/
function show_hide_form_trabj(flag) {
	if (flag == 1) {
		$("#div-tabla-trabj").show();
		$("#div-form-trabj").hide();
		$("#guardar_trabj").hide();
		$("#cancelar_trabj").hide();
    $("#footer-trabj").addClass("d-none");
		
	} else if (flag == 2) {
		$("#div-tabla-trabj").hide();
		$("#div-form-trabj").show();
		$("#guardar_trabj").show();
		$("#cancelar_trabj").show();
    $("#footer-trabj").removeClass("d-none");
	}
}

function limpiar_form_trabj() {

  $("#idpersona_trabajador").val("");
  $("#nombre_trabj").val("");
  $("#cargo_trabj").val("");
  $(".ql-editor").html("");

  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function tabla_principal_trabj() {

  tabla_trabj = $('#tabla-trabj').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_trabj) { tabla_trabj.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,5,6,7], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,5,6,7], }, title: 'Lista de trabajadores', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,5,6,7], }, title: 'Lista de trabajadores', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/landing_page.php?op=tabla_de_trabj',
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
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]],//Ordenar (columna,orden)
    columnDefs: [
      { targets: [5, 6, 7], visible: false, searchable: false, }, 
    ],
  }).DataTable();
}

function guardar_y_editar_trabj(e) {
  transferirContenido_trabj();

  var formData = new FormData($("#form-agregar-trabj")[0]);
 
  $.ajax({
    url: "../ajax/landing_page.php?op=guardar_editar_trabj",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Trabajador registrado correctamente.", "success");
	      tabla_trabj.ajax.reload(null, false);         
				limpiar_form_trabj();
        show_hide_form_trabj(1);        
			}else{
				ver_errores(e);
			}
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_trabj").css({"width": percentComplete+'%'});
          $("#barra_progress_trabj").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#barra_progress_trabj").css({ width: "0%",  });
      $("#barra_progress_trabj").text("0%");
    },
    complete: function () {
      $("#barra_progress_trabj").css({ width: "0%", });
      $("#barra_progress_trabj").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_trabajador(idpersona_trabajador){
  $(".tooltip").remove();
  $("#guardar_trabj").hide();
  $("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
  
  limpiar_form_trabj();

  show_hide_form_trabj(2)

  $.post("../ajax/landing_page.php?op=mostrar_trabj", { idpersona_trabajador: idpersona_trabajador }, function (e, status) {

    e = JSON.parse(e);  console.log(e);  

    if (e.status == true) {
      $("#idpersona_trabajador").val(e.data.idpersona_trabajador);
      $("#nombre_trabj").val(e.data.nombre_completo);        
      $("#cargo_trabj").val(e.data.cargo); 
      $("#editor1 .ql-editor").html(e.data.landing_descripcion);

      $("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

function editar_estado_landing_trabj(idpersona_trabajador, landing_estado) {
  let mensaje = "";
  
    if (landing_estado == 0) {
      mensaje = "Este Trabajador es visible en la página principal";
    } else if (landing_estado == 1) {
      mensaje = "Este Trabajador está oculto";
    }

    $.post("../ajax/landing_page.php?op=editar_trabjVisible", { idpersona_trabajador: idpersona_trabajador, landing_estado: landing_estado }, function (e) {
      try {
        e = JSON.parse(e);
        if (e.status == true) {
          Swal.fire({
            title: "Actualizado",
            text: mensaje,
            icon: "success"
          });
          tabla_trabj.ajax.reload(null, false);
        } else {
          ver_errores(e);
        }
      } catch (e) {
        ver_errores(e);
      }
    }).fail(function (e) {
      ver_errores(e);
    });

  
}

function transferirContenido_trabj() {
  var contenido_trabj = document.querySelector('#editor1 .ql-editor').innerHTML;  // Obtiene el contenido HTML del div dentro de #editor1
  document.getElementById('descripcion_trabj').value = contenido_trabj;  // Coloca el contenido en el textarea
}



/*==========================================================================================
-------------------------------------------P L A N E S-------------------------------------
==========================================================================================*/

function show_hide_form_plan(flag) {
	if (flag == 1) {
		$("#div-tabla-plan").show();
		$("#div-form-plan").hide();
		$("#guardar_plan").hide();
		$("#cancelar_plan").hide();
    $("#footer-plan").addClass("d-none");
		
	} else if (flag == 2) {
		$("#div-tabla-plan").hide();
		$("#div-form-plan").show();
		$("#guardar_plan").show();
		$("#cancelar_plan").show();
    $("#footer-plan").removeClass("d-none");
	}
}

//Función limpiar_form
function limpiar_form_plan() {
  //Mostramos los Materiales
  $("#idplan").val("");
  $("#nombre_plan").val("");
  $("#costo_plan").val("");
  $(".ql-editor").html("");

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

//Función Listar
function tabla_principal_plan() {

  tabla_plan = $('#tabla-plan').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_plan) { tabla_plan.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3], }, title: 'Lista de planes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3], }, title: 'Lista de planes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/landing_page.php?op=tabla_principal_plan',
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
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]]//Ordenar (columna,orden)
  }).DataTable();
}

//Función para guardar o editar
function guardar_y_editar_plan(e) {
  transferirContenido_plan();

  var formData = new FormData($("#form-agregar-plan")[0]);
 
  $.ajax({
    url: "../ajax/landing_page.php?op=guardar_editar_plan",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Plan registrado correctamente.", "success");
	      tabla_plan.ajax.reload(null, false);         
				limpiar_form_plan();
        show_hide_form_plan(1);        
			}else{
				ver_errores(e);
			}
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_plan").css({"width": percentComplete+'%'});
          $("#barra_progress_plan").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#barra_progress_plan").css({ width: "0%",  });
      $("#barra_progress_plan").text("0%");
    },
    complete: function () {
      $("#barra_progress_plan").css({ width: "0%", });
      $("#barra_progress_plan").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_plan(idplan) {
  $(".tooltip").remove();
  $("#guardar_plan").hide();
  $("#cargando-3-fomulario").hide();
  $("#cargando-4-fomulario").show();
  
  limpiar_form_plan();

  show_hide_form_plan(2)

  $.post("../ajax/plan.php?op=mostrar_plan", { idplan: idplan }, function (e, status) {

    e = JSON.parse(e);  console.log(e);  

    if (e.status == true) {
      $("#idplan").val(e.data.idplan);
      $("#nombre_plan").val(e.data.nombre);        
      $("#costo_plan").val(e.data.costo);   
      $(".ql-editor").html(e.data.landing_caracteristica);   

      $("#cargando-3-fomulario").show();
      $("#cargando-4-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

function editar_estado_landing_plan(idplan, landing_estado) {
  let mensaje = "";
  
    if (landing_estado == 0) {
      mensaje = "Este Plan es visible en la página principal";
    } else if (landing_estado == 1) {
      mensaje = "Este Plan está oculto";
    }

    $.post("../ajax/landing_page.php?op=editar_planVisible", { idplan: idplan, landing_estado: landing_estado }, function (e) {
      try {
        e = JSON.parse(e);
        if (e.status == true) {
          Swal.fire({
            title: "Actualizado",
            text: mensaje,
            icon: "success"
          });
          tabla_plan.ajax.reload(null, false);
        } else {
          ver_errores(e);
        }
      } catch (e) {
        ver_errores(e);
      }
    }).fail(function (e) {
      ver_errores(e);
    });

  
}

function transferirContenido_plan() {
  var contenido = document.querySelector('#editor .ql-editor').innerHTML;  // Obtiene el contenido HTML del div dentro de #editor
  document.getElementById('caracteristicas').value = contenido;  // Coloca el contenido en el textarea
}



/*==========================================================================================
-----------------------P R E G U N T A S   F R E C U E N T E S-----------------------------
==========================================================================================*/

function limpiar_form_preguntas(){
  $("#idpreguntas_frecuentes").val("");

  $("#pregunta_pf").val("");

  $("#respuesta_pf").val("");

}

function tabla_preguntasFrecuentes(){
  tabl_preguntas_frecuentes = $('#tabla-preguntas-frecuentes').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabl_preguntas_frecuentes) { tabl_preguntas_frecuentes.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3], }, title: 'Lista de preguntas frecuentes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3], }, title: 'Lista de preguntas frecuentes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/landing_page.php?op=tabla_principal_PregFerct',
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
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      if (data[4] != '') { $("td", row).eq(4).addClass("text-center"); }
    },
		language: {
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]]//Ordenar (columna,orden)
  }).DataTable();

}

function guardar_y_editar_pregFrec(e) {
  var formData = new FormData($("#form-agregar-preguntas")[0]);
 
  $.ajax({
    url: "../ajax/landing_page.php?op=guardar_y_editar_pregFrec",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Pregunta Frecuente registrado correctamente.", "success");
	      tabl_preguntas_frecuentes.ajax.reload(null, false);         
				limpiar_form_preguntas();
        $("#modal-agregar-preguntas").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_preguntas").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_pregunta").css({"width": percentComplete+'%'});
          $("#barra_progress_pregunta").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#guardar_registro_pregunta").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled');
      $("#barra_progress_pregunta").css({ width: "0%",  });
      $("#barra_progress_pregunta").text("0%");
    },
    complete: function () {
      $("#barra_progress_pregunta").css({ width: "0%", });
      $("#barra_progress_pregunta").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_pregFrec(idpreguntas_frecuentes) {
  $(".tooltip").remove();
  $("#cargando-5-fomulario").hide();
  $("#cargando-6-fomulario").show();
  
  limpiar_form_preguntas();

  $("#modal-agregar-preguntas").modal("show")

  $.post("../ajax/landing_page.php?op=mostrar_pregFrec", { idpreguntas_frecuentes: idpreguntas_frecuentes }, function (e, status) {

    e = JSON.parse(e);  console.log(e);  

    if (e.status == true) {
      $("#idpreguntas_frecuentes").val(e.data.idpreguntas_frecuentes);
      $("#pregunta_pf").val(e.data.pregunta);        
      $("#respuesta_pf").val(e.data.respuesta)    

      $("#cargando-5-fomulario").show();
      $("#cargando-6-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_pregFrec(idpreguntas_frecuentes) {

  crud_eliminar_papelera(
    "../ajax/landing_page.php?op=desactivar",
    "../ajax/landing_page.php?op=eliminar", 
    idpreguntas_frecuentes, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>Pregunta / Respuesta</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabl_preguntas_frecuentes.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );

}


/*==========================================================================================
-------------------------- F O R M   V A L I D A T I O N ----------------------------------
==========================================================================================*/

$(document).ready(function () {
  init_landingPage();
});

$(function () {

  $("#form-agregar-comentarioC").validate({
    rules: {
      nombre_cliente: { required: true,  maxlength: 60,  },
      centro_poblado: { required: true },
      fecha_comentarioc: { required: true }
    },
    messages: {
      nombre_cliente: {  required: "Campo requerido.", },
      centro_poblado: {  required: "Campo requerido.", },
      fecha_comentarioc: {  required: "Campo requerido.", },
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
      guardar_y_editar_comentarioC(e);      
    },

  });

  $("#form-agregar-trabj").validate({
    rules: {
      nombre_trabj: { required: true,  maxlength: 60,  },
      cargo_trabj: { required: true }
    },
    messages: {
      nombre_trabj: {  required: "Campo requerido.", },
      cargo_trabj: {  required: "Campo requerido.", },
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
      guardar_y_editar_trabj(e);      
    },

  });


  $("#form-agregar-plan").validate({
    rules: {
      nombre_plan: { required: true,  maxlength: 60,  },
      costo_plan: { required: true }
    },
    messages: {
      nombre_plan: {  required: "Campo requerido.", },
      costo_plan: {  required: "Campo requerido.", },
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
      guardar_y_editar_plan(e);      
    },

  });


  $("#form-agregar-preguntas").validate({
    rules: {
      pregunta_pf: { required: true,  maxlength: 100,  } ,     // terms: { required: true },
      respuesta_pf: { required: true }      // terms: { required: true },
    },
    messages: {
      pregunta_pf: {  required: "Campo requerido.", },
      respuesta_pf: {  required: "Campo requerido.", },
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
      guardar_y_editar_pregFrec(e);      
    },

  });
});


function fecha_actual(){
  // Obtener la fecha actual
  var fechaActual = new Date();

  // Formatear la fecha como YYYY-MM-DD (compatible con input type="date")
  var formattedDate = fechaActual.toISOString().split('T')[0];

  // Asignar la fecha formateada al campo de entrada
  $("#fecha_comentarioc").val(formattedDate);
}
