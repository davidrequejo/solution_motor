<!-- Start::app-sidebar -->
<aside class="app-sidebar sticky" id="sidebar">

  <!-- Start::main-sidebar-header -->
  <div class="main-sidebar-header">
    <a href="escritorio.php" class="header-logo">
      <img src="../assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
      <img src="../assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
      <img src="../assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
      <img src="../assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark">
      <img src="../assets/images/brand-logos/desktop-white.png" alt="logo" class="desktop-white">
      <img src="../assets/images/brand-logos/toggle-white.png" alt="logo" class="toggle-white">
    </a>
  </div>
  <!-- End::main-sidebar-header -->

  <!-- Start::main-sidebar -->
  <div class="main-sidebar" id="sidebar-scroll">

    <!-- Start::nav -->
    <nav class="main-menu-container nav nav-pills flex-column sub-open">
      <div class="slide-left" id="slide-left">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
          <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
        </svg>
      </div>
      <ul class="main-menu">
        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">I N I C I O</span></li>
        <!-- End::slide__category -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['dashboard'] == '1') { ?>
        <li class="slide">
          <a href="escritorio.php" class="side-menu__item">
            <i class="bx bx-home side-menu__icon"></i><span class="side-menu__label"> Dashboards</span>
          </a>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">L O G I S T I C A</span></li>
        <!-- End::slide__category -->
        
        <!-- Start::slide -->
        <?php  if ($_SESSION['compra'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class="bx bx-home side-menu__icon"></i>
            <span class="side-menu__label">Compras<span class="badge bg-warning-transparent ms-2">3</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Compras</a>  </li>
            <?php  if ($_SESSION['proveedores'] == '1') { ?>
            <li class="slide"> <a href="proveedores.php" class="side-menu__item">Proveedores</a> </li>
            <?php } ?>            
            <?php  if ($_SESSION['lista_de_compras'] == '1') { ?>
            <li class="slide"> <a href="compras.php" class="side-menu__item">Lista de compra</a> </li>         
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['articulo'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class="bx bx-home side-menu__icon"></i>
            <span class="side-menu__label">Articulos<span class="badge bg-warning-transparent ms-2">8</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Articulos</a></li>
            <?php  if ($_SESSION['producto'] == '1') { ?>
            <li class="slide"> <a href="producto.php" class="side-menu__item">Producto</a></li>
            <?php } ?>
            <?php  if ($_SESSION['servicio'] == '1') { ?>
            <li class="slide"> <a href="servicio.php" class="side-menu__item">Servicio</a></li>
            <?php } ?>
            <?php  if ($_SESSION['categoria_y_marca'] == '1') { ?>
            <li class="slide"> <a href="categoria_y_marca.php" class="side-menu__item">Categoria y Marca</a></li>
            <?php } ?>
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="unidad_medida.php" class="side-menu__item">Unidad Medida</a></li>  
            <?php } ?>    
            <?php  if ($_SESSION['producto_pasillo'] == '1') { ?>
            <li class="slide"> <a href="pasillo.php" class="side-menu__item">Pasillos</a></li>  
            <?php } ?>           
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">G E S T I O N - D E - V E N T A S</span></li>
        <!-- End::slide__category -->     
         
        <!-- Start::slide -->
        <?php  if ($_SESSION['ordenes'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-cart-add side-menu__icon'></i>
            <span class="side-menu__label">Ordenes<span class="badge bg-secondary-transparent ms-2">New</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Ordenes</a> </li>
            <?php  if ($_SESSION['orden_venta'] == '1') { ?>
            <li class="slide"> <a href="orden_venta.php" class="side-menu__item">Realizar Ordenes</a></li>
            <?php } ?>         
            <?php  if ($_SESSION['orden_venta_listar'] == '1') { ?>
            <li class="slide"> <a href="orden_venta_listar.php" class="side-menu__item">Ver Ordenes</a></li>
            <?php } ?>     
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide --> 

        <!-- Start::slide -->
        <?php  if ($_SESSION['ventas'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-cart-add side-menu__icon'></i>
            <span class="side-menu__label">Ventas<span class="badge bg-secondary-transparent ms-2">New</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Ventas</a> </li>
            <?php  if ($_SESSION['orden_venta_cobrar'] == '1') { ?>
            <li class="slide"> <a href="orden_venta_cobrar.php" class="side-menu__item">Cobrar Ordenes</a></li>
            <?php } ?> 
            <?php  if ($_SESSION['facturacion'] == '1') { ?>
            <li class="slide"> <a href="facturacion.php" class="side-menu__item">Facturación</a></li>
            <?php } ?>         
            <?php  if ($_SESSION['guia_de_remision'] == '1') { ?>
            <li class="slide"> <a href="guia_de_remision.php" class="side-menu__item">Guia de Remisión</a></li>
            <?php } ?>            
            <?php  if ($_SESSION['cotizacion'] == '1') { ?>
            <li class="slide"> <a href="cotizacion.php" class="side-menu__item">Cotizacion</a></li>
            <?php } ?>             
            <?php  if ($_SESSION['cliente'] == '1') { ?>
            <li class="slide"> <a href="clientes.php" class="side-menu__item">Clientes</a></li>
            <?php } ?>
            <?php  if ($_SESSION['anticipos'] == '1') { ?>
            <li class="slide"> <a href="anticipo_cliente.php" class="side-menu__item">Anticipos</a></li>
            <?php } ?>
            <?php  if ($_SESSION['periodo_facturado'] == '1') { ?>
            <li class="slide"> <a href="periodo_facturacion.php" class="side-menu__item">Periodos Facturados</a></li>
            <?php } ?>     
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide --> 

        <?php  if ($_SESSION['caja'] == '1') { ?>
        <li class="slide">
          <a href="caja.php" class="side-menu__item">
            <i class='bx bx-dollar-circle side-menu__icon' ></i><span class="side-menu__label"> Caja</span>
          </a>          
        </li>
        <?php } ?>

        <!-- Start::slide -->
        <?php  if ($_SESSION['ingreso_egreso'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-line-chart side-menu__icon'></i>
            <span class="side-menu__label">Ingreso/Egreso <span class="badge bg-secondary-transparent ms-2">New</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1 mega-menu">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Ingreso/Egreso <span class="badge bg-secondary-transparent ms-2">New</span></a></li>     
            <?php  if ($_SESSION['gasto_trabajador'] == '1') { ?>
            <li class="slide"> <a href="gasto_de_trabajador.php" class="side-menu__item">Trabajador </a></li>
            <?php } ?>      
            <?php  if ($_SESSION['otro_gasto'] == '1') { ?>
            <li class="slide"> <a href="otros_gastos.php" class="side-menu__item">Otros </a></li>
            <?php } ?>              
            <?php  if ($_SESSION['ingresos'] == '1') { ?>
            <li class="slide"> <a href="ingresos_caja.php" class="side-menu__item">Ingresos</a></li>
            <?php } ?>            
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">C O N T A B I L I D A D</span></li>
        <!-- End::slide__category -->        

        <!-- Start::slide -->
        <?php  if ($_SESSION['reporte'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-line-chart side-menu__icon'></i>
            <span class="side-menu__label">Reportes <span class="badge bg-secondary-transparent ms-2">New</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1 mega-menu">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Reportes <span class="badge bg-secondary-transparent ms-2">New</span></a></li>               
            
            <?php  if ($_SESSION['reporte_contador'] == '1') { ?>
            <li class="slide"> <a href="reporte_contador.php" class="side-menu__item">Contador</a></li>
            <?php } ?>  
            <?php  if ($_SESSION['reporte_venta_x_trabajador'] == '1') { ?>
            <li class="slide"> <a href="reporte_venta_x_trabajador.php" class="side-menu__item">Venta x Trabajador</a></li>
            <?php } ?>  
            <?php  if ($_SESSION['reporte_stock_de_producto'] == '1') { ?>
            <li class="slide"> <a href="reporte_validacion_estock.php" class="side-menu__item">Stock de Producto</a></li>
            <?php } ?>  
            <?php  if ($_SESSION['reporte_copia_estock'] == '1') { ?>
            <li class="slide"> <a href="reporte_copia_estock.php" class="side-menu__item">Copias Stock</a></li>
            <?php } ?>     
               
            <?php  if ($_SESSION['reporte_bitacora_sistema'] == '1') { ?>
            <li class="slide"> <a href="reporte_bitacora_sistema.php" class="side-menu__item">Bitacora</a></li>
            <?php } ?>              

            <!-- <li class="slide"> <a href="retraso_cobro.php" class="side-menu__item">Retraso de Cobros </a></li> -->            
            <!-- <li class="slide"> <a href="avance_cobro.php" class="side-menu__item">Avance de Cobros </a></li> -->            
            <!-- <li class="slide"> <a href="no_disponible.php" class="side-menu__item">Correo enviados</a></li> -->
            
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->        

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">G E S T I O N - R R H H</span></li>
        <!-- End::slide__category -->        

        <!-- Start::slide -->
        <?php  if ($_SESSION['planilla_personal'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-user-check side-menu__icon'></i>
            <span class="side-menu__label">Planilla personal</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">Planilla personal</a></li>  
            <?php  if ($_SESSION['registrar_trabajador'] == '1') { ?>          
            <li class="slide"><a href="trabajador.php" class="side-menu__item">Trabajadores</a></li>
            <?php } ?>
            <?php  if ($_SESSION['tipo_de_seguro'] == '1') { ?>
            <li class="slide"><a href="no_disponible.php" class="side-menu__item">Tipo de seguro</a></li>
            <?php } ?>
            <?php  if ($_SESSION['boleta_de_pago'] == '1') { ?>
            <li class="slide"><a href="no_disponible.php" class="side-menu__item">Boleta de pago</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">C O N F I G U R A C I O N</span></li>
        <!-- End::slide__category -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['SUNAT'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-building side-menu__icon'></i>
            <span class="side-menu__label">SUNAT</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">SUNAT</a></li>
            <?php  if ($_SESSION['catalogo_de_codigo'] == '1') { ?>
            <li class="slide"><a href="catalogos_y_codigos.php" class="side-menu__item">Catálago de Códigos</a></li>
            <?php } ?>             
            <?php  if ($_SESSION['correlativo_numeracion'] == '1') { ?>          
            <li class="slide"><a href="correlacion_comprobante.php" class="side-menu__item">Tipos de Comprobantes</a></li>
            <?php } ?>
                 
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->        

        <!-- Start::slide -->
        <?php  if ($_SESSION['configuracion'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-cog side-menu__icon'></i>
            <span class="side-menu__label">Configuración</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">Configuración</a></li>     
            
            <?php  if ($_SESSION['usuario'] == '1') { ?>
            <li class="slide"><a href="usuario.php" class="side-menu__item">Usuarios</a></li>     
            <?php } ?>     
            <?php  if ($_SESSION['general'] == '1') { ?>
            <li class="slide"><a href="general.php" class="side-menu__item">General</a></li>    
            <?php } ?>   
            <?php  if ($_SESSION['empresa'] == '1') { ?>
            <li class="slide"><a href="empresa.php" class="side-menu__item">Empresa</a></li>
            <?php } ?>    

          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['papelera'] == '1') { ?>
        <li class="slide">
          <a href="papelera.php" class="side-menu__item" target="_blank">
            <i class='bx bx-trash side-menu__icon'></i><span class="side-menu__label">Papelera</span>
          </a>
        </li>
        <?php } ?>    
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['landing_page'] == '1') { ?>
        <li class="slide">
          <a href="landing_page.php" class="side-menu__item" target="_blank">
            <i class='bx bx-layer side-menu__icon'></i><span class="side-menu__label">Landing Page</span>
          </a>
        </li>
        <?php } ?>   
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">S O P O R T E</span></li>
        <!-- End::slide__category --> 

        <!-- Start::slide -->
        <li class="slide">
          <a href="https://wa.link/1dpx0i" class="side-menu__item" target="_blank">
            <i class="bx bx-home side-menu__icon"></i><span class="side-menu__label"> Soporte Técnico</span>
          </a>
        </li>
        <!-- End::slide -->
        
      </ul>
      <div class="slide-right" id="slide-right">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
          height="24" viewBox="0 0 24 24">
          <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
        </svg>
      </div>
    </nav>
    <!-- End::nav -->

  </div>
  <!-- End::main-sidebar -->

</aside>
<!-- End::app-sidebar -->