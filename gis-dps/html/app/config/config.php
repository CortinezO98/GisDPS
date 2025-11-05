<?php
	error_reporting(0);
    ini_set('display_errors', '0');

    //Saber si estamos trabajando de forma local o remota
    define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

    //Definir la zona horaria del sistema
    date_default_timezone_set('America/Bogota');
    
    //Definir lenguaje
    define('LANG', 'es');

    //Definir COLOR PRINCIPAL
    define('COLOR_PRINCIPAL', '#1C2262');

    //Definir Variables App
    define('APP_SESSION', 'iqgis-dps');
    define('APP_NAME', 'IQGIS-DPS');
    define('APP_NAME_LOGIN', 'Departamento para la Prosperidad Social');
    define('APP_NAME_ALL', 'Gestión Integrada de Servicio');
    define('CLIENT_NAME', 'IQ Outsourcing');
    define('BASEPATH_IMAGE', '/var/www/html/templates/images');

    //Ruta base del proyecto
    define('BASEPATH', IS_LOCAL ? '/iqgis-front/templates/' : '/templates/');

    //Sal del sistema
    define('AUTH_SALT', '');

    //Url del sitio
    define('URL', IS_LOCAL ? 'http://localhost/iqgis-front/templates' : 'https://portalkiosko.asdcloud.co/templates');
    define('URL_MENU', IS_LOCAL ? 'http://localhost/iqgis-front/templates' : 'https://portalkiosko.asdcloud.co/templates');
    
    //Rutas de directorios y archivos
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', dirname(dirname(dirname(__FILE__))).DS);

    define('APP', ROOT.'app'.DS);
    define('CONFIG', APP.'config'.DS);
    define('FUNCTIONS', APP.'functions'.DS);
    
    define('TEMPLATES', ROOT.'templates'.DS);
    define('INCLUDES', TEMPLATES.'includes'.DS);
    define('INCLUDES_ROOT', ROOT.'includes'.DS);
    define('MODULES', TEMPLATES.'modules'.DS);
    define('PLUGINS_ROOT', ROOT.'assets/plugins/');

    //Rutas de archivos o assets con base URL
    define('ASSETS', BASEPATH.'assets/');
    define('CSS', ASSETS.'css/');
    define('FAVICON', ASSETS.'favicon/');
    define('FONTS', ASSETS.'fonts/');
    define('IMAGES', ASSETS.'images/');
    define('IMAGES_ROOT', ROOT.'templates/assets/images/');
    define('PLUGINS', ASSETS.'plugins/');
    define('JS', ASSETS.'js/');
    define('UPLOADS', ASSETS.'uploads/');
    define('UPLOADS_ROOT', ROOT.'templates/assets/uploads/');

    define('LOGO_ENTIDAD', IMAGES.'logo-entidad.png');
    define('LOGO_ENTIDAD_ROOT', ROOT.'templates/assets/images/logo-entidad.png');
    define('LOGO_CLIENTE', IMAGES.'logo-cliente.png');
    define('LOGO_MINI', IMAGES.'logo-mini.png');
    define('LOGO_FAVICON', FAVICON.'favicon.ico');
    
    if (IS_LOCAL) {
        //Credenciales BBDD local o desarrollo
        define('DB_ENGINE', 'mysql');
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'iqgis-dps-2');
        define('DB_USER', 'root');
        define('DB_PASS', 'desarrollo2021');
        define('DB_CHARSET', 'utf8');
    } else {
        //Credenciales BBDD producción
        define('DB_ENGINE', 'mysql');
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'iqgis_dps_2');
        define('DB_USER', 'iqgis_admin');
        define('DB_PASS', 'Jmie6!Gj*Esa');
        define('DB_CHARSET', 'utf8');
    }
?>
