<?php
    if (
        !isset($_SESSION[APP_SESSION . '_session_usu_id'])
        || $_SESSION[APP_SESSION . '_session_usu_id'] === null
        || $_SESSION[APP_SESSION . '_session_usu_id'] === ""
    ) {
        header("Location: " . URL . "/login");
        exit;
    }
?>
