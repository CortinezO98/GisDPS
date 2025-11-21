<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="<?php echo URL_MENU; ?>/dashboard">
        <i class="menu-icon fas fa-chart-pie"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Interacciones']) AND $_SESSION[APP_SESSION.'_session_modulos']['Interacciones']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#gestion-interacciones" aria-expanded="false" aria-controls="gestion-interacciones">
          <i class="menu-icon fas fa-tasks"></i>
          <span class="menu-title">Interacciones</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="gestion-interacciones">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/interacciones/interacciones?pagina=1&id=null&bandeja=<?php echo base64_encode('Hoy'); ?>">Registro Interacciones</a></li>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Interacciones-Configuración']) AND $_SESSION[APP_SESSION.'_session_modulos']['Interacciones-Configuración']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/interacciones/configuracion?pagina=1&id=null">Configuración</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#gestion-ocr" aria-expanded="false" aria-controls="gestion-ocr">
          <i class="menu-icon fas fa-file-pdf"></i>
          <span class="menu-title">Gestión OCR</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="gestion-ocr">
          <ul class="nav flex-column sub-menu">
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Gestión']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Gestión']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/gestion_ocr/familias_accion?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>&estado=null">FA-Gestión</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Consultas']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Consultas']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/gestion_ocr/familias_accion_consultas">FA-Consultas</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Consultas Enlace']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Consultas Enlace']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/gestion_ocr/familias_accion_consultas_enlace?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>&estado=null">FA-Consultas Enlace</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Usuarios']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión OCR-Usuarios']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/gestion_ocr/familias_accion_usuarios?pagina=1&id=null">FA-Usuarios</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#calidad" aria-expanded="false" aria-controls="calidad">
          <i class="menu-icon fas fa-user-check"></i>
          <span class="menu-title">Calidad</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="calidad">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/calidad/monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Mes Actual'); ?>">Monitoreos</a></li>

            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Calidad-Matriz Calidad']) AND $_SESSION[APP_SESSION.'_session_modulos']['Calidad-Matriz Calidad']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/calidad/matriz?pagina=1&id=null">Matriz de Calidad</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Calidad-Calculadora Muestral']) AND $_SESSION[APP_SESSION.'_session_modulos']['Calidad-Calculadora Muestral']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/calidad/cmuestral?pagina=1&id=null">Calculadora Muestral</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#gestion-turno" aria-expanded="false" aria-controls="gestion-turno">
          <i class="menu-icon fas fa-calendar-alt"></i>
          <span class="menu-title">Control Turnos</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="gestion-turno">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/turnos/turno_realizado?fechainicio=<?php echo base64_encode(date('Y-m-d'));?>&operacion=<?php echo base64_encode('Todos'); ?>&id=null">Turno Realizado</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión Conocimiento']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión Conocimiento']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#gestion-conocimiento" aria-expanded="false" aria-controls="gestion-conocimiento">
          <i class="menu-icon fas fa-book"></i>
          <span class="menu-title">Gestión Conocimiento</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="gestion-conocimiento">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/gestion_conocimiento/conocimiento">Gestión Conocimiento</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión Traslado IVA']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión Traslado IVA']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#traslado_iva" aria-expanded="false" aria-controls="traslado_iva">
          <i class="menu-icon fas fa-clipboard-list"></i>
          <span class="menu-title">Traslado IVA</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="traslado_iva">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/traslado_iva/casos?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>">Casos</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#gestion-canal-escrito" aria-expanded="false" aria-controls="gestion-canal-escrito">
          <i class="menu-icon fas fa-rectangle-list"></i>
          <span class="menu-title">Canal Escrito</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="gestion-canal-escrito">
          <ul class="nav flex-column sub-menu">
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Reparto']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Reparto']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito_reparto/reparto?pagina=1&id=null&bandeja=<?php echo base64_encode('Hoy'); ?>">Reparto</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Jóvenes Acción-Focalización']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Jóvenes Acción-Focalización']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito_jafocalizacion/jafocalizacion?pagina=1&id=null&bandeja=<?php echo base64_encode('Hoy'); ?>">Jóvenes en Acción<br>y Focalización</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-TMNC']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-TMNC']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito_tmnc/tmnc?pagina=1&id=null&bandeja=<?php echo base64_encode('Hoy'); ?>">Transferencias Monetarias<br>No Condicionadas</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Dashboard']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Dashboard']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito/reparto_estadisticas">Estadísticas</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Productividad']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Productividad']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito_productividad/reparto_estadisticas">Productividad</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Configuración']) AND $_SESSION[APP_SESSION.'_session_modulos']['Canal Escrito-Configuración']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito/configuracion?pagina=1&id=null">Configuración</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/canal_escrito_productividad/configuracion?pagina=1&id=null">Configuración Productividad</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Radicación']) AND $_SESSION[APP_SESSION.'_session_modulos']['Radicación']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#gestion-radicacion" aria-expanded="false" aria-controls="gestion-radicacion">
          <i class="menu-icon fas fa-envelope"></i>
          <span class="menu-title">Radicación</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="gestion-radicacion">
          <ul class="nav flex-column sub-menu">
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Radicación']) AND $_SESSION[APP_SESSION.'_session_modulos']['Radicación']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/radicados/buzon?pagina=1&id=null&bandeja=<?php echo base64_encode('Prioritarios'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">Buzón</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Radicación-Configuración']) AND $_SESSION[APP_SESSION.'_session_modulos']['Radicación-Configuración']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/radicados/buzon_plantillas?pagina=1&id=null">Plantillas</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Envíos WEB']) AND $_SESSION[APP_SESSION.'_session_modulos']['Envíos WEB']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#envios-web" aria-expanded="false" aria-controls="envios-web">
          <i class="menu-icon fas fa-paper-plane"></i>
          <span class="menu-title">Envíos WEB</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="envios-web">
          <ul class="nav flex-column sub-menu">
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Envíos WEB']) AND $_SESSION[APP_SESSION.'_session_modulos']['Envíos WEB']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/envios_web/correo?pagina=1&id=null&bandeja=<?php echo base64_encode('Todos'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">Correo</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Envíos WEB-Configuración']) AND $_SESSION[APP_SESSION.'_session_modulos']['Envíos WEB-Configuración']!=""): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo URL_MENU; ?>/envios_web/plantillas?pagina=1&id=null">Plantillas</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Administrador']) AND $_SESSION[APP_SESSION.'_session_modulos']['Administrador']!=""): ?>
      <li class="nav-item nav-category">Plataforma</li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#administrador" aria-expanded="false" aria-controls="administrador">
          <i class="menu-icon fas fa-cogs"></i>
          <span class="menu-title">Administrador</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="administrador">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a href="<?php echo URL_MENU; ?>/administrador/usuarios?pagina=1&id=null" class="nav-link" href="">Usuarios</a></li>
            <li class="nav-item"> <a href="<?php echo URL_MENU; ?>/administrador/areas?pagina=1&id=null" class="nav-link" href="">Áreas</a></li>
            <li class="nav-item"> <a href="<?php echo URL_MENU; ?>/administrador/ubicaciones?pagina=1&id=null" class="nav-link" href="">Ubicaciones</a></li>
            <li class="nav-item"> <a href="<?php echo URL_MENU; ?>/administrador/buzones?pagina=1&id=null" class="nav-link" href="">Buzones Correo</a></li>
            <li class="nav-item"> <a href="<?php echo URL_MENU; ?>/administrador/notificaciones_correo?pagina=1&id=null" class="nav-link" href="">Notificaciones Correo</a></li>
            <li class="nav-item"> <a href="<?php echo URL_MENU; ?>/administrador/logs?pagina=1&id=null" class="nav-link" href="">Logs</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <!-- <li class="nav-item nav-category">Ayuda</li>
    <li class="nav-item">
      <a class="nav-link" href="">
        <i class="menu-icon fas fa-circle-question"></i>
        <span class="menu-title">Manual de usuario</span>
      </a>
    </li> -->
  </ul>
</nav>
