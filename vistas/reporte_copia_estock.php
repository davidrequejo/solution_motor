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

    <?php $title_page = "Reporte Copias";
    include("template/head.php"); ?>

    <style>

      #tabla-resumen-copias_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-resumen-copias_filter label { width: 100% !important;  }
      #tabla-resumen-copias_filter label input { width: 100% !important; }

      #tabla-detalle-stock_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-detalle-stock_filter label { width: 100% !important;  }
      #tabla-detalle-stock_filter label input { width: 100% !important; }

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
      <?php if ($_SESSION['reporte_copia_estock'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="guardar_editar_copia_stock();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Lista de Copias Stock!</p>
                    <span class="fs-semibold text-muted">Reporte de copias de stock.</span>
                  </div>
                </div>
              </div>

              <div class="btn-list mt-md-0 mt-2 mb-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Copias</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xxl-12 col-xl-12 ">
                
                <div class="row" >

                 

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
                        Creador
                        <span class="charge_filtro_trabajador"></span>
                      </label>
                      <select class="form-control" name="filtro_trabajador" id="filtro_trabajador" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                    </div>
                  </div>                    
                  

                </div>
                  
                <div class="row">

                  <!-- ::::::::::::::::::::::::::::: TABLA DE RESUMEN ::::::::::::::::::::::::::::: -->
                  <div class="col-12 col-md-6 col-lg-4 col-xxl-4 mt-3">
                    <div class="card custom-card">
                      <div class="card-header">
                        <div class="card-title">Registro de Copias!</div>
                      </div>
                      <div class="card-body pb-1">
                        <div id="div-tabla" class="table-responsive">
                          <table id="tabla-resumen-copias" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                            <thead class="buscando_tabla">
                              <tr id="id_buscando_tabla" style="display: none;">
                                <th colspan="9" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                              </tr>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Ver</th>                                
                                <th>Creador</th>                                
                              </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Ver</th> 
                                <th>Creador</th>  
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>                    
                  </div>

                  <!-- ::::::::::::::::::::::::::::: TABLA DE DETALLE ::::::::::::::::::::::::::::: -->
                  <div class="col-12 col-md-6 col-lg-8 col-xxl-8 mt-3">
                    <div class="card custom-card">
                      <div class="card-header">
                        <div class="card-title">Detalle de Copias!</div>
                      </div>
                      <div class="card-body pb-1">
                        <div id="div-tabla" class="table-responsive">
                          <table id="tabla-detalle-stock" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                            <thead class="buscando_tabla">
                              <tr id="id_buscando_tabla" style="display: none;">
                                <th colspan="9" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                              </tr>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th class="text-nowrap" >Compra <span style="color: transparent !important;">-----</span> </th>                                      
                                <th class="text-nowrap" >Venta <span style="color: transparent !important;">-----</span> </th>                                      
                                <th>Estado</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th class="text-nowrap" >Compra </th>                                      
                                <th class="text-nowrap" >Venta </th>                                      
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


    <script src="scripts/reporte_copia_estock.js?version_jdl=1.16"></script>
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