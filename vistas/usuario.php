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
        
        <?php $title_page = "Usuarios"; include("template/head.php"); ?>    

      </head> 

      <body id="body-usuario" idusuario="<?php echo $_SESSION['idusuario']; ?>"  > 

        <?php include("template/switcher.php"); ?>
        <?php include("template/loader.php"); ?>

        <div class="page">
          <?php include("template/header.php") ?>
          <?php include("template/sidebar.php") ?>
          <?php if($_SESSION['usuario']==1) { ?>

          <!-- Start::app-content -->
          <div class="main-content app-content">
            <div class="container-fluid">

              <!-- Start::page-header -->
              <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                  <div class="d-md-flex d-block align-items-center ">
                    <button type="button" class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2); reload_usr_trab(); limpiar_form(); reload_ps();"  > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                    <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                    <button type="button" class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                    <div>
                      <p class="fw-semibold fs-18 mb-0">Lista de usuarios del sistema!</p>
                      <span class="fs-semibold text-muted">Adminstra de manera eficiente tus usuarios.</span>
                    </div>                
                  </div>
                </div>
                
                <div class="btn-list mt-md-0 mt-2">              
                  <nav>
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="javascript:void(0);"> Administracion</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Usuario</li>
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
                      <div id="div-tabla" class="table-responsive">
                        <table id="tabla-usuario" class="table table-bordered w-100" style="width: 100%;">
                          <thead>
                            <tr>
                              <th>#</th>   
                              <th>Opciones</th>                          
                              <th>Nombre</th>
                              <th>Usuario</th>
                              <th>Cargo</th>                          
                              <th>Teléfono</th>
                              <th>Sesión</th>
                              <th>Estado</th> 
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                          <tfoot>
                            <tr>
                              <th>#</th>
                              <th>Opciones</th>                          
                              <th>Nombre</th>
                              <th>Usuario</th>
                              <th>Cargo</th>                          
                              <th>Teléfono</th>
                              <th>Sesión</th>
                              <th>Estado</th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>    
                      <div id="div-form" style="display: none;">
                        <form name="form-agregar-usuario" id="form-agregar-usuario" method="POST" class="row g-3 needs-validation" novalidate>
                          <ul class="nav nav-tabs tab-style-2 mb-3"  role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="dato-usuario" data-bs-toggle="tab" data-bs-target="#dato-usuario-pane" type="button" role="tab" aria-selected="true"><i class="ri-gift-line me-1 align-middle"></i>Datos del usuario</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="rol-acceso" data-bs-toggle="tab" data-bs-target="#rol-acceso-pane" type="button" role="tab" aria-selected="false"><i class="ri-check-double-line me-1 align-middle"></i>Roles de acceso</button>
                            </li>                  
                          </ul>
                          <div class="tab-content" >
                            <div class="tab-pane fade show active text-muted" id="dato-usuario-pane" role="tabpanel" tabindex="0">
                              <div class="row gy-2" id="cargando-1-fomulario">
                                <!-- id usuario -->
                                <input type="hidden" name="idusuario" id="idusuario" />                                 
                              
                                <!-- Trabajador -->
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="idpersona" class="form-label">
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_usr_trab();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Trabajador: <span class="charge_idpersona"></span></label>
                                    <select class="form-control" name="idpersona" id="idpersona" onchange="ver_cargo();" required>   </select>
                                  </div>                                            
                                </div>
                                <!-- CArgo -->
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="cargo" class="form-label">Cargo:  <span class="charge_cargo"></span></label>
                                    <input type="text" class="form-control" name="cargo" id="cargo" readonly>
                                  </div>                                         
                                </div>
                                <!-- Usuario -->
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="login" class="form-label">Usuario:</label>
                                    <input type="text" class="form-control" name="login" id="login" required>
                                  </div>                                         
                                </div>
                                <!--  Contraseña -->
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="clave" class="form-label">Contraseña:</label>
                                    <div class="input-group mb-3">                            
                                      <input type="password" class="form-control" name="clave" id="clave" placeholder="Contraseña" aria-describedby="icon-view-password">
                                      <button class="btn btn-primary" type="button" onclick="ver_password(this);" id="icon-view-password"><i class="fa-solid fa-eye"></i></button>
                                    </div>
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
                            <div class="tab-pane fade " id="rol-acceso-pane" role="tabpanel" tabindex="0">
                              <ul class="ps-3 mb-0">
                              <div class="row gy-2" id="cargando-3-fomulario">  

                                <div class="col-xl-12">                        
                                  <div class="mail-notification-settings">
                                    <p class="fs-14 mb-1 fw-semibold text-primary">Roles de Módulo</p>
                                    <p class="fs-12 mb-0 text-muted">Escoje los módulos correspondientes al menú</p>
                                  </div>                        
                                  <div  id="permisos"> </div>
                                </div>    

                                <div class="col-xl-12">                          
                                  <div class="mail-notification-settings">
                                    <p class="fs-14 mb-1 fw-semibold text-primary">Rol Correlativos</p>
                                    <p class="fs-12 mb-0 text-muted">Selecciona el correlativo de los comprobantes.</p>
                                  </div>                        
                                  <div id="series"> </div>                        
                                </div>

                                <div class="col-xl-12">                        
                                  <div class="mail-notification-settings">
                                    <p class="fs-14 mb-1 fw-semibold text-primary">Rol para empresa</p>
                                    <p class="fs-12 mb-0 text-muted">Asigna al usuario a la empresa correcta</p>
                                  </div>                        
                                  <div id="empresas"> </div>                                           
                                </div>

                              </div>
                              <div class="row" id="cargando-4-fomulario" style="display: none;" >
                                <div class="col-lg-12 text-center">                         
                                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                                  <h4 class="bx-flashing">Cargando...</h4>
                                </div>
                              </div>
                              </ul>
                            </div>                  
                          </div>   
                          <!-- Chargue -->
                          <div class="p-l-25px col-lg-12" id="barra_progress_usuario_div" style="display: none;" >
                            <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                              <div id="barra_progress_usuario" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                            </div>
                          </div>
                          <!-- Submit -->
                          <button type="submit" style="display: none;" id="submit-form-usuario">Submit</button>
                        </form>
                      </div>                    
                    </div>  
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
          <?php } else { $title_submodulo ='Usuario'; $descripcion ='Lista de Usuarios del sistema!'; $title_modulo = 'Administracion'; include("403_error.php"); }?>   

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
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div>    
          
          <div class="modal fade modal-effect" id="modal-ver-historial-sesion" tabindex="-1" aria-labelledby="modal-agregar-historialLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-modal-img" id="modal-agregar-historialLabel1">Imagen</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                  <table id="tabla-historial-sesion" class="table table-bordered w-100" style="width: 100%;">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>                          
                        <th>Fecha</th>
                        <th>Dia</th>
                        <th>Mes</th>     
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th class="text-center">#</th>                          
                        <th>Fecha</th>
                        <th>Dia</th>
                        <th>Mes</th>   
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div>    

          <?php include("template/search_modal.php"); ?>
          <?php include("template/footer.php"); ?>

        </div>

        <?php include("template/scripts.php"); ?>
        <?php include("template/custom_switcherjs.php"); ?>    

        <script src="scripts/usuario.js?version_jdl=1.16"></script>
        <script> $(function () { $('[data-bs-toggle="tooltip"]').tooltip(); }); </script>

      
      </body>

    </html>
  <?php  
  }
  ob_end_flush();

?>
