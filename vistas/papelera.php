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
    <?php $title_page = "Papelera";
    include("template/head.php"); ?>
  </head>

  <body id="body-papelera">
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
              <h3 class="m-2"><i class="nav-icon fas fa-trash-alt"></i></h3>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Papelera</p>
                  <span class="fs-semibold text-muted">Administra la papelera de reciclaje.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Papelera</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Configuración</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">
            <div class="col-xxl-12 col-xl-12">
              <div>
                <div class="card custom-card">
                  <div class="card-body">
                    <div id="div-tabla" class="table-responsive">
                      <table id="tabla-papelera" class="table table-bordered w-100" style="width: 100%;">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Módulo</th>
                            <th>Archivo</th>
                            <th>Descripcion</th>
                            <th>Creado el</th>
                            <th>Reciclado el</th> 
                            
                            <th>Archivo</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Módulo</th>
                            <th>Archivo</th>
                            <th>Descripcion</th>
                            <th>Creado el</th>
                            <th>Reciclado el</th> 
                            
                            <th>Archivo</th>
                          </tr>
                        </tfoot>

                      </table>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End::row-1 -->

        </div>
      </div>
      <!-- End::app-content -->

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/papelera.js?version_jdl=1.16"></script>
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