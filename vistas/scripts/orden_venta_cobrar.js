var form_validate_facturacion;

// const choice_tipo_documento = new Choices('#cli_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

// const choice_distrito       = new Choices('#cli_distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// var choice_centro_poblado = new Choices('#cli_centro_poblado',  { shouldSort: false, removeItemButton: true,noResultsText: 'No hay resultados.', } );

function init() {

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardarOrdenPago").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-PagarOrden").submit(); } else { toastr_info('Espere!!', 'Por favor sea pasiente se estan procesando los datos...'); } });
  $("#guardar_registro_nuevo_cliente").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-nuevo-cliente").submit(); } else { toastr_info('Espere!!', 'Por favor sea pasiente se estan procesando los datos...'); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/orden_venta_cobrar.php?op=select2_banco", '#f_metodo_pago_1', null, 'charge_f_metodo_pago_1');

  lista_select2("../ajax/orden_venta_cobrar.php?op=select2_cliente", '#f_idpersona_cliente', null, '.charge_f_idpersona_cliente');

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_centro_poblado", choice_centro_poblado, null);

  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#cli_distrito', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#f_idpersona_cliente").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_metodo_pago_1").select2({ templateResult: templateBanco, templateSelection: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#cli_distrito").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});
  $("#cli_tipo_documento").select2({theme: "bootstrap4",dropdownParent: $('#modal-agregar-nuevo-cliente') , placeholder: "Seleccione",allowClear: true,});

  show_hide_form(1);
}


function templateBanco (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span><img src="${baseUrl}" class="img-circle mr-2 w-25px" ${onerror} />${state.text}</span>`);
  return $state;
};

function limpiar_venta_cobro(){
  $("#btn-guardarOrdenPago").html('Guardar Cambios').removeClass('disabled');

  $('#f_tipo_comprobante12').prop('checked', true).focus().trigger('change'); 
  $("#f_idpersona_cliente").val('').trigger('change'); 
  $("#f_metodo_pago_1").val('EFECTIVO').trigger('change'); 

  
  $('#html-metodos-de-pagos').html('');
  $("#f_total_recibido").val(0);
  $("#f_total_vuelto").val(0);
  $("#f_mp_serie_comprobante").val('');

  $('#f_total_recibido_1').val('');

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function show_hide_form(flag) {

  if (flag == 1) {

    $(".Div_PagarOrden").hide();
    $(".Div_ListarOrden").show();

  } else if (flag == 2) {

    $(".Div_PagarOrden").show();
    $(".Div_ListarOrden").hide();
    
  }
}

function Actualizar_data_Orden(){

  var filtro_fecha_i        = $("#filtro_fecha_i").val();
  var filtro_fecha_f        = $("#filtro_fecha_f").val();  

  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();

  // filtro de fechas
  if (filtro_fecha_i        == '' || filtro_fecha_i         == 0 || filtro_fecha_i        == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  if (filtro_fecha_f        == '' || filtro_fecha_f         == 0 || filtro_fecha_f        == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }


  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ...`);

  listar_ordenesCobro(filtro_fecha_i, filtro_fecha_f);
}

function listar_ordenesCobro(filtro_fecha_i,filtro_fecha_f){

  
  $.getJSON(`../ajax/orden_venta_cobrar.php?op=listarOrdenesCobrar&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}`, {}, function (e, textStatus, jqXHR) {

    if (e.status == true) { 
      $(".listarOrdenesCobrar").html(""); // Limpiamos la lista de ordenes

      e.data.forEach((val, key) => {

        // Supongamos que `val` es tu objeto con los datos
      let detallesHTML = '';

      $.each(val.detalleOrden, function(i, item) {
        detallesHTML += `✔️ ${item.pr_nombre} | S/ ${parseFloat(item.subtotal).toFixed(2)}\n`;
      });

      let tiempoTranscurrido = calcularTiempoTranscurrido(val.fecha_emision);

      $(".listarOrdenesCobrar").append(`
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 col-xxl-3">
          <div class="card custom-card">
            <div class="card-header d-block py-2">
              <div class="d-sm-flex d-block align-items-center">
                <div class="me-2">
                  <span class="avatar bg-light avatar-md mb-1">
                    <img src="../../assets/modulo/persona/perfil/${val.user_en_atencion_foto}" alt="">
                  </span>
                </div>
                <div class="flex-fill">
                  <a href="javascript:void(0)">
                    <span class="fs-14 fw-semibold">${val.user_en_atencion_nombre}</span>
                  </a>
                  <span class="d-block text-success">--</span>
                </div>
                <div class="text-sm-center">
                  <span class="fs-14 fw-semibold">N° Orden:</span>
                  <span class="d-sm-block">${val.serie_y_numero_comprobante}</span>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class=" align-items-center">
                <div class="">
                  <p class="mb-1 fw-semibold">${val.nombre_razonsocial}</p>
                  <textarea class="textarea_datatable bg-light" readonly style="height: 62px; width: 100%;">${detallesHTML.trim()}</textarea>
                </div>
              </div>
            </div>
            <div class="card-footer d-sm-flex d-block align-items-center justify-content-between">
              <div><span class="badge bg-success-transparent">${tiempoTranscurrido }</span></div>
              <div class="mt-sm-0 mt-2">
                <button class="btn btn-sm btn-danger-light btn-anular" onclick="Anular_orden(${val.idventa});">Anular</button>
                <button class="btn btn-sm btn-success-light btn-cobrar" onclick="Pagar_orden(${val.idventa},'${parseFloat(val.venta_total).toFixed(2)}',${val.idpersona_cliente});">Cobrar :<span class="fs-14 "> S/ ${parseFloat(val.venta_total).toFixed(2)}</span></button>
              </div>
            </div>
          </div>
        </div>
      `);
              
      }); 


      
    } else {
      ver_errores(e);
    }

  }).fail( function(e) { ver_errores(e); } );  


}

function calcularTiempoTranscurrido(fechaEmisionStr) {
  const fechaEmision = new Date(fechaEmisionStr);
  const ahora = new Date();
  const diffMs = ahora - fechaEmision;

  const diffMin = Math.floor(diffMs / 60000);
  const diffHoras = Math.floor(diffMin / 60);
  const diffDias = Math.floor(diffHoras / 24);

  if (diffMin < 1) return 'Justo ahora';
  if (diffMin < 60) return `Hace ${diffMin} min`;
  if (diffHoras < 24) return `Hace ${diffHoras} horas`;
  return `Hace ${diffDias} días`;
}

function Pagar_orden(idventa,total_apagar,idpersona_cliente) {
  $("#idventa").val(idventa);
  $("#f_venta_total").val(total_apagar);
  $("#f_idpersona_cliente").val(idpersona_cliente).trigger("change");  
  show_hide_form(2);
}

function Anular_orden(idventa) {

  Swal.fire({
    title: "¿Está seguro que deseas ANULAR LA ORDEN?",
    html: "No podrás Recuperar la Orden!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Anular!",
    preConfirm: (input) => {
      return fetch(`../ajax/orden_venta_cobrar.php?op=anularOrden&idventa=${idventa}`, {
        method: 'POST', // or 'PUT'     
      }).then(response => {
        //console.log(response);
        if (!response.ok) { throw new Error(response.statusText) }
        return response.json();
      }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); });
    },
    showLoaderOnConfirm: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (result.value.status == true){
        Swal.fire("Correcto!", "Venta guardada correctamente", "success");
        filtros();
      }  else {
        ver_errores(result.value);
      }      
    }
  });  
}

function guardar_editar_cobroOrden(e){ 

  renombrarInputsArrayContenedor('.f_mp_comprobante_validar', 'name', 'f_mp_comprobante'); // Ajustamos la numeracion de Array en los inputs: File

  var formData = new FormData($("#form-PagarOrden")[0]);

  // Seleccionar los divs con la clase f_mp_comprobante_validar
  const divs = document.querySelectorAll("div.f_mp_comprobante_validar");
  const emptyFileKeys = [];

  // Iterar sobre los divs para encontrar los inputs dentro
  divs.forEach((div) => {
    const input = div.querySelector("input[type='file']"); // Encontrar el input dentro del div

    if (input == '' || input == null) { } else{
      if (input.name == '' || input.name == null) { } else{
        const inputName = input.name; // Obtener el nombre del input
        const fileList = input.files; // Obtener los archivos seleccionados

        // Verificar si no tiene archivos seleccionados
        if (fileList.length === 0) {
          emptyFileKeys.push(inputName); // Registrar el nombre para procesamiento
        }
      }       
    }
  });

  console.log("Claves vacías:", emptyFileKeys);

  // Eliminar y reemplazar entradas vacías en FormData
  emptyFileKeys.forEach((key) => {
    formData.delete(key); // Eliminar la entrada original
    formData.append(key, ""); // Agregar un valor vacío como texto
  });

  $.ajax({
    url: "../ajax/orden_venta_cobrar.php?op=guardar_editar_OrdenVenta",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); 
        if (e.status == true) {
          
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");          
          show_hide_form(1);
          limpiar_venta_cobro();
          filtros();

          var screenHeight = window.innerHeight; 
          var height_calculado = screenHeight > 700 ? screenHeight * 0.75 : screenHeight * 0.65;

          var rutacarpeta = "../reportes/TicketFormatoPDF.php?id=" + e.data;
          $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);
          $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: ${height_calculado}px;" marginwidth="1" src=""> </iframe>`);
          $("#modal-imprimir-comprobante").modal("show");

        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_venta").html('Guardar Cambios').removeClass('disabled send-data');
    },
    xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					$("#barra_progress_orden_pago").css({ "width": percentComplete + '%' });
					$("#barra_progress_orden_pago div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
    beforeSend: function () {
      $("#guardar_registro_venta").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_orden_pago").css({ width: "0%", });
      $("#barra_progress_orden_pago div").text("0%");
      $("#barra_progress_orden_pago_div").show();
    },
    complete: function () {
      $("#barra_progress_orden_pago").css({ width: "0%", });
      $("#barra_progress_orden_pago div").text("0%");
      $("#barra_progress_orden_pago_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}



// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  M O S T R A R   S E R I E S                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function ver_series_comprobante(input) {

  var tipo_comprobante = $(input).val() == ''  || $(input).val() == null ? '' : $(input).val(); console.log(tipo_comprobante);  

    // VALIDANDO SEGUN: TIPO DE COMPROBANTE
  if (form_validate_facturacion) { // FORM-VALIDATE

    $('#f_tipo_comprobante_hidden').val(tipo_comprobante);
    if ( tipo_comprobante == '01') {   
      $("#f_idsunat_c01").val(2); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });       
      $("#f_total_recibido_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });    

    } else if ( tipo_comprobante == '03') {
      $("#f_idsunat_c01").val(3); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago_1").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_total_recibido_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });

    } else if ( tipo_comprobante == '12') {
      $("#f_idsunat_c01").val(12);  
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago_1").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_total_recibido_1").rules('add', { required: true, messages: { required: 'Campo requerido' } });
    }
  }
    

  $.getJSON("../ajax/orden_venta_cobrar.php?op=select2_series_comprobante", { tipo_comprobante: tipo_comprobante },  function (e, status) {    
    if (e.status == true) {      
      $("#f_serie_comprobante").html(e.data);
      $(".f_charge_serie_comprobante").html('');
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );

  cambio_de_tipo_comprobante = tipo_comprobante;
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  METODO DE PAGO                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════


var count_mp = 2;
function agregar_new_mp( es_editar = false, metodo_pago = null, monto = '', cod_baucher = '', img_baucher = ' ' ) {
  var id_cant = contarDivsArray('.f_metodo_pago_validar', 1);
  $('#html-metodos-de-pagos').append(`
    <div class="col-lg-12 htlm-mp-lista-${count_mp}">
      <div class="row">
        <div class="col-lg-12 text-center pt-1">
          <button type="button" class="btn btn-danger-light label-btn btn-sm rounded-pill btn-wave" onclick="eliminar_mp(${count_mp})"> <i class="bi bi-trash3 label-btn-icon me-2"></i> <span class="ms-4">Eliminar</span>  </button>
        </div>
       
        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 pt-3">
          <div class="form-group">
            <label for="f_metodo_pago_${count_mp}" class="form-label">
              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_metodo_pago(${count_mp});" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
              Método de pago
              <span class="charge_f_metodo_pago_${count_mp}"></span>
            </label>
            <select class="form-control form-control-sm f_metodo_pago_validar" name="f_metodo_pago[${id_cant}]" id="f_metodo_pago_${count_mp}" onchange="capturar_pago_venta(${count_mp});">
              <!-- Aqui se listara las opciones -->
            </select>
          </div>
        </div>                                 

        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3">
          <div class="form-group">
            <label for="f_total_recibido_${count_mp}" class="form-label">Monto a pagar</label>
            <input type="number" name="f_total_recibido[${id_cant}]" id="f_total_recibido_${count_mp}" class="form-control form-control-sm f_total_recibido_validar" value="${monto}" onClick="this.select();" onchange="calcular_vuelto(${count_mp});" onkeyup="calcular_vuelto(${count_mp});" placeholder="Ingrese monto a pagar.">
          </div>
        </div>        

        <div class="col-12" id="content-metodo-pago-${count_mp}">
          <div class="row">
            <!-- Código de Baucher -->
            <div class="col-sm-6 col-lg-6 col-xl-6 pt-3">
              <div class="form-group">
                <label for="f_mp_serie_comprobante_${count_mp}">Código de Baucher <span class="span-code-baucher-pago-${count_mp}"></span> </label>
                <input type="text" name="f_mp_serie_comprobante[]" id="f_mp_serie_comprobante_${count_mp}" class="form-control" value="${cod_baucher}" onClick="this.select();" placeholder="Codigo de baucher" />
              </div>
            </div>
            <!-- Baucher -->
            <div class="col-sm-6 col-lg-6 col-xl-6 pt-3 hidden">
              <div class="form-group">
                <input type="file" class="multiple-filepond f_mp_comprobante_validar" name="f_mp_comprobante[${id_cant}]" id="f_mp_comprobante_${count_mp}" data-allow-reorder="true" data-max-file-size="3MB" accept="image/*, application/pdf">
                <input type="hidden" name="f_mp_comprobante_old_${count_mp}" id="f_mp_comprobante_old_${count_mp}">
              </div>
            </div>
          </div>
        </div>

        <div class="col-12"> <div class="border-bottom border-block-end-dashed py-2"></div></div>
      </div>                                  
    </div>
  `);

  lista_select2("../ajax/orden_venta_cobrar.php?op=select2_banco", `#f_metodo_pago_${count_mp}`, metodo_pago, `charge_f_metodo_pago_${count_mp}`);  
  $(`#f_metodo_pago_${count_mp}`).select2({  templateResult: templateBanco, templateSelection: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });  

  // reglas de validación     
  $(`.f_metodo_pago_validar`).each(function(e) { $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } });  });
  $(`.f_total_recibido_validar`).each(function(e) { $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } });  });
  
  if (es_editar == true) {
    file_pond_mp_comprobante_2mas = FilePond.create(document.querySelector(`#f_mp_comprobante_${count_mp}`), FilePond_Facturacion_LabelsES ).addFile(`../assets/modulo/facturacion/ticket/${img_baucher}`, { index: 0 });;
  }else{
    file_pond_mp_comprobante_2mas = FilePond.create(document.querySelector(`#f_mp_comprobante_${count_mp}`), FilePond_Facturacion_LabelsES );
  }
  
  filePondInstances.push(file_pond_mp_comprobante_2mas); // Guarda la instancia en el arreglo
  
  count_mp++;

  if (form_validate_facturacion) { $("#form-PagarOrden").valid();}
}

function eliminar_mp(id) {
  $(`.htlm-mp-lista-${id}`).css({
    transition: 'transform 0.5s ease, opacity 0.5s ease',   // Transición suave para el cambio de tamaño y opacidad
    opacity: 0,                                            // Reduce la opacidad para desaparecer
    transform: 'scale(0.1)',                                 // Reduce el tamaño uniformemente en ambas direcciones (X y Y)
    transformOrigin: 'bottom'                               // El punto de escala es desde la parte inferior
  });

  setTimeout(function () {
    $(`.htlm-mp-lista-${id}`).remove();                     // Elimina el elemento después de la animación
    
    // Volvemos a renombrar los select
    renombrarInputsArray('.f_metodo_pago_validar', 'name', 'f_metodo_pago');
    renombrarInputsArray('.f_total_recibido_validar', 'name', 'f_total_recibido');
    renombrarInputsArrayContenedor('.f_mp_comprobante_validar', 'name', 'f_mp_comprobante');
    
  }, 500);                                                   // Tiempo de espera igual al de la animación

  
}


// .....::::::::::::::::::::::::::::::::::::: S E C C I O N   M E T O D O   D E   P A G O   :::::::::::::::::::::::::::::::::::::::..

function capturar_pago_venta(id) {   
  
  var metodo_pago = $(`#f_metodo_pago_${id}`).val() == null || $(`#f_metodo_pago_${id}`).val() == "" ? "" : $(`#f_metodo_pago_${id}`).val() ; //console.log(metodo_pago);
  
  $(`.span-code-baucher-pago-${id}`).html(`(${metodo_pago == null ? 'Seleccione metodo pago' : metodo_pago })`);  
  
  if (metodo_pago == null || metodo_pago == '' || metodo_pago == "EFECTIVO" || metodo_pago == "CREDITO") {
    $(`#content-metodo-pago-${id}`).hide();    
  } else if ( metodo_pago == "MIXTO" ) {
    $(`#content-metodo-pago-${id}`).show();       
  } else {    
    $(`#content-metodo-pago-${id}`).show();    
  }  
  calcular_vuelto();
  if (form_validate_facturacion) { $("#form-PagarOrden").valid();}
}

function calcular_vuelto(id) {

  var venta_total = $('#f_venta_total').val()     == null || $('#f_venta_total').val()    == '' ? 0 : parseFloat($('#f_venta_total').val()); 
  let contado = 0;

  // Recorrer cada input con el selector proporcionado
  $(`input.f_total_recibido_validar`).each(function () {
    const valor = parseFloat($(this).val()) || 0; // Convertir el valor a número o usar 0 si está vacío
    contado += valor; // Sumar al contado
  });

  var vuelto_2 = redondearExp((contado - venta_total), 2) ; 

  if ( contado == 0 ) { 
      
    $(`#f_total_vuelto`).val(vuelto_2);
    vuelto_2 < 0 ? $(`.f_total_vuelto`).addClass('bg-danger').removeClass('bg-success') : $(`.f_total_vuelto`).addClass('bg-success').removeClass('bg-danger') ;
    vuelto_2 < 0 ? $(`.falta_o_completo_${id}`).html('(falta)').addClass('text-danger').removeClass('text-success') : $(`.falta_o_completo_${id}`).html('(completo)').addClass('text-success').removeClass('text-danger') ;
  
  } else {
    $(`#f_total_vuelto`).val(vuelto_2);
    vuelto_2 < 0 ? $(`.f_total_vuelto`).addClass('bg-danger').removeClass('bg-success') : $(`.f_total_vuelto`).addClass('bg-success').removeClass('bg-danger') ;
    vuelto_2 < 0 ? $(`.falta_o_completo_${id}`).html('(falta)').addClass('text-danger').removeClass('text-success') : $(`.falta_o_completo_${id}`).html('(completo)').addClass('text-success').removeClass('text-danger') ;
  }
  if (form_validate_facturacion) { $("#form-PagarOrden").valid();}
}

// .....::::::::::::::::::::::::::::::::::::: S E C C I O N   CREAR CLIENTE  :::::::::::::::::::::::::::::::::::::::..

function crear_cliente() {
  limpiar_nuevo_cliente();
  $(`#modal-agregar-nuevo-cliente`).modal('show');
}

// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C R E A R   C L I E N T E                                                      ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════

function crear_cliente() {
  limpiar_nuevo_cliente();
  $(`#modal-agregar-nuevo-cliente`).modal('show');
}

function limpiar_nuevo_cliente() {
  // choice_tipo_documento.setChoiceByValue('1').passedElement.element.dispatchEvent(new Event('change'));
  $('#cli_tipo_documento').val('0').trigger('change');  
  
  $('#cli_nombre_razonsocial').val('');
  $('#cli_apellidos_nombrecomercial').val('');
  $('#cli_correo').val('');
  $('#cli_celular').val('');
  $('#cli_direccion').val('');
  $('#cli_direccion_referencia').val('');
  $('#cli_distrito').val('TARAPOTO').trigger('change');  
}

function guardar_editar_nuevo_cliente(e){ 
  var formData = new FormData($("#form-agregar-nuevo-cliente")[0]);
  $.ajax({
    url: "../ajax/orden_venta_cobrar.php?op=guardar_editar_cliente",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); 
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");          
          lista_select2("../ajax/orden_venta_cobrar.php?op=select2_cliente", '#f_idpersona_cliente', e.data, '.charge_f_idpersona_cliente');
          limpiar_nuevo_cliente();
          $('#modal-agregar-nuevo-cliente').modal('hide');
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_nuevo_cliente").html('Guardar Cambios').removeClass('disabled send-data');
    },
    xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					$("#barra_progress_nuevo_cliente").css({ "width": percentComplete + '%' });
					$("#barra_progress_nuevo_cliente div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
    beforeSend: function () {
      $("#guardar_registro_nuevo_cliente").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_nuevo_cliente").css({ width: "0%", });
      $("#barra_progress_nuevo_cliente div").text("0%");
      $("#barra_progress_nuevo_cliente_div").show();
    },
    complete: function () {
      $("#barra_progress_nuevo_cliente").css({ width: "0%", });
      $("#barra_progress_nuevo_cliente div").text("0%");
      $("#barra_progress_nuevo_cliente_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

$('#cli_tipo_documento').change(function () {
  var tipo = $(this).val() == '' ||  $(this).val() == null ? '' : $(this).val() ;

  if ( tipo == '0' ) { 
    $("#cli_numero_documento").val('00000000').addClass('bg-dark-transparent').prop('readonly', true).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('#cli_apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }else if (tipo == '1') {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_apellidos_nombrecomercial').rules('add', { required: true, minlength: 2, maxlength: 200, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } });
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }else if (tipo == '6') {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false).rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_apellidos_nombrecomercial').rules('remove', 'required');
    $('.label-nom-raz').html('Razón Social <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Nombre comercial <sup class="text-danger">*</sup>');
    $("#cli_numero_documento").rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('#cli_tipo_persona_sunat').val('JURÍDICA');
  } else {
    $("#cli_numero_documento").removeClass('bg-dark-transparent').prop('readonly', false);
    $('#cli_tipo_persona_sunat').val('NATURAL');   
  }

  $("#form-agregar-nuevo-cliente").valid();

});

$('#cli_distrito').on('change', async function () {  

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);

  await esperar(1000);  // Espera 5 segundos  

  if ($('#cli_distrito').val()== null || $('#cli_distrito').val() == '') {
    $("#departamento").val("");
    $("#provincia").val("");
    $("#ubigeo").val("");
    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito = $('#cli_distrito').select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {
      e = JSON.parse(e); 
      $("#cli_departamento").val(e.data.departamento);
      $("#cli_provincia").val(e.data.provincia);
      $("#cli_ubigeo").val(e.data.ubigeo_inei);

      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      
      $("#form-agregar-nuevo-cliente").valid();
    });
  }
});

function esperar(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

$(document).ready(function () {
  init();
  filtros();
});


function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}



$(function () {

  form_validate_facturacion=$("#form-PagarOrden").validate({
    rules: {
      f_idpersona_cliente:      { required: true },
      f_tipo_comprobante:       { required: true },
      f_serie_comprobante:      { required: true, },

      f_total_recibido:         { required: true, min: 0, step: 0.01},           
      f_total_vuelto:           { required: true, step: 0.01},
      f_mp_serie_comprobante:   { minlength: 4},
    },
    messages: {
      f_idpersona_cliente:      { required: "Campo requerido", },
      f_tipo_comprobante:       { required: "Campo requerido", },      
      f_serie_comprobante:      { required: "Campo requerido", },
      f_serie_comprobante:      { required: "Campo requerido", },

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
      guardar_editar_cobroOrden(e);      
    },

  });
$('#f_metodo_pago_1').rules('add', { required: true, messages: {  required: "Campo requerido" } });

    //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  
  $("#form-agregar-nuevo-cliente").validate({
    ignore: "",
    rules: {           
      cli_tipo_documento:           { required: true, minlength: 1, maxlength: 2, },       
      cli_numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      cli_nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      cli_apellidos_nombrecomercial:{ required: true, minlength: 4, maxlength: 200, },       
      cli_correo:    			          { minlength: 4, maxlength: 100, },       
      cli_celular:    			        { minlength: 8, maxlength: 9, },
      cli_direccion:    			      { minlength: 4, maxlength: 200, },       
      cli_direccion_referencia:     { minlength: 4, maxlength: 200, }, 	     
    },
    messages: {     
      cli_tipo_documento:    			  { required: "Campo requerido", },
      cli_numero_documento:    			{ required: "Campo requerido", }, 
      cli_nombre_razonsocial:    		{ required: "Campo requerido", }, 
      cli_apellidos_nombrecomercial:{ required: "Campo requerido", }, 
      cli_correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      cli_celular:    			        { minlength: "Mínimo {0} caracteres.", }, 
      cli_direccion:    			      { minlength: 4, maxlength: 200, },       
      cli_direccion_referencia:     { minlength: 4, maxlength: 200, },      
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
      guardar_editar_nuevo_cliente(e);      
    },
  });

});

function reload_f_idpersona_cliente(){ lista_select2("../ajax/orden_venta_cobrar.php?op=select2_cliente", '#f_idpersona_cliente', null, '.charge_f_idpersona_cliente'); }
function reload_filtro_fecha_i(){ $('#filtro_fecha_i').val("").trigger("change") } 
function reload_filtro_fecha_f(){ $('#filtro_fecha_f').val("").trigger("change") } 




function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_fecha_i        = $("#filtro_fecha_i").val();
  var filtro_fecha_f        = $("#filtro_fecha_f").val();  

  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();

  // filtro de fechas
  if (filtro_fecha_i        == '' || filtro_fecha_i         == 0 || filtro_fecha_i        == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  if (filtro_fecha_f        == '' || filtro_fecha_f         == 0 || filtro_fecha_f        == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }

 /* var filtro_cliente        = $("#filtro_cliente").select2('val');
  var filtro_tipo_persona   = $("#filtro_tipo_persona").select2('val');
  var filtro_comprobante    = $("#filtro_comprobante").select2('val');
  var filtro_metodo_pago    = $("#filtro_metodo_pago").select2('val');
  var filtro_centro_poblado = $("#filtro_centro_poblado").select2('val');
  

  var nombre_filtro_cliente     = ' ─ ' + $('#filtro_cliente').find(':selected').text();
  var nombre_filtro_comprobante = ' ─ ' + $('#filtro_comprobante').find(':selected').text();


  // filtro de cliente
  if (filtro_cliente        == '' || filtro_cliente         == 0 || filtro_cliente        == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }
  // filtro de filtro_tipo_persona
  if (filtro_tipo_persona   == '' || filtro_tipo_persona    == 0 || filtro_tipo_persona   == null) { filtro_tipo_persona = "";  }
  // filtro de comprobante
  if (filtro_comprobante    == '' || filtro_comprobante     == 0 || filtro_comprobante    == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }
  // filtro de metodo pago
  if (filtro_metodo_pago    == '' || filtro_metodo_pago     == 0 || filtro_metodo_pago    == null) { filtro_metodo_pago = ""; nombre_filtro_metodo_pago = ""; }
  // filtro de filtro_centro_poblado
  if (filtro_centro_poblado == '' || filtro_centro_poblado  == 0 || filtro_centro_poblado == null) { filtro_centro_poblado = "";  }*/

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_filtro_fecha_i} ${nombre_filtro_fecha_f} ...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_ordenesCobro(filtro_fecha_i, filtro_fecha_f);
}
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
// ═══════                                         S E C C I O N  -  C L I E N T E   V A L I D O                                                    ═══════
// ════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
function es_valido_cliente() {

  var id_cliente = $('#f_idpersona_cliente').val() == ''  || $('#f_idpersona_cliente').val() == null ? '' : $('#f_idpersona_cliente').val();

  if (id_cliente != null && id_cliente != '') {

    var tipo_comprobante = $('#f_tipo_comprobante_hidden').val() == ''  || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();
    var tipo_documento    = $('#f_idpersona_cliente').select2('data')[0].element.attributes.tipo_documento.value;
    var numero_documento  = $('#f_idpersona_cliente').select2('data')[0].element.attributes.numero_documento.value;
    var direccion         = $('#f_idpersona_cliente').select2('data')[0].element.attributes.direccion.value;  
    var campos_requeridos = ""; 
    var es_valido = true; 

    if (tipo_comprobante == '01') {       // FACTURA
       console.log(tipo_comprobante, tipo_documento, numero_documento, direccion);
      if ( tipo_documento == '6'  ) { }else{ campos_requeridos = campos_requeridos.concat(`<li>Tipo de Documento: RUC</li>`);  }
      if ( numero_documento != '' ) { }else{ campos_requeridos = campos_requeridos.concat(`<li>Numero de Documento</li>`);  }
      if ( direccion != '' ) {    }else{  campos_requeridos = campos_requeridos.concat(`<li>Direccion</li>`);  }
      if (tipo_documento == '6' && numero_documento != '' && direccion != '' ) {  es_valido = true;  } else {   es_valido = false; }

    } else if (tipo_comprobante == '03' || id_cliente == '1') {  // BOLETA
      
      if ( tipo_documento == '1' || tipo_documento == '6' ) {  }else{  campos_requeridos = campos_requeridos.concat(`<li>Tipo de Documento: DNI o RUC</li>`);  }
      if ( numero_documento != '' ) {  }else{  campos_requeridos = campos_requeridos.concat(`<li>Numero de Documento</li>`);  }
      if ( direccion == '' || direccion == null ) {  campos_requeridos = campos_requeridos.concat(`<li>Direccion</li>`);  }else{    }
      if ( (tipo_documento == '1' || tipo_documento == '6' || tipo_documento == '0' ) && numero_documento != ''  ) { es_valido = true; } else {  es_valido = false; }

    } else if (tipo_comprobante == '12' ) { // TICKET
      es_valido = true;
    }

    if (es_valido == true) {
     
    } else {

      if (tipo_comprobante == '03' && tipo_documento == '0' ) {
        Swal.fire({
          title: "Desea emitir Boleta?",
          html: "Si deseas emitir Boleta sin DNI, actualiza el numero con 8 ceros: 00000000, o ingrese el DNI correcto del cliente.",
          input: "text",
          inputValue: '00000000',
          inputAttributes: { autocapitalize: "off" },
          showCancelButton: true,
          confirmButtonText: "Actualizar DNI",
          showLoaderOnConfirm: true,
          preConfirm: async (numero_documento) => {
            try {
              var id_cliente = $("#f_idpersona_cliente").select2('val') == null ? '' : $("#f_idpersona_cliente").select2('val');
              const UrlUpdate_client = `../ajax/ajax_general.php?op=update_nro_documento_cliente&idpersona_cliente=${id_cliente}&numero_documento=${numero_documento}`;
              const response = await fetch(UrlUpdate_client);
              if (!response.ok) {
                return Swal.showValidationMessage(` ${JSON.stringify(await response.json())} `);
              }
              return response.json();
            } catch (error) {
              Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`);
            }
          },
          allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
          if (result.isConfirmed) {
            var id_cliente = $("#f_idpersona_cliente").select2('val') == null ? '' : $("#f_idpersona_cliente").select2('val');
            //lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', id_cliente);
             
            lista_select2("../ajax/orden_venta_cobrar.php?op=select2_cliente", '#f_idpersona_cliente', id_cliente);
            sw_success('Datos Actualizado!!', 'Se actualizado el Nro Documento correctamente');
          }else{
            $("#f_idpersona_cliente").val('').trigger('change'); 
          }
        });
      } else {
        sw_cancelar('Cliente no permitido', `El cliente no cumple con los siguientes requsitos:  <ul class="pt-3 text-left font-size-13px"> ${campos_requeridos} </ul>`, 10000);
        $("#f_idpersona_cliente").val('').trigger('change'); 
      }
      
    }   

  }
}

function calcular_vuelto(id) {

  var venta_total = $('#f_venta_total').val()     == null || $('#f_venta_total').val()    == '' ? 0 : parseFloat($('#f_venta_total').val()); 
  let contado = 0;

  // Recorrer cada input con el selector proporcionado
  $(`input.f_total_recibido_validar`).each(function () {
    const valor = parseFloat($(this).val()) || 0; // Convertir el valor a número o usar 0 si está vacío
    contado += valor; // Sumar al contado
  });

  var vuelto_2 = redondearExp((contado - venta_total), 2) ; 

  if ( contado == 0 ) { 
      
    $(`#f_total_vuelto`).val(vuelto_2);
    vuelto_2 < 0 ? $(`.f_total_vuelto`).addClass('bg-danger').removeClass('bg-success') : $(`.f_total_vuelto`).addClass('bg-success').removeClass('bg-danger') ;
    vuelto_2 < 0 ? $(`.falta_o_completo_${id}`).html('(falta)').addClass('text-danger').removeClass('text-success') : $(`.falta_o_completo_${id}`).html('(completo)').addClass('text-success').removeClass('text-danger') ;
  
  } else {
    $(`#f_total_vuelto`).val(vuelto_2);
    vuelto_2 < 0 ? $(`.f_total_vuelto`).addClass('bg-danger').removeClass('bg-success') : $(`.f_total_vuelto`).addClass('bg-success').removeClass('bg-danger') ;
    vuelto_2 < 0 ? $(`.falta_o_completo_${id}`).html('(falta)').addClass('text-danger').removeClass('text-success') : $(`.falta_o_completo_${id}`).html('(completo)').addClass('text-success').removeClass('text-danger') ;
  }
  if (form_validate_facturacion) { $("#form-PagarOrden").valid();}
}

function pago_rapido(val) {
  var pago_monto = quitar_formato_miles($(val).text()); //console.log(pago_monto);
  $('#f_total_recibido_1').val(pago_monto);
  calcular_vuelto(1);
  $("#form-PagarOrden").valid();
}

function pago_rapido_moneda(moneda) {
  $('#f_total_recibido_1').val(moneda);
  calcular_vuelto(1);
  $("#form-PagarOrden").valid();
}

