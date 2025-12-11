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
    <?php $title_page = "Productos";
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
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_producto(); create_code_producto('PR');"  > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <button class="btn-modal-effect btn btn-secondary label-btn btn-agregar m-r-10px  hidden" onclick="show_hide_form(3);  limpiar_form_producto(); create_code_producto('PR');"  > <i class="ri-archive-line label-btn-icon me-2"></i>Agrupar </button>
                <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Productos</p>
                  <span class="fs-semibold text-muted">Administra los productos.</span>
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
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Articulos</li>
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
                  <div class="card-header">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                      <div class="activar-scroll-x-auto scroll-xxl">
                        <!-- ::::::::::::::::::::: FILTRO CATEGORIA :::::::::::::::::::::: -->
                        <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                          <div class="form-group">
                            <label for="filtro_categoria" class="form-label">                         
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_categoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                              Categoría
                              <span class="charge_filtro_categoria"></span>
                            </label>
                            <select class="form-control" name="filtro_categoria" id="filtro_categoria" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                          </div>
                        </div>
                        <!-- ::::::::::::::::::::: FILTRO UNIDAD DE MEDIDA :::::::::::::::::::::: -->
                        <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                          <div class="form-group">
                            <label for="filtro_unidad_medida" class="form-label">                         
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_unidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                              Unidad Medida
                              <span class="charge_filtro_unidad_medida"></span>
                            </label>
                            <select class="form-control" name="filtro_unidad_medida" id="filtro_unidad_medida" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                          </div>
                        </div>
                        <!-- ::::::::::::::::::::: FILTRO MARCA :::::::::::::::::::::: -->
                        <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                          <div class="form-group">
                            <label for="filtro_marca" class="form-label">                         
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_marca();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                              Marca
                              <span class="charge_filtro_marca"></span>
                            </label>
                            <select class="form-control" name="filtro_marca" id="filtro_marca" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                          </div>
                        </div>
                        <!-- ::::::::::::::::::::: FILTRO UBICACION :::::::::::::::::::::: -->
                        <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                          <div class="form-group">
                            <label for="filtro_ubicacion" class="form-label">                         
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_ubicacion();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                              Ubicación
                              <span class="charge_filtro_ubicacion"></span>
                            </label>
                            <select class="form-control" name="filtro_ubicacion" id="filtro_ubicacion" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                          </div>
                        </div> 
                      </div> 
                    </div>
                  </div>
                  <div class="card-body">
                    <!-- ------------ Tabla de Productos ------------- -->
                    <div class="table-responsive" id="div-tabla">
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-productos">
                        <thead>
                          <tr > 
                            <th colspan="18" class="bg-danger buscando_tabla" style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                          </tr>
                          <tr >
                            <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">#</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">Acciones</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Presentación</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Stock</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Compra</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Venta</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Descripción</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Estado</th>
                            
                            <th style="border-top: 1px solid #f3f3f3 !important;">Categoria</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Marca</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">Código Alterno</th>
                            <th style="border-top: 1px solid #f3f3f3 !important;">ID</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Presentación</th>
                            <th>Stock</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            
                            <th>Categoria</th>
                            <th>Marca</th>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Código Alterno</th>
                            <th >ID</th>
                          </tr>
                        </tfoot>

                      </table>

                    </div>
                    <!-- ------------ Formulario de Productos ------------ -->
                    <div class="div-form" style="display: none;">
                      <form name="form-agregar-producto" id="form-agregar-producto" method="POST" class="needs-validation" novalidate>
                        <div class="row gy-2" id="cargando-1-formulario">
                          <!-- ID -->
                          <input type="hidden" name="idproducto" id="idproducto"/>
                          <input type="hidden" name="idproducto_n" id="idproducto_n"/>
                          <input type="hidden" name="tipo" id="tipo" value="PR"/>
                          <input type="hidden" name="idsucursal" id="idsucursal" value="1"/> 
                          <input type="hidden" name="idproducto_sucursal" id="idproducto_sucursal"/> 
                          <input type="hidden" name="idproducto_presentacion" id="idproducto_presentacion"/>

                          <div class="col-lg-12 col-xl-6 col-xxl-6">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >DATOS GENERALES</b></label></div>
                              </div>
                            </div> <!-- /.row -->
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">
                                 
                                  <!-- ----------------- CODIGO ----------------- -->
                                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                    <div class="form-group">
                                      <label for="codigo" class="form-label">Código Sistema <span class="charge_codigo"></span></label>
                                      <input type="text" class="form-control bg-light" name="codigo" id="codigo" onkeyup="mayus(this);"  readonly data-bs-toggle="tooltip" data-bs-original-title="No se puede editar" />
                                    </div>
                                  </div>

                                  <!-- ------------- CODIGO ALTERNO ------------- -->
                                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                    <div class="form-group">
                                      <label for="codigo_alterno" class="form-label">
                                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="generarcodigonarti();" data-bs-toggle="tooltip" title="Generar Codigo con el nombre de producto."><i class="las la-sync-alt"></i></span>
                                        Código Propio <span class="charge_codigo_alterno"></span>
                                      </label>
                                      <input type="text" class="form-control " name="codigo_alterno" id="codigo_alterno" onkeyup="mayus(this);" placeholder="ejemp: PR00001" />
                                    </div>
                                  </div>

                                  

                                  <!-- ---------------- NOMBRE ------------------ -->
                                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                    <div class="form-group">
                                      <label for="nombre" class="form-label">Nombre(*)</label>
                                      <textarea class="form-control" name="nombre" id="nombre" rows="1"></textarea>
                                    </div>
                                  </div>

                                  <!-- -------------- DESCRIPCION --------------- -->
                                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                    <div class="form-group">
                                      <label for="descripcion" class="form-label">Descripción(*)</label>
                                      <textarea class="form-control" name="descripcion" id="descripcion" rows="1"></textarea>
                                    </div>
                                  </div>

                                  <!-- ---------------- STOCK ------------------- -->
                                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                    <div class="form-group">
                                      <label for="stock" class="form-label">Stock(*)</label>
                                      <input type="number" class="form-control" name="stock" id="stock" />
                                    </div>
                                  </div>

                                  <!-- ------------- STOCK MININO --------------- -->
                                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                    <div class="form-group">
                                      <label for="stock_min" class="form-label">Stock Minimo(*)</label>
                                      <input type="number" class="form-control" name="stock_min" id="stock_min" />
                                    </div>
                                  </div>

                              </div> <!-- /.row -->
                            </div> <!-- /.card-body -->
                          </div> <!-- /.col-lg-12 -->

                          <div class="col-lg-12 col-xl-6 col-xxl-6">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >DETALLE PRODUCTO</b></label></div>
                              </div>
                            </div> <!-- /.row -->
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">

                                <!-- ------------- UNIDAD MEDIDA -------------- -->
                                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="u_medida" class="form-label">
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idunidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      U. Medida
                                      <span class="charge_idunidad_medida"></span>
                                    </label>
                                    <select class="form-control" name="u_medida" id="u_medida">
                                      <!-- lista de u medidas -->
                                    </select>
                                  </div>
                                </div>

                                <!-- ------------- CANTIDAD X U. MEDIDA -------------- -->
                                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="cant_um" class="form-label">Cantidad x U. Medida(*)</label>
                                    <input type="number" class="form-control" name="cant_um" id="cant_um" step="1" value="1"/>
                                  </div>
                                </div>

                                <!-- -------------- CATEGORIA ----------------- -->
                                <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                  <div class="form-group">
                                    <label for="categoria" class="form-label">
                                      <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_categoria(); limpiar_form_cat();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idcategoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Categoría
                                      <span class="charge_idcategoria"></span>
                                    </label>
                                    <select class="form-control" name="categoria" id="categoria">
                                      <!-- lista de categorias -->
                                    </select>
                                  </div>
                                </div>  

                                <!-- ---------------- MARCA ------------------- -->
                                <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                  <div class="form-group">
                                    <label for="marca" class="form-label">
                                      <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_marca(); limpiar_form_marca();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idmarca();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Marca
                                      <span class="charge_idmarca"></span>
                                    </label>
                                    <select class="form-control" name="marca" id="marca">
                                      <!-- lista de marcas -->
                                    </select>
                                  </div>
                                </div>

                                <!-- -------------- UBICACION ----------------- -->
                                <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                  <div class="form-group">
                                    <label for="ubicacion" class="form-label">
                                      <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_ubicacion(); limpiar_form_ubicacion();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idubicacion();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Ubicación
                                      <span class="charge_idubicacion"></span>
                                    </label>
                                    <select class="form-control" name="ubicacion" id="ubicacion">
                                      <!-- lista de prod-cat-ubicacion -->
                                    </select>
                                  </div>
                                </div> 

                                <!-- ------------- PRECIO COMPRA -------------- -->
                                <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                  <div class="form-group">
                                    <label for="precio_c" class="form-label">Precio Compra(*)</label>
                                    <input type="number" class="form-control" name="precio_c" id="precio_c" step="0.01" />
                                  </div>
                                </div>

                                <!-- ------------- PRECIO VENTA --------------- -->
                                <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                  <div class="form-group">
                                    <label for="precio_v" class="form-label">Precio Venta(*)</label>
                                    <input type="number" class="form-control" name="precio_v" id="precio_v" step="0.01" />
                                  </div>
                                </div> 

                                <!-- ------------ PRECIO X MAYOR -------------- -->
                                <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3 hidden">
                                  <div class="form-group">
                                    <label for="precio_x_mayor" class="form-label">Precio por Mayor</label>
                                    <input type="number" class="form-control" name="precio_x_mayor" id="precio_x_mayor" step="0.01" />
                                  </div>
                                </div>

                              </div> <!-- /.row -->
                            </div> <!-- /.card-body -->
                          </div> <!-- /.col-lg-12 -->

                          <!-- --------------------- IMAGEN ------------------- -->
                          <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-4">
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
                                <a class="btn btn-primary" onclick="cambiarImagen()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                                <a class="btn btn-light" onclick="removerImagen()"><i class="bi bi-trash fs-6"></i> Remover</a>
                              </div>
                            </div>
                          </div> 

                          <!-- ---------------- LISTA PRODUCTOS --------------- -->
                          <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-9 mt-3 hidden" id="list-productos" >
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;">
                                  <label class="bg-white" for=""><b class="mx-2" >
                                    LISTA PRODUCTOS</b>
                                    <span class="badge bg-primary m-r-4px cursor-pointer" onclick="listar_tabla_producto_g('PR');" data-bs-toggle="tooltip" title="Agregar Producto"><i class="las la-plus"></i></span>
                                  </label>
                                </div>
                              </div>
                            </div> <!-- /.row -->
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">

                                  <table id="tabla-productos-seleccionados" class="table table-striped table-bordered table-condensed table-hover">
                                    <thead class="bg-color-dark text-white">
                                      <th class="font-size-11px py-1" data-toggle="tooltip" data-original-title="Opciones">Op.</th>
                                      <th class="font-size-11px py-1">Cod</th>
                                      <th class="font-size-11px py-1">Producto</th>
                                      <th class="font-size-11px py-1">Presentación</th>
                                      <th class="font-size-11px py-1">Cantidad</th>
                                      <th class="font-size-11px py-1" data-toggle="tooltip" data-original-title="Precio Unitario">P/U</th>
                                      <th class="font-size-11px py-1">Subtotal</th>
                                      <th class="font-size-11px py-1 text-center"><i class='bx bx-cog fs-4'></i></th>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                      <td colspan="5"></td>

                                      <th class="text-right">
                                        <h5 class="fs-13 font-weight-bold">TOTAL</h5>
                                      </th>
                                      <th class="text-right">

                                        <h5 class="fs-13 font-weight-bold d-flex justify-content-between l_total_prod"><span>S/</span> 0.00</h5>
                                        <input type="hidden" name="l_total_prod" id="l_total_prod"/>

                                      </th>
                                      <th></th>
                                    </tfoot>
                                  </table>
                                </div>
                              </div> <!-- /.row -->
                            </div> <!-- /.card-body -->
                          </div> <!-- /.col-lg-12 -->

                        </div>
                        <div class="row" id="cargando-2-fomulario" style="display: none;" >
                          <div class="col-lg-12 text-center">                         
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div>
                        <!-- Chargue -->
                        <div class="p-l-25px col-lg-12" id="barra_progress_producto_div" style="display: none;" >
                          <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                            <div id="barra_progress_producto" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                          </div>
                        </div>
                        <!-- Submit -->
                        <button type="submit" style="display: none;" id="submit-form-producto">Submit</button>
                        
                      </form>
                    </div>
                  </div>
                  <div class="card-footer border-top-0">
                    <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1); limpiar_form_productO();" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                    <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                  </div> 
                </div>
              </div>
            </div>
          </div>
          <!-- End::row-1 -->


          <!-- MODAL - VER DETALLE -->
          <div class="modal fade modal-effect" id="modal-ver-detalle-producto" tabindex="-1" aria-labelledby="modal-ver-detalle-productoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" id="modal-ver-detalle-productoLabel1"><b>Detalles</b> - Producto</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >            
                  <div id="html-detalle-producto"></div>
                  <div class="text-center" id="html-detalle-imagen"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-VerDetalles -->


          <!-- MODAL - AGREGAR CATEGORIA -->
          <div class="modal fade modal-effect" id="modal-agregar-categoria" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-categoriaLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-categoriaLabel1">Registrar Categoría</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="formulario-categoria" id="formulario-categoria" method="POST" class="needs-validation" novalidate>
                    <div class="row gy-2" id="cargando-3-fomulario">
                      <input type="hidden" name="idcategoria" id="idcategoria">
                      
                      <div class="col-md-12">
                        <div class="form-label">
                          <label for="nombre_cat" class="form-label">Nombre(*)</label>
                          <input type="text" class="form-control" name="nombre_cat" id="nombre_cat" onkeyup="mayus(this);"/>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="descr_cat" class="form-label">Descripción</label>
                          <input type="text" class="form-control" name="descr_cat" id="descr_cat" onkeyup="mayus(this);"/>
                        </div>
                      </div>
                    </div>
                    <div class="row" id="cargando-4-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-categoria">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_cat();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="guardar_registro_categoria"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Agregar-Cartegoria -->


          <!-- MODAL - AGREGAR MARCA -->
          <div class="modal fade modal-effect" id="modal-agregar-marca" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-marcaLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-marcaLabel1">Registrar Marca</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="formulario-marca" id="formulario-marca" method="POST" class="needs-validation" novalidate>
                    <div class="row gy-2" id="cargando-5-fomulario">
                      <input type="hidden" name="idmarca" id="idmarca">
                      
                      <div class="col-md-12">
                        <div class="form-label">
                          <label for="nombre_marca" class="form-label">Nombre(*)</label>
                          <input type="text" class="form-control" name="nombre_marca" id="nombre_marca" onkeyup="mayus(this);"/>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="descr_marca" class="form-label">Descripción</label>
                          <input type="text" class="form-control" name="descr_marca" id="descr_marca" onkeyup="mayus(this);"/>
                        </div>
                      </div>
                    </div>
                    <div class="row" id="cargando-6-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-marca">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_marca();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="guardar_registro_marca"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Agregar-Marca -->


          <!-- MODAL - AGREGAR UM -->
          <div class="modal fade modal-effect" id="modal-agregar-u-m" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-u-mLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-u-mLabel1">Registrar Unidad de Medida</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="formulario-u-m" id="formulario-u-m" method="POST" class="row needs-validation" novalidate>
                    <div class="row gy-2" id="cargando-1-fomulario">
                      <input type="hidden" name="idsunat_c03_unidad_medida" id="idsunat_c03_unidad_medida">


                      <div class="col-md-6">
                        <div class="form-label">
                          <label for="nombre_um" class="form-label">Nombre(*)</label>
                          <input type="text" class="form-control" name="nombre_um" id="nombre_um" onkeyup="mayus(this);" />
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="descr_um" class="form-label">Descripción</label>
                          <input type="text" class="form-control" name="descr_um" id="descr_um" onkeyup="mayus(this);" />
                        </div>
                      </div>
                    </div>
                    <div class="row" id="cargando-2-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-u-m">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_um();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="guardar_registro_u_m"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-registrar-unidad-medida -->


          <!-- MODAL - AGREGAR UBICACION -->
          <div class="modal fade modal-effect" id="modal-agregar-ubicacion" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-ubicacionLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-ubicacionLabel1">Registrar Ubicación</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="formulario-ubicacion" id="formulario-ubicacion" method="POST" class="needs-validation" novalidate>
                    <div class="row gy-2" id="cargando-7-fomulario">
                      <input type="hidden" name="idproducto_categoria_ubicacion" id="idproducto_categoria_ubicacion">
                      
                      <div class="col-md-12">
                        <div class="form-label">
                          <label for="nombre_ubi" class="form-label">Nombre(*)</label>
                          <input type="text" class="form-control" name="nombre_ubi" id="nombre_ubi" onkeyup="mayus(this);"/>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="descr_ubi" class="form-label">Descripción</label>
                          <input type="text" class="form-control" name="descr_ubi" id="descr_ubi" onkeyup="mayus(this);"/>
                        </div>
                      </div>
                    </div>
                    <div class="row" id="cargando-8-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-ubicacion">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_cat();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="guardar_registro_ubicacion"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Agregar-Cartegoria -->


          <!-- MODAL - SELECIONAR PRODUCTO -->
          <div class="modal fade modal-effect" id="modal-list-producto" tabindex="-1" aria-labelledby="title-modal-list-producto-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="title-modal-list-producto-label">Seleccionar Producto</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive">
                  <table id="tabla-productos-g" class="table table-bordered w-100">
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
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal -->


          <!-- MODAL - IMPRIMIR CODIGO -->
          <div class="modal fade modal-effect" id="modal-form-codigo" role="dialog" tabindex="-1" aria-labelledby="modal-form-codigoLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-form-codigoLabel1">Imprimir código de barras</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="formulario-codg" id="formulario-codg" method="POST" class="needs-validation" novalidate>
                    <div class="row gy-2" id="cargando-12-fomulario">
                      <input type="hidden" name="idproducto" id="idproducto">
                      
                      <div class="col-md-6">
                        <div class="form-label">
                          <label for="codg" class="form-label">Codigo</label>
                          <input type="text" class="form-control" name="codg" id="codg" readonly/>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="cant_cg" class="form-label">Cantidad(*)</label>
                          <input type="number" class="form-control" name="cant_cg" id="cant_cg"/>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-label">
                          <label for="dim_x" class="form-label">Dimención X*</label>
                          <input type="number" class="form-control" name="dim_x" id="dim_x" step="0.1"/>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-label">
                          <label for="dim_y" class="form-label">Dimención Y*</label>
                          <input type="number" class="form-control" name="dim_y" id="dim_y" step="0.1"/>
                        </div>
                      </div>
                    </div>
                    <div class="row" id="cargando-13-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="button" style="display: none;" id="submit-form-imp">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_imprimir();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="enviar_impresion"><i class="bx bx-save bx-tada fs-lg"></i> Imprimir</button>
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Agregar-Cartegoria -->


          <!-- MODAL - VER CODIGO DE BARRA - PDF -->
          <div class="modal fade" id="modal-codigo-preview" tabindex="-1">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Vista previa</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <iframe id="iframe-codigo" src="" width="100%" height="500px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                  <button id="descargarPDF" class="btn btn-success">Descargar PDF</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-Ver-Codigo-Barra-PDF -->


          <!-- CANVAS TEMPORAL C.B. -->
          <div id="barcode-container" style="display: none;">
            <canvas id="barcode"></canvas>
          </div>
          <!-- End::CANVAS-TEMPORAL-C.B. -->


          <!-- MODAL - AGREGAR PRESENTACION -->
          <div class="modal fade modal-effect" id="modal-agregar-presentacion" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-presentacionLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-presentacionLabel1">Registrar Presentación</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="formulario-ps" id="formulario-ps" method="POST" class="row needs-validation" novalidate>
                    <div class="row" id="cargando-20-fomulario">
                      <input type="hidden" name="idproducto_sucursal_ps" id="idproducto_sucursal_ps">
                      <input type="hidden" name="idpresentacion" id="idpresentacion">

                      <div class="col-lg-12 col-xl-12 col-xxl-12">
                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >PRESENTACIONES</b></label></div>
                          </div>
                        </div> <!-- /.row -->
                        <div class="card-body p-2" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                          <div class="row">

                            <!-- ------------- UNIDAD MEDIDA -------------- -->
                            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                              <div class="form-group">
                                <label for="um_presentacion" class="form-label">U. Medida</label>
                                <select class="form-control" name="um_presentacion" id="um_presentacion">
                                  <!-- lista de u medidas -->
                                </select>
                              </div>
                            </div>
                            <!-- ------------- CANTIDAD -------------- -->
                            <div class="col-md-6">
                              <div class="form-label">
                                <label for="cant_ps" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" name="cant_ps" id="cant_ps" />
                              </div>
                            </div>
                            <!-- ------------- NOMBRE PRESENTACION -------------- -->  
                            <div class="col-md-12">
                              <div class="form-label">
                                <label for="nombre_presentacion" class="form-label">Nombre Presentacion</label>
                                <input type="text" class="form-control" name="nombre_presentacion" id="nombre_presentacion"  />
                              </div>
                            </div>

                            <!-- ------------- PRECIO PRESENTACION -------------- -->  
                            <div class="col-md-6">
                              <div class="form-label">
                                <label for="Precio_presentacion_und" class="form-label">Precio por Unidad</label>
                                <input type="text" class="form-control" name="precio_presentacion_und" id="precio_presentacion_und"  />
                              </div>
                            </div>

                            <!-- ------------- PRECIO PRESENTACION X PRESENTACION-------------- -->  
                            <div class="col-md-6">
                              <div class="form-label">
                                <label for="Precio_presentacion_total" class="form-label">Precio Total</label>
                                <input type="text" class="form-control" name="precio_presentacion_total" id="precio_presentacion_total"  />
                              </div>
                            </div>

                          </div> <!-- /.row -->
                        </div> <!-- /.card-body -->
                      </div> <!-- /.col-lg-12 -->  

                      <div class="col-lg-12 mt-3">
                        <div class="d-flex justify-content-end">
                          <button type="button" class="btn btn-sm btn-danger me-1"  onclick="limpiar_form_ps();"><i class="las la-times fs-12"></i> Limpiar</button>
                          <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_presentacion"><i class="bx bx-save bx-tada fs-12"></i> Guardar</button>
                        </div>                        
                      </div>

                    </div>
                    <div class="row" id="cargando-21-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-ps">Submit</button>
                  </form>

                  <div class="position-relative text-center my-3">
                    <hr class="border-top border-2 position-relative z-1">
                  </div>

                  <div class="table-responsive" id="tabla-presentaciones">
                    <!-- LISTA DE PRESENTACIONES -->
                  </div>
                  
                </div>
                
              </div>
            </div>
          </div>
          <!-- End::Modal-registrar-presetacion -->


          <!-- Modal para ver imagen -->
          <div class="modal fade modal-effect" id="modal-ver-img" tabindex="-1" role="dialog" aria-labelledby="modalVerImgLabel">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
              <div class="modal-content">

                <div class="modal-header">
                  <h6 class="modal-title title-modal-img"><!-- Nombre dinámico --></h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center html_ver_img">
                  <!-- Aquí se carga la imagen desde JS -->
                </div>

              </div>
            </div>
          </div>


        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='Producto'; $descripcion ='Lista de Producto del sistema!'; $title_modulo = 'Articulos'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/producto.js?version_jdl=1.16"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>