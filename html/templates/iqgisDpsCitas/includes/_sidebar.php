<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="<?php echo URL_MENU; ?>/dashboard">
        <i class="menu-icon fas fa-chart-pie"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas']) AND $_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#agendamiento" aria-expanded="false" aria-controls="agendamiento">
          <i class="menu-icon fas fa-calendar-check"></i>
          <span class="menu-title">Agendamiento Citas</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="agendamiento">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento/citas?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>&fecha=<?php echo base64_encode(date('Y-').'W'.date('W'));?>">Citas</a></li>
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento/atencion_scita?pagina=1&id=null&bandeja=<?php echo base64_encode('Hoy'); ?>">Atención sin cita</a></li>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas-Punto Atención']) AND $_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas-Punto Atención']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento/punto_atencion?pagina=1&id=null">Puntos de Atención</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas-Agenda']) AND $_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas-Agenda']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento/agenda?pagina=1&id=null">Agenda</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas FA']) AND $_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas FA']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#agendamiento_fa" aria-expanded="false" aria-controls="agendamiento_fa">
          <i class="menu-icon fas fa-calendar-days"></i>
          <span class="menu-title">Agendamiento Citas FA</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="agendamiento_fa">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento_fa/citas_fa?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>&fecha=<?php echo base64_encode(date('Y-').'W'.date('W'));?>">Citas</a></li>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas FA-Punto Atención']) AND $_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas FA-Punto Atención']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento_fa/punto_atencion_fa?pagina=1&id=null">Puntos de Atención</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas FA-Agenda']) AND $_SESSION[APP_SESSION.'_session_modulos']['Agendamiento Citas FA-Agenda']!=""): ?>
              <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/agendamiento_fa/agenda_fa?pagina=1&id=null">Agenda</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Gestión Kioscos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Gestión Kioscos']!=""): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#kioscos" aria-expanded="false" aria-controls="kioscos">
          <i class="menu-icon fas fa-location-dot"></i>
          <span class="menu-title">Gestión Kioscos</span>
          <i class="menu-arrow fas fa-angle-right"></i> 
        </a>
        <div class="collapse" id="kioscos">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?php echo URL_MENU; ?>/gestion_kioscos/programas?pagina=1&id=null">Programas</a></li>
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