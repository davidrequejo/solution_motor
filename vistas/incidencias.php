<?php
//Activamos el almacenamiento en el buffer
ob_start();
require "../config/funcion_general.php";
session_start();
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" data-bg-img="bgimg4" style="--primary-rgb: 208, 2, 149;" loader="enable">

  <head>

    <?php $title_page = "Incidencias";
    include("template/head.php"); ?>
    <style>
      .choices {
        margin-bottom: 0px !important;
      }
    </style>

  </head>

  <body id="body-gastos-trab">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['gastos_trabajador'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xl-3">
                <div class="card custom-card">
                  <div class="card-body p-0">
                    <div class="p-3 d-grid border-bottom border-block-end-dashed justify-content-center">
                      <div class="btn_guardar_new">
                      <button class="btn btn-primary d-flex align-items-center justify-content-center" onclick="limpiar_form(); view_form(2);">
                        <i class="ri-add-circle-line fs-16 align-middle me-1"></i>Crear Incidencia
                      </button>
                      </div>
                      <div class="btn_guargar_cancelar" style="display: none;">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="limpiar_form(); view_form(1)">Cancelar</button>
                      <button type="button" class="btn btn-primary btn-guardar">Crear</button> </div>
                    </div>
                    <!-- LISTA DE CATEOGRIAS -->
                    <div class="p-3 task-navigation border-bottom border-block-end-dashed div_add_inc" style="display: none;">
                      <div class="modal-body px-4">
                        <form name="form-agregar-incidencia" id="form-agregar-incidencia" method="POST" class="needs-validation" novalidate>
                          <input type="hidden" id="idincidencia" name="idincidencia">
                          <div class="row gy-2">
                            <!-- Asunto -->
                            <div class="col-xl-12">
                              <div class="form-group">
                                <label for="actividad" class="form-label">Asunto</label>
                                <!-- <input type="text" class="form-control" name="actividad" id="actividad" placeholder=" Descripción Asunto"> -->
                                <textarea type="text" class="form-control" name="actividad" id="actividad" cols="30" rows="1" laceholder=" Descripción Asunto"></textarea>
                              </div>
                            </div>
                            <!-- Trabajadores multiple marca-->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                              <div class="form-group">
                                <label for="Asignado">Asignado a</label>
                                <div class="select2-purple">
                                  <select name="id_trabajador[]" id="id_trabajador" class="form-control select2" multiple="multiple" data-dropdown-css-class="select2-purple" data-placeholder="Seleccione" style="width: 100%;"> </select>
                                </div>
                              </div>
                            </div> 
                            <!-- Fecha -->
                            <div class="col-xl-12">
                              <div class="form-group">
                                <label class="form-label">Fecha</label>
                                <div class="input-group">
                                  <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
                                  <input type="date" class="form-control" id="adDate" name="creacionfecha" placeholder="Elige fecha">
                                </div>
                              </div>
                            </div>
                            <!-- Fecha -->
                            <div class="col-12 class_fecha_fin" style="display: none;">
                              <div class="form-group">
                                <label class="form-label">Fecha Fin</label>
                                <div class="input-group">
                                  <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
                                  <input type="date" class="form-control" id="adDatefin" name="fecha_fin_inc" placeholder="Elige fecha">
                                </div>
                              </div>
                            </div>
                            <!-- estado incidencia -->
                            <div class="col-xl-12 class_estado_inc" style="display: none;" >
                              <div class="form-group">
                                <label for="actividad" class="form-label">Estado Incidencia</label>
                                <select class="form-select" id="estado_inc" name="estado_inc">
                                  <option value="1">Abierto</option>
                                  <option value="0">Cerrado</option>
                                </select> 
                              </div>
                            </div>
                            <!-- Prioridad -->
                            <div class="col-xl-12">
                              <div class="form-group">
                                <label class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad" name="prioridad">
                                  <option value="CRÍTICO">1 CRÍTICO</option>
                                  <option value="ALTO">2 ALTO</option>
                                  <option value="MEDIO">3 MEDIO</option>
                                  <option value="BAJO">4 BAJO</option>
                                </select>                
                              </div>
                            </div>
                            <!-- Categoría -->
                            <div class="mb-1 col-12">
                              <div class="form-group">
                                <label for="categoria" class="form-label">Categoría: </label>
                                <select name="categoria" id="categoria" class="form-select" required>
                                </select>
                              </div>
                            </div>
                            <!-- Detalle -->
                            <div class="col-xl-12">
                              <div class="form-group">
                                <label for="actividad_detalle" class="form-label">Detalle</label>
                                <textarea class="form-control" id="actividad_detalle" name="actividad_detalle" cols="30" rows="3"></textarea>
                              </div>
                            </div>
                          </div>
                          <!-- Submit -->
                          <button type="submit" style="display: none;" id="submit-form-incidencia">Submit</button>
                        </form>
                      </div>
                    </div>
                    <div class="p-3 task-navigation border-bottom border-block-end-dashed div_view_info">
                      <ul class="list-unstyled task-main-nav lista-items mb-0">
                        <li class="px-0 pt-0">
                          <span class="fs-11 text-muted op-7 fw-semibold">Categorías</span>
                        </li>
                      </ul>
                      <ul class="list-unstyled task-main-nav mb-0">

                        <li class="px-0 pt-2">
                          <span class="fs-11 text-muted op-7 fw-semibold">Estados</span>
                        </li>
                        <li class="active">
                          <a href="javascript:void(0);">
                            <div class="d-flex align-items-center">
                              <span class="me-2 lh-1">
                                <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-primary"></i>
                              </span>
                              <span class="flex-fill text-nowrap" onclick="estado('TODOS')">Todos</span>
                            </div>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0);">
                            <div class="d-flex align-items-center">
                              <span class="me-2 lh-1">
                                <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-primary"></i>
                              </span>
                              <span class="flex-fill text-nowrap" onclick="estado('1')">Abiertos</span>
                            </div>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0);">
                            <div class="d-flex align-items-center">
                              <span class="me-2 lh-1">
                                <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-primary"></i>
                              </span>
                              <span class="flex-fill text-nowrap" onclick="estado('0')">Cerrados</span>
                            </div>
                          </a>
                        </li>
                      </ul>
                    </div>
                    <div class="p-3 text-center">
                      <img src="../assets/images/media/media-66.png" alt="">
                    </div>
                    <!-- FIN LISTA DE CATEOGRIAS -->
                  </div>
                </div>
              </div>
              <!-- LISTA DE INCIDENCIAS POR PRIORIDAD  -->

              <div class="col-xl-9">
                <div class="row">
                  <div class="col-xl-12">
                    <div class="card custom-card">
                      <div class="card-body p-0">
                        <div class="d-flex p-3 align-items-center justify-content-between">
                          <div>
                            <h6 class="fw-semibold mb-0">Prioridad</h6>
                          </div>
                          <!-- PRIORIDAD -->
                          <div>
                            <ul class="nav nav-tabs nav-tabs-header mb-0 d-sm-flex d-block" role="tablist">
                              <li class="nav-item m-1">
                                <a class="nav-link active cursor-pointer" data-bs-toggle="tab" role="tab" aria-current="page" aria-selected="true" onclick="prioridad('TODOS')">TODOS</a>
                              </li>
                              <li class="nav-item m-1">
                                <a class="nav-link cursor-pointer" data-bs-toggle="tab" role="tab" aria-current="page" aria-selected="true" onclick="prioridad('CRÍTICO')">CRÍTICO</a>
                              </li>
                              <li class="nav-item m-1">
                                <a class="nav-link cursor-pointer" data-bs-toggle="tab" role="tab" aria-current="page" aria-selected="true" onclick="prioridad('ALTO')">ALTO</a>
                              </li>
                              <li class="nav-item m-1">
                                <a class="nav-link cursor-pointer" data-bs-toggle="tab" role="tab" aria-current="page" aria-selected="true" onclick="prioridad('MEDIO')">MEDIO</a>
                              </li>
                              <li class="nav-item m-1 ">
                                <a class="nav-link cursor-pointer" data-bs-toggle="tab" role="tab" aria-current="page" aria-selected="true" onclick="prioridad('BAJO')">BAJO</a>
                              </li>
                            </ul>
                          </div>
                          <!-- FIN PRIORIDAD -->

                          <div>
                            <div class="dropdown">
                              <button class="btn btn-icon btn-sm btn-light btn-wave waves-light waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                              </button>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0);">Select All</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">Share All</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">Delete All</a></li>
                              </ul>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-content task-tabs-container">
                    <div class="tab-pane show active p-0" id="all-tasks" role="tabpanel">
                      <div class="row list_incidencias" id="tasks-container">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- <ul class="pagination justify-content-end">
                  <li class="page-item disabled">
                    <a class="page-link">Previous</a>
                  </li>
                  <li class="page-item"><a class="page-link" href="javascript:void(0);">1</a></li>
                  <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                  <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                  <li class="page-item">
                    <a class="page-link" href="javascript:void(0);">Next</a>
                  </li>
                </ul> -->
              </div>
              <!-- LISTA DE INCIDENCIAS POR PRIORIDAD  -->

            </div>
            <!--End::row-1 -->


          </div>
        </div>
        <!-- End::app-content -->
      <?php } else {
        $title_submodulo = 'Gasto de Trabajador';
        $descripcion = 'Lista de Gasto de Trabajador del sistema!';
        $title_modulo = 'Gasto';
        include("403_error.php");
      } ?>


      <!-- MODAL - AGREGAR TRABAJADOR - charge 3 -->
      <div class="modal fade modal-effect" id="modal-agregar-fecha_fin" tabindex="-1" aria-labelledby="Modal-agregar-fecha_finLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-body px-4">

              <form name="form-agregar-fecha_fin" id="form-agregar-fecha_fin" method="POST" class="needs-validation" novalidate>

                <div class="row" id="cargando-3-fomulario">
                  <!-- idpersona -->
                  <input type="hidden" name="id_incidenciaupdate" id="id_incidenciaupdate" />

                  <!-- Fecha -->
                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-label">Fecha Fin</label>
                      <div class="input-group">
                        <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
                        <input type="date" class="form-control" id="addDatefin" name="fecha_fin" placeholder="Elige fecha">
                      </div>
                    </div>
                  </div>

                </div> <!-- /.row -->

                <button type="submit" style="display: none;" id="submit-form-fecha_fin">Submit</button>
              </form>

            </div>

            <div class="modal-footer">

              <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
              <button type="button" class="btn btn-sm btn-success label-btn btn-guardarfecha_fin"><i class="bx bx-save bx-tada"></i> Guardar</button>

            </div>
          </div>
        </div>
      </div>
      <!-- End::Modal-Agregar-Proveedor -->


      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>
    <!-- Flat Picker JS -->
    <script src="../assets/libs/flatpickr/flatpickr.min.js"></script>

    <!-- Internal To-Do-List JS -->
    <!-- <script src="../assets/js/todolist.js"></script> -->

    <!-- Custom JS -->
    <script src="../assets/js/custom.js"></script>

    <script src="scripts/incidencias.js?version_jdl=1.16"></script>

    <script>
      flatpickr("#adDate", { enableTime: false, dateFormat: "d-m-Y", });

      flatpickr("#adDatefin", { enableTime: false, dateFormat: "d-m-Y", });

      flatpickr("#addDatefin", { enableTime: false, dateFormat: "d-m-Y", });



      $(function() {
        $('[data-toggle="tooltip"]').tooltip();
      });
    </script>


  </body>

  </html>
<?php
}
ob_end_flush();
?>