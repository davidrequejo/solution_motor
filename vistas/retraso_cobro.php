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
    <?php $title_page = "Retraso de Cobros";  include("template/head.php"); ?>

    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">

    <link rel="stylesheet" href="../assets/libs/flatpickr/plugins/monthSelect/style.css">

    <style>
      #tabla-facturacion-detalle td {        
        vertical-align: middle !important;
        line-height: 1.462 !important;
        font-size: .6875rem !important;
        font-weight: 50 !important;
      }
      #tabla-comprobantes_filter label{ width: 100% !important; }
      #tabla-comprobantes_filter label input{ width: 100% !important; }
    </style>
  </head>

  <body id="body-ventas" idusuario="<?php echo $_SESSION['idusuario'];?>" idpersona="<?php echo $_SESSION['idpersona'];?>" idpersona_trabajador="<?php echo $_SESSION['idpersona_trabajador'];?>" >
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['retraso_de_cobro']==1) { ?>
      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <!-- <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <div><i class='fa-3x bx bx-line-chart mr-2'></i></div>
                <div>
                  <p class="fw-semibold fs-18 mb-0"> Avances de Cobros</p>
                  <span class="fs-semibold text-muted">Organiza tu tiempo y enfócate en tus cobros pendientes.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Realizar cobro</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Facturación</li>
                </ol>
              </nav>
            </div>
          </div> -->
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">    

            <div class="col-sm-4 mt-3">
              <div class="d-md-flex d-block align-items-center ">
                <div><i class='fa-3x bx bx-line-chart mr-2'></i></div>
                <div>
                  <p class="fw-semibold fs-18 mb-0"> Retraso de Cobros</p>
                  <span class="fs-semibold text-muted">Organiza tu tiempo y enfócate en tus cobros pendientes.</span>
                </div>
              </div>
            </div>
            
            <div class="col-sm-8 mt-3">
              
              <div class="card custom-card">
                <div class="card-body">
                  <div class="row">
                    
                    <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                    <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                      <div class="form-group">
                        <label for="filtro_periodo_anio" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_periodo_anio();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Cobro Año
                          <span class="charge_filtro_periodo_anio"></span>
                        </label>
                        <select class="form-control" name="filtro_periodo_anio" id="filtro_periodo_anio" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                    <div class="col-sm-6 col-md-6 col-lg-3 col-xl-4 col-xxl-4">
                      <div class="form-group">
                        <label for="filtro_trabajador" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_trabajador();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Trabajador
                          <span class="charge_filtro_trabajador"></span>
                        </label>
                        <select class="form-control" name="filtro_trabajador" id="filtro_trabajador" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    

                  </div> 
                </div>
              </div>
            </div>
            
            <!-- TABLA - FACTURA -->
            <div class="col-xl-6" id="div-tabla">
              <div class="card custom-card">     
                <div class="card-header justify-content-between">
                  <div class="card-title">Avance por Centro Poblado</div>                  
                </div>           
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-ventas">
                      <thead>
                        <tr>
                          <th class="text-center"><center>#</center></th>
                          <th>Mes Inicio</th>
                          <th>Avance</th>                            
                          <th>Cant Mes</th>     
                          <th><center>Ver</center></th>               
                          <th><center>Cobrado</center></th>               
                          <th><center>Total</center></th>               
                          <th><center>Avance</center></th>               
                        </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                        <tr>
                        <th class="text-center"><center>#</center></th>
                          <th>Mes Inicio</th>
                          <th>Avance</th>                         
                          <th class="text-center" >Cant Mes</th>     
                          <th><center>Ver</center></th>
                          <th><center>Cobrado</center></th>   
                          <th><center>Total</center></th>
                          <th><center>Avance</center></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>                
              </div>
            </div>

            <div class="col-xxl-3 col-xl-3" style="display: none !important;">
              <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                  <div class="card-title">Avance por plan</div>                  
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover text-nowrap" id="avance-plan">
                      <thead>
                        <tr>
                          <th scope="col">Plan</th>
                          <th scope="col">Avance</th>
                          <th scope="col">%</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="3">
                            <div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div>
                          </td>                          
                        </tr> 
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-xxl-3" style="display: none !important;">
              <div class="card custom-card">
                <div class="card-header justify-content-between">
                  <div class="card-title">Avance total</div>                  
                </div>
                <div class="card-body pb-0 px-2">
                  <div id="sale-value" class="p-1"></div>
                  <div class="row pt-1">
                    <!-- <div class="col-xl-12 border-bottom pb-3 text-center d-flex flex-wrap"><span class="fw-semibold ms-2 text-primary px-4">60% Increase in sale value since last week</span></div> -->
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 border-end p-3 text-center">
                      <p class="mb-1">Cobrado</p>
                      <h5 class="mb-1 fw-semibold total_avance_cobrado"><div class="text-center my-3"><div class="spinner-border" style="width: 15px; height: 15px; border-width: 3px;" role="status"></div></div></h5>
                      <p class="fs-10 text-muted mb-0">avance<span class="text-success ms-2"><i class="ri-arrow-up-s-line me-2 fw-bold align-middle d-inline-block"></i><span class="badge bg-success-transparent text-success fs-10 total_avance_cobrado_porcent">0%</span></span></p>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 p-3 text-center">
                      <p class="mb-1">Por Cobrar</p>
                      <h5 class="mb-1 fw-semibold total_avance_por_cobrar"><div class="text-center my-3"><div class="spinner-border" style="width: 15px; height: 15px; border-width: 3px;" role="status"></div></div></h5>
                      <p class="fs-10 text-muted mb-0">falta<span class="text-danger ms-2"><i class="ri-arrow-down-s-line me-2 fw-bold align-middle d-inline-block"></i><span class="badge bg-success-transparent text-danger fs-10 total_avance_por_cobrar_porcent">0%</span></span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
                    

          </div>
          <!-- End::row-1 -->         
        
          <!-- MODAL - AGREGAR PERIDDO - charge 3 -->
          <div class="modal fade modal-effect" id="modal-ver-detalle-cobro" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-periodoLabel">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-periodoLabel1">Registrar Periodo</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="html-detalle-cobro">
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times fs-lg"></i> Close</button>
                </div>
              </div> 
            </div>
          </div>
          <!-- End::Modal-Agregar-Producto -->    
           
          <!-- Start::Modal-pago-cliente-x-mes -->
          <div class="modal fade" id="pago-cliente-mes" tabindex="-1" aria-labelledby="pago-cliente-mesLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="pago-cliente-mesLabel1">Pagos por Mes</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="table-responsive" id="div_tabla_pagos_Cx_mes">
                    <div class="row" >
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>                      
                  </div>
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-pago-cliente-x-mes -->

          <!-- MODAL - IMPRIMIR -->
          <div class="modal fade modal-effect" id="modal-imprimir-comprobante" tabindex="-1" aria-labelledby="modal-imprimir-comprobante-label" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-imprimir-comprobante-label">COMPROBANTE</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >                  
                  <div id="html-imprimir-comprobante" class="text-center" > </div>
                </div>                
              </div>
            </div>
          </div> 

        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='venta'; $descripcion ='Lista de ventas del sistema!'; $title_modulo = 'ventas'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>      

    <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>      
    <script src="../assets/libs/flatpickr/plugins/monthSelect/index.js"></script>    
    
    <script src="scripts/retraso_cobro.js?version_jdl=1.16"></script>
    
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        console.log('Pagina termino de cargar');
      });
    </script>

    <?php include("template/custom_switcherjs.php"); ?> 
  </body>



  </html>
<?php
}
ob_end_flush();
?>