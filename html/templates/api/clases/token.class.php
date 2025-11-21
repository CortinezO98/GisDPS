<?php
    error_reporting(0);
    require_once 'conexion/conexion.php';
    class token extends conexion {
        public function insertarToken($licencia) {
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
            $date = date('Y-m-d H:i:s');
            $expira = date("Y-m-d H:i:s", strtotime("+ 1 hour", strtotime($date)));
            $estado = 'Activo';
            $query = "INSERT INTO `administrador_api_usuario_token`(`aut_usuario`, `aut_token`, `aut_estado`, `aut_fecha_registro`, `aut_fecha_expira`) VALUES ('".$licencia."','".$token."','".$estado."','".$date."','".$expira."')";
            $verifica = parent::nonQuery($query);
            if ($verifica==1) {
                $datostoken['token'] = $token;
                $datostoken['expira'] = $expira;
                return $datostoken;
            } else {
                return 0;
            }
        }

        public function buscarToken($token) {
            $query = "SELECT `aut_id`, `aut_usuario`, `aut_token`, `aut_estado`, `aut_fecha_registro`, `aut_fecha_expira` FROM `administrador_api_usuario_token` WHERE `aut_token`='".$token."'";
            
            $resp = parent::obtenerDatos($query);
            if ($resp) {
                if (date('Y-m-d H:i:s')<$resp[0]['aut_fecha_expira']) {
                    $resp[0]['estado_token'] = 1;    
                } else {
                    $resp[0]['estado_token'] = 0;    
                }
                
                return $resp;
            } else {
                return 0;
            }
        }
        
        public function actualizarToken($fecha) {
            $query = "UPDATE `administrador_api_usuario_token` SET `aut_estado`='Inactivo' WHERE `aut_estado`='Activo' AND `aut_fecha_expira`<'$fecha'";
            $verificar = parent::nonQuery($query);
            if ($verificar > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }
?>