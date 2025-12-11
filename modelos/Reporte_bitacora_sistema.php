<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Reporte_bitacora_sistema
  {

    // Variables globales
    public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
    //Implementamos nuestro constructor
    public function __construct()
    {
      $this->id_usr_sesion        =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      $this->id_persona_sesion    = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
      $this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
    }

    /*		
      C		-- Create (Crear)
      R		-- Read (Leer)
      U		-- Update (Actualizar)
      D		-- Delete (Eliminar)
      DA	-- Delete Activate (Eliminar Activar)
      T		-- Trash (Papelera)
      TA	-- Trash Activate (Papelera Activar)
    */	

    function listar_tabla( $filtro_fecha_i, $filtro_fecha_f, $filtro_usuario, $filtro_modulo ){

      $sql_filtro_fecha = ""; $sql_filtro_usuario = "";  $sql_filtro_modulo = ""; 

      if ( !empty($filtro_fecha_i) && !empty($filtro_fecha_f) ) { $sql_filtro_fecha = "AND DATE_FORMAT(bd.created_at, '%Y-%m-%d') BETWEEN '$filtro_fecha_i' AND '$filtro_fecha_f'"; } 
      else if (!empty($filtro_fecha_i)) { $sql_filtro_fecha = "AND DATE_FORMAT(bd.created_at, '%Y-%m-%d') = '$filtro_fecha_i'"; }
      else if (!empty($filtro_fecha_f)) { $sql_filtro_fecha = "AND DATE_FORMAT(bd.created_at, '%Y-%m-%d') = '$filtro_fecha_f'"; }

      if ( empty($filtro_usuario) ) { } else {  $sql_filtro_usuario = "AND bd.id_user = '$filtro_usuario'"; } 
      if ( empty($filtro_modulo) ) { } else {  $sql_filtro_modulo = "AND bd.nombre_tabla = '$filtro_modulo'"; } 
      

      $sql= "SELECT bd.*, 
      CASE 
        WHEN bd.nombre_tabla = 'prestamo' THEN 'Prestamo'
        WHEN bd.nombre_tabla = 'producto' THEN 'Producto'
        WHEN bd.nombre_tabla = 'prestamo_pago' THEN 'Pago de prestamo'
        WHEN bd.nombre_tabla = 'prestamo_refrendo' THEN 'Pago de Refrendo'
        WHEN bd.nombre_tabla = 'prestamo_reducir_capital' THEN 'Reducir Capital'
        WHEN bd.nombre_tabla = 'prestamo_aumentar_capital' THEN 'Aumentar Capital'
        WHEN bd.nombre_tabla = 'persona_cliente' THEN 'Cliente'
        WHEN bd.nombre_tabla = 'persona_trabajador' THEN 'Trabajador'
        WHEN bd.nombre_tabla = 'cuenta_bancaria' THEN 'Cuenta Bancaria'
        WHEN bd.nombre_tabla = 'bancos' THEN 'Bancos'
        ELSE  bd.nombre_tabla
      END as modifico_en,
      cb.mensaje, u.*
      FROM bitacora_bd as bd 
      inner join codigo_bitacora as cb on cb.idcodigo = bd.idcodigo
      LEFT JOIN 
      ( SELECT u.idusuario, vw_p.idpersona, vw_p.nombre_razonsocial as usuario_nombre,  vw_p.persona_nombre_completo as usuario_nombre_apellidos, vw_p.foto_perfil as usuario_foto_perfil, vw_p.cargo_trabajador as usuario_cargo_trabajador
        FROM usuario as u 
        INNER JOIN vw_persona_all as vw_p on vw_p.idpersona = u.idpersona
      ) as u on u.idusuario = bd.id_user
      where bd.estado = '1' $sql_filtro_fecha $sql_filtro_usuario $sql_filtro_modulo
      order by bd.created_at desc;";
      return ejecutarConsulta($sql);
    }      

    function mostrar_editar($idcuenta_bancaria){
      $sql = "SELECT cb.*,  b.nombre as banco_nombre, b.alias as banco_alias
      from cuenta_bancaria as cb
      INNER JOIN bancos as b on b.idbancos = cb.idbancos
      WHERE cb.idcuenta_bancaria = '$idcuenta_bancaria' ;";
      return ejecutarConsultaSimpleFila($sql);
    }    
    
   

    // ══════════════════════════════════════  S E L E C T 2  ══════════════════════════════════════     
    function select2_usuario(){
      $sql = "SELECT u.idusuario, vw_p.idpersona, vw_p.persona_nombre_completo as usuario_nombre_apellidos, vw_p.foto_perfil as usuario_foto_perfil, vw_p.cargo_trabajador as usuario_cargo_trabajador
      FROM usuario as u 
      INNER JOIN vw_persona_all as vw_p on vw_p.idpersona = u.idpersona ;";
      return ejecutarConsultaArray($sql);
    }  
    
    function select2_modulo(){
      $sql = "SELECT DISTINCT nombre_tabla, 
      CASE 
        WHEN nombre_tabla = 'prestamo' THEN 'Prestamo'
        WHEN nombre_tabla = 'producto' THEN 'Producto'
        WHEN nombre_tabla = 'prestamo_pago' THEN 'Pago de prestamo'
        WHEN nombre_tabla = 'prestamo_refrendo' THEN 'Pago de Refrendo'
        WHEN nombre_tabla = 'prestamo_reducir_capital' THEN 'Reducir Capital'
        WHEN nombre_tabla = 'prestamo_aumentar_capital' THEN 'Aumentar Capital'
        WHEN nombre_tabla = 'persona_cliente' THEN 'Cliente'
        WHEN nombre_tabla = 'persona_trabajador' THEN 'Trabajador'
        WHEN nombre_tabla = 'cuenta_bancaria' THEN 'Cuenta Bancaria'
        WHEN nombre_tabla = 'bancos' THEN 'Bancos'
        ELSE  nombre_tabla
      END as modifico_en
      FROM bitacora_bd order by 2;";
      return ejecutarConsultaArray($sql);
    } 
  }