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
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" data-bg-img="bgimg4" style="--primary-rgb: 208, 2, 149;" loader="enable">

  <head>

    <?php $title_page = "Gastos";
    include("template/head.php"); ?>

  </head>

  <body id="body-usuario">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>

      <?php if ($_SESSION['general'] == 1) { ?>
        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">
            <div class="row">

              <div class="col-12 col-sm-12 mt-4">
                <div class="card card-primary card-outline card-tabs mb-0">
                  <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs tab-style-2 mb-1" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dato-cliente" data-bs-toggle="tab" data-bs-target="#dato-cliente-pane" type="button" role="tab" aria-selected="true"><i class="ri-user-line me-1 align-middle"></i>CLIENTE</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rol-trab" data-bs-toggle="tab" data-bs-target="#rol-trab-pane" type="button" role="tab" aria-selected="false"><i class="ri-tools-line me-1 align-middle"></i>TRABAJADOR</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rol-general" data-bs-toggle="tab" data-bs-target="#rol-general-pane" type="button" role="tab" aria-selected="false"><i class="ri-dashboard-line me-1 align-middle"></i>GENERAL</button>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-12 col-xl-12 p-0">
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="dato-cliente-pane" role="tabpanel" tabindex="0">
                    <div class="row">

                      <!-- :::::::::::::::: P L A N E S :::::::::::::::: -->
                     

                      <!-- :::::::::::::::: Z O N A :::::::::::::::: -->
                      

                      <!-- :::::::::::::::: C E N T R O    P O B L A D O :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 col-xxl-6">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="limpiar_centro_poblado();" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" data-bs-target="#modal-agregar-centro-poblado"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Centro Poblado</p>
                                <span class="fs-semibold text-muted">Administra de manera eficiente tus lugares.</span>
                              </div>
                            </div>
                          </div>
                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Centro Poblado</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body table-responsive">
                            <table id="tabla-centro-poblado" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th>Descripción</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th>Descripción</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="tab-pane fade" id="rol-trab-pane" role="tabpanel" tabindex="0">
                    <div class="row">
                      <!-- :::::::::::::::: C A R G O    T R A B A J A D O R :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 col-xxl-6">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="limpiar_form_ct();" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" data-bs-target="#modal-agregar-c-t"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Cargo Trabajador</p>
                                <span class="fs-semibold text-muted">Administra de manera eficiente los cargos.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Cargo Trabajador</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body table-responsive">
                            <table id="tabla-cargo-trabajador" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="rol-general-pane" role="tabpanel" tabindex="0">
                    <div class="row">
                      <!-- :::::::::::::::: B A N C O S :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 col-xxl-6">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="limpiar_banco();" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" data-bs-target="#modal-agregar-bancos"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Método de Pago</p>
                                <span class="fs-semibold text-muted">Administra de manera eficiente tus Método de Pago.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Método de Pago</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body table-responsive">
                            <table id="tabla-bancos" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th>Formato Cta/CCI</th>
                                  <th class="text-center">Estado</th>

                                  <th>Nombre</th>
                                  <th>Alias</th>
                                  <th>Formato Cta</th>
                                  <th>Formato CCI</th>
                                  <th>Formato Cta. Dtrac.</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th>Formato Cta/CCI</th>
                                  <th class="text-center">Estado</th>

                                  <th>Nombre</th>
                                  <th>Alias</th>
                                  <th>Formato Cta</th>
                                  <th>Formato CCI</th>
                                  <th>Formato Cta. Dtrac.</th>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div>
                      <!-- :::::::::::::::: C A T   I N C I D E N C I A S ::::::::::::::::
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 col-xxl-6">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="limpiar_form_cat_inc();" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" data-bs-target="#modal-agregar-cat-inc"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Categorías Incidencias</p>
                                <span class="fs-semibold text-muted">Administra de manera eficiente tus Categorias de incidencias.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Categorías</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body table-responsive">
                            <table id="tabla-inc" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div> -->
                      <!-- :::::::::::::::: C A T   O T R O S   G A S T O S :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 col-xxl-6">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="limpiar_form_ie_categoria();" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" data-bs-target="#modal-agregar-ie-categoria"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Categorías Gastos</p>
                                <span class="fs-semibold text-muted">Administra de manera eficiente tus Categorias.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Categorías</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body table-responsive">
                            <table id="tabla-ie-categoria" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th>Descripción</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Nombre</th>
                                  <th>Descripción</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- MODAL:: REGISTRAR PLAN - charge 1 -->
            <div class="modal fade modal-effect" id="modal-agregar-plan" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-pagoLabel" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollabel">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-pagoLabel1">Planes</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="form-agregar-plan" id="form-agregar-plan" method="POST" class="needs-validation" novalidate>
                      <div class="row" id="cargando-1-fomulario">
                        <input type="hidden" name="idplan" id="idplan">

                        <div class="col-md-8">
                          <div class="form-label">
                            <label for="nombre_plan" class="form-label">Nombre del Plan(*)</label>
                            <input class="form-control" name="nombre_plan" id="nombre_plan" />
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="costo_plan" class="form-label">Monto(*)</label>
                            <input type="number" class="form-control" name="costo_plan" id="costo_plan" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-2-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-plan">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_form();"><i class="las la-times"></i> Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_plan"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-registrar-plan -->

            <!-- MODAL:: REGISTRAR ZONA - charge 3 -->
            <div class="modal fade modal-effect" id="modal-agregar-zona" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-zonaLabel" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollabel">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-zonaLabel">Zona</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="form-agregar-zona" id="form-agregar-zona" method="POST" class="needs-validation" novalidate>
                      <div class="row" id="cargando-3-fomulario">
                        <input type="hidden" name="idzona_antena" id="idzona_antena">

                        <div class="col-12">
                          <div class="form-label">
                            <label for="nombre_zona" class="form-label">Nombre del Zona(*)</label>
                            <input class="form-control" name="nombre_zona" id="nombre_zona" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form-group">
                            <label for="ip_antena" class="form-label">Ip Zona(*)</label>
                            <input type="text" class="form-control" name="ip_antena" id="ip_antena" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-4-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-zona">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_zona();"><i class="las la-times"></i> Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_zona"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-registrar-zona -->

            <!-- MODAL::REGISTRAR CENTRO POBLADO - charge 5 -->
            <div class="modal fade modal-effect" id="modal-agregar-centro-poblado" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-centro-poblado" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollabel">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-centro-poblado">Centro poblado</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="form-agregar-centro-poblado" id="form-agregar-centro-poblado" method="POST" class="needs-validation" novalidate>
                      <div class="row" id="cargando-5-fomulario">
                        <input type="hidden" name="idcentro_poblado" id="idcentro_poblado">

                        <div class="col-12">
                          <div class="form-label">
                            <label for="nombre_cp" class="form-label">Nombre(*)</label>
                            <input class="form-control" name="nombre_cp" id="nombre_cp" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form-group">
                            <label for="descripcion_cp" class="form-label">Descripcion</label>
                            <textarea class="form-control" name="descripcion_cp" id="descripcion_cp" cols="30" rows="2"></textarea>
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-6-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-cp">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_centro_poblado();"><i class="las la-times"></i> Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_cp"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-registrar-zona -->

            <!-- MODAL :: BANCOS - charge 7 -->
            <div class="modal fade modal-effect" id="modal-agregar-bancos" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-bancos" aria-hidden="true">
              <div class="modal-dialog modal-dialog-scrollable modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Agregar Método de Pago</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <!-- form start -->
                    <form id="form-agregar-bancos" name="form-agregar-bancos" method="POST" autocomplete="off">
                      <div class="card-body">
                        <div class="row" id="cargando-7-fomulario">
                          <!-- id banco -->
                          <input type="hidden" name="idbancos" id="idbancos" />

                          <!-- Nombre -->
                          <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                              <label class="form-label" for="nombre_b">Nombre</label>
                              <input type="text" name="nombre_b" id="nombre_b" class="form-control" placeholder="Nombre del banco." onkeyup="mayus(this);" />
                            </div>
                          </div>

                          <!-- alias -->
                          <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                              <label class="form-label" for="alias">Alias</label>
                              <input type="text" name="alias" id="alias" class="form-control" placeholder="Alias del banco." onkeyup="mayus(this);" />
                            </div>
                          </div>

                          <!-- Formato cuenta bancaria -->
                          <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                              <label class="form-label" for="formato_cta">Formato Cuenta Bancaria</label>
                              <input type="text" name="formato_cta" id="formato_cta" class="form-control" placeholder="Formato." value="00000000" data-inputmask="'mask': ['99-99-99-99', '99 99 99 99']" data-mask />
                            </div>
                          </div>

                          <!-- Formato CCI -->
                          <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                              <label class="form-label" for="formato_cci">Formato CCI</label>
                              <input type="text" name="formato_cci" id="formato_cci" class="form-control" placeholder="Formato." value="00000000" data-inputmask="'mask': ['99-99-99-99', '99 99 99 99']" data-mask />
                            </div>
                          </div>

                          <!-- Formato CCI -->
                          <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                              <label class="form-label" for="formato_detracciones">Formato Detracción</label>
                              <input type="text" name="formato_detracciones" id="formato_detracciones" class="form-control" placeholder="Formato." value="00000000" data-inputmask="'mask': ['99-99-99-99', '99 99 99 99']" data-mask />
                            </div>
                          </div>

                          <!--img-material-->
                          <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                            <label class="form-label" for="imagen1">Imagen</label>
                            <div style="text-align: center;">
                              <img onerror="this.src='../assets/images/default/img_defecto_banco.png';" src="../assets/images/default/img_defecto_banco.png" class="img-thumbnail" id="imagen1_i" style="cursor: pointer !important; height: 100% !important;" width="auto" />
                              <input style="display: none;" type="file" name="imagen1" id="imagen1" accept="image/*" />
                              <input type="hidden" name="imagen1_actual" id="imagen1_actual" />
                              <div class="text-center" id="imagen1_nombre"><!-- aqui va el nombre de la FOTO --></div>
                            </div>
                          </div>

                          <!-- barprogress -->
                          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px;">
                            <div class="progress" id="div_barra_progress_banco">
                              <div id="barra_progress_banco" class="progress-bar" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: 0%;">
                                0%
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="row" id="cargando-8-fomulario" style="display: none;">
                          <div class="col-lg-12 text-center">
                            <i class="fas fa-spinner fa-pulse fa-6x"></i><br />
                            <br />
                            <h4>Cargando...</h4>
                          </div>
                        </div>
                      </div>
                      <!-- /.card-body -->
                      <button type="submit" style="display: none;" id="submit-form-bancos">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_banco();"><i class="las la-times"></i> Close</button>
                    <button type="submit" class="btn btn-sm btn-success" id="guardar_registro_banco"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-banco -->

            <!-- MODAL - VER IMAGEN BANCO-->
            <div class="modal fade" id="modal-ver-perfil-banco" role="dialog" tabindex="-1" aria-labelledby="modal-ver-perfil-banco" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content bg-color-0202022e shadow-none border-0">
                  <div class="modal-header">
                    <h4 class="modal-title text-light foto-banco">Imagen</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="perfil-banco" class="class-style">

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-ver-imagen-banco -->

            <!-- MODAL:: REGISTRAR CARGO TRABAJADOR- charge 1 -->
            <div class="modal fade modal-effect" id="modal-agregar-c-t" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-c-tLabel" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollabel">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title">Cargo Trabajador</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="form-agregar-c-t" id="form-agregar-c-t" method="POST" class="row needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-9-fomulario">
                        <input type="hidden" name="idcargo_trabajador" id="idcargo_trabajador">

                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="nombre_plan" class="form-label">Nombre:</label>
                            <input class="form-control" name="nombre_ct" id="nombre_ct" onkeyup="mayus(this);" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-10-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-ct">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_ct();"><i class="las la-times"></i> Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_ct"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-registrar-cargo-trabajador -->

            <!-- MODAL:: CATEGORIA INCIDENCIA - charge 11 -->
            <div class="modal fade modal-effect" id="modal-agregar-cat-inc" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-cat-tLabel" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollabel">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title">Categoría Incidencia</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="form-agregar-inc" id="form-agregar-inc" method="POST" class="row needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-11-fomulario">
                        <input type="hidden" name="idincidencia_categoria" id="idincidencia_categoria">

                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="nombre_plan" class="form-label">Nombre:</label>
                            <input class="form-control" name="nombre_inc" id="nombre_inc" onkeyup="mayus(this);" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-12-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-inc">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_cat_inc();"><i class="las la-times"></i> Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_inc"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-categoria_incidencia -->

            <!-- MODAL:: INGRESO EGRESO CATEGORIA - charge 13 -->
            <div class="modal fade modal-effect" id="modal-agregar-ie-categoria" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-ie-categoriaLabel" aria-hidden="true">
              <div class="modal-dialog modal-md modal-agregar-ie-categorialabel">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title">Categoría Gastos</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="form-agregar-ie-categoria" id="form-agregar-ie-categoria" method="POST" class="needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-13-fomulario">
                        <input type="hidden" name="id_ie_categoria" id="id_ie_categoria">

                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="nombre_ie_cat" class="form-label">Nombre:</label>
                            <input class="form-control" name="nombre_ie_cat" id="nombre_ie_cat" onkeyup="mayus(this);" />
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="descr_ie_cat" class="form-label">Descripcion:</label>
                            <textarea  class="form-control" name="descr_ie_cat" id="descr_ie_cat"></textarea>
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-14-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-ie-categoria">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_ie_categoria();"><i class="las la-times"></i> Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_ie_categoria"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-categoria_incidencia -->

          </div>
        </div>
        <!-- End::app-content -->
      <?php } else {
        $title_submodulo = 'General';
        $descripcion = 'Lista de General del sistema!';
        $title_modulo = 'Configuracion';
        include("403_error.php");
      } ?>

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <!-- <script src="scripts/plan.js?version_jdl=1.16"></script>
    <script src="scripts/zona.js?version_jdl=1.16"></script>
    <script src="scripts/categoria_incidencia.js?version_jdl=1.16"></script> -->
    <script src="scripts/centro_poblado.js?version_jdl=1.16"></script>
    <script src="scripts/bancos.js?version_jdl=1.16"></script>
    <script src="scripts/cargo_trabajador.js?version_jdl=1.16"></script>
    <script src="scripts/ingreso_egreso_categoria.js?version_jdl=1.16"></script>



  </body>

  </html>
<?php
}
ob_end_flush();
?>