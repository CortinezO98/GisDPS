<?php
    require_once 'conexion/conexion.php';
    require_once 'respuestas.class.php';
    require_once 'token.class.php';
    require_once 'trasladoivacaso.class.php';

    class trasladoiva extends conexion {
        private $token = "";
        private $mensaje_file = "";
        private $fichero_estado = "";




        /**
         * MIN 9 longitud
max 17 1327_573144867555
         */





        public function post($datos, $files) {
            $_respuestas = new respuestas;
            $_token = new token;
            $_trasladoivacaso = new trasladoivacaso;
            // $datos = json_decode($json,true);
            if (empty($datos['token'])) {
                return $_respuestas->error_401();
            } else {
                $this->token = parent::sanitizar($datos['token']);
                
                $arrayToken = $_token->buscarToken($this->token);
                if ($arrayToken[0]['estado_token']) {
                    if (empty($datos['token']) || empty($datos['interaccion_id']) || empty($datos['interaccion_fecha']) || empty($datos['remitente']) || empty($datos['cliente_identificacion']) || empty($datos['cliente_nombre']) || empty($datos['titular_cedula']) || empty($datos['titular_fecha_expedicion']) || empty($datos['beneficiario_identificacion']) || empty($datos['departamento']) || empty($datos['municipio']) || empty($datos['direccion']) || empty($files)) {
                        return $_respuestas->error_400();
                    } else {
                        $fileName  =  $files['name'];
                        $tempPath  =  $files['tmp_name'];
                        $fileSize  =  $files['size'];
                        $upload_path = '../traslado_iva/storage/'.$datos['interaccion_id'].'/'; // ruta guardar ficheros
                        $upload_path_save = $datos['interaccion_id'].'/'.$fileName;
                        
                        $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // obtiene extensión fichero
                            
                        // extensiones válidas
                        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'pdf'); 
                        
                        // valida formato permitido
                        if(in_array($fileExt, $valid_extensions)) {
                            if (!file_exists($upload_path)) {
                                mkdir($upload_path, 0777, true);
                            }

                            //verifica que no exista fichero con el mismo nombre
                            if(!file_exists($upload_path . $fileName)) {
                                // check file size '10MB'
                                if($fileSize < 10000000){
                                    move_uploaded_file($tempPath, $upload_path . $fileName); // move file from system temporary path to our upload folder path 
                                    $this->mensaje_file = 'Fichero cargado exitosamente';
                                    $this->fichero_estado = 1;
                                    $datos['ruta_fichero']=$upload_path_save;
                                } else{       
                                    $this->mensaje_file = 'El fichero excede el tamaño permitido 10MB';
                                }
                            } else {
                                $this->mensaje_file = 'El fichero se encuentra duplicado';
                            }
                        } else {
                            $this->mensaje_file = 'Extensión o formato de fichero no permitida';   
                        }

                        if ($this->fichero_estado) {
                            //Crear caso
                            $verificar = $_trasladoivacaso->insertarCaso($datos);
                            if ($verificar) {
                                // $resp = $this->insertarLog();
                                $result = $_respuestas->error_200();
                                $result['ruta_fichero'] = 'https://dps.iq-online.net.co/traslado_iva/storage/'.$upload_path_save;
                                return $result;
                            } else {
                                unlink($upload_path . $fileName);
                                //error al guardar registro
                                return $_respuestas->error_500("Error interno, No hemos podido generar el caso");
                            }
                        } else {
                            //error al cargar el fichero
                            return $_respuestas->error_400($this->mensaje_file);
                        }

                    }
                } else {
                    return $_respuestas->error_401('El token enviado es inválido o ha caducado');
                }
            }
        }

        private function insertarLog() {
            $this->fecha_inicio = date('Y-m-d H:i:s');
            $this->fecha_fin = '';
            $this->estado = 'Activo';
            $query = "INSERT INTO `tb_workplace_licencias_log`(`wll_licencia`, `wll_mac`, `wll_ip`, `wll_estado`, `wll_fecha_inicio`, `wll_fecha_fin`) VALUES ('".$this->licencia."','".$this->mac."','".$this->ip."','".$this->estado."','".$this->fecha_inicio."','".$this->fecha_fin."')";
            $resp = parent::nonQueryId($query);
            if ($resp) {
                return $resp;
            } else {
                return 0;
            }
        }
    }
?>