<?php
	error_reporting(0);
    ini_set('display_errors', '0');

    // ¿Estamos en local (127.0.0.1) o en el servidor remoto?
    define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

    // Definir la zona horaria
    date_default_timezone_set('America/Bogota');

    // Lenguaje por defecto
    define('LANG', 'es');

    // Color principal de la app
    define('COLOR_PRINCIPAL', '#1C2262');

    // Nombres de la aplicación
    define('APP_SESSION',      'iqgis-dps');
    define('APP_NAME',         'IQGIS-DPS');
    define('APP_NAME_LOGIN',   'Departamento para la Prosperidad Social');
    define('APP_NAME_ALL',     'Gestión Integrada de Servicio');
    define('CLIENT_NAME',      'IQ Outsourcing');

    // Ruta base de imágenes en el sistema de archivos (si necesitas rutas absolutas en PHP):
    define('BASEPATH_IMAGE',   '/var/www/html/templates/images');

    /**
     *  ─── RUTA BASE VISIBLE EN EL NAVEGADOR ───
     *
     *  Cuando esté en local, asumimos que el proyecto se sirve como
     *  http://localhost/iqgis-dps-citas/  (por ejemplo).
     *
     *  En producción, para tu VirtualHost, dijimos que el alias 
     *  es /iqgisDpsCitas → /home/kiosadmin/gis-dps/html/templates/iqgisDpsCitas
     *
     *  Por tanto, BASEPATH en producción debe ser "/iqgisDpsCitas/".
     */
    if (IS_LOCAL) {
        define('BASEPATH', '/iqgis-dps-citas/'); 
    } else {
        define('BASEPATH', '/templates/iqgisDpsCitas/');
    }

    // (opcional) Sal (“salt”) si usas hashing propio
    define('AUTH_SALT', '');

    // URL pública principal (raíz) de la app
    if (IS_LOCAL) {
        define('URL', 'http://localhost/iqgis-dps-citas/');
        define('URL_MENU', 'http://localhost/iqgis-dps-citas/templates');
    } else {
        // Aunque en producción tu dominio sea portalkiosko.asdcloud.co,
        // tú “rewrite” te pone al VirtualHost /iqgisDpsCitas/ para este proyecto.
        define('URL', 'https://portalkiosko.asdcloud.co/templates/iqgisDpsCitas/');
        define('URL_MENU', 'https://portalkiosko.asdcloud.co/templates/iqgisDpsCitas/templates');
    }

    /**
     *  ─── RUTAS FÍSICAS EN EL SISTEMA DE ARCHIVOS ───
     *
     *  ROOT apunta a la carpeta raíz “app” de tu módulo,
     *  que en tu caso es /home/kiosadmin/gis-dps/html/templates/iqgisDpsCitas/app:
     */
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', dirname(dirname(dirname(__FILE__))) . DS);
    //    └→ __FILE__ es app/config/config.php
    //        dirname(dirname(dirname(__FILE__))) = /home/kiosadmin/gis-dps/html/templates/iqgisDpsCitas/

    define('APP',      ROOT . 'app' . DS);
    define('CONFIG',   APP . 'config' . DS);
    define('FUNCTIONS', APP . 'functions' . DS);

    // La carpeta “templates” dentro de iqgisDpsCitas:
    define('TEMPLATES', ROOT . 'templates' . DS);
    define('INCLUDES',  TEMPLATES . 'includes' . DS);
    define('INCLUDES_ROOT', ROOT . 'includes' . DS);
    define('MODULES',   TEMPLATES . 'modules' . DS);

    // La carpeta “assets/plugins” dentro de iqgisDpsCitas:
    define('PLUGINS_ROOT', ROOT . 'assets' . DS . 'plugins' . DS);

    /**
     *  ─── RUTAS DE ARCHIVOS ESTÁTICOS PARA EL NAVEGADOR ───
     *
     *  Durante la ejecución de, por ejemplo, login.php, tú escribirás
     *    <link href="<?php echo ASSETS; ?>css/style.css">
     *  y el navegador solicitará:
     *    https://portalkiosko.asdcloud.co/iqgisDpsCitas/templates/assets/css/style.css
     *
     *  Por eso ASSETS = BASEPATH . 'templates/assets/'
     */
    define('ASSETS',  BASEPATH . 'templates/assets/');
    define('CSS',     ASSETS . 'css'    . '/');
    define('FAVICON', ASSETS . 'favicon' . '/');
    define('FONTS',   ASSETS . 'fonts'   . '/');
    define('IMAGES',  ASSETS . 'images'  . '/');
    define('PLUGINS', ASSETS . 'plugins' . '/');
    define('JS',      ASSETS . 'js'      . '/');
    define('UPLOADS', ASSETS . 'uploads' . '/');

    // Si necesitas la ruta ABSOLUTA a las imágenes en disco:
    define('IMAGES_ROOT',   ROOT . 'templates' . DS . 'assets' . DS . 'images' . DS);
    define('UPLOADS_ROOT',  ROOT . 'templates' . DS . 'assets' . DS . 'uploads' . DS);

    // Ejemplos de logos:
    define('LOGO_ENTIDAD',       IMAGES . 'logo-entidad.png');
    define('LOGO_ENTIDAD_ROOT',  ROOT . 'templates' . DS . 'assets' . DS . 'images' . DS . 'logo-entidad.png');

    define('LOGO_CLIENTE',       IMAGES . 'logo-cliente.png');
    define('LOGO_MINI',          IMAGES . 'logo-mini.png');
    define('LOGO_FAVICON',       FAVICON . 'favicon.ico');


    /**
     *  ─── CREDENCIALES DE LA BASE DE DATOS ───
     */
    if (IS_LOCAL) {
        // Local / desarrollo
        define('DB_ENGINE',  'mysql');
        define('DB_HOST',    'localhost');
        define('DB_NAME',    'iqgis-dps-citas');
        define('DB_USER',    'root');
        define('DB_PASS',    '');
        define('DB_CHARSET', 'utf8');
    } else {
        // Producción
        define('DB_ENGINE',  'mysql');
        define('DB_HOST',    'localhost');
        define('DB_NAME',    'iqgisdpscitas2');
        define('DB_USER',    'iqgis_citas2_usr');
        define('DB_PASS',    'Wqeastsvca*!_890');
        define('DB_CHARSET', 'utf8');
    }
?>
