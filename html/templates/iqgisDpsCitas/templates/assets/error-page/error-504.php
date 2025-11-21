<?php
  require_once("../../../iniciador_index.php");
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>

  <style type="text/css">
    .text-left {
      text-align: left !important;
    }

    .text-right {
      text-align: right !important;
    }

    .font-weight-bold {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center text-center error-page bg-dark">
        <div class="row flex-grow">
          <div class="col-lg-7 mx-auto text-white">
            <div class="row align-items-center d-flex flex-row">
              <div class="col-lg-6 text-right pr-lg-4">
                <h1 class="display-1 mb-0">504</h1>
              </div>
              <div class="col-lg-6 text-left pl-lg-4">
                <h2 class="font-weight-bold">¡LO SIENTO!</h2>
                <h3 class="">Tiempo de espera agotado.</h3>
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-12 text-center mt-xl-2">
                <a class="text-white font-weight-medium" href="<?php echo URL_MENU; ?>">Regresar al inicio</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>