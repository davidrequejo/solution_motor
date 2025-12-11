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

    <?php $title_page = "Clientes";
    include("template/head.php"); ?>

    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">

    <style>
      #tabla-cliente_filter { width: calc(100% - 10px) !important; display: flex !important; justify-content: space-between !important; }
      #tabla-cliente_filter label { width: 100% !important;  }
      #tabla-cliente_filter label input { width: 100% !important; }

      #tabla_all_pagos_filter label{ width: 100% !important; }
      #tabla_all_pagos_filter label input{ width: 100% !important; }
      
    </style>

  </head>

  <body id="body-usuario">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['cliente']==1) { ?>

      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">
            
          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2); limpiar_cliente();"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar</button>
                <button type="button" class="btn btn-danger btn-cancelar btn-regresar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar-cobro m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0 title-body-pagina">Lista de clientes!  </p>
                  <span class="fs-semibold text-muted detalle-body-pagina">Adminstra de manera eficiente tus clientes.</span>
                </div>
              </div>
            </div>

            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Zonas</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->          
          <div class="row">

            <!-- ::::::::::::::::::: VER TABLA PRINCIPAL ::::::::::::::::::: -->
            <div class="col-xxl-12 col-xl-12 " id="div-tabla-principal">          
              <div class="card custom-card">
                <div class="p-3" >
                  <div class="activar-scroll-x-auto scroll-sm">
                    
                    <!-- ::::::::::::::::::::: FILTRO TIPO DE PERSONA :::::::::::::::::::::: -->
                    <div style="width: 150px;  min-width: 150px;">
                      <div class="form-group">
                        <label for="filtro_tipo_persona" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_tipo_persona');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Tipo Persona
                          <span class="charge_filtro_tipo_persona"></span>
                        </label>
                        <select class="form-control" name="filtro_tipo_persona" id="filtro_tipo_persona" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > 
                          <option value="NATURAL">NATURAL</option>
                          <option value="JURÍDICA">JURÍDICA</option>
                        </select>
                      </div>
                    </div> 
                    <!-- ::::::::::::::::::::: FILTRO CENTRO POBLADO :::::::::::::::::::::: -->
                    <div style="width: 250px;  min-width: 250px;">
                      <div class="form-group">
                        <label for="filtro_centro_poblado" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_centro_poblado');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Centro Poblado
                          <span class="charge_filtro_centro_poblado"></span>
                        </label> 
                        <select class="form-control" name="filtro_centro_poblado" id="filtro_centro_poblado" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > </select>
                      </div>
                    </div>                     
                  </div>
                </div>
                <div class="card-body">                      
                      
                  <nav class="nav bg-light border-2 p-2 nav-style-6 nav-pills mb-3 nav-justified d-sm-flex d-block" role="tablist">
                    <a class="nav-link " data-bs-toggle="tab" role="tab" aria-current="page" href="#nav-deudores" aria-selected="false" onclick="filtrar_grupo('tabla_deudores');" >Sin pagos <span class="cant-span-sinpagos badge bg-danger-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link " data-bs-toggle="tab" role="tab" href="#nav-deudores-parcial" aria-selected="true" onclick="filtrar_grupo('tabla_deudores_parcial');">Con Pagos Parciales <span class="cant-span-conpagosparciales badge bg-info-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link" data-bs-toggle="tab" role="tab" href="#nav-sin-deuda" aria-selected="false" onclick="filtrar_grupo('tabla_no_deuda');">Con pagos Completos <span class="cant-span-conpagostotal badge bg-info-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#nav-todos" aria-selected="false" onclick="filtrar_grupo('tabla_todos');">Todos <span class="cant-span-total badge bg-info-transparent ms-1 border border-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                  </nav>
                  <div class="tab-content">
                    <div class="tab-pane  text-muted " id="nav-deudores" role="tabpanel">
                      <div class="row">
                        <div class="col-lg-4">                        
                          <div  class="table-responsive">
                            <table id="tabla-cliente-deudor" class="table table-bordered w-100" style="width: 100%;">
                              <thead >                                
                                <tr>
                                  <th class="text-center">#</th>                                 
                                  <th>Cliente</th>                                                        
                                  <th>Deuda</th>                                                               
                                  <th class="text-center">Acciones</th>
                                  <th>Lugar/Direccion</th>                                    
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>                                 
                                  <th>Cliente</th>                                                        
                                  <th>Deuda</th>                                                               
                                  <th class="text-center">Acciones</th>
                                  <th>Lugar/Direccion</th>                                    
                                </tr>
                              </tfoot>
                            </table>
                          </div>   
                        </div>
                        <!-- End::col -->
                        <div class="col-lg-8 ">
                          <!-- Start::row-1 -->
                          <div class="row" id="detalle-cliente-deudor">

                            <div class="col-xl-4">
                              <div class="card border-0">
                                <div class="alert alert-danger border border-danger mb-0 p-2">
                                  <div class="d-flex align-items-start">
                                    <div class="me-2"><i class="fe fe-alert-octagon" style="font-size: x-large !important;"></i></div>
                                    <div class="text-danger w-100">
                                      <div class="fw-semibold d-flex justify-content-between">
                                        No hay Selección <button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
                                      </div>
                                      <div class="fs-12 op-8 mb-1">Seleccione un cliente para ver los detalles de su deuda.</div>
                                      <div class="fs-12  text-right"><a href="javascript:void(0);" class="text-danger fw-semibold" data-bs-dismiss="alert" aria-label="Close">Close</a></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>
                          <!--End::row-1 -->
                        </div>
                        <!-- End::col -->
                      </div>
                    </div>
                    <!-- End::tab-pane -->
                    <div class="tab-pane  text-muted" id="nav-deudores-parcial" role="tabpanel">
                      <div class="row">
                        <div class="col-lg-4">                        
                          <div  class="table-responsive">
                            <table id="tabla-cliente-deudor-parcial" class="table table-bordered w-100" style="width: 100%;">
                              <thead >                                
                                <tr>
                                  <th class="text-center">#</th>                                 
                                  <th>Cliente</th>                                                        
                                  <th>Cobrado</th>                                                               
                                  <th class="text-center">Acciones</th>
                                  <th>Trabajador</th>     
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>                                 
                                  <th>Cliente</th>                                                        
                                  <th>Cobrado</th>                                                               
                                  <th class="text-center">Acciones</th>
                                  <th>Trabajador</th>   
                                </tr>
                              </tfoot>
                            </table>
                          </div>   
                        </div>
                        <!-- End::col -->
                        <div class="col-lg-8">
                          <!-- Start::row-1 -->
                          <div class="row" id="detalle-cliente-no-deudor">

                            <div class="col-xl-4">
                              <div class="card border-0">
                                <div class="alert alert-success border border-success mb-0 p-2">
                                  <div class="d-flex align-items-start">
                                    <div class="me-2"><i class="fe fe-alert-octagon" style="font-size: x-large !important;"></i></div>
                                    <div class="text-success w-100">
                                      <div class="fw-semibold d-flex justify-content-between">
                                        No hay Selección <button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
                                      </div>
                                      <div class="fs-12 op-8 mb-1">Seleccione un cliente para ver los detalles.</div>
                                      <div class="fs-12  text-right"><a href="javascript:void(0);" class="text-success fw-semibold" data-bs-dismiss="alert" aria-label="Close">Close</a></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>
                          <!--End::row-1 -->
                        </div>
                        <!-- End::col -->
                      </div>                      
                    </div>
                    <!-- End::tab-pane -->
                    <div class="tab-pane text-muted" id="nav-sin-deuda" role="tabpanel">
                      <div class="row">
                        <div class="col-lg-4">                        
                          <div  class="table-responsive">
                            <table id="tabla-cliente-no-deudor" class="table table-bordered w-100" style="width: 100%;">
                              <thead >                                
                                <tr>
                                  <th class="text-center">#</th>                                 
                                  <th>Cliente</th>                                                        
                                  <th>Cobrado</th>                                                               
                                  <th class="text-center">Acciones</th>
                                  <th>Trabajador</th>     
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th class="text-center">#</th>                                 
                                  <th>Cliente</th>                                                        
                                  <th>Cobrado</th>                                                               
                                  <th class="text-center">Acciones</th>
                                  <th>Trabajador</th>   
                                </tr>
                              </tfoot>
                            </table>
                          </div>   
                        </div>
                        <!-- End::col -->
                        <div class="col-lg-8">
                          <!-- Start::row-1 -->
                          <div class="row" id="detalle-cliente-no-deudor">

                            <div class="col-xl-4">
                              <div class="card border-0">
                                <div class="alert alert-success border border-success mb-0 p-2">
                                  <div class="d-flex align-items-start">
                                    <div class="me-2"><i class="fe fe-alert-octagon" style="font-size: x-large !important;"></i></div>
                                    <div class="text-success w-100">
                                      <div class="fw-semibold d-flex justify-content-between">
                                        No hay Selección <button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
                                      </div>
                                      <div class="fs-12 op-8 mb-1">Seleccione un cliente para ver los detalles.</div>
                                      <div class="fs-12  text-right"><a href="javascript:void(0);" class="text-success fw-semibold" data-bs-dismiss="alert" aria-label="Close">Close</a></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>
                          <!--End::row-1 -->
                        </div>
                        <!-- End::col -->
                      </div>
                    </div>   
                    <!-- End::tab-pane -->                 
                    <div class="tab-pane show active text-muted" id="nav-todos" role="tabpanel">
                      <div  class="table-responsive">
                        <table id="tabla-cliente" class="table table-bordered w-100" style="width: 100%;">
                          <thead class="buscando_tabla">
                            <tr id="id_buscando_tabla"> 
                              <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                            </tr>
                            <tr>
                              <th class="text-center">#</th>
                              <th class="text-center">OP</th>                              
                              <th>Cliente</th>                              
                              <th>Lugar/Direccion</th>
                              <th>Observacion</th>

                              <th>Cliente</th>
                              <th>Tipo</th>
                              <th>Numero Doc.</th>
                              <th>Centro poblado</th>
                              <th>Dirección</th>

                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-center">#</th>
                              <th class="text-center">OP</th>                              
                              <th>Cliente</th>                              
                              <th>Lugar/Direccion</th>
                              <th>Observacion</th>

                              <th>Cliente</th>
                              <th>Tipo</th>
                              <th>Numero Doc.</th>
                              <th>Centro poblado</th>
                              <th>Dirección</th>

                            </tr>
                          </tfoot>
                        </table>
                      </div>    
                    </div>
                    <!-- End::tab-pane -->
                  </div>                    
                  <!-- End::tab-content -->                                 
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                </div>
              </div>
              
            </div>                       

            <!-- ::::::::::::::::::: FORMULARIO ::::::::::::::::::: -->
            <div class="col-xxl-12 col-xl-12 " id="div-form-cliente" style="display: none;">          
              <div class="card custom-card">                
                <div class="card-body">                  
                 
                  <form name="form-agregar-cliente" id="form-agregar-cliente" method="POST">

                    <div class="row" id="cargando-1-formulario">

                      <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">

                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS PERSONALES</b></label></div>
                          </div>
                        </div>

                        <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">

                          <div class="row ">

                            <input type="hidden" id="idpersona" name="idpersona">
                            <input type="hidden" id="idtipo_persona" name="idtipo_persona" value="3">
                            <input type="hidden" id="idbancos" name="idbancos" value="1">
                            <input type="hidden" id="idcargo_trabajador" name="idcargo_trabajador" value="1">
                            <!-- ----------- -->

                            <input type="hidden" id="idpersona_cliente" name="idpersona_cliente">

                            <!-- TIPO PERSONA -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="nombre_razonsocial">Tipo Persona: <sup class="text-danger">*</sup></label>
                                <select name="tipo_persona_sunat" id="tipo_persona_sunat" class="form-control" placeholder="Tipo Persona">
                                  <option value="NATURAL">NATURAL</option>
                                  <option value="JURÍDICA">JURÍDICA</option>
                                </select>
                              </div>
                            </div>

                            <!-- Tipo Doc -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="tipo_documento">Tipo Doc. <sup class="text-danger">*</sup></label>
                                <select name="tipo_documento" id="tipo_documento" class="form-control" placeholder="Tipo de documento" ></select>
                              </div>
                            </div>

                            <!-- N° de documento -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="numero_documento">N° de documento <sup class="text-danger">*</sup></label>
                                <div class="input-group ">
                                  <input type="text" class="form-control" name="numero_documento" id="numero_documento" placeholder="" aria-describedby="icon-view-password">
                                  <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-cliente', '_t', '#tipo_documento', '#numero_documento', '#nombre_razonsocial', '#apellidos_nombrecomercial', '#direccion', '#distrito' );">
                                    <i class='bx bx-search-alt' id="search_t"></i>
                                    <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                  </button>
                                </div>
                              </div>
                            </div>

                            <!-- Nombre -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mt-2" >
                              <div class="form-group">
                                <label class="form-label nombre_razon" for="nombre_razonsocial">Nombres <sup class="text-danger">*</sup></label>
                                <textarea name="nombre_razonsocial" class="form-control inpur_edit" id="nombre_razonsocial" rows="2" ></textarea>                                
                              </div>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mt-2" >
                              <div class="form-group">
                                <label class="form-label apellidos_nombrecomer" for="apellidos_nombrecomercial">Apellidos <sup class="text-danger">*</sup></label>
                                <textarea name="apellidos_nombrecomercial" class="form-control inpur_edit" id="apellidos_nombrecomercial" rows="2"></textarea>
                              </div>
                            </div>
                            <!-- Fecha cumpleaño -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-5 col-xl-5 col-xxl-5 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="fecha_nacimiento">Fecha nacimiento </label>
                                <input type="date" name="fecha_nacimiento" class="form-control inpur_edit" id="fecha_nacimiento" placeholder="Fecha de Nacimiento" onclick="calcular_edad('#fecha_nacimiento', '#edad', '.edad');" onchange="calcular_edad('#fecha_nacimiento', '#edad', '.edad');" />
                                <input type="hidden" name="edad" id="edad" />
                              </div>
                            </div>
                            <!-- Edad -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-2 col-xl-2 col-xxl-2 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="Edad">Edad </label>
                                <p class="edad" style="border: 1px solid #ced4da; border-radius: 4px; padding: 5px;">0 años.</p>

                              </div>
                            </div>
                            <!-- Celular  -->
                            <div class="col-12 col-sm-6 col-md-12 col-lg-5 col-xl-5 col-xxl-5 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="celular">Celular </label>
                                <input type="number" name="celular" class="form-control inpur_edit" id="celular" />
                              </div>
                            </div>

                            <!-- Correo -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="Correo">Correo </label>
                                <input type="email" name="correo" id="correo" class="form-control" placeholder="Correo"></input>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>
                      <!-- --------------DIRECCION -->
                      <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">

                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>UBICACIÓN</b></label></div>
                          </div>
                        </div>

                        <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">

                          <div class="row ">

                            <!-- Dirección -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="direccion">Dirección: <sup class="text-danger">*</sup></label>
                                <textarea name="direccion" class="form-control inpur_edit" id="direccion" placeholder="ejemp: Jr las flores" rows="2"></textarea>
                              </div>
                            </div>

                            <!-- Dirección -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="direccion_referencia">Referencia: <sup class="text-danger">*</sup></label>
                                <textarea name="direccion_referencia" class="form-control inpur_edit" id="direccion_referencia" placeholder="ejemp: Al costado del colegio" rows="2"></textarea>
                              </div>
                            </div>
                            <!-- Select centro poblado -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="centro_poblado">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('centroPbl');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Centro Poblado <sup class="text-danger">*</sup><span class="charge_idctroPbl"></span></label>
                                <select name="centro_poblado" id="centro_poblado" class="form-control" placeholder="Selecionar"></select>
                              </div>
                            </div> 

                            <!-- Distrito -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="distrito" class="form-label">Distrito: </label></label>
                                <select name="distrito" id="distrito" class="form-control" placeholder="Seleccionar" onchange="llenar_dep_prov_ubig(this);">
                                </select>
                              </div>
                            </div>
                            <!-- Departamento -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="departamento" class="form-label">Departamento: <span class="chargue-pro"></span></label>
                                <input type="text" class="form-control" name="departamento" id="departamento" readonly>
                              </div>
                            </div>
                            <!-- Provincia -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="provincia" class="form-label">Provincia: <span class="chargue-dep"></span></label>
                                <input type="text" class="form-control" name="provincia" id="provincia" readonly>
                              </div>
                            </div>
                            <!-- Ubigeo -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="ubigeo" class="form-label">Ubigeo: <span class="chargue-ubi"></span></label>
                                <input type="text" class="form-control" name="ubigeo" id="ubigeo" readonly>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>

                      <div class="col-12 col-md-12">

                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS TÉCNICOS </b>
                          </label></div>
                          </div>
                        </div>

                        <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                          <div class="row">                             
                            
                            <!--NOTA -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-9 col-xxl-9" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="nota">Nota </label>
                                <textarea class="form-control inpur_edit" name="nota" id="nota" cols="30" rows="2" placeholder="ejemp: Se removio el servicio por deuda" ></textarea>
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>

                      <!-- Imgen -->
                      <div class="col-md-4 col-lg-4 mt-4">
                        <span class=""> <b>Imagen de Perfil</b> </span>
                        <div class="mb-4 mt-2 d-sm-flex align-items-center">
                          <div class="mb-0 me-5">
                            <span class="avatar avatar-xxl avatar-rounded">
                              <img src="../assets/images/faces/9.jpg" alt="" id="imagenmuestra" onerror="this.src='../assets/modulo/persona/perfil/no-perfil.jpg';">
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

                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_usuario_div" style="display: none;">
                        <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                          <div id="barra_progress_usuario" class="progress-bar" style="width: 0%">
                            <div class="progress-bar-value">0%</div>
                          </div>
                        </div>
                      </div>

                    </div>

                    <div class="row" id="cargando-2-formulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" style="display: none;" id="submit-form-cliente">Submit</button>
                  </form>
                               
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                </div>
              </div>              
            </div>

            <!-- ::::::::::::::::::: REALIZAR PAGO ::::::::::::::::::: -->
            
          </div>          
          <!-- End::row-1 --> 
          
          <!-- Start::Modal-pago-cliente-x-mes -->
          <div class="modal fade" id="pago-cliente-mes" tabindex="-1" aria-labelledby="pago-cliente-mesLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="pago-cliente-mesLabel1">Pagos por Mes</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="table-responsive" id="div_tabla_pagos_Cx_mes">
                    <div class="row" >
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>                      
                  </div>
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-pago-cliente-x-mes -->

          <!-- MODAL - VER MESES COBRADOS -->
          <div class="modal fade modal-effect" id="modal-ver-meses-cobrados" role="dialog" tabindex="-1" aria-labelledby="modal-ver-meses-cobradosLabel">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-ver-meses-cobradosLabel1">Meses Cobrados</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="ver-meses-cobrados">                  
                  <div class="row" >
                    <div class="col-lg-12 text-center">
                      <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                      <h4 class="bx-flashing">Cargando...</h4>
                    </div>
                  </div>                  
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal -->

          <!-- Start::modal-imprimir_ticket -->
          <div class="modal fade" id="modal-imprimir-comprobante" tabindex="-1" aria-labelledby="modal-imprimir-comprobante-Label" aria-hidden="true">
            <div class="modal-dialog modal-md" >
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modal-imprimir-comprobante-Label"> <button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('modalAntcticket')"><i class="ri-printer-fill"></i></button> Ticket Pago Cliente</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="html-imprimir-comprobante" class="text-center" > </div>                   
                </div>
                
              </div>
            </div>
          </div>
          <!-- End::modal-imprimir_ticket -->

          <!-- MODAL - VER FOTO -->
          <div class="modal fade modal-effect" id="modal-ver-imgenes" tabindex="-1" aria-labelledby="modal-ver-imgenes" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title fs-13 title-ver-imgenes" id="modal-ver-imgenesLabel1">Imagen</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body html_modal_ver_imgenes">
                  
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal - Ver foto proveedor -->          

          <!-- MODAL - IMPRIMIR -->
          <div class="modal fade modal-effect" id="modal-imprimir-comprobante" tabindex="-1" aria-labelledby="modal-imprimir-comprobante-label" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-imprimir-comprobante-label">COMPROBANTE</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >                  
                  <div id="html-imprimir-comprobante" class="text-center" > </div>
                </div>                
              </div>
            </div>
          </div> 

          <!-- MODAL - VER DETALLE COMPROBANTE -->
          <div class="modal fade" id="modal_ver_comprobante_deudor">
            <div class="modal-dialog modal-md modal-dialog-centered text-center" role="document">
              <div class="modal-content modal-content-demo">
                <div class="modal-body text-start">
                  <div class="card custom-card">
                    <div class="card-header">
                      <div class="card-title">
                        Formatos de Comprobante <strong class="serie_comp"></strong>.
                      </div>
                    </div>
                    <div class="card-body">
                      <nav class="nav nav-style-6 nav-pills mb-3 nav-justified d-sm-flex d-block" role="tablist">
                        <a class="nav-link active btn_formato_ticket" data-bs-toggle="tab" role="tab" aria-current="page" href="#nav-products-justified" aria-selected="false">Formato Ticket</a>
                        <a class="nav-link btn_formato_a4" data-bs-toggle="tab" role="tab" href="#nav-cart-justified" aria-selected="true">Formato A4 </a>
                      </nav>
                      <div class="tab-content">
                        <div class="tab-pane show active text-muted formato_ticket" id="nav-products-justified" role="tabpanel">
                        </div>
                        <div class="tab-pane  text-muted formato_a4" id="nav-cart-justified" role="tabpanel">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          <!-- END::modal -->

          <!-- MODAL - VER RESUMEN DE PRODUCTOS VENDIDO -->
          <div class="modal fade" id="modal_ver_productos_vendidos">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
              <div class="modal-content modal-content-demo">
                <div class="modal-body text-start">
                  <div class="card custom-card">
                    <div class="card-header">
                      <div class="card-title"> Productos y Servicios Vendidos .</div>
                    </div>
                    <div class="card-body">
                      <nav class="nav nav-style-6 nav-pills mb-3 nav-venta d-sm-flex d-block" role="tablist">
                        <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#nav-resumen-venta" aria-selected="false">Resumen</a>
                        <a class="nav-link" data-bs-toggle="tab" role="tab" href="#nav-todos-venta" aria-selected="true">Todos <small>(Detallado)</small></a>
                      </nav>
                      <div class="tab-content">
                        <div class="tab-pane show active text-muted" id="nav-resumen-venta" role="tabpanel">
                          <div class="table-responsive">
                            <table id="tabla-resumen-producto-venta" class="table table-bordered w-100">
                              <thead class="text-nowrap">
                                <th>#</th>
                                <th>Nombre Producto</th>                              
                                <th>P/U. <small>(Promedio)</small> </th>
                                <th>Cant.</th>
                              </thead>
                              <tbody></tbody>
                            </table>                            
                          </div>
                        </div>
                        <div class="tab-pane  text-muted" id="nav-todos-venta" role="tabpanel">
                          <div class="table-responsive">
                            <table id="tabla-todos-producto-venta" class="table table-bordered w-100">
                              <thead class="text-nowrap">
                                <th>#</th>
                                <th>Nombre Producto</th>                              
                                <th>P/Unit.</th>
                                <th>Cant.</th>
                                <th>Comprobante</th>
                                <th>Emisión.</th>
                              </thead>
                              <tbody></tbody>
                            </table>                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-sm btn-secondary"   data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          <!-- END::modal -->

        </div>
      </div>
      <!-- End::app-content -->

      <?php } else { $title_submodulo ='Clientes'; $descripcion ='Lista de Clientes del sistema!'; $title_modulo = 'Ventas'; include("403_error.php"); }?>   


      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

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

    <!-- Dropzone JS -->
    <script src="../assets/libs/dropzone/dropzone-min.js"></script>
    <!-- Google Maps API -->
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyCW16SmpzDNLsrP-npQii6_8vBu_EJvEjA"></script>
    <!-- Google Maps JS -->
    <script src="../assets/libs/gmaps/gmaps.min.js"></script>

    <script src="scripts/cliente.js?version_jdl=1.16"></script>
    <!-- <script src="scripts/js_facturacion_cliente.js?version_jdl=1.16"></script> -->
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