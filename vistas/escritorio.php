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
    <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" loader="enable" style="--primary-rgb: 208, 2, 149;">

      <head>
        
        <?php $title_page = "Escritorio"; include("template/head.php"); ?>
        <link rel="stylesheet" href="../assets/libs/jsvectormap/css/jsvectormap.min.css">
        <link rel="stylesheet" href="../assets/libs/swiper/swiper-bundle.min.css">

      </head> 

      <body idempresa="<?php echo $_SESSION["idusuario"];?>" reporte="<?php echo $_SESSION["reporte"];?>" >

        <?php include("template/switcher.php"); ?>
        <?php include("template/loader.php"); ?>

        <div class="page">
          <?php include("template/header.php") ?>
          <?php include("template/sidebar.php") ?>

          <?php if ($_SESSION['dashboard'] == 1) { ?>

          <!-- Start::app-content -->
          <div class="main-content app-content">
            <div class="container-fluid">

              <!-- Start::page-header -->

              <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                  <p class="fw-semibold fs-18 mb-0">Bienvenido de nuevo, <?php echo $_SESSION["user_nombre"];?> !</p>
                  <span class="fs-semibold text-muted">Rastrea tu actividad de ventas, prospectos y acuerdos aquí.</span>
                </div>
                <div class="btn-list mt-md-0 mt-2">
                  <a href="facturacion.php" type="button" class="btn btn-outline-secondary btn-wave">
                    <i class="ri-upload-cloud-line me-2 align-middle d-inline-block"></i>Ir a Facturar
                  </a>
                  <!-- <button type="button" class="btn btn-primary btn-wave">
                    <i class="ri-filter-3-fill me-2 align-middle d-inline-block"></i>Filtros
                  </button> -->
                  
                </div>
              </div>

              <!-- End::page-header -->

              <!-- Start::row-1 -->
              <div class="row">
                <div class="col-sm-12">
                
                    <div class="card custom-card">
                      <div class="card-body">
                        <div class="row">                          
                          
                          <!-- ::::::::::::::::::::: FILTRO MES FACTURADO :::::::::::::::::::::: -->
                          <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                            <div class="form-group">
                              <label for="filtro_mes_contable" class="form-label">
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_mes_contable();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                                Mes Contable</label>
                              <input type="month" class="form-control form-control-sm form-control-primary" id="filtro_mes_contable" value="<?php echo date('Y-m'); ?>" onchange="delay(function(){filtros()}, 50 );">                        
                            </div>
                          </div>  
                          <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                          <div class="col-sm-6 col-md-6 col-lg-3 col-xl-4 col-xxl-4" <?php echo $_SESSION['user_cargo'] == 'TÉCNICO DE RED' ? 'style="display: none;"' : '' ; ?> >
                            <div class="form-group">
                              <label for="filtro_trabajador" class="form-label">                         
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_trabajador();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Trabajador
                                <span class="charge_filtro_trabajador"></span>
                              </label>
                              <select class="form-control form-control-sm form-control-primary text-primary" id="filtro_trabajador" onchange="delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                            </div>
                          </div>
                          

                        </div>
                      </div>
                    </div>
                  </div>
                <div class="col-xxl-9 col-xl-12">
                  <div class="row">
                    <div class="col-xl-4">
                      <!-- ::::::::::::::::::::: OBJETIVO MENSUAL ::::::::::::::::::::: -->
                      <div class="row">
                        <div class="col-xl-12">
                          <div class="card custom-card crm-highlight-card" style="background-color: #0a3a3dde !important;">
                            <div class="card-body">
                              <div class="d-flex align-items-center justify-content-between">
                                <div>
                                  <div class="fw-semibold fs-18 text-fixed-white mb-2">Tu objetivo mensual está incompleto!!</div>
                                  <span class="d-block fs-12 text-fixed-white">
                                    <span class="op-7">Has cobrado </span>
                                    <span class="fw-semibold text-warning obj_total_cobrado">0</span> de
                                    <span class="fw-semibold text-warning obj_total_cliente">0</span> 
                                    <span class="op-7">clientes del objetivo dado, también puedes verificar tu cobros (facturación)</span>.
                                  </span>
                                  <span class="d-block  mt-1 text-fixed-white">Vamos a cobrar, <a class="text-fixed-white fw-semibold" href="facturacion.php"><u>haz clic aquí</u></a></span>
                                </div>
                                <div> 
                                  <div id="crm-main"><div class="spinner-border spinner-border-lg text-white" role="status"></div></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ::::::::::::::::::::: TOP 5 CLIENTES ::::::::::::::::::::: -->
                      <div class="col-xl-12">
                        <div class="card custom-card">
                          <div class="card-header  justify-content-between">
                            <div class="card-title">
                              Top 5 Clientes
                            </div>
                            <!-- <div class="dropdown">
                              <a aria-label="anchor" href="javascript:void(0);" class="btn btn-icon btn-sm btn-light"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fe fe-more-vertical"></i>
                              </a>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0);">Week</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">Month</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">Year</a></li>
                              </ul>
                            </div> -->
                          </div>
                          <div class="card-body">
                            <ul class="list-unstyled crm-top-deals mb-0 top-5-clientes">
                              <li class="text-center"><div class="spinner-border spinner-border-lg" role="status"></div></li>                              
                            </ul>
                          </div>
                        </div>
                      </div>

                      <!-- ::::::::::::::::::::: POR DIA DE SEMANA ::::::::::::::::::::: -->
                      <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card custom-card">
                          <div class="card-header justify-content-between">
                            <div class="card-title">Cobros por Dia de Semana</div>
                            <!-- <div class="dropdown">
                              <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown" aria-expanded="false">View All<i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i></a>
                              <ul class="dropdown-menu" role="menu">
                                <li><a class="dropdown-item" href="javascript:void(0);">Today</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">This Week</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">Last Week</a></li>
                              </ul>
                            </div> -->
                          </div>
                          <div class="card-body py-0 ps-0">
                            <div id="crm-profits-earned"> <div class="text-center my-3"><div class="spinner-border spinner-border-lg" role="status"></div></div> </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-xl-8">
                      <div class="row">
                        <!-- ::::::::::::::::::::: CARDS POR COMPROBANTES ::::::::::::::::::::: -->
                        <div class="col-xxl-6 col-lg-6 col-md-6">
                          <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                              <div class="d-flex align-items-top justify-content-between">
                                <div>
                                  <span class="avatar avatar-md avatar-rounded bg-info">
                                    <i class="ti ti-file-check fs-16"></i>
                                  </span>
                                </div>
                                <div class="flex-fill ms-3">
                                  <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div>
                                      <p class="text-muted mb-0">Factura</p>
                                      <h4 class="fw-semibold mt-1 card-total-factura"><div class="spinner-border" role="status"></div></h4>
                                    </div>
                                    <div id="crm-total-customers"></div>
                                  </div>
                                  <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div>
                                      <a class="text-info" href="facturacion.php">Ver más<i class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                    </div>
                                    <div class="text-end">
                                      <p class="mb-0 text-success fw-semibold card-cantidad-factura">0</p>
                                      <span class="text-muted op-7 fs-9 fw-normal">cantidad factura</span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xxl-6 col-lg-6 col-md-6">
                          <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                              <div class="d-flex align-items-top justify-content-between">
                                <div>
                                  <span class="avatar avatar-md avatar-rounded bg-success">
                                    <i class="ti ti-file-check fs-16"></i> 
                                  </span>
                                </div>
                                <div class="flex-fill ms-3">
                                  <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div>
                                      <p class="text-muted mb-0">Boleta</p>
                                      <h4 class="fw-semibold mt-1 card-total-boleta"><div class="spinner-border" role="status"></div></h4>
                                    </div>
                                    <div id="crm-total-revenue"></div>
                                  </div>
                                  <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div>
                                      <a class="text-success" href="facturacion.php">Ver más<i class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                    </div>
                                    <div class="text-end">
                                      <p class="mb-0 text-success fw-semibold card-cantidad-boleta">0</p>
                                      <span class="text-muted op-7 fs-9 fw-normal">cantidad boleta</span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xxl-6 col-lg-6 col-md-6">
                          <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                              <div class="d-flex align-items-top justify-content-between">
                                <div>
                                  <span class="avatar avatar-md avatar-rounded bg-warning">
                                    <i class="bi bi-ticket-perforated fs-16"></i>
                                  </span>
                                </div>
                                <div class="flex-fill ms-3">
                                  <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div>
                                      <p class="text-muted mb-0">Ticket</p>
                                      <h4 class="fw-semibold mt-1 card-total-ticket"><div class="spinner-border" role="status"></div></h4>
                                    </div>
                                    <div id="crm-conversion-ratio"></div>
                                  </div>
                                  <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div>
                                      <a class="text-warning" href="facturacion.php">Ver más<i class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                    </div>
                                    <div class="text-end">
                                      <p class="mb-0 text-success fw-semibold card-cantidad-ticket">0</p>
                                      <span class="text-muted op-7 fs-9 fw-normal">cantidad ticket</span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xxl-6 col-lg-6 col-md-6">
                          <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                              <div class="d-flex align-items-top justify-content-between">
                                <div>
                                  <span class="avatar avatar-md avatar-rounded bg-primary">
                                    <i class="ti ti-briefcase fs-16"></i>
                                  </span>
                                </div>
                                <div class="flex-fill ms-3">
                                  <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div>
                                      <p class="text-muted mb-0">Total</p>
                                      <h4 class="fw-semibold mt-1 card-total"><div class="spinner-border" role="status"></div></h4>
                                    </div>
                                    <div id="crm-total-deals"></div>
                                  </div>
                                  <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div>
                                      <a class="text-primary" href="facturacion.php">Ver más<i class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                    </div>
                                    <div class="text-end">
                                      <p class="mb-0 text-success fw-semibold card-cantidad-total">0</p>
                                      <span class="text-muted op-7 fs-9 fw-normal">cantidad total</span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- ::::::::::::::::::::: CHART LINE POR COMPROBANTES ::::::::::::::::::::: -->
                        <div class="col-xl-12">
                          <div class="card custom-card">
                            <div class="card-header justify-content-between">
                              <div class="card-title">Comprobantes por mes</div>
                              <!-- <div class="dropdown">
                                <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown" aria-expanded="false"> View All<i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i> </a>
                                <ul class="dropdown-menu" role="menu">
                                  <li><a class="dropdown-item" href="javascript:void(0);">Today</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">This Week</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">Last Week</a></li>
                                </ul>
                              </div> -->
                            </div>
                            <div class="card-body">
                              <div class="content-wrapper">
                                <div id="crm-revenue-analytics"> <div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div> </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: TABLE TOP 5 PRODUCTOS ::::::::::::::::::::: -->
                    <div class="col-xl-12">
                      <div class="card custom-card">
                        <div class="card-header justify-content-between">
                          <div class="card-title">Top 5 Productos </div>                          
                        </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table text-nowrap table-hover border table-bordered tabla-top-5-productos">
                              <thead>
                                <tr>         
                                  <th scope="col">Producto/Servicio</th>
                                  <th scope="col" class="text-center">Categoria</th>
                                  <th scope="col" class="text-center">P/U</th>
                                  <th scope="col" class="text-center">Cantidad</th>
                                  <th scope="col" class="text-center">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="5">
                                    <div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div>
                                  </td>
                                </tr>                                
                              </tbody>
                            </table>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xxl-3 col-xl-12">
                  <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                      <div class="row">
                        <!-- ::::::::::::::::::::: TORTA TOP 5 TECNICOS ::::::::::::::::::::: -->
                        <div class="col-xl-12 col-xl-6">
                          <div class="card custom-card">
                            <div class="card-header justify-content-between">
                              <div class="card-title"> Top 5 Trabajadores</div>
                              <!-- <div class="dropdown">
                                <a aria-label="anchor" href="javascript:void(0);" class="btn btn-icon btn-sm btn-light" data-bs-toggle="dropdown"><i class="fe fe-more-vertical"></i></a>
                                <ul class="dropdown-menu">
                                  <li><a class="dropdown-item" href="javascript:void(0);">Week</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">Month</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">Year</a></li>
                                </ul>
                              </div> -->
                            </div>
                            <div class="card-body p-0 overflow-hidden">
                              <div class="leads-source-chart d-flex align-items-center justify-content-center">
                                <canvas id="leads-source" class="chartjs-chart w-100 p-4"></canvas>
                                <div class="lead-source-value">
                                  <span class="d-block fs-14">Total</span>
                                  <span class="d-block fs-25 fw-bold cart_pastel_total"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></span>
                                </div>
                              </div>
                              <div class="row row-cols-12 border-top border-block-start-dashed">
                                <div class="col p-0">
                                  <div class="ps-4 py-3 pe-3 text-center border-end border-inline-end-dashed">
                                    <div class="spinner-border" role="status"></div>
                                  </div>
                                </div>
                                <div class="col p-0">
                                  <div class="p-3 text-center border-end border-inline-end-dashed">
                                    <div class="spinner-border" role="status"></div>
                                  </div>
                                </div>
                                <div class="col p-0">
                                  <div class="p-3 text-center border-end border-inline-end-dashed">
                                    <div class="spinner-border" role="status"></div>
                                  </div>
                                </div>
                                <div class="col p-0">
                                  <div class="p-3 text-center">
                                    <div class="spinner-border" role="status"></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- ::::::::::::::::::::: LIST TOP 5 CENTRO POBLADO ::::::::::::::::::::: -->
                        <div class="col-xxl-12 col-xl-6">
                          <div class="card custom-card">
                            <div class="card-header justify-content-between">
                              <div class="card-title">Top 5 Centros Poblados</div>
                              <!-- <div class="dropdown">
                                <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown" aria-expanded="false">View All<i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                  <li><a class="dropdown-item" href="javascript:void(0);">Today</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">This Week</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">Last Week</a></li>
                                </ul>
                              </div> -->
                            </div>
                            <div class="card-body">
                              <div class="d-flex align-items-center mb-3">
                                <h4 class="fw-bold mb-0 total-centro-poblado">0.00</h4>
                                <div class="ms-2">
                                  <span class="badge bg-success-transparent">top<i class="ri-arrow-up-s-fill align-mmiddle ms-1"></i></span>
                                  <span class="text-muted ms-1 fs-9">mayor a menor aporte</span>
                                </div>
                              </div>
                              <div class="progress-stacked progress-animate progress-xs mb-4 progress-centro-poblado"> </div>
                              <ul class="list-unstyled mb-0 pt-2 list-centro-poblado">
                                <li class="primary">
                                  <div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>

                        <!-- ::::::::::::::::::::: INCIDENCIAS PENDIENTES ::::::::::::::::::::: -->
                        <div class="col-xxl-12 col-xl-6">
                          <div class="card custom-card">
                            <div class="card-header justify-content-between">
                              <div class="card-title">
                                Incidencias Pendientes <span class="fs-10">(Más antiguas)</span> 
                              </div>
                              <!-- <div class="dropdown">
                                <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown"
                                  aria-expanded="false">
                                  View All<i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                  <li><a class="dropdown-item" href="javascript:void(0);">Today</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">This Week</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0);">Last Week</a></li>
                                </ul>
                              </div> -->
                            </div>
                            <div class="card-body">
                              <div>
                                <ul class="list-unstyled mb-0 crm-recent-activity">
                                  <div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div>
                                </ul>
                              </div>
                            </div>
                          </div>
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

          

          <?php } else {
            $title_submodulo = 'Escritorio';
            $descripcion = 'Reporte general!';
            $title_modulo = 'Escritorio';
            include("403_error.php");
          } ?>

          <?php include("template/search_modal.php"); ?>
          <?php include("template/footer.php"); ?>

        </div>

        <?php include("template/scripts.php"); ?>


        <!-- JSVector Maps JS -->
        <script src="../assets/libs/jsvectormap/js/jsvectormap.min.js"></script>

        <!-- JSVector Maps MapsJS -->
        <script src="../assets/libs/jsvectormap/maps/world-merc.js"></script>

        <!-- Apex Charts JS -->
        <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>

        <!-- Chartjs Chart JS -->
        <script src="../assets/libs/chart.js/chart.min.js"></script>  

        <script src="scripts/escritorio.js?version_jdl=1.16"></script>
                
        <?php include("template/custom_switcherjs.php"); ?>

      </body>

    </html>
    <?php  
  }
  ob_end_flush();
?>
