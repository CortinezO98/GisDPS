<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="fas fa-bars"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="<?php echo URL; ?>">
        <img src="<?php echo LOGO_CLIENTE; ?>" alt="logo" />
      </a>
      <a class="navbar-brand brand-logo-mini" href="<?php echo URL; ?>">
        <img src="<?php echo LOGO_MINI; ?>" alt="logo" />
      </a>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-top"> 
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-sm-block ms-0">
        <h1 class="welcome-text font-size-12"><?php echo $title; ?></h1>
        <h3 class="welcome-sub-text font-size-11"><?php echo $subtitle; ?></h3>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <!-- <li class="nav-item dropdown"> 
        <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="icon-bell"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
          <a class="dropdown-item py-3">
            <p class="mb-0 font-weight-medium float-left">You have 7 unread mails </p>
            <span class="badge badge-pill badge-primary float-right">View all</span>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="<?php echo IMAGES; ?>faces/face10.jpg" alt="image" class="img-sm profile-pic">
            </div>
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">Marian Garner </p>
              <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="<?php echo IMAGES; ?>faces/face12.jpg" alt="image" class="img-sm profile-pic">
            </div>
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">David Grey </p>
              <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="<?php echo IMAGES; ?>faces/face1.jpg" alt="image" class="img-sm profile-pic">
            </div>
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">Travis Jenkins </p>
              <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
            </div>
          </a>
        </div>
      </li> -->
      <li class="nav-item dropdown d-none d-lg-block user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="<?php echo IMAGES; ?><?php echo ($_SESSION[APP_SESSION.'_session_usu_foto']!='') ? $_SESSION[APP_SESSION.'_session_usu_foto'] : 'avatar/sin definir.png'; ?>" alt="Profile image"> </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-md rounded-circle" src="<?php echo IMAGES; ?><?php echo ($_SESSION[APP_SESSION.'_session_usu_foto']!='') ? $_SESSION[APP_SESSION.'_session_usu_foto'] : 'avatar/sin definir.png'; ?>" style="width: 100px; height: 100px;" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold"><?php echo $_SESSION[APP_SESSION.'_session_usu_nombre_completo']; ?></p>
            <p class="fw-light text-muted mb-0"><?php echo $_SESSION[APP_SESSION.'_session_usu_correo']; ?></p>
          </div>
          <a href="<?php echo URL_MENU; ?>/perfil" class="dropdown-item"><span class="fas fa-user me-2"></span> Mi perfil</a>
          <a href="<?php echo URL_MENU; ?>/actividad?pagina=1&id=null" class="dropdown-item"><span class="fas fa-history me-2"></span> Actividad</a>
          <a href="<?php echo URL_MENU; ?>/logout" class="dropdown-item"><span class="fas fa-sign-out me-2"></span>Cerrar sesión</a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="fas fa-bars"></span>
    </button>
  </div>
</nav>