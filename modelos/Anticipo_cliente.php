<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Anticipo_cliente
  {

    //Implementamos nuestro constructor
    public $id_usr_sesion; 
    // public $id_empresa_sesion;
    //Implementamos nuestro constructor
    public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
    {
      $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }


    public function tabla_clientes() {

      $sql= "SELECT 
              ac.idpersona_cliente,
              p.nombre_razonsocial AS nombres,
              p.apellidos_nombrecomercial AS apellidos,
              SUM(CASE WHEN ac.tipo = 'EGRESO' THEN ac.total * -1 ELSE ac.total END) AS total_anticipo
            FROM anticipo_cliente ac
            INNER JOIN persona_cliente pc ON ac.idpersona_cliente = pc.idpersona_cliente
            INNER JOIN persona p ON pc.idpersona = p.idpersona
            WHERE ac.estado='1' AND ac.estado_delete='1'
            GROUP BY ac.idpersona_cliente";
      return ejecutarConsulta($sql);
    } 


    public function tabla_anticipos($idpersona_cliente){
      $sql = "SELECT 
                ac.idanticipo_cliente,
                ac.idpersona_cliente,
                p.nombre_razonsocial AS nombres, 
                p.apellidos_nombrecomercial AS apellidos,
                ac.tipo, 
                ac.fecha_anticipo,
                ac.descripcion, 
                stp_a.abreviatura AS tc_anticipo,  
                ac.serie_comprobante AS sc_anticipo, 
                ac.numero_comprobante AS nc_anticipo,
                stp_v.abreviatura AS tc_venta, 
                v.serie_comprobante AS sc_venta, 
                v.numero_comprobante AS nc_venta,
                ac.total,
                CASE 
                  WHEN ac.tipo = 'EGRESO' THEN ac.total * -1
                  ELSE ac.total 
                END AS monto_anticipo,
                SUM(CASE WHEN ac.tipo = 'EGRESO' THEN ac.total * -1 ELSE ac.total END) OVER (PARTITION BY ac.idpersona_cliente) AS total_anticipo
            FROM 
              anticipo_cliente ac
              LEFT JOIN venta v ON ac.idventa = v.idventa
              INNER JOIN persona_cliente pc ON ac.idpersona_cliente = pc.idpersona_cliente
              INNER JOIN persona p ON pc.idpersona = p.idpersona
              LEFT JOIN sunat_c01_tipo_comprobante stp_v ON v.tipo_comprobante = stp_v.idtipo_comprobante
              LEFT JOIN sunat_c01_tipo_comprobante stp_a ON ac.tipo_comprobante = stp_a.codigo
            WHERE ac.idpersona_cliente = '$idpersona_cliente'
            AND ac.estado='1' AND ac.estado_delete='1'
            GROUP BY
              ac.idanticipo_cliente,
              ac.idpersona_cliente,
              p.nombre_razonsocial,
              p.apellidos_nombrecomercial";
      return ejecutarConsulta($sql);

    }

    public function insertar($idpersona_cliente, $fecha, $descripcion, $tipo, $total, $serie){

      $sql_0 = "SELECT MAX(numero) + 1 AS numeracion_anticipo FROM sunat_c01_tipo_comprobante WHERE serie = '$serie'";
      $result = ejecutarConsultaSimpleFila($sql_0);
  
      // Revisar si la consulta fue exitosa y obtener el número de numeración
      if ($result['status'] === true && $result['data']) {
          $numeracion = $result['data']['numeracion_anticipo'];
      if (is_null($numeracion)) { $numeracion = 1; /* Si no hay registros previos, empezamos en 1 */ }
      } else {
          return $result; // Manejar el error, devolver o loguear según sea necesario
      }
  
      // Preparar la consulta de inserción con el número obtenido
      $sql = "INSERT INTO anticipo_cliente(idpersona_cliente, fecha_anticipo, descripcion, tipo, total, tipo_comprobante, serie_comprobante, numero_comprobante)
              VALUES ('$idpersona_cliente', '$fecha', '$descripcion', '$tipo', '$total', '102', '$serie', '$numeracion')";
  
      $insertar = ejecutarConsulta_retornarID($sql, 'C');
      if ($insertar['status'] == false) {
          return $insertar;
      }

      $sql_2 = "UPDATE sunat_c01_tipo_comprobante SET numero = '$numeracion' WHERE serie = '$serie'";
      $actualizar_correlativo = ejecutarConsulta($sql_2);

      return $insertar;
    }
  

    public function editar($idanticipo_cliente, $idpersona_cliente, $fecha, $descripcion, $tipo, $total, $serie_edit, $numero){
      
      $sql = "UPDATE anticipo_cliente SET idpersona_cliente='$idpersona_cliente', tipo='$tipo', fecha_anticipo='$fecha', tipo_comprobante='102',
                     serie_comprobante='$serie_edit', numero_comprobante='$numero', total='$total',  descripcion='$descripcion'
              WHERE idanticipo_cliente = '$idanticipo_cliente'";
      $editar = ejecutarConsulta($sql); if ($editar['status'] == false) {  return $editar; } 
      return $editar;
    }

    public function mostrar($id){
      $sql="SELECT * FROM anticipo_cliente WHERE idanticipo_cliente='$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function desactivar($idanticipo_cliente) {
      $sql="UPDATE anticipo_cliente SET estado='0' WHERE idanticipo_cliente='$idanticipo_cliente'";
      $desactivar= ejecutarConsulta($sql); if ($desactivar['status'] == false) {  return $desactivar; }
      
      return $desactivar;
    }

    public function eliminar($idanticipo_cliente) {
      $sql="UPDATE anticipo_cliente SET estado_delete='0' WHERE idanticipo_cliente='$idanticipo_cliente'";
      $eliminar= ejecutarConsulta($sql); if ($eliminar['status'] == false) {  return $eliminar; }
      
      return $eliminar;
    }

    public function imprimir_anticipo($id){
      $sql = "SELECT  
                ac.idanticipo_cliente,
                ac.idpersona_cliente, 
                p.nombre_razonsocial, 
                p.apellidos_nombrecomercial,
                p.numero_documento,
                p.direccion,
                DATE_FORMAT(ac.fecha_anticipo, '%d-%m-%Y') AS fecha_anticipo,
                ac.serie_comprobante,
                ac.numero_comprobante,
                ac.total
              FROM anticipo_cliente ac
              INNER JOIN persona_cliente pc ON ac.idpersona_cliente = pc.idpersona_cliente
              INNER JOIN persona p ON pc.idpersona = p.idpersona
              WHERE ac.idanticipo_cliente = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function selectSerie(){
      $sql = "SELECT serie FROM sunat_c01_tipo_comprobante WHERE codigo='102' GROUP BY serie";
      return ejecutarConsulta($sql);
    }

    public function numeracion($ser){
      $sql = "SELECT MAX(numero) + 1 AS numeracion_anticipo FROM sunat_c01_tipo_comprobante WHERE serie = '$ser'";
      return ejecutarConsulta($sql);
    }

    public function select_cliente()	{
      
      $sql="SELECT pc.idpersona_cliente, p.nombre_razonsocial AS nombres, p.apellidos_nombrecomercial AS apellidos
            FROM persona_cliente pc
            INNER JOIN persona p ON pc.idpersona = p.idpersona;";
      return ejecutarConsultaArray($sql);   
    }


    public function empresa(){
      $sql = "SELECT * FROM empresa ";
      return ejecutarConsultaSimpleFila($sql);
    }


  }