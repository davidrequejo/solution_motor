<?php
require_once "../sunat/ConfigSunat.php";


ob_start();
if (strlen(session_id()) < 1) {
  session_start();
} //Validamos si existe o no la sesión

require '../vendor/autoload.php';                   // CONEXION A COMPOSER
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['reporte_contador'] == 1) {

    require_once "../modelos/Reporte_contador.php";    
    
    $reporte_contador = new Reporte_contador();

    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    //---id cliente no va 
    switch ($_GET["op"]) {
      // ══════════════════════════════════════ VALIDAR SESION CLIENTE ══════════════════════════════════════
      
      case 'tabla_reporte_detalle':

        $rspta = $reporte_contador->tabla_reporte_detalle($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_trabajador"], $_GET["filtro_tipo_persona"], $_GET["filtro_centro_poblado"], $_GET["filtro_estado_sunat"] , $_GET["filtro_tipo_comprobante"] );
        $data = []; $count = 1; //echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){           

            $data[] = [
              "0" => '<span class="text-nowrap fs-11">'. $value['idventa_v2'].'</span>',             
              "1" =>  $value['name_day'],
              "2" =>  $value['fecha_emision'],
              "3" => '<span class="text-nowrap fs-11">'. $value['periodo'] .'</span>',
              "4" => '<span class="text-nowrap fs-11">'. $value['cliente_nombre_completo'].'</span>',
              "5" => '<span class="text-nowrap fs-11">'. $value['tipo_documento_abreviatura'] .'</span>',
              "6" => '<span class="text-nowrap fs-11">'. $value['numero_documento'].'</span>',
              "7" => '<span class="text-nowrap fs-11">'. $value['tipo_comprobante_v2'].'</span>',
              "8" => '<span class="text-nowrap fs-11">'. $value['serie_y_numero_comprobante'] .'</span>',
              "9" =>  $value['venta_total_v2'] ,
              "10" =>  $value['total_recibido'] ,
              "11" =>  $value['total_vuelto'] ,
              "12" => '<span class="text-nowrap fs-11">'. $value['metodos_pago_agrupado'] .'</span>',
              "13" => '<span class="text-nowrap fs-11">'. $value['user_en_atencion'] .'</span>',
              "14" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" ><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :                    
                ($value['sunat_estado'] == 'POR ENVIAR'     ?        
                '<span class="badge bg-warning-transparent cursor-pointer"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' : 
                '<span class="badge bg-danger-transparent cursor-pointer"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' 
                ) 
              ),              
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


      case 'exportar_excel_venta':
        $rspta = $reporte_contador->tabla_reporte_detalle($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_trabajador"], $_GET["filtro_tipo_persona"], $_GET["filtro_centro_poblado"], $_GET["filtro_estado_sunat"] , $_GET["filtro_tipo_comprobante"] );

        // echo json_encode($rspta); die();

        // Evita “basura” previa en el output
        while (ob_get_level() > 0) { ob_end_clean(); }
        // (Opcional si manejas muchos registros)
        // ini_set('memory_limit','512M'); set_time_limit(0);

        // Orden EXACTO según tu SELECT/alias
        $keys = [
          'idventa_v2', 'name_day', 'fecha_emision_dmy', 'hora_emision', 'periodo', 'cliente_nombre_completo',
          'tipo_documento_abreviatura', 'numero_documento', 'tipo_comprobante', 'tipo_comprobante_v2', 'serie_y_numero_comprobante',
          'venta_total_v2', 'total_recibido','total_vuelto',  'documento_relacionado',   'metodos_pago_agrupado',
           'user_en_atencion',  'sunat_estado',
        ];

        // Encabezados visibles (si quieres los alias crudos, usa $headers = $keys;)
        $headers = [
          'ID Venta', 'Día', 'F. Emisión', 'Hora Emisión', 'Periodo', 'Cliente',
          'TD', 'N° Doc', 'Cod. Comprobante', 'Comprobante', 'Serie-Número',
          'Total Venta', 'Total Recibido', 'Vuelto', 'Doc Relacionado', 'Métodos Pago',
          'Usuario Atención', 'Estado Sunat'
        ];

        // Normaliza el formato del modelo
        $data =  $rspta['data'] ;
        if (!is_array($data)) { $data = []; }

        // Mapea cada fila al orden de $keys
        $matrix = [];
        foreach ($data as $r) {
          $fila = [];
          foreach ($keys as $k) {
            $fila[] = isset($r[$k]) ? $r[$k] : '';
          }
          $matrix[] = $fila;
        }

        // Construye el Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventas');

        // Coloca headers y datos con fromArray (rápido y compatible)
        $sheet->fromArray($headers, null, 'A1');
        if (!empty($matrix)) {
          $sheet->fromArray($matrix, null, 'A2');
        }

        // Formato numérico a columnas 12-14 (L, M, N)
        $lastDataRow = count($matrix) + 1; // fila 1 = headers
        if ($lastDataRow > 1) {
          $sheet->getStyle("K2:K{$lastDataRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
          $sheet->getStyle("L2:L{$lastDataRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
          $sheet->getStyle("M2:M{$lastDataRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

          // Aplicar formato de fecha a la columna C
          // $sheet->getStyle("C2:C{$lastDataRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        }

        // Auto ancho
        $lastColIndex = count($headers);
        for ($i = 1; $i <= $lastColIndex; $i++) {
          $col = Coordinate::stringFromColumnIndex($i);
          $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Header en negrita
        $sheet->getStyle('A1:' . Coordinate::stringFromColumnIndex($lastColIndex) . '1')
              ->getFont()->setBold(true);

        // Enviar al navegador
        $filename = 'Reporte_de_Ventas_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

      // MUY IMPORTANTE: cortar la ejecución para que no se ejecute ob_end_flush() del final
      exit;
     
      // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

      case 'select2_filtro_trabajador':

        $rspta = $reporte_contador->select2_filtro_trabajador();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idusuario']  . '">' . $value['idusuario_formato']. ' '.  $value['nombre_razonsocial']  . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break; 

      case 'select2_centro_poblado':

        $rspta = $reporte_contador->select2_centro_poblado();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idcentro_poblado']  . '">' . $value['idcentro_poblado_v2']. ' '.  $value['nombre']  . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break; 



      case 'select2_estado_sunat':

        $rspta = $reporte_contador->select2_estado_sunat();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['sunat_estado']  . '">' .  $value['sunat_estado']  . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => [], 'aaData' => []];
        echo json_encode($rspta, true);
      break;
    }
  } else {
    $retorno = ['status' => 'nopermiso', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
    echo json_encode($retorno);
  }
}

ob_end_flush();
