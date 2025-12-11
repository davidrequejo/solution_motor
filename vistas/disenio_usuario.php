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
    <?php include("template/head.php"); ?>
  </head>

  <body id="body">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      
      <div class="row">
        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
          <h4 class="text-center"><b>ZONA: TOCACHE</b></h4>
          <br>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed">
              <thead>
                <th style="background-color: #FFD300;">N°</th>
                <th style="background-color: #FFD300;">APELLIDOS Y NOMBRES</th>
                <th style="background-color: #FFD300;">FECHA DE INICIO</th>
                <th style="background-color: #FFD300;">TELEFONO</th>
                <th style="background-color: #FFD300;">IMPORTE</th>
                <th style="background-color: #FFD300;">JUL</th>
                <th style="background-color: #FFD300;">AGO</th>
                <th style="background-color: #FFD300;">SEP</th>
                <th style="background-color: #FFD300;">OCT</th>
                <th style="background-color: #FFD300;">SEP</th>
                <th style="background-color: #FFD300;">NOV</th>
                <th style="background-color: #FFD300;">DIC</th>
                <th style="background-color: #FFD300;">ENE</th>
                <th style="background-color: #FFD300;">FEB</th>
                <th style="background-color: #FFD300;">MAR</th>
                <th style="background-color: #FFD300;">OBSERVACIONES</th>
              </thead>
              <tbody>
                <tr>
                  <th class="text-center">1</th>
                  <td>SANCHES KATIA</td>
                  <td>FIN DE MES</td>
                  <td></td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td></td>
                  <td></td>
                  <td>22527668</td>
                </tr>

                <tr>
                  <th class="text-center">2</th>
                  <td style="background-color: #96c8a2;">CAMACO LILI</td>
                  <td style="background-color: #96c8a2;">LOS 15</td>
                  <td style="background-color: #96c8a2;">942 132 760</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td style="background-color: red;"></td>
                  <td style="background-color: red;"></td>
                  <td></td>
                  <td>22511483</td>
                </tr>

                <tr>
                  <th class="text-center">3</th>
                  <td style="background-color: #96c8a2;">DIAS REINA</td>
                  <td style="background-color: #96c8a2;">FIN DE MES</td>
                  <td style="background-color: #96c8a2;">977 789 983</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td style="background-color: red;"></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>

                <tr>
                  <th class="text-center">1</th>
                  <td>VELASQUEZ EVELIN</td>
                  <td>FIN DE MES</td>
                  <td></td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td>50</td>
                  <td style="background-color: red;"></td>
                  <td style="background-color: red;"></td>
                  <td></td>
                  <td>22527668</td>
                </tr>

                <tr>
                  <th class="text-center">5</th>
                  <td>LISA PAREDES</td>
                  <td>FIN DE MES</td>
                  <td></td>
                  <td>60</td>
                  <td>60</td>
                  <td>60</td>
                  <td>60</td>
                  <td style="background-color: red;"></td>
                  <td style="background-color: red;"></td>
                  <td style="background-color: red;"></td>
                  <td style="background-color: red;"></td>
                  <td>X</td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>

                <tr>
                  <th class="text-center" style="background-color:gray;">1</th>
                  <td style="background-color:gray;">VALDIVIERZO SONIA</td>
                  <td style="background-color:gray;">2/2/2023</td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;">60</td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;"></td>
                  <td style="background-color:gray;">DESDE 8 MESES (DESDE MARZO)</td>
                </tr>

              </tbody>
            </table>
            
          </div>
        </div>
      </div>
      
      

      <?php include("template/search_modal.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>   
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