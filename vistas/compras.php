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
    <?php $title_page = "Compras";
    include("template/head.php"); ?>
    <style>
      #tabla-compras_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-compras_filter label { width: 100% !important;  }
      #tabla-compras_filter label input { width: 100% !important; }

      #tabla-productos_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-productos_filter label { width: 100% !important;  }
      #tabla-productos_filter label input { width: 100% !important; }

      #tabla-detalle-compra-x-producto_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-detalle-compra-x-producto_filter label { width: 100% !important;  }
      #tabla-detalle-compra-x-producto_filter label input { width: 100% !important; }
    </style>
  </head>

  <body id="body-compras">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['lista_de_compras']==1) { ?>
      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_compra(); "  > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Compras</p>
                  <span class="fs-semibold text-muted">Administra las Compras.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Lista de Compras</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Compras</li>
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
                    <!-- ------------ Tabla de Compras ------------- -->
                    <div class="table-responsive" id="div-tabla">
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-compras">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Tipo y Serie Comprob</th>
                            <th>Total</th> 
                            <th>Descripción</th>
                            <th>CFDI</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                          <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Tipo y Serie Comprob</th>
                            <th>Total</th>
                            <th>Descripción</th>
                            <th>CFDI</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                    <!-- FORM - COMPROBANTE -->
                    <div class="div-formulario"  style="display: none;">
                      <form name="form-agregar-compra" id="form-agregar-compra" method="POST" class="needs-validation" novalidate>
                        <div class="row gy-2" id="cargando-1-formulario">
                          <input type="hidden" name="idcompra" id="idcompra" />
                          <input type="hidden" name="idsucursal" id="idsucursal" value='1' />

                          <!-- ----------------- PROVEEDOR --------------- -->
                          <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-6">
                            <div class="form-group">
                              <label for="idproveedor" class="form-label">
                                <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_trabajador(); limpiar_proveedor();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idproveedor();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Proveedor
                                <span class="charge_idproveedor"></span>
                              </label>
                              <select class="form-control" name="idproveedor" id="idproveedor"></select>
                            </div>
                          </div>

                          <!-- ----------------- TIPO COMPROBANTE --------------- -->
                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                            <div class="form-group">
                              <label for="tipo_comprobante" class="form-label">Tipo Comprobante</label>
                              <select class="form-control" name="tipo_comprobante" id="tipo_comprobante" onchange="default_val_igv(); modificarSubtotales();"></select>
                            </div>
                          </div>

                          <!-- ----------------- SERIE --------------- -->
                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                            <div class="form-group">
                              <label for="serie" class="form-label">Numero-Serie</label>
                              <input class="form-control" name="serie" id="serie" onkeyup="mayus(this);" />
                            </div>
                          </div>

                          <!-- -------------- DESCRIPCION ------------- -->
                          <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-6">
                            <div class="form-group">
                              <label for="descripcion" class="form-label">Descripcion</label>
                              <textarea name="descripcion" id="descripcion" class="form-control" rows="1" placeholder="ejemp: Compra de Router, cable UTP."></textarea>
                            </div>
                          </div>

                          <!-- ----------- FECHA EMISION ------- -->
                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                            <div class="form-group">
                              <label for="fecha_compra" class="form-label">Fecha</label>
                              <input type="date" class="form-control" name="fecha_compra" id="fecha_compra"  max="<?php echo date('Y-m-d'); ?>">
                            </div>
                          </div>

                          <!-- ----------- FECHA EMISION ------- -->
                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                            <div class="form-group">
                              <label for="impuesto" class="form-label">Impuesto (%)</label>
                              <input type="number" class="form-control" name="impuesto" id="impuesto" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">
                            </div>
                          </div>

                          <div class="col-md-12 col-lg-12 col-xl-12 mt-3">

                          </div>
                          <!-- ------------ BOTON SELECCIONAR PRODUCTOS ----------- -->                        

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <button class="btn btn-info label-btn m-r-10px" type="button" data-bs-toggle="modal" data-bs-target="#modal-producto"  >
                              <i class="ri-add-circle-line label-btn-icon me-2"></i> 
                              Agregar Productos 
                            </button>
                          </div>

                          <div class="col-md-6 col-lg-8 col-xl-9 col-xxl-10">

                            <div class="position-relative">
                              <div class="input-group">
                                <button type="button" class="input-group-text buscar_x_code" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Buscar por codigo de producto."><i class='bx bx-search-alt'></i></button>
                                <input type="text" name="search_producto" id="search_producto" class="form-control" onkeyup="mayus(this);" placeholder="Busca por código, nombre o escanea el producto.">                                
                              </div>
                              <ul id="searchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                            </div>

                          </div>

                          <!-- <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <button class="btn btn-primary label-btn m-r-10px" type="button" data-bs-toggle="modal" data-bs-target="#modal-agregar-producto" onclick="limpiar_form_producto();"  >
                            <i class="ri-add-fill label-btn-icon me-2"></i> 
                              Crear Producto 
                            </button>
                          </div> -->

                          

                          <!-- ------- TABLA PRODUCTOS SELECCIONADOS ------ --> 
                          <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive pt-3">
                            <table id="tabla-productos-seleccionados" class="table table-striped table-bordered table-condensed table-hover">
                              <thead class="bg-color-dark text-white">
                                <th class="py-1" data-toggle="tooltip" data-original-title="Opciones">Op.</th>
                                <th class="py-1">Cod</th>
                                <th class="py-1">Producto</th>
                                <th class="py-1 text-nowrap">Unidad Medida </th> 
                                <th class="py-1 text-nowrap">Cant. Compra</th>     
                                <th class="py-1 text-nowrap">Cant. Presentación</th>   
                                <th class="py-1" data-toggle="tooltip" data-original-title="Precio Unitario">Precio</th>
                                <th class="py-1">Descuento</th>
                                <th class="py-1">Subtotal</th>
                                <th class="py-1 text-center" ><i class='bx bx-cog fs-4'></i></th>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <td colspan="7"></td>

                                <th class="text-right">
                                  <div class="fs-12 tipo_gravada">SUBTOTAL</div>
                                  <div class="fs-12" >DESCUENTO</div>
                                  <div class="fs-12 val_igv">IGV (18%)</div>
                                  <div class="fs-15 font-weight-bold">TOTAL</div>
                                </th>
                                <th class="text-right"> 
                                  <div class="ffs-12 ont-weight-bold d-flex justify-content-between subtotal_compra"> <span>S/</span>  0.00</div>
                                  <input type="hidden" name="subtotal_compra" id="subtotal_compra" />
                                  <input type="hidden" name="tipo_gravada" id="tipo_gravada" />

                                  <div class="fs-12 font-weight-bold d-flex justify-content-between descuento_compra"><span>S/</span> 0.00</div>
                                  <input type="hidden" name="descuento_compra" id="descuento_compra" />

                                  <div class="fs-12 font-weight-bold d-flex justify-content-between igv_compra"><span>S/</span> 0.00</div>
                                  <input type="hidden" name="igv_compra" id="igv_compra" />
                                  
                                  <div class="fs-15 font-weight-bold d-flex justify-content-between total_compra"><span>S/</span> 0.00</div>
                                  <input type="hidden" name="total_compra" id="total_compra" />
                                  
                                </th>
                                <th></th>
                              </tfoot>
                            </table>
                          </div>

                          <!-- Imgen -->
                          <div class="col-md-4 col-lg-4 mt-5">
                            <h6 class="card-title text-center">Comprobante</h6>
                            <div class="col-md-12 border-top p-2">

                              <div class="my-2 text-center">
                                <div class="btn-group edit_img">
                                  <button type="button" class="btn btn-primary py-1" id="doc1_i"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</button>
                                  <input type="hidden" id="doc_old_1" name="doc_old_1" />
                                  <input style="display: none;" id="doc1" type="file" name="doc1" accept="application/pdf, image/*" class="docpdf" />
                                  <button type="button" class="btn btn-info py-1" onclick="re_visualizacion(1, 'assets/modulo/gasto_de_trabajador', '100%', '300px'); reload_zoom();"><i class='bx bx-refresh bx-spin fs-5'></i>Refrescar</button>
                                </div>
                              </div>

                              <!-- imagen -->
                              <div id="doc1_ver" class="text-center ">
                                <img id="img_defect" src="../assets/images/default/img_defecto2.png" alt="" width="70%" />
                              </div>
                              <div  id="doc1_nombre" ><!-- aqui va el nombre del pdf --></div>
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

                        <!-- Chargue -->
                        <div class="p-l-25px col-lg-12" id="barra_progress_compra_div" style="display: none;" >
                          <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                            <div id="barra_progress_compra" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                          </div>
                        </div>
                        <!-- Submit -->
                        <button type="submit" style="display: none;" id="submit-form-compra">Submit</button>
                      </form>
                    </div>                   

                  </div>
                  <div class="card-footer border-top-0">
                    <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1); limpiar_form_compra();" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                    <button type="button" class="btn btn-success btn-guardar" id="guardar_registro_compra" style="display: none;"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End::row-1 -->

          <!-- MODAL - VER COMPROBANTE COMPRA -->
          <div class="modal fade modal-effect" id="modal-ver-comprobante1" tabindex="-1" aria-labelledby="modal-ver-comprobante1Label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-modal-comprobante1" id="modal-ver-comprobante1Label1">COMPROBANTE</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="comprobante-container1" class="text-center"> <!-- archivo --> 
                    <div class="row" >
                      <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div>
                    </div>
                  </div>
                </div>                
              </div>
            </div>
          </div> 
          <!-- End::Modal-Ver-Comprobante compra -->

          <!-- MODAL - VER FOTO PROVEEDOR -->
          <div class="modal fade modal-effect" id="modal-ver-foto-proveedor" tabindex="-1" aria-labelledby="modal-ver-foto-proveedor" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-foto-proveedor" id="modal-ver-foto-proveedorLabel1">Imagen</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body html_ver_foto_proveedor">
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal - Ver foto proveedor -->

          <!-- MODAL - SELECIONAR PRODUCTO -->
          <div class="modal fade modal-effect" id="modal-producto" tabindex="-1" aria-labelledby="modal-productoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modal-productoLabel1">Seleccionar Producto</h5>
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
          <!-- End::Modal-Producto -->

          <!-- MODAL - DETALLE COMPRA -->
          <div class="modal fade modal-effect" id="modal-detalle-compra" tabindex="-1" aria-labelledby="modal-detalle-compraLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-detalle-compraLabel1">Detalle - Compra</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                      <ul class="nav nav-tabs" id="custom-tab" role="tablist">
                        <!-- DATOS VENTA -->
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="rol-compra" data-bs-toggle="tab" data-bs-target="#rol-compra-pane" type="button" role="tab" aria-selected="true">COMPRA</button>
                        </li>
                        <!-- DATOS TOURS -->
                        <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rol-detalle" data-bs-toggle="tab" data-bs-target="#rol-detalle-pane" type="button" role="tab" aria-selected="true">PRODUCTOS</button>
                        </li>
                        
                      </ul>
                      <div class="tab-content" id="custom-tabContent">                                
                        <!-- /.tab-panel --> 
                      </div> 

                    <div class="row" id="cargando-4-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <i class="fas fa-spinner fa-pulse fa-6x"></i><br />
                        <br />
                        <h4>Cargando...</h4>
                      </div>
                    </div>
                    
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger py-1" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Detalle-Compra -->

          <!-- MODAL - AGREGAR PROVEEDOR - charge 3,4 -->
          <div class="modal fade modal-effect" id="modal-agregar-proveedor" tabindex="-1" aria-labelledby="Modal-agregar-proveedorLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-modal-img" id="Modal-agregar-proveedorLabel1">Agregar Proveedor</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                                      
                    <form name="form-agregar-proveedor" id="form-agregar-proveedor" method="POST" class="needs-validation" novalidate>
                            
                      <div class="row" id="cargando-3-fomulario">
                        <!-- idpersona -->
                        <input type="hidden" name="idpersona" id="idpersona" />   
                        <input type="hidden" name="tipo_persona_sunat" id="tipo_persona_sunat" value="NATURAL" />   
                        <input type="hidden" name="idtipo_persona" id="idtipo_persona" value="4" />   
                        <input type="hidden" name="centro_poblado" id="centro_poblado" value="1" />

                        <div class="col-lg-12 col-xl-12 col-xxl-6">
                          <div class="row">
                            <!-- Grupo -->
                            <div class="col-12 pl-0">
                              <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >DATOS GENERALES</b></label></div>
                            </div>
                          </div> <!-- /.row -->
                          <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                            <div class="row">

                              <!-- Tipo documento -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="tipo_documento" class="form-label">Tipo documento:  </label>
                                  <select name="tipo_documento" id="tipo_documento" class="form-select" required>           
                                    <option value="0">NINGUNO</option>
                                    <option value="1">DNI</option>
                                    <option value="6">RUC</option>                           
                                  </select>
                                </div>                                         
                              </div>
                              
                              <!--  Numero Documento -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="numero_documento" class="form-label">Numero Documento:</label>
                                  <div class="input-group">                            
                                    <input type="number" class="form-control" name="numero_documento" id="numero_documento" placeholder="" aria-describedby="icon-view-password">
                                    <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-proveedor', '_t', '#tipo_documento', '#numero_documento', '#nombre_razonsocial', '#apellidos_nombrecomercial', '#direccion', '#distrito', '#titular_cuenta' );" >
                                      <i class='bx bx-search-alt' id="search_t"></i>
                                      <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                    </button>
                                  </div>
                                </div>                        
                              </div>         
                            
                              <!-- Nombres -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="nombre_razonsocial" class="form-label label-nom-raz">Nombres:  </label></label>
                                  <input type="text" class="form-control" name="nombre_razonsocial" id="nombre_razonsocial" >
                                </div>                                         
                              </div>

                              <!-- Apellidos -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6 ">
                                <div class="form-group">
                                  <label for="apellidos_nombrecomercial" class="form-label label-ape-come">Apellidos:  </label></label>
                                  <input type="text" class="form-control" name="apellidos_nombrecomercial" id="apellidos_nombrecomercial" >
                                </div>                                         
                              </div>

                              <!-- Correo -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="correo" class="form-label">Correo:</label>
                                  <input type="email" class="form-control" name="correo" id="correo">
                                </div>                                         
                              </div>

                              <!-- Celular -->
                              <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="celular" class="form-label">Celular:</label>
                                  <input type="tel" class="form-control" name="celular" id="celular" >
                                </div>                                         
                              </div>                                   

                            </div> <!-- /.row -->
                          </div> <!-- /.card-body -->
                        </div> <!-- /.col-lg-12 -->

                        <div class="col-lg-12 col-xl-12 col-xxl-6">
                          <div class="row">
                            <!-- Grupo -->
                            <div class="col-12 pl-0">
                              <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >UBICACIÓN</b></label></div>
                            </div>
                          </div> <!-- /.row -->
                          <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                            <div class="row">

                              <!-- Direccion -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                <div class="form-group">
                                  <label for="direccion" class="form-label">Direccion:</label>
                                  <input type="text" class="form-control" name="direccion" id="direccion">
                                </div>                                         
                              </div>
                              <!-- Distrito -->
                              <div class="mb-1 col-md-3 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                <div class="form-group">
                                  <label for="distrito" class="form-label">Distrito: </label>
                                  <select name="distrito" id="distrito" class="form-select"  >
                                    
                                  </select>
                                </div>                                         
                              </div>
                              <!-- Departamento -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                  <label for="departamento" class="form-label">Departamento: <span class="chargue-pro"></span></label>
                                  <input type="text" class="form-control" name="departamento" id="departamento">
                                </div>                                         
                              </div>
                              <!-- Provincia -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                  <label for="provincia" class="form-label">Provincia: <span class="chargue-dep"></span></label>
                                  <input type="text" class="form-control" name="provincia" id="provincia">
                                </div>                                         
                              </div>
                              <!-- Ubigeo -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                  <label for="ubigeo" class="form-label">Ubigeo: <span class="chargue-ubi"></span></label>
                                  <input type="text" class="form-control" name="ubigeo" id="ubigeo">
                                </div>                                         
                              </div>
                            </div> <!-- /.row -->
                          </div> <!-- /.card-body -->
                        </div> <!-- /.col-lg-12 -->

                        <div class="mt-3 col-lg-12 col-xl-12 col-xxl-12">
                          <div class="row">
                            <!-- Grupo -->
                            <div class="col-12 pl-0">
                              <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >BANCO</b></label></div>
                            </div>
                          </div> <!-- /.row -->
                          <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                            <div class="row">

                              <!-- Banco -->
                              <div class="mb-1 col-md-12 col-lg-6 col-xl-4 col-xxl-4 mt-3">
                                <div class="form-group">
                                  <label for="idbanco" class="form-label">Entidad Financiera:  </label>
                                  <select name="idbanco" id="idbanco" class="form-select" required>                                       
                                  </select>
                                </div>                                         
                              </div>

                              <!-- Cuenta Bancaria -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-4 col-xxl-4 mt-3">
                                <div class="form-group">
                                  <label for="cuenta_bancaria" class="form-label">Cuenta Bancaria:</label>
                                  <input type="text" class="form-control" name="cuenta_bancaria" id="cuenta_bancaria" >
                                </div>                                         
                              </div>

                              <!-- CCI -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-4 col-xxl-4 mt-3">
                                <div class="form-group">
                                  <label for="cci" class="form-label">CCI:</label>
                                  <input type="text" class="form-control" name="cci" id="cci" >
                                </div>                                         
                              </div>

                            </div> <!-- /.row -->
                          </div> <!-- /.card-body -->
                        </div> <!-- /.col-lg-12 -->

                        <!-- Imgen -->
                        <div class="col-md-4 col-lg-4 mt-4">
                          <span class="" > <b>Logo Proveedor</b> </span>
                          <div class="mb-4 mt-2 d-sm-flex align-items-center">
                            <div class="mb-0 me-5">
                              <span class="avatar avatar-xxl avatar-rounded">
                                <img src="../assets/images/default/default_proveedor.png" alt="" id="imagenmuestra" onerror="this.src='../assets/images/default/default_proveedor.png';">
                                <a href="javascript:void(0);" class="badge rounded-pill bg-primary avatar-badge cursor-pointer">
                                  <input type="file" class="position-absolute w-100 h-100 op-0" name="imagen" id="imagen" accept="image/*">
                                  <input type="hidden" name="imagenactual" id="imagenactual">
                                  <i class="fe fe-camera  "></i>
                                </a>
                              </span>
                            </div>
                            <div class="btn-group">
                              <a class="btn btn-primary" onclick="cambiarImagenProveedor()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                              <a class="btn btn-light" onclick="removerImagenProveedor()"><i class="bi bi-trash fs-6"></i> Remover</a>
                            </div>
                          </div>
                        </div> 

                      </div> <!-- /.row -->

                      <div class="row" id="cargando-4-fomulario" style="display: none;" >
                        <div class="col-lg-12 text-center">                         
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>  <!-- /.row -->                                   
                      
                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_proveedor_div" style="display: none;" >
                        <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                          <div id="barra_progress_proveedor" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                        </div>
                      </div>
                      <!-- Submit -->
                      <button type="submit" style="display: none;" id="submit-form-proveedor">Submit</button>
                    </form>
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger"  data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                  <button type="button" class="btn btn-sm btn-success label-btn" id="guardar_registro_proveedor"><i class="bx bx-save bx-tada"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Agregar-Proveedor -->
          
          <!-- MODAL - AGREGAR PRODUCTO - charge p1 -->
          <div class="modal fade modal-effect" id="modal-agregar-producto" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-productoLabel">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-productoLabel1">Registrar Producto</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="form-agregar-producto" id="form-agregar-producto" method="POST" class="row needs-validation" novalidate >
                    <div class="row gy-2" id="cargando-P1-formulario">
                      <!-- ----------------------- ID ------------- -->
                      <input type="hidden" id="idproducto" name="idproducto">
                      <input type="hidden" name="tipo" id="tipo" value="PR">

                      <!-- ----------------- Categoria --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="categoria" class="form-label">Categoría</label>
                          <select class="form-control" name="categoria" id="categoria">
                            <!-- lista de categorias -->
                          </select>
                        </div>
                      </div>

                      <!-- ----------------- Unidad Medida --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="u_medida" class="form-label">U. Medida</label>
                          <select class="form-control" name="u_medida" id="u_medida">
                            <!-- lista de u medidas -->
                          </select>
                        </div>
                      </div>

                      <!-- ----------------- Marca --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="marca" class="form-label">Marca</label>
                          <select class="form-control" name="marca" id="marca">
                            <!-- lista de marcas -->
                          </select>
                        </div>
                      </div>
                      <!-- ----------------- Ubicacion --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="ubicacion" class="form-label">Ubicación</label>
                          <select class="form-control" name="ubicacion" id="ubicacion">
                            <!-- lista de ubicacion -->
                          </select>
                        </div>
                      </div>
                      <!-- --------- NOMBRE ------ -->
                      <div class="col-md-4 col-lg-4 col-xl-6 col-xxl-6 mt-3">
                        <div class="form-group">
                          <label for="nombre" class="form-label">Nombre(*)</label>
                          <textarea class="form-control" name="nombre" id="nombre" rows="1"></textarea>
                        </div>
                      </div>

                      <!-- --------- DESCRIPCION ------ -->
                      <div class="col-md-4 col-lg-4 col-xl-6 col-xxl-6 mt-3">
                        <div class="form-group">
                          <label for="descripcion" class="form-label">Descrición(*)</label>
                          <textarea class="form-control" name="descripcion" id="descripcion" rows="1"></textarea>
                        </div>
                      </div>

                      <!-- ----------------- STOCK --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="stock" class="form-label">Stock(*)</label>
                          <input type="number" class="form-control" name="stock" id="stock" />
                        </div>
                      </div>

                      <!-- ----------------- STOCK MININO --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="stock_min" class="form-label">Stock Minimo(*)</label>
                          <input type="number" class="form-control" name="stock_min" id="stock_min" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO VENTA --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="precio_v" class="form-label">Precio Venta(*)</label>
                          <input type="number" class="form-control" name="precio_v" id="precio_v" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO COMPRA --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="precio_c" class="form-label">Precio Compra(*)</label>
                          <input type="number" class="form-control" name="precio_c" id="precio_c" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO X MAYOR --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                        <div class="form-group">
                          <label for="precio_x_mayor" class="form-label">Precio por Mayor</label>
                          <input type="text" class="form-control" name="precio_x_mayor" id="precio_x_mayor" placeholder="precioB" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO DISTRIBUIDOR --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                        <div class="form-group">
                          <label for="precio_dist" class="form-label">Precio Distribuidor</label>
                          <input type="text" class="form-control" name="precio_dist" id="precio_dist" placeholder="precioC"/>
                        </div>
                      </div>

                      <!-- ----------------- PRECIO ESPECIAL --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                        <div class="form-group">
                          <label for="precio_esp" class="form-label">Precio Especial</label>
                          <input type="text" class="form-control" name="precio_esp" id="precio_esp" placeholder="precioD"/>
                        </div>
                      </div>

                      <!-- Imgen -->
                      <div class="col-md-6 col-lg-6 mt-4">
                        <span class="" > <b>Imagen Prducto</b> </span>
                        <div class="mb-4 mt-2 d-sm-flex align-items-center">
                          <div class="mb-0 me-5">
                            <span class="avatar avatar-xxl avatar-rounded">
                              <img src="../assets/modulo/productos/no-producto.png" alt="" id="imagenmuestraProducto" onerror="this.src='../assets/modulo/productos/no-producto.png';">
                              <a href="javascript:void(0);" class="badge rounded-pill bg-primary avatar-badge cursor-pointer">
                                <input type="file" class="position-absolute w-100 h-100 op-0" name="imagenProducto" id="imagenProducto" accept="image/*">
                                <input type="hidden" name="imagenactualProducto" id="imagenactualProducto">
                                <i class="fe fe-camera  "></i>
                              </a>
                            </span>
                          </div>
                          <div class="btn-group">
                            <a class="btn btn-primary" onclick="cambiarImagenProducto()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                            <a class="btn btn-light" onclick="removerImagenProducto()"><i class="bi bi-trash fs-6"></i> Remover</a>
                          </div>
                        </div>
                      </div> 

                    </div>
                    <div class="row" id="cargando-P2-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-producto">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_producto();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="guardar_registro_producto"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-Agregar-Producto -->

          <!-- MODAL - VER DETALLE COMPRA X PRODUCTO -->
          <div class="modal fade modal-effect" id="modal-detalle-compra-x-producto" tabindex="-1" aria-labelledby="modal-detalle-compra-x-productoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modal-detalle-compra-x-productoLabel1">Compra Anteriores por Producto</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive">
                  <table id="tabla-detalle-compra-x-producto" class="table table-bordered w-100">
                    <thead>
                      <th>#</th>
                      <th>N° Comprobante</th>
                      <th>Fecha</th>                              
                      <th>Compra</th>
                      <th>Presentación </th>
                      <th>Stock </th>
                    </thead>
                    <tbody></tbody>
                  </table>
                  
                </div>
                
              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='Compra'; $descripcion ='Lista de Compras del sistema!'; $title_modulo = 'Compras'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>   

    <script src="scripts/js_compras.js?version_jdl=1.16"></script>
    <script src="scripts/compras.js?version_jdl=1.16"></script>
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