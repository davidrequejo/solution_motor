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
    <?php $title_page = "Empresa";
    include("template/head.php"); ?>
  </head>

  <body id="body-tipo-de-tribujos">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['empresa']==1) { ?>

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
                  <p class="fw-semibold fs-18 mb-0">Empresa</p>
                  <span class="fs-semibold text-muted">Administra los datos de la Empresa.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Empresa</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Administracion</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">
            <div class="col-xxl-12 col-xl-12">
              
              <div class="card custom-card">
                <div class="card-body">
                  <!-- Start::Tabla-Empresa -->
                  <div class="table-responsive" id="div-tabla">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-empresa">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-center">Acciones</th>
                          <th>Logo</th>
                          <th>Razón Social</th>
                          <th>cuenta Bancaria</th>
                          <th>Ubicación</th>
                          <th>Contacto</th>
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                        <tr>
                        <th class="text-center">#</th>
                          <th class="text-center">Acciones</th>
                          <th>Logo</th>
                          <th>Razón Social</th>
                          <th>cuenta Bancaria</th>
                          <th>Ubicación</th>
                          <th>Contacto</th>
                          <th>Estado</th>
                        </tr>
                      </tfoot>

                    </table>

                  </div>
                  <!-- End::Tabla-Empresa -->

                  <!-- Start::Formulario-Empresa -->
                  <div id="div-formulario" style="display: none;">
                    <form name="formulario-empresa" id="formulario-empresa" method="POST" class="row g-3 needs-validation" novalidate>
                      <ul class="nav nav-tabs tab-style-2 mb-3"  role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="dato-empresa" data-bs-toggle="tab" data-bs-target="#dato-empresa-pane" type="button" role="tab" aria-selected="true"><i class="ri-building-line me-1 align-middle"></i>Datos de la Empresa</button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="rol-cuenta" data-bs-toggle="tab" data-bs-target="#rol-cuenta-pane" type="button" role="tab" aria-selected="false"><i class="ri-bank-line me-1 align-middle"></i>Cuentas Bancarias</button>
                        </li> 
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="rol-ubicacion" data-bs-toggle="tab" data-bs-target="#rol-ubicacion-pane" type="button" role="tab" aria-selected="false"><i class="ri-map-pin-line me-1 align-middle"></i>Ubicación</button>
                        </li>                  
                      </ul>

                      <div class="tab-content">

                        <div class="tab-pane fade show active text-muted" id="dato-empresa-pane" role="tabpanel" tabindex="0">
                          <div class="row" id="cargando-1-formulario">
                            <!-- ------ ID --------- -->
                            <input type="hidden" name="idempresa" id="idempresa"/>

                            <!-- --------- Tipo de Documento ------- -->
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="tipo_doc" class="form-label">Tipo de Documento:</label>
                                <select name="tipo_doc" id="tipo_doc" class="form-control">
                                  <option value="6">RUC</option>
                                </select>
                              </div>                                         
                            </div>
                            <!-- ---------- Numero de Documento ------------ -->
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="documento" class="form-label">Número de Documento:</label>
                                <div class="input-group">                            
                                  <input type="number" class="form-control" name="documento" id="documento" placeholder="" aria-describedby="icon-view-password">
                                  <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#formulario-empresa', '_t', '#tipo_doc', '#documento', '#razon_social', '#nomb_comercial', '#domicilio_fiscal', '#distrito' );" >
                                    <i class='bx bx-search-alt' id="search_t"></i>
                                    <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                  </button>
                                </div>                              
                              </div>
                            </div>
                            <!-- ----------- Razon Social -------------- -->
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="razon_social" class="form-label">Razon Social:</label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control"/>
                              </div>
                            </div>
                            <!-- ------------Nombre Comercial -------------- -->
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="nomb_comercial" class="form-label">Nombre Comercial:</label>
                                <input type="text" name="nomb_comercial" id="nomb_comercial" class="form-control"/>
                              </div>
                            </div>
                            <!-- --------- Telefono 1 ------- -->
                            <div class="col-md-2 mt-4">
                              <div class="form-group">
                                <label for="telefono1" class="form-label">Telefono 1:</label>
                                <input type="number" name="telefono1" id="telefono1" class="form-control">
                              </div>                                         
                            </div>
                            <!-- ---------- Telefono 2 ------------ -->
                            <div class="col-md-2 mt-4">
                              <div class="form-group">
                                <label for="telefono2" class="form-label">Telefono 2:</label>
                                <input type="number" name="telefono2" id="telefono2" class="form-control"/>
                              </div>
                            </div>
                            <!-- ------------ Link Web -------------- -->
                            <div class="col-md-4 mt-4">
                              <div class="form-group">
                                <label for="web" class="form-label">Web:</label>
                                <input type="link" name="web" id="web" class="form-control"/>
                              </div>
                            </div>
                            <!-- ------------ Link Web Consulta -------------- -->
                            <div class="col-md-4 mt-4">
                              <div class="form-group">
                                <label for="web_consulta" class="form-label">Web Consulta (CP):</label>
                                <input type="link" name="web_consulta" id="web_consulta" class="form-control"/>
                              </div>
                            </div>
                            <!-- ------------ Correo -------------- -->
                            <div class="col-md-4 mt-4">
                              <div class="form-group">
                                <label for="correo" class="form-label">Correo:</label>
                                <input type="email" name="correo" id="correo" class="form-control"/>
                              </div>
                            </div>
                            <!-- ------------- Logo C R ------------ -->
                            <div class="col-md-4 mt-4">
                              <div class="form-group">
                                <label for="logo_c_r" class="form-label">Forma del Logo:</label>
                                <select name="logo_c_r" id="logo_c_r" class="form-control">
                                  <option value="0">Rectandular</option>
                                  <option value="1" selected>Cuadrado</option>
                                </select>
                              </div>
                            </div>

                            <!-- ----------- Logo -------------- -->
                            <div class="p-3 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2">
                              <h6 class="card-title text-center">Logo:</h6>
                              <div class="col-md-12 border-top p-3">

                                <div class="my-2 text-center">
                                  <div class="btn-group edit_img">
                                    <button type="button" class="btn btn-primary py-1" id="doc1_i"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</button>
                                    <input type="hidden" id="doc_old_1" name="doc_old_1" />
                                    <input style="display: none;" id="doc1" type="file" name="doc1" accept="application/pdf, image/*" class="docpdf" />
                                    <button type="button" class="btn btn-info py-1" onclick="re_visualizacion(1, 'assets/modulo/empresa/logo', '60%'); reload_zoom();"><i class='bx bx-refresh bx-spin fs-5'></i>Refrescar</button>
                                  </div>
                                </div>

                                <!-- imagen -->
                                <div id="doc1_ver" class="text-center ">
                                  <img id="img_defect" src="../assets/images/default/img_defecto2.png" alt="" width="60%" />
                                </div>
                                <div  id="doc1_nombre" ><!-- aqui va el nombre del pdf --></div>
                              </div>
                            </div>

                          </div>
                          <div class="row" id="cargando-2-fomulario" style="display: none;" >
                            <div class="col-lg-12 text-center">                         
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>
                        </div>

                        <div class="tab-pane fade" id="rol-cuenta-pane" role="tabpanel" tabindex="0">
                          <div class="row" id="cargando-3-formulario">
                            <!-- ------------ Banco 1 ------------- -->
                            <div class="col-6">
                              <div class="rounded border p-3 border-3">
                                <h5 class="form-label text-center">
                                  PRIMER BANCO
                                  <i class="ri-information-line me-1 align-middle text-primary" title="Estos datos se mostrarán en la parte principal"></i>
                                </h5>
                                <div class="form-group mt-2">
                                  <label for="banco1" class="form-label">Entidad Bancaria:</label>
                                  <select id="banco1" name="banco1" class="form-control"> </select>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cuenta1" class="form-label">Cuenta:</label>
                                  <input type="text" id="cuenta1" name="cuenta1" class="form-control"/>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cci1" class="form-label">CCI:</label>
                                  <input type="text" id="cci1" name="cci1" class="form-control"/>
                                </div>
                              </div>
                            </div>

                            <!-- ------------ Banco 2 ------------- -->
                            <div class="col-6">
                              <div class="rounded border p-3 border-3">
                                <h5 class="form-label text-center">SEGUNDO BANCO</h5>
                                <div class="form-group mt-2">
                                  <label for="banco2" class="form-label">Entidad Bancaria:</label>
                                  <select id="banco2" name="banco2" class="form-control"> </select>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cuenta2" class="form-label">Cuenta:</label>
                                  <input type="text" id="cuenta2" name="cuenta2" class="form-control"/>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cci2" class="form-label">CCI:</label>
                                  <input type="text" id="cci2" name="cci2" class="form-control"/>
                                </div>
                              </div>
                            </div>

                            <!-- ------------ Banco 3 ------------- -->
                            <div class="col-6 mt-4">
                              <div class="rounded border p-3 border-3">
                                <h5 class="form-label text-center">TERCER BANCO</h5>
                                <div class="form-group mt-2">
                                  <label for="banco3" class="form-label">Entidad Bancaria:</label>
                                  <select id="banco3" name="banco3" class="form-control"> </select>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cuenta3" class="form-label">Cuenta:</label>
                                  <input type="text" id="cuenta3" name="cuenta3" class="form-control"/>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cci3" class="form-label">CCI:</label>
                                  <input type="text" id="cci3" name="cci3" class="form-control"/>
                                </div>
                              </div>
                            </div>
                            <!-- ------------ Banco 4 ------------- -->
                            <div class="col-6 mt-4">
                              <div class="rounded border p-3 border-3">
                                <h5 class="form-label text-center">CUARTO BANCO</h5>
                                <div class="form-group mt-2">
                                  <label for="banco4" class="form-label">Entidad Bancaria:</label>
                                  <select id="banco4" name="banco4" class="form-control"> </select>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cuenta4" class="form-label">Cuenta:</label>
                                  <input type="text" id="cuenta4" name="cuenta4" class="form-control"/>
                                </div>
                                <div class="form-group mt-2">
                                  <label for="cci4" class="form-label">CCI:</label>
                                  <input type="text" id="cci4" name="cci4" class="form-control"/>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row" id="cargando-4-fomulario" style="display: none;" >
                            <div class="col-lg-12 text-center">                         
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>
                        </div>

                        <div class="tab-pane fade" id="rol-ubicacion-pane" role="tabpanel" tabindex="0">
                          <div class="row" id="cargando-5-formulario">
                            <!-- ----------- Codigo de Pais -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="codg_pais" class="form-label">Código de País:</label>
                                <input type="text" name="codg_pais" id="codg_pais" class="form-control" value="PE" readonly/>
                              </div>
                            </div>
                            <!-- ----------- Domicilio Fiscal -------------- -->
                            <div class="col-md-8 mt-3">
                              <div class="form-group">
                                <label for="domicilio_fiscal" class="form-label">Domicilio Fiscal:</label>
                                <input type="text" name="domicilio_fiscal" id="domicilio_fiscal" class="form-control"/>
                              </div>
                            </div>
                            <!-- ----------- Distrito -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="distrito" class="form-label">Distrito:</label>
                                <select type="text" name="distrito" id="distrito" class="form-control" onchange="ubicacion_geografica(this);"> </select>
                              </div>
                            </div>
                            <!-- ----------- Departamento -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="departamento" class="form-label">Departamento: <span class="chargue-pro"></span></label>
                                <input type="text" name="departamento" id="departamento" class="form-control" readonly/>
                              </div>
                            </div>
                            <!-- ----------- Provincia -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="provincia" class="form-label">Provincia: <span class="chargue-dep"></span></label>
                                <input type="text" name="provincia" id="provincia" class="form-control" readonly/>
                              </div>
                            </div>
                            <!-- ----------- Ubigeo -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="ubigeo" class="form-label">Ubigeo:</label>
                                <input type="text" name="ubigeo" id="ubigeo" class="form-control"/>
                              </div>
                            </div>
                            <!-- ----------- Código Ubigeo -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="codg_ubigeo" class="form-label">Código Ubigeo: <span class="chargue-ubi"></span></label>
                                <input type="text" name="codg_ubigeo" id="codg_ubigeo" class="form-control" readonly/>
                              </div>
                            </div>
                            <!-- ----------- Referencia -------------- -->
                            <div class="col-md-4 mt-3">
                              <div class="form-group">
                                <label for="referencia" class="form-label">Referencia:</label>
                                <textarea name="referencia" id="referencia" class="form-control"></textarea>
                              </div>
                            </div>
                          </div>
                          <div class="row" id="cargando-6-fomulario" style="display: none;" >
                            <div class="col-lg-12 text-center">                         
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>
                        </div>

                      </div>
                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_empresa_div" style="display: none;" >
                        <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                          <div id="barra_progress_empresa" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                        </div>
                      </div>
                      <!-- Submit -->
                      <button type="submit" style="display: none;" id="submit-form-empresa">Submit</button>
                    </form>
                  </div>
                  <!-- End::Formulario-Empresa -->

                </div> <!-- /.card-body -->

                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button type="button" class="btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                </div>

              </div> <!-- /.card -->
                 
            </div> <!-- /.col -->
          </div>
          <!-- End::row-1 -->
      
        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='Empresa'; $descripcion ='Lista de Empresa del sistema!'; $title_modulo = 'Empresa'; include("403_error.php"); }?>   

      <!-- Start::Modal-Ver-Empresa -->
      <div class="modal fade modal-effect" id="modal-empresa" tabindex="-1" aria-labelledby="modal-empresaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title title-modal-empresa" id="modal-empresaLabel1">Empresa</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row gy-2">
                <!-- :::::::::: DATOS GENERALES :::::::::::: -->
                <div class="col-lg-12 col-xl-12 col-xxl-12">
                  <div class="row">
                    <!-- Grupo -->
                    <div class="col-12 pl-0">
                      <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >DATOS GENERALES</b></label></div>
                    </div>
                  </div>
                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                    <div class="row">
                      <!-- Tipo documento -->
                      <div class="mt-4 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_tp_doc" class="form-label">Tipo documento:  </label>
                          <input type="text" id="e_tp_doc" name="e_tp_doc" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Numero documento -->
                      <div class="mt-4 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_documento" class="form-label">Número Documento:  </label>
                          <input type="text" id="e_documento" name="e_documento" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Razon Social -->
                      <div class="mt-4 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-5" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_razon_social" class="form-label">Razon Social:  </label>
                          <input type="text" id="e_razon_social" name="e_razon_social" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Nombre Comercial -->
                      <div id="div-nomb-comercial" class="mt-2 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-5" style="margin-left: 0.6cm; display: none;">
                        <div class="form-group">
                          <label for="e_nomb_comercial" class="form-label">Nombre Comercial:  </label>
                          <input type="text" id="e_nomb_comercial" name="e_nomb_comercial" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Telefono 1 -->
                      <div class="mt-2 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_telefono1" class="form-label">Telefono 1:  </label>
                          <input type="text" id="e_telefono1" name="e_telefono1" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Telefono 2 -->
                      <div id="div-telefono2" class="mt-2 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-3" style="margin-left: 0.6cm; display: none;">
                        <div class="form-group">
                          <label for="e_telefono2" class="form-label">Telefono 2:  </label>
                          <input type="text" id="e_telefono2" name="e_telefono" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Correo -->
                      <div class="mt-2 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-5" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_correo" class="form-label">Correo:  </label>
                          <input type="text" id="e_correo" name="e_correo" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Web -->
                      <div class="mt-2 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-5" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_web" class="form-label">Web:  </label>
                          <input type="text" id="e_web" name="e_web" class="form-control" readonly/>
                        </div>                                         
                      </div>

                    </div>
                  </div>
                </div>

                <!-- :::::::::: CUENTAS BANCARIAS :::::::::::: -->
                <div class="col-lg-12 col-xl-12 col-xxl-12">
                  <div class="row">
                    <!-- Grupo -->
                    <div class="col-12 pl-0">
                      <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >CUENTAS BANCARIAS</b></label></div>
                    </div>
                  </div>
                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                    <div class="row">
                      <!-- ------------ Banco 1 ------------- -->
                      <div class="col-5 mt-4" style="margin-left: 0.6cm;">
                        <div class="rounded border p-3 border-3">
                          <h5 class="form-label text-center">BANCO 1</h5>
                          <div class="form-group mt-2">
                            <label for="e_banco1" class="form-label">Entidad Bancaria:</label>
                            <input type="text" id="e_banco1" name="e_banco1" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cuenta1" class="form-label">Cuenta:</label>
                            <input type="number" id="e_cuenta1" name="e_cuenta1" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cci1" class="form-label">CCI:</label>
                            <input type="number" id="e_cci1" name="e_cci1" class="form-control" readonly/>
                          </div>
                        </div>
                      </div>
                      <!-- ------------ Banco 2 ------------- -->
                      <div class="col-5 mt-4 div-banco2" style="margin-left: 0.6cm; display: none;">
                        <div class="rounded border p-3 border-3">
                          <h5 class="form-label text-center">BANCO 2</h5>
                          <div class="form-group mt-2">
                            <label for="e_banco2" class="form-label">Entidad Bancaria:</label>
                            <input type="text" id="e_banco2" name="e_banco2" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cuenta2" class="form-label">Cuenta:</label>
                            <input type="number" id="e_cuenta2" name="e_cuenta2" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cci2" class="form-label">CCI:</label>
                            <input type="number" id="e_cci2" name="e_cci2" class="form-control" readonly/>
                          </div>
                        </div>
                      </div>
                      <!-- ------------ Banco 3 ------------- -->
                      <div class="col-5 mt-4 div-banco3" style="margin-left: 0.6cm; display: none;">
                        <div class="rounded border p-3 border-3">
                          <h5 class="form-label text-center">BANCO 3</h5>
                          <div class="form-group mt-2">
                            <label for="e_banco3" class="form-label">Entidad Bancaria:</label>
                            <input type="text" id="e_banco3" name="e_banco3" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cuenta3" class="form-label">Cuenta:</label>
                            <input type="number" id="e_cuenta3" name="e_cuenta3" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cci3" class="form-label">CCI:</label>
                            <input type="number" id="e_cci3" name="e_cci3" class="form-control" readonly/>
                          </div>
                        </div>
                      </div>
                      <!-- ------------ Banco 4 ------------- -->
                      <div class="col-5 mt-4 div-banco4" style="margin-left: 0.6cm; display: none;">
                        <div class="rounded border p-3 border-3">
                          <h5 class="form-label text-center">BANCO 4</h5>
                          <div class="form-group mt-2">
                            <label for="e_banco4" class="form-label">Entidad Bancaria:</label>
                            <input type="text" id="e_banco4" name="e_banco4" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cuenta4" class="form-label">Cuenta:</label>
                            <input type="number" id="e_cuenta4" name="e_cuenta4" class="form-control" readonly/>
                          </div>
                          <div class="form-group mt-2">
                            <label for="e_cci4" class="form-label">CCI:</label>
                            <input type="number" id="e_cci4" name="e_cci4" class="form-control" readonly/>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- :::::::::: UBICACIÓN :::::::::::: -->
                <div class="col-lg-12 col-xl-12 col-xxl-12">
                  <div class="row">
                    <!-- Grupo -->
                    <div class="col-12 pl-0">
                      <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >UBICACIÓN</b></label></div>
                    </div>
                  </div>
                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                    <div class="row">
                      <!-- Cidigo pais -->
                      <div class="mt-4 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_codg_pais" class="form-label">Código País:  </label>
                          <input type="text" id="e_codg_pais" name="e_codg_pais" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- ubigue -->
                      <div class="mt-4 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_codg_ubigeo" class="form-label">Ubigeo:  </label>
                          <input type="text" id="e_codg_ubigeo" name="e_codg_ubigeo" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Domicilio Fiscal -->
                      <div class="mt-4 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-5" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_domicilio_fiscal" class="form-label">Domicilio Fiscal:  </label>
                          <input type="text" id="e_domicilio_fiscal" name="e_domicilio_fiscal" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Distrito -->
                      <div class="mt-2 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_distrito" class="form-label">Distrito:  </label>
                          <input type="text" id="e_distrito" name="e_distrito" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- departamento -->
                      <div class="mt-2 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_departamento" class="form-label">Departamento:  </label>
                          <input type="text" id="e_departamento" name="e_departamento" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- provincia -->
                      <div class="mt-2 mb-2 col-md-2 col-lg-2 col-xl-2 col-xxl-3" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_provincia" class="form-label">provincia:  </label>
                          <input type="text" id="e_provincia" name="e_provincia" class="form-control" readonly/>
                        </div>                                         
                      </div>
                      <!-- Referencia -->
                      <div class="mt-2 mb-2 col-md-4 col-lg-4 col-xl-4 col-xxl-5" style="margin-left: 0.6cm;">
                        <div class="form-group">
                          <label for="e_referencia" class="form-label">Referencia:  </label>
                          <input type="text" id="e_referencia" name="e_referencia" class="form-control" readonly/>
                        </div>                                         
                      </div>

                    </div>
                  </div>
                </div>

                <!-- :::::::::: LOGO :::::::::::: -->
                <div class="col-lg-12 col-xl-12 col-xxl-12">
                  <div class="row">
                    <!-- Grupo -->
                    <div class="col-12 pl-0">
                      <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >LOGO</b></label></div>
                    </div>
                  </div>
                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                    <div class="row">
                      <div class="mt-4" id="logo"></div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
            </div>
          </div>
        </div>
      </div>
      <!-- End::Modal-Ver-Empresa -->


      <!-- Start::Modal-Ver-Logo -->
      <div class="modal fade modal-effect" id="modal-ver-img" tabindex="-1" aria-labelledby="modal-ver-imgLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title title-modal-img" id="modal-ver-imgLabel1">Imagen</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body html_ver_img">
              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
            </div>
          </div>
        </div>
      </div>
      <!-- End::Modal-Ver-Logo -->

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/empresa.js?version_jdl=1.16"></script>
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