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
    <?php $title_page = "Unidad de Medida";
    include("template/head.php"); ?>
  </head>

  <body id="body-unidad-de-medida">
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
                <!-- <button type="button" class="btn-modal-effect btn btn-primary label-btn m-r-10px" data-bs-toggle="modal" data-bs-target="#modal-agregar-u-m" onclick="limpiar_form_um();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button> -->
                <div>
                  <p class="fw-semibold fs-18 mb-0">Unidad de Medida</p>
                  <span class="fs-semibold text-muted">Administra las unidades de medida.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Unidad de Medida</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Articulos</li>
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
                    <div class="table-responsive" id="div-tabla">
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-u-m">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Equivalencia</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>ID</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Equivalencia</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>ID</th>
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

      <!-- Start::Modal-registrar-unidad-medida -->
      <div class="modal fade modal-effect" id="modal-agregar-u-m" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-u-mLabel">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title" id="modal-agregar-u-mLabel1">Registrar Unidad de Medida</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form name="formulario-u-m" id="formulario-u-m" method="POST" class="row needs-validation" novalidate>
                <div class="row gy-2" id="cargando-1-fomulario">
                  <input type="hidden" name="idsunat_c03_unidad_medida" id="idsunat_c03_unidad_medida">


                  <div class="col-md-6">
                    <div class="form-label">
                      <label for="nombre_um" class="form-label">Nombre(*)</label>
                      <input type="text" class="form-control" name="nombre_um" id="nombre_um" onkeyup="mayus(this);" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-label">
                      <label for="abreviatura_um" class="form-label">Abreviatura(*)</label>
                      <input type="text" class="form-control" name="abreviatura_um" id="abreviatura_um" onkeyup="mayus(this);" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-label">
                      <label for="equivalencia_um" class="form-label">Equivalencia(*)</label>
                      <input type="number" class="form-control" name="equivalencia_um" id="equivalencia_um" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="descr_um" class="form-label">Descripción</label>
                      <input type="text" class="form-control" name="descr_um" id="descr_um" onkeyup="mayus(this);" />
                    </div>
                  </div>
                </div>
                <div class="row" id="cargando-2-fomulario" style="display: none;">
                  <div class="col-lg-12 text-center">
                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h4 class="bx-flashing">Cargando...</h4>
                  </div>
                </div>
                <button type="submit" style="display: none;" id="submit-form-u-m">Submit</button>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_um();"><i class="las la-times fs-lg"></i> Close</button>
              <button type="button" class="btn btn-primary btn-guardar" id="guardar_registro_u_m"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- End::Modal-registrar-unidad-medida -->

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/unidad_medida.js?version_jdl=1.16"></script>
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