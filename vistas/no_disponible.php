<?php
//Activamos el almacenamiento en el buffer
ob_start();
date_default_timezone_set('America/Lima'); require "../config/funcion_general.php";
session_start();
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" data-bg-img="bgimg4" style="--primary-rgb: 208, 2, 149;" loader="enable">

  <head>
    <?php $title_page = "No disponible";
    include("template/head.php"); ?>
  </head>

  <body id="body-productos">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>      

      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <div>
                  <p class="fw-semibold fs-18 mb-0">Modulo no disponible</p>
                  <span class="fs-semibold text-muted">Consulte con el administrador o creador de este sistema.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">              
              <nav>
                <ol class="breadcrumb mb-0">                  
                  <li class="breadcrumb-item"><a href="escritorio.php">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">No disponible</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">
            <div class="col-xl-12">
              <div class="about-container mb-4">
                <div class="aboutus-banner">
                  <div class="aboutus-banner-content">
                    <div class="row">
                      <div class="col-xxl-6 col-xl-6">
                        <div class="row justify-content-center">
                          <div class="col-xxl-9 col-xl-9 col-lg-10 col-md-8 col-sm-10 col-12">
                            <div class="p-3 text-start mb-4">
                              <h6 class="fw-semibold text-fixed-white pb-4">
                                <span class="about-heading-white">Detalles</span>
                              </h6>
                              <h3 class="fw-semibold mb-2 text-fixed-white">ESTE <span class="text-success">MODULO</span> NO ESTA DISPONIBLE</h3>
                              <p class="mb-4 fw-normal op-7 fs-16">
                                Estimado <?php echo $_SESSION["user_nombre"];?>, nos encantaría escucharte. 
                                Si tienes alguna pregunta, comentario o sugerencia, ¡no dudes en ponerte en contacto con nosotros! 
                                Estamos aquí para ayudarte y nos esforzamos por brindarte la mejor experiencia posible.
                              </p> 
                              <a href="https://wa.link/1dpx0i" class="btn btn-success btn-wave"><i class="bi bi-whatsapp"></i> Contactenos</a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xxl-6 col-xl-6 aboutus-banner-img">
                        <img src="../assets/images/media/media-80.svg" class="img-fluid" alt="">
                      </div>
                      <div class="col-xl-12 about-company-stats">
                        <div class="row justify-content-center">
                          <div class="col-xxl-5 col-xl-8 col-lg-10 col-md-8 col-sm-10 col-10">
                            <div class="card custom-card text-default shadow border">
                              <div class="card-body p-0">
                                <div class="row">
                                  <div class="col-xl-4 col-xxl-4 col-lg-4 col-md-4 col-sm-4 about-company-stats-border">
                                    <div class="text-center p-4 w-100 h-100 d-flex align-items-center justify-content-center">
                                      <div>
                                        <span class="fw-semibold">Facebook</span> <br>                                        
                                        <span class="avatar avatar-sm avatar-rounded">
                                          <a href="https://www.facebook.com/profile.php?id=100086343481837">
                                            <img src="../assets/images/jdl/logo-facebook.jpg" class="w-30px" alt="" />
                                          </a>                                            
                                        </span>                                        
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-xl-4 col-xxl-4 col-lg-4 col-md-4 col-sm-4 about-company-stats-border">
                                    <div class="text-center p-4 w-100 h-100 d-flex align-items-center justify-content-center">
                                      <div>
                                        <span class="fw-semibold">Instagran</span> <br>
                                        <span class="avatar avatar-sm avatar-rounded">
                                          <a href="https://www.instagram.com/jdltechnology/">
                                            <img src="../assets/images/jdl/logo-instagram.jpg" class="w-30px" alt="" />
                                          </a>                                            
                                        </span> 
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-xl-4 col-xxl-4 col-lg-4 col-md-4 col-sm-4">
                                    <div class="text-center p-4 w-100 h-100 d-flex align-items-center justify-content-center">
                                      <div>
                                        <span class="fw-semibold">TikTok</span> <br>
                                        <span class="avatar avatar-sm avatar-rounded">
                                          <a href="https://www.tiktok.com/@jdltechnology">
                                            <img src="../assets/images/jdl/logo-tiktok.jpg" class="w-30px" alt="" />
                                          </a>                                            
                                        </span> 
                                        </p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="py-5 px-3 bg-light">
                  <div class="row justify-content-center">
                    <div class="col-xxl-8 col-xl-10">                      
                      <div class="row pb-5 px-3 about-motto">
                        <div class="col-xxl-8 col-xl-8 col-lg-10 col-md-10 col-sm-12">
                          <div class="text-justify">
                            <div class="text-dark fs-26 fw-semibold mb-4"><span class="about-heading">Querido usuario,</span></div>
                            <p class="fs-14 mb-4 text-muted">Lamentablemente, en este momento el módulo que estás intentando acceder no se encuentra disponible. 
                              Estamos trabajando diligentemente para resolver este inconveniente y restaurar su funcionalidad lo antes posible. 
                              Por favor, acepta nuestras disculpas por cualquier molestia que esto pueda causarte.
                            </p>
                            <p class="fs-14 mb-4 text-muted"> Te invitamos a explorar otros aspectos de nuestro sistema que siguen disponibles, 
                              y agradecemos tu paciencia y comprensión mientras trabajamos para mejorar tu experiencia con nosotros.
                            </p>                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="py-5 px-3 bg-primary rounded-bottom">
                  <div class="row justify-content-center">
                    <div class="col-xl-4">
                      <div class="text-center">
                        <h6 class="fw-semibold text-fixed-white pb-4 mb-0">
                          <span class="about-heading-white">Para cualquier consulta</span>
                        </h6>
                        <p class=" text-fixed-white"> No dudes en contactarnos en cualquier momento.</p>
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Escribenos" aria-label="Ask Here"   aria-describedby="button-addon2">
                          <button class="btn btn-success btn-wave" type="button" id="button-addon2">Enviar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--End::row-1 -->     

        </div>
      </div>
      <!-- End::app-content -->
      
      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>