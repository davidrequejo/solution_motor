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
    <?php $title_page = "Periodos de Facturación";  include("template/head.php"); ?>

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
      <?php if($_SESSION['periodo_facturado']==1) { ?>
      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="limpiar_form_periodo(); " data-bs-toggle="modal" data-bs-target="#modal-agregar-periodo" style="display: none;" > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                
                <div>
                  <p class="fw-semibold fs-18 mb-0">Periodos de Facturación</p>
                  <span class="fs-semibold text-muted">Organiza tus períodos conforme a tus necesidades.</span>
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
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">     
            
            <!-- TABLA - FACTURA -->
            <div class="<?php echo $_SESSION['user_cargo'] == 'TÉCNICO DE RED' ? 'col-xl-12' : 'col-xl-9'; ?>" id="div-tabla">
              <div class="card custom-card">
                <div class="card-header justify-content-between">

                  <!-- ::::::::::::::::::::: FILTRO COMPROBANTE :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-6 col-lg-3 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_anio" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_anio();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Periodo Año
                        <span class="charge_filtro_anio"></span>
                      </label>
                      <select class="form-control" name="filtro_anio" id="filtro_anio" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                  
                  <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_periodo" class="form-label">
                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_periodo();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                        Periodo Mes</label>
                      <input type="month" class="form-control" name="filtro_periodo" id="filtro_periodo" value="" onchange="cargando_search(); delay(function(){filtros()}, 50 );">                        
                    </div>
                  </div>                   
                  <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-6 col-lg-3 col-xl-4 col-xxl-4">
                    <div class="form-group">
                      <label for="filtro_cliente" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Cliente
                        <span class="charge_filtro_cliente"></span>
                      </label>
                      <select class="form-control" name="filtro_cliente" id="filtro_cliente" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                  <!-- ::::::::::::::::::::: FILTRO COMPROBANTE :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-6 col-lg-3 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_comprobante" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_comprobante();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Comprobante
                        <span class="charge_filtro_comprobante"></span>
                      </label>
                      <select class="form-control" name="filtro_comprobante" id="filtro_comprobante" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                 
                  <!-- <div class="d-flex"> 
                    <div class="dropdown ms-2">
                      <button class="btn btn-icon btn-secondary-light btn-sm btn-wave waves-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu otros-filtros">                        
                        <li><a class="dropdown-item o-f-ac" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('ACEPTADA', '.o-f-ac')" ><i class="ri-check-fill align-middle me-1"></i> Solo aceptados</a></li>
                        <li><a class="dropdown-item o-f-an" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('ANULADO', '.o-f-an')" ><i class="ri-close-fill align-middle me-1"></i> Solo anulados</a></li>
                        <li><a class="dropdown-item o-f-to active" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('', '.o-f-to')" ><i class="bi bi-border-all align-middle me-1"></i> Todos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="view_mas_detalle();"><i class="bi bi-list-check"></i> Ver mas detalles</a></li>
                      </ul>
                    </div>
                  </div> -->

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-ventas">
                      <thead>
                        <tr>
                          <th class="text-center"><center>#</center></th>
                          <th class="text-center"><center>OP</center></th>
                          <th>ID</th>
                          <th>Periodo</th>
                          <th>Inicio</th>
                          <th>Fin</th>                          
                          <th>Total</th>    
                          <th>Cant</th>     
                          <th><center>Detalle</center></th>               
                        </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                        <tr>
                        <th class="text-center"><center>#</center></th>
                          <th class="text-center"><center>OP</center></th>
                          <th>ID</th>
                          <th>Periodo</th>
                          <th>Inicio</th>
                          <th>Fin</th>
                          <th>Total</th>  
                          <th class="text-center" >Cant</th>     
                          <th><center>Detalle</center></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>                
              </div>
            </div>

            <!-- REPORTE- MINI -->
            <div class="col-xl-3" id="div-mini-reporte" <?php echo $_SESSION['user_cargo'] == 'TÉCNICO DE RED' ? 'style="display: none;"' : '' ; ?> >
              <div class="card custom-card">
                <div class="card-body p-0">
                  <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-top">
                    <div class="svg-icon-background bg-info-transparent me-4 cursor-pointer" onclick="filtros();" data-bs-toggle="tooltip" title="Actualizar">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-info">
                        <path d="M11.5,20h-6a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1h5V7a3,3,0,0,0,3,3h3v5a1,1,0,0,0,2,0V9s0,0,0-.06a1.31,1.31,0,0,0-.06-.27l0-.09a1.07,1.07,0,0,0-.19-.28h0l-6-6h0a1.07,1.07,0,0,0-.28-.19.29.29,0,0,0-.1,0A1.1,1.1,0,0,0,11.56,2H5.5a3,3,0,0,0-3,3V19a3,3,0,0,0,3,3h6a1,1,0,0,0,0-2Zm1-14.59L15.09,8H13.5a1,1,0,0,1-1-1ZM7.5,14h6a1,1,0,0,0,0-2h-6a1,1,0,0,0,0,2Zm4,2h-4a1,1,0,0,0,0,2h4a1,1,0,0,0,0-2Zm-4-6h1a1,1,0,0,0,0-2h-1a1,1,0,0,0,0,2Zm13.71,6.29a1,1,0,0,0-1.42,0l-3.29,3.3-1.29-1.3a1,1,0,0,0-1.42,1.42l2,2a1,1,0,0,0,1.42,0l4-4A1,1,0,0,0,21.21,16.29Z" />
                      </svg>
                    </div>
                    <div class="flex-fill">
                      <h6 class="mb-2 fs-12">Total Factura
                        <span class="badge bg-info fw-semibold float-end vw_count_factura"> 0 </span>
                      </h6>
                      <div class="pb-0 mt-0">
                        <div>
                          <h4 class="fs-18 fw-semibold mb-2">S/ <span class="vw_total_factura" data-count="0"><div class="spinner-border spinner-border-sm" role="status"></div></span></h4>
                          <p class="text-muted fs-11 mb-0 lh-1">
                            <span class="text-success me-1 fw-semibold vw_total_factura_p">
                              <i class="ri-arrow-up-s-line me-1 align-middle"></i>0%
                            </span>
                            <span>this month</span>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-top">
                    <div class="svg-icon-background bg-success-transparent me-4 cursor-pointer" onclick="filtros();" data-bs-toggle="tooltip" title="Actualizar">                      
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-success">
                        <path d="M11.5,20h-6a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1h5V7a3,3,0,0,0,3,3h3v5a1,1,0,0,0,2,0V9s0,0,0-.06a1.31,1.31,0,0,0-.06-.27l0-.09a1.07,1.07,0,0,0-.19-.28h0l-6-6h0a1.07,1.07,0,0,0-.28-.19.29.29,0,0,0-.1,0A1.1,1.1,0,0,0,11.56,2H5.5a3,3,0,0,0-3,3V19a3,3,0,0,0,3,3h6a1,1,0,0,0,0-2Zm1-14.59L15.09,8H13.5a1,1,0,0,1-1-1ZM7.5,14h6a1,1,0,0,0,0-2h-6a1,1,0,0,0,0,2Zm4,2h-4a1,1,0,0,0,0,2h4a1,1,0,0,0,0-2Zm-4-6h1a1,1,0,0,0,0-2h-1a1,1,0,0,0,0,2Zm13.71,6.29a1,1,0,0,0-1.42,0l-3.29,3.3-1.29-1.3a1,1,0,0,0-1.42,1.42l2,2a1,1,0,0,0,1.42,0l4-4A1,1,0,0,0,21.21,16.29Z" />
                      </svg>
                    </div>
                    <div class="flex-fill">
                      <h6 class="mb-2 fs-12">Total Boleta
                        <span class="badge bg-success fw-semibold float-end vw_count_boleta">0  </span>
                      </h6>
                      <div>
                        <h4 class="fs-18 fw-semibold mb-2">S/ <span class="vw_total_boleta" data-count="0"><div class="spinner-border spinner-border-sm" role="status"></div></span></h4>
                        <p class="text-muted fs-11 mb-0 lh-1">
                          <span class="text-success me-1 fw-semibold vw_total_boleta_p">
                            <i class="ri-arrow-down-s-line me-1 align-middle"></i>0%
                          </span>
                          <span>this month</span>
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex align-items-top p-4 border-bottom border-block-end-dashed">
                    <div class="svg-icon-background bg-warning-transparent me-4 cursor-pointer" onclick="filtros();" data-bs-toggle="tooltip" title="Actualizar">
                      <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" class="svg-warning">
                        <path d="M13,16H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2ZM9,10h2a1,1,0,0,0,0-2H9a1,1,0,0,0,0,2Zm12,2H18V3a1,1,0,0,0-.5-.87,1,1,0,0,0-1,0l-3,1.72-3-1.72a1,1,0,0,0-1,0l-3,1.72-3-1.72a1,1,0,0,0-1,0A1,1,0,0,0,2,3V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V13A1,1,0,0,0,21,12ZM5,20a1,1,0,0,1-1-1V4.73L6,5.87a1.08,1.08,0,0,0,1,0l3-1.72,3,1.72a1.08,1.08,0,0,0,1,0l2-1.14V19a3,3,0,0,0,.18,1Zm15-1a1,1,0,0,1-2,0V14h2Zm-7-7H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Z" />
                      </svg>
                    </div>
                    <div class="flex-fill">
                      <h6 class="mb-2 fs-12">Total Ticket
                        <span class="badge bg-warning fw-semibold float-end vw_count_ticket">0 </span>
                      </h6>
                      <div>
                        <h4 class="fs-18 fw-semibold mb-2">S/ <span class="vw_total_ticket" data-count="0"><div class="spinner-border spinner-border-sm" role="status"></div></span></h4>
                        <p class="text-muted fs-11 mb-0 lh-1">
                          <span class="text-success me-1 fw-semibold vw_total_ticket_p">
                            <i class="ri-arrow-up-s-line me-1 align-middle"></i>0%
                          </span>
                          <span>this month</span>
                        </p>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="d-flex align-items-top p-4 border-bottom border-block-end-dashed">
                    <div class="svg-icon-background bg-light me-4">
                      <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" class="svg-dark">
                        <path d="M19,12h-7V5c0-0.6-0.4-1-1-1c-5,0-9,4-9,9s4,9,9,9s9-4,9-9C20,12.4,19.6,12,19,12z M12,19.9c-3.8,0.6-7.4-2.1-7.9-5.9C3.5,10.2,6.2,6.6,10,6.1V13c0,0.6,0.4,1,1,1h6.9C17.5,17.1,15.1,19.5,12,19.9z M15,2c-0.6,0-1,0.4-1,1v6c0,0.6,0.4,1,1,1h6c0.6,0,1-0.4,1-1C22,5.1,18.9,2,15,2z M16,8V4.1C18,4.5,19.5,6,19.9,8H16z" />
                      </svg>
                    </div>
                    <div class="flex-fill">
                      <h6 class="mb-2 fs-12">Overdue Invoices
                        <span class="badge bg-light text-default fw-semibold float-end">
                          1,105
                        </span>
                      </h6>
                      <div>
                        <h4 class="fs-18 fw-semibold mb-2">$<span class="count-up" data-count="32.47">32.47</span>K</h4>
                        <p class="text-muted fs-11 mb-0 lh-1">
                          <span class="text-success me-1 fw-semibFold">
                            <i class="ri-arrow-down-s-line me-1 align-middle"></i>0.46%
                          </span>
                          <span>this month</span>
                        </p>
                      </div>
                    </div>
                  </div> -->
                  <div class="p-4">
                    <p class="fs-15 fw-semibold">Mini reporte <span class="text-muted fw-normal">(Últimos 6 meses) :</span></p>
                    <div id="invoice-list-stats"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- FORMULARIO -->
            <div class="col-xl-12" id="div-formulario"  style="display: none;">              
              <div class="card custom-card">
                <div class="card-body">    
                  
                  <div class="row">
                    
                    <div class="col-md-4">
                      <!-- FORM - COMPROBANTE -->                    
                      <form name="form-cambio-periodo" id="form-cambio-periodo" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                        <input type="hidden" name="idperiodo_ver" id="idperiodo_ver">
                        <div class="row gy-3" id="cargando-1-formulario">
                          
                          <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                            <div class="row">    
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 7px;"><label class="bg-white" for=""><b>DATOS DE PERIODO</b></label></div>
                              </div>
                            </div>

                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row ">                                                                
                                <div class="col-md-12 mb-3">
                                  <label for="form-label">Periodo Actual</label>
                                  <h5 id="periodo-actual"></h5>
                                </div>
                                <!-- SALDO -->
                                <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                                  <div class="form-group">
                                    <label for="t_idperiodo" class="form-label">
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_periodo();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Periodo a cambiar          
                                      <span class="charge_t_idperiodo"></span>                            
                                    </label>
                                    <select class="form-control" name="t_idperiodo" id="t_idperiodo" > <!-- lista --> </select>
                                  </div>
                                </div> 

                              </div>
                            </div>                            
                          </div>
                           
                          <!-- Chargue -->
                          <div class="p-l-25px col-lg-12" id="barra_progress_venta_div" style="display: none;" >
                            <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                              <div id="barra_progress_venta" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                            </div>
                          </div>
                        
                        </div>  
                        
                        <!-- ::::::::::: CARGANDO ... :::::::: -->
                        <div class="row" id="cargando-2-fomulario" style="display: none;" >
                          <div class="col-lg-12 mt-5 text-center">                         
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" style="display: none;" id="submit-form-cambio-periodo">Submit</button>
                      </form>    
                      <div class="row">
                        <div class="col-lg-12 mt-3 mb-3">
                          <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-danger btn-cancelar" onclick="show_hide_form(1);" ><i class="las la-times fs-lg"></i> Cancelar</button>
                            <button type="button" class="btn btn-sm btn-success btn-guardar" id="guardar_registro_cambio_periodo" ><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                          </div>
                        </div>                       
                      </div>    
                    </div>
                    <div class="col-md-8">
                      <div class="row mb-3">                        
                        
                        <!-- ::::::::::::::::::::: FILTRO MES EMISION :::::::::::::::::::::: -->
                        <div class="col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                          <div class="form-group">
                            <label for="filtro_t_mes_emision" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_t_mes_emision();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                              Emisión Mes</label>
                            <input type="month" class="form-control" name="filtro_t_mes_emision" id="filtro_t_mes_emision" value="<?php echo date('Y-m')?>" onchange="delay(function(){filtros_2()}, 50 );">                        
                          </div>
                        </div>                   
                        <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                          <div class="form-group">
                            <label for="filtro_t_cliente" class="form-label">                         
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_t_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                              Cliente
                              <span class="charge_filtro_t_cliente"></span>
                            </label>
                            <select class="form-control" name="filtro_t_cliente" id="filtro_t_cliente" onchange="delay(function(){filtros_2()}, 50 );" > <!-- lista de categorias --> </select>
                          </div>
                        </div>
                        <!-- ::::::::::::::::::::: FILTRO COMPROBANTE :::::::::::::::::::::: -->
                        <div class="col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                          <div class="form-group">
                            <label for="filtro_t_comprobante" class="form-label">                         
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_t_comprobante();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                              Comprobante
                              <span class="charge_filtro_t_comprobante"></span>
                            </label>
                            <select class="form-control" name="filtro_t_comprobante" id="filtro_t_comprobante" onchange="delay(function(){filtros_2()}, 50 );" > <!-- lista de categorias --> </select>
                          </div>
                        </div>
                      </div>
                      <div class="table-responsive">
                        <table class="table table-bordered w-100" style="width: 100%;" id="tabla-comprobantes">
                          <thead>
                            <tr>
                              <th class="text-center"><center>#</center></th>
                              <th class="text-center"><center>OP</center></th>
                              <th>Creación</th>
                              <th>Periodo</th>
                              <th>Cliente</th>
                              <th>Correlativo</th>
                              <th>Total</th>                                    
                              <th><center>Estado</center></th>               
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                            <th class="text-center"><center>#</center></th>
                              <th class="text-center"><center>OP</center></th>
                              <th>Creación</th>
                              <th>Periodo</th>
                              <th>Cliente</th>
                              <th>Correlativo</th>
                              <th>Total</th>                               
                              <th><center>Estado</center></th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>                                   

                </div>
                <!-- <div class="card-footer border-top-0">
                  
                </div> -->
              </div>              
            </div>            

          </div>
          <!-- End::row-1 -->         
        
          <!-- MODAL - AGREGAR PERIDDO - charge 3 -->
          <div class="modal fade modal-effect" id="modal-agregar-periodo" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-periodoLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-periodoLabel1">Registrar Periodo</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="form-agregar-periodo" id="form-agregar-periodo" method="POST" class="needs-validation" novalidate >
                    <div class="row gy-2" id="cargando-3-formulario">

                      <!-- ----------------------- ID ------------- -->
                      <input type="hidden" id="idperiodo_contable" name="idperiodo_contable">

                      <div class="col-md-12 col-lg-12 col-xl-6 col-xxl-6">
                        <div class="form-group">
                          <label for="periodo" class="form-label">Periodo</label>
                          <input type="month" name="periodo" id="periodo" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-12 col-lg-12 col-xl-6 col-xxl-6">
                        <div class="form-group">
                          <label for="periodo_anio" class="form-label">Año <small class="text-info" id="periodo_anio_small">(Selecione el periodo)</small> </label>
                          <input type="number" name="periodo_anio" id="periodo_anio" class="form-control" value="0000" disabled>
                        </div>
                      </div>
                      <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                        <div class="form-group">
                          <label for="fecha_inicio" class="form-label"><i class="bi bi-info-circle-fill cursor-pointer" data-bs-toggle="tooltip" title="Uso: Para determinar qué documentos estarán incluidos a partir de esta fecha de inicio." ></i> Fecha Inicio</label>
                          <input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                        <div class="form-group">
                          <label for="fecha_fin" class="form-label"><i class="bi bi-info-circle-fill cursor-pointer" data-bs-toggle="tooltip" title="Uso: Para determinar qué documentos estarán incluidos antes de esta fecha de finalización." ></i> Fecha Fin</label>
                          <input type="text" name="fecha_fin" id="fecha_fin" class="form-control">
                        </div>
                      </div>

                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_periodo_div" style="display: none;" >
                        <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                          <div id="barra_progress_periodo" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                        </div>
                      </div>

                    </div>
                    <div class="row" id="cargando-4-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-periodo">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_periodo();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_periodo"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div> 
            </div>
          </div>
          <!-- End::Modal-Agregar-Producto -->         

        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='venta'; $descripcion ='Lista de ventas del sistema!'; $title_modulo = 'ventas'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>   

    <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>      
    <script src="../assets/libs/flatpickr/plugins/monthSelect/index.js"></script>     
    
    <script src="scripts/periodo_facturacion.js?version_jdl=1.16"></script>
    
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        console.log('Pagina termino de cargar');
      });
    </script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>