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
    <?php $title_page = "Ver Órdenes";
    include("template/head.php"); ?>

    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="../assets/libs/glightbox/css/glightbox.min.css">
    <style>
            
      #tabla-ventas_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-ventas_filter label { width: 100% !important;  }
      #tabla-ventas_filter label input { width: 100% !important; }

      #tabla-productos_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-productos_filter label { width: 100% !important;  }
      #tabla-productos_filter label input { width: 100% !important; }

    </style>
  </head>

  <body id="body-ventas" idusuario="<?php echo $_SESSION['idusuario']; ?>" idpersona="<?php echo $_SESSION['idpersona']; ?>" idpersona_trabajador="<?php echo $_SESSION['idpersona_trabajador']; ?>">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['orden_venta_listar'] == 1) { ?>
        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <!-- <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_venta(); " style="display: none;"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button> -->
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Lista de Ordenes de Venta</p>
                    <span class="fs-semibold text-muted">Administra tus ordenes.</span>
                  </div>
                </div>
              </div>
              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Ordenes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lista</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">

              <!-- TABLA - FACTURA -->
              <div class="col-xl-12" id="div-tabla">
                <div class="card custom-card">
                  <div class="p-3 " > 
                    <div class="row">
                      <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
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
                          <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                          <div style="width: 450px;  min-width: 200px;">
                            <div class="form-group">
                              <label for="filtro_cliente" class="form-label">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Cliente
                                <span class="charge_filtro_cliente"></span>
                              </label>
                              <select class="form-control" name="filtro_cliente" id="filtro_cliente" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                            </div>
                          </div>
                          <!-- ::::::::::::::::::::: FILTRO TIPO DE PERSONA :::::::::::::::::::::: -->
                          <div style="width: 150px;  min-width: 150px;">
                            <div class="form-group">
                              <label for="filtro_tipo_persona" class="form-label">                         
                                <span class="badge bg-info m-r-4px cursor-pointer" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Tipo Persona
                                <span class="charge_filtro_tipo_persona"></span>
                              </label>
                              <select class="form-control" name="filtro_tipo_persona" id="filtro_tipo_persona" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > 
                                <option value="NATURAL">NATURAL</option>
                                <option value="JURÍDICA">JURÍDICA</option>
                              </select>
                            </div>
                          </div>
                          <!-- ::::::::::::::::::::: FILTRO COMPROBANTE :::::::::::::::::::::: -->
                          <div style="width: 350px;  min-width: 200px;">
                            <div class="form-group">
                              <label for="filtro_comprobante" class="form-label">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_comprobante();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Comprobante
                                <span class="charge_filtro_comprobante"></span>
                              </label>
                              <select class="form-control" name="filtro_comprobante" id="filtro_comprobante" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                            </div>
                          </div>
                          <!-- ::::::::::::::::::::: FILTRO METODO PAGO :::::::::::::::::::::: -->
                          <div style="width: 350px;  min-width: 200px;">
                            <div class="form-group">
                              <label for="filtro_metodo_pago" class="form-label">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_metodo_pago();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Metodo de Pago
                                <span class="charge_filtro_metodo_pago"></span>
                              </label>
                              <select class="form-control" name="filtro_metodo_pago" id="filtro_metodo_pago" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                            </div>
                          </div> 
                          <!-- ::::::::::::::::::::: FILTRO CENTRO POBLADO :::::::::::::::::::::: -->
                          <div style="width: 250px;  min-width: 250px;">
                            <div class="form-group">
                              <label for="filtro_centro_poblado" class="form-label">                         
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_centro_poblado_venta();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Centro Poblado
                                <span class="charge_filtro_centro_poblado"></span>
                              </label> 
                              <select class="form-control" name="filtro_centro_poblado" id="filtro_centro_poblado" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > </select>
                            </div>
                          </div>                          
                        </div> 
                      </div>
                      
                    </div>                                    
                                                     
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-ventas">
                        <thead>
                          <tr>
                            <th class="text-center"><center>#</center></th>
                            <th class="text-center"><center>OP</center></th>
                            <th class="text-center"><center>ID</center></th>
                            <th>Creación</th>
                            <th>Cliente</th>
                            <th>Correlativo</th>
                            <th class="text-nowrap">Total <span style="color: transparent;" >--------</span></th>
                            <th>Cobrado?</th>                            
                            <th>Creador</th>                            
                            <th><center>Estado</center></th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center"><center>#</center></th>
                            <th class="text-center"><center>OP</center></th>
                            <th class="text-center"><center>ID</center></th>
                            <th>Creación</th>
                            <th>Cliente</th>
                            <th>Correlativo</th>
                            <th>Total</th>
                            <th>Cobrado</th> 
                            <th>Creador</th>                            
                            <th><center>Estado</center></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>              

              <!-- FORMULARIO charge 1,2 charge 3,4-->
              <div class="col-xl-12" id="div-formulario" style="display: none;">
                <div class="card custom-card">
                  <div class="card-body">

                    <!-- FORM - COMPROBANTE -->
                    <form name="form-facturacion" id="form-facturacion" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                      <div class="row" id="cargando-1-formulario">

                        <!-- IMPUESTO -->
                        <input type="hidden" name="o_idventa" id="o_idventa" />
                        <!-- IMPUESTO -->
                        <input type="hidden" class="form-control" name="o_impuesto" id="o_impuesto" value="0">
                        <!-- TIPO DOC -->
                        <input type="hidden" class="form-control" name="o_tipo_documento" id="o_tipo_documento" value="0">
                        <!-- NUMERO DOC -->
                        <input type="hidden" class="form-control" name="o_numero_documento" id="o_numero_documento" value="0">
                        

                        <!--  TIPO COMPROBANTE  -->
                        <input type="hidden" name="o_idsunat_c01" id="o_idsunat_c01" value="46">
                        <input type="hidden" name="o_tipo_comprobante" id="o_tipo_comprobante" value="103">

                        <div class="col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="row gy-3">                                                        

                            <div class="col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                              <div class="form-group">
                                <label for="o_serie_comprobante" class="form-label">Serie <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" title="Recuerde que si esta vacío no podra emitir para el tipo de comprobante selecionado, tendra que solicitar acceso."></i> <span class="o_charge_serie_comprobante"></span></label>
                                <select class="form-control" name="o_serie_comprobante" id="o_serie_comprobante"></select>
                              </div>
                            </div>

                            <!--  PROVEEDOR  -->
                            <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 div_idpersona_cliente">
                              <div class="form-group">
                                <label for="o_idcliente" class="form-label">                                  
                                  <!-- <span class="badge bg-warning m-r-4px cursor-pointer" onclick="modal_add_trabajador();" data-bs-toggle="tooltip" title="Actualizar Datos"><i class="bi bi-pencil"></i></span> -->
                                  <span class="badge bg-success m-r-4px cursor-pointer" onclick="crear_cliente();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_o_idcliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  Cliente
                                  <span class="charge_o_idcliente"></span>
                                </label>
                                <select class="form-control" name="o_idcliente" id="o_idcliente" ></select>
                              </div>
                            </div>                          
                            
                            <!-- DESCRIPCION -->
                            <div class="col-md-6 col-lg-12 col-xl-12 col-xxl-12"> 
                              <div class="form-group">
                                <label for="o_observacion_documento" class="form-label">Observacion <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" title="Esta observacion es para SUNAT."></i></label>
                                <textarea name="o_observacion_documento" id="o_observacion_documento" class="form-control" rows="2" placeholder="ejemp: Cobro de servicio de internet."></textarea>
                              </div>
                            </div>                            

                          </div>
                        </div>

                        <div class="col-md-12 col-lg-8 col-xl-8 col-xxl-8">
                          <div class="row" id="cargando-3-formulario">

                            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2 col-xxl-2 mt-xs-3 div_agregar_producto">
                              <button class="btn btn-info label-btn m-r-10px" type="button" onclick="listar_tabla_producto('PR');">
                                <i class="ri-add-circle-line label-btn-icon me-2"></i> Producto
                              </button>
                            </div>
                            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2 col-xxl-2 mt-xs-3 div_agregar_producto">
                              <button class="btn btn-primary label-btn m-r-10px" type="button" onclick="listar_tabla_producto('SR');">
                                <i class="ri-add-fill label-btn-icon me-2"></i> Servicio
                              </button>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-8 col-xxl-8 mt-xs-3 div_agregar_producto ">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="SI" id="precio_por_mayor">
                                <label class="form-check-label" for="precio_por_mayor">Precio por mayor?  </label>
                              </div>
                              <div class="position-relative">
                                <div class="input-group">
                                  <button type="button" class="input-group-text buscar_x_code" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Buscar por codigo de producto."><i class='bx bx-search-alt'></i></button>
                                  <input type="text" name="search_producto" id="search_producto" class="form-control" onkeyup="mayus(this);" placeholder="Busca por código, nombre o escanea el producto.">                                
                                </div>
                                <ul id="searchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                              </div>
                            </div> 
                            
                            <!-- ------- TABLA PRODUCTOS SELECCIONADOS ------ -->
                            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive pt-3">
                              <table id="tabla-productos-seleccionados" class="table table-striped table-bordered table-condensed table-hover">
                                <thead class="bg-color-dark text-white">
                                  <th class="font-size-11px py-1" data-toggle="tooltip" data-original-title="Opciones">Op.</th>
                                  <th class="font-size-11px py-1 td-codigo-producto" style="display: none;" >Cod</th>
                                  <th class="font-size-11px py-1">
                                    <span class="badge bg-outline-info me-1 cursor-pointer span-codigo-producto-show" onclick="hide_show_codigo_prodcuto(false)" ><i class="bi bi-card-checklist"></i></span>
                                    <span class="badge bg-outline-info me-1 cursor-pointer span-codigo-producto-hide" style="display: none;" onclick="hide_show_codigo_prodcuto(true)" ><i class="bi bi-eye-slash"></i></span>Producto
                                  </th>
                                  <th class="font-size-11px py-1">Unidad</th>
                                  <th class="font-size-11px py-1">Cantidad</th>
                                  <th class="font-size-11px py-1" data-toggle="tooltip" data-original-title="Precio Unitario">P/U</th>
                                  <th class="font-size-11px py-1">Descuento</th>
                                  <th class="font-size-11px py-1">Subtotal</th>
                                  <th class="font-size-11px py-1 text-center"><i class='bx bx-cog fs-4'></i></th>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                  <td colspan="5"></td>

                                  <th class="text-right">
                                    <h6 class="fs-11 o_tipo_gravada">SUBTOTAL</h6>
                                    <h6 class="fs-11 ">DESCUENTO</h6>
                                    <h6 class="fs-11 val_igv">IGV (18%)</h6>
                                    <h5 class="fs-13 font-weight-bold">TOTAL</h5>
                                  </th>
                                  <th class="text-right">
                                    <h6 class="fs-11 font-weight-bold d-flex justify-content-between o_venta_subtotal"> <span>S/</span> 0.00</h6>
                                    <input type="hidden" name="o_venta_subtotal" id="o_venta_subtotal" />
                                    <input type="hidden" name="o_tipo_gravada" id="o_tipo_gravada" />

                                    <h6 class="fs-11 font-weight-bold d-flex justify-content-between o_venta_descuento"><span>S/</span> 0.00</h6>
                                    <input type="hidden" name="o_venta_descuento" id="o_venta_descuento" />

                                    <h6 class="fs-11 font-weight-bold d-flex justify-content-between o_venta_igv"><span>S/</span> 0.00</h6>
                                    <input type="hidden" name="o_venta_igv" id="o_venta_igv" />

                                    <h5 class="fs-13 font-weight-bold d-flex justify-content-between o_venta_total"><span>S/</span> 0.00</h5>
                                    <input type="hidden" name="o_venta_total" id="o_venta_total" />

                                  </th>
                                  <th></th>
                                </tfoot>
                              </table>
                            </div>                            

                          </div>
                          <!-- ::::::::::: CARGANDO ... :::::::: -->
                          <div class="row" id="cargando-4-formulario" style="display: none;">
                            <div class="col-lg-12 mt-5 text-center">
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>
                        </div>

                      </div>

                      <!-- ::::::::::: CARGANDO ... :::::::: -->
                      <div class="row" id="cargando-2-formulario" style="display: none;">
                        <div class="col-lg-12 mt-5 text-center">
                          <div class="spinner-border me-4" style="width: 2.5rem; height: 2.5rem;" role="status"></div>
                          <h6 class="bx-flashing">Cargando...</h6>
                        </div>
                      </div>

                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_venta_div" style="display: none;">
                        <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                          <div id="barra_progress_venta" class="progress-bar" style="width: 0%">
                            <div class="progress-bar-value">0%</div>
                          </div>
                        </div>
                      </div>
                      <!-- Submit -->
                      <button type="submit" style="display: none;" id="submit-form-venta">Submit</button>
                    </form>

                  </div>
                  <div class="card-footer border-top-0">
                    <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1); limpiar_form_venta();" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                    <button type="button" class="btn btn-success btn-guardar" id="guardar_registro_venta" style="display: none;"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                  </div>
                </div>
              </div>              

            </div>
            <!-- End::row-1 -->

            <!-- MODAL - VER MESES COBRADOS -->
            <div class="modal fade modal-effect" id="modal-ver-meses-cobrados" role="dialog" tabindex="-1" aria-labelledby="modal-ver-meses-cobradosLabel">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-ver-meses-cobradosLabel1">Meses Cobrados</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body" id="ver-meses-cobrados">
                    <div class="row">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer py-2">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="las la-times fs-lg"></i> Close</button>
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

            <!-- MODAL - VER ESTADO -->
            <div class="modal fade modal-effect" id="modal-ver-estado" tabindex="-1" aria-labelledby="modal-ver-estado-label" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-ver-estado-label">VER ESTADO</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="html-ver-estado" class="text-left">

                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- MODAL - VER FOTO -->
            <div class="modal fade modal-effect" id="modal-ver-imgenes" tabindex="-1" aria-labelledby="modal-ver-imgenes" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title title-ver-imgenes" id="modal-ver-imgenesLabel1">Imagen</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body html_modal_ver_imgenes">

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="las la-times fs-lg"></i> Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal - Ver foto proveedor -->

            <!-- MODAL - VER COMPROBANTE DE PAGO -->
            <div class="modal fade modal-effect" id="modal-ver-metodo-pago" tabindex="-1" aria-labelledby="modal-ver-metodo-pago" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title title-ver-metodo-pago" id="modal-ver-metodo-pagoLabel1">Doc:</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row" id="html-ver-metodo-pago">

                    </div>

                  </div>
                  <div class="modal-footer p-1">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="las la-times fs-lg"></i> Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal - Ver foto proveedor -->

            <!-- MODAL - SELECIONAR PRODUCTO -->
            <div class="modal fade modal-effect" id="modal-producto" tabindex="-1" aria-labelledby="title-modal-producto-label" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="title-modal-producto-label">Seleccionar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body table-responsive">
                    <table id="tabla-productos" class="table table-bordered w-100">
                      <thead>
                        <th>Op.</th>
                        <th>Code</th>
                        <th>Nombre Producto</th>
                        <th>P/U.</th>
                        <th>Descripción</th>
                      </thead>
                      <tbody></tbody>
                    </table>

                  </div>                  
                </div>
              </div>
            </div>
            <!-- End::Modal -->

            <!-- MODAL - DETALLE venta -->
            <div class="modal fade modal-effect" id="modal-detalle-venta" tabindex="-1" aria-labelledby="modal-detalle-ventaLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-detalle-ventaLabel1">Detalle - venta</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">

                    <ul class="nav nav-tabs" id="custom-tab" role="tablist">
                      <!-- DATOS VENTA -->
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="rol-venta" data-bs-toggle="tab" data-bs-target="#rol-venta-pane" type="button" role="tab" aria-selected="true">venta</button>
                      </li>
                      <!-- DATOS TOURS -->
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rol-detalle" data-bs-toggle="tab" data-bs-target="#rol-detalle-pane" type="button" role="tab" aria-selected="true">PRODUCTOS</button>
                      </li>

                    </ul>
                    <div class="tab-content" id="custom-tabContent">
                      <!-- /.tab-panel -->
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger py-1" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal -->            

            <!-- MODAL - VER CUOTAS -->
            <div class="modal fade modal-effect" id="modal-ver-cuotas" tabindex="-1" aria-labelledby="modal-ver-cuotas" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title title-ver-cuotas" id="modal-ver-cuotasLabel1">Doc:</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row" id="html-detalle-cuotas-ventas">

                    </div>

                  </div>
                  <div class="modal-footer p-1">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="las la-times fs-lg"></i> Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal - Ver cuotas -->

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

          </div>
        </div>
        <!-- End::app-content -->
      <?php } else {
        $title_submodulo = 'Facturación';
        $descripcion = 'Lista de ventas del sistema!';
        $title_modulo = 'Facturación';
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
   

    <!-- HTML Imagen -->
    <!-- <script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script> -->

    <script src="scripts/orden_venta_listar.js?version_jdl=1.16"></script>
    <script src="scripts/js_orden_venta_listar.js?version_jdl=1.16"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip({ html: true });
        console.log('Pagina termino de cargar');
      });
    </script>




  </body>



  </html>
<?php
}
ob_end_flush();
?>