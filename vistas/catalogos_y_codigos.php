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
      <?php $title_page = "Catálogos de Códigos";
      include("template/head.php"); ?>
    </head>

    <body >
      <?php include("template/switcher.php"); ?>
      <?php include("template/loader.php"); ?>

      <div class="page">
        <?php include("template/header.php") ?>
        <?php include("template/sidebar.php") ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <!-- <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <button type="button" class="btn-modal-effect btn btn-primary label-btn m-r-10px" data-bs-toggle="modal" data-bs-target="#modal-doc-identidad" onclick="limpiar_form();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Documento de Identidad</p>
                    <span class="fs-semibold text-muted">Administra los tipos de documentos de identidad.</span>
                  </div>
                </div>
              </div>
              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Documento de Identidad</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Administración</li>
                  </ol>
                </nav>
              </div>
            </div> -->
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xl-12">
                <div class="row justify-content-center">
                  <div class="col-xl-6">
                    <div class="text-center p-3 faq-header mb-4">
                      <h5 class="mb-1 text-primary op-5 fw-semibold">SUNAT</h5>
                      <h4 class="mb-1 fw-semibold">Catálogos de Códigos </h4>
                      <p class="fs-15 text-muted op-7">Estos códigos no son editables y serán usados para la <b>Facturación Electrónica</b>! </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card custom-card">
                  <div class="card-header">
                    <div class="card-title"> Código 05: Tipos de tributos y otros conceptos </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-tipo-tributo">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Nombre</th>
                          <th>Código</th>
                          <th>Abreviatura</th> 
                          <th>unece5153</th>                          
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card custom-card">
                  <div class="card-header">
                    <div class="card-title"> Código 06: tipo de documento de identidad </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-doc-identidad">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Nombre</th>
                          <th>Código</th>
                          <th>Abreviatura</th>                          
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card custom-card">
                  <div class="card-header">
                    <div class="card-title"> Código 07: Tipo de afectación del IGV </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-afeccion-igv">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Nombre</th>
                          <th>Código</th>
                          <th>Tributo</th>                          
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card custom-card">
                  <div class="card-header">
                    <div class="card-title"> Códigos 09: Tipo de nota de crédito electrónica </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-codigo-nota-credito">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Nombre</th>
                          <th>Código</th>                        
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card custom-card">
                  <div class="card-header">
                    <div class="card-title"> Códigos 10: Tipo de nota de débito electrónica </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-codigo-nota-debito">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Nombre</th>
                          <th>Código</th>                       
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card custom-card">
                  <div class="card-header">
                    <div class="card-title"> Códigos 11: Tipo de valor de venta (Resumen diario de boletas y notas) </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-codigo-valor-venta">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Nombre</th>
                          <th>Código</th>                         
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
              
            </div>
            <!-- End::row-1 -->
        
          </div>
        </div>
        <!-- End::app-content -->
       

        <?php include("template/search_modal.php"); ?>
        <?php include("template/footer.php"); ?>
      </div>

      <?php include("template/scripts.php"); ?>
      <?php include("template/custom_switcherjs.php"); ?>

      <!-- Script de Modulos -->
      <script src="scripts/catalogos_y_codigos.js?version_jdl=1.16"></script>

      <script>
        $(document).ready(function () {
          $('[data-bs-toggle="tooltip"]').tooltip();
        });        
      </script>


    </body>

  </html>
<?php
}
ob_end_flush();
?>