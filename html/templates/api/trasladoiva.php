<?php
    require_once 'clases/respuestas.class.php';
    require_once 'clases/trasladoiva.class.php';

    $_trasladoiva = new trasladoiva;
    $_respuestas = new respuestas;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    if ($_SERVER['REQUEST_METHOD']=="POST") {
        //Recibir datos
        // $postBody = file_get_contents("php://input");
        //enviar datos a manejador
        $datosPeticion['interaccion_id']=$_POST['interaccion_id'];
        $datosPeticion['interaccion_fecha']=$_POST['interaccion_fecha'];
        $datosPeticion['remitente']=$_POST['remitente'];
        $datosPeticion['cliente_identificacion']=$_POST['cliente_identificacion'];
        $datosPeticion['cliente_nombre']=$_POST['cliente_nombre'];
        $datosPeticion['titular_cedula']=$_POST['titular_cedula'];
        $datosPeticion['titular_fecha_expedicion']=$_POST['titular_fecha_expedicion'];
        $datosPeticion['beneficiario_identificacion']=$_POST['beneficiario_identificacion'];
        $datosPeticion['link_foto']='';
        $datosPeticion['departamento']=$_POST['departamento'];
        $datosPeticion['municipio']=$_POST['municipio'];
        $datosPeticion['direccion']=$_POST['direccion'];
        // $datosPeticion['token']=$_POST['token'];
        $datosPeticion['token']=$_SERVER['HTTP_API_TK'];
        $datosArray = $_trasladoiva->post($datosPeticion, $_FILES['file_name']);
        //devolvemos respuesta
        header('Content-Type: application/json');
        if (isset($datosArray['result']['error_id'])) {
            $responseCode = $datosArray['result']['error_id'];
            http_response_code($responseCode);
        } else {
            http_response_code(200);
        }
        echo json_encode($datosArray);
    } else {
        header('Content-Type: application/json');
        $datosArray = $_respuestas->error_405();
        echo json_encode($datosArray);
    }
?>