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
    <?php $title_page = "Emitir Comprobante";
    include("template/head.php"); ?>

    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="../assets/libs/glightbox/css/glightbox.min.css">
    <style>
      #tabla-facturacion-detalle td {
        vertical-align: middle !important;
        line-height: 1.462 !important;
        font-size: .6875rem !important;
        font-weight: 50 !important;
      }

      #tabla-ventas_filter label {
        width: 100% !important;
      }

      #tabla-ventas_filter label input {
        width: 100% !important;
      }

      .imagen-metodo-pago img {
        /*width: 100% !important;  Ajusta el ancho al contenedor */
        /*height: auto !important;  Mantén la proporción de aspecto */
        width: 140px !important;
        /* Máximo ancho permitido */
        height: 130px !important;
        /* Máximo alto permitido */
        object-fit: contain !important;
        /* Asegura que la imagen no se deforme */
        border: 1px solid #ddd !important;
        /* Opcional: agrega un borde para resaltar el contenedor */
        box-sizing: border-box !important;
      }

      .div_pago_rapido img {
        width: 60px;
        /* Ajusta el tamaño de las imágenes */
        height: 100%;
        cursor: pointer;
        border: 3px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s ease;
        /* Suaviza la transición */
      }

      .div_pago_rapido img:hover {
        border-color: #007bff;
        /* Cambia el borde al pasar el ratón */
      }  

      
    </style>
  </head>

  <body id="body-ventas" idusuario="<?php echo $_SESSION['idusuario']; ?>" idpersona="<?php echo $_SESSION['idpersona']; ?>" idpersona_trabajador="<?php echo $_SESSION['idpersona_trabajador']; ?>">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['orden_venta'] == 1) { ?>
        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

              <!-- Start::page-header -->
              <div class="d-md-flex d-block align-items-center justify-content-between mt-1 mb-2 page-header-breadcrumb">
                <div>
                  <div class="d-md-flex d-block align-items-center ">
                    <!-- <button type="button" class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2); reload_usr_trab(); limpiar_form(); reload_ps();"  > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button> -->
                    <!-- <i class="bi bi-card-checklist fa-2x me-1"></i> -->
                    <i class="bi bi-list-check fs-18 me-1"></i>
                    <!-- <i class="bi bi-list-ol fa-2x me-1"></i> -->
                    <div>
                      <p class="fw-semibold fs-18 mb-0">Realiza tus ordenes!</p>
                      <!-- <span class="fs-semibold text-muted">Adminstra de manera eficiente tus ordenes de venta.</span> -->
                    </div>                
                  </div>
                </div>
                
                <div class="btn-list mt-md-0 mt-2">              
                  <nav>
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="javascript:void(0);"> Orden</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Realizar orden</li>
                    </ol>
                  </nav>
                </div>
              </div>          
              <!-- End::page-header -->

              <!-- Start::row-1 -->
              <div class="row bg-white m-0 py-2 rounded shadow-sm mb-2">
                <div class="col-12">
                  <label for="filtro_marca mb-1" class="form-label">
                    <div class="d-flex align-items-center">

                      <label for="filtro_marca" class="form-label mb-0">
                        <span class="badge bg-info me-1 cursor-pointer" onclick="reload_filtro_marca();" data-bs-toggle="tooltip" title="Actualizar">
                          <i class="las la-sync-alt"></i>
                        </span>
                        Buscar Productos
                        <span class="charge_filtro_marca"></span>
                      </label>

                      <div class="form-check ms-auto ps-5 hidden">
                        <input class="form-check-input" type="checkbox" value="SI" id="precio_por_mayor">
                        <label class="form-check-label" for="precio_por_mayor">
                          Precio por Mayor ?
                        </label>
                      </div>

                    </div>
                  </label>
                  <div class="position-relative mb-1">
                    <div class=" form-floating floating-primary">
                      <input type="text" name="search_producto" id="search_producto" class="form-control" onkeyup="mayus(this);" placeholder="Busca por código, nombre o escanea el producto.">
                      <label for="floatingInput">Busca por código, nombre o escanea el producto</label>
                    </div>
                    <ul class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xxl-8 col-xl-8 col-lg-8 col-md-12">
                  <div class="row" id="searchResults"></div>
                </div>

                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-12">
                  <form name="form-agregar-nueva-orden" id="form-agregar-nueva-orden" method="POST" class="needs-validation" novalidate>
                    <div  id="cargando-1-fomulario">
                              
                      <div class="card custom-card mb-2">
                        <div class="card-header">
                          <div class="card-title me-1">Resumen de Orden</div>
                        </div>

                        <div class="card-body p-0">

                          <div class="px-3 pt-3 pb-2 border-bottom border-block-end-dashed">
                            <div class="collapse" id="collapse-datos-extra">
                              <div class="row mb-2">

                                <!-- IMPUESTO -->
                                <input type="hidden" name="o_idventa" id="o_idventa" />
                                <!-- IMPUESTO -->
                                <input type="hidden" class="form-control" name="o_impuesto" id="o_impuesto" value="0">
                                <!-- TIPO DOC -->
                                <input type="hidden" class="form-control" name="o_tipo_comprobante" id="o_tipo_comprobante" value="103">
                                <!-- NUMERO DOC -->
                                <input type="hidden" class="form-control" name="o_idsunat_c01" id="o_idsunat_c01" value="46">
                                

                                <!-- CREAR Y MOSTRAR-->
                                <div class="col-md-12 col-lg-4 col-xl-6 col-xxl-6 ">
                                  <p class="mb-2 fw-semibold">Imprimir? <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" data-bs-html="true" aria-label="Esta opción te mostrara en automático el comprobante para: 🔸Enviarlo al cliente 🔸Descargarlo 🔸Imprimirlo" data-bs-original-title="Esta opción te mostrara en automático el comprobante para: 🔸Enviarlo al cliente 🔸Descargarlo 🔸Imprimirlo"></i></p>
                                  <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="o_crear_y_mostrar" id="o_crear_y_mostrar_1" value="SI" checked="">
                                    <label class="btn btn-outline-primary text-default" for="o_crear_y_mostrar_1">SI</label>
                                    <input type="radio" class="btn-check" name="o_crear_y_mostrar" id="o_crear_y_mostrar_2" value="NO">
                                    <label class="btn btn-outline-primary text-default" for="o_crear_y_mostrar_2">NO</label>
                                  </div>
                                </div>
                                <!-- ----------------- SERIE --------------- -->
                                <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 ">
                                  <div class="form-group">
                                    <label for="o_serie_comprobante" class="form-label">Serie</label>
                                    <select name="o_serie_comprobante" id="o_serie_comprobante" class="form-control" placeholder="Serie">                                     
                                    </select>
                                  </div>
                                </div>

                              </div>
                            </div>
                            <div class="">
                              <a class="ecommerce-more-link" data-bs-toggle="collapse" href="#collapse-datos-extra" role="button" aria-expanded="false" aria-controls="category-more">DATOS EXTRA</a>
                            </div>
                          </div>
                          <div class="p-3 border-bottom border-block-end-dashed">

                            <div class="form-group">
                              <label for="o_idcliente" class="form-label">
                                <span class="badge bg-success m-r-4px cursor-pointer" onclick="modal_add_nuevo_cliente();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_o_idcliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Cliente
                                <span class="charge_o_idcliente"></span>
                              </label>
                              <select class="form-control" name="o_idcliente" id="o_idcliente"></select>
                            </div>

                          </div>
                          <ul class="list-group mb-0 border-0 rounded-0 " id="productos-seleccionados">
                            <!-- Aquí se agregarán los productos seleccionados dinámicamente -->
                          </ul>

                          <div class="p-3 border-bottom border-block-end-dashed">
                            <!-- Subtotal -->
                            <div class="d-flex align-items-center justify-content-between">
                              <div class="fs-12 ">Subtotal</div>
                              <div class=" fs-12 text-dark o_venta_subtotal"> S/ 00.00</div>
                              <input type="hidden" id="o_venta_subtotal" name="o_venta_subtotal">
                            </div>
                            <!-- Descuento -->
                            <div class="d-flex align-items-center justify-content-between">
                              <div class="fs-12 ">Descuento</div>
                              <div class=" fs-12 text-dark o_venta_descuento"> S/ 00.00</div>
                              <input type="hidden" id="o_venta_descuento" name="o_venta_descuento">
                            </div>
                            <!-- IGV -->
                            <div class="d-flex align-items-center justify-content-between">
                              <div class="fs-12 val_igv">IGV</div>
                              <div class=" fs-12 text-dark o_venta_igv"> S/ 00.00</div>
                              <input type="hidden" id="o_venta_igv" name="o_venta_igv">
                            </div>
                            <!-- Total -->
                            <div class="d-flex align-items-center justify-content-between">
                              <div class="fs-17 fw-semibold">Total</div>
                              <div class="fw-semibold fs-16 text-dark o_venta_total"> S/ 00.00</div>
                              <input type="hidden" id="o_venta_total" name="o_venta_total">
                            </div>
                          </div>

                        </div>

                      </div>
                    
                    </div> <!-- -------------------------/.row -->

                    <div class="row" id="cargando-2-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div> <!-- /.row -->

                    <!-- Chargue -->
                    <div class="p-l-25px col-lg-12" id="barra_progress_nueva_orden_div" style="display: none;">
                        <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                          <div id="barra_progress_nueva_orden" class="progress-bar" style="width: 0%">
                            <div class="progress-bar-value">0%</div>
                          </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" style="display: none;" id="submit-form-nueva_orden">Submit</button>
                  </form> 
                  <div class="card-footer py-1 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Limpiar</button>
                    <button type="button" class="btn btn-sm btn-success label-btn" id="guardar_registro_nuevo_orden"><i class="bx bx-save bx-tada"></i> Guardar</button>
                  </div>
                </div>


              </div>
            
            <!--End::row-1 -->

          </div>

          <!-- MODAL - AGREGAR CLIENTE - charge 3,4 -->
          <div class="modal fade modal-effect" id="modal-agregar-nuevo-cliente" tabindex="-1" aria-labelledby="Modal-agregar-nuevo-clienteLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-modal-nuevo-cliente" id="Modal-agregar-nuevo-clienteLabel1">Agregar Cliente</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">

                  <form name="form-agregar-nuevo-cliente" id="form-agregar-nuevo-cliente" method="POST" class="needs-validation" novalidate>

                    <div class="row" id="cargando-3-fomulario">
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
                            <div class="mb-1 col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                              <div class="form-group">
                                <label class="form-label" for="cli_direccion">Dirección: <sup class="text-danger">*</sup></label>
                                <textarea name="cli_direccion" class="form-control inpur_edit" id="cli_direccion" placeholder="ejemp: Jr las flores" rows="2"></textarea>
                              </div>
                            </div>

                            <!-- Dirección -->
                            <div class="mb-1 col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
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
                                <label for="cli_departamento" class="form-label">Departamento: <span class="chargue-pro"></span></label>
                                <input type="text" class="form-control" name="cli_departamento" id="cli_departamento" readonly>
                              </div>
                            </div>
                            <!-- Provincia -->
                            <div class="mb-1 col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6">
                              <div class="form-group">
                                <label for="cli_provincia" class="form-label">Provincia: <span class="chargue-dep"></span></label>
                                <input type="text" class="form-control" name="cli_provincia" id="cli_provincia" readonly>
                              </div>
                            </div>
                            <!-- Ubigeo -->
                            <div class="mb-1 col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6">
                              <div class="form-group">
                                <label for="cli_ubigeo" class="form-label">Ubigeo: <span class="chargue-ubi"></span></label>
                                <input type="text" class="form-control" name="cli_ubigeo" id="cli_ubigeo" readonly>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>

                    </div> <!-- /.row -->

                    <div class="row" id="cargando-4-fomulario" style="display: none;">
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
        $title_submodulo = 'Orden de venta';
        $descripcion = 'Lista de Orden de venta del sistema!';
        $title_modulo = 'Ordenes';
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

  <script src="scripts/orden_venta.js?version_jdl=1.16"></script>
  <script>
    $(function() {
      $('[data-bs-toggle="tooltip"]').tooltip({
        html: true
      });
      console.log('Pagina termino de cargar');
    });
  </script>




  </body>



  </html>
<?php
}
ob_end_flush();
?>