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
    <?php $title_page = "Gastos";
    include("template/head.php"); ?>
  </head>

  <body id="body-correlacion-comprobante">
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
                <button type="button" class="btn-modal-effect btn btn-primary label-btn m-r-10px"  onclick="toastr_info('ERROR DE PERMISO', 'Usted <b>no está autorizado</b> para crear nuevos tipos de comprobantes. Por favor, consulte con el <b>administrador del sistema</b> para obtener más información.');"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Correlación de Numeración</p>
                  <span class="fs-semibold text-muted">Administra la correlación de numeración</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Correlación de Numeración</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Administracion</li>
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
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-correlacion-compb">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Serie</th>
                            <th>Número</th>
                            <th>UN1001</th>
                            <th>Estado</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Serie</th>
                            <th>Número</th>
                            <th>UN1001</th>
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
      <div class="modal fade modal-effect" id="modal-corrlacion-compb" role="dialog" tabindex="-1" aria-labelledby="modal-corrlacion-compbLabel">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title" id="modal-corrlacion-compbLabel1">Actulizar Correlacion</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form name="formulario-correlacion-compb" id="formulario-correlacion-compb" method="POST" class="needs-validation" novalidate>
                <div class="row gy-2" id="cargando-1-fomulario">
                  <input type="hidden" name="idtipo_comprobante" id="idtipo_comprobante">

                  <div class="col-md-6">
                    <div class="form-label">
                      <label for="codg" class="form-label">Código(*)</label>
                      <input type="number" class="form-control bg-light input-valido" name="codg" id="codg" readonly data-bs-toggle="tooltip" title="No es editable" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="un1001" class="form-label">Código UN-1001(*)</label>
                      <input type="number" class="form-control bg-light input-valido" name="un1001" id="un1001" readonly data-bs-toggle="tooltip" title="No es editable" />
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="abrt" class="form-label">Abreviatura(*)</label>                      
                      <textarea class="form-control bg-light input-valido" name="abrt" id="abrt" onkeyup="mayus(this);"  cols="30" rows="2" readonly data-bs-toggle="tooltip" title="No es editable"></textarea>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-label">
                      <label for="nombre" class="form-label">Nombre(*)</label>
                      <textarea class="form-control bg-light input-valido" name="nombre" id="nombre" onkeyup="mayus(this);" cols="30" rows="3" readonly data-bs-toggle="tooltip" title="No es editable"></textarea>                      
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="serie" class="form-label">Serie(*)</label>
                      <input type="text" class="form-control" name="serie" id="serie" onkeyup="mayus(this);" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="numero" class="form-label">Número(*)</label>
                      <input type="number" class="form-control" name="numero" id="numero"  />
                    </div>
                  </div>
                  
                </div>
                <div class="row" id="cargando-2-fomulario" style="display: none;">
                  <div class="col-lg-12 text-center">
                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h4 class="bx-flashing">Cargando...</h4>
                  </div>
                </div>
                <button type="submit" style="display: none;" id="submit-form-correlacion-compb">Submit</button>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form();"><i class="las la-times fs-lg"></i> Close</button>
              <button type="button" class="btn btn-primary btn-guardar" id="guardar_registro_correlacion_compb"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
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

    <script src="scripts/correlacion_comprobante.js?version_jdl=1.16"></script>

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