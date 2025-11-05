<?php
    error_reporting(0);
    require_once 'clases/auth.class.php';
    require_once 'clases/respuestas.class.php';

    $_auth = new auth;
    $_respuestas = new respuestas;

    if ($_SERVER['REQUEST_METHOD']=="POST") {
        //Recibir datos de HEADERS
        // $postBody = file_get_contents("php://input");
        $licencia=$_SERVER['PHP_AUTH_USER'];
        $password=$_SERVER['PHP_AUTH_PW'];
        //enviar datos a manejador
        $datosArray = $_auth->login($licencia, $password);

        //devolvemos respuesta
        header('Content-Type: application/json');
        if (isset($datosArray['error_id'])) {
            // $datosArray['licencia']=$licencia;
            $responseCode = $datosArray['error_id'];
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