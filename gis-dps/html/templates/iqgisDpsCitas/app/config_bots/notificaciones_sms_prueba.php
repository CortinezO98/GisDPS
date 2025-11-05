<?php
    $modulo_plataforma="Administrador";
    // require_once("/var/www/html/iniciador.php");
    require_once("../../iniciador.php");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    require_once("../../templates/assets/plugins/guzzle-master/vendor/autoload.php");

    $client = new GuzzleHttp\Client();
    $response = $client->post('https://api-sms.masivapp.com/send-message',
        ['json' => 
            [
                'to' => '573045599699',
                'text' => 'Mensaje de prueba, esta es una url => SHORTURL',
                'customdata' => 'Prueba',
                'isPremium' => false,
                'isFlash' => false,
                'isLongmessage' => false,
                'isRandomRoute' => false,
                'shortUrlConfig' => [
                    'url' => 'https://www.youtube.com'
                ]
            ],
            'auth' => ['UTASDIQDPS_0WRLJ', 'LE6am,s2vn']
        ]
    );

    $jsonData = json_decode($response->getBody(), true);

    echo "<pre>";
    print_r($jsonData);
    echo "</pre>";
    
    // print_r($response);
    
?>