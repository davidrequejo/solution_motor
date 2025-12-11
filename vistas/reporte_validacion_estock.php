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
      <?php if ($_SESSION['reporte_stock_de_producto'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="guardar_editar_copia_stock();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Lista de Validacion Stock!</p>
                    <span class="fs-semibold text-muted">Reporte de validacion de stock.</span>
                  </div>
                </div>
              </div>

              <div class="btn-list mt-md-0 mt-2 mb-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Validacion</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xxl-12 col-xl-12 ">
                
                <div class="row" >
          
                  <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_copia_stock" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_copia_stock');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Copias Stock
                        <span class="charge_filtro_copia_stock"></span>
                      </label>
                      <select class="form-control" name="filtro_copia_stock" id="filtro_copia_stock" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                    </div>
                  </div>    
                  
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_estado_stock" class="form-label">Estados Stock </label>
                      <select class="form-control" name="filtro_estado_stock" id="filtro_estado_stock" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> 
                        <option value="TODOS" >TODOS</option>
                        <option value="CMa0"  >Copia - Mayor a 0</option>
                        <option value="AMa0"  >Actual - Mayor a 0</option>
                        <option value="CAMa0" >Copia y Actual - Mayor a 0</option>
                        <option value="CAMe0" >Copia y Actual - Menor a 0</option>
                        <option value="CAD"   >Copia y Actual - Differente de 0</option>
                        <option value="VSV"   >Ver solo vendido</option>
                        <option value="SVD"   >Solo vendido - Diferente de 0</option>
                        <option value="SVMa0" >Solo vendido - Mayor a 0</option>
                        <option value="SVMe0" >Solo vendido - Menor a 0</option>
                      </select>
                    </div>
                  </div>    
                  

                </div>
                  
                <div class="row">                  

                  <!-- ::::::::::::::::::::::::::::: TABLA DE DETALLE ::::::::::::::::::::::::::::: -->
                  <div class="col-12 col-md-12 col-lg-12 col-xxl-12 mt-3">
                    <div class="card custom-card">
                      <div class="card-header">
                        <div class="card-title">Detalle de Copias!</div>
                      </div>
                      <div class="card-body pb-1">
                        <div id="div-tabla" class="table-responsive">
                          <div  class="bg-danger pt-2 pb-2 buscando_tabla" style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </div>
                          <table id="tabla-detalle-stock" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                            <thead >                              
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-nowrap">Copia Stock</th>
                                <th class="text-nowrap" >Actual Stock <!-- <span style="color: transparent !important;">-----</span> --> </th>                                       
                                <th class="text-nowrap" >Diff Copia/Actual <!-- <span style="color: transparent !important;">-----</span> --> </th>                                      
                                <th class="text-nowrap" >Cant Venta</th>
                                <th class="text-nowrap" >Diff Venta</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th class="text-center">#</th>                                      
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-nowrap">Copia Stock</th>
                                <th class="text-nowrap" >Actual Stock <!-- <span style="color: transparent !important;">-----</span> --> </th>                                       
                                <th class="text-nowrap" >Diff Copia/Actual <!-- <span style="color: transparent !important;">-----</span> --> </th>                                      
                                <th class="text-nowrap" >Cant Venta</th>
                                <th class="text-nowrap" >Diff Venta</th>
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


    <script src="scripts/reporte_validacion_estock.js?version_jdl=1.16"></script>
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