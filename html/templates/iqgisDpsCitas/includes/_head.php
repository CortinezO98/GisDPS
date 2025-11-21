<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="robots" content="noindex">
<title><?php echo APP_NAME; ?></title>
<link rel="stylesheet" href="<?php echo ASSETS; ?>bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo PLUGINS; ?>simple-line-icons/css/simple-line-icons.css">
<link rel="stylesheet" href="<?php echo PLUGINS; ?>fontawesome/css/all.css">
<link rel="stylesheet" href="<?php echo PLUGINS; ?>bootstrap-select/dist/css/bootstrap-select.min.css">
<!-- <link rel="stylesheet" href="<?php echo PLUGINS; ?>feather/feather.css"> -->
<!-- <link rel="stylesheet" href="<?php echo PLUGINS; ?>ti-icons/css/themify-icons.css"> -->
<!-- <link rel="stylesheet" href="<?php echo PLUGINS; ?>typicons/typicons.css"> -->
<style type="text/css">
    .dropdown-menu .show {
      max-width: 400px !important;
    }
</style>
<link rel="stylesheet" href="<?php echo CSS; ?>style.css?v=1">
<link rel="shortcut icon" href="<?php echo LOGO_FAVICON; ?>" />
<script src="<?php echo PLUGINS; ?>sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function alertButton(tipo, titulo=null, mensaje=null, url=null) {
      if (tipo=='success' && (url=='' || url==null)) {
        swal({
          title: titulo,
          text: mensaje,
          icon: "success",
          button: "Aceptar",
        });
      } else if (tipo=='success' && url!='' && url!=null) {
        swal({
          title: titulo,
          text: mensaje,
          icon: "success",
          button: "Aceptar",
        })
        .then((value) => {
          location.href = url;
        });
      } else if (tipo=='error') {
        swal({
          title: titulo,
          text: mensaje,
          icon: "warning",
          button: "Aceptar",
        });
      } else if (tipo=='cancel') {
        swal({
          title: "Cancelar",
          text: "¿Desea cancelar el registro?",
          icon: "warning",
          buttons: true,
          buttons: ["No, continuar", "Si, cancelar"],
          dangerMode: true,
        })
        .then((willCancel) => {
          if (willCancel) {
            location.href = url;
          }
        });
      }
    }
</script>