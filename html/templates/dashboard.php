<?php
require_once("../iniciador_index.php");
require_once("../security_session.php");
unset($_SESSION[APP_SESSION.'_session_password_recovery']);
unset($_SESSION[APP_SESSION.'_session_password_update']);

// Reforzar estabilidad del dashboard (sin alterar UI)
@set_time_limit(0);
@ini_set('memory_limit', '768M');
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

/* VARIABLES */
$title = "Inicio";
$subtitle = "Dashboard";

/* =====================================================
 * 1) CONSULTA PERMISOS MÓDULOS (resultado pequeño)
 * ===================================================== */
$consulta_string_permisos = "
  SELECT AUMP.`per_id`, AUMP.`per_usuario`, AUMP.`per_modulo`, AUMP.`per_perfil`,
         AM.`mod_modulo_nombre`
    FROM `administrador_usuario_modulo_perfil` AUMP
    LEFT JOIN `administrador_modulo` AM
      ON AUMP.`per_modulo` = AM.`mod_id`
   WHERE AUMP.`per_usuario` = ?";

$consulta_registros_permisos = $enlace_db->prepare($consulta_string_permisos);
$consulta_registros_permisos->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
$consulta_registros_permisos->execute();
$consulta_registros_permisos->bind_result($per_id,$per_usuario,$per_modulo,$per_perfil,$mod_modulo_nombre);

unset($_SESSION[APP_SESSION.'_session_modulos']);
$_SESSION[APP_SESSION.'_session_modulos'] = [];

while ($consulta_registros_permisos->fetch()) {
  // Mismo mapeo que antes: [nombre_modulo] => perfil
  $_SESSION[APP_SESSION.'_session_modulos'][$mod_modulo_nombre] = $per_perfil;
}
$consulta_registros_permisos->close();

/* =====================================================
 * 2) CONSULTA CONTROL TURNOS (optimizado + parámetros)
 * ===================================================== */
if (isset($_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']) && $_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']!="") {

  //fecha y hora de servidor actual a variable de sesión para control turno
  $_SESSION[APP_SESSION.'session_turno_actual'] = date("Y-m-d H:i:s");

  $consulta_string_turnos = "
    SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`,
           `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`,
           `cot_observaciones_fin`, `cot_registro_fecha`
      FROM `control_turno`
     WHERE `cot_usuario`=? AND `cot_inicio` LIKE CONCAT(?, '%') AND `cot_fin`=''
  ORDER BY `cot_id` ASC";

  $consulta_registros_turnos = $enlace_db->prepare($consulta_string_turnos);
  $hoy = date('Y-m-d');
  $consulta_registros_turnos->bind_param("ss", $_SESSION[APP_SESSION.'_session_usu_id'], $hoy);
  $consulta_registros_turnos->execute();
  $consulta_registros_turnos->bind_result(
    $cot_id,$cot_usuario,$cot_tipo,$cot_inicio,$cot_fin,$cot_duracion,
    $cot_fuente,$cot_obs_ini,$cot_obs_fin,$cot_reg_fecha
  );

  $_SESSION[APP_SESSION.'session_turno_inicio']='';
  $_SESSION[APP_SESSION.'session_turno_fin']='';
  $_SESSION[APP_SESSION.'session_observaciones_inicio_turno']='';
  $_SESSION[APP_SESSION.'session_actividad_inicio']='';
  $_SESSION[APP_SESSION.'session_actividad_fin']='';
  $_SESSION[APP_SESSION.'session_actividad_tipo']='';

  while ($consulta_registros_turnos->fetch()) {
    if ($cot_tipo === 'turno') {
      $_SESSION[APP_SESSION.'session_turno_inicio'] = $cot_inicio;
      $_SESSION[APP_SESSION.'session_turno_fin'] = $cot_fin;
      $_SESSION[APP_SESSION.'session_observaciones_inicio_turno'] = $cot_obs_ini;
    } elseif (in_array($cot_tipo, ['break','almuerzo','pausaactiva','capacitacion','retroalimentacion'], true)) {
      $_SESSION[APP_SESSION.'session_actividad_inicio'] = $cot_inicio;
      $_SESSION[APP_SESSION.'session_actividad_fin'] = $cot_fin;
      $_SESSION[APP_SESSION.'session_actividad_tipo'] = $cot_tipo;
    }
  }
  $consulta_registros_turnos->close();
}

/* =====================================================
 * 3) TOP ACTIVIDAD (últimos 10) – pequeño
 * ===================================================== */
$consulta_string_actividad = "
  SELECT L.`clog_id`, L.`clog_log_modulo`, L.`clog_log_tipo`, L.`clog_log_accion`,
         L.`clog_log_detalle`, L.`clog_user_agent`, L.`clog_remote_addr`,
         L.`clog_remote_host`, L.`clog_script`, L.`clog_registro_usuario`,
         L.`clog_registro_fecha`, U.`usu_nombres_apellidos`
    FROM `administrador_log` L
    LEFT JOIN `administrador_usuario` U
      ON L.`clog_registro_usuario`=U.`usu_id`
   WHERE L.`clog_registro_usuario`=?
ORDER BY L.`clog_registro_fecha` DESC
   LIMIT 10";

$consulta_registros_actividad = $enlace_db->prepare($consulta_string_actividad);
$consulta_registros_actividad->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
$consulta_registros_actividad->execute();
$consulta_registros_actividad->bind_result(
  $clog_id,$clog_modulo,$clog_tipo,$clog_accion,$clog_detalle,$clog_ua,
  $clog_addr,$clog_host,$clog_script,$clog_user,$clog_fecha,$clog_nombre
);

// Mantener misma variable que usas después
$resultado_registros_actividad = [];
while ($consulta_registros_actividad->fetch()) {
  $resultado_registros_actividad[] = [
    $clog_id,$clog_modulo,$clog_tipo,$clog_accion,$clog_detalle,$clog_ua,
    $clog_addr,$clog_host,$clog_script,$clog_user,$clog_fecha,$clog_nombre
  ];
}
$consulta_registros_actividad->close();

/* =====================================================
 * 4) VALIDA EXPIRA CONTRASEÑA (1 registro)
 * ===================================================== */
$consulta_string_phistorial = "
  SELECT `auc_id`, `auc_usuario`, `auc_contrasena`, `auc_registro_fecha`
    FROM `administrador_usuario_contrasenas`
   WHERE `auc_usuario`=?
ORDER BY `auc_registro_fecha` DESC
   LIMIT 1";
$consulta_registros_phistorial = $enlace_db->prepare($consulta_string_phistorial);
$consulta_registros_phistorial->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
$consulta_registros_phistorial->execute();
$consulta_registros_phistorial->bind_result($auc_id,$auc_usuario,$auc_contra,$auc_fecha);

$fecha_control_pexpira = date("Y-m-d");
$fecha_control_paviso  = date("Y-m-d");
if ($consulta_registros_phistorial->fetch()) {
  $fecha_control_pexpira = date("Y-m-d", strtotime("+ 30 day", strtotime($auc_fecha)));
  $fecha_control_paviso  = date("Y-m-d", strtotime("+ 20 day", strtotime($auc_fecha)));
}
$consulta_registros_phistorial->close();

/* =====================================================
 * 5) DASHBOARD CALIDAD (cuando aplique)
 * ===================================================== */
if (isset($_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos']) && $_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos']!="") {
  // Inicialización EXACTA de variables usadas más adelante
  $data_consulta = [];
  $array_anio_mes_dias_num = [];
  $array_anio_mes_dias     = [];
  $array_gestion           = [];
  $array_gestion_monitores = [];
  $array_gestion_monitores_doc = [];
  $array_semanas           = [];
  $array_monitor_dia       = [];
  $array_monitor_dia_doc   = [];
  $array_usuarios          = [];
  $array_matrices          = [];
  $array_usuarios_detalle  = [];
  $array_matrices_detalle  = [];
  $array_usuario_monitoreos= [];

  // Filtro mensual
  $filtro_permanente = date('Y-m');
  $filtro_mes = " AND `gcm_registro_fecha` LIKE ? ";
  $data_consulta[] = "{$filtro_permanente}%";
  $modulo_plataforma = "Calidad-Monitoreos";
  $permisos_usuario = $_SESSION[APP_SESSION.'_session_modulos'][$modulo_plataforma] ?? '';

  // Perfil → filtro
  $filtro_perfil = "";
  if ($permisos_usuario === "Supervisor") {
    $filtro_perfil = " AND (TUA.`usu_supervisor`=? OR `gcm_analista`=?)";
    $data_consulta[] = $_SESSION[APP_SESSION.'_session_usu_id'];
    $data_consulta[] = $_SESSION[APP_SESSION.'_session_usu_id'];
  } elseif ($permisos_usuario === "Usuario") {
    $filtro_perfil = " AND `gcm_analista`=?";
    $data_consulta[] = $_SESSION[APP_SESSION.'_session_usu_id'];
  } // Administrador/Gestor/Coordinador Nacional => sin filtro adicional

  // Construir arrays de días del mes
  $anio_mes_separado = explode("-", $filtro_permanente);
  $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, (int)$anio_mes_separado[1], (int)$anio_mes_separado[0]);
  for ($k=1; $k <= $numero_dias_mes; $k++) {
    $array_anio_mes_dias_num[] = validar_cero($k);
    $array_anio_mes_dias[]     = $filtro_permanente."-".validar_cero($k);
  }

  /* =============================
   * 5.1) Gestión general (counts)
   * ============================= */
  // Monitoreos
  $sql_cnt = "
    SELECT COUNT(`gcm_id`)
      FROM `gestion_calidad_monitoreo`
      LEFT JOIN `administrador_usuario` AS TUA
        ON `gestion_calidad_monitoreo`.`gcm_analista`=TUA.`usu_id`
     WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' {$filtro_mes} {$filtro_perfil}";
  $stmt = $enlace_db->prepare($sql_cnt);
  $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $stmt->execute();
  $stmt->bind_result($cnt_moni); $stmt->fetch(); $stmt->close();

  // ECUF
  $sql_cnt_ecuf = "
    SELECT COUNT(`gcm_id`)
      FROM `gestion_calidad_monitoreo`
      LEFT JOIN `administrador_usuario` AS TUA
        ON `gestion_calidad_monitoreo`.`gcm_analista`=TUA.`usu_id`
     WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' AND `gcm_nota_ecuf_estado`='0'
       {$filtro_mes} {$filtro_perfil}";
  $stmt = $enlace_db->prepare($sql_cnt_ecuf);
  $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $stmt->execute();
  $stmt->bind_result($cnt_ecuf); $stmt->fetch(); $stmt->close();

  // ECN
  $sql_cnt_ecn = "
    SELECT COUNT(`gcm_id`)
      FROM `gestion_calidad_monitoreo`
      LEFT JOIN `administrador_usuario` AS TUA
        ON `gestion_calidad_monitoreo`.`gcm_analista`=TUA.`usu_id`
     WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' AND `gcm_nota_ecn_estado`='0'
       {$filtro_mes} {$filtro_perfil}";
  $stmt = $enlace_db->prepare($sql_cnt_ecn);
  $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $stmt->execute();
  $stmt->bind_result($cnt_ecn); $stmt->fetch(); $stmt->close();

  // ENC
  $sql_cnt_enc = "
    SELECT COUNT(`gcm_id`)
      FROM `gestion_calidad_monitoreo`
      LEFT JOIN `administrador_usuario` AS TUA
        ON `gestion_calidad_monitoreo`.`gcm_analista`=TUA.`usu_id`
     WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' AND `gcm_nota_enc_estado`='0'
       {$filtro_mes} {$filtro_perfil}";
  $stmt = $enlace_db->prepare($sql_cnt_enc);
  $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $stmt->execute();
  $stmt->bind_result($cnt_enc); $stmt->fetch(); $stmt->close();

  $array_gestion['monitoreos'] = (int)$cnt_moni;
  $array_gestion['ecuf']       = (int)$cnt_ecuf;
  $array_gestion['ecn']        = (int)$cnt_ecn;
  $array_gestion['enc']        = (int)$cnt_enc;

  if ($array_gestion['monitoreos'] > 0) {
    $mon = (float)$array_gestion['monitoreos'];
    $array_gestion['pecuf'] = (($mon - (float)$array_gestion['ecuf']) / $mon) * 100.0;
    $array_gestion['pecn']  = (($mon - (float)$array_gestion['ecn'])  / $mon) * 100.0;
    $array_gestion['penc']  = (($mon - (float)$array_gestion['enc'])  / $mon) * 100.0;
  } else {
    $array_gestion['pecuf'] = 0;
    $array_gestion['pecn']  = 0;
    $array_gestion['penc']  = 0;
  }

  /* ============================================================
   * 5.2) Bloque “Gestor”: por monitor, por día, matrices, semanas
   *     (reemplazo de fetch_all por recorrido incremental)
   * ============================================================ */
  if ($permisos_usuario === "Gestor") {

    // 5.2.1 Gestión por monitor
    $sql_gestion_monitor = "
      SELECT `gcm_registro_usuario`, TUR.`usu_nombres_apellidos`, COUNT(`gcm_id`)
        FROM `gestion_calidad_monitoreo`
        LEFT JOIN `administrador_usuario` AS TUR
          ON `gcm_registro_usuario`=TUR.`usu_id`
       WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' {$filtro_mes}
    GROUP BY `gcm_registro_usuario`
    ORDER BY TUR.`usu_nombres_apellidos` ASC";

    $stmt = $enlace_db->prepare($sql_gestion_monitor);
    $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $stmt->execute();
    $stmt->bind_result($gm_user,$gm_nombre,$gm_count);

    $array_gestion_monitores_doc = [];
    while ($stmt->fetch()) {
      $array_gestion_monitores[$gm_user]['monitoreos'] = (int)$gm_count;
      $array_gestion_monitores[$gm_user]['nombre']     = $gm_nombre;
      $array_gestion_monitores[$gm_user]['ecuf'] = 0;
      $array_gestion_monitores[$gm_user]['ecn']  = 0;
      $array_gestion_monitores[$gm_user]['enc']  = 0;
      $array_gestion_monitores[$gm_user]['pecuf']= 0;
      $array_gestion_monitores[$gm_user]['pecn'] = 0;
      $array_gestion_monitores[$gm_user]['penc'] = 0;
      $array_gestion_monitores_doc[] = $gm_user;
    }
    $stmt->close();

    // ECUF por monitor
    $sql_ecuf_m = "
      SELECT `gcm_registro_usuario`, COUNT(`gcm_id`)
        FROM `gestion_calidad_monitoreo`
        LEFT JOIN `administrador_usuario` AS TUR
          ON `gcm_registro_usuario`=TUR.`usu_id`
       WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' AND `gcm_nota_ecuf_estado`='0'
             {$filtro_mes}
    GROUP BY `gcm_registro_usuario`";
    $stmt = $enlace_db->prepare($sql_ecuf_m);
    $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $stmt->execute();
    $stmt->bind_result($m_user,$m_cnt);
    while ($stmt->fetch()) {
      if (!isset($array_gestion_monitores[$m_user])) {
        $array_gestion_monitores[$m_user] = ['monitoreos'=>0,'nombre'=>'','ecuf'=>0,'ecn'=>0,'enc'=>0,'pecuf'=>0,'pecn'=>0,'penc'=>0];
        $array_gestion_monitores_doc[] = $m_user;
      }
      $array_gestion_monitores[$m_user]['ecuf'] = (int)$m_cnt;
    }
    $stmt->close();

    // ECN por monitor
    $sql_ecn_m = "
      SELECT `gcm_registro_usuario`, COUNT(`gcm_id`)
        FROM `gestion_calidad_monitoreo`
        LEFT JOIN `administrador_usuario` AS TUR
          ON `gcm_registro_usuario`=TUR.`usu_id`
       WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' AND `gcm_nota_ecn_estado`='0'
             {$filtro_mes}
    GROUP BY `gcm_registro_usuario`";
    $stmt = $enlace_db->prepare($sql_ecn_m);
    $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $stmt->execute();
    $stmt->bind_result($m_user,$m_cnt);
    while ($stmt->fetch()) {
      if (!isset($array_gestion_monitores[$m_user])) {
        $array_gestion_monitores[$m_user] = ['monitoreos'=>0,'nombre'=>'','ecuf'=>0,'ecn'=>0,'enc'=>0,'pecuf'=>0,'pecn'=>0,'penc'=>0];
        $array_gestion_monitores_doc[] = $m_user;
      }
      $array_gestion_monitores[$m_user]['ecn'] = (int)$m_cnt;
    }
    $stmt->close();

    // ENC por monitor
    $sql_enc_m = "
      SELECT `gcm_registro_usuario`, COUNT(`gcm_id`)
        FROM `gestion_calidad_monitoreo`
        LEFT JOIN `administrador_usuario` AS TUR
          ON `gcm_registro_usuario`=TUR.`usu_id`
       WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' AND `gcm_nota_enc_estado`='0'
             {$filtro_mes}
    GROUP BY `gcm_registro_usuario`";
    $stmt = $enlace_db->prepare($sql_enc_m);
    $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $stmt->execute();
    $stmt->bind_result($m_user,$m_cnt);
    while ($stmt->fetch()) {
      if (!isset($array_gestion_monitores[$m_user])) {
        $array_gestion_monitores[$m_user] = ['monitoreos'=>0,'nombre'=>'','ecuf'=>0,'ecn'=>0,'enc'=>0,'pecuf'=>0,'pecn'=>0,'penc'=>0];
        $array_gestion_monitores_doc[] = $m_user;
      }
      $array_gestion_monitores[$m_user]['enc'] = (int)$m_cnt;
    }
    $stmt->close();

    // Calcular porcentajes por monitor
    $array_gestion_monitores_doc = array_values(array_unique($array_gestion_monitores_doc));
    foreach ($array_gestion_monitores_doc as $doc) {
      $moni = (float)($array_gestion_monitores[$doc]['monitoreos'] ?? 0);
      if ($moni > 0) {
        $array_gestion_monitores[$doc]['pecuf'] = (($moni - (float)$array_gestion_monitores[$doc]['ecuf']) / $moni) * 100.0;
        $array_gestion_monitores[$doc]['pecn']  = (($moni - (float)$array_gestion_monitores[$doc]['ecn'])  / $moni) * 100.0;
        $array_gestion_monitores[$doc]['penc']  = (($moni - (float)$array_gestion_monitores[$doc]['enc'])  / $moni) * 100.0;
      } else {
        $array_gestion_monitores[$doc]['pecuf'] = 0;
        $array_gestion_monitores[$doc]['pecn']  = 0;
        $array_gestion_monitores[$doc]['penc']  = 0;
      }
    }

    // 5.2.2 Gestión por monitor y día (sin fetch_all)
    $array_semanas = ['total_1'=>0,'total_2'=>0,'total_3'=>0,'total_4'=>0];

    $sql_monitor_dia = "
      SELECT `gcm_registro_usuario`, TUR.`usu_nombres_apellidos`, `gcm_registro_fecha`, COUNT(`gcm_id`)
        FROM `gestion_calidad_monitoreo`
        LEFT JOIN `administrador_usuario` AS TUR
          ON `gcm_registro_usuario`=TUR.`usu_id`
       WHERE 1=1 AND `gcm_aplica_indicador`='Si-Calidad' {$filtro_mes}
    GROUP BY `gcm_registro_usuario`, `gcm_registro_fecha`
    ORDER BY TUR.`usu_nombres_apellidos` ASC, `gcm_registro_fecha` ASC";
    $stmt = $enlace_db->prepare($sql_monitor_dia);
    $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $stmt->execute();
    $stmt->bind_result($md_user,$md_nombre,$md_fecha,$md_cnt);

    while ($stmt->fetch()) {
      $fecha_iter = date('Y-m-d', strtotime($md_fecha));
      if (!isset($array_monitor_dia[$md_user])) {
        $array_monitor_dia[$md_user] = ['monitoreos'=>[],'nombre'=>$md_nombre];
        $array_monitor_dia_doc[] = $md_user;
      }
      $array_monitor_dia[$md_user]['monitoreos'][$fecha_iter] = (int)$md_cnt;

      $dia_recorre = (int)date('d', strtotime($md_fecha));
      if ($dia_recorre>=1 && $dia_recorre<=6)   { $array_semanas['total_1'] += (int)$md_cnt; }
      if ($dia_recorre>=7 && $dia_recorre<=13) { $array_semanas['total_2'] += (int)$md_cnt; }
      if ($dia_recorre>=14 && $dia_recorre<=20){ $array_semanas['total_3'] += (int)$md_cnt; }
      if ($dia_recorre>=21)                    { $array_semanas['total_4'] += (int)$md_cnt; }
    }
    $stmt->close();

    // Asegurar días con 0
    foreach ($array_monitor_dia as $usr => $info) {
      foreach ($array_anio_mes_dias as $dia) {
        if (!isset($array_monitor_dia[$usr]['monitoreos'][$dia])) {
          $array_monitor_dia[$usr]['monitoreos'][$dia] = 0;
        }
      }
    }
    $array_monitor_dia_doc = array_values(array_unique($array_monitor_dia_doc));

    $array_semanas['rango_1'] = '01 al 06 de '.$filtro_permanente;
    $array_semanas['rango_2'] = '07 al 13 de '.$filtro_permanente;
    $array_semanas['rango_3'] = '14 al 20 de '.$filtro_permanente;
    $array_semanas['rango_4'] = '21 al '.$numero_dias_mes.' de '.$filtro_permanente;

    // 5.2.3 Monitoreos por matriz/usuario
    $consulta_string = "
      SELECT TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_registro_usuario`,
             TUR.`usu_nombres_apellidos`, COUNT(TMC.`gcm_id`)
        FROM `gestion_calidad_monitoreo` AS TMC
        LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id`
        LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id`
       WHERE 1=1 ".str_replace('AND ', 'AND TMC.', $filtro_mes)."
    GROUP BY TMC.`gcm_matriz`, TMC.`gcm_registro_usuario`";

    $stmt = $enlace_db->prepare($consulta_string);
    $stmt->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $stmt->execute();
    $stmt->bind_result($m_id,$m_nombre,$u_id,$u_nombre,$cnt_mat);

    while ($stmt->fetch()) {
      $array_usuarios[] = $u_id;
      $array_matrices[] = $m_id;
      $array_usuarios_detalle[$u_id]['nombre'] = $u_nombre;
      if (!isset($array_matrices_detalle[$m_id]['nombre_matriz'])) {
        $array_matrices_detalle[$m_id]['nombre_matriz'] = $m_nombre;
      }
      if (!isset($array_matrices_detalle[$m_id]['cantidad'])) {
        $array_matrices_detalle[$m_id]['cantidad'] = 0;
      }
      $array_matrices_detalle[$m_id]['cantidad'] += (int)$cnt_mat;
      if (!isset($array_usuario_monitoreos[$m_id][$u_id])) {
        $array_usuario_monitoreos[$m_id][$u_id] = 0;
      }
      $array_usuario_monitoreos[$m_id][$u_id] += (int)$cnt_mat;
    }
    $stmt->close();

    $array_usuarios = array_values(array_unique($array_usuarios));
    $array_matrices = array_values(array_unique($array_matrices));

    // Asegurar ceros
    foreach ($array_matrices as $mid) {
      foreach ($array_usuarios as $uid) {
        if (!isset($array_usuario_monitoreos[$mid][$uid])) {
          $array_usuario_monitoreos[$mid][$uid] = 0;
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <?php require_once(ROOT.'includes/_head-charts.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only" onload="
        <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']!=''): ?>
            incremento_hora_actual();
        <?php
            if (!empty($_SESSION[APP_SESSION.'session_turno_inicio']) && empty($_SESSION[APP_SESSION.'session_turno_fin'])) {
                echo 'cronometro_turno();';
            }
        ?>
        <?php
            if (!empty($_SESSION[APP_SESSION.'session_actividad_inicio']) && empty($_SESSION[APP_SESSION.'session_actividad_fin'])) {
                echo 'cronometro_actividad();';
            }
        ?>
        <?php endif; ?>
">
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
              <div class="row">
                <div class="col-lg-12 d-flex flex-column">
                  <?php if (date('Y-m-d') >= $fecha_control_paviso): ?>
                    <p class="alert alert-warning py-1">¡Recuerde que su contraseña expira el <b><?php echo date('d/m/Y', strtotime($fecha_control_pexpira)); ?></b>, por favor realice el cambio antes de la fecha indicada! <a href="perfil" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mi perfil">
                      <i class="fas fa-user btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-lg-inline">Ir a Mi perfil</span>
                    </a></p>
                  <?php endif; ?>
                </div>
                <div class="col-lg-8 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <?php if (!empty($_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos'])): ?>
                              <div class="row">
                                <div class="col-md-6">
                                  <div id="grafica_gestion"></div>
                                </div>
                                <div class="col-md-6">
                                  <div id="grafica_resultado_indicadores"></div>
                                </div>
                                <?php if(($permisos_usuario ?? '')=="Gestor"): ?>
                                    <div class="col-md-12">
                                        <div id="grafica_gestion_monitor"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="grafica_monitor_dia"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="grafica_matriz"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="grafica_semana"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="grafica_monitoreos_matriz"></div>
                                    </div>
                                <?php endif; ?>
                              </div>
                          <?php else: ?>
                              <p class="alert alert-warning">
                                  <span class="fas fa-exclamation-triangle font-size-11 p-1"></span> ¡No se encontró dashboard activo!
                              </p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 d-flex flex-column">
                  <div class="row flex-grow">
                    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']!=""): ?>
                    <div class="col-md-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title card-title-dash">Control Turno</h4>
                          </div>
                          <div class="list align-items-center pt-0">
                            <div class="wrapper w-100">
                              <?php
                                //validacion de break y almuerzos cerrados en caso de haber inciado, para poder mostrar cierre de turno
                                $control_actividad_activa = (!empty($_SESSION[APP_SESSION.'session_actividad_inicio']) && empty($_SESSION[APP_SESSION.'session_actividad_fin'])) ? 1 : 0;
                                $control_turno_iniciado   = (!empty($_SESSION[APP_SESSION.'session_turno_inicio'])) ? 1 : 0;
                              ?>
                              <div class="col-md-12">
                                <div class="row px-2">
                                  <!-- Duración turno -->
                                  <?php if ($control_turno_iniciado): ?>
                                    <div class="col-md-6 px-1">
                                      <a href="#" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" title="Duración Turno" >
                                        <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline" id='cronometro_turno'></span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <!--boton incio de turno -->
                                  <?php if (!$control_turno_iniciado): ?>
                                    <div class="col-md-12 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('inicio'); ?>&tipo=<?php echo base64_encode('turno'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block" title="Iniciar Turno" style="background-color: <?php echo $array_colores_turnos['turno']; ?> !important; border: none; text-align: left !important;">
                                        <i class="fas fa-play btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Iniciar Turno</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <!--boton fin de turno -->
                                  <?php if ($control_turno_iniciado AND !$control_actividad_activa): ?>
                                    <div class="col-md-6 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('cierre'); ?>&tipo=<?php echo base64_encode('turno'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-icon-text font-size-12 d-block" title="Finalizar Turno" style="background-color: <?php echo $array_colores_turnos['turno']; ?> !important; border: none;">
                                        <i class="fas fa-stop btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Finalizar Turno</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <div class="col-md-12 pt-1">
                                <!-- Duración ACTIVIDAD -->
                                <?php
                                    //calculo de duracion actividad
                                    if ($control_turno_iniciado AND $control_actividad_activa) {
                                        if ($_SESSION[APP_SESSION.'session_actividad_tipo']=='break') {
                                            $icono='fa-coffee';
                                        } elseif ($_SESSION[APP_SESSION.'session_actividad_tipo']=='almuerzo') {
                                            $icono='fa-utensils';
                                        } elseif ($_SESSION[APP_SESSION.'session_actividad_tipo']=='pausaactiva') {
                                            $icono='fa-walking';
                                        } elseif ($_SESSION[APP_SESSION.'session_actividad_tipo']=='capacitacion') {
                                            $icono='fa-chalkboard-teacher';
                                        } elseif ($_SESSION[APP_SESSION.'session_actividad_tipo']=='retroalimentacion') {
                                            $icono='fa-retweet';
                                        } else {
                                            $icono='fa-clock';
                                        }
                                    }
                                ?>
                                <div class="row px-2">
                                  <!--botones de break -->
                                  <?php if ($control_turno_iniciado AND !$control_actividad_activa): ?>
                                    <div class="col-md-12 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('inicio'); ?>&tipo=<?php echo base64_encode('break'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block mb-1" title="Iniciar Break" style="background-color: <?php echo $array_colores_turnos['break']; ?> !important; border: none; text-align: left !important;">
                                        <i class="fas fa-play btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Iniciar Break</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <?php if ($control_turno_iniciado AND $control_actividad_activa AND $_SESSION[APP_SESSION.'session_actividad_tipo']=="break"): ?>
                                    <div class="col-6 px-1">
                                      <a href="#" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" title="Duración Actividad" >
                                        <i class="fas <?php echo $icono; ?> btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline" id='cronometro_actividad'></span>
                                      </a>
                                    </div>
                                    <div class="col-6 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('cierre'); ?>&tipo=<?php echo base64_encode('break'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block" title="Finalizar Break" style="background-color: <?php echo $array_colores_turnos['break']; ?> !important; border: none;">
                                        <i class="fas fa-stop btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Finalizar Break</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                </div>
                                <div class="row px-2">
                                  <!--botones de almuerzo -->
                                  <?php if ($control_turno_iniciado AND !$control_actividad_activa): ?>
                                    <div class="col-md-12 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('inicio'); ?>&tipo=<?php echo base64_encode('almuerzo'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block mb-1" title="Iniciar Almuerzo" style="background-color: <?php echo $array_colores_turnos['almuerzo']; ?> !important; border: none; text-align: left !important;">
                                        <i class="fas fa-play btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Iniciar Almuerzo</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <?php if ($control_turno_iniciado AND $control_actividad_activa AND $_SESSION[APP_SESSION.'session_actividad_tipo']=="almuerzo"): ?>
                                      <div class="col-6 px-1">
                                        <a href="#" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" title="Duración Actividad" >
                                          <i class="fas <?php echo $icono; ?> btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline" id='cronometro_actividad'></span>
                                        </a>
                                      </div>
                                      <div class="col-6 px-1">
                                        <a href="control_turno_procesar?accion=<?php echo base64_encode('cierre'); ?>&tipo=<?php echo base64_encode('almuerzo'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block" title="Finalizar Almuerzo" style="background-color: <?php echo $array_colores_turnos['almuerzo']; ?> !important; border: none;">
                                          <i class="fas fa-stop btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Finalizar Almuerzo</span>
                                        </a>
                                      </div>
                                  <?php endif; ?>
                                </div>
                                <div class="row px-2">
                                  <!--botones de PAUSA ACTIVA -->
                                  <?php if ($control_turno_iniciado AND !$control_actividad_activa): ?>
                                    <div class="col-12 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('inicio'); ?>&tipo=<?php echo base64_encode('pausaactiva'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block mb-1" title="Iniciar Pausa Activa" style="background-color: <?php echo $array_colores_turnos['pausaactiva']; ?> !important; border: none; text-align: left !important;">
                                        <i class="fas fa-play btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Iniciar Pausa Activa</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <?php if ($control_turno_iniciado AND $control_actividad_activa AND $_SESSION[APP_SESSION.'session_actividad_tipo']=="pausaactiva"): ?>
                                      <div class="col-6 px-1">
                                        <a href="#" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" title="Duración Actividad" >
                                          <i class="fas <?php echo $icono; ?> btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline" id='cronometro_actividad'></span>
                                        </a>
                                      </div>
                                      <div class="col-6 px-1">
                                        <a href="control_turno_procesar?accion=<?php echo base64_encode('cierre'); ?>&tipo=<?php echo base64_encode('pausaactiva'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block" title="Finalizar Pausa Activa" style="background-color: <?php echo $array_colores_turnos['pausaactiva']; ?> !important; border: none;">
                                          <i class="fas fa-stop btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Finalizar Pausa Activa</span>
                                        </a>
                                      </div>
                                  <?php endif; ?>
                                </div>
                                <div class="row px-2">
                                  <!--botones de CAPACITACIÓN -->
                                  <?php if ($control_turno_iniciado AND !$control_actividad_activa): ?>
                                    <div class="col-12 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('inicio'); ?>&tipo=<?php echo base64_encode('capacitacion'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block mb-1" title="Iniciar Capacitación" style="background-color: <?php echo $array_colores_turnos['capacitacion']; ?> !important; border: none; text-align: left !important;">
                                        <i class="fas fa-play btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Iniciar Capacitación</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <?php if ($control_turno_iniciado AND $control_actividad_activa AND $_SESSION[APP_SESSION.'session_actividad_tipo']=="capacitacion"): ?>
                                      <div class="col-6 px-1">
                                        <a href="#" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" title="Duración Actividad" >
                                          <i class="fas <?php echo $icono; ?> btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline" id='cronometro_actividad'></span>
                                        </a>
                                      </div>
                                      <div class="col-6 px-1">
                                        <a href="control_turno_procesar?accion=<?php echo base64_encode('cierre'); ?>&tipo=<?php echo base64_encode('capacitacion'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block" title="Finalizar Capacitación" style="background-color: <?php echo $array_colores_turnos['capacitacion']; ?> !important; border: none;">
                                          <i class="fas fa-stop btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Finalizar Capacitación</span>
                                        </a>
                                      </div>
                                  <?php endif; ?>
                                </div>
                                <div class="row px-2">
                                  <!--botones de RETROALIMENTACIÓN -->
                                  <?php if ($control_turno_iniciado AND !$control_actividad_activa): ?>
                                    <div class="col-12 px-1">
                                      <a href="control_turno_procesar?accion=<?php echo base64_encode('inicio'); ?>&tipo=<?php echo base64_encode('retroalimentacion'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block mb-1" title="Iniciar Retroalimentación" style="background-color: <?php echo $array_colores_turnos['retroalimentacion']; ?> !important; border: none; text-align: left !important;">
                                        <i class="fas fa-play btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Iniciar Retroalimentación</span>
                                      </a>
                                    </div>
                                  <?php endif; ?>
                                  <?php if ($control_turno_iniciado AND $control_actividad_activa AND $_SESSION[APP_SESSION.'session_actividad_tipo']=="retroalimentacion"): ?>
                                      <div class="col-6 px-1">
                                        <a href="#" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" title="Duración Actividad" >
                                          <i class="fas <?php echo $icono; ?> btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline" id='cronometro_actividad'></span>
                                        </a>
                                      </div>
                                      <div class="col-6 px-1">
                                        <a href="control_turno_procesar?accion=<?php echo base64_encode('cierre'); ?>&tipo=<?php echo base64_encode('retroalimentacion'); ?>" class="btn control-turno py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 d-block" title="Finalizar Retroalimentación" style="background-color: <?php echo $array_colores_turnos['retroalimentacion']; ?> !important; border: none;">
                                          <i class="fas fa-stop btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Finalizar Retro</span>
                                        </a>
                                      </div>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <div class="col-md-12 pt-1">
                                <!--botones observaciones -->
                                <?php if (empty($_SESSION[APP_SESSION.'session_observaciones_inicio_turno']) AND $control_turno_iniciado): ?>
                                  <div class="row px-2">
                                    <div class="col-12 px-1">
                                      <a href="control_turno_observacion.php" class="btn py-2 px-2 btn-danger btn-icon-text font-size-12 d-block" title="Ingresar observaciones" style="background-color: <?php echo $array_colores_turnos['observaciones']; ?> !important; border: none;">
                                        <i class="fas fa-triangle-exclamation btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Ingresar observaciones</span>
                                      </a>
                                    </div>
                                  </div>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title card-title-dash">Actividad</h4>
                          </div>
                          <ul class="bullet-line-list">
                            <?php for ($i=0; $i < count($resultado_registros_actividad); $i++): ?>
                            <li>
                              <div class="d-flex justify-content-between">
                                <div>
                                  <span class="text-light-green">
                                    <?php echo log_icono($resultado_registros_actividad[$i][2]); ?>
                                  </span> <?php echo $resultado_registros_actividad[$i][1].' | '.$resultado_registros_actividad[$i][3]; ?></div>
                                <p><?php echo date('H:i d/m', strtotime($resultado_registros_actividad[$i][10])); ?></p>
                              </div>
                            </li>
                            <?php endfor; ?>
                          </ul>
                          <div class="list align-items-center pt-3">
                            <div class="wrapper w-100">
                              
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
  <!-- CONTROL TURNOS -->
    <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Control Turnos']!=""): ?>
      <script type="text/javascript">
        function esDispositivoMovil() {
            var expresionRegular = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
            var esMovil = expresionRegular.test(navigator.userAgent);
            var plataformaMovil = /iPad|iPhone|iPod|Android/.test(navigator.platform);
            var anchoPantalla = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
            var altoPantalla = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
            var esPantallaPequeña = Math.min(anchoPantalla, altoPantalla) < 400;

            <?php if($_SESSION[APP_SESSION.'_session_usu_id']=='1020773403'): ?>
              alert('Movil: '+esMovil+' ancho: '+anchoPantalla+' alto: '+altoPantalla+' pantalla: '+esPantallaPequeña+' plataforma: '+plataformaMovil)
            <?php endif; ?>

            return esMovil || plataformaMovil || esPantallaPequeña;
        }

        if (esDispositivoMovil()) {
            var botones = document.querySelectorAll('.control-turno');

            botones.forEach(function(boton) {
                boton.removeAttribute('href');
                boton.classList.add('disabled');
                boton.style.pointerEvents = 'none';
                boton.style.opacity = '0.5';
            });
        } else {
            // desktop: sin cambios
        }
      </script>
      <script type="text/javascript">
        //fecha y hora actual obtenida del servidor
        var actual_anio=<?php echo date('Y',strtotime($_SESSION[APP_SESSION.'session_turno_actual']));?>;
        var actual_mes=<?php echo date('m',strtotime($_SESSION[APP_SESSION.'session_turno_actual']));?>;
        var actual_dia=<?php echo date('d',strtotime($_SESSION[APP_SESSION.'session_turno_actual']));?>;
        var actual_hora=<?php echo date('H',strtotime($_SESSION[APP_SESSION.'session_turno_actual']));?>;
        var actual_minuto=<?php echo date('i',strtotime($_SESSION[APP_SESSION.'session_turno_actual']));?>;
        var actual_segundo=<?php echo date('s',strtotime($_SESSION[APP_SESSION.'session_turno_actual']));?>;

        function incremento_hora_actual(){
            actual_segundo++;
            if (actual_segundo==60) { actual_minuto++; actual_segundo=0; }
            if (actual_minuto==60) { actual_hora++;   actual_minuto=0; }
            timeout=setTimeout("incremento_hora_actual()",1000);
        }

        function cronometro_turno(){
            var anio=<?php echo date('Y',strtotime($_SESSION[APP_SESSION.'session_turno_inicio']));?>;
            var mes=<?php echo date('m',strtotime($_SESSION[APP_SESSION.'session_turno_inicio']));?>;
            var dia=<?php echo date('d',strtotime($_SESSION[APP_SESSION.'session_turno_inicio']));?>;
            var hora=<?php echo date('H',strtotime($_SESSION[APP_SESSION.'session_turno_inicio']));?>;
            var minuto=<?php echo date('i',strtotime($_SESSION[APP_SESSION.'session_turno_inicio']));?>;
            var segundo=<?php echo date('s',strtotime($_SESSION[APP_SESSION.'session_turno_inicio']));?>;
            var actual = new Date(actual_anio,actual_mes,actual_dia,actual_hora,actual_minuto,actual_segundo);
            inicio_turno=new Date(anio,mes,dia,hora,minuto,segundo);
            var diff=new Date(actual-inicio_turno);
            var result=""+LeadingZero(diff.getUTCHours())+":"+LeadingZero(diff.getUTCMinutes())+":"+LeadingZero(diff.getUTCSeconds());
            document.getElementById('cronometro_turno').innerHTML = result;
            timeout_turno=setTimeout("cronometro_turno()",1000);
        }

        function cronometro_actividad(){
            var anio=<?php echo date('Y',strtotime($_SESSION[APP_SESSION.'session_actividad_inicio']));?>;
            var mes=<?php echo date('m',strtotime($_SESSION[APP_SESSION.'session_actividad_inicio']));?>;
            var dia=<?php echo date('d',strtotime($_SESSION[APP_SESSION.'session_actividad_inicio']));?>;
            var hora=<?php echo date('H',strtotime($_SESSION[APP_SESSION.'session_actividad_inicio']));?>;
            var minuto=<?php echo date('i',strtotime($_SESSION[APP_SESSION.'session_actividad_inicio']));?>;
            var segundo=<?php echo date('s',strtotime($_SESSION[APP_SESSION.'session_actividad_inicio']));?>;
            var actual = new Date(actual_anio,actual_mes,actual_dia,actual_hora,actual_minuto,actual_segundo);
            inicio=new Date(anio,mes,dia,hora,minuto,segundo);
            var diff=new Date(actual-inicio);
            var result=""+LeadingZero(diff.getUTCHours())+":"+LeadingZero(diff.getUTCMinutes())+":"+LeadingZero(diff.getUTCSeconds());
            document.getElementById('cronometro_actividad').innerHTML = result;
            timeout=setTimeout("cronometro_actividad()",1000);
        }

        function LeadingZero(Time) {
            return (Time < 10) ? "0" + Time : + Time;
        }
      </script>
    <?php endif; ?>
  <!-- CONTROL TURNOS -->

  <?php if (isset($_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos']) AND $_SESSION[APP_SESSION.'_session_modulos']['Calidad-Monitoreos']!=""): ?>
      <script type="text/javascript">
          Highcharts.chart('grafica_gestion', {
              chart: { type: 'column', height: 300 },
              title: {
                  text: 'Gestión General | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
                  style: { fontSize: '14px' }
              },
              subtitle: { text: null },
              xAxis: { categories: ['Monitoreos', 'ECUF', 'ECN', 'ENC'], title: { text: null } },
              yAxis: { min: 0, title: { text: 'Cantidad monitoreos', align: 'high' }, labels: { overflow: 'justify' } },
              tooltip: { headerFormat: '<span style="font-size:10px">{point.key}: <b>{point.y}</span>', pointFormat: '', footerFormat: '', shared: true, useHTML: true },
              plotOptions: { column: { dataLabels: { enabled: true } } },
              legend: false, credits: { enabled: false },
              series: [{ name: 'Casos', colorByPoint: true, data: [<?php echo $array_gestion['monitoreos']; ?>, <?php echo $array_gestion['ecuf']; ?>, <?php echo $array_gestion['ecn']; ?>, <?php echo $array_gestion['enc']; ?>] }]
          });

          Highcharts.chart('grafica_resultado_indicadores', {
              chart: { type: 'bar', height: 300 },
              title: {
                  text: 'Resultado Indicadores General | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
                  style: { fontSize: '14px' }
              },
              subtitle: { text: null },
              xAxis: { categories: ['PENC', 'PECN', 'PECUF'], title: { text: null } },
              yAxis: { min: 0, max: 100, title: { text: 'Porcentaje', align: 'high' }, labels: { overflow: 'justify' } },
              tooltip: { headerFormat: '<span style="font-size:10px">{point.key}: <b>{point.y} %</span>', pointFormat: '', footerFormat: '', shared: true, useHTML: true },
              plotOptions: { bar: { dataLabels: { enabled: true } } },
              legend: false, credits: { enabled: false },
              series: [{ name: 'Porcentaje', colorByPoint: true, data: [<?php echo number_format($array_gestion['penc'], 2, '.', ''); ?>, <?php echo number_format($array_gestion['pecn'], 2, '.', ''); ?>, <?php echo number_format($array_gestion['pecuf'], 2, '.', ''); ?>] }]
          });

          <?php if(($permisos_usuario ?? '')=="Gestor"): ?>
            Highcharts.chart('grafica_gestion_monitor', {
                chart: { zoomType: 'xy', height: 500 },
                title: {
                    text: 'Indicadores Monitor | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
                    style: { fontSize: '14px' }
                },
                subtitle: { text: null }, credits: { enabled: false },
                xAxis: [{ categories: [
                                <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                    '<?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['nombre']; ?>',
                                <?php endfor; ?>
                                ], crosshair: true }],
                yAxis: [{
                    labels: { format: '{value}', style: { color: Highcharts.getOptions().colors[2] } },
                    title:  { text: 'Porcentaje', style: { color: Highcharts.getOptions().colors[2] } },
                    opposite: true,
                }, {
                    gridLineWidth: 0, title: { text: 'Cantidad monitoreos', style: { color: Highcharts.getOptions().colors[0] } },
                    labels: { format: '{value}', style: { color: Highcharts.getOptions().colors[0] } }
                }],
                tooltip: { shared: true },
                legend: { layout: 'horizontal', align: 'center', x: 0, verticalAlign: 'top', y: -20, floating: false,
                          backgroundColor: (Highcharts.defaultOptions.legend.backgroundColor || 'rgba(255,255,255,0.25)') },
                series: [{
                    name: 'Monitoreos', type: 'column', yAxis: 1, color: '#4472C4',
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['monitoreos']; ?>,
                            <?php endfor; ?>
                    ],
                    dataLabels: { enabled: false, inside: true, rotation: 270, align: 'left', verticalAlign: 'bottom', y: -5,
                                  style: { fontSize: '9px', fontWeight: 'normal' } },
                    tooltip: { valueSuffix: '' }
                },{
                    name: 'ECUF', type: 'column', yAxis: 1, color: '#ED7D31',
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['ecuf']; ?>,
                            <?php endfor; ?>
                    ],
                    dataLabels: { enabled: false, inside: true, rotation: 270, align: 'left', verticalAlign: 'bottom', y: -5,
                                  style: { fontSize: '9px', fontWeight: 'normal' } },
                    tooltip: { valueSuffix: '' }
                },{
                    name: 'ECN', type: 'column', yAxis: 1, color: '#A5A5A5',
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['ecn']; ?>,
                            <?php endfor; ?>
                    ],
                    dataLabels: { enabled: false, inside: true, rotation: 270, align: 'left', verticalAlign: 'bottom', y: -5,
                                  style: { fontSize: '9px', fontWeight: 'normal' } },
                    tooltip: { valueSuffix: '' }
                },{
                    name: 'ENC', type: 'column', yAxis: 1, color: '#FFC000',
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['enc']; ?>,
                            <?php endfor; ?>
                    ],
                    dataLabels: { enabled: false, inside: true, rotation: 270, align: 'left', verticalAlign: 'bottom', y: -5,
                                  style: { fontSize: '9px', fontWeight: 'normal' } },
                    tooltip: { valueSuffix: '' }
                }, {
                    name: '% PECUF', type: 'spline', color: '#5B9BD5', yAxis: 0,
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo number_format($array_gestion_monitores[$array_gestion_monitores_doc[$i]]['pecuf'], 2, '.', ''); ?>,
                            <?php endfor; ?>
                    ],
                    marker: { enabled: true }, dataLabels: { enabled: false, style: { fontSize: '9px', fontWeight: 'normal' } },
                    dashStyle: 'shortdot', tooltip: { valueSuffix: ' %' }
                }, {
                    name: '% PECN', type: 'spline', color: '#70AD47', yAxis: 0,
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo number_format($array_gestion_monitores[$array_gestion_monitores_doc[$i]]['pecn'], 2, '.', ''); ?>,
                            <?php endfor; ?>
                    ],
                    marker: { enabled: true }, dataLabels: { enabled: false, style: { fontSize: '9px', fontWeight: 'normal' } },
                    dashStyle: 'shortdot', tooltip: { valueSuffix: ' %' }
                }, {
                    name: '% PENC', type: 'spline', color: '#264478', yAxis: 0,
                    data: [
                            <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                                <?php echo number_format($array_gestion_monitores[$array_gestion_monitores_doc[$i]]['penc'], 2, '.', ''); ?>,
                            <?php endfor; ?>
                    ],
                    marker: { enabled: true }, dataLabels: { enabled: false, style: { fontSize: '9px', fontWeight: 'normal' } },
                    dashStyle: 'shortdot', tooltip: { valueSuffix: ' %' }
                }],
            });
            
            Highcharts.chart('grafica_monitor_dia', {
                chart: { type: 'spline', height: 300 },
                title: {
                    text: 'Gestión Diaria Monitor | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
                    style: { fontSize: '14px' }
                },
                subtitle: { text: null },
                xAxis: { categories: [
                                <?php for ($i=0; $i < count($array_anio_mes_dias_num); $i++): ?>
                                    '<?php echo $array_anio_mes_dias_num[$i]; ?>',
                                <?php endfor; ?>
                                ] },
                yAxis: { title: { text: 'Cantidad monitoreos' } },
                tooltip: { shared: true, style: { fontSize: '10px' } },
                legend: { layout: 'vertical', align: 'right', verticalAlign: 'middle',
                          itemStyle: { fontWeight: 'normal', fontSize: '10px' } },
                plotOptions: { spline: { dataLabels: { enabled: false } } },
                credits: { enabled: false },
                series: [
                <?php for ($i=0; $i < count($array_monitor_dia_doc); $i++): ?>
                    {
                        name: '<?php echo $array_monitor_dia[$array_monitor_dia_doc[$i]]['nombre']; ?>',
                        data: [
                            <?php for ($j=0; $j < count($array_anio_mes_dias_num); $j++): ?>
                                <?php echo $array_monitor_dia[$array_monitor_dia_doc[$i]]['monitoreos'][$array_anio_mes_dias[$j]]; ?>,
                            <?php endfor; ?>
                            ]
                    },
                <?php endfor; ?>
                ],
                responsive: { rules: [{ condition: { maxWidth: 500 }, chartOptions: {
                    legend: { layout: 'horizontal', align: 'center', verticalAlign: 'bottom' } } }] }
            });

            Highcharts.chart('grafica_matriz', {
                chart: { plotBackgroundColor: null, plotBorderWidth: null, plotShadow: false, type: 'pie', height: 300 },
                credits: { enabled: false },
                title: { text: 'Monitoreos por Matriz', style: { fontSize: '14px' } },
                tooltip: { pointFormat: '<b>{point.y}</b> ({point.percentage:.1f}%)' },
                accessibility: { point: { valueSuffix: '%' } },
                plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer',
                    dataLabels: { enabled: true, format: '{point.name}: {point.y} [{point.percentage:.1f} %]' } } },
                series: [{ colorByPoint: true, data: [
                    <?php for ($i=0; $i < count($array_matrices); $i++): ?>
                    { name: '<?php echo $array_matrices_detalle[$array_matrices[$i]]['nombre_matriz']; ?>',
                      y: <?php echo $array_matrices_detalle[$array_matrices[$i]]['cantidad']; ?> },
                    <?php endfor; ?>
                ]}]
            });

            Highcharts.chart('grafica_semana', {
                chart: { type: 'spline', height: 300 },
                title: {
                    text: '% Participación Monitoreos Semanal | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
                    style: { fontSize: '14px' }
                },
                subtitle: { text: null },
                xAxis: { categories: ['<?php echo $array_semanas['rango_1']; ?>', '<?php echo $array_semanas['rango_2']; ?>', '<?php echo $array_semanas['rango_3']; ?>', '<?php echo $array_semanas['rango_4']; ?>'] },
                yAxis: { title: { text: 'Cantidad monitoreos' } },
                tooltip: { shared: true, style: { fontSize: '10px' } },
                legend: { layout: 'vertical', align: 'center', verticalAlign: 'top',
                          itemStyle: { fontWeight: 'normal', fontSize: '10px' } },
                plotOptions: {
                    spline: { allowPointSelect: true, cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                var total = <?php echo $array_semanas['total_1']; ?> + <?php echo $array_semanas['total_2']; ?> + <?php echo $array_semanas['total_3']; ?> + <?php echo $array_semanas['total_4']; ?>;
                                var porcentaje = (this.y/total)*100;
                                return this.y + ' [' + porcentaje.toFixed(2) + '%]';
                            }
                        }
                    }
                },
                credits: { enabled: false },
                series: [{ name: 'Cantidad monitoreos',
                           data: [<?php echo $array_semanas['total_1']; ?>, <?php echo $array_semanas['total_2']; ?>, <?php echo $array_semanas['total_3']; ?>, <?php echo $array_semanas['total_4']; ?>] }]
            });

            Highcharts.chart('grafica_monitoreos_matriz', {
                chart: { type: 'bar', height: <?php echo (count($array_usuarios)<3)? 200 : count($array_usuarios)*50; ?> },
                title: {
                    text: 'Gestión Monitores por Matriz | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
                    style: { fontSize: '14px' }
                },
                subtitle: { text: null },
                xAxis: { categories: [
                                <?php for ($i=0; $i < count($array_usuarios); $i++): ?>
                                    '<?php echo $array_usuarios_detalle[$array_usuarios[$i]]['nombre']; ?>',
                                <?php endfor; ?>
                    ], title: { text: null } },
                yAxis: { min: 0, title: { text: 'Cantidad monitoreos', align: 'high' }, labels: { overflow: 'justify' } },
                tooltip: { valueSuffix: ' monitoreos', shared: true },
                plotOptions: { bar: { dataLabels: { enabled: true } } },
                legend: { layout: 'horizontal', align: 'center', verticalAlign: 'top', x: 0, y: -20, floating: false,
                          borderWidth: 0, backgroundColor: (Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'), shadow: false },
                credits: { enabled: false },
                series: [
                    <?php for ($i=0; $i < count($array_matrices); $i++): ?>
                        { name: '<?php echo $array_matrices_detalle[$array_matrices[$i]]['nombre_matriz']; ?>',
                          data: [
                                <?php for ($j=0; $j < count($array_usuarios); $j++): ?>
                                    <?php echo $array_usuario_monitoreos[$array_matrices[$i]][$array_usuarios[$j]]; ?>,
                                <?php endfor; ?>
                          ] },
                    <?php endfor; ?>
                ]
            });
          <?php endif; ?>
      </script>
  <?php endif; ?>
</body>
</html>
