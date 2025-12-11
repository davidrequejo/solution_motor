<?php
  //Activamos el almacenamiento en el buffer
  ob_start();
  date_default_timezone_set('America/Lima'); require "../config/funcion_general.php";
  session_start();
  if (!isset($_SESSION["user_nombre"])){
    header("Location: index.php?file=".basename($_SERVER['PHP_SELF']));
  }else{

    ?>
      <!DOCTYPE html>
      <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" data-bg-img="bgimg4" style="--primary-rgb: 208, 2, 149;" loader="enable">

        <head>
          
          <?php $title_page = "Caja"; include("template/head.php"); ?>    

        </head> 

        <body id="body-caja">

          <?php include("template/switcher.php"); ?>
          <?php include("template/loader.php"); ?>

          <div class="page">
            <?php include("template/header.php") ?>
            <?php include("template/sidebar.php") ?>
            <?php if($_SESSION['caja']==1) { ?>

            <!-- Start::app-content -->
            <div class="main-content app-content">
              <div class="container-fluid">

                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                  <div>
                    <div class="d-md-flex d-block align-items-center ">
                      <button class="btn-modal-effect btn btn-primary label-btn m-r-10px" onclick="verificar_estado_caja();"><i class="ri-safe-line label-btn-icon me-2"></i>Aperturar </button>
                    <div>
                        <p class="fw-semibold fs-18 mb-0">Caja</p>
                        <span class="fs-semibold text-muted">Administra la caja de la empresa.</span>
                      </div>                
                    </div>
                  </div>
                  
                  <div class="btn-list mt-md-0 mt-2">              
                    <nav>
                      <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Caja</a></li>
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
                          <!-- ------------- Tabla CAJA ---------------- -->
                          <div id="div-tabla" class="table-responsive">
                            <table id="tabla-caja" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Fecha Apertura</th>
                                  <th>Fecha Cierre</th>
                                  <th>Monto Apertura</th>
                                  <th>Monto Cierre</th>
                                  <th>Estado</th>

                                  <th>Usuario</th>
                                  <th>Fecha Apertura</th>
                                  <th>Hora Apertura</th>
                                  <th>Fecha Cierre</th>
                                  <th>Hora Cierre</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Fecha Apertura</th>
                                  <th>Fecha Cierre</th>
                                  <th>Monto Apertura</th>
                                  <th>Monto Cierre</th>
                                  <th>Estado</th>

                                  <th>Usuario</th>
                                  <th>Fecha Apertura</th>
                                  <th>Hora Apertura</th>
                                  <th>Fecha Cierre</th>
                                  <th>Hora Cierre</th>
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
            <?php } else { $title_submodulo ='Caja'; $descripcion ='Lista de Caja del sistema!'; $title_modulo = 'Caja'; include("403_error.php"); }?>   

            
            <!-- Start::Modal-Registrar-Caja -->
            <div class="modal fade modal-effect" id="modal-registrar-caja" role="dialog" tabindex="-1" aria-labelledby="modal-registrar-cajaLabel">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-registrar-cajaLabel1"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="formulario-caja" id="formulario-caja" method="POST" class="needs-validation" novalidate>
                      <div class="row" id="cargando-1-fomulario">
                        <input type="hidden" name="idcaja" id="idcaja">
                        
                        <div class="col-md-6 c_f_inicio">
                          <div class="form-group">
                            <label for="f_inicio" class="form-label">Fecha Apertura<span class="text-danger">(*)</span></label>
                            <input type="datetime-local" class="form-control" name="f_inicio" id="f_inicio"/>
                          </div>
                        </div>
                        <div class="col-md-6 c_m_inicio">
                          <div class="form-group">
                            <label for="m_inicio" class="form-label">Monto Apertura<span class="text-danger">(*)</span></label>
                            <input type="number" class="form-control" name="m_inicio" id="m_inicio"/>
                          </div>
                        </div>
                        <div class="col-md-6 c_f_cierre">
                          <div class="form-group">
                            <label for="f_cierre" class="form-label">Fecha Cierre<span class="text-danger">(*)</span></label>
                            <input type="datetime-local" class="form-control" name="f_cierre" id="f_cierre"/>
                          </div>
                        </div>
                        <div class="col-md-6 c_m_cierre">
                          <div class="form-group">
                            <label for="m_cierre" class="form-label">Monto Cierre</label>
                            <input type="number" class="form-control" name="m_cierre" id="m_cierre" style="text-align: right;" readonly/>
                          </div>
                        </div>
                        <div class="col-md-6 mt-3 ms-2 c_automati">
                          <div class="form-group custom-toggle-switch d-flex align-items-center">
                            <input type="checkbox" name="swit_auto" id="swit_auto">
                            <label class="label-success" for="swit_auto"></label><span class="ms-2 fs-14">Cierre Automático</span>
                            <input type="hidden" name="sw_auto" id="sw_auto" value="0">
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div id="alerta-cierre-auto" class="alert alert-warning mt-2" role="alert" style="display: none;">
                            ⚠️ La caja se cerrará automáticamente a las 23:59 horas del día de apertura.
                          </div>
                          <div id="alerta-datos-cierre" class="alert alert-success mt-2" role="alert" style="display: none;">
                                                       
                          </div>
                        </div>
                        
                      </div>
                      <div class="row" id="cargando-2-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-caja">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_caja();"><i class="las la-times fs-lg"></i> Close</button>
                    <button type="button" class="btn btn-primary btn-guardar" id="guardar_registro_caja"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div> 
            <!-- End::Modal-Registrar-Caja -->

                     

            <?php include("template/search_modal.php"); ?>
            <?php include("template/footer.php"); ?>

          </div>

          <?php include("template/scripts.php"); ?>
          <?php include("template/custom_switcherjs.php"); ?> 

          <script src="scripts/caja.js?version_jdl=1.16"></script>
          <script> $(function () { $('[data-bs-toggle="tooltip"]').tooltip(); }); </script>

        
        </body>

      </html>
    <?php
  }
  ob_end_flush();
?>