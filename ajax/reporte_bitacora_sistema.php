<?php
require_once "../sunat/ConfigSunat.php";

ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['reporte_bitacora_sistema'] == 1) {
   

    require_once "../modelos/Reporte_bitacora_sistema.php";
    $reporte_bitacora_sistema = new Reporte_bitacora_sistema();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idcuenta_bancaria  = isset($_POST["idcuenta_bancaria"])? limpiarCadena($_POST["idcuenta_bancaria"]):"";
    $tipo_cuenta        = isset($_POST["tipo_cuenta"])? limpiarCadena($_POST["tipo_cuenta"]):"";
    $moneda             = isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"";
    $idbancos           = isset($_POST["idbancos"])? limpiarCadena($_POST["idbancos"]):"";
    $cta_cte            = isset($_POST["cta_cte"])? limpiarCadena($_POST["cta_cte"]):"";
    $cci                = isset($_POST["cci"])? limpiarCadena($_POST["cci"]):"";
    $saldo_inicial      = isset($_POST["saldo_inicial"])? limpiarCadena($_POST["saldo_inicial"]):"";
    
    
    switch ($_GET["op"]){    

      case 'listar_tabla_principal':
        $rspta = $reporte_bitacora_sistema->listar_tabla($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_usuario"], $_GET["filtro_modulo"] );
        $data = []; $count = 1;
        if($rspta['status'] == true){
          foreach($rspta['data'] as $key => $value){
            
            
            $data[]=[
              "0" => $count++,
              "1" => $value['modifico_en'] ,
              "2" => $value['created_at'],
              "3" => $value['mensaje'],    
              "4" => $value['usuario_nombre'],              
              "5" => $value['usuario_cargo_trabajador'],
              "6" => '<div class="bg-light fs-10" style="overflow: auto; resize: vertical; height: 30px; width: 300px;">' .($value['sql_d']). '</div>', 

            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;      

      case 'mostrar_editar' :
        $rspta = $reporte_bitacora_sistema->mostrar_editar($idcuenta_bancaria);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_detalle_cuenta_bancaria':
        $rspta = $reporte_bitacora_sistema->mostrar_editar($idcuenta_bancaria);
        $nombre_doc = $rspta['data']['imagen'];
        $html_table = '
          <div class="my-3" ><span class="h6"> Datos del Producto </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                <th scope="col">Nombre</th>
                <th scope="row">'.$rspta['data']['nombre'].'</th>            
              </tr>              
              <tr>
                <th scope="col">Código</th>
                <th scope="row">'.$rspta['data']['codigo'].'</th>
              </tr> 
              <tr>
                <th scope="col">Descripción</th>
                <th scope="row">'.$rspta['data']['descripcion'].'</th>
              </tr>                  
            </tbody>
          </table>

          <div class="my-3" ><span class="h6"> Detalles </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                  <th scope="col">Categoria</th>
                  <th scope="row">'.$rspta['data']['categoria'].'</th>            
                </tr> 
              <tr>
                <th scope="col">Marca</th>
                <th scope="row">'.$rspta['data']['marca'].'</th>            
              </tr>              
              <tr>
                <th scope="col">U. Medida</th>
                <th scope="row">'.$rspta['data']['unidad_medida'].'</th>
              </tr> 
              <tr>
                <th scope="col">Stock</th>
                <th scope="row">'.$rspta['data']['stock'].'</th>
              </tr>               
            </tbody>
          </table>

          <div class="my-3" ><span class="h6"> Precio </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                  <th scope="col">Precio Compra</th>
                  <th scope="row"> S/ '.$rspta['data']['precio_compra'].'</th>            
                </tr> 
              <tr>
                <th scope="col">Precio Venta</th>
                <th scope="row">S/ '.$rspta['data']['precio_venta'].'</th>            
              </tr>                            
            </tbody>
          </table>
        <div class="my-3" ><span class="h6"> Imagen </span></div>';
        $rspta = ['status' => true, 'message' => 'Todo bien', 'data' => $html_table, 'imagen' => $rspta['data']['imagen'], 'nombre_doc'=> $nombre_doc];
        echo json_encode($rspta, true);

      break;        
      

      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
          
      case 'select2_usuario':
        $rspta = $reporte_bitacora_sistema->select2_usuario(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idusuario'] . '" >' . $value['usuario_nombre_apellidos']  . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;   

      case 'select2_modulo':
        $rspta = $reporte_bitacora_sistema->select2_modulo(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['nombre_tabla'] . '" >' . $value['modifico_en']  . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;   

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;
    }

  }else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }


}
ob_end_flush();