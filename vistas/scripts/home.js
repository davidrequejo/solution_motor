function init(){

  mostrar_tecnico_redes();
  mostrar_planes();
  mostrar_preguntas_frecuentes();
  mostrar_comentarios();
  
}


//  :::::::::::: C O M E N T A R I O - C L I E N T E ::::::::::::::::::::
function mostrar_comentarios() {
  
  $.post("ajax/home.php?op=mostrar_comentarioC",  function(e, status){
    e = JSON.parse(e); console.log(e);
    if (e.status == true){

      $('#comentarios_cliente').html(''); //limpiar el div

      e.data.forEach((val, key)=> {

        var puntuacion =  val.landing_puntuacion;
        var estrellasHTML = '';
        for (var i = 0; i < 5; i++) {
          if (i < puntuacion) {
            estrellasHTML += '<i class="ri-star-fill text-warning"></i>'; // Estrella llena
          } else {
            estrellasHTML += '<i class="ri-star-line text-warning"></i>'; // Estrella vacía
          }
        }

        var fecha = moment(val.landing_fecha);
        var tiempo_transcurrido = fecha.fromNow();

        var codigoHTML = `<div class="swiper-slide">
        <div class="card custom-card testimonial-card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <span class="avatar avatar-md avatar-rounded me-3">
                <img src="assets/images/faces/15.jpg" alt="">
              </span>
              <div>
                <p class="mb-0 fw-semibold fs-14">${val.nombre_completo}</p>
                <p class="mb-0 fs-10 fw-semibold text-muted">${val.centro_poblado}</p>
              </div>
            </div>
            <div class="mb-3">
              <span class="text-muted">${val.landing_descripcion}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <span class="text-muted">Puntuación : </span>
                <span class="text-warning d-block ms-1">
                  ${estrellasHTML}
                </span>
              </div>
              <div class="float-end fs-12 fw-semibold text-muted text-end">
                <span>${tiempo_transcurrido}</span>
              </div>
            </div>
          </div>
        </div>
      </div>`;

        $('#comentarios_cliente').append(codigoHTML);

      });
      
    }else {
      ver_errores(response);
    }
  }).fail(function (e) { ver_errores(e); });
}

//  :::::::::::::::: T R A B A J A D O R - T E C N I C O  R E D E S   :::::::::::::::: 
function mostrar_tecnico_redes() {
  
  $.post("ajax/home.php?op=mostrar_tecnico_redes",  function(e, status){
    e = JSON.parse(e); console.log(e);
    if (e.status == true){

      $('#tecnico_redes').html(''); //limpiar el div

      e.data.forEach((val, key)=> {

        var codigoHTML = `<div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mx-auto">
          <div class="card custom-card text-center team-card ">
            <div class="card-body p-5">
              <span class="avatar avatar-xxl avatar-rounded mb-3 team-avatar">
                <img src="assets/images/brand-logos/logo-short.png" alt="">
              </span>
              <p class="fw-semibold fs-15 mb-0 text-default">${val.nombre_completo}</p>
              <span class="text-muted fs-12 text-primary fw-semibold">${val.cargo}</span>
              <p class="text-muted mt-2 fs-13">${val.landing_descripcion}</p>
              <div class="mt-2">
                <a href="https://wa.me/+51929676935?text=Deseo%20cotizar%20los%20planes%20de%20internet" class="btn btn-light text-success" target="_blank"><i class="bi bi-whatsapp"></i> Contacto</a>
              </div>
            </div>
          </div>
        </div>`;

        $('#tecnico_redes').append(codigoHTML);

      });
      
    }else {
      ver_errores(response);
    }
  }).fail(function (e) { ver_errores(e); });
}


//  :::::::::::::::: P L A N E S :::::::::::::::: 
function mostrar_planes() {
  
  $.post("ajax/home.php?op=mostrar_planes",  function(e, status){
    e = JSON.parse(e); console.log(e);
    if (e.status == true){

      $('#planes').html(''); //limpiar el div

      e.data.forEach((val, key)=> {
        var Instalacion = val.idplan == 1 ? "Instalación por zona de cobertura" : "Instalación";

        var codigoHTML = `<div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-12 border-end border-inline-end-dashed">
        <div class="p-4">
          <h6 class="fw-semibold text-center">${val.plan}</h6>
          <div class="py-3 d-flex align-items-center justify-content-center">
            <i class="bx bx-broadcast bx-burst la-4x text-primary"></i>
            <div class="text-end ms-5">
              <p class="fs-25 fw-semibold mb-0">S/ ${ parseFloat(val.costo) }</p>
              <p class="text-muted fs-11 fw-semibold mb-0">por mes</p>
            </div>
          </div>
          <div class="text-center fs-12 px-3 pt-3 mb-0">
          ${val.landing_caracteristica}
          </div>
          <div class="d-grid">
            <a href="https://wa.me/+51929676935?text=Deseo%20cotizar%20los%20planes%20de%20internet" target="_blank" class="btn btn-primary-light btn-wave">Empezar</a>
          </div>
        </div>
      </div>`;

        $('#planes').append(codigoHTML);

      });
      
    }else {
      ver_errores(response);
    }
  }).fail(function (e) { ver_errores(e); });
}


// ::::::::::: P R E G U N T A S   F R E C U E N T E S ::::::::::
function mostrar_preguntas_frecuentes() {
  $.post("ajax/home.php?op=mostrar_preguntas_frecuentes",  function(e, status){
    e = JSON.parse(e); 
    console.log(e);
    if (e.status == true){
      $('#accordionFAQ1').html(''); // Limpiar el div
      $('#accordionFAQ2').html(''); // Limpiar el div
      var dataLength = e.data.length;
      var groupSize = Math.ceil(dataLength / 2); // Calcular el tamaño de cada grupo

      var accordion1 = $('#accordionFAQ1');
      var accordion2 = $('#accordionFAQ2');

      e.data.forEach((val, key) => {
        var codigoHTML = `<div class="accordion-item">
          <h2 class="accordion-header" id="headingcustomicon${key + 1}">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsecustomicon${key + 1}" aria-expanded="false" aria-controls="collapsecustomicon${key + 1}">
              ${val.pregunta}
            </button>
          </h2>
          <div id="collapsecustomicon${key + 1}" class="accordion-collapse collapse ${(key == 0) ? 'show' : ''}" aria-labelledby="headingcustomicon${key + 1}" data-bs-parent="#accordionFAQ${(key < groupSize) ? '1' : '2'}">
            <div class="accordion-body">
              ${val.respuesta}
            </div>
          </div>
        </div>`;

        // Añadir al grupo correspondiente
        if (key < groupSize) {
          accordion1.append(codigoHTML);
        } else {
          accordion2.append(codigoHTML);
        }
      });
    } else {
      ver_errores(response);
    }
  }).fail(function (e) { 
    ver_errores(e); 
  });
}

$(document).ready(function () {
  init();
});

$('#precio-anio').on('click', function () {
  toastr_warning('No Disponible', 'Este plan no está disponible en este momento, tenga paciencia.', 700);
});


//  :::::::::::::::: ENVIAR MENSAJE WHATSAPP   :::::::::::::::: 
function enviar_mensaje_whatsapp(){  
  var nombre  = $('#contact_address_name').val();
  var phone   = $('#contact_address_phone').val();
  var address = $('#contact_address_address').val();
  var message = $('#contact_address_message').val();

  const phone_wa    = '+51929676935'; // Reemplaza con el número de teléfono en formato internacional
  const message_wa  = `*Nombre:*%20${nombre}%0A*Cel:*%20${phone}%0A*Direc:*%20${address}%0A*Mensaje:*%20${message}`;
 
  const whatsappUrl = `https://wa.me/${phone_wa}?text=${message_wa}`;  // Construir la URL de WhatsApp
  
  const a = document.createElement('a'); // Crear un elemento <a> dinámicamente
  a.href = whatsappUrl;
  a.target = '_blank';  
  a.click(); // Simular un clic en el enlace
}


// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..
$(function () {
  $("#form-contactar-home").validate({
    rules: {
      contact_address_name: { required: true, maxlength:100 },
      contact_address_phone: { required: true, number:true, minlength: 9, maxlength:9},
      contact_address_address: { required: true},
      contact_address_message: { maxlength: 200},
    },
    messages: {
      contact_address_name: { required: "Campo requerido",maxlength: "Máximo {0}" },
      contact_address_phone: { required: "Campo requerido",minlength: "Mínimo {0}", maxlength:"Máximo {0}" },
      contact_address_address: { required: "Campo requerido" },
      contact_address_message: { maxlength: "Máximo {0}" },      
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
      enviar_mensaje_whatsapp(e);
    },
  });
});


// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
