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

    <?php $title_page = "Ordenes";
    include("template/head.php"); ?>
    
    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="../assets/libs/glightbox/css/glightbox.min.css">
  <style>
    .textarea_datatable {
      resize: vertical;
      overflow-y: auto;
    }

    .imagen-metodo-pago img {
      /*width: 100% !important;  Ajusta el ancho al contenedor */
      /*height: auto !important;  Mantén la proporción de aspecto */
      width: 140px !important;/* Máximo ancho permitido */        
      height: 130px !important;/* Máximo alto permitido */        
      object-fit: contain !important;/* Asegura que la imagen no se deforme */        
      border: 1px solid #ddd !important;/* Opcional: agrega un borde para resaltar el contenedor */        
      box-sizing: border-box !important;
    }

    /* Ajusta el tamaño de las imágenes - Suaviza la transición */
    .div_pago_rapido img { width: 60px; height: 100%; cursor: pointer; border: 3px solid #ccc; border-radius: 5px; transition: border-color 0.3s ease; }
    /* Cambia el borde al pasar el ratón */
    .div_pago_rapido img:hover { border-color: #007bff; }

</style>
  </head>

  <body id="body-ordenes">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['orden_venta_cobrar'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <!-- <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);limpiar_form();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);limpiar_form();" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button> -->
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Ordenes</p>
                    <span class="fs-semibold text-muted" >Administra tus Ordenes.</span>
                  </div>
                </div>
              </div>

              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Ventas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ordenes</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xl-12">
                <div class="card custom-card">
                  <div class="card-body py-2">
                    <div class="row">
                      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-10 col-xxl-10">
                       <div class="activar-scroll-x-auto scroll-sm">                                             
                          <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                          <div style="width: 350px;  min-width: 200px;">
                            <div class="form-group">
                              <label for="filtro_fecha_i" class="form-label">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_i();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                                Fecha Inicio</label>
                              <input type="date" class="form-control" name="filtro_fecha_i" id="filtro_fecha_i" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                            </div>
                          </div>
                          <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                          <div style="width: 350px;  min-width: 200px;">
                            <div class="form-group">
                              <label for="filtro_fecha_f" class="form-label">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_f();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                                Fecha Fin</label>
                              <input type="date" class="form-control" name="filtro_fecha_f" id="filtro_fecha_f" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                            </div>
                          </div> 
                          <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                          <div style="width: 350px;  min-width: 200px;">
                            <div class="form-group">
                              <label for="" class="form-label">Clik para Actualizar <i class="las la-sync-alt"></i></label> <br>
                              <a type="button" class="form-control btn btn-secondary btn-wave btn-w-lg" onclick="delay(function(){Actualizar_data_Orden()}, 50 );">Actualizar</a>
                            </div>
                          </div>                           
                          
                        </div> 
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="row Div_ListarOrden listarOrdenesCobrar">
                  <!-- Start::lista de todas las ordenes para cobrar -->
                </div>
                <div class="row Div_PagarOrden"  style="display: none;">
                  <div class="col-6">
                    <div class="card custom-card">
                        <!-- Start::page-header -->
                        <div class="card-header d-block py-2">
                          <div>
                            <div class="d-md-flex d-block align-items-center ">
                              <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1); limpiar_venta_cobro();"><i class="ri-arrow-left-line"></i></button>                              
                              <div>
                                <span class="fw-semibold fs-18 mb-0">Nro Orden : <span class="text-primary">OV001-10</span> </span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- End::page-header -->
                      <div class="card-body">
                        <form name="form-PagarOrden" id="form-PagarOrden" method="POST" class="needs-validation" novalidate>
                          <div class="row" id="cargando-1-fomulario">
                            <input type="hidden" name="idventa" id="idventa">

                            <!--  TIPO COMPROBANTE  -->
                            <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                              <div class="mb-sm-0 mb-2">
                                <p class="fs-14 mb-2 fw-semibold">Tipo de comprobante </p>
                                <div class="mb-0 authentication-btn-group">
                                  <input type="hidden" id="f_tipo_comprobante_hidden" value="12">
                                  <input type="hidden" name="f_idsunat_c01" id="f_idsunat_c01" value="12">
                                  <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                                    <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante12" value="12" onchange="ver_series_comprobante('#f_tipo_comprobante12');es_valido_cliente();">
                                    <label class="btn btn-sm btn-outline-primary btn-tiket" for="f_tipo_comprobante12"><i class='bx bx-file-blank me-1 align-middle d-inline-block'></i> Ticket</label>

                                    <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante03" value="03" onchange="ver_series_comprobante('#f_tipo_comprobante03'); es_valido_cliente();">
                                    <label class="btn btn-sm btn-outline-primary btn-boleta" for="f_tipo_comprobante03"><i class="ri-article-line me-1 align-middle d-inline-block"></i>Boleta</label>

                                    <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante01" value="01" onchange="ver_series_comprobante('#f_tipo_comprobante01'); es_valido_cliente();">
                                    <label class="btn btn-sm btn-outline-primary" for="f_tipo_comprobante01"><i class="ri-article-line me-1 align-middle d-inline-block"></i> Factura</label>

                                  </div> 
                                </div>
                              </div>
                            </div>

                            <div class="col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                              <div class="form-group">
                                <label for="f_serie_comprobante" class="form-label">Serie <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" title="Recuerde que si esta vacío no podra emitir para el tipo de comprobante selecionado, tendra que solicitar acceso."></i> <span class="f_charge_serie_comprobante"></span></label>
                                <select class="form-control" name="f_serie_comprobante" id="f_serie_comprobante"></select>
                              </div>
                            </div>

                            <div class="col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                              <div class="form-group">
                                <label for="f_serie_comprobante" class="form-label">TOTAL <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" title="Recuerde que si esta vacío no podra emitir para el tipo de comprobante selecionado, tendra que solicitar acceso."></i> <span class="f_charge_serie_comprobante"></span></label>
                                <input type="text" class="form-control f_venta_total" id="f_venta_total"   name="f_venta_total"  readonly>
                              </div>
                            </div>

                            <!--  Cliente  -->
                            <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 mt-3 div_idpersona_cliente">
                              <div class="form-group">
                                <label for="f_idpersona_cliente" class="form-label">                                  
                                  <!-- <span class="badge bg-warning m-r-4px cursor-pointer" onclick="modal_add_trabajador();" data-bs-toggle="tooltip" title="Actualizar Datos"><i class="bi bi-pencil"></i></span> -->
                                  <span class="badge bg-success m-r-4px cursor-pointer" onclick="crear_cliente();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_idpersona_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  Cliente
                                  <span class="charge_f_idpersona_cliente"></span>
                                </label>
                                <select class="form-control" name="f_idpersona_cliente" id="f_idpersona_cliente" onchange="es_valido_cliente();"></select>
                              </div>
                            </div>

                            <div class="col-12 pt-3 activar-scroll-x-auto div_pago_rapido">
                              <button type="button" class="btn btn-primary btn-sm pago_rapido" onclick="pago_rapido(this)" data-bs-toggle="tooltip" title="Click para agregar monto!">0</button>
                              <img src="../assets/images/monedas/10-soles.webp" alt="10" onclick="pago_rapido_moneda(10)" data-bs-toggle="tooltip" title="Click para agregar monto!" >
                              <img src="../assets/images/monedas/20-soles.webp" alt="20" onclick="pago_rapido_moneda(20)" data-bs-toggle="tooltip" title="Click para agregar monto!" >
                              <img src="../assets/images/monedas/50-soles.webp" alt="50" onclick="pago_rapido_moneda(50)" data-bs-toggle="tooltip" title="Click para agregar monto!" >
                              <img src="../assets/images/monedas/100-soles.webp" alt="100" onclick="pago_rapido_moneda(100)" data-bs-toggle="tooltip" title="Click para agregar monto!" >
                              <img src="../assets/images/monedas/200-soles.webp" alt="200" onclick="pago_rapido_moneda(200)" data-bs-toggle="tooltip" title="Click para agregar monto!" >                              
                            </div>
                            
                            <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 div_m_pagos">
                              <div class="row">
                                <div class="col-lg-12 mt-3">
                                  <div class="flex-fill d-flex align-items-top border-bottom">
                                    <div class="me-2 cursor-pointer mb-1" data-bs-toggle="tooltip" title="Click para agregar!" onclick="agregar_new_mp(false);">
                                      <span class="avatar avatar-sm text-primary border bg-light"><i class="ti ti-layout-grid-add fs-15"></i></span>
                                    </div>
                                    <div class="flex-fill">
                                      <p class="fw-semibold fs-14 mb-0">Agregar <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" title="Haz clic para agregar diferentes métodos de pago y sus respectivos comprobantes."></i> </p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row">

                                <div class="col-lg-12">
                                  <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 pt-3">
                                      <div class="form-group">
                                        <label for="f_metodo_pago_1" class="form-label">
                                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_metodo_pago(1);" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                          Método de pago
                                          <span class="charge_f_metodo_pago_1"></span>
                                        </label>
                                        <select class="form-control form-control-sm f_metodo_pago_validar" name="f_metodo_pago[0]" id="f_metodo_pago_1" onchange="capturar_pago_venta(1);">
                                          <!-- Aqui se listara las opciones -->
                                        </select>
                                      </div>
                                    </div>

                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3">
                                      <div class="form-group">
                                        <label for="f_total_recibido_1" class="form-label">Monto a pagar</label>
                                        <input type="number" name="f_total_recibido[0]" id="f_total_recibido_1" class="form-control form-control-sm f_total_recibido_validar"  onClick="this.select();" onchange="calcular_vuelto(1);" onkeyup="calcular_vuelto(1);" placeholder="Ingrese monto a pagar.">
                                      </div>
                                    </div>

                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3">
                                      <div class="form-group">
                                        <label for="f_total_vuelto" class="form-label">Vuelto <small class="falta_o_completo_1"></small></label>
                                        <input type="number" name="f_total_vuelto" id="f_total_vuelto" class="form-control-plaintext form-control-sm px-2 f_total_vuelto" readonly placeholder="Ingrese monto a pagar.">
                                      </div>
                                    </div>

                                    <div class="col-12" id="content-metodo-pago-1">
                                      <div class="row">
                                        <!-- Código de Baucher -->
                                        <div class="col-sm-6 col-lg-6 col-xl-6 pt-3">
                                          <div class="form-group">
                                            <label for="f_mp_serie_comprobante_1">Código de Baucher <span class="span-code-baucher-pago-1"></span> </label>
                                            <input type="text" name="f_mp_serie_comprobante[]" id="f_mp_serie_comprobante_1" class="form-control" onClick="this.select();" placeholder="Codigo de baucher" />
                                          </div>
                                        </div>
                                        <!-- Baucher -->
                                        <div class="col-sm-6 col-lg-6 col-xl-6 pt-3 hidden">
                                          <div class="form-group">
                                            <input type="file" class="multiple-filepond f_mp_comprobante_validar" multiple name="f_mp_comprobante[0]" id="f_mp_comprobante_1" data-allow-reorder="true" data-max-file-size="3MB" accept="image/*, application/pdf">
                                            <input type="hidden" name="f_mp_comprobante_old_1" id="f_mp_comprobante_old_1">
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="col-12">
                                      <div class="border-bottom border-block-end-dashed py-2"></div>
                                    </div>
                                  </div>
                                </div>

                              </div>
                              <div class="row" id="html-metodos-de-pagos">
                              </div>
                            </div>
                          </div>
                          <div class="row" id="cargando-2-fomulario" style="display: none;">
                            <div class="col-lg-12 text-center">
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>
                                                  <!-- Chargue -->
                        <div class="p-l-25px col-lg-12" id="barra_progress_orden_pago_div" style="display: none;">
                          <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <div id="barra_progress_orden_pago" class="progress-bar" style="width: 0%">
                              <div class="progress-bar-value">0%</div>
                            </div>
                          </div>
                        </div>

                          <button type="submit" style="display: none;" id="submit-PagarOrden">Submit</button>
                        </form>
                      </div>
                      <div class="card-footer border-top-0">
                        <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1); limpiar_venta_cobro();"><i class="las la-times fs-lg"></i> Cancelar</button>
                        <button type="button" class="btn btn-success btn-guardarOrdenPago" id="guardar_registro_venta"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                      </div>
                    </div>
                  </div>
                </div>
                
                  
              </div>


                      
              <!-- MODAL - AGREGAR CLIENTE - charge 5,6 -->
              <div class="modal fade modal-effect" id="modal-agregar-nuevo-cliente" tabindex="-1" aria-labelledby="Modal-agregar-nuevo-clienteLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title title-modal-nuevo-cliente" id="Modal-agregar-nuevo-clienteLabel1">Agregar Cliente</h6>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">

                      <form name="form-agregar-nuevo-cliente" id="form-agregar-nuevo-cliente" method="POST" class="needs-validation" novalidate>

                        <div class="row" id="cargando-5-formulario">
                          <!-- idpersona -->
                          <input type="hidden" name="cli_idpersona" id="cli_idpersona" />
                          <input type="hidden" name="cli_tipo_persona_sunat" id="cli_tipo_persona_sunat" value="NATURAL" />
                          <input type="hidden" name="cli_idtipo_persona" id="cli_idtipo_persona" value="3" />
                          <input type="hidden" name="cli_centro_poblado" id="cli_centro_poblado" value="1" />

                          <div class="col-lg-12 col-xl-12 col-xxl-12">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2">DATOS GENERALES</b></label></div>
                              </div>
                            </div> <!-- /.row -->
                            <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">

                                <!-- Tipo documento -->
                                <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_tipo_documento" class="form-label">Tipo documento: </label>
                                    <select name="cli_tipo_documento" id="cli_tipo_documento" class="form-select" required>
                                      <option value="0">NINGUNO</option>
                                      <option value="1">DNI</option>
                                      <option value="6">RUC</option>
                                    </select>
                                  </div>
                                </div>

                                <!--  Numero Documento -->
                                <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_numero_documento" class="form-label">Numero Documento:</label>
                                    <div class="input-group">
                                      <input type="number" class="form-control" name="cli_numero_documento" id="cli_numero_documento" placeholder="" aria-describedby="icon-view-password">
                                      <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-nuevo-cliente', '_t1', '#cli_tipo_documento', '#cli_numero_documento', '#cli_nombre_razonsocial', '#cli_apellidos_nombrecomercial', '#cli_direccion', '#cli_distrito', '#cli_titular_cuenta' );">
                                        <i class='bx bx-search-alt' id="search_t1"></i>
                                        <div class="spinner-border spinner-border-sm" role="status" id="charge_t1" style="display: none;"></div>
                                      </button>
                                    </div>
                                  </div>
                                </div>

                                <!-- Nombres -->
                                <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_nombre_razonsocial" class="form-label label-nom-raz">Nombres: </label></label>
                                    <input type="text" class="form-control" name="cli_nombre_razonsocial" id="cli_nombre_razonsocial" onkeyup="mayus(this);">
                                  </div>
                                </div>

                                <!-- Apellidos -->
                                <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6 ">
                                  <div class="form-group">
                                    <label for="cli_apellidos_nombrecomercial" class="form-label label-ape-come">Apellidos: </label></label>
                                    <input type="text" class="form-control" name="cli_apellidos_nombrecomercial" id="cli_apellidos_nombrecomercial" onkeyup="mayus(this);">
                                  </div>
                                </div>

                                <!-- Correo -->
                                <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_correo" class="form-label">Correo:</label>
                                    <input type="email" class="form-control" name="cli_correo" id="cli_correo">
                                  </div>
                                </div>

                                <!-- Celular -->
                                <div class="col-md-12 col-lg-3 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_celular" class="form-label">Celular:</label>
                                    <input type="tel" class="form-control" name="cli_celular" id="cli_celular">
                                  </div>
                                </div>

                              </div> <!-- /.row -->
                            </div> <!-- /.card-body -->
                          </div> <!-- /.col-lg-12 -->



                          <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">

                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2">UBICACIÓN</b></label></div>
                              </div>
                            </div>

                            <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">

                              <div class="row ">

                                <!-- Dirección -->
                                <div class="mb-1 col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label class="form-label" for="cli_direccion">Dirección: <sup class="text-danger">*</sup></label>
                                    <textarea name="cli_direccion" class="form-control inpur_edit" id="cli_direccion" placeholder="ejemp: Jr las flores" rows="2"></textarea>
                                  </div>
                                </div>

                                <!-- Dirección -->
                                <div class="mb-1 col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label class="form-label" for="cli_direccion_referencia">Referencia: <sup class="text-danger">*</sup></label>
                                    <textarea name="cli_direccion_referencia" class="form-control inpur_edit" id="cli_direccion_referencia" placeholder="ejemp: Al costado del colegio" rows="2"></textarea>
                                  </div>
                                </div>
                                
                                
                                <!-- Distrito -->
                                <div class="mb-1 col-12 col-md-12 col-lg-12 col-xl-12 col-xl-12 col-xxl-12">
                                  <div class="form-group">
                                    <label for="cli_distrito" class="form-label">Distrito: </label></label>
                                    <select name="cli_distrito" id="cli_distrito" class="form-control" placeholder="Seleccionar" require>
                                    </select>
                                  </div>
                                </div>
                                <!-- Departamento -->
                                <div class="mb-1 col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_departamento" class="form-label">Departamento <small class="text-info">(no editable)</small>: <span class="chargue-pro"></span></label>
                                    <input type="text" class="form-control" name="cli_departamento" id="cli_departamento" readonly>
                                  </div>
                                </div>
                                <!-- Provincia -->
                                <div class="mb-1 col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_provincia" class="form-label">Provincia <small class="text-info">(no editable)</small>: <span class="chargue-dep"></span></label>
                                    <input type="text" class="form-control" name="cli_provincia" id="cli_provincia" readonly>
                                  </div>
                                </div>
                                <!-- Ubigeo -->
                                <div class="mb-1 col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cli_ubigeo" class="form-label">Ubigeo <small class="text-info">(no editable)</small>: <span class="chargue-ubi"></span></label>
                                    <input type="text" class="form-control" name="cli_ubigeo" id="cli_ubigeo" readonly>
                                  </div>
                                </div>

                              </div>

                            </div>

                          </div>

                        </div> <!-- /.row -->

                        <div class="row" id="cargando-5-formulario" style="display: none;">
                          <div class="col-lg-12 text-center">
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div> <!-- /.row -->

                        <!-- Chargue -->
                        <div class="p-l-25px col-lg-12" id="barra_progress_nuevo_cliente_div" style="display: none;">
                          <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <div id="barra_progress_nuevo_cliente" class="progress-bar" style="width: 0%">
                              <div class="progress-bar-value">0%</div>
                            </div>
                          </div>
                        </div>
                        <!-- Submit -->
                        <button type="submit" style="display: none;" id="submit-form-nuevo-cliente">Submit</button>
                      </form>

                    </div>
                    <div class="modal-footer py-1">
                      <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
                      <button type="button" class="btn btn-sm btn-success label-btn" id="guardar_registro_nuevo_cliente"><i class="bx bx-save bx-tada"></i> Guardar</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End::Modal -->


              <!-- MODAL - IMPRIMIR -->
              <div class="modal fade modal-effect" id="modal-imprimir-comprobante" tabindex="-1" aria-labelledby="modal-imprimir-comprobante-label" aria-hidden="true">
                <div class="modal-dialog modal-md">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title" id="modal-imprimir-comprobante-label">COMPROBANTE</h6>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div id="html-imprimir-comprobante" class="text-center"> </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End::Modal -->

            </div>
            <!-- End::row-1 -->
          </div>
        </div>
        <!-- End::app-content -->





      <?php } else {
        $title_submodulo = 'Ordenes';
        $descripcion = 'Lista de Ordenes del sistema!';
        $title_modulo = 'Ventas';
        include("403_error.php");
      } ?>

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>


        <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Filepond JS -->
    <script src="../assets/libs/filepond/filepond.min.js"></script>
    <script src="../assets/libs/filepond/locale/es-es.js"></script>
    <script src="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js"></script>
    <script src="https://unpkg.com/medium-zoom/dist/medium-zoom.min.js"></script>

    <!-- Dropzone JS -->
    <script src="../assets/libs/dropzone/dropzone-min.js"></script>
    <!-- Gallery JS -->
    <script src="../assets/libs/glightbox/js/glightbox.min.js"></script>
    <!-- Dragula JS -->
    <script src="../assets/libs/dragula/dragula.min.js"></script>    



    <script src="scripts/orden_venta_cobrar.js?version_jdl=1.16"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>


  </body>

  </html>
<?php
}
ob_end_flush();
?>