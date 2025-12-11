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
    <?php $title_page = "Guia de Remision";
    include("template/head.php"); ?>
    <style>
      /* #tabla-guia-remision td {
        vertical-align: middle !important;
        line-height: 1.462 !important;
        font-size: .6875rem !important;
        font-weight: 50 !important;
      } */

      #tabla-guia-remision_filter {
        width: calc(100% - 10px) !important; /* Asegúrate de que el contenedor ocupe el 100% del espacio disponible */
        display: flex !important; /* Usamos flexbox para que los elementos se ajusten dentro del contenedor */
        justify-content: space-between !important; /* Espacio entre el label y el input, si lo deseas */
      }
      #tabla-guia-remision_filter label {
        width: 100% !important;
      }
      #tabla-guia-remision_filter label input {
        width: 100% !important;
      }
    </style>
  </head>

  <body id="body-compras">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['facturacion']==1) { ?>
      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_guia(); "  > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Guía de Remisión Remitente</p>
                  <span class="fs-semibold text-muted">Administra las guías.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Lista de guías</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Guías</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">
            <div class="col-xxl-12 col-xl-12" id="div-tabla" >
              <div class="card custom-card">
                <div class="p-3 " > 
                  <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-11 col-xl-11">
                      <div class="activar-scroll-x-auto scroll-sm">                                             
                        <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                        <div style="width: 250px;  min-width: 200px;">
                          <div class="form-group">
                            <label for="filtro_fecha_i" class="form-label">
                              <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_i();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                              Fecha Inicio</label>
                            <input type="date" class="form-control" name="filtro_fecha_i" id="filtro_fecha_i" value="<?php echo date("Y-m-d"); ?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">
                          </div>
                        </div>
                        <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                        <div style="width: 250px;  min-width: 200px;">
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
                                                  
                      </div> 
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-1 col-xl-1 text-center">
                      <div class="dropdown ms-2">
                        <button class="btn btn-icon btn-secondary-light btn-sm btn-wave waves-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu otros-filtros">
                          <li><a class="dropdown-item o-f-poren" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('POR ENVIAR', '.o-f-poren')"><i class="ri-check-fill align-middle me-1"></i> Por enviar</a></li>
                          <li><a class="dropdown-item o-f-noen" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('NO ENVIADO', '.o-f-noen')"><i class="ri-check-fill align-middle me-1"></i> No enviado</a></li>
                          <li><a class="dropdown-item o-f-ac" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('ACEPTADA', '.o-f-ac')"><i class="ri-check-fill align-middle me-1"></i> Solo aceptados</a></li>
                          <li><a class="dropdown-item o-f-an" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('ANULADO', '.o-f-an')"><i class="ri-close-fill align-middle me-1"></i> Solo anulados</a></li>
                          <li><a class="dropdown-item o-f-to active" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('', '.o-f-to')"><i class="bi bi-border-all align-middle me-1"></i> Todos</a></li>
                          <!-- <li>
                            <hr class="dropdown-divider">
                          </li> -->
                          <!-- <li><a class="dropdown-item" href="javascript:void(0);" onclick="view_mas_detalle();"><i class="bi bi-list-check"></i> Ver mas detalles</a></li> -->
                        </ul>
                      </div>
                    </div>
                  </div>                                                    
                </div>
                <div class="card-body">
                  <!-- ------------ Tabla de Compras ------------- -->
                  <div class="table-responsive" >
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-guia-remision">
                      <thead>
                        <tr>
                          <th class="text-center"><center>#</center></th>
                          <th class="text-center"><center>OP</center></th>
                          <th class="text-center"><center>ID</center></th>
                          <th>Creación</th>
                          <th>Cliente</th>
                          <th>Correlativo</th>
                          <th class="text-nowrap">Conductor <span style="color: transparent;" >--------</span></th>
                          <th>Público/Privado</th>
                          
                          <th>Creador</th>
                          <th><center>SUNAT</center></th>                          
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
                          <th>Conductor</th>
                          <th>Público/Privado</th>                          
                          <th>Creador</th>
                          <th class="text-center"><center>SUNAT</center></th>                          
                          <th><center>Estado</center></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>              
            </div>

            <div class="col-xxl-12 col-xl-12" id="div-formulario" style="display: none;">              
              <div class="card custom-card">                
                <div class="card-body"> 
                  <!-- FORM - COMPROBANTE -->                  
                  <form name="form-agregar-guia-remsion" id="form-agregar-guia-remsion" method="POST" >
                    <div class="row gy-2" id="cargando-1-formulario">
                      <input type="hidden" name="idventa" id="idventa" />
                      <input type="hidden" name="f_tipo_comprobante" id="f_tipo_comprobante" value="09">

                      <div class="col-lg-6">                         
                        <div class="row">    
                          
                          <!-- ENVIO AUTOMATICO -->
                          <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 ">
                            <div class="custom-toggle-switch d-flex align-items-center mb-1">
                              <input id="f_crear_y_emitir" name="f_crear_y_emitir" type="checkbox"  value="SI" >
                              <label for="f_crear_y_emitir" class="label-warning"></label><span class="ms-3 fs-11">SUNAT <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" title="Esta opción debe estar siempre ACTIVA para Boleta, Factura, Guias o Nota Cred. de lo contrario se adjuntara a envios masivos a SUNAT."></i></span>
                            </div>
                          </div>
                          <!-- CREAR Y MOSTRAR-->
                          <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 ">
                            <div class="custom-toggle-switch d-flex align-items-center mb-1">
                              <input id="f_crear_y_mostrar" name="f_crear_y_mostrar" type="checkbox" checked="" value="SI">
                              <label for="f_crear_y_mostrar" class="label-warning"></label><span class="ms-3 fs-11">Crear y mostrar <i class="bi bi-exclamation-circle" data-bs-toggle="tooltip" data-bs-html="true"  title="Esta opción te mostrara en automático el comprobante para: 🔸Enviarlo al cliente 🔸Descargarlo 🔸Imprimirlo"></i></span> 
                            </div>
                          </div>

                          <div class="col-12">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DOCUMENTO</b></label></div>
                              </div>
                            </div>
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">                                  
                                <!-- ----------------- SERIE --------------- -->
                                <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                                  <div class="form-group">
                                    <label for="serie_comprobante" class="form-label">Numero-Serie</label>
                                    <select name="serie_comprobante" id="serie_comprobante" class="form-control" placeholder="Tipo de documento" ></select>
                                  </div>
                                </div>
                                <!-- Tipo Doc -->
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 " >
                                  <div class="form-group">
                                    <label class="form-label" for="modalidad_transporte">Modalidad. <sup class="text-danger">*</sup></label>
                                    <select name="modalidad_transporte" id="modalidad_transporte" class="form-control" placeholder="Tipo de documento" ></select>
                                  </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 " >
                                  <div class="form-group">
                                    <label class="form-label" for="motivo_traslado">Motivo. <sup class="text-danger">*</sup></label>
                                    <select name="motivo_traslado" id="motivo_traslado" class="form-control" placeholder="Tipo de documento" ></select>
                                  </div>
                                </div>
                              </div> 
                              <!-- End::row -->
                            </div>
                          </div>

                          <div class="col-12">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS DE CLIENTE</b></label></div>
                              </div>
                            </div>
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">
                                <!-- ----------------- ASOCIAR DOCUMENTO --------------- -->
                                <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="documento_asociado" class="form-label">                                          
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_documento_asociado();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Asociar Documento
                                      <span class="charge_documento_asociado"></span>
                                    </label>
                                    <select class="form-control" name="documento_asociado" id="documento_asociado"></select>
                                  </div>
                                </div>
                                <!-- ----------------- PROVEEDOR --------------- -->
                                <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="idcliente" class="form-label">
                                      <!-- <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_trabajador(); limpiar_proveedor();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idcliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Cliente
                                      <span class="charge_idcliente"></span>
                                    </label>
                                    <select class="form-control" name="idcliente" id="idcliente"></select>
                                  </div>
                                </div>
                              </div>
                              <!-- End::row -->                                  
                            </div>
                          </div>

                          <div class="col-12">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS DE TRASLADO</b></label></div>
                              </div>
                            </div>
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">                                  
                                <!-- ----------- DIRECCION PARTIDA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-5 col-xxl-5">
                                  <div class="form-group">
                                    <label for="partida_direccion" class="form-label">Direccion de Partida</label>
                                    <textarea name="partida_direccion" id="partida_direccion" class="form-control" rows="2" placeholder="ejemp: JR. LOS JARDINES." onkeyup="mayus(this);" ><?php echo $_SESSION['empresa_df'] ;?></textarea>                                        
                                  </div>
                                </div>
                                <!-- ----------- DISTRITO PARTIDA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-4">
                                  <div class="position-relative" >                                      
                                    <div class="form-group">
                                      <label for="partida_distrito" class="form-label">Distrito Partida</label>
                                      <input type="text" class="form-control" name="partida_distrito" id="partida_distrito" value="<?php echo $_SESSION['empresa_distrito'];?>" onkeyup="mayus(this);" >
                                    </div>
                                    <ul id="search_distrito_partida" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                  </div>
                                </div>
                                <!-- ----------- UBIGEO PARTIDA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                                  <div class="form-group">
                                    <label for="partida_ubigeo" class="form-label">Ubigeo Partida</label>
                                    <input type="text" class="form-control" name="partida_ubigeo" id="partida_ubigeo" value="<?php echo $_SESSION['empresa_codubigueo'];?>" >
                                  </div>
                                </div>
                                
                                <!-- ----------- DIRECCION LLEGADA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-5 col-xxl-5">
                                  <div class="form-group">
                                    <label for="llegada_direccion" class="form-label">Direccion de Llegada</label>
                                    <textarea name="llegada_direccion" id="llegada_direccion" class="form-control" rows="2" placeholder="ejemp: JR. LOS JARDINES." onkeyup="mayus(this);" ></textarea>                                        
                                  </div>
                                </div>
                                <!-- ----------- DISTRITO LLEGADA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-4">
                                  <div class="position-relative" >                                      
                                    <div class="form-group">
                                      <label for="llegada_distrito" class="form-label">Distrito Llegada</label>
                                      <input type="text" class="form-control" name="llegada_distrito" id="llegada_distrito" onkeyup="mayus(this);">                                        
                                    </div>
                                    <ul id="search_distrito_llegada" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                  </div>
                                </div>
                                <!-- ----------- UBIGEO LLEGADA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-3">
                                  <div class="form-group">
                                    <label for="llegada_ubigeo" class="form-label">Ubigeo Llegada</label>
                                    <input type="text" class="form-control" name="llegada_ubigeo" id="llegada_ubigeo" >
                                  </div>
                                </div>

                                <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-4">
                                  <div class="form-group">
                                    <label for="llegada_ubigeo" class="form-label">Peso Total(KGM)</label>
                                    <input type="number" class="form-control" name="peso_total" id="peso_total" min="0.01" step="0.01" required>
                                  </div>
                                </div>
                                
                              </div>
                              <!-- End::row -->
                            </div>
                            <!-- End::card-body -->
                          </div>

                          <div class="col-12">
                            <div class="row">
                              <!-- Grupo -->
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>UNIDAD DE TRANSPORTE Y EL CONDUCTOR</b></label></div>
                              </div>
                            </div>
                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row">
                                <input type="hidden" name="gr_tipo_documento" id="gr_tipo_documento" value="1">
                                <!-- ----------------- CHOFER PUBLICO --------------- -->
                                <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 div_motivo_publico" style="display: none;">
                                  <div class="form-group">
                                    <label for="idpersona_chofer" class="form-label">
                                      <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_chofer_publico(); limpiar_chofer_publico();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idpersona_chofer();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Conductor Publico
                                      <span class="charge_idpersona_chofer"></span>
                                    </label>
                                    <select class="form-control" name="idpersona_chofer" id="idpersona_chofer"></select>
                                  </div>
                                </div>

                                <!-- N° de documento -->
                                <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 div_motivo_privado" >
                                  <div class="form-group">
                                    <label class="form-label" for="numero_documento">N° de documento <sup class="text-danger">*</sup></label>
                                    <div class="position-relative">                                        
                                      <div class="input-group ">
                                        <input type="number" class="form-control" name="numero_documento" id="numero_documento" placeholder="" aria-describedby="icon-view-password">
                                        <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-guia-remsion', '_t', '#gr_tipo_documento', '#numero_documento', '#nombre_razonsocial', '#apellidos_nombrecomercial', '#direccion', '#distrito' );" data-bs-toggle="tooltip" data-bs-title="Buscar Reniec.">
                                          <i class='bx bx-search-alt' id="search_t"></i>
                                          <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                        </button>
                                      </div>
                                      <ul id="search_documento_conductor" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                    </div>
                                  </div>
                                </div>
                                <!-- ----------- NRO LICENCIA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-4 div_motivo_privado">
                                  <div class="form-group">
                                    <label for="numero_licencia" class="form-label">Nro Licencia</label>
                                    <input type="text" class="form-control" name="numero_licencia" id="numero_licencia" onkeyup="mayus(this);">
                                  </div>
                                </div>
                                <!-- ----------- NRO LICENCIA ----------- -->
                                <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-4">
                                  <div class="form-group">
                                    <label for="numero_placa" class="form-label">Placa Vehiculo(XXX-1234)</label>
                                    <input type="text" class="form-control" name="numero_placa" id="numero_placa"  placeholder="Ej: ABC-123456">
                                  </div>
                                </div>
                                <!-- Nombre -->
                                <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 div_motivo_privado" >
                                  <div class="position-relative" >                                      
                                    <div class="form-group">
                                      <label class="form-label nombre_razon" for="nombre_razonsocial">Nombres <sup class="text-danger">*</sup></label>
                                      <textarea name="nombre_razonsocial" class="form-control inpur_edit" id="nombre_razonsocial" rows="2" onkeyup="mayus(this);" ></textarea>                                
                                    </div>
                                    <ul id="search_nombre_conductor" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                  </div>
                                </div>

                                <!-- Apellidos -->
                                <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 div_motivo_privado" >
                                  <div class="form-group">
                                    <label class="form-label apellidos_nombrecomer" for="apellidos_nombrecomercial">Apellidos <sup class="text-danger">*</sup></label>
                                    <textarea name="apellidos_nombrecomercial" class="form-control inpur_edit" id="apellidos_nombrecomercial" rows="2" onkeyup="mayus(this);" ></textarea>
                                  </div>
                                </div>
                                

                              </div>
                              <!-- End::row -->
                            </div>  
                            <!-- End::card-body -->
                          </div>                              

                        </div>                         
                          
                      </div>

                      <div class="col-lg-6">
                        <div class="row">                               

                          <div class="col-md-12 col-lg-6 col-xl-10 col-xxl-10">
                            <div class="position-relative">                                
                              <div class="input-group">                              
                                <button type="button" class="input-group-text buscar_x_code" onclick="listar_producto_x_codigo();"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Buscar por codigo de producto."><i class='bx bx-search-alt'></i></button>
                                <input type="text" name="search_producto" id="search_producto" class="form-control form-control-sm" onkeyup="mayus(this);" placeholder="Digite el código de producto." >
                              </div>
                              <ul id="searchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                            </div>
                          </div>

                          <!-- ------- TABLA PRODUCTOS SELECCIONADOS ------ --> 
                          <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive pt-3">
                            <table id="tabla-productos-seleccionados" class="table table-striped table-bordered table-condensed table-hover">
                              <thead class="bg-color-dark text-white">
                                <th class="py-1" data-toggle="tooltip" data-original-title="Opciones">Op.</th>
                                <th class="py-1">Producto</th>
                                <th class="py-1">Cantidad</th>                                        
                                <th class="py-1" data-toggle="tooltip" data-original-title="Precio Unitario">P/U</th>
                                <th class="py-1">Subtotal</th>
                                <th class="py-1 text-center" ><i class='bx bx-cog fs-4'></i></th>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <td colspan="3"></td>

                                <th class="text-right">
                                  <h6 class="fs-11 tipo_gravada">SUBTOTAL</h6>
                                  <h6 class="fs-11">DESCUENTO</h6>
                                  <h6 class="fs-11 val_igv">IGV (18%)</h6>
                                  <h5 class="fs-13 font-weight-bold">TOTAL</h5>
                                </th>
                                <th class="text-right">
                                  <h6 class="fs-11 font-weight-bold d-flex justify-content-between f_guia_subtotal"> <span>S/</span> 0.00</h6>
                                  <input type="hidden" name="f_guia_subtotal" id="f_guia_subtotal" />
                                  <input type="hidden" name="f_tipo_gravada" id="f_tipo_gravada" />

                                  <h6 class="fs-11 font-weight-bold d-flex justify-content-between f_guia_descuento"><span>S/</span> 0.00</h6>
                                  <input type="hidden" name="f_guia_descuento" id="f_guia_descuento" />

                                  <h6 class="fs-11 font-weight-bold d-flex justify-content-between f_guia_igv"><span>S/</span> 0.00</h6>
                                  <input type="hidden" name="f_guia_igv" id="f_guia_igv" />

                                  <h5 class="fs-13 font-weight-bold d-flex justify-content-between f_guia_total"><span>S/</span> 0.00</h5>
                                  <input type="hidden" name="f_guia_total" id="f_guia_total" />

                                </th>
                                <th></th>
                              </tfoot>
                            </table>
                          </div>

                          <!-- -------------- DESCRIPCION ------------- -->
                          <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 mt-2">
                            <div class="form-group">
                              <label for="gr_observacion" class="form-label">Observación</label>
                              <textarea name="gr_observacion" id="gr_observacion" class="form-control" rows="2" placeholder="ejemp: Envio de paquete."></textarea>
                            </div>
                          </div>  
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
                    <div class="p-l-25px col-lg-12" id="barra_progress_guia_div" style="display: none;" >
                      <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                        <div id="barra_progress_guia" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                      </div>
                    </div>
                    <!-- Submit -->
                    <button type="submit" style="display: none;" id="submit-form-compra">Submit</button>
                  </form>                  
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1); limpiar_form_guia();" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button type="button" class="btn btn-success btn-guardar" id="guardar_registro_guia" style="display: none;"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
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
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger py-1" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
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
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
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

          <!-- MODAL - AGREGAR CHOFER PUBLICO - charge 3,4 -->
          <div class="modal fade modal-effect" id="modal-agregar-chofer-publico" tabindex="-1" aria-labelledby="Modal-agregar-chofer-publicoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-modal-img" id="Modal-agregar-chofer-publicoLabel1">Agregar Conductor Público</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                                      
                    <form name="form-agregar-chofer-publico" id="form-agregar-chofer-publico" method="POST" class="needs-validation" novalidate>
                            
                      <div class="row" id="cargando-3-fomulario">
                        <!-- idpersona -->
                        <input type="hidden" name="cp_idpersona" id="cp_idpersona" />   
                        <input type="hidden" name="cp_tipo_persona_sunat" id="cp_tipo_persona_sunat" value="JURÍDICA" />   
                        <input type="hidden" name="cp_tipo_documento_v2" id="cp_tipo_documento_v2" value="" />   
                        <input type="hidden" name="cp_idtipo_persona" id="cp_idtipo_persona" value="5" />   

                        <div class="col-lg-12 col-xl-12 col-xxl-12">
                          <div class="row">
                            <!-- Grupo -->
                            <div class="col-12 pl-0">
                              <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >DATOS GENERALES</b></label></div>
                            </div>
                          </div> <!-- /.row -->
                          <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                            <div class="row">

                              <!-- Tipo documento -->
                              <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="cp_tipo_documento" class="form-label">Tipo documento:  </label>
                                  <select name="cp_tipo_documento" id="cp_tipo_documento" class="form-select" required>   
                                    <option value="6">RUC</option>                                                             
                                  </select>
                                </div>                                         
                              </div>
                              
                              <!--  Numero Documento -->
                              <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="cp_numero_documento" class="form-label">Numero Documento:</label>
                                  <div class="input-group">                            
                                    <input type="number" class="form-control" name="cp_numero_documento" id="cp_numero_documento" placeholder="" aria-describedby="icon-view-password">
                                    <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-chofer-publico', '_t1', '#cp_tipo_documento', '#cp_numero_documento', '#cp_nombre_razonsocial', '#cp_apellidos_nombrecomercial', '#cp_direccion', '#cp_distrito', '#cp_titular_cuenta' );" >
                                      <i class='bx bx-search-alt' id="search_t1"></i>
                                      <div class="spinner-border spinner-border-sm" role="status" id="charge_t1" style="display: none;"></div>
                                    </button>
                                  </div>
                                </div>                        
                              </div>         
                            
                              <!-- Nombres -->
                              <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="cp_nombre_razonsocial" class="form-label label-nom-raz">Nombres:  </label></label>
                                  <input type="text" class="form-control" name="cp_nombre_razonsocial" id="cp_nombre_razonsocial" onkeyup="mayus(this);">
                                </div>                                         
                              </div>

                              <!-- Apellidos -->
                              <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6 ">
                                <div class="form-group">
                                  <label for="cp_apellidos_nombrecomercial" class="form-label label-ape-come">Apellidos:  </label></label>
                                  <input type="text" class="form-control" name="cp_apellidos_nombrecomercial" id="cp_apellidos_nombrecomercial" onkeyup="mayus(this);" >
                                </div>                                         
                              </div>

                              <!-- Correo -->
                              <div class="mb-1 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="cp_correo" class="form-label">Correo:</label>
                                  <input type="email" class="form-control" name="cp_correo" id="cp_correo" >
                                </div>                                         
                              </div>

                              <!-- Celular -->
                              <div class="col-md-12 col-lg-3 col-xl-6 col-xxl-6">
                                <div class="form-group">
                                  <label for="cp_celular" class="form-label">Celular:</label>
                                  <input type="tel" class="form-control" name="cp_celular" id="cp_celular" >
                                </div>                                         
                              </div>                                   

                            </div> <!-- /.row -->
                          </div> <!-- /.card-body -->
                        </div> <!-- /.col-lg-12 -->

                        <div class="col-lg-12 col-xl-12 col-xxl-12">
                          <div class="row">
                            <!-- Grupo -->
                            <div class="col-12 pl-0">
                              <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >CONDUCTOR</b></label></div>
                            </div>
                          </div> <!-- /.row -->
                          <div class="card-body p-3" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                            <div class="row">

                              <!-- numero_licencia -->
                              <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                <div class="form-group">
                                  <label for="cp_numero_licencia" class="form-label">Numero Licencia:</label>
                                  <input type="text" class="form-control" name="cp_numero_licencia" id="cp_numero_licencia">
                                </div>                                         
                              </div>
                              <!-- placa_vehiculo -->
                              <div class="mb-1 col-md-3 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                <div class="form-group">
                                  <label for="cp_placa_vehiculo" class="form-label">Placa Vehiculo: </label>
                                  <input type="text" class="form-control" name="cp_placa_vehiculo" id="cp_placa_vehiculo" >
                                </div>                                         
                              </div>
                              <!-- Departamento -->                              

                            </div> <!-- /.row -->
                          </div> <!-- /.card-body -->
                        </div> <!-- /.col-lg-12 --> 

                      </div> <!-- /.row -->

                      <div class="row" id="cargando-4-fomulario" style="display: none;" >
                        <div class="col-lg-12 text-center">                         
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>  <!-- /.row -->                                   
                      
                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_chofer_publico_div" style="display: none;" >
                        <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                          <div id="barra_progress_chofer_publico" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                        </div>
                      </div>
                      <!-- Submit -->
                      <button type="submit" style="display: none;" id="submit-form-chofer-publico">Submit</button>
                    </form>
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger"  data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                  <button type="button" class="btn btn-sm btn-success label-btn" id="guardar_registro_chofer_publico"><i class="bx bx-save bx-tada"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Agregar-chofer-publico -->
          
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

                      <!-- ----------------- Categoria --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="form-group">
                          <label for="categoria" class="form-label">Categoría</label>
                          <select class="form-control" name="categoria" id="categoria">
                            <!-- lista de categorias -->
                          </select>
                        </div>
                      </div>

                      <!-- ----------------- Unidad Medida --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="form-group">
                          <label for="u_medida" class="form-label">U. Medida</label>
                          <select class="form-control" name="u_medida" id="u_medida">
                            <!-- lista de u medidas -->
                          </select>
                        </div>
                      </div>

                      <!-- ----------------- Marca --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="form-group">
                          <label for="marca" class="form-label">Marca</label>
                          <select class="form-control" name="marca" id="marca">
                            <!-- lista de marcas -->
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

        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='Compra'; $descripcion ='Lista de Compras del sistema!'; $title_modulo = 'Compras'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>   

    <script src="scripts/guia_de_remision.js?version_jdl=1.16"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
      var p_direccion = "<?php echo $_SESSION['empresa_df']; ?>";
      var p_distrito = "<?php echo $_SESSION['empresa_distrito']; ?>";
      var p_ubigeo = "<?php echo $_SESSION['empresa_codubigueo']; ?>";
    </script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>