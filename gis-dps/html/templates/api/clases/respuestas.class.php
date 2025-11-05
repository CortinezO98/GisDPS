<?php
    class respuestas {
        public $response = [
            'status' => 'ok'
        ];

        public function error_200($valor="Transacción exitosa"){
            $this->response['status_id'] = '200';
            $this->response['mensaje'] = $valor;
            return $this->response;
        }
        public function error_400($valor="Datos enviados incompletos o con formato incorrecto"){
            $this->response['status'] = 'error';
            $this->response['status_id'] = '400';
            $this->response['mensaje'] = $valor;
            return $this->response;
        }

        public function error_401($valor="No autorizado"){
            $this->response['status'] = 'error';
            $this->response['status_id'] = '401';
            $this->response['mensaje'] = $valor;
            return $this->response;
        }

        public function error_405(){
            $this->response['status'] = 'error';
            $this->response['status_id'] = '405';
            $this->response['mensaje'] = 'Método no permitido';
            return $this->response;
        }

        public function error_500($valor="Error interno servidor"){
            $this->response['status'] = 'error';
            $this->response['status_id'] = '500';
            $this->response['mensaje'] = $valor;
            return $this->response;
        }
    }
?>