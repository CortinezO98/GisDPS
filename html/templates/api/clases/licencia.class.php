<?php
    error_reporting(0);
    require_once 'conexion/conexion.php';
    require_once 'respuestas.class.php';

    class licencia extends conexion {
        private $licencia = "";
        private $mac = "";
        private $ip = "";
        private $estado = "";
        private $fecha_inicio = "";
        private $fecha_fin = "";
        private $token = "";

        public function obtenerDatosUsuario($licencia){
            $query = "SELECT `au_id`, `au_usuario`, `au_contrasena`, `au_vigencia_inicio`, `au_vigencia_fin`, `au_estado`, `au_registro_usuario`, `au_registro_fecha` FROM `administrador_api_usuario` WHERE `au_usuario`='$licencia'";
            $datos = parent::obtenerDatos($query);
            if (isset($datos[0]['au_usuario'])) {
                return $datos;
            } else {
                return 0;
            }
        }
    }
?>