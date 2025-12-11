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

    <?php $title_page = "Reporte Contador";
    include("template/head.php"); ?>

    <style>

      #tabla-reporte-detalle_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-reporte-detalle_filter label { width: 100% !important;  }
      #tabla-reporte-detalle_filter label input { width: 100% !important; }

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
      <?php if ($_SESSION['reporte_contador'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Reporte Contador!</p>
                    <span class="fs-semibold text-muted">Reporte para el contador.</span>
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
                   
                  <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-4 col-lg-2 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_fecha_i" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_fecha_i');" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                        Fecha Inicio</label>
                      <input type="date" class="form-control" name="filtro_fecha_i" id="filtro_fecha_i" value="<?php echo date("Y-m-d"); ?>" >
                    </div>
                  </div>

                  <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-4 col-lg-2 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_fecha_f" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_fecha_f');" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                        Fecha Fin</label>
                      <input type="date" class="form-control" name="filtro_fecha_f" id="filtro_fecha_f" value="<?php echo date("Y-m-d"); ?>" >
                    </div>
                  </div>

                  <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_trabajador" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_trabajador');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Trabajador
                        <span class="charge_filtro_trabajador"></span>
                      </label>
                      <select class="form-control" name="filtro_trabajador" id="filtro_trabajador" > <!-- lista de categorias --> </select>
                    </div>
                  </div> 

                  <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-4 col-lg-2 col-xl-2 col-xxl-2">
                    <div class="form-group">
                      <label for="filtro_tipo_persona" class="form-label">
                        Tipo Persona
                      </label>
                      <select class="form-control" name="filtro_tipo_persona" id="filtro_tipo_persona" >
                        <option value="">TODOS</option>
                        <option value="NATURAL">NATURAL</option>
                        <option value="JURÍDICA">JURÍDICA</option>
                      </select>
                    </div>
                  </div> 

                  <!-- ::::::::::::::::::::: FILTRO CENTRO POBLADO :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_centro_poblado" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_centro_poblado');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Centro Poblado
                        <span class="charge_filtro_centro_poblado"></span>
                      </label>
                      <select class="form-control" name="filtro_centro_poblado" id="filtro_centro_poblado" > <!-- lista de categorias --> </select>
                    </div>
                  </div> 

                  <!-- ::::::::::::::::::::: FILTRO ESTADO SUNAT :::::::::::::::::::::: -->
                  <div class="col-sm-6 col-md-4 col-lg-4 col-xl-4 col-xxl-4 ">
                    <div class="form-group">     
                      <label for="filtro_estado_sunat" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_estado_sunat');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Estado Sunat
                        <span class="charge_filtro_estado_sunat"></span>
                      </label>                
                      <select class="form-control" multiple="multiple" name="filtro_estado_sunat[]" id="filtro_estado_sunat" > 
                        
                      </select>
                    </div>
                  </div>  

                  <!-- ::::::::::::::::::::: FILTRO TIPO COMPROBANTE :::::::::::::::::::::: -->
                  <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 col-xxl-8 ">
                    
                    <div class="form-group">       
                      <label for="filtro_estado_sunat" class="form-label">
                        Tipo Comprobante
                      </label>                         
                      <select class="form-control" multiple="multiple" name="filtro_tipo_comprobante[]" id="filtro_tipo_comprobante" > 
                        <option value="2" selected>01 FACTURA</option>
                        <option value="3" selected>03 BOLETA</option>
                        <option value="7">07 NC FACTURA</option>
                        <option value="8">07 NC BOLETA</option>
                        <option value="12" selected>12 TICKETS (nota de venta)</option>
                        <option value="46">103 ORDEN DE VENTA</option>
                      </select>
                    </div>
                  </div>                  

                </div>
                  
                <div class="row">

                  <!-- ::::::::::::::::::::::::::::: TABLA DE VENTA DETALLE ::::::::::::::::::::::::::::: -->
                  <div class="col-12 col-lg-12 col-xxl-12 mt-3">
                    <div class="card custom-card">
                      <div class="card-header">
                        <div class="card-title">
                          <button class="btn-modal-effect btn btn-info " onclick="previsulizar_documentos();"><i class="ti ti-eye-bolt"></i> Previsualizar </button>
                          <button class="btn-modal-effect btn btn-success btn-exportar-ventas " onclick="exportar_documentos();"><i class="ti ti-file-excel"></i> Descargar Excel</button>
                        </div>
                      </div>
                      <div class="card-body pb-1">
                        <div id="div-tabla" class="table-responsive">
                          <table id="tabla-reporte-detalle" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                            <thead class="buscando_tabla">
                              <tr id="id_buscando_tabla" style="display: none;">
                                <th colspan="15" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                              </tr>
                              <tr>
                                <th class="text-center">  <center>ID</center>             </th>  
                                <th>Dia</th>                         
                                <th class="text-center">  <center>Emision</center>                      </th>
                                <th>Periodo</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Num.</th>
                                <th>Comp.</th>
                                <th>Num.</th>
                                <th class="text-nowrap">Total Cobro</th>
                                <th>Recibido</th>
                                <th>Vuelto</th>
                                <th>  <center>Método</center>              </th>
                                <th> <center>Creador</center>       </th>
                                <th> <center>Estado</center>       </th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th class="text-center">  <center>ID</center>             </th>  
                                <th>Dia</th>                         
                                <th class="text-center">  <center>Emision</center>                      </th>
                                <th>Periodo</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Num.</th>
                                <th>Comp.</th>
                                <th>Num.</th>
                                <th class="text-nowrap">Total Cobro</th>
                                <th>Recibido</th>
                                <th>Vuelto</th>
                                <th>  <center>Método</center>              </th>
                                <th> <center>Creador</center>       </th>
                                <th> <center>Estado</center>       </th>
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


    <script src="scripts/reporte_contador.js?version_jdl=1.16"></script>
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