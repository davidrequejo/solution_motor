<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  Class Home
  {
    //Implementamos nuestro constructor
    public function __construct()
    {

    }

    public function mostrar_comentarioC(){
      $sql = "SELECT pc.idpersona_cliente, p.idpersona, p.foto_perfil,
      CASE 
       WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
       WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
       ELSE '-'
      END AS nombre_completo, cp.nombre AS centro_poblado, 
      pc.landing_descripcion, pc.landing_puntuacion, DATE_FORMAT(pc.landing_fecha, '%d-%m-%Y') AS landing_fecha
      FROM persona_cliente pc
      INNER JOIN persona AS p ON pc.idpersona = p.idpersona
      INNER JOIN centro_poblado AS cp ON pc.idcentro_poblado = cp.idcentro_poblado
      WHERE pc.landing_estado = 1 AND pc.estado = 1 AND pc.estado_delete = 1
      AND pc.landing_fecha IS NOT NULL";
      $competario = ejecutarConsultaArray($sql); if($competario['status'] == false){return $competario;}
      return $competario;
    }


    function mostrar_tecnico_redes(){
      $sql = "SELECT ct.nombre AS cargo, p.foto_perfil, p.celular, pt.landing_descripcion,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS nombre_completo
      FROM persona_trabajador AS pt
      INNER JOIN persona AS p ON pt.idpersona = p.idpersona
      INNER JOIN cargo_trabajador AS ct ON p.idcargo_trabajador = ct.idcargo_trabajador
      WHERE pt.landing_estado = 1";
      $mostrar = ejecutarConsultaArray($sql); if($mostrar['status'] == false){return $mostrar;}
      return $mostrar;
    }

    function mostrar_planes(){
      $sql = "SELECT idplan, nombre AS plan, costo, landing_caracteristica FROM plan WHERE landing_estado = 1 AND estado = 1 AND estado_delete = 1";
      $plan = ejecutarConsultaArray($sql); if($plan['status'] == false){return $plan;}
      return $plan;
    }

    public function mostrar_preguntas_frecuentes(){
      $sql = "SELECT pregunta, respuesta FROM preguntas_frecuentes WHERE estado = 1 AND estado_delete = 1";
      $pregFrecuentes = ejecutarConsultaArray($sql); if($pregFrecuentes['status'] == false){return $pregFrecuentes;}
      return $pregFrecuentes;
    }


  }
?>