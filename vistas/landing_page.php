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

    <?php $title_page = "Landing pages";
    include("template/head.php"); ?>

    <!-- Quill Editor CSS -->
    <link rel="stylesheet" href="../assets/libs/quill/quill.snow.css">
    <link rel="stylesheet" href="../assets/libs/quill/quill.bubble.css">
  </head>

  <body id="body-usuario">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>


    <div class="page">
      <?php include("template/header.php"); ?>
      <?php include("template/sidebar.php"); ?>

      <?php if ($_SESSION['configuracion'] == 1) { ?>
        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">
            <div class="row">

              <div class="col-12 col-sm-12 mt-4">
                <div class="card card-primary card-outline card-tabs mb-0">
                  <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs tab-style-2 mb-1" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cliente" data-bs-toggle="tab" data-bs-target="#cliente-pane" type="button" role="tab" aria-selected="true"><i class="ri-user-line me-1 align-middle"></i>CLIENTES</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="equipo" data-bs-toggle="tab" data-bs-target="#equipo-pane" type="button" role="tab" aria-selected="true"><i class="fas fa-user-tie"></i>EQUIPO</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="precio" data-bs-toggle="tab" data-bs-target="#precio-pane" type="button" role="tab" aria-selected="false"><i class="ri-tools-line me-1 align-middle"></i>PRECIOS</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="faqs" data-bs-toggle="tab" data-bs-target="#faqs-pane" type="button" role="tab" aria-selected="false"><i class="ri-dashboard-line me-1 align-middle"></i>FAQ'S</button>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-12 col-xl-12 p-0">
                <div class="tab-content">

                  <div class="tab-pane fade show active" id="cliente-pane" role="tabpanel" tabindex="0">
                    <div class="row">

                      <!-- :::::::::::::::: C O M E N T A R I O   C L I E N T E :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-12 col-xxl-12">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Comentarios de los Clientes</p>
                                <span class="fs-semibold text-muted">Administra los comentarios.</span>
                              </div>
                            </div>
                          </div>
                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Comentarios</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body">
                            <div class="table-responsive" id="div-tabla-comentarioC">
                              <table id="tabla-comentarioC" class="table table-bordered w-100" style="width: 100%;">
                                <thead>
                                  <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Acciones</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Comentario</th>
                                    <th>Puntuación</th>
                                    <th class="text-center">Estado</th>

                                    <th>Lista de Clientes</th>
                                    <th>Centro Poblado</th>
                                    <th>Comentario</th>
                                    <th>Puntuación (Estrellas)</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                  <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Acciones</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Comentario</th>
                                    <th>Puntuación</th>
                                    <th class="text-center">Estado</th>
                                    
                                    <th>Lista de Clientes</th>
                                    <th>Centro Poblado</th>
                                    <th>Comentario</th>
                                    <th>Puntuación (Estrellas)</th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                            <div id="div-form-comentarioC" style="display: none;">
                              <form name="form-agregar-comentarioC" id="form-agregar-comentarioC" method="POST" class="needs-validation" novalidate>
                                <div class="row" id="cargando-9-fomulario">
                                  <input type="hidden" name="idpersona_cliente" id="idpersona_cliente" />

                                  <div class="col-md-5">
                                    <div class="form-label">
                                      <label for="nombre_cliente" class="form-label">Nombre Completo(*)</label>
                                      <input class="form-control" name="nombre_cliente" id="nombre_cliente" readonly />
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="centro_poblado" class="form-label">Centro Poblado(*)</label>
                                      <input type="text" class="form-control" name="centro_poblado" id="centro_poblado" readonly />
                                    </div>
                                  </div>
                                  <div class="col-md-2">
                                    <div class="form-group">
                                      <label for="puntuacion" class="form-label">Puntuación:</label>
                                      <!-- disenio de estrellas que se pueden seleccionar -->
                                      <div class="puntuacion-container">
                                        <i class="ri-star-line fs-24 text-warning puntuacion-star" data-value="1"></i>
                                        <i class="ri-star-line fs-24 text-warning puntuacion-star" data-value="2"></i>
                                        <i class="ri-star-line fs-24 text-warning puntuacion-star" data-value="3"></i>
                                        <i class="ri-star-line fs-24 text-warning puntuacion-star" data-value="4"></i>
                                        <i class="ri-star-line fs-24 text-warning puntuacion-star" data-value="5"></i>
                                        </div>
                                      <input type="hidden" class="form-control" name="puntuacionc" id="puntuacionc" /> <!-- Aquí se almacenan el número de estrallas seleccionadas -->
                                    </div>
                                  </div>
                                  <div class="col-md-2">
                                    <div class="form-group">
                                      <label for="fecha_comentarioc" class="form-label">Fecha(*)</label>
                                      <input type="date" class="form-control" name="fecha_comentarioc" id="fecha_comentarioc" />
                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <div id="editor2">
                                    </div>
                                    <textarea name="descripcion_comentario" id="descripcion_comentario" class="hidden"></textarea>
                                  </div>

                                </div>
                                <div class="row" id="cargando-10-fomulario" style="display: none;">
                                  <div class="col-lg-12 text-center">
                                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                                    <h4 class="bx-flashing">Cargando...</h4>
                                  </div>
                                </div>
                                <button type="submit" style="display: none;" id="submit-form-comentarioC">Submit</button>
                              </form>
                            </div>
                          </div>
                          <div id="footer-comentarioC" name="footer-comentarioC" class="card-footer d-flex justify-content-end d-none">
                            <button id="cancelar_comentarioC" name="cancelar_comentarioC" class="btn-modal-effect btn btn-danger label-btn btn-cancelar m-r-10px" style="display: none;" onclick="show_hide_form_comentarioC(1);"><i class="ri-close-line label-btn-icon me-2"> </i> Cancelar</button>
                            <button id="guardar_comentarioC" name="guardar_comentarioC" class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>

                  <div class="tab-pane fade" id="equipo-pane" role="tabpanel" tabindex="0">
                    <div class="row">

                      <!-- :::::::::::::::: T R A B A J A D O R E S :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-12 col-xxl-12">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">

                              <div>
                                <p class="fw-semibold fs-18 mb-0">Trabajadores</p>
                                <span class="fs-semibold text-muted">Administra los trabajadores de la página principal.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Trabajadores</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body">
                            <div class="table-responsive" id="div-tabla-trabj">
                              <table id="tabla-trabj" class="table table-bordered w-100" style="width: 100%;">
                                <thead>
                                  <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Acciones</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Estado</th>

                                    <th>Lista de Trabajadores</th>
                                    <th>Cargo</th>
                                    <th>Descripción</th>
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

                                    <th>Lista de Trabajadores</th>
                                    <th>Cargo</th>
                                    <th>Descripción</th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                            <div id="div-form-trabj" style="display: none;">
                              <form name="form-agregar-trabj" id="form-agregar-trabj" method="POST" class="needs-validation" novalidate>
                                <div class="row" id="cargando-1-fomulario">
                                  <input type="hidden" name="idpersona_trabajador" id="idpersona_trabajador" />

                                  <div class="col-md-8">
                                    <div class="form-label">
                                      <label for="nombre_trabj" class="form-label">Nombre completo(*)</label>
                                      <input class="form-control" name="nombre_trabj" id="nombre_trabj" readonly />
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label for="cargo_trabj" class="form-label">Cargo(*)</label>
                                      <input type="text" class="form-control" name="cargo_trabj" id="cargo_trabj" readonly />
                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <div id="editor1">
                                    </div>
                                    <textarea name="descripcion_trabj" id="descripcion_trabj" class="hidden"></textarea>
                                  </div>

                                </div>
                                <div class="row" id="cargando-2-fomulario" style="display: none;">
                                  <div class="col-lg-12 text-center">
                                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                                    <h4 class="bx-flashing">Cargando...</h4>
                                  </div>
                                </div>
                                <button type="submit" style="display: none;" id="submit-form-trabj">Submit</button>
                              </form>
                            </div>
                          </div>
                          <div id="footer-trabj" name="footer-trabj" class="card-footer d-flex justify-content-end d-none">
                            <button id="cancelar_trabj" name="cancelar_trabj" class="btn-modal-effect btn btn-danger label-btn m-r-10px" style="display: none;" onclick="show_hide_form_trabj(1);"><i class="ri-close-line label-btn-icon me-2"> </i> Cancelar</button>
                            <button id="guardar_trabj" name="guardar_trabj" class="btn-modal-effect btn btn-success label-btn m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="tab-pane fade" id="precio-pane" role="tabpanel" tabindex="0">
                    <div class="row">

                      <!-- :::::::::::::::: P L A N E S :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 col-xxl-6">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">

                              <div>
                                <p class="fw-semibold fs-18 mb-0">Planes</p>
                                <span class="fs-semibold text-muted">Administra los planes de manera eficiente.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Planes</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body">
                            <div class="table-responsive" id="div-tabla-plan">
                              <table id="tabla-plan" class="table table-bordered w-100" style="width: 100%;">
                                <thead>
                                  <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Acciones</th>
                                    <th>Descripción</th>
                                    <th>Costo</th>
                                    <th class="text-center">Estado</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                  <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Acciones</th>
                                    <th>Descripción</th>
                                    <th>Costo</th>
                                    <th class="text-center">Estado</th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                            <div id="div-form-plan" style="display: none;">
                              <form name="form-agregar-plan" id="form-agregar-plan" method="POST" class="needs-validation" novalidate>
                                <div class="row" id="cargando-3-fomulario">
                                  <input type="hidden" name="idplan" id="idplan" />

                                  <div class="col-md-8">
                                    <div class="form-label">
                                      <label for="nombre_plan" class="form-label">Nombre del Plan(*)</label>
                                      <input class="form-control" name="nombre_plan" id="nombre_plan" readonly />
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label for="costo_plan" class="form-label">Monto(*)</label>
                                      <input type="number" class="form-control" name="costo_plan" id="costo_plan" readonly />
                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <div id="editor">
                                    </div>
                                    <textarea name="caracteristicas" id="caracteristicas" class="hidden"></textarea>
                                  </div>

                                </div>
                                <div class="row" id="cargando-4-fomulario" style="display: none;">
                                  <div class="col-lg-12 text-center">
                                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                                    <h4 class="bx-flashing">Cargando...</h4>
                                  </div>
                                </div>
                                <button type="submit" style="display: none;" id="submit-form-plan">Submit</button>
                              </form>
                            </div>
                          </div>
                          <div id="footer-plan" name="footer-plan" class="card-footer d-flex justify-content-end d-none">
                            <button id="cancelar_plan" name="cancelar_plan" class="btn-modal-effect btn btn-danger label-btn btn-cancelar m-r-10px" style="display: none;" onclick="show_hide_form_plan(1);"><i class="ri-close-line label-btn-icon me-2"> </i> Cancelar</button>
                            <button id="guardar_plan" name="guardar_plan" class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>

                  <div class="tab-pane fade" id="faqs-pane" role="tabpanel" tabindex="0">
                    <div class="row">

                      <!-- :::::::::::::::: P R E G U N T A S   F R E C U E N T E S :::::::::::::::: -->
                      <div class="col-sm-12 col-md-12 col-lg-8 col-xl-12 col-xxl-12">
                        <div class="d-md-flex d-block align-items-center justify-content-between mb-4 mt-2 page-header-breadcrumb">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="limpiar_form_preguntas();" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" data-bs-target="#modal-agregar-preguntas"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                              <div>
                                <p class="fw-semibold fs-18 mb-0">Preguntas Frecuentes</p>
                                <span class="fs-semibold text-muted">Administra las preguntas y respuestas.</span>
                              </div>
                            </div>
                          </div>

                          <div class="btn-list mt-md-0 mt-2">
                            <nav>
                              <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Preguntas</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Home</li>
                              </ol>
                            </nav>
                          </div>
                        </div>
                        <div class="card custom-card">
                          <div class="card-body table-responsive">
                            <table id="tabla-preguntas-frecuentes" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Pregunta</th>
                                  <th>Respuesta</th>
                                  <th class="text-center">Estado</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Pregunta</th>
                                  <th>Respuesta</th>
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

            <!-- MODAL :: preguntas frecuentes - charge 7 -->
            <div class="modal fade modal-effect" id="modal-agregar-preguntas" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-preguntas" aria-hidden="true">
              <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Pregunta Frecuente</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <!-- form start -->
                    <form id="form-agregar-preguntas" name="form-agregar-preguntas" method="POST" autocomplete="off">
                      <div class="card-body">
                        <div class="row" id="cargando-5-fomulario">
                          <!-- id	preguntas_frecuentes -->
                          <input type="hidden" name="	idpreguntas_frecuentes" id="	idpreguntas_frecuentes" />

                          <!-- Pregunta -->
                          <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                              <label class="form-label" for="pregunta_pf">Pregunta</label>
                              <input type="text" name="pregunta_pf" id="pregunta_pf" class="form-control" placeholder="preguntas." />
                            </div>
                          </div>

                          <!-- Respuesta -->
                          <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                              <label class="form-label" for="respuesta_pf">Respuesta</label>
                              <textarea name="respuesta_pf" id="respuesta_pf" class="form-control" placeholder="respuesta"></textarea>
                            </div>
                          </div>

                          <div class="row" id="cargando-6-fomulario" style="display: none;">
                            <div class="col-lg-12 text-center">
                              <i class="fas fa-spinner fa-pulse fa-6x"></i><br />
                              <br />
                              <h4>Cargando...</h4>
                            </div>
                          </div>
                        </div>
                        <!-- /.card-body -->
                        <button type="submit" style="display: none;" id="submit-form-preguntas">Submit</button>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_preguntas();"><i class="las la-times"></i> Close</button>
                    <button type="submit" class="btn btn-sm btn-success" id="guardar_registro_preguntas"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::modal-preguntas -->



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

    <!-- Quill Editor JS -->
    <script src="../assets/libs/quill/quill.min.js"></script>
    <!-- Internal Quill JS -->
    <script src="../assets/js/quill-editor.js"></script>

    <script src="scripts/landing_page.js?version_jdl=1.43"></script>

  </body>

  </html>
<?php
}
ob_end_flush();
?>