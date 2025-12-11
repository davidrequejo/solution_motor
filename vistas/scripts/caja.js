var tabla_caja;

function init(){
	$("#guardar_registro_caja").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-caja").submit(); } });

  listar_caja();

}


function limpiar_form_caja(){
  
  $("#guardar_registro_caja").html('Guardar Cambios').removeClass('disabled');
  
  // Valores del formulario
  $("#idcaja").val("");
  $("#f_inicio").val("");
  $("#m_inicio").val("");
  $("#swit_auto").prop("checked", false);
  $("#sw_auto").val("");
  $("#f_cierre").val("");
  $("#m_cierre").val("");
  $("#alerta-cierre-auto").hide();
  $("#alerta-datos-cierre").hide();

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}


function show_hide_form(flag) {
  if (flag == 1) {
    $("#modal-registrar-cajaLabel1").text("Aperturar Caja");
    $(".c_f_inicio").show();
    $(".c_m_inicio").show();
    $(".c_automati").show();

    $(".c_f_cierre").hide();
    $(".c_m_cierre").hide();

  } else if (flag == 2) {
    $("#modal-registrar-cajaLabel1").text("Cerrar Caja");
    $(".c_f_inicio").hide();
    $(".c_m_inicio").hide();
    $(".c_automati").hide();

    $(".c_f_cierre").show();
    $(".c_m_cierre").show();
    
  }else if (flag == 3) {
    $("#modal-registrar-cajaLabel1").text("Editar Caja");
    $(".c_f_inicio").show();
    $(".c_m_inicio").show();
    $(".c_automati").show();

    $(".c_f_cierre").show();
    $(".c_m_cierre").show();

  }
}


function listar_caja(){
  tabla_caja = $('#tabla-caja').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_caja) { tabla_caja.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,8,9,10,11,4,5,7,6], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,8,9,10,11,4,5,7,6], }, title: 'Lista de caja', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,8,9,10,11,4,5,7,6], }, title: 'Lista de caja', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: '../ajax/caja.php?op=listar_tabla_caja',
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
      { targets: [7, 8, 9, 10, 11],  visible: false,  searchable: false,  },
    ],
  }).DataTable();

}

function mostrar_caja(idcaja){
  $(".tooltip").remove();
  $("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
  
  limpiar_form_caja();

  $.post("../ajax/caja.php?op=mostrar_caja", { idcaja: idcaja }, function (e, status) {
    e = JSON.parse(e); 
    if (e.status == true) {

      $("#modal-registrar-caja").modal("show");

      $("#idcaja").val(e.data.idcaja);
      $("#f_inicio").val(e.data.fecha_inicio_format);
      $("#m_inicio").val(e.data.monto_apertura);
      $("#f_cierre").val(e.data.fecha_fin_format == null ? moment().format('YYYY-MM-DDTHH:mm') :  e.data.fecha_fin_format );
      $("#m_cierre").val(e.data.monto_cierre);
      if(e.data.estado_monto_cierre == 'AUTOMATICO'){
        $("#swit_auto").prop("checked", true);
        $("#sw_auto").val(2);
      } else {
        $("#swit_auto").prop("checked", false);
        $("#sw_auto").val(1);
      }
      let colorMonto = e.data.monto_cierre < 0 ? 'red' : 'success';

      let lista = `
        <table style="width: 100%;">
          <tr>
            <td style="text-align: left;">Facturación</td>
            <td style="text-align: right;">${ formato_miles(e.data.facturacion)}</td>
          </tr>
          <tr>
            <td style="text-align: left;">Ingreso</td>
            <td style="text-align: right;">${formato_miles(e.data.ingresos)}</td>
          </tr>
          <tr>
            <td style="text-align: left;">Gasto Trabajador</td>
            <td style="text-align: right;">- ${formato_miles(e.data.gasto_trabajador)}</td>
          </tr>
          <tr>
            <td style="text-align: left;">Otros Gastos</td>
            <td style="text-align: right;">- ${formato_miles(e.data.otro_gasto)}</td>
          </tr>
          <tr>
            <td style="text-align: left;">Compra</td>
            <td style="text-align: right;">- ${formato_miles(e.data.compra)}</td>
          </tr>
          <tr style="border-top: 2px solid #198754;">
            <td style="text-align: left; font-weight: bold;">Total</td>
            <td style="text-align: right; font-weight: bold; color: ${colorMonto};">${formato_miles(e.data.monto_cierre)}</td>
          </tr>
          
        </table>
      `;
      $("#alerta-datos-cierre").html(lista).show();


      $("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
    } else { ver_errores(e); }
    
  }).fail( function(e) { ver_errores(e); } );
}

function guardar_editar_caja(e){
  $("#sw_auto").val($("#swit_auto").is(":checked") ? 2 : 1); //1 manual - 2 automatico
  var formData = new FormData($("#formulario-caja")[0]);

  $.ajax({
    url: "../ajax/caja.php?op=guardar_editar_caja",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Caja registrada correctamente.", "success");
	      tabla_caja.ajax.reload(null, false);         
				limpiar_form_caja();
        $("#modal-registrar-caja").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_caja").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },

  });

}

function eliminar_papelera_caja(idcaja, idusuario, fecha_inicio){

   $.post("../ajax/caja.php?op=ver_usuario", { idusuario: idusuario }, function (e, status) {
    e = JSON.parse(e); 

    if (e.status) {

      if(e.data != null){

        crud_eliminar_papelera(
          "../ajax/caja.php?op=desactivar",
          "../ajax/caja.php?op=eliminar", 
          idcaja, 
          "!Elija una opción¡", 
          `<b class="text-danger"><del>${fecha_inicio}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
          function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
          function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
          function(){ tabla_caja.ajax.reload(null, false); },
          false, 
          false, 
          false,
          false
        );

      }else{
        sw_warning('¡Advertencia!', "No tienes permiso para cambiar este registro, por favor comuniquese con su Administrador");
      }
        
    } else { ver_errores(e); }
    
  }).fail( function(e) { ver_errores(e); } );
}

function verificar_estado_caja(){

   $.post("../ajax/caja.php?op=ver_estado_caja", { }, function (e, status) {
    e = JSON.parse(e); 

    if (e.status) {

      if(e.data == null){
        $("#modal-registrar-caja").modal("show");
        limpiar_form_caja(); show_hide_form(1);

    }else{
        sw_warning('Caja Activa', "Una caja está en uso, por favor cierra la caja para poder aperturar uno nuevo");
      }
        
    } else { ver_errores(e); }
    
  }).fail( function(e) { ver_errores(e); } );
}


$(document).ready(function () {
  init();
});


document.addEventListener('DOMContentLoaded', function () {
  const switchAuto = document.getElementById('swit_auto');
  const alertaCierre = document.getElementById('alerta-cierre-auto');

  function actualizarAlertaCierre() {
    if (switchAuto.checked) {
      alertaCierre.style.display = 'block';
    } else {
      alertaCierre.style.display = 'none';
    }
  }

  // Ejecuta al cargar
  actualizarAlertaCierre();

  // Escucha los cambios en el switch
  switchAuto.addEventListener('change', actualizarAlertaCierre);
});



$(function () {

  $("#formulario-caja").validate({
    rules: {
      f_inicio: { required: true },
      m_inicio: { required: true },
      f_cierre: {
        required: function () {
          return $("#idcaja").val().trim() !== "";
        }
      },
    },
    messages: {
      f_inicio: {  required: "Campo requerido.", },
      m_inicio: {  required: "Campo requerido.", },
      f_cierre: {  required: "Campo requerido.", },
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
      guardar_editar_caja(e);      
    },

  });

});