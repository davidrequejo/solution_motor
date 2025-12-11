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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script> 
  <head>
    <?php $title_page = "Predicción";
    include("template/head.php"); ?>

    <style>

      #tabla-productos_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-productos_filter label { width: 100% !important;  }
      #tabla-productos_filter label input { width: 100% !important; }
      
    </style>
  </head>

  <body id="body-productos">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['producto']==1) { ?> <!-- .:::: PERMISO DE MODULO ::::. -->

      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                
                <div>
                  <p class="fw-semibold fs-18 mb-0">Predicción de Ventas</p>
                  <span class="fs-semibold text-muted">Analiza la Predicción de ventas.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">              
              <nav>
                <ol class="breadcrumb mb-0">
                  <!-- <li class="breadcrumb-item">
                    <div class="form-check form-switch mb-0">
                      <label class="form-check-label" for="generar-cod-correlativo"></label>
                      <input class="form-check-input cursor-pointer" type="checkbox" id="generar-cod-correlativo" name="generar-cod-correlativo" onchange="create_code_producto('PR');" checked data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Activar generador código de barra correlativamente automático">
                    </div>
                  </li> -->
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Prediccion</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Reportes</li>
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
                    <h2 class="text-center mb-4">Predicción de Ventas por Mes</h2>
                    <div style="position: relative">
                      <canvas id="ventasChart" height="100"></canvas>
                      <svg id="lineaTrazo" width="100%" height="300" style="position:absolute; top:0; left:0; pointer-events:none;"></svg>
                    </div>
                    <!-- ------------ Tabla de Productos ------------- -->
                    <div class=" mt-5 table-responsive" id="tablaProductos"  style="display:none;">
                      <h4 class="mb-3">Top 10 Productos Más Vendidos</h4>
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-productos">
                        <thead>
                          
                          <tr >
                            <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">#</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Stock</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Precio Compra</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Precio Venta</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">% Aprobación</th>
                          </tr>
                        </thead>
                        <tbody id="tbodyProductos"></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Stock</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>% Aprobación</th>
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
      <?php } else { $title_submodulo ='Producto'; $descripcion ='Lista de Producto del sistema!'; $title_modulo = 'Articulos'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/prediccion.js?version_jdl=1.16"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>


  </body>



  </html>
<?php
}
ob_end_flush();
?>