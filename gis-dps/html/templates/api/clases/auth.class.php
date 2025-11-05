<?php
    error_reporting(0);
    require_once 'conexion/conexion.php';
    require_once 'respuestas.class.php';
    require_once 'licencia.class.php';
    require_once 'token.class.php';

    class auth extends conexion {
        public function login($licencia, $password){
            $_respuestas = new respuestas;
            $_licencia = new licencia;
            $_token = new token;
            // $datos = json_decode($json, true);
            if (!isset($licencia) || $licencia=="" || !isset($password) || $password=="") {
                //Error con los campos
                return $_respuestas->error_400();
            } else {
                //Todo está bien
                // $password = parent::encriptar($password);
                $datos = $_licencia->obtenerDatosUsuario($licencia);
                if ($datos) {
                    //Verificar si la contraseña es correcta
                    if ($password == $datos[0]['au_contrasena']) {
                        if ($datos[0]['au_estado'] == 'Activo') {
                            if ($datos[0]['au_vigencia_inicio']<=date('Y-m-d')) {
                                if ($datos[0]['au_vigencia_fin']>=date('Y-m-d')) {
                                    //Crear token
                                    $verificar = $_token->insertarToken($datos[0]['au_id']);
                                    if ($verificar) {
                                        $result = $_respuestas->error_200();
                                        $result['token'] = $verificar['token'];
                                        $result['expira'] = $verificar['expira'];
                                        return $result;
                                    } else {
                                        //error al guardar token
                                        return $_respuestas->error_500("Error interno, No hemos podido generar el token");    
                                    }
                                } else {
                                    //Usuario inactivo
                                    return $_respuestas->error_401("Licencia expirada");
                                }
                            } else {
                                //Usuario inactivo
                                return $_respuestas->error_401("Licencia no disponible");
                            }                            
                        } else {
                            //Usuario inactivo
                            return $_respuestas->error_401("Licencia no disponible");
                        }
                    } else {
                        //Contraseña incorrecta
                        return $_respuestas->error_401();
                    }
                } else {
                    //No existe la licencia
                    return $_respuestas->error_401();
                }
            }
        }

        
    }

?>