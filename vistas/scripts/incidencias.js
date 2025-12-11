var valor_id_categoria="";
var valor_prioridad="";
var estado_incidencia="";

// const choice_categoria = new Choices('#categoria',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

function init() {
  lista_de_items();
  
  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-incidencia").submit();  } });
  $(".btn-guardarfecha_fin").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-fecha_fin").submit();  } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/incidencias.php?op=listar_trabajador", '#id_trabajador', null);
  lista_select2("../ajax/incidencias.php?op=select2_cat_inc", "#categoria", null);  

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#id_trabajador").select2({placeholder: "Seleccionar trabajador", });
  $("#prioridad").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#categoria").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#estado_inc").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  //  select_trabajador();
   view_principal();
}

// function select_trabajador() {

//   const multipleCancelButton = new Choices(
//     '#id_trabajador', {
//       allowHTML: true,
//       removeItemButton: true,
//       noChoicesText:'No más hay opciones',
//       placeholder: true,
//       searchPlaceholderValue: 'Sin resultados',
//       renderSelectedChoices: 'auto',
//       loadingText: 'Cargando...',
//       noResultsText: 'No hay resultados',
//       itemSelectText: 'Presione para seleccionar',
//     }
//   );

//   $.getJSON("../ajax/incidencias.php?op=listar_trabajador", function (e, textStatus, jqXHR) {

//     e.data.forEach((val,key) => {
//       multipleCancelButton.setChoices([{
//         value: val.idpersona_trabajador,
//         label: val.nombre_razonsocial,
//       }]);
//     });
      
//   });
// }

//LISTAR  ALAMANCENES GENERALES
function lista_de_items() {

  $(".lista-items").html(`
  <li class="px-0 pt-0">
    <span class="fs-11 text-muted op-7 fw-semibold">Categorías</span>
  </li>
  <li class="active cursor-pointer">
  <a onclick="delay(function(){categoria('TODOS')}, 50 );">
    <div class="d-flex align-items-center">
      <span class="me-2 lh-1">
        <i class="ri-task-line align-middle fs-14"></i>
      </span>
      <span class="flex-fill text-nowrap">
        Todos
      </span>
      <!--<span class="badge bg-success-transparent rounded-pill">167</span>-->
    </div>
  </a>
</li>`);

  $.post("../ajax/incidencias.php?op=categorias_incidencias", function (e, status) {

    e = JSON.parse(e); console.log(e);
    // e.data.idtipo_tierra
    if (e.status == true) {
      var data_html = '';

      e.data.forEach((val, index) => {
        data_html = data_html.concat(`
        <li class="cursor-pointer">
        <a onclick="delay(function(){categoria('${val.idincidencia_categoria}')}, 50 );">
          <div class="d-flex align-items-center">
            <span class="me-2 lh-1">
              <i class="ri-star-line align-middle fs-14"></i>
            </span>
            <span class="flex-fill text-nowrap">
            ${val.nombre}
            </span>
          </div>
        </a>
      </li>
        
        `);
      });

      $(".lista-items").html(`
      <li class="px-0 pt-0">
        <span class="fs-11 text-muted op-7 fw-semibold">Categorías</span>
      </li>
      <li class="active cursor-pointer">
          <a onclick="delay(function(){categoria('TODOS')}, 50 );">
            <div class="d-flex align-items-center">
              <span class="me-2 lh-1">
                <i class="ri-task-line align-middle fs-14"></i>
              </span>
              <span class="flex-fill text-nowrap">
                Todos
              </span>
              <!--<span class="badge bg-success-transparent rounded-pill">167</span>-->
            </div>
          </a>
        </li> ${data_html}
      `);
    } else {
      ver_errores(e);
    }
  }).fail(function (e) { ver_errores(e); });
}

function categoria(idcategoria) { valor_id_categoria=""; valor_id_categoria=idcategoria; view_principal();}
function prioridad(prioridad) { valor_prioridad=""; valor_prioridad=prioridad; view_principal(); }
function estado(estado_inc) { estado_incidencia=""; estado_incidencia=estado_inc; view_principal(); }
// estado_incidencia

function view_principal() { 

  if (valor_id_categoria === null || valor_id_categoria === "") { valor_id_categoria='TODOS'; }

  if (valor_prioridad === null || valor_prioridad === "") { valor_prioridad='TODOS'; }

  if (estado_incidencia === null || estado_incidencia === "") { estado_incidencia='TODOS'; }

  $.post("../ajax/incidencias.php?op=view_incidencias",{ id_categoria: valor_id_categoria ,prioridad:valor_prioridad,estado_inc:estado_incidencia}, function (e, status) {

    e = JSON.parse(e); 

    if (e.status == true) {
      var data_html = '';
      var estado = ''; var color = ''; c_completed = '';
      var fecha_final = ''; var btn_finalizar =''; var margin_edit ='';
      if (e.data.length > 0) {
        
        e.data.forEach((val, index) => {

          if(val.estado_incidencia==='1'){ estado ='Abierto'; color='success'; c_completed='pending'; margin_edit='m-1';

          btn_finalizar=`<button class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light d-block" data-bs-toggle="modal" data-bs-target="#modal-agregar-fecha_fin" onclick="limpiar_formfecha_fin(); id_incidencia(${val.idincidencias});"><i class="ri-thumb-up-fill align-middle fw-normal"></i> Finalizar</button>`;
          
        } else { estado ='Cerrado'; color='danger'; c_completed='inprogress';  
        btn_finalizar=''; margin_edit='';
      }
          if (val.fecha_fin===null) { fecha_final='Por asignar'; } else { fecha_final=val.fecha_fin;}

          data_html = data_html.concat(`

          <div class="col-xl-4 task-card">

          <div class="card custom-card task-${c_completed}-card">
            <div class="card-body">
              <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                  <p class="fw-semibold mb-3 d-flex align-items-center"><a href="javascript:void(0);"><i class="ri-star-s-fill fs-16 me-1 text-warning"></i></a>${val.actividad}</p>
                  
                  <p class="mb-3">Asignado el : <span class="fs-12 mb-1 text-muted">${val.fecha_creacion}</span></p>
                  <p class="mb-3">Fecha prevista : <span class="fs-12 mb-1 text-muted">${fecha_final}</span></p>
                  <p class="mb-0">Asignado a :
                    <span class="avatar-list-stacked ms-1">
                    ${val.trabajadoreshtml}
                    </span>
                  </p>
                  <p class="mb-0 mt-3">Detalle</p>
                  <textarea class="form-control text-muted  mb-3" cols="30" rows="2" style=" border: none; outline: none;" >${val.actividad_detalle}</textarea>

                </div>
                <div>
                  <div class="btn-list ${margin_edit}">
                    <button class="btn btn-sm btn-icon btn-wave btn-primary-light" onclick="mostrar_editar(${val.idincidencias});"><i class="ri-edit-line"></i></button>
                    <button class="btn btn-sm btn-icon btn-wave btn-danger-light me-0" onclick="eliminar(${val.idincidencias},'${val.actividad}');"><i class="ri-delete-bin-line"></i></button>
                  </div>
                  ${btn_finalizar}
                  <span class="badge bg-${color}-gradient d-block cursor-pointer" style=" font-size: 13px;">${ estado}</span>
                </div>
              </div>
            </div>
          </div>

        </div>
          
          `);

        });

      } else{
        data_html =`
        <div class="card bg-white border-0" style="padding: 6px;">
          <div class="alert custom-alert1 alert-secondary">
            <div class="text-center px-5 pb-0">
              <svg class="custom-alert-icon svg-secondary" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
              </svg>
              <h5>NO HAY INCIDENCIAS</h5>
              <p style="margin-bottom: 1px !important;">Actualmente no hay incidencias registradas. </p>
              <p style="margin-bottom: 3px !important;">Por favor, revisa más tarde o contacta con el soporte técnico para obtener más información.</p>
              <div class="p-1 text-center">
              <img src="../assets/images/media/media-81.png" alt="" style=" width: 50%; ">
            </div>
            </div>
          </div>
        </div>      
        `;
      }

      $(".list_incidencias").html(`${data_html} `);
    } else {
      ver_errores(e);
    }
  }).fail(function (e) { ver_errores(e); });
}

function id_incidencia(id) { $("#id_incidenciaupdate").val(id);}

function limpiar_formfecha_fin(){ $("#addDatefin").val("");}


function guardarfechafin() {
  var formData = new FormData($("#form-agregar-fecha_fin")[0]);
  $.ajax({
    url: "../ajax/incidencias.php?op=guardar_fecha_fin",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");
          limpiar_formfecha_fin(); 
          $("#modal-agregar-fecha_fin").modal('hide');
          // view_form(1);
          view_principal();
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $(".guardarfecha_fin").html('Guardar Cambios').removeClass('disabled send-data');
    },
    beforeSend: function () {
      $(".guardarfecha_fin").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_gasto").css({ width: "0%", });
      $("#barra_progress_gasto div").text("0%");
      $("#barra_progress_gasto_div").show();
    },
    complete: function () {
      $("#barra_progress_gasto").css({ width: "0%", });
      $("#barra_progress_gasto div").text("0%");
      $("#barra_progress_gasto_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

function view_form(estado) { 

  console.log(estado);
  if (estado=='1') {
    $(".div_add_inc").hide();
    $(".btn_guargar_cancelar").hide();
    $(".btn_guargar_cancelar").hide();
    $(".div_view_info").show();
    $(".btn_guardar_new").show();
  }
  if (estado=='2') {
    $(".div_add_inc").show();
    $(".btn_guargar_cancelar").show();
    $(".btn_guargar_cancelar").show();
    $(".div_view_info").hide();
    $(".btn_guardar_new").hide();
    
    

    
  }{

  }
 }

function limpiar_form() {
  $("#actividad").val("");
  $("#id_trabajador").val(null).trigger("change"); 
  $("#prioridad").val(null).trigger("change"); 
  $("#categoria").val("").trigger("change"); 
  $("#unidad_medida").val("").trigger("change");  
  
  $("#adDate").val("");
  $("#actividad_detalle").val("");

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".form-select").removeClass('is-valid');
  $(".form-select").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function guardar_editar(e) {
  var formData = new FormData($("#form-agregar-incidencia")[0]);
  $.ajax({
    url: "../ajax/incidencias.php?op=guardar_editar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");

          limpiar_form(); 
          view_form(1);
          view_principal();

        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_gasto").html('Guardar Cambios').removeClass('disabled send-data');
    },
    beforeSend: function () {
      $("#guardar_registro_gasto").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_gasto").css({ width: "0%", });
      $("#barra_progress_gasto div").text("0%");
      $("#barra_progress_gasto_div").show();
    },
    complete: function () {
      $("#barra_progress_gasto").css({ width: "0%", });
      $("#barra_progress_gasto div").text("0%");
      $("#barra_progress_gasto_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

function eliminar(idincidencias,actividad) {
  console.log(idincidencias);
  Swal.fire({
    title: "¿Está Seguro de  Eliminar el registro?",
    html:`<b class="text-danger"><del>${actividad} </del></b>`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Eliminar!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("../ajax/incidencias.php?op=eliminar", { id_tabla: idincidencias }, function (e) {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Eliminado!", "El registro ha sido Eliminado.", "success");
          view_principal();
        } else {
          Swal.fire("Error!", e, "error");
        }
      });
    }
  });
}

function mostrar_editar(idincidencias) {

  // $("#addtask").modal('show');
  view_form(2);
  
  
  $(".class_fecha_fin").show();
  $(".class_estado_inc").show();
  $.post("../ajax/incidencias.php?op=mostrar",{ idincidencia: idincidencias}, function (e, status) {

    e = JSON.parse(e);  console.log(e);

    if (e.status == true) {
      $("#idincidencia").val(e.data.idincidencias);
      $("#actividad").val(e.data.actividad);
      $("#adDate").val(e.data.fecha_creacion); 
      $("#adDatefin").val(e.data.fecha_fin); 
      $("#prioridad").val(e.data.estado_revicion).trigger("change");
      $("#estado_inc").val(e.data.estado_incidencia).trigger("change"); 
      $("#categoria").val(e.data.idincidencia_categoria).trigger("change"); 
      $("#id_trabajador").val(e.data.trabadores).trigger("change");
      $("#actividad_detalle").val(e.data.actividad_detalle);

    } else {
      ver_errores(e);
    }
  }).fail(function (e) { ver_errores(e); });

  }


// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  T R A B A J A D O R    :::::::::::::::::::::::::::::::::::::::..
$(function () {

  $('#id_trabajador').on('change', function () { $(this).trigger('blur'); });
  $('#prioridad').on('change', function () { $(this).trigger('blur'); });
   $('#categoria').on('change', function () { $(this).trigger('blur'); });

  $("#form-agregar-incidencia").validate({
    ignore: "",
    rules: {

      actividad         : { required: true },
      id_trabajador     : { required: true },
      creacionfecha     : { required: true },
      prioridad         : { required: true },
      actividad_detalle : { required: true },
      categoria         : { required: true },
    },
    
    messages: {
      actividad         : { required: "Campo requerido" },
      id_trabajador     : { required: "Campo requerido" },
      serie_comprobante : { required: "Campo requerido" },
      creacionfecha     : { required: "Campo requerido" },
      prioridad         : { required: "Campo requerido" },
      actividad_detalle : { required: "Campo requerido" },
      categoria         : { required: "Campo requerido" },
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

  $("#form-agregar-fecha_fin").validate({
    ignore: "",
    rules: {

      fecha_fin         : { required: true },
    },
    
    messages: {
      fecha_fin         : { required: "Campo requerido" },
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
      guardarfechafin(e);
    },
  });
  
  $('#id_trabajador').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#prioridad').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#categoria').rules('add', { required: true, messages: { required: "Campo requerido" } });
});

$(document).ready(function () {
  init();
});
