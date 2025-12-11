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
    <?php $title_page = "Doc. de Identidad";
    include("template/head.php"); ?>
  </head>

  <body id="body-tipo-de-tribujos">
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
                <button type="button" class="btn-modal-effect btn btn-primary label-btn m-r-10px" data-bs-toggle="modal" data-bs-target="#modal-doc-identidad" onclick="limpiar_form();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Documento de Identidad</p>
                  <span class="fs-semibold text-muted">Administra los tipos de documentos de identidad.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Documento de Identidad</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Administración</li>
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
                    <!-- Tabla Tipo de Tributos -->
                    <div class="table-responsive" id="div-tabla">
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-doc-identidad">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Código SUNAT</th>
                            <th>Estado</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Código SUNAT</th>
                            <th>Estado</th>
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

      <!-- Start::Modal-registrar-tipo-tributo -->
      <div class="modal fade modal-effect" id="modal-doc-identidad" role="dialog" tabindex="-1" aria-labelledby="modal-doc-identidadLabel">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title" id="modal-doc-identidadLabel1"></h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form name="formulario-doc-identidad" id="formulario-doc-identidad" method="POST" class="row needs-validation" novalidate>
                <div class="row gy-2" id="cargando-1-fomulario">
                  <input type="hidden" name="idsunat_c06_doc_identidad" id="idsunat_c06_doc_identidad">

                  <div class="col-md-8">
                    <div class="form-label">
                      <label for="nombre" class="form-label">Nombre(*)</label>
                      <input type="text" class="form-control" name="nombre" id="nombre" onkeyup="mayus(this);" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="abrt" class="form-label"> Abreviatura(*)</label>
                      <input type="text" class="form-control" name="abrt" id="abrt" onkeyup="mayus(this);"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="codg" class="form-label">Código SUNAT(*)</label>
                      <input type="number" class="form-control" name="codg" id="codg"/>
                    </div>
                  </div>
                </div>
                <div class="row" id="cargando-2-fomulario" style="display: none;">
                  <div class="col-lg-12 text-center">
                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h4 class="bx-flashing">Cargando...</h4>
                  </div>
                </div>
                <button type="submit" style="display: none;" id="submit-form-doc-identidad">Submit</button>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form();"><i class="las la-times fs-lg"></i> Close</button>
              <button type="button" class="btn btn-primary btn-guardar" id="guardar_registro_doc_identidad"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
            </div>
          </div>
        </div>
      </div> 
      <!-- End::Modal-registrar-tipo-tributo -->

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/documento_de_identidad.js?version_jdl=1.16"></script>
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