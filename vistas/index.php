<?php  

    $ruta = ""; $file_go = isset($_GET["file"]) ? $_GET["file"] : "";

    function enrutamiento($tipo, $file) {
        if ($tipo == 'nube') {
            $link_host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/vistas/login.php?file='.$file;
        }else{
            if ($tipo == 'local') {
                $link_host = "http://localhost/libreria_sistema/vistas/login.php?file=".$file;
            }            
        }
        return $link_host;
    }

    if ($_SERVER['HTTP_HOST'] == "localhost") {
        $ruta = enrutamiento('local', $file_go);
    } else {
        $ruta = enrutamiento('nube', $file_go);
    }  

    header("Location: $ruta");
?>