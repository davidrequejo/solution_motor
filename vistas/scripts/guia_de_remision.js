var tabla_guias;
var tabla_productos;
var array_data_gr = [];
var estado_editar = false;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

const choice_tipo_documento = new Choices('#cp_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

function init(){
 
  listar_tabla_guia(); 

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-compra").submit(); }  });
  $("#guardar_registro_chofer_publico").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-chofer-publico").submit(); } });
  $("#guardar_registro_producto").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════

  lista_select2("../ajax/ajax_general.php?op=select2_series_comprobante&tipo_comprobante=09", '#serie_comprobante', 'G001');
  lista_select2("../ajax/guia_de_remision.php?op=select2_modalidad_transporte", '#modalidad_transporte', null);
  lista_select2("../ajax/guia_de_remision.php?op=select2_motivo_traslado", '#motivo_traslado', '01');
  lista_select2("../ajax/ajax_general.php?op=select2_lista_comprobante", '#documento_asociado', null, '.charge_documento_asociado');

  
  lista_select2("../ajax/guia_de_remision.php?op=listar_cliente", '#idcliente', null);
  lista_select2("../ajax/guia_de_remision.php?op=select2_chofer_publico", '#idpersona_chofer', null);
  lista_select2("../ajax/guia_de_remision.php?op=listar_crl_comprobante&tipos='00','01','03','12'", '#tipo_comprobante', null);

  lista_select2("../ajax/guia_de_remision.php?op=select_categoria", '#categoria', null);
  lista_select2("../ajax/guia_de_remision.php?op=select_u_medida", '#u_medida', null);
  lista_select2("../ajax/guia_de_remision.php?op=select_marca", '#marca', null);

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_tipo_documento", choice_tipo_documento, null); 
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null); 
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_banco", choice_idbanco, null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#documento_asociado").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idcliente").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#tipo_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idpersona_chofer").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $('#numero_placa').inputmask({
      "mask": "a{1,}-999999",  // Letras (al menos 1) seguidas de un guion y luego exactamente 6 dígitos
    "definitions": {
        "a": { "validator": "[A-Za-z]", "cardinality": 1 },  // Solo letras (mayúsculas o minúsculas)
        "9": { "validator": "[0-9]", "cardinality": 1 }  // Solo números después del guion
    },
    "clearMaskOnLostFocus": false, // No borra la máscara si el campo pierde el foco   
    "skipOptionalPartCharacter": "",  // Esto evita que se agreguen caracteres opcionales innecesarios    
    "autoUnmask": true,  // Evita mostrar caracteres extra en el input
    "showMaskOnHover": false, // No muestra la máscara en el campo cuando pasa el mouse
    "showMaskOnFocus": false, // No muestra la máscara cuando se enfoca el campo
    "placeholder": ""  // No mostrar guiones o caracteres de máscara mientras se escribe

  });
  
}


function show_hide_form(flag) {
	if (flag == 1) {
		$("#div-tabla").show();
		$("#div-formulario").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) {
		$("#div-tabla").hide();
		$("#div-formulario").show();

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();
	}
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   C O M P R A S :::::::::::::::::::::::::::::::::::::::::::::

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', '300px', true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form_guia(){

  array_data_gr = [];
  estado_editar = false;
  $("#idventa").val('');  

  $("#modalidad_transporte").val('').trigger('change');
  $("#motivo_traslado").val('01').trigger('change');
  $("#documento_asociado").val('').trigger('change');
  $("#idcliente").val('').trigger('change');
  $("#partida_direccion").val( p_direccion );
  $("#partida_distrito").val(p_distrito);
  $("#partida_ubigeo").val(p_ubigeo);
  $("#llegada_direccion").val('');
  $("#llegada_distrito").val('');
  $("#llegada_ubigeo").val('');
  $("#peso_total").val('');
  $("#idpersona_chofer").val('').trigger('change');
  $("#numero_documento").val('');  
  $("#numero_licencia").val('');  
  $("#numero_placa").val('');  
  $("#nombre_razonsocial").val('');  
  $("#apellidos_nombrecomercial").val('');  
  $("#gr_observacion").val('');  

  $("#total_guia").val("");     
  $(".total_guia").html("0");

  $(".subtotal_guia").html("<span>S/</span> 0.00");
  $("#subtotal_guia").val("");

  $(".descuento_guia").html("<span>S/</span> 0.00");
  $("#descuento_guia").val("");

  $(".igv_guia").html("<span>S/</span> 0.00");
  $("#igv_guia").val("");

  $(".total_guia").html("<span>S/</span> 0.00");
  $("#total_guia").val("");
  

  $(".filas").remove();
  cont = 0;

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function listar_tabla_guia(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_estado_sunat){
  tabla_guias = $("#tabla-guia-remision").dataTable({
    responsive: false, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_guias) { tabla_guias.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6,7,8,10], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6,7,8,10], }, title: 'Lista de guias', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6,7,8,10], }, title: 'Lista de guias', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/guia_de_remision.php?op=listar_tabla_guia&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_estado_sunat=${filtro_estado_sunat}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-copy").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Copiar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-pdf").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'PDF');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
      },
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: Opciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: Monto
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      // columna: Monto
      if (data[5] != '') { $("td", row).eq(5).addClass("text-nowrap text-center"); }
      // columna: Boucher
      if (data[8] != '') { $("td", row).eq(8).addClass("text-nowrap text-center"); }
      // columna: Boucher
      if (data[9] != '') { $("td", row).eq(9).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      search: "",
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [3], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function guardar_editar_guia(e) {
  var formData = new FormData($("#form-agregar-guia-remsion")[0]);   
  
  Swal.fire({
    title: "¿Está seguro que deseas guardar esta Guía Remisión?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/guia_de_remision.php?op=guardar_editar_guia", {
        method: 'POST', // or 'PUT'
        body: formData, // data can be `string` or {object}!        
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
        tabla_guias.ajax.reload(null, false);
        limpiar_form_guia(); show_hide_form(1); 
        if ($('#f_crear_y_mostrar').is(':checked')) {
          ver_formato_a4_completo(result.value.data, '09');          
        }
      } else if ( result.value.status == 'error_personalizado'){        
        tabla_guias.ajax.reload(null, false);
        limpiar_form_guia(); show_hide_form(1);  ver_errores(result.value);
      } else if ( result.value.status == 'error_usuario'){    
        ver_errores(result.value);

      } else if ( result.value.status == 'no_conexion_sunat'){    
        Swal.fire({
          title: result.value.titulo,
          icon: 'info',
          html: result.value.message,
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Salir!',
          confirmButtonAriaLabel: 'Enviar más tarde.',
          cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
          cancelButtonAriaLabel: 'Dejar como esta.'
        }).then((result) => {
          if (result.isConfirmed) {
           
            $.getJSON(`../ajax/guia_de_remision.php?op=cambiar_a_por_enviar&idventa=${result.value.id_tabla}`, data, function (e, textStatus, jqXHR) {
              if (e.status == true) {
                sw_success('Cambiado!!', "Tu registro ha actualizado." );   
              } else {
                ver_errores(e);
              }
            }).fail( function(e) { ver_errores(e); } );
          } else {
                  
          }
        });
      } else {
        ver_errores(result.value);
      }      
    }
  }); 
}

function mostrar_editar_guia(idguia) {
  $("#cargando-1-formulario").hide();
  $("#cargando-2-formulario").show();
  show_hide_form(2);
  $.getJSON("../ajax/guia_de_remision.php?op=mostrar_editar_guia", {idguia:idguia}, function (e, textStatus, jqXHR) {
    estado_editar = true;
    $("#idventa").val(e.data.guia_cabecera.idventa);
    // DOCUMENTO
    $("#serie_comprobante").val(e.data.guia_cabecera.serie_comprobante); 
    $("#modalidad_transporte").val(e.data.guia_cabecera.gr_cod_modalidad_traslado).trigger('change');
    $("#motivo_traslado").val(e.data.guia_cabecera.gr_cod_motivo_traslado).trigger('change');
    // DATOS DE CLIENTE
    $("#documento_asociado").val(e.data.guia_cabecera.gr_idventa_asociada).trigger('change');
    $("#idcliente").val(e.data.guia_cabecera.idpersona_cliente).trigger('change');
    // DATOS DE TRASLADO
    $("#partida_direccion").val( e.data.guia_cabecera.gr_partida_direccion );
    $("#partida_distrito").val(e.data.guia_cabecera.gr_partida_distrito);
    $("#partida_ubigeo").val(e.data.guia_cabecera.gr_partida_ubigeo);
    $("#llegada_direccion").val(e.data.guia_cabecera.gr_llegada_direccion);
    $("#llegada_distrito").val(e.data.guia_cabecera.gr_llegada_distrito);
    $("#llegada_ubigeo").val(e.data.guia_cabecera.gr_llegada_ubigeo);
    $("#peso_total").val(e.data.guia_cabecera.gr_peso_total);
    // UNIDAD DE TRANSPORTE Y EL CONDUCTOR
    $("#idpersona_chofer").val(e.data.guia_cabecera.gr_idconductor).trigger('change');
    $("#gr_tipo_documento").val(e.data.guia_cabecera.gr_chofer_tipo_documento);  
    $("#numero_documento").val(e.data.guia_cabecera.gr_chofer_numero_documento);  
    $("#numero_licencia").val(e.data.guia_cabecera.gr_numero_licencia);  
    $("#numero_placa").val(e.data.guia_cabecera.gr_placa);  
    $("#nombre_razonsocial").val(e.data.guia_cabecera.gr_chofer_nombre_razonsocial);  
    $("#apellidos_nombrecomercial").val(e.data.guia_cabecera.gr_chofer_apellidos_nombrecomercial);  

    $("#gr_observacion").val(e.data.guia_cabecera.observacion_documento);       

    $("#tabla-productos-seleccionados tbody").html('');
    e.data.guia_detalle.forEach((val, key) => {
      
    
      var subtotal = val.cantidad * val.precio_venta;          
      var img = val.imagen == "" || val.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${val.imagen}` ;          

      var fila = `
      <tr class="filas" id="fila${cont}"> 

        <td class="py-1">
          <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${val.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
          <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${val.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
        </td>            

        <td class="py-1">         
          <input type="hidden" name="idproducto[]" value="${val.idproducto}">

          <input type="hidden" name="pr_marca[]" value="${val.pr_marca}">
          <input type="hidden" name="pr_categoria[]" value="${val.pr_categoria}">
          <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="${val.pr_unidad_medida}">
          <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="${val.um_abreviatura}">

          <input type="hidden" class="descuento_${cont}" name="f_descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
          <input type="hidden" class="descuento_porcentaje_${cont}" name="descuento_porcentaje[]" value="0" >

          <div class="d-flex flex-fill align-items-center">
            <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(val.pr_nombre)}')"> </span></div>
            <div>
              <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${val.pr_nombre}</textarea>
              <div class="w-250px span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${val.pr_nombre}</span> </div>
              <span class="d-block fs-9 text-muted">UM: ${val.um_abreviatura} | M: <b>${val.pr_marca}</b> | C: <b>${val.pr_categoria}</b></span> 
            </div>
          </div>
        </td>                        

        <td class="py-1 form-group">
          <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${val.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${val.cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); update_price(); ">
          <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${val.cantidad}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
        </td> 

        <td class="py-1 form-group">
          <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${val.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
          <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${val.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
          <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
          <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
          <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${val.precio_compra}"  >
          <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${val.precio_venta}"  >
        </td>             

        <td class="py-1 text-right">
          <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${val.subtotal}</span> 
          <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
          <input type="hidden" name="subtotal_no_descuento_producto[]" id="subtotal_no_descuento_producto_${cont}" value="0" >
        </td>
        <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
        
      </tr>`;

      detalles = detalles + 1;
      $("#tabla-productos-seleccionados tbody").append(fila);
      array_data_gr.push({ id_cont: cont });
      modificarSubtotales();        
      

      // reglas de validación     
      $('.valid_precio_con_igv').each(function(e) { 
        $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
        $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
      });
      $('.valid_cantidad').each(function(e) { 
        $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
        $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
      });
      $('.valid_descuento').each(function(e) { 
        $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
        $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
      });          

      cont++;   
      evaluar();
    });
    toastr_success("Agregado!!",`Productos agregados !!`, 700);
    $("#cargando-1-formulario").show();
    $("#cargando-2-formulario").hide();
  });
}

function mostrar_detalle_guia(idcompra){
  $("#modal-detalle-compra").modal("show");

  $.post("../ajax/guia_de_remision.php?op=mostrar_detalle_guia", { idcompra: idcompra }, function (e, status) {          
      
    $('#custom-tabContent').html(e);      
    $('#custom-datos1_html-tab').click(); // click para ver el primer - Tab Panel
    $(".jq_image_zoom").zoom({ on: "grab" });      
    $("#excel_guia").attr("href",`../reportes/export_xlsx_venta_tours.php?id=${idcompra}`);      
    $("#print_pdf_guia").attr("href",`../reportes/comprobante_venta_tours.php?id=${idcompra}`);    
    
  }).fail( function(e) { ver_errores(e); } );

}

function eliminar_papelera_guia(idcompra, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/guia_de_remision.php?op=papelera",
    "../ajax/guia_de_remision.php?op=eliminar", 
    idcompra, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>Compra: ${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_guias.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function ver_img_comprobante(idcompra) {
  $('#modal-ver-comprobante1').modal('show');
  $("#comprobante-container1").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/guia_de_remision.php?op=mostrar_guia", { idcompra: idcompra },  function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      if (e.data.comprobante == "" || e.data.comprobante == null) { } else {
        var nombre_comprobante = `${e.data.tipo_comprobante} ${e.data.serie_comprobante}`;
        $('.title-modal-comprobante1').html(nombre_comprobante);
        $("#comprobante-container1").html(doc_view_download_expand(e.data.comprobante, 'assets/modulo/comprobante_guia',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
}

function cambiar_a_por_enviar(idventa, nombre) {

  crud_simple_alerta(
    `../ajax/guia_de_remision.php?op=cambiar_a_por_enviar&idventa=${idventa}`,
    '', 
    "Está Seguro?", 
    `Se cambiara el estado de: <b class="text-danger">${nombre}</b> <br> al hacerlo le permitirá crear mas Boletas o Facturas, en caso contrario desactive el envio a a SUNAT!`, 
    'Si, Cambiar',
    function(){ sw_success('Cambiado!!', "Tu registro ha actualizado." ) }, 
    function(){ tabla_guias.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   P R O D U C T O S :::::::::::::::::::::::::::::::::::::::::::::
//Declaración de variables necesarias para trabajar
var impuesto = 18;
var cont = 0;
var detalles = 0;
var conNO = 1;

function agregarDetalleComprobante(idproducto, codigo_barras, individual) {   
  
  // var precio_venta = 0;
  var precio_sin_igv =0;
  var cantidad = 1;
  var descuento = 0;
  var precio_igv = 0;

  var search_producto = ''; 

  if (idproducto == "" || idproducto == null ) {   
    if ( codigo_barras == null || codigo_barras == ''  ) {
      toastr_info('¡Campo vacío!', 'Por favor ingrese almenor un codigo e intente nuevamente.');
      return;
    } else {
      search_producto = codigo_barras == "OK" ? $("#search_producto").val() : codigo_barras;      
      $(`.buscar_x_code`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
    }    
  } else {    
    $(`.btn-add-producto-1-${idproducto}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);  
    $(`.btn-add-producto-2-${idproducto}`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`); 
    search_producto = idproducto;    
  }

  if (search_producto == null || search_producto == '') {  
    $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
    toastr_info('¡Campo vacío!', 'Por favor ingrese almenor un codigo e intente nuevamente.'); return;  
  }
       
  $.getJSON("../ajax/guia_de_remision.php?op=mostrar_producto", {'search': search_producto}, function (e, textStatus, jqXHR) {        
    
    if (e.status == true) {      
      if (e.data == null) {
        toastr_warning('No existe!!', 'Proporcione un codigo existente o el producto pertenece a otra categoria.');          
      } else {         
        
        if ( $(`.producto_${e.data.idproducto}`).hasClass("producto_selecionado") && individual == false ) {
          if (document.getElementsByClassName(`producto_${e.data.idproducto}`).length == 1) {
            var cant_producto = $(`.producto_${e.data.idproducto}`).val();
            var sub_total = parseInt(cant_producto, 10) + 1;
            $(`.producto_${e.data.idproducto}`).val(sub_total).trigger('change');
            toastr_success("Agregado!!",`Producto: ${$(`.nombre_producto_${e.data.idproducto}`).text()} agregado !!`, 700);
            modificarSubtotales();          
          }  
                  
         
        } else { 
      
          var subtotal = cantidad * e.data.precio_venta;          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;          

          var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1">
              <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${e.data.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${e.data.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
            </td>            

            <td class="py-1">         
              <input type="hidden" name="idproducto[]" value="${e.data.idproducto}">

              <input type="hidden" name="pr_marca[]" value="${e.data.marca}">
              <input type="hidden" name="pr_categoria[]" value="${e.data.categoria}">
              <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="${e.data.unidad_medida}">
              <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="${e.data.um_abreviatura}">

              <input type="hidden" class="descuento_${cont}" name="f_descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
              <input type="hidden" class="descuento_porcentaje_${cont}" name="descuento_porcentaje[]" value="0" >

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre)}')"> </span></div>
                <div>
                  <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${e.data.nombre}</textarea>
                  <div class="w-250px span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${e.data.nombre}</span> </div>
                  <span class="d-block fs-9 text-muted">UM: ${e.data.um_abreviatura} | M: <b>${e.data.marca}</b> | C: <b>${e.data.categoria}</b></span> 
                </div>
              </div>
            </td>                        

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${e.data.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); update_price(); ">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${e.data.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${e.data.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
              <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${e.data.precio_compra}"  >
              <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${e.data.precio_venta}"  >
            </td>             

            <td class="py-1 text-right">
              <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> 
              <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
              <input type="hidden" name="subtotal_no_descuento_producto[]" id="subtotal_no_descuento_producto_${cont}" value="0" >
            </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
            
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_gr.push({ id_cont: cont });
          modificarSubtotales();        
          toastr_success("Agregado!!",`Producto: ${e.data.nombre} agregado !!`, 700);

          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });          

          cont++;   
          evaluar();
        }
      }
    } else {
      ver_errores(e);
    }           
    
    $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
    $(`.btn-add-producto-1-${idproducto}`).html(`<span class="fa fa-plus"></span>`);        
    $(`.btn-add-producto-2-${idproducto}`).html(`<i class="fa-solid fa-list-ol"></i>`); 
    
  }).fail( function(e) { ver_errores(e); } );  
}

function evaluar() {
  if (detalles > 0) {
    $(".btn-guardar").show();
  } else {
    $(".btn-guardar").hide();
    cont = 0;
    $(".f_guia_subtotal").html("<span>S/</span> 0.00");
    $("#f_guia_subtotal").val(0);

    $(".f_guia_descuento").html("<span>S/</span> 0.00");
    $("#f_guia_descuento").val(0);

    $(".f_guia_igv").html("<span>S/</span> 0.00");
    $("#f_guia_igv").val(0);

    $(".f_guia_total").html("<span>S/</span> 0.00");
    $("#f_guia_total").val(0);
    $(".pago_rapido").html(0);

  }
}

function default_val_igv() { 
  if ($("#f_tipo_comprobante").val() == "01") { $("#f_impuesto").val(0); } 
} // FACTURA

function modificarSubtotales() {  

  var val_igv = $("#f_impuesto").val();

  if ($("#f_tipo_comprobante").val() == null) {    

    $("#f_impuesto").val(0);
    $(".val_igv").html('IGV (0%)');

    $("#f_tipo_gravada").val('SUBTOTAL');
    $(".f_tipo_gravada").html('SUBTOTAL');

    if (array_data_gr.length == 0) {
    } else {
      array_data_gr.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: IGV
        var precio_sin_igv = precio_con_igv;
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: precio + IGV
        var igv = 0;
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));
        
        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });
      calcularTotalesSinIgv();
    }
  } else if ($("#f_tipo_comprobante").val() == "12") {      // TICKET 

    if (array_data_gr.length === 0) {
      if (val_igv == '' || val_igv <= 0) {
        $("#f_tipo_gravada").val('SUBTOTAL');
        $(".f_tipo_gravada").html('SUBTOTAL');
        $(".val_igv").html(`IGV (0%)`);
      } else {
        $("#f_tipo_gravada").val('GRAVADA');
        $(".f_tipo_gravada").html('GRAVADA');
        $(".val_igv").html(`IGV (${redondearExp((val_igv * 100), 2)}%)`);
      }
      
    } else {
      // validamos el valor del igv ingresado        

      array_data_gr.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: Precio sin IGV
        var precio_sin_igv = redondearExp( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal'), 2);
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: IGV
        var igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));

        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });

      calcularTotalesConIgv();
    }
  } else if ($("#f_tipo_comprobante").val() == "01" || $("#f_tipo_comprobante").val() == "03" || $("#f_tipo_comprobante").val() == "09" ) { // FACTURA O BOLETA 

    $(".hidden").show(); //Mostramos: IGV, PRECIO SIN IGV
    $("#colspan_subtotal").attr("colspan", 7); //cambiamos el: colspan    
    $("#val_igv").prop("readonly",false);

    if (array_data_gr.length === 0) {
      if (val_igv == '' || val_igv <= 0) {
        $("#f_tipo_gravada").val('NO GRAVADA');
        $(".f_tipo_gravada").html('NO GRAVADA');
        $(".val_igv").html(`IGV (0%)`);
      } else {
        $("#f_tipo_gravada").val('GRAVADA');
        $(".f_tipo_gravada").html('GRAVADA');
        $(".val_igv").html(`IGV (${(parseFloat(val_igv) * 100).toFixed(2)}%)`);
      }
      
    } else {
      // validamos el valor del igv ingresado        

      array_data_gr.forEach((key, index) => {
        var cantidad        = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv  = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento       = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: Precio sin IGV
        var precio_sin_igv = ( quitar_igv_del_precio(precio_con_igv, val_igv, 'decimal')).toFixed(2);
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: IGV
        var igv = (parseFloat(precio_con_igv) - parseFloat(precio_sin_igv)).toFixed(2);
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2 ));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2 ));

        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto.toFixed(2)));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2 ));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2 ));
      });

      calcularTotalesConIgv();
    }
  } else {

    $("#f_impuesto").val(0);    
    $(".val_igv").html('IGV (0%)');

    $("#f_tipo_gravada").val('SUBTOTAL');
    $(".f_tipo_gravada").html('SUBTOTAL');

    if (array_data_gr.length === 0) {
    } else {
      array_data_gr.forEach((key, index) => {
        var cantidad = $(`.cantidad_${key.id_cont}`).val() == '' || $(`.cantidad_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${key.id_cont}`).val());
        var precio_con_igv = $(`.precio_con_igv_${key.id_cont}`).val() == '' || $(`.precio_con_igv_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_con_igv_${key.id_cont}`).val());
        var descuento = $(`.descuento_${key.id_cont}`).val() == '' || $(`.descuento_${key.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${key.id_cont}`).val());
        var subtotal_producto = 0;
        var subtotal_producto_no_dcto = 0;

        // Calculamos: IGV
        var precio_sin_igv = precio_con_igv;
        $(`.precio_sin_igv_${key.id_cont}`).val(precio_sin_igv);

        // Calculamos: precio + IGV
        var igv = 0;
        $(`.precio_igv_${key.id_cont}`).val(igv);

        // Calculamos: Subtotal de cada producto
        subtotal_producto_no_dcto = cantidad * parseFloat(precio_con_igv);
        subtotal_producto = cantidad * parseFloat(precio_con_igv) - descuento;

        // Calculamos: precio unitario descontado
        var precio_unitario_dscto = subtotal_producto / cantidad;
        $(`.precio_venta_descuento_${key.id_cont}`).val(redondearExp(precio_unitario_dscto, 2));

        // Calculamos: porcentaje descuento
        var porcentaje_monto = descuento / subtotal_producto_no_dcto;
        $(`.descuento_porcentaje_${key.id_cont}`).val(redondearExp(porcentaje_monto, 2));

        $(`.subtotal_producto_${key.id_cont}`).html(formato_miles(subtotal_producto));
        $(`#subtotal_producto_${key.id_cont}`).val(redondearExp(subtotal_producto, 2));
        $(`#subtotal_no_descuento_producto_${key.id_cont}`).val(redondearExp(subtotal_producto_no_dcto, 2));
      });

      calcularTotalesSinIgv();
    }
  }
  
  $("#form-agregar-guia-remsion").valid();
}

function calcularTotalesSinIgv() {
  var total = 0.0;
  var igv = 0;
  var descuento = 0;

  if (array_data_gr.length === 0) {
  } else {
    array_data_gr.forEach((element, index) => {
      total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text())) || 0;
      descuento += parseFloat( $(`.descuento_${element.id_cont}`).val() ) || 0;
    });

    $(".f_guia_subtotal").html("<span>S/</span> " + formato_miles(total));
    $("#f_guia_subtotal").val(redondearExp(total, 2));

    $(".f_guia_descuento").html("<span>S/</span> " + formato_miles(descuento));
    $("#f_guia_descuento").val(redondearExp(descuento, 2));

    $(".f_guia_igv").html("<span>S/</span> 0.00");
    $("#f_guia_igv").val(0.0);
    $(".val_igv").html('IGV (0%)');

    $(".f_guia_total").html("<span>S/</span> " + formato_miles(total));
    $("#f_guia_total").val(redondearExp(total, 2));
    $(".pago_rapido").html(formato_miles(total));
    $(".pago_rapido").html(formato_miles(total));
  }
}

function calcularTotalesConIgv() {
  var val_igv = $('#f_impuesto').val();
  var igv = 0;
  var total = 0.0;
  var descuento = 0.0;

  var subotal_sin_igv = 0;

  array_data_gr.forEach((element, index) => {
    total += parseFloat(quitar_formato_miles($(`.subtotal_producto_${element.id_cont}`).text())) || 0;
    descuento += parseFloat( $(`.descuento_${element.id_cont}`).val() ) || 0;
  });

  //console.log(total); 

  subotal_sin_igv = redondearExp(quitar_igv_del_precio(total, val_igv, 'entero') , 2);
  igv = (parseFloat(total) - parseFloat(subotal_sin_igv)).toFixed(2);

  $(".f_guia_subtotal").html(`<span>S/</span> ${formato_miles(subotal_sin_igv)}`);
  $("#f_guia_subtotal").val(redondearExp(subotal_sin_igv, 2));

  $(".f_guia_descuento").html("<span>S/</span> " + formato_miles(descuento));
  $("#f_guia_descuento").val(redondearExp(descuento, 2));

  $(".f_guia_igv").html("<span>S/</span> " + formato_miles(igv));
  $("#f_guia_igv").val(igv);

  $(".f_guia_total").html("<span>S/</span> " + formato_miles(total));
  $("#f_guia_total").val(redondearExp(total, 2));
  $(".pago_rapido").html(formato_miles(total, 2));
  $(".pago_rapido").html(formato_miles(total, 2));
  total = 0.0;
}

function eliminarDetalle(idproducto, indice) {
  $("#fila" + indice).remove();
  array_data_gr.forEach(function (car, index, object) { if (car.id_cont === indice) { object.splice(index, 1); } });
  modificarSubtotales();
  detalles = detalles - 1;
  toastr_warning("Removido!!","Producto removido", 700);
  evaluar();
}

function mostrar_detalle_venta(idventa) {  
  console.log(`Estado editar: ${estado_editar}`);
  
  var precio_sin_igv =0;
  var cantidad = 1;
  var descuento = 0;
  var precio_igv = 0;

  $("#tabla-productos-seleccionados tbody").html('');
  
  $.getJSON("../ajax/guia_de_remision.php?op=mostrar_detalle_venta", {'idventa': idventa}, function (e, status) {
    if (estado_editar == false) {      
    
      if (e.status == true) {

        $("#idcliente").val(e.data.venta.idpersona_cliente).trigger('change');    
          
        $("#impuesto").val(e.data.venta.val_igv);

        $.each(e.data.detalle, function(index, val1) {

          var img = val1.imagen == "" || val1.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${val1.imagen}` ;          
          var subtotal = parseFloat(val1.cantidad) * (val1.precio_venta);      
          var fila = `
            <tr class="filas" id="fila${cont}"> 

              <td class="py-1">
                <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${val1.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button> -->
                <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${val1.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
              </td>            

              <td class="py-1">         
                <input type="hidden" name="idproducto[]" value="${val1.idproducto}">

                <input type="hidden" name="pr_marca[]" value="${val1.marca}">
                <input type="hidden" name="pr_categoria[]" value="${val1.categoria}">
                <input type="hidden" class="um_nombre_${cont}" name="um_nombre[]" id="um_nombre[]" value="${val1.pr_unidad_medida}">
                <input type="hidden" class="um_abreviatura_${cont}" name="um_abreviatura[]" id="um_abreviatura[]" value="${val1.um_abreviatura}">

                <input type="hidden" class="descuento_${cont}" name="f_descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
                <input type="hidden" class="descuento_porcentaje_${cont}" name="descuento_porcentaje[]" value="0" >

                <div class="d-flex flex-fill align-items-center">
                  <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(val1.nombre_producto)}')"> </span></div>
                  <div>
                    <textarea style="display: none;" class="form-control w-250px fs-11 text-primary textarea_pr_nombre_${cont}" name="pr_nombre[]" rows="2" onkeyup="this.value = this.value.toUpperCase()" >${val1.nombre_producto}</textarea>
                    <div class="w-250px span_pr_nombre_${cont}" ><span class="d-block fs-11 fw-semibold text-primary cursor-pointer" ondblclick="div_ocultar_mostrar('.span_pr_nombre_${cont}', '.textarea_pr_nombre_${cont}')" >${val1.nombre_producto}</span> </div>
                    <span class="d-block fs-9 text-muted">UM: ${val1.um_abreviatura} | M: <b>${val1.marca}</b> | C: <b>${val1.categoria}</b></span> 
                  </div>
                </div>
              </td>                        

              <td class="py-1 form-group">
                <input type="number" class="w-100px valid_cantidad form-control form-control-sm producto_${val1.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${val1.cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input( this, '#cantidad_${cont}'); update_price(); ">
                <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${val1.cantidad}" min="0.01" required onkeyup="modificarSubtotales();" onchange="modificarSubtotales();" >            
              </td> 

              <td class="py-1 form-group">
                <input type="number" class="w-135px form-control form-control-sm valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${val1.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
                <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${val1.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
                <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
                <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
                <input type="hidden" class="precio_compra_${cont}" name="precio_compra[]" id="precio_compra[]" value="${val1.precio_compra}"  >
                <input type="hidden" class="precio_venta_descuento_${cont}" name="precio_venta_descuento[]" value="${val1.precio_venta}"  >
              </td>             

              <td class="py-1 text-right">
                <span class="text-right fs-11 subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> 
                <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > 
                <input type="hidden" name="subtotal_no_descuento_producto[]" id="subtotal_no_descuento_producto_${cont}" value="0" >
              </td>
              <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
              
            </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_gr.push({ id_cont: cont });
          modificarSubtotales();        
          
          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });
          $('.valid_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });

          cont++;
          evaluar();
        });
        
        $("#cargando-1-formulario").show();
        $("#cargando-2-fomulario").hide();
      } else{ ver_errores(e); }      
    }
    estado_editar = false;
    
  }).fail( function(e) { ver_errores(e); } );

}

$('#documento_asociado').on('change', function () {
  var id_venta = $(this).val();
  if (id_venta == null || id_venta == '') {
    
  }else{
    mostrar_detalle_venta(id_venta);
  }
  
});

$(document).ready(function () {
  init(); 
});

function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}

// ::::::::::::::::::::::::::::::::::::::::::::: FORMATOS DE IMPRESION :::::::::::::::::::::::::::::::::::::::::::::

function ver_formato_ticket(idventa, tipo_comprobante) {
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl").addClass("modal-md");
  if (tipo_comprobante == '01') {    
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);    
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - BOLETA`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - NOTA CREDITO`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '09') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - GUIA REMISIÓN`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - NOTA DE VENTA`);
    $("#modal-imprimir-comprobante").modal("show");
  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
    return;
  }
  var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
  $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
}


function ver_formato_a4_completo(idventa, tipo_comprobante) {  
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {    
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - BOLETA`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '09') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - GUIA REMISION`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#modal-imprimir-comprobante").modal("show");
  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
    return;
  }  

  var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
  $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);

}

function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
}

// .....::::::::::::::::::::::::::::::::::::: M O D A L I D A D   D E   G U I A   :::::::::::::::::::::::::::::::::::::::..

$('#modalidad_transporte').on('change', function () {
  var val_modalidad = $(this).val();  // Obtiene el valor seleccionado
  console.log(val_modalidad);
  
  if (val_modalidad == null || val_modalidad == '') {
    
  } else {
    if (val_modalidad == '01') {

      $('.div_motivo_privado').hide();
      $('.div_motivo_publico').show();

      $('#numero_documento').rules('remove');
      $('#numero_licencia').rules('remove');
      $('#nombre_razonsocial').rules('remove');
      $('#apellidos_nombrecomercial').rules('remove');

      $('#idpersona_chofer').rules('add', { required: true,  messages: { required: 'Campo requerido' } });

    } else if (val_modalidad == '02') {
      $('.div_motivo_privado').show();
      $('.div_motivo_publico').hide();

      $('#idpersona_chofer').rules('remove');
      
      $('#numero_documento').rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido' } }); 
      $('#numero_licencia').rules('add', { required: true, minlength: 9, maxlength: 10, messages: { required: 'Campo requerido' } }); 
      $('#nombre_razonsocial').rules('add', { required: true, minlength: 3, maxlength: 200, messages: { required: 'Campo requerido' } }); 
      $('#apellidos_nombrecomercial').rules('add', { required: true, minlength: 3, maxlength: 200, messages: { required: 'Campo requerido' } }); 
    }   
    
  }
});

$('#idpersona_chofer').on('change', function () {
  var data = $(this).select2('data')[0]; // Obtenemos el primer dato seleccionado
  var val_placa = '';
  if (data && data.element && data.element.attributes && data.element.attributes.placa_vehiculo) {
    val_placa = data.element.attributes.placa_vehiculo.value;
  } 
  $('#numero_placa').val(val_placa); // Coloca el valor o vacío
});

// .....::::::::::::::::::::::::::::::::::::: AGREGAR CHOFER PUBLICO   :::::::::::::::::::::::::::::::::::::::..
function limpiar_chofer_publico() {
  choice_tipo_documento.setChoiceByValue('6').passedElement.element.dispatchEvent(new Event('change'));
  $('#cp_numero_documento').val('');
  $('#cp_nombre_razonsocial').val('');
  $('#cp_apellidos_nombrecomercial').val('');
  $('#cp_correo').val('');
  $('#cp_celular').val('');
  $('#cp_numero_licencia').val('');
  $('#cp_placa_vehiculo').val('');
}
function modal_add_chofer_publico() {
  $('#modal-agregar-chofer-publico').modal('show');
}

$('#cp_tipo_documento').on('change', function () {
  var val_tipo = $(this).val(); console.log(val_tipo);
  
  if (val_tipo == null || val_tipo == '') {
    $('#cp_tipo_persona_sunat').val('');
  } else if ( val_tipo == '1') {
    $('#cp_tipo_persona_sunat').val('NATURAL');    
    $('.label-nom-raz').html('Nombres');    
    $('.label-ape-come').html('Apellidos');    
  } else if ( val_tipo == '6') {
    $('#cp_tipo_persona_sunat').val('JURÍDICA');
    $('.label-nom-raz').html('Razón Social');    
    $('.label-ape-come').html('Nombre Comercial');    
  }
});

function guardar_editar_chofer_publico(e){ 
  var formData = new FormData($("#form-agregar-chofer-publico")[0]);
  $.ajax({
    url: "../ajax/guia_de_remision.php?op=guardar_editar_chofer_publico",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); 
        if (e.status == true) {
          Swal.fire("Correcto!", "El registro se guardo exitosamente.", "success");
          lista_select2("../ajax/guia_de_remision.php?op=select2_chofer_publico", '#idpersona_chofer', e.data, '.charge_idpersona_chofer');
          limpiar_chofer_publico();
          $('#modal-agregar-chofer-publico').modal('hide');
        } else { ver_errores(e); }
      } catch (err) { console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>'); }
      $("#guardar_registro_chofer_publico").html('Guardar Cambios').removeClass('disabled send-data');
    },
    beforeSend: function () {
      $("#guardar_registro_chofer_publico").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_chofer_publico").css({ width: "0%", });
      $("#barra_progress_chofer_publico div").text("0%");
      $("#barra_progress_chofer_publico_div").show();
    },
    complete: function () {
      $("#barra_progress_chofer_publico").css({ width: "0%", });
      $("#barra_progress_chofer_publico div").text("0%");
      $("#barra_progress_chofer_publico_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

// .....::::::::::::::::::::::::::::::::::::: BUSQUEDA DE PRODUCTOS  :::::::::::::::::::::::::::::::::::::::..

$('#search_producto').on('keydown', function (e) {
   
  if (e.key === 'Enter') {
    e.preventDefault(); // Evita que se envíe un formulario si lo hubiera
    const codigo = $(this).val().trim();
    if (codigo !== '') {
      agregarDetalleComprobante(null, codigo, false);      
      $(this).val(''); // Limpiar el input para el siguiente escaneo
    }
  }
});

$(document).ready(function () {
  $('#search_producto').on('keyup', function () {
    let query = $(this).val();

    if (query.length >= 2) {
      $.getJSON(`../ajax/guia_de_remision.php?op=mostrar_producto_x_nombre`, { search: query }, function (e, textStatus, jqXHR) {
        
        let $resultsList = $('#searchResults');
        $resultsList.empty();

        if (e.data.length > 0) {
          e.data.forEach(function (val, key) {
            $resultsList.append(`<li class="list-group-item hover-text-success list-group-item-action bg-light cursor-pointer py-1" onclick="agregarDetalleComprobante(${val.idproducto},null, false)">               
              <span class="fs-12">${val.nombre} - Stock: ${parseFloat(val.stock)} - S/. ${ formato_miles(val.precio_venta)}</span> <br>
              <span class="fs-10">Cod: ${val.codigo} | Cat: ${val.categoria}</span>
            </li>`);
          });
        } else {
          $resultsList.append(`<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`);
        }
        $resultsList.show();
      });
    } else {
      $('#searchResults').hide();
    }
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#searchInput, #searchResults').length) { $('#searchResults').hide();  }
  });
});

// .....::::::::::::::::::::::::::::::::::::: BUSQUEDA DE DISTRITO ORIGEN  :::::::::::::::::::::::::::::::::::::::..

$(document).ready(function () {
  $('#partida_distrito').on('keyup', function () {
    let query = $(this).val();
    if (query.length >= 2) {
      $.getJSON(`../ajax/ajax_general.php?op=select2_distrito_nombre`, { search_nombre: query }, function (e, textStatus, jqXHR) {        
        let $resultsList = $('#search_distrito_partida');
        $resultsList.empty();
        if (e.data.length > 0) {
          e.data.forEach(function (val, key) {
            $resultsList.append(`<li class="list-group-item hover-text-success list-group-item-action bg-light cursor-pointer py-1" onclick="agregar_distrito_partida('.span_distrito_partida_${val.idubigeo_distrito}', '.span_ubigeo_partida_${val.idubigeo_distrito}')">               
              <span class="fs-11 span_distrito_partida_${val.idubigeo_distrito}">${val.nombre}</span> - <span class="fs-11 span_ubigeo_partida_${val.idubigeo_distrito}">${ val.ubigeo_inei}</span> <br>
              <span class="fs-9">Prov.: ${val.provincia} | Dep.: ${val.departamento}</span>
            </li>`);
          });
        } else {
          $resultsList.append(`<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`);
        }
        $resultsList.show();
      });
    } else {
      $('#search_distrito_partida').hide();
    }
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#searchInput, #search_distrito_partida').length) { $('#search_distrito_partida').hide();  }
  });
});

// .....::::::::::::::::::::::::::::::::::::: BUSQUEDA DE DISTRITO DESTINO  :::::::::::::::::::::::::::::::::::::::..

$(document).ready(function () {
  $('#llegada_distrito').on('keyup', function () {
    let query = $(this).val();
    if (query.length >= 2) {
      $.getJSON(`../ajax/ajax_general.php?op=select2_distrito_nombre`, { search_nombre: query }, function (e, textStatus, jqXHR) {        
        let $resultsList = $('#search_distrito_llegada');
        $resultsList.empty();
        if (e.data.length > 0) {
          e.data.forEach(function (val, key) {
            $resultsList.append(`<li class="list-group-item hover-text-success list-group-item-action bg-light cursor-pointer py-1" onclick="agregar_distrito_llegada('.span_distrito_llegada_${val.idubigeo_distrito}', '.span_ubigeo_llegada_${val.idubigeo_distrito}')">               
              <span class="fs-11 span_distrito_llegada_${val.idubigeo_distrito}">${val.nombre}</span> - <span class="fs-11 span_ubigeo_llegada_${val.idubigeo_distrito}">${ val.ubigeo_inei}</span> <br>
              <span class="fs-9">Prov.: ${val.provincia} | Dep.: ${val.departamento}</span>
            </li>`);
          });
        } else {
          $resultsList.append(`<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`);
        }
        $resultsList.show();
      });
    } else {
      $('#search_distrito_llegada').hide();
    }
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#searchInput, #search_distrito_llegada').length) { $('#search_distrito_llegada').hide();  }
  });
});

function agregar_distrito_partida(span_distrito, span_ubigeo) {
  $('#partida_distrito').val( $(span_distrito).text() );
  $('#partida_ubigeo').val( $(span_ubigeo).text() );
  $('#search_distrito_partida').hide();
  $("#form-agregar-guia-remsion").valid();
}

function agregar_distrito_llegada(span_distrito, span_ubigeo) {
  $('#llegada_distrito').val( $(span_distrito).text() );
  $('#llegada_ubigeo').val( $(span_ubigeo).text() );
  $('#search_distrito_llegada').hide();
  $("#form-agregar-guia-remsion").valid();
}

// .....::::::::::::::::::::::::::::::::::::: BUSQUEDA DE CHOFER  :::::::::::::::::::::::::::::::::::::::..

$(document).ready(function () {
  $('#numero_documento').on('keyup', function () { let query = $(this).val(); buscando_chofer_list(query, '#search_documento_conductor');  });
  $(document).on('click', function (e) {  if (!$(e.target).closest('#searchInput, #search_documento_conductor').length) { $('#search_documento_conductor').hide(); } });

  $('#nombre_razonsocial').on('keyup', function () { let query = $(this).val(); buscando_chofer_list(query, '#search_nombre_conductor');  });
  $(document).on('click', function (e) {  if (!$(e.target).closest('#searchInput, #search_nombre_conductor').length) { $('#search_nombre_conductor').hide(); } });


});

function buscando_chofer_list(search, div_list) {
  if (search.length >= 2) {
    $.getJSON(`../ajax/guia_de_remision.php?op=buscar_chofer`, { search_nombre: search }, function (e, textStatus, jqXHR) {        
      let $resultsList = $(div_list);
      $resultsList.empty();
      if (e.data.length > 0) {
        e.data.forEach(function (val, key) {
          $resultsList.append(`<li class="list-group-item hover-text-success list-group-item-action bg-light cursor-pointer py-1" onclick="mostrar_un_chofer(${val.idpersona})">               
            <span class="fs-11 span_distrito_llegada_${val.idubigeo_distrito}">${val.nombre_razonsocial} ${val.apellidos_nombrecomercial}</span>  <br>
            <span class="fs-9">Tipo: ${val.tipo_persona} | DNI: ${val.numero_documento} | Placa: ${val.placa_vehiculo}</span>
          </li>`);
        });
      } else {
        $resultsList.append(`<li class="list-group-item text-muted bg-light">No se encontraron resultados</li>`);
      }
      $resultsList.show();
    }).fail( function(e) { ver_errores(e); } );
  } else {
    $(div_list).hide();
  }
}

function mostrar_un_chofer(id) {
  $.getJSON(`../ajax/guia_de_remision.php?op=mostrar_un_chofer`, {id:id}, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $('#numero_documento').val(e.data.numero_documento);
      $('#numero_licencia').val(e.data.numero_licencia);
      $('#numero_placa').val(e.data.placa_vehiculo);
      $('#nombre_razonsocial').val(e.data.nombre_razonsocial);
      $('#apellidos_nombrecomercial').val(e.data.apellidos_nombrecomercial);
      
      $('#search_nombre_conductor').hide();
      $('#search_documento_conductor').hide();
      $("#form-agregar-guia-remsion").valid();
    } else {
      ver_errores();
    }
  }).fail( function(e) { ver_errores(e); } );
}


// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   V E R   E S T A D O   S U N A T  :::::::::::::::::::::::::::::::::::::::::::::

function ver_estado_documento(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' || tipo_comprobante == '09' ) {
   
    $("#html-ver-estado").html(`<div class="row" >
      <div class="col-lg-12 text-center">
        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="bx-flashing">Cargando...</h4>
      </div>
    </div>`);
    $("#modal-ver-estado").modal('show');
    $.getJSON(`../ajax/guia_de_remision.php?op=ver_estado_documento`, {idventa: idventa}, function (e, textStatus, jqXHR) {
      if (e.status == true) {
        $("#modal-ver-estado-label").html(`─ VER ESTADO: ${e.data.serie_comprobante}-${e.data.numero_comprobante}`);
        $("#html-ver-estado").html(`
          <b>Estado:</b> ${e.data.sunat_estado} <br>
          <b>Mensaje:</b> ${e.data.sunat_mensaje} <br>
          <b>Observacion:</b> ${e.data.sunat_observacion} <br>
          <b>Codigo:</b> ${e.data.sunat_code} <br>
          <b>Error:</b> ${e.data.sunat_error} <br>
        `);
      } else {
        ver_errores(e);
      }      
    }).fail( function(e) { ver_errores(e); } );
  } else {  
    toastr_warning('Sin estado SUNAT', 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.');
  }
}

function reenviar_doc_a_sunat(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' || tipo_comprobante == '09' ) {
    
    Swal.fire({
      title: "¿Está seguro de reenviar a SUNAT?",
      html: "Verifica que todos lo <b>campos</b> hayan sido actualizados o esten <b>conformes</b>!!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#28a745",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, Enviar!",
      preConfirm: (input) => {
        return fetch(`../ajax/guia_de_remision.php?op=reenviar_sunat&idventa=${idventa}&tipo_comprobante=${tipo_comprobante}`).then(response => {          
          if (!response.ok) { throw new Error(response.statusText) }
          return response.json();
        }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); })        
      },
      showLoaderOnConfirm: true,
    }).then((result) => {
      if (result.isConfirmed) {
        if (result.value.status == true){        
          Swal.fire("Correcto!", "Documento actualizado correctamente.", "success");
          tabla_guias.ajax.reload(null, false);               
        } else {
          ver_errores(result.value);
        }      
      }
    }); 
    
  } else {  
    toastr_warning('Sin respuesta!!', 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.');
  }
}

// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

  $("#form-agregar-guia-remsion").validate({
    ignore: '',
    rules: {
      serie_comprobante:        { required: true },
      modalidad_transporte:     { required: true },
      motivo_traslado:          { required: true,  },
      idcliente:              { required: true,  },

      partida_direccion:        { required: true, minlength: 4},
      partida_distrito:         { required: true, minlength: 4},
      partida_ubigeo:           { required: true, minlength: 6},
      llegada_direccion:        { required: true, minlength: 4},
      llegada_distrito:         { required: true, minlength: 4},
      llegada_ubigeo:           { required: true, minlength: 4},

      numero_documento:         { required: true, minlength: 8, maxlength: 8},
      numero_licencia:          { required: true, minlength: 9, maxlength: 10},
      numero_placa:             { required: true, minlength: 3, maxlength: 15},
      nombre_razonsocial:       { required: true, minlength: 3, maxlength: 200},
      apellidos_nombrecomercial:{ required: true, minlength: 3, maxlength: 200},

      descripcion:       {  minlength: 3,},
    },
    messages: {
      serie_comprobante:        { required: "Campo requerido", },
      modalidad_transporte:   { required: "Campo requerido", },
      motivo_traslado:       { required: "Campo requerido", },
      idcliente:              { minlength: "Minimo {0} caracteres", },
      partida_direccion:        { minlength: "Minimo {0} caracteres", },
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

    submitHandler: function (form) {
      guardar_editar_guia(form);
    },
  }); 

  $('#distrito').on('change', function() { $(this).trigger('blur'); });
  $("#form-agregar-chofer-publico").validate({
    ignore: "",
    rules: {           
      cp_tipo_documento:            { required: true, minlength: 1, maxlength: 2, },       
      cp_numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      cp_nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      cp_apellidos_nombrecomercial: { required: true, minlength: 4, maxlength: 200, },       
      cp_correo:    			          { minlength: 4, maxlength: 100, },       
      cp_celular:    			          { minlength: 8, maxlength: 9, },       

      cp_numero_licencia:    			  { minlength: 9, maxlength: 15, },       
      cp_placa_vehiculo:    			  { required: true, minlength: 4, maxlength: 15, }, 			
    },
    messages: {     
      cp_tipo_documento:    			  { required: "Campo requerido", },
      cp_numero_documento:    			{ required: "Campo requerido", }, 
      cp_nombre_razonsocial:    		{ required: "Campo requerido", }, 
      cp_apellidos_nombrecomercial: { required: "Campo requerido", }, 
      cp_correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      cp_celular:    			          { minlength: "Mínimo {0} caracteres.", }, 

      cp_numero_licencia:    			  { minlength: "Mínimo {0} caracteres.", },
      cp_placa_vehiculo:    			  { required: "Campo requerido",  minlength: "Mínimo {0} caracteres.",  maxlength: "Máximo {0} caracteres.", }, 
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
      guardar_editar_chofer_publico(e);      
    },
  });
  $('#distrito').rules('add', { required: true, messages: {  required: "Campo requerido" } });

  $("#form-agregar-producto").validate({
    ignore: "",
    rules: {           
      codigo:         { required: true, minlength: 2, maxlength: 20, },       
      categaria:    	{ required: true },       
      u_medida:    		{ required: true },       
      marca:    			{ required: true },       
      nombre:    			{ required: true, minlength: 2, maxlength: 20,  },       
      descripcion:    { required: true, minlength: 2, maxlength: 500, },       
      stock:          { required: true, min: 0,  },       
      stock_min:      { required: true, min: 0,  }, 
      precio_v:       { required: true, min: 0,  },       
      precio_c:       { required: true, min: 0,  },	
    },
    messages: {     
      cogido:    			{ required: "Campo requerido", },
      categaria:    	{ required: "Seleccione una opción", },
      u_medida:    		{ required: "Seleccione una opción", },
      marca:    			{ required: "Seleccione una opción", },
      nombre:    			{ required: "Campo requerido", }, 
      descripcion:    { required: "Campo requerido", },       
      stock:          { required: "Campo requerido", },       
      stock_min:      { required: "Campo requerido", }, 
      precio_v:       { required: "Campo requerido", },       
      precio_c:       { required: "Campo requerido", },	
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
      guardar_editar_producto(e);      
    },
  });

});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function update_price() {
  toastr_success("Actualizado!!",`Precio Actualizado.`, 700);
}
function reload_idcliente(){ lista_select2("../ajax/guia_de_remision.php?op=listar_cliente", '#idcliente', null, '.charge_idcliente'); }
function reload_idpersona_chofer(){ lista_select2("../ajax/guia_de_remision.php?op=select2_chofer_publico", '#idpersona_chofer', null, '.charge_idpersona_chofer'); }
function reload_documento_asociado(){   lista_select2("../ajax/ajax_general.php?op=select2_lista_comprobante", '#documento_asociado', null, '.charge_documento_asociado'); }
