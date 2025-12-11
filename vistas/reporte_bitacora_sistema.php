<?php
//Activamos el almacenamiento en el buffer
ob_start();
date_default_timezone_set('America/Lima');
require "../config/funcion_general.php";
session_start();
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" loader="enable">

  <head>
    <?php $title_page = "Bitacora de Sistema";
    include("template/head.php"); ?>
    <style>

      #tabla-bitacora-sistema_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-bitacora-sistema_filter label { width: 100% !important;  }
      #tabla-bitacora-sistema_filter label input { width: 100% !important;   }
      
    </style>
  </head>

  <body id="body-productos">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['reporte_bitacora_sistema'] == 1) { ?> <!-- .:::: PERMISO DE MODULO ::::. -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Bitacora de Sistema</p>
                    <span class="fw-semibold text-muted">Valida cada movimiento claro y organizado.</span>
                  </div>
                </div>
              </div>
              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <!-- <li class="breadcrumb-item">
                    <div class="form-check form-switch mb-0">
                      <label class="form-check-label" for="generar-cod-correlativo"></label>
                      <input class="form-check-input cursor-pointer" type="checkbox" id="generar-cod-correlativo" name="generar-cod-correlativo" checked data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Activar generador código de barra correlativamente automático">
                    </div>
                  </li> -->
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Reporte</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bitacora</li>
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
                    <div class="card-header">
                      <!-- ::::::::::::::::::::: FILTRO: CUENTA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2 mx-2">
                        <div class="form-group">
                          <label for="filtro_fecha_i" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_i();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                            Fecha Inicio</label>
                          <input type="date" class="form-control" name="filtro_fecha_i" id="filtro_fecha_i" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                        </div>
                      </div>
                      <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2 mx-2">
                        <div class="form-group">
                          <label for="filtro_fecha_f" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_f();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                            Fecha Fin</label>
                          <input type="date" class="form-control" name="filtro_fecha_f" id="filtro_fecha_f" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                        </div>
                      </div>                      
                      
                      <!-- ::::::::::::::::::::: FILTRO: USUARIO :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mx-2">
                        <div class="form-group">
                          <label for="filtro_usuario" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_usuario();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Usuario
                            <span class="charge_filtro_usuario"></span>
                          </label>
                          <select class="form-control" name="filtro_usuario" id="filtro_usuario" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>

                      <!-- ::::::::::::::::::::: FILTRO: USUARIO :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mx-2">
                        <div class="form-group">
                          <label for="filtro_modulo" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_modulo();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Modulo
                            <span class="charge_filtro_modulo"></span>
                          </label>
                          <select class="form-control" name="filtro_modulo" id="filtro_modulo" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>

                    </div>
                    <div class="card-body">
                      <!-- ------------ Tabla de Productos ------------- -->
                      <div class="table-responsive" id="div-tabla">
                        <table class="table table-bordered w-100" style="width: 100%;" id="tabla-bitacora-sistema">
                          <thead>
                            <tr>
                              <th colspan="15" class="bg-danger buscando_tabla" style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                            </tr>
                            <tr>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">
                                <center>#</center>
                              </th>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">Modifico en</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Creado</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Accion</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Usuario</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Cargo</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;"><i class="bi bi-upc"></i> Detalle</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-center">
                                <center>#</center>
                              </th>
                              <th class="text-center">Modifico en</th>
                              <th>Creado</th>
                              <th>Accion</th>
                              <th>Usuario</th>
                              <th>Cargo</th>
                              <th><i class="bi bi-upc"></i> Detalle</th>
                            </tr>
                          </tfoot>

                        </table>

                      </div>
                      <!-- ------------ Formulario de Productos ------------ -->
                      <div class="div-form" style="display: none;">

                      </div>
                    </div>
                    <div class="card-footer border-top-0">
                      <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                      <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::row-1 -->


            <!-- MODAL - VER DETALLE -->
            <div class="modal fade modal-effect" id="modal-ver-detalle-producto" tabindex="-1" aria-labelledby="modal-ver-detalle-productoLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="modal-ver-detalle-productoLabel1"><b>Detalles</b> - Producto</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="html-detalle-producto"></div>
                    <div class="text-center" id="html-detalle-imagen"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal-VerDetalles -->

          </div>
        </div>
        <!-- End::app-content -->
      <?php } else {
        $title_submodulo = 'Producto';
        $descripcion = 'Lista de Producto del sistema!';
        $title_modulo = 'Articulos';
        include("403_error.php");
      } ?>

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/reporte_bitacora_sistema.js?version_jdl=1.16"></script>
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