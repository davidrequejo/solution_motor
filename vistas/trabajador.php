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
        
        <?php $title_page = "Trabajadores"; include("template/head.php"); ?>    

      </head> 

      <body id="body-usuario" idusuario="<?php echo $_SESSION['idusuario']; ?>"  > 

        <?php include("template/switcher.php"); ?>
        <?php include("template/loader.php"); ?>

        <div class="page">
          <?php include("template/header.php") ?>
          <?php include("template/sidebar.php") ?>
          <?php if($_SESSION['registrar_trabajador']==1) { ?>

          <!-- Start::app-content -->
          <div class="main-content app-content ">
            <div class="container-fluid">

              <!-- Start::page-header -->
              <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                  <div class="d-md-flex d-block align-items-center ">
                    <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form(); "  > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                    <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                    <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                    <div>
                      <p class="fw-semibold fs-18 mb-0">Lista de Trabajadores del sistema!</p>
                      <span class="fs-semibold text-muted">Adminstra de manera eficiente tus trabajadores.</span>
                    </div>                
                  </div>
                </div>
                
                <div class="btn-list mt-md-0 mt-2">              
                  <nav>
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="javascript:void(0);">Planilla</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Trabajador</li>
                    </ol>
                  </nav>
                </div>
              </div>          
              <!-- End::page-header -->

              <!-- Start::row-1 -->
              <div class="row">
                <div class="col-xxl-12 col-xl-12">
                  
                  <div class="card custom-card ">                  
                    <div class="card-body">
                      <div id="div-tabla" class="table-responsive">
                        <table id="tabla-usuario" class="table table-bordered w-100" style="width: 100%;">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Opciones</th>                          
                              <th>Nombre</th>
                              <th>Edad</th>
                              <th>Cargo</th>                          
                              <th>Teléfono</th>
                              <th>Direccion</th>
                              <th>Ventas</th> 

                              <th>Nombre</th>
                              <th>Tipo Doc.</th>
                              <th>Nro Documento</th>
                              <th>Fecha Nac.</th>
                              <th>Edad</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                          <tfoot>
                            <tr>
                              <th>#</th>
                              <th>Opciones</th>                          
                              <th>Nombre</th>
                              <th>Edad</th>
                              <th>Cargo</th>                          
                              <th>Teléfono</th>
                              <th>Direccion</th>
                              <th>Ventas</th>

                              <th>Nombre</th>
                              <th>Tipo Doc.</th>
                              <th>Nro Documento</th>
                              <th>Fecha Nac.</th>
                              <th>Edad</th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>    
                      <div id="div-form" style="display: none;">
                        <form name="form-agregar-trabajador" id="form-agregar-trabajador" method="POST" class="needs-validation" novalidate>                          
                           
                          <div class="row gy-2" id="cargando-1-fomulario">
                            <!-- idpersona -->
                            <input type="hidden" name="idpersona" id="idpersona" />   
                            <input type="hidden" name="tipo_persona_sunat" id="tipo_persona_sunat" value="NATURAL" />   
                            <input type="hidden" name="idtipo_persona" id="idtipo_persona" value="2" />   
                            <input type="hidden" name="idpersona_trabajador" id="idpersona_trabajador"  />   

                            <div class="col-lg-12 col-xl-6- col-xxl-6">
                              <div class="row">
                                <!-- Grupo -->
                                <div class="col-12 pl-0">
                                  <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >DATOS GENERALES</b></label></div>
                                </div>
                              </div> <!-- /.row -->
                              <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                                <div class="row">

                                  <!-- Tipo documento -->
                                  <div class="mb-1 col-md-3 col-lg-3 col-xl-3 col-xxl-4">
                                    <div class="form-group">
                                      <label for="tipo_documento" class="form-label">Tipo documento:  </label>
                                      <select name="tipo_documento" id="tipo_documento" class="form-control" placeholder="Tipo de documento" >  </select>
                                    </div>                                         
                                  </div>
                                  
                                  <!--  Numero Documento -->
                                  <div class="mb-1 col-md-3 col-lg-3 col-xl-3 col-xxl-4">
                                    <div class="form-group">
                                      <label for="numero_documento" class="form-label">Numero Documento:</label>
                                      <div class="input-group">                            
                                        <input type="number" class="form-control" name="numero_documento" id="numero_documento" placeholder="" aria-describedby="icon-view-password">
                                        <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-trabajador','_t', '#tipo_documento', '#numero_documento', '#nombre_razonsocial', '#apellidos_nombrecomercial', '#direccion', '#distrito', '#titular_cuenta' );" >
                                          <i class='bx bx-search-alt' id="search_t"></i>
                                          <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                        </button>
                                      </div>
                                    </div>                        
                                  </div>                                  

                                  <!-- Cargo -->
                                  <div class="mb-1 col-md-3 col-lg-3 col-xl-6 col-xxl-4">
                                    <div class="form-group">
                                      <label for="idcargo_trabajador" class="form-label">
                                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_cargo();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                        Cargo:  
                                        <span class="charge_idcargo"></span>
                                      </label>
                                      <select name="idcargo_trabajador" id="idcargo_trabajador" class="form-control" >   </select>
                                    </div>                                         
                                  </div>
                                
                                  <!-- Nombres -->
                                  <div class="mb-1 col-md-6 col-lg-6 col-xl-4 col-xxl-6">
                                    <div class="form-group">
                                      <label for="nombre_razonsocial" class="form-label label-nom-raz">Nombres:  </label>
                                      <input type="text" class="form-control" name="nombre_razonsocial" id="nombre_razonsocial" >
                                    </div>                                         
                                  </div>

                                  <!-- Apellidos -->
                                  <div class="mb-1 col-md-6 col-lg-6 col-xl-4 col-xxl-6">
                                    <div class="form-group">
                                      <label for="apellidos_nombrecomercial" class="form-label label-ape-come">Apellidos:  </label>
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
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-4 col-xxl-6">
                                    <div class="form-group">
                                      <label for="celular" class="form-label">Celular:</label>
                                      <input type="tel" class="form-control" name="celular" id="celular" >
                                    </div>                                         
                                  </div> 

                                  <!-- Nacimiento -->
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-4 col-xxl-6">
                                    <div class="form-group">
                                      <label for="fecha_nacimiento" class="form-label">Nacimiento:  </label>
                                      <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" onclick="calcular_edad('#fecha_nacimiento', '#edad', '.edad');" onchange="calcular_edad('#fecha_nacimiento', '#edad', '.edad');" >
                                    </div>                                         
                                  </div>
                                  <!-- Edad -->
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-4 col-xxl-6">
                                    <div class="form-group">
                                      <label for="edad" class="form-label">Edad:</label>
                                      <input type="hidden" class="form-control" name="edad" id="edad" >
                                      <span class="form-control-mejorado edad">0</span>
                                    </div>                                         
                                  </div>                                  

                                </div> <!-- /.row -->
                              </div> <!-- /.card-body -->
                            </div> <!-- /.col-lg-12 -->

                            <div class="col-lg-12 col-xl-6- col-xxl-6">
                              <div class="row">
                                <!-- Grupo -->
                                <div class="col-12 pl-0">
                                  <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >SUNAT Y UBICACIÓN</b></label></div>
                                </div>
                              </div> <!-- /.row -->
                              <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                                <div class="row">
                                  <!-- RUC -->
                                  <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                    <div class="form-group">
                                      <label for="ruc" class="form-label">RUC:  </label>
                                      <input type="text" class="form-control" name="ruc" id="ruc" >
                                    </div>                                         
                                  </div>

                                  <!-- Usuario Sol -->
                                  <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                    <div class="form-group">
                                      <label for="usuario_sol" class="form-label">Usuario Sol:  </label>
                                      <input type="text" class="form-control" name="usuario_sol" id="usuario_sol" >
                                    </div>                                         
                                  </div>

                                  <!-- Clave Sol -->
                                  <div class="mb-1 col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                                    <div class="form-group">
                                      <label for="clave_sol" class="form-label">Clave Sol:  </label>
                                      <input type="text" class="form-control" name="clave_sol" id="clave_sol" >
                                    </div>                                         
                                  </div>
                                  <!-- Direccion -->
                                  <div class="mb-1 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                    <div class="form-group">
                                      <label for="direccion" class="form-label">Direccion:</label>
                                      <input type="text" class="form-control" name="direccion" id="direccion">
                                    </div>                                         
                                  </div>
                                  <!-- Distrito -->
                                  <div class="mb-1 col-md-3 col-lg-6 col-xl-6 col-xxl-6">
                                    <div class="form-group">
                                      <label for="distrito" class="form-label">Distrito:  </label>
                                      <select name="distrito" id="distrito" class="form-control" placeholder="Seleccionar" onchange="llenar_dep_prov_ubig(this);">                                  
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

                            <div class="col-lg-12 col-xl-12 col-xxl-12">
                              <div class="row">
                                <!-- Grupo -->
                                <div class="col-12 pl-0">
                                  <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b class="mx-2" >PAGOS</b></label></div>
                                </div>
                              </div> <!-- /.row -->
                              <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                                <div class="row">

                                  <!-- Sueldo mes -->
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                                    <div class="form-group">
                                      <label for="sueldo_mensual" class="form-label">Sueldo mes:</label>
                                      <input type="number" class="form-control" name="sueldo_mensual" id="sueldo_mensual" step="0.01" >
                                    </div>                                         
                                  </div>

                                  <!-- Sueldo dia -->
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
                                    <div class="form-group">
                                      <label for="sueldo_diario" class="form-label">Sueldo dia:</label>
                                      <input type="number" class="form-control" name="sueldo_diario" id="sueldo_diario" step="0.01" >
                                    </div>                                         
                                  </div>

                                  <!-- Banco -->
                                  <div class="mb-1 col-md-3 col-lg-6 col-xl-6 col-xxl-6">
                                    <div class="form-group">
                                      <label for="idbanco" class="form-label">
                                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_banco();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                        Banco:  
                                        <span class="charge_idbanco"></span>
                                      </label>
                                      <select name="idbanco" id="idbanco" class="form-control" placeholder="Seleccionar">                                  
                                      </select>
                                    </div>                                         
                                  </div>

                                  <!-- Cuenta Bancaria -->
                                  <div class="mb-1 col-md-6 col-lg-4 col-xl-4 col-xxl-4">
                                    <div class="form-group">
                                      <label for="cuenta_bancaria" class="form-label">Cuenta Bancaria:</label>
                                      <input type="text" class="form-control" name="cuenta_bancaria" id="cuenta_bancaria" >
                                    </div>                                         
                                  </div>

                                  <!-- CCI -->
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-4 col-xxl-4">
                                    <div class="form-group">
                                      <label for="cci" class="form-label">CCI:</label>
                                      <input type="text" class="form-control" name="cci" id="cci" >
                                    </div>                                         
                                  </div>

                                  <!-- CCI -->
                                  <div class="mb-1 col-md-6 col-lg-3 col-xl-4 col-xxl-4">
                                    <div class="form-group">
                                      <label for="titular_cuenta" class="form-label">Titular Cuenta:</label>
                                      <input type="text" class="form-control" name="titular_cuenta" id="titular_cuenta" >
                                    </div>                                         
                                  </div>

                                </div> <!-- /.row -->
                              </div> <!-- /.card-body -->
                            </div> <!-- /.col-lg-12 -->

                            <!-- Imgen -->
                            <div class="col-md-4 col-lg-4 mt-4">
                              <span class="" > <b>Imagen de Perfil</b> </span>
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

                          </div> <!-- /.row -->

                          <div class="row" id="cargando-2-fomulario" style="display: none;" >
                            <div class="col-lg-12 text-center">                         
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>  <!-- /.row -->                                   
                          
                          <!-- Chargue -->
                          <div class="p-l-25px col-lg-12" id="barra_progress_trabajador_div" style="display: none;" >
                            <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                              <div id="barra_progress_trabajador" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                            </div>
                          </div>
                          <!-- Submit -->
                          <button type="submit" style="display: none;" id="submit-form-trabajador">Submit</button>
                        </form>
                      </div>                    
                    </div>  
                    <div class="card-footer border-top-0">
                      <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                      <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>

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
          
          <div class="modal fade modal-effect" id="modal-ver-cliente" tabindex="-1" aria-labelledby="modal-agregar-clienteLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-modal-cliente" id="modal-agregar-clienteLabel1">Lista</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                  <table id="tabla-cliente" class="table table-bordered w-100" style="width: 100%;">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>                          
                        <th>Nombre</th>
                        <th>IP</th>
                        <th>Fecha</th>    
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th class="text-center">#</th>                          
                        <th>Nombre</th>
                        <th>Dia</th>
                        <th>Fecha</th>   
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div>    

          <?php include("template/search_modal.php"); ?>
          <?php include("template/footer.php"); ?>

        </div>

        <?php include("template/scripts.php"); ?>
        <?php include("template/custom_switcherjs.php"); ?>    

        <script src="scripts/trabajador.js?version_jdl=1.16"></script>
        <script> $(function () { $('[data-bs-toggle="tooltip"]').tooltip(); }); </script>

      
      </body>

    </html>
  <?php  
  }
  ob_end_flush();

?>
