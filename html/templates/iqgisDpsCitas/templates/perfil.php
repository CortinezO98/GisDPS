<?php
    require_once("../iniciador_index.php");
    require_once("../security_session.php");
    /*VARIABLES*/
    $title = "Administrador";
    $subtitle = "Mi Perfil";
    $pagina=validar_input($_GET['pagina']);
    $parametros_add='';

    unset($_SESSION[APP_SESSION.'_session_password_recovery']);
    unset($_SESSION[APP_SESSION.'_session_cambiar_foto']);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT.'includes/_navbar.php'); ?>
    <!-- navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT.'includes/_sidebar.php'); ?>
      <!-- sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">
              <div class="row justify-content-center">
                <div class="col-lg-5 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="d-flex align-items-center justify-content-between mb-0">
                            <h4 class="card-title card-title-dash">Tu información</h4>
                          </div>
                          <div class="table-responsive">
                            <table class="table select-table">
                              <tbody>
                                <tr>
                                  <td>
                                    <img class="img-md" src="<?php echo IMAGES; ?><?php echo ($_SESSION[APP_SESSION.'_session_usu_foto']!='') ? $_SESSION[APP_SESSION.'_session_usu_foto'] : 'avatar/sin definir.png'; ?>" style="width: 100px; height: 100px;" alt="Profile image">
                                  </td>
                                  <td>
                                    <a href="cambiar-foto" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Cambiar foto">
                                      <i class="fas fa-image btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-lg-inline">Cambiar foto</span>
                                    </a>
                                    <a href="password-recovery" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Cambiar contraseña">
                                      <i class="fas fa-key btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-lg-inline">Cambiar contraseña</span>
                                    </a>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="fw-bold">
                                    Nombre completo
                                  </td>
                                  <td>
                                    <p class="fw-light text-muted mb-0"><?php echo $_SESSION[APP_SESSION.'_session_usu_nombre_completo']; ?></p>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="fw-bold">
                                    Documento de identidad
                                  </td>
                                  <td>
                                    <p class="fw-light text-muted mb-0"><?php echo $_SESSION[APP_SESSION.'_session_usu_id']; ?></p>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="fw-bold">
                                    Correo
                                  </td>
                                  <td>
                                    <p class="fw-light text-muted mb-0"><?php echo $_SESSION[APP_SESSION.'_session_usu_correo']; ?></p>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="fw-bold">
                                    Cargo/rol
                                  </td>
                                  <td>
                                    <p class="fw-light text-muted mb-0"><?php echo $_SESSION[APP_SESSION.'_session_usu_cargo']; ?></p>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- footer -->
        <?php require_once(ROOT.'includes/_footer.php'); ?>
        <!-- footer -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>