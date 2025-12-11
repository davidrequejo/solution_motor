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

    <?php $title_page = "Reporte Venta";
    include("template/head.php"); ?>

    <style>

      #tabla-reporte-detalle-ov_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-reporte-detalle-ov_filter label { width: 100% !important;  }
      #tabla-reporte-detalle-ov_filter label input { width: 100% !important; }

      .style_tabla_datatable td,  tr {
        font-size: 11px;/* Reducir el tamaño de la fuente */      
        padding: 5px;/* Ajustar el padding */      
      }
    </style>
  </head>

  <body id="body-usuario">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['reporte_venta_x_trabajador'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Orden de venta por Trabajador!</p>
                    <span class="fs-semibold text-muted">Reporte de Cobros por trabajador.</span>
                  </div>
                </div>
              </div>

              <div class="btn-list mt-md-0 mt-2 mb-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Cobros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xxl-12 col-xl-12 ">
                
                <div class="row" >

                  <!-- ::::::::::::::::::::: FILTRO COBRADO :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_estado_cobrado" class="form-label">
                        <!-- <span class="badge bg-info m-r-4px cursor-pointer"  data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span> -->
                        Estado Cobrado
                        <span class="charge_filtro_estado_cobrado"></span>
                      </label>
                      <select class="form-control" name="filtro_estado_cobrado" id="filtro_estado_cobrado" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> 
                        <option value="COBRADO">COBRADO</option>
                        <option value="POR COBRADO">POR COBRAR</option>
                      </select>
                    </div>
                  </div>

                  <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_fecha_i" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_fecha_i');" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                        Fecha Inicio</label>
                      <input type="date" class="form-control" name="filtro_fecha_i" id="filtro_fecha_i" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                    </div>
                  </div>

                  <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_fecha_f" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_fecha_f');" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                        Fecha Fin</label>
                      <input type="date" class="form-control" name="filtro_fecha_f" id="filtro_fecha_f" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                    </div>
                  </div>

                  <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_trabajador" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_trabajador');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Trabajador
                        <span class="charge_filtro_trabajador"></span>
                      </label>
                      <select class="form-control" name="filtro_trabajador" id="filtro_trabajador" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                    </div>
                  </div>                    
                  

                </div>
                  
                <div class="row">

                  <!-- ::::::::::::::::::::::::::::: TABLA DE VENTA DETALLE ::::::::::::::::::::::::::::: -->
                  <div class="col-12 col-lg-6 col-xxl-6 mt-3">
                    <div class="card custom-card">
                      <div class="card-header">
                        <div class="card-title">Registro de Ordenes por Trabajador!</div>
                      </div>
                      <div class="card-body pb-1">
                        <div id="div-tabla" class="table-responsive">
                          <table id="tabla-reporte-detalle-ov" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                            <thead class="buscando_tabla">
                              <tr id="id_buscando_tabla" style="display: none;">
                                <th colspan="9" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                              </tr>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Cliente</th>
                                <th>Correlativo</th>
                                <th class="text-nowrap" >Total <span style="color: transparent !important;">-----</span> </th>                                      
                                <th class="text-nowrap" >Utilidad <span style="color: transparent !important;">-----</span> </th>                                      
                                <th>Cobrador</th>                                      
                                <th>Creación</th>

                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Celular</th>

                              </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Cliente</th>
                                <th>Correlativo</th>
                                <th>Total</th>                                      
                                <th>Utilidad</th>                                      
                                <th>Cobrador</th>                                      
                                <th>Creación</th>
                                
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Celular</th>

                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>                    
                  </div>

                  <!--Graficos del reporte-->
                  <div class="col-12 col-xl-6 col-xxl-6 mt-3">
                    <div class="row">

                      <!-- ::::::::::::::::::::::::::::: RESUMEN DE VENTA ::::::::::::::::::::::::::::: -->
                      <div class="col-xl-12 col-xxl-12">
                        <div class="row">

                          <!-- <div class="col-xxl-4 col-lg-4 col-md-6">
                            <div class="card custom-card overflow-hidden">
                              <div class="card-body" style=" padding: 5px !important; ">
                                <div class="d-flex align-items-top justify-content-between">
                                  <div>
                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                      <i class="fa-solid fa-wallet"></i>
                                    </span>
                                  </div>
                                  <div class="flex-fill ms-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                      <div>
                                        <p class="text-muted mb-0 ">Cantidad Doc. </p>
                                        <h7 class="fw-semibold mt-1 total_cantidad_doc">S/ 0.00</h7>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div> -->

                          <div class="col-xxl-4 col-lg-4 col-md-6">
                            <div class="card custom-card overflow-hidden">
                              <div class="card-body" style=" padding: 5px !important; ">
                                <div class="d-flex align-items-top justify-content-between">
                                  <div>
                                    <span class="avatar avatar-md avatar-rounded bg-secondary">
                                      <i class="ti ti-wallet fs-16"></i>
                                    </span>
                                  </div>
                                  <div class="flex-fill ms-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                      <div>
                                        <p class="text-muted mb-0  ">Total Venta  </p>
                                        <h7 class="fw-semibold mt-1 total_venta">S/ 0.00</h7>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="col-xxl-4 col-lg-4 col-md-6">
                            <div class="card custom-card overflow-hidden">
                              <div class="card-body" style=" padding: 5px !important; ">
                                <div class="d-flex align-items-top justify-content-between">
                                  <div>
                                    <span class="avatar avatar-md avatar-rounded bg-success">
                                      <i class="ti ti-wave-square fs-16"></i>
                                    </span>
                                  </div>
                                  <div class="flex-fill ms-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                      <div>
                                        <p class="text-muted mb-0  ">Total Compra  </p>
                                        <h7 class="fw-semibold mt-1 total_compra">S/ 0.00</h7>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="col-xxl-4 col-lg-4 col-md-6 mx-auto">
                            <div class="card custom-card overflow-hidden">
                              <div class="card-body" style=" padding: 5px !important; ">
                                <div class="d-flex align-items-top justify-content-between">
                                  <div>
                                    <span class="avatar avatar-md avatar-rounded bg-warning">
                                      <i class="ti ti-briefcase fs-16"></i>
                                    </span>
                                  </div>
                                  <div class="flex-fill ms-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                      <div>
                                        <p class="text-muted mb-0  ">Total Utilidad </p>
                                        <h7 class="fw-semibold mt-1 total_utilidad">S/ 0.00</h7>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          
                        </div>
                      </div>
                      
                      <!-- ::::::::::::::::::::::::::::: GRAFICA DE VENTAS ::::::::::::::::::::::::::::: -->
                      <div class="col-lg-12 col-xl-12 col-xxl-12">
                        <div class="card custom-card">
                          <div class="card-header justify-content-between">
                            <div class="card-title">
                              Grafico de Barras
                            </div>
                            <div >
                              <select name="filtro_tipo_chart" id="filtro_tipo_chart" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                                <option value="HORA">HORA</option>
                                <option value="DIA">DIA</option>
                                <option value="MES">MES</option>
                              </select>
                            </div>
                          </div>

                          <div class="card-body">
                            <div class="content-wrapper">
                              <div id="chart-ventas"></div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  
                  <div class="col-lg-12 col-xl-12 col-xxl-12">
                    <div class="row">

                      <!-- ::::::::::::::::::::::::::::: TOP 10 CANTIDAD VENDIDA ::::::::::::::::::::::::::::: -->
                      <div class="col-xl-6 col-xxl-6">
                        <div class="card custom-card">
                          <div class="card-header">
                            <div class="card-title">Top 10 por Cantidad Vendida</div>
                          </div>
                          <div class="card-body pb-1">
                            <div class="table-responsive">
                              <table id="tabla-top-10-cantidad" class="table table-bordered table-hover border-primary">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="w-400px">Producto</th>
                                    <th scope="col">Cant.</th>
                                    <th scope="col" class="text-nowrap">Costo <span style="color: transparent !important;">-----</span></th>
                                  </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="w-400px">Producto</th>
                                    <th scope="col">Cant.</th>
                                    <th scope="col" class="text-right">Costo</th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ::::::::::::::::::::::::::::: TOP 10 PRECIO VENDIDA ::::::::::::::::::::::::::::: -->                      
                      <div class="col-xl-6 col-xxl-6">
                        <div class="card custom-card">
                          <div class="card-header">
                            <div class="card-title">Top 10 por Precio Vendido</div>
                          </div>
                          <div class="card-body">
                            <div class="table-responsive">
                              <table id="tabla-to-10-precio" class="table table-bordered table-hover border-primary">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="w-400px" >Producto</th>
                                    <th scope="col">Cant.</th>
                                    <th scope="col" class="text-nowrap">Costo <span style="color: transparent !important;">-----</span></th>
                                  </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="w-400px">Producto</th>
                                    <th scope="col">Cant.</th>
                                    <th scope="col" class="text-right ">Costo</th>
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
            </div>
            <!-- End::row-1 -->

          </div>
        </div>
        <!-- End::app-content -->

      <?php } else {
        $title_submodulo = 'Reporte';
        $descripcion = 'Lista de reporte del sistema!';
        $title_modulo = 'reporte';
        include("403_error.php");
      } ?>


      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Internal Apex Pie Charts JS 
    <script src="../assets/js/apexcharts-pie.js"></script>-->


    <script src="scripts/reporte_venta_x_trabajador.js?version_jdl=1.16"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>
    <script>
      /* simple donut chart 
      var options = {
        series: [44, 55, 41, 17, 15,9],
        chart: {
          type: "donut",
          height: 290,
        },
        legend: {
          position: "bottom",
        },
        colors: ["#e6533c","#845adf", "#23b7e5", "#f5b849", "#49b6f5" , "#4eac4c"],
        labels: ["Team A", "Team B", "Team C", "Team D", "Team E","Team f"],
        dataLabels: {
          dropShadow: {
            enabled: false,
          },
        },
      };
      var chart = new ApexCharts(document.querySelector("#donut-simple"), options);
      chart.render();*/
    </script>


  </body>

  </html>
<?php
}
ob_end_flush();
?>