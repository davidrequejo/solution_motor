<?php
  //Activamos el almacenamiento en el buffer
  ob_start();
  date_default_timezone_set('America/Lima'); require "../config/funcion_general.php";
  session_start();
  if (!isset($_SESSION["user_nombre"])){
    header("Location: index.php?file=".basename($_SERVER['PHP_SELF']));
  }else{

    ?>
      <!DOCTYPE html>
      <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close" data-bg-img="bgimg4" style="--primary-rgb: 208, 2, 149;" loader="enable">

        <head>
          
          <?php $title_page = "Ingresos"; include("template/head.php"); ?>    

        </head> 

        <body id="body-ingreso">

          <?php include("template/switcher.php"); ?>
          <?php include("template/loader.php"); ?>

          <div class="page">
            <?php include("template/header.php") ?>
            <?php include("template/sidebar.php") ?>
            <?php if($_SESSION['ingresos']==1) { ?>

            <!-- Start::app-content -->
            <div class="main-content app-content">
              <div class="container-fluid">

                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                  <div>
                    <div class="d-md-flex d-block align-items-center ">
                      <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);limpiar_form();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                      <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);limpiar_form();" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                      <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                      <div>
                        <p class="fw-semibold fs-18 mb-0">Ingresos</p>
                        <span class="fs-semibold text-muted">Administra los ingresos.</span>
                      </div>                
                    </div>
                  </div>
                  
                  <div class="btn-list mt-md-0 mt-2">              
                    <nav>
                      <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Ingresos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ingreso/Egreso</li>
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
                          <!-- --------------- Tabla Ingresos ------------- -->
                          <div id="div-tabla" class="table-responsive">
                            <table id="tabla-ingresos" class="table table-bordered w-100" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Fecha</th>
                                  <th>Trabajador</th>
                                  <th >Comprobante</th>
                                  <th >Total</th>
                                  <th>Descripción</th>
                                  <th>CFDI</th>
                                  
                                  <th>Trabajador</th>
                                  <th>Tipo Doc.</th>
                                  <th>Nro. Doc.</th>
                                  <th>Proveedor</th>
                                  <th>Dia</th>
                                  <th>Mes</th>
                                  <th>Subtotal</th>
                                  <th>IGV</th>
                                  <th>Descripción Comprobante</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>
                                  <th class="text-center">Acciones</th>
                                  <th>Fecha</th>
                                  <th>Trabajador</th>
                                  <th >Comprobante</th>
                                  <th class="bg-light">Total</th>
                                  <th>Descripción</th>
                                  <th>CFDI</th>
                                  
                                  <th>Trabajador</th>
                                  <th>Tipo Doc.</th>
                                  <th>Nro. Doc.</th>
                                  <th>Proveedor</th>
                                  <th>Dia</th>
                                  <th>Mes</th>
                                  <th>Subtotal</th>
                                  <th>IGV</th>
                                  <th>Descripción Comprobante</th>
                                </tr>
                              </tfoot>
                            </table>
                          </div>

                          <!-- ------------- Formulario Ingresos ---------- -->
                          <div id="div-formulario" style="display: none;">
                            <form name="formulario-ingresos" id="formulario-ingresos" method="POST" class="row g-3 needs-validation" novalidate>
                              
                              <!-- ::::::::::::::::::: DATOS GENERALES ::::::::::::::::: -->
                              <div class="row gy-2" id="cargando-1-formulario">
                                <!-- ----------------------- ID ------------------------ -->
                                <input type="hidden" name="idingresos_caja" id="idingresos_caja"/>
                                <input type="hidden" name="tipo_gasto_modulo" id="tipo_gasto_modulo" value="INGRESOS"/>
                                <input type="hidden" name="idcaja" id="idcaja"/>

                                <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="row">

                                    <!-- Grupo -->
                                    <div class="col-12 pl-0">
                                      <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS DEL COMPROBANTE</b></label></div>
                                    </div>
                                  </div>

                                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                                    <div class="row ">

                                      <!-- --------------------- PROVEEDOR ------------------- -->
                                      <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                        <div class="form-group">
                                          <label for="idproveedor" class="form-label">
                                            <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_trabajador(); limpiar_proveedor();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idproveedor();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                            Proveedor
                                            <span class="charge_idproveedor"></span>
                                          </label>
                                          <select class="form-select" name="idproveedor" id="idproveedor"></select>
                                        </div>
                                      </div>

                                      <!-- ----------------- TIPO COMPROBANTE ---------------- -->
                                      <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                        <div class="form-group">
                                          <label for="tipo_comprobante" class="form-label">Tipo Comprobante</label>
                                          <select class="form-select form-select-lg" name="tipo_comprobante" id="tipo_comprobante">
                                            <option value="NINGUNO">NINGUNO</option>
                                            <option value="BOLETA">BOLETA</option>
                                            <option value="FACTURA">FACTURA</option>
                                            <option value="NOTA_DE_VENTA">NOTA DE VENTA</option>
                                          </select>
                                        </div>
                                      </div>

                                      <!-- ----------------- SERIE COMPROBANTE --------------- -->
                                      <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                        <div class="form-group">
                                          <label for="serie_comprobante" class="form-label">Serie Comprobante</label>
                                          <input type="text" class="form-control" name="serie_comprobante" id="serie_comprobante" onkeyup="mayus(this);" placeholder="ejemp: F001-00453" />
                                        </div>
                                      </div>

                                      <!-- ------------------ FECHA EMISION ------------------ -->
                                      <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                        <div class="form-group">
                                          <label for="fecha" class="form-label">Fecha Emisión(*)</label>
                                          <input type="date" class="form-control" name="fecha" id="fecha" max="<?php echo date('Y-m-d');?>" />
                                        </div>
                                      </div>

                                      <!-- --------------------- SUB TOTAL ------------------- -->
                                      <div class="col-md-6 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                        <div class="form-group">
                                          <label for="precio_sin_igv" class="form-label">Sub Total <small class="text-info" >(no editable)</small></label>
                                          <input type="number" class="form-control" name="precio_sin_igv" id="precio_sin_igv" readonly/>
                                        </div>
                                      </div>

                                      <!-- ----------------------- IGV ----------------------- -->
                                      <div class="col-md-6 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                        <div class="form-group">
                                          <label for="igv" class="form-label">IGV <small class="text-info" >(no editable)</small></label>
                                          <input type="number" class="form-control" name="igv" id="igv" placeholder="" value="0.00" readonly />
                                        </div>
                                      </div>

                                      <!-- --------------------- VALOR IGV ------------------- -->
                                      <div class="col-md-6 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                        <div class="form-group">
                                          <label for="val_igv" class="form-label">Val. IGV</label>
                                          <input type="number" class="form-control" name="val_igv" id="val_igv" value="0.00" onkeyup="calculandototales_fact();" />
                                        </div>
                                      </div>

                                      <!-- ---------------------- TOTAL ---------------------- -->
                                      <div class="col-md-6 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                        <div class="form-group">
                                          <label for="precio_con_igv" class="form-label">Total(*)</label>
                                          <input type="number" class="form-control" name="precio_con_igv" id="precio_con_igv" onkeyup="comprob_factura();"  onchange="comprob_factura();"/>
                                        </div>
                                      </div>

                                      <!-- --------------- DESCRIPCION COMPROBANTE ----------- -->
                                      <div class="col-md-6 col-lg-4 col-xl-12 col-xxl-12 mt-3">
                                        <div class="form-group">
                                          <label for="descr_comprobante" class="form-label">Descripción del Comprobante</label>
                                          <textarea class="form-control" name="descr_comprobante" id="descr_comprobante" rows="3" placeholder="ejemp: 
                                          - pago de energia --> 50.00
                                          - pago de agua --> 30.00." 
                                          style="white-space: pre-line;" ></textarea>
                                        </div>
                                      </div>

                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="row">
                                    <!-- Grupo -->
                                    <div class="col-12 pl-0">
                                      <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS EXTRA</b></label></div>
                                    </div>
                                  </div>

                                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                                    <div class="row">

                                      <!-- ------------------- TRABAJADOR -------------------- -->
                                      <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                                        <div class="form-group">
                                          <label for="idtrabajador" class="form-label">
                                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idtrabajador();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                            Nombre del Trabajador(*)
                                            <span class="charge_idtrabajador"></span>
                                          </label>
                                          <select class="form-select form-select-lg" name="idtrabajador" id="idtrabajador" ><!-- List de trabajadores --></select>
                                        </div>
                                      </div>

                                      <!-- --------------------- CATEGORIA -------------------  -->
                                      <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                        <div class="form-group">
                                          <label for="id_ie_categoria" class="form-label">
                                            <span class="badge bg-success m-r-4px cursor-pointer"  onclick="modal_add_categoria(); limpiar_form_cat();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idcategoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                            Categoria Ingreso
                                            <span class="charge_id_ie_categoria"></span>
                                          </label>
                                          <select class="form-select" name="id_ie_categoria" id="id_ie_categoria"></select>
                                        </div>
                                      </div>

                                      <!-- ------------------ PERIODO INGRESO ------------------ -->
                                      <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                        <div class="form-group">
                                          <label for="mes" class="form-label">Periodo Ingreso(*)</label>
                                          <input type="month" class="form-control" name="mes" id="mes" max="<?php echo date('Y-m');?>" />
                                        </div>
                                      </div>

                                    </div>
                                  </div>
                                </div>

                                <!-- --------------------- COMPROBANTE ----------------- -->
                                <div class="p-3 col-md-6 col-lg-4 col-xl-4 col-xxl-4">
                                  <h6 class="card-title text-center">Comprobante</h6>
                                  <div class="col-md-12 border-top p-3">

                                    <div class="my-2 text-center">
                                      <div class="btn-group edit_img">
                                        <button type="button" class="btn btn-primary py-1" id="doc1_i"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</button>
                                        <input type="hidden" id="doc_old_1" name="doc_old_1" />
                                        <input style="display: none;" id="doc1" type="file" name="doc1" accept="application/pdf, image/*" class="docpdf" />
                                        <button type="button" class="btn btn-info py-1" onclick="re_visualizacion(1, 'assets/modulo/ingresos_caja', '100%', '300px'); reload_zoom();"><i class='bx bx-refresh bx-spin fs-5'></i>Refrescar</button>
                                      </div>
                                    </div>

                                    <!-- imagen -->
                                    <div id="doc1_ver" class="text-center ">
                                      <img id="img_defect" src="../assets/images/default/img_defecto3.png" alt="" width="60%" />
                                    </div>
                                    <div  id="doc1_nombre" ><!-- aqui va el nombre del pdf --></div>
                                  </div>
                                </div>

                              </div>

                              <!-- :::::::::::::::::::::: CARGANDO ... ::::::::::::::::: -->
                              <div class="row" id="cargando-2-fomulario" style="display: none;" >
                                <div class="col-lg-12 text-center">                         
                                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                                  <h4 class="bx-flashing">Cargando...</h4>
                                </div>
                              </div>

                              <!-- Chargue -->
                              <div class="p-l-25px col-lg-12" id="barra_progress_ingresos_div" style="display: none;" >
                                <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                                  <div id="barra_progress_ingresos" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                                </div>
                              </div>
                              <!-- Submit -->
                              <button type="submit" style="display: none;" id="submit-form-ingresos">Submit</button>
                            </form>
                          </div>
                        </div>
                        <div class="card-footer border-top-0">
                          <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                          <button type="button" class="btn btn-success btn-guardar" id="guardar_registro_ingresos" style="display: none;"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End::row-1 -->
              </div>
            </div>
            <!-- End::app-content -->
            <?php } else { $title_submodulo ='Ingresos caja'; $descripcion ='Lista de Ingresos del sistema!'; $title_modulo = 'Ingresos'; include("403_error.php"); }?>   

            <!-- MODAL - VER COMPROBANTE -->
            <div class="modal fade modal-effect" id="modal-ver-comprobante" tabindex="-1" aria-labelledby="modal-ver-comprobanteLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title title-modal-comprobante" id="modal-ver-comprobanteLabel1">COMPROBANTE</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="comprobante-container" class="text-center"> <!-- archivo --> 
                      <div class="row" >
                        <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger py-1" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                  </div>
                </div>
              </div>
            </div> 
            <!-- End::Modal-Comprobante -->

            <!-- MODAL - VER DETALLE -->
            <div class="modal fade modal-effect" id="modal-ver-detalle" tabindex="-1" aria-labelledby="modal-ver-detalleLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="modal-ver-detalleLabel1"><b>Detalles</b> - Ingreso</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body" >            
                    <div id="html-detalle-compra"></div>
                    <div class="text-center" id="html-detalle-comprobante"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                  </div>
                </div>
              </div>
            </div> 
            <!-- End::Modal-VerDetalles -->

            <!-- MODAL - AGREGAR TRABAJADOR - charge 3 -->
            <div class="modal fade modal-effect" id="modal-agregar-proveedor" tabindex="-1" aria-labelledby="Modal-agregar-proveedorLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-scrollable">
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
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-3 col-xxl-6">
                                <div class="form-group">
                                  <label for="tipo_documento" class="form-label">Tipo documento:  </label>
                                  <select name="tipo_documento" id="tipo_documento" class="form-select" required>                                      
                                  </select>
                                </div>                                         
                              </div>
                              
                              <!--  Numero Documento -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-3 col-xxl-6">
                                <div class="form-group">
                                  <label for="numero_documento" class="form-label">Numero Documento:</label>
                                  <div class="input-group">                            
                                    <input type="number" class="form-control" name="numero_documento" id="numero_documento" placeholder="" aria-describedby="icon-view-password">
                                    <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-proveedor','_t', '#tipo_documento', '#numero_documento', '#nombre_razonsocial', '#apellidos_nombrecomercial', '#direccion', '#distrito', '#titular_cuenta' );" >
                                      <i class='bx bx-search-alt' id="search_t"></i>
                                      <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                    </button>
                                  </div>
                                </div>                        
                              </div>         
                            
                              <!-- Nombres -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-4 col-xxl-6">
                                <div class="form-group">
                                  <label for="nombre_razonsocial" class="form-label label-nom-raz">Nombres:  </label></label>
                                  <input type="text" class="form-control" name="nombre_razonsocial" id="nombre_razonsocial" >
                                </div>                                         
                              </div>

                              <!-- Apellidos -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-4 col-xxl-6 ">
                                <div class="form-group">
                                  <label for="apellidos_nombrecomercial" class="form-label label-ape-come">Apellidos:  </label></label>
                                  <input type="text" class="form-control" name="apellidos_nombrecomercial" id="apellidos_nombrecomercial" >
                                </div>                                         
                              </div>

                              <!-- Correo -->
                              <div class="mb-1 col-md-6 col-lg-4 col-xl-4 col-xxl-6">
                                <div class="form-group">
                                  <label for="correo" class="form-label">Correo:</label>
                                  <input type="email" class="form-control" name="correo" id="correo">
                                </div>                                         
                              </div>

                              <!-- Celular -->
                              <div class="col-md-6 col-lg-3 col-xl-4 col-xxl-6">
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
                                  <select name="distrito" id="distrito" class="form-select" onchange="llenar_dep_prov_ubig(this)">
                                    
                                  </select>
                                </div>                                         
                              </div>
                              <!-- Departamento -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                  <label for="departamento" class="form-label">Departamento: <span class="chargue-pro"></span></label>
                                  <input type="text" class="form-control" name="departamento" id="departamento" readonly>
                                </div>                                         
                              </div>
                              <!-- Provincia -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                  <label for="provincia" class="form-label">Provincia: <span class="chargue-dep"></span></label>
                                  <input type="text" class="form-control" name="provincia" id="provincia" readonly>
                                </div>                                         
                              </div>
                              <!-- Ubigeo -->
                              <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                  <label for="ubigeo" class="form-label">Ubigeo: <span class="chargue-ubi"></span></label>
                                  <input type="text" class="form-control" name="ubigeo" id="ubigeo" readonly>
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
                              <div class="mb-1 col-md-3 col-lg-6 col-xl-6 col-xxl-4 mt-3">
                                <div class="form-group">
                                  <label for="idbanco" class="form-label">Entidad Financiera:  </label>
                                  <select name="idbanco" id="idbanco" class="form-select" required>                                       
                                  </select>
                                </div>                                         
                              </div>

                              <!-- Cuenta Bancaria -->
                              <div class="mb-1 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                <div class="form-group">
                                  <label for="cuenta_bancaria" class="form-label">Cuenta Bancaria:</label>
                                  <input type="text" class="form-control" name="cuenta_bancaria" id="cuenta_bancaria" >
                                </div>                                         
                              </div>

                              <!-- CCI -->
                              <div class="mb-1 col-md-6 col-lg-3 col-xl-4 col-xxl-4 mt-3">
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
                              <a class="btn btn-primary" onclick="cambiarImagen()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                              <a class="btn btn-light" onclick="removerImagen()"><i class="bi bi-trash fs-6"></i> Remover</a>
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

            <!-- MODAL - VER IMAGEN -->
            <div class="modal fade modal-effect" id="modal-ver-img" tabindex="-1" aria-labelledby="modal-agregar-usuarioLabel" aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title title-modal-img" id="modal-agregar-usuarioLabel1">Imagen</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body html_ver_img">
                    
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                  </div>
                </div>
              </div>
            </div> 
            <!-- End::Modal-Ver-Imagen -->

            <!-- MODAL - AGREGAR CATEGORIA -->
            <div class="modal fade modal-effect" id="modal-agregar-ie-categoria" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-ie-categoriaLabel">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-ie-categoriaLabel1">Registrar Categoría</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="formulario-ie-categoria" id="formulario-ie-categoria" method="POST" class="needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-7-fomulario">
                        
                        <!-- --------------------- NOMBRE ----------------- -->
                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="nombre_ie_cat" class="form-label">Nombre(*)</label>
                            <input type="text" class="form-control" name="nombre_ie_cat" id="nombre_ie_cat" onkeyup="mayus(this);"/>
                          </div>
                        </div>

                        <!-- ------------------ DESCRIPCION --------------- -->
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="descr_ie_cat" class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descr_ie_cat" id="descr_ie_cat" onkeyup="mayus(this);"/>
                          </div>
                        </div>

                      </div>
                      <div class="row" id="cargando-8-fomulario" style="display: none;">
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
                     

            <?php include("template/search_modal.php"); ?>
            <?php include("template/footer.php"); ?>

          </div>

          <?php include("template/scripts.php"); ?>
          <?php include("template/custom_switcherjs.php"); ?> 

          <script src="scripts/ingresos_caja.js?version_jdl=1.16"></script>
          <script> $(function () { $('[data-bs-toggle="tooltip"]').tooltip(); }); </script>

        
        </body>

      </html>
    <?php
  }
  ob_end_flush();
?>